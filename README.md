# bitrix-updates

## Описание

Бот,
который отслеживает изменения на [странице истории версий](https://dev.1c-bitrix.ru/docs/versions.php)
и публикует в [телеграм-канал](https://t.me/BitrixUpdates).

## Установка

Выполнить

```sh
composer install
```

Переопределить значения DATABASE_URL, TOKEN и CHAT_ID, создав файл .env.local или задав реальные переменные окружения.

Пример:

```env
DATABASE_URL="sqlite:///%kernel.project_dir%/var/data.db"
TOKEN="5731045929:AAEVJbrcWdsf5c4RT3k_4I1NKnJfds6SAQ"
CHAT_ID="@BitrixUpdates"
```

Мигрировать

```sh
php bin/console doctrine:migrations:migrate
```

Поставить на крон.

Пример:

```sh
#!/bin/sh
/opt/php82/bin/php ~/bitrix-updates/bin/console app:get-versions
/opt/php82/bin/php ~/bitrix-updates/bin/console app:send-notifications
```
