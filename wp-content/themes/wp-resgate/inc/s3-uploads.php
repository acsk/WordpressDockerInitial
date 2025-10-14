<?php
/**
 * Integração do WP Resgate com Amazon S3.
 *
 * @package WP_Resgate
 */

if (!defined('ABSPATH')) {
    exit;
}

use WP_REST_Response;

/**
 * Verifica se a integração está habilitada e com dados suficientes.
 */
function wp_resgate_s3_is_enabled() {
    $config = wp_resgate_get_s3_config();

    if (!$config['enabled']) {
        return false;
    }

    return $config['bucket'] !== ''
        && $config['region'] !== ''
        && $config['access_key'] !== ''
        && $config['secret_key'] !== '';
}

/**
 * Retorna (singleton) o cliente S3 ou null caso indisponível.
 *
 * @return \Aws\S3\S3Client|null
 */
function wp_resgate_s3_client() {
    static $client = null;
    static $failure = false;

    if ($client instanceof \Aws\S3\S3Client) {
        return $client;
    }

    if ($failure || !wp_resgate_s3_is_enabled()) {
        return null;
    }

    $class = '\\Aws\\S3\\S3Client';

    if (!class_exists($class)) {
        $autoload_paths = [
            WP_RESGATE_THEME_PATH . '/vendor/autoload.php',
            WP_CONTENT_DIR . '/vendor/autoload.php',
            ABSPATH . 'vendor/autoload.php',
        ];

        foreach ($autoload_paths as $autoload) {
            if (file_exists($autoload)) {
                require_once $autoload;
                if (class_exists($class)) {
                    break;
                }
            }
        }
    }

    if (!class_exists($class)) {
        error_log('WP Resgate S3: AWS SDK não encontrado. Execute `composer require aws/aws-sdk-php` e garanta o autoload.');
        $failure = true;
        return null;
    }

    $config = wp_resgate_get_s3_config();

    try {
        $client = new $class([
            'version' => 'latest',
            'region' => $config['region'],
            'credentials' => [
                'key' => $config['access_key'],
                'secret' => $config['secret_key'],
            ],
        ]);
    } catch (\Exception $exception) {
        error_log('WP Resgate S3: falha ao inicializar cliente - ' . $exception->getMessage());
        $failure = true;
        return null;
    }

    return $client;
}

/**
 * Retorna a URL base (S3 ou CDN) para uploads.
 */
function wp_resgate_s3_get_base_url() {
    $config = wp_resgate_get_s3_config();

    if (!empty($config['cdn_base_url'])) {
        return untrailingslashit($config['cdn_base_url']);
    }

    $bucket = $config['bucket'];
    $region = $config['region'] ?: 'us-east-1';

    $base = sprintf('https://%s.s3.%s.amazonaws.com', $bucket, $region);
    if ($region === 'us-east-1') {
        $base = sprintf('https://%s.s3.amazonaws.com', $bucket);
    }

    if ($config['base_path'] !== '') {
        $base .= '/' . trim($config['base_path'], '/');
    }

    return untrailingslashit($base);
}

/**
 * Monta a chave no bucket a partir de um caminho relativo.
 */
function wp_resgate_s3_build_key($relative_path) {
    $config = wp_resgate_get_s3_config();
    $relative_path = ltrim($relative_path, '/');

    if ($config['base_path'] !== '') {
        $prefix = trim($config['base_path'], '/') . '/';
    } else {
        $prefix = '';
    }

    return $prefix . $relative_path;
}

/**
 * Retorna o caminho relativo dentro de uploads.
 */
function wp_resgate_s3_relative_path($absolute_path) {
    $uploads = wp_upload_dir();
    $base_dir = trailingslashit($uploads['basedir']);

    if (strpos($absolute_path, $base_dir) === 0) {
        return ltrim(str_replace($base_dir, '', $absolute_path), '/');
    }

    return ltrim($absolute_path, '/');
}

/**
 * URL completa do objeto no S3/CDN.
 */
function wp_resgate_s3_build_url($relative_path) {
    $base = wp_resgate_s3_get_base_url();
    return $base . '/' . ltrim($relative_path, '/');
}

/**
 * Envia um arquivo local para o S3.
 */
function wp_resgate_s3_upload_file($client, $relative_path, $absolute_path) {
    if (!file_exists($absolute_path)) {
        return false;
    }

    $config = wp_resgate_get_s3_config();
    $key = wp_resgate_s3_build_key($relative_path);

    $mime = wp_check_filetype($absolute_path);
    $content_type = !empty($mime['type']) ? $mime['type'] : 'application/octet-stream';

    try {
        $client->putObject([
            'Bucket' => $config['bucket'],
            'Key' => $key,
            'SourceFile' => $absolute_path,
            'ContentType' => $content_type,
            'CacheControl' => 'public, max-age=31536000',
        ]);
    } catch (\Exception $exception) {
        error_log('WP Resgate S3: erro ao enviar ' . $relative_path . ' - ' . $exception->getMessage());
        return false;
    }

    return true;
}

/**
 * Remove um arquivo do S3.
 */
function wp_resgate_s3_delete_file($client, $relative_path) {
    $config = wp_resgate_get_s3_config();
    $key = wp_resgate_s3_build_key($relative_path);

    try {
        $client->deleteObject([
            'Bucket' => $config['bucket'],
            'Key' => $key,
        ]);
    } catch (\Exception $exception) {
        error_log('WP Resgate S3: erro ao remover ' . $relative_path . ' - ' . $exception->getMessage());
    }
}

/**
 * Coleta todos os arquivos de um attachment (original + tamanhos).
 */
function wp_resgate_s3_collect_attachment_files($attachment_id, $metadata = []) {
    $files = [];
    $file = get_attached_file($attachment_id);

    if ($file && file_exists($file)) {
        $files[$file] = wp_resgate_s3_relative_path($file);
    }

    if (!empty($metadata['sizes']) && $file) {
        $base_dir = trailingslashit(pathinfo($file, PATHINFO_DIRNAME));

        foreach ($metadata['sizes'] as $size) {
            if (empty($size['file'])) {
                continue;
            }

            $size_file = $base_dir . $size['file'];
            if (file_exists($size_file)) {
                $files[$size_file] = wp_resgate_s3_relative_path($size_file);
            }
        }
    }

    if (!empty($metadata['original_image']) && $file) {
        $original = path_join(pathinfo($file, PATHINFO_DIRNAME), $metadata['original_image']);
        if (file_exists($original)) {
            $files[$original] = wp_resgate_s3_relative_path($original);
        }
    }

    return $files;
}

/**
 * Envia arquivos para o S3 após um upload.
 */
function wp_resgate_s3_handle_upload($metadata, $attachment_id) {
    if (!wp_resgate_s3_is_enabled()) {
        return $metadata;
    }

    $client = wp_resgate_s3_client();
    if (!$client) {
        return $metadata;
    }

    $files = wp_resgate_s3_collect_attachment_files($attachment_id, $metadata);
    if (empty($files)) {
        return $metadata;
    }

    foreach ($files as $absolute => $relative) {
        $uploaded = wp_resgate_s3_upload_file($client, $relative, $absolute);

        if ($uploaded && wp_resgate_get_s3_config()['delete_local']) {
            @unlink($absolute);
        }
    }

    update_post_meta($attachment_id, '_wp_resgate_s3_synced', time());

    return $metadata;
}
add_filter('wp_update_attachment_metadata', 'wp_resgate_s3_handle_upload', 20, 2);

/**
 * Remove os arquivos do S3 quando o attachment é deletado.
 */
function wp_resgate_s3_delete_attachment($attachment_id) {
    if (!wp_resgate_s3_is_enabled()) {
        return;
    }

    $client = wp_resgate_s3_client();
    if (!$client) {
        return;
    }

    $metadata = wp_get_attachment_metadata($attachment_id) ?: [];
    $files = wp_resgate_s3_collect_attachment_files($attachment_id, $metadata);

    foreach ($files as $relative) {
        wp_resgate_s3_delete_file($client, $relative);
    }
}
add_action('delete_attachment', 'wp_resgate_s3_delete_attachment');

/**
 * Reescreve URLs de anexos para o S3/CDN.
 */
function wp_resgate_s3_rewrite_url($url) {
    if (!wp_resgate_s3_is_enabled()) {
        return $url;
    }

    $uploads = wp_upload_dir();
    $baseurl = trailingslashit($uploads['baseurl']);

    if (strpos($url, $baseurl) !== 0) {
        return $url;
    }

    $relative = ltrim(str_replace($baseurl, '', $url), '/');
    return wp_resgate_s3_build_url($relative);
}

add_filter('wp_get_attachment_url', function ($url) {
    return wp_resgate_s3_rewrite_url($url);
}, 20, 1);

add_filter('wp_get_attachment_image_src', function ($image) {
    if (!is_array($image) || empty($image[0])) {
        return $image;
    }

    $image[0] = wp_resgate_s3_rewrite_url($image[0]);
    return $image;
}, 20);

add_filter('wp_calculate_image_srcset', function ($sources) {
    if (!is_array($sources)) {
        return $sources;
    }

    foreach ($sources as $width => $source) {
        if (!empty($source['url'])) {
            $sources[$width]['url'] = wp_resgate_s3_rewrite_url($source['url']);
        }
    }

    return $sources;
}, 20);

add_filter('wp_prepare_attachment_for_js', function ($response) {
    if (!is_array($response)) {
        return $response;
    }

    if (!empty($response['url'])) {
        $response['url'] = wp_resgate_s3_rewrite_url($response['url']);
    }

    if (!empty($response['sizes']) && is_array($response['sizes'])) {
        foreach ($response['sizes'] as $size => $data) {
            if (!empty($data['url'])) {
                $response['sizes'][$size]['url'] = wp_resgate_s3_rewrite_url($data['url']);
            }
        }
    }

    return $response;
}, 20);

add_filter('rest_prepare_attachment', function ($response) {
    if (!($response instanceof WP_REST_Response)) {
        return $response;
    }

    $data = $response->get_data();

    if (!empty($data['source_url'])) {
        $data['source_url'] = wp_resgate_s3_rewrite_url($data['source_url']);
    }

    if (!empty($data['media_details']['sizes'])) {
        foreach ($data['media_details']['sizes'] as $key => $size) {
            if (!empty($size['source_url'])) {
                $data['media_details']['sizes'][$key]['source_url'] = wp_resgate_s3_rewrite_url($size['source_url']);
            }
        }
    }

    $response->set_data($data);

    return $response;
}, 20);

/**
 * Reescreve URLs antigas em conteúdos HTML/texto.
 */
function wp_resgate_s3_replace_content_urls($content) {
    if (!wp_resgate_s3_is_enabled()) {
        return $content;
    }

    $config = wp_resgate_get_s3_config();
    $target = wp_resgate_s3_get_base_url();
    $origin = $config['origin_base_url'];

    if ($target === '' || $origin === '') {
        return $content;
    }

    $origin = untrailingslashit($origin);
    $target = untrailingslashit($target);

    if ($origin === $target) {
        return $content;
    }

    return str_replace($origin, $target, $content);
}

add_filter('the_content', 'wp_resgate_s3_replace_content_urls', 20);
add_filter('the_excerpt', 'wp_resgate_s3_replace_content_urls', 20);
add_filter('widget_text', 'wp_resgate_s3_replace_content_urls', 20);
add_filter('widget_custom_html_content', 'wp_resgate_s3_replace_content_urls', 20);
add_filter('post_thumbnail_html', 'wp_resgate_s3_replace_content_urls', 20);

add_filter('wp_get_attachment_image_attributes', function ($attr) {
    if (!is_array($attr)) {
        return $attr;
    }

    if (!empty($attr['src'])) {
        $attr['src'] = wp_resgate_s3_replace_content_urls($attr['src']);
    }

    if (!empty($attr['srcset'])) {
        $attr['srcset'] = wp_resgate_s3_replace_content_urls($attr['srcset']);
    }

    return $attr;
}, 20);
