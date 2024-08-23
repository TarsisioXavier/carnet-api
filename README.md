# API RESTful para Parcelas de Carnê  
API simples de exemplo de uma API para criação e visualização de carnês sem autenticação e autorização.  

## Ambiente  
O ambiente é servido usando Docker, se você não tiver o Docker instalado no seu SO siga as instruções [aqui](https://docs.docker.com/get-started/get-docker/) para instala-lo.  
Os microserviços estão separados pela seguinta faixa de IP:  
```
172.40.10.30 nginx
172.40.10.20 php-fpm
172.40.10.10 mysql
172.40.10.11 redis
172.40.10.12 mailhog
```

## Servindo o Projeto  
Siga os passos abaixo para servir o projeto.  
1. Clonar o repositório no seu local `git clone git@github.com:TarsisioXavier/carnet-api.git`.  
2. Entre no diretório criado e suba os contêineres do ambiente usando `docker-compose up -d`.  
3. Entre no container do PHP usando `docker exec -tiu app php-fpm83 sh`.  
4. Dentro do container, execute o comando `composer install` para instalar as dependências.  
5. A API estará acessível via `http://172.40.10.30/`.  
6. (Opicional): Execute `php artisan migrate:fresh --seed` para semear o banco de dados.  

A coleção do Postman se encontra em `docs/postman.json`, importe a coleção para o Postman e começe a interagir com a API.
