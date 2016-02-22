# Slim 3 API skeleton

[![Latest Version](https://img.shields.io/packagist/v/tuupola/slim-skeleton.svg?style=flat-square)](https://github.com/tuupola/slim-skeleton/releases)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)

This is Slim 3 API skeleton project for Composer. Project uses [Spot](http://phpdatamapper.com/) as persistence layer,  [Monolog](https://github.com/Seldaek/monolog) for logging, and [Fractal](http://fractal.thephpleague.com/) as serializer. [Vagrant](https://www.vagrantup.com/) virtualmachine config and [Paw](https://geo.itunes.apple.com/us/app/paw-http-rest-client/id584653203?mt=12&at=1010lc2t) project files are included for easy development.

## Install

Install the latest version using [composer](https://getcomposer.org/).

_IMPORTANT_: Install the PHP extension GMP for Calcs for generation of the token. php5-gmp for PHP5 and php-gmp for PHP7

``` bash
$ composer create-project --no-interaction --stability=dev tuupola/slim-api-skeleton app
```

## Usage

If you have [Vagrant](https://www.vagrantup.com/) installed start the virtual machine.

``` bash
$ cd app
$ vagrant up
```

Now you can access the api at [https://192.168.50.52/todos](https://192.168.50.52/todos)

```
$ curl "https://192.168.50.52/token"
    --include
    --insecure
    --header "Content-Type: application/json"
    --data '["todo.all"]'
    --user test:test

HTTP/1.1 201 Created
Content-Type: application/json

{
    "status": "ok",
    "token": "XXXXXXXXXX"
```

```
$ curl "https://192.168.50.52/todos"
    --include
    --insecure
    --header "Authorization: Bearer XXXXXXXXXX"
    --header "Content-Type: application/json"
    --data '{ "title": "Test the API", "order": 10 }'

HTTP/1.1 201 Created
Location: /todos/LwsIahyOYhp0g
Content-Type: application/json

{
    "data": {
        "uid": "LwsIahyOYhp0g",
        "order": 10,
        "title": "Test the API",
        "completed": false,
        "links": {
            "self": "/todos/LwsIahyOYhp0g"
        }
    },
    "status": "ok",
    "message": "New todo created"
}
```

```
$ curl "https://192.168.50.52/todos/LwsIahyOYhp0g"
    --include
    --insecure
    --header "Authorization: Bearer XXXXXXXXXX"
    --header "Content-Type: application/json"

HTTP/1.1 200 OK
ETag: "2ae6e2d14b7ad7754f34055d4aa54a13"
Last-Modified: Sun, 21 Feb 2016 01:40:20 GMT
Content-Type: application/json

{
    "data": {
        "uid": "LwsIahyOYhp0g",
        "order": 10,
        "title": "Test the API",
        "completed": false,
        "links": {
            "self": "/todos/LwsIahyOYhp0g"
        }
    },
    "status": "ok"
}
```

```
$ curl "https://192.168.50.52/todos/LwsIahyOYhp0g"
    --request DELETE
    --include
    --insecure
    --header "Authorization: Bearer XXXXXXXXXX"
    --header "Content-Type: application/json"

HTTP/1.1 200 OK
Content-Type: application/json

{
    "status": "ok",
    "message": "Todo deleted"
}
```

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.