#!/bin/bash

# Script para converter volumes Docker para bind mounts (pastas locais)
# Uso: ./setup-local-folders.sh

echo "🔄 Convertendo para pastas locais..."

# Para os containers
echo "⏹️  Parando containers..."
docker-compose down

# Cria as pastas locais
echo "📁 Criando pastas locais..."
mkdir -p ./wordpress
mkdir -p ./mysql-data

# Copia os dados dos volumes para as pastas locais
echo "📋 Copiando dados do WordPress..."
docker run --rm -v wordpressdockerinitial_wordpress_data:/source -v $(pwd)/wordpress:/dest alpine sh -c "cp -r /source/* /dest/"

echo "📋 Copiando dados do MySQL..."
docker run --rm -v wordpressdockerinitial_db_data:/source -v $(pwd)/mysql-data:/dest alpine sh -c "cp -r /source/* /dest/"

# Ajusta permissões
echo "🔐 Ajustando permissões..."
sudo chown -R $(id -u):$(id -g) ./wordpress ./mysql-data
chmod -R 755 ./wordpress
chmod -R 755 ./mysql-data

# Backup do docker-compose original
cp docker-compose.yml docker-compose.yml.backup

# Modifica o docker-compose.yml
cat > docker-compose.yml << 'EOF'
services:
  # Banco de dados MySQL
  db:
    image: mysql:8.0
    platform: linux/arm64/v8
    container_name: wordpress_db
    restart: unless-stopped
    environment:
      MYSQL_DATABASE: ${DB_NAME}
      MYSQL_USER: ${DB_USER}
      MYSQL_PASSWORD: ${DB_PASSWORD}
      MYSQL_ROOT_PASSWORD: ${DB_ROOT_PASSWORD}
    volumes:
      - ./mysql-data:/var/lib/mysql
    networks:
      - wordpress_network
    # Configurações básicas para MySQL 8.0
    command: 
      - "--max-connections=50"
      - "--innodb-buffer-pool-size=128M"
    deploy:
      resources:
        limits:
          memory: 512M
        reservations:
          memory: 256M

  # WordPress
  wordpress:
    depends_on:
      - db
    image: wordpress:6-php8.1-apache
    platform: linux/arm64/v8
    container_name: wordpress_app
    restart: unless-stopped
    ports:
      - "8090:80"
    environment:
      WORDPRESS_DB_HOST: db:3306
      WORDPRESS_DB_NAME: ${DB_NAME}
      WORDPRESS_DB_USER: ${DB_USER}
      WORDPRESS_DB_PASSWORD: ${DB_PASSWORD}
      # Configurações de performance
      PHP_MEMORY_LIMIT: 256M
      PHP_MAX_EXECUTION_TIME: 120
      PHP_UPLOAD_MAX_FILESIZE: 32M
      PHP_POST_MAX_SIZE: 32M
    volumes:
      - ./wordpress:/var/www/html
      - ./uploads.ini:/usr/local/etc/php/conf.d/uploads.ini
    networks:
      - wordpress_network
    deploy:
      resources:
        limits:
          memory: 512M
        reservations:
          memory: 256M

  # phpMyAdmin (opcional - para gerenciar o banco)
  phpmyadmin:
    depends_on:
      - db
    image: phpmyadmin/phpmyadmin:latest
    container_name: wordpress_phpmyadmin
    restart: unless-stopped
    ports:
      - "8091:80"
    environment:
      PMA_HOST: db
      PMA_PORT: 3306
      MYSQL_ROOT_PASSWORD: ${DB_ROOT_PASSWORD}
      # Configurações para reduzir uso de memória
      MEMORY_LIMIT: 128M
      UPLOAD_LIMIT: 32M
    networks:
      - wordpress_network
    deploy:
      resources:
        limits:
          memory: 256M
        reservations:
          memory: 128M

networks:
  wordpress_network:
    driver: bridge
EOF

# Atualiza o .gitignore
cat >> .gitignore << 'EOF'

# Pastas locais (bind mounts)
/wordpress/
/mysql-data/
docker-compose.yml.backup
EOF

echo "✅ Conversão concluída!"
echo ""
echo "📁 Agora os arquivos ficam em:"
echo "   WordPress: ./wordpress/"
echo "   MySQL: ./mysql-data/"
echo ""
echo "🚀 Para iniciar:"
echo "   docker-compose up -d"
echo ""
echo "📋 Backup do docker-compose original salvo em: docker-compose.yml.backup"