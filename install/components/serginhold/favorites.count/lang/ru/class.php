<?php
defined('B_PROLOG_INCLUDED') and (B_PROLOG_INCLUDED === true) or die();

$MESS = array_merge(!empty($MESS) ? $MESS : [], [
    'FBC_ERROR_MODULE' => 'Не подключен модуль #MODULE_NAME#',
    'FBC_ERROR_ITEM_ID' => 'Не задан ID элемента',
]);
