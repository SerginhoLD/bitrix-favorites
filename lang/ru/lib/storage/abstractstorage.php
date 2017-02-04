<?php
defined('B_PROLOG_INCLUDED') and (B_PROLOG_INCLUDED === true) or die();

$MESS = array_merge(!empty($MESS) ? $MESS : [], [
    'FAVORITES_MODULE_STORAGE_ERROR_USER_VALUE' => 'Не задан ID пользователя',
    'FAVORITES_MODULE_STORAGE_ERROR_ITEM_VALUE' => 'Не задан ID элемента',
    'FAVORITES_MODULE_STORAGE_ERROR_ITEM_IN_STORAGE' => 'Элемент уже является избранным',
    'FAVORITES_MODULE_STORAGE_ERROR_ITEM_NOT_IN_STORAGE' => 'Элемент не является избранным',
    'FAVORITES_MODULE_STORAGE_ERROR_ITEM_DELETE' => 'Ошибка удаления элемента из избранного',
]);
