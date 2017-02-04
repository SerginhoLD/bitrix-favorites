<?php
defined('B_PROLOG_INCLUDED') and (B_PROLOG_INCLUDED === true) or die();

use Bitrix\Main\Loader;
use Bitrix\Main\Result;
use Bitrix\Main\Error;
use Bitrix\Main\Localization\Loc;
use SerginhoLD\Favorites\FavoritesTable;
use SerginhoLD\Favorites\Storage\AbstractStorage;

Loc::loadMessages(__FILE__);

/**
 * Class FavoritesButton
 */
class FavoritesButton extends \CBitrixComponent
{
    const ACTION_ADD = 'add';
    const ACTION_DELETE = 'delete';
    
    /** @var AbstractStorage */
    private $oStorage = null;
    
    /**
     * @param $arParams
     * @return mixed
     */
    public function onPrepareComponentParams($arParams)
    {
        $arResult = & $this->arResult;
        $arResult['ERRORS'] = [];
        
        try
        {
            $module = 'serginhold.favorites';
            
            if (!Loader::includeModule($module))
            {
                $arResult['ERRORS'][] = Loc::getMessage('FBC_ERROR_MODULE', ['#MODULE_NAME#' => $module]);
                return $arParams;
            }
            
            /** ID избранного элемента */
            $arParams['ENTITY_ID'] = (int)$arParams['ENTITY_ID'];
            
            if ($arParams['ENTITY_ID'] < 1)
            {
                $arResult['ERRORS'][] = Loc::getMessage('FBC_ERROR_ITEM_ID');
                return $arParams;
            }
            
            /** Список возможных действий */
            $arResult['ACTIONS'] = [
                'ADD' => self::ACTION_ADD,
                'DELETE' => self::ACTION_DELETE,
            ];
            
            /** Текущее действие */
            if (!empty($arParams['ACTION']))
            {
                if (!in_array($arParams['ACTION'], $arResult['ACTIONS'], true))
                {
                    $arResult['ERRORS'][] = Loc::getMessage('FBC_ERROR_ACTION');
                    return $arParams;
                }
            }
            
            /** Тип избранного */
            $arParams['ENTITY_TYPE'] = isset($arParams['ENTITY_TYPE'])
                ? trim($arParams['ENTITY_TYPE'])
                : FavoritesTable::TYPE_IBLOCK_ELEMENT;
            
            if (!mb_strlen($arParams['ENTITY_TYPE']))
                $arParams['ENTITY_TYPE'] = FavoritesTable::TYPE_IBLOCK_ELEMENT;
            
            $arResult['AJAX_URL'] = $this->getPath() . '/ajax.php';
            $arParams['SHOW_ERRORS'] = (isset($arParams['SHOW_ERRORS']) && $arParams['SHOW_ERRORS'] === 'Y') ? 'Y' : 'N';
            
            $this->oStorage = FavoritesTable::getStorageForCurrentUser();
        }
        catch (Exception $e)
        {
            $arResult['ERRORS'][] = $e->getMessage();
        }
        
        return $arParams;
    }
    
    /**
     * Execute component
     */
    public function executeComponent()
    {
        $arParams = & $this->arParams;
        $arResult = & $this->arResult;
        
        $oRequest = $this->request;
        
        if (!empty($arParams['ACTION']) && $oRequest->isAjaxRequest() && $oRequest->isPost())
        {
            $this->executeAjaxAction();
        }
        
        if (empty($arResult['ERRORS']))
        {
            $oStorage = $this->oStorage;
            
            $arResult['USER_ID'] = $oStorage->getUser();
            $arResult['ACTIVE'] = $oStorage->has($arParams['ENTITY_ID'])->isSuccess();
        }
        
        $this->includeComponentTemplate();
    }
    
    /**
     * Ajax action
     */
    protected function executeAjaxAction()
    {
        $arParams = & $this->arParams;
        $arResult = & $this->arResult;
        
        $oActionResult = new Result();
        
        if (empty($arResult['ERRORS']))
        {
            $oStorage = $this->oStorage;
            
            switch ($arParams['ACTION'])
            {
                /** Добавляем в избранное */
                case self::ACTION_ADD:
                    $oActionResult = $oStorage->add($arParams['ENTITY_ID']);
                    break;
                
                /** Удаляем из избранного */
                case self::ACTION_DELETE:
                    $oActionResult = $oStorage->delete($arParams['ENTITY_ID']);
                    break;
                
                default:
                    $oActionResult->addError(new Error(Loc::getMessage('FBC_ERROR_ACTION')));
            }
        }
        
        $arResult['ERRORS'] = array_merge((array)$arResult['ERRORS'], $oActionResult->getErrorMessages());
        
        exit(json_encode([
            'action' => $arParams['ACTION'],
            'success' => empty($arResult['ERRORS']),
            'errors' => $arResult['ERRORS'],
        ]));
    }
}