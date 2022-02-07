# Payment Api

## Objetivo

- Deve existir dois tipos de usuários, normais e lojistas. É necessário que os usuários possuam Nome Completo, CPF, e-mail
e senha. CPF/CNPJ e e-mails devem ser unicos no sistema. Deste modo, seu sistema deve permitir apenas um cadastro com o mesmo CPF ou
e-mail.

- Usuários normais podem enviar e receber dinheiro para lojistas e outros usuários.

- Lojistas só recebem dinheiro, não podem realizar envio de dinheiro par ninguém.

- Validar se o usuário tem saldo antes da transferência

- Consultar serviço autorizador externo para validar a transferência.

- A operação de transferência deve ser uma transação, ou seja, pode ser revertida em caso de inconsistência e o dinheiro deve voltar
para a carteira do usuário que envia.

- No recebimento do pagamento o usuário que recebeu dinheiro deve receber uma notificação enviada por um serviço de terceiro,
que pode estar disponível/indisponível.

- Este serviço deve ser RESTFull

## Dependencias
- Docker

## Tecnologias
- PHP
- Laravel
- MySQL

## Iniciando o projeto

### Clonar Repositório
```bash
git clone https://github.com/HnkAlbuquerque/payment-api-app.git
```

### Antes de rodar o docker certifique que você tenha as seguintes portas disponíveis em seu ambiente
```bash
NGINX: 7000
MYSQL: 9306
PHP: 9004
```

### Executar o docker
```bash
docker-compose up -d --build
```

### Rode o composer para instalar as dependencias
```bash
docker-compose exec php composer install
```

### Arquivo de ambiente
```bash
docker-compose exec php cp .env.example .env
```

### Configure a conexão com o banco de dados no seu .env
```bash
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=db_app
DB_USERNAME=db_app
DB_PASSWORD=root
```

### Aplicações laravel precisam de uma chave de aplicação
```bash
docker-compose exec php php artisan key:generate
```

### Execute as migrations
```bash
docker-compose exec php php artisan migrate
```

### Popule o banco de dados para realizar transações
```bash
docker-compose exec php php artisan db:seed DatabaseSeeder
```

### Execute os testes
```bash
docker-compose exec php php artisan test
```

## Sobre a API
### Transação
Faça uma requisição `POST` para `api/fire-transaction`

### Payload 

Informe um payload JSON no seguinte formato abaixo
```json
{
    "payer" : 15,
    "payee" : 20,
    "value" : 5.00
}
```
`Payer` -> ID do usuário pagador
`Payee` -> ID do usuário beneficiário
`value` -> Valor da transação

### Resposta do Payload
Se tudo ocorrer bem será retornado a resposta abaixo
```json
{
    "data": {
        "id": "bb21adf5-fb3f-4b2f-8108-5920bcb61eba",
        "value": 20,
        "created_at": "2022-02-05T01:56:46.000000Z",
        "payer": {
            "id": 112,
            "name": "Prof. Nicholaus Murray II",
            "email": "summer28@example.com",
            "type": "client"
        },
        "payee": {
            "id": 95,
            "name": "Hope Koelpin",
            "email": "lela.konopelski@example.org",
            "type": "shopkeeper"
        }
    }
}
```

- `id` -> ID da transação.
- `value` -> Valor pago.
- `created_at` -> Data de criação da transação.

Pagador
- `payer`
  - `id` -> ID do pagador
  - `name` -> Nome do pagador
  - `email` -> E-mail do pagador
  - `type` -> Tipo do usuário `client` ou `shopkeeper` (porém o pagador por regra sempre será `client`)

Beneficiário
- `payer`
  - `id` -> iD do beneficiário
  - `name` -> Nome do beneficiário
  - `email` -> E-mail do pagador
  - `type` -> Tipo do usuário `client` ou `shopkeeper`

### Em caso de ERROS
Erros são retornados no formato abaixo onde `message` poderá ser mais de uma
```json
{
    "errors": {
        "message": "error message"
    }
}
```

### Retornar apenas uma transação
Faça uma requisição `GET` para `api/filter-transaction/{trasaction}` onde `{transaction}` é o ID da transação.

### Retornar todas as transações
Faça uma requisição `GET` para `api/transactions`.

### Usuários
### Criar um usuário aleatório
Faça uma requisição `GET` para `api/users/create`.

### Retornar apenas um usuário
Faça uma requisição `GET` para `api/filter-user/{user}` onde `{user}` é o ID do usuário.

### Retornar todos os usuários
Faça uma requisição `GET` para `api/users`.

### Adicionando dinheiro na carteira de um usuário
Os usuários são criados com 0 de saldo em suas carteiras.
Faça uma requisição `POST` para `api/plus-money` com o formato JSON abaixo.
```json
{
    "user_id": 50,
    "value" : 20.05
}
```

`user_id`-> ID do usuário
`value` -> Valor que deseja adicionar

### Resposta do depósito
```json
{
    "success": {
        "message": "Successfully deposited amount"
    }
}
```
