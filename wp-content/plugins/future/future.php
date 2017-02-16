<?php
/*
 * Plugin Name: Future
 * Author URI: http://www.sudvarg.com
 * Plugin URI: http://www.sudvarg.com/wordpress.php
 * Description: The 'Future' plugin allows posts with future scheduled dates to be integrated into a site. This can be useful, for example, with events that have associated dates in the future. Such future posts can, with this plugin, be displayed, both individually and in archive lists. This plugin also adds functionality to the built-in calendar widget. It adds a checkbox to include future posts in the calendar, and it allows the calendar to be configured to show posts from a single category.
 * Author: Marion Sudvarg
 * Version: 1.2.4
 */

/* The following license information applies to this plugin:

Copyright 2013 Sudvarg Digital Solutions (email : msudvarg@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.


*/


/*  The following license information applies to the following block:

Copyright 2009  Show Future Posts on Single Post Templates (email : stanley@dumanig.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.


*/

add_filter('the_posts', 'futurems_show_posts');

function futurems_show_posts($posts)
{
   global $wp_query, $wpdb;

   if(is_single() && $wp_query->post_count == 0)
   {
      $posts = $wpdb->get_results($wp_query->request);
   }

   return $posts;
}

/* End Stanley Dumanig's Show Future Posts on Single Post Templates block */


/* Begin Calendar Widget */
class Futurems_Widget_Calendar extends WP_Widget {

	function __construct() {
		$widget_ops = array('classname' => 'widget_calendar', 'description' => __( 'A calendar of your site&#8217;s Posts.') );
		parent::__construct('calendar', __('Calendar'), $widget_ops);
	}

	function widget( $args, $instance ) {
		extract($args);

		/** This filter is documented in wp-includes/default-widgets.php */
		$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );

		echo $before_widget;
		if ( $title )
			echo $before_title . $title . $after_title;
		echo '<div id="calendar_wrap">';
		futurems_get_calendar(true, true, $instance['category'], $instance['future']);
		echo '</div>';
		echo $after_widget;
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['category'] = $new_instance['category'];
		$instance['future'] = $new_instance['future'];
        if ($instance['future'] == "") { $instance['future'] = 0; }
		return $instance;
	}

	function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => '', 'category' => 0, 'future' => 0 ) );
		$title = strip_tags($instance['title']);
?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></p>

		<p><label for="<?php echo $this->get_field_id("category"); ?>"><?php _e("Category:"); ?></label>
		<?php wp_dropdown_categories( array( 'name' => $this->get_field_name("category"), 'selected' => $instance['category'], 'orderby' => 'Name' , 'hierarchical' => 1, 'show_option_all' => __("All Categories"), 'hide_empty' => '0' ) ); ?></p>

		<p><label for="<?php echo $this->get_field_id("future"); ?>"><?php _e("Include Future Posts?"); ?></label>
		<input type="checkbox" value="1" id=<?php echo $this->get_field_id("future"); ?> name="<?php echo $this->get_field_name("future"); ?>" <?php if($instance['future'] == 1) { echo "checked"; } ?> /></p>
<?php
	}
}


function futurems_widget_init() {
	unregister_widget('WP_Widget_Calendar');
	register_widget('Futurems_Widget_Calendar');
}
add_action('widgets_init', 'futurems_widget_init');


function futurems_get_calendar($initial = true, $echo = true, $category = 0, $future) {
	global $wpdb, $m, $monthnum, $year, $wp_locale, $posts;
    
    $sql_futurems = "";
    if ($future == 1) { $sql_futurems = "OR post_status = 'future'"; }

	$cache = array();
	$key = md5( $m . $monthnum . $year );
	if ( $cache = wp_cache_get( 'futurems_get_calendar', 'calendar' ) ) {
		if ( is_array($cache) && isset( $cache[ $key ] ) ) {
			if ( $echo ) {
				echo apply_filters( 'futurems_get_calendar',  $cache[$key] );
				return;
			} else {
				return apply_filters( 'futurems_get_calendar',  $cache[$key] );
			}
		}
	}

	if ( !is_array($cache) )
		$cache = array();

	// Quick check. If we have no posts at all, abort!
	if ( !$posts ) {
		$gotsome = $wpdb->get_var("SELECT 1 as test FROM $wpdb->posts WHERE post_type = 'post' AND (post_status = 'publish' $sql_futurems) LIMIT 1");
		if ( !$gotsome ) {
			$cache[ $key ] = '';
			wp_cache_set( 'futurems_get_calendar', $cache, 'calendar' );
			return;
		}
	}

	if ( isset($_GET['w']) )
		$w = ''.intval($_GET['w']);

	// week_begins = 0 stands for Sunday
	$week_begins = intval(get_option('start_of_week'));

	// Let's figure out when we are
	if ( !empty($monthnum) && !empty($year) ) {
		$thismonth = ''.zeroise(intval($monthnum), 2);
		$thisyear = ''.intval($year);
	} elseif ( !empty($w) ) {
		// We need to get the month from MySQL
		$thisyear = ''.intval(substr($m, 0, 4));
		$d = (($w - 1) * 7) + 6; //it seems MySQL's weeks disagree with PHP's
		$thismonth = $wpdb->get_var("SELECT DATE_FORMAT((DATE_ADD('{$thisyear}0101', INTERVAL $d DAY) ), '%m')");
	} elseif ( !empty($m) ) {
		$thisyear = ''.intval(substr($m, 0, 4));
		if ( strlen($m) < 6 )
				$thismonth = '01';
		else
				$thismonth = ''.zeroise(intval(substr($m, 4, 2)), 2);
	} else {
		$thisyear = gmdate('Y', current_time('timestamp'));
		$thismonth = gmdate('m', current_time('timestamp'));
	}

	$unixmonth = mktime(0, 0 , 0, $thismonth, 1, $thisyear);
	$last_day = date('t', $unixmonth);

	// Get the next and previous month and year with at least one post
	$previous = $wpdb->get_row("SELECT MONTH(post_date) AS month, YEAR(post_date) AS year
		FROM $wpdb->posts " . futurems_single_category_joins($category) . "
		WHERE post_date < '$thisyear-$thismonth-01'" . futurems_category_sql($category) . "
		AND post_type = 'post' AND (post_status = 'publish' $sql_futurems)
			ORDER BY post_date DESC
			LIMIT 1");
	$next = $wpdb->get_row("SELECT MONTH(post_date) AS month, YEAR(post_date) AS year
		FROM $wpdb->posts " . futurems_single_category_joins($category) . "
		WHERE post_date > '$thisyear-$thismonth-{$last_day} 23:59:59'" . futurems_category_sql($category) . "
		AND post_type = 'post' AND (post_status = 'publish' $sql_futurems)
			ORDER BY post_date ASC
			LIMIT 1");

	/* translators: Calendar caption: 1: month name, 2: 4-digit year */
	$calendar_caption = _x('%1$s %2$s', 'calendar caption');
	$calendar_output = '<table id="wp-calendar">
	<caption>' . sprintf($calendar_caption, $wp_locale->get_month($thismonth), date('Y', $unixmonth)) . '</caption>
	<thead>
	<tr>';

	$myweek = array();

	for ( $wdcount=0; $wdcount<=6; $wdcount++ ) {
		$myweek[] = $wp_locale->get_weekday(($wdcount+$week_begins)%7);
	}

	foreach ( $myweek as $wd ) {
		$day_name = (true == $initial) ? $wp_locale->get_weekday_initial($wd) : $wp_locale->get_weekday_abbrev($wd);
		$wd = esc_attr($wd);
		$calendar_output .= "\n\t\t<th scope=\"col\" title=\"$wd\">$day_name</th>";
	}

	$calendar_output .= '
	</tr>
	</thead>

	<tfoot>
	<tr>';

	if ( $previous ) {
		$calendar_output .= "\n\t\t".'<td colspan="3" id="prev"><a href="' . futurems_link(get_month_link($previous->year, $previous->month),$category,$future) . '" title="' . esc_attr( sprintf(__('View posts for %1$s %2$s'), $wp_locale->get_month($previous->month), date('Y', mktime(0, 0 , 0, $previous->month, 1, $previous->year)))) . '">&laquo; ' . $wp_locale->get_month_abbrev($wp_locale->get_month($previous->month)) . '</a></td>';
	} else {
		$calendar_output .= "\n\t\t".'<td colspan="3" id="prev" class="pad">&nbsp;</td>';
	}

	$calendar_output .= "\n\t\t".'<td class="pad">&nbsp;</td>';

	if ( $next ) {
		$calendar_output .= "\n\t\t".'<td colspan="3" id="next"><a href="' . futurems_link(get_month_link($next->year, $next->month),$category,$future) . '" title="' . esc_attr( sprintf(__('View posts for %1$s %2$s'), $wp_locale->get_month($next->month), date('Y', mktime(0, 0 , 0, $next->month, 1, $next->year))) ) . '">' . $wp_locale->get_month_abbrev($wp_locale->get_month($next->month)) . ' &raquo;</a></td>';
	} else {
		$calendar_output .= "\n\t\t".'<td colspan="3" id="next" class="pad">&nbsp;</td>';
	}

	$calendar_output .= '
	</tr>
	</tfoot>

	<tbody>
	<tr>';

	// Get days with posts
	$dayswithposts = $wpdb->get_results("SELECT DISTINCT DAYOFMONTH(post_date)
		FROM $wpdb->posts" . futurems_single_category_joins($category) . " WHERE post_date >= '{$thisyear}-{$thismonth}-01 00:00:00'" . futurems_category_sql($category) . "
		AND post_type = 'post' AND (post_status = 'publish' $sql_futurems) 
		AND post_date <= '{$thisyear}-{$thismonth}-{$last_day} 23:59:59'", ARRAY_N);
	if ( $dayswithposts ) {
		foreach ( (array) $dayswithposts as $daywith ) {
			$daywithpost[] = $daywith[0];
		}
	} else {
		$daywithpost = array();
	}

	if (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false || stripos($_SERVER['HTTP_USER_AGENT'], 'camino') !== false || stripos($_SERVER['HTTP_USER_AGENT'], 'safari') !== false)
		$ak_title_separator = "\n";
	else
		$ak_title_separator = ', ';

	$ak_titles_for_day = array();
	$ak_post_titles = $wpdb->get_results("SELECT DISTINCT ID, post_title, DAYOFMONTH(post_date) as dom "
		."FROM $wpdb->posts " . futurems_single_category_joins($category)
		."WHERE post_date >= '{$thisyear}-{$thismonth}-01 00:00:00' "
		."AND post_date <= '{$thisyear}-{$thismonth}-{$last_day} 23:59:59' " . futurems_category_sql($category)
		."AND post_type = 'post' AND (post_status = 'publish' $sql_futurems)"
	);
	if ( $ak_post_titles ) {
		foreach ( (array) $ak_post_titles as $ak_post_title ) {

				$post_title = esc_attr( apply_filters( 'the_title', $ak_post_title->post_title, $ak_post_title->ID ) );

				if ( empty($ak_titles_for_day['day_'.$ak_post_title->dom]) )
					$ak_titles_for_day['day_'.$ak_post_title->dom] = '';
				if ( empty($ak_titles_for_day["$ak_post_title->dom"]) ) // first one
					$ak_titles_for_day["$ak_post_title->dom"] = $post_title;
				else
					$ak_titles_for_day["$ak_post_title->dom"] .= $ak_title_separator . $post_title;
		}
	}

	// See how much we should pad in the beginning
	$pad = calendar_week_mod(date('w', $unixmonth)-$week_begins);
	if ( 0 != $pad )
		$calendar_output .= "\n\t\t".'<td colspan="'. esc_attr($pad) .'" class="pad">&nbsp;</td>';

	$daysinmonth = intval(date('t', $unixmonth));
	for ( $day = 1; $day <= $daysinmonth; ++$day ) {
		if ( isset($newrow) && $newrow )
			$calendar_output .= "\n\t</tr>\n\t<tr>\n\t\t";
		$newrow = false;

		if ( $day == gmdate('j', current_time('timestamp')) && $thismonth == gmdate('m', current_time('timestamp')) && $thisyear == gmdate('Y', current_time('timestamp')) )
			$calendar_output .= '<td id="today">';
		else
			$calendar_output .= '<td>';

		if ( in_array($day, $daywithpost) ) // any posts today?
				$calendar_output .= '<a href="' . futurems_link(get_day_link( $thisyear, $thismonth, $day ),$category,$future) . '" title="' . esc_attr( $ak_titles_for_day[ $day ] ) . "\">$day</a>";
		else
			$calendar_output .= $day;
		$calendar_output .= '</td>';

		if ( 6 == calendar_week_mod(date('w', mktime(0, 0 , 0, $thismonth, $day, $thisyear))-$week_begins) )
			$newrow = true;
	}

	$pad = 7 - calendar_week_mod(date('w', mktime(0, 0 , 0, $thismonth, $day, $thisyear))-$week_begins);
	if ( $pad != 0 && $pad != 7 )
		$calendar_output .= "\n\t\t".'<td class="pad" colspan="'. esc_attr($pad) .'">&nbsp;</td>';

	$calendar_output .= "\n\t</tr>\n\t</tbody>\n\t</table>";

	$cache[ $key ] = $calendar_output;
	wp_cache_set( 'futurems_get_calendar', $cache, 'calendar' );

	if ( $echo ) {
		echo apply_filters( 'futurems_get_calendar',  $calendar_output );
    }
	else {
		return apply_filters( 'futurems_get_calendar',  $calendar_output );
    }

}

function futurems_link($link,$category,$future) {
    $arr_params = array();
    if ($category > 0) { $arr_params['cat'] = $category; }
    if ($future == 1) { $arr_params['future'] = "all"; }
    return add_query_arg( $arr_params, $link );
}

function futurems_category_sql($category) {
	global $wpdb;
	if ( $category > 0 ) { return " AND $wpdb->term_taxonomy.taxonomy = 'category' AND $wpdb->term_taxonomy.term_id IN($category) "; }
    else { return " "; }
}

function futurems_single_category_joins($category) {
	global $wpdb;
	if ( $category > 0 ) {
		return "
            wposts LEFT JOIN $wpdb->postmeta wpostmeta ON wposts.ID = wpostmeta.post_id 
			LEFT JOIN $wpdb->term_relationships ON (wposts.ID = $wpdb->term_relationships.object_id)
			LEFT JOIN $wpdb->term_taxonomy ON ($wpdb->term_relationships.term_taxonomy_id = $wpdb->term_taxonomy.term_taxonomy_id)
        ";
	}
	else { return " "; }
}

add_filter('get_calendar', 'futurems_get_calendar');

/* End Calendar Widget */

/* Show Future Post List */

function futurems_show_post_list($query) {
    if ( $query->is_main_query() ) {
        $query->set( 'order', 'ASC' );
        $query->set( 'post_status', 'future' );
    }
}

/* Show All Posts List */

function futurems_show_all_post_list($query) {
    if ( $query->is_main_query() ) {
        $query->set( 'post_status', array('future','publish') );
    }
}

/* Show links to next and previous posts */

function futurems_get_adjacent_post($sql) {
  return preg_replace("/ AND p.post_status = '[^']*'/", "AND ( p.post_status = 'future' )", $sql);
}

function futurems_get_adjacent_post_all($sql) {
  return preg_replace("/ AND p.post_status = '[^']*'/", "AND ( p.post_status = 'publish' OR p.post_status = 'future' )", $sql);
}

/* Permalinks to archives showing future and all posts */

function futurems_permalink($link, $future = null) {
    if(is_null($future)) $future = $_GET['future'];
    if (preg_match('/\?/',$link)) {
        $link = $link . "&future=$future";
    }
    else { $link = $link . "?future=$future"; }
    return $link;
}

if ( in_array(@$_GET['future'], array('true','all') ) ) {
    add_filter('post_link', 'futurems_permalink');
    if ($_GET['future'] == "true") {
        add_action( 'pre_get_posts', 'futurems_show_post_list' );
        add_filter('get_next_post_where', 'futurems_get_adjacent_post');
        add_filter('get_previous_post_where', 'futurems_get_adjacent_post');
    }
    else {
        add_action( 'pre_get_posts', 'futurems_show_all_post_list' );
        add_filter('get_next_post_where', 'futurems_get_adjacent_post_all');
        add_filter('get_previous_post_where', 'futurems_get_adjacent_post_all');
    }
}


function futurems_paginate_links($link, $future = null) {
    if(is_null($future)) $future = $_GET['future'];
	$future_str = "future=" . $future;
	$future_pos = strpos($link,$future_str)-1;
	$future_len = strlen($future_str)+1;
	$link = substr($link,0,$future_pos) . substr($link,$future_pos+$future_len);
	$link = futurems_permalink($link, $future);
	return $link;
}
	

add_filter('paginate_links','futurems_paginate_links');

/* Add Future checkboxes to menus */

add_action('wp_update_nav_menu_item', 'future_nav_update',10, 3);
function future_nav_update($menu_id, $menu_item_db_id, $args ) {
	if ( array_key_exists('menu-item-future',$_REQUEST) ) {
		if ( is_array($_REQUEST['menu-item-future'])  ) {
			if ( array_key_exists($menu_item_db_id,$_REQUEST['menu-item-future']) ) {
				$future_value = $_REQUEST['menu-item-future'][$menu_item_db_id];
				update_post_meta( $menu_item_db_id, '_menu_item_future', $future_value );
			}
		}
	}
}

/*
 * Adds value of new field to $item object that will be passed to Walker_Nav_Menu_Edit_Future
 */
add_filter( 'wp_setup_nav_menu_item','future_nav_item' );
function future_nav_item($menu_item) {
    $menu_item->future = get_post_meta( $menu_item->ID, '_menu_item_future', true );
    return $menu_item;
}

add_filter( 'wp_edit_nav_menu_walker', 'future_nav_edit_walker',10,2 );
function future_nav_edit_walker($walker,$menu_id) {
    return 'Walker_Nav_Menu_Edit_Future';
}

/**
 * Copied from Walker_Nav_Menu_Edit class in core
 * 
 * Create HTML list of nav menu input items.
 *
 * @package WordPress
 * @since 3.0.0
 * @uses Walker_Nav_Menu
 */
class Walker_Nav_Menu_Edit_Future extends Walker_Nav_Menu  {
	/**
	 * Starts the list before the elements are added.
	 *
	 * @see Walker_Nav_Menu::start_lvl()
	 *
	 * @since 3.0.0
	 *
	 * @param string $output Passed by reference.
	 * @param int    $depth  Depth of menu item. Used for padding.
	 * @param array  $args   Not used.
	 */
	public function start_lvl( &$output, $depth = 0, $args = array() ) {}

	/**
	 * Ends the list of after the elements are added.
	 *
	 * @see Walker_Nav_Menu::end_lvl()
	 *
	 * @since 3.0.0
	 *
	 * @param string $output Passed by reference.
	 * @param int    $depth  Depth of menu item. Used for padding.
	 * @param array  $args   Not used.
	 */
	public function end_lvl( &$output, $depth = 0, $args = array() ) {}

	/**
	 * Start the element output.
	 *
	 * @see Walker_Nav_Menu::start_el()
	 * @since 3.0.0
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param object $item   Menu item data object.
	 * @param int    $depth  Depth of menu item. Used for padding.
	 * @param array  $args   Not used.
	 * @param int    $id     Not used.
	 */
	public function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
		global $_wp_nav_menu_max_depth;
		$_wp_nav_menu_max_depth = $depth > $_wp_nav_menu_max_depth ? $depth : $_wp_nav_menu_max_depth;

		ob_start();
		$item_id = esc_attr( $item->ID );
		$removed_args = array(
			'action',
			'customlink-tab',
			'edit-menu-item',
			'menu-item',
			'page-tab',
			'_wpnonce',
		);

		$original_title = '';
		if ( 'taxonomy' == $item->type ) {
			$original_title = get_term_field( 'name', $item->object_id, $item->object, 'raw' );
			if ( is_wp_error( $original_title ) )
				$original_title = false;
		} elseif ( 'post_type' == $item->type ) {
			$original_object = get_post( $item->object_id );
			$original_title = get_the_title( $original_object->ID );
		}

		$classes = array(
			'menu-item menu-item-depth-' . $depth,
			'menu-item-' . esc_attr( $item->object ),
			'menu-item-edit-' . ( ( isset( $_GET['edit-menu-item'] ) && $item_id == $_GET['edit-menu-item'] ) ? 'active' : 'inactive'),
		);

		$title = $item->title;

		if ( ! empty( $item->_invalid ) ) {
			$classes[] = 'menu-item-invalid';
			/* translators: %s: title of menu item which is invalid */
			$title = sprintf( __( '%s (Invalid)' ), $item->title );
		} elseif ( isset( $item->post_status ) && 'draft' == $item->post_status ) {
			$classes[] = 'pending';
			/* translators: %s: title of menu item in draft status */
			$title = sprintf( __('%s (Pending)'), $item->title );
		}

		$title = ( ! isset( $item->label ) || '' == $item->label ) ? $title : $item->label;

		$submenu_text = '';
		if ( 0 == $depth )
			$submenu_text = 'style="display: none;"';

		?>
		<li id="menu-item-<?php echo $item_id; ?>" class="<?php echo implode(' ', $classes ); ?>">
			<dl class="menu-item-bar">
				<dt class="menu-item-handle">
					<span class="item-title"><span class="menu-item-title"><?php echo esc_html( $title ); ?></span> <span class="is-submenu" <?php echo $submenu_text; ?>><?php _e( 'sub item' ); ?></span></span>
					<span class="item-controls">
						<span class="item-type"><?php echo esc_html( $item->type_label ); ?></span>
						<span class="item-order hide-if-js">
							<a href="<?php
								echo wp_nonce_url(
									add_query_arg(
										array(
											'action' => 'move-up-menu-item',
											'menu-item' => $item_id,
										),
										remove_query_arg($removed_args, admin_url( 'nav-menus.php' ) )
									),
									'move-menu_item'
								);
							?>" class="item-move-up"><abbr title="<?php esc_attr_e('Move up'); ?>">&#8593;</abbr></a>
							|
							<a href="<?php
								echo wp_nonce_url(
									add_query_arg(
										array(
											'action' => 'move-down-menu-item',
											'menu-item' => $item_id,
										),
										remove_query_arg($removed_args, admin_url( 'nav-menus.php' ) )
									),
									'move-menu_item'
								);
							?>" class="item-move-down"><abbr title="<?php esc_attr_e('Move down'); ?>">&#8595;</abbr></a>
						</span>
						<a class="item-edit" id="edit-<?php echo $item_id; ?>" title="<?php esc_attr_e('Edit Menu Item'); ?>" href="<?php
							echo ( isset( $_GET['edit-menu-item'] ) && $item_id == $_GET['edit-menu-item'] ) ? admin_url( 'nav-menus.php' ) : add_query_arg( 'edit-menu-item', $item_id, remove_query_arg( $removed_args, admin_url( 'nav-menus.php#menu-item-settings-' . $item_id ) ) );
						?>"><?php _e( 'Edit Menu Item' ); ?></a>
					</span>
				</dt>
			</dl>

			<div class="menu-item-settings" id="menu-item-settings-<?php echo $item_id; ?>">
				<?php if( 'custom' == $item->type ) : ?>
					<p class="field-url description description-wide">
						<label for="edit-menu-item-url-<?php echo $item_id; ?>">
							<?php _e( 'URL' ); ?><br />
							<input type="text" id="edit-menu-item-url-<?php echo $item_id; ?>" class="widefat code edit-menu-item-url" name="menu-item-url[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->url ); ?>" />
						</label>
					</p>
				<?php endif; ?>
				<p class="description description-thin">
					<label for="edit-menu-item-title-<?php echo $item_id; ?>">
						<?php _e( 'Navigation Label' ); ?><br />
						<input type="text" id="edit-menu-item-title-<?php echo $item_id; ?>" class="widefat edit-menu-item-title" name="menu-item-title[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->title ); ?>" />
					</label>
				</p>
				<p class="description description-thin">
					<label for="edit-menu-item-attr-title-<?php echo $item_id; ?>">
						<?php _e( 'Title Attribute' ); ?><br />
						<input type="text" id="edit-menu-item-attr-title-<?php echo $item_id; ?>" class="widefat edit-menu-item-attr-title" name="menu-item-attr-title[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->post_excerpt ); ?>" />
					</label>
				</p>
				<p class="field-link-target description">
					<label for="edit-menu-item-target-<?php echo $item_id; ?>">
						<input type="checkbox" id="edit-menu-item-target-<?php echo $item_id; ?>" value="_blank" name="menu-item-target[<?php echo $item_id; ?>]"<?php checked( $item->target, '_blank' ); ?> />
						<?php _e( 'Open link in a new window/tab' ); ?>
					</label>
				</p>
				<p class="field-css-classes description description-thin">
					<label for="edit-menu-item-classes-<?php echo $item_id; ?>">
						<?php _e( 'CSS Classes (optional)' ); ?><br />
						<input type="text" id="edit-menu-item-classes-<?php echo $item_id; ?>" class="widefat code edit-menu-item-classes" name="menu-item-classes[<?php echo $item_id; ?>]" value="<?php echo esc_attr( implode(' ', $item->classes ) ); ?>" />
					</label>
				</p>
				<p class="field-xfn description description-thin">
					<label for="edit-menu-item-xfn-<?php echo $item_id; ?>">
						<?php _e( 'Link Relationship (XFN)' ); ?><br />
						<input type="text" id="edit-menu-item-xfn-<?php echo $item_id; ?>" class="widefat code edit-menu-item-xfn" name="menu-item-xfn[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->xfn ); ?>" />
					</label>
				</p>
				<p class="field-description description description-wide">
					<label for="edit-menu-item-description-<?php echo $item_id; ?>">
						<?php _e( 'Description' ); ?><br />
						<textarea id="edit-menu-item-description-<?php echo $item_id; ?>" class="widefat edit-menu-item-description" rows="3" cols="20" name="menu-item-description[<?php echo $item_id; ?>]"><?php echo esc_html( $item->description ); // textarea_escaped ?></textarea>
						<span class="description"><?php _e('The description will be displayed in the menu if the current theme supports it.'); ?></span>
					</label>
				</p>
                
                <?php
                /*
                * This is the added field
                */
                ?>    
                <?php if( $item->type == 'taxonomy' ) : ?>  
                    <p class="field-custom description description-wide">
                        <label for="edit-menu-item-future-<?php echo $item_id; ?>">
                            <?php _e( 'Show Posts' ); ?><br />
                            <input type="radio"
                                id="edit-menu-item-future-false-<?php echo $item_id; ?>"
                                class="code edit-menu-item-future"
                                name="menu-item-future[<?php echo $item_id; ?>]"
                                value="false"
                                <?php if ( in_array( esc_attr($item->future), array("false","") ) ) echo 'checked="checked"'; ?>
                            /> Display only past posts<br />
                            <input type="radio"
                                id="edit-menu-item-future-true-<?php echo $item_id; ?>"
                                class="code edit-menu-item-future"
                                name="menu-item-future[<?php echo $item_id; ?>]"
                                value="true"
                                <?php if ( in_array( esc_attr($item->future), array("true") ) ) echo 'checked="checked"'; ?>
                            /> Display only future posts<br />
                            <input type="radio"
                                id="edit-menu-item-future-all-<?php echo $item_id; ?>"
                                class="code edit-menu-item-future" name="menu-item-future[<?php echo $item_id; ?>]"
                                value="all"
                                <?php if ( in_array( esc_attr($item->future), array("all") ) ) echo 'checked="checked"'; ?>
                            /> Display both past and future posts<br />
                        </label>
                    </p>
                <?php endif; ?>
                <?php
                /*
                * End added field
                */
                ?>
								<p class="field-move hide-if-no-js description description-wide">
					<label>
						<span><?php _e( 'Move' ); ?></span>
						<a href="#" class="menus-move-up"><?php _e( 'Up one' ); ?></a>
						<a href="#" class="menus-move-down"><?php _e( 'Down one' ); ?></a>
						<a href="#" class="menus-move-left"></a>
						<a href="#" class="menus-move-right"></a>
						<a href="#" class="menus-move-top"><?php _e( 'To the top' ); ?></a>
					</label>
				</p>

				<div class="menu-item-actions description-wide submitbox">
					<?php if( 'custom' != $item->type && $original_title !== false ) : ?>
						<p class="link-to-original">
							<?php printf( __('Original: %s'), '<a href="' . esc_attr( $item->url ) . '">' . esc_html( $original_title ) . '</a>' ); ?>
						</p>
					<?php endif; ?>
					<a class="item-delete submitdelete deletion" id="delete-<?php echo $item_id; ?>" href="<?php
					echo wp_nonce_url(
						add_query_arg(
							array(
								'action' => 'delete-menu-item',
								'menu-item' => $item_id,
							),
							admin_url( 'nav-menus.php' )
						),
						'delete-menu_item_' . $item_id
					); ?>"><?php _e( 'Remove' ); ?></a> <span class="meta-sep hide-if-no-js"> | </span> <a class="item-cancel submitcancel hide-if-no-js" id="cancel-<?php echo $item_id; ?>" href="<?php echo esc_url( add_query_arg( array( 'edit-menu-item' => $item_id, 'cancel' => time() ), admin_url( 'nav-menus.php' ) ) );
						?>#menu-item-settings-<?php echo $item_id; ?>"><?php _e('Cancel'); ?></a>
				</div>

				<input class="menu-item-data-db-id" type="hidden" name="menu-item-db-id[<?php echo $item_id; ?>]" value="<?php echo $item_id; ?>" />
				<input class="menu-item-data-object-id" type="hidden" name="menu-item-object-id[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->object_id ); ?>" />
				<input class="menu-item-data-object" type="hidden" name="menu-item-object[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->object ); ?>" />
				<input class="menu-item-data-parent-id" type="hidden" name="menu-item-parent-id[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->menu_item_parent ); ?>" />
				<input class="menu-item-data-position" type="hidden" name="menu-item-position[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->menu_order ); ?>" />
				<input class="menu-item-data-type" type="hidden" name="menu-item-type[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->type ); ?>" />
			</div><!-- .menu-item-settings-->
			<ul class="menu-item-transport"></ul>
		<?php
		$output .= ob_get_clean();
	}
}
 


add_filter( 'nav_menu_link_attributes', 'future_nav_menu_links',10,3);
function future_nav_menu_links( $atts, $item, $args ) {
    if ($item->type == 'taxonomy') { $atts['href'] = futurems_permalink($atts['href'], $item->future); }
    return $atts;
}


?>