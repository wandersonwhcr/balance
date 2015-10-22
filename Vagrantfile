Vagrant.configure(2) do |config|
    config.vm.box         = 'puppetlabs/debian-7.8-32-puppet'
    config.vm.box_version = '1.0.2'

    config.vm.network 'forwarded_port', guest: 80, host: 8000

    config.vm.provision 'puppet' do |puppet|
        puppet.manifests_path = 'puppet/manifests'
        puppet.options        = '--verbose --debug'
    end
end
