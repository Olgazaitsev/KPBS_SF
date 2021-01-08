function JCCalendarViewYear()
{
	this.ID = 'year';
	this._parent = null;

	this.SETTINGS = {};
	this.ENTRIES = [];

	//if (!window._MONTH_STYLE_LOADED)
	//{
		BX.loadCSS('/local/templates/.default/components/bitrix/intranet.absence.calendar.view/month/view.css');
		window._MONTH_STYLE_LOADED = true;
	//}


	BX.bind(window, 'resize', BX.proxy(this.__onresize, this));
}

JCCalendarViewYear.prototype.__onresize = function ()
{
	this.UnloadData(true);
	this.__drawData();
}

/*
intraface:
ID - property with view id. must be equal to ID in RegisterView input params.
_parent - property with link to global calendar object

Load - method running when it's needed to draw view interface
Unload - method running when switching to another interface. do not change innerHTML here, it will be empty yet
LoadData(DATA) - method running when it's needed to draw absences data
UnloadData() - method running when it's needed to clear absences data. this method is also being used inside the view for the purpose of cleaning and rewriting current data on window resize
SetSettings(SETTINGS) - recieve global settings
*/

JCCalendarViewYear.prototype.Load = function()
{
	this._parent.FILTER.SHORT_EVENTS = 'N';

	if (null != this.ENTRIES && this.ENTRIES.length > 0) this.UnloadData();

	this.__drawLayout();

	this.TYPE_BGCOLORS = this._parent.TYPE_BGCOLORS;

	this._parent.LoadData(
		this.SETTINGS.DATE_START,
		this.SETTINGS.DATE_FINISH
	);
}

JCCalendarViewYear.prototype.SetSettings = function (SETTINGS)
{
	this.SETTINGS = SETTINGS;

	today = new Date();

	if (null == this.SETTINGS.DATE_START || today >= this.SETTINGS.DATE_START && today <= this.SETTINGS.DATE_FINISH)
		this.SETTINGS.DATE_START = today
	this.SETTINGS.DATE_START.setMonth(0, 1);
	//this.SETTINGS.DATE_START.setDate(1);
	this.SETTINGS.DATE_START.setHours(0);
	this.SETTINGS.DATE_START.setMinutes(0);
	this.SETTINGS.DATE_START.setSeconds(0);
	this.SETTINGS.DATE_START.setMilliseconds(0);

	this.SETTINGS.DATE_FINISH = new Date(this.SETTINGS.DATE_START.valueOf());
	this.SETTINGS.DATE_FINISH.setMonth(this.SETTINGS.DATE_FINISH.getMonth()+12, 1);
}

JCCalendarViewYear.prototype.Unload = function()
{
	this._parent.FILTER.SHORT_EVENTS = 'Y';
	BX.unbind(window, 'resize', BX.proxy(this.__onresize, this));
	this.UnloadData();
}

JCCalendarViewYear.prototype.UnloadData = function(bClearOnlyVisual)
{
	if (null == this.ENTRIES)
		return;

	if (null == bClearOnlyVisual) bClearOnlyVisual = false;

	for (var i = 0; i < this.ENTRIES.length; i++)
	{
		if (null != this.ENTRIES[i].DATA)
		{
			for (var j = 0; j < this.ENTRIES[i].DATA.length; j++)
			{
				if (null == this.ENTRIES[i].DATA[j].VISUAL) continue;

				this._parent.UnRegisterEntry(this.ENTRIES[i].DATA[j]);
				BX.cleanNode(this.ENTRIES[i].DATA[j].VISUAL, true);
				this.ENTRIES[i].DATA[j].VISUAL = null;
			}
		}

		var obRow = BX('bx_calendar_user_' + this.ENTRIES[i]['ID']);
		if (null != obRow) {obRow.parentNode.removeChild(obRow); delete obRow;}
	}

	if (!bClearOnlyVisual) this.ENTRIES = null;
}

JCCalendarViewYear.prototype.LoadData = function(DATA)
{
	this.ENTRIES = DATA;
	if (BX.browser.IsIE())
		setTimeout(BX.proxy(this.__drawData, this), 10);
	else
		this.__drawData();
}

JCCalendarViewYear.prototype.changeMonth = function(dir)
{
	//if (dir != -1) dir = 1;

	this.SETTINGS.DATE_START.setMonth(this.SETTINGS.DATE_START.getMonth() + dir);

	this.SETTINGS.DATE_FINISH = new Date(this.SETTINGS.DATE_START);
	this.SETTINGS.DATE_FINISH.setMonth(this.SETTINGS.DATE_FINISH.getMonth() + 1)

	this.Load();
}

JCCalendarViewYear.prototype.changeYear = function(dir)
{
	if (dir != -1) dir = 1;

	this.SETTINGS.DATE_START.setYear(this.SETTINGS.DATE_START.getFullYear() + dir);
	this.SETTINGS.DATE_FINISH = new Date(this.SETTINGS.DATE_START);
	this.SETTINGS.DATE_FINISH.setMonth(this.SETTINGS.DATE_FINISH.getMonth() + 12)

	this.Load();
}

JCCalendarViewYear.prototype.__drawLayout = function()
{
	var _this = this;

	var today = new Date();
	today.setHours(0);
	today.setMinutes(0);
	today.setSeconds(0);
	today.setMilliseconds(0);

	this._parent.CONTROLS.CALENDAR.innerHTML = '';

	this.obTable = document.createElement('TABLE');
	this.obTable.className = 'bx-calendar-period-main-table';
	this.obTable.setAttribute('cellSpacing', '0');

	this._parent.CONTROLS.CALENDAR.appendChild(this.obTable);

	//this.obTable.appendChild(document.createElement('THEAD'));
	this.obTable.appendChild(document.createElement('TBODY'));

	// generate controls
	var obRow = this.obTable.tBodies[0].insertRow(-1);

	obRow.insertCell(-1);

	obRow.cells[0].className = 'bx-calendar-empty';
	obRow.cells[0].innerHTML = '&nbsp;';

	var cur_m = this.SETTINGS.DATE_START.getMonth();
	//obRow.cells[0].innerHTML +=
	this._parent.CONTROLS.DATEROW.innerHTML =
		'<table class="bx-calendar-month-control-table" align="center"><tr>' +
		//'<td><a href="javascript:void(0)" class="bx-calendar-month-icon bx-calendar-month-bback"></a></td>' +
		'<td class="bx-calendar-month-control-text"><a href="javascript:void(0)" class="bx-calendar-month-change2">' + this._parent.MONTHS[cur_m-2 < 0 ? cur_m+10 : cur_m-2] + '</a></td>' +
		'<td class="bx-calendar-month-control-text"><a href="javascript:void(0)" class="bx-calendar-month-change1">' + this._parent.MONTHS[cur_m-1 < 0 ? cur_m+11 : cur_m-1] + '</a></td>' +
		'<td><a href="javascript:void(0)" class="bx-calendar-month-icon bx-calendar-month-back"></a></td>' +
		'<td class="bx-calendar-month-control-text">' +
		//this._parent.MONTHS[this.SETTINGS.DATE_START.getMonth()] + ', ' + this.SETTINGS.DATE_START.getFullYear() +
		+ this.SETTINGS.DATE_START.getFullYear() +
		'</td>' +
		'<td><a href="javascript:void(0)" class="bx-calendar-month-icon bx-calendar-month-fwd"></a></td>' +
		'<td class="bx-calendar-month-control-text"><a href="javascript:void(0)" class="bx-calendar-month-change1">' + this._parent.MONTHS[cur_m+1 > 11 ? cur_m-11 : cur_m+1] + '</a></td>' +
		'<td class="bx-calendar-month-control-text"><a href="javascript:void(0)" class="bx-calendar-month-change2">' + this._parent.MONTHS[cur_m+2 > 11 ? cur_m-10 : cur_m+2] + '</a></td>' +
		//'<td><a href="javascript:void(0)" class="bx-calendar-month-icon bx-calendar-month-ffwd"></a></td>' +
		'</tr></table>';

	//var arLinks = obRow.cells[0].getElementsByTagName('A');
	var arLinks = this._parent.CONTROLS.DATEROW.getElementsByTagName('A');
	//arLinks[0].onclick = function() {_this.changeYear(-1)}
	arLinks[0].onclick = function() {_this.changeYear(-2)}
	arLinks[1].onclick = function() {_this.changeYear(-1)}
	arLinks[2].onclick = function() {_this.changeYear(-1)}
	arLinks[3].onclick = function() {_this.changeYear(1)}
	arLinks[4].onclick = function() {_this.changeYear(1)}
	arLinks[5].onclick = function() {_this.changeYear(2)}
	//arLinks[3].onclick = function() {_this.changeYear(1)}

	// generate dating cols
	var startMonth = this.SETTINGS.DATE_START.getMonth();
	var cur_date = new Date(this.SETTINGS.DATE_START.valueOf());
	var bDayViewRegistered = this._parent.isViewRegistered('day');
	var bMonthViewRegistered = this._parent.isViewRegistered('month');
	var cur_year = cur_date.getFullYear();
	while (cur_date.getFullYear() == cur_year) {
		var obCell = obRow.insertCell(-1);
		obCell.className = 'bx-calendar-month-day';
		obCell.className += ' bx-calendar-month-today';
		var cur_month = this._parent.MONTHS[cur_date.getMonth()];
		if(bMonthViewRegistered)
		{
			var obLink = obCell.appendChild(document.createElement('A'));
			obLink.href = "javascript:void(0)";
			obLink.BX_DAY = new Date(cur_date.valueOf());
			obLink.onclick = function()
			{
				_this.SETTINGS.DATE_START = this.BX_DAY;
				_this.SETTINGS.DATE_FINISH = this.BX_DAY;
				_this._parent.SetView('month');
			}
			obLink.innerHTML = cur_month;
		} else {
			obCell.innerHTML = cur_month;
		}

		cur_date.setMonth(cur_date.getMonth() + 1);
	}


	this._parent.CONTROLS.CALENDAR.appendChild(document.createElement('BR'));
	this._parent.CONTROLS.CALENDAR.appendChild(document.createElement('BR'));

	this.obPageBar = document.createElement('DIV');
	this.obPageBar.style.padding = '10px 10px 3px';
	this.obPageBar.style.fontSize = '0.95em';
	this._parent.CONTROLS.CALENDAR.appendChild(this.obPageBar);

	this._parent.CONTROLS.CALENDAR.appendChild(document.createElement('BR'));
	this._parent.CONTROLS.CALENDAR.appendChild(document.createElement('BR'));
	var obDiv = this._parent.CONTROLS.CALENDAR.appendChild(document.createElement('SPAN'));
	obDiv.className = 'bx-month-note';
	obDiv.innerHTML = this._parent.MESSAGES.INTR_ABSC_TPL_WARNING_MONTH;
	this._parent.CONTROLS.CALENDAR.appendChild(document.createElement('BR'));
	this._parent.CONTROLS.CALENDAR.appendChild(document.createElement('BR'));
}

JCCalendarViewYear.prototype.__drawData = function()
{
	var _this = this;

	var startMonth = this.SETTINGS.DATE_START.getMonth();
	var date_start = this.SETTINGS.DATE_START;
	var date_finish = new Date(date_start);
	date_finish.setMonth(date_finish.getMonth() + 12);
	date_finish.setSeconds(date_finish.getSeconds() - 1);

	for (var i = 0; i < (null == this.ENTRIES ? 0 : this.ENTRIES.length); i++)
	{
		// check user of actual range of absence
		for (var j = 0; j < this.ENTRIES[i]['DATA'].length; j++)
		{
			var ts_start = this.ENTRIES[i]['DATA'][j]['DATE_ACTIVE_FROM'];
			var ts_finish = this.ENTRIES[i]['DATA'][j]['DATE_ACTIVE_TO'];

			if (date_start.valueOf() > ts_finish.valueOf() || date_finish.valueOf() < ts_start.valueOf())
			{
				this.ENTRIES[i]['DATA'].splice(j, 1);
				j--;
			}
		}

		if (this.ENTRIES[i]['DATA'].length == 0 && !this._parent.FILTER.USERS_ALL)
		{
			continue;
		}

		var obRow = this.obTable.tBodies[0].insertRow(-1);

		//obRow.onmouseover = jsBXAC._hightlightRow;
		//obRow.onmouseout = jsBXAC._unhightlightRow;

		obRow.insertCell(-1);
		obRow.cells[0].className = 'bx-calendar-month-first-col';

		obRow.id = 'bx_calendar_user_' + this.ENTRIES[i]['ID'];

		var obNameContainer = obRow.cells[0].appendChild(document.createElement('DIV'));

		var strName = this._parent.FormatName(this.SETTINGS.NAME_TEMPLATE, this.ENTRIES[i]);

		obNameContainer.title = strName;

		if (this.ENTRIES[i]['DETAIL_URL'])
		{
			var obName = document.createElement('A');
			obName.appendChild(document.createTextNode(strName));
			obName.href = this.ENTRIES[i]['DETAIL_URL'];
		}
		else
		{
			var obName = document.createTextNode(strName);
		}

		obNameContainer.appendChild(obName);

		var tmp_date = new Date(this.SETTINGS.DATE_START.valueOf());
		var today = new Date();
		today.setHours(0);
		today.setMinutes(0);
		today.setSeconds(0);
		today.setMilliseconds(0);

		var cur_yearnew = tmp_date.getFullYear();

		while (tmp_date.getFullYear() == cur_yearnew) {
			var obCell = obRow.insertCell(-1);
			obCell.title = obNameContainer.title;
			obCell.className = 'bx-calendar-month-day';
			if (BX.browser.IsIE())
				obCell.innerHTML = '&nbsp;';

			tmp_date.setMonth(tmp_date.getMonth() + 1);

		}


	}

	var padding = 2, obPos, startOffset, finishOffset, start_pos, finish_pos, width, obFirstcell, ofFirstpos, leftFirst, allWidth, dateDiffer, height;

    dateDiffer = this.dateDiffer(date_finish, date_start);


	for (var i = 0; i < (null == this.ENTRIES ? 0 : this.ENTRIES.length); i++)
	{
		var obUserRow = BX('bx_calendar_user_' + this.ENTRIES[i]['ID']);

        obFirstcell = obUserRow.cells[1];
        ofFirstpos = BX.pos(obFirstcell, true);
        leftFirst = parseInt(ofFirstpos.left);
        allWidth = parseInt(ofFirstpos.width)*12;
        height = parseInt(ofFirstpos.height);

		if (obUserRow && this.ENTRIES[i]['DATA'])
		{
			var obRowPos = BX.pos(obUserRow, true);

			for (var j = 0; j < this.ENTRIES[i]['DATA'].length; j++)
			{
				var ts_start = this.ENTRIES[i]['DATA'][j]['DATE_ACTIVE_FROM'],
					ts_finish = this.ENTRIES[i]['DATA'][j]['DATE_ACTIVE_TO'];

                if(ts_start.valueOf()<date_start.valueOf()) {
                    ts_start = date_start;
                }

                if(ts_finish.valueOf()>date_finish.valueOf()) {
                    ts_finish = date_finish;
                }

				this.ENTRIES[i]['DATA'][j].VISUAL = document.createElement('DIV');

				this.ENTRIES[i]['DATA'][j].VISUAL.bx_color_variant = this.ENTRIES[i]['DATA'][j]['TYPE'].length ? this.ENTRIES[i]['DATA'][j]['TYPE'] : 'OTHER';

				this.ENTRIES[i]['DATA'][j].VISUAL.className = 'bx-calendar-entry bx-calendar-color-' + this.ENTRIES[i]['DATA'][j].VISUAL.bx_color_variant;
				this.ENTRIES[i]['DATA'][j].VISUAL.style.background = this.TYPE_BGCOLORS[(this.ENTRIES[i]['DATA'][j]['TYPE'].length ? this.ENTRIES[i]['DATA'][j]['TYPE'] : 'OTHER')];

				this.ENTRIES[i]['DATA'][j].VISUAL.innerHTML =
					'<nobr>'
					+ BX.util.htmlspecialchars(this.ENTRIES[i]['DATA'][j]['NAME'])
					+ ' (' + this.ENTRIES[i]['DATA'][j]['DATE_FROM'] + ' - ' + this.ENTRIES[i]['DATA'][j]['DATE_TO'] + ')'
					+ '</nobr>';

				this.ENTRIES[i]['DATA'][j].VISUAL.__bx_user_id = this.ENTRIES[i]['ID'];

				this.ENTRIES[i]['DATA'][j].VISUAL.style.top = (obRowPos.top) + 'px';



                width = ((this.dateDiffer(ts_finish, ts_start) / dateDiffer) * allWidth);
                start_pos = leftFirst + ((this.dateDiffer(ts_start, date_start) / dateDiffer) * allWidth);

				this.ENTRIES[i]['DATA'][j].VISUAL.style.left = parseInt(start_pos) + 'px';
                this.ENTRIES[i]['DATA'][j].VISUAL.style.width = parseInt(width) + 'px';
				//this.ENTRIES[i]['DATA'][j].VISUAL.style.width = (isNaN(width) || width < 20 ? '20' : width) + 'px';
				//this.ENTRIES[i]['DATA'][j].VISUAL.style.height = parseInt(obPos.height - padding) + 'px';
                this.ENTRIES[i]['DATA'][j].VISUAL.style.height = parseInt(height) + 'px';
				this._parent.MAIN_LAYOUT.appendChild(this.ENTRIES[i]['DATA'][j].VISUAL);
				this._parent.RegisterEntry(this.ENTRIES[i].DATA[j]);
			}
		}
	}

	if (this.SETTINGS.PAGE_COUNT > 1 || this.SETTINGS.PAGE_NUMBER > 0)
	{
		this.obPageBar.innerHTML = this._parent.MESSAGES.INTR_ABSC_TPL_PAGE_BAR + ': ';

		for (var i = 0; i <= this.SETTINGS.PAGE_NUMBER; i++)
		{
			if (i == this.SETTINGS.PAGE_NUMBER)
			{
				var page_link = document.createTextNode(i+1);
			}
			else
			{
				var page_link = document.createElement('A');
				page_link.href = 'javascript:void(0);';
				page_link.innerHTML = i+1;
				page_link.onclick = (function(i)
				{
					return function()
					{
						_this.SETTINGS.PAGE_NUMBER = i;
						_this.Load();
					};
				})(i);
			}

			this.obPageBar.appendChild(page_link);
			this.obPageBar.appendChild(document.createTextNode(' '));

			if (i == 0 && this.SETTINGS.PAGE_NUMBER >= 6)
			{
				var page_link = document.createTextNode('...');
				this.obPageBar.appendChild(page_link);
				this.obPageBar.appendChild(document.createTextNode(' '));

				i = this.SETTINGS.PAGE_NUMBER-4;
			}
		}

		if (this.SETTINGS.PAGE_COUNT-1 > this.SETTINGS.PAGE_NUMBER)
		{
			var page_link = document.createElement('A');
			page_link.href = 'javascript:void(0);';
			page_link.innerHTML = this._parent.MESSAGES.INTR_ABSC_TPL_PAGE_NEXT;
			page_link.onclick = function()
			{
				_this.SETTINGS.PAGE_NUMBER += 1;
				_this.Load();
			};

			this.obPageBar.appendChild(page_link);
		}
	}
}

JCCalendarViewYear.prototype.dateDiffer = function(date1, date2) {
    var daysLag = Math.ceil(Math.abs(date2.getTime() - date1.getTime()) / (1000 * 3600 * 24));
    return daysLag;
}