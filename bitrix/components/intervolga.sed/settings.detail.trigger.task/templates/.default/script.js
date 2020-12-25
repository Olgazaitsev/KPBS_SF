;function SedSettingsDetailTaskTrigger(params) {
    this.form = document.getElementById('sed-settings__task-trigger-detail__form');
    this.saveBtn = document.getElementById('sed-settings__task-trigger-detail__save-btn');
    this.actionsContainer = document.getElementById('sed-settings__actions-container');
    this.actionBlocks = {};
    this.notSelectedOptions = {};

    this.addActionBlockBtn = this.createNode({
        type: 'input',
        classes: ['sed-settings__action-block-line-item', 'adm-btn-save'],
        attributes: {type: 'button', value: BX.message('SET_DET_TRG_TPL.JS_BTN_ADD')}
    });

    this.availableActions = {
        container: this.createNode({classes: ['sed-settings__action-block']}),
        optionsContainer: this.createNode({type: 'select', classes: ['sed-settings__action-block-line-item']}),
        options: {}
    };

    if(!!this.form && !!this.saveBtn && !!this.actionsContainer && this.checkParams(params)) {
        this.actions = params.actions;
        // this.paramOptions = params.paramOptions;
        this.selectedActions = params.selectedActions;

        this.paramInputPrefix = params.paramInputPrefix;
        this.actionInputPrefix = params.actionInputPrefix;

        this.init();
    }
};


SedSettingsDetailTaskTrigger.prototype.checkParams = function (params) {
    return (
        typeof params == 'object' &&
        typeof params.actions == 'object' &&
        // typeof params.paramOptions == 'object' &&
        typeof params.selectedActions == 'object' &&
        !!params.paramInputPrefix &&
        !!params.actionInputPrefix
    );
};


SedSettingsDetailTaskTrigger.prototype.createNode = function(params) {
    if(typeof params != 'object') {
        return document.createElement('div');
    }

    if(!params.type) {
        params.type = 'div';
    }

    var node = document.createElement(params.type);
    if(!!params.classes && !!params.classes.length) {
        for(var i = 0; i < params.classes.length; i++) {
            node.classList.add(params.classes[i]);
        }
    }

    if(!!params.attributes) {
        for(var attrName in params.attributes) {
            if(params.attributes.hasOwnProperty(attrName)) {
                node.setAttribute(attrName, params.attributes[attrName]);
            }
        }
    }

    if(!!params.innerText) {
        node.innerText = params.innerText;
    }

    return node;
};


SedSettingsDetailTaskTrigger.prototype.init = function () {
    for (var actionId in this.selectedActions) {
        if(this.selectedActions.hasOwnProperty(actionId)) {

            var container = this.getActionBlock(actionId, this.selectedActions[actionId]);
            this.actionBlocks[actionId] = container;
            this.actionsContainer.appendChild(container);
        }
    }

    this.initCreateBlock();
    this.formSubmitHandler();
};


SedSettingsDetailTaskTrigger.prototype.formSubmitHandler = function () {
    var self = this;
    this.form.addEventListener('submit', function (e) {
        if(!!(Object.keys(self.notSelectedOptions).length)) {
            console.log(self.notSelectedOptions);
            (!!e.preventDefault) ? e.preventDefault() : (e.returnValue = false);
        }
        else {
            self.saveBtn.disabled = true;
        }

    }, false);
};


SedSettingsDetailTaskTrigger.prototype.initCreateBlock = function () {
    for (var actionId in this.actions) {
        if(this.actions.hasOwnProperty(actionId) && !this.actionBlocks[actionId]) {
            this.initCreateBlockOption(this.actions[actionId]['LABEL'], actionId);
        }
    }

    this.availableActions.container.appendChild(this.createNode({type: 'span', innerText: BX.message('SET_DET_TRG_TPL.JS_LABEL_ADD'), classes: ['sed-settings__action-block-line-item']}));
    this.availableActions.container.appendChild(this.availableActions.optionsContainer);
    this.availableActions.container.appendChild(this.addActionBlockBtn);
    this.actionsContainer.appendChild(this.availableActions.container);

    var self = this;
    this.addActionBlockBtn.addEventListener('click', function () {
        self.createBtnClickHandler();
    }, false);
};


SedSettingsDetailTaskTrigger.prototype.initCreateBlockOption = function (label, actionId) {
    var optionNode = this.createNode({
        type: 'option',
        innerText: label,
        attributes: {value: actionId}
    });

    this.availableActions.optionsContainer.appendChild(optionNode);
    this.availableActions.options[actionId] = optionNode;
};


SedSettingsDetailTaskTrigger.prototype.createBtnClickHandler = function () {
    this.addActionBlockBtn.disabled = true;

    for (var actionId in this.availableActions.options) {
        if(this.availableActions.options.hasOwnProperty(actionId) && !!this.availableActions.options[actionId].selected) {
            this.availableActions.options[actionId].remove();
            delete this.availableActions.options[actionId];

            if(!(Object.keys(this.availableActions.options).length)) {
                this.availableActions.container.style.display = 'none';
            }

            var container = this.getActionBlock(actionId);
            this.actionBlocks[actionId] = container;
            this.actionsContainer.insertBefore(container, this.availableActions.container);

            break;
        }
    }

    this.addActionBlockBtn.disabled = false;
};


SedSettingsDetailTaskTrigger.prototype.removeBtnClickHandler = function (actionId, paramIds) {
    if(!!this.actionBlocks[actionId]) {
        this.actionBlocks[actionId].remove();
        delete this.actionBlocks[actionId];

        for (var i = 0; i < paramIds.length; i++) {
            delete this.notSelectedOptions[paramIds[i]];
        }

        this.initCreateBlockOption(this.actions[actionId]['LABEL'], actionId);
        this.availableActions.container.style.display = 'block';
    }
};


SedSettingsDetailTaskTrigger.prototype.getActionBlock = function (actionId, selectedOptions) {

    if(!this.actions[actionId]) {
        return null;
    }

    if(typeof selectedOptions != 'object') {
        selectedOptions = {};
    }

    var container = this.createNode({classes: ['sed-settings__action-block']});
    var titleBlock = this.createNode({classes: ['sed-settings__action-block-line', 'sed-settings__action-block-title']});
    titleBlock.appendChild(this.createNode({
        type: 'span',
        innerText: this.actions[actionId]['LABEL']
    }));

    var removeBtn = this.createNode({
        type: 'input',
        attributes: {type: 'button', value: BX.message('SET_DET_TRG_TPL.JS_BTN_DEL')},
        classes: ['sed-settings__action-block-remove-btn']
    });

    var self = this;
    removeBtn.addEventListener('click', function () {
        self.removeBtnClickHandler(actionId, Object.keys(self.actions[actionId]['PARAMS']));
    }, false);

    titleBlock.appendChild(removeBtn);
    container.appendChild(titleBlock);

    for (var paramId in this.actions[actionId]['PARAMS']) {
        if(this.actions[actionId]['PARAMS'].hasOwnProperty(paramId)) {
            var paramBlock = this.getParamBlock(paramId, actionId, selectedOptions[paramId]);
            container.appendChild(paramBlock);
        }
    }

    return container;
};


SedSettingsDetailTaskTrigger.prototype.getParamBlock = function (paramId, actionId, defaultValue) {

    var param = this.actions[actionId]['PARAMS'][paramId];

    var paramBlock = this.createNode({classes: ['sed-settings__action-block-line']});

    var paramBlockTitleClasses = ['sed-settings__action-block-line-item'];
    if(!!param['IS_REQUIRED']) {
        paramBlockTitleClasses.push('sed-settings__required');
    }

    paramBlock.appendChild(this.createNode({
        type: 'span',
        innerText: param['LABEL'],
        classes: paramBlockTitleClasses
    }));

    paramBlock.appendChild(this.createNode({
        type: 'input',
        attributes: {name: this.actionInputPrefix + actionId, type: 'hidden'}
    }));

    var paramBlockInnerContainer = this.createNode({type: 'span', classes: ['sed-settings__action-block-line-item']});

    /**
     * Блок с параметрами может быть нескольких видов
     *  1. "select" - param['TYPE'] == {'ROLE', 'T_TYPE', 'PROCESS_STATUS', 'T_GROUP', ...}, обязательно установлено param['HAS_OPTIONS'] == true
     *  2. "input" - вариант по умолчанию
     *  3. "textarea" - param['TYPE'] == 'TEXTAREA'
     *  4. ...
     */

    if(!!param['HAS_OPTIONS']) {

        var paramBlockSelect = null;
        var paramBlockOptions = null;

        if(!!param['IS_MULTIPLE']) {
            paramBlockOptions = this.getParamOptionNodes(param['OPTIONS'], true, defaultValue);
            paramBlockSelect = this.createNode({
                type: 'select',
                attributes: {name: this.paramInputPrefix + paramId + '[]', multiple: '', size: Object.keys(paramBlockOptions).length}
            });
        }
        else {
            paramBlockOptions = this.getParamOptionNodes(param['OPTIONS'], false, defaultValue);
            paramBlockSelect = this.createNode({
                type: 'select',
                attributes: {name: this.paramInputPrefix + paramId}
            });
        }

        var defaultOption = null;
        if(!!param['IS_REQUIRED']) {
            defaultOption = this.createNode({
                type: 'option',
                innerText: '',
                attributes: {disabled: '', hidden: ''}
            });

            if(!defaultValue) {
                defaultOption.selected = true;
            }

            this.initRequiredParamNodeHandler(paramId, paramBlockSelect, !defaultValue, ['change']);
        }
        else {
            defaultOption = this.createNode({
                type: 'option',
                attributes: {value: ''},
                innerText: BX.message('SET_DET_TRG_TPL.JS_OPTION_EMPTY')
            });

            if(!defaultValue) {
                defaultOption.selected = true;
            }
        }
        paramBlockSelect.appendChild(defaultOption);

        for (var optionId in paramBlockOptions) {
            if(paramBlockOptions.hasOwnProperty(optionId)) {
                paramBlockSelect.appendChild(paramBlockOptions[optionId]);
            }
        }

        paramBlockInnerContainer.appendChild(paramBlockSelect);
    }
    else {
        if(param['TYPE'] == 'TEXTAREA') {
            var textAreaNode = this.createNode({
                type: 'textarea',
                attributes: {name: this.paramInputPrefix + paramId},
                innerText: (!!defaultValue) ? defaultValue : ''
            });

            if(!!param['IS_REQUIRED']) {
                this.initRequiredParamNodeHandler(paramId, textAreaNode, !defaultValue, ['input', 'change']);
            }

            paramBlockInnerContainer.appendChild(textAreaNode);
        }
        else {
            var inputNode = this.createNode({
                type: 'input',
                attributes: {
                    name: this.paramInputPrefix + paramId,
                    value: (!!defaultValue) ? defaultValue : ''
                }
            });

            if(!!param['IS_REQUIRED']) {
                this.initRequiredParamNodeHandler(paramId, inputNode, !defaultValue, ['input', 'change']);
            }

            paramBlockInnerContainer.appendChild(inputNode);
        }
    }

    paramBlock.appendChild(paramBlockInnerContainer);
    return paramBlock;
};


SedSettingsDetailTaskTrigger.prototype.initRequiredParamNodeHandler = function (paramId, requiredNode, emptyByDefault, events) {
    var self = this;

    if(typeof events === 'undefined' || !events.length) {
        return;
    }

    if(emptyByDefault) {
        this.notSelectedOptions[paramId] = true;
        requiredNode.classList.add('sed-settings__empty');
    }

    events.forEach(function (eventName) {
        requiredNode.addEventListener(eventName, function () {
            if(!!this.value) {
                requiredNode.classList.remove('sed-settings__empty');
                delete self.notSelectedOptions[paramId];
            }
            else {
                requiredNode.classList.add('sed-settings__empty');
                self.notSelectedOptions[paramId] = true;
            }
        }, false);
    });
};


SedSettingsDetailTaskTrigger.prototype.getParamOptionNodes = function (options, isMultiple, selected) {
    var optionNodes = {};

    if(typeof options == 'object') {
        for (var optionId in options) {
            if(options.hasOwnProperty(optionId)) {

                optionNodes[optionId] = this.createNode({
                    type: 'option',
                    attributes: {value: optionId},
                    innerText: options[optionId]
                });

                if(!!selected) {
                    optionNodes[optionId].selected = (isMultiple) ? (this.includes(selected, optionId)) : (optionId == selected);
                }
            }
        }
    }

    return optionNodes;
};


SedSettingsDetailTaskTrigger.prototype.includes = function (arr, item) {
    if(!!arr && !!arr.length) {
        for (var i = 0; i < arr.length; i++) {
            if(arr[i] == item) {
                return true;
            }
        }
    }

    return false;
};