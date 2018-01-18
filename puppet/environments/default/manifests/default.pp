exec { 'apt-get : update secure':
    path    => ['/usr/bin', '/usr/sbin', '/bin'],
    command => 'apt-get update',
}

package { "apt-get : https":
    name      => "apt-transport-https",
    subscribe => [
        Exec["apt-get : update secure"],
    ],
}

exec { 'apt-get : update':
    path    => ['/usr/bin', '/usr/sbin', '/bin'],
    command => 'apt-get update',
}

# nginx

file { "nginx : list":
    path    => "/etc/apt/sources.list.d/nginx.list",
    content => "deb http://nginx.org/packages/debian stretch nginx",
    notify  => [
        Exec["apt-get : update"],
    ],
}

exec { "nginx : key":
    path    => ['/usr/bin', '/usr/sbin', '/bin'],
    unless  => "apt-key list | grep nginx",
    command => "wget -O- http://nginx.org/keys/nginx_signing.key | apt-key add -",
    notify  => [
        Exec["apt-get : update"],
    ],
}

package { "nginx":
    name    => "nginx",
    require => [
        File["nginx : list"],
        Exec["nginx : key"],
    ],
    subscribe => [
        Exec["apt-get : update"],
    ],
}

service { "nginx":
    ensure  => "running",
    enable  => true,
    require => [
        Package["nginx"],
    ],
}

file { "nginx : conf":
    path    => "/etc/nginx/nginx.conf",
    source  => "puppet:///modules/archives/nginx_conf",
    require => [
        Package["nginx"],
    ],
    notify => [
        Service["nginx"],
    ],
}

file { "nginx : fastcgi":
    path    => "/etc/nginx/fastcgi_params",
    source  => "puppet:///modules/archives/nginx_fastcgi",
    require => [
        Package["nginx"],
    ],
    notify => [
        Service["nginx"],
    ],
}

file { "nginx : default":
    ensure  => absent,
    path    => "/etc/nginx/conf.d/default.conf",
    require => [
        Package["nginx"],
    ],
    notify => [
        Service["nginx"],
    ],
}

file { "nginx : virtualhost":
    path    => "/etc/nginx/conf.d/10-balance.conf",
    source  => "puppet:///modules/archives/nginx_virtualhost",
    require => [
        Package["nginx"],
    ],
    notify => [
        Service["nginx"],
    ],
}

# postgresql

file { "postgresql : list":
    path    => "/etc/apt/sources.list.d/postgresql.list",
    content => "deb http://apt.postgresql.org/pub/repos/apt stretch-pgdg main",
}

exec { "postgresql : key":
    path    => ["/usr/bin", "/usr/sbin", "/bin"],
    unless  => "apt-key list | grep PostgreSQL",
    command => "wget -O- https://www.postgresql.org/media/keys/ACCC4CF8.asc | apt-key add -",
}

package { "postgresql":
    name    => "postgresql",
    require => [
        File["postgresql : list"],
        Exec["postgresql : key"],
    ],
    subscribe => [
        Exec["apt-get : update"],
    ],
}

# php

file { "php : list":
    path    => "/etc/apt/sources.list.d/php.list",
    content => "deb https://packages.sury.org/php stretch main",
    notify  => [
        Exec["apt-get : update"],
    ],
}

exec { "php : key":
    path    => ["/usr/bin", "/usr/sbin", "/bin"],
    creates => "/etc/apt/trusted.gpg.d/php.gpg",
    command => "wget -O /etc/apt/trusted.gpg.d/php.gpg https://packages.sury.org/php/apt.gpg",
    notify  => [
        Exec["apt-get : update"],
    ],
}

package { "php : cli":
    name    => "php5.6-cli",
    require => [
        File["php : list"],
        Exec["php : key"],
    ],
    subscribe => [
        Exec["apt-get : update"],
    ],
}

package { "php : fpm":
    name    => "php5.6-fpm",
    require => [
        File["php : list"],
        Exec["php : key"],
    ],
    subscribe => [
        Exec["apt-get : update"],
    ],
}

file { "php : timezone":
    path    => "/etc/php/5.6/mods-available/timezone.ini",
    content => "date.timezone = \"America/Sao_Paulo\"",
    require => [
        Package["php : cli"],
        Package["php : fpm"],
    ],
}

service { "php":
    name    => "php5.6-fpm",
    ensure  => "running",
    require => [
        Package["php : fpm"],
    ],
}

file { "php : cli : timezone":
    ensure  => link,
    path    => "/etc/php/5.6/cli/conf.d/99-timezone.ini",
    target  => "/etc/php/5.6/mods-available/timezone.ini",
    require => [
        File["php : timezone"],
    ],
}

file { "php : fpm : timezone":
    ensure  => link,
    path    => "/etc/php/5.6/fpm/conf.d/99-timezone.ini",
    target  => "/etc/php/5.6/mods-available/timezone.ini",
    require => [
        File["php : timezone"],
    ],
    notify  => [
        Service["php"],
    ],
}

package { "php : postgresql":
    name    => "php5.6-pgsql",
    require => [
        Package["php : cli"],
        Package["php : fpm"],
    ],
    notify  => [
        Service["php"],
    ],
    subscribe => [
        Exec["apt-get : update"],
    ],
}

package { "php : xml":
    name    => "php5.6-xml",
    require => [
        Package["php : cli"],
        Package["php : fpm"],
    ],
    notify  => [
        Service["php"],
    ],
    subscribe => [
        Exec["apt-get : update"],
    ],
}

package { "php : intl":
    name    => "php5.6-intl",
    require => [
        Package["php : cli"],
        Package["php : fpm"],
    ],
    notify  => [
        Service["php"],
    ],
    subscribe => [
        Exec["apt-get : update"],
    ],
}

package { "php : xdebug":
    name    => "php-xdebug",
    require => [
        Package["php : cli"],
        Package["php : fpm"],
    ],
    notify  => [
        Service["php"],
    ],
    subscribe => [
        Exec["apt-get : update"],
    ],
}

# composer

exec { "composer":
    path    => ["/usr/bin", "/usr/sbin", "/bin"],
    creates => "/usr/bin/composer",
    command => "wget https://getcomposer.org/composer.phar -O /usr/bin/composer && chmod +x /usr/bin/composer"
}

exec { "composer : update":
    path        => ["/usr/bin", "/usr/sbin", "/bin"],
    command     => "composer self-update",
    environment => "COMPOSER_HOME=/root/.composer",
    require     => [
        Exec["composer"],
        Package["php : cli"],
    ],
}

# apigen

exec { "apigen":
    path    => ["/usr/bin", "/usr/sbin", "/bin"],
    creates => "/usr/bin/apigen",
    command => "wget -O- http://www.apigen.org/apigen.phar -o /usr/bin/apigen && chmod +x /usr/bin/apigen"
}

# nodejs

file { "nodejs : list":
    path    => "/etc/apt/sources.list.d/nodejs.list",
    content => "deb https://deb.nodesource.com/node_6.x stretch main",
    require => [
        Package["apt-get : https"],
    ],
    notify  => [
        Exec["apt-get : update"],
    ],
}

exec { "nodejs : key":
    path    => ["/usr/bin", "/usr/sbin", "/bin"],
    unless  => "apt-key list | grep nodesource",
    command => "wget -O- https://deb.nodesource.com/gpgkey/nodesource.gpg.key | apt-key add -",
    notify  => [
        Exec["apt-get : update"],
    ]
}

package { "nodejs":
    name      => "nodejs",
    subscribe => [
        Exec["apt-get : update"],
    ],
}

# bower

exec { "bower":
    path    => ["/usr/bin", "/usr/sbin"],
    creates => "/usr/bin/bower",
    command => "npm install -g bower",
    require => [
        Package["nodejs"],
    ],
}

# git

package { "git":
    name      => "git",
    subscribe => [
        Exec["apt-get : update"],
    ],
}

# phppgadmin

exec { "phppgadmin":
    path    => ["/usr/bin", "/usr/sbin", "/bin"],
    creates => "/usr/share/phppgadmin",
    command => "mkdir /usr/share/phppgadmin && wget http://downloads.sourceforge.net/phppgadmin/phpPgAdmin-5.1.tar.gz?download -O - | tar xzf - --strip 1 --directory /usr/share/phppgadmin",
}

file { "phppgadmin : virtualhost":
    path    => "/etc/nginx/conf.d/20-phppgadmin.conf",
    source  => "puppet:///modules/archives/phppgadmin_virtualhost",
    require => [
        Package["nginx"],
        Exec["phppgadmin"],
    ],
    notify => [
        Service["nginx"],
    ],
}

# balance

exec { "balance : composer":
    path        => ["/usr/bin", "/usr/sbin", "/bin"],
    command     => "composer install",
    user        => "vagrant",
    timeout     => 0,
    cwd         => "/vagrant",
    environment => "COMPOSER_HOME=/home/vagrant/.composer",
    require => [
        Package["php : postgresql"],
        Package["php : intl"],
        Package["php : xdebug"],
        Exec["composer : update"],
    ],
}

exec { "balance : dbuser":
    path    => ["/usr/bin", "/usr/sbin", "/bin"],
    command => "psql -tAc \"CREATE ROLE balance LOGIN PASSWORD 'balance'\"",
    unless  => "psql -tAc \"SELECT 1 FROM pg_roles WHERE rolname = 'balance'\" | grep '.'",
    user    => "postgres",
    require => [
        Package["postgresql"]
    ]
}

exec { "balance : dbname":
    path    => ["/usr/bin", "/usr/sbin", "/bin"],
    command => "psql -tAc \"CREATE DATABASE balance WITH OWNER balance ENCODING = 'UTF8' TEMPLATE = template0\"",
    unless  => "psql -tAc \"SELECT 1 FROM pg_database WHERE datname = 'balance'\" | grep '.'",
    user    => "postgres",
    require => [
        Exec["balance : dbuser"]
    ]
}

exec { "balance : phinx":
    path    => ["/usr/bin", "/usr/sbin", "/bin"],
    command => "php vendor/bin/phinx migrate",
    user    => "vagrant",
    cwd     => "/vagrant",
    require => [
        Exec["balance : dbname"]
    ],
}

exec { "balance : bower":
    path        => ["/usr/bin", "/usr/sbin", "/bin"],
    command     => "bower install",
    user        => "vagrant",
    timeout     => 0,
    cwd         => "/vagrant",
    environment => "HOME=.",
    require     => [
        Exec["bower"],
    ],
}
