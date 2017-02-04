<?php
namespace SerginhoLD\Favorites;

use Bitrix\Main\Application;
use Bitrix\Main\Entity;
use Bitrix\Main\UserTable;
use SerginhoLD\Favorites\Storage;

/**
 * Class FavoritesTable
 * @package SerginhoLD\Favorites
 */
class FavoritesTable extends Entity\DataManager
{
    const TYPE_IBLOCK_ELEMENT = 'IBLOCK_ELEMENT';
    
    /**
     * @var Storage\AbstractStorage
     */
    private static $oStorageForCurrentUser = null;
    
    /**
     * @return string
     */
    public static function getTableName()
    {
        return 'sld_favorites';
    }
    
    /**
     * @return array
     */
    public static function getMap()
    {
        return array(
            new Entity\IntegerField('ID', array(
                'primary' => true,
                'autocomplete' => true,
            )),
            new Entity\IntegerField('USER_ID', array(
                'required' => true,
            )),
            new Entity\StringField('ENTITY_TYPE', array(
                'required' => true,
                'default_value' => self::TYPE_IBLOCK_ELEMENT,
            )),
            new Entity\IntegerField('ENTITY_ID', array(
                'required' => true,
            )),
            
            new Entity\ReferenceField(
                'USER',
                UserTable::class,
                ['=this.USER_ID' => 'ref.ID'],
                ['join_type' => 'INNER']
            ),
        );
    }
    
    /**
     * Возвращает контейнер избранных элементов для текущего пользователя
     * @param string $type
     * @return Storage\AbstractStorage
     */
    public static function getStorageForCurrentUser($type = self::TYPE_IBLOCK_ELEMENT)
    {
        if (self::$oStorageForCurrentUser instanceof Storage\AbstractStorage)
            return self::$oStorageForCurrentUser;
        
        /** @global \CUser $USER */
        global $USER;
        
        $oStorage = null;
        $userID = null;
        
        if (!empty($USER) && ($USER instanceof \CUser) && $USER->IsAuthorized())
            $userID = (int)$USER->GetID();
        
        if ($userID > 0)
            $oStorage = new Storage\DatabaseStorage($userID, $type);
        else
            $oStorage = new Storage\SessionStorage(null, $type);
        
        return self::$oStorageForCurrentUser = $oStorage;
    }
    
    /**
     * Событие после авторизации пользователя
     * Проверяем есть ли в сессии избранные элементы и переносим их в базу данных
     * @param $arFields
     */
    public static function OnAfterUserLoginEvent(& $arFields)
    {
        try
        {
            if ($arFields['USER_ID'] > 0)
            {
                $oSessionStorage = new Storage\SessionStorage();
                $arSessionFavorites = $oSessionStorage->getAll();
                
                self::$oStorageForCurrentUser = null;
                
                if (!empty($arSessionFavorites))
                {
                    $oConnection = Application::getInstance()->getConnection(self::getConnectionName());
                    $oSqlHelper = $oConnection->getSqlHelper();
                    
                    $table = self::getTableName();
                    
                    $sInsertFields = null;
                    $arInsertValues = [];
                    
                    $sDuplicateUpdate = $oSqlHelper->quote('ENTITY_ID');
                    $sDuplicateUpdate = $sDuplicateUpdate . '=' . $sDuplicateUpdate;
                    
                    foreach ($arSessionFavorites as $type => $arItems)
                    {
                        foreach ($arItems as $itemID)
                        {
                            $arInsert = $oSqlHelper->prepareInsert($table, [
                                'USER_ID' => $arFields['USER_ID'],
                                'ENTITY_TYPE' => $type,
                                'ENTITY_ID' => $itemID,
                            ]);
                            
                            if (empty($arInsertFields))
                                $sInsertFields = $arInsert[0];
                            
                            $arInsertValues[] = '(' . $arInsert[1] . ')';
                        }
                    }
                    
                    $sql = sprintf("INSERT INTO %s (%s) VALUES %s ON DUPLICATE KEY UPDATE %s",
                        $oSqlHelper->quote($table), $sInsertFields, implode(',', $arInsertValues), $sDuplicateUpdate);
                    
                    /** @var \Bitrix\Main\DB\Result $oInsertResult */
                    $oInsertResult = $oConnection->query($sql);
                    
                    if ($oConnection->getAffectedRowsCount() > 0)
                    {
                        $oSessionStorage->clearAll();
                    }
                }
            }
        }
        catch (\Exception $e) {}
    }
}