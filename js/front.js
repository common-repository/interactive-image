function showDataII(img, id) {
	
	//console.log(img.width);
	jQuery(img).parent().find('svg').width(img.width);
	jQuery(img).parent().find('svg').height(img.height);

	//calcul dimension hotspot
	var ii_hotspot_w = 32;
	var ii_hotspot_h = 32;
	ii_hotspot_w = ii_hotspot_w/img.naturalWidth*100;
	ii_hotspot_h = ii_hotspot_h/img.naturalHeight*100;

	//on ajoute les zones
	jQuery('#interactive_image_'+id).find('.zone').each(function(){
		showPath(jQuery(this).data('points'), jQuery(this).data('color'), img.naturalWidth, img.naturalHeight, jQuery(this).data('title'), jQuery(this).html(), jQuery(this).data('id-image'));
	});

	//on ajoute les hotspots
	jQuery('#interactive_image_'+id).find('.hotspot').each(function(){
		var position = jQuery(this).data('position').split(',');
		showHotspot(position[0], position[1], ii_hotspot_w, ii_hotspot_h, jQuery(this).data('title'), jQuery(this).html(), jQuery(this).data('id'), jQuery(this).data('id-image'));
	});
	
	//infobulle au click
    jQuery(document).on('click', '.interactive_image svg path, .interactive_image svg image', function (e) {

    	jQuery(this).parent().find('.on').tooltip('destroy').removeClass('on');
        jQuery(this).addClass('on');
        var _this = this;
        jQuery(this).tooltip({
            items: jQuery(this).parent().find('.on'),
            position: {
                my: 'left+30 top+30',
                at: 'left top',
                of: jQuery(_this).parent().parent(),
                collision: 'flip'
            },
            content: function () {
				return jQuery(this).attr('title');
			}
        });
        jQuery(this).trigger('mouseenter');
        e.stopImmediatePropagation();
    });

    //hide
    jQuery(document).on('click', '.interactive_image', function () {
        jQuery(this).find('.on').tooltip('destroy').removeClass('on');
        //jQuery(this).removeClass('on');
    });

    //prevent mouseout and other related events from firing their handlers
    jQuery('.interactive_image svg path, .interactive_image svg image').on('mouseout', function (e) {
        e.stopImmediatePropagation();
    });	

}

function makeSVG(tag, attrs) {
    var el= document.createElementNS('http://www.w3.org/2000/svg', tag);
    for (var k in attrs)
    	if(k == 'href')
    		el.setAttributeNS('http://www.w3.org/1999/xlink','href', attrs[k]);
    	else
         	el.setAttribute(k, attrs[k]);
     return el;
}

function showPath(points, color, width, height, title, description, id_image)
{
	var zone_points = points.split(',');
	//construit le chemin svg
	var path;
	var limit = zone_points.length-2;
	for(var i = 0; i <= limit; i=i+2)
	{
		if(i == 0)
			path = 'M'+((zone_points[i]/width)*100)+' '+((zone_points[i+1]/height)*100);
		else
			path += 'L'+((zone_points[i]/width)*100)+' '+((zone_points[i+1]/height)*100);
		if(i == limit)
			path += 'Z';
	}

	//console.log(path);

	//on ajoute la zone au svg
	var path_el = makeSVG('path', {d: path, fill: color, title: '<h2>'+title+'</h2>'+description});	
	console.log('#interactive_image_'+id_image); 	
	jQuery('#interactive_image_'+id_image).find('svg').get(0).appendChild(path_el);
}

function showHotspot(x, y, width, height, title, description, id, id_image)
{
	//console.log('#interactive_image_'+id_image);
	//on ajoute le hotspot
	var hotspot = makeSVG('image', {'href': ii_hotspot_url, x: x, y: y, width: width, height: height, preserveAspectRatio: 'none', title: '<h2>'+title+'</h2>'+description, rel: id });
	jQuery('#interactive_image_'+id_image).find('svg').get(0).appendChild(hotspot);	

    return hotspot;
}

jQuery(document).ready(function(){

	jQuery('.interactive_image').each(function(){

		var id = jQuery(this).attr('id').replace('interactive_image_', '');

		jQuery(this).find('img.the_image').one('load', function(){
			showDataII(this, id);
		}).each(function() {
		  if(this.complete) {
		      jQuery(this).load();
		  }
		});
	});

	jQuery(window).resize(function() {

		//on redmensionne tous les svg à la taille des images
		jQuery('.interactive_image').each(function(){
			jQuery(this).find('svg').width(jQuery(this).find('img').width());
			jQuery(this).find('svg').height(jQuery(this).find('img').height());
		});

	});

});