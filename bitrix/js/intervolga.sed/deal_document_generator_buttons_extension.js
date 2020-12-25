(function () {
    BX.ready(function () {
        if (!BX.DocumentGenerator || !BX.DocumentGenerator.Button) {
            return;
        }
        BX.DocumentGenerator.Button.prototype.fillLinksFromResponseOriginal = BX.DocumentGenerator.Button.prototype.fillLinksFromResponse;

        BX.DocumentGenerator.Button.prototype.fillLinksFromResponse = function (response) {
            this.fillLinksFromResponseOriginal(response);
            if (this.provider !== BX.message('DOCUMENT_GENERATOR_DEAL_DATA_PROVIDER_CLASS')) {
                return;
            }

            this.links.templates.push({
                delimiter: true
            });
            this.links.templates.push({
                text: BX.message('SED_CREATE_CONTRACT_PROCESS_BUTTON_TITLE'),
                onclick: 'window.open("/sed/add/?' + BX.message('SED_CREATE_CONTRACT_PROCESS_BUTTON_DEAL_UF_CODE') + '=' + this.value + '");',
            })
        }
    });
})()