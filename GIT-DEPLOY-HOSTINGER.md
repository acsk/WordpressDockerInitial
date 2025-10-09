# Git Deploy Hostinger - Configuração Completa

## 🚀 Passo a Passo: Git Deploy Hostinger

### 1️⃣ **Preparar Repositório Local**
```bash
# Ir para o diretório do projeto
cd /Users/andrecabral/Projetos/Estudos/WordpressDockerInitial

# Verificar status atual
git status

# Adicionar todos os arquivos
git add .

# Fazer commit inicial
git commit -m "Setup inicial WP Resgate para deploy Hostinger"
```

### 2️⃣ **Configurar no Painel Hostinger**

**A. Gerar Chave SSH (se não tiver):**
```bash
# Gerar nova chave SSH
ssh-keygen -t rsa -b 4096 -C "seu-email@gmail.com"

# Visualizar chave pública
cat ~/.ssh/id_rsa.pub
```

**B. No Painel Hostinger:**
1. Clicar em **"Gerar chave SSH"**
2. Colar sua chave pública SSH
3. Salvar

**C. Criar Repositório:**
1. **Repositório:** `https://github.com/acsk/WordpressDockerInitial.git`
2. **Ramo:** `v1.1.1` (ou `main`)
3. **Diretório:** deixar vazio (vai para `public_html`)

### 3️⃣ **Configurar Repositório GitHub/Privado**

**Opção A - Usar GitHub Existente:**
```bash
# Adicionar remote para seu GitHub existente
git remote -v

# Se não tiver origin, adicionar:
git remote add origin https://github.com/acsk/WordpressDockerInitial.git

# Push para GitHub
git push -u origin v1.1.1
```

**Opção B - Criar Repositório Privado (Recomendado):**
1. Ir para GitHub → New Repository
2. Nome: `wp-resgate-hostinger`
3. Private ✅
4. Criar

```bash
# Adicionar novo remote
git remote add hostinger-repo https://github.com/acsk/wp-resgate-hostinger.git

# Push inicial
git push -u hostinger-repo v1.1.1
```

### 4️⃣ **Configurar Deploy Automático**

**Criar arquivo `.htaccess` para WordPress:**
```bash
# Criar .htaccess no root
cat > .htaccess << 'EOF'
# BEGIN WordPress
<IfModule mod_rewrite.c>
RewriteEngine On
RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]
RewriteBase /
RewriteRule ^index\.php$ - [L]
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule . /index.php [L]
</IfModule>
# END WordPress
EOF
```

**Criar script de deploy personalizado:**
```bash
# Criar deploy.sh
cat > deploy.sh << 'EOF'
#!/bin/bash
echo "🚀 Deploy WP Resgate para Hostinger..."

# Preparar arquivos para deploy
echo "📦 Preparando arquivos..."

# Copiar apenas arquivos necessários para uma pasta temp
mkdir -p deploy-temp
cp -r wordpress/* deploy-temp/
cp .htaccess deploy-temp/
cp README.md deploy-temp/

# Remover arquivos desnecessários
rm -rf deploy-temp/wp-config.php
rm -rf deploy-temp/mysql-data

echo "✅ Arquivos preparados"

# Commit e push
git add .
git commit -m "Deploy: $(date '+%Y-%m-%d %H:%M:%S')"
git push origin v1.1.1

echo "🎉 Deploy enviado para Hostinger!"
EOF

chmod +x deploy.sh
```

### 5️⃣ **Estrutura Recomendada**

**Criar `.gitignore` específico:**
```bash
cat > .gitignore << 'EOF'
# WordPress Core (não versionar se já existe no servidor)
# wp-admin/
# wp-includes/
# index.php
# wp-*.php

# Configurações específicas do servidor
wp-config.php

# Docker (local apenas)
docker-compose.yml
.env
uploads.ini
mysql-data/

# Arquivos temporários
*.log
.DS_Store
.vscode/
node_modules/

# Uploads (geralmente já existem no servidor)
# wordpress/wp-content/uploads/
EOF
```

### 6️⃣ **Deploy WordPress Específico**

**Criar estrutura otimizada:**
```bash
# Mover arquivos WordPress para raiz
mv wordpress/* ./
rmdir wordpress

# Atualizar estrutura
git add .
git commit -m "Reestruturar para deploy Hostinger"
git push origin v1.1.1
```

## 🔧 Configuração no Hostinger

### **Na tela que você mostrou:**

1. **Repositório:** 
   - Se público: `https://github.com/acsk/WordpressDockerInitial.git`
   - Se privado: `git@github.com:acsk/wp-resgate-hostinger.git`

2. **Ramo:** `v1.1.1`

3. **Diretório:** deixar **VAZIO** (vai para public_html)

4. Clicar **"Criar"**

## ⚙️ Configuração Pós-Deploy

### **1. Configurar wp-config.php no servidor:**
```php
// Conectar via File Manager e editar wp-config.php
define('DB_NAME', 'nome_do_banco_hostinger');
define('DB_USER', 'usuario_hostinger');
define('DB_PASSWORD', 'senha_hostinger');
define('DB_HOST', 'localhost');

// Configurações específicas Hostinger
define('WP_HOME','https://seu-dominio.com');
define('WP_SITEURL','https://seu-dominio.com');
```

### **2. Importar Banco de Dados:**
```bash
# Exportar do local
docker exec wordpress_db mysqldump -u wordpress_user -p'wordpress_password_123' wordpress_db > wp-resgate-export.sql

# Importar no Hostinger via phpMyAdmin
# Ou usar o File Manager para upload do .sql
```

## 🚀 Workflow de Deploy

### **Deploy Simples:**
```bash
# Fazer mudanças no código
# ...

# Deploy automático
./deploy.sh
```

### **Deploy Manual:**
```bash
git add .
git commit -m "Atualização: descrição das mudanças"
git push origin v1.1.1
```

**O Hostinger detectará automaticamente e fará o deploy! 🎉**

## ✅ Vantagens do Git Deploy

- ✅ **Automático:** Push = Deploy instantâneo
- ✅ **Histórico:** Controle total de versões
- ✅ **Rollback:** Voltar versões facilmente
- ✅ **Seguro:** Só os arquivos necessários
- ✅ **Rápido:** Só sincroniza mudanças

**Quer que eu te ajude a configurar algum desses passos específicos?** 🤔