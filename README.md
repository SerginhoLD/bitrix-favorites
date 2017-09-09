# Избранное (Модуль для 1С-Битрикс)

[![Latest Stable Version](https://poser.pugx.org/serginhold/bitrix-favorites/v/stable)](https://packagist.org/packages/serginhold/bitrix-favorites) [![Total Downloads](https://poser.pugx.org/serginhold/bitrix-favorites/downloads)](https://packagist.org/packages/serginhold/bitrix-favorites) [![License](https://poser.pugx.org/serginhold/bitrix-favorites/license)](LICENSE.md)

Модуль для хранения избранных элементов.
Если пользователь не авторизован, использует Cookie для хранения избранных элементов.

```php
use SerginhoLD\Favorites;

$storage = Favorites\Factory::getStorageForCurrentUser();
$storage->add(5);
$storage->add(33);

$items = $storage->getList();

var_dump($items);
```
```
array(2) {
  [0]=>
  string(1) "5"
  [1]=>
  string(2) "33"
}
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