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
        Exec["apt-get : update"],
    ],
}

package { "php : fpm":
    name    => "php5-fpm",
    require => [
        File["php : list"],
        Exec["php : key"],
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
