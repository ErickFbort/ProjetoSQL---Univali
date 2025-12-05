# ✈️ Sistema de Gestão de Processos Aéreos

Sistema web simples e moderno para cadastro e gerenciamento de processos aéreos, desenvolvido como parte do projeto de desenvolvimento.

## 📋 Funcionalidades

- ✅ **Cadastro de Processos**: Registro completo de informações sobre processos aéreos
- 📝 **Edição de Processos**: Atualização de dados já cadastrados
- 🗑️ **Exclusão de Processos**: Remoção de processos com confirmação
- 🔍 **Busca e Filtragem**: Pesquisa rápida por qualquer campo do processo
- 💾 **Armazenamento Local**: Dados salvos no navegador (localStorage)
- 📱 **Design Responsivo**: Interface adaptável para diferentes tamanhos de tela

## 🚀 Como Usar

### Opção 1: Abrir diretamente no navegador

1. Abra o arquivo `index.html` em qualquer navegador moderno (Chrome, Firefox, Edge, Safari)
2. Não é necessário instalar nada ou configurar servidor

### Opção 2: Usar um servidor local (recomendado)

Se você tiver Python instalado:

```bash
# Python 3
python -m http.server 8000

# Ou Python 2
python -m SimpleHTTPServer 8000
```

Depois acesse: `http://localhost:8000`

Ou usando Node.js com http-server:

```bash
npx http-server -p 8000
```

## 📖 Campos do Formulário

- **Número do Processo**: Identificador único do processo (ex: PRO-2024-001)
- **Tipo de Processo**: Licenciamento, Autorização, Certificação, Fiscalização ou Outro
- **Empresa/Organização**: Nome da empresa responsável
- **Responsável**: Nome da pessoa responsável pelo processo
- **Data de Início**: Data em que o processo foi iniciado
- **Data Prevista de Conclusão**: Data estimada para finalização (opcional)
- **Status**: Em Análise, Aprovado, Rejeitado, Pendente ou Concluído
- **Observações**: Informações adicionais sobre o processo (opcional)

## 🎨 Interface

A aplicação possui uma interface moderna e intuitiva com:

- Design gradiente moderno
- Cards informativos para cada processo
- Cores diferenciadas por status
- Animações suaves
- Layout responsivo para mobile

## 💾 Armazenamento de Dados

Os dados são armazenados localmente no navegador usando `localStorage`. Isso significa que:

- Os dados persistem mesmo após fechar o navegador
- Os dados são específicos para cada navegador/usuário
- Não há necessidade de banco de dados ou servidor

**Nota**: Para produção, recomenda-se integrar com um backend e banco de dados real.

## 🔧 Estrutura do Projeto

```
ProjetoSQL/
├── index.html      # Estrutura HTML da aplicação
├── styles.css      # Estilos e design da interface
├── script.js       # Lógica JavaScript e funcionalidades CRUD
└── README.md       # Este arquivo
```

## 📝 Próximos Passos (Melhorias Futuras)

- [ ] Integração com banco de dados (SQL)
- [ ] Autenticação de usuários
- [ ] Exportação de dados (PDF, Excel)
- [ ] Relatórios e estatísticas
- [ ] Notificações por email
- [ ] Histórico de alterações
- [ ] Upload de documentos anexos

## 🛠️ Tecnologias Utilizadas

- HTML5
- CSS3 (com Flexbox e Grid)
- JavaScript (ES6+)
- LocalStorage API

## 📄 Licença

Este projeto foi desenvolvido para fins educacionais como parte do trabalho de desenvolvimento.

---

**Desenvolvido com ❤️ para gestão de processos aéreos**

