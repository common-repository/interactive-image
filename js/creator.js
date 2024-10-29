function makeSVG(tag, attrs)
{

    var el= document.createElementNS('http://www.w3.org/2000/svg', tag);

    for (var k in attrs)

    	if(k == 'href')

    		el.setAttributeNS('http://www.w3.org/1999/xlink','href', attrs[k]);

    	else

         	el.setAttribute(k, attrs[k]);

     return el;

}

function showPath(points, width, height, title, description)
{

	var zone_points = points.split(',');

	//construit le chemin svg

	var path;

	var limit = zone_points.length-2;

	for(let i = 0; i <= limit; i=i+2)

	{

		if(i == 0)

			path = 'M'+((zone_points[i]/width)*100)+' '+((zone_points[i+1]/height)*100);

		else

			path += 'L'+((zone_points[i]/width)*100)+' '+((zone_points[i+1]/height)*100);

		if(i == limit)

			path += 'Z';

	}

	console.log(path);

	//on ajoute la zone au svg

	var path_el = makeSVG('path', {d: path, fill: ii_zone_color, title: '<h2>'+title+'</h2>'+description});		    	

	jQuery('#svg_ii').get(0).appendChild(path_el);

}

function showHotspot(x, y, title, description, id)
{

	//on ajoute le hotspot

	var hotspot = makeSVG('image', {'href': ii_hotspot_url, class: 'draggable', x: x, y: y, width: ii_hotspot_w, height: ii_hotspot_h, preserveAspectRatio: 'none', title: '<h2>'+title+'</h2>'+description, rel: id });

	jQuery('#svg_ii').get(0).appendChild(hotspot);



	//hide

	/*jQuery(document).on('click', '#svg_ii .on', function () {

	    jQuery(this).tooltip('close');

	    jQuery(this).removeClass('on');

	});*/



	//et déplaçable

	var start_x, start_y;

	jQuery('#svg_ii image.draggable').draggable({

		start: function(event, ui) {

			start_x = (parseFloat(jQuery(this).attr('x'))*ii_image_width)/100;

			start_y = (parseFloat(jQuery(this).attr('y'))*ii_image_height)/100;

		},

        drag: function (event, ui) {

        	var c_x = ((start_x+ui.position.left-ui.originalPosition.left)/ii_image_width)*100;

        	var c_y = ((start_y+ui.position.top-ui.originalPosition.top)/ii_image_height)*100;

        	jQuery(this).attr('x', c_x);

        	jQuery(this).attr('y', c_y);

        },

        stop: function(event, ui) {

			start_x = 0;

			start_y = 0;

			//on saveugarde la nouvelle position en BDD

			save_hotspot_position_ii(jQuery(this).attr('rel'), jQuery(this).attr('x'), jQuery(this).attr('y'));

		}

    });

    return hotspot;

}

jQuery(document).ready(function(){

	jQuery('#hotspot').click(function(){

		jQuery('#ii_creator_settings #ii_hotspot').show();

		jQuery('#ii_creator_settings #ii_zone').hide();

	});	

	jQuery('#zone').click(function(){

		jQuery('#ii_creator_settings #ii_zone').show();

		jQuery('#ii_creator_settings #ii_hotspot').hide();

	});

	if(jQuery('#zone').is(':checked'))
	{

		jQuery('#ii_creator_settings #ii_zone').show();

		jQuery('#ii_creator_settings #ii_hotspot').hide();

	}
	else if(jQuery('#hotspot').is('*'))
	{
		jQuery('#ii_creator_settings #ii_zone').hide();

		jQuery('#ii_creator_settings #ii_hotspot').show();
	}

	//choix d'une image dans la librairie Wordpress

    jQuery('#ii_creator_settings .choose_img').click(function(e) {

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

            jQuery('#ii_creator_settings input[name='+jQuery(_this).attr('rel')+']').val(image_url);

        });

    });	 

    jQuery('#ii_add_zone').click(function(){

		//on sauvegarde la zone en BDD via Ajax

		var description = tinymce.activeEditor.getContent();

		save_zone_ii(description);		    	

    });


    jQuery('#ii_add_hotspot').click(function(){

    	//on affiche le svg

    	jQuery('#see_zones').prop('checked', true);

    	jQuery('#svg_ii').show();

    	var id = jQuery('#ii_creator_settings input[name=id_data]').val();

    	if(id == '')
    	{

	    	var default_x = default_y = 50;

	    	//on affiche le hotspot au centre de l'image

	    	var hotspot = showHotspot(default_x, default_y, jQuery('#ii_creator_settings input[name=title]').val(), tinymce.activeEditor.getContent());

	    	jQuery(document).on('click', '#svg_ii image:last-child', function () {

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

		            	//alert(jQuery(this).attr('title'));

      					return jQuery(this).attr('title');

      				}

		        });

		        jQuery(this).trigger('mouseenter');

		    });

		    //hide

		    jQuery(document).on('click', '#svg_ii .on', function () {

		        jQuery(this).tooltip('destroy');

		        jQuery(this).removeClass('on');

		    });

		    //prevent mouseout and other related events from firing their handlers

		    jQuery('#svg_ii image:last-child').on('mouseout', function (e) {

		        e.stopImmediatePropagation();

		    });

		    jQuery([document.documentElement, document.body]).animate({

		        scrollTop: jQuery(hotspot).offset().top

		    }, 500);

	    	//on sauvegarde le hotspot en BDD

    		save_hotspot_ii(tinymce.activeEditor.getContent(), default_x, default_y, hotspot);

	    }
	    else

	    	//on sauvegarde le hotspot en BDD

	    	update_hotspot_info_ii(id, jQuery('#ii_creator_settings input[name=title]').val(), tinymce.activeEditor.getContent());

    });

    jQuery('#see_zones').click(function(){


    	if(jQuery(this).is(':checked'))

    		jQuery('#svg_ii').show();

    	else
    	{
    		jQuery('#svg_ii').hide();

    		jQuery('#svg_ii .on').tooltip('close');
    	}

    });

    if(jQuery('#see_zones').is(':checked'))

    	jQuery('#svg_ii').show();

    jQuery(document).on('click', '#svg_ii', function(e){

		if(jQuery(e.target).attr('id') == 'svg_ii' && jQuery('#zone').is(':checked'))
		{
    		alert('To draw a new zone you need to disable \"See all zones and hotspots\" first.');

    	}
   		else if(jQuery('#hotspot').is(':checked') && !jQuery(e.target).is('image') && !jQuery(e.target).is('path'))
    	{
    		console.log(jQuery(e.target));

    		alert('To add a hotspot, click "Add hotspot" button above');
    	}

    });

    jQuery(document).on('click', '#ii canvas', function(e){

    	if(jQuery('#hotspot').is(':checked'))
    	{
    		alert('To add a new hotspot, click "Add hotspot" button above. The hotspot will appear on the center of the image, then you can move it.');
    	}

    });

});