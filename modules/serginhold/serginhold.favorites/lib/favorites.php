<?php
namespace SerginhoLD\Favorites;

use Bitrix\Main\Entity;
use Bitrix\Main\UserTable;
use SerginhoLD\Favorites\Storage;

/**
 * Class FavoritesTable
 * @package Bella\Favorites
 */
class FavoritesTable extends Entity\DataManager
{
    const ENTITY_TYPE_IBLOCK_ELEMENT = 'IBLOCK_ELEMENT';
    
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
            new Entity\IntegerField('ENTITY_TYPE', array(
                'required' => true,
                //'default_value' => 'IBLOCK_ELEMENT',
                'default_value' => self::ENTITY_TYPE_IBLOCK_ELEMENT,
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
     * @param string $type
     * @return Storage\AbstractStorage
     */
    public static function getStorageForCurrentUser($type = self::ENTITY_TYPE_IBLOCK_ELEMENT)
    {
        if (!empty(self::$oStorageForCurrentUser) && (self::$oStorageForCurrentUser instanceof Storage\AbstractStorage))
            return self::$oStorageForCurrentUser;
        
        /** @global \CUser $USER */
        global $USER;
        
        $oStorage = null;
        $userID = null;
        
        if ($USER && ($USER instanceof \CUser) && $USER->IsAuthorized())
            $userID = (int)$USER->GetID();
        
        if ($userID > 0)
            $oStorage = new Storage\DatabaseStorage($userID, $type);
        else
            $oStorage = new Storage\SessionStorage(null, $type);
        
        return self::$oStorageForCurrentUser = $oStorage;
    }
}