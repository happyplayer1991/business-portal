/***************************************
 *** Customer Photo Crop Extension ***
 ***************************************
 *
 * @copyright   Copyright (c) 2015
 * @company     NetAttingo Technologies
 * @package     Netgo_Customerpic
 * @author 		Vipin
 * @dev			77vips@gmail.com
 *
 */
//Show loader while uploading photo
function submit_photo(base_url) { 
	jQuery('#loading_progress').html('<img src="'+base_url+'skin/frontend/base/default/customerpic/img/ajax-loader.gif"> Uploading your photo...');
}

//Show_popup : show the popup
function show_popup(id) { 
	jQuery('#'+id).show();
}

//Close_popup : close the popup
function close_popup(id) { 
	jQuery('#'+id).hide();
}

//Show_popup_crop : show the crop popup
function show_popup_crop(url, customer_id, img_width, img_height) { 
	jQuery('#cropbox').attr('src', url); 
	try {
		jcrop_api.destroy();
	} catch (e) {
		// object not defined
	}
	//Initialize the Jcrop using the TARGET_W and TARGET_H that initialized before
    jQuery('#cropbox').Jcrop({
      aspectRatio: img_width / img_height,
      setSelect:   [ 100, 100, img_width, img_height ],
      onSelect: updateCoords
    },function(){
        jcrop_api = this;
    });
 
	jQuery('#photo_url').val(url);
	jQuery('#customer_id').val(customer_id); 
	jQuery('#popup_upload').hide();
	jQuery('#loading_progress').html('');
	jQuery('#photo').val('');

	//Show the crop popup
	jQuery('#popup_crop').show();
}

//Crop_photo : 
function crop_photo(img_width, img_height) {
	var x_ = jQuery('#x').val();
	var y_ = jQuery('#y').val();
	var w_ = jQuery('#w').val();
	var h_ = jQuery('#h').val();
	var photo_url = jQuery('#photo_url').val();
	var customer_id = jQuery('#customer_id').val();

	//Hide thecrop  popup
	jQuery('#popup_crop').hide(); 

	//Display the loading texte
	var getUrl = window.location; 
	var baseUrl = getUrl .protocol + "//" + getUrl.host + "/" + getUrl.pathname.split('/')[1];
	jQuery('#photo_container').html('<img src="'+baseUrl+'/skin/frontend/base/default/customerpic/img/ajax-loader.gif"> Processing...');

	 
	jQuery.ajax({ 
		url: baseUrl+'/netgo/customerpic/crop',
		type: 'POST',
		data: {x:x_, y:y_, w:w_, h:h_, photo_url:photo_url, targ_w:img_width, targ_h:img_height, customer_id:customer_id},
		success:function(data){ 
			jQuery('#photo_container').html(data+'<span class="upload_btn_img" onclick="show_popup(\'popup_upload\')"><span class="ed-txt">Edit</span></span>	');
		}
	});
}

//UpdateCoords : updates hidden input values after every crop selection
function updateCoords(c) {
	jQuery('#x').val(c.x);
	jQuery('#y').val(c.y);
	jQuery('#w').val(c.w);
	jQuery('#h').val(c.h);
}

