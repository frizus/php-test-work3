# О репозитории
ETL задача. Данные представляют собой связку агентств недвижимости, риелторов, недвижимости и продавцов

Создание БД из `dump.sql`, импорт данных (создание, обновление, не удаление) в БД из Excel файлов

REST для просмотра данных с фильтрами

Настроен конфиг докера для работы

Используется [phpenv](https://github.com/phpenv/phpenv)

## Системные требования

* Docker >= 26.1.4
* Утилита `make`
* `WSL2 Ubuntu` (не обязательно); на самой Linux не на WSL2 работоспособность не проверял

## Установка
Через докер
```bash
make full-setup
make up # запускает сервер на http://localhost:8080
```

## Использование
* http://localhost:8080/ - главная содержит ссылки на другие страницы

Импортирование недвижимости:
* Способ не подключаясь к консоли контейнера:
  ```bash
  # Импортировать app/estate.xlsx
  make import-estate
  # Импортировать app/estate_update.xlsx
  make import-estate-update
  ```

* Через консоль PHP-контейнера:
  ```bash
  # Подключение к консоли контейнера (после make up)
  make connect-php

  # Импортировать app/estate.xlsx
  make import-estate 
  # или
  ./console app:import-estate -v

  # Импортировать app/estate_update.xlsx
  make import-estate-update
  # или
  ./console app:import-estate -v estate_update.xlsx
  ```

* Пересоздание БД (через консоль PHP-контейнера):
  ```bash
  # Подключение к консоли контейнера (после make up)
  make connect-php
  
  # Пересоздать БД
  make db
  # или
  ./console app:db -v
  ```


## Описание для работы с конфигом докера и разработки

### Настройка xdebug

https://habr.com/ru/articles/712670/

В шаге 2:

* В `General/PHP executable` указать: `/home/docker_user/.phpenv/shims/php`

В шаге 3:

* В `Host` указать `localhost`
* В `Port` указать `9001`
* В `Name` указать `xdebug-server` 
* в `Use path mappings` директории `app`
указать `Absolute path on the server`: `/home/docker_user/app` (это значение по умолчанию для `PROJECT_ROOT` из `.env`)


Шаги 6, 7 пропустить 


### Настройка php

```bash
make copy-env
```
Указать версию php в `.env` - переменная `PHP_VERSION` (можно посмотреть версии здесь https://www.php.net/downloads.php)
Чтобы скопировать из контейнера конфиг, надо сделать в папке этого конфига докера (возьмем за условность
версию php `8.3.8`, но вы заместо этой версии поставьте свою, если будете менять):
```bash
docker-compose cp php:/home/docker_user/.phpenv/versions/8.3.8/etc/ ./php/8.3.8/
```
Конфиги php копируются в `./php/8.3.8/`
Файлы относящиеся к конфигу (`php.ini`, `php-fpm.conf`, `php-fpm.conf.default`, `conf.d/`, `php-fpm.d/`), 
остальные можно удалить

Суть в том, что все файлы выбранной версии php `./php/8.3.8/` копируются с заменой в папку контейнера 
`/home/docker_user/.phpenv/versions/8.3.8/etc/`
так, что для конкретной версии php вы можете настроить конфиг

Запустить build php-контейнера, файлы скопируются снова (докер понимает, что файлы изменены и копирует их снова,
а остальная часть повторного build'а проходит быстро, так как закеширована докером)
```bash
# Запустить build php-контейнера
make build-php
# Запустить build php-контейнера без кеша
make rebuild-php
```

Изменения добавлены в дефолтном php `8.3.8`:
* `php-fpm.d/www.conf`: `listen = 127.0.0.1:9000` заменен на `listen = 9000`
* `conf.d/xdebug.ini`

Если нужно сложнее, настраивайте вручную, подключаясь к контейнеру

### WSL2 Ubuntu:

Для того, чтобы были права на редактирование файлов проекта надо сделать следующее:

#### Новая группа для текущего пользователя:

```bash
make copy-env
```

Для совместной работы с файлами из IDE и веб-серверу/генераторам файлов из Docker, добавьте группу пользователей:
```bash
sudo groupadd -g 830 site_editor
```
`GID` `830` указан также в `.env` переменной `SITE_EDITOR_GID`

и добавить текущего пользователя в эту группу:
```bash
sudo usermod -a -G site_editor `whoami`
```

Проверить, что пользователь в группе:
```bash
groups `whoami`
```

#### Настройка git:

Для `git` в WSL2 Ubuntu написать:
https://stackoverflow.com/questions/71849415/i-cannot-add-the-parent-directory-to-safe-directory-in-git
```bash
git config --global --add safe.directory "*"
```
Чтобы из-за разрешенного внешнего доступа к файлам только по группе пользователей не писал ошибку при работе

#### Настройка терминала:
В `~/.bashrc` добавить:
```bash
echo "
umask 002
" >> ~/.bashrc
```

#### Настройка WSL:
В `/etc/wsl.conf` добавить (нужны root-права):
```bash
echo "
[filesystem]
umask=002
" | sudo tee -a /etc/wsl.conf
```

### Windows

#### PHPStorm:

Включить перевод на новую строку линуксовскую, чтобы линтер не выдавал ошибки из-за перевода строк
https://stackoverflow.com/a/40472391

## Затраченное время
1. Настройка среды (модифицировал свое заготовленное) - 1.75 часов
2. Импорт - 11.83 часа
3. Подключение к БД - 4.916 часа
4. REST API - 7.5 часа
5. Линтер - 0.16 часа
6. Полировка, правка багов - 0.33 часа

Всего: 26.486 часа (4 дня)
