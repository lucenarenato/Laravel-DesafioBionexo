# Desafio Bionexo feito com PHP Laravel | Desafio Bionexo - BeeCare

## Docker PHP, Mysql, PhpMyadmin e Selenium

<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## Imagens do projeto

<p align="center">
  <img src="2023-08-04_01-48.png"/>
</p>
<p align="center">
  <img src="2023-08-04_02-29_1.png"/>
</p>
<p align="center">
  <img src="2023-08-04_01-47.png"/>
</p>
<p align="center">
  <img src="2023-08-04_01-46.png"/>
</p>
<p align="center">
  <img src="2023-08-07_10-24.png"/>
</p>
<p align="center">
  <img src="2023-08-04_01-50.png"/>
</p>

## Requisitos
- Docker
- PHP 8.2
- composer
- MYSQL
- chromedriver

## instalação inicial

```bash
    composer install -o
    cp .env.example .env
```

## Run sail
```sh
    ./vendor/bin/sail up  
    ./vendor/bin/sail artisan migrate --seed
    ./vendor/bin/sail artisan jwt:secret
    ./vendor/bin/sail artisan key:generate
    ./vendor/bin/sail artisan config:cache
    ./vendor/bin/sail composer dumpautoload -o
    ./vendor/bin/sail artisan l5-swagger:generate
    ./vendor/bin/sail artisan route:list
    ./vendor/bin/sail down
```
> Refazer dados da tabela:
`./vendor/bin/sail artisan migrate:fresh --seed`

# Api Documentation Swagger

Executar o comando abaixo, após o preenchimento dos annotations
`./vendor/bin/sail artisan l5-swagger:generate`

**Acessar o SWAGGER(Documentação da API)**:
- http://laravel.test/api/documentation

## Selenium
> Point your WebDriver tests to http://localhost:4444
`http://localhost:4444/ui`
- password = secret.

## Caso tenha problema com docker sail
`docker-compose up -d`
> Swagger
`docker exec -i laravel-bionexo-laravel.test-1 php artisan l5-swagger:generate`

# Videos com demostração:
- https://drive.google.com/file/d/1LGx7AIcjXY_UJoHC2BQOFU-fUU18hUdo/view?usp=sharing
- https://drive.google.com/file/d/1jFutLrch2gea5hImNteQL7XEn8jJf5zd/view?usp=sharing

## Fontes de Estudo.
- https://github.com/php-webdriver/php-webdriver/wiki
- https://github.com/php-webdriver/php-webdriver/wiki/Chrome#installation
- https://github.com/seleniumhq/selenium
- https://github.com/seleniumhq/docker-selenium/#quick-start
- https://regex101.com/
- [Livro Expressões Regulares] - https://www.piazinho.com.br/

## Fontes do desafio
- https://testpages.herokuapp.com/styled/tag/table.html
- https://testpages.herokuapp.com/styled/basic-html-form-test.html
- https://testpages.herokuapp.com/styled/download/download.html
- https://testpages.herokuapp.com/styled/file-upload-test.html
