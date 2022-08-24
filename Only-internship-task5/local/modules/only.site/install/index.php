<?

use Bitrix\Main\EventManager;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;

class only_site extends CModule
{
    const MODULE_ID = 'only.site';

    public $MODULE_ID = 'only.site',
      $MODULE_VERSION,
      $MODULE_VERSION_DATE,
      $MODULE_NAME,
      $PARTNER_NAME,
      $MODULE_DESCRIPTION;

    public function __construct()
    {
        $arModuleVersion = array();
        include __DIR__ . '/version.php';

        if (is_array($arModuleVersion) && array_key_exists('VERSION', $arModuleVersion)) {
            $this->MODULE_VERSION = $arModuleVersion['VERSION'];
            $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
        }

        $this->MODULE_NAME = Loc::getMessage('ONLY_DEV_SYTE_MODULE_NAME');
        $this->PARTNER_NAME = Loc::getMessage('ONLY_DEV_SYTE_PARTNER_NAME');
        $this->MODULE_DESCRIPTION = Loc::getMessage('ONLY_DEV_SYTE_MODULE_DESCRIPTION');
    }

    function InstallFiles($arParams = array())
    {
        return true;
    }

    function UnInstallFiles()
    {
        return true;
    }

    public function DoInstall()
    {
        ModuleManager::registerModule($this->MODULE_ID);

        if (Loader::includeModule($this->MODULE_ID)) {
            \CAgent::AddAgent( "\\Only\\Site\\Agents\\Iblock::clearOldLogs();", "only.site", "N", 3600, "", "Y");
            $this->installEvents();
        }
        return true;
    }

    public function DoUninstall()
    {

        ModuleManager::unregisterModule($this->MODULE_ID);
        \CAgent::RemoveModuleAgents("only.site");
        if (Loader::includeModule($this->MODULE_ID)) {
            $this->uninstallEvents();
        }
        return true;
    }

    /**
     * @return bool
     */
    public function installEvents()
    {

        EventManager::getInstance()
          ->registerEventHandler(
            'iblock',
            'OnAfterIBlockElementAdd',
            'only.site',
            '\\Only\\\Site\\Handlers\\Iblock',
            'addLog'
          );
        return true;

    }

    /**
     * @return bool
     */
    public function uninstallEvents()
    {
        EventManager::getInstance()
          ->unRegisterEventHandler(
            'iblock',
            'OnAfterIBlockElementAdd',
            'only.site',
            '\\Only\\\Site\\Handlers\\Iblock',
            'addLog'
          );
        return true;

    }
}
