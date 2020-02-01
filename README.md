crypto-portfolio-api
==============

### FYI

It's always possible to find something to improve in code.
I could cover more code with tests, improve authentication and CRUD, create fixtures for users, create docker registry so you don't have to build containers, use travis for github, .env parameters with CI and so on, but I believe it's enough for job application.
If you insist more improvements, let me know.

### Issues
* https://github.com/doctrine/orm/issues/7999 
  So I solved it with nullable id for asset.

### To start project use following commands:
```sh
$ docker-compose build
$ docker-compose up -d
$ docker-compose exec crypto-php composer install
$ docker-compose exec crypto-php bin/console doctrine:migrations:migrate
$ docker-compose exec crypto-php bin/phpunit
```

Server should be reachable at http://127.0.0.1:8000


### Create asset

Every user has his own token.

http://127.0.0.1:8000/assets/create?token=3edec063b94637eb1cc4395aa5abab91 - POST method
```
{
    "label": "binance",
    "currency": "ETH",
    "value": 30
}
```

You will get your unique id in response.

### Get all user assets

http://127.0.0.1:8000/assets?token=3edec063b94637eb1cc4395aa5abab91 - GET method

### Get one user asset

Change your unique id with one that you got when created asset.

http://127.0.0.1:8000/assets/1770c7f2385a69e111dc1bf6adfba6e7?token=3edec063b94637eb1cc4395aa5abab91 - GET method

### Update asset

http://127.0.0.1:8000/assets/update/1770c7f2385a69e111dc1bf6adfba6e7?token=3edec063b94637eb1cc4395aa5abab91 - PUT method

### Delete asset

http://127.0.0.1:8000/assets/delete/1770c7f2385a69e111dc1bf6adfba6e7?token=3edec063b94637eb1cc4395aa5abab91 - DELETE method
