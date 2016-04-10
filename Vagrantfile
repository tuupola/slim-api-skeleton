# Fix permissions after you run commands on both hosts and guest machine
system("
    if [ #{ARGV[0]} = 'up' ]; then
        echo 'Setting world write permissions for ./logs/*'
        chmod a+w ./logs
        chmod a+w ./logs/*
    fi
")

VAGRANTFILE_API_VERSION = "2"

Vagrant.configure(VAGRANTFILE_API_VERSION) do |config|
  config.vm.box = "puppetlabs/centos-6.6-64-nocm"
  config.vm.network "private_network", ip: "192.168.50.52"

  # Configure cached packages to be shared between instances of the same base box.
  # More info on http://fgrehm.viewdocs.io/vagrant-cachier/usage
  if Vagrant.has_plugin?("vagrant-cachier")
      config.cache.scope = :box
  end

  # Make sure logs folder will be writable for Apache
  config.vm.synced_folder "logs", "/vagrant/logs", owner: 48, group: 48

  # Install all needed packages
  config.vm.provision "shell", name: "rpm", inline: <<-SHELL
    rpm -Uvh https://mirror.webtatic.com/yum/el6/latest.rpm
    rpm -Uvh http://download.fedoraproject.org/pub/epel/6/x86_64/epel-release-6-8.noarch.rpm
  SHELL

  # PHP and modules
  config.vm.provision "shell", name: "php", inline: <<-SHELL
    yum -y install php56w php56w-opcache
    yum -y install php56w-pdo
    yum -y install php56w-mcrypt
    yum -y install php56w-mysqlnd
    yum -y install mod_ssl
    yum -y install php56w-xmlwriter
  SHELL

  # Use the provided example environment
  config.vm.provision "shell", name: "environment", inline: <<-SHELL
    cd /vagrant && cp .env.example .env
  SHELL

  # Install Composer and dependencies
  config.vm.provision "shell", name: "composer", inline: <<-SHELL
    curl -sS https://getcomposer.org/installer | php && mv composer.phar /usr/local/bin/composer
    cd /vagrant && /usr/local/bin/composer install
  SHELL

  # MySQL
  config.vm.provision "shell", name: "mysql", inline: <<-SHELL
    yum -y install mysql
    yum -y install mysql-server
    /etc/init.d/mysqld restart
    /sbin/chkconfig --levels 235 mysqld on

    echo "CREATE DATABASE example" | mysql -u root
    cd /vagrant && bin/db migrate
  SHELL

  # Update Apache config and restart
  config.vm.provision "shell", name: "apache", inline: <<-'SHELL'
    sed -i -e "s/DocumentRoot \"\/var\/www\/html\"/DocumentRoot \/vagrant\/public/" /etc/httpd/conf/httpd.conf
    # If you prefer Slim app to be in subfolder comment above and uncomment below
    #echo "Alias /foobar/ /vagrant/public/" >> /etc/httpd/conf/httpd.conf
    echo "EnableSendfile off" >> /etc/httpd/conf/httpd.conf
    sed -i -e "s/ErrorLog logs\/error_log/ErrorLog \/vagrant\/logs\/error_log/" /etc/httpd/conf/httpd.conf
    sed -i -e "s/CustomLog logs\/access_log/CustomLog \/vagrant\/logs\/access_log/" /etc/httpd/conf/httpd.conf
    sed -i -e "s/AllowOverride None/AllowOverride All/" /etc/httpd/conf/httpd.conf

    /etc/init.d/httpd restart
    /sbin/chkconfig --levels 235 httpd on

    # Make sure Apache also runs after vagrant reload
    echo "# Start Apache after /vagrant is mounted" > /etc/init/httpd.conf
    echo "start on vagrant-mounted" >> /etc/init/httpd.conf
    echo "exec /etc/init.d/httpd restart" >> /etc/init/httpd.conf
  SHELL

  # Stop iptable because it will cause too much confusion
  config.vm.provision "shell", name: "iptables", inline: <<-SHELL
    /etc/init.d/iptables stop
    /sbin/chkconfig iptables off
  SHELL

  # Install Grunt and npm dependencies
  #config.vm.provision "shell", name: "grunt", inline: <<-SHELL
  #  yum -y install npm
  #  npm install -g grunt-cli
  #  cd /vagrant && npm install
  #SHELL

  config.vm.post_up_message = <<MESSAGE

  ███████╗██╗     ██╗███╗   ███╗██████╗
  ██╔════╝██║     ██║████╗ ████║╚════██╗
  ███████╗██║     ██║██╔████╔██║ █████╔╝
  ╚════██║██║     ██║██║╚██╔╝██║ ╚═══██╗
  ███████║███████╗██║██║ ╚═╝ ██║██████╔╝
  ╚══════╝╚══════╝╚═╝╚═╝     ╚═╝╚═════╝

 You can access me at: http://192.168.50.52/todos

MESSAGE

end