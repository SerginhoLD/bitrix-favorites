<?php
defined('B_PROLOG_INCLUDED') and (B_PROLOG_INCLUDED === true) or die();

/**
 * @var array $arParams
 * @var array $arResult
 * @global \CMain $APPLICATION
 * @global \CUser $USER
 * @global \CDatabase $DB
 * @var \CBitrixComponentTemplate $this
 * @var string $templateName
 * @var string $templateFile
 * @var string $templateFolder
 * @var string $componentPath
 * @var FavoritesButton $component
 */

\CJSCore::Init(['jquery']);
//var_dump($templateFolder);
/*
echo '<pre>' . htmlspecialchars(print_r($arParams, 1)) . '</pre>';
var_dump($arResult['ACTIVE']);
echo '<pre>' . htmlspecialchars(print_r($arResult, 1)) . '</pre>';

$s = \SerginhoLD\Favorites\FavoritesTable::getStorageForCurrentUser();
echo '<pre>' . htmlspecialchars(print_r($s->getList(), 1)) . '</pre>';*/
?>
<button class="js-addToFavorites <?= ($arResult['ACTIVE']) ? 'active' : null ?>"
        data-id="<?= $arParams['ENTITY_ID'] ?>"
        data-type="<?= htmlspecialchars($arParams['ENTITY_TYPE']) ?>"
        data-url="<?= htmlspecialchars($arResult['AJAX_URL']) ?>"
        data-action-add="<?= $component::ACTION_ADD ?>"
        data-action-delete="<?= $component::ACTION_DELETE ?>">Кнопка</button>