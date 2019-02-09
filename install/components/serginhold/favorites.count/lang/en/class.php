<?php
defined('B_PROLOG_INCLUDED') and (B_PROLOG_INCLUDED === true) or die();

$MESS = array_merge(!empty($MESS) ? $MESS : [], [
    'FBC_ERROR_MODULE' => 'Not connected module #MODULE_NAME#',
    'FBC_ERROR_ITEM_ID' => 'Not specified item ID',
]);
