<?php
class thc_gui_builder {
	static function build($displayMode, $includeHolidays, $countryIso, $numberOfHolidays, $firstDayOfWeek, $unique_id)
	{
		$output = '';

		global $wp_query;
		$yearToShow = $displayMode == 1 && isset($wp_query->query_vars['thc-month']) ? substr($wp_query->query_vars['thc-month'], 0, 4) : date('Y');
		$monthToShow = $displayMode == 1 && isset($wp_query->query_vars['thc-month']) ? intval(substr($wp_query->query_vars['thc-month'], 4, 2)) : date('n');

		$args = array(
				'post_type'  => thc_constants::POSTTYPE,
				'meta_query' => array(
					array(
						'key'     => 'eventDate',
						'value'   => ($yearToShow - ($displayMode == 0 ? 0 : 1)) . '-' . str_pad($monthToShow, 2 , '0', STR_PAD_LEFT) . '-' . ($displayMode == 0 ? date('d') : '01'),
						'compare' => '>=',
					),
				),
				'orderby' => 'eventDate',
				'order' => 'ASC',
				'posts_per_page' => $displayMode == 0 ? $numberOfHolidays : 100
			);

		$query = new WP_Query( $args );
		$events = array();

		// The Loop
		if ( $query->have_posts() ) {
			$separator = '';
			while ( $query->have_posts() ) {
				$query->the_post();

				$eventDate = get_post_meta( $query->post->ID, 'eventDate', true );
				$eventTime = get_post_meta( $query->post->ID, 'eventTime', true );

				$eventDateEnd = get_post_meta( $query->post->ID, 'eventDateEnd', true);
				$eventTimeEnd = get_post_meta( $query->post->ID, 'eventTimeEnd', true);

				$url = get_permalink();

				if(empty($eventDateEnd))
				{
					$eventDateEnd = $eventDate; //use $eventDate as default for backwards compatibility
				}

				$days_difference = thc_helper::get_difference_in_days($eventDate, $eventDateEnd);

				for($i = 0; $i <= $days_difference; $i++)
				{
					$currentEventDate = date('Y-m-d', strtotime($eventDate. ' + ' . $i . ' days'));

					$event = new thc_event();

					$event->formattedDate = thc_string_helper::format_current_date($eventDate, $eventTime, $eventDateEnd, $eventTimeEnd, $currentEventDate);

					request_helper::set_surpress_title_filter(true);
					$event->title = get_the_title();
					request_helper::set_surpress_title_filter(false);

					if($displayMode == 1)
					{
						$event->title = thc_string_helper::format_current_title($eventDate, $eventTime, $eventDateEnd, $eventTimeEnd, $currentEventDate, $event->title);
					}

					$event->eventDate = $currentEventDate;
					$event->url = $url;
					$event->isExternal = false;

					$events[] = $event;
				}
			}
		}

		/* Restore original Post Data */
		wp_reset_postdata();

		if($displayMode == 0)
		{
			if($includeHolidays)
			{
				$fromDate = $yearToShow . '-' . str_pad($monthToShow, 2 , '0', STR_PAD_LEFT) . '-' . date('d');

				$events = thc_helper::add_remote_events($events, $countryIso, $unique_id, NULL, $fromDate);

				usort($events, array(__CLASS__, 'sortByDate'));
			}

			$events = array_slice($events, 0, $numberOfHolidays);

			$output .= '<div class="thc-list thc-holidays" style="display:table; border-collapse: collapse;">';

			$output .= thc_list::render_list($events, $countryIso);

			$output .= '</div>';
		}
		else
		{
			if($includeHolidays)
			{
				$events = thc_helper::add_remote_events($events, $countryIso, $unique_id);
			}

			$output .= '<div class="thc-calendar widget_calendar">';
			$output .= thc_calendar::draw_calendar($monthToShow,$yearToShow, $firstDayOfWeek == 0, $events, $countryIso);
			$output .= '</div>';
		}

		return $output;
	}

	static function sortByDate($a, $b) {
		return strcasecmp($a->eventDate, $b->eventDate);
	}
}
?>
