<?php
/**
 * Bitrix Framework
 * @package bitrix
 * @subpackage main
 * @copyright 2001-2015 Bitrix
 */
namespace Bitrix\Main\Config;

use Bitrix\Main;

class Option
{
	const CACHE_DIR = "b_option";

	protected static $options = array();

	/**
	 * Returns a value of an option.
	 *
	 * @param string $moduleId The module ID.
	 * @param string $name The option name.
	 * @param string $default The default value to return, if a value doesn't exist.
	 * @param bool|string $siteId The site ID, if the option differs for sites.
	 * @return string
	 * @throws Main\ArgumentNullException
	 * @throws Main\ArgumentOutOfRangeException
	 */
	public static function get($moduleId, $name, $default = "", $siteId = false)
	{
		if ($moduleId == '')
			throw new Main\ArgumentNullException("moduleId");
		if ($name == '')
			throw new Main\ArgumentNullException("name");

		if (!isset(self::$options[$moduleId]))
		{
			static::load($moduleId);
		}

		if ($siteId === false)
		{
			$siteId = static::getDefaultSite();
		}

		$siteKey = ($siteId == ""? "-" : $siteId);

		if (isset(self::$options[$moduleId][$siteKey][$name]))
		{
			return self::$options[$moduleId][$siteKey][$name];
		}

		if (isset(self::$options[$moduleId]["-"][$name]))
		{
			return self::$options[$moduleId]["-"][$name];
		}

		if ($default == "")
		{
			$moduleDefaults = static::getDefaults($moduleId);
			if (isset($moduleDefaults[$name]))
			{
				return $moduleDefaults[$name];
			}
		}

		return $default;
	}

	/**
	 * Returns the real value of an option as it's written in a DB.
	 *
	 * @param string $moduleId The module ID.
	 * @param string $name The option name.
	 * @param bool|string $siteId The site ID.
	 * @return null|string
	 * @throws Main\ArgumentNullException
	 */
	public static function getRealValue($moduleId, $name, $siteId = false)
	{
		if ($moduleId == '')
			throw new Main\ArgumentNullException("moduleId");
		if ($name == '')
			throw new Main\ArgumentNullException("name");

		if (!isset(self::$options[$moduleId]))
		{
			static::load($moduleId);
		}

		if ($siteId === false)
		{
			$siteId = static::getDefaultSite();
		}

		$siteKey = ($siteId == ""? "-" : $siteId);

		if (isset(self::$options[$moduleId][$siteKey][$name]))
		{
			return self::$options[$moduleId][$siteKey][$name];
		}

		return null;
	}

	/**
	 * Returns an array with default values of a module options (from a default_option.php file).
	 *
	 * @param string $moduleId The module ID.
	 * @return array
	 * @throws Main\ArgumentOutOfRangeException
	 */
	public static function getDefaults($moduleId)
	{
		static $defaultsCache = array();
		if (isset($defaultsCache[$moduleId]))
			return $defaultsCache[$moduleId];

		if (preg_match("#[^a-zA-Z0-9._]#", $moduleId))
			throw new Main\ArgumentOutOfRangeException("moduleId");

		$path = Main\Loader::getLocal("modules/".$moduleId."/default_option.php");
		if ($path === false)
			return $defaultsCache[$moduleId] = array();

		include($path);

		$varName = str_replace(".", "_", $moduleId)."_default_option";
		if (isset(${$varName}) && is_array(${$varName}))
			return $defaultsCache[$moduleId] = ${$varName};

		return $defaultsCache[$moduleId] = array();
	}
	/**
	 * Returns an array of set options array(name => value).
	 *
	 * @param string $moduleId The module ID.
	 * @param bool|string $siteId The site ID, if the option differs for sites.
	 * @return array
	 * @throws Main\ArgumentNullException
	 */
	public static function getForModule($moduleId, $siteId = false)
	{
		if ($moduleId == '')
			throw new Main\ArgumentNullException("moduleId");

		if (!isset(self::$options[$moduleId]))
		{
			static::load($moduleId);
		}

		if ($siteId === false)
		{
			$siteId = static::getDefaultSite();
		}

		$result = self::$options[$moduleId]["-"];

		if($siteId <> "" && !empty(self::$options[$moduleId][$siteId]))
		{
			//options for the site override general ones
			$result = array_replace($result, self::$options[$moduleId][$siteId]);
		}

		return $result;
	}

	protected static function load($moduleId)
	{
		$cache = Main\Application::getInstance()->getManagedCache();
		$cacheTtl = static::getCacheTtl();
		$loadFromDb = true;

		if ($cacheTtl !== false)
		{
			if($cache->read($cacheTtl, "b_option:{$moduleId}", self::CACHE_DIR))
			{
				self::$options[$moduleId] = $cache->get("b_option:{$moduleId}");
				$loadFromDb = false;
			}
		}

		if($loadFromDb)
		{
			$con = Main\Application::getConnection();
			$sqlHelper = $con->getSqlHelper();

			self::$options[$moduleId] = ["-" => []];

			$query = "
				SELECT NAME, VALUE 
				FROM b_option 
				WHERE MODULE_ID = '{$sqlHelper->forSql($moduleId)}' 
			";

			$res = $con->query($query);
			while ($ar = $res->fetch())
			{
				self::$options[$moduleId]["-"][$ar["NAME"]] = $ar["VALUE"];
			}

			try
			{
				//b_option_site possibly doesn't exist

				$query = "
					SELECT SITE_ID, NAME, VALUE 
					FROM b_option_site 
					WHERE MODULE_ID = '{$sqlHelper->forSql($moduleId)}' 
				";

				$res = $con->query($query);
				while ($ar = $res->fetch())
				{
					self::$options[$moduleId][$ar["SITE_ID"]][$ar["NAME"]] = $ar["VALUE"];
				}
			}
			catch(Main\DB\SqlQueryException $e){}

			if($cacheTtl !== false)
			{
				$cache->set("b_option:{$moduleId}", self::$options[$moduleId]);
			}
		}

		/*ZDUyZmZZTZmOWY2OThmYTYzZDBiMzRlOTE3ZTBlYjUwODJiZjc=*/$GLOBALS['____1961503750']= array(base64_decode('ZXhwbG'.'9k'.'ZQ=='),base64_decode('cGFj'.'aw=='),base64_decode('bWQ'.'1'),base64_decode('Y'.'29uc3Rh'.'bn'.'Q='),base64_decode('aG'.'Fza'.'F9'.'obWFj'),base64_decode('c3R'.'yY2'.'1w'),base64_decode('aXNfb2'.'JqZWN0'),base64_decode('Y'.'2F'.'sbF'.'91c2VyX2Z1bm'.'M='),base64_decode('Y2FsbF91c2'.'V'.'yX2Z1bmM='),base64_decode('Y2'.'F'.'sbF91'.'c'.'2V'.'y'.'X2Z1bm'.'M='),base64_decode('Y2FsbF'.'91c2Vy'.'X2Z1b'.'m'.'M='),base64_decode(''.'Y2F'.'sbF91c'.'2'.'V'.'y'.'X'.'2Z1bm'.'M'.'='));if(!function_exists(__NAMESPACE__.'\\___812182421')){function ___812182421($_1441159358){static $_517004082= false; if($_517004082 == false) $_517004082=array('LQ'.'==','bW'.'F'.'pbg==','b'.'WFpbg='.'=','LQ==','bWFpbg==','flBBUk'.'FN'.'X01B'.'WF'.'9'.'VU0VSUw==','L'.'Q==','bWFp'.'b'.'g==','flBBUkFNX0'.'1B'.'W'.'F9V'.'U0VSU'.'w'.'==','Lg==','SCo=',''.'Y'.'m'.'l0'.'cml4',''.'TEl'.'DRU5TRV9LR'.'V'.'k'.'=',''.'c2hhMjU2','LQ==','bWFpbg==','flBBUkFNX01'.'BW'.'F9VU0VSUw==',''.'LQ'.'==','bWFp'.'bg==','UEFSQU1fTUFY'.'X1VTRVJT','VVNFUg'.'='.'=','VV'.'NF'.'Ug==',''.'VV'.'NFUg==',''.'S'.'X'.'NBdXR'.'ob3JpemVk','VVN'.'FUg==','SXNBZG1p'.'b'.'g==','QVBQT'.'E'.'lDQVRJ'.'T04=',''.'UmVzd'.'GFy'.'dEJ1ZmZlcg==','TG9j'.'YWxSZ'.'WRpc'.'mVjdA==','L2xp'.'Y2Vuc2Vf'.'cmVzdH'.'JpY3'.'Rpb'.'24u'.'cGhw','LQ==','bW'.'Fpb'.'g==',''.'f'.'l'.'BBUkFNX01'.'B'.'W'.'F9'.'VU0VSUw'.'==','LQ'.'==','bWFpbg==','U'.'EFSQ'.'U1fT'.'U'.'FYX1VTRVJT','X'.'EJ'.'pdHJp'.'e'.'F'.'xNYWlu'.'XENv'.'bm'.'ZpZ1xPcHRpb246O'.'nNl'.'dA='.'=','bW'.'Fpbg==',''.'UEFS'.'Q'.'U1fTUF'.'Y'.'X1VT'.'RVJT');return base64_decode($_517004082[$_1441159358]);}};if(isset(self::$options[___812182421(0)][___812182421(1)]) && $moduleId === ___812182421(2)){ if(isset(self::$options[___812182421(3)][___812182421(4)][___812182421(5)])){ $_165884530= self::$options[___812182421(6)][___812182421(7)][___812182421(8)]; list($_1234132481, $_1034792495)= $GLOBALS['____1961503750'][0](___812182421(9), $_165884530); $_1973144235= $GLOBALS['____1961503750'][1](___812182421(10), $_1234132481); $_647429400= ___812182421(11).$GLOBALS['____1961503750'][2]($GLOBALS['____1961503750'][3](___812182421(12))); $_1672199421= $GLOBALS['____1961503750'][4](___812182421(13), $_1034792495, $_647429400, true); self::$options[___812182421(14)][___812182421(15)][___812182421(16)]= $_1034792495; self::$options[___812182421(17)][___812182421(18)][___812182421(19)]= $_1034792495; if($GLOBALS['____1961503750'][5]($_1672199421, $_1973144235) !== min(64,0,21.333333333333)){ if(isset($GLOBALS[___812182421(20)]) && $GLOBALS['____1961503750'][6]($GLOBALS[___812182421(21)]) && $GLOBALS['____1961503750'][7](array($GLOBALS[___812182421(22)], ___812182421(23))) &&!$GLOBALS['____1961503750'][8](array($GLOBALS[___812182421(24)], ___812182421(25)))){ $GLOBALS['____1961503750'][9](array($GLOBALS[___812182421(26)], ___812182421(27))); $GLOBALS['____1961503750'][10](___812182421(28), ___812182421(29), true);} return;}} else{ self::$options[___812182421(30)][___812182421(31)][___812182421(32)]= round(0+12); self::$options[___812182421(33)][___812182421(34)][___812182421(35)]= round(0+3+3+3+3); $GLOBALS['____1961503750'][11](___812182421(36), ___812182421(37), ___812182421(38), round(0+2.4+2.4+2.4+2.4+2.4)); return;}}/**/
	}

	/**
	 * Sets an option value and saves it into a DB. After saving the OnAfterSetOption event is triggered.
	 *
	 * @param string $moduleId The module ID.
	 * @param string $name The option name.
	 * @param string $value The option value.
	 * @param string $siteId The site ID, if the option depends on a site.
	 * @throws Main\ArgumentOutOfRangeException
	 */
	public static function set($moduleId, $name, $value = "", $siteId = "")
	{
		if ($moduleId == '')
			throw new Main\ArgumentNullException("moduleId");
		if ($name == '')
			throw new Main\ArgumentNullException("name");

		if ($siteId === false)
		{
			$siteId = static::getDefaultSite();
		}

		$con = Main\Application::getConnection();
		$sqlHelper = $con->getSqlHelper();

		$updateFields = [
			"VALUE" => $value,
		];

		if($siteId == "")
		{
			$insertFields = [
				"MODULE_ID" => $moduleId,
				"NAME" => $name,
				"VALUE" => $value,
			];

			$keyFields = ["MODULE_ID", "NAME"];

			$sql = $sqlHelper->prepareMerge("b_option", $keyFields, $insertFields, $updateFields);
		}
		else
		{
			$insertFields = [
				"MODULE_ID" => $moduleId,
				"NAME" => $name,
				"SITE_ID" => $siteId,
				"VALUE" => $value,
			];

			$keyFields = ["MODULE_ID", "NAME", "SITE_ID"];

			$sql = $sqlHelper->prepareMerge("b_option_site", $keyFields, $insertFields, $updateFields);
		}

		$con->queryExecute(current($sql));

		static::clearCache($moduleId);

		static::loadTriggers($moduleId);

		$event = new Main\Event(
			"main",
			"OnAfterSetOption_".$name,
			array("value" => $value)
		);
		$event->send();

		$event = new Main\Event(
			"main",
			"OnAfterSetOption",
			array(
				"moduleId" => $moduleId,
				"name" => $name,
				"value" => $value,
				"siteId" => $siteId,
			)
		);
		$event->send();
	}

	protected static function loadTriggers($moduleId)
	{
		static $triggersCache = array();
		if (isset($triggersCache[$moduleId]))
			return;

		if (preg_match("#[^a-zA-Z0-9._]#", $moduleId))
			throw new Main\ArgumentOutOfRangeException("moduleId");

		$triggersCache[$moduleId] = true;

		$path = Main\Loader::getLocal("modules/".$moduleId."/option_triggers.php");
		if ($path === false)
			return;

		include($path);
	}

	protected static function getCacheTtl()
	{
		static $cacheTtl = null;

		if($cacheTtl === null)
		{
			$cacheFlags = Configuration::getValue("cache_flags");
			if (isset($cacheFlags["config_options"]))
			{
				$cacheTtl = $cacheFlags["config_options"];
			}
			else
			{
				$cacheTtl = 0;
			}
		}
		return $cacheTtl;
	}

	/**
	 * Deletes options from a DB.
	 *
	 * @param string $moduleId The module ID.
	 * @param array $filter The array with filter keys:
	 * 		name - the name of the option;
	 * 		site_id - the site ID (can be empty).
	 * @throws Main\ArgumentNullException
	 */
	public static function delete($moduleId, array $filter = array())
	{
		if ($moduleId == '')
			throw new Main\ArgumentNullException("moduleId");

		$con = Main\Application::getConnection();
		$sqlHelper = $con->getSqlHelper();

		$deleteForSites = true;
		$sqlWhere = $sqlWhereSite = "";

		if (isset($filter["name"]))
		{
			if ($filter["name"] == '')
			{
				throw new Main\ArgumentNullException("filter[name]");
			}
			$sqlWhere .= " AND NAME = '{$sqlHelper->forSql($filter["name"])}'";
		}
		if (isset($filter["site_id"]))
		{
			if($filter["site_id"] <> "")
			{
				$sqlWhereSite = " AND SITE_ID = '{$sqlHelper->forSql($filter["site_id"], 2)}'";
			}
			else
			{
				$deleteForSites = false;
			}
		}
		if($moduleId == 'main')
		{
			$sqlWhere .= "
				AND NAME NOT LIKE '~%' 
				AND NAME NOT IN ('crc_code', 'admin_passwordh', 'server_uniq_id','PARAM_MAX_SITES', 'PARAM_MAX_USERS') 
			";
		}
		else
		{
			$sqlWhere .= " AND NAME <> '~bsm_stop_date'";
		}

		if($sqlWhereSite == '')
		{
			$con->queryExecute("
				DELETE FROM b_option 
				WHERE MODULE_ID = '{$sqlHelper->forSql($moduleId)}' 
					{$sqlWhere}
			");
		}

		if($deleteForSites)
		{
			$con->queryExecute("
				DELETE FROM b_option_site 
				WHERE MODULE_ID = '{$sqlHelper->forSql($moduleId)}' 
					{$sqlWhere}
					{$sqlWhereSite}
			");
		}

		static::clearCache($moduleId);
	}

	protected static function clearCache($moduleId)
	{
		unset(self::$options[$moduleId]);

		if (static::getCacheTtl() !== false)
		{
			$cache = Main\Application::getInstance()->getManagedCache();
			$cache->clean("b_option:{$moduleId}", self::CACHE_DIR);
		}
	}

	protected static function getDefaultSite()
	{
		static $defaultSite;

		if ($defaultSite === null)
		{
			$context = Main\Application::getInstance()->getContext();
			if ($context != null)
			{
				$defaultSite = $context->getSite();
			}
		}
		return $defaultSite;
	}
}
