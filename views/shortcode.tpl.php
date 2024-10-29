<div id="interactive_image_<?php echo (int)$interactive_image->id ?>" class="interactive_image">

	<img class="the_image" src="<?php echo esc_url($interactive_image->url) ?>" alt="" />

	<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 100 100" preserveAspectRatio="none" />

	<?php



	$script_svg = '';



	foreach($datas as $data)

	{		

		if(!empty($data->position))

		{

			$position = explode(',', $data->position);

			//$script_svg .= 'showHotspot('.$position[0].', '.$position[1].', "'.$data->title.'", "'.str_replace('"', '\"', $data->description).'", '.$data->id.');';

			echo '<div class="hotspot" data-position="'.esc_attr($position[0]).','.esc_attr($position[1]).'" data-title="'.esc_attr($data->title).'" data-id="'.esc_attr($data->id).'" data-id-image="'.esc_attr($interactive_image->id).'">'.wp_kses_post($data->description).'</div>';

		}

		else

			//$script_svg .= 'showPath("'.$data->points.'", ii_image_width, ii_image_height, "'.$data->title.'", "'.str_replace('"', '\"', $data->description).'");';

			echo '<div class="zone" data-points="'.esc_attr($data->points).'" data-color="'.esc_attr($interactive_image->zone_color).'" data-title="'.esc_attr($data->title).'" data-id="'.esc_attr($data->id).'"  data-id-image="'.esc_attr($interactive_image->id).'">'.wp_kses_post($data->description).'</div>';

	}





	echo "<script>var ii_hotspot_url = '".esc_url(plugins_url( 'images/hotspot.png', dirname(__FILE__)))."';</script>";

?>

</div>