This project was created purely for testing. In this regard, a file with “secret” information and so on was added. (The information is not secret, but test information. So anyone can use it.)

# testingRun:
```
docker-compose up -d
docker exec -it composer_app bash
composer install
composer test
```