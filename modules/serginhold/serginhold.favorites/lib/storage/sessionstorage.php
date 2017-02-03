<?php
namespace SerginhoLD\Favorites\Storage;

use Bitrix\Main\Result;
use Bitrix\Main\Error;
use Bitrix\Main\Localization\Loc;
use SerginhoLD\Favorites\FavoritesTable;

/**
 * Class SessionStorage
 * @package Bella\Favorites\Storage
 */
class SessionStorage extends AbstractStorage
{
    /**
     * SessionStorage constructor.
     * @param null $userID
     * @param string $type
     */
    public function __construct($userID = null, $type = FavoritesTable::ENTITY_TYPE_IBLOCK_ELEMENT)
    {
        parent::__construct($userID, $type);
        
        if (session_status() === PHP_SESSION_NONE)
            session_start();
    }
    
    /**
     * Возвращает массив избранных элементов текущей сессии
     * @return array
     */
    protected function & getStorage()
    {
        $key = FavoritesTable::getTableName();
        $type = $this->getType();
        
        if (!isset($_SESSION[$key][$type]) || !is_array($_SESSION[$key][$type]))
            $_SESSION[$key][$type] = [];
        
        return $_SESSION[$key][$type];
    }
    
    /**
     * {@inheritdoc}
     */
    public function has($id)
    {
        $id = (int)$id;
        $oResult = new Result();
        
        if ($id < 1)
            $oResult->addError(new Error(Loc::getMessage('FAVORITES_MODULE_STORAGE_ERROR_ITEM_VALUE')));
        
        if ($oResult->isSuccess())
        {
            $arStorage = $this->getStorage();
            
            if (!in_array($id, $arStorage, true))
                $oResult->addError(new Error(Loc::getMessage('FAVORITES_MODULE_STORAGE_ERROR_ITEM_NOT_IN_STORAGE')));
        }
        
        return $oResult;
    }
    
    /**
     * {@inheritdoc}
     */
    public function add($id)
    {
        $id = (int)$id;
        $oResult = new Result();
        $oHasResult = $this->has($id);
        
        if ($oHasResult->isSuccess())
        {
            $oResult->addError(new Error(Loc::getMessage('FAVORITES_MODULE_STORAGE_ERROR_ITEM_IN_STORAGE')));
        }
        else
        {
            $arStorage = & $this->getStorage();
            $arStorage[] = $id;
        }
        
        return $oResult;
    }
    
    /**
     * {@inheritdoc}
     */
    public function delete($id)
    {
        $id = (int)$id;
        $oResult = $this->has($id);
        
        if ($oResult->isSuccess())
        {
            $arStorage = & $this->getStorage();
            unset($arStorage[array_search($id, $arStorage, true)]);
        }
        
        return $oResult;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getList(array $arParams = [])
    {
        $arStorage = $this->getStorage();
        
        if (!empty($arParams))
        {
            $limit = isset($arParams['limit']) ? (int)$arParams['limit'] : null;
            $offset = isset($arParams['offset']) ? (int)$arParams['offset'] : null;
            
            $arStorage = array_slice($arStorage, $offset, $limit);
        }
        
        return $arStorage;
    }
}