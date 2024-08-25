<?php

use Bitrix\Main\Loader;
use Bitrix\Main\ORM\Entity;
use Bitrix\Main\Application;

class orm_local extends CModule
{
    function __construct()
    {
        $this->MODULE_ID = 'orm.local';
        $this->MODULE_VERSION = '1.0.0';
        $this->MODULE_VERSION_DATE = '2024-08-25 00:00:00';
        $this->MODULE_NAME = 'orm.local';
        $this->MODULE_DESCRIPTION = 'orm.local';
        $this->PARTNER_NAME = 'eto_oleh';
        $this->PARTNER_URI = 'eto_oleh@vk.com';
    }

    /**
     *
     * Регистрация модуля в системе
     *
     * @return void
     */
    function DoInstall()
    {
        \Bitrix\Main\ModuleManager::registerModule($this->MODULE_ID);
        $this->InstallDB();
    }

    /**
     *
     * Удаление модуля из системе
     *
     * @return void
     */
    function DoUninstall()
    {
        $this->UnInstallDB();
        \Bitrix\Main\ModuleManager::unRegisterModule($this->MODULE_ID);
    }

    /**
     *
     * Установка Базы Данных
     *
     * @return void
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\LoaderException
     * @throws \Bitrix\Main\SystemException
     */
    function InstallDB()
    {
        Loader::includeModule($this->MODULE_ID);

        if (!Application::getConnection(\Orm\Local\OrmTable::getConnectionName())->isTableExists(
            Entity::getInstance('\Orm\Local\OrmTable')->getDBTableName()
        )
        ) {
            Entity::getInstance('\Orm\Local\OrmTable')->createDbTable();
        }

    }

    /**
     *
     * Удаление базы
     *
     * @return void
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\DB\SqlQueryException
     * @throws \Bitrix\Main\LoaderException
     * @throws \Bitrix\Main\SystemException
     */
    function UnInstallDB()
    {
        Loader::includeModule($this->MODULE_ID);

        Application::getConnection(\Orm\Local\OrmTable::getConnectionName())->
        queryExecute('drop table if exists ' . Entity::getInstance('\Orm\Local\OrmTable')->getDBTableName());

    }
}