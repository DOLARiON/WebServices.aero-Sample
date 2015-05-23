<?php
/**
 * @package		Travel Agency Web Services (WebServices.aero)
 * @subpackage	Check\Index
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
 *  Hide Function
 * ==============================================================================================================================
 */

function HideDoc($Var)
{
	$Var	= preg_split("//u", $Var, -1, PREG_SPLIT_NO_EMPTY);
	$Count	= round(count($Var)/3);

	for ($i=$Count;$i<($Count*2);$i++)
		$Var[$i] = '*';

	return implode('', $Var);
}

function HidePhone($Var)
{
	$Var	= preg_split("//u", $Var, -1, PREG_SPLIT_NO_EMPTY);
	$Count	= round(count($Var)/3);

	for ($i=$Count;$i<($Count*2);$i++)
		$Var[$i] = '*';

	return implode('', $Var);
}

function HideEmail($Var)
{
	$Temp	= explode('@', $Var);

	$Name	= current($Temp);
	$Count	= round(mb_strlen($Name)/2);

	for ($i=0;$i<$Count;$i++)
		$Name{$i} = '*';

	$Temp[0]	= $Name;

	return mb_strtolower(implode('@', $Temp));
}

/**
 * ==============================================================================================================================
 *  / Hide Function
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
$Result	= $Client->Check($Access, $Request, $Params);

/**
 * ==============================================================================================================================
 *  / Request
 * ==============================================================================================================================
 */

/**
 * ==============================================================================================================================
 *  HTML
 * ==============================================================================================================================
 */

?><!DOCTYPE html>
<html lang="ru">
<head>
	<base href="<?php echo ($_SERVER['SERVER_PORT']=='80')?'http':'https';?>://<?php echo $_SERVER['HTTP_HOST'];?>/">
	<title>WebServices.aero - Sample</title>

	<!-- Meta -->
	<meta charset="UTF-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge">

	<!--[if lt IE 9]>
	<script src="//html5shim.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->

	<!-- Styles -->
	<link href="/css/bootstrap.min.css" rel="stylesheet" />
	<link href="/css/style.css" rel="stylesheet" />

	<link rel="shortcut icon" href="/favicon.png" />

	<!--[if lt IE 9]>
	<script type="text/javascript" src="/js/modernizr.custom.js"></script>
	<![endif]-->

	<script type="text/javascript" src="/js/jquery-2.1.4.min.js"></script>
	<script type="text/javascript" src="/js/bootstrap.min.js"></script>
</head>
<body>

<?php
if ( ! isset($Result->Result->PNR))
{
?>

<div class="container">
	<div class="row-fluid">
		<div class="span9">

<div class="alert">
	К сожалению, мы не можем найти ваше бронирование.
	<br>Пожалуйста, проверьте номер бронирования и фамилию и повторите попытку.
</div>

		</div>
		<div class="span3">
			<div class="well-alt">
				<form action="/check/" method="get" style="margin:0px;">
					<input type="text" name="pnr" placeholder="№ брони" value="" class="input-medium">
					<input type="text" name="surname" placeholder="Фамилия" value="" class="input-medium">
					<button type="submit" class="btn btn-success"><i class="icon-search icon-white"></i> Найти</button>
				</form>
			</div>

			<ul style="margin-top:30px;">
				<li><a href="/#" target="_blank">Как обменять/вернуть авиабилет</a></li>
				<li><a href="/#" target="_blank">Общие стандарты багажа</a></li>
				<li><a href="/#" target="_blank">Часто задаваемые вопросы</a></li>
				<li><a href="/#" target="_blank">Договор публичной оферты</a></li>
			</ul>
		</div>
	</div>
</div>

</body>
</html>

<?php
	return;
}
?>

<?php
$PNRs = array();
$PNRs[] = $Result->Result->PNR;

if (isset($Result->Result->Packages) && is_array($Result->Result->Packages) && count($Result->Result->Packages))
	foreach ($Result->Result->Packages AS $Package)
	{
		if ($Package->System != 'Avia')
			continue;

		$PNRs[] = $Package->PNR;
	}
?>

<div class="container">
	<div class="row-fluid">
		<div class="span9">

<h3><a href="/" style="text-decoration:underline;">Авиабилеты</a> &rarr; Бронирование: <?php echo implode(', ', $PNRs);?></h3>

<input type="hidden" id="RequestPNR" value="<?php echo $Result->Result->PNR;?>">
<input type="hidden" id="RequestSurname" value="<?php echo reset($Result->Result->Surnames);?>">

<?php if (in_array($Result->Result->Status, array('Cancelled','Voided','Refunded'))){ ?>
<div class="alert">
	Ваше бронирование отменено. Ниже представлена архивная информация.
</div>
<?php } ?>

<?php
if (is_array($Result->Notifications) && count($Result->Notifications))
{
	$Notifications = array();

	foreach ($Result->Notifications AS $Notification)
		$Notifications[] = $Notification->Message;
?>
<div class="alert alert-info">
	<strong>Системное сообщение:</strong> <?php echo implode('.<br>', $Notifications);?>.
</div>
<?php
}
?>

<div id="FareRulesModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="width:750px;">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
		<h3 id="myModalLabel">Правила тарифа</h3>
	</div>
	<div class="modal-body">
		<div class="results"></div>
		<div id="ProgressBarFareRules" class="loading hide">
			<div class="progress progress-striped active">
				<div class="bar" style="width:0%;">0%</div>
			</div>
		</div>
	</div>
</div>

<div class="row-fluid results" style="margin-bottom:20px">
<?php

$ArrivalAirport	= '';
$AirportChanged	= FALSE;

foreach ($Result->Result->Itineraries AS $VariantID => $Variant)
{
	$FirstSegment	= current($Variant->Segments);
	$LastSegment	= end($Variant->Segments);

	$VariantID--;
?>

<?php if ($VariantID > 0 && $VariantID&1){ ?>
</div>
<div class="row-fluid results" style="margin-bottom:20px">
<?php } ?>

	<div class="span6">
		<h3 class="label"><?php echo $FirstSegment->DepartureCityName;?> - <?php echo $LastSegment->ArrivalCityName;?></h3>
		<div class="highlight active">
		<?php
		$SegmentNum		= 0;
		$SegmentsTotal	= count($Variant->Segments);

		foreach ($Variant->Segments AS $SegmentID => &$Segment){

			$SegmentNum++;

			//** Время стыковки
			if ($SegmentID > 0){
				//** Время в пересадки
				$sTime	= new stdClass();
				$sTime->source	= round(($Segment->DepartureDate-$lastArrivalDate)/60);

				if ($sTime->source >= 1440){ // Если больше суток
					$sTime->days	= floor($sTime->source/1440);
					$sTime->hours	= $sTime->source - ($sTime->days*1440);
					$sTime->hours	= floor($sTime->hours/60);
					$sTime->minutes	= $sTime->source - ($sTime->days*1440) - ($sTime->hours*60);

					if ($sTime->days || $sTime->hours || $sTime->minutes)
						$sTime		= $sTime->days.'д.'.$sTime->hours.'ч.'.$sTime->minutes.'м.';
					else
						$sTime		= FALSE;
				} else {
					$sTime->hours	= floor($sTime->source/60);
					$sTime->minutes	= $sTime->source - ($sTime->days*1440) - ($sTime->hours*60);

					if ($sTime->hours || $sTime->minutes)
						$sTime		= $sTime->hours.'ч.'.$sTime->minutes.'м.';
					else
						$sTime		= FALSE;
				}
			}

			if ($ArrivalAirport && $ArrivalAirport != $Segment->DepartureAirport)
				$AirportChanged	= TRUE;

			$ArrivalAirport		= $Segment->ArrivalAirport;
			$lastArrivalDate	= $Segment->ArrivalDate;

			//** Время в пути
			$TIME	= FALSE;

			if ($SegmentsTotal == $SegmentNum)
			{
				$TIME	= new stdClass();
				$TIME->source	= $Variant->Duration;

				if ($TIME->source >= 1440){ // Если больше суток
					$TIME->days		= floor($TIME->source/1440);
					$TIME->hours	= $TIME->source - ($TIME->days*1440);
					$TIME->hours	= floor($TIME->hours/60);
					$TIME->minutes	= $TIME->source - ($TIME->days*1440) - ($TIME->hours*60);

					if ($TIME->days || $TIME->hours || $TIME->minutes)
						$TIME		= $TIME->days.'д.'.$TIME->hours.'ч.'.$TIME->minutes.'м.';
					else
						$TIME		= FALSE;
				} else {
					$TIME->hours	= floor($TIME->source/60);
					$TIME->minutes	= $TIME->source - ($TIME->days*1440) - ($TIME->hours*60);

					if ($TIME->hours || $TIME->minutes)
						$TIME		= $TIME->hours.'ч.'.$TIME->minutes.'м.';
					else
						$TIME		= FALSE;
				}
			}

			$DepartureName	= $Segment->DepartureCityName;

			if ($Segment->DepartureCityName !== $Segment->DepartureAirportName)
				$DepartureName = $DepartureName.', '.$Segment->DepartureAirportName;

			$ArrivalName	= $Segment->ArrivalCityName;

			if ($Segment->ArrivalCityName !== $Segment->ArrivalAirportName)
				$ArrivalName = $ArrivalName.', '.$Segment->ArrivalAirportName;

			$SubClassExtended = array();
			if ($Segment->SubClassExtended)
				$SubClassExtended[] = $Segment->SubClassExtended;
			if ($Segment->FareBasisCode && mb_substr($Segment->FareBasisCode, 0, 1) != '{')
				$SubClassExtended[] = $Segment->FareBasisCode;

			$TimeAdd = '';

			if (strtotime(date('00:00:00 d-m-Y', $Segment->DepartureDate)) < strtotime(date('00:00:00 d-m-Y', $Segment->ArrivalDate)))
			{
				$TimeAdd		= '+1';
			} elseif (strtotime(date('00:00:00 d-m-Y', $Segment->DepartureDate)) > strtotime(date('00:00:00 d-m-Y', $Segment->ArrivalDate))){
				$TimeAdd		= '-1';
			}

			$OperatingAirlineName = '';

			if ($Segment->MarketingAirline != $Segment->OperatingAirline)
			{
				$OperatingAirlineName = $Segment->OperatingAirlineName?$Segment->OperatingAirlineName:$Segment->OperatingAirline;
				$OperatingAirlineName = ' (<abbr title="Опер. перевозчик: '.$OperatingAirlineName.'" class="initialism">'.$Segment->OperatingAirline.'</abbr>)';
			}
		?>

			<?php if ($SegmentID > 0){ ?>
				<div class="switch">Пересадка. Время стыковки: <?php echo $sTime;?></div>
			<?php } ?>

			<div class="routes">
				<div class="flight">
					<?php echo $Segment->MarketingAirline;?> <?php echo $Segment->FlightNumber;?><br><?php echo date('d M', $Segment->DepartureDate);?></div>
				<div class="time">
					<?php echo date('H:i', $Segment->DepartureDate);?><br><?php echo $TimeAdd?'<span style="font-size:12px;letter-spacing:-1px;">':'';?><?php echo date('H:i', $Segment->ArrivalDate);?><?php echo $TimeAdd;?><?php echo $TimeAdd?'</span>':'';?></div>
				<div class="airports">
					<?php echo $DepartureName;?><br>
					<?php echo $ArrivalName;?></div>
				<div class="logo">
					<img src="<?php echo str_replace('{ID}', $Segment->MarketingAirline, TAConfigAccess::$AirlineLogoURL);?>" class="img-rounded">
				</div>
				<div class="time2">
					<?php if ($TIME){ ?>
						в пути:<br /><?=$TIME;?>
					<?php } else echo '&nbsp;'; ?>
				</div>
				<div class="airline">
					<?php echo $Segment->MarketingAirlineName?$Segment->MarketingAirlineName:$Segment->MarketingAirline;?><?php echo $OperatingAirlineName;?><br>
						<?php echo $Segment->AircraftName?$Segment->AircraftName:$Segment->Aircraft;?>, Класс: <?php echo $Segment->ClassExtended;?> (<?php echo implode(', ', $SubClassExtended);?>)</div>
			</div>

		<?php
		}
		?>
			<div style="clear:both;"></div>
		</div>
	</div>
<?php
}
?>
</div>

<?php
$Warnings = array();

if (count($PNRs) > 1)
	$Warnings[] = 'Вероятнее всего, пассажирам не удастся сразу пройти регистрацию и сдать багаж до конечной точки перелета!'
		.'<br>Проинформируйте их, чтобы обязательно уточнили на стойке регистрации на каких пересадках им потребуется заново пройти регистрацию на следующий рейс и сначала забрать багаж с предыдущего рейса, а затем заново сдать багаж на следующий рейс!'
		.'<br>Это очень важно т.к. на следующий рейс без повторного прохождения регистрации их могут не посадить и багаж останется в пункте пересадки!'
		;

if (count($PNRs) > 1)
	$Warnings[] = 'Перелёт с ручной стыковкой. В случае, если первый рейс задержится и пассажиры не успеют на следующий рейс — им придется заново оформлять билет!';

if ($AirportChanged)
	$Warnings[] = 'Аэропорт прилета не соответствует следующему аэропорту вылета. Пассажирам придется самостоятельно прибыть из аэропорта прилёта в аэропорт вылета. Про багаж необходимо уточнить на стойке регистрации.';

if (count($Warnings))
{
?>

<div class="alert alert-info">
	<strong style="font-size:16px;">Обратите внимание!</strong>
	<ul style="margin-top:10px;">
		<li><?php echo implode('</li><li style="margin-top:10px;">', $Warnings);?></li>
	</ul>
</div>
<?php
}
?>

<style type="text/css">
.popover {
	text-align: left;
	max-width: 400px;
	width: 400px;
}
.popover td {
	border-left-width: 0px;
}
</style>


<h4><i class="icon-user"></i> Пассажиры:</h4>
<table class="table table-bordered table-striped table-hover">
<thead>
	<tr>
		<th>№</th>
		<th>Пассажир</th>
		<th>Дата рождения</th>
		<th>Документ</th>
		<th>Срок действия</th>
	</tr>
</thead>
<tbody>
<?php
$TravellerCount = 0;

foreach ($Result->Result->Travellers AS $TravellerID => $Travellers)
	foreach ($Travellers AS $TravellerNum => $TravellerData)
	{
		$TravellerCount++;

		$SeatSelected = array();

		foreach ($Result->Result->SeatSelected AS $Seat)
			if ($Seat->Traveller == $TravellerID.':'.$TravellerNum)
				$SeatSelected[] = $Seat->Number;
?>
	<tr>
		<td><?php echo $TravellerCount;?></td>
		<td><?php echo $TravellerData->Surname;?> <?php echo $TravellerData->Name;?> <?php echo $TravellerData->Patronymic;?> <nobr>(<?php echo $TravellerID;?>, <?php echo $TravellerData->Citizen;?>)</nobr></td>
		<td><?php echo date('d-m-Y',$TravellerData->Birthday);?></td>
		<td><?php echo HideDoc($TravellerData->Document->Number);?></td>
		<td><?php echo date('d-m-Y',$TravellerData->Document->ExpireDate);?></td>
	</tr>
<?php
	}
?>
</tbody>
</table>

<h4><i class="icon-bullhorn"></i> Контакты:</h4>
<table class="table table-bordered table-striped table-hover">
<tbody>
	<tr>
<?php
foreach ($Result->Result->Contacts AS $ContactID => $ContactData)
{
	if ( ! $ContactData) continue;

	switch ($ContactID)
	{
		case 'Email':		$ContactName = 'Email'; HideEmail($ContactData); break;
		case 'PhoneMobile':	$ContactName = 'Мобильный'; HidePhone($ContactData); break;
		case 'PhoneHome':	$ContactName = 'Домашний'; HidePhone($ContactData); break;

		default: $ContactName = $ContactID; break;
	}
?>
		<td><?php echo $ContactName;?>: <strong><?php echo $ContactData;?></strong></td>
<?php
}
?>
	</tr>
</tbody>
</table>

<?php
$Pricing		= array();
$TotalPrices	= array();
$TotalPrices[]	= $Result->Result->TotalPrice->Total;

foreach ($Result->Result->Pricing AS $Traveller)
	$Pricing[$Traveller->Type] = clone $Traveller;

foreach ($Result->Result->Packages AS $Package)
{
	if ($Package->System != 'Avia')
		continue;

	foreach ($Package->Pricing AS $Traveller)
	{
		$Pricing[$Traveller->Type]->Base	+= $Traveller->Base;
		$Pricing[$Traveller->Type]->Tax		+= $Traveller->Tax;
		$Pricing[$Traveller->Type]->Fee		+= $Traveller->Fee;
		$Pricing[$Traveller->Type]->Total	+= $Traveller->Total;
	}

	$TotalPrices[]	= $Package->TotalPrice->Total;
}
?>

<h4><i class="icon-shopping-cart"></i> Стоимость:</h4>
<table class="table table-bordered table-striped table-hover table-condensed noWrap">
	<thead>
		<tr>
			<th>Пассажиры</th>
			<th class="w1p">Тариф</th>
			<th class="w1p">&nbsp;</th>
			<th class="w1p">Таксы</th>
			<th class="w1p">&nbsp;</th>
			<th class="w1p">Сбор</th>
			<th colspan="2" class="w1p">&nbsp;</th>
		</tr>
	</thead>
	<tbody>
		<?php
		foreach ($Pricing AS $Traveller)
		{
			switch ($Traveller->Type)
			{
				case 'ADT': $Legend = 'Взрослый'; break;
				case 'CHD': $Legend = 'Ребенок'; break;
				case 'INF': $Legend = 'Младенец'; break;
			}

			for ($i=0;$i<count($Result->Result->Travellers->{$Traveller->Type});$i++)
			{
		?>
		<tr>
			<td><?php echo $Legend;?></td>
			<td><?php echo number_format($Traveller->Base, 2, ',', ' ');?></td>
			<td>+</td>
			<td><?php echo number_format($Traveller->Tax, 2, ',', ' ');?></td>
			<td>+</td>
			<td><?php echo number_format($Traveller->Fee, 2, ',', ' ');?></td>
			<td>=</td>
			<td><?php echo number_format($Traveller->Total, 2, ',', ' ');?> <?php echo $Traveller->Currency;?></td>
		</tr>
		<?php
			}
		}
		?>
		<tr>
			<td colspan="6" style="text-align:right!important;font-weight:bold;">Сбор платежной системы:</td>
			<td>=</td>
			<td><i>см. ниже</i></td>
		</tr>
		<tr>
			<td colspan="6" style="text-align:right!important;font-weight:bold;">Итого для всех Пассажиров:</td>
			<td>=</td>
			<td><?php echo number_format(array_sum($TotalPrices), 2, ',', ' ');?> <?php echo $Result->Result->TotalPrice->Currency;?></td>
		</tr>
	</tbody>
</table>

<?php
$Tickets = array();

if (is_array($Result->Result->Tickets) && count($Result->Result->Tickets))
	foreach ($Result->Result->Tickets AS $Ticket)
	{
		$Tickets[] = '<li data-ticket="'.$Ticket.'">'.$Ticket.'&nbsp;&nbsp;&nbsp;
			<a href="tickets/print.php?pnr='.urlencode($Result->Result->PNR).'&surname='.urlencode(reset($Result->Result->Surnames)).'&ticket='.$Ticket.'" class="btn btn-success btn-mini" target="_blank">Распечатать</a>
			<a href="tickets/pdf.php?pnr='.urlencode($Result->Result->PNR).'&surname='.urlencode(reset($Result->Result->Surnames)).'&ticket='.$Ticket.'&download=true" class="btn btn-success btn-mini">Скачать PDF</a></li>';
	}

if (count($Result->Result->Packages))
	foreach ($Result->Result->Packages AS $Package)
	{
		if ($Package->System != 'Avia')
			continue;

		if (is_array($Package->Tickets) && count($Package->Tickets))
			foreach ($Package->Tickets AS $Ticket)
			{
				$Tickets[] = '<li data-ticket="'.$Ticket.'">'.$Ticket.'&nbsp;&nbsp;&nbsp;
					<a href="tickets/print.php?pnr='.urlencode($Package->PNR).'&surname='.urlencode(reset($Result->Result->Surnames)).'&ticket='.$Ticket.'" class="btn btn-success btn-mini" target="_blank">Распечатать</a>
					<a href="tickets/pdf.php?pnr='.urlencode($Package->PNR).'&surname='.urlencode(reset($Package->Surnames)).'&ticket='.$Ticket.'&download=true" class="btn btn-success btn-mini">Скачать PDF</a></li>';
			}
	}

if (count($Tickets))
{
?>

<h4><i class="icon-file"></i> Билеты:</h4>
<ul>
	<?php echo implode(EOL, $Tickets);?>
</ul>

<?php
}
?>

<h4><i class="icon-check"></i> Оплата:</h4>

<script type="text/javascript">
function PaymentInformation(Button)
{
	alert('Для оплаты бронирования — мы ждем вас в офисе.');
}

function PaymentPrint(Button)
{
}

function PaymentTransfer(Button)
{
	$(Button).attr('disabled','disabled');

	var invoice	= $(Button).closest('form').find('input[name="invoice_id"]').val();
	var gate	= $(Button).data('gate');

	window.location = '/pay/?invoice='+invoice+'&gate='+gate
}

function PaymentFrame(Button)
{
	//$(Button).attr('disabled','disabled');

	var Invoice	= $(Button).closest('form').find('input[name="invoice_id"]').val();
	var Gate	= $(Button).data('gate');

	$(Button).closest('form').find('iframe').attr('src', '/pay/?invoice='+Invoice+'&gate='+Gate+'&iframe=true');
	$(Button).closest('form').find('iframe').slideDown();
}

function PaymentFrameConfirm(Status)
{
	window.location = window.location.href+'&PayStatus='+Status;
}

function PaymentFrameClose()
{
	$('iframe').attr('src', '/images/index.html');
	$('iframe').slideUp();
}

function PaymentFormToggle(ID)
{
	if ($(ID).parent().find('table').is(':visible'))
	{
		$(ID).html('&darr; Развернуть');
		$(ID).parent().find('table').hide();
	}
	else
	{
		$(ID).html('&uarr; Свернуть');
		$(ID).parent().find('table').show();
	}
}
</script>

<?php
$Invoices = array_reverse($Result->Result->Invoices);

foreach ($Invoices AS $Invoice)
{
	switch ($Invoice->Status)
	{
		case 'Issue':		$Status = '<span class="muted">Не оплачен</span>';	break;
		case 'Cancel':		$Status = '<span class="text-error">Отменен</span>';	break;
		case 'Paid':		$Status = '<span class="label label-success">Оплачен</span>';	break;
		case 'Refunded':	$Status = '<span class="text-warning">Возвращен</span>';	break;
	}

	$StatusText = array();
	$StatusText[] = '<i class="icon-list-alt"></i> Счет №'.$Invoice->ID;
	$StatusText[] = 'Статус: '.$Status;

	if ($Invoice->Status == 'Issue')
		$StatusText[] = 'Оплатить до: '.date('H:i d-m-Y', strtotime($Result->Result->PNRExpireDate));
?>
<form class="form-inline" method="POST" action="/pay/">
	<input type="hidden" name="do" value="pay">
	<input type="hidden" name="invoice_id" value="<?php echo $Invoice->ID;?>">
	<input type="hidden" name="invoice_gate" value="">

	<?php if ($Invoice->Status == 'Cancel'){ ?>
		<a href="javascript:void(0)" onclick="PaymentFormToggle(this)" style="float:right;">&darr; Развернуть</a>
	<?php } ?>

	<p><i><?php echo implode(', ', $StatusText);?></i></p>

	<table class="table table-striped table-hover noWrap" <?php echo ($Invoice->Status=='Cancel')?'style="display:none;"':'';?>>
	<tbody>
		<?php
		foreach ($Invoice->Options AS $Option)
		{
			if ($Invoice->PayGate == $Option->Gate)
				$Paid = '<button type="button" class="btn btn-mini btn-success disabled" style="white-space:nowrap;"><i class="icon-check icon-white"></i></button> ';
			else
				$Paid = '';
		?>
		<tr>
			<td><?php echo $Paid;?><?php echo $Option->Name;?></td>
			<td style="width:20%"><?php echo number_format($Option->Summ, 2, ',', ' ');?> <?php echo $Option->Currency;?></td>
			<td style="width:10%">
			<?php if ($Invoice->Status == 'Issue'){ ?>
				<button type="button" class="btn btn-mini btn-success" data-gate="<?php echo $Option->Gate;?>" onclick="Payment<?php echo $Option->Type;?>(this)" style="white-space:nowrap;">
					<i class="icon-shopping-cart icon-white"></i> Оплатить</button>
			<?php } else { ?>
				<button type="button" class="btn btn-mini <?php echo $Paid?'btn-success':'';?> disabled" style="white-space:nowrap;">
					<i class="icon-shopping-cart icon-white"></i> <?php echo $Paid?'Оплачено':'Оплатить';?></button>
			<?php } ?>
			</td>
		</tr>
		<?php
		}
		?>
	</tbody>
	</table>

	<div>
		<iframe src="images/index.html" style="width:100%;height:480px;border-width:0px;display:none;"></iframe>
	</div>
</form>
<?php
}
?>

<?php if ( ! in_array($Result->Result->Status, array('Issued','Cancelled','Voided','Refunded'))){ ?>
<fieldset>
	<hr>
	<form class="form-inline" method="POST" action="/cancel/" >
		<input type="hidden" name="pnr" value="<?php echo $Result->Result->PNR;?>">
		<input type="hidden" name="surname" value="<?php echo reset($Result->Result->Surnames);?>">
		<button type="button" class="btn btn-danger" onclick="$(this).attr('disabled','disabled');$(this).parent().submit();">Отменить бронирование</button>
	</form>
</fieldset>
<?php } ?>

		</div>
		<div class="span3" style="margin-top:30px;">
			<div class="well-alt">
				<form action="/check/" method="get" style="margin:0px;">
					<input type="text" name="pnr" placeholder="№ брони" value="" class="input-medium">
					<input type="text" name="surname" placeholder="Фамилия" value="" class="input-medium">
					<button type="submit" class="btn btn-success"><i class="icon-search icon-white"></i> Найти</button>
				</form>
			</div>

			<ul style="margin-top:30px;">
				<li><a href="/#" target="_blank">Как обменять/вернуть авиабилет</a></li>
				<li><a href="/#" target="_blank">Общие стандарты багажа</a></li>
				<li><a href="/#" target="_blank">Часто задаваемые вопросы</a></li>
				<li><a href="/#" target="_blank">Договор публичной оферты</a></li>
			</ul>
		</div>
	</div>
</div>

<script type="text/javascript">
var $buoop = {
		reminder: 24,
		l: 'ru',
		test: false,
		text: 'Ваш браузер <b>устарел</b>. Он имеет <b>уязвимости в безопасности</b> и может <b>не показывать все возможности</b> на этом и других сайтах. <a href="http://www.google.ru/intl/ru/chrome/" target="_blank">Мы рекомендуем установить Google Chrome!</a><div id="buorgclose">X</div>',
		newwindow: true
	};
</script>

<script type="text/javascript" src="//browser-update.org/update.js" charset="UTF-8"></script>

</body>
</html>




