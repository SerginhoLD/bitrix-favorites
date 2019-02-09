<?php
namespace SerginhoLD\Favorites;

use Bitrix\Main\Application;
use Bitrix\Main\Result;
use Bitrix\Main\Error;
use Bitrix\Main\Entity\DataManager;

/**
 * Class DatabaseStorage
 * @package SerginhoLD\Favorites\Storage
 */
class DatabaseStorage extends AbstractStorage implements DatabaseStorageInterface
{
    /** @var DataManager|string */
    protected $dataManager = FavoritesTable::class;
    
    /**
     * DatabaseStorage constructor.
     * @param $userId
     * @param string $type
     */
    public function __construct($userId, $type = self::TYPE_IBLOCK_ELEMENT)
    {
        $this->_setUserId($userId);
        $this->_setType($type);
    }
    
    /**
     * @return DataManager|string
     */
    public function getDataManager()
    {
        return $this->dataManager;
    }
    
    /**
     * @param int $id
     * @return bool
     */
    public function has($id)
    {
        $id = (int)$id;
        
        if ($id < 1)
        {
            return false;
        }
        
        $table = $this->getDataManager();
        
        $arItem = $table::getList([
            'filter' => [
                '=USER_ID' => $this->getUserId(),
                '=ENTITY_TYPE' => $this->getType(),
                '=ENTITY_ID' => $id,
            ],
        ])->fetch();
        
        if ($arItem === false)
        {
            return false;
        }
        
        return true;
    }
    
    /**
     * @param int $id
     * @return Result
     */
    public function add($id)
    {
        $id = (int)$id;
        $result = new Result();
        
        if ($id < 1)
        {
            $result->addError(new Error("Argument 'id' is empty"));
        }
        
        $table = $this->getDataManager();
        $connection = Application::getInstance()->getConnection($table::getConnectionName());
        $helper = $connection->getSqlHelper();
        
        $sql = sprintf('INSERT INTO %1$s (%2$s, %3$s, %4$s) VALUES (%5$u, \'%6$s\', %7$u) ON DUPLICATE KEY UPDATE %8$s=%8$s',
            $helper->quote($table::getTableName()),
            $helper->quote('USER_ID'),
            $helper->quote('ENTITY_TYPE'),
            $helper->quote('ENTITY_ID'),
            $this->getUserId(), $helper->forSql($this->getType()), $id,
            $helper->quote('ID')
        );
        
        try
        {
            $connection->query($sql);
        }
        catch (\Exception $e)
        {
            $result->addError(new Error($e->getMessage()));
        }
        
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
        
        if ($id < 1)
        {
            $result->addError(new Error("Argument 'id' is empty"));
        }
        
        $table = $this->getDataManager();
        $connection = Application::getInstance()->getConnection($table::getConnectionName());
        $helper = $connection->getSqlHelper();
        
        $sql = sprintf('DELETE FROM %1$s WHERE %2$s = %3$u AND %4$s = \'%5$s\' AND %6$s = %7$u',
            $helper->quote($table::getTableName()),
            $helper->quote('USER_ID'), $this->getUserId(),
            $helper->quote('ENTITY_TYPE'), $helper->forSql($this->getType()),
            $helper->quote('ENTITY_ID'), $id
        );
        
        try
        {
            $connection->query($sql);
        }
        catch (\Exception $e)
        {
            $result->addError(new Error($e->getMessage()));
        }
        
        return $result;
    }
    
    /**
     * @param array $parameters
     * @return int[]
     */
    public function getList(array $parameters = [])
    {
        $parameters['filter']['=USER_ID'] = $this->getUserId();
        $parameters['filter']['=ENTITY_TYPE'] = $this->getType();
        $parameters['select'] = ['ENTITY_ID'];
        
        $table = $this->getDataManager();
        return array_column($table::getList($parameters)->fetchAll(), 'ENTITY_ID');
    }
    
    /**
     * @return Result
     */
    public function flush()
    {
        return $this->_flush(false);
    }
    
    /**
     * @param bool $allTypes
     * @return Result
     */
    protected function _flush($allTypes = false)
    {
        $result = new Result();
        $table = $this->getDataManager();
        $connection = Application::getInstance()->getConnection($table::getConnectionName());
        $helper = $connection->getSqlHelper();
        
        $sql = sprintf(
            'DELETE FROM %1$s WHERE %2$s = %3$u',
            $helper->quote($table::getTableName()),
            $helper->quote('USER_ID'), $this->getUserId()
        );
        
        if (!$allTypes)
        {
            $sql .= sprintf(
                ' AND %1$s = \'%2$s\'',
                $helper->quote('ENTITY_TYPE'), $helper->forSql($this->getType())
            );
        }
        
        try
        {
            $connection->query($sql);
        }
        catch (\Exception $e)
        {
            $result->addError(new Error($e->getMessage()));
        }
        
        return $result;
    }

    /**
     * @return int
     */
    public function count()
    {
        $table = $this->getDataManager();

        return (int)$table::getCount([
            '=USER_ID' => $this->getUserId(),
            '=ENTITY_TYPE' => $this->getType(),
        ]);
    }
}
