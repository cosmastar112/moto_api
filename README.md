# Тестовое задание API Аренды мотоциклов

## Задание

1. использовать Yii2, PHP 7.3
2. реализовать сущности:
    - мотоцикл
        - модель
        - цвет
    - аренда
        - юзернейм
        - мотоцикл
        - дата начала аренды
        - дата конца аренды
3. Реализовать механику аренды - пользователи, находящиеся в разных таймзонах, могут создавать аренды на мотоциклы.
    - Один мотоцикл не может быть одновременно быть в аренде у нескольких пользователей (проверяем по юзернейму).
    - При создании аренды должна происходить проверка возможности аренды выбранного мотоцикла на выбранное время
4. реализовать админку – CRUD операции
    - вывод списка мотоциклов и их создание/редактирование/удаление
    - вывод списка аренд
5. реализовать выдачу данных в формате json по RESTful протоколу отдельным контроллером
    - GET /api/v1/moto - получение списка мотоциклов
    - GET /api/v1/moto/{id} - получение списка аренд указанного мотоцикла
    - POST /api/v1/moto/{id}/rent - создание аренды мотоцикла на указанный интервал времени (в запросе время должно включать и таймзону)

Будет плюсом:
1. Использование docker
2. Использование docker-compose
3. Написание unit тестов
4. Swagger

Результат представить в виде репозитория в git (можно на github например) с информацией по разворачиванию проекта (можно в readme).

## Настройка окружения
### Docker
Для Linux-систем: по умолчанию владельцем файлов, которые создаются при работе контейнера, является пользователь root. Если доступ к файлам будет осуществляться с хоста, то это может быть проблемой, т.к. файлы будут доступны только для чтения. Чтобы сделать файлы доступными для не-root пользователя, необходимо в [конфиге](https://github.com/cosmastar112/moto_api/blob/master/docker-compose.yml#L13) вручную установить UID и GID пользователя. Чтобы определить uid и gid, можно воспользоваться соответственно командами id -u и id -g.

Пример значения:
~~~
user: 1000:1000
~~~

При использовании Windows данная проблема не появляется.

Установить зависимости:
~~~
docker-compose run --rm app composer install
~~~

Развернуть приложение:
~~~
docker-compose up --build -d
~~~

Применить миграции:
~~~
docker-compose exec app php yii migrate-api-v1 --db=db --interactive=0
docker-compose exec app php yii migrate-api-v1 --db=test_db --interactive=0
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

#### Composer
Установить зависимости:
~~~
composer install
~~~

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
php yii migrate-api-v1 --db=db --interactive=0
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
php yii migrate-api-v1 --db=test_db --interactive=0
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
curl -X POST -H "Content-Type: application/json" -d "{\"username\":\"username1\", \"date_rent_started\":\"2022-03-07 11:33:00\", \"date_rent_ended\":\"2022-03-07 12:33:00\", \"timezone\":\"Europe\/Samara\"}" http://moto-rent-api.loc:8080/api/v1/moto/2/rent
~~~

## Документация

Документация доступна по ссылке: http://moto-rent-api.loc:8080/docs/index.html

Для документирования API используется библиотека [zircote/swagger-php](https://github.com/zircote/swagger-php). Визуальное представление документации создается средствами [Swagger UI](https://github.com/swagger-api/swagger-ui).

#### Unit-тесты

Используется [Codeception](https://codeception.com/). Запуск:
~~~
vendor/bin/codecept run unit
~~~