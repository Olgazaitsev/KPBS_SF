;function SedDetailFileUpload(params) {

    if(typeof params != 'object') {
        return;
    }
    
    this.fileSelectBtn = document.getElementById(params.fileSelectBtn);
    this.fileUploadBtn = document.getElementById(params.fileUploadBtn);
    this.fileInput = document.getElementById(params.fileInput);
    this.comment = document.getElementById(params.comment);
    this.form = document.getElementById(params.form);
    this.btnClass = 'webform-small-button-accept';
    this.fileExt = params.fileExt;

    if(this.checkRequired()) {
        this.bindHandlers();
    }
}

SedDetailFileUpload.prototype.checkRequired = function () {
    return (!!this.form && !!this.fileSelectBtn && !!this.fileUploadBtn && !!this.fileInput && !!this.comment && !!this.fileExt);
};

SedDetailFileUpload.prototype.checkFileName = function (fileName) {
    if(!fileName) {
        return false;
    }

    var regExp = /^.*\.([^\.]+)$/;
    var res = regExp.exec(fileName);

    return (!!res && (res[1] == this.fileExt));
};

SedDetailFileUpload.prototype.bindHandlers = function () {
    var self = this;

    this.fileSelectBtn.addEventListener('click', function () {
        self.fileInput.click();
    }, false);

    this.fileInput.addEventListener('change', function (e) {
        if(self.checkFileName(this.files[0].name)) {
            self.fileSelectBtn.classList.remove(self.btnClass);
            self.fileSelectBtn.innerText = this.files[0].name;
            self.fileUploadBtn.classList.add(self.btnClass);
        }
        else {
            alert(BX.message('SED_DET_JS.ANOTHER_EXT') + self.fileExt);
            e.preventDefault();
            return false;
        }
    }, false);

    this.fileUploadBtn.addEventListener('click', function (e) {
        if(!self.fileInput.files[0]) {
            alert(BX.message('SED_DET_JS.SELECT_FILE'));
            e.preventDefault();
            return false;
        }
        
        if(!self.comment.value) {
            alert(BX.message('SED_DET_JS.ENTER_COMMENT'));
            e.preventDefault();
            return false;
        }

    }, false);

    this.form.addEventListener('submit', function (e) {
        self.fileUploadBtn.disabled = true;
        self.fileSelectBtn.disabled = true;
    }, false);
};