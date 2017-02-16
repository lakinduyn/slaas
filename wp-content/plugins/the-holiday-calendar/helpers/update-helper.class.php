<?php
class thc_update_helper
{
	static function migrate_widget_settings()
	{
		//1. check if settings exist
		$settings = get_option(thc_settings_helper::OPTION_NAME);
		
		if($settings == null)
		{
			$settings = array();
			
			//Default values
			$date_format = 5; //US
			$enable_readmore = 1;
			$show_date_in_title = 1;
			
			try
			{				
				//2. If not, try to find properties of active widget
				$plugin_number = self::get_active_plugin_number();
				
				if($plugin_number != -1)
				{
					$widget_properties = get_option('widget_the_holiday_calendar');
					
					$enableReadMore = 1;
					
					if($widget_properties != null && array_key_exists($plugin_number, $widget_properties))
					{						
						$date_format_key = 'dateFormat';
						$enable_readmore_key = 'enableReadMore';
						
						if(array_key_exists($date_format_key, $widget_properties[$plugin_number]))
						{
							$date_format = $widget_properties[$plugin_number][$date_format_key];
						}
						
						if(array_key_exists($enable_readmore_key, $widget_properties[$plugin_number]))
						{
							$enable_readmore = $widget_properties[$plugin_number][$enable_readmore_key];
						}
					}
				}
			}
			catch(Exception $ex)
			{
			
			}
			
			//3. Save properties as settings
			$settings[thc_settings_helper::DATE_FORMAT_KEY] = $date_format;
			$settings[thc_settings_helper::HIDE_READMORE_KEY] = $enable_readmore ? 0 : 1;
			$settings[thc_settings_helper::SHOW_DATE_IN_TITLE_KEY] = $show_date_in_title;
			
			update_option(thc_settings_helper::OPTION_NAME, $settings);
		}
	}
	
	function get_active_plugin_number() 
	{
		$side_bars = get_option('sidebars_widgets');
			
		foreach($side_bars as $side_bar_key => $side_bar_value)
		{
			if(!thc_string_helper::contains($side_bar_key, 'inactive') && $side_bar_key != 'array_version')
			{
				if(is_array($side_bar_value))
				{
					foreach($side_bar_value as $widget_key => $widget_value)
					{
						if(thc_string_helper::starts_with($widget_value, 'the_holiday_calendar'))
						{
							$exploded = explode('-', $widget_value);
							
							$plugin_number = end($exploded);
							
							return $plugin_number;
						}
					}
				}
			}
		}
		
		return -1;
	}
}
?>