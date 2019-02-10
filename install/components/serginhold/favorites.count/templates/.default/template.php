<?php
defined('B_PROLOG_INCLUDED') and (B_PROLOG_INCLUDED === true) or die();

use Bitrix\Main\Localization\Loc;
use SerginhoLD\Favorites\Components\CountComponent;

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
 * @var CountComponent $component
 */

Loc::loadMessages(__FILE__);
?>
<a href="<?= $arParams['LINK'] ?>">
    <?= Loc::getMessage('FCT_TITLE') ?>:
    <span class="js-sld-favorites-count" data-type="<?= $arParams['ENTITY_TYPE'] ?>"><?= $arResult['COUNT'] ?></span>
</a>
