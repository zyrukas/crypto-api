crypto-portfolio-api
==============

### To start project use following commands:
```sh
$ docker-compose build
$ docker-compose up -d
$ docker-compose exec crypto-php composer install
$ docker-compose exec crypto-php vendor/bin/phpunit
$ docker-compose exec crypto-php bin/console doctrine:migrations:migrate
$ docker-compose exec crypto-php bin/console doctrine:fixtures:load
```

Server should be reachable at http://127.0.0.1:8000

Auth token generated from fixtures
X-AUTH-TOKEN: 6c4d3cbb569fa002d525b2302d4cf546

### Create asset

Every user has his own token.

http://127.0.0.1:8000/assets/create - POST method
```
{
    "label": "binance",
    "currency": "ETH",
    "value": 30
}
```

You will get your unique id in response.

### Get all user assets

http://127.0.0.1:8000/assets - GET method

### Get one user asset

Change your unique id with one that you got when created asset.

http://127.0.0.1:8000/assets/1770c7f2385a69e111dc1bf6adfba6e7 - GET method

### Update asset

http://127.0.0.1:8000/assets/1770c7f2385a69e111dc1bf6adfba6e7 - PUT method

### Delete asset

http://127.0.0.1:8000/assets/1770c7f2385a69e111dc1bf6adfba6e7 - DELETE method
