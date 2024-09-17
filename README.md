
### Pré-requisitos

Antes de iniciar, certifique-se de ter os seguintes itens instalados:

- [PHP](https://www.php.net/) 7.4 ou superior
- [Composer](https://getcomposer.org/)
- Servidor de banco de dados (MySQL, PostgreSQL, etc.)


#### Para inslatar o projeto basta seguir os passos a baixo:
Clone o projeto:
`> git clone https://github.com/ronaldovsneto/febacapital.git livraria`

Entre dentro da pasta clonada:
`> cd livraria`

Para instalar:
`> composer install`

#### Para configurar:

Configuração do banco de dados (**config/db.php**):

	return [
        'class' => 'yii\db\Connection',
        'dsn' => 'mysql:host=localhost;dbname=sua_base_de_dados',
        'username' => 'seu_usuario',
        'password' => 'sua_senha',
        'charset' => 'utf8',
    ];`

Após o banco de dados configurado, basta subir os migrations:
`> yii migrate`

Com a base de dados executada, basta criar um usuário para consumir os serviços feitos:
`> php yii user/create --username=<usuario> --password=<senha> --name=<nome>`

## **Observação**
Dentro da pasta **ext** foi adiciona um collection para consumir os serviços.

###### Serviços:
#### Login:
-- Rota: ***api/login***
-- Método: ***POST***
-- Body:


    {
      "username": "exemplo",
      "password": "senha123"
    }


####  Criar Cliente:
-- Rota: ***api/cliente***
-- Método: ***POST***
-- Body:


    {
      "name": "Royal's",
      "cpf": "98765432112",
      "zipcode": "58297000",
      "sex": "M",
      "neighborhood": "Centro"
    }

#### Listar Clientes:
-- Rota: ***api/cliente***
-- Método: ***GET***
-- Path Params:
- filter (string)
- orderBy (string)
- orderDirection (ASC ou DESC)
- limit (number)

#### Criar Livro:
-- Rota: ***api/livro***
-- Método: ***POST***
-- Body:


    {
      "isbn": "9788535922806",
      "title": "1234567891234",
      "author": "1234567891234",
      "price": 12.5,
      "inventory": 50
    }

#### Listar Livros:
-- Rota: ***api/livro***
-- Método: ***GET***
-- Path Params:
- filter (string)
- orderBy (string)
- orderDirection (ASC ou DESC)
- limit (number)
