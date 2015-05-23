/**
 * @package		Travel Agency Web Services (WebServices.aero)
 * @subpackage	Js\Application
 * @copyright	Copyright (C) 2000 - 2015 Eugene Design. All rights reserved.
 * @license		GNU/GPLv2
 */

jQuery.fn.serializeAndEncode = function()
{
	return escape(this.find('select,textarea,input').serialize());
}

function CitiesFormatResult(City)
{
	return '<p>'+City.name+'</p>';
}

function CitiesFormatSelection(City,Container)
{
	if ($(Container).closest('.select2-container').attr('id') == 's2id_DepartureName')
		$('#Departure').val(City.id);

	if ($(Container).closest('.select2-container').attr('id') == 's2id_ArrivalName')
		$('#Arrival').val(City.id);

	return City.name;
}

$(function()
{
	$('#DepartureDate-Append,#ArrivalDate-Append').datepicker({
			format: "dd-mm-yyyy",
			language: "ru",
			weekStart: 1,
			autoclose: true
		}).on('changeDate', function(event){
			if ($(event.target).attr('id') == 'DepartureDate-Append')
				$('#ArrivalDate-Append').datepicker('setStartDate',$(event.target).find('input').val());
		});

	$("#DepartureName,#ArrivalName").select2({
			placeholder: 'Введите первые буквы города...',
			minimumInputLength: 2,
			ajax: {
					url: 'ajax/city.php',
					dataType: 'json',
					data: function (term, page)
						{
							return {
									Query: term,
									PageLimit: 10
								};
						},
					results: function (data, page)
						{
							return { results: data.Cities };
						}
				},
			formatResult:			CitiesFormatResult,
			formatSelection:		CitiesFormatSelection,
			formatInputTooShort:	function (term, minLength) { return 'Не менее 2х символов'; },
			escapeMarkup:			function (m) { return m; }
		});
});

function CitySwap()
{
	var DepartureAirport		= $('#s2id_DepartureName .select2-chosen').text();
	var DepartureAirportID		= $('#Departure').val();
	var ArrivalAirport			= $('#s2id_ArrivalName .select2-chosen').text();
	var ArrivalAirportID		= $('#Arrival').val();
	
	$('#s2id_DepartureName .select2-chosen').text(ArrivalAirport);
	$('#Departure').val(ArrivalAirportID);
	
	$('#s2id_ArrivalName .select2-chosen').text(DepartureAirport);
	$('#Arrival').val(DepartureAirportID);
}

function RouteUpdate(Way)
{
	if (Way == 'OneWay')
	{
		$('#Route button').removeClass('active');
		$('#Route button[value="OneWay"]').addClass('active');

		$('#Route-OneWay').fadeOut();
	}
	else
	{
		$('#Route button').removeClass('active');
		$('#Route button[value="RoundTrip"]').addClass('active');

		$('#Route-OneWay').fadeIn();
	}
}

function ClassUpdate(Class)
{
	switch (Class)
	{
		case 'Econom':
			$('#Class button').removeClass('active');
			$('#Class button[value="'+Class+'"]').addClass('active');
			break;
		case 'Business':
			$('#Class button').removeClass('active');
			$('#Class button[value="'+Class+'"]').addClass('active');
			break;
		case 'First':
			$('#Class button').removeClass('active');
			$('#Class button[value="'+Class+'"]').removeClass('active');
			break;
	}
}

function TravellersUpdate(ID, Count)
{
	if (Count == 'UP' || Count == 'DN')
	{
		if (Count == 'UP')
			Count = parseInt($('#Travellers-'+ID).val())+1;
		else
			Count = parseInt($('#Travellers-'+ID).val())-1;
		
		TravellersUpdate(ID, Count);
		return;
	}
	
	Count = parseInt(Count.toString(), 10);
	
	switch (ID)
	{
		case 'ADT':
			if ((Count+parseInt($('#Travellers-CHD').val())) <= 9)
			{
				$('#Travellers-'+ID).val(Count);
			}
			else
			{
				//alert('Максимум - 9 пассажиров и 9 младенцев.');
				
				setTimeout(function(){
						TravellersUpdate('ADT', $('#Travellers-ADT').val());
						TravellersUpdate('CHD', $('#Travellers-CHD').val());
						TravellersUpdate('INF', $('#Travellers-INF').val());
					}, 300);
				return;
			}
			
			if (parseInt($('#Travellers-'+ID).val()) < 1)
				$('#Travellers-'+ID).val(1);
			
			if (parseInt($('#Travellers-INF').val()) > parseInt($('#Travellers-'+ID).val()))
				$('#Travellers-INF').val($('#Travellers-'+ID).val());
		break;
		case 'CHD':
			if ((Count+parseInt($('#Travellers-ADT').val())) <= 9)
			{
				$('#Travellers-'+ID).val(Count);
			}
			else
			{
				//alert('Максимум - 9 пассажиров(взрослых и детей) и 9 младенцев (1 младенец на 1 взрослого).');
				
				setTimeout(function(){
						TravellersUpdate('ADT', $('#Travellers-ADT').val());
						TravellersUpdate('CHD', $('#Travellers-CHD').val());
						TravellersUpdate('INF', $('#Travellers-INF').val());
					}, 300);
				return;
			}
			
			if (parseInt($('#Travellers-'+ID).val()) < 0)
				$('#Travellers-'+ID).val(0);
		break;
		case 'INF':
			if (parseInt($('#Travellers-ADT').val()) >= Count)
			{
				$('#Travellers-'+ID).val(Count);
			}
			else
			{
				//alert('Младенцев не может быть больше чем Взрослых.');
				return;
			}
			
			if (parseInt($('#Travellers-'+ID).val()) < 0)
				$('#Travellers-'+ID).val(0);
		break;
	}
	
	$('#Travellers-ADT-Display button').each(function(index)
		{
			if ($(this).val() == $('#Travellers-ADT').val())
			{
				if ( ! $(this).hasClass('active'))
					$(this).addClass('active');
			}
			else
			{
				if ($(this).hasClass('active'))
					$(this).removeClass('active');
			}
		});
	
	
	$('#Travellers-CHD-Display').html($('#Travellers-CHD').val()!='0'?$('#Travellers-CHD').val():'&mdash;');
	$('#Travellers-INF-Display').html($('#Travellers-INF').val()!='0'?$('#Travellers-INF').val():'&mdash;');
}

var ProgressCount = 0;
var ProgressID;
var ProgressHolder;

function ProgressStart(ID)
{
	if (ProgressHolder != null)
	{
		ProgressStop();
		return;
	}

	if (ID == null)
		ProgressID = 'ProgressBar';
	else
		ProgressID = ID;

	ProgressCount = 0;

	$('#'+ProgressID+' .bar').css('width', '0%');
	$('#'+ProgressID+' .bar').text('');

	if (ProgressID == 'ProgressBar')
		$('#'+ProgressID+'').slideDown('slow');
	else
		$('#'+ProgressID+'').show();

	ProgressHolder = setInterval(function(){
			if (ProgressCount > 49){
				ProgressStop();
				return;
				//ProgressCount = 0;
			} else {
				ProgressCount++;
			}

			$('#'+ProgressID+' .bar').css('width', (ProgressCount*2)+'%');
		}, 1000);
}

function ProgressStop()
{
	$('#'+ProgressID+' .bar').css('width', '100%');
	$('#'+ProgressID+' .bar').text('Группировка результатов...');

	clearInterval(ProgressHolder);
	ProgressHolder = null;
}

var AJAXSearch;
var AJAXCalendar;

function Search()
{
	/* --- SearchBTN --- */
	$('#SearchBTN').attr('disabled', 'disabled');
	/* --- / SearchBTN --- */
	
	/* --- SearchBTN --- */
	setTimeout(function(){
			$('#SearchBTN').removeAttr('disabled');
		}, 5000);
	/* --- / SearchBTN --- */
	
	ProgressStart();
	
	//** Фильтры
	FilterDeInit();

	$('#Results').slideUp('slow',function()
		{
			$('#Results').html('');
			
			if (AJAXSearch && AJAXSearch.readystate != 4)
				AJAXSearch.abort();
			
			/** **/
			AJAXSearch = $.ajax({
				url:		'ajax/search.php',
				timeout:	700000,
				dataType:	'html',
				global:		false,
				type:		'POST',
				data:
					{
						'Departure':	$('#Departure').val(),
						'Arrival':		$('#Arrival').val(),
						'Route':		$('#Route .active').attr('value'),
						'Class':		$('#Class .active').attr('value'),
						'DepartureDate':	$('#DepartureDate').val(),
						'ArrivalDate':		$('#ArrivalDate').val(),
						'DepartureNear':	$('#DepartureNear').is(':checked')?'Yes':'No',
						'ArrivalNear':		$('#ArrivalNear').is(':checked')?'Yes':'No',
						'DepartureNearKM':	$('#DepartureNearKM').val(),
						'ArrivalNearKM':	$('#ArrivalNearKM').val(),
						'ADT':		$('#Travellers-ADT').val(),
						'CHD':		$('#Travellers-CHD').val(),
						'INF':		$('#Travellers-INF').val(),
						'Stops':	$('#NoStops').is(':checked')?'NonStop':'All'
					},
				success: function(data)
					{
						$('#Results').html(data);
						$('#Results').slideDown();

						ProgressStop();
						
						$('#Results .extended-link').popover({
								html: true,
								placement: 'right',
								trigger: 'hover',
								title: 'Варианты оплаты',
								content: function()
									{
										return $(this).closest('fieldset').find('.extended-data').html();
									},
								template: '<div class="popover" style="max-width:800px;"><div class="arrow"></div><h3 class="popover-title" style="text-align:left;"></h3><div class="popover-content" style="text-align:left;font-weight:normal;font-size:12px;"></div></div>',
								delay: { show: 100, hide: 300 }
							});
						
						//** Фильтры
						FilterInit();

						setTimeout(function(){
								$('#ProgressBar').slideUp('slow');
								
								//** Scroll
								$.scrollport('#Results', {mode: 'roll', speed: 50});
							}, 1000);

					},
				error: function(data)
					{
						$('#Results').html('<div class="alert">Временный сбой обработки запроса. Не получен ответ от системы. Пожалуйста, повторите попытку.</div>');

						ProgressStop();

						$('#ProgressBar').slideUp('fast');
						$('#Results').slideDown();
					}
			});
			/** **/
		});
}

function SearchMore()
{
	SearchDisplayed = SearchDisplayed + SearchDisplayLimit;
	FilterApply();
}

function Calendar()
{
	/* --- SearchBTN --- */
	$('#CalendarBTN').attr('disabled', 'disabled');
	/* --- / SearchBTN --- */
	
	/* --- SearchBTN --- */
	setTimeout(function(){
			$('#CalendarBTN').removeAttr('disabled');
		}, 5000);
	/* --- / SearchBTN --- */
	
	$('#Calendar').parent().hide();
	$('#Calendar').html('<i><img src="images/loading.gif"> Анализ соседних дат...</i>');
	$('#Calendar').parent().slideDown('slow');
	
	/** **/
	AJAXCalendar = $.ajax({
		url:		'ajax/calendar.php',
		timeout:	700000,
		dataType:	'html',
		global:		false,
		type:		'POST',
		data:
			{
				'dep': $("#dep").val(),
				'arr': $("#arr").val(),
				'dest': $("#dest .active").attr('value'),
				'class': $("#class .active").attr('value'),
				'ddep': $("#ddep").val(),
				'darr': $("#darr").val(),
				'adt': $("#Search-ADT").val(),
				'chd': $("#Search-CHD").val(),
				'inf': $("#Search-INF").val(),
				'logic': $("#logic").val(),
				'stops': $("#Search-NoStops").is(':checked')?'NonStop':'All',
				'timelimit': $("#Timelimit").val(),
				'tariff': $("#Tariff").val()
			},
		success: function(data)
			{
				$('#Calendar').html(data);
				$('#Calendar').parent().slideDown('slow');

				setTimeout(function(){
						/** Scroll ** /
						if ($("#Search-Scroll").is(':checked'))
						$('html,body').animate({
								scrollTop: ($('#Calendar').offset().top-90)
							}, 1200);
						/** /Scroll **/
					}, 1000);

			},
		error: function(data)
			{
				$('#Calendar').html('<div class="alert">Временный сбой обработки запроса. Не получен ответ от системы. Пожалуйста, повторите попытку.</div>');
				$('#Calendar').parent().slideDown('slow');
			}
	});
	/** **/
}

function Check(Itinerary,ID)
{
	if ($(Itinerary).hasClass('active'))
		return;

	$(Itinerary).parent().find('input[name="ITINERARY"]').val(ID);
	$(Itinerary).parent().find('.highlight').each(function(index)
		{
			if ($(this).hasClass('active'))
				$(this).removeClass('active');
		});

	$(Itinerary).addClass('active');
}

function FareRules(ID)
{
	var Variants = new Array();

	$('#BOOK-'+ID+'-GROUP input[name="ITINERARY"]').each(function(index)
		{
			Variants.push($(this).val());
		});

	$('#FareRulesModal .results').hide();
	$('#FareRulesModal .results').html('');

	$('#FareRulesModal').modal();

	ProgressStart('ProgressBarFareRules');

	/** **/
	AJAXBook = $.ajax({
			url:		'ajax/farerules.php',
			timeout:	700000,
			dataType:	'html',
			global:		false,
			type:		'POST',
			data:
				{
					'ID': $("#RequestID").val(),
					'Variants': Variants
				},
			success: function(data) {
					ProgressStop();

					$('#FareRulesModal .results').html(data);
					$('#FareRulesModal .results').slideDown(1000);

					$('#ProgressBarFareRules').slideUp('slow');

					/* * /
					setTimeout(function(){
							$('#FareRulesModal .modal-body').scrollTo('table:eq(0)', 1000);
						}, 2000);
					/* */
				},
			error: function(data) {
					$('#FareRulesModal .results').html('<div class="error">Временный сбой обработки запроса. Не получен ответ от системы. Пожалуйста, повторите попытку.</div>');

					ProgressStop();

					$('#ProgressBarFareRules').slideUp('fast');
					$('#FareRulesModal .results').slideDown('slow');
				}
		});
	/** **/
}

function Book(ID)
{
	var Variants = new Array();

	$('#BOOK-'+ID+'-GROUP input[name="ITINERARY"]').each(function(index) {
			Variants.push($(this).val());
		});

	$('#BookModal .results').hide();
	$('#BookModal .results').html('');

	$('#BookModal').modal();

	ProgressStart('ProgressBarBook');

	/** **/
	AJAXBook = $.ajax({
			url:		'ajax/information.php',
			timeout:	700000,
			dataType:	'html',
			global:		false,
			type:		'POST',
			data:
				{
					'ID': RequestID,
					'Variants': Variants
				},
			beforeSend: function ()
				{
					$('#BookModal button').attr('disabled','disabled');
				},
			success: function(data)
				{
					ProgressStop();

					$('#BookModal .results').html(data);
					$('#BookModal .results').slideDown(1000);

					$('#ProgressBarBook').slideUp('slow');

					/* */
					setTimeout(function(){
							//** Scroll
							$('#BookModal .modal-body').scrollport('table:eq(0)', {mode: 'roll', speed: 25});
						}, 2000);
					/* */
				},
			error: function(data)
				{
					$('#BookModal .results').html('<div class="error">Временный сбой обработки запроса. Не получен ответ от системы. Пожалуйста, повторите попытку.</div>');

					ProgressStop();

					$('#ProgressBarBook').slideUp('fast');
					$('#BookModal .results').slideDown('slow');
				},
			complete : function ()
				{
					$('#BookModal button').removeAttr('disabled');
	            }
		});
	/** **/
}

function BookProcess()
{
	$('#BookResults').html('');

	ProgressStart('ProgressBarBook');

	/* */
	setTimeout(function(){
			//** Scroll
			$('#BookModal .modal-body').scrollport('#ProgressBarBook', {mode: 'roll', speed: 25});
		}, 100);
	/* */

	/** **/
	AJAXBook = $.ajax({
			url:		'ajax/book.php',
			timeout:	700000,
			dataType:	'html',
			global:		false,
			type:		'POST',
			data:
				{
					'ID':	$('#BookID').val(),
					'Data':	$('#BookForm').serializeAndEncode()
				},
			beforeSend : function ()
				{
					//** Отключаем кнопку
					$('#BookModal input').attr('disabled','disabled');
					$('#BookModal select').attr('disabled','disabled');
					$('#BookModal button').attr('disabled','disabled');
				},
			success: function(data)
				{
					ProgressStop();

					$('#BookResults').html(data);

					if ($('#BookResults').find('.errors').html())
					{
						$('#BookFormErrors').html($('#BookResults').find('.errors').html());

						/* */
						setTimeout(function(){
								//** Scroll
								$('#BookModal .modal-body').scrollport('#BookFormErrors', {mode: 'roll', speed: 25});
							}, 500);
						/* */
					}
					else
					{
						$('#BookFormErrors').html('');
					}

					setTimeout(function(){ $('#ProgressBarBook').slideUp('slow'); }, 1000);
				},
			error: function(data)
				{
					$('#BookModal input').removeAttr('disabled');
					$('#BookModal select').removeAttr('disabled');
					$('#BookModal button').removeAttr('disabled');
					
					$('#BookResults').html('<div class="error">Временный сбой обработки запроса. Не получен ответ от системы. Пожалуйста, повторите попытку.</div>');
					
					ProgressStop();
					
					$('#ProgressBarBook').slideUp('fast');
					$('#BookResults').slideDown('slow');
				},
			complete : function ()
				{
	            }
		});
	/** **/
}

function FilterInit()
{
	if ( ! $('#Filters').length)
		return;
	
	if ( ! $('#FilterDataSwitchPrices').length)
		return;
	
	$('#FilterTypesSign').show();
	$('#FilterTypes').show();
	$('#FilterTypes').html($('#FilterDataTypes').html());
	
	$('#FilterSwitchPrices').html($('#FilterDataSwitchPrices').html());
	$('#FilterMarketingAirlines').html($('#FilterDataMarketingAirlines').html());
	
	for (var i=0;i<6;i++)
	{	
		$('#FilterItinerary-'+i+'-SwitchTime').val([0,24]);
		$('#FilterItinerary-'+i+'-DepartureTime').val([0,24]);
		$('#FilterItinerary-'+i+'-ArrivalTime').val([0,24]);
		
		$('#FilterItinerary-'+i+'-SwitchTime').parent().find('td:eq(0)').text('от 0 час.');
		$('#FilterItinerary-'+i+'-SwitchTime').parent().find('td:eq(1)').text('до 24 час.');
		
		$('#FilterItinerary-'+i+'-DepartureTime').parent().find('td:eq(0)').text('от 00:00');
		$('#FilterItinerary-'+i+'-DepartureTime').parent().find('td:eq(1)').text('до 23:59');
		
		$('#FilterItinerary-'+i+'-ArrivalTime').parent().find('td:eq(0)').text('от 00:00');
		$('#FilterItinerary-'+i+'-ArrivalTime').parent().find('td:eq(1)').text('до 23:59');

		if ($('#FilterDataItinerary-'+i+'-DepartureAirport').length || $('#FilterDataItinerary-'+i+'-ArrivalAirport').length)
		{
			$('#FilterItinerary-'+i+' h5').html($('#FilterDataItinerary-'+i+'-Name').val());
			$('#FilterItinerary-'+i+'-DepartureAirport').html($('#FilterDataItinerary-'+i+'-DepartureAirport').html());
			$('#FilterItinerary-'+i+'-ArrivalAirport').html($('#FilterDataItinerary-'+i+'-ArrivalAirport').html());
			$('#FilterItinerary-'+i).fadeIn();
		}
		else
		{
			$('#FilterItinerary-'+i).fadeOut();
		}
	}
	
	//** Отображаем Фильтры
	$('#Filters').fadeIn();
}

function FilterDeInit()
{
	$('#Filters').fadeOut();
}

var AJAXFilter;
var FilterData = {};

function FilterApply()
{
	// Скрываем кнопку
	$('#ResultsFull').hide();
	
	$('#Results').css('filter', 'blur(5px)');
	$('#Results').css('-webkit-filter', 'blur(5px)');
	
	/* --- FilterBTN --- */
	$('#FilterBTN').removeClass('btn-success');
	$('#FilterBTN').html('<i class="icon-refresh"></i>');
	$('#FilterBTN').attr('disabled', 'disabled');
	/* --- / FilterBTN --- */
	
	var Types		= [];
	var TypesTemp	= $('#FilterTypes input').serializeArray();
	jQuery.each(TypesTemp, function(i, Data){
		Types[i] = Data.value;
	});

	var Switches		= [];
	var SwitchesTemp	= $('#FilterSwitchPrices input').serializeArray();
	jQuery.each(SwitchesTemp, function(i, Data){
			Switches[i] = Data.value;
		});
	
	var MarketingAirlines		= [];
	var MarketingAirlinesTemp	= $('#FilterMarketingAirlines input').serializeArray();
	jQuery.each(MarketingAirlinesTemp, function(i, Data){
			MarketingAirlines[i] = Data.value;
		});
	
	FilterData.Types				= Types;
	FilterData.Switches				= Switches;
	FilterData.MarketingAirlines	= MarketingAirlines;
	
	for (var i=0;i<6;i++)
	{
		if ( ! $('#FilterItinerary-'+i).is(":visible"))
			continue;
		
		window['FilterData']['Itinerary'+i+'FlightNumber']		= $('#FilterItinerary-'+i+'-FlightNumber').val();
		window['FilterData']['Itinerary'+i+'SwitchTime']		= $('#FilterItinerary-'+i+'-SwitchTime').val();
		window['FilterData']['Itinerary'+i+'DepartureTime']		= $('#FilterItinerary-'+i+'-DepartureTime').val();
		window['FilterData']['Itinerary'+i+'ArrivalTime']		= $('#FilterItinerary-'+i+'-ArrivalTime').val();
		
		var DepartureAirport		= [];
		var DepartureAirportTemp	= $('#FilterItinerary-'+i+'-DepartureAirport input').serializeArray();
		jQuery.each(DepartureAirportTemp, function(ii, Data){
				DepartureAirport[ii] = Data.value;
			});
		
		var ArrivalAirport			= [];
		var ArrivalAirportTemp		= $('#FilterItinerary-'+i+'-ArrivalAirport input').serializeArray();
		jQuery.each(ArrivalAirportTemp, function(ii, Data){
				ArrivalAirport[ii] = Data.value;
			});
		
		window['FilterData']['Itinerary'+i+'DepartureAirport']	= DepartureAirport;
		window['FilterData']['Itinerary'+i+'ArrivalAirport']	= ArrivalAirport;
	}
	
	/** --- Request --- **/
	
	if (AJAXFilter && AJAXFilter.readystate != 4)
		AJAXFilter.abort();

	AJAXFilter = $.ajax(
		{
			url:		'ajax/filter.php',
			timeout:	30000,
			dataType:	'html',
			global:		false,
			type:		'POST',
			data: {
					'ID':		RequestID,
					'Filter':	FilterData,
					'Display':	SearchDisplayed
				},
			success: function(data)
				{
					$('#Results').css('filter', 'none');
					$('#Results').css('-webkit-filter', 'none');
					$('#Results').html(data);
					
					$('#Results .extended-link').popover({
							html: true,
							placement: 'right',
							trigger: 'hover',
							title: 'Стоимость перелета',
							content: function()
								{
									return $(this).closest('fieldset').find('.extended-data').html();
								},
							template: '<div class="popover" style="max-width:800px;"><div class="arrow"></div><h3 class="popover-title" style="text-align:left;"></h3><div class="popover-content" style="text-align:left;font-weight:normal;font-size:12px;"></div></div>',
							delay: { show: 100, hide: 300 }
						});
					
					if (SearchTotal > SearchDisplayed)
						$('#ResultsAdd').show();
					
					/* --- FilterBTN --- */
					$('#FilterBTN').addClass('btn-success');
					$('#FilterBTN').html('<i class="icon-check icon-white"></i> Применить');
					$('#FilterBTN').removeAttr('disabled');
					/* --- / FilterBTN --- */
				},
			error: function(data)
				{
					//$('#Results').html('<div class="alert">Временный сбой обработки запроса. Не получен ответ от системы. Пожалуйста, повторите попытку.</div>');
				
					$('#Results').css('filter', 'none');
					$('#Results').css('-webkit-filter', 'none');
					
					/* --- FilterBTN --- */
					$('#FilterBTN').addClass('btn-success');
					$('#FilterBTN').html('<i class="icon-check icon-white"></i> Применить');
					$('#FilterBTN').removeAttr('disabled');
					/* --- / FilterBTN --- */
				}
		});

	/** --- / Request --- **/
}

function FilterReset()
{
	$('#Filters input:checkbox').prop('checked', false);
	
	for (var i=0;i<6;i++)
	{	
		$('#FilterItinerary-'+i+'-FlightNumber').val('');
		
		$('#FilterItinerary-'+i+'-SwitchTime').val([0,24]);
		$('#FilterItinerary-'+i+'-DepartureTime').val([0,24]);
		$('#FilterItinerary-'+i+'-ArrivalTime').val([0,24]);

		$('#FilterItinerary-'+i+'-SwitchTime').parent().find('td:eq(0)').text('от 0 час.');
		$('#FilterItinerary-'+i+'-SwitchTime').parent().find('td:eq(1)').text('до 24 час.');

		$('#FilterItinerary-'+i+'-DepartureTime').parent().find('td:eq(0)').text('от 00:00');
		$('#FilterItinerary-'+i+'-DepartureTime').parent().find('td:eq(1)').text('до 23:59');

		$('#FilterItinerary-'+i+'-ArrivalTime').parent().find('td:eq(0)').text('от 00:00');
		$('#FilterItinerary-'+i+'-ArrivalTime').parent().find('td:eq(1)').text('до 23:59');
	}
	
	$('#Results fieldset').show();
	$('#Results fieldset .highlight').show();
}


