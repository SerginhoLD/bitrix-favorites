<?php
namespace SerginhoLD\Favorites\Components;

use Bitrix\Main\Loader;
use Bitrix\Main\Result;
use Bitrix\Main\Error;
use Bitrix\Main\Localization\Loc;
use SerginhoLD\Favorites;

/**
 * Class CountComponent
 * @package SerginhoLD\Favorites\Components
 */
class CountComponent extends \CBitrixComponent
{
    /** @var Favorites\StorageInterface */
    protected $storage = null;
    
    /** @var string */
    protected $type;
    
    /**
     * {@inheritdoc}
     */
    public function __construct($component = null)
    {
        parent::__construct($component);
        Loc::loadMessages(__FILE__);
    }
    
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
            
            /** Тип избранного */
            $arParams['ENTITY_TYPE'] = trim($arParams['ENTITY_TYPE']);
            
            $arParams['ENTITY_TYPE'] = !empty($arParams['ENTITY_TYPE'])
                ? $arParams['ENTITY_TYPE']
                : Favorites\StorageInterface::TYPE_IBLOCK_ELEMENT;
            
            $this->type = & $arParams['ENTITY_TYPE'];

            $arParams['SHOW_ERRORS'] = (isset($arParams['SHOW_ERRORS']) && $arParams['SHOW_ERRORS'] === 'Y') ? 'Y' : 'N';
            
            $this->storage = Favorites\Factory::getStorageForCurrentUser($this->type);
        }
        catch (\Exception $e)
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
        $arResult = & $this->arResult;
        
        if (empty($arResult['ERRORS']))
        {
            try
            {
                $storage = $this->storage;
                
                $arResult['USER_ID'] = $storage->getUserId();
                $arResult['COUNT'] = count($storage);
            }
            catch (\Exception $e)
            {
                $arResult['ERRORS'][] = $e->getMessage();
            }
        }
        
        $this->includeComponentTemplate();
    }
}
