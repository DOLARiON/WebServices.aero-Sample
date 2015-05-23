<?php
/**
 * @package		Travel Agency Web Services (WebServices.aero)
 * @subpackage	AJAX\Information
 * @copyright	Copyright (C) 2000 - 2015 Eugene Design. All rights reserved.
 * @license		GNU/GPLv2
 */

// We are a valid entry point.
define('_TAEXEC', TRUE);

define('TAPATH_BASE', dirname(dirname(__FILE__)));

require TAPATH_BASE.DIRECTORY_SEPARATOR.'init.php';
require TAPATH_BASE.DIRECTORY_SEPARATOR.'config.php';
require TAPATH_LIBRARIES.DS.'BaseJsonRpcClient.php';
require TAPATH_LIBRARIES.DS.'Helpers.php';

/**
 * ==============================================================================================================================
 *  Init
 * ==============================================================================================================================
 */

set_time_limit(1200);
error_reporting(E_ALL & ~E_NOTICE);
ini_set('display_errors', TRUE);

date_default_timezone_set( TAConfigGeneral::$Offset			);
     mb_internal_encoding( TAConfigGeneral::$Encoding		);
                setlocale( LC_ALL, TAConfigGeneral::$Locale	);

header('Cache-Control: no-cache, must-revalidate');
header('Content-type: text/html; charset=utf-8');
header('Expires: Mon, 20 Jun 1997 05:00:00 GMT');

/**
 * ==============================================================================================================================
 *  / Init
 * ==============================================================================================================================
 */

if (extension_loaded('zlib'))
	ob_start('ob_gzhandler');

/**
 * ==============================================================================================================================
 *  Params
 * ==============================================================================================================================
 */

$Access = array(
		'Type'			=> TAConfigAccess::$AuthType,
		'System'		=> TAConfigAccess::$AuthSystem,
		'Key'			=> TAConfigAccess::$AuthKey,
		'UserIP'		=> Helpers::UserIP(),
		'UserUUID'		=> '',
	);

$Request = array(
		'PNR'		=> (string)$_REQUEST['pnr'],
		'Surname'	=> (string)$_REQUEST['surname'],
	);

$Params = array(
		'Compress'		=> 'GZip',
		'Language'		=> 'RU',
		'Currency'		=> array('RUB'),
	);

/**
 * ==============================================================================================================================
 *  / Params
 * ==============================================================================================================================
 */

/**
 * ==============================================================================================================================
 *  Request
 * ==============================================================================================================================
 */

$Client	= new BaseJsonRpcClient(TAConfigAccess::$URLExtended);
$Result	= $Client->Cancel($Access, $Request, $Params);

/**
 * ==============================================================================================================================
 *  / Request
 * ==============================================================================================================================
 */

header('Location: '.$_SERVER['HTTP_REFERER']);
