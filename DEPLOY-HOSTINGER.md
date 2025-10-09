# Deploy Automático: WordPress Local → Hostinger

## 🎯 Objetivo
Configurar deploy automático do projeto WP Resgate local para hospedagem Hostinger, sincronizando apenas arquivos alterados.

## 🔧 Opção 1: Git Deploy (Recomendado)

### Pré-requisitos
- Hostinger Premium/Business (suporte a Git)
- Acesso SSH à hospedagem
- Git configurado localmente

### 1️⃣ **Configurar Repositório Remoto**
```bash
# No seu projeto local
cd /Users/andrecabral/Projetos/Estudos/WordpressDockerInitial

# Adicionar remote da Hostinger
git remote add hostinger ssh://usuario@seu-dominio.com/~/public_html

# Verificar remotes
git remote -v
```

### 2️⃣ **Criar Script de Deploy**
```bash
# Criar arquivo deploy.sh
#!/bin/bash
echo "🚀 Iniciando deploy para Hostinger..."

# Fazer commit das mudanças
git add .
git commit -m "Deploy: $(date '+%Y-%m-%d %H:%M:%S')"

# Push para Hostinger
git push hostinger main

echo "✅ Deploy concluído!"
```

### 3️⃣ **Hook de Deploy Automático**
```bash
# No servidor Hostinger (via SSH)
# Criar hook post-receive
cat > ~/public_html/.git/hooks/post-receive << 'EOF'
#!/bin/bash
cd ~/public_html
git --git-dir=.git --work-tree=. checkout -f main

# Sincronizar apenas arquivos do tema
rsync -av --delete wordpress/wp-content/themes/wp-resgate/ wp-content/themes/wp-resgate/

echo "✅ Site atualizado automaticamente!"
EOF

chmod +x ~/public_html/.git/hooks/post-receive
```

## 🔧 Opção 2: FTP/SFTP Automático

### **Usando lftp (Linux/Mac)**
```bash
# Instalar lftp
brew install lftp  # Mac
# ou apt install lftp  # Linux

# Criar script ftp-deploy.sh
#!/bin/bash
echo "📤 Sincronizando via FTP..."

lftp -f "
open -u seu-usuario,sua-senha ftp.seu-dominio.com
set ftp:ssl-allow no
mirror -R --delete --verbose \
  wordpress/wp-content/themes/wp-resgate \
  public_html/wp-content/themes/wp-resgate
quit
"

echo "✅ Upload concluído!"
```

### **Usando rsync + SSH**
```bash
#!/bin/bash
echo "🔄 Sincronizando via rsync..."

rsync -avz --delete \
  -e "ssh -p 22" \
  wordpress/wp-content/themes/wp-resgate/ \
  usuario@seu-dominio.com:~/public_html/wp-content/themes/wp-resgate/

echo "✅ Sincronização concluída!"
```

## 🔧 Opção 3: GitHub Actions (Profissional)

### **Workflow Automático**
```yaml
# .github/workflows/deploy.yml
name: Deploy to Hostinger

on:
  push:
    branches: [ main ]

jobs:
  deploy:
    runs-on: ubuntu-latest
    
    steps:
    - uses: actions/checkout@v2
    
    - name: Deploy via FTP
      uses: SamKirkland/FTP-Deploy-Action@4.3.3
      with:
        server: ftp.seu-dominio.com
        username: ${{ secrets.FTP_USERNAME }}
        password: ${{ secrets.FTP_PASSWORD }}
        local-dir: ./wordpress/wp-content/themes/wp-resgate/
        server-dir: /public_html/wp-content/themes/wp-resgate/
```

## 📋 Configuração Específica Hostinger

### **1. Acesso SSH (se disponível)**
```bash
# Conectar via SSH
ssh usuario@seu-dominio.com

# Navegar para diretório web
cd ~/public_html

# Verificar estrutura
ls -la
```

### **2. Configurações FTP**
- **Servidor:** ftp.seu-dominio.com
- **Porta:** 21 (FTP) ou 22 (SFTP)
- **Diretório:** /public_html/
- **SSL:** Verificar nas configurações Hostinger

### **3. Estrutura de Arquivos**
```
public_html/
├── wp-config.php (NÃO sobrescrever)
├── wp-content/
│   └── themes/
│       └── wp-resgate/ (sincronizar este)
├── wp-admin/ (WordPress core)
└── wp-includes/ (WordPress core)
```

## ⚠️ Cuidados Importantes

### **Arquivos para NÃO sincronizar:**
- `wp-config.php` (configurações do servidor)
- `.env` (credenciais locais)
- `docker-compose.yml`
- `mysql-data/`
- `.git/` (se não usar Git deploy)

### **Criar .gitignore**
```bash
# Adicionar ao .gitignore
wp-config.php
.env
docker-compose.yml
mysql-data/
uploads.ini
*.log
.DS_Store
```

## 🚀 Script Recomendado (Deploy Simples)

```bash
#!/bin/bash
# deploy-hostinger.sh

echo "🚀 WP Resgate - Deploy para Hostinger"

# Configurações (editar com seus dados)
FTP_HOST="ftp.seu-dominio.com"
FTP_USER="seu-usuario"
FTP_PASS="sua-senha"
REMOTE_DIR="public_html/wp-content/themes/wp-resgate"
LOCAL_DIR="wordpress/wp-content/themes/wp-resgate"

# Usar lftp para sincronização
lftp -f "
open -u $FTP_USER,$FTP_PASS $FTP_HOST
set ftp:ssl-allow no
mirror -R --delete --verbose $LOCAL_DIR $REMOTE_DIR
quit
"

echo "✅ Deploy concluído!"
echo "🌐 Acesse: https://seu-dominio.com"
```

## 📊 Recomendação Final

**Para Hostinger Compartilhada:**
1. **Simples:** Script FTP com lftp
2. **Profissional:** GitHub Actions + FTP
3. **Avançado:** Git deploy (se SSH disponível)

**Uso:**
```bash
# Tornar executável
chmod +x deploy-hostinger.sh

# Executar deploy
./deploy-hostinger.sh
```

Isso sincronizará apenas os arquivos alterados, mantendo seu WordPress online funcionando! 🎯