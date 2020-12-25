//alert('!!!!!');

BX.SocNetLogDestination.getItemDepartmentHtml = function (name, relation, categoryId, categoryOpened) {
    if (!name) {
        name = 'lm';
    }

    categoryId = categoryId ? categoryId : false;
    categoryOpened = categoryOpened ? true : false;

    var bFirstRelation = false;
    var
        activeClass = null,
        i = null;

    if (
        typeof relation == 'undefined'
        || !relation
    ) // root
    {
        relation = BX.SocNetLogDestination.obItems[name].departmentRelation;
        bFirstRelation = true;
    }

    var html = '';
    for (i in relation) {
        if (
            relation.hasOwnProperty(i)
            && relation[i].type == 'category'
        ) {
            var category = BX.SocNetLogDestination.obItems[name].department[relation[i].id];
            activeClass = (
                BX.SocNetLogDestination.obItemsSelected[name][relation[i].id]
                    ? BX.SocNetLogDestination.obTemplateClassSelected['department']
                    : ''
            );
            bFirstRelation = (bFirstRelation && category.id != 'EX');

            html += '<div class="bx-finder-company-department' + (bFirstRelation ? ' bx-finder-company-department-opened' : '') + '">\
            <a href="#' + category.id + '" class="bx-finder-company-department-inner" onclick="return BX.SocNetLogDestination.OpenCompanyDepartment(\'' + name + '\', this.parentNode, \'' + category.entityId + '\')" hidefocus="true">\
                <div class="bx-finder-company-department-arrow"></div>\
                <div class="bx-finder-company-department-text">' + category.name + '</div>\
                <span class="bx-plus" onclick="BX.SocNetLogDestination.m_getDepartmentRelationData(\'' + name + '\', \'' + category.entityId + '\')">+</span>\
            </a>\
        </div>';

            html += '<div class="bx-finder-company-department-children' + (bFirstRelation ? ' bx-finder-company-department-children-opened' : '') + '">';
            if (
                !BX.SocNetLogDestination.obDepartmentSelectDisable[name]
                && !bFirstRelation
                && category.id != 'EX'
            ) {
                html += '<a class="bx-finder-company-department-check ' + activeClass + ' bx-finder-element" hidefocus="true" onclick="return BX.SocNetLogDestination.selectItem(\'' + name + '\', this, \'department\', \'' + relation[i].id + '\', \'department\')" rel="' + relation[i].id + '" href="#' + relation[i].id + '">';
                html += '<span class="bx-finder-company-department-check-inner">\
                    <div class="bx-finder-company-department-check-arrow"></div>\
                    <div class="bx-finder-company-department-check-text" rel="' + category.name + ': ' + BX.message("LM_POPUP_CHECK_STRUCTURE") + '">' + BX.message("LM_POPUP_CHECK_STRUCTURE") + '</div>\
                </span>\
            </a>';
            }
            html += BX.SocNetLogDestination.getItemDepartmentHtml(name, relation[i].items, category.entityId, bFirstRelation);
            html += '</div>';
        }
    }

    if (categoryId) {
        html += '<div class="bx-finder-company-department-employees" id="bx-lm-category-relation-' + categoryId + '">';
        userCount = 0;
        for (i in relation) {
            if (
                relation.hasOwnProperty(i)
                && relation[i].type == 'user'
            ) {
                var user = BX.SocNetLogDestination.obItems[name].users[relation[i].id];
                if (user == null) {
                    continue;
                }

                activeClass = (
                    BX.SocNetLogDestination.obItemsSelected[name][relation[i].id]
                        ? BX.SocNetLogDestination.obTemplateClassSelected['department-user']
                        : ''
                );
                html += '<a href="#' + user.id + '" class="bx-finder-company-department-employee ' + activeClass + ' bx-finder-element" rel="' + user.id + '" onclick="return BX.SocNetLogDestination.selectItem(\'' + name + '\', this, \'department-user\', \'' + user.id + '\', \'users\')" hidefocus="true">\
                <div class="bx-finder-company-department-employee-info">\
                    <div class="bx-finder-company-department-employee-name">' + user.name + '</div>\
                    <div class="bx-finder-company-department-employee-position">' + user.desc + '</div>\
                </div>\
                <div style="' + (user.avatar ? 'background:url(\'' + user.avatar + '\') no-repeat center center; background-size: cover;' : '') + '" class="bx-finder-company-department-employee-avatar"></div>\
            </a>';
                userCount++;
            }
        }
        if (userCount <= 0) {
            if (!BX.SocNetLogDestination.obDepartmentLoad[name][categoryId]) {
                html += '<div class="bx-finder-company-department-employees-loading">' + BX.message('LM_PLEASE_WAIT') + '</div>';
            }

            if (categoryOpened) {
                BX.SocNetLogDestination.getDepartmentRelation(name, categoryId);
            }
        }
        html += '</div>';
    }

    return html;
};


// !!!!
BX.SocNetLogDestination.m_getDepartmentRelationData = function (name, departmentId){
    console.info('BX.SocNetLogDestination.m_getDepartmentRelationData name: ' + name);
    console.info('BX.SocNetLogDestination.m_getDepartmentRelationData departmentId: ' + departmentId);
    /*
        if (BX.SocNetLogDestination.obDepartmentLoad[name][departmentId]) {
            return false;
        }
    */

    if(!name)
        name = 'lm';
    console.info('BX.SocNetLogDestination.m_getDepartmentRelationData start ajax');
    BX.ajax({
        url: BX.SocNetLogDestination.obPathToAjax[name],
        method: 'POST',
        dataType: 'json',
        data: {
            LD_DEPARTMENT_RELATION: 'Y',
            DEPARTMENT_ID: departmentId,
            sessid: BX.bitrix_sessid(),
            nt: BX.SocNetLogDestination.obUserNameTemplate[name]
        },
        onsuccess: function (data) {
            console.info('BX.SocNetLogDestination.m_getDepartmentRelationData ajax success');
            console.info(JSON.stringify(data));
            for (var user in data.USERS) {
                try {
                    console.info('BX.SocNetLogDestination.m_getDepartmentRelationData select item name: ' + name + ' id: ' + data.USERS[user].id);
                    BX.SocNetLogDestination.selectItem(name, this, 'department-user', data.USERS[user].id, 'users');
                }
                catch (e) {
                    console.exception(e);
                }
            }
        },
        onfailure: function (data) {
            console.error(data);
        }
    });
};
// !!!!!
