<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Loader;
use Orm\Local\WorkDayController;
use Bitrix\Main\Context;

class JobStatsOrm extends CBitrixComponent
{
    /**
     * Валидация параметров
     *
     * @param $arParams
     * @return array
     */
    public function onPrepareComponentParams($arParams)
    {
        if (!$arParams["CACHE_TIME"]) {
            $arParams["CACHE_TIME"] = 3600;
        }

        return parent::onPrepareComponentParams($arParams);
    }

    /**
     *
     * Выполнение компонента
     *
     * @return void
     * @throws \Bitrix\Main\LoaderException
     */
    public function executeComponent()
    {
        if (!Loader::includeModule("orm.local")) {
            ShowError(GetMessage("EX2_70_IB_CHECK"));
            return;
        }

        if ($this->StartResultCache()) {

            $request = Context::getCurrent()->getRequest();
            $postValues = $request->getPostList()->toArray();

            if ($postValues['start']) {
                if (new WorkDayController('start')) {
                    echo 'Start working';
                }
            }

            if ($postValues['stop']) {
                if (new WorkDayController('stop')) {
                    echo 'Stop working';
                }
            }

            if ($postValues['pause']) {
                if (new WorkDayController('pause')) {
                    echo 'Pause working';
                }
            }

            if ($postValues['nopause']) {
                if (new WorkDayController('nopause')) {
                    echo 'No pause working';
                }
            }

            $this->includeComponentTemplate();
        }
    }
}