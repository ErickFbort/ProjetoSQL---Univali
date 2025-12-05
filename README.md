# ✈️ Sistema H&E - Gestão de Processos Aéreos

Sistema web completo para cadastro e gerenciamento de processos aéreos, desenvolvido como parte do projeto de desenvolvimento da Univali.

**Repositório:** [ProjetoSQL - Univali](https://github.com)

## 📋 Funcionalidades

- ✅ **Cadastro de Processos**: Registro completo de informações sobre processos aéreos
- 📝 **Edição de Processos**: Atualização de dados já cadastrados
- 🗑️ **Exclusão de Processos**: Remoção de processos com confirmação
- 🔍 **Busca e Filtragem**: Pesquisa rápida por qualquer campo do processo
- 💾 **Banco de Dados MySQL**: Integração completa com MySQL para persistência de dados
- 🔄 **API REST**: API PHP para operações CRUD completas
- 📱 **Design Responsivo**: Interface adaptável para diferentes tamanhos de tela
- 🎨 **Identidade Visual H&E**: Design moderno com tema azul escuro e laranja

## 🚀 Instalação e Configuração

### Pré-requisitos

- PHP 7.4+ 
- MySQL 8.0+
- Servidor web (Apache/Nginx) ou PHP built-in server

### Instalação Rápida

1. **Clonar o repositório:**
   ```bash
   git clone https://github.com/ErickFbort/ProjetoSQL---Univali.git
   cd ProjetoSQL---Univali
   ```

2. **Configurar o banco de dados:**
   ```bash
   ./configurar_banco.sh
   ```
   Ou manualmente:
   ```bash
   mysql -u root -p < database.sql
   ```

3. **Configurar credenciais:**
   Edite `api.php` (linhas 11-14) com suas credenciais MySQL:
   ```php
   define('DB_USER', 'root');
   define('DB_PASS', 'sua_senha');
   ```

4. **Iniciar o servidor:**
   ```bash
   ./iniciar.sh
   ```
   Ou manualmente:
   ```bash
   php -S localhost:8000
   ```

5. **Acessar o sistema:**
   Abra no navegador: `http://localhost:8000/index.html`

### Verificar Instalação

Execute o script de diagnóstico:
```bash
./verificar_instalacao.sh
```

## 💻 Como Usar

### Modo com MySQL (Recomendado)

1. Configure o banco de dados (veja Instalação acima)
2. Inicie o servidor PHP
3. Acesse via navegador

### Modo LocalStorage (Teste/Demo)

1. Abra `index.html` diretamente no navegador
2. Funciona sem servidor (dados salvos no navegador)
3. Perfeito para demonstração rápida

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

O sistema suporta dois modos de armazenamento:

### Modo MySQL (Recomendado)
- Dados persistidos em banco de dados MySQL
- API REST em PHP para operações CRUD
- Dados centralizados e seguros
- Veja `README_SQL.md` para detalhes

### Modo LocalStorage (Fallback)
- Funciona sem servidor para testes
- Dados salvos no navegador
- Perfeito para desenvolvimento e demonstração

## 🔧 Estrutura do Projeto

```
ProjetoSQL/
├── index.html              # Frontend principal
├── styles.css              # Estilos e design
├── script.js               # Lógica JavaScript e CRUD
├── api.php                 # API REST PHP
├── config.php              # Configuração do banco
├── database.sql            # Estrutura do banco MySQL
├── crud_queries.sql        # Queries SQL de exemplo
├── iniciar.sh              # Script de inicialização
├── configurar_banco.sh     # Script de configuração
├── verificar_instalacao.sh # Diagnóstico do sistema
├── README.md               # Este arquivo
└── README_SQL.md           # Documentação do banco
```

## 📝 Próximos Passos (Melhorias Futuras)

- [x] Integração com banco de dados (SQL) ✅
- [ ] Autenticação de usuários
- [ ] Exportação de dados (PDF, Excel)
- [ ] Relatórios e estatísticas
- [ ] Notificações por email
- [ ] Histórico de alterações
- [ ] Upload de documentos anexos

## 🛠️ Tecnologias Utilizadas

- **Frontend**: HTML5, CSS3, JavaScript (ES6+)
- **Backend**: PHP 7.4+
- **Banco de Dados**: MySQL 8.0+
- **API**: REST API com JSON
- **Design**: Identidade Visual H&E (Azul escuro + Laranja)

## 📄 Licença

Este projeto foi desenvolvido para fins educacionais como parte do trabalho de desenvolvimento.

---

**Desenvolvido com ❤️ para gestão de processos aéreos**

