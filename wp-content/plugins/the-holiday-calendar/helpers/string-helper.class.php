<?php
class thc_string_helper {
	static function starts_with($haystack, $needle) {
		// search backwards starting from haystack length characters from the end
		return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== FALSE;
	}

	static function ends_with($haystack, $needle) {
		// search forward starting from end minus needle length characters
		return $needle === "" || (($temp = strlen($haystack) - strlen($needle)) >= 0 && strpos($haystack, $needle, $temp) !== FALSE);
	}

	static function contains($haystack, $needle) {
		return strpos($haystack,$needle) !== false;
	}

	static function convert_dates_to_single_line($startDate, $startTime, $endDate, $endTime)
	{
		$formattedString = thc_helper::formatDate($startDate); //12/23/2016

		if(!empty($startTime))
		{
			$formattedString .= ' ' . $startTime; //12/23/2016 13:15
		}

		$timeSeparator = ' - ';

		if($startDate != $endDate)
		{
			$formattedString .= ' - ' . thc_helper::formatDate($endDate); //12/23/2016 13:15 - 12/27/2016

			$timeSeparator = ' ';
		}

		if(!empty($endTime))
		{
			$formattedString .= $timeSeparator . $endTime; //12/23/2016 13:15 - (12/27/2016) 22:30
		}

		return $formattedString;
	}

	static function format_current_date($startDate, $startTime, $endDate, $endTime, $currentDate)
	{
		$formattedString = thc_helper::formatDate($currentDate); //12/25/2016

		$timeSeparator = ' ';

		if($currentDate == $startDate)
		{
			if(!empty($startTime))
			{
				$formattedString .= ' ' . $startTime; //12/23/2016 13:15

				$timeSeparator = ' - ';
			}
		}

		if($currentDate == $endDate)
		{
			if(!empty($endTime))
			{
				$formattedString .= $timeSeparator . $endTime; //12/27/2016 (13:15 -) 22:30
			}
		}

		return $formattedString;
	}

	static function format_current_title($startDate, $startTime, $endDate, $endTime, $currentDate, $title)
	{
		$formattedString = $title; //Summer concert

		$timeSeparator = ' ';

		if($currentDate == $startDate)
		{
			if(!empty($startTime))
			{
				$formattedString .= ' ' . $startTime; //Summer concert 13:15

				$timeSeparator = ' - ';
			}
		}

		if($currentDate == $endDate)
		{
			if(!empty($endTime))
			{
				$formattedString .= $timeSeparator . $endTime; //Summer concert (13:15 -) 22:30
			}
		}

		return $formattedString;
	}
}
?>
