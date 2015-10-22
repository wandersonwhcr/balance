exec { 'apt-get : update':
    path        => ['/usr/bin', '/usr/sbin', '/bin'],
    command     => 'apt-get update',
    refreshonly => false,
}
