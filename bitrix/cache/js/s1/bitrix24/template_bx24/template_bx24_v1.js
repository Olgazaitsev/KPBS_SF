
; /* Start:"a:4:{s:4:"full";s:97:"/bitrix/components/bitrix/main.interface.buttons/templates/.default/script.min.js?160748815244683";s:6:"source";s:77:"/bitrix/components/bitrix/main.interface.buttons/templates/.default/script.js";s:3:"min";s:81:"/bitrix/components/bitrix/main.interface.buttons/templates/.default/script.min.js";s:3:"map";s:81:"/bitrix/components/bitrix/main.interface.buttons/templates/.default/script.map.js";}"*/
BX.namespace("BX.Main");if(typeof BX.Main.interfaceButtons==="undefined"){BX.Main.interfaceButtons=function(t,e){this.classItem="main-buttons-item";this.classItemSublink="main-buttons-item-sublink";this.classItemText="main-buttons-item-text";this.classItemCounter="main-buttons-item-counter";this.classItemIcon="main-buttons-item-icon";this.classItemMore="main-buttons-item-more";this.classOnDrag="main-buttons-drag";this.classDropzone="main-buttons-submenu-dropzone";this.classSeporator="main-buttons-submenu-separator";this.classHiddenLabel="main-buttons-hidden-label";this.classSubmenuItem="main-buttons-submenu-item";this.classItemDisabled="main-buttons-disabled";this.classItemOver="over";this.classItemActive="main-buttons-item-active";this.classSubmenu="main-buttons-submenu";this.classSecret="secret";this.classItemLocked="locked";this.submenuIdPrefix="main_buttons_popup_";this.childMenuIdPrefix="main_buttons_popup_child_";this.submenuWindowIdPrefix="menu-popup-";this.classSettingMenuItem="main-buttons-submenu-setting";this.classEditState="main-buttons-edit";this.classEditItemButton="main-buttons-item-edit-button";this.classDragItemButton="main-buttons-item-drag-button";this.classSettingsApplyButton="main-buttons-submenu-settings-apply";this.classSettingsResetButton="main-buttons-submenu-settings-reset";this.classSetHome="main-buttons-set-home";this.classSetHide="main-buttons-set-hide";this.classManage="main-buttons-manage";this.classContainer="main-buttons";this.classSubmenuNoHiddenItem="main-buttons-submenu-item-no-hidden";this.classDefaultSubmenuItem="menu-popup-item";this.classInner="main-buttons-inner-container";this.listContainer=null;this.pinContainer=null;this.dragItem=null;this.overItem=null;this.moreButton=null;this.messages=null;this.licenseParams=null;this.isSubmenuShown=false;this.isSubmenuShownOnDragStart=false;this.isSettingsEnabled=true;this.containerId=e.containerId;this.tmp={};this.init(t,e);return{getItemById:BX.delegate(this.getItemById,this),getAllItems:BX.delegate(this.getAllItems,this),getHiddenItems:BX.delegate(this.getHiddenItems,this),getVisibleItems:BX.delegate(this.getVisibleItems,this),getDisabledItems:BX.delegate(this.getDisabledItems,this),getMoreButton:BX.delegate(this.getMoreButton,this),adjustMoreButtonPosition:BX.delegate(this.adjustMoreButtonPosition,this),showSubmenu:BX.delegate(this.showSubmenu,this),closeSubmenu:BX.delegate(this.closeSubmenu,this),refreshSubmenu:BX.delegate(this.refreshSubmenu,this),getCurrentSettings:BX.delegate(this.getCurrentSettings,this),saveSettings:BX.delegate(this.saveSettings,this),setCounterValueByItemId:BX.delegate(this.setCounterValueByItemId,this),getCounterValueByItemId:BX.delegate(this.getCounterValueByItemId,this),updateCounter:BX.delegate(this.updateCounter,this),getActive:BX.delegate(this.getActive,this),isEditEnabled:BX.delegate(this.isEditEnabled,this),isActiveInMoreMenu:BX.delegate(this.isActiveInMoreMenu,this),isSettingsEnabled:this.isSettingsEnabled,classes:{item:this.classItem,itemText:this.classItemText,itemCounter:this.classItemCounter,itemIcon:this.classItemIcon,itemDisabled:this.classItemDisabled,itemOver:this.classItemOver,itemActive:this.classItemActive,itemLocked:this.classItemLocked,submenu:this.classSubmenu,submenuItem:this.classSubmenuItem,containerOnDrag:this.classOnDrag,classSettingMenuItem:this.classSettingMenuItem},itemsContainer:this.listContainer,itemsContainerId:this.listContainer.id}};BX.Main.interfaceButtons.prototype={init:function(t,e){this.listContainer=BX(this.getId());if(!BX.type.isPlainObject(e)){throw"BX.MainButtons: params is not Object"}if(!("containerId"in e)||!BX.type.isNotEmptyString(e.containerId)){throw"BX.MainButtons: containerId not set in params"}if(!BX.type.isDomNode(this.listContainer)){throw"BX.MainButtons: #"+e.containerId+" is not dom node"}if("classes"in e&&BX.type.isPlainObject(e.classes)){this.setCustomClasses(e.classes)}if("messages"in e&&BX.type.isPlainObject(e.messages)){this.setMessages(e.messages)}if("licenseWindow"in e&&BX.type.isPlainObject(e.licenseWindow)){this.setLicenseWindowParams(e.licenseWindow)}if("disableSettings"in e&&e.disableSettings==="true"){this.isSettingsEnabled=false;this.visibleControlMoreButton()}if("ajaxSettings"in e){this.ajaxSettings=e.ajaxSettings}this.moreButton=this.getMoreButton();this.listChildItems={};if(this.isSettingsEnabled){this.dragAndDropInit()}this.adjustMoreButtonPosition();this.bindOnClickOnMoreButton();this.bindOnScrollWindow();this.setContainerHeight();BX.bind(this.getContainer(),"click",BX.delegate(this._onDocumentClick,this));BX.addCustomEvent("onPullEvent-main",BX.delegate(this._onPush,this));this.updateMoreButtonCounter();if(this.isActiveInMoreMenu()){this.activateItem(this.moreButton)}var i=this.getVisibleItems();var s=BX.type.isArray(i)&&i.length>0?i[0]:null;var n=BX.Buttons.Utils.getByTag(s,"a");if(!BX.type.isDomNode(n)){return}var a=n.getAttribute("href");if(a.charAt(0)==="?"){a=n.pathname+n.search}if(!this.lastHomeLink){this.lastHomeLink=a}this.bindOnResizeFrame();var o=Array.from(this.container.querySelectorAll(".main-buttons-item-child-button"));o.forEach(function(t){var e=t.closest(".main-buttons-item-child");if(e.dataset.isOpened){this.realChildButton=e;var i=e.closest(".main-buttons-item-child-button-cloned");if(i){this.clonedChildButton=i}}BX.Event.bind(t,"click",this.onShowChildButtonClick.bind(this))},this)},calculateChildListWidth:function(){if(this.realChildButton){var t=this.realChildButton.querySelectorAll(".main-buttons-item-child-list-inner .main-buttons-item");var e=10;return Array.from(t).reduce(function(t,e){var i=BX.Text.toNumber(BX.Dom.style(e,"width"));var s=BX.Text.toNumber(BX.Dom.style(e,"margin-left"));var n=BX.Text.toNumber(BX.Dom.style(e,"margin-right"));return t+i+s+n},e)}return 0},onShowChildButtonClick:function(t){t.preventDefault();if(!this.realChildButton){this.realChildButton=t.currentTarget.closest(".main-buttons-item-child")}var e=this.realChildButton.querySelector(".main-buttons-item-child-list");var i=BX.Dom.attr(this.realChildButton,"data-child-items");var s=BX.Dom.attr(this.realChildButton,"data-is-opened");var n={};if(s){BX.Dom.attr(this.realChildButton,"data-is-opened",null);i.forEach(function(t){var e=this.getContainer().querySelector('[data-id="'+t+'"]');BX.Dom.style(e,"display",null);if(t.hasOwnProperty("PARENT_ITEM_ID")){n[t["PARENT_ITEM_ID"]]="N"}},this);if(this.clonedChildButton){BX.Dom.remove(this.clonedChildButton)}BX.Dom.style(e,{overflow:null,"max-width":null});n=JSON.stringify(n);BX.userOptions.save("ui",this.listContainer.id,"expanded_lists",n)}else{BX.Dom.attr(this.realChildButton,"data-is-opened",true);BX.Dom.style(e,"max-width",this.calculateChildListWidth()+"px");this.cloneChildButton(this.realChildButton);i.forEach(function(t){var e=this.getContainer().querySelector('[data-id="'+t+'"]');BX.Dom.insertBefore(e,this.realChildButton);BX.Dom.style(e,"display","inline-block");if(t.hasOwnProperty("PARENT_ITEM_ID")){n[t["PARENT_ITEM_ID"]]="Y"}},this);setTimeout(function(){BX.Dom.style(e,"overflow","unset")}.bind(this),200);n=JSON.stringify(n);BX.userOptions.save("ui",this.listContainer.id,"expanded_lists",n)}setTimeout(function(){this._onResizeHandler()}.bind(this),200)},cloneChildButton:function(t){this.clonedChildButton=BX.Runtime.clone(t);var e=this.clonedChildButton.querySelector(".main-buttons-item-child-list");if(e){BX.Dom.remove(e)}BX.Dom.addClass(this.clonedChildButton,"main-buttons-item-child-button-cloned");BX.Dom.style(this.clonedChildButton,"transition","none");BX.Dom.insertBefore(this.clonedChildButton,t);BX.Event.bind(this.clonedChildButton,"click",this.onShowChildButtonClick.bind(this));setTimeout(function(){BX.Dom.style(this.clonedChildButton,"transition",null)}.bind(this))},_onDocumentClick:function(t){var e=this.getItem(t);var i,s,n,a,o,r;if(this.isDragButton(t.target)){t.preventDefault();t.stopPropagation()}if(BX.type.isDomNode(e)){if(this.isSettings(e)){this.enableEdit();BX.hide(this.getSettingsButton());BX.show(this.getSettingsApplyButton());return false}if(this.isApplySettingsButton(e)){t.preventDefault();t.stopPropagation();this.disableEdit();BX.show(this.getSettingsButton());BX.hide(this.getSettingsApplyButton());return false}if(this.isResetSettingsButton(e)){this.resetSettings();return false}if(this.isLocked(e)){t.preventDefault();this.showLicenseWindow();return false}if(this.isEditButton(t.target)){var u,h;t.preventDefault();t.stopPropagation();if(this.isSubmenuItem(e)){e=this.getItemAlias(e)}try{u=JSON.parse(BX.data(e,"item"))}catch(t){}h=this.getItemEditMenu();if(h&&h.popupWindow.isShown()&&this.lastEditNode===e){h.popupWindow.close()}else{this.showItemEditMenu(u,t.target)}this.lastEditNode=e;return false}if(this.isSetHide(e)){o=this.getVisibleItems();r=BX.type.isArray(o)?o.length:null;a=this.editItemData.ID.replace(this.listContainer.id+"_","");s=this.getItemById(a);n=this.getItemAlias(s);s=this.isVisibleItem(s)?s:n;if(this.isDisabled(n)){this.enableItem(n)}else if(!this.isDisabled(n)&&r>2){this.disableItem(n)}if(r===2){BX.onCustomEvent(window,"BX.Main.InterfaceButtons:onHideLastVisibleItem",[s,this])}this.refreshSubmenu();this.saveSettings();this.adjustMoreButtonPosition();if(this.isEditEnabled()){this.enableEdit();BX.hide(this.getSettingsButton());BX.show(this.getSettingsApplyButton())}this.editMenu.popupWindow.close();return false}if(this.isSetHome(e)){a=this.editItemData.ID.replace(this.listContainer.id+"_","");s=this.getItemById(a);n=this.getItemAlias(s);if(this.isDisabled(n)){this.enableItem(n)}this.listContainer.insertBefore(s,BX.firstChild(this.listContainer));this.adjustMoreButtonPosition();this.refreshSubmenu();this.saveSettings();if(this.isEditEnabled()){this.enableEdit();BX.hide(this.getSettingsButton());BX.show(this.getSettingsApplyButton())}this.editMenu.popupWindow.close();return false}if(!this.isSublink(t.target)){i=this.dataValue(e,"onclick");if(BX.type.isNotEmptyString(i)){t.preventDefault();this.execScript(i)}}}if(this.isEditEnabled()){this.getSubmenu().popupWindow.setAutoHide(false)}},isActiveInMoreMenu:function(){var t=this.getHiddenItems();var e=this.getDisabledItems();var i=t.concat(e);return i.some(function(t){var e;try{e=JSON.parse(BX.data(t,"item"))}catch(t){}return BX.type.isPlainObject(e)&&("IS_ACTIVE"in e&&e.IS_ACTIVE===true||e.IS_ACTIVE==="true"||e.IS_ACTIVE==="Y")},this)},_onPush:function(t,e){if(t==="user_counter"&&e&&BX.message("SITE_ID")in e){var i=e[BX.message("SITE_ID")];for(var s in i){if(i.hasOwnProperty(s)){this.updateCounter(s,i[s])}}}},bindOnScrollWindow:function(){BX.bind(window,"scroll",BX.delegate(this._onScroll,this))},getActive:function(){var t=this.getAllItems();var e,i;var s=null;if(BX.type.isArray(t)){t.forEach(function(t){try{e=JSON.parse(BX.data(t,"item"))}catch(t){e=null}if(BX.type.isPlainObject(e)&&"IS_ACTIVE"in e&&(e.IS_ACTIVE===true||e.IS_ACTIVE==="true"||e.IS_ACTIVE==="Y")){s=e}},this)}if(BX.type.isPlainObject(s)){i=BX(s.ID);if(BX.type.isDomNode(i)){s.NODE=i}else{s.NODE=null}}return s},isSetHome:function(t){return BX.hasClass(t,this.classSetHome)},isSetHide:function(t){return BX.hasClass(t,this.classSetHide)},getSettingsButton:function(){return BX.Buttons.Utils.getByClass(this.getSubmenuContainer(),this.classSettingMenuItem)},getSettingsApplyButton:function(){return BX.Buttons.Utils.getByClass(this.getSubmenuContainer(),this.classSettingsApplyButton)},isApplySettingsButton:function(t){return BX.hasClass(t,this.classSettingsApplyButton)},enableEdit:function(){var t=this.getSubmenu();if(t&&"popupWindow"in t){t.popupWindow.setAutoHide(false)}BX.addClass(this.listContainer,this.classEditState);BX.addClass(this.getSubmenuContainer(),this.classEditState);this.isEditEnabledState=true},disableEdit:function(){var t=this.getSubmenu();if(t&&"popupWindow"in t){t.popupWindow.setAutoHide(true)}BX.removeClass(this.listContainer,this.classEditState);BX.removeClass(this.getSubmenuContainer(),this.classEditState);this.isEditEnabledState=false},isEditEnabled:function(){return this.isEditEnabledState},showItemEditMenu:function(t,e){if(BX.type.isPlainObject(t)&&"ID"in t){var i=[this.listContainer.id,"_edit_item"].join("");var s=BX.PopupMenu.getMenuById(i);if(s){BX.PopupMenu.destroy(i)}s=this.createItemEditMenu(t,i,e);s.popupWindow.show()}},getContainer:function(){if(!BX.type.isDomNode(this.container)){this.container=BX(this.containerId).parentNode}return this.container},getItemEditMenu:function(){return BX.PopupMenu.getMenuById([this.listContainer.id,"_edit_item"].join(""))},createItemEditMenu:function(t,e,i){var s;var n=[{text:this.message("MIB_SET_HOME"),className:"main-buttons-set-home menu-popup-no-icon"}];var a=t.ID.replace(this.listContainer.id+"_","");var o=this.getItemById(a);if(this.isDisabled(o)){n.push({text:this.message("MIB_SET_SHOW"),className:"main-buttons-set-hide menu-popup-no-icon"})}else{n.push({text:this.message("MIB_SET_HIDE"),className:"main-buttons-set-hide menu-popup-no-icon"})}var r=BX.pos(i);var u={menuId:e,anchor:i,menuItems:n,settings:{autoHide:true,offsetTop:0,offsetLeft:r.width/2,zIndex:20,angle:{position:"top",offset:r.width/2}}};s=BX.PopupMenu.create(u.menuId,u.anchor,u.menuItems,u.settings);if(this.isVisibleItem(o)){t.NODE=o}else{t.NODE=this.getItemAlias(o)}this.editItemData=t;if("menuItems"in s&&BX.type.isArray(s.menuItems)){s.menuItems.forEach(function(t){BX.bind(t.layout.item,"click",BX.delegate(this._onDocumentClick,this))},this)}BX.onCustomEvent(window,"BX.Main.InterfaceButtons:onBeforeCreateEditMenu",[s,t,this]);this.editMenu=s;return s},setHome:function(){var t=this.getVisibleItems();var e=BX.type.isArray(t)&&t.length>0?t[0]:null;var i=BX.Buttons.Utils.getByTag(e,"a");if(!BX.type.isDomNode(i)){return}var s=i.getAttribute("href");if(s.charAt(0)==="?"){s=i.pathname+i.search}if(!this.lastHomeLink){this.lastHomeLink=s}if(this.lastHomeLink!==s){BX.userOptions.save("ui",this.listContainer.id,"firstPageLink",s);BX.onCustomEvent("BX.Main.InterfaceButtons:onFirstItemChange",[s,e])}this.lastHomeLink=s},isEditButton:function(t){return BX.hasClass(t,this.classEditItemButton)},isDragButton:function(t){return BX.hasClass(t,this.classDragItemButton)},isResetSettingsButton:function(t){return BX.hasClass(t,this.classSettingsResetButton)},getContainerHeight:function(){var t=this.getAllItems().map(function(t){var e=getComputedStyle(t);return BX.height(t)+parseInt(e.marginTop)+parseInt(e.marginBottom)});return Math.max.apply(Math,t)},setContainerHeight:function(){var t=this.getContainerHeight();var e=8;BX.height(this.listContainer,t-e)},setLicenseWindowParams:function(t){this.licenseParams=t||{}},message:function(t){var e;try{e=this.messages[t]}catch(t){e=""}return e},setCustomClasses:function(t){if(!BX.type.isPlainObject(t)){return}this.classItem=t.item||this.classItem;this.classItemSublink=t.itemSublink||this.classItemSublink;this.classItemText=t.itemText||this.classItemText;this.classItemCounter=t.itemCounter||this.classItemCounter;this.classItemIcon=t.itemIcon||this.classItemIcon;this.classItemMore=t.itemMore||this.classItemMore;this.classItemOver=t.itemOver||this.classItemOver;this.classItemActive=t.itemActive||this.classItemActive;this.classItemDisabled=t.itemDisabled||this.classItemDisabled;this.classOnDrag=t.onDrag||this.classOnDrag;this.classDropzone=t.dropzone||this.classDropzone;this.classSeporator=t.separator||this.classSeporator;this.classSubmenuItem=t.submenuItem||this.classSubmenuItem;this.classSubmenu=t.submenu||this.classSubmenu;this.classSecret=t.secret||this.classSecret;this.classItemLocked=t.itemLocked||this.classItemLocked},setMessages:function(t){if(!BX.type.isPlainObject(t)){return}this.messages=t},makeFullItemId:function(t){if(!BX.type.isNotEmptyString(t)){return}return[this.listContainer.id,t.replace("-","_")].join("_")},getItemById:function(t){var e=null;var i;if(BX.type.isNotEmptyString(t)){i=this.makeFullItemId(t);e=BX.Buttons.Utils.getBySelector(this.listContainer,"#"+i)}return e},getItemCounterObject:function(t){var e=null;if(BX.type.isDomNode(t)){e=BX.Buttons.Utils.getByClass(t,this.classItemCounter)}return e},setCounterValue:function(t,e){var i=this.getItemCounterObject(t);if(BX.type.isDomNode(i)){i.innerText=e>99?"99+":e>0?e:"";t.dataset.counter=e}this.updateMoreButtonCounter()},updateCounter:function(t,e){if(t.indexOf("crm")===0&&e<0){return}var i,s,n;var a=null;var o=this.getAllItems();if(BX.type.isArray(o)){o.forEach(function(e){try{s=JSON.parse(BX.data(e,"item"))}catch(t){s={}}if(BX.type.isPlainObject(s)&&"COUNTER_ID"in s&&s.COUNTER_ID===t){a=e}},this)}i=this.getItemCounterObject(a);if(BX.type.isDomNode(i)){a=this.getItem(i);i.innerText=e>99?"99+":e>0?e:"";a.dataset.counter=e}n=this.getItemAlias(a);if(BX.type.isDomNode(n)){i=this.getItemCounterObject(n);if(BX.type.isDomNode(i)){i.innerText=e>99?"99+":e>0?e:"";n.dataset.counter=e}}this.updateMoreButtonCounter()},setCounterValueByItemId:function(t,e){var i=e!==null?parseFloat(e):null;var s,n;if(!BX.type.isNotEmptyString(t)){throw"Bad first arg. Need string as item id"}if(i!==null&&!BX.type.isNumber(i)){throw"Bad two arg. Need number counter value - Integer, Float or string with number"}s=this.getItemById(t);if(!BX.type.isDomNode(s)){console.info("Not found node with id #"+t);return}n=this.getItemAlias(s);this.setCounterValue(s,i);this.setCounterValue(n,i)},getCounterValueByItemId:function(t){var e,i;var s=NaN;if(!BX.type.isNotEmptyString(t)){throw"Bad first arg. Need string item id"}else{e=this.getItemById(t);s=this.dataValue(e,"counter");s=parseFloat(s);if(!BX.type.isNumber(s)){i=this.getItemCounterObject(e);s=parseFloat(i.innerText)}}return s},setMoreButtonCounter:function(t){var e=this.getItemCounterObject(this.moreButton);var i=t>99?"99+":t>0?t:"";i=parseInt(i);i=BX.type.isNumber(i)?i:"";e.innerText=i},bindOnClickOnMoreButton:function(){BX.bind(this.moreButton,"click",BX.delegate(this._onClickMoreButton,this))},bindOnResizeFrame:function(){window.frames["maininterfacebuttonstmpframe-"+this.getId()].onresize=BX.throttle(this._onResizeHandler,20,this)},getId:function(){return BX.Buttons.Utils.getByClass(this.getContainer(),this.classInner).id},getAllItems:function(){return BX.Buttons.Utils.getByClass(this.listContainer,this.classItem,true)},getVisibleItems:function(){var t=this.getAllItems();var e=this;var i=[];if(t&&t.length){i=t.filter(function(t){return e.isVisibleItem(t)&&!e.isDisabled(t)})}return i},getHiddenItems:function(){var t=this.getAllItems();var e=[];var i=this;if(t&&t.length){e=t.filter(function(t){return!i.isVisibleItem(t)&&!i.isDisabled(t)})}return e},getDisabledItems:function(){return this.getAllItems().filter(function(t){return this.isDisabled(t)},this)},getMoreButton:function(){var t=null;this.getAllItems().forEach(function(e){!t&&BX.hasClass(e,this.classItemMore)&&(t=e)},this);return t},getLastVisibleItem:function(){var t=this.getVisibleItems();var e=null;if(BX.type.isArray(t)&&t.length){e=t[t.length-1]}if(!BX.type.isDomNode(e)){e=null}return e},adjustMoreButtonPosition:function(){var t=this.getLastVisibleItem();var e=this.isMoreButton(t);if(!e&&t.parentNode===this.listContainer){this.listContainer.insertBefore(this.moreButton,t)}this.updateMoreButtonCounter()},getSubmenuId:function(t){var e="";if(BX.type.isDomNode(this.listContainer)&&BX.type.isNotEmptyString(this.listContainer.id)){e=this.submenuIdPrefix+this.listContainer.id}if(t){e=this.submenuWindowIdPrefix+e}return e},getChildMenuId:function(){var t="";if(BX.type.isDomNode(this.listContainer)&&BX.type.isNotEmptyString(this.listContainer.id)){t=this.childMenuIdPrefix+this.listContainer.id}return t},getSubmenuItemText:function(t){var e,i,s;if(!BX.type.isDomNode(t)){return null}e=this.findChildrenByClassName(t,this.classItemText);i=this.findChildrenByClassName(t,this.classItemCounter);if(BX.type.isDomNode(i)&&BX.type.isDomNode(e)){i.dataset.counter=this.dataValue(t,"counter");s=e.outerHTML+i.outerHTML}else{e=this.dataValue(t,"text");i=this.dataValue(t,"counter");s=e}return s},getChildMenuItemText:function(t){var e,i,s;if(!BX.type.isDomNode(t)){return null}e=this.findChildrenByClassName(t,this.classItemText);i=this.findChildrenByClassName(t,this.classItemCounter);if(BX.type.isDomNode(i)&&BX.type.isDomNode(e)){i.dataset.counter=this.dataValue(t,"counter");s=e.outerHTML+i.outerHTML}else{e=this.dataValue(t,"text");s=e}return s},getLockedClass:function(t){var e="";if(BX.type.isDomNode(t)&&this.isLocked(t)){e=this.classItemLocked}return e},getSubmenuItems:function(){var t=this.getAllItems();var e=this.getHiddenItems();var i=this.getDisabledItems();var s=[];var n,a;if(t.length){t.forEach(function(t){if(e.indexOf(t)===-1&&i.indexOf(t)===-1){s.push({text:this.getSubmenuItemText(t),href:this.dataValue(t,"url"),onclick:this.dataValue(t,"onclick"),title:t.getAttribute("title"),className:[this.classSubmenuItem,this.getIconClass(t),this.classSecret,this.getAliasLink(t),this.getLockedClass(t)].join(" ")})}},this)}if(e.length){e.forEach(function(t){try{n=JSON.parse(this.dataValue(t,"item"))}catch(t){n=null}a=[this.classSubmenuItem,this.getIconClass(t),this.getAliasLink(t),this.getLockedClass(t)];if(BX.type.isPlainObject(n)&&("IS_ACTIVE"in n&&n.IS_ACTIVE===true||n.IS_ACTIVE==="true"||n.IS_ACTIVE==="Y")){a.push(this.classItemActive)}s.push({text:this.getSubmenuItemText(t),href:this.dataValue(t,"url"),onclick:this.dataValue(t,"onclick"),title:t.getAttribute("title"),className:a.join(" "),items:this.getChildMenuItems(t)})},this)}if(this.isSettingsEnabled){s.push({text:"<span>"+this.message("MIB_HIDDEN")+"</span>",className:[this.classSeporator,this.classSubmenuItem,this.classHiddenLabel].join(" ")});if(!i.length){s.push({text:"<span>"+this.message("MIB_NO_HIDDEN")+"</span>",className:[this.classSubmenuItem,this.classSubmenuNoHiddenItem].join(" ")})}if(i.length){i.forEach(function(t){try{n=JSON.parse(this.dataValue(t,"item"))}catch(t){n=null}a=[this.classSubmenuItem,this.classItemDisabled,this.getIconClass(t),this.getAliasLink(t),this.getLockedClass(t)];if(BX.type.isPlainObject(n)&&("IS_ACTIVE"in n&&n.IS_ACTIVE===true||n.IS_ACTIVE==="true"||n.IS_ACTIVE==="Y")){a.push(this.classItemActive)}s.push({text:this.getSubmenuItemText(t),href:this.dataValue(t,"url"),onclick:this.dataValue(t,"onclick"),title:t.getAttribute("title"),className:a.join(" "),items:this.getChildMenuItems(t)})},this)}s.push({text:"<span>"+this.message("MIB_MANAGE")+"</span>",className:[this.classSeporator,this.classSubmenuItem,this.classHiddenLabel,this.classManage].join(" ")});s.push({text:this.message("MIB_SETTING_MENU_ITEM"),className:[this.classSettingMenuItem,this.classSubmenuItem].join(" ")});s.push({text:this.message("MIB_APPLY_SETTING_MENU_ITEM"),className:[this.classSettingsApplyButton,this.classSubmenuItem].join(" ")});s.push({text:this.message("MIB_RESET_SETTINGS"),className:[this.classSettingsResetButton,this.classSubmenuItem].join(" ")})}return s},getChildMenuItems:function(t){var e;try{e=JSON.parse(this.dataValue(t,"item"))}catch(t){e=null}if(!BX.type.isPlainObject(e)){return[]}if(!BX.type.isArray(this.listChildItems[t.id])){var i={};this.setListAllItems(i,e);var s=this.getListItems(i,"");if(s.length){this.listChildItems[t.id]=BX.type.isArray(s[0].items)?s[0].items:[]}}return this.listChildItems[t.id]},setListAllItems:function(t,e){var i=[];if(BX.type.isPlainObject(e)){i.push(e)}else{i=e}i.forEach(function(e){t[e["ID"].replace(this.containerId+"_","")]=e;if(BX.type.isArray(e["ITEMS"])){this.setListAllItems(t,e["ITEMS"])}},this)},getListItems:function(t,e){var i=[];for(var s in t){if(!t.hasOwnProperty(s)){continue}var n=t[s];if(n["PARENT_ID"]===e){var a={},o=[],r=n.hasOwnProperty("AJAX_OPTIONS");if(r){a=this._getEvents(n["AJAX_OPTIONS"]);o=[{id:"loading",text:this.message("MIB_MAIN_BUTTONS_LOADING")}]}var u,h={text:n["TEXT"],href:n["URL"],onclick:n["ON_CLICK"],title:n["TITLE"],events:a,items:o};if(r){h.cacheable=true}else{u=this.getListItems(t,s);if(u.length){h.items=u}}i.push(h);delete t[s]}}return i},_setAjaxMode:function(t){for(var e in t){if(!t.hasOwnProperty(e)){continue}if(t[e].hasOwnProperty("ajaxOptions")){t[e].cacheable=true;t[e].events=this._getEvents(t[e]["ajaxOptions"]);t[e].items=[{id:"loading",text:this.message("MIB_MAIN_BUTTONS_LOADING")}]}}},_getEvents:function(t){var e=this;return{onSubMenuShow:function(){if(this.subMenuLoaded){return}var i=this.getSubMenu();i.removeMenuItem("loading");var s=i.getMenuItem("loading");e.getSubItems(t).then(function(t){e._setAjaxMode(t);this.subMenuLoaded=true;this.addSubMenu(t);this.showSubMenu()}.bind(this)).catch(function(t){if(s){s.getLayout().text.innerText=t}})}}},getSubItems:function(t){return new Promise(function(e,i){if(this.progress){i(this.message("MIB_MAIN_BUTTONS_LOADING"));return}if(t.mode==="component"){this.progress=true;BX.ajax.runComponentAction(t.component,t.action,{mode:t.componentMode,signedParameters:t.signedParameters?t.signedParameters:{},data:t.data}).then(function(t){this.progress=false;e(t.data)}.bind(this))}else{this.progress=true;BX.ajax.runAction(t.action,{data:t.data}).then(function(t){this.progress=false;e(t.data)}.bind(this))}})},getSubmenuArgs:function(){var t=this.getSubmenuId();var e=this.moreButton;var i=BX.pos(e);var s=this.getSubmenuItems();var n={autoHide:true,offsetLeft:i.width/2-80,angle:{position:"top",offset:100},zIndex:0,events:{onPopupClose:BX.delegate(this._onSubmenuClose,this)}};return[t,e,s,n]},getChildMenuArgs:function(t){var e=this.getChildMenuId();var i=this.getChildMenuItems(t);if(!i||BX.type.isArray(i)&&!i.length){return[]}var s={autoHide:true,angle:true,offsetLeft:t.getBoundingClientRect().width/2};return[e,t,i,s]},visibleControlMoreButton:function(){var t=this.getHiddenItems();if(!t.length||t.length===1&&this.isMoreButton(t[0])){this.getMoreButton().style.display="none"}else{this.getMoreButton().style.display=""}},createSubmenu:function(){var t=BX.PopupMenu.create.apply(BX.PopupMenu,this.getSubmenuArgs());if(this.isSettingsEnabled){this.dragAndDropInitInSubmenu()}t.menuItems.forEach(function(t){BX.bind(t.layout.item,"click",BX.delegate(this._onDocumentClick,this))},this);return t},createChildMenu:function(t){return BX.PopupMenu.create.apply(BX.PopupMenu,this.getChildMenuArgs(t))},showSubmenu:function(){var t=this.getSubmenu();if(t!==null){t.popupWindow.show()}else{this.destroySubmenu();t=this.createSubmenu();t.popupWindow.show()}this.setSubmenuShown(true);this.activateItem(this.moreButton);if(this.isEditEnabled()){t.popupWindow.setAutoHide(false)}},showChildMenu:function(t){var e=BX.PopupMenu.getMenuById(this.getChildMenuId()),i=null;if(e&&e.bindElement){if(e.bindElement.id!==t.id){this.destroyChildMenu(t);i=this.createChildMenu(t);i.popupWindow.show()}else{e.popupWindow.show()}}else{this.destroyChildMenu(t);i=this.createChildMenu(t);i.popupWindow.show()}},closeSubmenu:function(){var t=this.getSubmenu();if(t===null){return}t.popupWindow.close();if(!this.isActiveInMoreMenu()){this.deactivateItem(this.moreButton)}this.setSubmenuShown(false)},closeChildMenu:function(t){var e=this.getChildMenu(t);if(e===null){return}e.popupWindow.close()},getSubmenu:function(){return BX.PopupMenu.getMenuById(this.getSubmenuId())},getChildMenu:function(){return BX.PopupMenu.getMenuById(this.getChildMenuId())},destroySubmenu:function(){BX.PopupMenu.destroy(this.getSubmenuId())},destroyChildMenu:function(){BX.PopupMenu.destroy(this.getChildMenuId())},refreshSubmenu:function(){var t=this.getSubmenu();var e;if(t===null){return}e=this.getSubmenuArgs();if(BX.type.isArray(e)){this.destroySubmenu();this.createSubmenu();this.showSubmenu()}},setSubmenuShown:function(t){this.isSubmenuShown=false;if(BX.type.isBoolean(t)){this.isSubmenuShown=t}},activateItem:function(t){if(!BX.type.isDomNode(t)){return}if(!BX.hasClass(t,this.classItemActive)){BX.addClass(t,this.classItemActive)}},deactivateItem:function(t){if(!BX.type.isDomNode(t)){return}if(BX.hasClass(t,this.classItemActive)){BX.removeClass(t,this.classItemActive)}},getCurrentSettings:function(){var t={};this.getAllItems().forEach(function(e,i){t[e.id]={sort:i,isDisabled:this.isDisabled(e)}},this);return t},saveSettings:function(){var t=this.getCurrentSettings();var e="settings";var i;if(!BX.type.isPlainObject(t)){return}if(BX.type.isDomNode(this.listContainer)){if("id"in this.listContainer){i=this.listContainer.id;t=JSON.stringify(t);BX.userOptions.save("ui",i,e,t);this.setHome()}}},resetSettings:function(){var t=null;var e=BX.PopupWindowManager.create(this.listContainer.id+"_reset_popup",null,{content:this.message("MIB_RESET_ALERT"),autoHide:false,overlay:true,closeByEsc:true,closeIcon:true,draggable:{restrict:true},titleBar:this.message("MIB_RESET_SETTINGS"),buttons:[t=new BX.PopupWindowButton({text:this.message("MIB_RESET_BUTTON"),className:"popup-window-button-create",events:{click:function(){if(BX.hasClass(t.buttonNode,"popup-window-button-wait")){return}BX.addClass(t.buttonNode,"popup-window-button-wait");this.handleResetSettings(function(i){if(i){BX.removeClass(t.buttonNode,"popup-window-button-wait");e.setContent(i)}else{var s="settings";BX.userOptions.save("ui",this.listContainer.id,s,JSON.stringify({}));BX.userOptions.save("ui",this.listContainer.id,"firstPageLink","");window.location.reload()}}.bind(this))}.bind(this)}}),new BX.PopupWindowButtonLink({text:this.message("MIB_CANCEL_BUTTON"),className:"popup-window-button-link-cancel",events:{click:function(){this.popupWindow.close()}}})]});e.show()},handleResetSettings:function(t){var e=[];BX.onCustomEvent("BX.Main.InterfaceButtons:onBeforeResetMenu",[e,this]);var i=new BX.Promise;var s=i;for(var n=0;n<e.length;n++){i=i.then(e[n])}i.then(function(e){t(null,e)},function(e){t(e,null)});s.fulfill()},moveButtonAlias:function(t){var e,i;if(!t||!this.dragItem){return}e=this.getItemAlias(this.dragItem);i=this.getItemAlias(t);if(this.isListItem(e)){if(!i){this.listContainer.appendChild(e)}else{this.listContainer.insertBefore(e,i)}}},moveButton:function(t){var e;if(!BX.type.isDomNode(t)||!BX.type.isDomNode(this.dragItem)){return}if(this.isListItem(t)){if(this.isDisabled(this.dragItem)){this.dragItem.dataset.disabled="false"}if(BX.type.isDomNode(t)){this.listContainer.insertBefore(this.dragItem,t)}else{this.listContainer.appendChild(this.dragItem)}}if(this.isSubmenuItem(t)){if(this.isDisabled(this.dragItem)&&!this.isDisabled(t)){this.enableItem(this.dragItem)}e=this.getSubmenuContainer();e.insertBefore(this.dragItem,t)}},getSubmenuContainer:function(){var t=this.getSubmenu();var e=null;if(t!==null){e=t.itemsContainer}return e},findNextSiblingByClass:function(t,e){var i=t;for(;!!t;t=t.nextElementSibling){if(e){if(BX.hasClass(t,e)&&t!==i){return t}}else{return null}}},findParentByClassName:function(t,e){for(;t&&t!==document;t=t.parentNode){if(e){if(BX.hasClass(t,e)){return t}}else{return null}}},findChildrenByClassName:function(t,e){var i=null;if(BX.type.isDomNode(t)&&BX.type.isNotEmptyString(e)){i=BX.Buttons.Utils.getByClass(t,e)}return i},dragAndDropInit:function(){this.getAllItems().forEach(function(t,e){if(!this.isSeparator(t)&&!this.isSettings(t)&&!this.isApplySettingsButton(t)&&!this.isResetSettingsButton(t)){t.setAttribute("draggable","true");t.setAttribute("tabindex","-1");t.dataset.link="item"+e;BX.bind(t,"dragstart",BX.delegate(this._onDragStart,this));BX.bind(t,"dragend",BX.delegate(this._onDragEnd,this));BX.bind(t,"dragenter",BX.delegate(this._onDragEnter,this));BX.bind(t,"dragover",BX.delegate(this._onDragOver,this));BX.bind(t,"dragleave",BX.delegate(this._onDragLeave,this));BX.bind(t,"drop",BX.delegate(this._onDrop,this))}BX.bind(t,"mouseover",BX.delegate(this._onMouse,this));BX.bind(t,"mouseout",BX.delegate(this._onMouse,this))},this)},dragAndDropInitInSubmenu:function(){var t=this.getSubmenu();var e=t.menuItems;e.forEach(function(t){if(!this.isSeparator(t.layout.item)&&!this.isSettings(t.layout.item)&&!this.isApplySettingsButton(t.layout.item)&&!this.isResetSettingsButton(t.layout.item)){t.layout.item.draggable=true;t.layout.item.dataset.sortable=true;BX.bind(t.layout.item,"dragstart",BX.delegate(this._onDragStart,this));BX.bind(t.layout.item,"dragenter",BX.delegate(this._onDragEnter,this));BX.bind(t.layout.item,"dragover",BX.delegate(this._onDragOver,this));BX.bind(t.layout.item,"dragleave",BX.delegate(this._onDragLeave,this));BX.bind(t.layout.item,"dragend",BX.delegate(this._onDragEnd,this));BX.bind(t.layout.item,"drop",BX.delegate(this._onDrop,this))}if(BX.hasClass(t.layout.item,this.classHiddenLabel)&&!BX.hasClass(t.layout.item,this.classManage)){BX.bind(t.layout.item,"dragover",BX.delegate(this._onDragOver,this))}},this)},getItem:function(t){if(!BX.type.isDomNode(t)){if(!t||!BX.type.isDomNode(t.target)){return null}}else{t={target:t}}var e=this.findParentByClassName(t.target,this.classItem);if(!BX.type.isDomNode(e)){e=this.findParentByClassName(t.target,this.classDefaultSubmenuItem)}return e},setOpacity:function(t){if(!BX.type.isDomNode(t)){return}BX.style(t,"opacity",".1")},unsetOpacity:function(t){if(!BX.type.isDomNode(t)){return}BX.style(t,"opacity","1")},setDragStyles:function(){BX.addClass(this.listContainer,this.classOnDrag);BX.addClass(BX(this.getSubmenuId(true)),this.classOnDrag);this.setOpacity(this.dragItem)},unsetDragStyles:function(){var t=this.getSubmenu();this.getAllItems().forEach(function(t){this.unsetOpacity(t);BX.removeClass(t,"over")},this);if(t&&"menuItems"in t&&BX.type.isArray(t.menuItems)&&t.menuItems.length){t.menuItems.forEach(function(t){this.unsetOpacity(t);BX.removeClass(t.layout.item,"over")},this)}BX.removeClass(this.listContainer,this.classOnDrag);BX.removeClass(BX(this.getSubmenuId(true)),this.classOnDrag)},getIconClass:function(t){var e="";if(BX.type.isDomNode(t)&&"dataset"in t&&"class"in t.dataset&&BX.type.isNotEmptyString(t.dataset.class)){e=t.dataset.class}return e},disableItem:function(t){var e=this.getItemAlias(t);if(t&&"dataset"in t){t.dataset.disabled="true";if(e){e.dataset.disabled="true"}}},enableItem:function(t){var e;if(!BX.type.isDomNode(t)){return}if(this.isSubmenuItem(t)){BX.removeClass(t,this.classItemDisabled);e=this.getItemAlias(t);if(BX.type.isDomNode(e)){e.dataset.disabled="false"}}},getAliasLink:function(t){return this.dataValue(t,"link")||""},getItemAlias:function(t){var e=null;if(!BX.type.isDomNode(t)){return e}var i=this.getAllItems();var s=this.isSubmenuItem(t);var n=this.isListItem(t);if(!s&&!n){return e}if(s){i.forEach(function(i){BX.hasClass(t,this.getAliasLink(i))&&(e=i)},this)}if(n){e=BX.Buttons.Utils.getByClass(document,this.getAliasLink(t))}return e},hideItem:function(t){!!t&&BX.addClass(t,this.classSecret)},showItem:function(t){!!t&&BX.removeClass(t,this.classSecret)},fakeDragItem:function(){var t=null;if(!BX.type.isDomNode(this.dragItem)||!BX.type.isDomNode(this.overItem)){return}if(this.isDragToSubmenu()){t=this.getItemAlias(this.dragItem);if(t!==this.dragItem){this.listContainer.appendChild(this.dragItem);this.dragItem=t;this.showItem(this.dragItem);this.adjustMoreButtonPosition();this.updateSubmenuItems();this.tmp.moved=false;this.tmp.movetToSubmenu=true;this.setOpacity(this.dragItem)}}if(this.isDragToList()&&!this.tmp.movetToSubmenu){t=this.getItemAlias(this.dragItem);if(t!==this.dragItem){this.hideItem(this.dragItem);this.dragItem=t;this.adjustMoreButtonPosition();this.updateSubmenuItems();this.setOpacity(this.dragItem)}}this.tmp.movetToSubmenu=false},updateSubmenuItems:function(){var t=this.getHiddenItems();var e=this.getDisabledItems();var i=this;var s=[];var n,a,o;n=this.getSubmenu();if(n===null){return}a=n.menuItems;if(!BX.type.isArray(a)||!a.length){return}s=e.concat(t);a.forEach(function(t){o=[].some.call(s,function(e){return BX.hasClass(t.layout.item,i.dataValue(e,"link"))||i.isDisabled(t.layout.item)||i.isSeparator(t.layout.item)||i.isDropzone(t.layout.item)});if(o||(i.isSettings(t.layout.item)||i.isApplySettingsButton(t.layout.item)||i.isResetSettingsButton(t.layout.item)||i.isNotHiddenItem(t.layout.item)||i.isSeparator(t.layout.item)||t.layout.item===i.dragItem)&&!i.isMoreButton(t.layout.item)){i.showItem(t.layout.item)}else{i.hideItem(t.layout.item)}})},isNotHiddenItem:function(t){return BX.hasClass(t,this.classSubmenuNoHiddenItem)},getNotHidden:function(){return BX.Buttons.Utils.getByClass(this.getSubmenuContainer(),this.classSubmenuNoHiddenItem)},setOverStyles:function(t){if(BX.type.isDomNode(t)&&!BX.hasClass(t,this.classItemOver)){BX.addClass(t,this.classItemOver)}},unsetOverStyles:function(t){if(BX.type.isDomNode(t)&&BX.hasClass(t,this.classItemOver)){BX.removeClass(t,this.classItemOver)}},dataValue:function(t,e){var i="";var s;if(BX.type.isDomNode(t)){s=BX.data(t,e);if(typeof s!=="undefined"){i=s}}return i},execScript:function(script){if(BX.type.isNotEmptyString(script)){eval(script)}},showLicenseWindow:function(){var t;if(!B24.licenseInfoPopup){return}t=B24.licenseInfoPopup;t.init({B24_LICENSE_BUTTON_TEXT:this.message("MIB_LICENSE_BUY_BUTTON"),B24_TRIAL_BUTTON_TEXT:this.message("MIB_LICENSE_TRIAL_BUTTON"),IS_FULL_DEMO_EXISTS:this.licenseParams.isFullDemoExists,HOST_NAME:this.licenseParams.hostname,AJAX_URL:this.licenseParams.ajaxUrl,LICENSE_ALL_PATH:this.licenseParams.licenseAllPath,LICENSE_DEMO_PATH:this.licenseParams.licenseDemoPath,FEATURE_GROUP_NAME:this.licenseParams.featureGroupName,AJAX_ACTIONS_URL:this.licenseParams.ajaxActionsUrl,B24_FEATURE_TRIAL_SUCCESS_TEXT:this.message("MIB_LICENSE_WINDOW_TRIAL_SUCCESS_TEXT")});t.show("main-buttons",this.message("MIB_LICENSE_WINDOW_HEADER_TEXT"),this.message("MIB_LICENSE_WINDOW_TEXT"))},_onDragStart:function(t){var e=this.getVisibleItems();var i=BX.type.isArray(e)?e.length:null;this.dragItem=this.getItem(t);if(!BX.type.isDomNode(this.dragItem)){return}if(i===2&&this.isListItem(this.dragItem)){t.preventDefault();BX.onCustomEvent(window,"BX.Main.InterfaceButtons:onHideLastVisibleItem",[this.dragItem,this]);return}if(this.isMoreButton(this.dragItem)||this.isSeparator(this.dragItem)||this.isNotHiddenItem(this.dragItem)||BX.Dom.attr(this.dragItem,"data-parent-item-id")||BX.Dom.attr(this.dragItem,"data-has-child")){t.preventDefault();return}this.isSubmenuShownOnDragStart=!!this.isSubmenuShown;if(this.isListItem(this.dragItem)){this.showSubmenu()}this.setDragStyles();if(!this.isEditEnabled()){this.enableEdit()}},_onDragEnd:function(t){t.preventDefault();var e=this.getItem(t);var i,s;if(!BX.type.isDomNode(e)){return}this.unsetDragStyles();if(!this.isSubmenuShownOnDragStart){this.refreshSubmenu();if(!this.isEditEnabled()){this.closeSubmenu()}}else{this.refreshSubmenu()}i=BX.findNextSibling(this.dragItem,BX.delegate(function(t){return this.isVisibleItem(t)},this));s=BX.findPreviousSibling(this.dragItem,BX.delegate(function(t){return this.isVisibleItem(t)},this));if(BX.type.isDomNode(s)&&(BX.hasClass(s,this.classHiddenLabel)||this.isDisabled(s)&&this.isSubmenuItem(s))||(BX.type.isDomNode(i)&&BX.hasClass(i,this.classManage)||this.isDisabled(i)&&this.isSubmenuItem(i))){this.disableItem(this.dragItem);this.refreshSubmenu()}if(this.isEditEnabled()){this.enableEdit();BX.show(this.getSettingsApplyButton());BX.hide(this.getSettingsButton())}else{this.disableEdit();BX.hide(this.getSettingsApplyButton());BX.show(this.getSettingsButton())}this.updateMoreButtonCounter();this.saveSettings();this.dragItem=null;this.overItem=null;this.tmp.moved=false},updateMoreButtonCounter:function(){var t,e,i,s;t=this.getHiddenItems();s=this.getDisabledItems();t=t.concat(s);e=0;if(BX.type.isArray(t)){t.forEach(function(t){e+=parseInt(this.dataValue(t,"counter"))||0},this)}if(BX.type.isNumber(e)){this.setMoreButtonCounter(e)}},_onDragEnter:function(t){var e=this.getItem(t);if(BX.type.isDomNode(e)&&this.isNotHiddenItem(e)){this.setOverStyles(e)}},_onDragOver:function(t){t.preventDefault();var e=null;this.overItem=this.getItem(t);if(!BX.type.isDomNode(this.overItem)||!BX.type.isDomNode(this.dragItem)||this.overItem===this.dragItem||this.isNotHiddenItem(this.overItem)||BX.Dom.attr(this.overItem,"data-parent-item-id")||BX.Dom.attr(this.overItem,"data-has-child")){return}this.fakeDragItem();if(this.isNext(t)&&this.isGoodPosition(t)&&!this.isMoreButton(this.overItem)){e=this.findNextSiblingByClass(this.overItem,this.classItem);if(this.isMoreButton(e)&&!this.tmp.moved){e=e.previousElementSibling;this.tmp.moved=true}if(!BX.type.isDomNode(e)){e=this.findNextSiblingByClass(this.overItem,this.classSubmenuItem)}if(BX.type.isDomNode(e)){this.moveButton(e);this.moveButtonAlias(e);this.adjustMoreButtonPosition();this.updateSubmenuItems()}}if(!this.isNext(t)&&this.isGoodPosition(t)&&!this.isMoreButton(this.overItem)||!this.isGoodPosition(t)&&this.isMoreButton(this.overItem)&&this.getVisibleItems().length===1){this.moveButton(this.overItem);this.moveButtonAlias(this.overItem);this.adjustMoreButtonPosition();this.updateSubmenuItems()}},_onDragLeave:function(t){var e=this.getItem(t);if(BX.type.isDomNode(e)){this.unsetOverStyles(t.target)}},_onDrop:function(t){var e=this.getItem(t);if(!BX.type.isDomNode(e)){return}if(this.isNotHiddenItem(e)||this.isDisabled(e)){this.disableItem(this.dragItem);this.adjustMoreButtonPosition()}this.unsetDragStyles();t.preventDefault()},getIndex:function(t,e){return[].indexOf.call(t||[],e)},_onSubmenuClose:function(){this.setSubmenuShown(false);if(this.isEditEnabled()){this.activateItem(this.moreButton)}else{if(!this.isActiveInMoreMenu()){this.deactivateItem(this.moreButton)}}},_onResizeHandler:function(){this.adjustMoreButtonPosition();this.updateSubmenuItems();if(!this.isSettingsEnabled){this.visibleControlMoreButton()}},_onClickMoreButton:function(t){t.preventDefault();this.showSubmenu()},_onMouse:function(t){var e=this.getItem(t);if(t.type==="mouseover"&&!BX.hasClass(e,this.classItemOver)){if(!BX.hasClass(e,this.classItemMore)){this.showChildMenu(e)}BX.addClass(e,this.classItemOver)}if(t.type==="mouseout"&&BX.hasClass(e,this.classItemOver)){BX.removeClass(e,this.classItemOver)}},getSettingsResetButton:function(){return BX.Buttons.Utils.getByClass(this.getSubmenuContainer(),this.classSettingsResetButton)},_onScroll:function(){if(BX.style(this.pinContainer,"position")==="fixed"){this.closeSubmenu()}},isDisabled:function(t){var e=false;if(BX.type.isDomNode(t)){e=this.dataValue(t,"disabled")==="true"||BX.hasClass(t,this.classItemDisabled)}return e},isSettings:function(t){var e=false;if(BX.type.isDomNode(t)){e=BX.hasClass(t,this.classSettingMenuItem)}return e},isLocked:function(t){var e=false;if(BX.type.isDomNode(t)){e=this.dataValue(t,"locked")==="true"||BX.hasClass(t,this.classItemLocked)}return e},isDropzone:function(t){return BX.hasClass(t,this.classDropzone)},isNext:function(t){var e=this.dragItem.getBoundingClientRect();var i=this.overItem.getBoundingClientRect();var s=getComputedStyle(this.dragItem);var n=parseInt(s.marginRight.replace("px",""));var a=null;if(this.isListItem(this.overItem)){a=t.clientX>i.left-n&&t.clientX>e.right}if(this.isSubmenuItem(this.overItem)){a=t.clientY>e.top}return a},isGoodPosition:function(t){var e=this.overItem;var i,s;if(!BX.type.isDomNode(e)){return false}i=e.getBoundingClientRect();if(this.isListItem(e)){s=this.isNext(t)&&t.clientX>=i.left+i.width/2||!this.isNext(t)&&t.clientX<=i.left+i.width/2}if(this.isSubmenuItem(e)){s=this.isNext(t)&&t.clientY>=i.top+i.height/2||!this.isNext(t)&&t.clientY<=i.top+i.height/2}return s},isSubmenuItem:function(t){return BX.hasClass(t,this.classSubmenuItem)},isVisibleItem:function(t){if(!BX.type.isDomNode(t)){return false}return t.offsetTop===0},isMoreButton:function(t){var e=false;if(BX.type.isDomNode(t)&&BX.hasClass(t,this.classItemMore)){e=true}return e},isListItem:function(t){var e=false;if(BX.type.isDomNode(t)&&BX.hasClass(t,this.classItem)){e=true}return e},isSublink:function(t){var e=false;if(BX.type.isDomNode(t)){e=BX.hasClass(t,this.classItemSublink)}return e},isSeparator:function(t){var e=false;if(BX.type.isDomNode(t)){e=BX.hasClass(t,this.classSeporator)}return e},isDragToSubmenu:function(){return!this.isSubmenuItem(this.dragItem)&&this.isSubmenuItem(this.overItem)},isDragToList:function(){return this.isSubmenuItem(this.dragItem)&&!this.isSubmenuItem(this.overItem)}}}if(typeof BX.Main.interfaceButtonsManager==="undefined"){BX.Main.interfaceButtonsManager={data:{},init:function(t){var e=null;if(!BX.type.isPlainObject(t)||!("containerId"in t)){throw"BX.Main.interfaceButtonsManager: containerId not set in params Object"}e=BX(t.containerId);if(BX.type.isDomNode(e)){this.data[t.containerId]=new BX.Main.interfaceButtons(e,t)}else{BX(BX.delegate(function(){e=BX(t.containerId);if(!BX.type.isDomNode(e)){throw"BX.Main.interfaceButtonsManager: container is not dom node"}this.data[t.containerId]=new BX.Main.interfaceButtons(e,t)},this))}},getById:function(t){var e=null;if(BX.type.isString(t)&&BX.type.isNotEmptyString(t)){try{e=this.data[t]}catch(t){}}return e},getObjects:function(){return this.data}}}
/* End */
;
; /* Start:"a:4:{s:4:"full";s:94:"/bitrix/components/bitrix/main.interface.buttons/templates/.default/utils.min.js?1607488152575";s:6:"source";s:76:"/bitrix/components/bitrix/main.interface.buttons/templates/.default/utils.js";s:3:"min";s:80:"/bitrix/components/bitrix/main.interface.buttons/templates/.default/utils.min.js";s:3:"map";s:80:"/bitrix/components/bitrix/main.interface.buttons/templates/.default/utils.map.js";}"*/
(function(){"use strict";BX.namespace("BX.Buttons");BX.Buttons.Utils={getByClass:function(e,t,l){var n=[];if(t){n=(e||document.body).getElementsByClassName(t);if(!l){n=n.length?n[0]:null}else{n=[].slice.call(n)}}return n},getByTag:function(e,t,l){var n=[];if(t){n=(e||document.body).getElementsByTagName(t);if(!l){n=n.length?n[0]:null}else{n=[].slice.call(n)}}return n},getBySelector:function(e,t,l){var n=[];if(t){if(!l){n=(e||document.body).querySelector(t)}else{n=(e||document.body).querySelectorAll(t);n=[].slice.call(n)}}return n}}})();
/* End */
;
; /* Start:"a:4:{s:4:"full";s:91:"/bitrix/components/bitrix/tasks.iframe.popup/templates/.default/logic.min.js?16074881537131";s:6:"source";s:72:"/bitrix/components/bitrix/tasks.iframe.popup/templates/.default/logic.js";s:3:"min";s:76:"/bitrix/components/bitrix/tasks.iframe.popup/templates/.default/logic.min.js";s:3:"map";s:76:"/bitrix/components/bitrix/tasks.iframe.popup/templates/.default/logic.map.js";}"*/
BX.namespace("Tasks.Component");BX.Tasks.Component.IframePopup=function(t){this.opts=BX.merge({},t);this.vars={skip:true,callbacks:{},resizeInterval:false,resizeLock:true,lastHeight:false};this.sys={scope:null};this.instances={win:false};this.ctrls={iframe:null,wrap:null,close:null};this.setCallbacks(t.callbacks);this.bindEvents()};BX.mergeEx(BX.Tasks.Component.IframePopup.prototype,{add:function(t){this.edit(0,t)},view:function(t){this.open("view",t)},edit:function(t,e){this.open("edit",t,{urlParams:e})},open:function(t,e,n){e=parseInt(e);if(isNaN(e)||e<0){return}n=n||{};var i=this.getPath(t,e,n.urlParams);if(BX.Bitrix24&&"PageSlider"in BX.Bitrix24){BX.Bitrix24.PageSlider.open(i)}else{this.toggleLoading(true);this.getWindow().show();this.getWindow().setBindElement(this.getWindowCoords());this.getWindow().adjustPosition();this.getIframe().src=i}},close:function(){this.getWindow().close()},bindEvents:function(){BX.bind(window,"resize",BX.throttle(this.onWindowResize,100,this));BX.addCustomEvent(window,"tasksIframeLoad",this.onContentLoaded.bind(this));BX.addCustomEvent(window,"tasksIframeUnload",this.onContentUnLoaded.bind(this))},bindInnerDocumentEvents:function(){var t=this.getContentDocument();if(t){BX.bind(t,"keydown",this.onInnerDocumentKeyDown.bind(this))}},getIframe:function(){if(this.ctrls.iframe===null){this.ctrls.iframe=BX.create("iframe",{attrs:{scrolling:"no",frameBorder:"0"}})}return this.ctrls.iframe},getWindow:function(){if(this.instances.win===false){this.instances.win=new BX.PopupWindow("tasks-iframe-popup",{top:0,left:0},{autoHide:false,closeByEsc:true,content:this.getIframeContainer(),overlay:true,lightShadow:false,closeIcon:true,contentNoPaddings:true,draggable:false,titleBar:true,events:{onPopupClose:BX.delegate(this.onPopupClose,this),onPopupShow:BX.delegate(this.onPopupOpen,this)}});this.ctrls.close=BX.create("div",{props:{className:"hidden"},attrs:{id:"tasks-popup-close",title:BX.message("TASKS_TIP_COMPONENT_TEMPLATE_CLOSE_WINDOW")},events:{click:BX.delegate(this.onCloseClicked,this)},children:[BX.create("span")]});BX.insertAfter(this.ctrls.close,BX("popup-window-overlay-tasks-iframe-popup"))}return this.instances.win},setTitle:function(t,e){var n="";if(t!=false){t=t=="view"?"VIEW":"EDIT";e=parseInt(e);if(isNaN(e)||e<=0){e=0}if(t=="EDIT"&&e==0){t="NEW"}n=BX.message("TASKS_TIP_COMPONENT_TEMPLATE_"+t+"_TASK_TITLE");if(e>0){n=n.replace("#TASK_ID#",e)}}this.getWindow().setTitleBar(n)},getPath:function(t,e,n){t=t=="view"?"view":"edit";e=parseInt(e);var i=this.opts.pathToTasks.replace("#task_id#",e);i=i+(i.indexOf("?")==-1?"?":"&")+"IFRAME=Y";if(BX.type.isPlainObject(n)){for(var s in n){i+="&"+s+"="+encodeURIComponent(n[s])}}i=i.replace("#action#",t);return i},getWindowCoords:function(){var t=BX.pos(this.getIframeContainer()).width;var e=BX.GetWindowSize().innerWidth;var n=BX.GetWindowScrollPos().scrollTop;return{left:Math.floor((e-t)/2),top:30+n}},getContentDocument:function(){var t=this.getIframe();var e=null;if(t.contentDocument){e=t.contentDocument}if(t.contentWindow){e=t.contentWindow.document}return e&&e.body?e:null},getIframeContainer:function(){if(this.ctrls.wrap===null){this.ctrls.wrap=this.ctrls.wrap=BX.create("div",{props:{className:"tasks-iframe-wrap loading fixedHeight"},attrs:{id:"tasks-iframe-wrap"},children:[this.getIframe()]})}return this.ctrls.wrap},getContentContainer:function(){var t=this.getContentDocument();if(t){return t.getElementById("tasks-content-outer")}return null},onCloseClicked:function(){this.getWindow().close()},onTaskGlobalEvent:function(t,e){if(BX.type.isNotEmptyString(t)){var n=t.toString().toUpperCase();e=e||{};e.task=e.task||{};e.options=e.options||{};var i=[];var s=parseInt(e.task.ID);if(n=="DELETE"&&!isNaN(s)&&s){i.push(e.task.ID)}else if(n=="ADD"||n=="UPDATE"){if(e.taskUgly){i.push(e.taskUgly)}else{return}}if(!e.options.STAY_AT_PAGE){this.close()}if(typeof this.vars.callbacks[n]!="undefined"&&this.vars.callbacks[n]!==false){var o=this.vars.callbacks[n];if(BX.type.isString(o)){o=BX.Tasks.deReference(o,window)}if(BX.type.isFunction(o)){o.apply(window,i)}}}},onContentLoaded:function(){var t=this.getContentDocument();if(t){var e=this.parseUrl(t.location.pathname);if(e){this.setTitle(e.action,e.taskId)}}this.toggleLoading(false);this.startMonitorContent();this.bindInnerDocumentEvents()},onContentUnLoaded:function(){this.setTitle(false);this.stopMonitorContent()},onPopupOpen:function(){BX.toggleClass(this.ctrls.close,"hidden");this.toggleLoading(true)},onPopupClose:function(){BX.toggleClass(this.ctrls.close,"hidden");this.lockHeight();this.stopMonitorContent();this.toggleLoading(true);this.vars.lastHeight=false;this.getIframe().src="about:blank"},onWindowResize:function(){if(this.getWindow().isShown()){this.getWindow().setBindElement(this.getWindowCoords())}},onContentResize:function(){if(this.getWindow().isShown()&&!this.vars.resizeLock){var t=this.getContentDocument();if(t){var e=this.getContentContainer();if(e){var n=e.offsetHeight;if(n!=this.vars.lastHeight){this.getIframeContainer().style.height=n+"px";this.vars.lastHeight=n;this.unLockHeight()}this.getWindow().popupContainer.style.marginBottom="40px";this.getWindow().resizeOverlay()}}}},onInnerDocumentKeyDown:function(t){if(BX.Tasks.Util.isEsc(t)){this.close()}},lockHeight:function(){this.toggleFixedHeight(true)},unLockHeight:function(){this.toggleFixedHeight(false)},toggleFixedHeight:function(t){BX[t?"addClass":"removeClass"](this.getIframeContainer(),"fixedHeight")},toggleLoading:function(t){BX[t?"addClass":"removeClass"](this.getIframeContainer(),"loading")},stopMonitorContent:function(){this.vars.resizeLock=true},startMonitorContent:function(){this.vars.resizeLock=false;if(this.vars.resizeInterval===false){this.vars.resizeInterval=setInterval(BX.proxy(this.onContentResize,this),300)}},setCallbacks:function(t){if(BX.type.isPlainObject(t)){BX.Tasks.each(t,function(t,e){if(t=="#SHOW_ADDED_TASK_DETAIL#"){return}if(t!==false&&(BX.type.isFunction(t)||BX.type.isNotEmptyString(t))){this.vars.callbacks[e]=t}}.bind(this))}},showCreateForm:function(){this.add()},parseUrl:function(t){var e=this.opts.pathToTasks;if(e){e=e.toLowerCase().replace("#action#","(view|edit){1}").replace("#task_id#","(\\d+)");var n=t.match(new RegExp(e));if(n&&BX.type.isArray(n)){var i=n[1]||false;var s=n[2]||false;if(i&&s){return{action:i,taskId:parseInt(s)}}}}return null},onTaskAdded:function(t,e,n,i,s){BX.onCustomEvent(this,"onTaskAdded",[t,e,n,i,s])},onTaskChanged:function(t,e,n,i,s){BX.onCustomEvent(this,"onTaskChanged",[t,e,n,i,s])},onTaskDeleted:function(t){BX.onCustomEvent(this,"onTaskDeleted",[t])}});BX.Tasks.Component.IframePopup.create=function(t){if(window.top!=window){return}if(typeof BX.Tasks.Singletons=="undefined"){BX.Tasks.Singletons={}}if(typeof BX.Tasks.Singletons.iframePopup=="undefined"){BX.Tasks.Singletons.iframePopup=new BX.Tasks.Component.IframePopup(t);window.taskIFramePopup=BX.Tasks.Singletons.iframePopup;window.BX.TasksIFrameInst=BX.Tasks.Singletons.iframePopup}else{BX.Tasks.Singletons.iframePopup.setCallbacks(t.callbacks)}return BX.Tasks.Singletons.iframePopup};
/* End */
;
; /* Start:"a:4:{s:4:"full";s:67:"/bitrix/components/bitrix/search.title/script.min.js?16074881606443";s:6:"source";s:48:"/bitrix/components/bitrix/search.title/script.js";s:3:"min";s:52:"/bitrix/components/bitrix/search.title/script.min.js";s:3:"map";s:52:"/bitrix/components/bitrix/search.title/script.map.js";}"*/
function JCTitleSearch(t){var e=this;this.arParams={AJAX_PAGE:t.AJAX_PAGE,CONTAINER_ID:t.CONTAINER_ID,INPUT_ID:t.INPUT_ID,MIN_QUERY_LEN:parseInt(t.MIN_QUERY_LEN)};if(t.WAIT_IMAGE)this.arParams.WAIT_IMAGE=t.WAIT_IMAGE;if(t.MIN_QUERY_LEN<=0)t.MIN_QUERY_LEN=1;this.cache=[];this.cache_key=null;this.startText="";this.running=false;this.runningCall=false;this.currentRow=-1;this.RESULT=null;this.CONTAINER=null;this.INPUT=null;this.WAIT=null;this.ShowResult=function(t){if(BX.type.isString(t)){e.RESULT.innerHTML=t}e.RESULT.style.display=e.RESULT.innerHTML!==""?"block":"none";var s=e.adjustResultNode();var i;var n;var l=BX.findChild(e.RESULT,{tag:"table",class:"title-search-result"},true);if(l){n=BX.findChild(l,{tag:"th"},true)}if(n){var r=BX.pos(l);r.width=r.right-r.left;var a=BX.pos(n);a.width=a.right-a.left;n.style.width=a.width+"px";e.RESULT.style.width=s.width+a.width+"px";e.RESULT.style.left=s.left-a.width-1+"px";if(r.width-a.width>s.width)e.RESULT.style.width=s.width+a.width-1+"px";r=BX.pos(l);i=BX.pos(e.RESULT);if(i.right>r.right){e.RESULT.style.width=r.right-r.left+"px"}}var o;if(l)o=BX.findChild(e.RESULT,{class:"title-search-fader"},true);if(o&&n){i=BX.pos(e.RESULT);o.style.left=i.right-i.left-18+"px";o.style.width=18+"px";o.style.top=0+"px";o.style.height=i.bottom-i.top+"px";o.style.display="block"}};this.onKeyPress=function(t){var s=BX.findChild(e.RESULT,{tag:"table",class:"title-search-result"},true);if(!s)return false;var i;var n=s.rows.length;switch(t){case 27:e.RESULT.style.display="none";e.currentRow=-1;e.UnSelectAll();return true;case 40:if(e.RESULT.style.display=="none")e.RESULT.style.display="block";var l=-1;for(i=0;i<n;i++){if(!BX.findChild(s.rows[i],{class:"title-search-separator"},true)){if(l==-1)l=i;if(e.currentRow<i){e.currentRow=i;break}else if(s.rows[i].className=="title-search-selected"){s.rows[i].className=""}}}if(i==n&&e.currentRow!=i)e.currentRow=l;s.rows[e.currentRow].className="title-search-selected";return true;case 38:if(e.RESULT.style.display=="none")e.RESULT.style.display="block";var r=-1;for(i=n-1;i>=0;i--){if(!BX.findChild(s.rows[i],{class:"title-search-separator"},true)){if(r==-1)r=i;if(e.currentRow>i){e.currentRow=i;break}else if(s.rows[i].className=="title-search-selected"){s.rows[i].className=""}}}if(i<0&&e.currentRow!=i)e.currentRow=r;s.rows[e.currentRow].className="title-search-selected";return true;case 13:if(e.RESULT.style.display=="block"){for(i=0;i<n;i++){if(e.currentRow==i){if(!BX.findChild(s.rows[i],{class:"title-search-separator"},true)){var a=BX.findChild(s.rows[i],{tag:"a"},true);if(a){window.location=a.href;return true}}}}}return false}return false};this.onTimeout=function(){e.onChange(function(){setTimeout(e.onTimeout,500)})};this.onChange=function(t){if(e.running){e.runningCall=true;return}e.running=true;if(e.INPUT.value!=e.oldValue&&e.INPUT.value!=e.startText){e.oldValue=e.INPUT.value;if(e.INPUT.value.length>=e.arParams.MIN_QUERY_LEN){e.cache_key=e.arParams.INPUT_ID+"|"+e.INPUT.value;if(e.cache[e.cache_key]==null){if(e.WAIT){var s=BX.pos(e.INPUT);var i=s.bottom-s.top-2;e.WAIT.style.top=s.top+1+"px";e.WAIT.style.height=i+"px";e.WAIT.style.width=i+"px";e.WAIT.style.left=s.right-i+2+"px";e.WAIT.style.display="block"}BX.ajax.post(e.arParams.AJAX_PAGE,{ajax_call:"y",INPUT_ID:e.arParams.INPUT_ID,q:e.INPUT.value,l:e.arParams.MIN_QUERY_LEN},function(s){e.cache[e.cache_key]=s;e.ShowResult(s);e.currentRow=-1;e.EnableMouseEvents();if(e.WAIT)e.WAIT.style.display="none";if(!!t)t();e.running=false;if(e.runningCall){e.runningCall=false;e.onChange()}});return}else{e.ShowResult(e.cache[e.cache_key]);e.currentRow=-1;e.EnableMouseEvents()}}else{e.RESULT.style.display="none";e.currentRow=-1;e.UnSelectAll()}}if(!!t)t();e.running=false};this.onScroll=function(){if(BX.type.isElementNode(e.RESULT)&&e.RESULT.style.display!=="none"&&e.RESULT.innerHTML!==""){e.adjustResultNode()}};this.UnSelectAll=function(){var t=BX.findChild(e.RESULT,{tag:"table",class:"title-search-result"},true);if(t){var s=t.rows.length;for(var i=0;i<s;i++)t.rows[i].className=""}};this.EnableMouseEvents=function(){var t=BX.findChild(e.RESULT,{tag:"table",class:"title-search-result"},true);if(t){var s=t.rows.length;for(var i=0;i<s;i++)if(!BX.findChild(t.rows[i],{class:"title-search-separator"},true)){t.rows[i].id="row_"+i;t.rows[i].onmouseover=function(t){if(e.currentRow!=this.id.substr(4)){e.UnSelectAll();this.className="title-search-selected";e.currentRow=this.id.substr(4)}};t.rows[i].onmouseout=function(t){this.className="";e.currentRow=-1}}}};this.onFocusLost=function(t){setTimeout(function(){e.RESULT.style.display="none"},250)};this.onFocusGain=function(){if(e.RESULT.innerHTML.length)e.ShowResult()};this.onKeyDown=function(t){if(!t)t=window.event;if(e.RESULT.style.display=="block"){if(e.onKeyPress(t.keyCode))return BX.PreventDefault(t)}};this.adjustResultNode=function(){if(!(BX.type.isElementNode(e.RESULT)&&BX.type.isElementNode(e.CONTAINER))){return{top:0,right:0,bottom:0,left:0,width:0,height:0}}var t=BX.pos(e.CONTAINER);e.RESULT.style.position="absolute";e.RESULT.style.top=t.bottom+2+"px";e.RESULT.style.left=t.left+"px";e.RESULT.style.width=t.width+"px";return t};this._onContainerLayoutChange=function(){if(BX.type.isElementNode(e.RESULT)&&e.RESULT.style.display!=="none"&&e.RESULT.innerHTML!==""){e.adjustResultNode()}};this.Init=function(){this.CONTAINER=document.getElementById(this.arParams.CONTAINER_ID);BX.addCustomEvent(this.CONTAINER,"OnNodeLayoutChange",this._onContainerLayoutChange);this.RESULT=document.body.appendChild(document.createElement("DIV"));this.RESULT.className="title-search-result";this.INPUT=document.getElementById(this.arParams.INPUT_ID);this.startText=this.oldValue=this.INPUT.value;BX.bind(this.INPUT,"focus",function(){e.onFocusGain()});BX.bind(this.INPUT,"blur",function(){e.onFocusLost()});this.INPUT.onkeydown=this.onKeyDown;if(this.arParams.WAIT_IMAGE){this.WAIT=document.body.appendChild(document.createElement("DIV"));this.WAIT.style.backgroundImage="url('"+this.arParams.WAIT_IMAGE+"')";if(!BX.browser.IsIE())this.WAIT.style.backgroundRepeat="none";this.WAIT.style.display="none";this.WAIT.style.position="absolute";this.WAIT.style.zIndex="1100"}BX.bind(this.INPUT,"bxchange",function(){e.onChange()});var t=BX.findParent(this.CONTAINER,BX.is_fixed);if(BX.type.isElementNode(t)){BX.bind(window,"scroll",BX.throttle(this.onScroll,100,this))}};BX.ready(function(){e.Init(t)})}
/* End */
;
; /* Start:"a:4:{s:4:"full";s:96:"/bitrix/templates/bitrix24/components/bitrix/search.title/.default/script.min.js?160748813228167";s:6:"source";s:76:"/bitrix/templates/bitrix24/components/bitrix/search.title/.default/script.js";s:3:"min";s:80:"/bitrix/templates/bitrix24/components/bitrix/search.title/.default/script.min.js";s:3:"map";s:80:"/bitrix/templates/bitrix24/components/bitrix/search.title/.default/script.map.js";}"*/
BX.namespace("BX.B24SearchTitle");BX.B24SearchTitle=function(e){var t=this;this.arParams={AJAX_PAGE:e.AJAX_PAGE,CONTAINER_ID:e.CONTAINER_ID,INPUT_ID:e.INPUT_ID,MIN_QUERY_LEN:parseInt(e.MIN_QUERY_LEN),FORMAT:typeof e.FORMAT!="undefined"&&e.FORMAT=="json"?"json":"html",CATEGORIES_ALL:typeof e.CATEGORIES_ALL!="undefined"?e.CATEGORIES_ALL:[],USER_URL:typeof e.USER_URL!="undefined"?e.USER_URL:"",GROUP_URL:typeof e.GROUP_URL!="undefined"?e.GROUP_URL:"",WAITER_TEXT:typeof e.WAITER_TEXT!="undefined"?e.WAITER_TEXT:"",CURRENT_TS:parseInt(e.CURRENT_TS),GLOBAL_SEARCH_CATEGORIES:typeof e.GLOBAL_SEARCH_CATEGORIES=="object"?e.GLOBAL_SEARCH_CATEGORIES:[],MORE_USERS_URL:e.MORE_USERS_URL,IS_CRM_INSTALLED:e.IS_CRM_INSTALLED=="Y"};if(e.MIN_QUERY_LEN<=0)e.MIN_QUERY_LEN=1;this.cache=[];this.cache_key=null;this.startText="";this.currentRow=-1;this.RESULT=null;this.CONTAINER=null;this.INPUT=null;this.xhr=null;this.blockAjax=false;this.searchStarted=false;this.ITEMS={obClientDb:null,obClientDbData:{},obClientDbDataSearchIndex:{},bMenuInitialized:false,initialized:{sonetgroups:false,menuitems:false},oDbSearchResult:{}};this.searchByAjax=false;this.selectedItemDataId=null;this.CreateResultWrap=function(){if(t.RESULT==null){this.RESULT=document.body.appendChild(document.createElement("DIV"));this.RESULT.className="title-search-result title-search-result-header search-title-top-result-header"}};this.MakeResultFromClientDB=function(e,s){var i=null;var a,r,l,o,n=null;for(a=0;a<e.length;a++){var c=e[a].toLowerCase();if(typeof t.ITEMS.oDbSearchResult[c]!="undefined"&&t.ITEMS.oDbSearchResult[c].length>0){for(r=0;r<t.ITEMS.oDbSearchResult[c].length;r++){o=t.ITEMS.oDbSearchResult[c][r];n=o.substr(0,1);for(l=0;l<t.arParams.CATEGORIES_ALL.length;l++){if(typeof t.arParams.CATEGORIES_ALL[l].CLIENTDB_PREFIX!="undefined"&&t.arParams.CATEGORIES_ALL[l].CLIENTDB_PREFIX==n){if(i==null){i={}}if(typeof i.CATEGORIES=="undefined"){i.CATEGORIES={}}if(typeof i.CATEGORIES[l]=="undefined"){i.CATEGORIES[l]={ITEMS:[],TITLE:t.arParams.CATEGORIES_ALL[l].TITLE}}if(n=="U"){i.CATEGORIES[l].ITEMS.push({ICON:typeof t.ITEMS.obClientDbData.users[o].avatar!="undefined"?t.ITEMS.obClientDbData.users[o].avatar:"",ITEM_ID:o,MODULE_ID:"",NAME:t.ITEMS.obClientDbData.users[o].name,PARAM1:"",URL:t.arParams.USER_URL.replace("#user_id#",t.ITEMS.obClientDbData.users[o].entityId),TYPE:"users"})}else if(n=="G"){if(typeof t.ITEMS.obClientDbData.sonetgroups[o].site!="undefined"&&t.ITEMS.obClientDbData.sonetgroups[o].site==BX.message("SITE_ID")){i.CATEGORIES[l].ITEMS.push({ICON:typeof t.ITEMS.obClientDbData.sonetgroups[o].avatar!="undefined"?t.ITEMS.obClientDbData.sonetgroups[o].avatar:"",ITEM_ID:o,MODULE_ID:"",NAME:t.ITEMS.obClientDbData.sonetgroups[o].name,PARAM1:"",URL:t.arParams.GROUP_URL.replace("#group_id#",t.ITEMS.obClientDbData.sonetgroups[o].entityId),TYPE:"sonetgroups",IS_MEMBER:typeof t.ITEMS.obClientDbData.sonetgroups[o].isMember!="undefined"&&t.ITEMS.obClientDbData.sonetgroups[o].isMember=="Y"?1:0})}}else if(n=="M"){i.CATEGORIES[l].ITEMS.push({ICON:"",ITEM_ID:o,MODULE_ID:"",NAME:t.ITEMS.obClientDbData.menuitems[o].name,PARAM1:"",URL:t.ITEMS.obClientDbData.menuitems[o].entityId,CHAIN:typeof t.ITEMS.obClientDbData.menuitems[o].chain!="undefined"?t.ITEMS.obClientDbData.menuitems[o].chain:false})}break}}}}}if(i!==null){for(var h in i.CATEGORIES){if(i.CATEGORIES.hasOwnProperty(h)){i.CATEGORIES[h].ITEMS.sort(t.resultCmp)}}}return i};this.resultCmp=function(e,t){if(typeof e.TYPE!="undefined"&&typeof t.TYPE!="undefined"&&e.TYPE=="sonetgroups"&&t.TYPE=="sonetgroups"&&typeof e.IS_MEMBER!="undefined"&&typeof t.IS_MEMBER!="undefined"){if(e.IS_MEMBER==t.IS_MEMBER){if(e.NAME==t.NAME){return 0}return e.NAME<t.NAME?-1:1}return e.IS_MEMBER>t.IS_MEMBER?-1:1}else{if(e.NAME==t.NAME){return 0}return e.NAME<t.NAME?-1:1}};this.BuildResult=function(e,s){var i=[];var a=null;var r=null;var l="";var o=true;if(typeof e==="object"&&e&&typeof e.CATEGORIES!="undefined"&&BX.type.isNotEmptyObject(e.CATEGORIES)){for(var n in e.CATEGORIES){if(n=="all")continue;if(e.CATEGORIES.hasOwnProperty(n)){if(o){o=false}a=e.CATEGORIES[n];if(typeof a.ITEMS!="undefined"){var c=0;var h=false;var u=[];for(var f in a.ITEMS){if(a.ITEMS.hasOwnProperty(f)){if(c>=7){h=true;break}var d=a.ITEMS[f];if(d.TYPE=="all")continue;if(d.TYPE=="users"||d.TYPE=="sonetgroups"){l="search-title-top-block-"+d.TYPE}else{l="search-title-top-block-section"}r=this.BuildResultItem(d);u.push(r);c++}}if(u&&d){i.push(BX.create("div",{attrs:{className:"search-title-top-block "+l},children:[BX.create("div",{props:{className:"search-title-top-subtitle"},children:[BX.create("div",{props:{className:"search-title-top-subtitle-text"},html:a.TITLE})]}),BX.create("div",{props:{className:"search-title-top-list-wrap"},children:[BX.create("div",{attrs:{className:"search-title-top-list search-title-top-list-js","bx-search-block-id":d.TYPE},children:u})]})]}));if(h&&d.TYPE=="users"){var E={URL:this.arParams.MORE_USERS_URL+this.INPUT.value,ITEM_ID:d.TYPE+"_more"};var p=this.BuildMoreBlock(E);i.push(p)}}}}}}i.push(BX.create("div",{attrs:{style:"margin-bottom: 20px;"+(!s?"display:none;":""),id:"title-search-waiter"},children:[BX.create("div",{props:{className:"title-search-waiter"},children:[BX.create("span",{props:{className:"title-search-waiter-img"}}),BX.create("span",{props:{className:"title-search-waiter-text"},html:t.arParams.WAITER_TEXT})]})]}));i=this.BuildGlobalSearchCategories(i);var I=BX.create("div",{props:{className:"search-title-top-result"},children:i});return I};this.BuildResultItem=function(e){if(!(typeof e=="object"&&e))return;if(this.selectedItemDataId==null){this.selectedItemDataId=e.ITEM_ID}var t=BX.create("div",{attrs:{className:"search-title-top-item search-title-top-item-js"+(this.selectedItemDataId==e.ITEM_ID?" search-title-top-item-selected":""),title:typeof e.CHAIN!="undefined"&&BX.type.isArray(e.CHAIN)?e.CHAIN.join(" -> "):"","bx-search-item-id":e.ITEM_ID},children:[BX.create("a",{attrs:{href:e.URL,className:"search-title-top-item-link"},children:[e.TYPE=="users"||e.TYPE=="sonetgroups"?BX.create("span",{attrs:{style:typeof e.ICON!="undefined"&&e.ICON.length>0?"background-image: url('"+e.ICON+"')":""},props:{className:"search-title-top-item-img"+(!e.ICON?" search-title-top-item-img-default-"+e.TYPE:"")}}):"",BX.create("span",{props:{className:"search-title-top-item-text"},children:[BX.create("span",{html:e.NAME})]})]}),e.TYPE=="users"?BX.create("span",{attrs:{className:"search-title-top-item-message"},events:{click:BX.proxy(function(){if(BX.IM){BXIM.openMessenger(this.userId)}else{window.open("","","status=no,scrollbars=yes,resizable=yes,width=700,height=550,top="+Math.floor((screen.height-550)/2-14)+",left="+Math.floor((screen.width-700)/2-5));return false}},{userId:e.ITEM_ID.substring(1)})}}):""],events:{mouseover:BX.proxy(function(){this.UnSelectAll();this.SelectItem(BX.proxy_context)},this),mouseout:BX.proxy(function(){this.UnSelectItem(BX.proxy_context);this.selectedItemDataId=null},this)}});return t};this.BuildMoreBlock=function(e){var t=BX.create("div",{attrs:{className:"search-title-top-block search-title-top-more-block",style:"margin-top: -35px;"},children:[BX.create("div",{props:{className:"search-title-top-list-wrap"},children:[BX.create("div",{attrs:{className:"search-title-top-list search-title-top-list-js"},children:[BX.create("div",{attrs:{className:"search-title-top-more search-title-top-item-js","bx-search-item-id":e.ITEM_ID},children:[BX.create("a",{attrs:{className:"search-title-top-more-text",href:e.URL},html:BX.message("SEARCH_MORE")})]})]})]})]});return t};this.BuildGlobalSearchCategories=function(e){var t=[];for(var s in this.arParams.GLOBAL_SEARCH_CATEGORIES){if(!this.arParams.GLOBAL_SEARCH_CATEGORIES.hasOwnProperty(s))continue;var i=this.arParams.GLOBAL_SEARCH_CATEGORIES[s].limited===true;var a={NAME:this.arParams.GLOBAL_SEARCH_CATEGORIES[s].text,URL:this.arParams.GLOBAL_SEARCH_CATEGORIES[s].url+(i?"":this.INPUT.value),ITEM_ID:s};var r=this.BuildResultItem(a);t.push(r)}var l=BX.create("div",{attrs:{className:"search-title-top-block search-title-top-block-tools",id:"search-title-block-tools"},children:[BX.create("div",{props:{className:"search-title-top-subtitle"},children:[BX.create("div",{props:{className:"search-title-top-subtitle-text"},html:BX.message("GLOBAL_SEARCH")})]}),BX.create("div",{attrs:{className:"search-title-top-list-height-wrap",id:"search-title-global-categories-height-wrap"},children:[BX.create("div",{attrs:{className:"search-title-top-list-wrap",id:"search-title-global-categories-wrap"},children:[BX.create("div",{attrs:{className:"search-title-top-list search-title-top-list-js"},children:t}),BX.create("div",{attrs:{className:"search-title-top-arrow"}})]})]})]});e.push(l);return e};this.BuildEntities=function(e){var s=[];var i=[];var a=[];var r=[];var l=[];var o=[];var n=[];var c=[];var h=false,u=false,f=false,d=false,E=false,p=false,I=false,m=false;var T=e&&e.data&&BX.type.isArray(e.data.items)?e.data.items:[];for(var S=0;S<T.length;S++){var B=e.data.items[S];var R={NAME:BX.util.htmlspecialchars(B.title),URL:B.links.show,ITEM_ID:B.type+B.id};if(B.type==="CONTACT"){if(s.length<10){s.push(R)}else{h=true}}else if(B.type==="COMPANY"){if(i.length<10){i.push(R)}else{u=true}}else if(B.type==="DEAL"){if(a.length<10){a.push(R)}else{f=true}}else if(B.type==="LEAD"){if(r.length<10){r.push(R)}else{d=true}}else if(B.type==="QUOTE"){if(l.length<10){l.push(R)}else{p=true}}else if(B.type==="INVOICE"){if(o.length<10){o.push(R)}else{E=true}}else if(B.module==="disk"){if(n.length<10){n.push(R)}else{I=true}}else if(B.type==="TASK"){if(c.length<10){c.push(R)}else{m=true}}}var C={};if(e&&e.data&&BX.type.isArray(e.data.limits)){e.data.limits.forEach(function(e){if(!BX.type.isPlainObject(e)){return}if(BX.type.isNotEmptyString(e.type)){C[e.type.toLowerCase()]=e}else if(BX.type.isNotEmptyString(e.module)){C[e.module.toLowerCase()]=e}})}this.BuildEntityBlock(a,"CRM: "+BX.message("SEARCH_CRM_DEAL"),"deal",C.deal);if(f){R={URL:this.arParams.GLOBAL_SEARCH_CATEGORIES["deal"]["url"]+this.INPUT.value,ITEM_ID:"deal_more"};var A=this.BuildMoreBlock(R);BX.firstChild(t.RESULT).insertBefore(A,BX("search-title-block-tools"))}this.BuildEntityBlock(s,"CRM: "+BX.message("SEARCH_CRM_CONTACT"),"contact",C.contact);if(h){R={URL:this.arParams.GLOBAL_SEARCH_CATEGORIES["contact"]["url"]+this.INPUT.value,ITEM_ID:"contact_more"};var A=this.BuildMoreBlock(R);BX.firstChild(t.RESULT).insertBefore(A,BX("search-title-block-tools"))}this.BuildEntityBlock(i,"CRM: "+BX.message("SEARCH_CRM_COMPANY"),"company",C.company);if(u){R={URL:this.arParams.GLOBAL_SEARCH_CATEGORIES["company"]["url"]+this.INPUT.value,ITEM_ID:"company_more"};var A=this.BuildMoreBlock(R);BX.firstChild(t.RESULT).insertBefore(A,BX("search-title-block-tools"))}this.BuildEntityBlock(r,"CRM: "+BX.message("SEARCH_CRM_LEAD"),"lead",C.lead);if(d){R={URL:this.arParams.GLOBAL_SEARCH_CATEGORIES["lead"]["url"]+this.INPUT.value,ITEM_ID:"lead_more"};var A=this.BuildMoreBlock(R);BX.firstChild(t.RESULT).insertBefore(A,BX("search-title-block-tools"))}this.BuildEntityBlock(o,"CRM: "+BX.message("SEARCH_CRM_INVOICE"),"invoice",C.invoice);if(E){R={URL:this.arParams.GLOBAL_SEARCH_CATEGORIES["invoice"]["url"]+this.INPUT.value,ITEM_ID:"invoice_more"};var A=this.BuildMoreBlock(R);BX.firstChild(t.RESULT).insertBefore(A,BX("search-title-block-tools"))}this.BuildEntityBlock(l,"CRM: "+BX.message("SEARCH_CRM_QUOTE"),"quote",C.quote);if(p){R={URL:this.arParams.GLOBAL_SEARCH_CATEGORIES["quote"]["url"]+this.INPUT.value,ITEM_ID:"quote_more"};var A=this.BuildMoreBlock(R);BX.firstChild(t.RESULT).insertBefore(A,BX("search-title-block-tools"))}this.BuildEntityBlock(n,BX.message("SEARCH_DISK"),"disk",C.disk);if(I){R={URL:this.arParams.GLOBAL_SEARCH_CATEGORIES["disk"]["url"]+this.INPUT.value,ITEM_ID:"disk_more"};var A=this.BuildMoreBlock(R);BX.firstChild(t.RESULT).insertBefore(A,BX("search-title-block-tools"))}this.BuildEntityBlock(c,BX.message("SEARCH_TASKS"),"task",C.task);if(m){R={URL:this.arParams.GLOBAL_SEARCH_CATEGORIES["tasks"]["url"]+this.INPUT.value,ITEM_ID:"task_more"};var A=this.BuildMoreBlock(R);BX.firstChild(t.RESULT).insertBefore(A,BX("search-title-block-tools"))}BX("title-search-waiter").style.display="none";t.checkSelectedItem()};this.BuildEntityBlock=function(e,s,i,a){if(e.length>0){var r=[];for(var l in e){var o=t.BuildResultItem(e[l]);r.push(o)}if(r){this.BuildEntity(r,s,i)}}else if(BX.type.isPlainObject(a)){this.buildLimits(a,s)}};this.BuildEntity=function(e,s,i){var a=BX.create("div",{attrs:{className:"search-title-top-block search-title-top-block-section"},children:[BX.create("div",{props:{className:"search-title-top-subtitle"},children:[BX.create("div",{props:{className:"search-title-top-subtitle-text"},html:s})]}),BX.create("div",{props:{className:"search-title-top-list-wrap"},children:[BX.create("div",{attrs:{className:"search-title-top-list search-title-top-list-js","bx-search-block-id":i},children:e})]})]});BX.firstChild(t.RESULT).insertBefore(a,BX("search-title-block-tools"))};this.buildLimits=function(e,s){var i=BX.create("div",{attrs:{className:"search-title-top-block search-title-top-block-section"},html:'<div class="search-title-top-subtitle">'+'<div class="search-title-top-subtitle-text">'+s+"</div>"+"</div>"+'<div class="search-title-top-list-wrap">'+'<div class="search-title-top-list">'+'<div class="search-title-top-list-limits">'+'<div class="search-title-top-list-limits-block">'+'<span class="search-title-top-list-limits-icon"></span>'+"</div>"+'<div class="search-title-top-list-limits-block">'+'<div class="search-title-top-list-limits-name">'+(BX.type.isString(e.title)?e.title:"")+"</div>"+'<div class="search-title-top-list-limits-content">'+(BX.type.isString(e.description)?e.description:"")+"</div>"+(BX.type.isArray(e.buttons)&&e.buttons.length>0?'<div class="ui-btn-container ui-btn-container-center">'+e.buttons.join("")+"</div>":"")+"</div>"+"</div>"+"</div>"+"</div>"});BX.firstChild(t.RESULT).insertBefore(i,BX("search-title-block-tools"))};this.checkSelectedItem=function(){var e=BX.findChild(t.RESULT,{className:"search-title-top-item-selected"},true);if(BX.type.isDomNode(e)&&BX("search-title-global-categories-wrap").contains(e)){var s=BX.findChild(t.RESULT,{className:"search-title-top-item-js"},true);t.UnSelectAll();t.SelectItem(s)}};this.ShowResult=function(e,s,i){t.CreateResultWrap();var a=0;var r=0;var l=0;if(BX.browser.IsIE()){a=0;r=1;l=-1;if(/MSIE 7/i.test(navigator.userAgent)){a=-1;r=-1;l=-2}}var o=BX.pos(t.CONTAINER);o.width=o.right-o.left;t.RESULT.style.position="absolute";t.RESULT.style.top=o.bottom+a-1+"px";t.RESULT.style.left=o.left+r+"px";t.RESULT.style.width=o.width+l+"px";if(typeof t.arParams.FORMAT!="undefined"&&t.arParams.FORMAT=="json"){e=t.BuildResult(e,!!s);BX.cleanNode(t.RESULT);if(BX.type.isDomNode(e)&&e.innerHTML.length){t.RESULT.appendChild(e);if(BX.type.isDomNode(BX("search-title-block-tools"))&&BX.type.isDomNode(BX("search-title-global-categories-wrap"))){BX.bind(BX("search-title-global-categories-wrap"),"mouseover",BX.proxy(function(){t.toggleGlobalCategories("open")},t));BX.bind(BX("search-title-global-categories-wrap"),"mouseout",BX.proxy(function(){t.toggleGlobalCategories("close")},t));t.RESULT.style.display="block"}else{t.RESULT.style.display="block"}if(i){BX("title-search-waiter").style.display="block";if(t.arParams.IS_CRM_INSTALLED){var n=BX.ajax.runAction("crm.api.entity.search",{data:{searchQuery:this.INPUT.value,options:{scope:"index"}}});n.then(this.BuildEntities.bind(this))}var c=BX.ajax.runAction("disk.commonActions.search",{data:{searchQuery:this.INPUT.value}});c.then(this.BuildEntities.bind(this));var h=BX.ajax.runAction("tasks.task.search",{data:{searchQuery:this.INPUT.value}});h.then(this.BuildEntities.bind(this))}}}else{t.RESULT.innerHTML=e}};this.toggleGlobalCategories=function(e){var t=BX("search-title-global-categories-wrap");var s=BX("search-title-global-categories-height-wrap");if(!BX.type.isDomNode(t)||!BX.type.isDomNode(s))return;if(e=="open"){BX.addClass(t,"search-title-top-list-wrap-hover");s.style.height=t.offsetHeight+"px"}else{var i=BX.findChild(t,{className:"search-title-top-item-selected"},true,false);if(!i){BX.removeClass(t,"search-title-top-list-wrap-hover");s.style.height=""}}};this.SyncResult=function(e,s){var i=null,a=[],r=[],l=[],o=[];s=s.toLowerCase();for(var n=0;n<t.arParams.CATEGORIES_ALL.length;n++){if(typeof t.arParams.CATEGORIES_ALL[n].CODE!="undefined"){if(typeof e.CATEGORIES[n]!="undefined"){if(t.arParams.CATEGORIES_ALL[n].CODE=="custom_menuitems"){i={};for(var c=0;c<e.CATEGORIES[n].ITEMS.length;c++){i[e.CATEGORIES[n].ITEMS[c].ITEM_ID]=t.ConvertAjaxToClientDB(e.CATEGORIES[n].ITEMS[c],"menuitems");l.push(e.CATEGORIES[n].ITEMS[c].ITEM_ID)}BX.onCustomEvent(t,"onFinderAjaxSuccess",[i,t.ITEMS,"menuitems"])}else if(t.arParams.CATEGORIES_ALL[n].CODE=="custom_sonetgroups"){i={};for(c=0;c<e.CATEGORIES[n].ITEMS.length;c++){i[e.CATEGORIES[n].ITEMS[c].ITEM_ID]=t.ConvertAjaxToClientDB(e.CATEGORIES[n].ITEMS[c],"sonetgroups");r.push(e.CATEGORIES[n].ITEMS[c].ITEM_ID)}BX.onCustomEvent(t,"onFinderAjaxSuccess",[i,t.ITEMS,"sonetgroups"])}else if(t.arParams.CATEGORIES_ALL[n].CODE=="custom_users"){for(c=0;c<e.CATEGORIES[n].ITEMS.length;c++){a.push(e.CATEGORIES[n].ITEMS[c].ITEM_ID)}}}var h=0;if(t.arParams.CATEGORIES_ALL[n].CODE=="custom_users"&&BX.type.isNotEmptyString(s)&&typeof t.ITEMS.oDbSearchResult[s]!="undefined"&&t.ITEMS.oDbSearchResult[s].length>0){o=[];for(h=0;h<t.ITEMS.oDbSearchResult[s].length;h++){if(t.ITEMS.oDbSearchResult[s][h].match(/U(\d+)/)!==null){o.push(t.ITEMS.oDbSearchResult[s][h])}}if(o.length>0){BX.onCustomEvent("syncClientDb",[t.ITEMS,false,o,a,"users"])}}if(t.arParams.CATEGORIES_ALL[n].CODE=="custom_sonetgroups"&&BX.type.isNotEmptyString(s)&&typeof t.ITEMS.oDbSearchResult[s]!="undefined"&&t.ITEMS.oDbSearchResult[s].length>0){o=[];for(h=0;h<t.ITEMS.oDbSearchResult[s].length;h++){if(t.ITEMS.oDbSearchResult[s][h].match(/G(\d+)/)!==null){o.push(t.ITEMS.oDbSearchResult[s][h])}}if(o.length>0){BX.onCustomEvent("syncClientDb",[t.ITEMS,false,o,r,"sonetgroups"])}}if(t.arParams.CATEGORIES_ALL[n].CODE=="custom_menuitems"&&BX.type.isNotEmptyString(s)&&typeof t.ITEMS.oDbSearchResult[s]!="undefined"&&t.ITEMS.oDbSearchResult[s].length>0){o=[];for(h=0;h<t.ITEMS.oDbSearchResult[s].length;h++){if(t.ITEMS.oDbSearchResult[s][h].match(/M\/(.+)/)!==null){o.push(t.ITEMS.oDbSearchResult[s][h])}}if(o.length>0){BX.onCustomEvent("syncClientDb",[t.ITEMS,false,o,l,"menuitems"])}}}}};this.ConvertAjaxToClientDB=function(e,t){var s=null;if(t=="sonetgroups"){s={id:"G"+e.ID,entityId:e.ID,name:e.NAME,avatar:e.ICON,desc:"",isExtranet:e.IS_EXTRANET?"Y":"N",site:e.SITE,checksum:e.CHECKSUM,isMember:typeof e.IS_MEMBER!="undefined"&&e.IS_MEMBER?"Y":"N"}}else if(t=="menuitems"){s={id:"M"+e.URL,entityId:e.URL,name:e.NAME,checksum:e.CHECKSUM,chain:typeof e.CHAIN!="undefined"&&BX.type.isArray(e.CHAIN)?e.CHAIN:null}}else if(t=="users"){s={id:"U"+e.ID,entityId:e.ID,name:e.NAME,login:e.LOGIN,active:e.ACTIVE,avatar:e.ICON,desc:e.DESCRIPTION,isExtranet:"N",isEmail:"N",checksum:e.CHECKSUM}}return s};this.onKeyPress=function(e){t.CreateResultWrap();var s=BX.findChild(t.RESULT,{tag:"div",class:"search-title-top-result"},true);if(!s)return false;var i=BX.findChildren(t.RESULT,{className:"search-title-top-list-js"},true);switch(e){case 27:t.RESULT.style.display="none";break;case 40:if(t.RESULT.style.display=="none")t.RESULT.style.display="block";var a=BX.findChildren(t.RESULT,{className:"search-title-top-item-js"},true);if(this.selectedItemDataId===null){t.SelectItem(a[0])}else{var r=t.RESULT.querySelector("[bx-search-item-id='"+t.selectedItemDataId+"']");if(!BX.type.isDomNode(r))return false;var l=BX.findParent(r,{className:"search-title-top-list-js"},true);if(!BX.type.isDomNode(l))return false;var o=BX.findChildren(l,{className:"search-title-top-item-js"},true);var n=r.offsetLeft;var c=r.offsetTop;var h=r.offsetWidth;var u=n+h;var f=[];var d=null;for(var E in o){if(o[E].offsetTop<=c){continue}else{if(d===null)d=o[E].offsetTop}if(d&&o[E].offsetTop==d){f.push(o[E])}}if(f.length>0){t.UnSelectAll();for(E in f){if(f[E].offsetLeft+f[E].offsetWidth>n){var p=f[Number(E)+1];if(p&&p.offsetLeft<=u){var I=f[E].offsetLeft+f[E].offsetWidth-n;var m=u-p.offsetLeft;if(m>I){t.SelectItem(p);return true}}t.SelectItem(f[E]);return true}}t.SelectItem(f[f.length-1]);return true}else{for(var E in i){if(i[E]==l){if(i[Number(E)+1]){t.UnSelectAll();var T=BX.firstChild(i[Number(E)+1],{className:"search-title-top-item-js"},true);if(BX.type.isDomNode(T)){t.SelectItem(T)}return true}}}}}return true;case 38:if(t.RESULT.style.display=="none")t.RESULT.style.display="block";if(this.selectedItemDataId!==null){r=t.RESULT.querySelector("[bx-search-item-id='"+t.selectedItemDataId+"']");if(!BX.type.isDomNode(r))return false;l=BX.findParent(r,{className:"search-title-top-list-js"},true);if(!BX.type.isDomNode(l))return false;o=BX.findChildren(l,{className:"search-title-top-item-js"},true);n=r.offsetLeft;c=r.offsetTop;h=r.offsetWidth;u=n+h;f=[];d=null;o=o.reverse();for(E in o){if(o[E].offsetTop>=c){continue}else{if(d===null)d=o[E].offsetTop}if(d&&o[E].offsetTop==d){f.push(o[E])}}f=f.reverse();if(f.length>0){t.UnSelectAll();for(E in f){if(f[E].offsetLeft+f[E].offsetWidth>n){p=f[Number(E)+1];if(p&&p.offsetLeft<=u){I=f[E].offsetLeft+f[E].offsetWidth-n;m=u-p.offsetLeft;if(m>I){t.SelectItem(p);return true}}t.SelectItem(f[E]);return true}}t.SelectItem(f[f.length-1]);return true}else{for(var E in i){if(i[E]==l){if(i[Number(E)-1]){t.UnSelectAll();T=BX.firstChild(i[Number(E)-1],{className:"search-title-top-item-js"},true);if(BX.type.isDomNode(T)){t.SelectItem(T)}}}}}}return true;case 39:if(this.selectedItemDataId!==null){r=t.RESULT.querySelector("[bx-search-item-id='"+t.selectedItemDataId+"']");if(!BX.type.isDomNode(r))return false;l=BX.findParent(r,{className:"search-title-top-list-js"},true);if(!BX.type.isDomNode(l))return false;o=BX.findChildren(l,{className:"search-title-top-item-js"},true);n=r.offsetLeft;c=r.offsetTop;for(E in o){if(o[E].offsetTop!=c)continue;if(o[E].offsetLeft>n){t.UnSelectAll();t.SelectItem(o[E]);return true}}}return true;case 37:if(this.selectedItemDataId!==null){r=t.RESULT.querySelector("[bx-search-item-id='"+t.selectedItemDataId+"']");if(!BX.type.isDomNode(r))return false;l=BX.findParent(r,{className:"search-title-top-list-js"},true);if(!BX.type.isDomNode(l))return false;o=BX.findChildren(l,{className:"search-title-top-item-js"},true);if(o){o=o.reverse()}n=r.offsetLeft;c=r.offsetTop;for(E in o){if(o[E].offsetTop!=c)continue;if(o[E].offsetLeft<n){t.UnSelectAll();t.SelectItem(o[E]);return true}}}return true;case 13:if(t.RESULT.style.display=="block"&&this.selectedItemDataId!==null){r=t.RESULT.querySelector("[bx-search-item-id='"+t.selectedItemDataId+"']");if(BX.type.isDomNode(r)){var S=BX.findChild(r,{tag:"a"},true);window.location=S.href}}return false}return false};this.UnSelectAll=function(){var e=BX.findChildren(t.RESULT,{className:"search-title-top-item-selected"},true);for(var s=0;s<e.length;s++){t.UnSelectItem(e[s])}};this.SelectItem=function(e){if(!BX.type.isDomNode(e))return;BX.addClass(e,"search-title-top-item-selected");t.selectedItemDataId=e.getAttribute("bx-search-item-id");var s=BX.findParent(e,{className:"search-title-top-block-tools"},true);if(BX.type.isDomNode(s)){t.toggleGlobalCategories("open")}};this.UnSelectItem=function(e){if(!BX.type.isDomNode(e))return;BX.removeClass(e,"search-title-top-item-selected");var s=BX.findParent(e,{className:"search-title-top-block-tools"},true);if(BX.type.isDomNode(s)){t.toggleGlobalCategories("close")}};this.onFocusGain=function(){if(t.RESULT&&t.RESULT.innerHTML.length){t.RESULT.style.display="block"}};this.onKeyUp=function(e){if(!t.searchStarted){return false}e=e||window.event;if(e.keyCode==37||e.keyCode==38||e.keyCode==39||e.keyCode==40)return;var s=BX.util.trim(t.INPUT.value);if(s.length>=1&&(s==t.oldValue||s==t.oldClientValue||s==t.startText)&&!(s==t.oldValue&&s!=t.oldClientValue&&t.oldValue.length==t.arParams.MIN_QUERY_LEN&&t.oldClientValue.length==t.arParams.MIN_QUERY_LEN-1)){return}if(t.xhr){t.xhr.abort()}if(s.length>=1){BX.removeClass(t.CONTAINER.parentNode.parentNode,"header-search-empty");BX.addClass(t.CONTAINER.parentNode.parentNode,"header-search-not-empty");t.selectedItemDataId=null;t.cache_key=t.arParams.INPUT_ID+"|"+s;if(t.cache[t.cache_key]==null){t.blockAjax=false;var i=[s];t.oldClientValue=s;var a={searchString:s};BX.onCustomEvent("findEntityByName",[t.ITEMS,a,{},t.ITEMS.oDbSearchResult]);if(a.searchString!=s){i.push(a.searchString)}var r=t.MakeResultFromClientDB(i,s);t.searchByAjax=false;t.ShowResult(r,s.length>=t.arParams.MIN_QUERY_LEN);if(s.length>=t.arParams.MIN_QUERY_LEN){t.SendAjax(s)}}else{t.blockAjax=true;t.oldClientValue=s;t.ShowResult(t.cache[t.cache_key],true,true)}}else{BX.addClass(t.CONTAINER.parentNode.parentNode,"header-search-empty");BX.removeClass(t.CONTAINER.parentNode.parentNode,"header-search-not-empty");if(t.RESULT){t.RESULT.style.display="none"}}};this.SendAjax=BX.debounce(function(e){if(t.blockAjax){return}t.oldValue=e;t.xhr=BX.ajax({method:"POST",dataType:t.arParams.FORMAT,url:t.arParams.AJAX_PAGE,data:{ajax_call:"y",INPUT_ID:t.arParams.INPUT_ID,FORMAT:t.arParams.FORMAT,q:e},preparePost:true,onsuccess:function(s){if(typeof s!="undefined"&&s&&s.CATEGORIES!="undefined"){for(var i in s.CATEGORIES){if(s.CATEGORIES.hasOwnProperty(i)){s.CATEGORIES[i].ITEMS.sort(t.resultCmp)}}t.cache[t.cache_key]=s;t.searchByAjax=true;t.ShowResult(s,false,true);t.SyncResult(s,e)}}})},1e3);this.onWindowResize=function(){if(t.RESULT!=null){t.ShowResult()}};this.onKeyDown=function(e){e=e||window.event;t.searchStarted=!(e.keyCode==27||e.keyCode==40||e.keyCode==38||e.keyCode==13);if(t.RESULT&&t.RESULT.style.display=="block"){if(t.onKeyPress(e.keyCode)){return BX.PreventDefault(e)}}};this.Init=function(){this.CONTAINER=BX(this.arParams.CONTAINER_ID);this.INPUT=BX(this.arParams.INPUT_ID);this.startText=this.oldValue=this.INPUT.value;BX.bind(this.INPUT,"focus",BX.proxy(this.onFocusGain,this));this.INPUT.onkeydown=this.onKeyDown;BX.Finder(false,"searchTitle",[],{},t);BX.onCustomEvent(t,"initFinderDb",[this.ITEMS,"searchTitle",null,["users","sonetgroups","menuitems"],t]);setTimeout(function(){t.CheckOldStorage(t.ITEMS.obClientDbData)},5e3);if(!this.ITEMS.bLoadAllInitialized){BX.addCustomEvent("loadAllFinderDb",BX.delegate(function(e){this.ItemsLoadAll(e)},this));this.ITEMS.bLoadAllInitialized=true}var e=BX.findChild(this.CONTAINER,{className:"search-title-top-delete"},true);if(BX.type.isDomNode(e)){BX.bind(e,"click",BX.proxy(function(e){this.INPUT.value="";this.onKeyUp()},this))}BX.bind(this.INPUT,"input",BX.proxy(function(t){this.onKeyDown(t);this.onKeyUp(t);var s=BX.findChild(this.CONTAINER,{className:"header-search-icon"},true);if(BX.type.isDomNode(e)){s.style.display=this.INPUT.value!=""?"none":"block"}},this));BX.bind(document,"click",BX.proxy(this.checkAutoHide,this))};this.checkAutoHide=function(e){if(t.RESULT&&!t.RESULT.contains(e.target)&&!document.forms["search-form"].contains(e.target)){setTimeout(function(){t.RESULT.style.display="none"},250)}};this.CheckOldStorage=function(e){if(!t.ITEMS.obClientDb){return}var s=null;var i=60*60*24*30;var a=null;for(var r in e){if(e.hasOwnProperty(r)){if(r=="sonetgroups"||r=="menuitems"){a=false;for(var l in e[r]){if(e[r].hasOwnProperty(l)){s=e[r][l];if(typeof s.timestamp!="undefined"&&parseInt(s.timestamp)>0&&t.arParams.CURRENT_TS>parseInt(s.timestamp)+i){a=true}break}}if(a){BX.Finder.clearEntityDb(t.ITEMS.obClientDb,r)}}}}};this.ItemsLoadAll=function(e){if(typeof e.entity!="undefined"&&typeof this.ITEMS.initialized[e.entity]!="undefined"&&!this.ITEMS.initialized[e.entity]&&typeof e.callback=="function"){if(e.entity=="sonetgroups"||e.entity=="menuitems"){BX.ajax.runAction("intranet.searchentity.getall",{data:{entity:e.entity}}).then(function(t){if(typeof t.data.items!="undefined"){BX.onCustomEvent("onFinderAjaxLoadAll",[t.data.items,this.ITEMS,e.entity]);e.callback()}}.bind(this),function(e){})}this.ITEMS.initialized[e.entity]=true}};BX.ready(function(){t.Init(e)})};
/* End */
;
; /* Start:"a:4:{s:4:"full";s:90:"/bitrix/templates/bitrix24/components/bitrix/menu/left_vertical/map.min.js?160748813210943";s:6:"source";s:70:"/bitrix/templates/bitrix24/components/bitrix/menu/left_vertical/map.js";s:3:"min";s:74:"/bitrix/templates/bitrix24/components/bitrix/menu/left_vertical/map.min.js";s:3:"map";s:74:"/bitrix/templates/bitrix24/components/bitrix/menu/left_vertical/map.map.js";}"*/
BX.namespace("BX.Bitrix24");BX.Bitrix24.SlidingPanel=function(t){this.containerClassName=this.containerClassName||"sliding-panel-window";this.container=BX.create("div",{props:{className:this.containerClassName}});this.overlayClassName=this.overlayClassName||"sliding-panel-overlay";this.overlay=BX.create("div",{props:{className:this.overlayClassName}});this.isOpen=false;this.header=BX("header");this.menuItems=BX("menu-items-block");this.imBar=BX("bx-im-bar");this.panel=BX("panel");this.creatorConfirmedPanel=BX("creatorconfirmed");this.animation=null;this.startParams=this.startParams||{};this.endParams=this.endParams||{};this.currentParams=null;BX.bind(this.container,"click",this.onContainerClick.bind(this));BX.addCustomEvent("onTopPanelCollapse",this.onTopPanelCollapse.bind(this))};BX.Bitrix24.SlidingPanel.prototype={animateStep:function(t){},setContent:function(){},open:function(){if(this.isOpen){return}this.isOpen=true;BX.bind(document,"keyup",BX.proxy(this.onDocumentKeyUp,this));BX.bind(document,"click",BX.proxy(this.onDocumentClick,this));this.header.addEventListener("click",BX.proxy(this.onHeaderClick,this),true);if(!this.overlay.parentNode){document.body.appendChild(this.overlay)}if(!this.container.parentNode){this.setContent();this.overlay.appendChild(this.container)}var t=window.innerWidth-document.documentElement.clientWidth;document.body.style.paddingRight=t+"px";if(this.imBar){this.imBar.style.right=t+"px"}if(this.panel){this.panel.style.zIndex=3001}if(this.creatorConfirmedPanel){this.creatorConfirmedPanel.style.zIndex=3e3}document.body.style.overflow="hidden";this.adjustPosition();if(this.animation){this.animation.stop()}this.animation=new BX.easing({duration:300,start:this.currentParams?this.currentParams:this.startParams,finish:this.endParams,transition:BX.easing.transitions.linear,step:BX.proxy(function(t){this.currentParams=t;this.animateStep(t)},this),complete:BX.proxy(function(){this.onTrasitionEnd()},this)});this.animation.animate()},close:function(t){if(!this.isOpen){if(this.animation){this.animation.stop(true)}return}this.isOpen=false;BX.unbind(document,"keyup",BX.proxy(this.onDocumentKeyUp,this));BX.unbind(document,"click",BX.proxy(this.onDocumentClick,this));this.header.removeEventListener("click",BX.proxy(this.onHeaderClick,this),true);this.container.classList.remove(this.containerClassName+"-open");if(this.animation){this.animation.stop()}if(t===true){this.currentParams=this.startParams;this.onTrasitionEnd()}else{this.animation=new BX.easing({duration:300,start:this.currentParams,finish:this.startParams,transition:BX.easing.transitions.linear,step:BX.proxy(function(t){this.currentParams=t;this.animateStep(t)},this),complete:BX.proxy(function(){this.onTrasitionEnd()},this)});this.animation.animate()}},adjustPosition:function(){var t=BX.pos(this.menuItems);var i=window.pageYOffset||document.documentElement.scrollTop;if(i>0){this.overlay.style.bottom=-i+"px"}var e=i>t.top?i:t.top;this.overlay.style.top=e+"px"},onTrasitionEnd:function(){this.animation=null;if(this.isOpen){this.currentParams=this.endParams;this.container.classList.add(this.containerClassName+"-open")}else{this.currentParams=this.startParams;if(this.overlay.parentNode){this.overlay.parentNode.removeChild(this.overlay)}if(this.imBar){this.imBar.style.right=""}if(this.panel){this.panel.style.cssText=""}if(this.creatorConfirmedPanel){this.creatorConfirmedPanel.style.cssText=""}document.body.style.cssText="";this.container.style.cssText="";this.header.style.cssText="";this.overlay.style.cssText=""}},onContainerClick:function(t){t.stopPropagation()},onDocumentKeyUp:function(t){if(t.keyCode===27){this.close()}},onDocumentClick:function(t){if(BX.isParentForNode(this.container,t.target)){return}this.close()},onHeaderClick:function(t){if(this.isOpen&&t.target.className.match(/help-/)){this.close(true)}},onTopPanelCollapse:function(){if(this.isOpen){this.adjustPosition()}}};BX.Bitrix24.GroupPanel=function(t){this.containerClassName="group-panel-window";this.overlayClassName="group-panel-overlay";this.startParams={translateX:-100,opacity:0};this.endParams={translateX:0,opacity:65};BX.Bitrix24.SlidingPanel.apply(this,arguments);t=t||{};this.ajaxPath=BX.type.isNotEmptyString(t.ajaxPath)?t.ajaxPath:null;this.siteId=BX.type.isNotEmptyString(t.siteId)?t.siteId:BX.message("SITE_ID");this.menu=BX("menu-all-groups-link");this.leftMenu=BX("menu-items-block");this.content=BX("group-panel-content");this.items=BX("group-panel-items");this.counter=BX("group-panel-header-filter-counter");var i=this.items.getElementsByClassName("group-panel-item-intranet");var e=this.items.getElementsByClassName("group-panel-item-extranet");if(i.length<=20&&e.length<=20){BX.addClass(this.container,"group-panel-window-one-column")}this.closeLink=BX("group-panel-close-link");this.filters=[].slice.call(this.content.getElementsByClassName("group-panel-header-filter"));for(var n=0;n<this.filters.length;n++){var s=this.filters[n];BX.bind(s,"click",BX.proxy(this.onFilterClick,this))}BX.bind(this.items,"click",this.onItemsClick.bind(this));BX.bind(this.closeLink,"click",this.close.bind(this));BX.bind(this.menu,"click",this.onMenuClick.bind(this));var a=function(){this.close(true)}.bind(this);BX.addCustomEvent("BX.Bitrix24.Map:onBeforeOpen",a);BX.addCustomEvent("BX.Bitrix24.LeftMenuClass:onDragStart",a);BX.addCustomEvent("BX.Bitrix24.LeftMenuClass:onMenuToggle",a)};BX.Bitrix24.GroupPanel.prototype=Object.create(BX.Bitrix24.SlidingPanel.prototype);BX.Bitrix24.GroupPanel.prototype.constructor=BX.Bitrix24.GroupPanel;BX.Bitrix24.GroupPanel.prototype.super=BX.Bitrix24.SlidingPanel.prototype;BX.Bitrix24.GroupPanel.prototype.setContent=function(){this.container.appendChild(this.content)};BX.Bitrix24.GroupPanel.prototype.animateStep=function(t){this.container.style.transform="translateX("+t.translateX+"%)";this.overlay.style.backgroundColor="rgba(0, 0, 0, "+t.opacity/100+")"};BX.Bitrix24.GroupPanel.prototype.open=function(){BX.onCustomEvent("BX.Bitrix24.GroupPanel:onBeforeOpen",[this]);if(window.pulse_loading&&window.pulse_loading.open){window.pulse_loading.close(true)}this.container.style.display="block";BX.addClass(this.menu.parentNode,"menu-item-block-hover");this.menu.innerHTML=BX.message("menu_hide");var t=BX.pos(this.leftMenu);this.overlay.style.left=t.right+"px";this.super.open.apply(this,arguments);BX.onCustomEvent("BX.Bitrix24.GroupPanel:onOpen",[this])};BX.Bitrix24.GroupPanel.prototype.close=function(){BX.onCustomEvent("BX.Bitrix24.GroupPanel:onBeforeClose",[this]);this.menu.innerHTML=BX.message("menu_show");this.super.close.apply(this,arguments);BX.onCustomEvent("BX.Bitrix24.GroupPanel:onClose",[this])};BX.Bitrix24.GroupPanel.prototype.onTrasitionEnd=function(){this.super.onTrasitionEnd.apply(this,arguments);if(!this.isOpen){this.leftMenu.style.removeProperty("z-index");BX.removeClass(this.menu.parentNode,"menu-item-block-hover")}};BX.Bitrix24.GroupPanel.prototype.onMenuClick=function(t){if(this.isOpen){this.close()}else{this.open()}t.stopPropagation()};BX.Bitrix24.GroupPanel.prototype.onFilterClick=function(t){var i=BX.type.isDomNode(BX.proxy_context)?BX.proxy_context:null;var e=this.content.dataset.filter?this.content.dataset.filter:"all";var n=i.dataset.filter?i.dataset.filter:"all";if(e!==n){this.content.dataset.filter=n;this.saveFilter(n);new BX.easing({duration:50,start:{opacity:1},finish:{opacity:0},transition:BX.easing.transitions.linear,step:BX.delegate(function(t){this.items.style.opacity=t.opacity/100},this),complete:BX.delegate(function(){BX.removeClass(this.content,"group-panel-content-"+e);BX.addClass(this.content,"group-panel-content-"+n);new BX.easing({duration:50,start:{opacity:0},finish:{opacity:1},transition:BX.easing.transitions.linear,step:BX.delegate(function(t){this.items.style.opacity=t.opacity/100},this),complete:BX.delegate(function(){this.items.style.cssText=""},this)}).animate()},this)}).animate()}t.stopPropagation()};BX.Bitrix24.GroupPanel.prototype.onItemsClick=function(t){if(!BX.hasClass(t.target,"group-panel-item-star")){return}var i=t.target;var e=i.parentNode;var n=e.dataset.id;var s=BX.hasClass(e,"group-panel-item-favorite")?"remove_from_favorites":"add_to_favorites";BX.toggleClass(e,"group-panel-item-favorite");this.animateStart(i);this.animateCounter(s==="add_to_favorites");BX.ajax({method:"POST",dataType:"json",url:this.ajaxPath,data:{sessid:BX.bitrix_sessid(),site_id:this.siteId,action:s,groupId:n}});t.preventDefault()};BX.Bitrix24.GroupPanel.prototype.animateStart=function(t){var i=t.cloneNode();i.style.marginLeft="-"+t.offsetWidth+"px";t.parentNode.appendChild(i);new BX.easing({duration:200,start:{opacity:100,scale:100},finish:{opacity:0,scale:300},transition:BX.easing.transitions.linear,step:function(t){i.style.transform="scale("+t.scale/100+")";i.style.opacity=t.opacity/100},complete:function(){i.parentNode.removeChild(i)}}).animate()};BX.Bitrix24.GroupPanel.prototype.animateCounter=function(t){this.counter.innerHTML=t===false?"-1":"+1";new BX.easing({duration:400,start:{opacity:100,top:0},finish:{opacity:0,top:-20},transition:BX.easing.transitions.linear,step:function(t){this.counter.style.top=t.top+"px";this.counter.style.opacity=t.opacity/100}.bind(this),complete:function(){this.counter.style.cssText=""}.bind(this)}).animate()};BX.Bitrix24.GroupPanel.prototype.saveFilter=function(t){if(!this.ajaxPath||!BX.type.isNotEmptyString(t)){return}BX.ajax({method:"POST",dataType:"json",url:this.ajaxPath,data:{sessid:BX.bitrix_sessid(),site_id:this.siteId,action:"set_group_filter",filter:t}})};BX.Bitrix24.Map=function(t){this.containerClassName="sitemap-window";this.overlayClassName="sitemap-window-overlay";this.startParams={translateY:-100,opacity:0};this.endParams={translateY:0,opacity:65};BX.Bitrix24.SlidingPanel.apply(this,arguments);this.menu=BX("sitemap-menu");this.content=BX("sitemap-content");this.closeLink=BX("sitemap-close-link");BX.bind(this.menu,"click",this.onMenuClick.bind(this));BX.bind(this.closeLink,"click",this.close.bind(this))};BX.Bitrix24.Map.prototype=Object.create(BX.Bitrix24.SlidingPanel.prototype);BX.Bitrix24.Map.prototype.constructor=BX.Bitrix24.Map;BX.Bitrix24.Map.prototype.super=BX.Bitrix24.SlidingPanel.prototype;BX.Bitrix24.Map.prototype.setContent=function(){this.container.appendChild(this.content)};BX.Bitrix24.Map.prototype.animateStep=function(t){this.container.style.transform="translateY("+t.translateY+"%)";this.overlay.style.backgroundColor="rgba(0, 0, 0, "+t.opacity/100+")"};BX.Bitrix24.Map.prototype.open=function(){BX.onCustomEvent("BX.Bitrix24.Map:onBeforeOpen",[this]);this.menu.classList.add("sitemap-menu-open");this.super.open.apply(this,arguments)};BX.Bitrix24.Map.prototype.close=function(){this.menu.classList.remove("sitemap-menu-open");this.super.close.apply(this,arguments)};BX.Bitrix24.Map.prototype.onMenuClick=function(t){if(this.isOpen){this.close()}else{this.open()}t.stopPropagation()};
/* End */
;
; /* Start:"a:4:{s:4:"full";s:93:"/bitrix/templates/bitrix24/components/bitrix/menu/left_vertical/script.min.js?160748813266724";s:6:"source";s:73:"/bitrix/templates/bitrix24/components/bitrix/menu/left_vertical/script.js";s:3:"min";s:77:"/bitrix/templates/bitrix24/components/bitrix/menu/left_vertical/script.min.js";s:3:"map";s:77:"/bitrix/templates/bitrix24/components/bitrix/menu/left_vertical/script.map.js";}"*/
BX.namespace("BX.Bitrix24");BX.Bitrix24.LeftMenu={init:function(e){e=typeof e==="object"?e:{};this.ajaxPath=e.ajaxPath||null;this.isAdmin=e.isAdmin==="Y";this.hiddenCounters=e.hiddenCounters||[];this.allCounters=e.allCounters||{};this.isBitrix24=e.isBitrix24==="Y";this.siteId=e.siteId||null;this.siteDir=e.siteDir||null;this.isExtranet=e.isExtranet==="Y";this.isCompositeMode=e.isCompositeMode===true;this.isCollapsedMode=e.isCollapsedMode===true;this.activeItemsId=[];this.isCurrentPageInLeftMenu=false;this.currentPagePath=null;this.menuSelectedNode=null;this.showPresetPopup=e.showPresetPopup==="Y";this.showImportConfiguration=e.showImportConfiguration==="Y";this.urlImportConfiguration=e.urlImportConfiguration||"";this.isCustomPresetAvailable=e.isCustomPresetAvailable==="Y";this.customPresetExists=e.customPresetExists==="Y";this.isCurrentPageStandard=false;this.topMenuSelectedNode=null;this.topItemSelectedObj=null;this.isPublicConverted=e.isPublicConverted==="Y";this.spotlightId=BX.type.isNotEmptyString(e.spotlightId)?e.spotlightId:null;this.lastScrollOffset=0;this.logoMaskNeeded=null;this.islogoMaskMode=false;this.isScrollMode=false;this.scrollModeThreshold=20;this.menuContainer=document.getElementById("menu-items-block");if(!this.menuContainer){return false}this.templateHeaderHeight=null;this.menuHeader=this.menuContainer.querySelector(".menu-items-header");this.menuBody=this.menuContainer.querySelector(".menu-items-body");this.menuMoreButton=this.menuContainer.querySelector(".menu-favorites-more-btn");this.menuItemsBlock=this.menuContainer.querySelector(".menu-items");this.inviteEmployeesBox=this.menuContainer.querySelector(".menu-invite-employees");this.licenseBox=this.menuContainer.querySelector(".menu-license-all-container");this.settingsBox=this.menuContainer.querySelector(".menu-settings-btn");this.settingsIconBox=this.settingsBox.querySelector(".menu-settings-icon-box");this.settingsBtnText=this.settingsBox.querySelector(".menu-settings-btn-text");this.settingsSaveBtn=this.menuContainer.querySelector(".menu-settings-save-btn");this.timeout={};this.highlight(document.location.pathname+document.location.search);this.makeTextIcons();this.handleMenuItemMouseEnter=this.handleMenuItemMouseEnter.bind(this);this.handleMenuItemMouseClick=this.handleMenuItemMouseClick.bind(this);this.templateHeaderHeight=null;this.fixedAdminPanelHeight=0;this.adminPanel=BX("bx-panel");this.adminPanelHeight=null;if(this.adminPanel){var t=BX.getClass("BX.admin.panel.state");if(t&&t.fixed){this.fixedAdminPanelHeight=this.getAdminPanelHeight()}BX.addCustomEvent("onTopPanelCollapse",this.handleAdminPanelCollapse.bind(this));BX.addCustomEvent("onTopPanelFix",this.handleAdminPanelFix.bind(this));this.adjustAdminPanel()}document.addEventListener("scroll",this.handleDocumentScroll.bind(this));this.slidingModeTimeoutId=null;BX.bind(this.menuContainer,"dblclick",this.handleMenuDoubleClick.bind(this));BX.bind(this.menuContainer,"mouseenter",this.handleMenuMouseEnter.bind(this));BX.bind(this.menuContainer,"mouseleave",this.handleMenuMouseLeave.bind(this));BX.bind(this.menuContainer,"transitionend",this.handleSlidingTransitionEnd.bind(this));this.isMenuMouseEnterBlocked=false;this.isMenuMouseLeaveBlocked=false;this.mainTable=document.querySelector(".bx-layout-table");this.header=document.querySelector("#header");this.headerBurger=this.header.querySelector(".menu-switcher");this.headerLogo=this.header.querySelector(".logo");this.headerLogoBlock=this.header.querySelector(".header-logo-block");this.headerSettings=this.header.querySelector(".header-logo-block-settings");this.menuHeaderLogo=this.menuHeader.querySelector(".logo");this.menuHeaderBurger=this.menuHeader.querySelector(".menu-switcher");this.menuHeaderTitle=this.menuHeader.querySelector(".menu-items-header-title");this.livefeedCounter={decrementStack:0,value:0};if(this.headerSettings){BX.bind(this.headerLogoBlock,"mouseenter",this.handleHeaderLogoMouserEnter.bind(this));BX.bind(this.headerLogoBlock,"mouseleave",this.handleHeaderLogoMouserLeave.bind(this));BX.bind(this.menuHeader,"mouseenter",this.handleHeaderLogoMouserEnter.bind(this));BX.bind(this.menuHeader,"mouseleave",this.handleHeaderLogoMouserLeave.bind(this))}BX.bind(this.menuHeaderBurger,"click",this.handleBurgerClick.bind(this));BX.bind(this.menuHeaderTitle,"click",this.handleBurgerClick.bind(this,true));this.siteMapItem=this.menuContainer.querySelector(".menu-sitemap-btn");if(this.siteMapItem){BX.bind(this.siteMapItem,"click",this.handleSiteMapClick.bind(this));BX.bind(this.siteMapItem,"click",this.handleMenuItemMouseClick);BX.bind(this.siteMapItem,"mouseenter",this.handleMenuItemMouseEnter)}this.upButton=this.menuContainer.querySelector(".menu-btn-arrow-up");BX.bind(this.upButton,"click",this.handleUpButtonClick.bind(this));BX.bind(this.upButton,"mouseenter",this.handleUpButtonMouseEnter.bind(this));BX.bind(this.upButton,"mouseleave",this.handleUpButtonMouseLeave.bind(this));BX.bind(BX("left-menu-hidden-separator"),"click",BX.proxy(this.showHideMoreItems,this));BX.bind(this.menuMoreButton,"click",BX.proxy(this.showHideMoreItems,this));BX.bind(this.settingsBox,"mouseenter",this.handleMenuItemMouseEnter);BX.bind(this.settingsBox,"click",this.showSettingsPopup.bind(this));BX.bind(this.settingsBox,"click",this.handleMenuItemMouseClick);BX.bind(this.settingsSaveBtn,"click",function(){this.applyEditMode()}.bind(this));if(this.inviteEmployeesBox){BX.bind(this.inviteEmployeesBox,"mouseenter",this.handleMenuItemMouseEnter)}if(this.licenseBox){BX.bind(this.licenseBox,"mouseenter",this.handleMenuItemMouseEnter)}jsDD.Enable();if(BX.type.isDomNode(this.menuItemsBlock)){var i=this.menuItemsBlock.getElementsByClassName("menu-item-block");for(var s=0;s<i.length;s++){i[s].onbxdragstart=BX.proxy(this.menuItemDragStart,this);i[s].onbxdrag=BX.proxy(this.menuItemDragMove,this);i[s].onbxdragstop=BX.proxy(this.menuItemDragStop,this);i[s].onbxdraghover=BX.proxy(this.menuItemDragHover,this);jsDD.registerDest(i[s],100);jsDD.registerObject(i[s]);BX.bind(i[s],"mouseenter",this.handleMenuItemMouseEnter);BX.bind(i[s],"click",this.handleMenuItemMouseClick)}}BX.addCustomEvent("BX.Bitrix24.GroupPanel:onOpen",this.handleGroupPanelOpen.bind(this));BX.addCustomEvent("BX.Bitrix24.GroupPanel:onClose",this.handleGroupPanelClose.bind(this));BX.addCustomEvent("BX.Main.InterfaceButtons:onFirstItemChange",BX.proxy(function(e,t){this.onTopMenuFirstItemChange(e,t)},this));BX.addCustomEvent("BX.Main.InterfaceButtons:onHideLastVisibleItem",BX.proxy(function(e){this.showMessage(e,BX.message("MENU_TOP_ITEM_LAST_HIDDEN"))},this));BX.addCustomEvent("BX.Main.InterfaceButtons:onBeforeCreateEditMenu",function(e,t,i){var s=BX.type.isDomNode(BX("bx_left_menu_"+t.DATA_ID));e.addMenuItem({text:BX.message(s?"MENU_DELETE_FROM_LEFT_MENU":"MENU_ADD_TO_LEFT_MENU"),onclick:function(i,n){var o={id:t.DATA_ID,text:BX.util.htmlspecialcharsback(t.TEXT),subLink:t.SUB_LINK,counterId:t.COUNTER_ID,counterValue:t.COUNTER};var a=document.createElement("a");a.href=t.URL;o.link=BX.util.htmlspecialcharsback(a.pathname+a.search);o.link=o.link[0]!=="/"?"/"+o.link:o.link;if(s){this.deleteStandardItem(t.DATA_ID)}else{var r="",u="";if(BX.type.isDomNode(t.NODE)){var l=t.NODE.getBoundingClientRect();r=l.left;u=l.top}this.addStandardItem(o,r,u)}BX.PopupMenu.destroy(e.id)}.bind(this)})}.bind(this));BX.addCustomEvent("BX.Main.InterfaceButtons:onBeforeResetMenu",function(e){e.push(function(){var e=new BX.Promise;BX.ajax({method:"POST",dataType:"json",url:this.ajaxPath,data:{sessid:BX.bitrix_sessid(),site_id:this.siteId,action:"clear_cache"},onsuccess:function(){e.fulfill()},onfailure:function(t){e.reject("Error: "+t)}});return e}.bind(this))}.bind(this));if(this.showPresetPopup){this.showPresetPopupFunction("global")}else{if(this.showImportConfiguration){this.showImportConfigurationSlider()}}this.menuSelectedNode=BX.findChild(this.menuContainer,{className:"menu-item-active"},true,false);if(BX.type.isDomNode(this.menuSelectedNode)){var n=this.menuSelectedNode.getAttribute("data-link")}var o=document.location.pathname;var a=document.location.pathname+document.location.search;if(n===o||n===a){this.isCurrentPageInLeftMenu=true}this.handleDocumentScroll();return true},isEditMode:function(){return BX.hasClass(this.menuContainer,"menu-favorites-editable")},isCollapsed:function(){return this.isCollapsedMode},switchToEditMode:function(){if(this.isEditMode()){return}this.toggle(true);BX.addClass(this.menuContainer,"menu-favorites-editable");var e=this.menuContainer.querySelectorAll(".menu-item-active");for(var t=0;t<e.length;t++){BX.removeClass(e[t],"menu-item-active");this.activeItemsId.push(e[t].id)}},applyEditMode:function(){if(!this.isEditMode()){return}BX.removeClass(this.menuContainer,"menu-favorites-editable");for(var e in this.activeItemsId){BX.addClass(BX(this.activeItemsId[e]),"menu-item-active")}this.activeItemsId=[]},areMoreItemsShowed:function(){return BX.hasClass(BX("left-menu-hidden-items-block"),"menu-item-favorites-more-open")?true:false},animateShowingHiddenItems:function(){var e=BX("left-menu-hidden-items-block");if(!BX.hasClass(e,"menu-item-favorites-more-open")){e.style.height="0px";e.style.opacity=0;t(true,e,e.scrollHeight)}else{t(false,e,e.offsetHeight)}function t(e,t,i){t.style.overflow="hidden";new BX.easing({duration:200,start:{opacity:e?0:100,height:e?0:i},finish:{opacity:e?100:0,height:e?i:0},transition:BX.easing.transitions.linear,step:function(e){t.style.opacity=e.opacity/100;t.style.height=e.height+"px"},complete:function(){BX.toggleClass(BX("left-menu-hidden-items-block"),"menu-item-favorites-more-open");t.style.overflow="";t.style.height=""}}).animate()}},showHideMoreItems:function(e){if(this.isEditMode())return;if(e!==false){this.animateShowingHiddenItems()}else{BX.toggleClass(BX("left-menu-hidden-items-block"),"menu-item-favorites-more-open")}BX.toggleClass(this.menuMoreButton,"menu-favorites-more-btn-open");var t=BX("menu-more-btn-text");if(t){if(t.innerHTML===BX.message("more_items_hide")){t.innerHTML=BX.message("more_items_show")}else{t.innerHTML=BX.message("more_items_hide")}}},openMenuPopup:function(e,t){var i=BX("bx_left_menu_"+t);if(!BX.type.isDomNode(i))return;var s=[];var n=i.getAttribute("data-delete-perm");var o=i.getAttribute("data-type");if(i.getAttribute("data-status")==="show"){s.push({text:BX.message("hide_item"),className:"menu-popup-no-icon",onclick:BX.proxy(function(){var e=BX.proxy_context;e.popupWindow.close();this.hideItem(t);BX.PopupMenu.destroy("popup_"+t)},this)})}if(i.getAttribute("data-status")==="hide"){s.push({text:BX.message("show_item"),className:"menu-popup-no-icon",onclick:BX.proxy(function(){var e=BX.proxy_context;e.popupWindow.close();this.showItem(t);BX.PopupMenu.destroy("popup_"+t)},this)})}if(!this.isExtranet&&o!=="self"&&BX.previousSibling(i).id!="left-menu-empty-item"&&this.isPublicConverted){s.push({text:BX.message("MENU_SET_MAIN_PAGE"),className:"menu-popup-no-icon",onclick:BX.proxy(function(){var e=BX.proxy_context;e.popupWindow.close();this.setMainPage(t);BX.PopupMenu.destroy("popup_"+t)},this)})}if(o==="self"){s.push({text:BX.message("MENU_DELETE_SELF_ITEM"),className:"menu-popup-no-icon",onclick:BX.proxy(function(){var e=BX.proxy_context;e.popupWindow.close();this.showConfirmWindow({id:"left-menu-delete-self-item",titleBar:BX.message("MENU_DELETE_SELF_ITEM"),okButtonText:BX.message("MENU_DELETE"),content:BX.message("MENU_DELETE_SELF_ITEM_CONFIRM"),onsuccess:BX.proxy(function(){this.deleteSelfItem(t);BX.PopupMenu.destroy("popup_"+t)},this),onfailure:BX.proxy(function(){BX.PopupMenu.destroy("popup_"+t)},this)})},this)});s.push({text:BX.message("MENU_EDIT_ITEM"),className:"menu-popup-no-icon",onclick:BX.proxy(function(){var s=BX.proxy_context;s.popupWindow.close();var n=BX.findChild(i,{tagName:"a"},true,false);var o={id:t,text:i.querySelector("[data-role='item-text']").innerText,link:i.getAttribute("data-link"),openInNewPage:n.getAttribute("target")=="_blank"};this.showSelfItemPopup(e,o);BX.PopupMenu.destroy("popup_"+t)},this)})}if(o==="standard"){s.push({text:BX.message("MENU_RENAME_ITEM"),className:"menu-popup-no-icon",onclick:BX.proxy(function(){var s={id:t,text:i.querySelector("[data-role='item-text']").innerText};this.showStandardEditItemPopup(e,s);BX.PopupMenu.destroy("popup_"+t)},this)});s.push({text:BX.message("MENU_REMOVE_STANDARD_ITEM"),className:"menu-popup-no-icon",onclick:BX.proxy(function(){var e=BX.proxy_context;e.popupWindow.close();this.deleteStandardItem(t);BX.PopupMenu.destroy("popup_"+t)},this)})}if(this.isAdmin){if(n==="Y"){s.push({text:BX.message("MENU_ADD_ITEM_TO_ALL"),className:"menu-popup-no-icon",onclick:BX.proxy(function(){this.addItemToAll(t);BX.PopupMenu.destroy("popup_"+t)},this)})}if(n==="A"){if(o==="custom"){s.push({text:BX.message("MENU_DELETE_CUSTOM_ITEM_FROM_ALL"),className:"menu-popup-no-icon",onclick:BX.proxy(function(){this.deleteCustomItemFromAll(t);BX.PopupMenu.destroy("popup_"+t)},this)})}else{s.push({text:BX.message("MENU_DELETE_ITEM_FROM_ALL"),className:"menu-popup-no-icon",onclick:BX.proxy(function(){this.deleteItemFromAll(t);BX.PopupMenu.destroy("popup_"+t)},this)})}}}s.push({text:this.isEditMode()?BX.message("MENU_EDIT_READY_FULL"):BX.message("MENU_SETTINGS_MODE"),className:"menu-popup-no-icon",onclick:BX.proxy(function(){BX.PopupMenu.destroy("popup_"+t);this.isEditMode()?this.applyEditMode():this.switchToEditMode()},this)});BX.PopupMenu.show({id:"popup_"+t,bindElement:e,items:s,offsetTop:0,offsetLeft:12,angle:true,events:{onPopupShow:function(){this.isMenuMouseLeaveBlocked=true;BX.addClass(i,"menu-item-block-hover")}.bind(this),onPopupClose:function(){this.isMenuMouseLeaveBlocked=false;BX.removeClass(i,"menu-item-block-hover")}.bind(this),onPopupDestroy:function(){this.isMenuMouseLeaveBlocked=false;BX.removeClass(i,"menu-item-block-hover")}.bind(this)}})},showSettingsPopup:function(e){var t="leftMenuSettingsPopup";if(BX.PopupMenu.getMenuById(t)){BX.PopupMenu.destroy(t);return}var i=this;var s="default";if(BX.type.isDomNode(this.menuSelectedNode)){s=this.menuSelectedNode.getAttribute("data-type")}if(this.isCurrentPageInLeftMenu&&s==="default"){n={text:BX.message(this.isCurrentPageInLeftMenu?"MENU_DELETE_PAGE_FROM_LEFT_MENU":"MENU_ADD_PAGE_TO_LEFT_MENU"),className:"menu-popup-no-icon menu-popup-disable-text"}}else{var n={text:BX.message(this.isCurrentPageInLeftMenu?"MENU_DELETE_PAGE_FROM_LEFT_MENU":"MENU_ADD_PAGE_TO_LEFT_MENU"),className:"menu-popup-no-icon",onclick:BX.proxy(function(){BX.proxy_context.popupWindow.close();if(this.isCurrentPageInLeftMenu){this.deleteStandardItem()}else{this.addStandardItem()}},this)}}var o=[{text:BX.message("SORT_ITEMS"),className:"menu-popup-no-icon",onclick:function(){this.popupWindow.close();i.switchToEditMode()}},{text:this.isCollapsedMode?BX.message("MENU_EXPAND"):BX.message("MENU_COLLAPSE"),className:"menu-popup-no-icon",onclick:BX.proxy(function(){BX.proxy_context.popupWindow.close();this.toggle()},this)},n,{text:BX.message("MENU_ADD_SELF_PAGE"),className:"menu-popup-no-icon",onclick:BX.proxy(function(){BX.proxy_context.popupWindow.close();this.showSelfItemPopup(this.settingsBox)},this)}];if(!this.isExtranet){o.push({text:BX.message("MENU_SET_DEFAULT2"),className:"menu-popup-no-icon",onclick:function(){this.popupWindow.close();i.setDefaultMenu(true)}})}o.push({text:BX.message("MENU_SET_DEFAULT"),className:"menu-popup-no-icon",onclick:function(){this.popupWindow.close();i.setDefaultMenu()}});if(this.isCustomPresetAvailable&&this.isAdmin){o.push({text:BX.message("MENU_SAVE_CUSTOM_PRESET"),className:"menu-popup-no-icon",onclick:function(){this.popupWindow.close();i.showCustomPresetPopup()}})}BX.PopupMenu.show(t,this.settingsBox,o,{offsetTop:0,offsetLeft:50,angle:true,events:{onPopupShow:function(){this.isMenuMouseLeaveBlocked=true}.bind(this),onPopupClose:function(){this.isMenuMouseLeaveBlocked=false;this.switchToSlidingMode(false);BX.PopupMenu.destroy(t)}.bind(this),onPopupDestroy:function(){this.isMenuMouseLeaveBlocked=false;this.switchToSlidingMode(false)}.bind(this)}})},showMessage:function(e,t,i){var s=BX.PopupWindowManager.create("left-menu-message",e,{content:'<div class="left-menu-message-popup">'+t+"</div>",darkMode:true,offsetTop:i==="right"?-45:2,offsetLeft:i==="right"?215:0,angle:i==="right"?{position:"left"}:true,events:{onPopupClose:function(){if(s){s.destroy();s=null}}},autoHide:true});s.show();setTimeout(function(){if(s){s.destroy();s=null}},3e3)},showError:function(e){this.showMessage(e,BX.message("edit_error"))},onTopMenuFirstItemChange:function(e,t){if(!e)return;var i=t.getAttribute("data-top-menu-id");var s=this.menuItemsBlock.querySelector("[data-top-menu-id='"+i+"']");if(BX.type.isDomNode(s)){s.setAttribute("data-link",e);var n=BX.findChild(s,{tagName:"a",className:"menu-item-link"},true,false);if(n){n.setAttribute("href",e)}}if(BX.type.isDomNode(t)){this.showMessage(t,BX.message("MENU_ITEM_MAIN_SECTION_PAGE"))}if(BX.type.isDomNode(s)&&BX.previousSibling(s)===BX("left-menu-empty-item")){var o={sessid:BX.bitrix_sessid(),site_id:this.siteId,action:"set_first_page",firstPageUrl:e}}else{o={sessid:BX.bitrix_sessid(),site_id:this.siteId,action:"clear_cache"}}BX.ajax({method:"POST",dataType:"json",url:this.ajaxPath,data:o,onsuccess:BX.proxy(function(){},this),onfailure:function(){}})},showSelfItemPopup:function(e,t){var i=false;if(typeof t==="object"&&t){i=true}var s=BX.create("form",{attrs:{name:"menuAddToFavoriteForm"},children:[BX.create("label",{attrs:{for:"menuPageToFavoriteName",className:"menu-form-label"},html:BX.message("MENU_ITEM_NAME")}),BX.create("input",{attrs:{value:i?t.text:"",name:"menuPageToFavoriteName",type:"text",className:"menu-form-input"}}),BX.create("br"),BX.create("br"),BX.create("label",{attrs:{for:"menuPageToFavoriteLink",className:"menu-form-label"},html:BX.message("MENU_ITEM_LINK")}),BX.create("input",{attrs:{value:i?t.link:"",name:"menuPageToFavoriteLink",type:"text",className:"menu-form-input"}}),BX.create("br"),BX.create("br"),BX.create("input",{attrs:{value:"",name:"menuOpenInNewPage",type:"checkbox",checked:!i||t.openInNewPage?"checked":"",id:"menuOpenInNewPage"}}),BX.create("label",{attrs:{for:"menuOpenInNewPage",className:"menu-form-label"},html:BX.message("MENU_OPEN_IN_NEW_PAGE")})]});if(i){s.appendChild(BX.create("input",{attrs:{name:"menuItemId",type:"hidden",value:t.id}}))}BX.PopupWindowManager.create("menu-self-item-popup",e,{closeIcon:true,offsetTop:1,offsetLeft:20,lightShadow:true,draggable:{restrict:true},closeByEsc:true,titleBar:i?BX.message("MENU_EDIT_SELF_PAGE"):BX.message("MENU_ADD_SELF_PAGE"),content:s,buttons:[button=new BX.PopupWindowButton({text:i?BX.message("MENU_SAVE_BUTTON"):BX.message("MENU_ADD_BUTTON"),className:"popup-window-button-create",events:{click:BX.proxy(function(){var e=document.forms["menuAddToFavoriteForm"];var s=e.elements["menuPageToFavoriteName"];var n=e.elements["menuPageToFavoriteLink"];var o=e.elements["menuOpenInNewPage"].checked;var a=BX.util.trim(s.value);var r=this.refineUrl(n.value);if(!a||!r){if(!r){BX.addClass(n,"menu-form-input-error");n.focus()}if(!a){BX.addClass(s,"menu-form-input-error");s.focus()}}else{BX.addClass(button.buttonNode,"popup-window-button-wait");BX.removeClass(s,"menu-form-input-error");BX.removeClass(n,"menu-form-input-error");var u={text:a,link:r,openInNewPage:o?"Y":"N"};if(i){u.id=t.id}this.saveSelfItem(i?"edit":"add",u,this.onSelfItemSave.bind(this))}},this)}}),new BX.PopupWindowButtonLink({text:BX.message("MENU_CANCEL"),className:"popup-window-button-link-cancel",events:{click:function(){this.popupWindow.close()}}})],events:{onPopupClose:function(){BX.PopupWindowManager.getCurrentPopup().destroy()},onPopupShow:function(){var e=document.forms["menuAddToFavoriteForm"];var t=e.elements["menuPageToFavoriteName"];t&&setTimeout(function(){t.focus()},100)}}}).show()},onSelfItemSave:function(e){if(e){this.showConfirmWindow({alertMode:true,titleBar:BX.message("MENU_ERROR_OCCURRED"),content:e})}else{BX.PopupWindowManager.getCurrentPopup().destroy()}},saveSelfItem:function(e,t,i){BX.ajax({method:"POST",dataType:"json",url:this.ajaxPath+(e==="edit"?"":"?analyticsLabel=selfItem"),data:{sessid:BX.bitrix_sessid(),site_id:this.siteId,itemData:t,action:e==="edit"?"update_self_item":"add_self_item"},onsuccess:BX.proxy(function(s){if(s.hasOwnProperty("error")){i(s.error)}else{var n={text:t.text,link:t.link,type:"self",openInNewPage:t.openInNewPage==="Y"?"Y":"N"};if(e==="add"&&s.hasOwnProperty("itemId")){n.id=s.itemId;this.generateItemHtml(n)}else if(e==="edit"){n.id=t.id;this.updateItemHtml(n)}i("")}},this),onfailure:function(){i()}})},deleteSelfItem:function(e){var t=BX("bx_left_menu_"+e);if(!BX.type.isDomNode(t))return;if(t.getAttribute("data-delete-perm")==="A"){this.deleteItemFromAll(e)}BX.ajax({method:"POST",dataType:"json",url:this.ajaxPath,data:{sessid:BX.bitrix_sessid(),site_id:this.siteId,action:"delete_self_item",menu_item_id:e},onsuccess:BX.proxy(function(e){if(e.error){this.showError(t)}else{BX.remove(t)}},this),onfailure:function(){}})},refineUrl:function(e){e=BX.util.trim(e);if(!BX.type.isNotEmptyString(e)){return""}if(!e.match(/^https?:\/\//i)&&!e.match(/^\//i)){e="http://"+e}else{var t=document.createElement("a");t.href=e;if(document.location.host===t.host){e=t.pathname+t.search+t.hash}}return e},addItemToAll:function(e){var t=BX("bx_left_menu_"+e);if(!BX.type.isDomNode(t))return;var i=t.getAttribute("data-link"),s=t.querySelector("[data-role='item-text']"),n=s.innerText,o=t.getAttribute("data-counter-id"),a=BX.findChild(t,{tagName:"a"},true,false),r=BX.type.isDomNode(a)&&a.hasAttribute("target")&&a.getAttribute("target")==="_blank";BX.ajax({method:"POST",dataType:"json",url:this.ajaxPath,data:{sessid:BX.bitrix_sessid(),site_id:this.siteId,action:"add_item_to_all",itemInfo:{id:e,link:i,text:n,counterId:o,openInNewPage:r?"Y":"N"}},onsuccess:BX.proxy(function(e){if(e.error){this.showError(t)}else{t.setAttribute("data-delete-perm","A");this.showMessage(t,BX.message("MENU_ITEM_WAS_ADDED_TO_ALL"))}},this)})},deleteItemFromAll:function(e){var t=BX("bx_left_menu_"+e);if(!BX.type.isDomNode(t))return;BX.ajax({method:"POST",dataType:"json",url:this.ajaxPath,data:{sessid:BX.bitrix_sessid(),site_id:this.siteId,action:"delete_item_from_all",menu_item_id:e},onsuccess:BX.proxy(function(e){if(e.error){this.showError(t)}else{t.setAttribute("data-delete-perm","Y");this.showMessage(t,BX.message("MENU_ITEM_WAS_DELETED_FROM_ALL"))}},this)})},deleteCustomItemFromAll:function(e){var t=BX("bx_left_menu_"+e);if(!BX.type.isDomNode(t))return;var i=t.getAttribute("data-type");if(i!=="custom")return;BX.ajax({method:"POST",dataType:"json",url:this.ajaxPath,data:{sessid:BX.bitrix_sessid(),site_id:this.siteId,action:"delete_custom_item_from_all",menu_item_id:e},onsuccess:BX.proxy(function(e){if(e.error){this.showError(t)}else{BX.remove(t)}},this)})},updateCounters:function(e,t){t=t!==false;var i=0;for(var s in e){if(!e.hasOwnProperty(s)){continue}this.allCounters[s]=e[s];var n=BX(s==="**"?"menu-counter-live-feed":"menu-counter-"+s.toLowerCase(),true);if(n){if(s==="**"){this.livefeedCounter.value=e[s];if(e[s]<=0){this.livefeedCounter.decrementStack=0}i=this.livefeedCounter.value-this.livefeedCounter.decrementStack}else{i=e[s]}if(i>0){n.innerHTML=i>99?"99+":i;BX.addClass(n.parentNode.parentNode.parentNode,"menu-item-with-index")}else{BX.removeClass(n.parentNode.parentNode.parentNode,"menu-item-with-index");if(i<0){var o=BX("menu-counter-warning-"+s.toLowerCase());if(o){o.style.display="inline-block"}}}if(t){BX.localStorage.set("lmc-"+s,e[s],5)}}}var a=0;for(var r=0,u=this.hiddenCounters.length;r<u;r++){if(this.allCounters[this.hiddenCounters[r]]){a+=+this.allCounters[this.hiddenCounters[r]]}}if(BX.type.isDomNode(BX("menu-hidden-counter"))){if(a>0){BX.removeClass(BX("menu-hidden-counter"),"menu-hidden-counter")}else{BX.addClass(BX("menu-hidden-counter"),"menu-hidden-counter")}BX("menu-hidden-counter").innerHTML=a>99?"99+":a}},decrementCounter:function(e,t){if(!e)return;t=parseInt(t);if(e.id=="menu-counter-live-feed"){this.livefeedCounter.decrementStack+=t;var i=this.livefeedCounter.value-this.livefeedCounter.decrementStack;if(i>0){e.innerHTML=i}else{BX.removeClass(e.parentNode.parentNode.parentNode,"menu-item-with-index")}}},recountHiddenCounters:function(){var e=0;var t=BX.findChildren(BX("left-menu-hidden-items-list"),{className:"menu-item-block"},true);this.hiddenCounters=[];if(t){for(var i=0,s=t.length;i<s;i++){var n=t[i].getAttribute("data-counter-id");this.hiddenCounters.push(n);if(this.allCounters[n]){e+=Number(this.allCounters[n])}}}if(e>0){BX.removeClass(BX("menu-hidden-counter"),"menu-hidden-counter")}else{BX.addClass(BX("menu-hidden-counter"),"menu-hidden-counter")}BX("menu-hidden-counter").innerHTML=e>99?"99+":e},checkMoreButton:function(e){var t=this.menuMoreButton;if(e===true||e===false){if(e){BX.removeClass(t,"menu-favorites-more-btn-hidden")}else{BX.addClass(t,"menu-favorites-more-btn-hidden")}return e}var i=BX("left-menu-hidden-items-list").getElementsByClassName("menu-item-block");if(i.length>0){BX.removeClass(t,"menu-favorites-more-btn-hidden");return true}else{BX.addClass(t,"menu-favorites-more-btn-hidden");return false}},hideItem:function(e){var t=BX("bx_left_menu_"+e);if(!BX.type.isDomNode(t))return;t.setAttribute("data-status","hide");BX("left-menu-hidden-items-list").appendChild(t);this.checkMoreButton(true);if(t.getAttribute("data-counter-id")){this.recountHiddenCounters()}this.saveItemsSort("?analyticsLabel=hideItem&itemId="+e)},showItem:function(e){var t=BX("bx_left_menu_"+e);if(!BX.type.isDomNode(t))return;if(BX.type.isDomNode(this.menuItemsBlock)){t.setAttribute("data-status","show");this.menuItemsBlock.insertBefore(t,BX("left-menu-hidden-items-block"))}this.checkMoreButton();if(t.getAttribute("data-counter-id")){this.recountHiddenCounters()}this.saveItemsSort("?analyticsLabel=showItem&itemId="+e)},saveItemsSort:function(e){var t=[],i=[],s="";var n=BX.findChildren(this.menuContainer,{className:"menu-item-block"},true);for(var o=0;o<n.length;o++){if(o===0){s=n[o].getAttribute("data-link")}if(n[o].getAttribute("data-status")==="show"){t.push(n[o].getAttribute("data-id"))}else if(n[o].getAttribute("data-status")==="hide"){i.push(n[o].getAttribute("data-id"))}}var a={show:t,hide:i};BX.ajax({method:"POST",dataType:"json",url:this.ajaxPath+(e?e:""),data:{sessid:BX.bitrix_sessid(),site_id:this.siteId,action:"save_items_sort",items:a,firstItemLink:s},onsuccess:function(e){},onfailure:function(){}})},showItemWarning:function(e){e=BX.type.isPlainObject(e)?e:{};var t=BX.type.isNotEmptyString(e.itemId)?e.itemId:"";var i=BX("bx_left_menu_"+t);if(!BX.type.isDomNode(i)){return}this.removeItemWarning(t);var s=BX.create("a",{props:{className:"menu-post-warn-icon"},attrs:{title:BX.type.isNotEmptyString(e.title)?e.title:""},events:BX.type.isNotEmptyObject(e.events)?e.events:{}});var n=i.querySelector(".menu-item-link");if(n){BX.addClass(i,"menu-item-warning-state");n.appendChild(s)}},removeItemWarning:function(e){var t=BX("bx_left_menu_"+e);if(!BX.type.isDomNode(t)){return}var i=t.querySelector(".menu-post-warn-icon");if(i){BX.remove(i)}BX.removeClass(t,"menu-item-warning-state")},animateTopItemToLeft:function(e,t,i){if(typeof e!=="object")return;var s=BX.create("div",{text:e.text,attrs:{style:"position: absolute; z-index: 1000;"}});s.style.top=i+25+"px";document.body.appendChild(s);var n=this.menuItemsBlock.getBoundingClientRect().bottom;if(this.areMoreItemsShowed()){n-=BX("left-menu-hidden-items-list").offsetHeight}new BX.easing({duration:500,start:{left:t},finish:{left:30},transition:BX.easing.makeEaseOut(BX.easing.transitions.quart),step:function(e){s.style.left=e.left+"px"},complete:BX.proxy(function(){new BX.easing({duration:500,start:{top:i+25},finish:{top:n},transition:BX.easing.makeEaseOut(BX.easing.transitions.quart),step:function(e){s.style.top=e.top+"px"},complete:BX.proxy(function(){BX.remove(s);e.type="standard";this.isCurrentPageInLeftMenu=true;this.generateItemHtml(e);this.saveItemsSort()},this)}).animate()},this)}).animate()},addStandardItem:function(e,t,i){if(typeof e!=="object"){this.checkCurrentPageInTopMenu();if(this.isCurrentPageStandard&&BX.type.isDomNode(this.topMenuSelectedNode)){var s=this.topMenuSelectedNode.getBoundingClientRect();t=s.left;i=s.top;e={id:this.topItemSelectedObj.DATA_ID,text:this.topItemSelectedObj.TEXT,link:BX.type.isNotEmptyString(this.currentPagePath)?this.currentPagePath:this.topItemSelectedObj.URL,counterId:this.topItemSelectedObj.COUNTER_ID,counterValue:this.topItemSelectedObj.COUNTER,isStandardItem:true,subLink:this.topItemSelectedObj.SUB_LINK}}else{e={text:BX("pagetitle").innerText,link:BX.type.isNotEmptyString(this.currentPagePath)?this.currentPagePath:document.location.pathname+document.location.search,isStandardItem:false}}}if(!t||!i){var n=BX("pagetitle").getBoundingClientRect();t=n.left;i=n.top}var o=this.ajaxPath;o=BX.util.add_url_param(o,{b24statAction:"addFavMenuItem"});BX.ajax({method:"POST",dataType:"json",url:o,data:{sessid:BX.bitrix_sessid(),site_id:this.siteId,itemData:e,action:"add_standard_item"},onsuccess:BX.proxy(function(s){if(s.hasOwnProperty("error")){this.showConfirmWindow({alertMode:true,titleBar:BX.message("MENU_ERROR_OCCURRED"),content:s.error})}else{if(s.hasOwnProperty("itemId")){e.id=s.itemId;BX.onCustomEvent("BX.Bitrix24.LeftMenuClass:onMenuItemAdded",[e,this]);this.animateTopItemToLeft(e,t,i);this.showMessage(BX("uiToolbarStar"),BX.message("MENU_ITEM_WAS_ADDED_TO_LEFT"));BX("uiToolbarStar").title=BX.message("MENU_DELETE_PAGE_FROM_LEFT_MENU");BX.addClass(BX("uiToolbarStar"),"ui-toolbar-star-active");this.isCurrentPageInLeftMenu=true}}},this),onfailure:function(){}})},deleteStandardItem:function(e){var t={};this.checkCurrentPageInTopMenu();if(e&&BX.type.isDomNode(BX("bx_left_menu_"+e))){t={id:e}}else if(this.isCurrentPageStandard&&this.topItemSelectedObj.DATA_ID){t={id:this.topItemSelectedObj.DATA_ID}}else{t={link:document.location.pathname+document.location.search}}var i=this.ajaxPath;i=BX.util.add_url_param(i,{b24statAction:"removeFavMenuItem"});BX.ajax({method:"POST",dataType:"json",url:i,data:{sessid:BX.bitrix_sessid(),site_id:this.siteId,itemData:t,action:"delete_standard_item"},onsuccess:BX.proxy(function(e){if(e.hasOwnProperty("error")){this.showConfirmWindow({alertMode:true,titleBar:BX.message("MENU_ERROR_OCCURRED"),content:e.error})}else{if(e.hasOwnProperty("itemId")){BX.onCustomEvent("BX.Bitrix24.LeftMenuClass:onMenuItemDeleted",[e,this]);var t=BX("bx_left_menu_"+e.itemId);if(!BX.type.isDomNode(t))return;if(t.getAttribute("data-delete-perm")==="A"){this.deleteItemFromAll(e.itemId)}this.showMessage(BX("uiToolbarStar"),BX.message("MENU_ITEM_WAS_DELETED_FROM_LEFT"));BX("uiToolbarStar").title=BX.message("MENU_ADD_PAGE_TO_LEFT_MENU");this.animateTopItemFromLeft("bx_left_menu_"+e.itemId);BX.removeClass(BX("uiToolbarStar"),"ui-toolbar-star-active");this.isCurrentPageInLeftMenu=false}}},this),onfailure:function(){}})},updateStandardItem:function(e){BX.ajax({method:"POST",dataType:"json",url:this.ajaxPath,data:{sessid:BX.bitrix_sessid(),site_id:this.siteId,itemText:e.text,itemId:e.id,action:"update_standard_item"},onsuccess:BX.proxy(function(t){if(t.hasOwnProperty("error")){this.showConfirmWindow({alertMode:true,titleBar:BX.message("MENU_ERROR_OCCURRED"),content:t.error})}else{this.updateItemHtml(e);BX.PopupWindowManager.getCurrentPopup().destroy()}},this),onfailure:function(){}})},showStandardEditItemPopup:function(e,t){var i=false;if(typeof t==="object"&&t){i=true}var s=BX.create("form",{attrs:{name:"menuAddToFavoriteForm"},children:[BX.create("label",{attrs:{for:"menuPageToFavoriteName",className:"menu-form-label"},html:BX.message("MENU_ITEM_NAME")}),BX.create("input",{attrs:{value:i?t.text:"",name:"menuPageToFavoriteName",type:"text",className:"menu-form-input"}}),BX.create("input",{attrs:{name:"menuItemId",type:"hidden",value:t.id}})]});BX.PopupWindowManager.create("menu-standard-item-popup-edit",e,{closeIcon:true,offsetTop:1,lightShadow:true,draggable:{restrict:true},closeByEsc:true,titleBar:BX.message("MENU_RENAME_ITEM"),content:s,buttons:[new BX.PopupWindowButton({text:BX.message("MENU_SAVE_BUTTON"),className:"popup-window-button-create",events:{click:BX.proxy(function(){var e=document.forms["menuAddToFavoriteForm"];var i=e.elements["menuPageToFavoriteName"];var s=BX.util.trim(i.value);if(!s){BX.addClass(i,"menu-form-input-error");i.focus()}else{BX.removeClass(i,"menu-form-input-error");var n={text:s,id:t.id};this.updateStandardItem(n)}},this)}}),new BX.PopupWindowButtonLink({text:BX.message("MENU_CANCEL"),className:"popup-window-button-link-cancel",events:{click:function(){BX.PopupWindowManager.getCurrentPopup().destroy()}}})],events:{onPopupClose:function(){BX.PopupWindowManager.getCurrentPopup().destroy()}}}).show()},animateTopItemFromLeft:function(e){if(!BX.type.isDomNode(BX(e)))return;new BX.easing({duration:700,start:{left:BX(e).offsetLeft,opacity:1},finish:{left:400,opacity:0},transition:BX.easing.makeEaseOut(BX.easing.transitions.quart),step:function(t){BX(e).style.paddingLeft=t.left+"px";BX(e).style.opacity=t.opacity},complete:BX.proxy(function(){BX.remove(BX(e));this.isCurrentPageInLeftMenu=false;this.saveItemsSort()},this)}).animate()},generateItemHtml:function(e){if(!(typeof e=="object"&&e))return;var t=[BX.create("span",{attrs:{className:"menu-item-icon-box"},children:[BX.create("span",{attrs:{className:"menu-item-icon"},text:this.getShortName(e.text)})]}),BX.create("span",{text:e.text,attrs:{className:"menu-item-link-text","data-role":"item-text"}})];var i=BX.type.isNotEmptyString(e.counterId);if(i){t.push(BX.create("span",{attrs:{className:"menu-item-index-wrap"},children:[BX.create("span",{attrs:{className:"menu-item-index",id:"menu-counter-"+e.counterId},html:e.counterValue})]}))}var s=this;var n=BX.create("li",{attrs:{className:"menu-item-block menu-item-no-icon-state "+(i&&e.counterValue?" menu-item-with-index":""),id:"bx_left_menu_"+e.id,"data-type":e.type==="standard"?"standard":"self","data-delete-perm":"Y","data-id":e.id,"data-link":e.link,"data-status":"show","data-new-page":e.openInNewPage==="Y"?"Y":"N"},children:[BX.create("span",{attrs:{className:"menu-fav-editable-btn menu-favorites-btn"},children:[BX.create("span",{attrs:{className:"menu-favorites-btn-icon"}})],events:{click:BX.proxy(function(){this.openMenuPopup(BX.proxy_context,e.id)},this)}}),BX.create("span",{attrs:{className:"menu-favorites-btn menu-favorites-draggable"},children:[BX.create("span",{attrs:{className:"menu-fav-draggable-icon"}})],events:{onmousedown:function(){BX.addClass(this.parentNode,"menu-item-draggable")},onmouseup:function(){BX.removeClass(this.parentNode,"menu-item-draggable")}}}),BX.create("a",{attrs:{href:e.link,className:"menu-item-link",target:e.openInNewPage==="Y"?"_blank":""},children:t})]});if(BX.type.isDomNode(this.menuItemsBlock)){this.menuItemsBlock.insertBefore(n,BX("left-menu-hidden-items-block"))}n.onbxdragstart=BX.proxy(this.menuItemDragStart,this);n.onbxdrag=BX.proxy(this.menuItemDragMove,this);n.onbxdragstop=BX.proxy(this.menuItemDragStop,this);n.onbxdraghover=BX.proxy(this.menuItemDragHover,this);jsDD.registerDest(n,100);jsDD.registerObject(n)},updateItemHtml:function(e){if(!(typeof e=="object"&&e))return;var t=BX("bx_left_menu_"+e.id);if(!BX.type.isDomNode(t))return;if(e.link){t.setAttribute("data-link",e.link);var i=BX.findChild(t,{tagName:"a"},true,false);if(BX.type.isDomNode(i)){i.setAttribute("href",e.link);if(e.hasOwnProperty("openInNewPage")){i.setAttribute("target",e.openInNewPage=="Y"?"_blank":"");t.setAttribute("data-new-page",e.openInNewPage=="Y"?"Y":"N")}}}if(e.text){var s=t.querySelector("[data-role='item-text']");if(BX.type.isDomNode(s)){s.textContent=e.text;var n=t.querySelector(".menu-item-icon");if(n){n.textContent=this.getShortName(s.textContent)}}}},setMainPage:function(e){var t=BX("bx_left_menu_"+e);if(!BX.type.isDomNode(t))return;if(BX.type.isDomNode(this.menuItemsBlock)){if(t.getAttribute("data-status")=="hide"){t.setAttribute("data-status","show")}var i=t.offsetTop;var s=BX.create("div",{attrs:{className:"menu-draggable-wrap"},style:{top:i}});var n=t.nextElementSibling;if(n){t.parentNode.insertBefore(s,n)}else{t.parentNode.appendChild(s)}s.appendChild(t);BX.addClass(t,"menu-item-draggable");new BX.easing({duration:500,start:{top:i},finish:{top:0},transition:BX.easing.makeEaseOut(BX.easing.transitions.quart),step:function(e){s.style.top=e.top+"px"},complete:BX.proxy(function(){this.menuItemsBlock.insertBefore(t,BX("left-menu-empty-item").nextSibling);BX.removeClass(t,"menu-item-draggable");BX.remove(s);this.saveItemsSort("?analyticsLabel=mainPage&itemId="+e)},this)}).animate()}},showConfirmWindow:function(e){e=e||{};var t=BX.type.isNotEmptyString(e.id)?e.id:BX.util.getRandomString();var i=BX.PopupWindowManager.create(t,null,{content:'<div class="left-menu-confirm-popup">'+(BX.type.isNotEmptyString(e.content)?e.content:"")+"</div>",titleBar:BX.type.isNotEmptyString(e.titleBar)?e.titleBar:false,closeByEsc:true,closeIcon:true,draggable:true,buttons:[new BX.PopupWindowButton({text:BX.type.isNotEmptyString(e.okButtonText)?e.okButtonText:"OK",className:"popup-window-button-create",events:{click:function(){if(BX.type.isFunction(e.onsuccess)){e.onsuccess()}this.popupWindow.destroy()}}}),e.alertMode!==true?new BX.PopupWindowButtonLink({text:BX.type.isNotEmptyString(e.cancelButtonText)?e.cancelButtonText:BX.message("MENU_CANCEL"),className:"popup-window-button-link-cancel",events:{click:function(){if(BX.type.isFunction(e.onfailure)){e.onfailure()}this.popupWindow.destroy()}}}):null]});i.show()},setDefaultMenu:function(e){if(this.isExtranet||e!==true){if(!confirm(BX.message("MENU_SET_DEFAULT_CONFIRM")))return;BX.ajax({method:"POST",dataType:"json",url:this.ajaxPath+"?analyticsLabel=defaultMenu",data:{sessid:BX.bitrix_sessid(),site_id:this.siteId,action:"set_default_menu"},onsuccess:BX.proxy(function(){document.location.reload()},this),onfailure:function(){}})}else{this.showPresetPopupFunction("personal")}},menuItemDragStart:function(){BX.onCustomEvent("BX.Bitrix24.LeftMenuClass:onDragStart");var e=BX.proxy_context;if(BX.type.isDomNode(this.menuItemsBlock)){var t=this.menuItemsBlock.getElementsByClassName("menu-item-block");for(var i=0;i<t.length;i++){jsDD.registerDest(t[i],100);jsDD.registerObject(t[i])}}if(e.getAttribute("data-type")==="self"){jsDD.unregisterDest(BX("left-menu-empty-item"))}else{jsDD.registerDest(BX("left-menu-empty-item"),100)}jsDD.registerDest(BX("left-menu-hidden-empty-item"),100);jsDD.registerDest(BX("left-menu-hidden-separator"),100);if(!this.isEditMode()){this.areMoreItemsShowedState=this.areMoreItemsShowed();if(!this.areMoreItemsShowedState){this.showHideMoreItems()}}this.itemHeight=e.offsetHeight;BX.addClass(e,"menu-item-draggable");BX.addClass(this.menuContainer,"menu-drag-mode");this.itemDomBlank=e.parentNode.insertBefore(BX.create("div",{style:{height:"0px"}}),e);this.itemMoveBlank=BX.create("div",{style:{height:this.itemHeight+"px"}});this.draggableBlock=BX.create("div",{attrs:{className:"menu-draggable-wrap"},children:[e]});this.menuItemsBlockCoord=BX.pos(this.menuItemsBlock);this.menuItemsBlock.style.position="relative";this.menuItemsBlock.appendChild(this.draggableBlock)},menuItemDragMove:function(e,t){t-=this.menuItemsBlockCoord.top;var i=this.menuItemsBlock.offsetHeight;if(t<0)t=0;if(t>i-this.itemHeight)t=i-this.itemHeight;this.draggableBlock.style.top=t-5+"px"},menuItemDragHover:function(e,t,i){var s=BX.proxy_context;if(e==s){this.itemDomBlank.parentNode.insertBefore(this.itemMoveBlank,this.itemDomBlank)}else if(s.getAttribute("data-type")=="self"&&e.id==="left-menu-empty-item"){return}else{if(BX.findParent(e,{className:"menu-items"})){if(BX.nextSibling(e))e.parentNode.insertBefore(this.itemMoveBlank,BX.nextSibling(e));else e.parentNode.appendChild(this.itemMoveBlank)}}},menuItemDragStop:function(){var e=BX.proxy_context;BX.removeClass(this.menuContainer,"menu-drag-mode");BX.removeClass(e,"menu-item-draggable");var t=BX.findChild(this.menuContainer,{className:"menu-item-block"},true,false);if(BX.type.isDomNode(t)&&t.getAttribute("data-type")=="self"){this.showMessage(t,BX.message("MENU_SELF_ITEM_FIRST_ERROR"),"right");this.menuItemsBlock.replaceChild(e,this.itemDomBlank)}else if(this.itemMoveBlank&&BX.findParent(this.itemMoveBlank,{className:"menu-items"})){this.itemMoveBlank.parentNode.replaceChild(e,this.itemMoveBlank);if(e.parentNode.id=="left-menu-hidden-items-list"){if(e.getAttribute("data-status")=="show"&&e.getAttribute("data-counter-id")){this.recountHiddenCounters()}e.setAttribute("data-status","hide")}else{if(e.getAttribute("data-status")=="hide"&&e.getAttribute("data-counter-id")){this.recountHiddenCounters()}e.setAttribute("data-status","show")}var i="?analyticsLabel=sortItem";var s=BX.previousSibling(e);if(BX.type.isDomNode(s)&&s.id=="left-menu-empty-item"&&!this.isExtranet){this.showMessage(e,BX.message("MENU_ITEM_MAIN_PAGE"),"right");i="?analyticsLabel=mainPage&itemId="+e.getAttribute("data-id")}this.checkMoreButton();this.saveItemsSort(i)}else{this.menuItemsBlock.replaceChild(e,this.itemDomBlank)}BX.remove(this.draggableBlock);BX.remove(this.itemDomBlank);BX.remove(this.itemMoveBlank);jsDD.enableDest(e);this.menuItemsBlock.style.position="static";if(!this.isEditMode()&&!this.areMoreItemsShowedState){this.showHideMoreItems()}this.draggableBlock=null;this.menuItemsBlockCoord=null;this.itemDomBlank=null;this.itemMoveBlank=null;this.areMoreItemsShowedState=null;jsDD.refreshDestArea()},clearCompositeCache:function(){BX.ajax.post(this.ajaxPath,{sessid:BX.bitrix_sessid(),action:"clear"},function(e){})},adjustAdminPanel:function(){if(!this.adminPanel){return}var e=this.adminPanel.getBoundingClientRect();if(e.bottom>0){this.menuContainer.style.top=Math.max(e.bottom,this.fixedAdminPanelHeight)+"px"}else{this.menuContainer.style.top=Math.max(0,this.fixedAdminPanelHeight)+"px"}},adjustSpotlight:function(e){if(this.spotlightId===null){return}var t=BX.getClass("BX.SpotLight.Manager");if(t===null){return}var i=t.get(this.spotlightId);if(i){if(e){i.show()}else{i.close()}}},handleSpotlightMouseEnter:function(){BX.Bitrix24.LeftMenu.spotlightId=null},handleBurgerClick:function(e){this.menuHeaderBurger.classList.add("menu-switcher-hover");this.toggle(e,function(){this.isMenuMouseEnterBlocked=true;setTimeout(function(){this.menuHeaderBurger.classList.remove("menu-switcher-hover");this.isMenuMouseEnterBlocked=false}.bind(this),100)}.bind(this))},handleMenuMouseEnter:function(e){if(!this.isCollapsed()){return}if(!this.isMenuMouseEnterBlocked){if(this.slidingModeTimeoutId){clearTimeout(this.slidingModeTimeoutId)}this.slidingModeTimeoutId=setTimeout(function(){this.slidingModeTimeoutId=null;this.switchToSlidingMode(true)}.bind(this),400)}},handleMenuMouseLeave:function(e){clearTimeout(this.slidingModeTimeoutId);this.slidingModeTimeoutId=null;if(!this.isMenuMouseLeaveBlocked){this.switchToSlidingMode(false)}},handleMenuDoubleClick:function(e){if(e.target===this.menuBody){this.toggle()}},handleHeaderLogoMouserEnter:function(e){BX.addClass(this.headerSettings,"header-logo-block-settings-show")},handleHeaderLogoMouserLeave:function(e){if(!this.headerSettings.hasAttribute("data-rename-portal")){BX.removeClass(this.headerSettings,"header-logo-block-settings-show")}},handleAdminPanelCollapse:function(e){this.adminPanelHeight=null;if(BX.admin.panel.isFixed()){this.fixedAdminPanelHeight=this.getAdminPanelHeight()}this.adjustAdminPanel()},handleAdminPanelFix:function(e){if(e){this.fixedAdminPanelHeight=this.getAdminPanelHeight()}else{this.fixedAdminPanelHeight=0}this.adjustAdminPanel()},handleSiteMapClick:function(){this.switchToSlidingMode(false);BX.SidePanel.Instance.open("bitrix24-sitemap",{cacheable:false,contentCallback:function(){var e=new BX.Promise;e.fulfill(BX("sitemap").innerHTML);return e}})},handleUpButtonClick:function(){this.isMenuMouseEnterBlocked=true;if(this.isUpButtonReversed()){window.scrollTo(0,this.lastScrollOffset);this.lastScrollOffset=0;this.unreverseUpButton()}else{this.lastScrollOffset=window.pageYOffset;window.scrollTo(0,0);this.reverseUpButton()}setTimeout(function(){clearTimeout(this.slidingModeTimeoutId);this.slidingModeTimeoutId=null;this.isMenuMouseEnterBlocked=false}.bind(this),100)},handleUpButtonMouseEnter:function(){this.isMenuMouseEnterBlocked=true;clearTimeout(this.slidingModeTimeoutId);this.slidingModeTimeoutId=null},handleUpButtonMouseLeave:function(){this.isMenuMouseEnterBlocked=false},handleMenuItemMouseEnter:function(e){this.handleMenuMouseEnter(e)},handleMenuItemMouseClick:function(e){if(this.isCollapsed()){clearTimeout(this.slidingModeTimeoutId);this.slidingModeTimeoutId=null}},handleDocumentScroll:function(){this.adjustAdminPanel();this.applyScrollMode();if(window.pageYOffset>document.documentElement.clientHeight){this.showUpButton();if(this.isUpButtonReversed()){this.unreverseUpButton();this.lastScrollOffset=0}}else{if(!this.isUpButtonReversed()){this.hideUpButton()}}if(window.pageXOffset>0){this.menuContainer.style.left=-window.pageXOffset+"px";this.upButton.style.left=-window.pageXOffset+(this.isCollapsed()?0:172)+"px"}else{this.menuContainer.style.removeProperty("left");this.upButton.style.removeProperty("left")}},handleGroupPanelOpen:function(){this.isMenuMouseLeaveBlocked=true},handleGroupPanelClose:function(){this.isMenuMouseLeaveBlocked=false},showUpButton:function(){this.menuContainer.classList.add("menu-up-button-active")},hideUpButton:function(){this.menuContainer.classList.remove("menu-up-button-active")},reverseUpButton:function(){this.menuContainer.classList.add("menu-up-button-reverse")},unreverseUpButton:function(){this.menuContainer.classList.remove("menu-up-button-reverse")},isUpButtonReversed:function(){return this.menuContainer.classList.contains("menu-up-button-reverse")},isDefaultTheme:function(){return document.body.classList.contains("bitrix24-default-theme")},getTopPadding:function(){return this.isDefaultTheme()?0:9},getAdminPanelHeight:function(){if(this.adminPanelHeight!==null){return this.adminPanelHeight}if(this.adminPanel){this.adminPanelHeight=this.adminPanel.offsetHeight}else{this.adminPanelHeight=0}return this.adminPanelHeight},getTemplateHeaderHeight:function(){if(this.templateHeaderHeight===null){var e=BX("header");if(e){this.templateHeaderHeight=e.offsetHeight}}return this.templateHeaderHeight?this.templateHeaderHeight:0},switchToSlidingMode:function(e,t){if(e===false){if(this.slidingModeTimeoutId){clearTimeout(this.slidingModeTimeoutId);this.slidingModeTimeoutId=null}if(BX.hasClass(this.mainTable,"menu-sliding-mode")){if(t!==true){BX.addClass(this.mainTable,"menu-sliding-closing-mode")}BX.removeClass(this.mainTable,"menu-sliding-mode menu-sliding-opening-mode")}}else if(this.isCollapsedMode&&!BX.hasClass(this.mainTable,"menu-sliding-mode")){BX.removeClass(this.mainTable,"menu-sliding-closing-mode");if(t!==true){BX.addClass(this.mainTable,"menu-sliding-opening-mode")}BX.addClass(this.mainTable,"menu-sliding-mode")}},handleSlidingTransitionEnd:function(e){if(e.target===this.menuContainer){BX.removeClass(this.mainTable,"menu-sliding-opening-mode menu-sliding-closing-mode")}},switchToScrollMode:function(e){if(e===false){if(this.isScrollMode===true){this.adjustSpotlight(true);BX.removeClass(this.mainTable,"menu-scroll-mode");this.isScrollMode=false}}else{if(this.isScrollMode===false){this.adjustSpotlight(false);BX.addClass(this.mainTable,"menu-scroll-mode");this.isScrollMode=true}}},switchToLogoMaskMode:function(e){if(e===false){if(this.islogoMaskMode===true){BX.removeClass(this.mainTable,"menu-logo-mask-mode");this.islogoMaskMode=false}}else{if(this.islogoMaskMode===false){BX.addClass(this.mainTable,"menu-logo-mask-mode");this.islogoMaskMode=true}}},applyScrollMode:function(){if(this.isLogoMaskNeeded()){this.switchToLogoMaskMode(true)}var e=this.scrollModeThreshold;if(this.fixedAdminPanelHeight===0&&this.adminPanel){e+=this.getAdminPanelHeight()}if(window.pageYOffset>e){this.switchToScrollMode(true)}else{this.switchToScrollMode(false)}},isLogoMaskNeeded:function(){if(this.logoMaskNeeded===null){if(this.menuHeaderLogo.querySelector(".logo-image-container")){this.logoMaskNeeded=false}else{var e=this.menuHeaderLogo;if(e.offsetWidth===0){e=this.headerLogo}this.logoMaskNeeded=e.offsetWidth>200}}return this.logoMaskNeeded},toggle:function(e,t){var i=BX("layout-left-column");if(!i){return}var s=!BX.hasClass(this.mainTable,"menu-collapsed-mode");if(e===true&&s||e===false&&!s||BX.hasClass(this.mainTable,"menu-animation-mode")){return}if(this.isEditMode()){this.applyEditMode()}BX.onCustomEvent("BX.Bitrix24.LeftMenuClass:onMenuToggle",[e,this]);var n=this.menuHeader.querySelector(".logo-image-container");if(n){var o=this.header.querySelector(".logo-image-container").offsetWidth;if(o>0){n.style.width=o+"px"}}this.isMenuMouseEnterBlocked=true;this.switchToSlidingMode(false,true);this.applyScrollMode();i.style.overflow="hidden";BX.addClass(this.mainTable,"menu-animation-mode "+(s?"menu-animation-closing-mode":"menu-animation-opening-mode"));var a=[].slice.call(i.querySelectorAll(".menu-item-link"));var r=i.querySelector(".menu-collapsed-more-btn");var u=i.querySelector(".menu-default-more-btn");var l=i.querySelector(".menu-sitemap-icon-box");var d=i.querySelector(".menu-sitemap-btn-text");var m=i.querySelector(".menu-invite-employees-text");var h=i.querySelector(".menu-invite-icon-box");var c=i.querySelector(".menu-license-all-container");var p=i.querySelector(".menu-license-all-default");var f=i.querySelector(".menu-license-all-collapsed");var B=i.querySelector(".menu-item-separator");var g=i.querySelector(".menu-item-index-more");var X=this.mainTable.querySelector(".page-header");var y=document.getElementById("bx-im-bar");var v=y?y.offsetWidth:0;new BX.easing({duration:300,start:{translateIcon:s?-100:0,translateText:s?0:-100,translateMoreBtn:s?0:-84,translateLicenseBtn:s?0:-100,burgerMenuWidth:s?33:66,sidebarWidth:s?240:66,opacity:s?100:0,opacityRevert:s?0:100},finish:{translateIcon:s?0:-100,translateText:s?-100:-18,translateMoreBtn:s?-84:0,translateLicenseBtn:s?-100:0,burgerMenuWidth:s?66:33,sidebarWidth:s?66:240,opacity:s?0:100,opacityRevert:s?100:0},transition:BX.easing.makeEaseOut(BX.easing.transitions.quart),step:function(e){i.style.width=e.sidebarWidth+"px";this.menuContainer.style.width=e.sidebarWidth+"px";this.menuHeaderBurger.style.width=e.burgerMenuWidth+"px";this.headerBurger.style.width=e.burgerMenuWidth+"px";if(X){X.style.maxWidth="calc(100vw - "+e.sidebarWidth+"px - "+v+"px)"}if(s){if(l){l.style.transform="translateX("+e.translateIcon+"px)";l.style.opacity=e.opacityRevert/100}if(d){d.style.transform="translateX("+e.translateText+"px)";d.style.opacity=e.opacity/100}if(h){h.style.transform="translateX("+e.translateIcon+"px)";h.style.opacity=e.opacityRevert/100}if(m){m.style.transform="translateX("+e.translateText+"px)";m.style.opacity=e.opacity/100}this.settingsIconBox.style.transform="translateX("+e.translateIcon+"px)";this.settingsIconBox.style.opacity=e.opacityRevert/100;this.settingsBtnText.style.transform="translateX("+e.translateText+"px)";this.settingsBtnText.style.opacity=e.opacity/100;r.style.transform="translateX("+e.translateIcon+"px)";r.style.opacity=e.opacityRevert/100;u.style.transform="translateX("+e.translateMoreBtn+"px)";u.style.opacity=e.opacity/100;if(g){g.style.transform="translateX("+e.translateIcon+"px)";g.style.opacity=e.opacityRevert/100}if(c){p.style.transform="translateX("+e.translateLicenseBtn+"px)";p.style.opacity=e.opacity/100;f.style.transform="translateX("+e.translateIcon+"px)";f.style.opacity=e.opacityRevert/100}a.forEach(function(t){var i=t.querySelector(".menu-item-icon-box");var s=t.querySelector(".menu-item-link-text");var n=t.querySelector(".menu-item-index");s.style.transform="translateX("+e.translateText+"px)";s.style.opacity=e.opacity/100;i.style.transform="translateX("+e.translateIcon+"px)";i.style.opacity=e.opacityRevert/100;if(n){n.style.transform="translateX("+e.translateIcon+"px)";n.style.opacity=e.opacityRevert/100}})}else{B.style.opacity=0;if(l){l.style.transform="translateX("+e.translateIcon+"px)";l.style.opacity=e.opacityRevert/100}if(d){d.style.transform="translateX("+e.translateText+"px)";d.style.opacity=e.opacity/100}if(h){h.style.transform="translateX("+e.translateIcon+"px)";h.style.opacity=e.opacityRevert/100}if(m){m.style.transform="translateX("+e.translateText+"px)";m.style.opacity=e.opacity/100}this.settingsIconBox.style.transform="translateX("+e.translateIcon+"px)";this.settingsIconBox.style.opacity=e.opacityRevert/100;this.settingsBtnText.style.transform="translateX("+e.translateText+"px)";this.settingsBtnText.style.opacity=e.opacity/100;r.style.transform="translateX("+e.translateIcon+"px)";r.style.opacity=e.opacityRevert/100;u.style.transform="translateX("+e.translateMoreBtn+"px)";u.style.opacity=e.opacity/100;if(g){g.style.transform="translateX("+e.translateText+"px)"}if(c){p.style.transform="translateX("+e.translateLicenseBtn+"px)";p.style.opacity=e.opacity/100;f.style.transform="translateX("+e.translateIcon+"px)";f.style.opacity=e.opacityRevert/100}a.forEach(function(t){var i=t.querySelector(".menu-item-icon-box");var s=t.querySelector(".menu-item-link-text");var n=t.querySelector(".menu-item-index");s.style.transform="translateX("+e.translateText+"px)";s.style.opacity=e.opacity/100;s.style.display="inline-block";i.style.transform="translateX("+e.translateIcon+"px)";i.style.opacity=e.opacityRevert/100;if(n){n.style.transform="translateX("+e.translateText+"px)"}})}var t=document.createEvent("Event");t.initEvent("resize",true,true);window.dispatchEvent(t)}.bind(this),complete:function(){if(s){this.isCollapsedMode=true;BX.addClass(this.mainTable,"menu-collapsed-mode")}else{this.isCollapsedMode=false;BX.removeClass(this.mainTable,"menu-collapsed-mode")}BX.removeClass(this.mainTable,"menu-animation-mode menu-animation-opening-mode menu-animation-closing-mode");var e=[i,B,this.menuHeaderBurger,this.headerBurger,this.settingsIconBox,this.settingsBtnText,u,r,n,l,d,h,m,g,p,f,this.menuContainer,X];e.forEach(function(e){if(e){e.style.cssText=""}});a.forEach(function(e){var t=e.querySelector(".menu-item-icon-box");var i=e.querySelector(".menu-item-link-text");var s=e.querySelector(".menu-item-index");e.style.cssText="";i.style.cssText="";t.style.cssText="";if(s){s.style.cssText=""}});this.isMenuMouseEnterBlocked=false;this.adjustAdminPanel();if(BX.type.isFunction(t)){t()}var o=s?"collapse_menu":"expand_menu";BX.ajax({method:"POST",dataType:"json",url:BX.util.add_url_param(this.ajaxPath,{analyticsLabel:o}),data:{sessid:BX.bitrix_sessid(),site_id:this.siteId,action:o}});var c=document.createEvent("Event");c.initEvent("resize",true,true);window.dispatchEvent(c)}.bind(this)}).animate()},highlight:function(e){if(!BX.type.isNotEmptyString(e)||!this.menuContainer){return false}var t=this.menuContainer.getElementsByTagName("li");var i=-1;var s=-1;var n=-1;var o=null;for(var a=0,r=t.length;a<r;a++){var u=[];var l=t[a].getAttribute("data-link");if(BX.type.isNotEmptyString(l)){var d=t[a].getAttribute("data-type");u.push({priority:BX.util.in_array(d,["standard","admin"])?3:2,url:l})}var m=t[a].getAttribute("data-all-links");if(BX.type.isNotEmptyString(m)){m.split(",").forEach(function(e){e=BX.util.trim(e);if(BX.type.isNotEmptyString(e)){u.push({priority:1,url:e})}})}for(var h=0,c=u.length;h<c;h++){var p=u[h].url;var f=u[h].priority;var B=this.isItemSelected(p,e);if(B){var g=p.length;if(g>s||g===s&&f>n){i=a;o=p;s=g;n=f}}}}if(i<0){return}var X=t[i];BX.addClass(X,"menu-item-active");var y=X.parentNode.parentNode;if(BX.hasClass(y,"menu-item-favorites-more")&&!BX.hasClass(y,"menu-item-favorites-more-open")){this.showHideMoreItems(false)}return true},makeTextIcons:function(){var e=this.menuContainer.getElementsByTagName("li");for(var t=0,i=e.length;t<i;t++){var s=e[t];var n=!s.classList.contains("menu-item-no-icon-state");if(n){continue}var o=s.querySelector(".menu-item-icon");var a=s.querySelector(".menu-item-link-text");if(o&&a){o.textContent=this.getShortName(a.textContent)}}},getShortName:function(e){if(!BX.type.isString(e)||!BX.type.isNotEmptyString(e.trim())){return"..."}var t="";e=e.replace(/['`".,:;~|{}*^$#@&+\-=?!()[\]<>\n\r]+/g,"").trim();if(!BX.type.isNotEmptyString(e)){t=e.substring(0,2)}else if(e.length===2){t=e}var i=e.split(/[\s,]+/);if(i.length<=1){t=e.substring(0,1)}else if(i.length===2){t=i[0].substring(0,1)+i[1].substring(0,1)}else{var s=i[0];var n=i[1];for(var o=1;o<i.length;o++){if(i[o].length>3){n=i[o];break}}t=s.substring(0,1)+n.substring(0,1)}return t.toUpperCase()},getSelectedItem:function(e,t){if(!BX.type.isNotEmptyString(e)||!this.menuContainer){return false}var i=this.menuContainer.getElementsByTagName("li");var s=-1;var n=-1;var o=null;for(var a=0,r=i.length;a<r;a++){var u=[];if(t){var l=i[a].getAttribute("data-all-links");if(BX.type.isNotEmptyString(l)){u=u.concat(l.split(","))}}else{var d=i[a].getAttribute("data-link");if(BX.type.isNotEmptyString(d)){u.push(d)}}for(var m=0,h=u.length;m<h;m++){var c=u[m];if(!BX.type.isNotEmptyString(c)){continue}var p=this.isItemSelected(c,e);if(p){var f=c.length;if(f>n){s=a;o=u[m];n=f}}}}return s>=0?i[s]:null},isItemSelected:function(e,t){var i=t;var s=t.indexOf("?");if(s!==-1){t=t.substring(0,s)}var n=this.getUrlWithIndex(t);e=e.replace(/(\/index\.php)($|\?)/,"$2");if(t.indexOf(e)===0||n.indexOf(e)===0){return true}s=e.indexOf("?");if(s===-1){return false}var o=e.substring(0,s);if(o!==t&&o!==n){return false}var a=true;var r=this.getUrlParams(e);var u=this.getUrlParams(i);for(var l in r){if(!r.hasOwnProperty(l)){continue}var d=r[l];var m=typeof u[l]!=="undefined"?u[l]:[];for(var h=0;h<d.length;h++){var c=d[h];if(!BX.util.in_array(c,m)){a=false;break}}}return a},getUrlParams:function(e){var t={};var i=e.indexOf("?");if(i===-1){return t}var s=e.substring(i+1).split("&");for(var n=0;n<s.length;n++){var o=s[n];var a=o.indexOf("=");if(a===0){continue}var r=a===-1?o:o.substring(0,a);r=r.replace("[]","");var u=a===-1?"":o.substring(a+1);if(t[r]){t[r].push(u)}else{t[r]=[u]}}return t},getUrlWithIndex:function(e){if(!BX.type.isNotEmptyString(e)){e=""}var t=e.indexOf("?");var i=t>=0?"?"+e.substring(t+1):"";var s=t>=0?e.substring(0,t):e;if(s.match(/\.php$/)){return e}if(s.slice(-1)!=="/"){s+="/"}return s+"index.php"+i},checkCurrentPageInTopMenu:function(){var e=document.location.pathname+document.location.search;if(BX.Main&&BX.Main.interfaceButtonsManager){var t=BX.Main.interfaceButtonsManager.getObjects();var i=Object.keys(t);if(i[0]){var s=t[i[0]];this.topItemSelectedObj=s.getActive();if(typeof this.topItemSelectedObj==="object"&&this.topItemSelectedObj){if(this.topItemSelectedObj.hasOwnProperty("NODE")){this.topMenuSelectedNode=this.topItemSelectedObj.NODE}var n=document.createElement("a");n.href=this.topItemSelectedObj.URL;var o=n.pathname[0]!=="/"?"/"+n.pathname:n.pathname;this.topItemSelectedObj.URL=BX.util.htmlspecialcharsback(o+n.search);this.topItemSelectedObj.TEXT=BX.util.htmlspecialcharsback(this.topItemSelectedObj.TEXT);this.isCurrentPageStandard=this.topItemSelectedObj.URL===e&&this.topItemSelectedObj.URL.indexOf("workgroups")===-1}}}return this.isCurrentPageStandard},checkLinkInMenu:function(e){if(!BX.type.isNotEmptyString(e))return;if(BX.type.isDomNode(this.menuItemsBlock)){var t=this.menuItemsBlock.getElementsByClassName("menu-item-block");for(var i=0;i<t.length;i++){if(t[i].getAttribute("data-link")==e)return t[i]}}return false},getStructureForHelper:function(){var e={menu:{}};if(BX.type.isDomNode(this.menuContainer)){var t=["show","hide"];for(var i in t){var s=this.menuContainer.querySelectorAll("[data-status='"+t[i]+"']");if(s){var n=[];for(var o=0,a=s.length;o<a;o++){if(s[o].getAttribute("data-type")!="default"){continue}n.push(s[o].getAttribute("data-id"))}}e[t[i]]=n}}return e},initPagetitleStar:function(){this.checkCurrentPageInTopMenu();var e=BX("uiToolbarStar");if(!e){return false}var t=document.location.pathname+document.location.search;var i=this.checkLinkInMenu(t);if(typeof i=="object"){BX.addClass(e,"ui-toolbar-star-active")}e.title=BX.message(BX.hasClass(e,"ui-toolbar-star-active")?"MENU_DELETE_PAGE_FROM_LEFT_MENU":"MENU_ADD_PAGE_TO_LEFT_MENU");if(typeof i=="object"&&i.getAttribute("data-type")==="default"){e.title=BX.message("MENU_STAR_TITLE_DEFAULT_PAGE");BX.bind(e,"click",BX.proxy(function(){this.showMessage(BX.proxy_context,BX.message("MENU_STAR_TITLE_DEFAULT_PAGE_DELETE_ERROR"))},this));return true}BX.bind(e,"click",BX.proxy(function(){if(BX.hasClass(e,"ui-toolbar-star-active")){this.deleteStandardItem()}else{this.addStandardItem()}},this));return true},initPreset:function(){var e=BX("left-menu-preset-popup");if(!BX.type.isDomNode(e))return;this.presetItems=e.getElementsByClassName("js-left-menu-preset-item");if(typeof this.presetItems=="object"){for(var t=0;t<this.presetItems.length;t++){BX.bind(this.presetItems[t],"click",BX.proxy(function(){this.selectPreset(BX.proxy_context)},this))}}},selectPreset:function(e){for(var t=0;t<this.presetItems.length;t++){BX.removeClass(this.presetItems[t],"left-menu-popup-selected")}if(BX.type.isDomNode(e)){BX.addClass(e,"left-menu-popup-selected")}},showImportConfigurationSlider:function(){if(this.urlImportConfiguration!==""){BX.SidePanel.Instance.open(this.urlImportConfiguration)}},showPresetPopupFunction:function(e){BX.ready(function(){var t=null;BX.PopupWindowManager.create("menu-preset-popup",null,{closeIcon:false,offsetTop:1,overlay:true,lightShadow:true,contentColor:"white",draggable:{restrict:true},closeByEsc:false,content:BX("left-menu-preset-popup"),buttons:[t=new BX.PopupWindowButton({text:BX.message("MENU_CONFIRM_BUTTON"),className:"popup-window-button-create",events:{click:BX.proxy(function(){if(BX.hasClass(t.buttonNode,"popup-window-button-wait")){return}BX.addClass(t.buttonNode,"popup-window-button-wait");var i=document.forms["left-menu-preset-form"];var s="";if(i){presets=i.elements["presetType"];for(var n=0;n<presets.length;n++){if(presets[n].checked){s=presets[n].value;break}}}BX.ajax({method:"POST",dataType:"json",url:this.ajaxPath+"?analyticsLabel="+s+(e=="global"?"&analyticsFirst=y":""),data:{sessid:BX.bitrix_sessid(),site_id:this.siteId,siteDir:this.siteDir,action:"set_preset",preset:s,mode:e=="global"?"global":"personal"},onsuccess:BX.proxy(function(e){if(e.hasOwnProperty("url")){document.location.href=e.url}else{document.location.reload()}},this),onfailure:function(){document.location.reload()}})},this)}}),new BX.PopupWindowButton({text:BX.message("MENU_DELAY_BUTTON"),events:{click:BX.proxy(function(){BX.ajax({method:"POST",dataType:"json",url:this.ajaxPath+"?analyticsLabel=later"+(e=="global"?"&analyticsFirst=y":""),data:{sessid:BX.bitrix_sessid(),action:"delay_set_preset"}});BX.proxy_context.popupWindow.close();if(this.showImportConfiguration){this.showImportConfigurationSlider()}},this)}})]}).show();this.initPreset()}.bind(this))},showCustomPresetPopup:function(){var e=BX.create("form",{attrs:{id:"customPresetForm",style:"min-width: 350px"},children:[BX.create("div",{attrs:{style:"margin: 15px 0 15px 9px;"},children:[BX.create("input",{attrs:{type:"radio",name:"customPresetSettings",id:"customPresetCurrentUser",value:"currentUser"}}),BX.create("label",{attrs:{for:"customPresetCurrentUser"},html:BX.message("MENU_CUSTOM_PRESET_CURRENT_USER")})]}),BX.create("div",{attrs:{style:"margin: 0 0 38px 9px;"},children:[BX.create("input",{attrs:{type:"radio",name:"customPresetSettings",id:"customPresetNewUser",value:"newUser",checked:"checked"}}),BX.create("label",{attrs:{for:"customPresetNewUser"},html:BX.message("MENU_CUSTOM_PRESET_NEW_USER")})]}),BX.create("hr",{attrs:{style:"background-color: #edeef0; border: none; color:  #edeef0; height: 1px;"}})]});var t=[],i=[],s=[],n="";var o=BX.findChildren(this.menuContainer,{className:"menu-item-block"},true);for(var a=0;a<o.length;a++){if(a==0){n=o[a].getAttribute("data-link")}if(o[a].getAttribute("data-status")=="show"){t.push(o[a].getAttribute("data-id"))}else if(o[a].getAttribute("data-status")=="hide"){i.push(o[a].getAttribute("data-id"))}if(o[a].getAttribute("data-type")=="self"||o[a].getAttribute("data-type")=="standard"||o[a].getAttribute("data-type")=="custom"){var r=o[a].querySelector("[data-role='item-text']");var u={ID:o[a].getAttribute("data-id"),LINK:o[a].getAttribute("data-link"),TEXT:BX.util.htmlspecialcharsback(r.innerHTML)};if(o[a].getAttribute("data-new-page")=="Y"){u.NEW_PAGE="Y"}s.push(u)}}this.menuItemsCustomSort={show:t,hide:i};BX.PopupWindowManager.create("menu-custom-preset-popup",null,{closeIcon:true,offsetTop:1,overlay:true,contentColor:"white",contentNoPaddings:true,lightShadow:true,draggable:{restrict:true},closeByEsc:true,titleBar:BX.message("MENU_CUSTOM_PRESET_POPUP_TITLE"),content:e,buttons:[button=new BX.PopupWindowButton({text:BX.message("MENU_SAVE_BUTTON"),className:"popup-window-button-create",events:{click:BX.proxy(function(){if(BX.hasClass(button.buttonNode,"popup-window-button-wait")){return}BX.addClass(button.buttonNode,"popup-window-button-wait");var e=BX("customPresetForm");if(BX.type.isDomNode(e)){var t=e.elements["customPresetSettings"].value}BX.ajax({method:"POST",dataType:"json",url:this.ajaxPath+"?analyticsLabel=customPreset",data:{sessid:BX.bitrix_sessid(),site_id:this.siteId,siteDir:this.siteDir,action:"save_custom_preset",userApply:t,itemsSort:this.menuItemsCustomSort,customItems:s,firstItemLink:n},onsuccess:BX.proxy(function(){BX.removeClass(button.buttonNode,"popup-window-button-wait");BX.PopupWindowManager._currentPopup.close();this.customPresetExists=true;BX.PopupWindowManager.create("menu-custom-preset-success-popup",null,{closeIcon:true,contentColor:"white",titleBar:BX.message("MENU_CUSTOM_PRESET_POPUP_TITLE"),content:BX.message("MENU_CUSTOM_PRESET_SUCCESS")}).show()},this),onfailure:function(){}})},this),close:function(){this.popupWindow.destroy()}}}),new BX.PopupWindowButton({text:BX.message("MENU_CANCEL"),className:"popup-window-button-link popup-window-button-link-cancel",events:{click:function(){this.popupWindow.close()}}})]}).show()},showLoader:function(){var e=BX.create("div",{html:'<div style="display: block" class="intranet-loader-container"> <svg class="intranet-loader-circular" viewBox="25 25 50 50"> <circle class="intranet-loader-path" cx="50" cy="50" r="20" fill="none" stroke-miterlimit="10" /> </svg> </div>'});document.body.appendChild(e)}};BX.Bitrix24.LeftMenuClass=BX.Bitrix24.LeftMenu;BX.namespace("BX.Intranet.ExpandTariff");BX.Intranet.ExpandTariff={redirect:function(e){if(typeof e!=="object")return;var t=e.COUNTER_URL||"",i=e.LICENSE_PATH||"",s=e.HOST||"";BX.ajax.post(t,{action:"upgradeButton",host:s},BX.proxy(function(){document.location.href=i},this))}};
/* End */
;
; /* Start:"a:4:{s:4:"full";s:95:"/bitrix/templates/bitrix24/components/bitrix/im.messenger/.default/script.min.js?16074881324178";s:6:"source";s:76:"/bitrix/templates/bitrix24/components/bitrix/im.messenger/.default/script.js";s:3:"min";s:80:"/bitrix/templates/bitrix24/components/bitrix/im.messenger/.default/script.min.js";s:3:"map";s:80:"/bitrix/templates/bitrix24/components/bitrix/im.messenger/.default/script.map.js";}"*/
BX.namespace("BX.Intranet.Bitrix24.ImBar");(function(){var e=0;var n=null;var t=false;var i=false;var r=20;function a(){var t=l();if(t){var i=BX.getClass("BX.admin.panel.state");if(i&&i.fixed){e=d()}BX.addCustomEvent("onTopPanelCollapse",function(){n=null;if(BX.admin.panel.isFixed()){e=d()}u()}.bind(this));BX.addCustomEvent("onTopPanelFix",function(n){if(n){e=d()}else{e=0}u()}.bind(this));u()}BX.bind(window,"scroll",o);BX.bind(window,"resize",o);o();BX.bind(BX("bx-im-bar-notify"),"click",function(){if(typeof BXIM=="undefined")return false;BXIM.openNotify()});BX.bind(BX("bx-im-bar-search"),"click",function(){if(typeof BXIM=="undefined")return false;BXIM.openMessenger(0,"im")});BX.bind(BX("bx-im-bar-ol"),"click",function(){if(typeof BXIM=="undefined")return false;BXIM.openMessenger(0,"im-ol")});BX.bind(BX("bx-im-btn-call"),"click",function(e){if(typeof BXIM=="undefined")return false;BXIM.webrtc.openKeyPad(e)});BX.bind(window,"scroll",function(){if(typeof BXIM=="undefined"||!BXIM.messenger.popupPopupMenu)return true;if(BX.util.in_array(BXIM.messenger.popupPopupMenu.uniquePopupId.replace("bx-messenger-popup-",""),["createChat","contactList"])){BXIM.messenger.popupPopupMenu.close()}});BX.bindDelegate(BX("bx-im-external-recent-list"),"contextmenu",{className:"bx-messenger-cl-item"},function(e){if(typeof BXIM=="undefined")return false;BXIM.messenger.openPopupMenu(this,"contactList",false);return BX.PreventDefault(e)});BX.bindDelegate(BX("bx-im-external-recent-list"),"click",{className:"bx-messenger-cl-item"},function(e){if(typeof BXIM=="undefined")return false;BXIM.openMessenger(this.getAttribute("data-userId"));return BX.PreventDefault(e)});BX.addCustomEvent("onMessengerWindowBodyOverflow",function(e,n){var t=BX.findChildrenByClassName(BX("im-workarea-popup"),"bx-im-fullscreen-popup-td",true);for(var i=0;i<t.length;i++){var r=getComputedStyle(t[i],null);r=r?parseInt(r.getPropertyValue("padding-left").toString().replace("px","")):85;t[i].style.paddingRight=r+n+"px"}document.body.style.paddingRight=n+"px";BX("bx-im-bar").style.right=n+"px"});BX.addCustomEvent("onImUpdateCounterNotify",function(e){var n=BX.findChildByClassName(BX("bx-im-bar-notify"),"bx-im-informer-num");if(!n)return false;if(e>0){n.innerHTML='<div class="bx-im-informer-num-digit">'+(e>99?"99+":e)+"</div>"}else{n.innerHTML=""}});BX.addCustomEvent("onImUpdateCounterMessage",function(e,n){var t=null;if(n==="LINES"){t=BX("bx-im-bar-ol")}else{return false}var i=t&&BX.findChildByClassName(t,"bx-im-informer-num");if(!i)return false;if(e>0){i.innerHTML='<div class="bx-im-informer-num-digit">'+(e>99?"99+":e)+"</div>"}else{i.innerHTML=""}});BX.bind(BX("im-workarea-backgound-selector"),"change",function(){BX("im-workarea-backgound-selector-title").innerHTML=this.options[this.selectedIndex].text});BX.addCustomEvent("onMessengerWindowInit",function(){BX("im-workarea-backgound-selector-title").innerHTML=BX("im-workarea-backgound-selector").options[BX("im-workarea-backgound-selector").selectedIndex].text});BX.addCustomEvent("onImInit",function(e){e.notify.panelButtonCall=BX("bx-im-btn-call");e.notify.panelButtonCallAnlgePosition="bottom";e.notify.panelButtonCallAnlgeOffset=131;BX.MessengerCommon.recentListRedraw()})}function o(){var n=s();if(!n||n.dataset.lockRedraw==="true"){return}u();var a=document.documentElement.scrollWidth-document.documentElement.clientWidth;if(a>0){if(!t){BX.addClass(n,"bx-im-bar-transparent");t=true}}else{if(t){BX.removeClass(n,"bx-im-bar-transparent");t=false}}var o=r;if(e===0&&l()){o+=d()}if(window.pageYOffset>o){if(!i){BX.addClass(n,"bx-im-bar-scroll-mode");i=true}}else{if(i){BX.removeClass(n,"bx-im-bar-scroll-mode");i=false}}}function s(){return BX("bx-im-bar")}function l(){return BX("bx-panel")}function u(){var n=l();var t=s();if(!n||!t){return}var i=n.getBoundingClientRect();if(i.bottom>0){t.style.top=Math.max(i.bottom,e)+"px"}else{t.style.top=Math.max(0,e)+"px"}}function d(){if(n!==null){return n}var e=l();if(e){n=e.offsetHeight}else{n=0}return n}function f(){BX.MessengerWindow.closePopup()}BX.Intranet.Bitrix24.ImBar.init=a;BX.Intranet.Bitrix24.ImBar.redraw=o;window.bxFullscreenClose=f})();
/* End */
;
; /* Start:"a:4:{s:4:"full";s:84:"/bitrix/components/bitrix/ui.toolbar/templates/.default/script.min.js?16074881593831";s:6:"source";s:65:"/bitrix/components/bitrix/ui.toolbar/templates/.default/script.js";s:3:"min";s:69:"/bitrix/components/bitrix/ui.toolbar/templates/.default/script.min.js";s:3:"map";s:69:"/bitrix/components/bitrix/ui.toolbar/templates/.default/script.map.js";}"*/
(function(){"use strict";BX.namespace("BX.UI");BX.UI.ToolbarManager={toolbars:{},create:function(t){var i=new BX.UI.Toolbar(t);if(this.get(i.getId())){throw new Error("The toolbar instance with the same 'id' already exists.")}this.toolbars[i.getId()]=i;return i},getDefaultToolbar:function(){return this.get("default-toolbar")},get:function(t){return t in this.toolbars?this.toolbars[t]:null}};BX.UI.Toolbar=function(t){t=BX.type.isPlainObject(t)?t:{};this.titleMinWidth=BX.type.isNumber(t.titleMinWidth)?t.titleMinWidth:158;this.titleMaxWidth=BX.type.isNumber(t.titleMaxWidth)?t.titleMaxWidth:"";this.filterMinWidth=BX.type.isNumber(t.filterMinWidth)?t.filterMinWidth:300;this.filterMaxWidth=BX.type.isNumber(t.filterMaxWidth)?t.filterMaxWidth:748;this.id=BX.Type.isStringFilled(t.id)?t.id:BX.Text.getRandom();this.toolbarContainer=t.target;if(!BX.Type.isDomNode(this.toolbarContainer)){throw new Error('BX.UI.Toolbar: "target" parameter is required.')}this.titleContainer=this.toolbarContainer.querySelector(".ui-toolbar-title-box");this.filterContainer=this.toolbarContainer.querySelector(".ui-toolbar-filter-box");this.filterButtons=this.toolbarContainer.querySelector(".ui-toolbar-filter-buttons");this.rightButtons=this.toolbarContainer.querySelector(".ui-toolbar-right-buttons");this.afterTitleButtons=this.toolbarContainer.querySelector(".ui-toolbar-after-title-buttons");if(!this.filterContainer){this.filterMinWidth=0;this.filterMaxWidth=0}this.buttons=Object.create(null);this.buttonIds=BX.Type.isArray(t.buttonIds)?t.buttonIds:[];if(!this.buttonIds.length){return}this.buttonIds.forEach(function(t){var i=BX.UI.ButtonManager.createByUniqId(t);if(i){i.getContainer().originalWidth=i.getContainer().offsetWidth;if(!i.getIcon()&&!BX.Type.isStringFilled(i.getDataSet()["toolbarCollapsedIcon"])){if(i.getColor()===BX.UI.ButtonColor.PRIMARY){i.setDataSet({toolbarCollapsedIcon:BX.UI.ButtonIcon.ADD})}else{console.warn('BX.UI.Toolbar: the button "'+i.getText()+'" '+"doesn't have an icon for collapsed mode. "+'Use the "data-toolbar-collapsed-icon" attribute.')}}this.buttons[t]=i}else{console.warn('BX.UI.Toolbar: the button "'+t+"\" wasn't initialized.")}},this);this.windowWidth=document.body.offsetWidth;this.reduceItemsWidth();window.addEventListener("resize",function(){if(this.isWindowIncreased()){this.increaseItemsWidth()}else{this.reduceItemsWidth()}}.bind(this))};BX.UI.Toolbar.prototype={getButtons:function(){return this.buttons},getButton:function(t){return t in this.buttons?this.buttons[t]:null},getId:function(){return this.id},isWindowIncreased:function(){var t=this.windowWidth;var i=document.body.offsetWidth;this.windowWidth=i;return i>t},getContainerSize:function(){return this.toolbarContainer.offsetWidth},getInnerTotalWidth:function(){return this.toolbarContainer.scrollWidth},reduceItemsWidth:function(){if(this.getInnerTotalWidth()<=this.getContainerSize()){return}var t=Object.values(this.getButtons());for(var i=t.length-1;i>=0;i--){var e=t[i];if(!e.getIcon()&&!BX.Type.isStringFilled(e.getDataSet()["toolbarCollapsedIcon"])){continue}if(e.isCollapsed()){continue}e.setCollapsed(true);if(!e.getIcon()){e.setIcon(e.getDataSet()["toolbarCollapsedIcon"])}if(this.getInnerTotalWidth()<=this.getContainerSize()){return}}},increaseItemsWidth:function(){var t=Object.values(this.getButtons());for(var i=0;i<t.length;i++){var e=t[i];var o=e.getContainer();if(!e.isCollapsed()){continue}var n=this.titleMinWidth+this.filterMinWidth+(this.afterTitleButtons?this.afterTitleButtons.offsetWidth:0)+(this.filterButtons?this.filterButtons.offsetWidth:0)+(this.rightButtons?this.rightButtons.offsetWidth:0)+(o.originalWidth-o.offsetWidth);if(n>this.getContainerSize()){break}e.setCollapsed(false);if(e.getIcon()===e.getDataSet()["toolbarCollapsedIcon"]){e.setIcon(null)}}}}})();
/* End */
;
; /* Start:"a:4:{s:4:"full";s:54:"/bitrix/templates/bitrix24/bitrix24.js?160748813228801";s:6:"source";s:38:"/bitrix/templates/bitrix24/bitrix24.js";s:3:"min";s:0:"";s:3:"map";s:0:"";}"*/

/*Global Settings */
(function() {

	var iframeMode = window !== window.top;
	var search = window.location.search;
	var sliderMode = search.indexOf("IFRAME=") !== -1 || search.indexOf("IFRAME%3D") !== -1;

	if (iframeMode && sliderMode)
	{
		return;
	}
	else if (iframeMode)
	{
		window.top.location = window.location.href;
		return;
	}

	BX.addCustomEvent("onFrameDataRequestFail", function(response) {
		top.location = "/auth/?backurl=" + encodeURIComponent(B24.getBackUrl());
	});

	BX.addCustomEvent("onAjaxFailure", function(status) {
		var redirectUrl = "/auth/?backurl=" + B24.getBackUrl();
		if (status == "auth" && typeof(window.frameRequestStart) !== "undefined")
		{
			top.location = redirectUrl;
		}
	});

	BX.addCustomEvent("onPopupWindowInit", function(uniquePopupId, bindElement, params) {
		//if (BX.util.in_array(uniquePopupId, ["task-legend-popup"]))
		//	params.lightShadow = true;

		if (uniquePopupId == "bx_log_filter_popup")
		{
			params.lightShadow = true;
			params.className = "";
		}
		else if (uniquePopupId == "task-legend-popup")
		{
			params.lightShadow = true;
			params.offsetTop = -15;
			params.offsetLeft = -670;
			params.angle = {offset : 740};
		}
		else if ((uniquePopupId == "task-gantt-filter") || (uniquePopupId == "task-list-filter"))
		{
			params.lightShadow = true;
			params.className = "";
		}
		else if (uniquePopupId.indexOf("sonet_iframe_popup_") > -1)
		{
			params.lightShadow = true;
		}
	});

	BX.addCustomEvent("onJCClockInit", function(config) {

		JCClock.setOptions({
			"centerXInline" : 83,
			"centerX" : 83,
			"centerYInline" : 67,
			"centerY" : 79,
			"minuteLength" : 31,
			"hourLength" : 26,
			"popupHeight" : 229,
			"inaccuracy" : 15,
			"cancelCheckClick" : true
		});
	});

	/*BX.PopupWindow.setOptions({
		"angleMinTop" : 35,
		"angleMinRight" : 10,
		"angleMinBottom" : 35,
		"angleMinLeft" : 10,
		"angleTopOffset" : 5,
		"angleLeftOffset" : 45,
		"offsetLeft" : 0 //-15,
		"offsetTop" : 2,
		"positionTopXOffset" : -11 //20
	});*/

	BX.addCustomEvent("onPullEvent-main", function(command,params){
		if (command == "user_counter" && params[BX.message("SITE_ID")])
		{
			var counters = BX.clone(params[BX.message('SITE_ID')]);
			B24.updateCounters(counters);
		}
	});

	BX.addCustomEvent("onPullEvent-bitrix24", BX.delegate(function(command,params){
		if (command == "userLimitNotify")
		{
			BX.UI.Notification.Center.notify({
				content: params.message
			});
		}
	}, this));

	BX.addCustomEvent(window, "onImUpdateCounter", function(counters){

		if (!counters)
			return;

		B24.updateCounters(BX.clone(counters));
	});

	BX.addCustomEvent("onCounterDecrement", function(iDecrement) {
		B24.decrementCounter(BX("menu-counter-live-feed"), iDecrement)
	});

	BX.addCustomEvent("onImUpdateCounterNotify", function(counter) {
		B24.updateInformer(BX("im-informer-events", true), counter);
	});

	BX.addCustomEvent("onImUpdateCounterMessage", function(counter) {
		B24.updateInformer(BX("im-informer-messages", true), counter);
		B24.updateCounters({'im-message': counter});
	});

	BX.addCustomEvent("onImUpdateCounterNetwork", function(counter) {
		B24.updateInformer(BX("b24network-informer-events", true), counter);
	});

	BX.addCustomEvent("Kanban.Grid:onFixedModeStart", function() {
		BX.ready(function() {
			BX("footer").style.visibility = "hidden";
		});
	});

//connection status===
	BX.addCustomEvent("onPullError", BX.delegate(function(error, code) {
		if (error == 'AUTHORIZE_ERROR')
		{
			B24.connectionStatus("offline");
		}
		else if (error == 'RECONNECT' && (code == 1008 || code == 1006))
		{
			B24.connectionStatus("connecting");
		}
	}, this));

	BX.addCustomEvent("onImError", BX.delegate(function(error, sendErrorCode) {
		if (error == 'AUTHORIZE_ERROR' || error == 'SEND_ERROR' && sendErrorCode == 'AUTHORIZE_ERROR')
		{
			B24.connectionStatus("offline");
		}
		else if (error == 'CONNECT_ERROR')
		{
			B24.connectionStatus("offline");
		}
	}, this));

	BX.addCustomEvent("onPullStatus", BX.delegate(function(status){
		if (status == 'offline')
			B24.connectionStatus("offline");
		else
			B24.connectionStatus("online");
	}, this));

//==connection status

	if (BX.browser.SupportLocalStorage())
	{
		BX.addCustomEvent(window, 'onLocalStorageSet', function(params)
		{
			if (params.key.substring(0, 4) == 'lmc-')
			{
				var counters = {};
					counters[params.key.substring(4)] = params.value;
				B24.updateCounters(counters, false);
			}
		});
	}

	if (BX.getClass("BX.rest.AppLayout"))
	{
		var placementInterface = BX.rest.AppLayout.initializePlacement("DEFAULT");
		placementInterface.prototype.showHelper = function(params, cb)
		{
			var query = "";
			if (BX.type.isNumber(params))
			{
				query = "redirect=detail&code=" + params;
			}
			else if (BX.type.isNotEmptyString(params))
			{
				query = params;
			}
			else if (BX.type.isPlainObject(params))
			{
				for (var param in params)
				{
					if (query.length)
					{
						query += "&";
					}

					query += param + "=" + params[param];
				}
			}

			if (query.length)
			{
				BX.Helper.show(query);
			}
		};
	}

	BX.ready(function () {
		BX.bind(window, "scroll", BX.throttle(B24.onScroll, 150, B24));
	});
})();

var B24 = {

	b24ConnectionStatusState: "online",
	b24ConnectionStatus: null,
	b24ConnectionStatusText: null,
	b24ConnectionStatusTimeout: null,

	formateDate : function(time){
		return BX.util.str_pad(time.getHours(), 2, '0', 'left') + ':' + BX.util.str_pad(time.getMinutes(), 2, '0', 'left');
	},

	openLanguagePopup: function(button)
	{
		if (!BX.type.isDomNode(BX("b24LangPopupContent")))
			return;

		BX.PopupWindowManager.create('b24LangPopup', button, {
			content: BX("b24LangPopupContent"),
			closeIcon: false,
			autoHide: true,
			closeByEsc: true,
			angle: {offset: 50}
		}).show();
	},

	changeLanguage: function(lang)
	{
		window.location.href = "/auth/?user_lang=" + lang + "&backurl=" + B24.getBackUrl();
	},

	getBackUrl: function()
	{
		var backUrl = window.location.pathname;
		var query = B24.getQueryString(["logout", "login", "back_url_pub", "user_lang"]);
		return backUrl + (query.length > 0 ? "?" + query : "");
	},

	getQueryString : function(ignoredParams)
	{
		var query = window.location.search.substring(1);
		if (!BX.type.isNotEmptyString(query))
		{
			return "";
		}

		var vars = query.split("&");
		ignoredParams = BX.type.isArray(ignoredParams) ? ignoredParams : [];

		var result = "";
		for (var i = 0; i < vars.length; i++)
		{
			var pair = vars[i].split("=");
			var equal = vars[i].indexOf("=");
			var key = pair[0];
			var value = BX.type.isNotEmptyString(pair[1]) ? pair[1] : false;
			if (!BX.util.in_array(key, ignoredParams))
			{
				if (result !== "")
				{
					result += "&";
				}
				result += key + (equal !== -1 ? "=" : "") + (value !== false ? value : "" );
			}
		}

		return result;
	},

	updateInformer : function(informer, counter)
	{
		if (!informer)
			return false;

		if (counter > 0)
		{
			informer.innerHTML = counter;
			BX.addClass(informer, "header-informer-act");
		}
		else
		{
			informer.innerHTML = "";
			BX.removeClass(informer, "header-informer-act");
		}
	},

	updateCounters : function(counters, send)
	{
		BX.ready(function ()
		{
			if (BX.getClass("BX.Bitrix24.LeftMenuClass"))
			{
				BX.Bitrix24.LeftMenuClass.updateCounters(counters, send);
			}
		});
	},

	decrementCounter : function(node, iDecrement)
	{
		BX.ready(function ()
		{
			if (BX.getClass("BX.Bitrix24.LeftMenuClass"))
			{
				BX.Bitrix24.LeftMenuClass.decrementCounter(node, iDecrement);
			}
		});
	},

	showNotifyPopup : function(button)
	{
		if (BX.hasClass(button, "header-informer-press"))
		{
			BX.removeClass(button, "header-informer-press");
			BXIM.closeNotify();
		}
		else
		{
			BXIM.openNotify();
		}
	},

	showMessagePopup : function(button)
	{
		if (typeof(BXIM) == 'undefined')
			return false;

		BXIM.toggleMessenger();
	},

	closeBanner : function(bannerId)
	{
		BX.userOptions.save('bitrix24', 'banners',  bannerId, 'Y');
		var banner = BX("sidebar-banner-" + bannerId);
		if (banner)
		{
			banner.style.minHeight = "auto";
			banner.style.overflow = "hidden";
			banner.style.border = "none";
			(new BX.easing({
				duration : 500,
				start : { height : banner.offsetHeight, opacity : 100 },
				finish : { height : 0, opacity: 0 },
				transition : BX.easing.makeEaseOut(BX.easing.transitions.quart),
				step : function(state){
					if (state.height >= 0)
					{
						banner.style.height = state.height + "px";
						banner.style.opacity = state.opacity/100;
					}

					if (state.height <= 17)
					{
						banner.style.marginBottom = state.height + "px";
					}
				},
				complete : function() {
					banner.style.display = "none";
				}
			})).animate();
		}
	},

	showLoading: function(timeout)
	{
		timeout = timeout || 500;
		function show()
		{
			var loader = BX("b24-loader");
			if (loader)
			{
				BX.addClass(loader, "b24-loader-show intranet-loader-show");
				return true;
			}

			return false;
		}

		setTimeout(function() {
			if (!show() && !BX.isReady)
			{
				BX.ready(show);
			}
		}, timeout);
	}
};

/***************** UP button **********************/
B24.onScroll = function()
{
	var windowScroll = BX.GetWindowScrollPos();
	if (B24.b24ConnectionStatus)
	{
		if (B24.b24ConnectionStatus.getAttribute('data-float') == 'true')
		{
			if (windowScroll.scrollTop < 60)
			{
				BX.removeClass(B24.b24ConnectionStatus, 'bx24-connection-status-float');
				B24.b24ConnectionStatus.setAttribute('data-float', 'false');
			}
		}
		else
		{
			if (windowScroll.scrollTop > 60)
			{
				BX.addClass(B24.b24ConnectionStatus, 'bx24-connection-status-float');
				B24.b24ConnectionStatus.setAttribute('data-float', 'true');
			}
		}
	}
};

B24.goUp = function(fn)
{
	var windowScroll = BX.GetWindowScrollPos();

	(new BX.easing({
		duration : 500,
		start : { scroll : windowScroll.scrollTop },
		finish : { scroll : 0 },
		transition : BX.easing.makeEaseOut(BX.easing.transitions.quart),
		step : function(state){
			window.scrollTo(0, state.scroll);
		},
		complete: function() {
			BX.onCustomEvent(window, 'onGoUp');

			if (BX.type.isFunction(fn))
			{
				fn();
			}

		}
	})).animate();
};

/***************** Left Menu ************************/
B24.toggleMenu = function(menuItem, messageShow, messageHide)
{
	var menuBlock = BX.findChild(menuItem.parentNode, {tagName:'ul'}, false, false);

	var menuItems = BX.findChildren(menuBlock, {tagName : "li"}, false);
	if (!menuItems)
		return;

	var toggleText = BX.findChild(menuItem, {className:"menu-toggle-text"}, true, false);
	if (!toggleText)
		return;

	if (BX.hasClass(menuBlock, "menu-items-close"))
	{
		menuBlock.style.height = "0px";
		BX.removeClass(menuBlock, "menu-items-close");
		BX.removeClass(BX.nextSibling(BX.nextSibling(menuItem)), "menu-items-close");
		menuBlock.style.opacity = 0;
		animation(true, menuBlock, menuBlock.scrollHeight);

		toggleText.innerHTML = messageHide;
		BX.userOptions.save("bitrix24", menuItem.id, "hide", "N");
	}
	else
	{
		animation(false, menuBlock, menuBlock.offsetHeight);
		toggleText.innerHTML = messageShow;
		BX.userOptions.save("bitrix24", menuItem.id, "hide", "Y");
	}

	function animation(opening, menuBlock, maxHeight)
	{
		menuBlock.style.overflow = "hidden";
		(new BX.easing({
			duration : 200,
			start : { opacity: opening ? 0 : 100, height: opening ? 0 : maxHeight },
			finish : { opacity: opening ? 100 : 0, height: opening ? maxHeight : 0 },
			transition : BX.easing.transitions.linear,
			step : function(state)
			{
				menuBlock.style.opacity = state.opacity/100;
				menuBlock.style.height = state.height + "px";

			},
			complete : function()
			{
				if (!opening)
				{
					BX.addClass(menuBlock, "menu-items-close");
					BX.addClass(BX.nextSibling(BX.nextSibling(menuItem)), "menu-items-close");
				}
				menuBlock.style.cssText = "";
			}

		})).animate();
	}
};

B24.licenseInfoPopup = {
	show: function(popupId, title, content, showDemoButton)
	{
		if (BX.getClass("BX.Bitrix24.LicenseInfoPopup"))
		{
			BX.Bitrix24.LicenseInfoPopup.show(popupId, title, content, showDemoButton);
		}
	}
};

function showPartnerForm(arParams)
{
	BX = window.BX;
	BX.Bitrix24PartnerForm =
	{
		bInit: false,
		popup: null,
		arParams: {}
	};
	BX.Bitrix24PartnerForm.arParams = arParams;
	BX.message(arParams['MESS']);
	BX.Bitrix24PartnerForm.popup = BX.PopupWindowManager.create("BXPartner", null, {
		autoHide: false,
		zIndex: 0,
		offsetLeft: 0,
		offsetTop: 0,
		overlay : true,
		draggable: {restrict:true},
		closeByEsc: true,
		titleBar: BX.message('BX24_PARTNER_TITLE'),
		closeIcon: { right : "12px", top : "10px"},
		buttons: [
			new BX.PopupWindowButtonLink({
				text: BX.message('BX24_CLOSE_BUTTON'),
				className: "popup-window-button-link-cancel",
				events: { click : function()
				{
					this.popupWindow.close();
				}}
			})
		],
		content: '<div style="width:450px;height:230px"></div>',
		events: {
			onAfterPopupShow: function()
			{
				this.setContent('<div style="width:450px;height:230px">'+BX.message('BX24_LOADING')+'</div>');
				BX.ajax.post(
					'/bitrix/tools/b24_site_partner.php',
					{
						lang: BX.message('LANGUAGE_ID'),
						site_id: BX.message('SITE_ID') || '',
						arParams: BX.Bitrix24PartnerForm.arParams
					},
					BX.delegate(function(result)
						{
							this.setContent(result);
						},
						this)
				);
			}
		}
	});

	BX.Bitrix24PartnerForm.popup.show();
}

/****************** Timemanager *********************/
B24.Timemanager = {

	inited : false,

	layout : {
		block : null,
		timer : null,
		info : null,
		event : null,
		tasks : null,
		status : null
	},

	data : null,
	timer : null,
	clock : null,

	formatTime : function(ts, bSec)
	{
		return BX.util.str_pad(parseInt(ts/3600), 2, '0', 'left')+':'+BX.util.str_pad(parseInt(ts%3600/60), 2, '0', 'left')+(!!bSec ? (':'+BX.util.str_pad(ts%60, 2, '0', 'left')) : '');
	},

	formatWorkTime : function(h, m, s)
	{
		return '<span class="tm-popup-notice-time-hours"><span class="tm-popup-notice-time-number">' + h + '</span></span><span class="tm-popup-notice-time-minutes"><span class="tm-popup-notice-time-number">' + BX.util.str_pad(m, 2, '0', 'left') + '</span></span><span class="tm-popup-notice-time-seconds"><span class="tm-popup-notice-time-number">' + BX.util.str_pad(s, 2, '0', 'left') + '</span></span>';
	},

	formatCurrentTime : function(hours, minutes, seconds)
	{
		var mt = "";
		if (BX.isAmPmMode())
		{
			mt = "AM";
			if (hours > 12)
			{
				hours = hours - 12;
				mt = "PM";
			}
			else if (hours == 0)
			{
				hours = 12;
				mt = "AM";
			}
			else if (hours == 12)
			{
				mt = "PM";
			}

			mt = '<span class="time-am-pm">' + mt + '</span>';
		}
		else
			hours = BX.util.str_pad(hours, 2, "0", "left");

		return '<span class="time-hours">' + hours + '</span>' +
			'<span class="time-semicolon">:</span>' +
			'<span class="time-minutes">' + BX.util.str_pad(minutes, 2, "0", "left") + '</span>' +
			mt;
	},

	init : function(reportJson)
	{
		BX.addCustomEvent("onTimeManDataRecieved", BX.proxy(this.onDataRecieved, this));
		BX.addCustomEvent("onTimeManNeedRebuild", BX.proxy(this.onDataRecieved, this));
		BX.addCustomEvent("onPlannerDataRecieved", BX.proxy(this.onPlannerDataRecieved, this));
		BX.addCustomEvent("onPlannerQueryResult", BX.proxy(this.onPlannerQueryResult, this));
		BX.addCustomEvent("onTaskTimerChange", BX.proxy(this.onTaskTimerChange, this));

		BX.timer.registerFormat("worktime_notice_timeman",BX.proxy(this.formatWorkTime, this));
		BX.timer.registerFormat("bitrix24_time",BX.proxy(this.formatCurrentTime, this));

		BX.addCustomEvent(window, "onTimemanInit", BX.proxy(function() {

			this.inited = true;

			this.layout.block = BX("timeman-block");
			this.layout.timer = BX("timeman-timer");
			this.layout.info = BX("timeman-info");
			this.layout.event = BX("timeman-event");
			this.layout.tasks = BX("timeman-tasks");
			this.layout.status = BX("timeman-status");
			this.layout.statusBlock = BX("timeman-status-block");
			this.layout.taskTime = BX("timeman-task-time");
			this.layout.taskTimer = BX("timeman-task-timer");

			window.BXTIMEMAN.ShowFormWeekly(reportJson);

			BX.bind(this.layout.block, "click", BX.proxy(this.onTimemanClick, this));

			BXTIMEMAN.setBindOptions({
				node: this.layout.block,
				mode: "popup",
				popupOptions: {
					angle : { position : "top", offset : 130},
					offsetTop : 10,
					autoHide : true,
					offsetLeft : -60,
					zIndex : -1,
					events : {
						onPopupClose : BX.proxy(function() {
							BX.removeClass(this.layout.block, "timeman-block-active");
						}, this)
					}
				}
			});

			this.redraw();

		}, this));
	},

	onTimemanClick : function()
	{
		BX.addClass(this.layout.block, "timeman-block-active");
		BXTIMEMAN.Open();
	},

	onTaskTimerChange : function(params)
	{
		if (params.action === 'refresh_daemon_event')
		{
			if(!!this.taskTimerSwitch)
			{
				this.layout.taskTime.style.display = '';
				if(this.layout.info.style.display != 'none')
				{
					this.layout.statusBlock.style.display = 'none';
				}
				this.taskTimerSwitch = false;
			}

			var s = '';
			s += this.formatTime(parseInt(params.data.TIMER.RUN_TIME||0) + parseInt(params.data.TASK.TIME_SPENT_IN_LOGS||0), true);

			if(!!params.data.TASK.TIME_ESTIMATE && params.data.TASK.TIME_ESTIMATE > 0)
			{
				s += ' / ' + this.formatTime(parseInt(params.data.TASK.TIME_ESTIMATE));
			}

			this.layout.taskTimer.innerHTML = s;
		}
		else if(params.action === 'start_timer')
		{
			this.taskTimerSwitch = true;
		}
		else if(params.action === 'stop_timer')
		{
			this.layout.taskTime.style.display = 'none';
			this.layout.statusBlock.style.display = '';
		}
	},

	setTimer : function()
	{
		if (this.timer)
		{
			this.timer.setFrom(new Date(this.data.INFO.DATE_START * 1000));
			this.timer.dt = -this.data.INFO.TIME_LEAKS * 1000;
		}
		else
		{
			this.timer = BX.timer(this.layout.timer, {
				from: new Date(this.data.INFO.DATE_START*1000),
				dt: -this.data.INFO.TIME_LEAKS * 1000,
				display: "simple"
			});
		}
	},

	stopTimer : function()
	{
		if (this.timer != null)
		{
			BX.timer.stop(this.timer);
			this.timer = null;
		}
	},

	redraw_planner: function(data)
	{
		if(!!data.TASKS_ENABLED)
		{
			data.TASKS_COUNT = !data.TASKS_COUNT ? 0 : data.TASKS_COUNT;
			this.layout.tasks.innerHTML = data.TASKS_COUNT;
			this.layout.tasks.style.display = data.TASKS_COUNT == 0 ? "none" : "inline-block";
		}

		if(!!data.CALENDAR_ENABLED)
		{
			this.layout.event.innerHTML = data.EVENT_TIME;
			this.layout.event.style.display = data.EVENT_TIME == '' ? 'none' : 'inline-block';
		}

		this.layout.info.style.display =
			(BX.style(this.layout.tasks, "display") == 'none' && BX.style(this.layout.event, "display") == 'none')
				? 'none'
				: 'block';
	},

	redraw : function()
	{
		this.redraw_planner(this.data.PLANNER);

		if (this.data.STATE == "CLOSED" && (this.data.CAN_OPEN == "REOPEN" || !this.data.CAN_OPEN))
			this.layout.status.innerHTML = this.getStatusName("COMPLETED");
		else
			this.layout.status.innerHTML = this.getStatusName(this.data.STATE);

		// if (this.data.STATE == "OPENED")
		// 	this.setTimer();
		// else
		// {
		// 	this.stopTimer();
		// 	var workedTime = (this.data.INFO.DATE_FINISH - this.data.INFO.DATE_START - this.data.INFO.TIME_LEAKS);
		// 	this.layout.timer.innerHTML = BX.timeman.formatTime(workedTime);
		// }
		if (!this.timer)
			this.timer = BX.timer({container: this.layout.timer, display : "bitrix24_time"}); //BX.timer.clock(this.layout.timer);

		var statusClass = "";
		if (this.data.STATE == "CLOSED")
		{
			if (this.data.CAN_OPEN == "REOPEN" || !this.data.CAN_OPEN)
				statusClass = "timeman-completed";
			else
				statusClass = "timeman-start";
		}
		else if (this.data.STATE == "PAUSED")
			statusClass = "timeman-paused";
		else if (this.data.STATE == "EXPIRED")
			statusClass = "timeman-expired";

		BX.removeClass(this.layout.block, "timeman-completed timeman-start timeman-paused timeman-expired");
		BX.addClass(this.layout.block, statusClass);

		if (statusClass == "timeman-start" || statusClass == "timeman-paused")
		{
			this.startAnimation();
		}
		else
		{
			this.endAnimation();
		}
	},

	getStatusName : function(id)
	{
		return BX.message("TM_STATUS_" + id);
	},

	onDataRecieved : function(data)
	{
		data.OPEN_NOW = false;

		this.data = data;

		if (this.inited)
			this.redraw();
	},

	onPlannerQueryResult : function(data, action)
	{
		if (this.inited)
			this.redraw_planner(data);
	},

	onPlannerDataRecieved : function(ob, data)
	{
		if (this.inited)
			this.redraw_planner(data);
	},

	animation : null,
	animationTimeout : 30000,
	blinkAnimation : null,
	blinkLimit : 10,
	blinkTimeout : 750,

	startAnimation : function()
	{
		if (this.animation !== null)
		{
			this.endAnimation();
		}

		this.startBlink();
		this.animation = setInterval(BX.proxy(this.startBlink, this), this.animationTimeout);
	},

	endAnimation : function()
	{
		this.endBlink();

		if (this.animation)
		{
			clearInterval(this.animation);
		}

		this.animation = null;
	},

	startBlink : function()
	{
		if (this.blinkAnimation !== null)
		{
			this.endBlink();
		}

		var counter = 0;
		this.blinkAnimation = setInterval(BX.proxy(function()
		{
			if (++counter >= this.blinkLimit)
			{
				clearInterval(this.blinkAnimation);
				BX.show(BX("timeman-background", true));
			}
			else
			{
				BX.toggle(BX("timeman-background", true));
			}

		}, this), this.blinkTimeout);
	},

	endBlink : function()
	{
		if (this.blinkAnimation)
		{
			clearInterval(this.blinkAnimation);
		}

		BX("timeman-background", true).style.cssText = "";
		this.blinkAnimation = null;
	}
};

/****************** Invite Dialog *******************/
B24.Bitrix24InviteDialog =
{
	bInit: false,
	popup: null,
	arParams: {}
};

B24.Bitrix24InviteDialog.Init = function(arParams)
{
	if(arParams)
		B24.Bitrix24InviteDialog.arParams = arParams;

	if(B24.Bitrix24InviteDialog.bInit)
		return;

	BX.message(arParams['MESS']);

	B24.Bitrix24InviteDialog.bInit = true;

	BX.ready(BX.delegate(function()
	{
		B24.Bitrix24InviteDialog.popup = BX.PopupWindowManager.create("B24InviteDialog", null, {
			autoHide: false,
			zIndex: 0,
			offsetLeft: 0,
			offsetTop: 0,
			overlay:true,
			draggable: {restrict:true},
			closeByEsc: true,
			titleBar: BX.message('BX24_INVITE_TITLE_INVITE'),
			contentColor: "white",
			contentNoPaddings: true,
			closeIcon: { right : "12px", top : "10px"},
			buttons: [
			],
			className: 'bx-b24-invite-dialog-popup',
			content: '<div style="width:500px;height:300px; background: url(/bitrix/templates/bitrix24/images/loader.gif) no-repeat center;"></div>',
			events: {
				onAfterPopupShow: function()
				{
					B24.Bitrix24InviteDialog.loadForm();
				},
				onPopupClose: function()
				{
					BX.InviteDialog.onInviteDialogClose();
				}
			}
		});
	}, this));
};

B24.Bitrix24InviteDialog.ShowForm = function(arParams)
{
	B24.Bitrix24InviteDialog.Init(arParams);
	B24.Bitrix24InviteDialog.popup.params.zIndex = (BX.WindowManager? BX.WindowManager.GetZIndex() : 0);
	B24.Bitrix24InviteDialog.popup.show();
};

B24.Bitrix24InviteDialog.loadForm = function()
{
	B24.Bitrix24InviteDialog.popup.setContent('<div style="width:500px;height:300px; background: url(/bitrix/templates/bitrix24/images/loader.gif) no-repeat center;"></div>');
	BX.ajax.post(
		'/bitrix/tools/intranet_invite_dialog.php',
		{
			lang: BX.message('LANGUAGE_ID'),
			site_id: BX.message('SITE_ID') || '',
			arParams: B24.Bitrix24InviteDialog.arParams
		},
		BX.delegate(function(result)
			{
				B24.Bitrix24InviteDialog.popup.setContent(result);
				B24.Bitrix24InviteDialog.popup.adjustPosition();
			},
			this)
	);
};

B24.Bitrix24InviteDialog.ReInvite = function(reinvite_user_id)
{
	BX.ajax.post(
		'/bitrix/tools/intranet_invite_dialog.php',
		{
			lang: BX.message('LANGUAGE_ID'),
			site_id: BX.message('SITE_ID') || '',
			reinvite: reinvite_user_id,
			sessid: BX.bitrix_sessid()
		},
		BX.delegate(function(result)
			{
			},
			this)
	);
};

B24.connectionStatus = function(status)
{
	if (!(status == 'online' || status == 'connecting' || status == 'offline'))
		return false;

	if (this.b24ConnectionStatusState == status)
		return false;

	this.b24ConnectionStatusState = status;

	var statusClass = '';

	if (status == 'offline')
	{
		b24ConnectionStatusStateText = BX.message('BITRIX24_CS_OFFLINE');
		statusClass = 'bx24-connection-status-offline';
	}
	else if (status == 'connecting')
	{
		b24ConnectionStatusStateText = BX.message('BITRIX24_CS_CONNECTING');
		statusClass = 'bx24-connection-status-connecting';
	}
	else if (status == 'online')
	{
		b24ConnectionStatusStateText = BX.message('BITRIX24_CS_ONLINE');
		statusClass = 'bx24-connection-status-online';
	}

	clearTimeout(this.b24ConnectionStatusTimeout);

	var connectionPopup = document.querySelector('[data-role="b24-connection-status"]');
	if (!connectionPopup)
	{
		var windowScroll = BX.GetWindowScrollPos();
		var isFloat = windowScroll.scrollTop > 60;

		this.b24ConnectionStatus = BX.create("div", {
			attrs : {
				className : "bx24-connection-status "+(this.b24ConnectionStatusState == 'online'? "bx24-connection-status-hide": "bx24-connection-status-show bx24-connection-status-"+this.b24ConnectionStatusState)+(isFloat? " bx24-connection-status-float": ""),
				"data-role" : "b24-connection-status",
				"data-float" : isFloat? "true": "false"
			},
			children : [
				BX.create("div", { props : { className : "bx24-connection-status-wrap" }, children : [
					this.b24ConnectionStatusText = BX.create("span", { props : { className : "bx24-connection-status-text"}, html: b24ConnectionStatusStateText}),
					BX.create("span", { props : { className : "bx24-connection-status-text-reload"}, children : [
						BX.create("span", { props : { className : "bx24-connection-status-text-reload-title"}, html: BX.message('BITRIX24_CS_RELOAD')}),
						BX.create("span", { props : { className : "bx24-connection-status-text-reload-hotkey"}, html: (BX.browser.IsMac()? "&#8984;+R": "Ctrl+R")})
					], events: {
						'click': function(){ location.reload() }
					}})
				]})
			]
		});
	}
	else
	{
		this.b24ConnectionStatus = connectionPopup;
	}

	if (!this.b24ConnectionStatus)
		return false;

	if (status == 'online')
	{
		clearTimeout(this.b24ConnectionStatusTimeout);
		this.b24ConnectionStatusTimeout = setTimeout(BX.delegate(function(){
			BX.removeClass(this.b24ConnectionStatus, "bx24-connection-status-show");
			this.b24ConnectionStatusTimeout = setTimeout(BX.delegate(function(){
				BX.removeClass(this.b24ConnectionStatus, "bx24-connection-status-hide");
			}, this), 1000);
		}, this), 4000);
	}

	this.b24ConnectionStatus.className = "bx24-connection-status bx24-connection-status-show "+statusClass+" "+(this.b24ConnectionStatus.getAttribute('data-float') == 'true'? 'bx24-connection-status-float': '');
	this.b24ConnectionStatusText.innerHTML = b24ConnectionStatusStateText;

	if (!connectionPopup)
	{
		var nextNode = BX.findChild(document.body, {className: "bx-layout-inner-table"}, true, false);
		nextNode.parentNode.insertBefore(this.b24ConnectionStatus, nextNode);
	}

	return true;
};

B24.showPartnerOrderForm = function (params)
{
	if (typeof params !== "object")
		return;

	BX.PopupWindowManager.create("B24PartnerOrderForm", null, {
		autoHide: true,
		zIndex: 0,
		offsetLeft: 0,
		offsetTop: 0,
		overlay: true,
		height: Math.min(document.documentElement.clientHeight - 100, 740),
		width: 550,
		draggable: {restrict:true},
		closeByEsc: true,
		contentColor: "white",
		contentNoPaddings: true,
		closeIcon: { right : "5px", top : "10px"},
		content:
			'<script data-b24-form="inline/'+params.id+'/'+params.sec+'" data-skip-moving="true">'+
				'(function(w,d,u){'+
					'var s=d.createElement("script");s.async=true;s.src=u+"?"+(Date.now()/180000|0);'+
					'var h=d.getElementsByTagName("script")[0];h.parentNode.insertBefore(s,h);'+
				'})(window,document,"https://cp.bitrix.ru/upload/crm/form/loader_${params.id}_${params.sec}.js");'+
			'</script>',
		events: {
			onPopupFirstShow: function()
			{
				(function(w,d,u){
					var s=d.createElement('script');s.async=true;s.src=u+'?'+(Date.now()/180000|0);
					var h=d.getElementsByTagName('script')[0];h.parentNode.insertBefore(s,h);
				})(window,document,'https://cp.bitrix.ru/upload/crm/form/loader_'+params.id+'_'+params.sec+'.js')
			}
		}
	}).show();
};

B24.upgradeButtonRedirect = function(params)
{
	if (typeof params !== "object")
		return;

	var url = params.COUNTER_URL || "",
		licensePath = params.LICENSE_PATH || "",
		host = params.HOST || "";

	BX.ajax.post(
		url,
		{
			action: "upgradeButton",
			host: host
		},
		BX.proxy(function(){
			document.location.href = licensePath;
		}, this)
	);
}

/* End */
;; /* /bitrix/components/bitrix/tasks.iframe.popup/templates/.default/logic.min.js?16074881537131*/
; /* /bitrix/components/bitrix/search.title/script.min.js?16074881606443*/
; /* /bitrix/templates/bitrix24/components/bitrix/search.title/.default/script.min.js?160748813228167*/
; /* /bitrix/templates/bitrix24/components/bitrix/menu/left_vertical/map.min.js?160748813210943*/
; /* /bitrix/templates/bitrix24/components/bitrix/menu/left_vertical/script.min.js?160748813266724*/
; /* /bitrix/templates/bitrix24/components/bitrix/im.messenger/.default/script.min.js?16074881324178*/
; /* /bitrix/components/bitrix/ui.toolbar/templates/.default/script.min.js?16074881593831*/
; /* /bitrix/templates/bitrix24/bitrix24.js?160748813228801*/
; /* /bitrix/components/bitrix/main.interface.buttons/templates/.default/script.min.js?160748815244683*/
; /* /bitrix/components/bitrix/main.interface.buttons/templates/.default/utils.min.js?1607488152575*/

//# sourceMappingURL=template_bx24.map.js