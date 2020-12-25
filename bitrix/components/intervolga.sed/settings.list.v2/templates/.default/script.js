;function CSTSettingsList(params) {
    this.sortByInput = document.getElementById('cts-settings__list__sort-by');
    this.sortOrderInput = document.getElementById('cts-settings__list__sort-order');
    this.form = document.getElementById('cts-settings__list__form');
    this.cancelFilterBtn = document.getElementById('cts-settings__list__cancel-filter');
    this.sortableColumnHeaders = document.getElementsByClassName('cts-settings__list__field-header adm-list-table-cell-sort');
    this.filterInputs = document.getElementsByClassName('cts-settings__list__filter-input');
    this.tableRows = document.getElementsByClassName('cts-settings__list__table-row');

    this.detailUrl = params.detailUrl;
    this.detailPageParam = params.detailPageParam;
    this.ajaxUrl = params.ajaxUrl;
    this.sessId = params.sessId;
    this.deleteActionName = params.deleteActionName;
    this.deleteActionExtraParams = params.deleteActionExtraParams;
    this.disableDeleteAction = (params.disableDeleteAction == 'Y');

    this.popup = null;

    if(this.checkRequired()) {
        this.bindHandlers();
    }
};

CSTSettingsList.prototype.bindHandlers = function () {
    var self = this;

    // сброс фильтра
    this.cancelFilterBtn.addEventListener('click', function () {
        for (var i = 0; i < self.filterInputs.length; i++) {
            self.filterInputs[i].value = '';
        }
        self.form.submit();
    }, false);

    // Сортировка по нажатию на заголовок таблицы
    for (var i = 0; i < this.sortableColumnHeaders.length; i++) {
        this.bindTableHeaderClick(this.sortableColumnHeaders[i]);
    }

    // Показ детальной страницы по двойному клику на элемент
    if(!!this.detailUrl && !!this.tableRows.length) {
        for (var rowIndex = 0; rowIndex < this.tableRows.length; rowIndex++) {

            var entityId = this.tableRows[rowIndex].getAttribute('data-id');
            if(!!entityId) {
                this.bindTableRowDoubleClick(this.tableRows[rowIndex], entityId);

                var popupBlock = this.tableRows[rowIndex].getElementsByClassName('adm-list-table-popup-block');
                if(!!popupBlock[0]) {
                    this.bindRowPopup(popupBlock[0], entityId);
                }
            }
        }
    }

    // постраничная навигация, изменение количества элементов на странице
    var paginationSizeSelector = document.getElementsByClassName('cts-settings__list__pagination-size-select')[0];
    if(!!paginationSizeSelector) {
        paginationSizeSelector.addEventListener('change', function () {
            self.reloadWithPaginationSizeChange(this.value);
        }, false);
    }

};

CSTSettingsList.prototype.reloadWithPaginationSizeChange = function (pageSize) {
    var parsedGetParams = document.location.search.substr(1).split('&');
    var keyValuePair;
    var paramFound = false;

    for (var i = 0; i < parsedGetParams.length; i++) {
        keyValuePair = parsedGetParams[i].split('=');

        if (keyValuePair[0] == 'SIZEN') {
            keyValuePair[1] = pageSize;
            parsedGetParams[i] = keyValuePair.join('=');
            paramFound = true;
            break;
        }
    }

    if(!paramFound) {
        parsedGetParams[parsedGetParams.length] = ['SIZEN', pageSize].join('=');
    }

    document.location.search = parsedGetParams.join('&');
};

CSTSettingsList.prototype.getPopupBlock = function (type, labelText) {
    var block = document.createElement('span');
    block.classList.add('bx-core-popup-menu-item');

    var icon = document.createElement('span');
    icon.classList.add('bx-core-popup-menu-item-icon');

    if(type == 'edit') {
        icon.classList.add('adm-menu-edit');
    }
    else if(type == 'delete') {
        icon.classList.add('adm-menu-delete');
    }

    var label = document.createElement('span');
    label.classList.add('bx-core-popup-menu-item-text');
    label.innerText = labelText;

    block.appendChild(icon);
    block.appendChild(label);

    return block;
};

CSTSettingsList.prototype.initPopup = function () {
    var self = this;

    this.popup = document.createElement('div');
    this.popup.classList.add('bx-core-popup-menu', 'bx-core-popup-menu-bottom', 'bx-core-popup-menu-level0');
    this.popup.style.zIndex = 1000;
    this.popup.style.position = 'absolute';
    this.popup.style.display = 'none';
    this.popup.style.height = 'auto';
    this.popup.style.width = 'auto';

    var angle = document.createElement('span');
    angle.classList.add('bx-core-popup-menu-angle');
    angle.style.left = '11px';

    var editBlock = this.getPopupBlock('edit', BX.message('SET_DET_TRG_TPL.JS_BTN_CHANGE'));

    this.popup.appendChild(angle);
    this.popup.appendChild(editBlock);

    if(!this.disableDeleteAction) {
        var deleteBlock = this.getPopupBlock('delete', BX.message('SET_DET_TRG_TPL.JS_BTN_DEL'));
        this.popup.appendChild(deleteBlock);

        deleteBlock.addEventListener('click', function () {
            self.callAjaxAction(self.deleteActionName, self.popup.getAttribute('data-entity-id'));

        }, false);
    }

    document.body.appendChild(this.popup);

    editBlock.addEventListener('click', function () {
        self.popupEditClickHandler();
    }, false);

    document.addEventListener('click', function(e) {
        var popupNode = e.target.closest('.bx-core-popup-menu');
        var popupBlockNode = e.target.closest('.adm-list-table-popup-block');
        if (!popupNode && !popupBlockNode) {
            // console.log('clicked outside the popup');
            self.popup.style.display = 'none';
        }
    }, false);
};

CSTSettingsList.prototype.showPopup = function (entityId, coords) {
    if(!this.popup) {
        this.initPopup();
    }

    this.popup.style.display = 'block';
    this.popup.style.top = coords.Y + 'px';
    this.popup.style.left = coords.X + 'px';
    this.popup.setAttribute('data-entity-id', entityId);
};

CSTSettingsList.prototype.bindRowPopup = function (node, entityId) {
    var self = this;

    node.addEventListener('click', function (event) {
        self.showPopup(entityId, self.getNodeCenteredCoords(node));
    }, false);
};

CSTSettingsList.prototype.getNodeCenteredCoords = function (node) {
    var bodyRect = document.body.getBoundingClientRect();
    var nodeRect = node.getBoundingClientRect();

    return {
        Y: (nodeRect.top - bodyRect.top) + nodeRect.height,
        X: (nodeRect.left - bodyRect.left)
    };
};

CSTSettingsList.prototype.popupEditClickHandler = function () {
    this.goToDetail(this.popup.getAttribute('data-entity-id'));
};

CSTSettingsList.prototype.goToDetail  = function (entityId) {
    if(!!entityId) {
        window.location.href = this.setUrlParam(this.detailUrl, this.detailPageParam, entityId);
    }
};

CSTSettingsList.prototype.bindTableRowDoubleClick = function (node, entityId) {
    var self = this;

    node.addEventListener('dblclick', function () {
        self.goToDetail(entityId);
    }, false);
};

CSTSettingsList.prototype.setUrlParam = function (url, paramName, paramValue) {
    var querySymbolIndex = url.indexOf('?');
    if(querySymbolIndex == -1) {
        return (url + '?' + paramName + '=' + paramValue);
    }
    else {
        var params = url.substr(querySymbolIndex + 1).split('&');
        var paramsIndex = params.length;
        while(paramsIndex--) {
            var paramPair = params[paramsIndex].split('=');

            if(paramPair[0] == paramName) {
                paramPair[1] = paramValue;
                params[paramsIndex] = paramPair.join('=');
                break;
            }
        }

        if(paramsIndex < 0) {
            params[params.length] = [paramName,paramValue].join('=');
        }

        return url.substr(0, querySymbolIndex) + '?' + params.join('&');
    }
};

CSTSettingsList.prototype.bindTableHeaderClick = function (node) {
    var self = this;

    node.addEventListener('click', function () {
        var fieldToSort = node.getAttribute('data-sort-by');
        if(self.sortByInput.value == fieldToSort) {
            self.sortOrderInput.value = (self.sortOrderInput.value == 'DESC') ? 'ASC' : 'DESC';
            self.form.submit();
        }
        else {
            self.sortByInput.value = fieldToSort;
            self.sortOrderInput.value = 'ASC';
            self.form.submit();
        }

    }, false);
};

CSTSettingsList.prototype.checkRequired = function () {
    return (!!this.form && !!this.sortByInput && !!this.sortOrderInput && !!this.sortableColumnHeaders.length && !!this.filterInputs.length && !!this.cancelFilterBtn);
};

CSTSettingsList.prototype.setFormBlock = function (flag) {
    if(!flag) {
        this.form.classList.remove('blocked');
    }
    else {
        this.form.classList.add('blocked');
    }
};

CSTSettingsList.prototype.callAjaxAction = function (action, entityId) {
    var self = this;
    var actionParams = {entityId: entityId};

    if(typeof this.deleteActionExtraParams == 'object') {
        for (var paramKey in this.deleteActionExtraParams) {
            if(this.deleteActionExtraParams.hasOwnProperty(paramKey)) {
                actionParams[paramKey] = this.deleteActionExtraParams[paramKey];
            }
        }
    }

    this.setFormBlock(true);

    BX.ajax({
        url: self.ajaxUrl,
        method: 'post',
        dataType: 'json',
        async: true,
        processData: true,
        emulateOnload: true,
        start: true,
        data: {
            action: action,
            params: actionParams,
            sessId: self.sessId
        },
        cache: false,
        onsuccess: BX.delegate(function(result){
            if(!!result.errorInfo) {
                self.processAjaxError(result.errorInfo);
                this.setFormBlock(false);
            }
            else {
                window.location.reload();
            }
        }, self),
        onfailure: BX.delegate(function(type, e){
            this.setFormBlock(false);
        }, self)
    });
};

CSTSettingsList.prototype.processAjaxError = function (errorInfo) {
    alert(errorInfo.description);
};