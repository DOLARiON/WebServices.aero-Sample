<?php
/**
 * @package		Travel Agency Web Services (WebServices.aero)
 * @subpackage	AJAX\Filter
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
 *  Sort functions
 * ==============================================================================================================================
 */

function SortDisplayPrice(&$First, &$Second)
{
	$Options = array();

	foreach ($First->PaymentOptions AS $Option)
		if ($Option->Group == 'CreditCard')
			$Options[(int)$Option->Summ] = $Option;

	if (count($Options))
		$TotalPrice = $Options[min(array_keys($Options))]->Summ;
	else
		$TotalPrice = $First->TotalPrice->Total;

	$First->DisplayPrice	= $TotalPrice;

	$Options = array();

	foreach ($Second->PaymentOptions AS $Option)
		if ($Option->Group == 'CreditCard')
			$Options[(int)$Option->Summ] = $Option;

	if (count($Options))
		$TotalPrice = $Options[min(array_keys($Options))]->Summ;
	else
		$TotalPrice = $Second->TotalPrice->Total;

	$Second->DisplayPrice	= $TotalPrice;

	if ((int)$First->DisplayPrice == (int)$Second->DisplayPrice)
		return 0;

	return ((int)$First->DisplayPrice < (int)$Second->DisplayPrice) ? -1 : 1;
}

/**
 * ==============================================================================================================================
 *  / Sort functions
 * ==============================================================================================================================
 */

$Display = TAConfigAccess::$DisplayLimit;

if ((int)$_REQUEST['Display'])
	$Display = (int)$_REQUEST['Display'];

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

$Params = array(
		'Compress'		=> 'GZip',
		'Format'		=> TAConfigAccess::$GroupFormat,
		'Return'		=> 'ByTimelimit',
		'ReturnLimit'	=> 1900,
		'Language'		=> 'RU',
		'Sort'			=> 'CreditCard',
		'Currency'		=> array('RUB'),
		'TimeLimit'		=> 180,
		'Tariff'		=> TAConfigAccess::$Tariff,
	);

$Client	= new BaseJsonRpcClient(TAConfigAccess::$URLExtended);
$Result	= $Client->Regroup($Access, $_REQUEST['ID'], $Params);

/**
 * ==============================================================================================================================
 *  / Request
 * ==============================================================================================================================
 */

$Itineraries	= array();
$FilterParams	= $_REQUEST['Filter'];

for ($i=0;$i<6;$i++)
{
	if ( ! isset($FilterParams['Itinerary'.$i.'DepartureTime']))
		continue;

	$Itineraries[]	= array(
			'FlightNumber'		=> $FilterParams['Itinerary'.$i.'FlightNumber'],
			'DepartureTime'		=> array('Min'=>$FilterParams['Itinerary'.$i.'DepartureTime'][0]*60, 'Max'=>$FilterParams['Itinerary'.$i.'DepartureTime'][1]*60),
			'DepartureAirport'	=> is_array($FilterParams['Itinerary'.$i.'DepartureAirport'])&&count($FilterParams['Itinerary'.$i.'DepartureAirport'])?array_values($FilterParams['Itinerary'.$i.'DepartureAirport']):array(),

			'SwitchTime'		=> array('Min'=>$FilterParams['Itinerary'.$i.'SwitchTime'][0]*60, 'Max'=>$FilterParams['Itinerary'.$i.'SwitchTime'][1]*60),
			'ArrivalTime'		=> array('Min'=>$FilterParams['Itinerary'.$i.'ArrivalTime'][0]*60, 'Max'=>$FilterParams['Itinerary'.$i.'ArrivalTime'][1]*60),
			'ArrivalAirport'	=> is_array($FilterParams['Itinerary'.$i.'ArrivalAirport'])&&count($FilterParams['Itinerary'.$i.'ArrivalAirport'])?array_values($FilterParams['Itinerary'.$i.'ArrivalAirport']):array(),

			//'OperatingAirline'	=> '4G',
			//'SwitchInterval'		=> array('Min'=>0,'Max'=>1440),
		);
}

$Params = array(
		'Types'					=> (array)$FilterParams['Types'],
		'Switches'				=> count((array)$FilterParams['Switches'])?array('Min'=>min((array)$FilterParams['Switches']),'Max'=>max((array)$FilterParams['Switches'])):array(),
		'MarketingAirlines'		=> is_array($FilterParams['MarketingAirlines'])&&count($FilterParams['MarketingAirlines'])?array_values($FilterParams['MarketingAirlines']):array(),
		'Itineraries'			=> $Itineraries,
	);

/**
 * ==============================================================================================================================
 *  Request
 * ==============================================================================================================================
 */

$FilterArray	= array();
$Client			= new BaseJsonRpcClient(TAConfigAccess::$URLExtended);
$FilterResult	= $Client->SearchFilter($Access, $_REQUEST['ID'], $Params);

if (isset($FilterResult->Result->Data) && is_array($FilterResult->Result->Data) && count($FilterResult->Result->Data))
foreach ($FilterResult->Result->Data AS $Rec)
{
	if ($Rec->Status != 'Pass')
		continue;

	foreach ($Rec->Itineraries AS $Itinerary)
	{
		foreach ($Itinerary->Variants AS $Variant)
			if ($Variant->Status == 'Pass')
				$FilterArray[$Rec->RecommendationID][] = $Variant->VariantID;
	}
}

/**
 * ==============================================================================================================================
 *  / Request
 * ==============================================================================================================================
 */

echo '<p id="SearchDisplayed"><i class="icon-info-sign"></i> Результатов, подходящих под условия фильтров: '.count($FilterArray).', отображено: '.$Display.'.</p>';

?>

<hr/>

<?php
$Displayed = 0;

foreach ($Result->Result->Data AS $GroupID => $Group)
{
	if ( ! isset($FilterArray[$Group->RecommendationID]))
		continue;

	$Displayed++;

	$SystemData		= array();
	$SystemData[]	= $Group->GDS;
?>

<fieldset id="BOOK-<?php echo $Group->RecommendationID;?>-GROUP">
	<legend class="RUB">
		<?php echo number_format($Group->TotalPrice->Total, 0, ',', ' ');?> руб.
		<a href="javascript:void(0)" class="extended-link"><i class="icon-question-sign"></i></a>
		<?php if (isset($Group->Extended) && $Group->Extended){ ?>
			<span class="label label-success">Ручная стыковка</span>
		<?php } ?>

		<?php if (count($SystemData)){ ?>
		<span style="font-style:italic;font-weight:normal;color:silver;font-size:12px;">(<?php echo implode(', ', $SystemData);?>)</span>
		<?php } ?>

		<div class="pull-right">
			<a href="javascript:void(0)" onclick="FareRules('<?php echo $Group->RecommendationID;?>')" class="btn btn-link">
				<i class="icon-list-alt"></i> Правила тарифа</a>
			<a href="javascript:void(0)" onclick="Book('<?php echo $Group->RecommendationID;?>')" class="btn btn-success">
				<i class="icon-ok icon-white"></i> Забронировать</a>
		</div>
	</legend>

	<div class="extended-data" style="display:none;">
		<table class="table table-striped table-condensed" style="margin-bottom:10px;">
		<tbody>
			<tr>
				<td><i>Тариф:</i></td>
				<td><?php echo $Group->TotalPrice->Base;?> <?php echo $Group->TotalPrice->Currency;?></td>
				<td class="noWrap">&nbsp;&nbsp;&nbsp;</td>
				<td><i>Комиссия А/К:</i></td>
				<td>&mdash;</td>
			</tr>
			<tr>
				<td><i>Таксы:</i></td>
				<td><?php echo $Group->TotalPrice->Tax;?> <?php echo $Group->TotalPrice->Currency;?></td>
				<td class="noWrap">&nbsp;&nbsp;&nbsp;</td>
				<td><i>Тарифная скидка (в т.ч.):</i></td>
				<td>&mdash;</td>
			</tr>
			<tr>
				<td><i>Сбор (всего):</i></td>
				<td><?php echo $Group->TotalPrice->Fee;?> <?php echo $Group->TotalPrice->Currency;?></td>
				<td class="noWrap">&nbsp;&nbsp;&nbsp;</td>
				<td><i>Тарифный сбор (в т.ч.):</i></td>
				<td><?php
				if ((float)$Group->Commission->TariffServiceConverted)
					switch ($Group->Commission->TariffServiceConvertedCurrency)
					{
						case 'PRS':
							echo $Group->Commission->TariffServiceConverted.'%';
							break;
						default:
							switch ($Group->Commission->TariffServiceParams)
							{
								case 'Traveller':
									echo $Group->Commission->TariffServiceConverted.' '.$Group->Commission->TariffServiceConvertedCurrency.'/Пасс.';
									break;
								case 'TravellerSegment':
									echo $Group->Commission->TariffServiceConverted.' '.$Group->Commission->TariffServiceConvertedCurrency.'/Сегм.';
									break;
								default:
									echo $Group->Commission->TariffServiceConverted.' '.$Group->Commission->TariffServiceConvertedCurrency;
									break;
							}
							break;
					}
				else
					echo '&mdash;';
				?></td>
			</tr>
		</tbody>
		</table>

		<b>Варианты оплаты:</b>
		<table class="table table-striped table-condensed" style="margin-bottom:0px;">
		<tbody>
		<?php foreach ((array)$Group->PaymentOptions AS $Option){ ?>
			<tr>
				<td><i><?php echo $Option->Name;?>:</i></td>
				<td><?php echo number_format($Option->Summ, 2, ',', ' ');?> <?php echo $Option->Currency;?></td>
			</tr>
		<?php } ?>
		</tbody>
		</table>
	</div>

	<div class="row-fluid">
	<?php
	foreach ($Group->Itineraries AS $Itinerary)
	{
		$Variant		= current($Itinerary->Variants);
		$FirstSegment	= current($Variant->Segments);
		$LastSegment	= end($Variant->Segments);

		$FirstVariant	= current($Itinerary->Variants);
	?>
		<div class="span6">
			<h3 class="label"><?php echo $FirstSegment->DepartureCityName;?> - <?php echo $LastSegment->ArrivalCityName;?></h3>

			<input type="hidden" name="ITINERARY" data-id="<?php echo $Itinerary->ItineraryID;?>" value="<?php echo $FirstVariant->VariantID;?>" />

		<?php foreach ($Itinerary->Variants AS $VariantID => $Variant){ ?>

			<?php
			if ( ! in_array($Variant->VariantID, $FilterArray[$Group->RecommendationID]))
				continue;
			?>

			<?php if ($VariantID){ ?>
			<hr />
			<?php } ?>

			<div data-variantid="<?php echo $Variant->VariantID;?>" class="highlight <?php echo ($FirstVariant->VariantID==$Variant->VariantID)?'active':'';?> cursorPointer" onclick="Check(this,'<?php echo $Variant->VariantID;?>')">
				<?php
				$SegmentNum		= 0;
				$SegmentsTotal	= count($Variant->Segments);

				foreach ($Variant->Segments AS $SegmentID => $Segment)
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
							<?php echo $Segment->MarketingAirline;?> <?php echo $Segment->FlightNumber;?><br><?php echo date('d', $Segment->DepartureDate);?> <?php echo TAConfigDates::$MonthsShort[date('n', $Segment->DepartureDate)];?></div>
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

		<?php } ?>

		</div>
	<?php } ?>
	</div>

</fieldset>

<?php
	if ($Displayed >= $Display)
		break;
}

