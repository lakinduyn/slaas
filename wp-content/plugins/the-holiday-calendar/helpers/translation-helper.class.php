<?php
class thc_translation_helper {
	static function get_month_name($monthNumber)
	{
		switch(self::get_current_language())
		{
			//Portuguese
			case 'pt':	$monthTranslations = array('janeiro', 'fevereiro', 'março', 'abril', 'maio', 'junho', 'julho', 'agosto', 'setembro', 'outubro', 'novembro', 'dezembro');
						break;
			//French
			case 'fr':	$monthTranslations = array('janvier', 'février', 'mars', 'avril', 'mai', 'juin', 'juillet', 'août', 'septembre', 'octobre', 'novembre', 'décembre');
						break;
			//German
			case 'de':	$monthTranslations = array('Januar', 'Februar', 'März', 'April', 'Mai', 'Juni', 'Juli', 'August', 'September', 'Oktober', 'November', 'Dezember');
						break;
			//Hindi
			case 'hi':	$monthTranslations = array('जनवरी', 'फ़रवरी', 'मार्च', 'अप्रैल', 'मई', 'जून', 'जुलाई', 'आगस्त', 'सितम्बर', 'अकतूबर', 'नवेम्बर', 'दिसम्बर');
						break;							
			//Japanese
			case 'ja':	$monthTranslations = array('一月', '二月', '三月', '四月', '五月', '六月', '七月', '八月', '九月', '十月', '十一月', '十二月');
						break;
			//Russian
			case 'ru':	$monthTranslations = array('Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь');
						break;
			//Spanish
			case 'es':	$monthTranslations = array('enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre');
						break;
			//Korean
			case 'ko':	$monthTranslations = array('일월', '이월', '삼월', '사월', '오월', '유월', '칠월', '팔월', '구월', '시월', '십일월', '십이월');
						break;
			//English
			default:	$monthTranslations = array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
						break;
		}
		
		return $monthTranslations[$monthNumber - 1];
	}
	
	static function get_week_name_abbreviations()
	{
		switch(self::get_current_language())
		{
			//Portuguese
			case 'pt':	$weekdayTranslations = array('D', 'S', 'T', 'Q', 'Q', 'S', 'S');
						break;
			//French
			case 'fr':	$weekdayTranslations = array('D', 'L', 'M', 'M', 'J', 'V', 'S');
						break;
			//German
			case 'de':	$weekdayTranslations = array('S', 'M', 'D', 'M', 'D', 'F', 'S');
						break;
			//Hindi
			case 'hi':	$weekdayTranslations = array('र', 'सो', 'मं', 'बु', 'गु', 'शु', 'श');
						break;
			//Japanese
			case 'ja':	$weekdayTranslations = array('日', '月', '火', '水', '木', '金', '土');
						break;
			//Russian
			case 'ru':	$weekdayTranslations = array('В', 'П', 'В', 'С', 'Ч', 'П', 'С');
						break;
			//Spanish
			case 'es':	$weekdayTranslations = array('D', 'L', 'M', 'M', 'J', 'V', 'S');
						break;
			//Korean
			case 'ko':	$weekdayTranslations = array('일', '월', '화', '수', '목', '금', '토');
						break;
			//English
			default:	$weekdayTranslations = array('S', 'M', 'T', 'W', 'T', 'F', 'S');
						break;
		}
		
		return $weekdayTranslations;
	}
	
	static function get_read_more_text($event)
	{
		$read_more_string = null;
		
		switch(self::get_current_language())
		{
			//Portuguese
			case 'pt':	$read_more_string = 'Leia mais sobre %1$s na %2$s.';
						break;
			//French
			case 'fr':	$read_more_string = 'En savoir plus sur %1$s sur %2$s.';
						break;
			//German
			case 'de':	$read_more_string = 'Lesen Sie mehr über %1$s auf %2$s.';
						break;
			//Hindi
			case 'hi':	$read_more_string = 'Read more about %1$s on %2$s.';
						break;
			//Japanese
			case 'ja':	$read_more_string = 'Read more about %1$s on %2$s.';
						break;
			//Russian
			case 'ru':	$read_more_string = 'Узнайте больше о %1$s на %2$s.';
						break;
			//Spanish
			case 'es':	$read_more_string = 'Leer más sobre %1$s en %2$s.';
						break;
			//Korean
			case 'ko':	$read_more_string = 'Read more about %1$s on %2$s.';
						break;
			//English
			default:	$read_more_string = 'Read more about %1$s on %2$s.';
						break;
		}
		
		$holiday_url = '<a href="' . $event->url . '" target="_blank" title="Read more about ' . $event->title . ' on TheHolidayCalendar.com">' . $event->title . '</a>';
		$website_url = '<a href="http://www.theholidaycalendar.com/" title="The Holiday Calendar - All holidays in one place!" target="_blank">TheHolidayCalendar.com</a>';
		
		return sprintf('<p>' . $read_more_string . '</p>', $holiday_url, $website_url);
	}
	
	static function get_current_language()
	{
		$exploded = explode('-', get_bloginfo('language'));
		return $exploded[0];
	}
}
?>