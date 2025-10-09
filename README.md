# WordPress Docker - Configuração Mínima

Este projeto configura um ambiente WordPress completo usando Docker com o menor uso de recursos possível.

## 🚀 Componentes

- **WordPress** 6.x com PHP 8.1 e Apache
- **MySQL** 8.0 (otimizado para baixo consumo)
- **phpMyAdmin** 5.x (para gerenciamento do banco)

## 📋 Pré-requisitos

- Docker
- Docker Compose
- 2GB de RAM disponível (mínimo)

## 🍎 Compatibilidade Apple Silicon (M1/M2/M3)

Este projeto está configurado para funcionar nativamente em Macs com chip Apple Silicon (ARM64). As imagens Docker foram otimizadas para a arquitetura ARM64/v8.

**Nota**: Você pode ver avisos sobre plataforma durante o `docker-compose up`, mas isso é normal e não afeta o funcionamento.

## ⚡ Início Rápido

1. **Clone ou baixe o projeto**
   ```bash
   git clone <url-do-repositorio>
   cd WordpressDockerInitial
   ```

2. **Configure as variáveis de ambiente**
   ```bash
   # O arquivo .env já está configurado com valores padrão
   # Você pode editá-lo se necessário
   cp .env.example .env  # se necessário
   ```

3. **Inicie os containers**
   ```bash
   docker-compose up -d
   ```

4. **Acesse o WordPress**
   - WordPress: http://localhost:8090
   - phpMyAdmin: http://localhost:8091

## 🔧 Configurações de Recursos

### Limites de Memória
- **MySQL**: 512MB máximo, 256MB reservado
- **WordPress**: 512MB máximo, 256MB reservado  
- **phpMyAdmin**: 256MB máximo, 128MB reservado

### Otimizações MySQL
- Buffer pool: 128MB
- Máximo de conexões: 50
- Configuração otimizada para MySQL 8.0
- Compatível com Apple Silicon (ARM64)

## 📁 Estrutura do Projeto

```
WordpressDockerInitial/
├── docker-compose.yml    # Configuração dos containers
├── .env                 # Variáveis de ambiente
├── .env.example         # Template de variáveis
├── .gitignore          # Arquivos ignorados pelo Git
├── uploads.ini         # Configurações PHP para uploads
└── README.md           # Esta documentação
```

## 📂 Onde ficam os arquivos?

### 🎯 **Localização dos Dados**

Os arquivos do WordPress e MySQL ficam armazenados em **volumes Docker gerenciados**:

#### **WordPress (Temas, Plugins, Uploads)**
- **Local físico**: `/var/lib/docker/volumes/wordpressdockerinitial_wordpress_data/_data`
- **Dentro do container**: `/var/www/html`
- **Volume**: `wordpressdockerinitial_wordpress_data`

#### **MySQL (Banco de dados)**  
- **Local físico**: `/var/lib/docker/volumes/wordpressdockerinitial_db_data/_data`
- **Dentro do container**: `/var/lib/mysql`
- **Volume**: `wordpressdockerinitial_db_data`

### 🛠️ **Como acessar os arquivos**

#### **Método 1: Via container (Recomendado)**
```bash
# Acessar arquivos do WordPress
docker exec -it wordpress_app bash
cd /var/www/html

# Listar arquivos
docker exec wordpress_app ls -la /var/www/html

# Copiar arquivo do container para o host
docker cp wordpress_app:/var/www/html/wp-config.php ./wp-config-backup.php

# Copiar arquivo do host para o container
docker cp meu-theme.zip wordpress_app:/var/www/html/wp-content/themes/
```

#### **Método 2: Usar bind mounts (Arquivos locais)**
Se você quiser que os arquivos fiquem em pastas locais visíveis no Finder/Explorer, pode alterar o `docker-compose.yml`:

```yaml
# Substituir esta seção no docker-compose.yml:
volumes:
  - wordpress_data:/var/www/html

# Por esta (cria pasta local):
volumes:
  - ./wordpress:/var/www/html
  - ./uploads.ini:/usr/local/etc/php/conf.d/uploads.ini
```

#### **Método 3: Script automático para pastas locais**
Se você quiser converter automaticamente para usar pastas locais visíveis no Finder:

```bash
# Executar o script de conversão
./setup-local-folders.sh
```

Este script irá:
- ✅ Parar os containers  
- ✅ Criar pastas `./wordpress` e `./mysql-data`
- ✅ Copiar todos os dados dos volumes para as pastas
- ✅ Ajustar permissões
- ✅ Modificar o `docker-compose.yml` automaticamente
- ✅ Fazer backup da configuração original

⚠️ **Importante**: Após usar o script, os arquivos ficarão em pastas locais visíveis no seu sistema de arquivos!

## 🗄️ Banco de Dados

### Credenciais Padrão
- **Database**: wordpress_db
- **Usuário**: wordpress_user
- **Senha**: wordpress_password_123
- **Root**: root_password_456

### phpMyAdmin
- **URL**: http://localhost:8091
- **Usuário**: root
- **Senha**: root_password_456

## 🛠️ Comandos Úteis

### Gerenciar containers
```bash
# Iniciar
docker-compose up -d

# Parar
docker-compose down

# Ver logs
docker-compose logs -f

# Reiniciar um serviço
docker-compose restart wordpress
```

### Backup e restore
```bash
# Backup do banco de dados
docker-compose exec db mysqldump -u root -p${DB_ROOT_PASSWORD} wordpress_db > backup-$(date +%Y%m%d).sql

# Restaurar backup
docker-compose exec -T db mysql -u root -p${DB_ROOT_PASSWORD} wordpress_db < backup-20251007.sql

# Backup completo dos arquivos WordPress
docker run --rm -v wordpressdockerinitial_wordpress_data:/data -v $(pwd):/backup alpine tar czf /backup/wordpress-backup-$(date +%Y%m%d).tar.gz -C /data .

# Restaurar backup dos arquivos
docker run --rm -v wordpressdockerinitial_wordpress_data:/data -v $(pwd):/backup alpine tar xzf /backup/wordpress-backup-20251007.tar.gz -C /data
```

### Gerenciamento de arquivos
```bash
# Instalar um tema via terminal
docker cp meu-tema.zip wordpress_app:/var/www/html/wp-content/themes/
docker exec wordpress_app chown -R www-data:www-data /var/www/html/wp-content/themes/

# Instalar um plugin via terminal  
docker cp meu-plugin.zip wordpress_app:/var/www/html/wp-content/plugins/
docker exec wordpress_app chown -R www-data:www-data /var/www/html/wp-content/plugins/

# Ver logs em tempo real
docker-compose logs -f wordpress
docker-compose logs -f db

# Acessar MySQL diretamente
docker-compose exec db mysql -u root -p${DB_ROOT_PASSWORD}
```

### Limpar tudo (CUIDADO!)
```bash
# Para containers e remove volumes (perde todos os dados)
docker-compose down -v
docker system prune -a
```

## 🔒 Segurança

Para produção, certifique-se de:

1. **Alterar todas as senhas** no arquivo `.env`
2. **Usar HTTPS** (considere usar um proxy reverso)
3. **Configurar firewall** adequadamente  
4. **Fazer backups regulares**
5. **Manter containers atualizados**

## 🐛 Troubleshooting

### Container não inicia
```bash
# Verificar logs
docker-compose logs [nome-do-servico]

# Verificar recursos do sistema
docker stats
```

### WordPress não conecta ao banco
1. Verifique se as credenciais no `.env` estão corretas
2. Aguarde o MySQL terminar a inicialização
3. Reinicie o container do WordPress

### Erro de memória
1. Verifique se tem RAM suficiente disponível
2. Ajuste os limites no `docker-compose.yml`
3. Feche outros programas para liberar memória

## 📊 Monitoramento

### Verificar uso de recursos
```bash
# Ver estatísticas em tempo real
docker stats

# Ver uso de disco dos volumes
docker system df
```

### Logs importantes
```bash
# WordPress
docker-compose logs wordpress

# MySQL  
docker-compose logs db

# Todos os serviços
docker-compose logs -f
```

## 🔄 Atualizações

### Atualizar WordPress
```bash
# Parar containers
docker-compose down

# Atualizar imagens
docker-compose pull

# Reiniciar
docker-compose up -d
```

## 💡 Dicas de Performance

1. **Use SSD** para melhor performance do MySQL
2. **Monitore a RAM** regularmente  
3. **Faça limpeza** de plugins desnecessários
4. **Configure cache** no WordPress (plugins como W3 Total Cache)
5. **Otimize imagens** antes de fazer upload

## 📞 Suporte

Para problemas ou dúvidas, verifique:
- Logs dos containers
- Documentação oficial do WordPress
- Issues do projeto no GitHub# Deploy automático funcionando - Qui  9 Out 2025 16:36:51 -03
