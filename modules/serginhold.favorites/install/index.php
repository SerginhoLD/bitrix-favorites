<?php
defined('B_PROLOG_INCLUDED') and (B_PROLOG_INCLUDED === true) or die();

use Bitrix\Main\Application;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;
use Bitrix\Main\IO\FileNotFoundException;
use SerginhoLD\Favorites\FavoritesTable;

// TODO: в разработке

Loc::loadMessages(__FILE__);

if (class_exists('serginhold_favorites'))
    return;

/**
 * Class serginhold_favorites
 */
class serginhold_favorites extends \CModule
{
    /** @var string id модуля */
    public $MODULE_ID = 'serginhold.favorites';
    
    /** @var string автор */
    public $PARTNER_NAME = 'Sergey Zubrilin';
    
    /** @var string url автора */
    public $PARTNER_URI = 'https://github.com/SerginhoLD';
    
    /**
     * bella_favorites constructor.
     */
    function __construct()
    {
        $this->MODULE_NAME = Loc::getMessage('FAVORITES_MODULE_NAME');
        
        include __DIR__ . '/version.php';
    
        if (isset($arModuleVersion['VERSION'], $arModuleVersion['VERSION_DATE']))
        {
            $this->MODULE_VERSION = $arModuleVersion['VERSION'];
            $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
        }
    }
    
    /**
     * Установка модуля
     */
    public function DoInstall()
    {
        $this->InstallDB();
        
        // TODO: CopyDirFiles
        
        ModuleManager::registerModule($this->MODULE_ID);
    }
    
    /**
     * Удаление модуля
     */
    public function DoUninstall()
    {
        $this->UnInstallDB();
        
        ModuleManager::unRegisterModule($this->MODULE_ID);
    }
    
    /**
     * SQL запрос
     * @param string $filename
     * @throws FileNotFoundException
     */
    protected function executeSqlFile($filename)
    {
        $oConnection = Application::getInstance()->getConnection();
        
        $sqlFile = __DIR__ . '/db/' . $oConnection->getType() . '/' . $filename;
        //var_dump($sqlFile); var_dump(file_get_contents($sqlFile)); exit;
        
        
        if (!is_file($sqlFile))
        {
            throw new FileNotFoundException($sqlFile);
        }
        
        $oConnection->executeSqlBatch(file_get_contents($sqlFile));
    }
    
    /**
     * Создание таблиц в базе данных
     */
    public function InstallDB()
    {
        $this->executeSqlFile('install.sql');
    }
    
    /**
     * Удаление таблиц в базе данных
     */
    public function UnInstallDB()
    {
        $this->executeSqlFile('uninstall.sql');
    }
}

