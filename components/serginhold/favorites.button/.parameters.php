<?php
defined('B_PROLOG_INCLUDED') and (B_PROLOG_INCLUDED === true) or die();

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use SerginhoLD\Favorites\FavoritesTable;

/** @var array $arCurrentValues */

if(!Loader::includeModule('serginhold.favorites'))
    return;

Loc::loadMessages(__FILE__);

$arComponentParameters = [
    'PARAMETERS' => [
        'ENTITY_TYPE' => [
            'PARENT' => 'BASE',
            'NAME' => Loc::getMessage('FBP_ENTITY_TYPE'),
            'TYPE' => 'STRING',
            'DEFAULT' => FavoritesTable::ENTITY_TYPE_IBLOCK_ELEMENT,
        ],
        'ENTITY_ID' => [
            'PARENT' => 'BASE',
            'NAME' => Loc::getMessage('FBP_ENTITY_ID'),
            'TYPE' => 'STRING',
            'DEFAULT' => '',
        ],
    ],
];