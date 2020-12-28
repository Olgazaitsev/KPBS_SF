<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
?>
<script type="text/javascript">
if (window.JCCalendarViewYear)
	jsBXAC.SetViewHandler(new JCCalendarViewYear());
else
	BX.loadScript(
		'/local/templates/.default/components/bitrix/intranet.absence.calendar.view/year/view.js',
		function() {jsBXAC.SetViewHandler(new JCCalendarViewYear())}
	);
</script>