# Como publicar no GitHub

## 1. Criar repositório no GitHub
1. Acesse https://github.com/new
2. Nome: `wordpress-docker-initial`
3. Descrição: `🐳 WordPress Docker setup otimizado para desenvolvimento com recursos mínimos e suporte Apple Silicon`
4. **NÃO** marque "Initialize with README" (já temos um)
5. Clique em "Create repository"

## 2. Conectar repositório local ao GitHub
```bash
# Adicionar remote origin
git remote add origin https://github.com/SEU_USUARIO/wordpress-docker-initial.git

# Verificar remote
git remote -v

# Push inicial com todas as tags
git push -u origin main
git push origin --tags
```

## 3. Configurar branch protection (opcional)
No GitHub, vá em Settings > Branches e configure:
- Require pull request reviews
- Require status checks to pass before merging

## 4. Adicionar badges no README (opcional)
Adicione no topo do README.md:
```markdown
![Docker](https://img.shields.io/badge/Docker-2496ED?style=flat&logo=docker&logoColor=white)
![WordPress](https://img.shields.io/badge/WordPress-21759B?style=flat&logo=wordpress&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-4479A1?style=flat&logo=mysql&logoColor=white)
![Apple Silicon](https://img.shields.io/badge/Apple%20Silicon-000000?style=flat&logo=apple&logoColor=white)
```

## 5. Releases
Para criar uma release:
1. Vá em "Releases" no GitHub
2. Clique "Create a new release"
3. Escolha a tag `v1.0.0`
4. Título: `v1.0.0 - WordPress Docker Initial`
5. Descreva as funcionalidades principais
6. Publique a release

## Comandos úteis para manutenção

### Novas versões
```bash
# Fazer mudanças...
git add .
git commit -m "feat: nova funcionalidade"

# Criar nova tag
git tag -a v1.1.0 -m "v1.1.0: Descrição das mudanças"

# Push com tags
git push origin main
git push origin v1.1.0
```

### Atualizar CHANGELOG
Sempre atualizar o CHANGELOG.md antes de criar uma nova versão.