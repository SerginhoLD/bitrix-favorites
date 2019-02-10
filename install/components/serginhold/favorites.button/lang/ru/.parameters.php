<?php
defined('B_PROLOG_INCLUDED') and (B_PROLOG_INCLUDED === true) or die();

$MESS = array_merge(!empty($MESS) ? $MESS : [], [
    'FBP_ENTITY_TYPE' => 'Тип элемента',
    'FBP_ENTITY_ID' => 'ID элемента',
    'FBP_GET_COUNT' => 'Получить общее кол-во',
]);
