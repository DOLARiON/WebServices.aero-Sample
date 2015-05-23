<?php
/**
 * @package		Travel Agency Web Services (WebServices.aero)
 * @subpackage	AJAX\Search
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

$DepartureRadius	= (int)$_REQUEST['DepartureNearKM'];
$ArrivalRadius		= (int)$_REQUEST['ArrivalNearKM'];

if ( ! $DepartureRadius || $DepartureRadius < 1 || $DepartureRadius > 500)
	$DepartureRadius = 300;

if ( ! $ArrivalRadius || $ArrivalRadius < 1 || $ArrivalRadius > 500)
	$ArrivalRadius = 300;

$Routes = array();

$Routes[] = array(
		'Departure'	=> mb_strtoupper((string)$_REQUEST['Departure']),
		'Arrival'	=> mb_strtoupper((string)$_REQUEST['Arrival']),
		'Date'		=> (string)$_REQUEST['DepartureDate'],
		'DepartureRadius'	=> ((string)$_REQUEST['DepartureNear']=='Yes')?$DepartureRadius:'0',
		'ArrivalRadius'		=> ((string)$_REQUEST['ArrivalNear']=='Yes')?$ArrivalRadius:'0',
	);

if ((string)$_REQUEST['Route'] == 'RoundTrip')
	$Routes[] = array(
			'Departure'	=> mb_strtoupper((string)$_REQUEST['Arrival']),
			'Arrival'	=> mb_strtoupper((string)$_REQUEST['Departure']),
			'Date'		=> (string)$_REQUEST['ArrivalDate'],
			'DepartureRadius'	=> ((string)$_REQUEST['ArrivalNear']=='Yes')?$ArrivalRadius:'0',
			'ArrivalRadius'		=> ((string)$_REQUEST['DepartureNear']=='Yes')?$DepartureRadius:'0',
		);

$Request = array(
		'Routes'		=> $Routes,
		'Logic'			=> 'Default',
		'Class'			=> (string)$_REQUEST['Class'],
		'Travellers'	=> array(
				'ADT' => (int)$_REQUEST['ADT'],
				'CHD' => (int)$_REQUEST['CHD'],
				'INF' => (int)$_REQUEST['INF']
			),
		'Stops'			=> ((string)$_REQUEST['Stops']=='NonStop')?'NonStop':'All',//('All','NonStop','One','Two')
	);

$Params = array(
		'Compress'		=> 'GZip',
		'Format'		=> TAConfigAccess::$GroupFormat,
		'Return'		=> 'ByTimelimit',
		'ReturnLimit'	=> $Display,
		'Language'		=> 'RU',
		'Sort'			=> 'CreditCard',
		'Currency'		=> array('RUB'),
		'TimeLimit'		=> 180,
		'Tariff'		=> TAConfigAccess::$Tariff,
	);

$Client	= new BaseJsonRpcClient(TAConfigAccess::$URLExtended);
// $Result	= $Client->Search($Access, $Request, $Params);
$Result	= $Client->Regroup($Access, '555ff5f8af632cb539000018', $Params);

usort($Result->Result->Data, 'SortDisplayPrice');

/**
 * ==============================================================================================================================
 *  / Request
 * ==============================================================================================================================
 */

echo '<p id="SearchDisplayed"><i class="icon-info-sign"></i> Длительность поиска: '.round(microtime(true)-$_SERVER['REQUEST_TIME_FLOAT']).' сек. Найдено вариантов: '.(int)$Result->Result->Count.'.</p>';

?>

<?php if (in_array('Extended', $Result->Result->Filter->Types)){ ?>

<div class="alert alert-success alert-block">
	<h4>Обратите внимание,</h4>
	в выдаче присутствют варианты <span class="label label-success">Ручной стыковки</span>.
</div>

<?php } ?>

<script type="text/javascript">
RequestID		= '<?php echo $Result->Result->RequestID;?>';
SearchTotal		= <?php echo (int)$Result->Result->Count;?>;
SearchDisplayed		= <?php echo (int)$Result->Result->Count>$Display?$Display:(int)$Result->Result->Count;?>;
SearchDisplayLimit	= <?php echo TAConfigAccess::$DisplayLimit;?>;
</script>

<?php
if (isset($Result->Errors) && count((array)$Result->Errors))
{
	if (is_object($Result->Errors))
		$Result->Errors = array($Result->Errors);
?>
<div class="alert alert-error">
	<button type="button" class="close" data-dismiss="alert">&times;</button>
	<h4>Допущены ошибки при поиске</h4>
	<ul>
	<?php foreach ($Result->Errors AS $Error){ ?>
		<li><?php echo $Error->Message;?></li>
	<?php } ?>
	</ul>
</div>
<?php
}

if ( ! (int)$Result->Result->Count)
{
?>
<div class="alert alert-error">
	<button type="button" class="close" data-dismiss="alert">&times;</button>
	<h4>Сожалеем,</h4>
	<p>Мы не смогли найти рейс, подходящий под условия Вашего поиска.<br />
		Причиной этого, скорее всего, является тот факт, что выбранное направление не обслуживается авиакомпаниями в заданные даты...
		<br /><br />...или в нашей поисковой системе произошел сбой - попробуйте повторить поиск позже.</p>
</div>

<?php
if ((string)$_REQUEST['Stops'] == 'NonStop')
{
?>
<div class="alert alert-success">
	<button type="button" class="close" data-dismiss="alert">&times;</button>
	<h4>Вы выставили ограничение на перелеты без пересадок!</h4>
	<p>Попробуйте запросить перелет с пересадкой.</p>
	<a href="javascript:void(0)" onclick="$('#NoStops').attr('checked', false);Search()" class="btn btn-large btn-success noWrap" style="width:280px;">
			<i class="icon-search icon-white"></i> Найти перелеты с пересадкой</a>
</div>
<?php
}
?>

<?php
	return;
}
?>

<hr/>

<?php
foreach ($Result->Result->Data AS $GroupID => $Group)
{
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
}
?>

<input type="hidden" id="ResultsTotal" value="<?php echo $Result->Result->Count;?>">

<?php if ($Result->Result->Count > $Display){ ?>
<p id="ResultsFull" style="text-align:center!important;">
	<button class="btn btn-hg btn-success" type="button" onclick="SearchMore();" style="width:500px;"><i class="icon-circle-arrow-down icon-white"></i> Отобразить еще <?php echo TAConfigAccess::$DisplayLimit;?> вариантов (всего <?php echo $Result->Result->Count;?>)</button>
</p>
<?php } ?>

<?php

/**
 * ==============================================================================================================================
 *  Filters
 * ==============================================================================================================================
 */

if (isset($Result->Result->Filter) && count((array)$Result->Result->Filter))
{
	$Filter = $Result->Result->Filter
?>

<input type="hidden" id="FilterDataPriceMin" value="<?php echo $Filter->PriceMin;?>">
<input type="hidden" id="FilterDataPriceMax" value="<?php echo $Filter->PriceMax;?>">

<div id="FilterDataTypes" style="display:none;">
<?php if (in_array('Original', $Filter->Types)){ ?>
	<label class="checkbox" style="position:relative;">
		<input type="checkbox" name="Types[]" value="Original" onchange="FilterApply()">
		<span class="label">Автоматическая</span>
		<span style="color:gray;font-size:13px;letter-spacing:-1px;background-color:#fff;text-align:right;position:absolute;top:0px;right:0px;">(от <?php echo number_format(min($Filter->TypePrices->Original), 0, ',', ' ');?> р.)</span>
	</label>
<?php } ?>
<?php if (in_array('Extended', $Filter->Types)){ ?>
	<label class="checkbox" style="position:relative;">
		<input type="checkbox" name="Types[]" value="Extended" onchange="FilterApply()">
		<span class="label label-success">Ручная</span>
		<span style="color:gray;font-size:13px;letter-spacing:-1px;background-color:#fff;text-align:right;position:absolute;top:0px;right:0px;">(от <?php echo number_format(min($Filter->TypePrices->Extended), 0, ',', ' ');?> р.)</span>
	</label>
<?php } ?>
</div>

<div id="FilterDataSwitchPrices" style="display:none;">
<?php foreach ((array)$Filter->SwitchPrices AS $PriceID => $PriceVal){ ?>
	<label class="checkbox" style="position:relative;">
		<input type="checkbox" name="Switches[]" value="<?php echo $PriceID;?>" onchange="FilterApply()">
		<?php
		switch ($PriceID)
		{
			case 0: echo 'без пересадок'; break;
			case 1: echo '1 пересадка'; break;
			default: echo $PriceID.' пересадки'; break;
		}
		?> <span style="color:gray;font-size:13px;letter-spacing:-1px;background-color:#fff;text-align:right;position:absolute;top:0px;right:0px;">(от <?php echo number_format(min($PriceVal), 0, ',', ' ');?> р.)</span>
	</label>
<?php } ?>
</div>

<div id="FilterDataMarketingAirlines" style="display:none;">
<?php foreach ((array)$Filter->MarketingAirlines AS $AirlineID => $AirlineVal){ ?>
	<label class="checkbox" style="position:relative;">
		<input type="checkbox" name="MarketingAirlines[]" value="<?php echo $AirlineID;?>" onchange="FilterApply()">
		<img src="<?php echo str_replace('{ID}', $AirlineID, TAConfigAccess::$AirlineLogoURL);?>" style="height:12px;"> <?php echo $AirlineVal;?>
		<?php if (isset($Filter->MarketingAirlinesPrice->{$AirlineID}) && $Filter->MarketingAirlinesPrice->{$AirlineID}){ ?>
			<span style="color:gray;font-size:13px;letter-spacing:-1px;background-color:#fff;text-align:right;position:absolute;top:0px;right:0px;">(от <?php echo number_format($Filter->MarketingAirlinesPrice->{$AirlineID}, 0, ',', ' ');?> р.)</span>
		<?php } ?>
	</label>
<?php } ?>
	<p style="margin:10px 0px 0px 0px;padding-top:5px;border-top:1px solid #eeeeee;"><a href="javascript:void(0)" onclick="$(this).parent().parent().find('input:checkbox').prop('checked',true);" style="text-decoration:underline;">Отметить</a> или <a href="javascript:void(0)" onclick="$(this).parent().parent().find('input:checkbox').prop('checked',false);" style="text-decoration:underline;">Снять</a> все.</p>
</div>

<?php
/* --- Itineraries --- */
foreach ((array)$Filter->Itineraries AS $ItineraryID => $Itinerary)
{
	$Itinerary->DepartureAirport	= (array)$Itinerary->DepartureAirport;
	$Itinerary->ArrivalAirport		= (array)$Itinerary->ArrivalAirport;

	asort($Itinerary->DepartureAirport);
	asort($Itinerary->ArrivalAirport);
?>
<input type="hidden" id="FilterDataItinerary-<?php echo $ItineraryID;?>-Name" value="<?php echo $Itinerary->Name;?>">
<div id="FilterDataItinerary-<?php echo $ItineraryID;?>-DepartureAirport" style="display:none;">
	<b><i class="icon-plane opacity4"></i> А/П вылета</b>
	<?php foreach ($Itinerary->DepartureAirport AS $AirportID => $AirportVal){ ?>
	<label class="checkbox" style="position:relative;">
		<input type="checkbox" name="Itineraries[<?php echo $ItineraryID;?>][DepartureAirport][]" value="<?php echo $AirportID;?>" onchange="FilterApply()">
		<?php echo $AirportVal;?>
		<?php if (isset($Itinerary->DepartureAirportPrice->{$AirportID}) && $Itinerary->DepartureAirportPrice->{$AirportID}){ ?>
			<span style="color:gray;font-size:13px;letter-spacing:-1px;background-color:#fff;text-align:right;position:absolute;top:0px;right:0px;">(от <?php echo number_format($Itinerary->DepartureAirportPrice->{$AirportID}, 0, ',', ' ');?> р.)</span>
		<?php } ?>
	</label>
	<?php } ?>
</div>
<div id="FilterDataItinerary-<?php echo $ItineraryID;?>-ArrivalAirport" style="display:none;">
	<b><i class="icon-plane opacity4"></i> А/П посадки</b>
	<?php foreach ($Itinerary->ArrivalAirport AS $AirportID => $AirportVal){ ?>
	<label class="checkbox" style="position:relative;">
		<input type="checkbox" name="Itineraries[<?php echo $ItineraryID;?>][ArrivalAirport][]" value="<?php echo $AirportID;?>" onchange="FilterApply()">
		<?php echo $AirportVal;?>
		<?php if (isset($Itinerary->ArrivalAirportPrice->{$AirportID}) && $Itinerary->ArrivalAirportPrice->{$AirportID}){ ?>
			<span style="color:gray;font-size:13px;letter-spacing:-1px;background-color:#fff;text-align:right;position:absolute;top:0px;right:0px;">(от <?php echo number_format($Itinerary->ArrivalAirportPrice->{$AirportID}, 0, ',', ' ');?> р.)</span>
		<?php } ?>
	</label>
	<?php } ?>
</div>
<?php
}
/* --- Itineraries --- */

}

/**
 * ==============================================================================================================================
 *  / Filters
 * ==============================================================================================================================
 */

