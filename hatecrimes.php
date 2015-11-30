<?php
/*
Plugin Name: Hate Crime
Description: Custom Post Type Hate Crime for crimenesdelodio.info project
Author: Gerald Kogler
Author URI: http://go.yuri.at
Text Domain: hatecrimes
*/

wp_enqueue_script('jquery-ui-datepicker');
wp_enqueue_script('jquery-ui-tabs');
wp_enqueue_style('jquery-ui-css', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css');

/* load textdomain */
add_action( 'init', 'hatecrime_load_textdomain' );
function hatecrime_load_textdomain() {
	load_plugin_textdomain('hatecrimes', false, dirname(plugin_basename(__FILE__)));
}

/* create custom post type HATE CRIME */
add_action( 'init', 'create_post_type_hatecrime' );
function create_post_type_hatecrime() {

	register_post_type( 'hatecrime',
		array(
			'labels' => array(
				'name' => __( 'Hate Crimes', 'hatecrimes' ),
				'singular_name' => __( 'Hate Crime', 'hatecrimes' )
			),
		'public' => true,
		'has_archive' => false,
		'taxonomies' => array( 'type'),
		)
	);
	register_taxonomy(
		'type',
		'hatecrime',
		array(
			'label' => __( 'Type', 'hatecrimes' ),
			'rewrite' => array( 'slug' => 'type' ),
			'hierarchical' => true,
		)
	);
	register_taxonomy(
		'sentence_type',
		'hatecrime',
		array(
			'label' => __( 'Sentence', 'hatecrimes' ),
			'rewrite' => array( 'slug' => 'sentence_type' ),
			'hierarchical' => true,
		)
	);
	register_taxonomy(
		'delict',
		'hatecrime',
		array(
			'label' => __( 'Delict', 'hatecrimes' ),
			'rewrite' => array( 'slug' => 'delict' ),
			'hierarchical' => true,
		)
	);
}

function add_hatecrime_meta_boxes() {
	add_meta_box("hatecrime_meta", __("Hate Crime details", 'hatecrimes'), "add_hatecrime_details_hatecrime_meta_box", "hatecrime", "normal", "low");
}

function add_hatecrime_details_hatecrime_meta_box() {
	wp_nonce_field(basename(__FILE__), "meta-box-nonce");

	global $post;
	$custom = get_post_custom( $post->ID );
 
	?>	<style>.width99 {width:99%;}</style>
	<p>
		<label><?php _e('Date', 'hatecrimes')?>:</label> 
		<input type="text" class="datepicker" name="date" value="<?= @$custom['date'][0] ?>" />
	</p>
	<p>
		<label><strong><?php _e('Adress', 'hatecrimes')?>:</strong> </label><br />

		<label><?php _e('Street', 'hatecrimes')?>:</label>
		<input type="text" name="street" value="<?= @$custom['street'][0] ?>" /><br />

		<label><?php _e('Neighbourhood', 'hatecrimes')?>:</label>
		<input type="text" name="neighbourhood" value="<?= @$custom['neighbourhood'][0] ?>" /><br />

		<label><?php _e('City', 'hatecrimes')?>:</label>
		<input type="text" name="city" value="<?= @$custom['city'][0] ?>" /><br />

		<label><?php _e('Province', 'hatecrimes')?>:</label>
		<select name="province">
			<option value=""> </option> 

			<?php
				$provinces = getProvinces();
				foreach ($provinces as $province) {
					echo '<option value="'.$province.'" ';
					if (@$custom["province"][0] == $province) echo 'selected="selected"';
					echo '>'.$province.'</option>';
				}
			?>

		</select>
	</p>
	<p>
		<label><strong><?php _e('Location', 'hatecrimes')?>:</strong> </label><br />
		<label><?php _e('Latitude', 'hatecrimes')?>:</label> 
		<input type="text" name="latitude" value="<?= @$custom['latitude'][0] ?>" /><br />
		<label><?php _e('Longitude', 'hatecrimes')?>:</label> 
		<input type="text" name="longitude" value="<?= @$custom['longitude'][0] ?>" />
	</p>

	<p>
		<label><?php _e('Judicial body', 'hatecrimes')?>:</label> 
		<input type="text" name="trial" value="<?= @$custom['trial'][0] ?>" class="width99 translate" />
	</p>
	<p>
		<label><?php _e('Sentence', 'hatecrimes')?>:</label> 
		<input type="text" name="sentence" value="<?= @$custom['sentence'][0] ?>" class="width99 translate" />
	</p>
	<p>
		<label><?php _e('Legal qualification', 'hatecrimes')?>:</label> 
		<input type="text" name="legal" value="<?= @$custom['legal'][0] ?>" class="width99 translate" />
	</p>
	<p>
		<label><?php _e('Age of aggressor', 'hatecrimes')?>:</label> 
		<input type="text" name="age" value="<?= @$custom['age'][0] ?>" />
	</p>
	<p>
		<label><?php _e('Sources', 'hatecrimes')?>:</label><br />
		<textarea name="sources" rows="10" cols="80"><?= @$custom['sources'][0] ?></textarea>
	</p>

	<script type="text/javascript">
	jQuery(document).ready(function($) {
		$('.datepicker').datepicker({
			defaultDate: "-14y",
			changeMonth: true,
			changeYear: true,
			yearRange: '1991:2016',
			dateFormat : 'mm/dd/yy'
		});
	});
	</script>
	<?php
}
/**
 * Save custom field data when creating/updating posts
 */
function save_hatecrime_custom_fields() {
    if (!isset($_POST["meta-box-nonce"]) || !wp_verify_nonce($_POST["meta-box-nonce"], basename(__FILE__)))
        return $post_id;
 
    if(!current_user_can("edit_post", $post_id))
        return $post_id;
 
    if(defined("DOING_AUTOSAVE") && DOING_AUTOSAVE)
        return $post_id;

  global $post;
 
  if ( $post ) {
    update_post_meta($post->ID, "date", @$_POST["date"]);
    update_post_meta($post->ID, "street", @$_POST["street"]);
    update_post_meta($post->ID, "neighbourhood", @$_POST["neighbourhood"]);
    update_post_meta($post->ID, "city", @$_POST["city"]);
    update_post_meta($post->ID, "province", @$_POST["province"]);
    update_post_meta($post->ID, "latitude", @$_POST["latitude"]);
    update_post_meta($post->ID, "longitude", @$_POST["longitude"]);
    update_post_meta($post->ID, "trial", @$_POST["trial"]);
    update_post_meta($post->ID, "sentence", @$_POST["sentence"]);
    update_post_meta($post->ID, "legal", @$_POST["legal"]);
    update_post_meta($post->ID, "age", @$_POST["age"]);
    update_post_meta($post->ID, "sources", @$_POST["sources"]);
  }
}
add_action( 'admin_init', 'add_hatecrime_meta_boxes' );
add_action( 'save_post', 'save_hatecrime_custom_fields' );

//activate jquery validation plugin
function add_jquery_validation_hatecrime() {
    wp_enqueue_script(
		'jquery-validate',
		plugin_dir_url( __FILE__ ) . 'jquery.validate.min.js',
		array('jquery'),
		'1.11.2',
		true
	);
}
add_action( 'wp_enqueue_scripts', 'add_jquery_validation_hatecrime' );

function getProvinces() {
	return array(
		'Alava', 
		'Albacete', 
		'Alicante', 
		'Almeria', 
		'Asturias', 
		'Avila', 
		'Badajoz', 
		'Barcelona', 
		'Burgos', 
		'Caceres', 
		'Cadiz', 
		'Cantabria', 
		'Castellon', 
		'Cuidad Real', 
		'Cordoba', 
		'Cuenca', 
		'La Coruña', 
		'Girona', 
		'Granada', 
		'Guadalajara', 
		'Guipuxcoa', 
		'Huelva', 
		'Huesca', 
		'Islas Baleares', 
		'Jaen', 
		'La Rioja', 
		'Las Palmas', 
		'Leon', 
		'Lleida', 
		'Lugo', 
		'Madrid', 
		'Malaga', 
		'Murcia', 
		'Navarra', 
		'Orense', 
		'Palencia', 
		'Pontevedra', 
		'Salamanca', 
		'Santa Cruz de Tenerife', 
		'Sevilla', 
		'Segovia', 
		'Soria', 
		'Tarragona', 
		'Teruel', 
		'Toledo', 
		'Valencia', 
		'Valladolid', 
		'Vizcaya', 
		'Zamora', 
		'Zaragoza'
	);
}

?>