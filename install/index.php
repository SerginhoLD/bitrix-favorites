<?php
defined('B_PROLOG_INCLUDED') and (B_PROLOG_INCLUDED === true) or die();

use Bitrix\Main\Application;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;
use Bitrix\Main\EventManager;
use Bitrix\Main\IO\Directory;
use Bitrix\Main\IO\FileNotFoundException;
use SerginhoLD\Favorites\FavoritesTable;

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
     * serginhold_favorites constructor.
     */
    function __construct()
    {
        $this->MODULE_NAME = Loc::getMessage('FAVORITES_MODULE_NAME');
        
        include __DIR__ . '/version.php';
        
        if (isset($arModuleVersion, $arModuleVersion['VERSION'], $arModuleVersion['VERSION_DATE']))
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
        $this->InstallFiles();
        
        ModuleManager::registerModule($this->MODULE_ID);
        
        $this->InstallEvents();
    }
    
    /**
     * Удаление модуля
     */
    public function DoUninstall()
    {
        $this->UnInstallFiles();
        $this->UnInstallEvents();
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
     * Удаление таблиц из базы данных
     */
    public function UnInstallDB()
    {
        $this->executeSqlFile('uninstall.sql');
    }
    
    /**
     * @return string
     */
    private function getBxDir()
    {
        $modulePath = str_replace([DIRECTORY_SEPARATOR, Application::getDocumentRoot()], ['/', null], __FILE__);
        
        if (mb_strpos($modulePath, '/local/') === 0)
            return 'local';
        
        return 'bitrix';
    }
    
    /**
     * Копирует файлы
     */
    public function InstallFiles()
    {
        CopyDirFiles(
            __DIR__ . '/components',
            Application::getDocumentRoot() . '/' . $this->getBxDir() . '/components',
            true, true
        );
    }
    
    /**
     * Удаляет файлы
     */
    public function UnInstallFiles()
    {
        $oComponentsDir = new Directory(__DIR__ . '/components/serginhold');
        $arChildrenDir = $oComponentsDir->getChildren();
        $localComponentsDir = Application::getDocumentRoot() . '/' . $this->getBxDir() . '/components/serginhold';
        
        foreach ($arChildrenDir as $oDir)
        {
            if ($oDir->isDirectory())
            {
                $localDir = $localComponentsDir . '/' . $oDir->getName();
                
                if (is_dir($localDir))
                    Directory::deleteDirectory($localDir);
            }
        }
    }
    
    /**
     * Регистрация событий
     */
    public function InstallEvents()
    {
        $oEventManager = EventManager::getInstance();
        
        $oEventManager->registerEventHandler('main', 'OnAfterUserAuthorize',
            $this->MODULE_ID, FavoritesTable::class, 'OnAfterUserAuthorize');
    }
    
    /**
     * Удаление событий
     */
    public function UnInstallEvents()
    {
        $oEventManager = EventManager::getInstance();
        
        $oEventManager->unRegisterEventHandler('main', 'OnAfterUserAuthorize',
            $this->MODULE_ID, FavoritesTable::class, 'OnAfterUserAuthorize');
    }
}

