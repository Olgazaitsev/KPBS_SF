<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
?>
<script type="text/javascript">
if (window.JCCalendarViewHalfyear)
	jsBXAC.SetViewHandler(new JCCalendarViewHalfyear());
else
	BX.loadScript(
		'/local/templates/.default/components/bitrix/intranet.absence.calendar.view/halfyear/view.js',
		function() {jsBXAC.SetViewHandler(new JCCalendarViewHalfyear())}
	);
</script>