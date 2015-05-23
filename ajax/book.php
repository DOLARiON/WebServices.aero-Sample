<?php
/**
 * @package		Travel Agency Web Services (WebServices.aero)
 * @subpackage	AJAX\Book
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

parse_str(urldecode($_REQUEST['Data']), $Data);

/**
 * ==============================================================================================================================
 *  Прописываем Паспорта
 * ==============================================================================================================================
 */

foreach ($Data['Travellers'] AS $TravellerType => $Travellers)
{
	foreach ($Travellers AS $TravellerID => $Traveller)
	{
		if ( ! (int)$Traveller['Document']['ExpireDate']['Year'] || ! (int)$Traveller['Document']['ExpireDate']['Month'] || ! (int)$Traveller['Document']['ExpireDate']['Day'])
		{
			$Birthday	= strtotime($Traveller['Birthday']['Year'].'-'.$Traveller['Birthday']['Month'].'-'.$Traveller['Birthday']['Day']);
			$Years		= (int)((date('Ymd',time()) - date('Ymd',$Birthday)) / 10000);

			if ($Years < 20)
				$ExpireDate = strtotime(($Traveller['Birthday']['Year']+20).'-'.$Traveller['Birthday']['Month'].'-'.$Traveller['Birthday']['Day']);
			elseif ($Years < 45)
				$ExpireDate = strtotime(($Traveller['Birthday']['Year']+45).'-'.$Traveller['Birthday']['Month'].'-'.$Traveller['Birthday']['Day']);
			else
				$ExpireDate = strtotime('+20 YEARS');

			$Data['Travellers'][$TravellerType][$TravellerID]['Document']['ExpireDate']['Day']		= date('j',$ExpireDate);
			$Data['Travellers'][$TravellerType][$TravellerID]['Document']['ExpireDate']['Month']	= date('n',$ExpireDate);
			$Data['Travellers'][$TravellerType][$TravellerID]['Document']['ExpireDate']['Year']		= date('Y',$ExpireDate);
		}

		if (strpos($Data['Travellers'][$TravellerType][$TravellerID]['Name'], ' '))
			list($Data['Travellers'][$TravellerType][$TravellerID]['Name'], $Data['Travellers'][$TravellerType][$TravellerID]['Patronymic']) = explode(' ', $Data['Travellers'][$TravellerType][$TravellerID]['Name']);
	}
}

/**
 * ==============================================================================================================================
 *  / Прописываем Паспорта
 * ==============================================================================================================================
 */

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
		'BookID'		=> (string)$_REQUEST['ID'],
		'Travellers'	=> $Data['Travellers'],
		'Contacts'		=> $Data['Contacts'],
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
$Result	= $Client->Book($Access, $Request, $Params);

/**
 * ==============================================================================================================================
 *  / Request
 * ==============================================================================================================================
 */

/**
 * ==============================================================================================================================
 *  Parce
 * ==============================================================================================================================
 */

if (is_array($Result->Errors) && count($Result->Errors))
{
	if (is_object($Result->Errors))
		$Result->Errors = array($Result->Errors);
?>
<div class="errors" style="display:none">
<div class="alert alert-error">
	<h5>Допущены ошибки при заполнении формы:</h5>
	<ul>
	<?php foreach ($Result->Errors AS $Error){ ?>
		<li><?php echo $Error->Message;?></li>
	<?php } ?>
	</ul>
</div>
</div>
<?php
}
?>

<?php
if (isset($Result->Result->PNR) && strlen($Result->Result->PNR) && isset($Result->Result->Surnames) && count($Result->Result->Surnames))
{
?>

<script type="text/javascript">
$(function()
{
	$('#BookModal .modal-footer p').show();
	window.location = '/check/?pnr=<?php echo $Result->Result->PNR;?>&surname=<?php echo current($Result->Result->Surnames);?>';
});
</script>

<?php
}
else
{
?>

<script type="text/javascript">
$(function()
{
	$('#BookModal input').removeAttr('disabled');
	$('#BookModal select').removeAttr('disabled');
	$('#BookModal button').removeAttr('disabled');
});
</script>

<div class="errors" style="display:none">
<div class="alert alert-block">
	<h5>К сожалению, мы не смогли подтвердить ваше бронирование.</h5>
	<p>Вероятнее всего, нам не удалось подтвердить место на одном из сегментов перелета.</p>
</div>
</div>

<?php
}
?>
