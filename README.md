<p align="center"><a href="#" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

## Laravel-приложения REST API

## Стек:
- `PHP 8.2`
- `Laravel 12`
- `PostgresSQL 14`
- `Docker / Docker Compose`
- `L5-Swagger`

## Установка:
1. Склонировать репозиторий: `git clone https://github.com/Constantine1995/rest-api-app`
2. Скопировать `.env.example` в `.env`: `cp .env.example .env`
3. Добавить ваш `API_KEY` в `.env`
4. Выполнить команду: `docker-compose up --build`
5. Выполнить команды в контейнере: <br>
   `docker exec -it app bash`<br><br>
   `php artisan migrate:fresh --seed`<br>
   `php artisan key:generate`<br>
   `mkdir -p storage/api-docs`<br>
   `chmod 755 storage/api-docs`<br>
   `php artisan l5-swagger:generate`<br>

## Доступ к БД:
**Хост/IP:** localhost<br>
**Порт:** 5433<br>
**Имя БД:** postgres<br>
**Пользователь:** admin<br>
**Пароль:** 24HDK2NK32nfw<br>

## Документация API (Swagger):
После установки документация будет доступна по адресу:  
**http://localhost:85/api/documentation**

## Структура API:
`GET /api/organizations/building/{id}` - cписок организаций в конкретном здании<br>
`GET /api/organizations/activity/{id}` - cписок организаций по виду деятельности<br>
`GET /api/organizations/activity-with-children/{id}` - поиск организаций по виду деятельности и его дочерним категориям<br>
`GET /api/organizations/geo/rectangle` - cписок организаций в заданной прямоугольной области<br>
`GET /api/organizations/geo/radius` - cписок организаций в заданном радиусе<br>
`GET /api/organizations/{id}` - информация об организации по ID<br>
`GET /api/organizations/search` - поиск организаций по названию<br>

## Пример запроса:
```bash
curl --location 'http://localhost:85/api/organizations/building/1?per_page=15' \
--header 'X-API-KEY: 5fff06a3-***-***-***'
```
## Тесты
Написаны минимальные `Feature/Unit` тесты

