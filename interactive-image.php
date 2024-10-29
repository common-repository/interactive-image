<?php



/*



Plugin Name: Interactive Image



Plugin URI: 



Version: 1.14



Description: Create interactives images with hotspots and zones



Author: Manu225



Author URI: https://www.info-d-74.com/en/shop/



Network: false



Text Domain: interactive-image



Domain Path:



*/







register_activation_hook( __FILE__, 'interactive_image_install' );



register_uninstall_hook(__FILE__, 'interactive_image_desinstall');







function interactive_image_install() {







	global $wpdb;







	$images_table = $wpdb->prefix . "interactive_image";



	$images_data_table = $wpdb->prefix . "interactive_image_data";







	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');







	$sql = "



        CREATE TABLE `".$images_table."` (



          id int(11) NOT NULL AUTO_INCREMENT,          



          name varchar(50) NOT NULL,



          url varchar(200) NOT NULL,



          zone_color varchar(30) NOT NULL,



          PRIMARY KEY  (id)



        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;



    ";



    dbDelta($sql);







    $sql = "



        CREATE TABLE `".$images_data_table."` (



          id int(11) NOT NULL AUTO_INCREMENT,          



          title varchar(200) NOT NULL,



          position varchar(20) NOT NULL,



          description text,



          points text,          



          id_image int(11),



          PRIMARY KEY (id)



        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;



    ";



    dbDelta($sql);











}







function interactive_image_desinstall() {







	global $wpdb;







	$images_table = $wpdb->prefix . "interactive_image";



	$images_data_table = $wpdb->prefix . "interactive_image_data";







	//suppression des tables



	$sql = "DROP TABLE ".$images_table.";";



	$wpdb->query($sql);



    $sql = "DROP TABLE ".$images_data_table.";"; 



	$wpdb->query($sql);







}







add_action( 'admin_menu', 'register_image_interactive_menu' );



function register_image_interactive_menu() {







	add_menu_page('Interactive Images', 'Interactive Images', 'edit_pages', 'interactive_images', 'interactive_images', plugins_url( 'images/icon.png', __FILE__ ), 20);







}







add_action('admin_print_styles', 'admin_image_interactive_css' );



function admin_image_interactive_css() {



    wp_enqueue_style( 'interactive_image_css', plugins_url('css/admin.css', __FILE__) );



    wp_enqueue_style( 'jquery-ui-theme',  plugins_url('css/jquery-ui.css', __FILE__) );



}







add_action( 'admin_enqueue_scripts', 'admin_image_interactive_script' );



function admin_image_interactive_script() {



    wp_enqueue_media();



    wp_enqueue_script( 'jquery-ui-core');



    wp_enqueue_script( 'jquery-ui-draggable');



    wp_enqueue_script( 'jquery-ui-droppable' );



    wp_enqueue_script( 'jquery-ui-accordion');



    wp_enqueue_script( 'jquery-ui-tooltip');



    wp_register_script( 'jquery.canvasAreaDraw', plugins_url( 'js/jquery.canvasAreaDraw.min.js', __FILE__ ) );



	wp_enqueue_script( 'jquery.canvasAreaDraw');



}







function interactive_images() {







	global $wpdb;







	$images_table = $wpdb->prefix . "interactive_image";



	$images_data_table = $wpdb->prefix . "interactive_image_data";







	if(current_user_can('edit_pages'))



	{



		if(isset($_GET['task']))



		{



			switch($_GET['task'])



			{



				case 'add':







					if(sizeof($_POST))



					{







						$query = "INSERT INTO ".$images_table." (`id`, `name`, `url`, `zone_color`)



						VALUES (%d, %s, %s, %s)";



						$query = $wpdb->prepare( $query, '', sanitize_text_field(stripslashes_deep($_POST['name'])), sanitize_text_field(stripslashes_deep($_POST['url'])), esc_html($_POST['zone_color']) );



						$wpdb->query( $query );







						$_GET['id'] = $wpdb->insert_id;







						//on affiche le créateur d'image interactive



						$interactive_image = $wpdb->get_row("SELECT * FROM ".$images_table." ORDER BY id DESC LIMIT 1");



						$selected_data = new stdClass();



						include(plugin_dir_path( __FILE__ ) . 'views/interactive_image_creator.php');







					}



					else



					{



						$query = "SELECT * FROM ".$images_table." ORDER BY name ASC";



						$interactive_images = $wpdb->get_results($query);







						include(plugin_dir_path( __FILE__ ) . 'views/interactive_images_list.php');



					}







				break;







				case 'edit':







					if(sizeof($_POST) && is_numeric($_POST['id']))



					{







						$query = "REPLACE INTO ".$images_table." (`id`, `name`, `url`, `zone_color`)



						VALUES (%d, %s, %s, %s)";



						$query = $wpdb->prepare( $query, $_POST['id'], sanitize_text_field(stripslashes_deep($_POST['name'])), sanitize_text_field(stripslashes_deep($_POST['url'])), esc_html($_POST['zone_color']) );



						$wpdb->query( $query );







						//on affiche le créateur d'image interactive



						$query = $wpdb->prepare("SELECT * FROM ".$images_table." WHERE id = %d", (int)$_POST['id']);



						$interactive_image = $wpdb->get_row( $query );



						$q = "SELECT * FROM ".$images_data_table." WHERE id_image = %d";



						$query = $wpdb->prepare( $q, (int)$_POST['id']);



						$datas = $wpdb->get_results( $query );



						$selected_data = new stdClass();



						include(plugin_dir_path( __FILE__ ) . 'views/interactive_image_creator.php');







					}



					else



					{



						$query = "SELECT * FROM ".$images_table." ORDER BY name ASC";



						$interactive_images = $wpdb->get_results($query);







						include(plugin_dir_path( __FILE__ ) . 'views/interactive_images_list.php');



					}







				break;







				case 'manage':







					if(is_numeric($_GET['id']))



					{



						$q = "SELECT * FROM ".$images_table." WHERE id = %d";



						$query = $wpdb->prepare( $q, (int)$_GET['id']);



						$interactive_image = $wpdb->get_row( $query );



						if($interactive_image)

						{

							$q = "SELECT * FROM ".$images_data_table." WHERE id_image = %d ORDER BY points DESC";

							$query = $wpdb->prepare( $q, (int)$_GET['id']);



							$datas = $wpdb->get_results( $query );



							//zone/marker en édition ?

							if(is_numeric($_GET['id_data']))	

							{

								$q = "SELECT * FROM ".$images_data_table." WHERE id = %d";

								$query = $wpdb->prepare( $q, (int)$_GET['id_data']);

								$selected_data = $wpdb->get_row( $query );

							}	

							else

								$selected_data = new stdClass();



							//on affiche le créateur d'image interactive							

							include(plugin_dir_path( __FILE__ ) . 'views/interactive_image_creator.php');



						}	



					}



				break;



				case 'remove':



					if(is_numeric($_GET['id']))

					{

						//on supprime les données et le graph

						$q = "DELETE FROM ".$images_data_table." WHERE id_content = %d";

						$query = $wpdb->prepare( $q, (int)$_GET['id']);

						$wpdb->query( $query );



						$q = "DELETE FROM ".$images_table." WHERE id = %d";

						$query = $wpdb->prepare( $q, (int)$_GET['id']);

						$wpdb->query( $query );



					}



					$query = "SELECT * FROM ".$images_table." ORDER BY name ASC";

					$interactive_images = $wpdb->get_results($query);



					include(plugin_dir_path( __FILE__ ) . 'views/interactive_images_list.php');



				break;



				case 'remove_data':



					if(is_numeric($_GET['id_data']))

					{

						//on supprime la zone

						$q = "DELETE FROM ".$images_data_table." WHERE id = %d";

						$query = $wpdb->prepare( $q, (int)$_GET['id_data']);

						$wpdb->query( $query );



						//wp_redirect( 'admin.php?page=interactive_images&task=manage&id='.$_GET['id'] );

						//exit;



						$q = "SELECT * FROM ".$images_table." WHERE id = %d";

						$query = $wpdb->prepare( $q, (int)$_GET['id']);

						$interactive_image = $wpdb->get_row( $query );



						if($interactive_image)

						{

							$q = "SELECT * FROM ".$images_data_table." WHERE id_image = %d";

							$query = $wpdb->prepare( $q, (int)$_GET['id']);

							$datas = $wpdb->get_results( $query );				

							$selected_data = new stdClass();



							//on affiche le créateur d'image interactive							

							include(plugin_dir_path( __FILE__ ) . 'views/interactive_image_creator.php');

						}		



					}



				break;



			}



		}

		else

		{

			$query = "SELECT * FROM ".$images_table." ORDER BY name ASC";

			$interactive_images = $wpdb->get_results($query);



			include(plugin_dir_path( __FILE__ ) . 'views/interactive_images_list.php');

		}

	}

}





//Ajax save zone

add_action( 'wp_ajax_save_zone_ii', 'save_zone_interactive_image' );



function save_zone_interactive_image() {



	check_ajax_referer( 'save_zone_ii' );



	if(current_user_can('edit_pages'))

	{

		if(is_numeric($_POST['id_image']))

		{

			global $wpdb;



			$images_data_table = $wpdb->prefix . "interactive_image_data";



			if(!is_numeric($_POST['id_data']))

			{

				$query = "INSERT INTO ".$images_data_table." (title, description, points, id_image) VALUES (%s, %s, %s, %d)";

				$query = $wpdb->prepare( $query, sanitize_text_field(stripslashes_deep($_POST['title'])), wp_kses_post(stripslashes_deep($_POST['description'])), sanitize_text_field($_POST['points']), (int)$_POST['id_image'] );

				$wpdb->query( $query );

				wp_die((int)$wpdb->insert_id);

			}

			else

			{

				$query = "REPLACE INTO ".$images_data_table." (id, position, title, description, points, id_image) VALUES (%d, '', %s, %s, %s, %d)";

				$query = $wpdb->prepare( $query, (int)$_POST['id_data'], sanitize_text_field(stripslashes_deep($_POST['title'])), wp_kses_post(stripslashes_deep($_POST['description'])), sanitize_text_field($_POST['points']), (int)$_POST['id_image'] );

				$wpdb->query( $query );

				wp_die(0);

			}

		}

	}



	wp_die();



}





//Ajax save hotspot

add_action( 'wp_ajax_save_hotspot_ii', 'save_hotspot_interactive_image' );



function save_hotspot_interactive_image() {



	check_ajax_referer( 'save_hotspot_ii' );



	if(current_user_can('edit_pages'))

	{

		if(is_numeric($_POST['id_image']))

		{

			global $wpdb;



			$images_data_table = $wpdb->prefix . "interactive_image_data";



			$query = "INSERT INTO ".$images_data_table." (title, description, position, id_image) VALUES (%s, %s, %s, %d)";

			$query = $wpdb->prepare( $query, sanitize_text_field(stripslashes_deep($_POST['title'])), wp_kses_post(stripslashes_deep($_POST['description'])), (float)$_POST['x'].','.(float)$_POST['y'], (int)$_POST['id_image'] );

			$wpdb->query( $query );

			wp_die((int)$wpdb->insert_id);

		}

	}



	wp_die();

}



//Ajax save hotspot

add_action( 'wp_ajax_save_hotspot_position_ii', 'save_hotspot_position_interactive_image' );



function save_hotspot_position_interactive_image() {



	check_ajax_referer( 'save_hotspot_position_ii' );



	if(current_user_can('edit_pages') && is_numeric($_POST['id_hotspot']))

	{

		global $wpdb;



		$images_data_table = $wpdb->prefix . "interactive_image_data";

		$query = "UPDATE ".$images_data_table." SET position=%s WHERE id=%d";

		$query = $wpdb->prepare( $query, (float)$_POST['x'].','.(float)$_POST['y'], (int)$_POST['id_hotspot']);

		$wpdb->query( $query );

		wp_die(0);

	}



	wp_die();



}



//Ajax save hotspot

add_action( 'wp_ajax_update_hotspot_info_ii', 'update_hotspot_interactive_image' );



function update_hotspot_interactive_image() {



	check_ajax_referer( 'update_hotspot_ii' );

	

	if(current_user_can('edit_pages') && is_numeric($_POST['id_hotspot']))

	{	

		global $wpdb;



		$images_data_table = $wpdb->prefix . "interactive_image_data";

		$query = "UPDATE ".$images_data_table." SET title=%s, description=%s WHERE id=%d";

		$query = $wpdb->prepare( $query, sanitize_text_field(stripslashes_deep($_POST['title'])), wp_kses_post(stripslashes_deep($_POST['description'])), (int)$_POST['id_hotspot']);



		$wpdb->query( $query );

		wp_die(0);

	}



	wp_die();

}



add_shortcode('interactive-image', 'display_interactive_image');



function display_interactive_image($atts) {



	if(is_numeric($atts['id']))

	{

		global $wpdb;

		

		$images_table = $wpdb->prefix . "interactive_image";

		$images_data_table = $wpdb->prefix . "interactive_image_data";



		$q = "SELECT * FROM ".$images_table." WHERE id = %d";

		$query = $wpdb->prepare( $q, (int)$atts['id']);



		$interactive_image = $wpdb->get_row( $query );

		if($interactive_image)

		{

			$q = "SELECT * FROM ".$images_data_table." WHERE id_image = %d";

			$query = $wpdb->prepare( $q, (int)$atts['id']);

			$datas = $wpdb->get_results( $query );



			//on inclut jquery

			wp_enqueue_script( 'jquery' );

			wp_enqueue_script( 'jquery-ui-core');

		    wp_enqueue_script( 'jquery-ui-tooltip');

			wp_enqueue_script( 'interactive-image-front-js', plugins_url( 'js/front.js', __FILE__ ));

			wp_enqueue_style( 'interactive-image-front-css', plugins_url('css/front.css', __FILE__) );

			wp_enqueue_style( 'jquery-ui-theme',  plugins_url('css/jquery-ui.css', __FILE__) );



			ob_start();

			//on affiche le créateur d'image interactive							

			include(plugin_dir_path( __FILE__ ) . 'views/shortcode.tpl.php');

			$html = ob_get_clean();



			return $html;

		}

		else

			echo 'Interactive image with ID '.(int)$atts['id'].' not found!';

	}



}