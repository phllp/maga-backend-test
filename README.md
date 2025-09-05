# Backend Dev Test

## Pré Requisitos

- Docker
- NodeJS 18 >
- npm 9 >
- php 8.1.2
- compose

## Instruções de Configuração (com docker)

1. Configurar variáveis de ambiente

Renomeie o arquivo `.env.example` para `.env` e altere o valor das variáveis para os valores adequados. Para o ambiente de desenvolvimento nenhuma alteração no valor das variáveis é necessário.

2. Execute

```
docker compose up -d db

docker compose build app

docker compose up app

docker compose exec app php scripts/create_schema.php
```

A aplicação deve estar acessível em `localhost:8000`

## Instruções de Configuração (sem docker)

1. Configurar variáveis de ambiente

Renomeie o arquivo `.env.example` para `.env` e altere o valor das variáveis para os valores adequados. Para o ambiente de desenvolvimento nenhuma alteração no valor das variáveis é necessário.

2. Subir instância do banco de dados

Execute o comando `docker compose up -d` para subir a instância do postgres configurada no arquivo `docker-compose.yaml`

Com o banco rodando é possível executar as migrations com `php scripts/create_schema.php`. Também é possível executar as seeds com `php scripts/seed_pessoa.php`.

3. Instalar dependências - Compose

Execute o comando `composer install` para instalar as dependências do projeto.

4. Configurar o tailwindcss

No projeto é utilizada a lib de estilização `tailwindcss`, para utilizá-la é necessário executar o comando `npm i` e em seguida `npm run build`.

Para desenvolvimento execute `npm run dev` para que o css seja reestruturado de acordo com as atualizações feitas no código

5. Executar o servidor de desenvolvimento

Para iniciar o servidor execute o comando `php -S localhost:8000 -t public`

A aplicação deve estar acessível em `localhost:8000`
