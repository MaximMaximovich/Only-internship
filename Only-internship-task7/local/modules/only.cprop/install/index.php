<?

use Bitrix\Main\EventManager;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;

class only_cprop extends CModule
{
    const MODULE_ID = 'only.cprop';

    public $MODULE_ID = 'only.cprop',
      $MODULE_VERSION,
      $MODULE_VERSION_DATE,
      $MODULE_NAME,
      $PARTNER_NAME,
      $MODULE_DESCRIPTION,
      $PARTNER_URI,
      $MODULE_GROUP_RIGHTS;

    public function __construct()
    {
        $arModuleVersion = array();
        include __DIR__ . '/version.php';

        if (is_array($arModuleVersion) && array_key_exists('VERSION', $arModuleVersion)) {
            $this->MODULE_VERSION = $arModuleVersion['VERSION'];
            $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
        }

        $this->MODULE_NAME = Loc::getMessage('ONLY_CPROP_MODULE_NAME');
        $this->PARTNER_NAME = Loc::getMessage('ONLY_CPROP_PARTNER_NAME');
        $this->MODULE_DESCRIPTION = Loc::getMessage('ONLY_CPROP_MODULE_DESCRIPTION');
        $this->PARTNER_URI = Loc::getMessage('ONLY_CPROP_PARTNER_URI');
        $this->MODULE_GROUP_RIGHTS = 'N';


        $this->FILE_PREFIX = 'cprop';
        $this->MODULE_FOLDER = str_replace('.', '_', $this->MODULE_ID);
        $this->FOLDER = 'bitrix';

        $this->INSTALL_PATH_FROM = '/' . $this->FOLDER . '/modules/' . $this->MODULE_ID;

    }

    function isVersionD7()
    {
        return CheckVersion(ModuleManager::getVersion('main'), '14.00.00');
    }

    function DoInstall()
    {
        global $APPLICATION;
        if ($this->isVersionD7()) {
            $this->InstallDB();
            $this->InstallEvents();
            $this->InstallFiles();

            ModuleManager::registerModule($this->MODULE_ID);
        } else {
            $APPLICATION->ThrowException(Loc::getMessage('ONLY_CPROP_INSTALL_ERROR_VERSION'));
        }
    }

    function DoUninstall()
    {
        ModuleManager::unRegisterModule($this->MODULE_ID);

        $this->UnInstallFiles();
        $this->UnInstallEvents();
        $this->UnInstallDB();
    }


    function InstallDB()
    {
        return true;
    }

    function UnInstallDB()
    {
        return true;
    }

    function installFiles()
    {
        return true;
    }

    function uninstallFiles()
    {
        return true;
    }

    function getEvents()
    {
        return [
          ['HANDLER'=>'CIBlockPropertyCProp', 'FROM_MODULE' => 'iblock', 'EVENT' => 'OnIBlockPropertyBuildList', 'TO_METHOD' => 'GetUserTypeDescription'],
          ['HANDLER'=>'CUserTypeCProp', 'FROM_MODULE' => 'main', 'EVENT' => 'OnUserTypeBuildList', 'TO_METHOD' => 'GetUserTypeDescription'],
        ];
    }

    function InstallEvents()
    {

        $eventManager = EventManager::getInstance();

        $arEvents = $this->getEvents();
        foreach ($arEvents as $arEvent) {
            $eventManager->registerEventHandler(
              $arEvent['FROM_MODULE'],
              $arEvent['EVENT'],
              $this->MODULE_ID,
              $arEvent['HANDLER'],
              $arEvent['TO_METHOD']
            );
        }

        return true;
    }

    function UnInstallEvents()
    {

        $eventManager = EventManager::getInstance();

        $arEvents = $this->getEvents();
        foreach ($arEvents as $arEvent) {
            $eventManager->unregisterEventHandler(
              $arEvent['FROM_MODULE'],
              $arEvent['EVENT'],
              $this->MODULE_ID,
              $arEvent['HANDLER'],
              $arEvent['TO_METHOD']
            );
        }

        return true;
    }

}
