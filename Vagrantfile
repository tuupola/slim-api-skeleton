# Fix permissions after you run commands on both hosts and guest machine
if !Vagrant::Util::Platform.windows?
  system("
      if [ #{ARGV[0]} = 'up' ]; then
          echo 'Setting group write permissions for ./logs/*'
          chmod 775 ./logs
          chmod 664 ./logs/*
      fi
  ")
end

VAGRANTFILE_API_VERSION = "2"

Vagrant.configure(VAGRANTFILE_API_VERSION) do |config|
  config.vm.box = "puppetlabs/centos-7.2-64-nocm"
  config.vm.network "private_network", ip: "192.168.50.52"

  # Configure cached packages to be shared between instances of the same base box.
  # More info on http://fgrehm.viewdocs.io/vagrant-cachier/usage
  if Vagrant.has_plugin?("vagrant-cachier")
      config.cache.scope = :box
  end

  # Make sure logs folder is owned by apache with group vagrant
  config.vm.synced_folder "logs", "/vagrant/logs", owner: 48, group: 500

  # Install all needed packages
  config.vm.provision "shell", name: "rpm", inline: <<-'SHELL'
    yum -y install epel-release
    yum -y install yum-utils
    yum -y install https://rpms.remirepo.net/enterprise/remi-release-7.rpm
    rpm -Uvh https://mirror.ghettoforge.org/distributions/gf/gf-release-latest.gf.el7.noarch.rpm
  SHELL

  # PHP and modules
  config.vm.provision "shell", name: "php", inline: <<-'SHELL'
    yum-config-manager --enable remi-php80
    yum -y install php
    yum -y install php-intl
    yum -y install php-mbstring
    yum -y install php-mysqlnd
    yum -y install php-pdo
    #yum -y install php-xml
    yum -y install mod_ssl
  SHELL

  # Install basic tools
  config.vm.provision "shell", name: "tools", inline: <<-'SHELL'
    yum -y install zsh
    yum -y install telnet
    yum -y install screen
  SHELL

  # Use the provided example environment
  config.vm.provision "shell", name: "environment", inline: <<-'SHELL'
    cd /vagrant && cp .env.example .env
  SHELL

  # # Install Composer and dependencies
  # config.vm.provision "shell", name: "composer", inline: <<-SHELL
  #   curl -sS https://getcomposer.org/installer | php && mv composer.phar /usr/local/bin/composer
  #   cd /vagrant && /usr/local/bin/composer install
  # SHELL

  # MariaDB
  config.vm.provision "shell", name: "mariadb", inline: <<-'SHELL'
    cat <<EOF | sudo tee /etc/yum.repos.d/mariadb.repo
[mariadb]
name = MariaDB
baseurl = http://yum.mariadb.org/10.0/centos7-amd64
gpgkey=https://yum.mariadb.org/RPM-GPG-KEY-MariaDB
gpgcheck=1
EOF
      yum -y install MariaDB-server
      yum -y install MariaDB-client
      usermod --append --groups vagrant mysql
      cat <<EOF | sudo tee /etc/my.cnf.d/server.cnf
[mysqld]
general-log
general-log-file=/vagrant/logs/query_log
log-output=file
EOF
    systemctl restart mysql
    systemctl enable mysql
  SHELL

  config.vm.provision "shell", name: "migrate", inline: <<-'SHELL'
    echo "CREATE DATABASE development_db" | mysql -u root
    cd /vagrant && vendor/bin/phinx migrate
    cd /vagrant && vendor/bin/phinx seed:run
  SHELL

  # Update Apache config and restart
  config.vm.provision "shell", name: "apache", inline: <<-'SHELL'
    sed -i -e "s/DocumentRoot \"\/var\/www\/html\"/DocumentRoot \/vagrant\/public/" /etc/httpd/conf/httpd.conf
    # If you prefer Slim app to be in subfolder comment above and uncomment below
    #echo "Alias /api/ /vagrant/public/" >> /etc/httpd/conf/httpd.conf
    echo "EnableSendfile off" >> /etc/httpd/conf/httpd.conf
    echo '<Directory "/vagrant">' >> /etc/httpd/conf/httpd.conf
    echo '    AllowOverride All' >> /etc/httpd/conf/httpd.conf
    echo '    Require all granted' >> /etc/httpd/conf/httpd.conf
    echo '</Directory>' >> /etc/httpd/conf/httpd.conf
    sed -i -e "s/ErrorLog logs\/error_log/ErrorLog \/vagrant\/logs\/error_log/" /etc/httpd/conf/httpd.conf
    sed -i -e "s/CustomLog logs\/access_log/CustomLog \/vagrant\/logs\/access_log/" /etc/httpd/conf/httpd.conf
    sed -i -e "s/AllowOverride None/AllowOverride All/" /etc/httpd/conf/httpd.conf
    sed -i -e "s/ErrorLog logs\/ssl_error_log/ErrorLog \/vagrant\/logs\/error_log/" /etc/httpd/conf.d/ssl.conf
    sed -i -e "s/TransferLog logs\/ssl_access_log/TransferLog \/vagrant\/logs\/access_log/" /etc/httpd/conf.d/ssl.conf
    # Restart Apache for the first time
    systemctl restart httpd
    systemctl enable httpd
  SHELL

  # Stop firewalld because it will cause too much confusion
  config.vm.provision "shell", name: "firewalld", inline: <<-'SHELL'
    systemctl stop firewalld
    systemctl disable firewalld
  SHELL

  # Make sure Apache and also runs after vagrant reload
  config.vm.provision "shell", run: "always", name: "force-start-services", inline: <<-'SHELL'
    systemctl restart httpd
  SHELL

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