<?php
namespace SerginhoLD\Favorites;

use Bitrix\Main\Entity\DataManager;

/**
 * Interface DatabaseStorageInterface
 * @package SerginhoLD\Favorites\Storage
 */
interface DatabaseStorageInterface extends StorageInterface
{
    /**
     * @return DataManager|string
     */
    public function getDataManager();
}