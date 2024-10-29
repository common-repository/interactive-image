<script>



		jQuery(document).ready(function(){			



			//choix d'une image dans la librairie Wordpress

		    jQuery('.form_ii .choose_img').click(function(e) {

		    	var _this = this;

		        e.preventDefault();

		        var image = wp.media({ 

		            title: 'Upload Image',

		            // mutiple: true if you want to upload multiple files at once

		            multiple: false

		        }).open()

		        .on('select', function(e){

		            // This will return the selected image from the Media Uploader, the result is an object

		            var uploaded_image = image.state().get('selection').first();

		            // We convert uploaded_image to a JSON object to make accessing it easier

		            // Output to the console uploaded_image

		            var image_url = uploaded_image.toJSON().url;

		            // Let's assign the url value to the input field

		            jQuery('.form_ii input[name='+jQuery(_this).attr('rel')+']').val(image_url);

		        });

		    });	

			jQuery(document).ready(function(){

				jQuery('.form_ii input[name=zone_color]').wpColorPicker();

			});	   

		});

</script>

<h2>All interactive images</h2>

<form action="<?php echo esc_url(admin_url('admin.php?page=interactive_images&task=add')); ?>" method="post" class="form_ii" id="form_new_ii">

	<?php wp_nonce_field( 'edit_ii' ) ?>

	<label>Name: </label><input type="text" name="name" required /><br />

	<label>Zones color: </label><input type="text" name="zone_color" required /><br />

	<label>Picture: </label><input type="text" name="url" required /><button class="choose_img button-secondary" rel="url">Browser library</button><br /><br />

	<input type="submit" value="Create interactive image" class="button button-primary" />

</form>

<div id="ii-list">

<?php

if(sizeof($interactive_images) > 0)
{
	foreach($interactive_images as $interactive_image)
	{
		echo '<div class="ii"><h2>'.esc_html($interactive_image->name).'</h2><br />

		<a href="'.esc_url(admin_url('admin.php?page=interactive_images&task=manage&id='.(int)$interactive_image->id)).'"><img src="'.esc_url(plugins_url( 'images/edit.png', dirname(__FILE__))).'" alt="" /></a>

		<a href="'.esc_url(admin_url('admin.php?page=interactive_images&task=remove&id='.(int)$interactive_image->id)).'"><img src="'.esc_url(plugins_url( 'images/remove.png', dirname(__FILE__))).'" alt="" /></a>

		</div>';
	}
}

else

	echo '<p>No interactive image created yet!</p>';

?>

</div>

<h2><a href="https://profiles.wordpress.org/manu225/#content-plugins" target="_blank">Discover all my free plugins</a></h2>