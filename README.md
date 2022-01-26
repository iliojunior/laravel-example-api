# Laravel simple test
## Stack
 - Laravel 8.75
 - PHP 8.1.2
 - PHP Unit 9.5.10
 - Docker
 - Docker-Compose
 
## How to run
Copy .env.example to .env

_Note: It is prepared to work directly with docker in project._

Install docker and docker-compose then run:
`docker-compose up -d`

So, **Bazinga!**

Now you can do requests (curl, postman...):
 - GET http://localhost:8080/api/companies/:cnpj
 - PUT http://localhost:8080/api/companies/:cnpj
 - DELETE http://localhost:8080/api/companies/:cnpj

## Install composer dependencies
I installed composer dependencies by docker.
`docker run --rm --interactive --tty -v $PWD:/app composer install`

## Run tests
With docker containers running
`docker exec -it app-php php artisan test`


Note: 

- These docker images was create by me on previous projects then i used here to earn time.

- These docker commands was use in Linux Ubuntu, may need some attention on windows
