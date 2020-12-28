<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
?>
<script type="text/javascript">
if (window.JCCalendarViewQuarter)
	jsBXAC.SetViewHandler(new JCCalendarViewQuarter());
else
	BX.loadScript(
		'/local/templates/.default/components/bitrix/intranet.absence.calendar.view/quarter/view.js',
		function() {jsBXAC.SetViewHandler(new JCCalendarViewQuarter())}
	);
</script>