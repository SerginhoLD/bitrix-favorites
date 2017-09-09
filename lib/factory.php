<?php
namespace SerginhoLD\Favorites;

use Bitrix\Main\Application;

/**
 * Class Factory
 * @package SerginhoLD\Favorites
 */
class Factory
{
    /**
     * @param int $userId
     * @param string $type
     * @return StorageInterface|DatabaseStorageInterface
     */
    public static function getStorageByUserId($userId, $type = StorageInterface::TYPE_IBLOCK_ELEMENT)
    {
        return new DatabaseStorage($userId, $type);
    }
    
    /**
     * @param string $type
     * @return StorageInterface|DatabaseStorageInterface|LocalStorageInterface
     */
    public static function getStorageForCurrentUser($type = StorageInterface::TYPE_IBLOCK_ELEMENT)
    {
        /** @global \CUser $USER */
        global $USER;
        
        $userId = null;
        
        if (!empty($USER) && ($USER instanceof \CUser) && $USER->IsAuthorized())
        {
            $userId = (int)$USER->GetID();
        }
        
        return ($userId > 0)
            ? static::getStorageByUserId($userId, $type)
            : new LocalStorage($type);
    }
}