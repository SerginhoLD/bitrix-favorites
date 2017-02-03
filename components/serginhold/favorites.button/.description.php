<?php
defined('B_PROLOG_INCLUDED') and (B_PROLOG_INCLUDED === true) or die();

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

$arComponentDescription = [
    'NAME' => Loc::getMessage('FAVORITES_BUTTON_NAME'),
    'DESCRIPTION' => null,
    'SORT' => 100,
    'CACHE_PATH' => 'Y',
    'PATH' => [
        'ID' => 'serginhold.favorites',
        'NAME' => Loc::getMessage('FAVORITES_MODULE_NAME'),
    ],
];