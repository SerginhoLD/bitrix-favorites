<?php
namespace SerginhoLD\Favorites;

use Bitrix\Main\Result;
use Bitrix\Main\Entity\DataManager;

/**
 * Interface DatabaseStorageInterface
 * @package SerginhoLD\Favorites\Storage
 */
interface LocalStorageInterface extends StorageInterface
{
    /**
     * @return string[]
     */
    public function getAllTypes();
    
    /**
     * @return array
     */
    public function getAllItems();
    
    /**
     * @return Result
     */
    public function flushAll();
    
    /**
     * @param DataManager|string $dataManager
     * @return resource
     */
    //public function saveToDataManager($dataManager);
}