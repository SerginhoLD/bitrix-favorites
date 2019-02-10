<?php
namespace SerginhoLD\Favorites\Components;

use Bitrix\Main\Loader;
use Bitrix\Main\Result;
use Bitrix\Main\Error;
use Bitrix\Main\Localization\Loc;
use SerginhoLD\Favorites;

/**
 * Class ButtonComponent
 * @package SerginhoLD\Favorites\Components
 */
class ButtonComponent extends \CBitrixComponent
{
    const ACTION_ADD = 'add';
    const ACTION_DELETE = 'delete';
    
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
            $arParams['ENTITY_TYPE'] = trim($arParams['ENTITY_TYPE']);
            
            $arParams['ENTITY_TYPE'] = !empty($arParams['ENTITY_TYPE'])
                ? $arParams['ENTITY_TYPE']
                : Favorites\StorageInterface::TYPE_IBLOCK_ELEMENT;
            
            $this->type = & $arParams['ENTITY_TYPE'];
            
            $arResult['AJAX_URL'] = $this->getPath() . '/ajax.php';
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
        $arParams = & $this->arParams;
        $arResult = & $this->arResult;
        
        $request = $this->request;
        
        if (!empty($arParams['ACTION']) && $request->isAjaxRequest() && $request->isPost())
        {
            $this->executeAjaxAction();
        }
        
        if (empty($arResult['ERRORS']))
        {
            try
            {
                $storage = $this->storage;
                
                $arResult['USER_ID'] = $storage->getUserId();
                $arResult['ACTIVE'] = $storage->has($arParams['ENTITY_ID']);

                if ($arParams['GET_COUNT'] === 'Y')
                    $arResult['COUNT'] = $storage->count();
            }
            catch (\Exception $e)
            {
                $arResult['ERRORS'][] = $e->getMessage();
            }
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
        
        $result = new Result();
        $count = null;
        
        if (empty($arResult['ERRORS']))
        {
            try
            {
                $storage = $this->storage;

                switch ($arParams['ACTION'])
                {
                    /** Добавляем в избранное */
                    case self::ACTION_ADD:
                        $result = $storage->add($arParams['ENTITY_ID']);
                        break;
                    
                    /** Удаляем из избранного */
                    case self::ACTION_DELETE:
                        $result = $storage->delete($arParams['ENTITY_ID']);
                        break;
                    
                    default:
                        $result->addError(new Error(Loc::getMessage('FBC_ERROR_ACTION')));
                }

                if ($arParams['GET_COUNT'])
                    $count = $storage->count();
            }
            catch (\Exception $e)
            {
                $result->addError(new Error($e->getMessage()));
            }
        }
        
        $arResult['ERRORS'] = array_merge((array)$arResult['ERRORS'], $result->getErrorMessages());
        
        exit(json_encode([
            'action' => $arParams['ACTION'],
            'entity_id' => $arParams['ENTITY_ID'],
            'count' => $count,
            'success' => empty($arResult['ERRORS']),
            'errors' => $arResult['ERRORS'],
        ]));
    }
}
