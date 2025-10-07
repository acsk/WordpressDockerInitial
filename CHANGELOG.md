# Changelog

Todas as mudanças notáveis deste projeto serão documentadas neste arquivo.

O formato é baseado em [Keep a Changelog](https://keepachangelog.com/pt-BR/1.0.0/),
e este projeto adere ao [Semantic Versioning](https://semver.org/lang/pt-BR/).

## [1.0.0] - 2025-10-07

### Adicionado
- Configuração Docker completa para WordPress
- Suporte nativo para Apple Silicon (ARM64)
- WordPress 6.x com PHP 8.1 e Apache
- MySQL 8.0 otimizado para recursos mínimos (128MB buffer pool)
- phpMyAdmin 5.x para gerenciamento do banco
- Bind mounts para edição direta de arquivos no VS Code
- Script `setup-local-folders.sh` para conversão automática
- Configurações PHP otimizadas (`uploads.ini`)
- Template de variáveis de ambiente (`.env.example`)
- Documentação completa com guias de uso
- Comandos úteis para backup e restore
- Troubleshooting e dicas de performance

### Características
- **Recursos mínimos**: Total ~1.3GB RAM (WordPress: 512MB + MySQL: 512MB + phpMyAdmin: 256MB)
- **Desenvolvimento friendly**: Arquivos editáveis diretamente no VS Code
- **Compatibilidade**: Otimizado para macOS com chip Apple
- **Flexibilidade**: Suporte para volumes Docker ou pastas locais
- **Segurança**: Exclusão automática de arquivos sensíveis
- **Performance**: Configurações otimizadas para desenvolvimento

### Portas
- WordPress: http://localhost:8090
- phpMyAdmin: http://localhost:8091

### Credenciais Padrão
- Database: `wordpress_db`
- Usuário: `wordpress_user`
- Senha: `wordpress_password_123`
- Root: `root_password_456`