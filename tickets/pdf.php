<?php
/**
 * @package		Travel Agency Web Services (WebServices.aero)
 * @subpackage	Tickets\PDF
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

/**
 * ==============================================================================================================================
 *  Parce URL
 * ==============================================================================================================================
 */

if ( ! preg_match('/^[1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZАБВГДЕЁЖЗИЙКЛМНОПРСТУФХЦЧШЩЪЫЬЭЮЯ\-]{1,100}$/iu', $_REQUEST['pnr']))
	die();

if ( ! preg_match('/^[1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZАБВГДЕЁЖЗИЙКЛМНОПРСТУФХЦЧШЩЪЫЬЭЮЯ\-]{1,100}$/iu', $_REQUEST['surname']))
	die();

if ( ! preg_match('/^[1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZАБВГДЕЁЖЗИЙКЛМНОПРСТУФХЦЧШЩЪЫЬЭЮЯ\-]{1,100}$/iu', $_REQUEST['ticket']))
	die();

/**
 * ==============================================================================================================================
 *  / Parce URL
 * ==============================================================================================================================
 */

/**
 * ==============================================================================================================================
 *  Request
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
		'PNR'		=> $_REQUEST['pnr'],
		'Surname'	=> $_REQUEST['surname'],
		'Ticket'	=> $_REQUEST['ticket'],
	);

$Params = array(
		'Compress'		=> 'GZip',
		'Language'		=> 'RU',
		'Currency'		=> array('RUB'),
	);

$Client	= new BaseJsonRpcClient(TAConfigAccess::$URLExtended);
$Result = $Client->Ticket($Access, $Request, $Params);

/**
 * ==============================================================================================================================
 *  / Request
 * ==============================================================================================================================
 */

if ( ! isset($Result->Result->PNR) || ! $Result->Result->PNR)
	return;

/**
 * ==============================================================================================================================
 *  HTML
 * ==============================================================================================================================
 */

ob_start();

?><!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8" />
	<meta http-equiv="content-type" content="text/html;charset=utf-8">

	<!-- Styles -->
	<link href="../css/bootstrap.pdf.css" rel="stylesheet" />
</head>
<body style="background:#ffffff;background-image:none;">

<style type="text/css">
.logo {
	position: absolute;
	top: 0px;
	left: 550px;
}
.w100p		{ width: 100%; }
</style>

<div class="container" style="position:relative;">
	<img src="http://<?php echo $_SERVER['HTTP_HOST'];?>/images/logo.png" class="logo">
	<table class="noMarPad w100p vtop">
	<tbody>
		<tr>
			<td style="font-size:12px;line-height:14px;">
				<p><strong>WebServices.aero - Sample</strong>
				<br>г. Москва, Ленинский пр-т, дом №1</p>

			<p>Тел: (495) 111-22-33
				<br>Email: info@webservices.aero
				<br>IATA: <?php echo TAConfigAccess::$IATA;?></p>
			</td>
			<td style="width:400px;">
				&nbsp;
			</td>
		</tr>
	</tbody>
	</table>
</div>

<style type="text/css">
.table-data {
	width: 100%;
	font-size: 10px !important;
	line-height: 12px !important;
	margin: 10px 0px!important;
}
.table-data th {
	font-size: 10px !important;
	line-height: 12px !important;
}
.table-data .key {
	width: 10%;
	white-space: nowrap!important;
	line-height: 12px!important;
	padding-right: 10px!important;
	padding-bottom: 10px!important;
	text-align: right!important;
}
.table-data .value {
	font-weight: bold!important;
	font-size: 13px!important;
	line-height: 14px!important;
	padding-left: 20px!important;
	padding-bottom: 10px!important;
}

.table-border td {
	border: 1px solid #dddddd!important;
	padding: 5px 0px!important;
}

.rules {
	text-align: justify!important;
	font-size: 10px!important;
	line-height: 12px!important;
	padding: 0px 0px 0px 0px!important;
}
.table-segment-header {
	background-color: #f2f2f2 !important;
}
.table-segment-header table {
	margin-top: 4px!important;
	width: 100%!important;
}
.table-segment-header table .airline {
	font-weight: bold!important;
	font-size: 14px!important;
	white-space: nowrap!important;
	width: 20%;
}
.table-segment-header table .flight {
	font-weight: bold!important;
	font-size: 14px!important;
}
.table-segment-header table .route {
	font-weight: bold!important;
	font-size: 14px!important;
	padding-left: 15px!important;
	width: 35%;
	white-space: nowrap;
}
.table-segment-header table .route span {
	font-weight: normal;
}
.table-segment-header table .date {
	font-weight: bold!important;
	white-space: nowrap;
}
</style>

<div class="container">
	<h5 class="center" style="color:black;">ЭЛЕКТРОННЫЙ БИЛЕТ (МАРШРУТНЫЙ ЛИСТ ПАССАЖИРА) / <span style="color:gray">ELECTRONIC TICKET (PASSANGER ITINERARY RECEIPT)</span></h5>

	<table class="noMarPad w100p vtop">
	<tbody>
		<tr>
			<td>
				<table class="table-data">
				<tbody>
					<tr>
						<td class="key">ПАССАЖИР<br>PASSANGER</td>
						<td class="value"><?php echo ($Result->Result->Traveller->Sex=='Male')?'MR':'MRS';?> <?php echo $Result->Result->Traveller->Name;?> <?php echo $Result->Result->Traveller->Surname;?></td>
					</tr>
					<tr>
						<td class="key">ДОКУМЕНТ<br>IDENTIFICATION</td>
						<td class="value"><?php echo $Result->Result->Traveller->Document->Number;?></td>
					</tr>
					<tr>
						<td class="key">НОМЕР БИЛЕТА<br>TICKET NUMBER</td>
						<td class="value"><?php echo $Result->Result->Ticket;?></td>
					</tr>
					<tr>
						<td class="key">ЧАСТОЛЕТАЮЩИЙ ПАССАЖИР<br>FREQUENT FLYER</td>
						<td class="value">&mdash;</td>
					</tr>
				</tbody>
				</table>
			</td>
			<td>
				<table class="table-data">
				<tbody>
					<tr>
						<td class="key">ДАТА ПРОДАЖИ<br>SELLING DATE</td>
						<td class="value"><?php echo mb_strtoupper(date('d M Y', $Result->Result->SaleDate));?></td>
					</tr>
					<tr>
						<td class="key">НОМЕР БРОНИ<br>PNR LOCATOR</td>
						<td class="value"><?php echo $Result->Result->PNR;?><?php echo $Result->Result->PNRLocator?' (Авиакомпании: '.$Result->Result->PNRLocator.')':'';?></td>
					</tr>
					<tr>
						<td class="key">ВАЛИДИРУЮЩИЙ ПЕРЕВОЗЧИК<br> VALIDATING CARRIER</td>
						<td class="value"><?php echo $Result->Result->ValidatingAirlineName;?> (<?php echo $Result->Result->ValidatingAirline;?>)</td>
					</tr>
					<tr>
						<td class="key">КЛАСС ПЕРЕЛЕТА<br>CLASS</td>
						<td class="value"><?php echo $Result->Result->RequestData->Class;?></td>
					</tr>
					<tr style="display:none;">
						<td class="key">ЛОКАТОР АВИАКОМПАНИИ<br>AIRLINE LOCATOR</td>
						<td class="value"><?php echo ($Result->Result->Traveller->Bonus->Company&&$Result->Result->Traveller->Bonus->Number)?$Result->Result->Traveller->Bonus->Company.' '.$Result->Result->Traveller->Bonus->Number:'&mdash;';?></td>
					</tr>
				</tbody>
				</table>
			</td>
		</tr>
	</tbody>
	</table>

	<div class="row-fluid">
		<div class="span12 rules">
			<p>НА РЕГИСТРАЦИИ ВАМ НЕОБХОДИМО ИМЕТЬ ПРИ СЕБЕ ОРИГИНАЛЫ ДОКУМЕНТОВ, УДОСТОВЕРЯЮЩИХ ЛИЧНОСТЬ, ЗАЯВЛЕННЫЕ ПРИ БРОНИРОВАНИИ. ПАССАЖИР НЕСЕТ ОТВЕТСТВЕННОСТЬ ЗА ДЕЙСТВИТЕЛЬНОСТЬ СВОИХ ДОКУМЕНТОВ, И ИХ СООТВЕТСТВИЕ ДЕЙСТВУЮЩИМ ПРАВИЛАМ НА ВЪЕЗД В СТРАНУ НАЗНАЧЕНИЯ ИЛИ ВЫЕЗД.</p>
			<p>AT CHECK-IN, PLEASE, SHOW A PICTURE IDENTIFICATION AND THE DOCUMENT YOU GAVE FOR REFERENCE AT RESERVATION TIME. PASSENGER IS RESPONSIBLE FOR THE VALIDITY OF THEIR DOCUMENTS AND THEIR COMPLIANCE WITH REGULATIONS TO ENTER THE COUNTRY OF DESTINATION OR EXIT.</p>
		</div>
	</div>
</div>

<div class="container">
	<h5>ДЕТАЛИ ПЕРЕЛЕТА / <span style="color:gray">FLIGHT DETAILS</span></h5>
<?php
foreach ($Result->Result->Itineraries AS $Itinerary)
{
	foreach ($Itinerary->Segments AS $Segment)
	{
		//** Время в пути
		if ($Segment->FlightTime){
			$TIME	= new stdClass();
			$TIME->source	= $Segment->FlightTime;

			if ($TIME->source >= 1440){ // Если больше суток
				$TIME->days		= floor($TIME->source/1440);
				$TIME->hours	= $TIME->source - ($TIME->days*1440);
				$TIME->hours	= floor($TIME->hours/60);
				$TIME->minutes	= $TIME->source - ($TIME->days*1440) - ($TIME->hours*60);

				if ($TIME->days || $TIME->hours || $TIME->minutes)
					$TIME		= $TIME->days.'д. '.$TIME->hours.'ч. '.$TIME->minutes.'м.';
				else
					$TIME		= FALSE;
			} else {
				$TIME->hours	= floor($TIME->source/60);
				$TIME->minutes	= $TIME->source - ($TIME->hours*60);

				if ($TIME->hours || $TIME->minutes)
					$TIME		= $TIME->hours.'ч. '.$TIME->minutes.'м.';
				else
					$TIME		= FALSE;
			}
		} else {
			$TIME	= FALSE;
		}
?>
	<div class="row-fluid table-segment-header">
		<div class="span12">
			<table>
			<tbody>
				<tr>
					<td class="route"><?php echo $Segment->DepartureCityName;?> <span><?php echo $Segment->DepartureCity;?></span> –  <?php echo $Segment->ArrivalCityName;?> <span><?php echo $Segment->ArrivalCity;?></span></td>
					<td class="date"><?php echo TAConfigDates::$DWeeks[date('N', $Segment->DepartureDate)];?>, <?php echo date('d', $Segment->DepartureDate);?> <?php echo TAConfigDates::$Months[date('n', $Segment->DepartureDate)];?> <?php echo date('Y', $Segment->DepartureDate);?>, <?php echo date('H:i', $Segment->DepartureDate);?></td>
					<td class="flight">Рейс <?php echo $Segment->MarketingAirline;?>-<?php echo $Segment->FlightNumber;?></td>
					<td class="airline"><?php echo $Segment->MarketingAirlineName;?></td>
				</tr>
			</tbody>
			</table>
		</div>
	</div>
	<?php
	$DeparturePrint	= array();

	$DeparturePrint[] = TAConfigDates::$DWeeks[date('N', $Segment->DepartureDate)];
	$DeparturePrint[] = date('d', $Segment->DepartureDate).' '.TAConfigDates::$Months[date('n', $Segment->DepartureDate)].' '.date('Y', $Segment->DepartureDate);
	$DeparturePrint[] = date('H:i', $Segment->DepartureDate);

	if (trim($Segment->DepartureTerminal))
		$DeparturePrint[] = $Segment->DepartureAirportName.' (терм. '.trim($Segment->DepartureTerminal).') <span style="font-weight:normal;font-style:italic;">('.$Segment->DepartureAirport.')</span>';
	else
		$DeparturePrint[] = $Segment->DepartureAirportName.' <span style="font-weight:normal;font-style:italic;">('.$Segment->DepartureAirport.')</span>';

	$ArrivalPrint	= array();

	$ArrivalPrint[] = TAConfigDates::$DWeeks[date('N', $Segment->ArrivalDate)];
	$ArrivalPrint[] = date('d', $Segment->ArrivalDate).' '.TAConfigDates::$Months[date('n', $Segment->ArrivalDate)].' '.date('Y', $Segment->ArrivalDate);
	$ArrivalPrint[] = date('H:i', $Segment->ArrivalDate);

	if (trim($Segment->ArrivalTerminal))
		$ArrivalPrint[] = $Segment->ArrivalAirportName.' (терм. '.trim($Segment->ArrivalTerminal).') <span style="font-weight:normal;font-style:italic;">('.$Segment->ArrivalAirport.')</span>';
	else
		$ArrivalPrint[] = $Segment->ArrivalAirportName.' <span style="font-weight:normal;font-style:italic;">('.$Segment->ArrivalAirport.')</span>';
	?>

	<table class="noMarPad w100p vtop">
	<tbody>
		<tr>
			<td>
				<table class="table-data">
				<tbody>
					<tr>
						<td class="key">ВЫЛЕТ<br>DEPATURE</td>
						<td class="value"><?php echo implode(', ', $DeparturePrint);?></td>
					</tr>
					<tr>
						<td class="key">ПРИЛЁТ<br>ARRIVAL</td>
						<td class="value"><?php echo implode(', ', $ArrivalPrint);?></td>
					</tr>
					<tr>
						<td class="key">ТИП САМОЛЕТА<br>EQUIPMENT</td>
						<td class="value"><?php echo $Segment->Aircraft;?> (<?php echo $Segment->AircraftName;?>)</td>
					</tr>
				</tbody>
				</table>
			</td>
			<td style="width:35%">
				<table class="table-data">
				<tbody>
					<tr>
						<td class="key">ПРОДОЛЖИТЕЛЬНОСТЬ<br>DURATION</td>
						<td class="value"><?php echo $TIME?$TIME:'&nbsp;';?></td>
					</tr>
					<tr>
						<td class="key">БАГАЖ<br>BAGGAGE</td>
						<td class="value"><?php echo $Segment->Baggage;?></td>
					</tr>
					<tr>
						<td class="key">КЛАСС<br>CLASS</td>
						<td class="value"><?php echo $Segment->SubClassExtended;?><?php echo $Segment->FareBasisCode?' ('.$Segment->FareBasisCode.')':'';?></td>
					</tr>
				</tbody>
				</table>
			</td>
		</tr>
	</tbody>
	</table>

<?php
	}
}
?>
</div>

<?php if ($Result->Result->Price){ ?>

<div class="container">
	<table class="noMarPad w100p vtop">
	<tbody>
		<tr>
			<td style="width:800px;">
				<h5>РАСЧЕТ СТОИМОСТИ / <span style="color:gray">CALCULATION</span></h5>
			</td>
			<td>
				<table class="table-data" style="margin:0px 0px;">
					<tbody>
						<tr>
							<td class="key">ФОРМА ОПЛАТЫ
								<br>FORM OF PAYMENT</td>
							<td class="value"><?php echo $Result->Result->FormOfPayment;?></td>
						</tr>
					</tbody>
				</table>
			</td>
		</tr>
	</tbody>
	</table>

	<table class="table-data table-border text-center">
	<thead>
		<tr>
			<th>ТАРИФ<br>FARE</th>
			<th>АЭРОПОРТОВЫЕ СБОРЫ<br>TAXES</th>
			<?php if ($Result->Result->Price->NDS){ ?>
			<th>ВКЛЮЧАЯ НДС<br>VAT</th>
			<?php } ?>
			<th>СЕРВИСНЫЙ СБОР<br>SERVICE FEE</th>
			<?php if ($Result->Result->Price->NDSFee){ ?>
			<th>ВКЛЮЧАЯ НДС<br>VAT</th>
			<?php } ?>
			<th>СБОР ПЛАТ. ШЛЮЗА<br>PAY FEE</th>
			<th>СТОИМОСТЬ ПЕРЕВОЗКИ<br>GRAND TOTAL</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td class="value" style="padding-bottom:5px;"><?php echo number_format($Result->Result->Price->Base, 2, ',', ' ');?> <?php echo $Result->Result->Price->Currency;?></td>
			<td class="value" style="padding-bottom:5px;"><?php echo number_format($Result->Result->Price->Tax, 2, ',', ' ');?> <?php echo $Result->Result->Price->Currency;?></td>
			<?php if ($Result->Result->Price->NDS){ ?>
			<td class="value" style="padding-bottom:5px;"><?php echo number_format($Result->Result->Price->NDS, 2, ',', ' ');?> <?php echo $Result->Result->Price->Currency;?></td>
			<?php } ?>
			<td class="value" style="padding-bottom:5px;"><?php echo number_format($Result->Result->Price->Fee, 2, ',', ' ');?> <?php echo $Result->Result->Price->Currency;?></td>
			<?php if ($Result->Result->Price->NDSFee){ ?>
			<td class="value" style="padding-bottom:5px;"><?php echo number_format($Result->Result->Price->NDSFee, 2, ',', ' ');?> <?php echo $Result->Result->Price->Currency;?></td>
			<?php } ?>
			<td class="value" style="padding-bottom:5px;"><?php echo number_format($Result->Result->Price->PayFee, 2, ',', ' ');?> <?php echo $Result->Result->Price->Currency;?></td>
			<td class="value" style="padding-bottom:5px;"><?php echo number_format($Result->Result->Price->Total + $Result->Result->Price->PayFee, 2, ',', ' ');?> <?php echo $Result->Result->Price->Currency;?></td>
		</tr>
	</tbody>
	</table>
</div>

<?php } ?>

<div class="container">
	<h5>УСЛОВИЯ ИСПОЛЬЗОВАНИЯ ТАРИФА / <span style="color:gray">ENDORSEMENT/RESTRICTION</span></h5>

	<table class="noMarPad w100p vtop">
	<tbody>
		<tr>
			<td style="width:480px;">
				<strong style="font-size:12px;">ПРИМЕЧАНИЕ</strong>
				<p class="justify" style="font-size:8px;line-height:10px;">ПРИ ОТКАЗЕ ПАССАЖИРА ОТ ПЕРЕВОЗКИ ВОЗВРАТ ДЕНЕЖНЫХ СУММ МОЖЕТ БЫТЬ ПРОИЗВЕДЕН: ЛИЦУ, УКАЗАННОМУ В АВИАБИЛЕТЕ, ПРИ ПРЕДЬЯВЛЕНИИ ДОКУМЕНТА, УДОСТОВЕРЯЮЩЕГО ЕГО ЛИЧНОСТЬ; ЛИЦУ, ОПЛАТИВШЕМУ ПЕРЕВОЗКУ ПРИ ПРЕДЬЯВЛЕНИИ ДОКУМЕНТА, УДОСТОВЕРЯЮЩЕГО ТАКУЮ ОПЛАТУ, И ДОКУМЕНТА, УДОСТОВЕРЯЮЩЕГО ЛИЧНОСТЬ И ПРАВО НА ПОЛУЧЕНИЕ ЭТИХ СУММ (ДОВЕРЕННОСТИ, ЗАВЕРЕННОЙ НОТАРИАЛЬНО ДЛЯ ФИЗИЧЕСКИХ ЛИЦ ИЛИ ДОВЕРЕННОСТИ, ЗАВЕРЕННОЙ УПОЛНОМОЧЕННЫМ ОРГАНОМ ЮРИДИЧЕСКОГО ЛИЦА ДЛЯ ПРЕДСТАВИТЕЛЕЙ ЮРИДИЧЕСКИХ ЛИЦ; ДОВЕРЕННОМУ ЛИЦУ ПАССАЖИРА ПРИ ПРЕДЬЯВЛЕНИИ НОТАРИАЛЬНО ЗАВЕРЕНОЙ ДОВЕРЕННОСТИИ ДОКУМЕНТА, УДОСТОВЕРЯЮЩЕГО ЛИЧНОСТЬ. ВОЗВРАТ БИЛЕТА, ОФОРМЛЕННОГО ПО ЗАГРАНИЧНОМУ ПАСПОРТУ, МОЖЕТ БЫТЬ ПРОИЗВЕДЕН ПО РОССИЙСКОМУ ПАСПОРТУ, ПРИ УСЛОВИИ НАЛИЧИЯ В РОССИЙСКОМ ПАСПОРТЕ ОТМЕТКИ О ВЫДАЧЕ ЗАГРАНИЧНОГО ПАСПОРТА. ВОЗВРАТ НЕИСПОЛЬЗОВАННОЙ ЧАСТИ ПЕРЕВОЗКИ ПРОИЗВОДИТСЯ В ПРЕДЕЛАХ СРОКА ГОДНОСТИ БИЛЕТА . ДЛЯ ПЕРЕВОЗОК МЕЖДУ ПУНКТАМИ ПОЛЕТОВ НА ТЕРРИТОРИИ РОССИЙСКОЙ ФЕДЕРАЦИИ И МЕЖДУНАРОДНЫХ ПЕРЕВОЗОК, НАЧИНАЮЩИХСЯ НА ТЕРРИТОРИИ РОССИЙСКОЙ ФЕДЕРАЦИИ: - ВОЗВРАТ ПРОИЗВОДИТСЯ БЕЗ ШТРАФНЫХ САНКЦИЙ ПРИ ОБРАЩЕНИИ ПАССАЖИРА БОЛЕЕ ЧЕМ ЗА 24 ЧАСА ДО НАЧАЛА ПЕРЕВОЗКИ (ЕСЛИ ИНОЕ НЕ ОГОВАРИВАЕТСЯ ПЕРЕВОЗЧИКОМ). - ВОЗВРАТ ПРОИЗВОДИТСЯ СО ШТРАФОМ В РАЗМЕРЕ 25% ОТ ПРИМЕНЕННОГО ТАРИФА ПРИ ОБРАЩЕНИИ ПАССАЖИРА МЕНЕЕ ЧЕМ ЗА 24 ЧАСА ДО НАЧАЛА ПЕРЕВОЗКИ (ЕСЛИ ИНОЕ НЕ ОГОВАРИВАЕТСЯ ПЕРЕВОЗЧИКОМ). - ПРИ ПРИМЕНЕНИИ СПЕЦИАЛЬНЫХ ТАРИФОВ В СЛУЧАЕ НЕЯВКИ ПАССАЖИРА НА РЕЙС БИЛЕТ ВОЗВРАТУ НЕ ПОДЛЕЖИТ. АВИАКОМПАНИЯМИ ВВОДИТСЯ "ПЛАТА ЗА ПРОЦЕДУРУ ВОЗВРАТА", ЯВЛЯЮЩАЯСЯ ПЛАТОЙ ЗА ОПЕРАЦИИ ПО АННУЛИРОВАНИЮ БРОНИРОВАНИЯ ПЕРЕВОЗКИ, ПО ОСУЩЕСТВЛЕНИЮ РАСЧЕТОВ СУММ, ПРИЧИТАЮЩИХСЯ ДЛЯ ВОЗВРАТА, ПО ОФОРМЛЕНИЮ ВОЗВРАТА СУММ ЗА ОПЕРАЦИИ С ИСПОЛЬЗОВАНИЕМ ПЛАТЕЖНЫХ СИСТЕМ.</p>
			</td>
			<td style="width:520px;">
				<p style="padding: 0px 0px 0px 100px;font-size:8px;line-height:10px;">
					<strong style="font-size:12px;">NOTICE</strong><br>
					CARRIAGE AND OTHER SERVICES PROVIDED BY THE CARRIER ARE SUBJECT TO CONDITIONS OF CARRIAGE, WHICH ARE HEREBY INCORPORATED BY REFERENCE. THESE CONDITIONS MAY BE OBTAINED FROM THE ISSUING CARRIER.<br>
					THE ITINERARY/RECEIPT CONSTITUTES THE 'PASSENGER TICKET' FOR THE PURPOSES OF ARTICLE 3 OF THE WARSAW CONVENTION, EXCEPT WHERE THE CARRIER DELIVERS TO THE PASSENGER ANOTHER DOCUMENT COMPLYING WITH THE REQUIREMENTS OF ARTICLE 3.<br>
					IF THE PASSENGER'S JOURNEY INVOLVES AN ULTIMATE DESTINATION OR STOP IN A COUNTRY OTHER THAN THE COUNTRY OF DEPARTURE THE WARSAW CONVENTION MAY BE APPLICABLE AND THE CONVENTION GOVERNS AND IN MOST CASES LIMITS THE LIABILITY OF CARRIERS FOR DEATH OR PERSONAL INJURY AND IN RESPECT OF LOSS OF OR DAMAGE TO BAGGAGE. SEE ALSO NOTICES HEADED ADVICE TO INTERNATIONAL PASSENGERS ON LIMITATION OF LIABILITY' AND 'NOTICE OF BAGGAGE LIABILITY LIMITATIONS'.<br>
					<span style="font-size:12px;line-height:14px;"><br>Я подтверждаю, что данные в настоящем авиабилете соответствуют моему запросу и являются правильными. С условиями применения тарифа, обмена и возврата данного авиабилета ознакомлен.</span>
					<span style="font-size:12px;line-height:14px;"><br><br>___________________&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;___________________
					<br>Дата&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Подпись</span>
				</p>
			</td>
		</tr>
	</tbody>
	</table>

</div>

<h3 style="color:gray;text-align:center;">ЖЕЛАЕМ ВАМ ПРИЯТНОГО ПОЛЕТА!</h3>

</body>
</html>

<?php

$HTML = ob_get_clean();

/**
 * ==============================================================================================================================
 *  /HTML
 * ==============================================================================================================================
 */

/**
 * ==============================================================================================================================
 *  PDF
 * ==============================================================================================================================
 */

if ( ! defined('DOMPDF_FONT_CACHE'))
{
	define('DOMPDF_FONT_CACHE', TAPATH_TMP);
}

if ( ! defined('DOMPDF_ENABLE_REMOTE'))
{
	define('DOMPDF_ENABLE_REMOTE', true);
}

require_once(TAPATH_LIBRARIES.DS.'dompdf/dompdf_config.inc.php');
require_once(TAPATH_LIBRARIES.DS.'dompdf/lib/php-font-lib/FontLib/Autoloader.php');

$PDF = new \DOMPDF();
$PDF->load_html($HTML);
$PDF->render();
$PDF->stream($Result->Result->Ticket.'.pdf', array('Attachment' => TRUE));

/**
 * ==============================================================================================================================
 *  / PDF
 * ==============================================================================================================================
 */

