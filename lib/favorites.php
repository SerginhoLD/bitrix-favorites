<?php
namespace SerginhoLD\Favorites;

use Bitrix\Main\Application;
use Bitrix\Main\Entity;
use Bitrix\Main\UserTable;
use Bitrix\Main\ArgumentNullException;
//use SerginhoLD\Favorites\Storage;

/**
 * Class FavoritesTable
 * @package SerginhoLD\Favorites
 */
class FavoritesTable extends Entity\DataManager
{
    const TYPE_IBLOCK_ELEMENT = 'IBLOCK_ELEMENT';
    
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
        return [
            'ID' => new Entity\IntegerField('ID', [
                'primary' => true,
                'autocomplete' => true,
            ]),
            'USER_ID' => new Entity\IntegerField('USER_ID', [
                'required' => true,
            ]),
            'ENTITY_TYPE' => new Entity\StringField('ENTITY_TYPE', [
                'required' => true,
                'default_value' => self::TYPE_IBLOCK_ELEMENT,
            ]),
            'ENTITY_ID' => new Entity\IntegerField('ENTITY_ID', [
                'required' => true,
            ]),
            'USER' => new Entity\ReferenceField(
                'USER',
                UserTable::class,
                ['=this.USER_ID' => 'ref.ID'],
                ['join_type' => 'INNER']
            ),
        ];
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
                static::insertFromLocalStorage($arFields['USER_ID']);
            }
        }
        catch (\Exception $e) {}
    }
    
    /**
     * @param int $userId
     * @return bool
     * @throws ArgumentNullException
     */
    public static function insertFromLocalStorage($userId)
    {
        $userId = (int)$userId;
        
        if ($userId < 1)
        {
            throw new ArgumentNullException('userId');
        }
        
        $localStorage = new LocalStorage();
        $arFavorites = $localStorage->getAllItems();
        
        if (!empty($arFavorites))
        {
            $connection = Application::getInstance()->getConnection(static::getConnectionName());
            $helper = $connection->getSqlHelper();
            $table = static::getTableName();
            
            $sInsertFields = null;
            $arInsertValues = [];
            
            $sDuplicateUpdate = $helper->quote('ENTITY_ID');
            $sDuplicateUpdate = $sDuplicateUpdate . '=' . $sDuplicateUpdate;
            
            foreach ($arFavorites as $type => $arItems)
            {
                foreach ($arItems as $itemId)
                {
                    $arInsert = $helper->prepareInsert($table, [
                        'USER_ID' => $userId,
                        'ENTITY_TYPE' => $type,
                        'ENTITY_ID' => $itemId,
                    ]);
                    
                    if (empty($sInsertFields))
                        $sInsertFields = $arInsert[0];
                    
                    $arInsertValues[] = '(' . $arInsert[1] . ')';
                }
            }
            
            $sql = sprintf("INSERT INTO %s (%s) VALUES %s ON DUPLICATE KEY UPDATE %s",
                $helper->quote($table), $sInsertFields, implode(',', $arInsertValues), $sDuplicateUpdate
            );
            
            /** @var \Bitrix\Main\DB\Result $oInsertResult */
            $oInsertResult = $connection->query($sql);
            
            if ($connection->getAffectedRowsCount() < 1)
            {
                return false;
            }
            
            $localStorage->flushAll();
        }
        
        return true;
    }
}