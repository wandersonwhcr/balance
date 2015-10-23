package { "apt-get : https":
    name => "apt-transport-https",
}

exec { 'apt-get : update':
    path        => ['/usr/bin', '/usr/sbin', '/bin'],
    command     => 'apt-get update',
    refreshonly => true,
}

# nginx

file { "nginx : list":
    path    => "/etc/apt/sources.list.d/nginx.list",
    content => "deb http://nginx.org/packages/debian/ wheezy nginx",
    notify  => [
        Exec["apt-get : update"],
    ],
}

exec { "nginx : key":
    path    => ['/usr/bin', '/usr/sbin', '/bin'],
    unless  => "apt-key list | grep nginx",
    command => "wget -O - http://nginx.org/keys/nginx_signing.key | apt-key add -",
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
    content => "deb http://apt.postgresql.org/pub/repos/apt/ wheezy-pgdg main",
}

exec { "postgresql : key":
    path    => ["/usr/bin", "/usr/sbin", "/bin"],
    unless  => "apt-key list | grep PostgreSQL",
    command => "wget -O - https://www.postgresql.org/media/keys/ACCC4CF8.asc | apt-key add -",
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
    content => "deb http://packages.dotdeb.org wheezy-php56 all",
    notify  => [
        Exec["apt-get : update"],
    ],
}

exec { "php : key":
    path    => ["/usr/bin", "/usr/sbin", "/bin"],
    unless  => "apt-key list | grep dotdeb",
    command => "wget -O - http://www.dotdeb.org/dotdeb.gpg | apt-key add -",
    notify  => [
        Exec["apt-get : update"],
    ],
}

package { "php : cli":
    name    => "php5-cli",
    require => [
        File["php : list"],
        Exec["php : key"],
    ],
    subscribe => [
        Exec["apt-get : update"],
    ],
}

package { "php : fpm":
    name    => "php5-fpm",
    require => [
        File["php : list"],
        Exec["php : key"],
    ],
    subscribe => [
        Exec["apt-get : update"],
    ],
}

service { "php":
    name    => "php5-fpm",
    ensure  => "running",
    require => [
        Package["php : fpm"],
    ],
}

package { "php : postgresql":
    name    => "php5-pgsql",
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
    command => "wget -O /usr/bin/composer  https://getcomposer.org/composer.phar && chmod +x /usr/bin/composer"
}

exec { "composer : update":
    path        => ["/usr/bin", "/usr/sbin", "/bin"],
    command     => "composer self-update",
    environment => [
        ["COMPOSER_HOME=/root/.composer"],
    ],
    require     => [
        Exec["composer"],
        Package["php : cli"],
    ],
}

# nodejs

file { "nodejs : list":
    path    => "/etc/apt/sources.list.d/nodejs.list",
    content => "deb https://deb.nodesource.com/node wheezy main",
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
    command => "wget -O - https://deb.nodesource.com/gpgkey/nodesource.gpg.key | apt-key add -",
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

# balance

exec { "balance : composer":
    path        => ["/usr/bin", "/usr/sbin", "/bin"],
    command     => "composer install",
    user        => "vagrant",
    timeout     => 0,
    cwd         => "/vagrant",
    environment => [
        ["COMPOSER_HOME=/home/vagrant/.composer"],
    ],
    require => [
        Exec["composer : update"],
    ],
}

# balance user db

exec { "balance : dbuser":
    path    => ["/usr/bin", "/usr/sbin", "/bin"],
    command => "psql -tAc \"CREATE ROLE balance LOGIN PASSWORD 'balance'\"",
    unless  => "psql -tAc \"SELECT COUNT(1) FROM pg_roles WHERE rolname = 'balance'\" | grep '.'",
    user    => "postgres",
    require => [
        Package["postgresql"]
    ]
}

# balance db

exec { "balance : dbname":
    path    => ["/usr/bin", "/usr/sbin", "/bin"],
    command => "psql -tAc \"CREATE DATABASE balance WITH OWNER balance ENCODING = 'UTF8' TEMPLATE = template0 \"",
    unless  => "psql -tAc \"SELECT COUNT(1) FROM pg_database WHERE datname = 'balance'\" | grep '.'",
    user    => "postgres",
    require => [
        Exec["balance : dbuser"]
    ]
}
