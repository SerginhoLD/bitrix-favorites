<?php
namespace SerginhoLD\Favorites\Storage;

use Bitrix\Main\Application;
use Bitrix\Main\Result;
use Bitrix\Main\Error;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ArgumentException;
use SerginhoLD\Favorites\FavoritesTable;

/**
 * Class DatabaseStorage
 * @package SerginhoLD\Favorites\Storage
 */
class DatabaseStorage extends AbstractStorage
{
    /**
     * DatabaseStorage constructor.
     * @param null $userID
     * @param string $type
     */
    public function __construct($userID = null, $type = FavoritesTable::TYPE_IBLOCK_ELEMENT)
    {
        parent::__construct($userID, $type);
    }
    
    /**
     * @param int $id ID пользователя
     * @return $this
     * @throws \Exception
     */
    protected function setUser($id = null)
    {
        $id = (int)$id;
        
        if ($id < 1)
            throw new ArgumentException(Loc::getMessage('FAVORITES_MODULE_STORAGE_ERROR_USER_VALUE'), 'id');
        
        parent::setUser($id);
        
        return $this;
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
            $arItem = FavoritesTable::getList([
                'filter' => [
                    '=USER_ID' => $this->getUser(),
                    '=ENTITY_TYPE' => $this->getType(),
                    '=ENTITY_ID' => $id,
                ],
            ])->fetch();
            
            if ($arItem === false)
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
            $oAddResult = FavoritesTable::add([
                'USER_ID' => $this->getUser(),
                'ENTITY_TYPE' => $this->getType(),
                'ENTITY_ID' => $id,
            ]);
            
            if (!$oAddResult->isSuccess())
            {
                $arErrors = $oAddResult->getErrorMessages();
                
                foreach ($arErrors as $message)
                    $oResult->addError(new Error($message));
            }
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
            $oConnection = Application::getInstance()->getConnection(FavoritesTable::getConnectionName());
            $oSqlHelper = $oConnection->getSqlHelper();
            
            $table = FavoritesTable::getTableName();
            
            $sql = sprintf("DELETE FROM %s WHERE %s = %u AND %s = '%s' AND %s = %u",
                $oSqlHelper->quote($table),
                $oSqlHelper->quote('USER_ID'), $this->getUser(),
                $oSqlHelper->quote('ENTITY_TYPE'), $oSqlHelper->forSql($this->getType()),
                $oSqlHelper->quote('ENTITY_ID'), $id);
            
            /** @var \Bitrix\Main\DB\Result $oDeleteResult */
            $oDeleteResult = $oConnection->query($sql);
            
            if ($oConnection->getAffectedRowsCount() < 1)
                $oResult->addError(new Error(Loc::getMessage('FAVORITES_MODULE_STORAGE_ERROR_ITEM_DELETE')));
        }
        
        return $oResult;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getList(array $arParams = [])
    {
        $arParams['filter']['=USER_ID'] = $this->getUser();
        $arParams['filter']['=ENTITY_TYPE'] = $this->getType();
        
        return array_column(FavoritesTable::getList($arParams)->fetchAll(), 'ENTITY_ID');
    }
    
    /**
     * {@inheritdoc}
     */
    public function getAll()
    {
        $arResult = [];
        
        $arFavorites = FavoritesTable::getList([
            'filter' => [
                '=USER_ID' => $this->getUser(),
            ],
        ])->fetchAll();
        
        foreach ($arFavorites as $arItem)
        {
            $arResult[$arItem['ENTITY_TYPE']][] = $arItem['ENTITY_ID'];
        }
        
        return $arResult;
    }
    
    /**
     * {@inheritdoc}
     */
    public function clearAll()
    {
        $oConnection = Application::getInstance()->getConnection(FavoritesTable::getConnectionName());
        $oSqlHelper = $oConnection->getSqlHelper();
        
        $table = FavoritesTable::getTableName();
        
        $sql = sprintf("DELETE FROM %s WHERE %s = %u", $oSqlHelper->quote($table), $oSqlHelper->quote('USER_ID'), $this->getUser());
        
        /** @var \Bitrix\Main\DB\Result $oDeleteResult */
        $oDeleteResult = $oConnection->query($sql);
        
        return $this;
    }
}