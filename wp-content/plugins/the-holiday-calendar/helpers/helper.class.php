<?php
class thc_helper
{
	static function get_remote_events_as_posts($countryIso, $widgetId = NULL, $date = NULL)
	{
		$show_readmore = !thc_settings_helper::get_hide_readmore();

		global $wp_query, $wp;
		$events = self::add_remote_events(array(), $countryIso, $widgetId, $date);

		$posts = array();

		foreach($events as $event)
		{
			$content = null;
			$post = new stdClass();

			$show_excerpt = !thc_settings_helper::get_hide_holiday_excerpt();

			if($show_readmore)
			{
				$content = thc_translation_helper::get_read_more_text($event);
			}

			if($show_excerpt)
			{
				$content = $event->teaser . $content;
			}

			$read_more_text_id = uniqid();

			$read_more_texts = request_helper::get_read_more_texts();
			$read_more_texts[$read_more_text_id] = $content;

			request_helper::set_read_more_texts($read_more_texts);

			$post->post_excerpt = $content . '<!--' . thc_constants::EXCERPT_MARKER_PREFIX . $read_more_text_id . '-->';

			//$post->ID = -1;
			$post->post_author = 1;
			$post->post_date = current_time('mysql');
			$post->post_date_gmt =  current_time('mysql', $gmt = 1);
			$post->post_content = $content;
			$post->post_title = $event->title;
			$post->post_status = 'publish';
			$post->ping_status = 'closed';
			$post->post_password = '';
			//$post->post_name = '?' . $_SERVER['QUERY_STRING'];
			$post->to_ping = '';
			$post->pinged = '';
			$post->modified = $post->post_date;
			$post->modified_gmt = $post->post_date_gmt;
			$post->post_content_filtered = '';
			$post->post_parent = 0;
			//$post->guid = get_home_url('/' . $post->post_name); // use url instead?
			$post->menu_order = 0;
			$post->post_type = thc_constants::POSTTYPE;
			$post->post_mime_type = '';
			$post->comment_status = 'closed';
			$post->comment_count = 0;
			$post->filter = 'raw';
			$post->ancestors = array(); // 3.6

			$posts[] = $post;
		}
		// reset wp_query properties to simulate a found page
		unset($wp_query->query['error']);
		$wp->query = array();
		$wp_query->query_vars['error'] = '';
		$wp_query->is_404 = FALSE;

		$wp_query->found_posts += count($events);
		$wp_query->post_count += count($events);

		$wp_query->posts = $posts;

		return $posts;
	}

	static function add_remote_events($events, $countryIso, $widgetId = NULL, $date = NULL, $from_date = NULL)
	{
		$fromDate = '2000-01-01';
		$plugin_holidays = null;

		$now = date('U');
		$last_attempt = thc_cache_helper::get_remote_holidays_last_attempt($countryIso);
		$previous_version =	thc_cache_helper::get_remote_holidays_previous_plugin_version();

		if(isset($previous_version) && $previous_version == thc_constants::PLUGIN_VERSION && $last_attempt >= ($now - thc_constants::DAY_IN_SECONDS))
		{
			$plugin_holidays = thc_cache_helper::get_remote_holidays($countryIso);
		}

		if ($plugin_holidays == null) {
			$url = 'http://www.theholidaycalendar.com/handlers/pluginData.ashx?pluginVersion=' . thc_constants::PLUGIN_VERSION . '&amountOfHolidays=1000&fromDate=' . $fromDate . '&pluginId=' . (!is_null($widgetId) ? $widgetId : '00000000-0000-0000-0000-000000000000') . '&url=' . site_url() . '&countryIso=' . $countryIso . '&dateFormat=' . thc_settings_helper::get_date_format();

			$result = wp_remote_get($url, array('timeout' => 30));

			if(is_wp_error( $result ))
			{
				return $events;
			}

			$plugin_holidays = self::convert_json_to_plugin_holidays($result['body']);

			thc_cache_helper::set_remote_holidays($plugin_holidays, $countryIso);
			thc_cache_helper::set_remote_holidays_last_attempt($now, $countryIso);
			thc_cache_helper::set_remote_holidays_previous_plugin_version();
		}

		if(isset($from_date))
		{
			$plugin_holidays = array_filter($plugin_holidays, array(new higher_or_equal_filter($from_date), 'is_higher_or_equal'));
		}

		//echo var_dump($rows);
		foreach($plugin_holidays as $plugin_holiday)
		{
			if(is_null($date) || $date == $plugin_holiday->{'date'})
			{
				$event = new thc_event();

				$event->formattedDate = $plugin_holiday->formattedDate;
				$event->title = $plugin_holiday->title;
				$event->eventDate = $plugin_holiday->{'date'};
				$event->url = $plugin_holiday->url;
				$event->isExternal = true;
				$event->teaser = $plugin_holiday->teaser;

				$events[] = $event;
			}
		}

		return $events;
	}

	static function convert_json_to_plugin_holidays($json_string)
	{
		$plugin_holidays = array();
		$json_holidays = json_decode($json_string);

		if(count($json_holidays) > 0)
		{
			foreach($json_holidays as $json_holiday)
			{
				$plugin_holidays[] = thc_plugin_holiday::create_from_object( $json_holiday );
			}
		}

		return $plugin_holidays;
	}

	static function formatDate($dateToFormat)
	{
		list($year, $month, $day) = sscanf($dateToFormat, '%04d-%02d-%02d');
		$dateToFormat = new DateTime("$year-$month-$day");

		//$dateToFormat = date_create_from_format('Y-m-d', $dateToFormat);
		/*
			0: dd-mm-yy
			1: dd.mm.yy
			2: dd.mm.yyyy
			3: dd/mm/yy
			4: dd/mm/yyyy
			5: mm/dd/yyyy (US)
			6: yy/mm/dd
			7: yyyy년 m월 d일
			8: dd-mm-yyyy
		*/

		$dateFormat = thc_settings_helper::get_date_format();

		switch ($dateFormat)
		{
			case 0:
				return date_format($dateToFormat,"d-m-y");
			case 1:
				return date_format($dateToFormat,"d.m.y");
			case 2:
				return date_format($dateToFormat,"d.m.Y");
			case 3:
				return date_format($dateToFormat,"d/m/y");
			case 4:
				return date_format($dateToFormat,"d/m/Y");
			case 5:
				return date_format($dateToFormat,"n/j/Y");
			case 6:
				return date_format($dateToFormat,"y/m/d");
			case 7:
				return date_format($dateToFormat,"Y&#45380; m&#50900; d&#51068;");
			case 8:
				return date_format($dateToFormat,"d-m-Y");
		}

		throw new InvalidOperationException("Date format not supported");
	}

	function gen_uuid() {
		return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
			// 32 bits for "time_low"
			mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),

			// 16 bits for "time_mid"
			mt_rand( 0, 0xffff ),

			// 16 bits for "time_hi_and_version",
			// four most significant bits holds version number 4
			mt_rand( 0, 0x0fff ) | 0x4000,

			// 16 bits, 8 bits for "clk_seq_hi_res",
			// 8 bits for "clk_seq_low",
			// two most significant bits holds zero and one for variant DCE1.1
			mt_rand( 0, 0x3fff ) | 0x8000,

			// 48 bits for "node"
			mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
		);
	}

	function validate_us_date($test_date) {
		$date_regex = '/^(0[1-9]|1[0-2])\/(0[1-9]|1\d|2\d|3[01])\/(19|20)\d{2}$/';
		return preg_match($date_regex, $test_date);
	}

	function validate_time($test_time) {
		$time_regex = '/^([0-9]|0[0-9]|1[0-9]|2[0-3]):[0-5][0-9]$/';
		return preg_match($time_regex, $test_time);
	}

	static function get_difference_in_days($date1, $date2) {
		$datediff = strtotime($date2) - strtotime($date1);

		return floor($datediff/(60*60*24));
	}
}

class higher_or_equal_filter {
	private $value;

	function __construct($value) {
			$this->value = $value;
	}

	function is_higher_or_equal($plugin_holiday) {
			return $plugin_holiday->{'date'} >= $this->value;
	}
}
?>
