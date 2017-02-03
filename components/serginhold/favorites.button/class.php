<?php
defined('B_PROLOG_INCLUDED') and (B_PROLOG_INCLUDED === true) or die();

use Bitrix\Main\Application;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use SerginhoLD\Favorites\FavoritesTable;
use SerginhoLD\Favorites\Storage\AbstractStorage;

// TODO: Тестовый компонент добавления элемента в избранное (в разработке)

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
        //$result["CACHE_TYPE"] = isset($arParams["CACHE_TYPE"]) ? $arParams["CACHE_TYPE"] : 'N';
        //$result["CACHE_TIME"] = isset($arParams["CACHE_TIME"]) ? $arParams["CACHE_TIME"] : 3600;
        
        $arResult = &$this->arResult;
        $arResult['ERRORS'] = [];
        
        if (!Loader::includeModule('serginhold.favorites'))
        {
            $arResult['ERRORS'][] = 'Не подключен модуль';
        }
    
        if (!isset($arParams['ENTITY_TYPE']) || !mb_strlen(trim($arParams['ENTITY_TYPE'])))
            $arParams['ENTITY_TYPE'] = FavoritesTable::ENTITY_TYPE_IBLOCK_ELEMENT;
        else
            $arParams['ENTITY_TYPE'] = trim($arParams['ENTITY_TYPE']);
        
        $arParams['ENTITY_ID'] = (int)$arParams['ENTITY_ID'];
        
        if ($arParams['ENTITY_ID'] < 1)
        {
            $arResult['ERRORS'][] = 'Не задан ID элемента';
        }
        
        if (!empty($arParams['ACTION']))
        {
            if (!in_array($arParams['ACTION'], [self::ACTION_ADD, self::ACTION_DELETE], true))
            {
                $arResult['ERRORS'][] = 'Неизвестный тип события';
            }
        }
        
        $arResult['AJAX_URL'] = $this->getPath() . '/ajax.php';
        
        $this->oStorage = FavoritesTable::getStorageForCurrentUser();
        
        return $arParams;
    }
    
    public function executeComponent()
    {
        $arParams = &$this->arParams;
        $arResult = &$this->arResult;
        
        $oRequest = $this->request;
        
        if (!empty($arParams['ACTION']) && $oRequest->isAjaxRequest() && $oRequest->isPost())
        {
            $this->executeAjaxAction();
        }
        
        //if ($this->startResultCache())
        {
            if (empty($arResult['ERRORS']))
            {
                $oStorage = $this->oStorage;
                
                $arResult['USER_ID'] = $oStorage->getUser();
                $arResult['ACTIVE'] = $oStorage->has($arParams['ENTITY_ID'])->isSuccess();
    
                $this->includeComponentTemplate();
            }
            
            if (!empty($arResult['ERRORS']))
            {
                foreach ($arResult['ERRORS'] as $errorText)
                {
                    \ShowError($errorText);
                }
            }
        }
        
        //return $this->arResult;
    }
    
    protected function executeAjaxAction()
    {
        $arParams = &$this->arParams;
        $arResult = &$this->arResult;
        
        if (empty($arResult['ERRORS']))
        {
            $oStorage = $this->oStorage;
            
            switch ($arParams['ACTION'])
            {
                /** Добавляем в избранное */
                case self::ACTION_ADD:
                    
                    $oResult = $oStorage->add($arParams['ENTITY_ID']);
                    
                    if (!$oResult->isSuccess())
                        $arResult['ERRORS'] = array_merge((array)$arResult['ERRORS'], $oResult->getErrorMessages());
                    
                    break;
                
                /** Удаляем из избранного */
                case self::ACTION_DELETE:
                    
                    $oResult = $oStorage->delete($arParams['ENTITY_ID']);
                    
                    if (!$oResult->isSuccess())
                        $arResult['ERRORS'] = array_merge((array)$arResult['ERRORS'], $oResult->getErrorMessages());
    
                    break;
                
                default:
                    $arResult['ERRORS'][] = 'Неизвестный Action';
            }
        }
        
        exit(json_encode([
            'action' => $arParams['ACTION'],
            'success' => empty($arResult['ERRORS']),
            'errors' => $arResult['ERRORS'],
        ]));
    }
}