<?php
namespace SerginhoLD\Favorites\Storage;

use Bitrix\Main\Application;
use Bitrix\Main\Result;
use Bitrix\Main\Error;
use Bitrix\Main\Web\Cookie;
use Bitrix\Main\Localization\Loc;
use SerginhoLD\Favorites\FavoritesTable;

/**
 * Class LocalStorage
 * @package SerginhoLD\Favorites\Storage
 */
class LocalStorage extends AbstractStorage
{
    /**
     * @var array
     */
    private $arData = [];
    
    /**
     * @var \Bitrix\Main\HttpRequest
     */
    protected $oRequest = null;
    
    /**
     * SessionStorage constructor.
     * @param null $userID
     * @param string $type
     */
    public function __construct($userID = null, $type = FavoritesTable::TYPE_IBLOCK_ELEMENT)
    {
        parent::__construct($userID, $type);
        
        $this->oRequest = Application::getInstance()->getContext()->getRequest();
    }
    
    /**
     * @return string
     */
    protected function getCookieName()
    {
        return FavoritesTable::getTableName();
    }
    
    /**
     * Возвращает массив всех избранных элементов
     * @return array
     */
    protected function & getData()
    {
        if (empty($this->arData))
        {
            $cookieValue = $this->oRequest->getCookie($this->getCookieName());
            
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
     * @return array
     */
    protected function & getItemsForCurrentType()
    {
        $arData = & $this->getData();
        $type = $this->getType();
        
        if (!isset($arData[$type]))
            $arData[$type] = [];
        
        return $arData[$type];
    }
    
    /**
     * Сохраняет массив всех избранных элементов
     * @return $this
     */
    protected function save()
    {
        $oCookie = new Cookie($this->getCookieName(), json_encode($this->getData()));
        
        setcookie(
            $oCookie->getName(),
            $oCookie->getValue(),
            $oCookie->getExpires(),
            $oCookie->getPath(),
            $oCookie->getDomain(),
            $oCookie->getSecure(),
            $oCookie->getHttpOnly()
        );
        
        return $this;
    }
    
    /**
     * {@inheritdoc}
     */
    public function has($id)
    {
        $id = (int)$id;
        $oResult = new Result();
        
        if ($id < 1)
            $oResult->addError(new Error(Loc::getMessage('FAVORITES_MODULE_STORAGE_ERROR_ITEM_VALUE')));
        
        if ($oResult->isSuccess())
        {
            $arItems = $this->getItemsForCurrentType();
            
            if (!in_array($id, $arItems, true))
                $oResult->addError(new Error(Loc::getMessage('FAVORITES_MODULE_STORAGE_ERROR_ITEM_NOT_IN_STORAGE')));
        }
        
        return $oResult;
    }
    
    /**
     * {@inheritdoc}
     */
    public function add($id)
    {
        $id = (int)$id;
        $oResult = new Result();
        $oHasResult = $this->has($id);
        
        if ($oHasResult->isSuccess())
        {
            $oResult->addError(new Error(Loc::getMessage('FAVORITES_MODULE_STORAGE_ERROR_ITEM_IN_STORAGE')));
        }
        else
        {
            $arItems = & $this->getItemsForCurrentType();
            $arItems[] = $id;
            
            $this->save();
        }
        
        return $oResult;
    }
    
    /**
     * {@inheritdoc}
     */
    public function delete($id)
    {
        $id = (int)$id;
        $oResult = $this->has($id);
        
        if ($oResult->isSuccess())
        {
            $arItems = & $this->getItemsForCurrentType();
            unset($arItems[array_search($id, $arItems, true)]);
            
            $this->save();
        }
        
        return $oResult;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getList(array $arParams = [])
    {
        $arItems = $this->getItemsForCurrentType();
        
        if (!empty($arParams))
        {
            $limit = isset($arParams['limit']) ? (int)$arParams['limit'] : null;
            $offset = isset($arParams['offset']) ? (int)$arParams['offset'] : null;
            
            $arItems = array_slice($arItems, $offset, $limit);
        }
        
        return $arItems;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getAll()
    {
        $arData = $this->getData();
        
        foreach ($arData as $type => $arItems)
        {
            if (!is_array($arItems) || empty($arItems))
                unset($arData[$type]);
        }
        
        return $arData;
    }
    
    /**
     * {@inheritdoc}
     */
    public function deleteAll($type = null)
    {
        $arData = & $this->getData();
        
        if (!is_null($type))
        {
            if (isset($arData[$type]))
                unset($arData[$type]);
        }
        else
        {
            $arData = [];
        }
        
        return $this->save();
    }
}