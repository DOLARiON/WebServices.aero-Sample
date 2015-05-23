<?php
/**
 * @package		Travel Agency Web Services (WebServices.aero)
 * @subpackage	TA\Init
 * @copyright	Copyright (C) 2000 - 2015 Eugene Design. All rights reserved.
 * @license		GNU/GPLv2
 */

// No direct access.
defined('_TAEXEC') or die;

/**
 * ==============================================================================================================================
 *  Init
 * ==============================================================================================================================
 */

if ( ! isset($_SERVER['REQUEST_TIME_FLOAT']))
	$_SERVER['REQUEST_TIME_FLOAT'] = microtime(true);

/**
 * ==============================================================================================================================
 *  / Init
 * ==============================================================================================================================
 */

/**
 * ==============================================================================================================================
 *  Path
 * ==============================================================================================================================
 */

if ( ! defined('TAPATH_BASE'))
	define('TAPATH_BASE', dirname(__FILE__));

//Defines.
define('DS',		DIRECTORY_SEPARATOR);
define('DOT',		'.');
define('COMMA',		',');
define('DASH',		'-');
define('EOL',		"\n");
define('EOL_MAC',	"\r");
define('EOL_WIN',	"\r\n");
define('SPR',		';');
define('SEPARATE',	';');
define('COLON',		':');
define('SPACE',		' ');
define('YES',		'YES');
define('NO',		'NO');

define('TAPATH_CACHE',			TAPATH_BASE.DS.'cache');
define('TAPATH_AJAX',			TAPATH_BASE.DS.'ajax');
define('TAPATH_LIBRARIES',		TAPATH_BASE.DS.'libraries');
define('TAPATH_TMP',			TAPATH_BASE.DS.'tmp');

/**
 * ==============================================================================================================================
 *  / Path
 * ==============================================================================================================================
 */

