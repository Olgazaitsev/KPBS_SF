<?php namespace Intervolga\Sed\Entities;

use Intervolga\Sed\Tables\ProcessTable;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class Process extends TableElement
{
    const INITIATOR_TTYPE_CODE = 'SED_INITIATOR_TTYPE';

    const INITIATOR_TSTATUS_CODE_NEW = 'NEW';
    const INITIATOR_TSTATUS_CODE_PAUSED = 'PAUSED';
    const INITIATOR_TSTATUS_CODE_PROGRESS = 'PROGRESS';
    const INITIATOR_TSTATUS_CODE_APPROVED = 'APPROVED';
    const INITIATOR_TSTATUS_CODE_NOT_APPROVED = 'NOT_APPROVED';

    const PARTICIPANT_TSTATUS_CODE_ACCEPTED = 'ACCEPTED';
    const PARTICIPANT_TSTATUS_CODE_PAUSED = 'PAUSED';
    const PARTICIPANT_TSTATUS_CODE_APPROVED = 'APPROVED';
    const PARTICIPANT_TSTATUS_CODE_NOT_APPROVED = 'NOT_APPROVED';
    const PARTICIPANT_TSTATUS_CODE_NOT_RELEVANT = 'NOT_RELEVANT';

    /**
     * @return \Bitrix\Main\Entity\Base
     */
    protected static function getEntity()
    {
        return ProcessTable::getEntity();
    }


    public function setName($value)
    {
        return $this->setFieldValue('NAME', $value);
    }

    public function getName()
    {
        return $this->getFieldValue('NAME');
    }


    public static function getAllProcessNames()
    {
        $processNames = array();
        $processList = static::getListAll();
        if(!empty($processList)) {
            foreach ($processList as $process) {
                $processNames[$process->getId()] = $process->getName();
            }
        }

        return $processNames;
    }

    public static function createInitiatorTType($processId)
    {
        try {
            $ttype = \Intervolga\Sed\Entities\TaskTypeElement::getByXmlId(static::INITIATOR_TTYPE_CODE);

            \Intervolga\Sed\Entities\ProcessTaskType::createEmpty()
                ->setProcessId($processId)
                ->setTaskTypeId($ttype->getId())
                ->save();
        }
        catch (\Bitrix\Main\ObjectNotFoundException $e) {
            $ttype = \Intervolga\Sed\Entities\TaskTypeElement::createEmpty()
                ->setCode(static::INITIATOR_TTYPE_CODE)
                ->setName(Loc::getMessage('PROCESS.INITIATOR'))
                ->save();

            \Intervolga\Sed\Entities\ProcessTaskType::createEmpty()
                ->setProcessId($processId)
                ->setTaskTypeId($ttype->getId())
                ->save();

            $statuses = array(
                static::INITIATOR_TSTATUS_CODE_NEW => array(
                    'NAME' => Loc::getMessage('PROCESS.ST_IN_NEW'),
                    'NATIVE_STATUS' => \CTasks::STATE_PENDING,
                ),
                static::INITIATOR_TSTATUS_CODE_PAUSED => array(
                    'NAME' => Loc::getMessage('PROCESS.ST_IN_PAUSED'),
                    'NATIVE_STATUS' => \CTasks::STATE_PENDING,
                ),
                static::INITIATOR_TSTATUS_CODE_PROGRESS => array(
                    'NAME' => Loc::getMessage('PROCESS.ST_IN_WORK'),
                    'NATIVE_STATUS' => \CTasks::STATE_IN_PROGRESS,
                ),
                static::INITIATOR_TSTATUS_CODE_APPROVED => array(
                    'NAME' => Loc::getMessage('PROCESS.ST_IN_AGREED'),
                    'NATIVE_STATUS' => \CTasks::STATE_COMPLETED,
                ),
                static::INITIATOR_TSTATUS_CODE_NOT_APPROVED => array(
                    'NAME' => Loc::getMessage('PROCESS.ST_IN_NOT_AGREED'),
                    'NATIVE_STATUS' => \CTasks::STATE_DECLINED,
                )
            );

            foreach ($statuses as $statusCode => $status) {
                \Intervolga\Sed\Entities\TaskStatusElement::createEmpty($ttype)
                    ->setCode($statusCode)
                    ->setName($status['NAME'])
                    ->setNativeTaskStatus($status['NATIVE_STATUS'])
                    ->save();
            }
        }
    }

    /**
     * @param int $processId
     * @param string $ttypeName
     * @param string $ttypeCode
     * @param int|null $ttypeSort
     * @return \Intervolga\Sed\Entities\TaskTypeElement
     */
    public static function createNonInitiatorTType($processId, $ttypeName, $ttypeCode, $ttypeSort = null)
    {
        $ttype = \Intervolga\Sed\Entities\TaskTypeElement::createEmpty()
            ->setName($ttypeName)
            ->setCode($ttypeCode)
            ->setSort($ttypeSort)
            ->save();

        \Intervolga\Sed\Entities\ProcessTaskType::createEmpty()
            ->setProcessId($processId)
            ->setTaskTypeId($ttype->getId())
            ->save();

        $statuses = array(
            static::PARTICIPANT_TSTATUS_CODE_ACCEPTED => array(
                'NAME' => Loc::getMessage('PROCESS.ST_PT_ACCEPTED'),
                'NATIVE_STATUS' => \CTasks::STATE_PENDING,
            ),
            static::PARTICIPANT_TSTATUS_CODE_PAUSED => array(
                'NAME' => Loc::getMessage('PROCESS.ST_PT_PAUSED'),
                'NATIVE_STATUS' => \CTasks::STATE_DEFERRED,
            ),
            static::PARTICIPANT_TSTATUS_CODE_APPROVED => array(
                'NAME' => Loc::getMessage('PROCESS.ST_PT_AGREED'),
                'NATIVE_STATUS' => \CTasks::STATE_COMPLETED,
            ),
            static::PARTICIPANT_TSTATUS_CODE_NOT_APPROVED => array(
                'NAME' => Loc::getMessage('PROCESS.ST_PT_NOT_AGREED'),
                'NATIVE_STATUS' => \CTasks::STATE_COMPLETED,
            ),
            static::PARTICIPANT_TSTATUS_CODE_NOT_RELEVANT => array(
                'NAME' => Loc::getMessage('PROCESS.ST_PT_NO_NEED_AGREE'),
                'NATIVE_STATUS' => \CTasks::STATE_DECLINED,
            )
        );

        foreach ($statuses as $statusCode => $status) {
            \Intervolga\Sed\Entities\TaskStatusElement::createEmpty($ttype)
                ->setCode($statusCode)
                ->setName($status['NAME'])
                ->setNativeTaskStatus($status['NATIVE_STATUS'])
                ->save();
        }

        return $ttype;
    }

    /**
     * @param $code
     * @return bool
     */
    public static function isTStatusCodeDefault($code)
    {
        return ($code == static::PARTICIPANT_TSTATUS_CODE_ACCEPTED || $code == static::PARTICIPANT_TSTATUS_CODE_PAUSED || $code == static::PARTICIPANT_TSTATUS_CODE_APPROVED
            || $code == static::PARTICIPANT_TSTATUS_CODE_NOT_APPROVED || $code == static::PARTICIPANT_TSTATUS_CODE_NOT_RELEVANT);
    }
}