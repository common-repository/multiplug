<?php
/*
Plugin Name: MultiPlug
Plugin URI: http://meandmymac.net/plugins/
Description: Common top-level menu for plugins created at Me And My Mac. WordPress 2.6 and up.
Author: Arnan de Gans
Version: 0.2
Author URI: http://meandmymac.net/
*/

add_action('admin_menu', 'multiplug_menu');

/*-------------------------------------------------------------
 Name:      multiplug_menu

 Purpose:   Create a Toplevel menu
 Receive:   -none-
 Return:    -none-
-------------------------------------------------------------*/
function multiplug_menu(){
   	add_menu_page('multiplug', 'MultiPlug', 'read', 'multiplug', 'multiplug_info');
}

/*-------------------------------------------------------------
 Name:      multiplug_info

 Purpose:   A general information sheet for MultiPlug with usefull information
 Receive:   -none-
 Return:    -none-
-------------------------------------------------------------*/
function multiplug_info() {
	
	?>
	<div class="wrap">
	
		<h2>MultiPlug</h2>
		
		<table class="widefat" style="margin-top: .5em">
		<thead>
			<tr>
				<th scope="col">About</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>MultiPlug is an umbrella plugin which creates and handles the adding of menus for several plugins. Currently the menus for various plugins made by Arnan de Gans from Meandmymac.net are covered but others may hook into MultiPlug as well.</td>
			</tr>
		</tbody>
		</table>
	
		<br class="clear" />
	
		<table class="widefat" style="margin-top: .5em">
		<thead>
			<tr>
				<th scope="col">10 most recent threads on the meandmymac.net support forum (<a href="http://forum.at.meandmymac.net" target="_blank" title="Me And My Mac Forum">Go there</a>)</th>
			</tr>
		</thead>
		<tbody>
	
			<?php multiplug_rss('http://forum.at.meandmymac.net/rss/topics'); ?>
		
		</tbody>
		</table>
	
		<br class="clear" />
	
		<table class="widefat" style="margin-top: .5em">
		<thead>
			<tr>
				<th scope="col">Hook your plugin to MultiPlug</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td><p>It's rather easy. Your plugin only has to add it's dashboard pages to the multiplug menu.<br />
				It works the same as with any submenu page on the dashboard. Instead attaching it to edit.php or post-new.php you attach it to ‘multiplug’.</p>
				
				<p><strong>Example (I use this for the Events plugin):</strong><br />
				<pre>add_submenu_page('multiplug', 'Events > Add new', 'Add Event', $events_config['minlevel'], 'wp-events', 'events_schedule');</pre></p>
				
				<p><strong>Lets break that down:</strong><br />
				- First variable is assigned to the multiplug menu. <br />
				- The 2nd is the menubar title (in the browser) and 3rd the navigation title (in WP). <br />
				- 4th is the user level. <br />
				- 5th is your plugin file without extention.<br />
				- 6th is the function inside that file to call.</p>
				
				<p>For more information on sub menus go to the codex: <a href="http://codex.wordpress.org/Adding_Administration_Menus#Sub-Menus" target="_blank">Adding Administration Menus</a>.</p>
				
				<p>To have MultiPlug activate or even install when a plugin in need is activated you can include a little function in your plugins register_activation_hook() that checks if MultiPlug is present and active. If not install it or prompt that it's missing.</p>
				</td>
			</tr>
		</tbody>
		</table>

	</div>
	<?php
}

/*-------------------------------------------------------------
 Name:      multiplug_rss

 Purpose:   A very simple RSS parser for the Meandmymac.net forum
 Receive:   $rss
 Return:    -none-
-------------------------------------------------------------*/
function multiplug_rss( $rss ) {
	if ( is_string( $rss ) ) {
		require_once(ABSPATH . WPINC . '/rss.php');
		if ( !$rss = fetch_rss($rss) )
			return;
	}

	if ( is_array( $rss->items ) && !empty( $rss->items ) ) {
		$rss->items = array_slice($rss->items, 0, 10);
		foreach ( (array) $rss->items as $item ) {
			while ( strstr($item['link'], 'http') != $item['link'] )
				$item['link'] = substr($item['link'], 1);
			$link = clean_url(strip_tags($item['link']));
			
			$title = attribute_escape(strip_tags($item['title']));
			if ( empty($title) )
				$title = __('Untitled');
				
			if (isset($item['pubdate']))
				$date = $item['pubdate'];
			elseif (isset($item['published']))
				$date = $item['published'];

			if ($date) {
				if ($date_stamp = strtotime($date))
					$date = date_i18n( get_option('date_format'), $date_stamp);
				else
					$date = '';
			}				
				
			echo '<tr><td>';
			if ( $link == '' ) {
				echo "$title"; 
			} else {
				echo '<a href="'.$link.'" title="'.$desc.'">'.$title.'</a> <span style="float: right;">'.$date.'</span>'; 
			}
			echo '</td></tr>';
		}
	} else {
		echo '<tr><td>' . __( 'An error has occurred; the feed is probably down. Try again later.' ) . '</td></tr>';
	}
}
?>