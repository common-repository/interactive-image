<script src="<?php echo esc_url(plugins_url( 'js/creator.js', dirname(__FILE__))) ?>"></script>

<script>

	function save_zone_ii(description)
	{

		var points = jQuery('.canvas-area').val().split(',');

		if(points.length >= 6) //pas de zone défini
		{

			jQuery('#ii_zone .loading').show();

			jQuery.post(ajaxurl, {action: 'save_zone_ii', id_data: jQuery('#ii_creator_settings input[name=id_data]').val(), id_image: jQuery('#ii_creator_settings input[name=id]').val(), title: jQuery('#ii_creator_settings input[name=title]').val(), description: description, points: jQuery('#ii_creator .canvas-area').val(), _ajax_nonce: '<?php echo esc_attr(wp_create_nonce( "save_zone_ii" )); ?>' }, function(id){

				console.log(id);				

			    if(window.location.href.indexOf('task=manage') != -1)

			    	window.location.reload();

			    else

			    	window.location.href=window.location.href.replace('task=add', 'task=manage&id='+jQuery('#ii_creator_settings input[name=id]').val());

			});

		}
		else

			alert('Your zone need minimum 3 points to be saved! Please draw it on your image below.');

	}

	function save_hotspot_ii(description, x, y, hotspot)
	{

		jQuery('#ii_hotspot .loading').show();

		jQuery.post(ajaxurl, {action: 'save_hotspot_ii', id_hotspot: jQuery('#ii_creator_settings input[name=id_hotspot]').val(), id_image: jQuery('#ii_creator_settings input[name=id]').val(), title: jQuery('#ii_creator_settings input[name=title]').val(), description: description, x: x, y: y, _ajax_nonce: '<?php echo esc_attr(wp_create_nonce( "save_hotspot_ii" )); ?>' }, function(id){

			console.log(id);

			//reacharge la page si besoin (hospot existant)

			if(id == 0)

		    	window.location.reload();

		    else
			{
			    //jQuery('#ii_hotspot .loading').hide();

			    jQuery(hotspot).attr('rel', id);

			    var infos = jQuery(hotspot).attr('title');

			    infos += '<?php echo '<p><a href="'.esc_url(admin_url('admin.php?page=interactive_images&task=manage&id='.$interactive_image->id.'&id_data=')).'\'+id+\'" title="Edit zone"><img src="'.esc_url(plugins_url( 'images/edit.png', dirname(__FILE__))).'" /></a> <a href="'.esc_url(admin_url('admin.php?page=interactive_images&task=remove_data&id='.$interactive_image->id.'&id_data=')).'\'+id+\'" title="Remove zone"><img src="'.esc_url(plugins_url( 'images/remove.png', dirname(__FILE__))).'" /></a></p>' ?>';

			    console.log(infos);

			    jQuery(hotspot).attr('title', infos);

			}

		});

	}

	function save_hotspot_position_ii(id, x, y)
	{

		jQuery.post(ajaxurl, {action: 'save_hotspot_position_ii', id_hotspot: id, x: x, y: y, _ajax_nonce: '<?php echo esc_attr(wp_create_nonce( "save_hotspot_position_ii" )); ?>' }, function(id){

			console.log(id);			

		});

	}

	function update_hotspot_info_ii(id, title, description)
	{

		jQuery('#ii_hotspot .loading').show();

		jQuery.post(ajaxurl, {action: 'update_hotspot_info_ii', id_hotspot: id, title: title, description: description, _ajax_nonce: '<?php echo esc_attr(wp_create_nonce( "update_hotspot_ii" )); ?>' }, function(id){

			window.location.reload();

		});

	}

</script>

<div id="ii_creator_settings">

	<h2>Interactive image settings</h2>

	<form action="<?php echo esc_url(admin_url('admin.php?page=interactive_images&task=edit')) ?>" method="post" class="form_ii">

		<input type="hidden" name="id" value="<?php echo (int)$interactive_image->id ?>" />

		<label>Name: </label><input type="text" name="name" value="<?php esc_attr_e($interactive_image->name) ?>" required /><br />

		<label>Zones color: </label><input type="color" name="zone_color" value="<?php esc_attr_e($interactive_image->zone_color) ?>" /><br />

		<label>Picture: </label><input type="text" name="url" value="<?php esc_attr_e($interactive_image->url) ?>" required /><button class="choose_img button-secondary" rel="url">Browser library</button><br />

		<input type="submit" value="Save settings" class="button-primary" />

		<br />

		<strong>Shortcode: <input type="text" readonly value="[interactive-image id=<?php echo (int)$interactive_image->id ?>]" onClick="this.select()" /></strong>

	</form>

	<h2>Managing interactive areas</h2>

	<input type="hidden" name="id" value="<?php esc_attr_e($_REQUEST['id']) ?>" />

	<input type="hidden" name="id_data" value="<?php if($_GET['task'] != 'remove_data') @esc_attr_e($_GET['id_data']) ?>" />

	<?php if(@empty($_GET['id_data']) || $_GET['task'] == 'remove_data') : ?>

	<input type="radio" name="type_data" value="hotspot" id="hotspot" <?php echo (@empty($selected_data->points) ? 'checked' : '') ?> /> <label for="hotspot">Hotspot</label> <input type="radio" name="type_data" value="zone" id="zone" <?php echo (!@empty($selected_data->points) ? 'checked' : '') ?> /> <label for="zone">Zone</label><br />

	<?php endif; ?>

	<input type="text" name="title" placeholder="Title" value="<?php echo (property_exists($selected_data, 'title') ? esc_html($selected_data->title) : '') ?>" />

	<?php wp_editor( property_exists($selected_data, 'description') ? $selected_data->description : '', 'description', array('editor_height' => 100, 'textarea_rows' => 20) ); ?>

	<div id="ii_hotspot" <?php echo (property_exists($selected_data, 'points') && $selected_data->points == '' ? 'style="display:block; "' : '') ?>>

		<?php if(!@empty($selected_data->position) && @is_numeric($selected_data->id)) : ?>

		<img src="<?php echo esc_url(plugins_url( 'images/loading.gif', dirname(__FILE__))) ?>" alt="Saving..." class="loading" />

		<button id="ii_add_hotspot" class="button-primary">Save hotspot</button>

		<a class="button button-secondary" href="<?php echo esc_url(admin_url('admin.php?page=interactive_images&task=manage&id='.$interactive_image->id)) ?>">Add a new hotspot or zone</a>

		<?php else : ?>

		<button id="ii_add_hotspot" class="button-primary">Add hotspot</button>

		<?php endif; ?>	

	</div>

	<div id="ii_zone" <?php echo (property_exists($selected_data, 'points') && $selected_data->points != '' ? 'style="display:block; "' : '') ?>>

		Click on the image to build your zone. Then click save button below.<br />

		<img src="<?php echo esc_url(plugins_url( 'images/loading.gif', dirname(__FILE__))) ?>" alt="Saving..." class="loading" />

		<button id="ii_add_zone" class="button-primary">Save zone</button>

		<?php if(!empty($selected_data->points)) echo '<a class="button button-secondary" href="'.esc_url(admin_url('admin.php?page=interactive_images&task=manage&id='.(int)$interactive_image->id)).'">Add a new zone or hotspot</a>'; ?>

	</div>

</div>

<div id="ii_creator">

	<div>

		<input type="checkbox" id="see_zones" <?php echo (empty($selected_data->points) ? 'checked="checked"' : '') ?> /> 

		<label for="see_zones">See all zones and hotspots</label>

	</div>

	<div id="ii">

		<textarea rows=3 name="coords1" class="canvas-area" disabled placeholder="Shape Coordinates" data-image-url="<?php esc_attr_e($interactive_image->url); ?>"><?php esc_html_e($selected_data->points) ?></textarea>

		<svg id="svg_ii" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" preserveAspectRatio="none" />

	</div>

	<?php

	$script_svg = '';

	foreach($datas as $data)
	{

		$data->description .= '<p><a href="'.esc_url(admin_url('admin.php?page=interactive_images&task=manage&id='.(int)$interactive_image->id.'&id_data='.(int)$data->id)).'" title="Edit zone"><img src="'.esc_url(plugins_url( 'images/edit.png', dirname(__FILE__))).'" /></a> <a href="'.esc_url(admin_url('admin.php?page=interactive_images&task=remove_data&id='.(int)$interactive_image->id.'&id_data=')).(int)$data->id.'" title="Remove zone"><img src="'.esc_url(plugins_url( 'images/remove.png', dirname(__FILE__))).'" /></a></p>';

		if(!empty($data->position))
		{

			$position = explode(',', $data->position);

			$script_svg .= 'showHotspot('.(float)$position[0].', '.(float)$position[1].', "'.esc_js($data->title).'", "'.str_replace('"', '\"', wp_kses_post($data->description)).'", '.(int)$data->id.');';
		}
		else

			$script_svg .= 'showPath("'.esc_html($data->points).'", ii_image_width, ii_image_height, "'.esc_js($data->title).'", "'.str_replace('"', '\"', wp_kses_post($data->description)).'");';

	}

	echo "<script>

			var ii_image_width, ii_image_height;

			var ii_zone_color = '".esc_html($interactive_image->zone_color)."';

			var ii_hotspot_url = '".esc_url(plugins_url( 'images/hotspot.png', dirname(__FILE__)))."';

			var ii_hotspot_w = 32;

			var ii_hotspot_h = 32;


			window.onload = function() {

				const img = new Image();

				img.onload = function() {				  	

				  	ii_image_width = this.width;

				  	ii_image_height = this.height;

				  	jQuery('#ii_creator svg').width(ii_image_width);

					jQuery('#ii_creator svg, #ii').height(ii_image_height);

					jQuery('#ii button.btn').html('Clear current drawing zone');

					ii_hotspot_w = ii_hotspot_w/ii_image_width*100;

					ii_hotspot_h = ii_hotspot_h/ii_image_height*100;

					".$script_svg."

				    jQuery(document).on('click', '#svg_ii path, #svg_ii image', function () {

				    	jQuery('#svg_ii .on').tooltip('destroy').removeClass('on');

				        jQuery(this).addClass('on');

				        jQuery(this).tooltip({

				            items: '#svg_ii .on',

				            position: {
				                my: 'left+30 center',

				                at: 'right center',

				                collision: 'flip'
				            },

				            content: function () {

              					return jQuery(this).attr('title');

              				}

				        });

				        jQuery(this).trigger('mouseenter');

				    });

				    //hide

				    jQuery(document).on('click', '#svg_ii .on', function () {

				        jQuery(this).tooltip('close');

				        jQuery(this).removeClass('on');

				    });

				    //prevent mouseout and other related events from firing their handlers

				    jQuery('#svg_ii path, #svg_ii image').on('mouseout', function (e) {

				        e.stopImmediatePropagation();

				    });				    

				}

				img.src = '".esc_url($interactive_image->url)."';		

		};

		</script>";

	?>

</div>