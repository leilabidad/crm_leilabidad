<?php
/**
 * @package leila_bidad
 * @version 1.0
 */
/*
Plugin Name: پلاگین استودیو بهتر
Description: این پلاگین صرفا برای تست لیلا بیداد در استودیو بهتر می باشد.
Author: لیلا بیداد
Version: 1.0
Author URI: http://leilabidad.com
*/

function vegetable_setup() {
	$labels = array(
		'name' => __( 'گیاهان', 'vegetable_leilabidad_plugin' ),
		'singular_name' => __( 'گیاهان', 'vegetable_leilabidad_plugin' ),
		'add_new_item' => __( 'افزودن گیاه جدید', 'vegetable_leilabidad_plugin' ),
		'edit_item' => __( 'ویرایش گیاه', 'vegetable_leilabidad_plugin' ),
		'new_item' => __( 'گیاه جدید', 'vegetable_leilabidad_plugin' ),
		'not_found' => __( 'گیاهی یافت نشد', 'vegetable_leilabidad_plugin' ),
		'all_items' => __( 'همه گیاهان', 'vegetable_leilabidad_plugin' )
	);
	$args = array(
		'labels' => $labels,
		'public' => true,
		'show_ui' => true,
		'show_in_menu' => true,
		'has_archive' => true,
		'map_meta_cap' => true,
		'menu_icon' => 'dashicons-admin-links',		
		'supports' => array( 'title', 'editor', 'thumbnail', 'author' ),
		'taxonomies' => array( 'vegetable-categury' )
	);
	register_post_type( 'vegetable', $args );
}
add_action( 'init', 'vegetable_setup' );

/**
 * Register taxonomies
 */
function vegetable_register_taxonomies(){

	$labels = array(
		'name' => __( 'طبقه بندی', 'vegetable_leilabidad_plugin' ),
		'label' => __( 'طبقه بندی', 'vegetable_leilabidad_plugin' ),
		'add_new_item' => __( 'افزودن طبقه جدید', 'vegetable_leilabidad_plugin' ),
	);

	$args = array(
		'labels' => $labels,
		'label' => __( 'طبقه بندی', 'vegetable_leilabidad_plugin' ),
		'show_ui' => true,
		'show_admin_column' => true
	);
	register_taxonomy( 'vegetable-categury', array( 'vegetable' ), $args );
}
add_action( 'init', 'vegetable_register_taxonomies' );

/**
 * Add meta box
 
 */
function vegetable_add_meta_boxes( $post ){
	add_meta_box( 'vegetable_meta_box', __( 'Nutrition facts', 'vegetable_leilabidad_plugin' ), 'vegetable_build_meta_box', 'vegetable', 'side', 'low' );
}
add_action( 'add_meta_boxes_vegetable', 'vegetable_add_meta_boxes' );

/**
 * Build custom field meta box
 *
 * @param post $post The post object
 */
function vegetable_build_meta_box( $post ){
	// make sure the form request comes from WordPress
	wp_nonce_field( basename( __FILE__ ), 'vegetable_meta_box_nonce' );

	// retrieve the _vegetable_cholesterol current value
	$current_cholesterol = get_post_meta( $post->ID, '_vegetable_cholesterol', true );

	// retrieve the _vegetable_carbohydrates current value
	$current_carbohydrates = get_post_meta( $post->ID, '_vegetable_carbohydrates', true );

	$vitamins = array( 'Vitamin A', 'Thiamin (B1)', 'Riboflavin (B2)', 'Niacin (B3)', 'Pantothenic Acid (B5)', 'Vitamin B6', 'Vitamin B12', 'Vitamin C', 'Vitamin D', 'Vitamin E', 'Vitamin K' );
	
	// stores _vegetable_vitamins array 
	$current_vitamins = ( get_post_meta( $post->ID, '_vegetable_vitamins', true ) ) ? get_post_meta( $post->ID, '_vegetable_vitamins', true ) : array();

	?>
	<div class='inside'>

		<h3><?php _e( 'Cholesterol', 'vegetable_leilabidad_plugin' ); ?></h3>
		<p>
			<input type="radio" name="cholesterol" value="0" <?php checked( $current_cholesterol, '0' ); ?> /> بلی<br />
			<input type="radio" name="cholesterol" value="1" <?php checked( $current_cholesterol, '1' ); ?> /> خیر
		</p>

		<h3><?php _e( 'Carbohydrates', 'vegetable_leilabidad_plugin' ); ?></h3>
		<p>
			<input type="text" name="carbohydrates" value="<?php echo $current_carbohydrates; ?>" /> 
		</p>

		<h3><?php _e( 'Vitamins', 'vegetable_leilabidad_plugin' ); ?></h3>
		<p>
		<?php
		foreach ( $vitamins as $vitamin ) {
			?>
			<input type="checkbox" name="vitamins[]" value="<?php echo $vitamin; ?>" <?php checked( ( in_array( $vitamin, $current_vitamins ) ) ? $vitamin : '', $vitamin ); ?> /><?php echo $vitamin; ?> <br />
			<?php
		}
		?>
		</p>
	</div>
	<?php
}

/**
 * Store custom field meta box data
 *
 * @param int $post_id The post ID.
 * @link https://codex.wordpress.org/Plugin_API/Action_Reference/save_post
 */
function vegetable_save_meta_box_data( $post_id ){
	// verify meta box nonce
	if ( !isset( $_POST['vegetable_meta_box_nonce'] ) || !wp_verify_nonce( $_POST['vegetable_meta_box_nonce'], basename( __FILE__ ) ) ){
		return;
	}

	// return if autosave
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ){
		return;
	}

  // Check the user's permissions.
	if ( ! current_user_can( 'edit_post', $post_id ) ){
		return;
	}

	// store custom fields values
	// cholesterol string
	if ( isset( $_REQUEST['cholesterol'] ) ) {
		update_post_meta( $post_id, '_vegetable_cholesterol', sanitize_text_field( $_POST['cholesterol'] ) );
	}

	// store custom fields values
	// carbohydrates string
	if ( isset( $_REQUEST['carbohydrates'] ) ) {
		update_post_meta( $post_id, '_vegetable_carbohydrates', sanitize_text_field( $_POST['carbohydrates'] ) );
	}

	// store custom fields values
	// vitamins array
	if( isset( $_POST['vitamins'] ) ){
		$vitamins = (array) $_POST['vitamins'];

		// sinitize array
		$vitamins = array_map( 'sanitize_text_field', $vitamins );

		// save data
		update_post_meta( $post_id, '_vegetable_vitamins', $vitamins );
	}else{
		// delete data
		delete_post_meta( $post_id, '_vegetable_vitamins' );
	}
}
add_action( 'save_post_vegetable', 'vegetable_save_meta_box_data' );

/////////////////////////ساخت شورت کد
function getPostViews($postID){
    $count_key = 'post_views_count';
    $count = get_post_meta($postID, $count_key, true);
    if($count==''){
        delete_post_meta($postID, $count_key);
        add_post_meta($postID, $count_key, '0');
        return "بازدید نشده";
    }
    return $count.' بازدید';
}
function setPostViews($postID) {
    $count_key = 'post_views_count';
    $count = get_post_meta($postID, $count_key, true);
    if($count==''){
        $count = 0;
        delete_post_meta($postID, $count_key);
        add_post_meta($postID, $count_key, '0');
    }else{
        $count++;
        update_post_meta($postID, $count_key, $count);
    }
}

// Remove issues with prefetching adding extra views
remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0); 



	function wpex_pagination() {
		
    $the_query = new WP_Query( array('posts_per_page'=>10,
                                 'post_type'=>'vegetable',
                                 'paged' => get_query_var('paged') ? get_query_var('paged') : 1) 
                            ); 
                            ?>
<?php while ($the_query -> have_posts()) : $the_query -> the_post();  ?>
<div class="col-xs-12 file">
<a href="<?php the_permalink(); ?>" class="file-title" target="_blank">
<i class="fa fa-angle-right" aria-hidden="true"></i> <?php echo get_the_title(); ?>
</a>
<div class="file-description"><?php echo getPostViews(get_the_ID()); ?></div>
</div>
<?php
endwhile;

$big = 999999999; // need an unlikely integer
 echo paginate_links( array(
    'base' => str_replace( $big, '%#%', get_pagenum_link( $big ) ),
    'format' => '?paged=%#%',
    'current' => max( 1, get_query_var('paged') ),
    'total' => $the_query->max_num_pages
) );

wp_reset_postdata();

	}
	


 function show_taxonomies($atts) {

wpex_pagination();



 }
add_shortcode('show-taxonomies', 'show_taxonomies');

// Add to a column in WP-Admin
add_filter('manage_posts_columns', 'posts_column_views');
add_action('manage_posts_custom_column', 'posts_custom_column_views',5,2);
function posts_column_views($defaults){
    $defaults['post_views'] = __('Views');
    return $defaults;
}
function posts_custom_column_views($column_name, $id){
    if($column_name === 'post_views'){
        echo getPostViews(get_the_ID());
    }
}