<?php
namespace SerginhoLD\Favorites;

use Bitrix\Main\ArgumentNullException;

/**
 * Class AbstractStorage
 * @package SerginhoLD\Favorites\Storage
 */
abstract class AbstractStorage implements StorageInterface
{
    /** @var int */
    private $userId = null;
    
    /** @var string */
    protected $type = self::TYPE_IBLOCK_ELEMENT;
    
    /**
     * @return int
     */
    public function getUserId()
    {
        return $this->userId;
    }
    
    /**
     * @param int $id
     * @return $this
     * @throws ArgumentNullException
     */
    protected function _setUserId($id)
    {
        $id = (int)$id;
        
        if ($id < 1)
        {
            throw new ArgumentNullException('id');
        }
        
        $this->userId = $id;
        return $this;
    }
    
    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }
    
    /**
     * @param string $type
     * @return $this
     * @throws ArgumentNullException
     */
    protected function _setType($type)
    {
        $type = trim($type);
        
        if (empty($type) && $type !== '0')
        {
            throw new ArgumentNullException('type');
        }
        
        $this->type = $type;
        return $this;
    }
}