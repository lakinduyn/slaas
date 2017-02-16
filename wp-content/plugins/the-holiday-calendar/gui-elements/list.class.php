<?php
class thc_list {
	static function render_list($events, $countryIso)
	{
		$list = '';
		
		foreach($events as $event)
		{
			if($event->isExternal)
			{
				$url = get_post_type_archive_link(thc_constants::POSTTYPE);
				$url = add_query_arg(array('date' => $event->eventDate), $url);
				$url = add_query_arg(array('country' => $countryIso), $url);
			}
			else
			{
				$url = $event->url;
			}
			
			$list .= '<div class="thc-holiday" style="display: table-row;">';
			$eventTitle = '<a href="' . $url . '" title="' . $event->title . '"' . (!$event->isExternal ? 'class="customEvent"' : '') . '>' . $event->title . '</a>';
			
			$list .= '<div class="date" style="display: table-cell; padding-right: 10px;">' . $event->formattedDate . '</div><div class="name" style="display: table-cell; padding-bottom: 10px;">' . $eventTitle . '</div>';
			
			$list .= '</div>';
		}
		
		return $list;
	}
}
?>