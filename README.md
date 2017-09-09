# Избранное (Модуль для 1С-Битрикс)

[![Latest Stable Version](https://poser.pugx.org/serginhold/bitrix-favorites/v/stable)](https://packagist.org/packages/serginhold/bitrix-favorites) [![Total Downloads](https://poser.pugx.org/serginhold/bitrix-favorites/downloads)](https://packagist.org/packages/serginhold/bitrix-favorites) [![License](https://poser.pugx.org/serginhold/bitrix-favorites/license)](LICENSE.md)

Модуль для хранения избранных элементов.
Если пользователь не авторизован, использует Cookie.

```php
use SerginhoLD\Favorites;

$storage = Favorites\Factory::getStorageForCurrentUser();

$storage->add(5);
$storage->add(22);
$storage->add(33);

$storage->delete(22);

$items = $storage->getList();

print_r($items);
```
```
Array
(
    [0] => 5
    [2] => 33
)
```

DataManager для хранения элементов в базе данных:
```php
use SerginhoLD\Favorites\FavoritesTable;

$items = FavoritesTable::getList([
    'filter' => [
        '=USER_ID' => 1,
        '=ENTITY_TYPE' => FavoritesTable::TYPE_IBLOCK_ELEMENT,
    ],
    'select' => [
        '*',
        'USER_LOGIN' => 'USER.LOGIN',
    ],
])->fetchAll();

print_r($items);
```
```
Array
(
    [0] => Array
        (
            [ID] => 1
            [USER_ID] => 1
            [ENTITY_TYPE] => IBLOCK_ELEMENT
            [ENTITY_ID] => 2
            [USER_LOGIN] => admin
        )

)
```

## Установка

### Composer
```bash
composer require serginhold/bitrix-favorites
```

### Ручная установка
* Создать папку `serginhold.favorites` в папке `/local/modules/` или `/bitrix/modules/`
* Скопировать файлы модуля в папку `serginhold.favorites`

## Требования
* PHP >= 5.5.0

## Лицензия
[MIT](LICENSE.md)