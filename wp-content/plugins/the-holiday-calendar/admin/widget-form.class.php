<?php
class thc_widget_form {
	static function render_form($instance, $plugin)
	{
		// Check values
		if( $instance) {
			 $title = esc_attr($instance['title']);
		} else {
			 $title = '';
		}

		$countries = array('United States' => 'US', 'India' => 'IN', 'Japan' => 'JP', 'Brazil' => 'BR', 'Russia' => 'RU', 'Germany' => 'DE', 'United Kingdom' => 'GB', 'France' => 'FR', 'Mexico' => 'MX', 'South Korea' => 'KR', 'Australia' => 'AU', 'Ireland' => 'IE', 'Italy' => 'IT');
		$selectedCountry = isset($instance['country2']) ? $instance['country2'] : 'US';

		ksort($countries);

		$showPoweredBy = isset($instance['show_powered_by']) ? $instance['show_powered_by'] : '0';
		$includeThcEvents = isset($instance['includeThcEvents2']) ? $instance['includeThcEvents2'] : '1';
		$displayMode = isset($instance['displayMode']) ? $instance['displayMode'] : '0';
		$firstDayOfWeek = isset($instance['firstDayOfWeek']) ? $instance['firstDayOfWeek'] : '0';
		$numberOfHolidays = isset($instance['numberOfHolidays']) ? $instance['numberOfHolidays'] : '3';

		?>

		<p>
			<label for="<?php echo $plugin->get_field_id('title'); ?>"><?php _e('Widget Title', 'wp_widget_plugin'); ?></label>
			<input class="widefat" id="<?php echo $plugin->get_field_id('title'); ?>" name="<?php echo $plugin->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
		</p>
		<p>
			<input class="checkbox" type="checkbox" <?php checked($includeThcEvents, '1'); ?> id="<?php echo $plugin->get_field_id('includeThcEvents2'); ?>" name="<?php echo $plugin->get_field_name('includeThcEvents2'); ?>" value="1" />
			<label for="<?php echo $plugin->get_field_id('includeThcEvents2'); ?>">Include holidays (with settings below)</label>
		</p>
		<p>
			<label for="<?php echo $plugin->get_field_id('country2'); ?>">Country</label>
			<select class="widefat" id="<?php echo $plugin->get_field_id('country2'); ?>" name="<?php echo $plugin->get_field_name('country2'); ?>" >
			<?php foreach($countries as $country => $iso) { ?>
			  <option <?php selected( $selectedCountry, $iso ); ?> value="<?php echo $iso; ?>"><?php echo $country; ?></option>
			<?php } ?>
			</select>
		</p>
		<p>
			Display mode:&nbsp;
			<label><input class="radio" type="radio" <?php checked($displayMode, '0'); ?> id="<?php echo $plugin->get_field_id('displayMode'); ?>" name="<?php echo $plugin->get_field_name('displayMode'); ?>" value="0" />
			List</label>&nbsp;
			<label><input class="radio" type="radio" <?php checked($displayMode, '1'); ?> id="<?php echo $plugin->get_field_id('displayMode'); ?>" name="<?php echo $plugin->get_field_name('displayMode'); ?>" value="1" />
			Calendar</label>
		</p>
		<p>
			First day of the week:&nbsp;
			<label><input class="radio" type="radio" <?php checked($firstDayOfWeek, '0'); ?> id="<?php echo $plugin->get_field_id('firstDayOfWeek'); ?>" name="<?php echo $plugin->get_field_name('firstDayOfWeek'); ?>" value="0" />
			Sun</label>&nbsp;
			<label><input class="radio" type="radio" <?php checked($firstDayOfWeek, '1'); ?> id="<?php echo $plugin->get_field_id('firstDayOfWeek'); ?>" name="<?php echo $plugin->get_field_name('firstDayOfWeek'); ?>" value="1" />
			Mon</label>
		</p>
		<p>
			<label for="<?php echo $plugin->get_field_id('numberOfHolidays'); ?>">Number of holidays (in list mode)</label>
			<select class="widefat" id="<?php echo $plugin->get_field_id('numberOfHolidays'); ?>" name="<?php echo $plugin->get_field_name('numberOfHolidays'); ?>" >
			<?php for($i = 3; $i <= 5; $i++) { ?>
			  <option <?php selected( $numberOfHolidays, $i ); ?> value="<?php echo $i; ?>"><?php echo $i; ?></option>
			<?php } ?>
			</select>
		</p>
		<p>
			<input class="checkbox" type="checkbox" <?php checked($showPoweredBy, '1'); ?> id="<?php echo $plugin->get_field_id('show_powered_by'); ?>" name="<?php echo $plugin->get_field_name('show_powered_by'); ?>" value="1" />
			<label for="<?php echo $plugin->get_field_id('show_powered_by'); ?>">Enable "Powered by The Holiday Calendar". Thank you!!!</label>
		</p>
		<p style="font-style: italic;">Additional settings can be found on the plugin's settings page.</p>
		<p style="font-style: italic;">New! Short codes are now also supported. Check the <a title="WordPress › The Holiday Calendar « WordPress Plugins" target="_blank" href="https://wordpress.org/plugins/the-holiday-calendar/installation/">manual</a> for more info.</p>
		<?php
	}
}
?>
