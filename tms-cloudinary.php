<?php
/*
Plugin Name: TMSoftware cloudinary
Plugin URI: http://offorsi.is
Description: This is not just a plugin
Author: Þórarinn I. Tómasson
Version: 0.1
Author URI: http://offorsi.is
*/


require __DIR__ . '/vendor/autoload.php';


\Cloudinary::config(array(
  "cloud_name" => "tmsoftware",
  "api_key" => "376895436531487",
  "api_secret" => "Wz664adYG5_XtTBU2pCWOEUBBr8"
));



$cloudinary = TMS\Cloudinary_WP_Integration::get_instance();

$cloudinary->setup();





function modify_post_thumbnail_html($html, $post_id, $post_thumbnail_id, $size, $attr) {
    $id = get_post_thumbnail_id(); // gets the id of the current post_thumbnail (in the loop)
    $src = wp_get_attachment_image_src($id, $size); // gets the image url specific to the passed in size (aka. custom image size)
    $alt = get_the_title($id); // gets the post thumbnail title
    $class = (isset($attr['class'])? $attr['class'] : '' ); // gets classes passed to the post thumbnail, defined here for easier function access
    $imgmeta = wp_get_attachment_metadata( $id, true ); // $unfiltered if true

    if( !empty( $imgmeta["cloudinary_data"]["public_id"] ) ) {
  //  var_dump($imgmeta["cloudinary_data"]["public_id"]);
    // Check to see if a 'retina' class exists in the array when calling "the_post_thumbnail()", if so output different <img/> html
    $act = (isset($imgmeta['cloud_tms'])?$imgmeta['cloud_tms']: 'orginal');
    if (strpos($class, 'retina') !== false) {

      if($act == 'sepia'){
         $html = cl_image_tag($imgmeta["cloudinary_data"]["public_id"],  array("effect" => 'sepia') );    //$argArr = array("effect"=> 'sepia');
      }
      elseif($act == 'cartoonify'){
         $html = cl_image_tag($imgmeta["cloudinary_data"]["public_id"],  array("effect" => 'cartoonify') );    //$argArr = array("effect"=> 'sepia');
      }
      elseif($act == 'sharpen'){
         $html = cl_image_tag($imgmeta["cloudinary_data"]["public_id"],  array("effect" => 'sharpen:112') );    //$argArr = array("effect"=> 'sepia');
      }
      elseif($act == 'face'){
         $html = cl_image_tag($imgmeta["cloudinary_data"]["public_id"],  array("crop" => 'crop', 'gravity' => 'face') );    //$argArr = array("effect"=> 'sepia');
      }
      else {
        $html = cl_image_tag($imgmeta["cloudinary_data"]["public_id"] );
      }

    //  $html = cl_image_tag($imgmeta["cloudinary_data"]["public_id"],  array("effect" => 'sepia') );
  //  $html =  cl_image_tag($imgmeta["cloudinary_data"]["public_id"], array("effect"=> get_field('effect', $post_id), "zoom"=>3.8, "crop" => "fill", 'radius' => get_field('radius', $post_id),   "gravity" =>  get_field('gravity', $post_id) ));    // $html = '<img src="" alt="" data-src="' . $src[0] . '" data-alt="' . $alt . '" class="' . $class . '" />';
    } else {
      if( $act == 'sepia'){
        $html = cl_image_tag($imgmeta["cloudinary_data"]["public_id"],  array("effect" => 'sepia') );  //   $argArr = array("effect"=> 'sepia');
      }
      elseif( $act == 'cartoonify'){
         $html = cl_image_tag($imgmeta["cloudinary_data"]["public_id"],  array("effect" => 'cartoonify') );    //$argArr = array("effect"=> 'sepia');
      }
      elseif($act == 'sharpen'){
         $html = cl_image_tag($imgmeta["cloudinary_data"]["public_id"],  array("effect" => 'sharpen:112') );    //$argArr = array("effect"=> 'sepia');
      }
      elseif($act == 'face'){
         $html = cl_image_tag($imgmeta["cloudinary_data"]["public_id"],  array("crop" => 'crop', 'gravity' => 'face') );    //$argArr = array("effect"=> 'sepia');
      }
      else {
            $html = cl_image_tag($imgmeta["cloudinary_data"]["public_id"] );
      }

     //  $html =  cl_image_tag($imgmeta["cloudinary_data"]["public_id"], array("effect"=> get_field('effect', $post_id), "zoom"=>3.8,  "crop" => "fill", 'radius' => get_field('radius', $post_id),   "gravity" =>  get_field('gravity', $post_id) )); // $html = '<img src="' . $src[0] . '" alt="' . $alt . '" class="' . $class . '" />';
    }
  }else {
    $html = '<img src="' . $src[0] . '" alt="' . $alt . '" class="' . $class . '" />';
  }
  //$html .= print_r($imgmeta);
    return $html;
}
add_filter('post_thumbnail_html', 'modify_post_thumbnail_html', 99, 5);


/*
// define the admin_post_thumbnail_html callback
function filter_admin_post_thumbnail_html( $content, $post_id, $thumbnail_id ) {
    // make filter magic happen here...
    $imgmeta = wp_get_attachment_metadata( $thumbnail_id, true );

    $content = '<img src="http://localhost:8888/image-dev/wp-content/uploads/2017/04/maxresdefault-1-300x200.jpg" draggable="false" alt="">';
    return $content;
};

// add the filter
add_filter( 'admin_post_thumbnail_html', 'filter_admin_post_thumbnail_html', 10, 3 );

*/




/* attachment field input */

add_filter( 'attachment_fields_to_edit', 'attachment_fields_to_edit', 10, 2 );


/**
 * Add watermark buttons on attachment image locations
 */
function attachment_fields_to_edit( $form_fields, $post ) {

	//f ( $this->options['watermark_image']['manual_watermarking'] == 1 && $this->options['backup']['backup_image'] ) {

		$data = wp_get_attachment_metadata( $post->ID, false );

    $src = wp_get_attachment_image_src($post->ID, 'full');

    $pubid = $data["cloudinary_data"]["public_id"];

		// is this really an image?
		//if ( in_array( get_post_mime_type( $post->ID ), $this->allowed_mime_types ) && is_array( $data ) ) {
			$form_fields['image_progressive'] = array(
				'show_in_edit'	 => true,
				'tr'			 => '
				<div id="image_watermark_buttons"' . /* ( get_post_meta( $post->ID, $this->is_watermarked_metakey, true ) ? ' class="watermarked"' : '' )  . */ ' data-id="' . $post->ID . '" style="display: none;">
					<label class="setting">
						<span class="name">' . __( 'Myndvinnsla', 'image-watermark' ) . '</span>
						<span class="value" style="width: 63%">
                <a href="#" class="iw-watermark-action" data-pubid="'. $pubid .'" data-src="'. $src[0] .'" data-action="orginal" data-id="' . $post->ID . '">' . __( 'Orginal', 'tms-pro' ) . '</a>
                 | <a href="#" class="iw-watermark-action" data-pubid="'. $pubid .'" data-src="'. $src[0] .'" data-action="auto" data-id="' . $post->ID . '">' . __( 'Auto improve', 'tms-pro' ) . '</a>
                 | <a href="#" class="iw-watermark-action" data-pubid="'. $pubid .'" data-src="'. $src[0] .'" data-action="sharpen" data-id="' . $post->ID . '">' . __( 'Sharpen', 'tms-pro' ) . '</a>
                 | <a href="#" class="iw-watermark-action" data-pubid="'. $pubid .'" data-src="'. $src[0] .'" data-action="sepia" data-id="' . $post->ID . '">' . __( 'Make sepia', 'tms-pro' ) . '</a>
                 | <a href="#" class="iw-watermark-action" data-pubid="'. $pubid .'" data-src="'. $src[0] .'" data-action="cartoonify" data-id="' . $post->ID . '">' . __( 'Make cartoon', 'tms-pro' ) . '</a>
                 | <a href="#" class="iw-watermark-action" data-pubid="'. $pubid .'" data-src="'. $src[0] .'" data-action="face" data-id="' . $post->ID . '">' . __( 'Crop face', 'tms-pro' ) . '</a>
            </span>
					</label>
					<div class="clear"></div><label class="setting">
          <span class="name">' . __( 'Skerping', 'image-watermark' ) . '</span>
          <span class="value" style="width: 63%">
          <input type="range" name="points" min="0" max="300" step="5" value="0" onchange="showValue(this.value)">
          <span id="range">0</span>
          </span>
          </label>
				</div>
				<script>
					jQuery( document ).ready( function ( $ ) {
						// if ( typeof watermarkImageActions != "undefined" ) {
							$( "#image_watermark_buttons" ).show();
					//	}
					});
          function showValue(newValue)
          {
	           document.getElementById("range").innerHTML=newValue;
          }
				</script>'
			);
	//	}
	//}

	return $form_fields;
}



function tmspro_enqueue_script() {
wp_enqueue_script( 'tmscript', plugin_dir_url( __FILE__ ) . 'tmscript.js', array('jquery'), '1.0', false );

wp_localize_script( 'tmscript', 'iwImageActionArgs', array( 'ajax_url' => admin_url('admin-ajax.php'), '_nonce'	=> wp_create_nonce( 'image-watermark' ), ) );


wp_enqueue_script( 'jquery-cloudinary', plugin_dir_url( __FILE__ ) . 'bower_components/cloudinary-jquery/cloudinary-jquery.js', array('jquery'), '1.0', false );


}
add_action('admin_enqueue_scripts', 'tmspro_enqueue_script');





// Setup Ajax action hook
add_action( 'wp_ajax_read_me_later',  'read_me_later'  );

function read_me_later() {
    $rml_post_id = $_POST['attachment_id'];
    $act = $_POST['iw-action'];
    $echo = array();

    $d = wp_get_attachment_metadata($rml_post_id);
// $upload_dir = wp_upload_dir();

//    $image = wp_get_image_editor( $upload_dir['baseurl'] . '/' .  $d['file'] );
$data = array($act);
$d['cloud_tms'] = $act;
wp_update_attachment_metadata( $rml_post_id, $d );

/*if ( ! add_post_meta( $rml_post_id, 'cloud_tms', $act, true ) ) {
 update_post_meta( $rml_post_id, 'cloud_tms', $act );
}*/

  //add_post_meta( $rml_post_id, 'my_key2', $act );
  //  $time = substr( $d['file'], 0, 7 );
  //  $udir = wp_upload_dir( $time );
  //  $image = new Imagick();
  //  $image->readImage("/path/to/image.jpg");

}





/* Register shortcode_atts_gallery filter callback */
//add_filter( 'shortcode_atts_gallery', 'meks_gallery_atts', 10, 3 );






/*
a:6:{s:5:"width";i:1440;s:6:"height";i:600;s:4:"file";s:20:"2017/04/2-layers.png";s:5:"sizes";a:5:{s:9:"thumbnail";a:4:{s:4:"file";s:20:"2-layers-150x150.png";s:5:"width";i:150;s:6:"height";i:150;s:9:"mime-type";s:9:"image/png";}s:6:"medium";a:4:{s:4:"file";s:20:"2-layers-300x125.png";s:5:"width";i:300;s:6:"height";i:125;s:9:"mime-type";s:9:"image/png";}s:12:"medium_large";a:4:{s:4:"file";s:20:"2-layers-768x320.png";s:5:"width";i:768;s:6:"height";i:320;s:9:"mime-type";s:9:"image/png";}s:5:"large";a:4:{s:4:"file";s:21:"2-layers-1024x427.png";s:5:"width";i:1024;s:6:"height";i:427;s:9:"mime-type";s:9:"image/png";}s:12:"thumb_single";a:4:{s:4:"file";s:21:"2-layers-1140x530.png";s:5:"width";i:1140;s:6:"height";i:530;s:9:"mime-type";s:9:"image/png";}}s:10:"image_meta";a:12:{s:8:"aperture";s:1:"0";s:6:"credit";s:0:"";s:6:"camera";s:0:"";s:7:"caption";s:0:"";s:17:"created_timestamp";s:1:"0";s:9:"copyright";s:0:"";s:12:"focal_length";s:1:"0";s:3:"iso";s:1:"0";s:13:"shutter_speed";s:1:"0";s:5:"title";s:0:"";s:11:"orientation";s:1:"0";s:8:"keywords";a:0:{}}s:15:"cloudinary_data";a:7:{s:9:"public_id";s:15:"2-layers_ni4qvz";s:5:"width";i:1440;s:6:"height";i:600;s:5:"bytes";i:1422956;s:3:"url";s:81:"http://res.cloudinary.com/tmsoftware/image/upload/v1492266962/2-layers_ni4qvz.png";s:10:"secure_url";s:82:"https://res.cloudinary.com/tmsoftware/image/upload/v1492266962/2-layers_ni4qvz.png";s:5:"sizes";a:20:{i:1000;a:5:{s:5:"width";i:1000;s:6:"height";i:417;s:5:"bytes";i:635047;s:3:"url";s:96:"http://res.cloudinary.com/tmsoftware/image/upload/c_scale,w_1000/v1492266962/2-layers_ni4qvz.png";s:10:"secure_url";s:97:"https://res.cloudinary.com/tmsoftware/image/upload/c_scale,w_1000/v1492266962/2-layers_ni4qvz.png";}i:994;a:5:{s:5:"width";i:994;s:6:"height";i:414;s:5:"bytes";i:581312;s:3:"url";s:95:"http://res.cloudinary.com/tmsoftware/image/upload/c_scale,w_994/v1492266962/2-layers_ni4qvz.png";s:10:"secure_url";s:96:"https://res.cloudinary.com/tmsoftware/image/upload/c_scale,w_994/v1492266962/2-layers_ni4qvz.png";}i:985;a:5:{s:5:"width";i:985;s:6:"height";i:411;s:5:"bytes";i:572910;s:3:"url";s:95:"http://res.cloudinary.com/tmsoftware/image/upload/c_scale,w_985/v1492266962/2-layers_ni4qvz.png";s:10:"secure_url";s:96:"https://res.cloudinary.com/tmsoftware/image/upload/c_scale,w_985/v1492266962/2-layers_ni4qvz.png";}i:951;a:5:{s:5:"width";i:951;s:6:"height";i:397;s:5:"bytes";i:540783;s:3:"url";s:95:"http://res.cloudinary.com/tmsoftware/image/upload/c_scale,w_951/v1492266962/2-layers_ni4qvz.png";s:10:"secure_url";s:96:"https://res.cloudinary.com/tmsoftware/image/upload/c_scale,w_951/v1492266962/2-layers_ni4qvz.png";}i:917;a:5:{s:5:"width";i:917;s:6:"height";i:382;s:5:"bytes";i:507352;s:3:"url";s:95:"http://res.cloudinary.com/tmsoftware/image/upload/c_scale,w_917/v1492266962/2-layers_ni4qvz.png";s:10:"secure_url";s:96:"https://res.cloudinary.com/tmsoftware/image/upload/c_scale,w_917/v1492266962/2-layers_ni4qvz.png";}i:884;a:5:{s:5:"width";i:884;s:6:"height";i:369;s:5:"bytes";i:477096;s:3:"url";s:95:"http://res.cloudinary.com/tmsoftware/image/upload/c_scale,w_884/v1492266962/2-layers_ni4qvz.png";s:10:"secure_url";s:96:"https://res.cloudinary.com/tmsoftware/image/upload/c_scale,w_884/v1492266962/2-layers_ni4qvz.png";}i:849;a:5:{s:5:"width";i:849;s:6:"height";i:354;s:5:"bytes";i:444495;s:3:"url";s:95:"http://res.cloudinary.com/tmsoftware/image/upload/c_scale,w_849/v1492266962/2-layers_ni4qvz.png";s:10:"secure_url";s:96:"https://res.cloudinary.com/tmsoftware/image/upload/c_scale,w_849/v1492266962/2-layers_ni4qvz.png";}i:813;a:5:{s:5:"width";i:813;s:6:"height";i:339;s:5:"bytes";i:412428;s:3:"url";s:95:"http://res.cloudinary.com/tmsoftware/image/upload/c_scale,w_813/v1492266962/2-layers_ni4qvz.png";s:10:"secure_url";s:96:"https://res.cloudinary.com/tmsoftware/image/upload/c_scale,w_813/v1492266962/2-layers_ni4qvz.png";}i:777;a:5:{s:5:"width";i:777;s:6:"height";i:324;s:5:"bytes";i:381723;s:3:"url";s:95:"http://res.cloudinary.com/tmsoftware/image/upload/c_scale,w_777/v1492266962/2-layers_ni4qvz.png";s:10:"secure_url";s:96:"https://res.cloudinary.com/tmsoftware/image/upload/c_scale,w_777/v1492266962/2-layers_ni4qvz.png";}i:741;a:5:{s:5:"width";i:741;s:6:"height";i:309;s:5:"bytes";i:351019;s:3:"url";s:95:"http://res.cloudinary.com/tmsoftware/image/upload/c_scale,w_741/v1492266962/2-layers_ni4qvz.png";s:10:"secure_url";s:96:"https://res.cloudinary.com/tmsoftware/image/upload/c_scale,w_741/v1492266962/2-layers_ni4qvz.png";}i:701;a:5:{s:5:"width";i:701;s:6:"height";i:292;s:5:"bytes";i:318276;s:3:"url";s:95:"http://res.cloudinary.com/tmsoftware/image/upload/c_scale,w_701/v1492266962/2-layers_ni4qvz.png";s:10:"secure_url";s:96:"https://res.cloudinary.com/tmsoftware/image/upload/c_scale,w_701/v1492266962/2-layers_ni4qvz.png";}i:660;a:5:{s:5:"width";i:660;s:6:"height";i:275;s:5:"bytes";i:286223;s:3:"url";s:95:"http://res.cloudinary.com/tmsoftware/image/upload/c_scale,w_660/v1492266962/2-layers_ni4qvz.png";s:10:"secure_url";s:96:"https://res.cloudinary.com/tmsoftware/image/upload/c_scale,w_660/v1492266962/2-layers_ni4qvz.png";}i:618;a:5:{s:5:"width";i:618;s:6:"height";i:258;s:5:"bytes";i:255302;s:3:"url";s:95:"http://res.cloudinary.com/tmsoftware/image/upload/c_scale,w_618/v1492266962/2-layers_ni4qvz.png";s:10:"secure_url";s:96:"https://res.cloudinary.com/tmsoftware/image/upload/c_scale,w_618/v1492266962/2-layers_ni4qvz.png";}i:573;a:5:{s:5:"width";i:573;s:6:"height";i:239;s:5:"bytes";i:222879;s:3:"url";s:95:"http://res.cloudinary.com/tmsoftware/image/upload/c_scale,w_573/v1492266962/2-layers_ni4qvz.png";s:10:"secure_url";s:96:"https://res.cloudinary.com/tmsoftware/image/upload/c_scale,w_573/v1492266962/2-layers_ni4qvz.png";}i:526;a:5:{s:5:"width";i:526;s:6:"height";i:219;s:5:"bytes";i:190760;s:3:"url";s:95:"http://res.cloudinary.com/tmsoftware/image/upload/c_scale,w_526/v1492266962/2-layers_ni4qvz.png";s:10:"secure_url";s:96:"https://res.cloudinary.com/tmsoftware/image/upload/c_scale,w_526/v1492266962/2-layers_ni4qvz.png";}i:478;a:5:{s:5:"width";i:478;s:6:"height";i:199;s:5:"bytes";i:160906;s:3:"url";s:95:"http://res.cloudinary.com/tmsoftware/image/upload/c_scale,w_478/v1492266962/2-layers_ni4qvz.png";s:10:"secure_url";s:96:"https://res.cloudinary.com/tmsoftware/image/upload/c_scale,w_478/v1492266962/2-layers_ni4qvz.png";}i:423;a:5:{s:5:"width";i:423;s:6:"height";i:176;s:5:"bytes";i:129127;s:3:"url";s:95:"http://res.cloudinary.com/tmsoftware/image/upload/c_scale,w_423/v1492266962/2-layers_ni4qvz.png";s:10:"secure_url";s:96:"https://res.cloudinary.com/tmsoftware/image/upload/c_scale,w_423/v1492266962/2-layers_ni4qvz.png";}i:358;a:5:{s:5:"width";i:358;s:6:"height";i:149;s:5:"bytes";i:95740;s:3:"url";s:95:"http://res.cloudinary.com/tmsoftware/image/upload/c_scale,w_358/v1492266962/2-layers_ni4qvz.png";s:10:"secure_url";s:96:"https://res.cloudinary.com/tmsoftware/image/upload/c_scale,w_358/v1492266962/2-layers_ni4qvz.png";}i:288;a:5:{s:5:"width";i:288;s:6:"height";i:120;s:5:"bytes";i:64629;s:3:"url";s:95:"http://res.cloudinary.com/tmsoftware/image/upload/c_scale,w_288/v1492266962/2-layers_ni4qvz.png";s:10:"secure_url";s:96:"https://res.cloudinary.com/tmsoftware/image/upload/c_scale,w_288/v1492266962/2-layers_ni4qvz.png";}i:200;a:5:{s:5:"width";i:200;s:6:"height";i:83;s:5:"bytes";i:33366;s:3:"url";s:95:"http://res.cloudinary.com/tmsoftware/image/upload/c_scale,w_200/v1492266962/2-layers_ni4qvz.png";s:10:"secure_url";s:96:"https://res.cloudinary.com/tmsoftware/image/upload/c_scale,w_200/v1492266962/2-layers_ni4qvz.png";}}}}
*/



/*
// https://wordpress.stackexchange.com/questions/134014/how-do-i-change-modify-the-post-thumbnail-html-output
function my_custom_image_attributes( $attr, $attachment ) {
  remove_filter('wp_get_attachment_image_attributes','my_custom_image_attributes');
  $image = wp_get_attachment_image_src( $attachment->ID, 'full' );
  $attr['data-src'] = $image[0];
  $attr['data-alt'] = $attachment->post_title;
  $attr['class'] .= ' retina';
  return $attr;
}
add_filter('wp_get_attachment_image_attributes','my_custom_image_attributes');

*/


/*
Mjög useful responsive feature fyrir wp
https://viastudio.com/optimizing-your-theme-for-wordpress-4-4s-responsive-images/
Hvernig á að intergrata það í þemu


http://www.makeuseof.com/tag/complete-guide-featured-thumbnails-image-sizes-wordpress/

http://stackoverflow.com/questions/14200815/how-to-hook-into-wordpress-thumbnail-generation

https://wordpress.stackexchange.com/questions/60599/how-to-replace-the-post-thumbnail-template-tag-and-show-the-first-inside-the-pos
*/


/*
function silencio_post_thumbnail_sizes_attr($attr, $attachment, $size) {
    //Calculate Image Sizes by type and breakpoint
    //Header Images
    if ($size === 'header-thumb') {
        $attr['sizes'] = '(max-width: 768px) 92vw, (max-width: 992px) 450px, (max-width: 1200px) 597px, 730px';
    //Blog Thumbnails
    } else if ($size === 'blog-thumb') {
        $attr['sizes'] = '(max-width: 992px) 200px, (max-width: 1200px) 127px, 160px';
    }
    return $attr;
}
add_filter('wp_get_attachment_image_attributes', 'silencio_post_thumbnail_sizes_attr', 10 , 3);

*/
/*

function adjust_image_sizes_attr( $sizes, $size ) {
   $sizes = '(max-width: 709px) 85vw, (max-width: 909px) 67vw, (max-width: 1362px) 62vw, 840px';
   return $sizes;
}
add_filter( 'wp_calculate_image_sizes', 'adjust_image_sizes_attr', 10 , 2 );
*/
