<?php
define('STOP_STATISTICS', true);
define('NO_KEEP_STATISTIC', 'Y');
define('NO_AGENT_STATISTIC', 'Y');
define('DisableEventsCheck', true);
//define("BX_SECURITY_SHOW_MESSAGE", true);

/**
 * @global \CMain $APPLICATION
 * @global \CUser $USER
 */

$elementID = null;
$arErrors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST')
{
    if (!empty($_POST['ENTITY_ID']))
    {
        $elementID = (int)$_POST['ENTITY_ID'];
    }
    
    if (empty($_POST['ACTION']))
    {
        $arErrors[] = 'Не указан тип события';
    }
}
else
{
    $arErrors[] = 'Не POST';
}

if (!$elementID || $elementID < 1)
{
    $arErrors[] = 'Не укзан ID элемента';
}

// header('Content-Type: application/json');

if (empty($arErrors))
{
    require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';
    
    \CBitrixComponent::includeComponentClass('serginhold:favorites.button');
    
    $oComponent = new FavoritesButton;
    $oComponent->arParams = $oComponent->onPrepareComponentParams([
        'ENTITY_ID' => $elementID,
        'ENTITY_TYPE' => (string)$_POST['ENTITY_TYPE'],
        'ACTION' => $_POST['ACTION'],
    ]);
    
    $oComponent->executeComponent();
}
// TODO: Чтобы лишний раз ядро не подключать
else
{
    exit(json_encode([
        'action' => $_POST['ACTION'],
        'success' => false,
        'errors' => $arErrors,
    ]));
}
    