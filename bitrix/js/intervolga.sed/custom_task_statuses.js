var CustomTaskStatuses = BX.namespace('CustomTaskStatuses');

CustomTaskStatuses.hideExtraUfInTaskEdit = function (fieldToExcludeIds) {
    var fields = BX.findChildrenByClassName(document, 'js-id-item-set-item tasks-uf-panel-row tasks', true);
    fields.forEach(function (element) {
        if (fieldToExcludeIds.indexOf(element.getAttribute('data-item-value')) !== -1) {
            // element.style.display = "none";
            element.remove();
        }
    });
};

CustomTaskStatuses.hideExtraUfInTaskDetail = function (ufText) {
    var elementNames = BX.findChildrenByClassName(document, 'task-detail-property-name', true);
    elementNames.forEach(function (element) {
        if (element.innerText == ufText) {
            element.parentNode.remove();
        }
    });
};

CustomTaskStatuses.reCountLogElementsInTaskDetail = function () {
    var taskTable = BX('task-log-table');
    if (!!taskTable) {
        var logElements = BX.findChildren(taskTable, {tag: 'tr'}, true);
        var counter = BX('task-switcher-text-log-count');
        if (logElements.length > 1 && !!counter) {
            counter.innerText = logElements.length - 1; // без заголовка таблицы
        }
    }
};

CustomTaskStatuses.changeStatusNameInSidebar = function (statusName) {
    if (!!statusName) {
        var statusNameContainer = BX('task-detail-status-name');
        if (!!statusNameContainer) {
            statusNameContainer.innerText += ' / ' + statusName;
        }
    }
};

CustomTaskStatuses.removeContractItem = function (contractId, sessId) {
    var self = this;

    BX.ajax({
        url: '/bitrix/tools/intervolga.sed/ajax.php',
        method: 'post',
        dataType: 'json',
        async: true,
        processData: true,
        emulateOnload: true,
        start: true,
        data: {
            action: 'DeleteContract',
            params: {
                entityId: contractId
            },
            sessId: sessId
        },
        cache: false,
        onsuccess: BX.delegate(function (result) {
            console.log(result);

            if (!!result.errorInfo) {
                alert(result.errorInfo.description);
            }
            else {
                window.location.reload();
            }
        }, self),
        onfailure: BX.delegate(function (type, e) {
        }, self)
    });
};

CustomTaskStatuses.widgetButtonsHandler = function () {
    var self = this;
    self.buttonsPanel = null;
    self.buttonsContainer = null;
    self.queryInstance = null;
    self.taskId = null;
    self.taskTypeId = null;
    self.userId = null;
    self.sessId = null;
    self.currentStatusId = null;
    self.errorPopup = null;
    self.ajaxUrl = '/bitrix/tools/intervolga.sed/ajax.php';
    self.customButtonsNodes = [];
    self.transitionsScheme = {};
    self.buttonsToRemoveCodes = ['START', 'PAUSE', 'COMPLETE', 'APPROVE', 'DISAPPROVE'];

    self.init = function (params) {
        self.currentStatusId = parseInt(params.currentStatusId);
        self.transitionsScheme = params.transitionsScheme;
        self.taskId = parseInt(params.taskId);
        self.taskTypeId = parseInt(params.taskTypeId);
        self.userId = parseInt(params.userId);
        self.sessId = params.sessId;

        self.buttonsContainer = BX.findChild(document, {attribute: {'data-bx-id': "task-view-b-buttonset"}}, true);
        self.buttonsPanel = BX('bx-component-scope-bitrix_tasks_widget_buttonstask_1');

        self.createCustomButtonsNodes();
        self.replaceButtons();
    };

    self.replaceButtons = function () {
        if (!!self.buttonsContainer) {
            var defaultButtons = BX.findChildren(self.buttonsContainer, {attribute: {'data-bx-id': "task-view-b-button"}});
            if (defaultButtons.length > 0) {
                defaultButtons.forEach(function (element, index) {
                    var dataAction = element.getAttribute('data-action');
                    if (!!dataAction && self.buttonsToRemoveCodes.indexOf(dataAction) !== -1) {
                        element.remove();
                    }
                });
            }

            self.customButtonsNodes.forEach(function (element) {
                BX.prepend(element.node, self.buttonsContainer);
            });
        }
    };

    self.createCustomButtonsNodes = function () {
        if (!self.currentStatusId || typeof self.transitionsScheme[self.currentStatusId] == 'undefined') {
            return;
        }

        self.customButtonsNodes = [];
        for (var nextStatusId in self.transitionsScheme[self.currentStatusId]) {
            if (self.transitionsScheme[self.currentStatusId].hasOwnProperty(nextStatusId)) {
                var nextStatus = self.transitionsScheme[self.currentStatusId][nextStatusId];
                self.createCustomButtonsNode(nextStatusId, nextStatus);
            }
        }

        self.customButtonsNodes.sort(function(a, b){return b.sort - a.sort});
    };

    self.createCustomButtonsNode = function (statusId, transitionInfo) {
        var btnHoverModeClass = '';
        switch (transitionInfo.btnHoverMode) {
            case '0':
                btnHoverModeClass = ' task-sed-button-hover-mode-highlight';
                break;
            case '1':
                btnHoverModeClass = ' task-sed-button-hover-mode-shade';
                break;
        }
        var node = BX.create('span', {
            attrs: {
                // 'data-bx-id': 'task-view-b-button',
                // 'data-bx-id': 'task-view',
                'data-action': 'CHANGE_UF',
                'data-status': statusId,
                className: 'task-view-button change-uf webform-small-button webform-small-button-accept show-button' + btnHoverModeClass,
                style: 'display: inline-block !important; background: #' + transitionInfo.btnColor
            },
            children: [
                BX.create('span', {
                    attrs: {
                        className: 'webform-small-button-text',
                        style: 'color: #' + transitionInfo.btnTextColor
                    },
                    events: {
                        click: function (e) {
                            self.togglePanelActivity(false);
                            self.changeTaskStatus(
                                statusId,
                                transitionInfo.needComment,
                                transitionInfo.nativeStatusId
                            );
                        }
                    },
                    text: transitionInfo.customStatusTitle
                })
            ]
        });
        self.customButtonsNodes.push({sort: transitionInfo.btnSort, node: node});
    };

    self.togglePanelActivity = function (activity) {
        if (!!self.buttonsPanel) {
            if (!!activity) {
                self.buttonsPanel.classList.remove('inactive');
            }
            else {
                self.buttonsPanel.classList.add('inactive');
            }
        }
    };

    self.getQuery = function () {
        if (!self.queryInstance) {
            self.queryInstance = new BX.Tasks.Util.Query({
                autoExec: true,
                url: self.ajaxUrl
            });
        }
        return self.queryInstance;
    };

    self.changeTaskStatus = function (newUfStatusId, needComment, nativeStatusId) {
        BX.ajax({
            url: self.ajaxUrl,
            method: 'post',
            dataType: 'json',
            async: true,
            processData: true,
            emulateOnload: true,
            start: true,
            data: {
                'action': 'SetTaskUfStatus',
                'params': {
                    'taskId': self.taskId,
                    'taskTypeId': self.taskTypeId,
                    'userId': self.userId,
                    'newUfStatusId': newUfStatusId,
                    'needComment': needComment,
                    'nativeStatusId': nativeStatusId
                },
                'sessId': self.sessId
            },
            cache: false,
            onsuccess: BX.delegate(function (result) {
                if (!!result.errorInfo) {
                    self.processAjaxError(result.errorInfo);
                }
                else {
                    window.location.reload();
                }
            }, self),
            onfailure: BX.delegate(function (type, e) {
            }, self)
        });
    };

    self.processAjaxError = function (errorInfo) {
        if (!errorInfo.type) {
            return;
        }
        if (errorInfo.type == 'comment') {
            self.showErrorPopup(errorInfo.description);
        }
    };

    self.showErrorPopup = function (errorMsg) {
        var errorPopup = self.getErrorPopup();
        errorPopup.setButtons([
            new BX.PopupWindowButton({
                text: BX.message("JS_CORE_WINDOW_CLOSE"),
                className: "",
                events: {
                    click: function () {
                        errorPopup.close();
                        self.togglePanelActivity(true);
                    }
                }
            })
        ]);

        errorPopup.setContent("<div style='width: 350px;padding: 10px; font-size: 12px; color: red;'>" + BX.util.htmlspecialchars(errorMsg) + "</div>");
        errorPopup.show();
    };

    self.getErrorPopup = function () {
        if (!self.errorPopup) {
            self.errorPopup = BX.Tasks.Runtime.errorPopup;
            if (!self.errorPopup) {
                self.errorPopup = new BX.PopupWindow("task-error-popup", null, {lightShadow: true});
            }
        }
        return self.errorPopup;
    }
};