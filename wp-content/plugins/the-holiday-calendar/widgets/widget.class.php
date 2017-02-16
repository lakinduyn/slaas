<?php
class thc_widget {
	static function show($args, $instance) {
		extract( $args );
		// these are the widget options
		$title = apply_filters('widget_title', $instance['title']);

		echo $before_widget;
		// Display the widget
		echo '<div class="widget-text wp_widget_plugin_box">';

		// Check if title is set
		if ( $title ) {
		  echo $before_title . $title . $after_title;
		}

		$displayMode = isset($instance['displayMode']) ? $instance['displayMode'] : '0';

		$includeHolidays = !isset($instance['includeThcEvents2']) || $instance['includeThcEvents2'] == '1';
		$countryIso = $includeHolidays ? (isset($instance['country2']) ? $instance['country2'] : 'US') : null;
		$numberOfHolidays = isset($instance['numberOfHolidays']) ? $instance['numberOfHolidays'] : '3';
		$unique_id = $instance['unique_id'];
		$firstDayOfWeek = isset($instance['firstDayOfWeek']) ? $instance['firstDayOfWeek'] : '0';

		echo '<div class="thc-widget-content">';

		echo thc_gui_builder::build($displayMode, $includeHolidays, $countryIso, $numberOfHolidays, $firstDayOfWeek, $unique_id);

		echo '</div>';

		if('1' == $instance['show_powered_by'] ) {
			echo '<div class="thc-widget-footer" style="clear: left;"><span class="thc-powered-by" style="clear: left;">Powered by&nbsp;</span><a href="http://www.theholidaycalendar.com/" title="The Holiday Calendar - All holidays in one overview" target="_blank">The Holiday Calendar</a></div>';
		}

		echo '</div>';

		echo $after_widget;
	}
}
?>
