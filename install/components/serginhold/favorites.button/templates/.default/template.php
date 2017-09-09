<?php
defined('B_PROLOG_INCLUDED') and (B_PROLOG_INCLUDED === true) or die();

use Bitrix\Main\Localization\Loc;

/**
 * @global \CMain $APPLICATION
 * @global \CUser $USER
 * @global \CDatabase $DB
 * @var array $arParams
 * @var array $arResult
 * @var \CBitrixComponentTemplate $this
 * @var string $templateName
 * @var string $templateFile
 * @var string $templateFolder
 * @var string $componentPath
 * @var FavoritesButton $component
 */

Loc::loadMessages(__FILE__);

\CJSCore::Init(['jquery']);
?>
<a href="" class="btn btn-xs js-add-to-favorites <?= $arResult['ACTIVE'] ? 'btn-success' : 'btn-default' ?>"
        data-id="<?= $arParams['ENTITY_ID'] ?>"
        data-type="<?= htmlspecialchars($arParams['ENTITY_TYPE']) ?>"
        data-url="<?= htmlspecialchars($arResult['AJAX_URL']) ?>"
        data-action-add="<?= $arResult['ACTIONS']['ADD'] ?>"
        data-action-delete="<?= $arResult['ACTIONS']['DELETE'] ?>"
        data-title-add="<?= htmlspecialchars(Loc::getMessage('FBT_ADD')) ?>"
        data-title-delete="<?= htmlspecialchars(Loc::getMessage('FBT_DELETE')) ?>">
    <?= htmlspecialchars($arResult['ACTIVE'] ? Loc::getMessage('FBT_DELETE') : Loc::getMessage('FBT_ADD')) ?>
</a>