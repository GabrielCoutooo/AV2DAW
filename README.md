# AV2DAW – Sistema de Locação de Veículos

Este projeto é uma solução completa para gestão de locação de veículos, desenvolvido para a disciplina de 3DAW. Ele utiliza PHP, MySQL, HTML, CSS e JavaScript, com organização modular e APIs RESTful para integração entre frontend e backend.

## Visão Geral

O sistema permite:

- Cadastro, autenticação e gerenciamento de administradores e clientes
- Cadastro e gerenciamento de veículos, categorias e filiais
- Processo de locação, devolução, check-in/check-out e controle de status dos veículos
- Registro de ocorrências, promoções e pagamentos
- Interface administrativa e área do cliente, com painéis, formulários e modais interativos

## Estrutura do Projeto

```
AV2DAW/
├── config/           # Configurações e conexão com o banco
├── public/           # Arquivos públicos (index, APIs, assets)
│   ├── api/          # Endpoints REST (adm e client)
│   ├── css/          # Estilos
│   ├── js/           # Scripts JavaScript
│   └── images/       # Imagens e uploads
├── views/            # Páginas HTML/PHP (admin e cliente)
├── Alucar.sql        # Script de criação e popularização do banco
├── README.md         # Este arquivo
```

## Instalação e Uso

1. **Banco de Dados**

   - Importe o arquivo `Alucar.sql` em seu MySQL/MariaDB.
   - O banco será criado com todas as tabelas, índices, triggers e dados de exemplo.

2. **Configuração**

   - Edite `config/config.php` com as credenciais do seu banco de dados.
   - Ajuste `BASE_URL` se necessário para refletir o caminho do seu servidor local.

3. **Execução**
   - Acesse via navegador:  
      `http://localhost/AV2DAW/public/`
   - O sistema está pronto para uso, com áreas separadas para administradores e clientes.

## Funcionalidades Principais

- **Administração**

  - Cadastro e login de administradores
  - Gerenciamento de veículos, categorias, filiais e vendedores
  - Painel de controle com visão geral das locações e status dos veículos
  - CRUD completo via modais e formulários dinâmicos

- **Cliente**

  - Cadastro, login e atualização de perfil
  - Consulta de veículos disponíveis, simulação e realização de locações
  - Pagamento, acompanhamento de reservas e registro de ocorrências

- **APIs REST**

  - Endpoints organizados em `public/api/adm/` e `public/api/client/`
  - Retorno sempre em JSON, com tratamento robusto de erros

- **Banco de Dados**
  - Estrutura relacional completa, com triggers para sincronização automática de status
  - Tabelas para admin, cliente, veículo, locação, pagamento, promoção, entre outras

## Tecnologias Utilizadas

- **Backend:** PHP 7+ (procedural e orientado a objetos)
- **Frontend:** HTML5, CSS3, JavaScript puro
- **Banco de Dados:** MySQL/MariaDB
- **Extras:** Triggers SQL, autenticação por sessão, tratamento de erros global

## Organização dos Códigos

- **config/**: Parâmetros de conexão, constantes globais e sessão segura
- **public/api/**: Lógica de negócio exposta via endpoints RESTful
- **public/js/**: Scripts para interatividade, validação e requisições AJAX
- **views/**: Interfaces para administradores e clientes, separadas por contexto
- **Alucar.sql**: Criação e popularização do banco, incluindo triggers e exemplos

## Créditos

Desenvolvido por Gabriel Couto e Jefferson Souza para a disciplina de 3DAW.

---
