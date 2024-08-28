<?php

declare(strict_types=1);

namespace Orm\Local;

use Bitrix\Im\Internals\Query;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\Engine\CurrentUser;
use Bitrix\Main\SystemException;
use Bitrix\Main\Type\DateTime;
use Exception;

/**
 *
 * Контроллер который обрабатывает запросы
 *
 */
class WorkDayController
{

    protected $profileEntity;
    protected $workDayEntity;
    protected $currentUser;
    protected $workDateElement;
    protected $dataWorkPauseDayRes;

    protected $dataWorkPauseDayResElement;


    /**
     * @param string $action
     * @throws ArgumentException
     * @throws SystemException
     */
    function __construct(string $action)
    {
        $this->init();


        switch ($action) {
            case 'start':
                $this->startWork();
                break;
            case 'stop':
                $this->stopWork();
                break;
            case 'pause':
                $this->startPause($this->dataWorkPauseDayRes, $this->workDateElement);
                break;
            case 'nopause':
                $this->stopPause($this->dataWorkPauseDayResElement);
                break;
        }
    }

    /**
     *
     * Инициализация
     *
     * @return void
     * @throws ArgumentException
     * @throws SystemException
     * @throws \Bitrix\Main\ObjectException
     * @throws \Bitrix\Main\ObjectPropertyException
     */
    public function init(): void
    {
        $this->profileEntity =  ProfileTable::getEntity();
        $this->workDayEntity =  WorkDayTable::getEntity();
        $this->workDayPauseEntity =  WorkDayPauseTable::getEntity();

        $dataUserRes =
            (new Query($this->profileEntity))
                ->setFilter([
                    'LOGIN' => CurrentUser::get()->getLogin(),
                ])
                ->setSelect(['ID', 'LOGIN'])
                ->whereNotNull('LOGIN')
                ->exec();

        $this->currentUser = $dataUserRes->fetch();


        $dataWorkDayRes =
            (new Query($this->workDayEntity))
                ->setFilter([
                    'PROFILE_ID' => $this->currentUser['ID'],
                    '>DATE_START' => new DateTime(DateTime::createFromTimestamp(time())->format('d.m.Y') .' 00:00:00'),
                ])
                ->setSelect(['ID', 'PROFILE_ID', 'DATE_START', 'DATE_STOP'])
                ->whereNotNull('DATE_START')
                ->exec();

        $this->workDateElement = $dataWorkDayRes->fetch();


        $dataWorkPauseDayRes =
            (new Query($this->workDayPauseEntity))
                ->setSelect(['ID', 'WORKDAY_ID', 'DATE_START', 'DATE_STOP'])
                ->whereNotNull('DATE_START')
                ->setOrder(['ID' => 'DESC'])
                ->exec();

        $this->dataWorkPauseDayRes = $dataWorkPauseDayRes;
        $this->dataWorkPauseDayResElement = $dataWorkPauseDayRes->fetch();

    }


    /**
     *
     * Метод начнет работу
     *
     * @return string
     *
     */
    public function startWork()
    {
        try {
                WorkDayTable::add([
                    'PROFILE_ID' => $this->currentUser['ID'],
                    'NAME' => CurrentUser::get()->getFirstName(),
                    'DATE_START' => new DateTime(),
                    'DATE_STOP' => null,
                ]);

        } catch (Exception $e) {
            return $e->getMessage() . "\n";
        }
    }


    /**
     *
     * Метод остановит работу
     *
     * @return string|void
     */
    public function stopWork()
    {
        try {
            $dataWorkDayRes =
                (new Query($this->workDayEntity))
                    ->setFilter([
                        'PROFILE_ID' => $this->currentUser['ID'],
                        '>DATE_START' => new DateTime(DateTime::createFromTimestamp(time())->format('d.m.Y') .' 00:00:00'),
                    ])
                    ->setSelect(['ID', 'PROFILE_ID', 'DATE_START', 'DATE_STOP'])
                    ->whereNotNull('DATE_START')
                    ->exec();

            $workDateElement = $dataWorkDayRes->fetch();

            WorkDayTable::update($workDateElement['ID'],[
                'DATE_STOP' => new DateTime,
            ]);

        } catch (Exception $e) {
            return $e->getMessage() . "\n";
        }
    }

    /**
     *
     * Метод поставит на паузу работу
     *
     * @return string|void
     */
    public function startPause($dataWorkPauseDayRes, $workDateData)
    {
        try {
            if ($dataWorkPauseDayRes->getSelectedRowsCount() == 0)
            {
                WorkDayPauseTable::add([
                    'WORKDAY_ID' => $workDateData['ID'],
                    'DATE_START' => new DateTime,
                    'DATE_STOP' => null,
                ]);

            }

            WorkDayPauseTable::add([
                'WORKDAY_ID' => $workDateData['ID'],
                'DATE_START' => new DateTime,
                'DATE_STOP' => null,
            ]);

            if ($dataWorkPauseDayRes->getSelectedRowsCount() > 1) {
                return;
            }

        } catch (Exception $e) {
            return $e->getMessage() . "\n";
        }
    }

    /**
     *
     * Метод уберет паузу с работы
     *
     * @param $dataWorkPauseDay
     * @return string|void
     */
    public function stopPause($dataWorkPauseDay)
    {
        try {

            WorkDayPauseTable::update($dataWorkPauseDay['ID'],[
                'DATE_STOP' => new DateTime(),
            ]);

        } catch (Exception $e) {
            return $e->getMessage() . "\n";
        }
    }
}
