#!/usr/bin/env bash
set -euo pipefail

# Carrega variáveis do .env (opcional)
# shellcheck disable=SC1091
if [ -f .env ]; then
    export $(grep -Ev '^(#|$)' .env | xargs -0 -n1 -d'\n' printf '%s\0')
fi

AWS_REGION=${AWS_REGION:-us-east-2}
AWS_PROFILE=${AWS_PROFILE:-wpprotegido.site.s3}
S3_BUCKET=${S3_BUCKET:-wpprotegido-site-097786717489}
THEME_SLUG=${THEME_SLUG:-wp-resgate}

LOCAL_ASSETS_DIR="wp-content/themes/${THEME_SLUG}/assets"
S3_ASSETS_PREFIX="${THEME_SLUG}/assets"

if [ ! -d "${LOCAL_ASSETS_DIR}" ]; then
    echo "[sync-assets] Diretório ${LOCAL_ASSETS_DIR} não encontrado. Abortando." >&2
    exit 1
fi

# Sobe os assets
aws s3 sync "${LOCAL_ASSETS_DIR}/" \
    "s3://${S3_BUCKET}/${S3_ASSETS_PREFIX}/" \
    --profile "${AWS_PROFILE}" \
    --region "${AWS_REGION}" \
    --cache-control "public, max-age=31536000, immutable" \
    --delete

echo "[sync-assets] Assets sincronizados com s3://${S3_BUCKET}/${S3_ASSETS_PREFIX}/"
