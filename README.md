# Balance

O Balance é um pequeno sistema Web para controle de balancete contábil simples, com cadastro de contas, lançamentose visualização do balancete.

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
return array(
    'db' => array(
        'driver'   => 'pgsql',
        'database' => 'balance',
        'username' => 'balance',
        'password' => 'balance',
        'hostname' => 'localhost',
        'port'     => '5432',
        'charset'  => 'UNICODE',
    ),
);
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
