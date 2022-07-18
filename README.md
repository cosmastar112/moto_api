# Тестовое задание API Аренды мотоциклов

## Зависимости
~~~
composer install
~~~

## Настройка окружения
### Docker
Развернуть приложение:
~~~
docker-compose up --build -d
~~~

Применить миграции:
~~~
docker-compose exec app php yii migrate --db=db --interactive=0
docker-compose exec app php yii migrate --db=test_db --interactive=0
~~~

Добавить в hosts связь IP-адреса с именем хоста:
~~~
127.0.0.1    moto-rent-api.loc
127.0.0.1    db
~~~

### Windows (без Docker)

* Apache 2.4
* PHP 7.3.33
* MySQL 5.7

#### Apache

Настройка виртуального хоста:

1. Подключить конфиг виртуальных хостов к основному конфигу веб-сервера. Добавить в конфиг веб-сервера (<Директория Apache>/conf/httpd.conf):
~~~
# Virtual hosts
Include conf/extra/httpd-vhosts.conf
~~~

2. Скопировать [конфиг](https://github.com/cosmastar112/moto_api/blob/master/apache/moto-rent-api.loc.conf) виртуального хоста moto-rent-api.loc в директорию (которую нужно создать) с виртуальными конфигами - <Директория Apache>/conf/extra/vh.

3. Подключить конфиг виртуального хоста moto-rent-api.loc в конфиге виртуальных хостов. Добавить в <Директория Apache>/conf/extra/httpd-vhosts.conf:
~~~
Include conf/extra/vh/moto-rent-api.loc.conf
~~~

4. Создать ссылку на директорию web проекта в <Директория Apache>/htdocs. Можно воспользоваться утилитой junction:
~~~
<Директория проекта>\apache\junction64.exe <Директория Apache>\htdocs\moto-rent-api.loc <Директория проекта>\web
~~~

5. Добавить в файл hosts связь IP-адреса с именем хоста:
~~~
127.0.0.1    moto-rent-api.loc
127.0.0.1    db
~~~

#### Структура БД

##### Рабочая БД
Создать БД
~~~
CREATE SCHEMA `moto` ;
~~~
Создать пользователя "moto" (в БД "moto"), выдать ему права на БД "moto"
~~~
CREATE USER 'moto'@'localhost' IDENTIFIED BY 'password';
GRANT ALL ON moto.* TO 'moto'@'localhost';
~~~
Применить миграции для БД "moto"
~~~
cd /d <Директория проекта>
php yii migrate --db=db --interactive=0
~~~

##### Тестовая БД
Создать БД
~~~
CREATE SCHEMA `moto_test` ;
~~~
Создать пользователя "moto" (в БД "moto_test"), выдать ему права на БД "moto_test"
~~~
CREATE USER 'moto'@'localhost' IDENTIFIED BY 'password';
GRANT ALL ON moto_test.* TO 'moto'@'localhost';
~~~
Применить миграции для БД "moto_test"
~~~
cd /d <Директория проекта>
php yii migrate --db=test_db --interactive=0
~~~

## Использование (на примере curl)

### Список мотоциклов
~~~
curl -X GET http://moto-rent-api.loc:8080/api/v1/moto
~~~

### Аренды мотоцикла
~~~
curl -X GET http://moto-rent-api.loc:8080/api/v1/moto/1
~~~

### Создание аренды
~~~
curl -X POST -H "Content-Type: application/json" -d "{\"username\":\"username1\", \"date_rent_started\":\"2022-03-07 11:33:00\", \"date_rent_ended\":\"2022-03-07 12:33:00\", \"timezone\":\"2\"}" http://moto-rent-api.loc:8080/api/v1/moto/2/rent
~~~

## Документация

Документация доступна по ссылке: http://moto-rent-api.loc:8080/docs/index.html

Для документирования API используется библиотека [zircote/swagger-php](https://github.com/zircote/swagger-php). Визуальное представление документации создается средствами [Swagger UI](https://github.com/swagger-api/swagger-ui).

#### Unit-тесты

Используется [Codeception](https://codeception.com/). Запуск:
~~~
vendor/bin/codecept run unit
~~~