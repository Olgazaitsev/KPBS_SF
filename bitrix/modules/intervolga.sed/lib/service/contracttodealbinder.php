<?php
namespace intervolga\sed\service;


use Bitrix\Crm\DealTable;
use Bitrix\Crm\Timeline\CommentEntry;
use Bitrix\Disk\File;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Localization\Loc;
use CFile;
use Intervolga\Sed\Entities\Contract;
use Intervolga\Sed\Tables\ContractTable;


/**
 * Класс сервиса для управления связью между согласованием и сделкой, прикрепленной к нему.
 *
 * Class ContractFileToDealBinder
 *
 * @package intervolga\sed\service
 */
class ContractToDealBinder {
    /**
     * @var Contract - объект согласования, с которым взаимодействует сервис.
     */
    private $contract;

    /**
     * @var string - код пользовательского поля сделки, в которое прикрепляется файл договора.
     */
    private $dealFileUserFieldCode;

    /**
     * @var string - код пользовательского поля согласования, в котором хранится связь со сделкой.
     */
    private $contractDealUserFieldCode;

    /**
     * ContractToDealBinder constructor.
     *
     * @param int $contractId - id согласования.
     *
     * @throws \Bitrix\Main\ArgumentNullException
     * @throws \Bitrix\Main\ArgumentOutOfRangeException
     * @throws \Bitrix\Main\ObjectNotFoundException
     */
    public function __construct(int $contractId) {
        $this->contract = Contract::getById($contractId);
        $this->dealFileUserFieldCode = Option::get('intervolga.sed', 'intervolga_sed_deal_contract_file_code');
        $this->contractDealUserFieldCode = Option::get('intervolga.sed', 'intervolga_sed_contract_deal_code');
        if (!$this->isRequiredOptionsSet()) {
            throw new \RuntimeException(Loc::getMessage('REQUIRED_OPTIONS_NOT_SET_ERROR'));
        }
    }

    /**
     * Добавляет комментарий о добавлении согласования в сделку, указанную у него в пользовательском поле.
     *
     * @throws \Exception
     */
    public function addCommentToDeal(): void {
        $dealId = $this->getDealId();
        $this->createCommentForDeal(
                $dealId,
                Loc::getMessage('CONTRACT_ADDED_COMMENT_TEXT', array(
                        '#CONTRACT_ID#' => $this->contract->getId()
                ))
        );
    }

    /**
     * Прикрепляет файл договора из согласования к сделке и отправляет комментарий об этом.
     */
    public function attachContractFileToDeal(): void {
        $dealContractFileUserFieldInfo = $this->getUserFieldInfo($this->dealFileUserFieldCode, DealTable::getUfId(), 'file');
        if (empty($dealContractFileUserFieldInfo)) {
            throw new \RuntimeException(Loc::getMessage('NO_USER_FIELD_FOR_CONTRACT_FILE_ERROR'));
        }

        $dealId = $this->getDealId();

        try {
            $diskFileId = $this->contract->getFileId();
            $diskFile = File::getById($diskFileId);

            $fileId = $diskFile->getFileId();
            $fileInfo = CFile::makeFileArray($fileId);
            $fileInfo['name'] = $diskFile->getOriginalName();
            if ($dealContractFileUserFieldInfo['MULTIPLE'] == 'Y') {
                $fileInfo = array($fileInfo);
            }

            $updateDealFieldList = array(
                    $this->dealFileUserFieldCode => $fileInfo,
                    'MODIFY_BY_ID' => $this->contract->getReferenceUserId()
            );

            $deal = new \CCrmDeal(false);
            $deal->Update($dealId, $updateDealFieldList);
            if (!empty($deal->LAST_ERROR)) {
                throw new \RuntimeException($deal->LAST_ERROR);
            }

            $this->createCommentForDeal(
                    $dealId,
                    Loc::getMessage('CONTRACT_FILE_ATTACHED_COMMENT_TEXT')
            );
        } catch (\Exception $exception) {
            throw new \RuntimeException(Loc::getMessage('ATTACH_FILE_FAILURE_ERROR'), 0, $exception);
        }
    }

    /**
     * Возвращает информацию о пользовательском поле в виде массива.
     *
     * @param string $userFieldCode - код пользовательского поля.
     * @param string $entityId - код сущности, к которому привязано поле.
     * @param string $userFieldType - код типа пользовательского поля.
     *
     * @return array
     */
    private function getUserFieldInfo(string $userFieldCode, string $entityId, string $userFieldType = ''): array {
        if (empty($entityId)) {
            throw new \InvalidArgumentException($entityId);
        }

        $userFieldFilter = array(
                'FIELD_NAME' => $userFieldCode,
                'ENTITY_ID' => $entityId
        );

        if (!empty($userFieldType)) {
            $userFieldFilter['USER_TYPE_ID'] = $userFieldType;
        }

        $userFieldResult = \CUserTypeEntity::GetList(
                array(),
                $userFieldFilter
        );

        $userFieldInfo = $userFieldResult->Fetch();
        if (!$userFieldInfo) {
            return array();
        }

        return $userFieldInfo;
    }

    /**
     * Создает текстовый комментарий в сделке от лица пользователя, являющегося инициатором согласования.
     *
     * @param int $dealId - id сделки, для которой нужно создать комментарий.
     * @param string $commentText - текст комментария.
     *
     * @throws \Bitrix\Main\ArgumentException
     */
    private function createCommentForDeal(int $dealId, string $commentText): void {
        $entryId = CommentEntry::create(
                array(
                        'TEXT' => $commentText,
                        'SETTINGS' => array(
                                'HAS_FILES' => 'N'
                        ),
                        'AUTHOR_ID' => $this->contract->getReferenceUserId(),
                        'BINDINGS' => array(
                                array(
                                        'ENTITY_TYPE_ID' => \CCrmOwnerType::Deal,
                                        'ENTITY_ID' => $dealId
                                )
                        )
                )
        );

        if ($entryId <= 0) {
            throw new \RuntimeException(Loc::getMessage('CREATE_COMMENT_FAILURE_ERROR'));
        }
    }

    /**
     * Возвращает id сделки, указанной в соответствующем пользовательском поле согласования.
     *
     * @return int - id сделки.
     */
    private function getDealId(): int {
        $contractDealUserFieldInfo = $this->getUserFieldInfo($this->contractDealUserFieldCode, ContractTable::getUfId(), 'crm');
        if (empty($contractDealUserFieldInfo)) {
            throw new \RuntimeException(Loc::getMessage('NO_USER_FIELD_FOR_DEAL_REFERENCE_ERROR'));
        }

        $dealId = intval($this->contract->getUserFieldValue($this->contractDealUserFieldCode));

        $isDealExists = \CCrmDeal::Exists($dealId);
        if (!$isDealExists) {
            throw new \RuntimeException(Loc::getMessage('DEAL_NOT_FOUND_ERROR'));
        }

        return $dealId;
    }

    /**
     * Возвращает, установлены ли необходимые настройки в модуле.
     *
     * @return bool
     */
    private function isRequiredOptionsSet(): bool {
        return !empty($this->contractDealUserFieldCode) && !empty($this->dealFileUserFieldCode);
    }
}
