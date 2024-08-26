<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Loader;
use Orm\Local\OrmTable;

class ExampleOrm extends CBitrixComponent
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
            $this->setArResult();
            $this->includeComponentTemplate();
        }

    }

    /**
     * Производим выборку
     */
    public function setArResult()
    {
        $elements = OrmTable::query()
            ->setSelect([
                'ID',
                'NAME',
                'DATE_INSERT',
            ])->fetchAll();

        if (!$elements) {
            ShowError(GetMessage("EX2_70_IB_CHECK"));
            return;
        }

        foreach ($elements as $key => $element) {
            $elements[$key]['DATE_INSERT'] = date('Y-m-d H:i:s', strtotime($element['DATE_INSERT']));
        }
        $this->arResult["ITEMS"] = $elements;

    }

}
