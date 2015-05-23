<?php
/**
 * @package		Travel Agency Web Services (WebServices.aero)
 * @subpackage	Index.php
 * @copyright	Copyright (C) 2000 - 2015 Eugene Design. All rights reserved.
 * @license		GNU/GPLv2
 */

// We are a valid entry point.
define('_TAEXEC', TRUE);

require 'init.php';
require TAPATH_BASE.DS.'config.php';

/**
 * ==============================================================================================================================
 *  Init
 * ==============================================================================================================================
 */

set_time_limit(1200);
error_reporting(E_ALL);
ini_set('display_errors', FALSE);

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
	<link href="/css/bootstrap.datepicker.css" rel="stylesheet" />
	<link href="/css/select2.css" rel="stylesheet" />
	<link href="/css/nouislider.fox.css" rel="stylesheet" />
	<link href="/css/style.css" rel="stylesheet" />

	<link rel="shortcut icon" href="/favicon.png" />

	<!--[if lt IE 9]>
	<script type="text/javascript" src="/js/modernizr.custom.js"></script>
	<![endif]-->

	<script type="text/javascript" src="/js/jquery-2.1.4.min.js"></script>
	<script type="text/javascript" src="/js/bootstrap.min.js"></script>
	<script type="text/javascript" src="/js/scrollport.min.js"></script>

	<script type="text/javascript" src="/js/select2.min.js"></script>
	<script type="text/javascript" src="/js/select2.ru.js"></script>

	<script type="text/javascript" src="/js/bootstrap.datepicker.js" charset="UTF-8"></script>
	<script type="text/javascript" src="/js/bootstrap.datepicker.ru.js" charset="UTF-8"></script>

	<script type="text/javascript" src="/js/nouislider.min.js" charset="UTF-8"></script>

	<script type="text/javascript" src="/js/application.js"></script>
</head>
<body>

<div class="container">
	<div class="row-fluid">
		<div class="span9">

<script type="text/javascript">

var DeparturePreload	= {id: 'MOW', name: 'Москва (MOW), Россия'};
var ArrivalPreload		= {id: 'LED', name: 'Санкт-Петербург (LED), Россия'};

$(function()
{
	$('#DepartureDate-Append').datepicker('setStartDate', '<?php echo date('d-m-Y', strtotime('+7 DAYS'));?>');
	$('#ArrivalDate-Append').datepicker('setStartDate', '<?php echo date('d-m-Y', strtotime('+14 DAYS'));?>');
	$('#DepartureDate-Append,#ArrivalDate-Append').datepicker('setEndDate', '<?php echo date('d-m-Y', strtotime('+1 YEAR'));?>');

	$("#DepartureName").select2('data', DeparturePreload);
	$("#ArrivalName").select2('data', ArrivalPreload);
});

</script>

<div class="row-fluid">
	<div class="span6" style="position:relative;">
		<h5 style="margin:0px 0px 3px 0px;">Откуда:</h5>
		<input type="text" id="DepartureName" value="" class="span11" />
		<input type="hidden" id="Departure" value="" />

		<button type="button" class="btn" onclick="CitySwap()" style="position:absolute;top:22px;right:-12px;"><i class="icon-refresh"></i></button>

		<div id="Route" class="btn-group" data-toggle="buttons-radio">
			<button type="button" class="btn active" value="RoundTrip" onclick="RouteUpdate('RoundTrip')">Туда и обратно</button>
			<button type="button" class="btn" value="OneWay" onclick="RouteUpdate('OneWay')">В одну сторону</button>
		</div>
	</div>
	<div class="span6">
		<h5 style="margin:0px 0px 3px 0px;">Куда:</h5>
		<input type="text" id="ArrivalName" value="" class="span11" />
		<input type="hidden" id="Arrival" value="" />

		<div id="Class" class="btn-group" data-toggle="buttons-radio">
			<button type="button" class="btn active" value="Econom" onclick="ClassUpdate('Econom')">Эконом</button>
			<button type="button" class="btn" value="Business" onclick="ClassUpdate('Business')">Бизнес</button>
			<button type="button" class="btn" value="First" onclick="ClassUpdate('First')">Первый</button>
		</div>
	</div>
</div>

<div class="row-fluid">
	<div class="span6">
		<h5 style="margin:18px 0px 3px 0px;">Дата вылета:</h5>
		<div class="input-append date" id="DepartureDate-Append">
			<input type="text" id="DepartureDate" value="15-06-2015" class="span10" />
			<span class="add-on"><i class="icon-calendar"></i></span>
		</div>
	</div>
	<div class="span6">
		<div id="Route-OneWay">
			<h5 style="margin:18px 0px 3px 0px;">Дата прилёта:</h5>
			<div class="input-append date" id="ArrivalDate-Append">
				<input type="text" id="ArrivalDate" value="22-06-2015" class="span10" />
				<span class="add-on"><i class="icon-calendar"></i></span>
			</div>
		</div>
	</div>
</div>

<div class="row-fluid">
	<div class="span6">
		<h5 style="margin:7px 0px 3px 0px;">Взрослых (12+ лет):</h5>
		<div id="Travellers-ADT-Display" class="btn-group" data-toggle="buttons-radio">
			<button type="button" class="btn active" value="1" onclick="TravellersUpdate('ADT',1)">1</button>
			<button type="button" class="btn" value="2" onclick="TravellersUpdate('ADT',2)">2</button>
			<button type="button" class="btn" value="3" onclick="TravellersUpdate('ADT',3)">3</button>
			<button type="button" class="btn" value="4" onclick="TravellersUpdate('ADT',4)">4</button>
			<button type="button" class="btn" value="5" onclick="TravellersUpdate('ADT',5)">5</button>
			<button type="button" class="btn" value="6" onclick="TravellersUpdate('ADT',6)">6</button>
			<button type="button" class="btn" value="7" onclick="TravellersUpdate('ADT',7)">7</button>
			<button type="button" class="btn" value="8" onclick="TravellersUpdate('ADT',8)">8</button>
			<!-- <button type="button" class="btn" value="9" onclick="TravellersUpdate('ADT',9)">9</button> -->
		</div>
		<input type="hidden" id="Travellers-ADT" value="1">
	</div>
	<div class="span6">
		<div class="row-fluid">
			<div class="span6">
				<h5 style="margin:7px 0px 3px 0px;">Детей (2...<12 лет):</h5>
				<div class="input-prepend input-append">
					<button class="btn" onclick="TravellersUpdate('CHD','DN')"><i class="icon-minus"></i></button>
					<span id="Travellers-CHD-Display" class="span1 text-center uneditable-input" style="cursor:default;color:#333333;">&mdash;</span>
					<button class="btn" onclick="TravellersUpdate('CHD','UP')"><i class="icon-plus"></i></button>
				</div>
				<input type="hidden" id="Travellers-CHD" value="0">
			</div>
			<div class="span6">
				<h5 style="margin:7px 0px 3px 0px;">Младенцев (<2 лет):</h5>
				<div class="input-prepend input-append">
					<button class="btn" onclick="TravellersUpdate('INF','DN')"><i class="icon-minus"></i></button>
					<span id="Travellers-INF-Display" class="span1 text-center uneditable-input" style="cursor:default;color:#333333;">&mdash;</span>
					<button class="btn" onclick="TravellersUpdate('INF','UP')"><i class="icon-plus"></i></button>
				</div>
				<input type="hidden" id="Travellers-INF" value="0">
			</div>
		</div>
	</div>
</div>

<hr>

<div class="row-fluid">
	<div class="span3" style="width:133px;">
		<a id="SearchBTN" href="javascript:void(0)" onclick="Search()" class="btn btn-large btn-success noWrap" style="width:80px;">
			<i class="icon-search icon-white"></i> Найти</a>
		<a id="CalendarBTN" href="javascript:void(0)" onclick="Calendar()" class="btn noWrap" style="width:94px;margin-top:5px;">
			<i class="icon-search"></i> Календарь</a>
	</div>
	<div class="span3" style="width:193px;">
		<label class="checkbox">
			<input id="NoStops" type="checkbox" value="Yes">
			Без пересадок
		</label>
	</div>
	<div class="span3 text-right" style="width:180px;float:right;">
		<a href="/?view=multi" class="btn"><i class="icon-tasks"></i> Сложный маршрут</a>
		<a href="/?view=nearest" class="btn" style="margin-top:9px;"><i class="icon-globe"></i> Соседний аэропорт</a>
	</div>
</div>

<hr>

<div id="BookModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="width:750px;">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
		<h3 id="myModalLabel">Подтверждение бронирования</h3>
	</div>
	<div class="modal-body">
		<div class="results"></div>
		<div id="ProgressBarBook" class="loading hide">
			<div class="progress progress-striped active">
				<div class="bar" style="width:0%;">0%</div>
			</div>
		</div>
	</div>
	<div class="modal-footer">
		<p style="float:left;margin:0px;margin-top:5px;display:none;"><img src="/images/loading.gif"> Переадрессация на страницу с информацией о бронировании...</p>
		<button type="button" class="btn btn-success" onclick="BookProcess()">
			Бронирование <i class="icon-arrow-right icon-white"></i></button>
	</div>
</div>

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

<div class="row-fluid" style="display:none;">
	<div id="Calendar" class="span12 calendar"></div>
</div>

<div class="row-fluid" id="ProgressBar" style="display:none">
	<div class="span12">
		<div class="progress progress-striped active">
			<div class="bar" style="width: 0%;">0%</div>
		</div>
	</div>
</div>

<script type="text/javascript">
var RequestID		= '';
var SearchTotal		= 0;
var SearchDisplayed		= 0;
var SearchDisplayLimit	= 0;
</script>

<div class="row-fluid">
	<div id="Results" class="span12 results" style="display:none;"></div>
</div>

		</div>
		<div class="span3" style="margin-top:20px;">
			<div class="well-alt">
				<form action="/check/" method="get" style="margin:0px;">
					<input type="text" name="pnr" placeholder="№ брони" value="" class="input-medium">
					<input type="text" name="surname" placeholder="Фамилия" value="" class="input-medium">
					<button type="submit" class="btn btn-success"><i class="icon-search icon-white"></i> Найти</button>
				</form>
			</div>

<?php
$FilterItinerary			= array();
$FilterItinerarySwitchTime	= array();

for ($i=0;$i<6;$i++)
{
	$FilterItinerary[] = '#FilterItinerary-'.$i.'-DepartureTime';
	$FilterItinerary[] = '#FilterItinerary-'.$i.'-ArrivalTime';

	$FilterItinerarySwitchTime[] = '#FilterItinerary-'.$i.'-SwitchTime';
}
?>

<script type="text/javascript">
$(document).ready(function()
{
	$('<?php echo implode(',',$FilterItinerary);?>').noUiSlider({
			range: [0, 24]
			,start: [0, 24]
			,step: 1
			,connect: 'lower'
			,slide: function()
				{
					var values = $(this).val();

					if (values[0] == 24){
						values[0] = '23:00';
					} else if (values[0] < 10) {
						values[0] = '0'+values[0]+':00';
					} else {
						values[0] = values[0]+':00';
					}

					if (values[1] == 24){
						values[1] = '23:59';
					} else if (values[1] < 10) {
						values[1] = '0'+values[1]+':00';
					} else {
						values[1] = values[1]+':00';
					}

					$(this).parent().find('td:eq(0)').text('от '+values[0]);
					$(this).parent().find('td:eq(1)').text('до '+values[1]);

					//FilterApply();
				}
		});

	$('<?php echo implode(',',$FilterItinerarySwitchTime);?>').noUiSlider({
			range: [0, 24]
			,start: [0, 24]
			,step: 1
			,connect: 'lower'
			,slide: function()
				{
					var values = $(this).val();

					$(this).parent().find('td:eq(0)').text('от '+values[0]+' час.');
					$(this).parent().find('td:eq(1)').text('до '+values[1]+' час.');

					//FilterApply();
				}
		});
});
</script>

<div id="Filters" style="margin-bottom:30px;display:none;">
	<h4 style="color:#e47600;">Фильтр</h4>

<div class="well-alt" style="padding: 10px 10px 10px 10px;margin-top:20px;">
	<h5 id="FilterTypesSign" style="border-bottom:1px solid #eeeeee;margin:0px 0 10px 0;display:none;">Стыковки</h5>
	<div id="FilterTypes" style="display:none;"></div>

	<h5 style="border-bottom:1px solid #eeeeee;margin:0px 0 10px 0;">Пересадки</h5>
	<div id="FilterSwitchPrices"></div>

	<h5 style="border-bottom:1px solid #eeeeee;margin:20px 0 10px 0;">Перевозчики</h5>
	<div id="FilterMarketingAirlines"></div>
</div>

<?php for ($i=0;$i<6;$i++){ ?>

<div id="FilterItinerary-<?php echo $i;?>" class="well-alt" style="padding: 10px 10px 15px 10px;margin-top:10px;display:none;">
	<a href="javascript:void(0)" onclick="$(this).parent().find('.toggle').slideToggle();$(this).parent().find('.toggle-alt').slideToggle()"><h5 style="border-bottom:1px solid #eeeeee;margin:0px 0 10px 0;"></h5></a>

	<div class="toggle" style="<?php echo ($i)?'display:none;':'';?>">
		<div>
			<table class="w100p" style="margin-bottom:5px;font-size:14px;">
			<tbody>
				<tr>
					<td><b><i class="icon-plane"></i> Номер рейса</b></td>
					<td class="text-right"><input type="text" id="FilterItinerary-<?php echo $i;?>-FlightNumber" style="margin-bottom:0px;width:50px;"></td>
				</tr>
			</tbody>
			</table>
		</div>

		<div style="margin-top:20px;">
			<b><i class="icon-random"></i> Время пересадки</b>
			<table class="w100p" style="margin-bottom:5px;font-size:12px;">
			<tbody>
				<tr>
					<td>от 0 час.</td>
					<td class="text-right">до 24 час.</td>
				</tr>
			</tbody>
			</table>
			<div id="FilterItinerary-<?php echo $i;?>-SwitchTime" class="noUiSlider" style="margin-bottom:10px;width:195px;"></div>
		</div>

		<div style="margin-top:20px;">
			<b><i class="icon-time"></i> Время вылета</b>
			<table class="w100p" style="margin-bottom:5px;font-size:12px;">
			<tbody>
				<tr>
					<td>от 00:00</td>
					<td class="text-right">до 23:59</td>
				</tr>
			</tbody>
			</table>
			<div id="FilterItinerary-<?php echo $i;?>-DepartureTime" class="noUiSlider" style="margin-bottom:10px;width:195px;"></div>
		</div>

		<div style="margin-top:20px;">
			<b><i class="icon-time"></i> Время посадки</b>
			<table class="w100p" style="margin-bottom:5px;font-size:12px;">
			<tbody>
				<tr>
					<td>от 00:00</td>
					<td class="text-right">до 23:59</td>
				</tr>
			</tbody>
			</table>
			<div id="FilterItinerary-<?php echo $i;?>-ArrivalTime" class="noUiSlider" style="margin-bottom:10px;width:195px;"></div>
		</div>

		<div id="FilterItinerary-<?php echo $i;?>-DepartureAirport" style="margin-top:20px;"></div>
		<div id="FilterItinerary-<?php echo $i;?>-ArrivalAirport" style="margin-top:20px;"></div>
	</div>
	<div class="toggle-alt" style="<?php echo ($i)?'':'display:none;';?>">
		<a href="javascript:void(0)" onclick="$(this).parent().parent().find('.toggle').slideToggle();$(this).parent().parent().find('.toggle-alt').slideToggle()" class="italic silver">Развернуть...</a>
	</div>
</div>

<?php } ?>

<button id="FilterBTN" class="btn btn-success" onclick="FilterApply()" style="min-width:118px;"><i class="icon-check icon-white"></i> Применить</button> <button class="btn" onclick="FilterReset()" style="float:right;">Сбросить</button>
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

