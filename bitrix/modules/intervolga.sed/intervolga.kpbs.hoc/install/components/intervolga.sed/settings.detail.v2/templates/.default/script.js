;document.addEventListener("DOMContentLoaded", function() {
    var form = document.getElementById('cts-settings__detail__form');
    var saveBtn = document.getElementById('cts-settings__detail__save-btn');

    if(!!form && !!saveBtn) {
        form.addEventListener('submit', function () {
            saveBtn.disabled = true;
        }, false);
    }
});