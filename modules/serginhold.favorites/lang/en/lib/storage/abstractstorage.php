<?php
defined('B_PROLOG_INCLUDED') and (B_PROLOG_INCLUDED === true) or die();

$MESS = array_merge(!empty($MESS) ? $MESS : [], [
    'FAVORITES_MODULE_STORAGE_ERROR_USER_VALUE' => 'Do not set user ID',
    'FAVORITES_MODULE_STORAGE_ERROR_ITEM_VALUE' => 'Not specified item ID',
    'FAVORITES_MODULE_STORAGE_ERROR_ITEM_IN_STORAGE' => 'The element is already selected',
    'FAVORITES_MODULE_STORAGE_ERROR_ITEM_NOT_IN_STORAGE' => 'The element is not selected',
    'FAVORITES_MODULE_STORAGE_ERROR_ITEM_DELETE' => 'Failed to delete item',
]);
