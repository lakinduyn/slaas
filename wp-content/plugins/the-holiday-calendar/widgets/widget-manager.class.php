<?php
class thc_widget_manager {
	function update_widget_instance($new_instance, $old_instance)
	{
		$instance = $old_instance;
		// Fields
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['show_powered_by'] = $new_instance['show_powered_by'];
		$instance['country2'] = $new_instance['country2'];
		
		if(isset($new_instance['includeThcEvents2']))
		{
			$instance['includeThcEvents2'] = $new_instance['includeThcEvents2'];
		}
		else
		{
			$instance['includeThcEvents2'] = '0';
		}

		if(!array_key_exists('unique_id', $instance))
		{
			$instance['unique_id'] = thc_helper::gen_uuid();
		}

		$instance['displayMode'] = $new_instance['displayMode'];
		$instance['firstDayOfWeek'] = $new_instance['firstDayOfWeek'];
		$instance['numberOfHolidays'] = $new_instance['numberOfHolidays'];
		
		session_helper::clear_session();
		thc_cache_helper::clear_cache($instance['country2']);
		
		return $instance;
	}
}
?>