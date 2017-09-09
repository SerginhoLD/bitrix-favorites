<?php
namespace SerginhoLD\Favorites;

use Bitrix\Main\Result;
use SerginhoLD\Favorites\FavoritesTable;

/**
 * Interface StorageInterface
 * @package SerginhoLD\Favorites\Storage
 */
interface StorageInterface
{
    const TYPE_IBLOCK_ELEMENT = FavoritesTable::TYPE_IBLOCK_ELEMENT;
    
    /**
     * @return int
     */
    public function getUserId();
    
    /**
     * @return string
     */
    public function getType();
    
    /**
     * @param array $parameters
     * @return array
     */
    public function getList(array $parameters = []);
    
    /**
     * @param int $id
     * @return bool
     */
    public function has($id);
    
    /**
     * @param int $id
     * @return Result
     */
    public function add($id);
    
    /**
     * @param int $id
     * @return Result
     */
    public function delete($id);
    
    /**
     * @return Result
     */
    public function flush();
}