<?php
namespace SerginhoLD\Favorites;

use Bitrix\Main\Application;
use Bitrix\Main\Result;
use Bitrix\Main\Error;
use Bitrix\Main\HttpRequest as Request;
use Bitrix\Main\Response;
use Bitrix\Main\Web\Cookie;

/**
 * Class LocalStorage
 * @package SerginhoLD\Favorites\Storage
 */
class LocalStorage extends AbstractStorage implements LocalStorageInterface
{
    /** @var Request */
    protected $request;
    
    /** @var Response */
    protected $response;
    
    /** @var array */
    private $arData = [];
    
    /** @var bool */
    private $isGetFromCookie = false;
    
    /**
     * LocalStorage constructor.
     * @param string $type
     */
    public function __construct($type = self::TYPE_IBLOCK_ELEMENT)
    {
        $this->_setType($type);
        $this->request = Application::getInstance()->getContext()->getRequest();
        $this->response = Application::getInstance()->getContext()->getResponse();
    }
    
    /**
     * @return string
     */
    protected function _getCookieName()
    {
        return FavoritesTable::getTableName();
    }
    
    /**
     * Возвращает массив всех избранных элементов
     * @return array
     */
    protected function & _getData()
    {
        if (empty($this->arData) && !$this->isGetFromCookie)
        {
            $this->isGetFromCookie = true;
            $cookieValue = $this->request->getCookie($this->_getCookieName());
            
            if (!empty($cookieValue))
            {
                $arNewData = json_decode($cookieValue, true);
                $this->arData = (json_last_error() === JSON_ERROR_NONE) ? (array)$arNewData : [];
            }
        }
        
        return $this->arData;
    }
    
    /**
     * Возвращает массив избранных элементов для текущего типа
     * @return int[]
     */
    protected function & _getItemsForCurrentType()
    {
        $arData = & $this->_getData();
        $type = $this->getType();
        
        if (!isset($arData[$type]))
            $arData[$type] = [];
        
        return $arData[$type];
    }
    
    /**
     * Сохраняет массив всех избранных элементов
     * @return $this
     */
    protected function _save()
    {
        $cookie = new Cookie($this->_getCookieName(), json_encode($this->arData));
        
        setcookie(
            $cookie->getName(),
            $cookie->getValue(),
            $cookie->getExpires(),
            $cookie->getPath(),
            $cookie->getDomain(),
            $cookie->getSecure(),
            $cookie->getHttpOnly()
        );
        
        $this->response->addCookie($cookie);
        return $this;
    }
    
    /**
     * @param int $id
     * @return bool
     */
    public function has($id)
    {
        $id = (int)$id;
        return in_array($id, $this->_getItemsForCurrentType(), true);
    }
    
    /**
     * @param int $id
     * @return Result
     */
    public function add($id)
    {
        $id = (int)$id;
        $result = new Result();
        
        if ($this->has($id))
        {
            return $result;
        }
        
        $arItems = & $this->_getItemsForCurrentType();
        $arItems[] = $id;
        
        $this->_save();
        return $result;
    }
    
    /**
     * @param int $id
     * @return Result
     */
    public function delete($id)
    {
        $id = (int)$id;
        $result = new Result();
        
        if (!$this->has($id))
        {
            return $result;
        }
        else
        {
            $arItems = & $this->_getItemsForCurrentType();
            unset($arItems[array_search($id, $arItems, true)]);
            
            $this->_save();
        }
        
        return $result;
    }
    
    /**
     * @param array $parameters
     * @return int[]
     */
    public function getList(array $parameters = [])
    {
        $arItems = $this->_getItemsForCurrentType();
        
        if (!empty($parameters))
        {
            $limit = isset($parameters['limit']) ? (int)$parameters['limit'] : null;
            $offset = isset($parameters['offset']) ? (int)$parameters['offset'] : null;
            
            if ($limit || $offset)
            {
                $arItems = array_slice($arItems, $offset, $limit);
            }
        }
        
        return $arItems;
    }
    
    /**
     * @return Result
     */
    public function flush()
    {
        unset($this->arData[$this->getType()]);
        
        $this->_save();
        return new Result();
    }
    
    /**
     * @return Result
     */
    public function flushAll()
    {
        $this->arData = [];
        $this->_save();
        return new Result();
    }
    
    /**
     * @return string[]
     */
    public function getAllTypes()
    {
        return array_unique(array_keys($this->_getData()));
    }
    
    /**
     * @return array
     */
    public function getAllItems()
    {
        $arData = $this->_getData();
        
        foreach ($arData as $type => $arItems)
        {
            if (!is_array($arItems) || empty($arItems))
                unset($arData[$type]);
        }
        
        return $arData;
    }

    /**
     * @return int
     */
    public function count()
    {
        $arItems = $this->_getItemsForCurrentType();
        return count($arItems);
    }
}
