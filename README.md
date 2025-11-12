# AV2DAW - Projeto com Padrão MVC

Projeto referente à AV2 da disciplina de 3DAW com implementação do padrão arquitetural Model-View-Controller (MVC).

## Estrutura do Projeto

```
AV2DAW/
├── app/                          # Código da aplicação
│   ├── controllers/              # Controllers (lógica de controle)
│   │   └── Controller.php        # Classe base Controller
│   ├── models/                   # Models (lógica de dados)
│   │   └── Model.php             # Classe base Model
│   └── views/                    # Views (apresentação)
│       ├── home.php              # Página inicial
│       └── 404.php               # Página de erro 404
├── config/                       # Configurações
│   └── config.php                # Configuração de banco de dados
├── public/                       # Arquivos públicos (raiz do servidor web)
│   ├── index.php                 # Front Controller (ponto de entrada)
│   ├── css/                      # Folhas de estilo
│   │   └── style.css
│   ├── js/                       # Scripts JavaScript
│   │   └── script.js
│   └── images/                   # Imagens
├── Alucar.sql                    # Script do banco de dados
├── .htaccess                     # Reescrita de URLs (Apache)
└── README.md                     # Este arquivo
```

## Como Usar

### 1. Configurar Banco de Dados

Editar `config/config.php` com suas credenciais:
```php
$host = 'localhost';
$user = 'seu_usuario';
$password = 'sua_senha';
$database = 'alucar';
```

Importar o arquivo `Alucar.sql` no banco de dados.

### 2. Acessar a Aplicação

```
http://localhost/AV2DAW/public/index.php
```

Ou com reescrita de URLs habilitada:
```
http://localhost/AV2DAW/public/
```

### 3. Criar um Controller

Exemplo em `app/controllers/HomeController.php`:
```php
<?php
require_once APP_ROOT . '/app/controllers/Controller.php';

class HomeController extends Controller {
    public function index() {
        $data = [
            'title' => 'Home'
        ];
        $this->render('home', $data);
    }
}
?>
```

### 4. Criar um Model

Exemplo em `app/models/User.php`:
```php
<?php
require_once APP_ROOT . '/app/models/Model.php';

class User extends Model {
    protected $table = 'usuarios';
    
    public function getUserByEmail($email) {
        return $this->query("SELECT * FROM {$this->table} WHERE email = ?", [$email]);
    }
}
?>
```

### 5. Criar uma View

As views são incluídas em `app/views/` e podem usar variáveis passadas pelo controller:
```php
<?php
// $title está disponível na view se foi passada pelo controller
?>
<h1><?php echo $title; ?></h1>
```

## Componentes Principais

### Model
- Gerencia toda a lógica de dados
- Realiza operações no banco de dados
- Herda da classe base `Model`

### View
- Responsável pela apresentação
- Contém HTML e PHP para exibição
- Recebe dados do Controller

### Controller
- Gerencia a lógica da aplicação
- Comunica entre Model e View
- Processa requisições do usuário
- Herda da classe base `Controller`

## Arquivos Importantes

| Arquivo | Descrição |
|---------|-----------|
| `public/index.php` | Front Controller - ponto de entrada |
| `config/config.php` | Configurações e conexão BD |
| `app/controllers/Controller.php` | Classe base para controllers |
| `app/models/Model.php` | Classe base para models |
| `.htaccess` | Reescrita de URLs |

## Tecnologias

- PHP 7.0+
- MySQL
- HTML5
- CSS3
- JavaScript

## Autor

Gabriel Couto e Jefferson Souza