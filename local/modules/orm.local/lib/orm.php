<?php

namespace Orm\Local;

use Bitrix\Main\ORM;
use Bitrix\Main\ORM\Data\DataManager;
use \Bitrix\Main\Type\DateTime;

class OrmTable extends DataManager
{
    /**
     * Имя таблицы
     */
    public static function getTableName()
    {
        return 'my_orm_table';
    }

    /**
     * Описание таблицы
     */
    public static function getMap()
    {
        return [
            new ORM\Fields\IntegerField('ID', [
                'primary'      => true,
                'autocomplete' => true,
            ]),
            new ORM\Fields\StringField('NAME', [
                'required' => true,
            ]),
            new ORM\Fields\DatetimeField('DATE_INSERT', [
                'required' => true,
                'default_value' => new DateTime(),
            ]),
        ];
    }
}
