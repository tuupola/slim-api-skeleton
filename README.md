# Slim 3 API skeleton

[![Latest Version](https://img.shields.io/packagist/v/tuupola/slim-skeleton.svg?style=flat-square)](https://github.com/tuupola/slim-skeleton/releases)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)

This is Slim 3 API skeleton project for Composer. Project uses [Spot](http://phpdatamapper.com/) as persistence layer,  [Monolog](https://github.com/Seldaek/monolog) for logging, and [Fractal](http://fractal.thephpleague.com/) as serializer. [Vagrant](https://www.vagrantup.com/) virtualmachine config and [Paw](https://luckymarmot.com/paw) project files are included for easy development.

## Install

Install the latest version using [composer](https://getcomposer.org/).

``` bash
$ composer create-project --no-interaction --stability=dev tuupola/slim-api-skeleton app
```

## Usage

If you have [Vagrant](https://www.vagrantup.com/) installed start the virtual machine.

``` bash
$ cd app
$ vagrant up
```

Now you can access the api at [http://192.168.50.52/todos](http://192.168.50.52/todos)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.