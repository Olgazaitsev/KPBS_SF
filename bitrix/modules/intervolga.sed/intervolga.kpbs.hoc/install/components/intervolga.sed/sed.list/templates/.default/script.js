;BX.ready(function () {
    var contractTableElements = BX.findChildrenByClassName(document, 'change-parent-node', true);
    if(contractTableElements.length > 0) {
        var map = [];
        map['role'] = ['default', 'new', 'accepted', 'progress', 'completed', 'completed', 'completed', 'declined'];

        contractTableElements.forEach(function (el) {
            var type = el.getAttribute('data-type');
            var option = el.getAttribute('data-option');
            el.parentElement.classList.add('grid-item-changed');
            if(!!map[type] && !!map[type][option]) {
                el.parentElement.classList.add(type, map[type][option]);
            }
        });
    }
});