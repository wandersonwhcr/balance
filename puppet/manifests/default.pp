exec { 'apt-get : update':
    path        => ['/usr/bin', '/usr/sbin', '/bin'],
    command     => 'apt-get update',
    refreshonly => false,
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
    require => [
        Package["nginx"],
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

service { "php : service":
    name    => "php5-fpm",
    ensure  => "running",
    require => [
         Package["php : fpm"],
    ],
}
