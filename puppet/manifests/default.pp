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
