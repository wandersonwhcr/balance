# Balance

O Balance é um pequeno sistema Web para controle de balancete contábil simples, com cadastro de contas, lançamentos e visualização do balancete.

[![Build Status](https://travis-ci.org/wandersonwhcr/balance.svg?branch=master)](https://travis-ci.org/wandersonwhcr/balance)
[![codecov.io](https://codecov.io/github/wandersonwhcr/balance/coverage.svg?branch=develop)](https://codecov.io/github/wandersonwhcr/balance?branch=develop)
[![Latest Stable Version](https://poser.pugx.org/wandersonwhcr/balance/v/stable)](https://packagist.org/packages/wandersonwhcr/balance)
[![Latest Unstable Version](https://poser.pugx.org/wandersonwhcr/balance/v/unstable)](https://packagist.org/packages/wandersonwhcr/balance)
[![Dependency Status](https://www.versioneye.com/user/projects/5669d59343cfea0028000180/badge.svg?style=flat)](https://www.versioneye.com/user/projects/5669d59343cfea0028000180)
[![Dependency Status](https://www.versioneye.com/user/projects/5669d59443cfea0031000172/badge.svg?style=flat)](https://www.versioneye.com/user/projects/5669d59443cfea0031000172)
[![License](https://poser.pugx.org/wandersonwhcr/balance/license)](https://packagist.org/packages/wandersonwhcr/balance)

O funcionamento básico pode ser encontrado na [Wiki](//github.com/wandersonwhcr/balance/wiki) do projeto.

Você pode contribuir com este projeto! Acesse o arquivo [`CONTRIBUTING.md`](//github.com/wandersonwhcr/balance/blob/master/CONTRIBUTING.md) e saiba mais.

## Instalação Rápida: Vagrant

Este projeto possui uma instalação rápida através do Vagrant, bastando copiar o arquivo de configuração `Vagrantfile` da distribuição e solicitar a criação máquina virtual.

```bash
cp Vagrantfile.dist Vagrantfile
vagrant up
```

O projeto estará acessível através de um navegador no endereço `http://localhost:8000`.

## Instalação Completa

O Balance é um sistema que utliza um servidor Web, PHP e banco de dados PostgreSQL para armazenamento das informações. A instalação deste projeto deve feita através do `composer` e `bower`.

### Requisitos Mínimos

* Servidor Web (Apache, Nginx, IIS);
* PHP 5.6 ou superior (inclusive PHP 7);
* PHP com Extensões `pgsql` e `intl`;
* PostgreSQL 9.4 ou superior; e
* Composer e Bower.


### Criação da Estrutura

A inicialização do projeto deve ser efetuada utilizando o `composer`.

```bash
composer create-project --no-dev wandersonwhcr/balance
```

Para criação do banco de dados, utilize os seguintes comandos em modo administrador.

```bash
psql -c "CREATE ROLE balance LOGIN PASSWORD 'balance'" -U postgres
psql -c "CREATE DATABASE balance WITH OwNER balance ENCODING = 'UTF8' TEMPLATE = template0" -U postgres
```

Lembre-se que você pode alterar o usuário e o banco de dados conforme a sua necessidade. Após, você precisa configurar o sistema para acessar o banco de dados corretamente, efetuando uma cópia das configurações globais para configurações locais.

```bash
cp config/autoload/config.global.php config/autoload/config.local.php
```

Após, edite o arquivo `config/autload/config.local.php`, informando as configurações de acesso ao banco de dados. O arquivo inicialmente terá a seguinte estrutura.

```php
<?php
return [
    'db' => [
        'driver'   => 'pgsql',
        'database' => 'balance',
        'username' => 'balance',
        'password' => 'balance',
        'hostname' => 'localhost',
        'port'     => '5432',
        'charset'  => 'UTF8',
    ],
];
```

O próximo passo é executar o `phinx` para que seja possível a criação da estrutura inicial do banco de dados.

```bash
php vendor/bin/phinx migrate
```

A instalação das dependências de visualização deverão ser feitas através do `bower` com o seguinte comando.

```bash
bower install
```

Para acesso ao sistema utilizando o servidor Web, precisamos criar um _alias_ de diretório no _virtualhost_ correspondente, direcionando todas os acessos que possuem o padrão `^/module/([a-zA-Z0-9]+)/(.*)$` para o diretório `/module/$1/public/$2`. Por exemplo, no Nginx, isto pode ser feito da seguinte forma:

```
server {
    listen       80;
    server_name  domain.example.com;
    root         /var/www/domain.example.com;
    index        index.php;

    location / {
        try_files $uri $uri/ /index.php$is_args$args;
    }

    location /module {
        rewrite ^/module/([a-zA-Z0-9]+)/(.*)$ /../module/$1/public/$2 break;
    }

    location ~ [^/]\.php(/|$) {
        fastcgi_split_path_info ^(.+?\.php)(/.*)$;

        fastcgi_pass  unix:/var/run/php5-fpm.sock;
        fastcgi_index index.php;
        include       fastcgi_params;
    }
}
```

## História

Quando eu estava cursando meu Técnico em Informática em 2005, desenvolvi um pequeno _software_ em Delphi que efetuava um balancete contábil simples, fruto de um trabalho final para a disciplina de Contabilidade. Depois de alguns meses, comecei a efetuar lançamentos contábeis utilizando papel e caneta, mantendo este trabalho por 6 anos. Em 2011, desenvolvi um aplicativo de código-fonte fechado para executar o mesmo trabalho que eu efetuava no papel, buscando, algum dia, disponibilizar o código.

Decidindo reescrevê-lo, no ano de 2015 eu resolvi adicionar todas as tecnologias que dominava no momento. Assim, criei este projeto para contribuir com a comunidade de software livre, buscando novos conhecimentos.

## Licença

Este projeto é _opensource_ e utiliza a Licença MIT, descrita no arquivo [`LICENSE`](//github.com/wandersonwhcr/balance/blob/master/LICENSE).
