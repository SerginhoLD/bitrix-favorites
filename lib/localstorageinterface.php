<?php
namespace SerginhoLD\Favorites;

use Bitrix\Main\Result;

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
}