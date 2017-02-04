<?php
namespace SerginhoLD\Favorites\Storage;

defined('B_PROLOG_INCLUDED') and (B_PROLOG_INCLUDED === true) or die();

use Bitrix\Main\Result;
use Bitrix\Main\Localization\Loc;
use SerginhoLD\Favorites\FavoritesTable;

Loc::loadMessages(__FILE__);

/**
 * Class AbstractStorage
 * @package SerginhoLD\Favorites\Storage
 */
abstract class AbstractStorage
{
    /**
     * @var int|null ID пользователя
     */
    private $userID = null;
    
    /**
     * @var string Тип избранного
     */
    private $type = null;
    
    /**
     * AbstractStorage constructor.
     * @param int|null $userID ID пользователя
     * @param string $type Тип избранного
     */
    public function __construct($userID = null, $type = FavoritesTable::TYPE_IBLOCK_ELEMENT)
    {
        $this->setUser($userID)->setType($type);
    }
    
    /**
     * @param int|null $id ID пользователя
     * @return $this
     */
    protected function setUser($id = null)
    {
        $this->userID = $id;
        return $this;
    }
    
    /**
     * Возвращает ID пользователя
     * @return int|null
     */
    public function getUser()
    {
        return $this->userID;
    }
    
    /**
     * @param string $type Тип избранного
     * @return $this
     */
    public function setType($type = FavoritesTable::TYPE_IBLOCK_ELEMENT)
    {
        $type = trim((string)$type);
        
        if (!mb_strlen($type))
            $type = FavoritesTable::TYPE_IBLOCK_ELEMENT;
        
        $this->type = $type;
        
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
     * @param int $id ID элемента
     * @return Result
     */
    abstract public function has($id);
    
    /**
     * @param int $id ID элемента ифноблока
     * @return Result
     */
    abstract public function add($id);
    
    /**
     * @param int $id ID элемента ифноблока
     * @return Result
     */
    abstract public function delete($id);
    
    /**
     * @param array $arParams
     * @see \Bitrix\Main\DataManager::getList($arParams)
     * @return array Массив ID элеметов
     */
    abstract public function getList(array $arParams = []);
    
    /**
     * Возвращает все избранные элементы независимо от типа
     * @return array
     */
    abstract public function getAll();
    
    /**
     * Очищает таблицу избранных элементов
     * @return $this
     */
    abstract public function clearAll();
}