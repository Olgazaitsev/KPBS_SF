<?php
/**
 * Bitrix Framework
 * @package bitrix
 * @subpackage main
 * @copyright 2001-2013 Bitrix
 */

require_once(substr(__FILE__, 0, strlen(__FILE__) - strlen("/include.php"))."/bx_root.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/start.php");

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/classes/general/virtual_io.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/classes/general/virtual_file.php");

$application = \Bitrix\Main\Application::getInstance();
$application->initializeExtendedKernel(array(
	"get" => $_GET,
	"post" => $_POST,
	"files" => $_FILES,
	"cookie" => $_COOKIE,
	"server" => $_SERVER,
	"env" => $_ENV
));

//define global application object
$GLOBALS["APPLICATION"] = new CMain;

if(defined("SITE_ID"))
	define("LANG", SITE_ID);

if(defined("LANG"))
{
	if(defined("ADMIN_SECTION") && ADMIN_SECTION===true)
		$db_lang = CLangAdmin::GetByID(LANG);
	else
		$db_lang = CLang::GetByID(LANG);

	$arLang = $db_lang->Fetch();

	if(!$arLang)
	{
		throw new \Bitrix\Main\SystemException("Incorrect site: ".LANG.".");
	}
}
else
{
	$arLang = $GLOBALS["APPLICATION"]->GetLang();
	define("LANG", $arLang["LID"]);
}

if($arLang["CULTURE_ID"] == '')
{
	throw new \Bitrix\Main\SystemException("Culture not found, or there are no active sites or languages.");
}

$lang = $arLang["LID"];
if (!defined("SITE_ID"))
	define("SITE_ID", $arLang["LID"]);
define("SITE_DIR", $arLang["DIR"]);
define("SITE_SERVER_NAME", $arLang["SERVER_NAME"]);
define("SITE_CHARSET", $arLang["CHARSET"]);
define("FORMAT_DATE", $arLang["FORMAT_DATE"]);
define("FORMAT_DATETIME", $arLang["FORMAT_DATETIME"]);
define("LANG_DIR", $arLang["DIR"]);
define("LANG_CHARSET", $arLang["CHARSET"]);
define("LANG_ADMIN_LID", $arLang["LANGUAGE_ID"]);
define("LANGUAGE_ID", $arLang["LANGUAGE_ID"]);

$culture = \Bitrix\Main\Localization\CultureTable::getByPrimary($arLang["CULTURE_ID"], ["cache" => ["ttl" => CACHED_b_lang]])->fetchObject();

$context = $application->getContext();
$context->setLanguage(LANGUAGE_ID);
$context->setCulture($culture);

$request = $context->getRequest();
if (!$request->isAdminSection())
{
	$context->setSite(SITE_ID);
}

$application->start();

$GLOBALS["APPLICATION"]->reinitPath();

if (!defined("POST_FORM_ACTION_URI"))
{
	define("POST_FORM_ACTION_URI", htmlspecialcharsbx(GetRequestUri()));
}

$GLOBALS["MESS"] = array();
$GLOBALS["ALL_LANG_FILES"] = array();
IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/tools.php");
IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/classes/general/database.php");
IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/classes/general/main.php");
IncludeModuleLangFile(__FILE__);

error_reporting(COption::GetOptionInt("main", "error_reporting", E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR|E_PARSE) & ~E_STRICT & ~E_DEPRECATED);

if(!defined("BX_COMP_MANAGED_CACHE") && COption::GetOptionString("main", "component_managed_cache_on", "Y") <> "N")
{
	define("BX_COMP_MANAGED_CACHE", true);
}

require_once($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/filter_tools.php");
require_once($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/ajax_tools.php");

/*ZDUyZmZOWU2MjM3ODBlZTc1YThkYWVkOGFkMWNkYjk0ZTM0Yzg=*/$GLOBALS['_____659297627']= array(base64_decode('R2V0T'.'W9kdW'.'x'.'lR'.'XZlbnRz'),base64_decode('R'.'XhlY'.'3'.'V0ZU'.'1vZHVsZUV2ZW5'.'0R'.'Xg='));$GLOBALS['____933132382']= array(base64_decode('Z'.'GVma'.'W5l'),base64_decode(''.'c3Ry'.'b'.'GV'.'u'),base64_decode('YmFz'.'ZTY0X2RlY29kZQ='.'='),base64_decode('dW5'.'zZXJp'.'Y'.'Wxpem'.'U='),base64_decode('aXNfYX'.'JyYXk='),base64_decode(''.'Y291bn'.'Q='),base64_decode(''.'aW5'.'fYX'.'JyY'.'Xk='),base64_decode('c'.'2VyaWFsaXpl'),base64_decode('YmFzZTY'.'0X2'.'VuY2'.'9'.'kZQ'.'=='),base64_decode('c3'.'RybGVu'),base64_decode('YXJyY'.'Xlf'.'a2V5X'.'2V4'.'aXN0cw='.'='),base64_decode(''.'YX'.'J'.'y'.'YXlfa2V5X2V4a'.'X'.'N0cw='.'='),base64_decode('bWt0'.'a'.'W1l'),base64_decode(''.'ZGF'.'0ZQ=='),base64_decode(''.'Z'.'GF0ZQ=='),base64_decode('YXJyYXlfa2V'.'5X2V'.'4aXN0cw'.'='.'='),base64_decode('c3RybGVu'),base64_decode('YXJyYXlfa2V5X'.'2V4aX'.'N0cw=='),base64_decode('c3RybGVu'),base64_decode('YX'.'JyYXlfa'.'2'.'V'.'5X2V4aXN'.'0c'.'w=='),base64_decode('YXJyYXlfa2V5X2V4a'.'XN'.'0'.'cw=='),base64_decode('b'.'Wt'.'0aW1l'),base64_decode('ZG'.'F0Z'.'Q=='),base64_decode(''.'Z'.'GF0'.'ZQ'.'=='),base64_decode('bWV0a'.'G9kX2V'.'4aXN'.'0c'.'w'.'=='),base64_decode('Y2'.'Fs'.'bF91c'.'2Vy'.'X'.'2Z1b'.'m'.'NfYX'.'JyYX'.'k'.'='),base64_decode('c3RybGV'.'u'),base64_decode('YXJyYXlf'.'a2V5X'.'2V4a'.'XN0cw=='),base64_decode(''.'YXJyYXlfa2V5X2'.'V4aXN0'.'cw=='),base64_decode('c2'.'VyaW'.'Fs'.'aX'.'pl'),base64_decode(''.'Y'.'mFzZTY0'.'X2VuY29kZ'.'Q'.'='.'='),base64_decode('c3R'.'ybGVu'),base64_decode('YXJyYXlfa2V'.'5X2V'.'4aXN0cw'.'=='),base64_decode('Y'.'XJyYXlfa2V5'.'X'.'2V4aXN0cw='.'='),base64_decode('Y'.'XJy'.'YXlfa2V5X2'.'V4'.'aX'.'N0cw='.'='),base64_decode('aXN'.'fYXJyYXk='),base64_decode('YXJyYX'.'lfa2V5X'.'2V4aXN0c'.'w=='),base64_decode('c2V'.'yaWFsaXpl'),base64_decode('YmFzZTY0X2VuY29kZQ=='),base64_decode('YXJyYXlfa2'.'V5'.'X2V4a'.'X'.'N0cw=='),base64_decode('YXJy'.'YXlf'.'a2V5X2V4'.'aXN0cw=='),base64_decode('c2VyaW'.'Fsa'.'Xpl'),base64_decode('Y'.'mFzZTY0X'.'2VuY'.'29k'.'ZQ='.'='),base64_decode('aXNf'.'YXJy'.'YXk='),base64_decode('aX'.'NfYXJyYXk='),base64_decode('aW5fY'.'XJ'.'yYXk='),base64_decode(''.'YXJyYXl'.'fa2'.'V5X2V4aXN0'.'cw'.'=='),base64_decode('a'.'W5f'.'Y'.'XJy'.'YXk='),base64_decode('bWt0aW'.'1l'),base64_decode('ZGF'.'0'.'ZQ=='),base64_decode('ZGF0ZQ'.'=='),base64_decode('ZGF0'.'ZQ'.'=='),base64_decode(''.'bWt0aW1l'),base64_decode(''.'ZGF0Z'.'Q'.'=='),base64_decode('ZG'.'F0ZQ'.'=='),base64_decode('aW5f'.'YXJyYXk='),base64_decode('YXJyYX'.'lf'.'a2V5X2'.'V4aXN0cw=='),base64_decode('YX'.'JyYX'.'lfa2V'.'5X2V'.'4aXN0cw=='),base64_decode('c2VyaWFsaXpl'),base64_decode('YmFzZ'.'TY0X2'.'V'.'uY29kZQ'.'='.'='),base64_decode('Y'.'XJyYXlfa2V5X2V'.'4aXN0'.'cw=='),base64_decode('aW50dmFs'),base64_decode(''.'dG'.'lt'.'Z'.'Q'.'=='),base64_decode('Y'.'X'.'JyYXlfa'.'2V5X2V4aXN0cw='.'='),base64_decode(''.'ZmlsZV'.'9le'.'GlzdHM='),base64_decode(''.'c3'.'RyX'.'3JlcG'.'xh'.'Y2'.'U'.'='),base64_decode(''.'Y'.'2xhc3NfZXhp'.'c3Rz'),base64_decode(''.'ZGVmaW5l'));if(!function_exists(__NAMESPACE__.'\\___304841661')){function ___304841661($_1029122179){static $_1616451800= false; if($_1616451800 == false) $_1616451800=array('SU'.'5'.'UU'.'k'.'FOR'.'VRfRURJVElPTg==','WQ==',''.'bWFpb'.'g'.'='.'=','fmNwZl9'.'t'.'YX'.'BfdmFs'.'dWU=','','ZQ'.'='.'=','Z'.'g'.'='.'=','Z'.'Q==','Rg==','WA='.'=',''.'Zg'.'==','bWF'.'pbg==','fm'.'NwZ'.'l'.'9tYXB'.'f'.'dmFsdWU=','UG9y'.'d'.'GFs','R'.'g==','ZQ'.'==','ZQ='.'=','WA='.'=',''.'R'.'g==','RA==','R'.'A'.'==','bQ==','ZA==','WQ='.'=','Zg==','Z'.'g='.'=',''.'Zg==','Zg==','UG'.'9ydGFs','Rg==','ZQ'.'='.'=','Z'.'Q'.'==','W'.'A'.'==','Rg'.'==','R'.'A==',''.'RA==',''.'bQ==',''.'ZA==',''.'WQ==','bWFpbg==','T24=',''.'U2'.'V0dGluZ3NDa'.'GFuZ2U'.'=','Zg==',''.'Zg==',''.'Zg'.'==','Z'.'g'.'='.'=','bWFpbg==','fmNwZ'.'l'.'9'.'t'.'Y'.'XBfdm'.'FsdWU=','ZQ'.'==','ZQ==','Z'.'Q==','RA'.'='.'=','ZQ==','Z'.'Q'.'==',''.'Zg==','Zg==','Zg'.'==','ZQ==','bWFpbg'.'==','fmNwZl9tYX'.'BfdmFsdWU'.'=','ZQ==','Zg==','Zg'.'==','Zg='.'=','Zg==','bWFp'.'bg='.'=',''.'fmNwZl9'.'tYXB'.'f'.'dm'.'F'.'sdWU=','ZQ==','Zg'.'='.'=',''.'UG9ydGFs','U'.'G'.'9y'.'dG'.'Fs','ZQ='.'=','ZQ==','UG9y'.'dG'.'Fs','Rg==','WA==',''.'Rg==','RA='.'=','ZQ==',''.'ZQ'.'==','RA==','bQ'.'==','ZA==',''.'W'.'Q==','ZQ'.'='.'=',''.'WA==','ZQ==','Rg==',''.'ZQ==','R'.'A==','Z'.'g'.'==','ZQ==','R'.'A==','ZQ==',''.'bQ==','ZA==','W'.'Q==','Zg==','Z'.'g==','Zg==','Z'.'g='.'=','Zg==','Z'.'g='.'=','Z'.'g==','Zg==',''.'bW'.'Fpbg'.'='.'=','fmNwZl9tYX'.'BfdmFsdWU=','ZQ==','ZQ==','UG'.'9ydGFs','Rg==','WA='.'=','VFl'.'QR'.'Q==','REF'.'UR'.'Q==','RkVBVFVSRVM=','RV'.'hQ'.'S'.'VJ'.'FRA==','VFlQRQ==','RA==',''.'VFJZX'.'0RBWVNfQ09'.'VT'.'lQ=','REFURQ==','VFJZX'.'0R'.'B'.'WVNfQ0'.'9V'.'TlQ=','R'.'VhQSVJFRA==','RkV'.'BVFVSRVM=','Z'.'g==',''.'Zg'.'==',''.'RE9DVU1FT'.'lRfUk'.'9PV'.'A'.'==','L'.'2J'.'p'.'dHJpe'.'C9tb2'.'R1bGVz'.'Lw'.'==','L'.'2luc3RhbGw'.'vaW5kZXgucGhw','Lg'.'==',''.'Xw==','c2V'.'hcmNo','Tg==','','','QUNU'.'S'.'VZF','WQ='.'=','c29ja'.'WFsb'.'mV0d29y'.'aw'.'='.'=','YWx'.'sb'.'3dfZnJpZW'.'x'.'kc'.'w==','WQ==','S'.'UQ=','c'.'29ja'.'WF'.'sbmV0'.'d29yaw='.'=','YWxsb3'.'df'.'ZnJpZW'.'xkcw==',''.'SU'.'Q=',''.'c29'.'jaW'.'Fsb'.'mV0'.'d'.'2'.'9yaw='.'=','YWxsb3dfZnJpZWxkcw==',''.'T'.'g==','','',''.'QU'.'N'.'USV'.'ZF','WQ'.'==','c29j'.'aWFsb'.'m'.'V0d29ya'.'w==',''.'YWxsb3'.'dfbW'.'l'.'jcm9i'.'bG9nX3'.'V'.'zZ'.'XI'.'=','WQ==',''.'SUQ=','c29ja'.'W'.'Fs'.'bm'.'V0d29'.'y'.'aw='.'=','YW'.'xsb'.'3dfbWlj'.'cm9ibG9'.'nX3V'.'zZX'.'I=','SUQ=','c29j'.'a'.'WF'.'s'.'bmV0d29yaw==','YWxs'.'b3d'.'fbWljc'.'m'.'9ibG'.'9nX3VzZXI=','c2'.'9jaWFsbmV'.'0'.'d29yaw='.'=','YWxsb3d'.'fb'.'Wl'.'jcm9'.'ibG9'.'nX'.'2d'.'yb3Vw','WQ==','SUQ=','c2'.'9jaWFsbmV'.'0d29'.'yaw'.'='.'=','YWxsb3dfbWl'.'j'.'cm9ibG9nX'.'2'.'dy'.'b3Vw',''.'SUQ=','c29'.'jaWFsbmV0d29'.'ya'.'w==','YWx'.'sb3d'.'fb'.'Wl'.'j'.'cm9i'.'bG9nX2dyb3V'.'w',''.'T'.'g==','','','QUNUSVZF','WQ'.'==','c29'.'ja'.'WFs'.'bmV'.'0d'.'2'.'9'.'yaw==',''.'YWx'.'s'.'b3dfZmlsZX'.'NfdX'.'Nl'.'cg'.'==',''.'WQ==','SUQ=','c'.'29j'.'aWFsbmV0'.'d29'.'y'.'aw==','YWxs'.'b3d'.'fZml'.'s'.'ZXNfdXN'.'lcg==','S'.'UQ=',''.'c29jaWFsbm'.'V0d2'.'9ya'.'w==','YWxsb3dfZ'.'mlsZX'.'NfdXNlcg==','Tg==','','','Q'.'U'.'NUSVZF','WQ='.'=','c29'.'j'.'aW'.'Fs'.'bmV0d2'.'9yaw==','YWxsb3'.'dfYmxvZ1'.'91c'.'2Vy',''.'WQ==','SUQ=','c2'.'9jaWFs'.'bmV0'.'d29ya'.'w'.'==','YWx'.'sb3'.'dfYmxvZ'.'191c2Vy','SUQ=','c29'.'j'.'a'.'WF'.'sb'.'mV0'.'d'.'29ya'.'w==','YWxsb3dfYm'.'xvZ191c2Vy','Tg==','','','QU'.'NUSVZF','WQ==','c'.'29jaWFs'.'b'.'m'.'V0d29ya'.'w'.'==','YWxsb3dfcG'.'hvd'.'G9fdXNlc'.'g==','W'.'Q==','SU'.'Q=','c'.'29jaWF'.'sbmV0d29yaw='.'=','YWx'.'sb3dfcGhv'.'dG'.'9f'.'dXNlcg'.'==','S'.'UQ=','c29jaWF'.'sbmV'.'0d2'.'9yaw==','Y'.'Wxsb3dfc'.'Gh'.'vdG9'.'fdXN'.'lc'.'g'.'==','Tg==','','','QUNU'.'S'.'VZF','WQ==','c29jaWFsb'.'mV0d2'.'9yaw==',''.'Y'.'Wxsb3'.'dfZm9ydW1fd'.'XNlc'.'g==','WQ==','SUQ=',''.'c29j'.'aWFsbmV0'.'d29ya'.'w'.'==','YWxsb'.'3dfZm9ydW1fdXNl'.'cg='.'=','SUQ=','c2'.'9jaWFsbmV0d29'.'ya'.'w==','YWxsb3dfZm'.'9ydW1fdXNlcg'.'==',''.'Tg==','','','QUNUSVZF','WQ='.'=','c29ja'.'WFsbmV0d29yaw==','YWxsb3dfdG'.'Fza3Nfd'.'XNl'.'cg'.'='.'=','WQ==','S'.'U'.'Q'.'=','c29jaWF'.'sbmV'.'0d29y'.'a'.'w==',''.'YWxsb3'.'df'.'dGFza3NfdXNlcg==',''.'SUQ=','c29j'.'a'.'WF'.'sbmV0'.'d29ya'.'w==','YWxs'.'b3dfdGFza'.'3Nf'.'dXNl'.'cg==',''.'c29jaWFs'.'bmV'.'0d29yaw==',''.'YW'.'xsb3dfdGFza3NfZ3'.'Jvd'.'X'.'A=','WQ'.'==','SUQ=','c29'.'jaWF'.'sbmV0d2'.'9y'.'aw==',''.'YWxs'.'b'.'3dfdGFz'.'a3NfZ3'.'Jvd'.'XA=',''.'S'.'UQ=','c2'.'9jaWFsbmV0d'.'29'.'ya'.'w==',''.'YW'.'xsb'.'3dfdGFza3NfZ3J'.'vdX'.'A=','dGFz'.'a3'.'M=','Tg==','','','QUNUSVZ'.'F','WQ==','c29jaWFsbmV'.'0d29yaw='.'=','YWxsb'.'3dfY2F'.'sZW5kYXJfdXNlcg==','WQ='.'=','SUQ=',''.'c'.'2'.'9jaWF'.'sbmV'.'0d'.'29ya'.'w='.'=','YWxsb3dfY2FsZW5kYX'.'JfdXNlcg==','SUQ=','c'.'29jaWFsb'.'mV0d2'.'9yaw==','Y'.'Wx'.'sb3d'.'fY2'.'FsZW5'.'kYXJfdXN'.'lcg==','c29ja'.'WFs'.'b'.'m'.'V0d29y'.'aw'.'==','Y'.'Wxsb3dfY2FsZW5k'.'YX'.'J'.'fZ3JvdXA=','W'.'Q==','SUQ=','c2'.'9ja'.'W'.'FsbmV0d2'.'9yaw==','YWx'.'sb3'.'df'.'Y2'.'F'.'sZ'.'W'.'5kYXJfZ3JvdX'.'A=','SUQ=','c29'.'j'.'aWFsb'.'mV0d29yaw==','YWxs'.'b3d'.'fY2F'.'sZ'.'W5kY'.'XJfZ'.'3JvdXA=','QUNUS'.'VZ'.'F','WQ'.'==','Tg==','ZXh0'.'cmF'.'uZXQ=','aW'.'Jsb2Nr','T2'.'5'.'BZ'.'nRlck'.'lCbG9ja0Vs'.'ZW1lbnRVcGRhdGU=','a'.'W50cmFu'.'ZXQ=','Q0ludHJhbmV0RXZlb'.'nRI'.'YW5kb'.'GVyc'.'w==','U1B'.'SZ'.'W'.'dpc3Rlc'.'lVwZG'.'F0Z'.'W'.'RJdGVt','Q'.'0ludHJ'.'hbmV'.'0'.'U2'.'hhcmVw'.'b2'.'ludDo6'.'Q'.'Wd'.'l'.'b'.'nRMaXN0cy'.'g'.'pOw'.'==',''.'a'.'W'.'50cmF'.'uZ'.'XQ=','T'.'g'.'==','Q0'.'ludH'.'JhbmV0U2'.'hhcm'.'Vw'.'b2ludDo6QWdlbnRRdWV'.'1'.'Z'.'SgpOw'.'==','aW5'.'0cmFuZX'.'Q=','Tg==','Q'.'0'.'ludHJhbmV0U'.'2hhcmVwb2ludDo6QWdlbnR'.'Vc'.'GRhdGUoKTs=','aW'.'50cmFuZXQ=','Tg==','aW'.'Jsb2Nr','T25BZn'.'RlcklC'.'bG9ja0VsZW1'.'lb'.'nRBZG'.'Q=','aW5'.'0cm'.'F'.'uZ'.'XQ'.'=','Q0lu'.'dHJhbmV0RXZl'.'bnRIY'.'W'.'5kbGVyc'.'w==',''.'U1BSZW'.'d'.'pc3Rl'.'cl'.'VwZGF'.'0'.'ZW'.'RJ'.'dGVt','aWJsb'.'2Nr','T2'.'5'.'BZ'.'nRlc'.'kl'.'C'.'b'.'G'.'9ja0V'.'sZW'.'1lbnRVcGRhdGU=','a'.'W50cmF'.'uZXQ'.'=','Q0lud'.'HJhbmV0RX'.'Zl'.'b'.'nRIY'.'W'.'5kbGVycw==','U1BSZ'.'W'.'dpc3'.'Rlcl'.'VwZ'.'GF0ZW'.'RJdGVt','Q0ludHJ'.'hbmV0'.'U2'.'h'.'hcmVwb2'.'ludDo6Q'.'WdlbnRMaXN0cy'.'gpOw==',''.'aW50cm'.'FuZ'.'XQ=','Q0l'.'u'.'dHJhbmV'.'0U2hhcmVwb2lu'.'dDo6QWdlbnR'.'RdWV1'.'ZSgpO'.'w==','a'.'W'.'50cm'.'FuZ'.'XQ=','Q0ludH'.'Jhb'.'mV0U2'.'hhc'.'mVwb2'.'ludDo'.'6QWdlbnRVcGRhdGUoKTs'.'=','a'.'W5'.'0cmFuZ'.'X'.'Q=','Y'.'3Jt','bWF'.'pbg==','T25CZ'.'WZvcmVQcm9sb2'.'c=','bWF'.'p'.'bg==',''.'Q1'.'dpemFyZFNvb'.'F'.'Bh'.'bmV'.'s'.'SW50cm'.'Fu'.'ZX'.'Q=','U2hvd1'.'Bhb'.'mVs','L21vZHV'.'sZXMva'.'W'.'50cmFuZXQv'.'cGFuZWx'.'fYnV0d'.'G9uLn'.'BocA==','R'.'U5DT'.'0R'.'F','WQ='.'=');return base64_decode($_1616451800[$_1029122179]);}};$GLOBALS['____933132382'][0](___304841661(0), ___304841661(1));class CBXFeatures{ private static $_720126572= 30; private static $_1204098140= array( "Portal" => array( "CompanyCalendar", "CompanyPhoto", "CompanyVideo", "CompanyCareer", "StaffChanges", "StaffAbsence", "CommonDocuments", "MeetingRoomBookingSystem", "Wiki", "Learning", "Vote", "WebLink", "Subscribe", "Friends", "PersonalFiles", "PersonalBlog", "PersonalPhoto", "PersonalForum", "Blog", "Forum", "Gallery", "Board", "MicroBlog", "WebMessenger",), "Communications" => array( "Tasks", "Calendar", "Workgroups", "Jabber", "VideoConference", "Extranet", "SMTP", "Requests", "DAV", "intranet_sharepoint", "timeman", "Idea", "Meeting", "EventList", "Salary", "XDImport",), "Enterprise" => array( "BizProc", "Lists", "Support", "Analytics", "crm", "Controller",), "Holding" => array( "Cluster", "MultiSites",),); private static $_486159815= false; private static $_582354921= false; private static function __1446044309(){ if(self::$_486159815 == false){ self::$_486159815= array(); foreach(self::$_1204098140 as $_1297479870 => $_766028852){ foreach($_766028852 as $_657275124) self::$_486159815[$_657275124]= $_1297479870;}} if(self::$_582354921 == false){ self::$_582354921= array(); $_180624574= COption::GetOptionString(___304841661(2), ___304841661(3), ___304841661(4)); if($GLOBALS['____933132382'][1]($_180624574)>(1492/2-746)){ $_180624574= $GLOBALS['____933132382'][2]($_180624574); self::$_582354921= $GLOBALS['____933132382'][3]($_180624574); if(!$GLOBALS['____933132382'][4](self::$_582354921)) self::$_582354921= array();} if($GLOBALS['____933132382'][5](self::$_582354921) <=(1124/2-562)) self::$_582354921= array(___304841661(5) => array(), ___304841661(6) => array());}} public static function InitiateEditionsSettings($_1961450934){ self::__1446044309(); $_208442356= array(); foreach(self::$_1204098140 as $_1297479870 => $_766028852){ $_67200702= $GLOBALS['____933132382'][6]($_1297479870, $_1961450934); self::$_582354921[___304841661(7)][$_1297479870]=($_67200702? array(___304841661(8)): array(___304841661(9))); foreach($_766028852 as $_657275124){ self::$_582354921[___304841661(10)][$_657275124]= $_67200702; if(!$_67200702) $_208442356[]= array($_657275124, false);}} $_1670963827= $GLOBALS['____933132382'][7](self::$_582354921); $_1670963827= $GLOBALS['____933132382'][8]($_1670963827); COption::SetOptionString(___304841661(11), ___304841661(12), $_1670963827); foreach($_208442356 as $_1936402899) self::__768463514($_1936402899[(1080/2-540)], $_1936402899[round(0+0.33333333333333+0.33333333333333+0.33333333333333)]);} public static function IsFeatureEnabled($_657275124){ if($GLOBALS['____933132382'][9]($_657275124) <= 0) return true; self::__1446044309(); if(!$GLOBALS['____933132382'][10]($_657275124, self::$_486159815)) return true; if(self::$_486159815[$_657275124] == ___304841661(13)) $_1504528957= array(___304841661(14)); elseif($GLOBALS['____933132382'][11](self::$_486159815[$_657275124], self::$_582354921[___304841661(15)])) $_1504528957= self::$_582354921[___304841661(16)][self::$_486159815[$_657275124]]; else $_1504528957= array(___304841661(17)); if($_1504528957[(946-2*473)] != ___304841661(18) && $_1504528957[(890-2*445)] != ___304841661(19)){ return false;} elseif($_1504528957[min(70,0,23.333333333333)] == ___304841661(20)){ if($_1504528957[round(0+0.33333333333333+0.33333333333333+0.33333333333333)]< $GLOBALS['____933132382'][12]((1152/2-576), min(136,0,45.333333333333),(171*2-342), Date(___304841661(21)), $GLOBALS['____933132382'][13](___304841661(22))- self::$_720126572, $GLOBALS['____933132382'][14](___304841661(23)))){ if(!isset($_1504528957[round(0+0.5+0.5+0.5+0.5)]) ||!$_1504528957[round(0+2)]) self::__508964215(self::$_486159815[$_657275124]); return false;}} return!$GLOBALS['____933132382'][15]($_657275124, self::$_582354921[___304841661(24)]) || self::$_582354921[___304841661(25)][$_657275124];} public static function IsFeatureInstalled($_657275124){ if($GLOBALS['____933132382'][16]($_657275124) <= 0) return true; self::__1446044309(); return($GLOBALS['____933132382'][17]($_657275124, self::$_582354921[___304841661(26)]) && self::$_582354921[___304841661(27)][$_657275124]);} public static function IsFeatureEditable($_657275124){ if($GLOBALS['____933132382'][18]($_657275124) <= 0) return true; self::__1446044309(); if(!$GLOBALS['____933132382'][19]($_657275124, self::$_486159815)) return true; if(self::$_486159815[$_657275124] == ___304841661(28)) $_1504528957= array(___304841661(29)); elseif($GLOBALS['____933132382'][20](self::$_486159815[$_657275124], self::$_582354921[___304841661(30)])) $_1504528957= self::$_582354921[___304841661(31)][self::$_486159815[$_657275124]]; else $_1504528957= array(___304841661(32)); if($_1504528957[min(120,0,40)] != ___304841661(33) && $_1504528957[(1184/2-592)] != ___304841661(34)){ return false;} elseif($_1504528957[(912-2*456)] == ___304841661(35)){ if($_1504528957[round(0+0.2+0.2+0.2+0.2+0.2)]< $GLOBALS['____933132382'][21](min(212,0,70.666666666667),(147*2-294),(838-2*419), Date(___304841661(36)), $GLOBALS['____933132382'][22](___304841661(37))- self::$_720126572, $GLOBALS['____933132382'][23](___304841661(38)))){ if(!isset($_1504528957[round(0+0.5+0.5+0.5+0.5)]) ||!$_1504528957[round(0+0.4+0.4+0.4+0.4+0.4)]) self::__508964215(self::$_486159815[$_657275124]); return false;}} return true;} private static function __768463514($_657275124, $_1797500326){ if($GLOBALS['____933132382'][24]("CBXFeatures", "On".$_657275124."SettingsChange")) $GLOBALS['____933132382'][25](array("CBXFeatures", "On".$_657275124."SettingsChange"), array($_657275124, $_1797500326)); $_1030146964= $GLOBALS['_____659297627'][0](___304841661(39), ___304841661(40).$_657275124.___304841661(41)); while($_2122800945= $_1030146964->Fetch()) $GLOBALS['_____659297627'][1]($_2122800945, array($_657275124, $_1797500326));} public static function SetFeatureEnabled($_657275124, $_1797500326= true, $_49024040= true){ if($GLOBALS['____933132382'][26]($_657275124) <= 0) return; if(!self::IsFeatureEditable($_657275124)) $_1797500326= false; $_1797500326=($_1797500326? true: false); self::__1446044309(); $_890618375=(!$GLOBALS['____933132382'][27]($_657275124, self::$_582354921[___304841661(42)]) && $_1797500326 || $GLOBALS['____933132382'][28]($_657275124, self::$_582354921[___304841661(43)]) && $_1797500326 != self::$_582354921[___304841661(44)][$_657275124]); self::$_582354921[___304841661(45)][$_657275124]= $_1797500326; $_1670963827= $GLOBALS['____933132382'][29](self::$_582354921); $_1670963827= $GLOBALS['____933132382'][30]($_1670963827); COption::SetOptionString(___304841661(46), ___304841661(47), $_1670963827); if($_890618375 && $_49024040) self::__768463514($_657275124, $_1797500326);} private static function __508964215($_1297479870){ if($GLOBALS['____933132382'][31]($_1297479870) <= 0 || $_1297479870 == "Portal") return; self::__1446044309(); if(!$GLOBALS['____933132382'][32]($_1297479870, self::$_582354921[___304841661(48)]) || $GLOBALS['____933132382'][33]($_1297479870, self::$_582354921[___304841661(49)]) && self::$_582354921[___304841661(50)][$_1297479870][(774-2*387)] != ___304841661(51)) return; if(isset(self::$_582354921[___304841661(52)][$_1297479870][round(0+0.66666666666667+0.66666666666667+0.66666666666667)]) && self::$_582354921[___304841661(53)][$_1297479870][round(0+0.66666666666667+0.66666666666667+0.66666666666667)]) return; $_208442356= array(); if($GLOBALS['____933132382'][34]($_1297479870, self::$_1204098140) && $GLOBALS['____933132382'][35](self::$_1204098140[$_1297479870])){ foreach(self::$_1204098140[$_1297479870] as $_657275124){ if($GLOBALS['____933132382'][36]($_657275124, self::$_582354921[___304841661(54)]) && self::$_582354921[___304841661(55)][$_657275124]){ self::$_582354921[___304841661(56)][$_657275124]= false; $_208442356[]= array($_657275124, false);}} self::$_582354921[___304841661(57)][$_1297479870][round(0+0.66666666666667+0.66666666666667+0.66666666666667)]= true;} $_1670963827= $GLOBALS['____933132382'][37](self::$_582354921); $_1670963827= $GLOBALS['____933132382'][38]($_1670963827); COption::SetOptionString(___304841661(58), ___304841661(59), $_1670963827); foreach($_208442356 as $_1936402899) self::__768463514($_1936402899[(796-2*398)], $_1936402899[round(0+0.33333333333333+0.33333333333333+0.33333333333333)]);} public static function ModifyFeaturesSettings($_1961450934, $_766028852){ self::__1446044309(); foreach($_1961450934 as $_1297479870 => $_1672334784) self::$_582354921[___304841661(60)][$_1297479870]= $_1672334784; $_208442356= array(); foreach($_766028852 as $_657275124 => $_1797500326){ if(!$GLOBALS['____933132382'][39]($_657275124, self::$_582354921[___304841661(61)]) && $_1797500326 || $GLOBALS['____933132382'][40]($_657275124, self::$_582354921[___304841661(62)]) && $_1797500326 != self::$_582354921[___304841661(63)][$_657275124]) $_208442356[]= array($_657275124, $_1797500326); self::$_582354921[___304841661(64)][$_657275124]= $_1797500326;} $_1670963827= $GLOBALS['____933132382'][41](self::$_582354921); $_1670963827= $GLOBALS['____933132382'][42]($_1670963827); COption::SetOptionString(___304841661(65), ___304841661(66), $_1670963827); self::$_582354921= false; foreach($_208442356 as $_1936402899) self::__768463514($_1936402899[(1376/2-688)], $_1936402899[round(0+0.25+0.25+0.25+0.25)]);} public static function SaveFeaturesSettings($_1591937353, $_1362178020){ self::__1446044309(); $_1098746244= array(___304841661(67) => array(), ___304841661(68) => array()); if(!$GLOBALS['____933132382'][43]($_1591937353)) $_1591937353= array(); if(!$GLOBALS['____933132382'][44]($_1362178020)) $_1362178020= array(); if(!$GLOBALS['____933132382'][45](___304841661(69), $_1591937353)) $_1591937353[]= ___304841661(70); foreach(self::$_1204098140 as $_1297479870 => $_766028852){ if($GLOBALS['____933132382'][46]($_1297479870, self::$_582354921[___304841661(71)])) $_1701841208= self::$_582354921[___304841661(72)][$_1297479870]; else $_1701841208=($_1297479870 == ___304841661(73))? array(___304841661(74)): array(___304841661(75)); if($_1701841208[(974-2*487)] == ___304841661(76) || $_1701841208[(231*2-462)] == ___304841661(77)){ $_1098746244[___304841661(78)][$_1297479870]= $_1701841208;} else{ if($GLOBALS['____933132382'][47]($_1297479870, $_1591937353)) $_1098746244[___304841661(79)][$_1297479870]= array(___304841661(80), $GLOBALS['____933132382'][48]((139*2-278),(916-2*458),(1236/2-618), $GLOBALS['____933132382'][49](___304841661(81)), $GLOBALS['____933132382'][50](___304841661(82)), $GLOBALS['____933132382'][51](___304841661(83)))); else $_1098746244[___304841661(84)][$_1297479870]= array(___304841661(85));}} $_208442356= array(); foreach(self::$_486159815 as $_657275124 => $_1297479870){ if($_1098746244[___304841661(86)][$_1297479870][(1408/2-704)] != ___304841661(87) && $_1098746244[___304841661(88)][$_1297479870][min(118,0,39.333333333333)] != ___304841661(89)){ $_1098746244[___304841661(90)][$_657275124]= false;} else{ if($_1098746244[___304841661(91)][$_1297479870][(986-2*493)] == ___304841661(92) && $_1098746244[___304841661(93)][$_1297479870][round(0+0.25+0.25+0.25+0.25)]< $GLOBALS['____933132382'][52]((766-2*383),(158*2-316), min(62,0,20.666666666667), Date(___304841661(94)), $GLOBALS['____933132382'][53](___304841661(95))- self::$_720126572, $GLOBALS['____933132382'][54](___304841661(96)))) $_1098746244[___304841661(97)][$_657275124]= false; else $_1098746244[___304841661(98)][$_657275124]= $GLOBALS['____933132382'][55]($_657275124, $_1362178020); if(!$GLOBALS['____933132382'][56]($_657275124, self::$_582354921[___304841661(99)]) && $_1098746244[___304841661(100)][$_657275124] || $GLOBALS['____933132382'][57]($_657275124, self::$_582354921[___304841661(101)]) && $_1098746244[___304841661(102)][$_657275124] != self::$_582354921[___304841661(103)][$_657275124]) $_208442356[]= array($_657275124, $_1098746244[___304841661(104)][$_657275124]);}} $_1670963827= $GLOBALS['____933132382'][58]($_1098746244); $_1670963827= $GLOBALS['____933132382'][59]($_1670963827); COption::SetOptionString(___304841661(105), ___304841661(106), $_1670963827); self::$_582354921= false; foreach($_208442356 as $_1936402899) self::__768463514($_1936402899[min(236,0,78.666666666667)], $_1936402899[round(0+0.33333333333333+0.33333333333333+0.33333333333333)]);} public static function GetFeaturesList(){ self::__1446044309(); $_413474102= array(); foreach(self::$_1204098140 as $_1297479870 => $_766028852){ if($GLOBALS['____933132382'][60]($_1297479870, self::$_582354921[___304841661(107)])) $_1701841208= self::$_582354921[___304841661(108)][$_1297479870]; else $_1701841208=($_1297479870 == ___304841661(109))? array(___304841661(110)): array(___304841661(111)); $_413474102[$_1297479870]= array( ___304841661(112) => $_1701841208[(984-2*492)], ___304841661(113) => $_1701841208[round(0+0.33333333333333+0.33333333333333+0.33333333333333)], ___304841661(114) => array(),); $_413474102[$_1297479870][___304841661(115)]= false; if($_413474102[$_1297479870][___304841661(116)] == ___304841661(117)){ $_413474102[$_1297479870][___304841661(118)]= $GLOBALS['____933132382'][61](($GLOBALS['____933132382'][62]()- $_413474102[$_1297479870][___304841661(119)])/ round(0+43200+43200)); if($_413474102[$_1297479870][___304841661(120)]> self::$_720126572) $_413474102[$_1297479870][___304841661(121)]= true;} foreach($_766028852 as $_657275124) $_413474102[$_1297479870][___304841661(122)][$_657275124]=(!$GLOBALS['____933132382'][63]($_657275124, self::$_582354921[___304841661(123)]) || self::$_582354921[___304841661(124)][$_657275124]);} return $_413474102;} private static function __928025779($_1685395747, $_554320243){ if(IsModuleInstalled($_1685395747) == $_554320243) return true; $_459639424= $_SERVER[___304841661(125)].___304841661(126).$_1685395747.___304841661(127); if(!$GLOBALS['____933132382'][64]($_459639424)) return false; include_once($_459639424); $_1480193035= $GLOBALS['____933132382'][65](___304841661(128), ___304841661(129), $_1685395747); if(!$GLOBALS['____933132382'][66]($_1480193035)) return false; $_929204072= new $_1480193035; if($_554320243){ if(!$_929204072->InstallDB()) return false; $_929204072->InstallEvents(); if(!$_929204072->InstallFiles()) return false;} else{ if(CModule::IncludeModule(___304841661(130))) CSearch::DeleteIndex($_1685395747); UnRegisterModule($_1685395747);} return true;} protected static function OnRequestsSettingsChange($_657275124, $_1797500326){ self::__928025779("form", $_1797500326);} protected static function OnLearningSettingsChange($_657275124, $_1797500326){ self::__928025779("learning", $_1797500326);} protected static function OnJabberSettingsChange($_657275124, $_1797500326){ self::__928025779("xmpp", $_1797500326);} protected static function OnVideoConferenceSettingsChange($_657275124, $_1797500326){ self::__928025779("video", $_1797500326);} protected static function OnBizProcSettingsChange($_657275124, $_1797500326){ self::__928025779("bizprocdesigner", $_1797500326);} protected static function OnListsSettingsChange($_657275124, $_1797500326){ self::__928025779("lists", $_1797500326);} protected static function OnWikiSettingsChange($_657275124, $_1797500326){ self::__928025779("wiki", $_1797500326);} protected static function OnSupportSettingsChange($_657275124, $_1797500326){ self::__928025779("support", $_1797500326);} protected static function OnControllerSettingsChange($_657275124, $_1797500326){ self::__928025779("controller", $_1797500326);} protected static function OnAnalyticsSettingsChange($_657275124, $_1797500326){ self::__928025779("statistic", $_1797500326);} protected static function OnVoteSettingsChange($_657275124, $_1797500326){ self::__928025779("vote", $_1797500326);} protected static function OnFriendsSettingsChange($_657275124, $_1797500326){ if($_1797500326) $_948324997= "Y"; else $_948324997= ___304841661(131); $_1992244711= CSite::GetList(($_67200702= ___304841661(132)),($_1461174651= ___304841661(133)), array(___304841661(134) => ___304841661(135))); while($_645377026= $_1992244711->Fetch()){ if(COption::GetOptionString(___304841661(136), ___304841661(137), ___304841661(138), $_645377026[___304841661(139)]) != $_948324997){ COption::SetOptionString(___304841661(140), ___304841661(141), $_948324997, false, $_645377026[___304841661(142)]); COption::SetOptionString(___304841661(143), ___304841661(144), $_948324997);}}} protected static function OnMicroBlogSettingsChange($_657275124, $_1797500326){ if($_1797500326) $_948324997= "Y"; else $_948324997= ___304841661(145); $_1992244711= CSite::GetList(($_67200702= ___304841661(146)),($_1461174651= ___304841661(147)), array(___304841661(148) => ___304841661(149))); while($_645377026= $_1992244711->Fetch()){ if(COption::GetOptionString(___304841661(150), ___304841661(151), ___304841661(152), $_645377026[___304841661(153)]) != $_948324997){ COption::SetOptionString(___304841661(154), ___304841661(155), $_948324997, false, $_645377026[___304841661(156)]); COption::SetOptionString(___304841661(157), ___304841661(158), $_948324997);} if(COption::GetOptionString(___304841661(159), ___304841661(160), ___304841661(161), $_645377026[___304841661(162)]) != $_948324997){ COption::SetOptionString(___304841661(163), ___304841661(164), $_948324997, false, $_645377026[___304841661(165)]); COption::SetOptionString(___304841661(166), ___304841661(167), $_948324997);}}} protected static function OnPersonalFilesSettingsChange($_657275124, $_1797500326){ if($_1797500326) $_948324997= "Y"; else $_948324997= ___304841661(168); $_1992244711= CSite::GetList(($_67200702= ___304841661(169)),($_1461174651= ___304841661(170)), array(___304841661(171) => ___304841661(172))); while($_645377026= $_1992244711->Fetch()){ if(COption::GetOptionString(___304841661(173), ___304841661(174), ___304841661(175), $_645377026[___304841661(176)]) != $_948324997){ COption::SetOptionString(___304841661(177), ___304841661(178), $_948324997, false, $_645377026[___304841661(179)]); COption::SetOptionString(___304841661(180), ___304841661(181), $_948324997);}}} protected static function OnPersonalBlogSettingsChange($_657275124, $_1797500326){ if($_1797500326) $_948324997= "Y"; else $_948324997= ___304841661(182); $_1992244711= CSite::GetList(($_67200702= ___304841661(183)),($_1461174651= ___304841661(184)), array(___304841661(185) => ___304841661(186))); while($_645377026= $_1992244711->Fetch()){ if(COption::GetOptionString(___304841661(187), ___304841661(188), ___304841661(189), $_645377026[___304841661(190)]) != $_948324997){ COption::SetOptionString(___304841661(191), ___304841661(192), $_948324997, false, $_645377026[___304841661(193)]); COption::SetOptionString(___304841661(194), ___304841661(195), $_948324997);}}} protected static function OnPersonalPhotoSettingsChange($_657275124, $_1797500326){ if($_1797500326) $_948324997= "Y"; else $_948324997= ___304841661(196); $_1992244711= CSite::GetList(($_67200702= ___304841661(197)),($_1461174651= ___304841661(198)), array(___304841661(199) => ___304841661(200))); while($_645377026= $_1992244711->Fetch()){ if(COption::GetOptionString(___304841661(201), ___304841661(202), ___304841661(203), $_645377026[___304841661(204)]) != $_948324997){ COption::SetOptionString(___304841661(205), ___304841661(206), $_948324997, false, $_645377026[___304841661(207)]); COption::SetOptionString(___304841661(208), ___304841661(209), $_948324997);}}} protected static function OnPersonalForumSettingsChange($_657275124, $_1797500326){ if($_1797500326) $_948324997= "Y"; else $_948324997= ___304841661(210); $_1992244711= CSite::GetList(($_67200702= ___304841661(211)),($_1461174651= ___304841661(212)), array(___304841661(213) => ___304841661(214))); while($_645377026= $_1992244711->Fetch()){ if(COption::GetOptionString(___304841661(215), ___304841661(216), ___304841661(217), $_645377026[___304841661(218)]) != $_948324997){ COption::SetOptionString(___304841661(219), ___304841661(220), $_948324997, false, $_645377026[___304841661(221)]); COption::SetOptionString(___304841661(222), ___304841661(223), $_948324997);}}} protected static function OnTasksSettingsChange($_657275124, $_1797500326){ if($_1797500326) $_948324997= "Y"; else $_948324997= ___304841661(224); $_1992244711= CSite::GetList(($_67200702= ___304841661(225)),($_1461174651= ___304841661(226)), array(___304841661(227) => ___304841661(228))); while($_645377026= $_1992244711->Fetch()){ if(COption::GetOptionString(___304841661(229), ___304841661(230), ___304841661(231), $_645377026[___304841661(232)]) != $_948324997){ COption::SetOptionString(___304841661(233), ___304841661(234), $_948324997, false, $_645377026[___304841661(235)]); COption::SetOptionString(___304841661(236), ___304841661(237), $_948324997);} if(COption::GetOptionString(___304841661(238), ___304841661(239), ___304841661(240), $_645377026[___304841661(241)]) != $_948324997){ COption::SetOptionString(___304841661(242), ___304841661(243), $_948324997, false, $_645377026[___304841661(244)]); COption::SetOptionString(___304841661(245), ___304841661(246), $_948324997);}} self::__928025779(___304841661(247), $_1797500326);} protected static function OnCalendarSettingsChange($_657275124, $_1797500326){ if($_1797500326) $_948324997= "Y"; else $_948324997= ___304841661(248); $_1992244711= CSite::GetList(($_67200702= ___304841661(249)),($_1461174651= ___304841661(250)), array(___304841661(251) => ___304841661(252))); while($_645377026= $_1992244711->Fetch()){ if(COption::GetOptionString(___304841661(253), ___304841661(254), ___304841661(255), $_645377026[___304841661(256)]) != $_948324997){ COption::SetOptionString(___304841661(257), ___304841661(258), $_948324997, false, $_645377026[___304841661(259)]); COption::SetOptionString(___304841661(260), ___304841661(261), $_948324997);} if(COption::GetOptionString(___304841661(262), ___304841661(263), ___304841661(264), $_645377026[___304841661(265)]) != $_948324997){ COption::SetOptionString(___304841661(266), ___304841661(267), $_948324997, false, $_645377026[___304841661(268)]); COption::SetOptionString(___304841661(269), ___304841661(270), $_948324997);}}} protected static function OnSMTPSettingsChange($_657275124, $_1797500326){ self::__928025779("mail", $_1797500326);} protected static function OnExtranetSettingsChange($_657275124, $_1797500326){ $_1729034057= COption::GetOptionString("extranet", "extranet_site", ""); if($_1729034057){ $_1971667044= new CSite; $_1971667044->Update($_1729034057, array(___304841661(271) =>($_1797500326? ___304841661(272): ___304841661(273))));} self::__928025779(___304841661(274), $_1797500326);} protected static function OnDAVSettingsChange($_657275124, $_1797500326){ self::__928025779("dav", $_1797500326);} protected static function OntimemanSettingsChange($_657275124, $_1797500326){ self::__928025779("timeman", $_1797500326);} protected static function Onintranet_sharepointSettingsChange($_657275124, $_1797500326){ if($_1797500326){ RegisterModuleDependences("iblock", "OnAfterIBlockElementAdd", "intranet", "CIntranetEventHandlers", "SPRegisterUpdatedItem"); RegisterModuleDependences(___304841661(275), ___304841661(276), ___304841661(277), ___304841661(278), ___304841661(279)); CAgent::AddAgent(___304841661(280), ___304841661(281), ___304841661(282), round(0+125+125+125+125)); CAgent::AddAgent(___304841661(283), ___304841661(284), ___304841661(285), round(0+300)); CAgent::AddAgent(___304841661(286), ___304841661(287), ___304841661(288), round(0+1200+1200+1200));} else{ UnRegisterModuleDependences(___304841661(289), ___304841661(290), ___304841661(291), ___304841661(292), ___304841661(293)); UnRegisterModuleDependences(___304841661(294), ___304841661(295), ___304841661(296), ___304841661(297), ___304841661(298)); CAgent::RemoveAgent(___304841661(299), ___304841661(300)); CAgent::RemoveAgent(___304841661(301), ___304841661(302)); CAgent::RemoveAgent(___304841661(303), ___304841661(304));}} protected static function OncrmSettingsChange($_657275124, $_1797500326){ if($_1797500326) COption::SetOptionString("crm", "form_features", "Y"); self::__928025779(___304841661(305), $_1797500326);} protected static function OnClusterSettingsChange($_657275124, $_1797500326){ self::__928025779("cluster", $_1797500326);} protected static function OnMultiSitesSettingsChange($_657275124, $_1797500326){ if($_1797500326) RegisterModuleDependences("main", "OnBeforeProlog", "main", "CWizardSolPanelIntranet", "ShowPanel", 100, "/modules/intranet/panel_button.php"); else UnRegisterModuleDependences(___304841661(306), ___304841661(307), ___304841661(308), ___304841661(309), ___304841661(310), ___304841661(311));} protected static function OnIdeaSettingsChange($_657275124, $_1797500326){ self::__928025779("idea", $_1797500326);} protected static function OnMeetingSettingsChange($_657275124, $_1797500326){ self::__928025779("meeting", $_1797500326);} protected static function OnXDImportSettingsChange($_657275124, $_1797500326){ self::__928025779("xdimport", $_1797500326);}} $GLOBALS['____933132382'][67](___304841661(312), ___304841661(313));/**/			//Do not remove this

//component 2.0 template engines
$GLOBALS["arCustomTemplateEngines"] = array();

require_once($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/classes/general/urlrewriter.php");

/**
 * Defined in dbconn.php
 * @param string $DBType
 */

\Bitrix\Main\Loader::registerAutoLoadClasses(
	"main",
	array(
		"CSiteTemplate" => "classes/general/site_template.php",
		"CBitrixComponent" => "classes/general/component.php",
		"CComponentEngine" => "classes/general/component_engine.php",
		"CComponentAjax" => "classes/general/component_ajax.php",
		"CBitrixComponentTemplate" => "classes/general/component_template.php",
		"CComponentUtil" => "classes/general/component_util.php",
		"CControllerClient" => "classes/general/controller_member.php",
		"PHPParser" => "classes/general/php_parser.php",
		"CDiskQuota" => "classes/".$DBType."/quota.php",
		"CEventLog" => "classes/general/event_log.php",
		"CEventMain" => "classes/general/event_log.php",
		"CAdminFileDialog" => "classes/general/file_dialog.php",
		"WLL_User" => "classes/general/liveid.php",
		"WLL_ConsentToken" => "classes/general/liveid.php",
		"WindowsLiveLogin" => "classes/general/liveid.php",
		"CAllFile" => "classes/general/file.php",
		"CFile" => "classes/".$DBType."/file.php",
		"CTempFile" => "classes/general/file_temp.php",
		"CFavorites" => "classes/".$DBType."/favorites.php",
		"CUserOptions" => "classes/general/user_options.php",
		"CGridOptions" => "classes/general/grids.php",
		"CUndo" => "/classes/general/undo.php",
		"CAutoSave" => "/classes/general/undo.php",
		"CRatings" => "classes/".$DBType."/ratings.php",
		"CRatingsComponentsMain" => "classes/".$DBType."/ratings_components.php",
		"CRatingRule" => "classes/general/rating_rule.php",
		"CRatingRulesMain" => "classes/".$DBType."/rating_rules.php",
		"CTopPanel" => "public/top_panel.php",
		"CEditArea" => "public/edit_area.php",
		"CComponentPanel" => "public/edit_area.php",
		"CTextParser" => "classes/general/textparser.php",
		"CPHPCacheFiles" => "classes/general/cache_files.php",
		"CDataXML" => "classes/general/xml.php",
		"CXMLFileStream" => "classes/general/xml.php",
		"CRsaProvider" => "classes/general/rsasecurity.php",
		"CRsaSecurity" => "classes/general/rsasecurity.php",
		"CRsaBcmathProvider" => "classes/general/rsabcmath.php",
		"CRsaOpensslProvider" => "classes/general/rsaopenssl.php",
		"CASNReader" => "classes/general/asn.php",
		"CBXShortUri" => "classes/".$DBType."/short_uri.php",
		"CFinder" => "classes/general/finder.php",
		"CAccess" => "classes/general/access.php",
		"CAuthProvider" => "classes/general/authproviders.php",
		"IProviderInterface" => "classes/general/authproviders.php",
		"CGroupAuthProvider" => "classes/general/authproviders.php",
		"CUserAuthProvider" => "classes/general/authproviders.php",
		"CTableSchema" => "classes/general/table_schema.php",
		"CCSVData" => "classes/general/csv_data.php",
		"CSmile" => "classes/general/smile.php",
		"CSmileGallery" => "classes/general/smile.php",
		"CSmileSet" => "classes/general/smile.php",
		"CGlobalCounter" => "classes/general/global_counter.php",
		"CUserCounter" => "classes/".$DBType."/user_counter.php",
		"CUserCounterPage" => "classes/".$DBType."/user_counter.php",
		"CHotKeys" => "classes/general/hot_keys.php",
		"CHotKeysCode" => "classes/general/hot_keys.php",
		"CBXSanitizer" => "classes/general/sanitizer.php",
		"CBXArchive" => "classes/general/archive.php",
		"CAdminNotify" => "classes/general/admin_notify.php",
		"CBXFavAdmMenu" => "classes/general/favorites.php",
		"CAdminInformer" => "classes/general/admin_informer.php",
		"CSiteCheckerTest" => "classes/general/site_checker.php",
		"CSqlUtil" => "classes/general/sql_util.php",
		"CFileUploader" => "classes/general/uploader.php",
		"LPA" => "classes/general/lpa.php",
		"CAdminFilter" => "interface/admin_filter.php",
		"CAdminList" => "interface/admin_list.php",
		"CAdminUiList" => "interface/admin_ui_list.php",
		"CAdminUiResult" => "interface/admin_ui_list.php",
		"CAdminUiContextMenu" => "interface/admin_ui_list.php",
		"CAdminUiSorting" => "interface/admin_ui_list.php",
		"CAdminListRow" => "interface/admin_list.php",
		"CAdminTabControl" => "interface/admin_tabcontrol.php",
		"CAdminForm" => "interface/admin_form.php",
		"CAdminFormSettings" => "interface/admin_form.php",
		"CAdminTabControlDrag" => "interface/admin_tabcontrol_drag.php",
		"CAdminDraggableBlockEngine" => "interface/admin_tabcontrol_drag.php",
		"CJSPopup" => "interface/jspopup.php",
		"CJSPopupOnPage" => "interface/jspopup.php",
		"CAdminCalendar" => "interface/admin_calendar.php",
		"CAdminViewTabControl" => "interface/admin_viewtabcontrol.php",
		"CAdminTabEngine" => "interface/admin_tabengine.php",
		"CCaptcha" => "classes/general/captcha.php",
		"CMpNotifications" => "classes/general/mp_notifications.php",

		//deprecated
		"CHTMLPagesCache" => "lib/composite/helper.php",
		"StaticHtmlMemcachedResponse" => "lib/composite/responder.php",
		"StaticHtmlFileResponse" => "lib/composite/responder.php",
		"Bitrix\\Main\\Page\\Frame" => "lib/composite/engine.php",
		"Bitrix\\Main\\Page\\FrameStatic" => "lib/composite/staticarea.php",
		"Bitrix\\Main\\Page\\FrameBuffered" => "lib/composite/bufferarea.php",
		"Bitrix\\Main\\Page\\FrameHelper" => "lib/composite/bufferarea.php",
		"Bitrix\\Main\\Data\\StaticHtmlCache" => "lib/composite/page.php",
		"Bitrix\\Main\\Data\\StaticHtmlStorage" => "lib/composite/data/abstractstorage.php",
		"Bitrix\\Main\\Data\\StaticHtmlFileStorage" => "lib/composite/data/filestorage.php",
		"Bitrix\\Main\\Data\\StaticHtmlMemcachedStorage" => "lib/composite/data/memcachedstorage.php",
		"Bitrix\\Main\\Data\\StaticCacheProvider" => "lib/composite/data/cacheprovider.php",
		"Bitrix\\Main\\Data\\AppCacheManifest" => "lib/composite/appcache.php",
	)
);

require_once($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/classes/".$DBType."/agent.php");
require_once($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/classes/".$DBType."/user.php");
require_once($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/classes/".$DBType."/event.php");
require_once($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/classes/general/menu.php");
AddEventHandler("main", "OnAfterEpilog", array("\\Bitrix\\Main\\Data\\ManagedCache", "finalize"));
require_once($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/classes/".$DBType."/usertype.php");

if(file_exists(($_fname = $_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/classes/general/update_db_updater.php")))
{
	$US_HOST_PROCESS_MAIN = False;
	include($_fname);
}

if(file_exists(($_fname = $_SERVER["DOCUMENT_ROOT"]."/bitrix/init.php")))
	include_once($_fname);

if(($_fname = getLocalPath("php_interface/init.php", BX_PERSONAL_ROOT)) !== false)
	include_once($_SERVER["DOCUMENT_ROOT"].$_fname);

if(($_fname = getLocalPath("php_interface/".SITE_ID."/init.php", BX_PERSONAL_ROOT)) !== false)
	include_once($_SERVER["DOCUMENT_ROOT"].$_fname);

if(!defined("BX_FILE_PERMISSIONS"))
	define("BX_FILE_PERMISSIONS", 0644);
if(!defined("BX_DIR_PERMISSIONS"))
	define("BX_DIR_PERMISSIONS", 0755);

//global var, is used somewhere
$GLOBALS["sDocPath"] = $GLOBALS["APPLICATION"]->GetCurPage();

if((!(defined("STATISTIC_ONLY") && STATISTIC_ONLY && substr($GLOBALS["APPLICATION"]->GetCurPage(), 0, strlen(BX_ROOT."/admin/"))!=BX_ROOT."/admin/")) && COption::GetOptionString("main", "include_charset", "Y")=="Y" && strlen(LANG_CHARSET)>0)
	header("Content-Type: text/html; charset=".LANG_CHARSET);

if(COption::GetOptionString("main", "set_p3p_header", "Y")=="Y")
	header("P3P: policyref=\"/bitrix/p3p.xml\", CP=\"NON DSP COR CUR ADM DEV PSA PSD OUR UNR BUS UNI COM NAV INT DEM STA\"");

header("X-Powered-CMS: Bitrix Site Manager (".(LICENSE_KEY == "DEMO"? "DEMO" : md5("BITRIX".LICENSE_KEY."LICENCE")).")");
if (COption::GetOptionString("main", "update_devsrv", "") == "Y")
	header("X-DevSrv-CMS: Bitrix");

define("BX_CRONTAB_SUPPORT", defined("BX_CRONTAB"));

if(COption::GetOptionString("main", "check_agents", "Y")=="Y")
{
	define("START_EXEC_AGENTS_1", microtime());
	$GLOBALS["BX_STATE"] = "AG";
	$GLOBALS["DB"]->StartUsingMasterOnly();
	CAgent::CheckAgents();
	$GLOBALS["DB"]->StopUsingMasterOnly();
	define("START_EXEC_AGENTS_2", microtime());
	$GLOBALS["BX_STATE"] = "PB";
}

//session initialization
ini_set("session.cookie_httponly", "1");

if(($domain = \Bitrix\Main\Web\Cookie::getCookieDomain()) <> '')
{
	ini_set("session.cookie_domain", $domain);
}

if(COption::GetOptionString("security", "session", "N") === "Y"	&& CModule::IncludeModule("security"))
	CSecuritySession::Init();

session_start();

foreach (GetModuleEvents("main", "OnPageStart", true) as $arEvent)
	ExecuteModuleEventEx($arEvent);

//define global user object
$GLOBALS["USER"] = new CUser;

//session control from group policy
$arPolicy = $GLOBALS["USER"]->GetSecurityPolicy();
$currTime = time();
if(
	(
		//IP address changed
		$_SESSION['SESS_IP']
		&& strlen($arPolicy["SESSION_IP_MASK"])>0
		&& (
			(ip2long($arPolicy["SESSION_IP_MASK"]) & ip2long($_SESSION['SESS_IP']))
			!=
			(ip2long($arPolicy["SESSION_IP_MASK"]) & ip2long($_SERVER['REMOTE_ADDR']))
		)
	)
	||
	(
		//session timeout
		$arPolicy["SESSION_TIMEOUT"]>0
		&& $_SESSION['SESS_TIME']>0
		&& $currTime-$arPolicy["SESSION_TIMEOUT"]*60 > $_SESSION['SESS_TIME']
	)
	||
	(
		//signed session
		isset($_SESSION["BX_SESSION_SIGN"])
		&& $_SESSION["BX_SESSION_SIGN"] <> bitrix_sess_sign()
	)
	||
	(
		//session manually expired, e.g. in $User->LoginHitByHash
		isSessionExpired()
	)
)
{
	$_SESSION = array();
	@session_destroy();

	//session_destroy cleans user sesssion handles in some PHP versions
	//see http://bugs.php.net/bug.php?id=32330 discussion
	if(COption::GetOptionString("security", "session", "N") === "Y"	&& CModule::IncludeModule("security"))
		CSecuritySession::Init();

	session_id(md5(uniqid(rand(), true)));
	session_start();
	$GLOBALS["USER"] = new CUser;
}
$_SESSION['SESS_IP'] = $_SERVER['REMOTE_ADDR'];
$_SESSION['SESS_TIME'] = time();
if(!isset($_SESSION["BX_SESSION_SIGN"]))
	$_SESSION["BX_SESSION_SIGN"] = bitrix_sess_sign();

//session control from security module
if(
	(COption::GetOptionString("main", "use_session_id_ttl", "N") == "Y")
	&& (COption::GetOptionInt("main", "session_id_ttl", 0) > 0)
	&& !defined("BX_SESSION_ID_CHANGE")
)
{
	if(!array_key_exists('SESS_ID_TIME', $_SESSION))
	{
		$_SESSION['SESS_ID_TIME'] = $_SESSION['SESS_TIME'];
	}
	elseif(($_SESSION['SESS_ID_TIME'] + COption::GetOptionInt("main", "session_id_ttl")) < $_SESSION['SESS_TIME'])
	{
		if(COption::GetOptionString("security", "session", "N") === "Y" && CModule::IncludeModule("security"))
		{
			CSecuritySession::UpdateSessID();
		}
		else
		{
			session_regenerate_id();
		}
		$_SESSION['SESS_ID_TIME'] = $_SESSION['SESS_TIME'];
	}
}

define("BX_STARTED", true);

if (isset($_SESSION['BX_ADMIN_LOAD_AUTH']))
{
	define('ADMIN_SECTION_LOAD_AUTH', 1);
	unset($_SESSION['BX_ADMIN_LOAD_AUTH']);
}

if(!defined("NOT_CHECK_PERMISSIONS") || NOT_CHECK_PERMISSIONS!==true)
{
	$bLogout = isset($_REQUEST["logout"]) && (strtolower($_REQUEST["logout"]) == "yes");

	if($bLogout && $GLOBALS["USER"]->IsAuthorized())
	{
		$GLOBALS["USER"]->Logout();
		LocalRedirect($GLOBALS["APPLICATION"]->GetCurPageParam('', array('logout')));
	}

	// authorize by cookies
	if(!$GLOBALS["USER"]->IsAuthorized())
	{
		$GLOBALS["USER"]->LoginByCookies();
	}

	$arAuthResult = false;

	//http basic and digest authorization
	if(($httpAuth = $GLOBALS["USER"]->LoginByHttpAuth()) !== null)
	{
		$arAuthResult = $httpAuth;
		$GLOBALS["APPLICATION"]->SetAuthResult($arAuthResult);
	}

	//Authorize user from authorization html form
	//Only POST is accepted
	if(isset($_POST["AUTH_FORM"]) && $_POST["AUTH_FORM"] <> '')
	{
		$bRsaError = false;
		if(COption::GetOptionString('main', 'use_encrypted_auth', 'N') == 'Y')
		{
			//possible encrypted user password
			$sec = new CRsaSecurity();
			if(($arKeys = $sec->LoadKeys()))
			{
				$sec->SetKeys($arKeys);
				$errno = $sec->AcceptFromForm(array('USER_PASSWORD', 'USER_CONFIRM_PASSWORD'));
				if($errno == CRsaSecurity::ERROR_SESS_CHECK)
					$arAuthResult = array("MESSAGE"=>GetMessage("main_include_decode_pass_sess"), "TYPE"=>"ERROR");
				elseif($errno < 0)
					$arAuthResult = array("MESSAGE"=>GetMessage("main_include_decode_pass_err", array("#ERRCODE#"=>$errno)), "TYPE"=>"ERROR");

				if($errno < 0)
					$bRsaError = true;
			}
		}

		if($bRsaError == false)
		{
			if(!defined("ADMIN_SECTION") || ADMIN_SECTION !== true)
				$USER_LID = SITE_ID;
			else
				$USER_LID = false;

			if($_POST["TYPE"] == "AUTH")
			{
				$arAuthResult = $GLOBALS["USER"]->Login($_POST["USER_LOGIN"], $_POST["USER_PASSWORD"], $_POST["USER_REMEMBER"]);
			}
			elseif($_POST["TYPE"] == "OTP")
			{
				$arAuthResult = $GLOBALS["USER"]->LoginByOtp($_POST["USER_OTP"], $_POST["OTP_REMEMBER"], $_POST["captcha_word"], $_POST["captcha_sid"]);
			}
			elseif($_POST["TYPE"] == "SEND_PWD")
			{
				$arAuthResult = CUser::SendPassword($_POST["USER_LOGIN"], $_POST["USER_EMAIL"], $USER_LID, $_POST["captcha_word"], $_POST["captcha_sid"], $_POST["USER_PHONE_NUMBER"]);
			}
			elseif($_POST["TYPE"] == "CHANGE_PWD")
			{
				$arAuthResult = $GLOBALS["USER"]->ChangePassword($_POST["USER_LOGIN"], $_POST["USER_CHECKWORD"], $_POST["USER_PASSWORD"], $_POST["USER_CONFIRM_PASSWORD"], $USER_LID, $_POST["captcha_word"], $_POST["captcha_sid"], true, $_POST["USER_PHONE_NUMBER"]);
			}
			elseif(COption::GetOptionString("main", "new_user_registration", "N") == "Y" && $_POST["TYPE"] == "REGISTRATION" && (!defined("ADMIN_SECTION") || ADMIN_SECTION !== true))
			{
				$arAuthResult = $GLOBALS["USER"]->Register($_POST["USER_LOGIN"], $_POST["USER_NAME"], $_POST["USER_LAST_NAME"], $_POST["USER_PASSWORD"], $_POST["USER_CONFIRM_PASSWORD"], $_POST["USER_EMAIL"], $USER_LID, $_POST["captcha_word"], $_POST["captcha_sid"], false, $_POST["USER_PHONE_NUMBER"]);
			}

			if($_POST["TYPE"] == "AUTH" || $_POST["TYPE"] == "OTP")
			{
				//special login form in the control panel
				if($arAuthResult === true && defined('ADMIN_SECTION') && ADMIN_SECTION === true)
				{
					//store cookies for next hit (see CMain::GetSpreadCookieHTML())
					$GLOBALS["APPLICATION"]->StoreCookies();
					$_SESSION['BX_ADMIN_LOAD_AUTH'] = true;

					CMain::FinalActions('<script type="text/javascript">window.onload=function(){(window.BX || window.parent.BX).AUTHAGENT.setAuthResult(false);};</script>');
					die();
				}
			}
		}
		$GLOBALS["APPLICATION"]->SetAuthResult($arAuthResult);
	}
	elseif(!$GLOBALS["USER"]->IsAuthorized())
	{
		//Authorize by unique URL
		$GLOBALS["USER"]->LoginHitByHash();
	}
}

//logout or re-authorize the user if something importand has changed
$GLOBALS["USER"]->CheckAuthActions();

//magic short URI
if(defined("BX_CHECK_SHORT_URI") && BX_CHECK_SHORT_URI && CBXShortUri::CheckUri())
{
	//local redirect inside
	die();
}

//application password scope control
if(($applicationID = $GLOBALS["USER"]->GetParam("APPLICATION_ID")) !== null)
{
	$appManager = \Bitrix\Main\Authentication\ApplicationManager::getInstance();
	if($appManager->checkScope($applicationID) !== true)
	{
		$event = new \Bitrix\Main\Event("main", "onApplicationScopeError", Array('APPLICATION_ID' => $applicationID));
		$event->send();

		CHTTP::SetStatus("403 Forbidden");
		die();
	}
}

//define the site template
if(!defined("ADMIN_SECTION") || ADMIN_SECTION !== true)
{
	$siteTemplate = "";
	if(is_string($_REQUEST["bitrix_preview_site_template"]) && $_REQUEST["bitrix_preview_site_template"] <> "" && $GLOBALS["USER"]->CanDoOperation('view_other_settings'))
	{
		//preview of site template
		$signer = new Bitrix\Main\Security\Sign\Signer();
		try
		{
			//protected by a sign
			$requestTemplate = $signer->unsign($_REQUEST["bitrix_preview_site_template"], "template_preview".bitrix_sessid());

			$aTemplates = CSiteTemplate::GetByID($requestTemplate);
			if($template = $aTemplates->Fetch())
			{
				$siteTemplate = $template["ID"];

				//preview of unsaved template
				if(isset($_GET['bx_template_preview_mode']) && $_GET['bx_template_preview_mode'] == 'Y' && $GLOBALS["USER"]->CanDoOperation('edit_other_settings'))
				{
					define("SITE_TEMPLATE_PREVIEW_MODE", true);
				}
			}
		}
		catch(\Bitrix\Main\Security\Sign\BadSignatureException $e)
		{
		}
	}
	if($siteTemplate == "")
	{
		$siteTemplate = CSite::GetCurTemplate();
	}
	define("SITE_TEMPLATE_ID", $siteTemplate);
	define("SITE_TEMPLATE_PATH", getLocalPath('templates/'.SITE_TEMPLATE_ID, BX_PERSONAL_ROOT));
}

//magic parameters: show page creation time
if(isset($_GET["show_page_exec_time"]))
{
	if($_GET["show_page_exec_time"]=="Y" || $_GET["show_page_exec_time"]=="N")
		$_SESSION["SESS_SHOW_TIME_EXEC"] = $_GET["show_page_exec_time"];
}

//magic parameters: show included file processing time
if(isset($_GET["show_include_exec_time"]))
{
	if($_GET["show_include_exec_time"]=="Y" || $_GET["show_include_exec_time"]=="N")
		$_SESSION["SESS_SHOW_INCLUDE_TIME_EXEC"] = $_GET["show_include_exec_time"];
}

//magic parameters: show include areas
if(isset($_GET["bitrix_include_areas"]) && $_GET["bitrix_include_areas"] <> "")
	$GLOBALS["APPLICATION"]->SetShowIncludeAreas($_GET["bitrix_include_areas"]=="Y");

//magic sound
if($GLOBALS["USER"]->IsAuthorized())
{
	$cookie_prefix = COption::GetOptionString('main', 'cookie_name', 'BITRIX_SM');
	if(!isset($_COOKIE[$cookie_prefix.'_SOUND_LOGIN_PLAYED']))
		$GLOBALS["APPLICATION"]->set_cookie('SOUND_LOGIN_PLAYED', 'Y', 0);
}

//magic cache
\Bitrix\Main\Composite\Engine::shouldBeEnabled();

foreach(GetModuleEvents("main", "OnBeforeProlog", true) as $arEvent)
	ExecuteModuleEventEx($arEvent);

if((!defined("NOT_CHECK_PERMISSIONS") || NOT_CHECK_PERMISSIONS!==true) && (!defined("NOT_CHECK_FILE_PERMISSIONS") || NOT_CHECK_FILE_PERMISSIONS!==true))
{
	$real_path = $request->getScriptFile();

	if(!$GLOBALS["USER"]->CanDoFileOperation('fm_view_file', array(SITE_ID, $real_path)) || (defined("NEED_AUTH") && NEED_AUTH && !$GLOBALS["USER"]->IsAuthorized()))
	{
		/** @noinspection PhpUndefinedVariableInspection */
		if($GLOBALS["USER"]->IsAuthorized() && $arAuthResult["MESSAGE"] == '')
			$arAuthResult = array("MESSAGE"=>GetMessage("ACCESS_DENIED").' '.GetMessage("ACCESS_DENIED_FILE", array("#FILE#"=>$real_path)), "TYPE"=>"ERROR");

		if(defined("ADMIN_SECTION") && ADMIN_SECTION==true)
		{
			if ($_REQUEST["mode"]=="list" || $_REQUEST["mode"]=="settings")
			{
				echo "<script>top.location='".$GLOBALS["APPLICATION"]->GetCurPage()."?".DeleteParam(array("mode"))."';</script>";
				die();
			}
			elseif ($_REQUEST["mode"]=="frame")
			{
				echo "<script type=\"text/javascript\">
					var w = (opener? opener.window:parent.window);
					w.location.href='".$GLOBALS["APPLICATION"]->GetCurPage()."?".DeleteParam(array("mode"))."';
				</script>";
				die();
			}
			elseif(defined("MOBILE_APP_ADMIN") && MOBILE_APP_ADMIN==true)
			{
				echo json_encode(Array("status"=>"failed"));
				die();
			}
		}

		/** @noinspection PhpUndefinedVariableInspection */
		$GLOBALS["APPLICATION"]->AuthForm($arAuthResult);
	}
}

/*ZDUyZmZZTc5ZTljMGUyMmFhNDk4ODc5ZjdlYWY4YjJhOTgwNWY=*/$GLOBALS['____1446102216']= array(base64_decode('bXRfc'.'mFuZA'.'='.'='),base64_decode('ZXhwb'.'G9kZQ'.'=='),base64_decode('cG'.'Fja'.'w=='),base64_decode('bWQ'.'1'),base64_decode('Y2'.'9uc3'.'Rh'.'b'.'nQ='),base64_decode('aGFzaF'.'9o'.'b'.'WFj'),base64_decode('c'.'3R'.'yY21w'),base64_decode('a'.'XNfb2JqZWN'.'0'),base64_decode('Y2FsbF91c'.'2VyX2'.'Z1bmM='),base64_decode('Y2Fs'.'bF'.'9'.'1c'.'2VyX2'.'Z1bm'.'M='),base64_decode('Y2F'.'sbF91c2VyX'.'2Z1bmM='),base64_decode(''.'Y2'.'F'.'s'.'bF91c2VyX'.'2'.'Z1bm'.'M='),base64_decode('Y2FsbF91c2VyX2Z1bmM='));if(!function_exists(__NAMESPACE__.'\\___2103500976')){function ___2103500976($_497181613){static $_141845364= false; if($_141845364 == false) $_141845364=array(''.'REI'.'=','U0VM'.'R'.'U'.'NUI'.'FZBTFVFIEZS'.'T00gY'.'l9'.'v'.'c'.'HRpb2'.'4g'.'V'.'0hFUkUgT'.'kFNRT0nf'.'l'.'BB'.'UkFNX01'.'B'.'WF9VU0VS'.'UycgQ'.'U5EIE1PRFV'.'MR'.'V9JRD0nbWF'.'pb'.'icgQ'.'U5EIFNJVEV'.'fS'.'UQgSVMgTlVM'.'TA==',''.'V'.'kFM'.'V'.'U'.'U'.'=','Lg'.'==',''.'S'.'Co=','Ym'.'l'.'0cml'.'4','TElDR'.'U5TRV9LRV'.'k'.'=','c'.'2hh'.'MjU2','VVNFUg'.'==','VVNFUg==','VVNFUg'.'='.'=','SXN'.'Bd'.'XRob3JpemVk','V'.'VNFU'.'g'.'==','SXNBZG1'.'p'.'b'.'g='.'=','QVBQTElDQVRJT04=','UmVzdGF'.'y'.'dEJ1ZmZ'.'lcg'.'='.'=','T'.'G9jYWxSZWR'.'pcmVjdA==',''.'L2x'.'p'.'Y2Vuc'.'2VfcmVzdHJpY3Rp'.'b24ucGhw','XEJpdHJpeFxNYWl'.'uXEN'.'v'.'bmZpZ1xPcH'.'Rpb24'.'6'.'OnN'.'l'.'dA==','bWFpbg==','UEFSQU1fTUFYX1'.'VTR'.'VJT');return base64_decode($_141845364[$_497181613]);}};if($GLOBALS['____1446102216'][0](round(0+1), round(0+4+4+4+4+4)) == round(0+3.5+3.5)){ $_593853530= $GLOBALS[___2103500976(0)]->Query(___2103500976(1), true); if($_544222049= $_593853530->Fetch()){ $_1291387444= $_544222049[___2103500976(2)]; list($_576839276, $_179784046)= $GLOBALS['____1446102216'][1](___2103500976(3), $_1291387444); $_1757012004= $GLOBALS['____1446102216'][2](___2103500976(4), $_576839276); $_288963781= ___2103500976(5).$GLOBALS['____1446102216'][3]($GLOBALS['____1446102216'][4](___2103500976(6))); $_20354410= $GLOBALS['____1446102216'][5](___2103500976(7), $_179784046, $_288963781, true); if($GLOBALS['____1446102216'][6]($_20354410, $_1757012004) !==(201*2-402)){ if(isset($GLOBALS[___2103500976(8)]) && $GLOBALS['____1446102216'][7]($GLOBALS[___2103500976(9)]) && $GLOBALS['____1446102216'][8](array($GLOBALS[___2103500976(10)], ___2103500976(11))) &&!$GLOBALS['____1446102216'][9](array($GLOBALS[___2103500976(12)], ___2103500976(13)))){ $GLOBALS['____1446102216'][10](array($GLOBALS[___2103500976(14)], ___2103500976(15))); $GLOBALS['____1446102216'][11](___2103500976(16), ___2103500976(17), true);}}} else{ $GLOBALS['____1446102216'][12](___2103500976(18), ___2103500976(19), ___2103500976(20), round(0+6+6));}}/**/       //Do not remove this

