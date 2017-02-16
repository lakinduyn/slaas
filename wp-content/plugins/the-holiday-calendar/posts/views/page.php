<?php 
global $wp_query;
$day = isset($wp_query->query_vars['date']) ? $wp_query->query_vars['date'] : date('Y-m-d');
$countryIso = isset($wp_query->query_vars['country']) ? $wp_query->query_vars['country'] : 'US';
$formattedDate = thc_helper::formatDate($day);
?>	
<div id="mva7-thc-main">
	<div id="mva7-thc-events">
<?php
	$events = array();
	while( have_posts() ) : the_post();		
		$events[] = array($formattedDate, get_the_title(), $day, get_the_content());	
	endwhile;
	
	$events = thc_helper::add_remote_events($events, $countryIso, NULL, $day);
		
	if(!empty($events))
	{
		foreach($events as $event)
		{		
?>
			<div class="mva7-thc-event">
				<h2><?php echo $event[1]; ?></h2>
				<p><?php echo isset($event[3]) ? $event[3] : '-'; ?></p>
			</div>
<?php
		}
	}
	else
	{
?>
		<p>No events on this day!</p>
<?php
	}
?>
	<a href="<?php echo site_url(); ?>">< Homepage</a>
	</div>
</div>