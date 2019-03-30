<?php
define('STOP_STATISTICS', true);
define('NO_KEEP_STATISTIC', 'Y');
define('NO_AGENT_STATISTIC', 'Y');
define('DisableEventsCheck', true);

$elementId = null;
$arErrors = [];
$action = isset($_POST['ACTION']) ? (string)$_POST['ACTION'] : null;
$entityType = isset($_POST['ENTITY_TYPE']) ? (string)$_POST['ENTITY_TYPE'] : null;

if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST')
    {
        if (!empty($_POST['ENTITY_ID']))
        {
            $elementId = (int)$_POST['ENTITY_ID'];
            
            if ($elementId < 1)
                $arErrors[] = 'Not specified item ID';
        }
        
        if (empty($action))
            $arErrors[] = 'Unknown action';
    }
    else
    {
        $arErrors[] = 'No post data';
    }
}
else
{
    $arErrors[] = 'Not ajax';
}

if (!empty($arErrors))
{
    exit(json_encode([
        'action' => $action,
        'success' => false,
        'entity_id' => $elementId,
        'errors' => $arErrors,
    ]));
}

require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';

\CBitrixComponent::includeComponentClass('serginhold:favorites.button');

$component = new \SerginhoLD\Favorites\Components\ButtonComponent();
$component->arParams = $component->onPrepareComponentParams([
    'ENTITY_ID' => $elementId,
    'ENTITY_TYPE' => $entityType,
    'GET_COUNT' => 'Y',
    'ACTION' => $action,
]);

$component->executeComponent();
