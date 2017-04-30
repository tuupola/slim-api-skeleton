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


### Get a token

```
$ curl "https://192.168.50.52/token" \
    --request POST \
    --include \
    --insecure \
    --header "Content-Type: application/json" \
    --data '["todo.all"]' \
    --user test:test

HTTP/1.1 201 Created
Content-Type: application/json

{
    "token": "XXXXXXXXXX",
    "expires": 1491030210
}
```

### Create a new todo

```
$ curl "https://192.168.50.52/todos" \
    --request POST \
    --include \
    --insecure \
    --header "Authorization: Bearer $TOKEN" \
    --header "Content-Type: application/json" \
    --data '{ "title": "Test the API", "order": 10 }'

HTTP/1.1 201 Created
ETag: "c39de417d4d1f5fe22d19cad68d672d8"
Last-Modified: Sat, 16 Apr 2016 10:21:50 GMT
Location: /todos/12Cf2ZjVvyu3A
Content-Type: application/json

{
    "data": {
        "uid": "12Cf2ZjVvyu3A",
        "order": 10,
        "title": "Test the API",
        "completed": false,
        "links": {
            "self": "/todos/12Cf2ZjVvyu3A"
        }
    }
}
```

### Get an existing todo

```
$ curl "https://192.168.50.52/todos/12Cf2ZjVvyu3A" \
    --include \
    --insecure \
    --header "Authorization: Bearer $TOKEN"

HTTP/1.1 200 OK
ETag: "c39de417d4d1f5fe22d19cad68d672d8"
Last-Modified: Sat, 16 Apr 2016 10:21:50 GMT
Content-Type: application/json

{
    "data": {
        "uid": "12Cf2ZjVvyu3A",
        "order": 10,
        "title": "Test the API",
        "completed": false,
        "links": {
            "self": "/todos/12Cf2ZjVvyu3A"
        }
    }
}
```

### Update part of an existing todo

```
$ curl "https://192.168.50.52/todos/12Cf2ZjVvyu3A" \
    --request PATCH \
    --include \
    --insecure \
    --header "Authorization: Bearer $TOKEN" \
    --header "Content-Type: application/json" \
    --header 'If-Match: "c39de417d4d1f5fe22d19cad68d672d8"' \
    --data '{ "order": 27 }'

HTTP/1.1 200 OK
ETag: "ab6070930158fc8323aa4550aff438b7"
Last-Modified: Sat, 16 Apr 2016 10:27:16 GMT
Content-Type: application/json

{
    "data": {
        "uid": "12Cf2ZjVvyu3A",
        "order": 27,
        "title": "Test the API",
        "completed": false,
        "links": {
            "self": "/todos/12Cf2ZjVvyu3A"
        }
    }
}
```

### Fully update an existing todo

```
$ curl "https://192.168.50.52/todos/12Cf2ZjVvyu3A" \
    --request PUT \
    --include \
    --insecure \
    --header "Authorization: Bearer $TOKEN" \
    --header "Content-Type: application/json" \
    --header 'If-Match: "ab6070930158fc8323aa4550aff438b7"' \
    --data '{ "title": "Full update", "order": 66, "completed": true }'

HTTP/1.1 200 OK
ETag: "451665ea7769851880f411750bbd873c"
Last-Modified: Sat, 16 Apr 2016 10:28:45 GMT
Content-Type: application/json

{
    "data": {
        "uid": "12Cf2ZjVvyu3A",
        "order": 66,
        "title": "Full update",
        "completed": true,
        "links": {
            "self": "/todos/12Cf2ZjVvyu3A"
        }
    }
}
```

### Delete an existing todo

```
$ curl "https://192.168.50.52/todos/12Cf2ZjVvyu3A" \
    --request DELETE \
    --include \
    --insecure \
    --header "Authorization: Bearer $TOKEN"

HTTP/1.1 204 No Content
```

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.