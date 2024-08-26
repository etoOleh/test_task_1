<?php

namespace Orm\Local;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\ORM;
use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\SystemException;


class ProfileTable extends DataManager
{
    /**
     * Имя таблицы
     */
    public static function getTableName(): string
    {
        return 'my_profile_table';
    }

    /**
     * Описание таблицы
     * @throws ArgumentException|SystemException
     */
    public static function getMap(): array
    {
        return [
            new ORM\Fields\IntegerField('ID', [
                'primary' => true,
                'autocomplete' => true,
            ]),
            new ORM\Fields\StringField('LOGIN', [
                'required' => true,
            ]),
            new ORM\Fields\StringField('NAME', [
                'nullable' => true,
            ]),
            new ORM\Fields\StringField('LAST_NAME', [
                'nullable' => true,
            ]),
            new ORM\Fields\StringField('OFFSET', [
                'nullable' => true,
            ]),
        ];
    }
}
