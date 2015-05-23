<?php
/**
 * @package		Travel Agency Web Services (WebServices.aero)
 * @subpackage	TA\Config
 * @copyright	Copyright (C) 2000 - 2015 Eugene Design. All rights reserved.
 * @license		GNU/GPLv2
 */

// No direct access.
defined('_TAEXEC') or die;

/**
 * ==============================================================================================================================
 *  General
 * ==============================================================================================================================
 */

class TAConfigGeneral
{
	static $Secret				= 'I3z9iMMMTYkk2msv1uyHSyM4BpDYhw';
	static $Offset				= 'Europe/Moscow';
	static $Encoding			= 'UTF-8';
	static $Locale				= 'ru_RU.UTF-8';

	static $Countries		= array(
			array('ID'=>'AM', 'Name'=>'Армения'),
			array('ID'=>'BY', 'Name'=>'Беларусь'),
			array('ID'=>'KZ', 'Name'=>'Казахстан'),
			array('ID'=>'RU', 'Name'=>'Россия','Selected'=>TRUE),
			array('ID'=>'UA', 'Name'=>'Украина'),
			array('ID'=>'',   'Name'=>'--------------------------','Disabled'=>TRUE),
			array('ID'=>'AU', 'Name'=>'Австралия'),
			array('ID'=>'AT', 'Name'=>'Австрия'),
			array('ID'=>'AZ', 'Name'=>'Азербайджан'),
			array('ID'=>'AL', 'Name'=>'Албания'),
			array('ID'=>'DZ', 'Name'=>'Алжир'),
			array('ID'=>'AS', 'Name'=>'Американское Самоа'),
			array('ID'=>'AI', 'Name'=>'Ангилья'),
			array('ID'=>'AO', 'Name'=>'Ангола'),
			array('ID'=>'AD', 'Name'=>'Андорра'),
			array('ID'=>'AQ', 'Name'=>'Антарктида'),
			array('ID'=>'AG', 'Name'=>'Антигуа и Барбуда'),
			array('ID'=>'AR', 'Name'=>'Аргентина'),
			array('ID'=>'AM', 'Name'=>'Армения'),
			array('ID'=>'AW', 'Name'=>'Аруба'),
			array('ID'=>'AF', 'Name'=>'Афганистан'),
			array('ID'=>'BS', 'Name'=>'Багамы'),
			array('ID'=>'BD', 'Name'=>'Бангладеш'),
			array('ID'=>'BB', 'Name'=>'Барбадос'),
			array('ID'=>'BH', 'Name'=>'Бахрейн'),
			array('ID'=>'BY', 'Name'=>'Беларусь'),
			array('ID'=>'BZ', 'Name'=>'Белиз'),
			array('ID'=>'BE', 'Name'=>'Бельгия'),
			array('ID'=>'BJ', 'Name'=>'Бенин'),
			array('ID'=>'BM', 'Name'=>'Бермуды'),
			array('ID'=>'BG', 'Name'=>'Болгария'),
			array('ID'=>'BO', 'Name'=>'Боливия'),
			array('ID'=>'BA', 'Name'=>'Босния и Герцеговина'),
			array('ID'=>'BW', 'Name'=>'Ботсвана'),
			array('ID'=>'BR', 'Name'=>'Бразилия'),
			array('ID'=>'IO', 'Name'=>'Британская территория в Индийском океане'),
			array('ID'=>'BN', 'Name'=>'Бруней-Даруссалам'),
			array('ID'=>'BF', 'Name'=>'Буркина-Фасо'),
			array('ID'=>'BI', 'Name'=>'Бурунди'),
			array('ID'=>'BT', 'Name'=>'Бутан'),
			array('ID'=>'VU', 'Name'=>'Вануату'),
			array('ID'=>'HU', 'Name'=>'Венгрия'),
			array('ID'=>'VE', 'Name'=>'Венесуэла'),
			array('ID'=>'VG', 'Name'=>'Виргинские острова, Британские'),
			array('ID'=>'VI', 'Name'=>'Виргинские острова, США'),
			array('ID'=>'VN', 'Name'=>'Вьетнам'),
			array('ID'=>'GA', 'Name'=>'Габон'),
			array('ID'=>'HT', 'Name'=>'Гаити'),
			array('ID'=>'GY', 'Name'=>'Гайана'),
			array('ID'=>'GM', 'Name'=>'Гамбия'),
			array('ID'=>'GH', 'Name'=>'Гана'),
			array('ID'=>'GP', 'Name'=>'Гваделупа'),
			array('ID'=>'GT', 'Name'=>'Гватемала'),
			array('ID'=>'GN', 'Name'=>'Гвинея'),
			array('ID'=>'GW', 'Name'=>'Гвинея-Бисау'),
			array('ID'=>'DE', 'Name'=>'Германия'),
			array('ID'=>'GG', 'Name'=>'Гернси'),
			array('ID'=>'GI', 'Name'=>'Гибралтар'),
			array('ID'=>'HN', 'Name'=>'Гондурас'),
			array('ID'=>'HK', 'Name'=>'Гонконг'),
			array('ID'=>'GD', 'Name'=>'Гренада'),
			array('ID'=>'GL', 'Name'=>'Гренландия'),
			array('ID'=>'GR', 'Name'=>'Греция'),
			array('ID'=>'GE', 'Name'=>'Грузия'),
			array('ID'=>'GU', 'Name'=>'Гуам'),
			array('ID'=>'DK', 'Name'=>'Дания'),
			array('ID'=>'JE', 'Name'=>'Джерси'),
			array('ID'=>'DJ', 'Name'=>'Джибути'),
			array('ID'=>'DM', 'Name'=>'Доминика'),
			array('ID'=>'DO', 'Name'=>'Доминиканская Республика'),
			array('ID'=>'EG', 'Name'=>'Египет'),
			array('ID'=>'ZM', 'Name'=>'Замбия'),
			array('ID'=>'EH', 'Name'=>'Западная Сахара'),
			array('ID'=>'ZW', 'Name'=>'Зимбабве'),
			array('ID'=>'IL', 'Name'=>'Израиль'),
			array('ID'=>'IN', 'Name'=>'Индия'),
			array('ID'=>'ID', 'Name'=>'Индонезия'),
			array('ID'=>'JO', 'Name'=>'Иордания'),
			array('ID'=>'IQ', 'Name'=>'Ирак'),
			array('ID'=>'IR', 'Name'=>'Иран, Исламская Республика'),
			array('ID'=>'IE', 'Name'=>'Ирландия'),
			array('ID'=>'IS', 'Name'=>'Исландия'),
			array('ID'=>'ES', 'Name'=>'Испания'),
			array('ID'=>'IT', 'Name'=>'Италия'),
			array('ID'=>'YE', 'Name'=>'Йемен'),
			array('ID'=>'CV', 'Name'=>'Кабо-Верде'),
			array('ID'=>'KZ', 'Name'=>'Казахстан'),
			array('ID'=>'KH', 'Name'=>'Камбоджа'),
			array('ID'=>'CM', 'Name'=>'Камерун'),
			array('ID'=>'CA', 'Name'=>'Канада'),
			array('ID'=>'QA', 'Name'=>'Катар'),
			array('ID'=>'KE', 'Name'=>'Кения'),
			array('ID'=>'CY', 'Name'=>'Кипр'),
			array('ID'=>'KG', 'Name'=>'Киргизия'),
			array('ID'=>'KI', 'Name'=>'Кирибати'),
			array('ID'=>'CN', 'Name'=>'Китай'),
			array('ID'=>'CC', 'Name'=>'Кокосовые (Килинг) острова'),
			array('ID'=>'CO', 'Name'=>'Колумбия'),
			array('ID'=>'KM', 'Name'=>'Коморы'),
			array('ID'=>'CG', 'Name'=>'Конго'),
			array('ID'=>'CD', 'Name'=>'Конго, Демократическая Республика'),
			array('ID'=>'XK', 'Name'=>'Косово'),
			array('ID'=>'CR', 'Name'=>'Коста-Рика'),
			array('ID'=>'CI', 'Name'=>'Кот д\'Ивуар'),
			array('ID'=>'CU', 'Name'=>'Куба'),
			array('ID'=>'KW', 'Name'=>'Кувейт'),
			array('ID'=>'LA', 'Name'=>'Лаос'),
			array('ID'=>'LV', 'Name'=>'Латвия'),
			array('ID'=>'LS', 'Name'=>'Лесото'),
			array('ID'=>'LB', 'Name'=>'Ливан'),
			array('ID'=>'LY', 'Name'=>'Ливия'),
			array('ID'=>'LR', 'Name'=>'Либерия'),
			array('ID'=>'LI', 'Name'=>'Лихтенштейн'),
			array('ID'=>'LT', 'Name'=>'Литва'),
			array('ID'=>'LU', 'Name'=>'Люксембург'),
			array('ID'=>'MU', 'Name'=>'Маврикий'),
			array('ID'=>'MR', 'Name'=>'Мавритания'),
			array('ID'=>'MG', 'Name'=>'Мадагаскар'),
			array('ID'=>'YT', 'Name'=>'Майотта'),
			array('ID'=>'MO', 'Name'=>'Макао'),
			array('ID'=>'MW', 'Name'=>'Малави'),
			array('ID'=>'MY', 'Name'=>'Малайзия'),
			array('ID'=>'ML', 'Name'=>'Мали'),
			array('ID'=>'UM', 'Name'=>'Малые Тихоокеанские отдаленные острова Соединенных Штатов'),
			array('ID'=>'MV', 'Name'=>'Мальдивы'),
			array('ID'=>'MT', 'Name'=>'Мальта'),
			array('ID'=>'MA', 'Name'=>'Марокко'),
			array('ID'=>'MQ', 'Name'=>'Мартиника'),
			array('ID'=>'MH', 'Name'=>'Маршалловы острова'),
			array('ID'=>'MX', 'Name'=>'Мексика'),
			array('ID'=>'FM', 'Name'=>'Микронезия, Федеративные Штаты'),
			array('ID'=>'MZ', 'Name'=>'Мозамбик'),
			array('ID'=>'MD', 'Name'=>'Молдова'),
			array('ID'=>'MC', 'Name'=>'Монако'),
			array('ID'=>'MN', 'Name'=>'Монголия'),
			array('ID'=>'MS', 'Name'=>'Монтсеррат'),
			array('ID'=>'MM', 'Name'=>'Мьянма'),
			array('ID'=>'NA', 'Name'=>'Намибия'),
			array('ID'=>'NR', 'Name'=>'Науру'),
			array('ID'=>'NP', 'Name'=>'Непал'),
			array('ID'=>'NE', 'Name'=>'Нигер'),
			array('ID'=>'NG', 'Name'=>'Нигерия'),
			array('ID'=>'AN', 'Name'=>'Нидерландские Антилы'),
			array('ID'=>'NL', 'Name'=>'Нидерланды'),
			array('ID'=>'NI', 'Name'=>'Никарагуа'),
			array('ID'=>'NU', 'Name'=>'Ниуэ'),
			array('ID'=>'NZ', 'Name'=>'Новая Зеландия'),
			array('ID'=>'NC', 'Name'=>'Новая Каледония'),
			array('ID'=>'NO', 'Name'=>'Норвегия'),
			array('ID'=>'AE', 'Name'=>'Объединенные Арабские Эмираты'),
			array('ID'=>'OM', 'Name'=>'Оман'),
			array('ID'=>'BV', 'Name'=>'Остров Буве'),
			array('ID'=>'CP', 'Name'=>'Остров Клиппертон'),
			array('ID'=>'IM', 'Name'=>'Остров Мэн'),
			array('ID'=>'NF', 'Name'=>'Остров Норфолк'),
			array('ID'=>'CX', 'Name'=>'Остров Рождества'),
			array('ID'=>'MF', 'Name'=>'Остров Святого Мартина'),
			array('ID'=>'HM', 'Name'=>'Остров Херд и острова Макдональд'),
			array('ID'=>'KY', 'Name'=>'Острова Кайман'),
			array('ID'=>'CK', 'Name'=>'Острова Кука'),
			array('ID'=>'TC', 'Name'=>'Острова Теркс и Кайкос'),
			array('ID'=>'PK', 'Name'=>'Пакистан'),
			array('ID'=>'PW', 'Name'=>'Палау'),
			array('ID'=>'PS', 'Name'=>'Палестинская территория, оккупированная'),
			array('ID'=>'PA', 'Name'=>'Панама'),
			array('ID'=>'VA', 'Name'=>'Папский Престол (Государство — город Ватикан)'),
			array('ID'=>'PG', 'Name'=>'Папуа-Новая Гвинея'),
			array('ID'=>'PY', 'Name'=>'Парагвай'),
			array('ID'=>'PE', 'Name'=>'Перу'),
			array('ID'=>'PN', 'Name'=>'Питкерн'),
			array('ID'=>'PL', 'Name'=>'Польша'),
			array('ID'=>'PT', 'Name'=>'Португалия'),
			array('ID'=>'PR', 'Name'=>'Пуэрто-Рико'),
			array('ID'=>'MK', 'Name'=>'Республика Македония'),
			array('ID'=>'RE', 'Name'=>'Реюньон'),
			array('ID'=>'RU', 'Name'=>'Россия'),
			array('ID'=>'RW', 'Name'=>'Руанда'),
			array('ID'=>'RO', 'Name'=>'Румыния'),
			array('ID'=>'WS', 'Name'=>'Самоа'),
			array('ID'=>'SM', 'Name'=>'Сан-Марино'),
			array('ID'=>'ST', 'Name'=>'Сан-Томе и Принсипи'),
			array('ID'=>'SA', 'Name'=>'Саудовская Аравия'),
			array('ID'=>'SZ', 'Name'=>'Свазиленд'),
			array('ID'=>'SH', 'Name'=>'Святая Елена'),
			array('ID'=>'KP', 'Name'=>'Северная Корея'),
			array('ID'=>'MP', 'Name'=>'Северные Марианские острова'),
			array('ID'=>'BL', 'Name'=>'Сен-Бартельми'),
			array('ID'=>'PM', 'Name'=>'Сен-Пьер и Микелон'),
			array('ID'=>'SN', 'Name'=>'Сенегал'),
			array('ID'=>'VC', 'Name'=>'Сент-Винсент и Гренадины'),
			array('ID'=>'LC', 'Name'=>'Сент-Люсия'),
			array('ID'=>'KN', 'Name'=>'Сент-Китс и Невис'),
			array('ID'=>'RS', 'Name'=>'Сербия'),
			array('ID'=>'SC', 'Name'=>'Сейшелы'),
			array('ID'=>'SG', 'Name'=>'Сингапур'),
			array('ID'=>'SY', 'Name'=>'Сирийская Арабская Республика'),
			array('ID'=>'SK', 'Name'=>'Словакия'),
			array('ID'=>'SI', 'Name'=>'Словения'),
			array('ID'=>'GB', 'Name'=>'Великобритания'),
			array('ID'=>'US', 'Name'=>'США'),
			array('ID'=>'SB', 'Name'=>'Соломоновы острова'),
			array('ID'=>'SO', 'Name'=>'Сомали'),
			array('ID'=>'SD', 'Name'=>'Судан'),
			array('ID'=>'SR', 'Name'=>'Суринам'),
			array('ID'=>'SL', 'Name'=>'Сьерра-Леоне'),
			array('ID'=>'TJ', 'Name'=>'Таджикистан'),
			array('ID'=>'TH', 'Name'=>'Таиланд'),
			array('ID'=>'TZ', 'Name'=>'Танзания, Объединенная Республика'),
			array('ID'=>'TW', 'Name'=>'Тайвань (Китай)'),
			array('ID'=>'TL', 'Name'=>'Тимор-Лесте'),
			array('ID'=>'TG', 'Name'=>'Того'),
			array('ID'=>'TK', 'Name'=>'Токелау'),
			array('ID'=>'TO', 'Name'=>'Тонга'),
			array('ID'=>'TT', 'Name'=>'Тринидад и Тобаго'),
			array('ID'=>'TV', 'Name'=>'Тувалу'),
			array('ID'=>'TN', 'Name'=>'Тунис'),
			array('ID'=>'TM', 'Name'=>'Туркмения'),
			array('ID'=>'TR', 'Name'=>'Турция'),
			array('ID'=>'UG', 'Name'=>'Уганда'),
			array('ID'=>'UZ', 'Name'=>'Узбекистан'),
			array('ID'=>'UA', 'Name'=>'Украина'),
			array('ID'=>'WF', 'Name'=>'Уоллис и Футуна'),
			array('ID'=>'UY', 'Name'=>'Уругвай'),
			array('ID'=>'FO', 'Name'=>'Фарерские острова'),
			array('ID'=>'FJ', 'Name'=>'Фиджи'),
			array('ID'=>'PH', 'Name'=>'Филиппины'),
			array('ID'=>'FI', 'Name'=>'Финляндия'),
			array('ID'=>'FK', 'Name'=>'Фолклендские острова (Мальвинские)'),
			array('ID'=>'FR', 'Name'=>'Франция'),
			array('ID'=>'GF', 'Name'=>'Французская Гвиана'),
			array('ID'=>'PF', 'Name'=>'Французская Полинезия'),
			array('ID'=>'TF', 'Name'=>'Французские Южные территории'),
			array('ID'=>'HR', 'Name'=>'Хорватия'),
			array('ID'=>'CF', 'Name'=>'Центрально-Африканская Республика'),
			array('ID'=>'TD', 'Name'=>'Чад'),
			array('ID'=>'ME', 'Name'=>'Черногория'),
			array('ID'=>'CZ', 'Name'=>'Чехия'),
			array('ID'=>'CL', 'Name'=>'Чили'),
			array('ID'=>'CH', 'Name'=>'Швейцария'),
			array('ID'=>'SE', 'Name'=>'Швеция'),
			array('ID'=>'SJ', 'Name'=>'Шпицберген и Ян Майен'),
			array('ID'=>'LK', 'Name'=>'Шри-Ланка'),
			array('ID'=>'EC', 'Name'=>'Эквадор'),
			array('ID'=>'GQ', 'Name'=>'Экваториальная Гвинея'),
			array('ID'=>'AX', 'Name'=>'Эландские острова'),
			array('ID'=>'SV', 'Name'=>'Эль-Сальвадор'),
			array('ID'=>'ER', 'Name'=>'Эритрея'),
			array('ID'=>'EE', 'Name'=>'Эстония'),
			array('ID'=>'ET', 'Name'=>'Эфиопия'),
			array('ID'=>'ZA', 'Name'=>'Южная Африка'),
			array('ID'=>'GS', 'Name'=>'Южная Джорджия и Южные Сандвичевы острова'),
			array('ID'=>'KR', 'Name'=>'Южная Корея'),
			array('ID'=>'JM', 'Name'=>'Ямайка'),
			array('ID'=>'JP', 'Name'=>'Япония'),
		);
}

/**
 * ==============================================================================================================================
 *  / General
 * ==============================================================================================================================
 */

/**
 * ==============================================================================================================================
 *  Access
 * ==============================================================================================================================
 */

class TAConfigAccess
{
	static $AuthType		= 'Site';
	static $AuthSystem		= 'Agent';
	static $AuthKey			= 'KEY-KEY-KEY';
	static $Tariff			= '1000';
	static $UserUUID		= '';

	static $URL				= 'https://ws.site.ru/';
	static $URLExtended		= 'https://ws.site.ru/services/avia/';
	static $URLExtendedFast	= 'http://ws.site.ru:82/services/avia/';
	static $AirlineLogoURL	= 'https://ws.site.ru/images/airlines/icons/{ID}.gif';
	static $IATA			= '92000000';

	static $GroupFormat		= 'FilteredCombined';
	static $DisplayLimit	= 20;

	static $DisplayNDS		= 'No';
	static $DisplayNDSFee	= 'No';
}

/**
 * ==============================================================================================================================
 *  / Access
 * ==============================================================================================================================
 */

/**
 * ==============================================================================================================================
 *  Dates
 * ==============================================================================================================================
 */

class TAConfigDates
{
	static $DWeeks			= array(1=>'Понедельник','Вторник','Среда','Четверг','Пятница','Суббота','Воскресенье');
	static $DWeeksShort		= array(1=>'Пн','Вт','Ср','Чт','Пт','Сб','Вс');
	static $Months			= array(1=>'Январь','Февраль','Март','Апрель','Май','Июнь','Июль','Август','Сентябрь','Октябрь','Ноябрь','Декабрь');
	static $MonthsAlt		= array(1=>'Января','Февраля','Марта','Апреля','Мая','Июня','Июля','Августа','Сентября','Октября','Ноября','Декабря');
	static $MonthsShort		= array(1=>'Янв','Фев','Мар','Апр','Мая','Июн','Июл','Авг','Сен','Окт','Ноя','Дек');
}

/**
 * ==============================================================================================================================
 *  / Dates
 * ==============================================================================================================================
 */

