SedComponentAdd = function () {
    var self = this;

    self.tabList = [];
    self.formList = [];
    self.requiredInputList = [];
    self.popupList = [];

    self.selector = null;

    self.init = function () {
        self.tabList = Array.from(document.getElementsByClassName('sed-add-tab'));
        self.selector = document.getElementById('sed-process-selector');
        self.formList = self.reIndexArrayByAttribute(Array.from(document.getElementsByClassName('sed-add-form')), 'data-process-id');
        self.requiredInputList = self.reIndexArrayByAttribute(Array.from(document.querySelectorAll('input.sed-add-input-required')), 'data-process-id', true);
        self.popupList = Array.from(document.getElementsByClassName('user-role-popup'));

        self.bindEvents();
    };

    self.reIndexArrayByAttribute = function (arr, attributeName, isArrayType) {
        var result = [];
        arr.forEach(function (element) {
            if (!!isArrayType) {
                if (!result[element.getAttribute(attributeName)]) {
                    result[element.getAttribute(attributeName)] = [];
                }
                result[element.getAttribute(attributeName)].push(element);
            }
            else {
                result[element.getAttribute(attributeName)] = element;
            }
        });

        return result;
    };

    self.bindEvents = function () {
        self.initSelectorEvents();
        self.initInputEvents();
        self.initFormSubmit();
    };

    self.initFormSubmit = function () {
        self.formList.forEach(function (form) {
            form.addEventListener('submit', function (event) {
                let processId = event.srcElement.getAttribute('data-process-id');
                if (!processId) {
                    return;
                }

                let correctData = true;
                if (self.requiredInputList[processId]) {
                    self.requiredInputList[processId].forEach(function (element) {
                        if (!element.value) {
                            correctData = false;
                            element.classList.add('error');
                        }
                        else {
                            element.classList.remove('error');
                        }
                    });
                }

                let userFields = Array.from(form.querySelectorAll('div.user-field-value.sed-add-input.sed-add-input-required'));
                if (userFields) {
                    userFields.forEach(function (userField) {
                        let bHasValidInput = false;
                        let userFieldInputs = null;
                        let findValidInputValue = function (userFieldInputs, validator = function (element) {
                            return !!element.value;
                        }) {
                            for (let userFieldInput of userFieldInputs) {
                                if (validator(userFieldInput)) {
                                    return true;
                                }
                            }
                            return false;
                        };
                        switch (userField.dataset.userTypeId) {
                            case 'string':
                                userFieldInputs = userField.querySelectorAll('textarea[name^="' + userField.dataset.fieldName + '"]');
                                if (userFieldInputs) {
                                    bHasValidInput = findValidInputValue(userFieldInputs);
                                    if (bHasValidInput) {
                                        break;
                                    }
                                }
                            case 'integer':
                            case 'double':
                            case 'date':
                            case 'datetime':
                            case 'employee':
                            case 'crm':
                                userFieldInputs = userField.querySelectorAll('input[name^="' + userField.dataset.fieldName + '"][type=text]');
                                bHasValidInput = findValidInputValue(userFieldInputs);
                                break;
                            case 'address':
                            case 'money':
                            case 'disk_file':
                                userFieldInputs = userField.querySelectorAll('input[name^="' + userField.dataset.fieldName + '"][type=hidden]');
                                bHasValidInput = findValidInputValue(userFieldInputs);
                                break;
                            case 'iblock_section':
                            case 'iblock_element':
                            case 'hlblock':
                            case 'crm_status':
                                userFieldInputs = userField.querySelectorAll('select[name^="' + userField.dataset.fieldName + '"]');
                                bHasValidInput = findValidInputValue(userFieldInputs);
                                break;
                            case 'file':
                                userFieldInputs = userField.querySelectorAll('input[name^="' + userField.dataset.fieldName + '"][type=file]');
                                bHasValidInput = findValidInputValue(userFieldInputs);
                                break;
                            case 'video':
                                userFieldInputs = userField.querySelectorAll('input[name^="' + userField.dataset.fieldName + '"][name$="[FILE]"][type=file]');
                                bHasValidInput = findValidInputValue(userFieldInputs);
                                break;
                            case 'resourcebooking':
                                userFieldInputs = userField.querySelectorAll('input[name^="' + userField.dataset.fieldName + '"][type=hidden]');
                                bHasValidInput = findValidInputValue(userFieldInputs, function (element) {
                                    return element.value && element.value !== 'empty';
                                });
                                break;
                            default:
                                bHasValidInput = true;
                                break;
                        }

                        if (bHasValidInput) {
                            userField.classList.remove('error');
                        } else {
                            correctData = false;
                            userField.classList.add('error');
                        }
                    });
                }

                if (!correctData) {
                    event.preventDefault();
                }
            });
        });
    };

    self.initSelectorEvents = function () {
        if (!!self.selector && self.tabList.length > 0) {
            self.selector.addEventListener('change', function () {
                self.updateTabList(this.value);
            });
        }
    };

    self.initInputEvents = function () {
        document.addEventListener('click', function (event) {
            var target = event.target;
            var userRoleContainer = target.closest('.user-role-container');

            if (!userRoleContainer) {
                self.hideAllPopup();
            }
            else if (target.classList.contains('user-role-input') && !userRoleContainer.classList.contains('open')) {
                self.hideAllPopup();
                userRoleContainer.classList.add('open');
            }
        });
    };

    self.hideAllPopup = function () {
        self.popupList.forEach(function (element) {
            element.parentElement.classList.remove('open');
        });
    };

    self.updateTabList = function (processId) {
        self.tabList.forEach(function (element) {
            element.style.display = (element.getAttribute('data-process-id') == processId) ? 'block' : 'none';
        });
    };
};

SedComponentAdd.onPopupValueChanged = function (data) {
    var containers = Array.from(document.getElementsByClassName('user-role-container value open'));
    if (containers.length < 1) {
        return;
    }

    var activeContainer = containers[0];
    var userInfo = data.pop();

    var participantId = activeContainer.getAttribute('data-role-id');
    if (!!participantId && !!userInfo) {
        Array.from(activeContainer.children).forEach(function (child) {
            if (child.classList.contains('user-role-input')) {
                child.value = userInfo.name;
            }
            else if (child.classList.contains('user-role-input-hidden')) {
                child.value = userInfo.id;
            }
        });

        activeContainer.classList.remove('open');
    }
};

document.addEventListener("DOMContentLoaded", function (event) {
    var SedComponentAddInstance = new SedComponentAdd();
    SedComponentAddInstance.init();
});