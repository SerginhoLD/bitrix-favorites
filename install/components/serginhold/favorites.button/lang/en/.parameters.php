<?php
defined('B_PROLOG_INCLUDED') and (B_PROLOG_INCLUDED === true) or die();

$MESS = array_merge(!empty($MESS) ? $MESS : [], [
    'FBP_ENTITY_TYPE' => 'Item type',
    'FBP_ENTITY_ID' => 'Item ID',
    'FBP_GET_COUNT' => 'Get count',
]);
