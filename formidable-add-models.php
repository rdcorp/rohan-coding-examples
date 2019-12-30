<?php
/*
Plugin Name: Formidable - Add Models
Description: Adds models as woocommerce products from formidable forms
Author: Rohan Vyas
*/

$formidable_form_id = 8;
//$formidable_form_id = 14;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

add_action('frm_after_create_entry_' . $formidable_form_id, 'formidable_add_model_create');
add_action('woocommerce_product_options_general_product_data', 'formidable_add_model_general_settings', 20);
add_action('woocommerce_process_product_meta', 'formidable_add_model_save_settings', 25);
add_filter('woocommerce_get_product_attributes', 'formidable_add_model_get_product_attributes');
add_filter('get_product_addons', 'formidable_add_model_get_product_addons', 500);

function formidable_add_model_get_product_addons($addons)
{
	$post_id = get_the_ID();

	$options = array();
	$terms = wp_get_object_terms($post_id, 'pa_genres');
	if(!$terms)
		return $addons;

	foreach($terms as $term)
	{
		$options[] = array(
			'label' => $term->name . ':',
			'price' => '',
			'min' => '',
			'max' => '',
			'duration' => ''
		);
	}

	foreach($addons as $i => $addon)
	{
		if($addon['name'] == 'The job offer is…')
			$addons[$i]['options'] = $options;
	}

	return $addons;
}

function formidable_add_model_get_product_attributes($attributes)
{
	uasort($attributes, 'formidable_add_model_order_product_attributes');


	return $attributes;
}

function formidable_add_model_order_product_attributes($p1, $p2)
{
	$attributes = array(
		'pa_shoots-nudes' => 150,
		'pa_age' => 99,
		'pa_height' => 95,
		'pa_ethnicity' => 90,
		'pa_dress-size' => 80,
		'pa_shoe' => 75,
		'pa_hair-colour' => 70,
		'pa_eye-colour' => 65,
		'pa_country' => 60,
		'pa_city' => 55,
		'pa_profession' => 53,
		'pa_talents' => 50,
		'pa_availabilty' => 45,
		'pa_month' => 40,
		'pa_genres' => 35,
		'pa_portfolio-type' => 1
	);

	$order1 = 1000;
	$order2 = 1000;

	if(isset($attributes[$p1['name']]))
		$order1 = $attributes[$p1['name']];
	if(isset($attributes[$p2['name']]))
		$order2 = $attributes[$p2['name']];

	if($order1 > $order2)
		return -1;
	else if($order1 < $order2)
		return 1;
	else
		return 0;

}

function formidable_add_model_create($entry_id)
{
	global $wpdb;
	require_once( ABSPATH . 'wp-admin/includes/image.php' );

	$table = array(
		'type' => 143,
		'agency_name' => 144,
		'first_name' => 76,
		'last_name' => 146,
		'desc' => 108,
		'images' => 111,
		'etnicity' => 85,
		'profession' => 86,
		'height' => 84,
		'age' => 78,
		'availability' => 94,
		'hair' => 168,
		'dress' => 79,
		'country' => 162,
		'city' => 163,
		'shoe' => 82,
		'eye' => 169,
		'profession' => 86,
		'genres' => 90,
		'talents' => 171,
		'phone' => 97,
		'whatsapp' => 98,
		'email' => 99,
		'twitter_url' => 115,
		'facebook_url' => 116,
		'instagram_url' => 117,
		'snapchat_url' => 118,
		'url1' => 128,
		'url2' => 142,
		'url3' => 141,
		'agency_name' => 144,
		'price_options' => 364,
		'minimum_hourly' => 365,
		
		'price_acting' => 371,
		'price_advertising_product' => 374,
		'price_body_paint' => 375,
		'price_body_parts_modelling' => 376,
		'price_catalog' => 377,
		'price_commercial_print' => 378,
		'price_cosplay' => 379,
		'price_dance' => 380,
		'price_editorial' => 381,
		'price_erotic' => 382,
		'price_fashion' => 383,
		'price_fashion_print' => 384,
		'price_fitness_modelling' => 385,
		'price_glamour_lingerie' => 386,
		'price_hair_and_makeup' => 387,
		'price_hosting_events' => 388,
		'price_implied_nude' => 389,
		'price_lifestyle' => 390,
		'price_lingerie_underwear' => 391,
		'price_nude_modelling' => 392,
		'price_performance_artist' => 393,
		'price_pinup' => 394,
		'price_pregnancy' => 395,
		'price_product_print' => 396,
		'price_promotional_modelling' => 397,
		'price_runway_model' => 398,
		'price_showroom_model' => 399,
		'price_spokesperson_host' => 400,
		'price_sports' => 401,
		'price_stunts' => 402,
		'price_swimwear_bikini' => 403,
		'price_topless' => 404,
		'price_trade_show_modelling' => 405,
		'price_underwater' => 406
	);

	$entry_id = (int)$entry_id;
	if(!$entry_id)
		return;
//$entry_id = 4057;

	//$basic_data = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}frm_items WHERE id = {$entry_id}");

	$data = array();
	$data_array = $wpdb->get_results("SELECT i.meta_value, i.field_id, f.name FROM {$wpdb->prefix}frm_item_metas as i
	LEFT JOIN {$wpdb->prefix}frm_fields as f on f.id = i.field_id
	WHERE item_id = {$entry_id}");
	if(!$data_array)
		return;

	foreach($data_array as $arr)
	{
		$data[$arr->field_id] = $arr->meta_value;
	}

	//var_dump($data);die;

	$type = $data[$table['type']];
	$agency_name = $data[$table['agency_name']];
	$name = $data[$table['first_name']] . ' ' .$data[$table['last_name']];
	$etnicity = $data[$table['etnicity']];
	$age = $data[$table['age']];
	$profession = $data[$table['profession']];
	$city = $data[$table['city']];
	$country = $data[$table['country']];
	$state = $data[$table['state']];
	$height = $data[$table['height']];
	$availability = unserialize($data[$table['availability']]);
	$genres = unserialize($data[$table['genres']]);
	$hair = $data[$table['hair']];
	$eye = $data[$table['eye']];
	$dress = $data[$table['dress']];
	$shoe = $data[$table['shoe']];
	$profession = $data[$table['profession']];
	$talents = unserialize($data[$table['talents']]);
	$phone = $data[$table['phone']];
	$whatsapp = $data[$table['whatsapp']];
	$email = $data[$table['email']];
	$twitter_url = $data[$table['twitter_url']];
	$facebook_url = $data[$table['facebook_url']];
	$instagram_url = $data[$table['instagram_url']];
	$snapchat_url = $data[$table['snapchat_url']];
	$url1 = $data[$table['url1']];
	$url2 = $data[$table['url2']];
	$url3 = $data[$table['url3']];
	$agency_name = $data[$table['agency_name']];
	
	
	$minimum_hourly = 0;
	
	$price_acting = 0;
	$price_advertising_product = 0;
	$price_body_paint = 0;
	$price_body_parts_modelling = 0;
	$price_catalog = 0;
	$price_commercial_print = 0;
	$price_cosplay = 0;
	$price_dance = 0;
	$price_editorial = 0;
	$price_erotic = 0;
	$price_fashion = 0;
	$price_fashion_print = 0;
	$price_fitness_modelling = 0;
	$price_glamour_lingerie = 0;
	$price_hair_and_makeup = 0;
	$price_hosting_events = 0;
	$price_implied_nude = 0;
	$price_lifestyle = 0;
	$price_lingerie_underwear = 0;
	$price_nude_modelling = 0;
	$price_performance_artist = 0;
	$price_pinup = 0;
	$price_pregnancy = 0;
	$price_product_print = 0;
	$price_promotional_modelling = 0;
	$price_runway_model = 0;
	$price_showroom_model = 0;
	$price_spokesperson_host = 0;
	$price_sports = 0;
	$price_stunts = 0;
	$price_swimwear_bikini = 0;
	$price_topless = 0;
	$price_trade_show_modelling = 0;
	$price_underwater = 0;
	
	
		
	if($data[$table["price_options"]] == "I Prefer offer: I want client to make offer payment amount")
	{
		$minimum_hourly = $data[$table["minimum_hourly"]];
	}
	else {
		$price_acting = $data[$table['price_acting']];
    	$price_advertising_product = $data[$table['price_advertising_product']];
    	$price_body_paint = $data[$table['price_body_paint']];
		$price_body_parts_modelling = $data[$table['price_body_parts_modelling']];
		$price_catalog = $data[$table['price_catalog']];
		$price_commercial_print = $data[$table['price_commercial_print']];
		$price_cosplay = $data[$table['price_cosplay']];
		$price_dance = $data[$table['price_dance']];
		$price_editorial = $data[$table['price_editorial']];
		$price_erotic = $data[$table['price_erotic']];
		$price_fashion = $data[$table['price_fashion']];
		$price_fashion_print = $data[$table['price_fashion_print']];
		$price_fitness_modelling = $data[$table['price_fitness_modelling']];
		$price_glamour_lingerie = $data[$table['price_glamour_lingerie']];
		$price_hair_and_makeup = $data[$table['price_hair_and_makeup']];
		$price_hosting_events = $data[$table['price_hosting_events']];
		$price_implied_nude = $data[$table['price_implied_nude']];
		$price_lifestyle = $data[$table['price_lifestyle']];
		$price_lingerie_underwear = $data[$table['price_lingerie_underwear']];
		$price_nude_modelling = $data[$table['price_nude_modelling']];
		$price_performance_artist = $data[$table['price_performance_artist']];
		$price_pinup = $data[$table['price_pinup']];
		$price_pregnancy = $data[$table['price_pregnancy']];
		$price_product_print = $data[$table['price_product_print']];
		$price_promotional_modelling = $data[$table['price_promotional_modelling']];
		$price_runway_model = $data[$table['price_runway_model']];
		$price_showroom_model = $data[$table['price_showroom_model']];
		$price_spokesperson_host = $data[$table['price_spokesperson_host']];
		$price_sports = $data[$table['price_sports']];
		$price_stunts = $data[$table['price_stunts']];
		$price_swimwear_bikini = $data[$table['price_swimwear_bikini']];
		$price_topless = $data[$table['price_topless']];
		$price_trade_show_modelling = $data[$table['price_trade_show_modelling']];
		$price_underwater = $data[$table['price_underwater']];
	}

	
	$attributes = array(
		//'pa_shoots-nudes' => '',
		'pa_age' => $age,
		'pa_height' => $height,
		'pa_ethnicity' => $etnicity,
		'pa_dress-size' => $dress,
		'pa_shoe' => $shoe,
		'pa_hair-colour' => $hair,
		'pa_eye-colour' => $eye,
		'pa_country' => $country,
		'pa_city' => $city,
		'pa_profession' => $profession,
		'pa_talents' => $talents,
		'pa_availabilty' => $availability,
		'pa_month' => array('jan', 'feb', 'mar', 'apr', 'may', 'jun' , 'july', 'aug' ,'sep', 'oct', 'nov' ,'dec'),
		'pa_genres' => $genres,
		'pa_minimum_hourly' => $minimum_hourly,

		'pa_price_acting' => $price_acting,
		'pa_price_advertising_product' => $price_advertising_product,
		'pa_price_body_paint' => $price_body_paint,
		'pa_price_body_parts_modelling' => $price_body_parts_modelling,
		'pa_price_catalog' => $price_catalog,
		'pa_price_commercial_print' => $price_commercial_print,
		'pa_price_cosplay' => $price_cosplay,
		'pa_price_dance' => $price_dance,
		'pa_price_editorial' => $price_editorial,
		'pa_price_erotic' => $price_erotic,
		'pa_price_fashion' => $price_fashion,
		'pa_price_fashion_print' => $price_fashion_print,
		'pa_price_fitness_modelling' => $price_fitness_modelling,
		'pa_price_glamour_lingerie' => $price_glamour_lingerie,
		'pa_price_hair_and_makeup' => $price_hair_and_makeup,
		'pa_price_hosting_events' => $price_hosting_events,
		'pa_price_implied_nude' => $price_implied_nude,
		'pa_price_lifestyle' => $price_lifestyle,
		'pa_price_lingerie_underwear' => $price_lingerie_underwear,
		'pa_price_nude_modelling' => $price_nude_modelling,
		'pa_price_performance_artist' => $price_performance_artist,
		'pa_price_pinup' => $price_pinup,
		'pa_price_pregnancy' => $price_pregnancy,
		'pa_price_product_print' => $price_product_print,
		'pa_price_promotional_modelling' => $price_promotional_modelling,
		'pa_price_runway_model' => $price_runway_model,
		'pa_price_showroom_model' => $price_showroom_model,
		'pa_price_spokeperson_host' => $price_spokesperson_host,
		'pa_price_sports' => $price_sports,
		'pa_price_stunts' => $price_stunts,
		'pa_price_swimwear_bikini' => $price_swimwear_bikini,
		'pa_price_topless' => $price_topless,
		'pa_price_trade_show_modelling' => $price_trade_show_modelling,
		'pa_price_underwater' => $price_underwater
	);
	

	
	//var_dump($attributes);die;
	$excerpt_type = 'Freelance Model';
	$excerpt_type_val = '';
	if(strpos($type, 'Freelance Model') !== false)
	{
		$excerpt_type_val = str_replace('Freelance Model ', '', $type);
		$excerpt_type_val = trim(trim($excerpt_type_val, '()'));
	}
	else
	{
		$excerpt_type = 'Agency Model';
		$excerpt_type_val = str_replace('Agency Model ', '', $type);
		$excerpt_type_val = trim(trim($excerpt_type_val, '()'));
		$excerpt_type_val.= " - {$agency_name}";
	}

	$excerpts = array(
		//$excerpt_type => $excerpt_type_val,
		'Name' => $name,
		'Age' => $age,
		'Height' => $height,
		'Ethnicity' => $etnicity,
		'Dress size' => $dress,
		'Shoe size' => $shoe,
		'Hair Color' => $hair,
		'Eye Color' => $eye,
		'Country' => $country,
		'City' => $city,
		'Profession' => $profession,
		'Talents' => implode(', ', $talents),
		'Agency Name' => $agency_name,
	);

	$appointments = array(
		'Monday:' => '1',
		'Tuesday:' => '2',
		'Wednesday:' => '3',
		'Thursday:' => '4',
		'Friday:' => '5',
		'Saturday:' => '6',
		'Sunday:' => '7',
	);

	$general_data = array(
		'_phone_field' => $phone,
		'_whatsapp_field' => $whatsapp,
		'_email_field' => $email,
		'_twitter_url_field' => $twitter_url,
		'_facebook_url_field' => $facebook_url,
		'_instagram_url_field' => $instagram_url,
		'_snapchat_url_field' => $snapchat_url,
		'_website1_url_field' => $url1,
		'_website2_url_field' => $url2,
		'_website3_url_field' => $url3,
	);


	//create the user
	$user_password = wp_generate_password();
	$user_id = wp_create_user($email, $password, $email);
	$user = new WP_User($user_id);
	$user->set_role('subscriber');
	wp_update_user($user);

	update_user_meta($user_id, 'pw_user_status', 'denied');

	wp_new_user_notification($user_id, null, 'admin');

	$post = array(
		'post_title' => $name,
		'post_content' => $data[$table['desc']],
		'post_status' => 'pending',
		'post_type' => 'product',
		'post_excerpt' => formidable_add_model_get_excerpt($excerpts)
	);
	$post_id = wp_insert_post($post);

	formidable_add_model_create_attributes($post_id, $attributes);
	formidable_add_model_create_appointments($post_id, $appointments, $availability);

	//add the general data
	foreach($general_data as $key => $val)
	{
		add_post_meta($post_id, $key, $val);
	}
	
	wp_set_object_terms($post_id, 'appointment', 'product_type');
	update_post_meta( $post_id, '_virtual', 0);
	update_post_meta( $post_id, '_wc_appointment_has_price_label', 'yes');
	update_post_meta( $post_id, '_wc_appointment_price_label', 'Negotiable');


	$images = array();
	$images_array = unserialize($data[$table['images']]);
	if($images_array)
	{
		foreach($images_array as $image_post_id)
		{
			$res = get_post_meta((int)$image_post_id, '_wp_attached_file');
			$images[] = reset($res);
		}
	}
	if($images)
	{
		//insert the images
		$attach_ids = array();
		foreach($images as $image)
		{
			$filetype = wp_check_filetype($image);
			$type = $filetype['type'];
			$dir = wp_upload_dir();
			$filename = $dir['basedir'] . '/' . $image;

			//insert the first image
			$image_attachment = array(
				'post_title' => basename($image),
				'post_content' => '',
				'post_status' => 'inherit',
				'post_mime_type' => $filetype['type']
			);
			$attach_id = wp_insert_attachment($image_attachment, $filename, $post_id);
			$attach_ids[] = $attach_id;

			$attach_data = wp_generate_attachment_metadata($attach_id, $filename);
			wp_update_attachment_metadata($attach_id, $attach_data);
		}

		$thumb_attach_id = reset($attach_ids);
		$attach_ids = implode(',', $attach_ids);

		add_post_meta($post_id, '_product_image_gallery', $attach_ids);
		update_post_meta($post_id, '_thumbnail_id', $thumb_attach_id);
	}
}

function formidable_add_model_get_excerpt($excerpts)
{
	$arr = array();
	foreach($excerpts as $name => $val)
	{
		$val = trim($val);
		if(!$val)
			continue;

		$arr[] = "{$name}: {$val}";
	}

	return implode("\n", $arr);
}

function formidable_add_model_create_appointments($post_id, $appointments, $availability)
{
	$arr = array();
	foreach($appointments as $name => $app)
	{
		if(in_array($name, $availability))
			continue;

		$arr[] = array(
			'type' => 'days',
          	'appointable' => 'no',
          	'qty' => '',
          	'from' => $app,
          	'to' => $app,
		);
	}

	update_post_meta($post_id, '_wc_appointment_availability', $arr);
}

function formidable_add_model_create_attributes($post_id, $attributes)
{
	$data = array();
	foreach($attributes as $key => $attr)
	{
		if(!is_array($attr))
			$attr = array($attr);

		$i = 0;
		foreach($attr as $a)
		{
			$rr = false;
			if($i)
				$rr = true;

			wp_set_object_terms($post_id, $a, $key , $rr);

            /*if (strpos($a, 'price') !== false || strpos($a, 'hourly') !== false) {
                $data[$key] = array(
    				'name'=>$key,
                    'value'=>'',
                    'is_visible' => '0',
                    'is_variation' => '0',
                    'is_taxonomy' => '1'
    			);
            }
            else {*/
                $data[$key] = array(
    				'name'=>$key,
                    'value'=>'',
                    'is_visible' => '1',
                    'is_variation' => '0',
                    'is_taxonomy' => '1'
    			);    
            /*}*/

			$i++;
		}
	}

	update_post_meta($post_id,'_product_attributes',$data);
}

function formidable_add_model_general_settings()
{
	echo '<div class="options_group">';

	woocommerce_wp_text_input( array(
			'id'          => '_phone_field',
			'label'       => __( 'Phone Number', '' )
	));
	woocommerce_wp_text_input( array(
			'id'          => '_whatsapp_field',
			'label'       => __( 'WhatsApp Number ', '' )
		) );
	woocommerce_wp_text_input( array(
			'id'          => '_email_field',
			'label'       => __( 'Email', '' )
		) );
	woocommerce_wp_text_input( array(
			'id'          => '_twitter_url_field',
			'label'       => __( 'Twitter', '' )
		) );
	woocommerce_wp_text_input( array(
			'id'          => '_facebook_url_field',
			'label'       => __( 'Facebook ', '' )
		) );
	woocommerce_wp_text_input( array(
			'id'          => '_instagram_url_field',
			'label'       => __( 'Instagram', '' )
		) );
	woocommerce_wp_text_input( array(
			'id'          => '_snapchat_url_field',
			'label'       => __( 'Snapchat', '' )
		) );
	woocommerce_wp_text_input( array(
			'id'          => '_website1_url_field',
			'label'       => __( 'Website1', '' )
		) );
	woocommerce_wp_text_input( array(
			'id'          => '_website2_url_field',
			'label'       => __( 'Website1', '' )
		) );
	woocommerce_wp_text_input( array(
			'id'          => '_website3_url_field',
			'label'       => __( 'Website1', '' )
		) );

	echo '</div>';
}

function formidable_add_model_save_settings($post_id)
{
	$meta_to_save = array(
		'_phone_field', '_whatsapp_field' , '_email_field' ,'_twitter_url_field' , '_facebook_url_field', '_instagram_url_field', '_snapchat_url_field',
		'_website1_url_field', '_website2_url_field', '_website3_url_field'
	);

	foreach($meta_to_save as $meta_key)
	{
		$value = ! empty( $_POST[ $meta_key ] ) ? $_POST[ $meta_key ] : '';
		$value = sanitize_text_field( $value );

		update_post_meta($post_id, $meta_key, $value );
	}
}

function lm_hide_attributes_from_additional_info_tabs( $attributes, $product ) {
	/**
	 * Array of attributes to hide from the Additional Information
	 * tab on single WooCommerce product pages.
	 */
	$hidden_attributes = [
		'pa_price_acting',
		'pa_price_advertising_product',
		'pa_price_body_paint',
		'pa_price_body_parts_modelling',
		'pa_price_catalog',
		'pa_price_commercial_print',
		'pa_price_cosplay',
		'pa_price_dance',
		'pa_price_editorial',
		'pa_price_erotic',
		'pa_price_fashion',
		'pa_price_fashion_print',
		'pa_price_fitness_modelling',
		'pa_price_glamour_lingerie',
		'pa_price_hair_and_makeup',
		'pa_price_hosting_events',
		'pa_price_implied_nude',
		'pa_price_lifestyle',
		'pa_price_lingerie_underwear',
		'pa_price_nude_modelling',
		'pa_price_performance_artist',
		'pa_price_pinup',
		'pa_price_pregnancy',
		'pa_price_product_print',
		'pa_price_promotional_modelling',
		'pa_price_runway_model',
		'pa_price_showroom_model',
		'pa_price_spokeperson_host',
		'pa_price_sports',
		'pa_price_stunts',
		'pa_price_swimwear_bikini',
		'pa_price_topless',
		'pa_price_trade_show_modelling',
		'pa_price_underwater'
	];
	foreach ( $hidden_attributes as $hidden_attribute ) {
		if ( ! isset( $attributes[ $hidden_attribute ] ) ) {
			continue;
		}
		$attribute = $attributes[ $hidden_attribute ];
		$attribute->set_visible( false );
	}
	return $attributes;
}
add_filter( 'woocommerce_product_get_attributes', 'lm_hide_attributes_from_additional_info_tabs', 20, 2 );