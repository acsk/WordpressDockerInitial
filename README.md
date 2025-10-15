# WP Resgate - WordPress Rescue Service

Sistema completo de gestão de leads para serviços de resgate WordPress, com integração Google Sheets e painel administrativo.

## 🚀 Recursos

- ✅ **Sistema de Leads Completo**
- ✅ **Integração Google Sheets** via webhook
- ✅ **Painel Administrativo** profissional  
- ✅ **5 Custom Post Types** (Serviços, Depoimentos, FAQ, etc)
- ✅ **Design Responsivo** Bootstrap 5
- ✅ **Deploy Automático** para Hostinger

## 🛠️ Estrutura do Projeto

```
/
├── wp-admin/           # WordPress Admin
├── wp-content/         # Conteúdos WordPress
│   └── themes/
│       └── wp-resgate/ # Tema personalizado
├── wp-includes/        # WordPress Core
├── scripts/            # Scripts de automação
├── google-apps-script/ # Códigos Google Apps Script
└── docs/               # Documentação (será criada)
```

## 🌐 URLs

- **Local:** http://localhost:8090
- **Admin:** http://localhost:8090/wp-admin
- **phpMyAdmin:** http://localhost:8091

## 🚀 Como Usar

### Desenvolvimento Local:
```bash
docker-compose up -d
```

### Deploy para Hostinger:
```bash
git add .
git commit -m "Sua mensagem"
git push origin v1.1.1
```

## 📊 Status: ✅ Funcionando

- **Sistema de Leads:** Operacional
- **Google Sheets:** Integrado  
- **Deploy Automático:** Ativo
- **Admin Panel:** Sem duplicação

## 📚 Documentação complementar

- [`docs/assets-s3.md`](docs/assets-s3.md) — como sincronizar e servir os assets do tema via S3/CDN.
- [`docs/cdn-setup.md`](docs/cdn-setup.md) — guia completo (IAM, S3, CloudFront, DNS, WordPress, automações).

---
**Desenvolvido para WP Resgate** - Sistema profissional de gestão de leads WordPress
