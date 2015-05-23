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
			'RequestID'		=> (string)$_REQUEST['ID'],
			'Variants'		=> (array)$_REQUEST['Variants'],
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
$Result	= $Client->Information($Access, $Request, $Params);

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

if (isset($Result->Errors) && count($Result->Errors))
{
	if (is_object($Result->Errors))
		$Result->Errors = array($Result->Errors);
?>
<div class="alert alert-error">
	<button type="button" class="close" data-dismiss="alert">&times;</button>
	<h4>Сожалеем,</h4>
	<ul>
	<?php foreach ($Result->Errors AS $Error){ ?>
		<li><?php echo $Error->Message;?></li>
	<?php } ?>
	</ul>
</div>
<?php
}

if ( ! $Result->Result)
	return;
?>

<div class="row-fluid" style="margin-bottom:20px">
<?php

$ItineraryNum	= 0;

$Countries		= array();
$BonusCompanies	= array();

foreach ($Result->Result->Itineraries AS $Variant)
{
	$FirstSegment	= current($Variant->Segments);
	$LastSegment	= end($Variant->Segments);
?>
	<div class="span6">
		<h3 class="label"><?php echo $FirstSegment->DepartureCityName;?> - <?php echo $LastSegment->ArrivalCityName;?></h3>
		<div class="highlight active">
		<?php
		$SegmentNum		= 0;
		$SegmentsTotal	= count($Variant->Segments);

		foreach ($Variant->Segments AS $SegmentID => &$Segment)
		{
			$SegmentNum++;

			//** Время стыковки
			if ($SegmentID > 0)
			{
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
					$TIME->minutes	= $TIME->source - ($TIME->hours*60);

					if ($TIME->hours || $TIME->minutes)
						$TIME		= $TIME->hours.'ч.'.$TIME->minutes.'м.';
					else
						$TIME		= FALSE;
				}
			}

			$Countries[] = $Segment->DepartureCountry;
			$Countries[] = $Segment->ArrivalCountry;

			$DepartureName	= $Segment->DepartureCityName;

			if ($Segment->DepartureCityName !== $Segment->DepartureAirportName)
				$DepartureName = $DepartureName.', '.$Segment->DepartureAirportName;

			$ArrivalName	= $Segment->ArrivalCityName;

			if ($Segment->ArrivalCityName !== $Segment->ArrivalAirportName)
				$ArrivalName = $ArrivalName.', '.$Segment->ArrivalAirportName;

			// Бонусная компания
			$BonusCompanies[$Segment->MarketingAirline] = $Segment->MarketingAirlineName;

			$SubClassExtended = array();
			if ($Segment->SubClassExtended)
				$SubClassExtended[] = $Segment->SubClassExtended;
			if ($Segment->FareBasisCode && mb_substr($Segment->FareBasisCode, 0, 1) != '{')
				$SubClassExtended[] = $Segment->FareBasisCode;

			$TimeAdd = '';

			if (strtotime(date('00:00:00 d-m-Y', $Segment->DepartureDate)) < strtotime(date('00:00:00 d-m-Y', $Segment->ArrivalDate)))
				$TimeAdd		= '+1';

			elseif (strtotime(date('00:00:00 d-m-Y', $Segment->DepartureDate)) > strtotime(date('00:00:00 d-m-Y', $Segment->ArrivalDate)))
				$TimeAdd		= '-1';

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
					<?php echo $Segment->MarketingAirlineName?$Segment->MarketingAirlineName:$Segment->MarketingAirline;?><?php echo $OperatingAirlineName;?>, <?php echo $Segment->AircraftName?$Segment->AircraftName:$Segment->Aircraft;?><br>
								Мест: <?php echo $Segment->AvailableSeats?$Segment->AvailableSeats:'&mdash;';?>, Класс: <?php echo $Segment->ClassExtended;?> (<?php echo implode(', ', $SubClassExtended);?>)</div>
			</div>

		<?php
		}
		?>
			<div style="clear:both;"></div>
		</div>
	</div>

	<?php if ($ItineraryNum&1){ ?>
	</div>
	<div class="row-fluid">
	<?php } ?>

<?php
	$ItineraryNum++;
}
?>
</div>

<?php
$Pricing		= array();
$TotalPrices	= array();
$TotalPrices[]	= $Result->Result->TotalPrice->Total;

foreach ($Result->Result->Pricing AS $Traveller)
	$Pricing[$Traveller->Type] = clone $Traveller;

if (is_array($Result->Result->Packages) && count($Result->Result->Packages))
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

<table class="table table-striped table-hover table-condensed1 noWrap" style="margin-top:20px;">
<thead>
	<tr>
		<th>Пассажиры</th>
		<th colspan="2" class="w1p">Тариф</th>
		<th colspan="2" class="w1p">Таксы</th>
		<th class="w1p">Сбор</th>
		<th style="text-align:center;width:1%!important;">Кол-во</th>
		<th class="w1p">&nbsp;</th>
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
?>
	<tr>
		<td><?php echo $Legend;?></td>
		<td>(<?php echo number_format($Traveller->Base, 2, ',', ' ');?></td>
		<td>+</td>
		<td><?php echo number_format($Traveller->Tax, 2, ',', ' ');?></td>
		<td>+</td>
		<td><?php echo number_format($Traveller->Fee, 2, ',', ' ');?>)</td>
		<td style="text-align:center;">x<?php echo $Result->Result->Travellers->{$Traveller->Type};?></td>
		<td>=</td>
		<td><?php echo number_format($Traveller->Total*$Result->Result->Travellers->{$Traveller->Type}, 2, ',', ' ');?> <?php echo $Traveller->Currency;?></td>
	</tr>
<?php
}
?>
	<tr>
		<td colspan="7" style="text-align:right!important;font-weight:bold;">Итого для всех Пассажиров:</td>
		<td>=</td>
		<td><?php echo number_format(array_sum($TotalPrices), 2, ',', ' ');?> <?php echo $Result->Result->TotalPrice->Currency;?></td>
	</tr>
</tbody>
</table>

<?php
if ($Result->Result->LatinRegistration != 'TRUE')
{
	$PlaceHolderSurname		= 'Можно Русскими';
	$PlaceHolderName		= 'Можно Русскими';
?>
<div class="alert alert-success">
	Информация о Фамилии и Имени пассажиров может быть внесена Русскими буквами!
</div>
<?php
}
else
{
	$PlaceHolderSurname		= 'Латиницей';
	$PlaceHolderName		= 'Латиницей';
?>
<div class="alert">
	Информация о Фамилии и Имени пассажиров должна быть внесена латиницей!
</div>
<?php
}

$Countries = array_unique($Countries);

if (count($Countries) > 1 || current($Countries) != 'RU')
	$PlaceHolderPatronymic = 'Имя';
else
{
	if (isset($BonusCompanies['SU']) || isset($BonusCompanies['UN']))
		$PlaceHolderPatronymic = 'Имя Отчество';
	else
		$PlaceHolderPatronymic = 'Имя';
}
?>

<div id="BookFormErrors"></div>
<div id="BookForm">

<?php
foreach ($Result->Result->Travellers AS $TravellerID => $TravellerCount) if ($TravellerCount)
{
	for ($i=0;$i<$TravellerCount;$i++)
	{
		switch ($TravellerID)
		{
			case 'ADT': $Legend = 'Взрослый'; break;
			case 'CHD': $Legend = 'Ребенок'; break;
			case 'INF': $Legend = 'Младенец'; break;
		}
?>
	<fieldset>
		<legend style="margin-bottom:10px;"><i class="icon-user" style="margin-top:5px;"></i> Пассажир #<?php echo ($i+1);?> &mdash; <?php echo $Legend;?></legend>

		<form class="form-inline">
			<table class="noMarPad">
			<tr>
				<td>&nbsp;</td>
				<td><span style="margin-left:8px;font-style:italic;">Фамилия:</span></td>
				<td><span style="margin-left:8px;font-style:italic;"><?php echo $PlaceHolderPatronymic;?>:</span></td>
				<td><span style="margin-left:20px;font-style:italic;">Дата рождения:</span></td>
			</tr>
			<tr>
				<td>
					<div id="Sex" class="btn-group" data-toggle="buttons-radio">
						<button type="button" class="btn active" value="Male" onclick="$('#Travellers-<?php echo $TravellerID;?>-<?php echo $i;?>-Sex').val($(this).val())">М</button>
						<button type="button" class="btn" value="Female" onclick="$('#Travellers-<?php echo $TravellerID;?>-<?php echo $i;?>-Sex').val($(this).val())">Ж</button>
					</div>
					<input type="hidden" id="Travellers-<?php echo $TravellerID;?>-<?php echo $i;?>-Sex" name="Travellers[<?php echo $TravellerID;?>][<?php echo $i;?>][Sex]" value="Male">
				</td>
				<td>
					<input type="text" class="input-small" placeholder="<?php echo $PlaceHolderSurname;?>" style="width:140px;margin-left:5px;text-transform:uppercase;" id="Travellers-<?php echo $TravellerID;?>-<?php echo $i;?>-Surname" name="Travellers[<?php echo $TravellerID;?>][<?php echo $i;?>][Surname]">
				</td>
				<td>
					<input type="text" class="input-small" placeholder="<?php echo $PlaceHolderName;?>" style="width:140px;margin-left:5px;text-transform:uppercase;" id="Travellers-<?php echo $TravellerID;?>-<?php echo $i;?>-Name" name="Travellers[<?php echo $TravellerID;?>][<?php echo $i;?>][Name]">
				</td>
				<td class="noWrap w1p">
					<select class="input-mini" style="margin-left:15px" id="Travellers-<?php echo $TravellerID;?>-<?php echo $i;?>-Birthday-Day" name="Travellers[<?php echo $TravellerID;?>][<?php echo $i;?>][Birthday][Day]">
					<?php for ($ii=1;$ii<=31;$ii++){ ?>
						<option value="<?php echo $ii;?>"><?php echo $ii;?></option>
					<?php } ?>
					</select>
					<select class="input-small" style="width:130px;" id="Travellers-<?php echo $TravellerID;?>-<?php echo $i;?>-Birthday-Month" name="Travellers[<?php echo $TravellerID;?>][<?php echo $i;?>][Birthday][Month]">
						<option value="1">Января</option>
						<option value="2">Февраля</option>
						<option value="3">Марта</option>
						<option value="4">Апреля</option>
						<option value="5">Мая</option>
						<option value="6">Июня</option>
						<option value="7">Июля</option>
						<option value="8">Августа</option>
						<option value="9">Сентября</option>
						<option value="10">Октября</option>
						<option value="11">Ноября</option>
						<option value="12">Декабря</option>
					</select>
					<select class="input-small" style="width:80px;" id="Travellers-<?php echo $TravellerID;?>-<?php echo $i;?>-Birthday-Year" name="Travellers[<?php echo $TravellerID;?>][<?php echo $i;?>][Birthday][Year]">
					<?php for ($ii=(date('Y')-100);$ii<=date('Y');$ii++){ ?>
						<option value="<?php echo $ii;?>" <?php if ($ii == 1980){ ?>selected="selected"<?php } ?>><?php echo $ii;?></option>
					<?php } ?>
					</select>
				</td>
			</tr>
			</table>

			<table class="noMarPad" style="margin-top:12px!important;">
			<tr>
				<td><span style="font-style:italic;">Гражданство:</span></td>
				<td class="noWrap w10p"><span style="font-style:italic;">Серия и номер документа:</span></td>
				<td><span style="margin-left:20px;font-style:italic;">Действителен до:</span></td>
			</tr>
			<tr>
				<td>
					<select class="input-small" style="width:192px;" id="Travellers-<?php echo $TravellerID;?>-<?php echo $i;?>-Citizen" name="Travellers[<?php echo $TravellerID;?>][<?php echo $i;?>][Citizen]">
					<?php
					foreach (TAConfigGeneral::$Countries AS $Country)
					{
						$Tags = array();
						$Tags[] = 'value="'.$Country['ID'].'"';

						if (isset($Country['Disabled']) && $Country['Disabled'])
							$Tags[] = 'disabled="disabled"';

						if (isset($Country['Selected']) && $Country['Selected'])
							$Tags[] = 'selected="selected"';
					?>
						<option <?php echo implode(' ', $Tags);?>><?php echo $Country['Name'];?></option>
					<?php
					}
					?>
					</select>
				</td>
				<td class="noWrap w10p">
					<input type="text" class="input-small" placeholder="Серия и номер документа" style="width:190px;margin-right:2px;text-transform:uppercase;" id="Travellers-<?php echo $TravellerID;?>-<?php echo $i;?>-Document-Number" name="Travellers[<?php echo $TravellerID;?>][<?php echo $i;?>][Document][Number]">
				</td>
				<td class="noWrap w1p">
					<select class="input-mini" style="margin-left:15px" id="Travellers-<?php echo $TravellerID;?>-<?php echo $i;?>-Document-ExpireDate-Day" name="Travellers[<?php echo $TravellerID;?>][<?php echo $i;?>][Document][ExpireDate][Day]">
						<option value="0">--</option>
					<?php for ($ii=1;$ii<=31;$ii++){ ?>
						<option value="<?php echo $ii;?>"><?php echo $ii;?></option>
					<?php } ?>
					</select>
					<select class="input-small" style="width:130px;" id="Travellers-<?php echo $TravellerID;?>-<?php echo $i;?>-Document-ExpireDate-Month" name="Travellers[<?php echo $TravellerID;?>][<?php echo $i;?>][Document][ExpireDate][Month]">
						<option value="0">--</option>
						<option value="1">Января</option>
						<option value="2">Февраля</option>
						<option value="3">Марта</option>
						<option value="4">Апреля</option>
						<option value="5">Мая</option>
						<option value="6">Июня</option>
						<option value="7">Июля</option>
						<option value="8">Августа</option>
						<option value="9">Сентября</option>
						<option value="10">Октября</option>
						<option value="11">Ноября</option>
						<option value="12">Декабря</option>
					</select>
					<select class="input-small" style="width:80px;" id="Travellers-<?php echo $TravellerID;?>-<?php echo $i;?>-Document-ExpireDate-Year" name="Travellers[<?php echo $TravellerID;?>][<?php echo $i;?>][Document][ExpireDate][Year]">
						<option value="0">--</option>
					<?php for ($ii=date('Y');$ii<=(date('Y')+20);$ii++){ ?>
						<option value="<?php echo $ii;?>"><?php echo $ii;?></option>
					<?php } ?>
					</select>
				</td>
			</tr>
			</table>

			<?php if (in_array($TravellerID, array('ADT'))){ ?>

			<table class="noMarPad" style="margin-top:12px!important;">
			<tr>
				<td>&nbsp;</td>
				<td class="noWrap w10p"><span style="font-style:italic;">Авиакомпания:</span></td>
				<td><span style="margin-left:10px;font-style:italic;">Номер карты:</span></td>
			</tr>
			<tr>
				<td class="right" style="width:195px;"><strong style="padding-right:10px;">Бонусная карта:</strong></td>
				<td>
					<select class="input-small" style="width:205px;margin-right:5px;" name="Travellers[<?php echo $TravellerID;?>][<?php echo $i;?>][Bonus][Company]">
						<option value=""> -- Выберите -- </option>
					<?php foreach ($BonusCompanies AS $CompanyID => $CompanyName){ ?>
						<option value="<?php echo $CompanyID;?>"><?php echo $CompanyName;?></option>
					<?php } ?>
					</select>
				</td>
				<td>
					<input type="text" class="input-small" placeholder="Номер карты" style="width:275px;margin-left:10px;" name="Travellers[<?php echo $TravellerID;?>][<?php echo $i;?>][Bonus][Number]">
				</td>
			</tr>
			</table>

			<?php } ?>
		</form>
	</fieldset>
<?php
	}
}
?>

	<fieldset>
		<legend style="margin-bottom:10px;"><i class="icon-envelope" style="margin-top:5px;"></i> Контактная информация пассажира</legend>

		<table class="noMarPad vtop" style="width:100%!important">
		<tr>
			<td style="width:34%!important"><span style="font-style:italic;">Телефон мобильный:</span></td>
			<td style="width:34%!important"><span style="font-style:italic;">Телефон домашний:</span></td>
			<td><span style="font-style:italic;">Эл.почта:</span></td>
		</tr>
		<tr>
			<td style="vertical-align:top;">
				<input type="text" class="input-medium" placeholder="+7 926 123-4567" style="width:200px;margin-bottom:0px;" name="Contacts[PhoneMobile]" value="">
			</td>
			<td style="vertical-align:top;">
				<input type="text" class="input-medium" placeholder="+7 495 123-4567" style="width:200px;margin-bottom:0px;" name="Contacts[PhoneHome]">
				<br><span style="font-size:11px;color:gray;font-style:italic;">(Не обязательно)</span>
			</td>
			<td style="vertical-align:top;">
				<input type="text" class="input-medium" placeholder="pochta@pochta.ru" style="width:200px;margin-bottom:0px;" name="Contacts[Email]" value="">
			</td>
		</tr>
		</table>
	</fieldset>
</div>

<input type="hidden" id="BookID" value="<?php echo $Result->Result->BookID;?>">

<div id="BookResults" style="display:none"></div>

