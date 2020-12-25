<?php namespace Intervolga\Sed\Admin;

use \Bitrix\Main\Localization\Loc;
use \Bitrix\Main\Web\Uri;

Loc::loadMessages(__FILE__);

abstract class SettingsList extends AbstractSettings
{
    /** @var array $fields */
    protected $fields;
    /** @var array $filter */
    protected $filter;
    /** @var array $sort */
    protected $sort;
    /** @var array $extraFormInputs */
    protected $extraFormInputs;
    /** @var array $pagination */
    protected $pagination;

    /**
     * @return string
     */
    abstract protected function getAddBtnLabel();

    abstract protected function getData();

    abstract protected function initTableData();


    public function __construct(array $params = array())
    {
        parent::__construct($params);

        $this->prepareParams();
        $this->initFields();
        $this->initFilter();
        $this->initSort();
        $this->getData();
        $this->fillResult();
    }

    /**
     * @param array $filter
     * @return int
     * @throws \Bitrix\Main\NotImplementedException();
     */
    protected function getDataCountByFilter($filter)
    {
        throw new \Bitrix\Main\NotImplementedException();
    }

    protected function prepareParams()
    {
        if (!strlen($this->params['LIST_PAGE_URL'])) {
            $this->params['LIST_PAGE_URL'] = $this->request->getRequestedPage();
        }
    }

    protected function initFields()
    {
        $this->fields = array(
            'ID' => array(
                'LABEL' => 'ID',
                'TYPE' => 'INPUT',
                'USED_BY_FILTER' => 'Y'
            )
        );
    }

    protected function initFilter()
    {
        $this->filter = array();

        foreach ($this->fields as $fieldCode => $field) {
            if ($field['USED_BY_FILTER'] == 'Y') {
                $requestValue = trim($this->request->getQuery($fieldCode));
                if (strlen($requestValue)) {
                    $this->filter[$fieldCode] = $requestValue;
                }
            }
        }
    }

    protected function initSort()
    {
        $by = mb_strtoupper($this->request->getQuery('BY'));
        if (strlen($by) && $this->fields[$by] && ($this->fields[$by]['USED_BY_FILTER']) == 'Y') {
            $this->sort = array(
                'BY' => $by,
                'ORDER' => (mb_strtoupper($this->request->getQuery('ORDER')) == 'DESC') ? 'DESC' : 'ASC'
            );
        } else {
            $this->sort = array(
                'BY' => 'ID',
                'ORDER' => 'DESC'
            );
        }
    }

    /**
     * @param array $filter
     */
    protected function initPagination($filter)
    {
        $allowedPageSizes = array(10, 20, 50, 100, 500);

        $pageNumber = (int)$this->request->getQuery('PAGEN');
        $pageSize = (int)$this->request->getQuery('SIZEN');

        $className = $this->getClassNameWithoutNS();

        $previousPageNumber =& $_SESSION['SED_PAGINATION'][$className]['PAGEN'];
        $previousPageNumber = (int)$previousPageNumber;

        $previousPageSize =& $_SESSION['SED_PAGINATION'][$className]['SIZEN'];
        $previousPageSize = (int)$previousPageSize;

        $elementsCount = $this->getDataCountByFilter($filter);


        if ($pageSize > 0) {
            
           if ($pageSize > $elementsCount) {
               $pageSize = $elementsCount;
           }

            $maxPageNumber = ceil($elementsCount / $pageSize);

           if ($pageSize != $previousPageSize) {
               $pageNumber = $previousPageNumber = 1;
               $previousPageSize = $pageSize;
           }
           else {
               if ($pageNumber > $maxPageNumber) {
                   $pageNumber = $maxPageNumber;
               }
               else if ($pageNumber < 1) {
                   $pageNumber = 1;
               }
               
               if ($pageNumber != $previousPageNumber) {
                   $previousPageNumber = $pageNumber;
               }
           }
        }
        else {
            $pageSize = $previousPageSize;
            $pageNumber = $previousPageNumber;
            
            if ($pageSize < 1 || $pageNumber < 1) {
                $pageSize = $previousPageSize = $allowedPageSizes[0];
                $pageNumber = $previousPageNumber = 1;
            }

            $maxPageNumber = ceil($elementsCount / $pageSize);
        }

        $this->pagination = array(
            'LIMIT' => $pageSize,
            'OFFSET' => ($pageNumber - 1) * $pageSize,
            'ELEMENTS_TO_DISPLAY' => $this->getPageNumbersToDisplay($pageNumber, $pageSize, $maxPageNumber, $elementsCount, $allowedPageSizes)
        );
    }

    /**
     * @param int $pageNumber
     * @param Uri|null $uriInstance
     * @return string
     */
    protected function getUriWithPaginationParam($pageNumber, $pageSize, $uriInstance)
    {
        if (!($uriInstance instanceof Uri)) {
            $request = \Bitrix\Main\Application::getInstance()->getContext()->getRequest();
            $uriInstance = new Uri($request->getRequestUri());
        }

        return $uriInstance->addParams(array('PAGEN' => $pageNumber, 'SIZEN' => $pageSize))->getUri();
    }

    protected function getPageNumbersToDisplay($pageNumber, $pageSize, $maxPageNumber, $elementsCount, $allowedPageSizes)
    {
        $result = array(
            'PAGE_NUMBER' => $pageNumber,
            'MAX_PAGE_NUMBER' => $maxPageNumber,
            'PAGE_SIZE' => $pageSize,
            'CNT' => $elementsCount,
            'LINKS' => array(),
            'ALLOWED_PAGE_SIZES' => array(),
            'TOTAL_BLOCK' => array()
        );

        $request = \Bitrix\Main\Application::getInstance()->getContext()->getRequest();
        $uri = new Uri($request->getRequestUri());

        // pages block
        if ($pageNumber < 5) {
            for ($index = 1; $index < $pageNumber; ++$index) {
                $result['LINKS'][] = array('VALUE' => $index, 'VISIBLE' => true, 'HREF' => static::getUriWithPaginationParam($index, $pageSize, $uri));
            }
        }
        else {
            $mediumPageNumber = floor($pageNumber / 2);
            $result['LINKS'][] = array('VALUE' => 1, 'VISIBLE' => true, 'HREF' => static::getUriWithPaginationParam(1, $pageSize, $uri));
            $result['LINKS'][] = array('VALUE' => $mediumPageNumber, 'VISIBLE' => false, 'HREF' => static::getUriWithPaginationParam($mediumPageNumber, $pageSize, $uri));
            $result['LINKS'][] = array('VALUE' => $pageNumber - 1, 'VISIBLE' => true, 'HREF' => static::getUriWithPaginationParam($pageNumber - 1, $pageSize, $uri));
        }

        $result['LINKS'][] = array('VALUE' => $pageNumber, 'VISIBLE' => true, 'HREF' => static::getUriWithPaginationParam($pageNumber, $pageSize, $uri));

        if (($maxPageNumber - $pageNumber) < 5) {
            for ($index = $pageNumber + 1; $index <= $maxPageNumber; ++$index) {
                $result['LINKS'][] = array('VALUE' => $index, 'VISIBLE' => true, 'HREF' => static::getUriWithPaginationParam($index, $pageSize, $uri));
            }
        }
        else {
            $mediumPageNumber = ceil(($maxPageNumber + $pageNumber) / 2);
            $result['LINKS'][] = array('VALUE' => $pageNumber + 1, 'VISIBLE' => true, 'HREF' => static::getUriWithPaginationParam($pageNumber + 1, $pageSize, $uri));
            $result['LINKS'][] = array('VALUE' => $mediumPageNumber, 'VISIBLE' => false, 'HREF' => static::getUriWithPaginationParam($mediumPageNumber, $pageSize, $uri));
            $result['LINKS'][] = array('VALUE' => $maxPageNumber, 'VISIBLE' => true, 'HREF' => static::getUriWithPaginationParam($maxPageNumber, $pageSize, $uri));
        }

        // page-arrows-block
        if ($pageNumber != 1) {
            $result['PREV_PAGE_LINK'] = static::getUriWithPaginationParam($pageNumber - 1, $pageSize, $uri);
        }
        if ($pageNumber != $maxPageNumber) {
            $result['NEXT_PAGE_LINK'] = static::getUriWithPaginationParam($pageNumber + 1, $pageSize, $uri);
        }

        // pages-number block
        foreach ($allowedPageSizes as $allowedPageSize) {
            if ($allowedPageSize < $elementsCount) {
                $result['ALLOWED_PAGE_SIZES'][$allowedPageSize] = $allowedPageSize;
            }
        }
        $result['ALLOWED_PAGE_SIZES'][$elementsCount] = Loc::getMessage('CTS.ADMIN_LIST.PAGINATION_SIZE_ALL');

        // pages-total block
        $firstElementIndex = (($pageNumber - 1) * $pageSize) + 1;
        $lastElementIndex = $firstElementIndex + $pageSize;
        if ($lastElementIndex > $elementsCount) {
            $lastElementIndex = $elementsCount;
        }

        $result['TOTAL_BLOCK'] = array(
            'CNT' => $elementsCount,
            'FROM' => $firstElementIndex,
            'TO' => $lastElementIndex
        );

        return $result;
    }

    /**
     * @return string
     */
    protected function getClassNameWithoutNS()
    {
        return array_pop(explode('\\', get_class($this)));
    }

    protected function initTableHeaders()
    {
        $result = array();

        foreach ($this->fields as $fieldCode => $field) {
            $result[$fieldCode] = array(
                'LABEL' => $field['LABEL'],
                'SORTABLE' => $field['USED_BY_FILTER']
            );

            if ($this->sort['BY'] == $fieldCode) {
                $result[$fieldCode]['SORTED'] = $this->sort['ORDER'];
            }
        }

        return $result;
    }

    protected function fillResult()
    {
        if (count($this->errors)) {
            $this->result['ERROR_MSG'] = array(
                'TITLE' => Loc::getMessage('SED.ADMIN_LIST.ERROR_MSG.TITLE'),
                'BODY' => implode('<br>', $this->errors)
            );
        }
        else {
            $this->result['EXTRA_INPUTS'] = $this->extraFormInputs;
            $this->result['DEFAULT_OPTION'] = Loc::getMessage('SED.ADMIN_LIST.DEFAULT_OPTION');

            $this->result['SORT'] = array(
                'BY' => $this->sort['BY'],
                'ORDER' => $this->sort['ORDER'],
            );

            $this->result['PAGINATION'] = $this->pagination['ELEMENTS_TO_DISPLAY'];

            $this->result['FILTER'] = array(
                'TITLE' => Loc::getMessage('SED.ADMIN_LIST.FILTER.TITLE'),
                'SEARCH_BTN_LABEL' => Loc::getMessage('SED.ADMIN_LIST.FILTER.SEARCH_BTN_LABEL'),
                'SEARCH_CANCEL_BTN_LABEL' => Loc::getMessage('SED.ADMIN_LIST.FILTER.SEARCH_CANCEL_BTN_LABEL'),
                'FIELDS' => array()
            );

            foreach ($this->fields as $fieldCode => $field) {
                if ($field['USED_BY_FILTER'] == 'Y') {
                    $this->result['FILTER']['FIELDS'][$fieldCode] = array(
                        'LABEL' => $field['LABEL'],
                        'TYPE' => $field['TYPE'],
                        'OPTIONS' => $field['OPTIONS'],
                        'MULTIPLE' => $field['MULTIPLE'],
                        'VALUE' => $this->filter[$fieldCode]
                    );
                }
            }

            $this->result['TABLE'] = array(
                'ADD_ITEM_BTN' => array(
                    'LABEL' => static::getAddBtnLabel(),
                    'URL' => $this->params['DETAIL_PAGE_URL']
                ),
                'HEADERS' => $this->initTableHeaders(),
                'DATA' => $this->initTableData()
            );
        }
    }
}