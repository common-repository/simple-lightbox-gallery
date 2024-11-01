<?php
/**
 * Plugin Name:  Lightbox slider - Responsive Lightbox Gallery.
 * Version: 1.10.1
 * Description:  Lightbox slider plugin is allow users to view larger versions of images, simple slide shows and Gallery view with grid layout.
 * Author: Weblizar
 * Author URI: https://www.weblizar.com
 * Plugin URI: https://wordpress.org/plugins/simple-lightbox-gallery/
 * Text Domain: simple-lightbox-gallery
 */

/**
 * Constant Variable
 */
defined( 'ABSPATH' ) or die();
define( 'WEBLIZAR_SLGF_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

// Image Crop Size Function
add_image_size( 'slgf_12_thumb', 500, 9999, array( 'center', 'top' ) );
add_image_size( 'slgf_346_thumb', 400, 9999, array( 'center', 'top' ) );
add_image_size( 'slgf_12_same_size_thumb', 500, 500, array( 'center', 'top' ) );
add_image_size( 'slgf_346_same_size_thumb', 400, 400, array( 'center', 'top' ) );

add_filter( 'image_size_names_choose', 'WEBLIZAR_SLGF_IMAGES_SIZES_FILTER' );

function WEBLIZAR_SLGF_IMAGES_SIZES_FILTER( $sizes ) {
	return array_merge(
		$sizes,
		array(
			'slgf_346_thumb'           => __( 'Size 400 X AUTO' ),
			'slgf_12_thumb'            => __( 'Size 500 X AUTO' ),
			'slgf_12_same_size_thumb'  => __( 'Same Size 500 X 500' ),
			'slgf_346_same_size_thumb' => __( 'Same Size 400 X 400' ),
		)
	);
}
/**
 * Support and Our Products Page
 */
add_action( 'admin_menu', 'slgf_SettingsPage' );
function slgf_SettingsPage() {
	add_submenu_page( 'edit.php?post_type=slgf_slider', esc_html__( 'Help and Support', 'simple-lightbox-gallery' ), esc_html__( 'Help and Support', 'simple-lightbox-gallery' ), 'administrator', 'SLGF-help-page', 'SLGF_Help_and_Support_page' );
}

function SLGF_Help_and_Support_page() {
	 wp_enqueue_style( 'wrgf-help_and_support-custom-css', WEBLIZAR_SLGF_PLUGIN_URL . 'css/help_and_support.css' );
	require_once 'help_and_support.php';
}

/**
 * Get Responsive Gallery Pro Plugin Page
 */
function SLGF_Pro_page_Function() {
	 // css
	wp_enqueue_style( 'font-awesome', WEBLIZAR_SLGF_PLUGIN_URL . 'css/all.min.css' );
	wp_enqueue_style( 'wrgf-pricing-table-css', WEBLIZAR_SLGF_PLUGIN_URL . 'css/pricing-table.css' );
	wp_enqueue_style( 'bootstrap', WEBLIZAR_SLGF_PLUGIN_URL . 'css/bootstrap.min.css' );
}

function SLGF_Recommendation_Page() {
	wp_enqueue_style( 'recom2', WEBLIZAR_SLGF_PLUGIN_URL . 'css/recom.css' );
	require_once 'recommendations.php';
}

/**
 * Weblizar Lightbox Slider Pro Shortcode Detect Function
 */
function slgf_js_css_load_function() {
	global $wp_query;
	$Posts   = $wp_query->posts;
	$Pattern = get_shortcode_regex();
	foreach ( $Posts as $Post ) {
		if ( isset( $Post->post_content ) && strpos( $Post->post_content, 'SLGF' ) ) {
			/**
			 * js scripts
			 */
			/*
				wp_enqueue_style('wp-color-picker');
				wp_enqueue_script('wp-color-picker');
			*/
			
			wp_enqueue_script( 'jquery' );
			wp_enqueue_script( 'wl-slgf-hover-pack-js', WEBLIZAR_SLGF_PLUGIN_URL . 'js/hover-pack.js', array( 'jquery' ) );
			wp_enqueue_script( 'wl-slgf-rpg-script', WEBLIZAR_SLGF_PLUGIN_URL . 'js/reponsive_photo_gallery_script.js', array( 'jquery' ) );

		   /**
			 * css scripts
			 */
			wp_register_style( 'wl-slgf-hover-pack-css', WEBLIZAR_SLGF_PLUGIN_URL . 'css/hover-pack.css' );
			wp_enqueue_style( 'wl-slgf-hover-pack-css' );

			wp_register_style( 'wl-slgf-img-gallery-css', WEBLIZAR_SLGF_PLUGIN_URL . 'css/img-gallery.css' );
			wp_enqueue_style( 'wl-slgf-img-gallery-css' );

			/**
			 * font awesome css
			 */
			wp_register_style( 'font-awesome-5', WEBLIZAR_SLGF_PLUGIN_URL . 'css/all.min.css' );
			wp_enqueue_style( 'font-awesome-5' );

			/*** envira & isotope js */
			wp_register_script( 'slgf_envira-js', WEBLIZAR_SLGF_PLUGIN_URL . 'js/masonry.pkgd.min.js', array( 'jquery' ), '', true );
			wp_enqueue_script( 'slgf_envira-js' );

			wp_register_script( 'slgf_imagesloaded', WEBLIZAR_SLGF_PLUGIN_URL . 'js/imagesloaded.pkgd.min.js', array( 'jquery' ), '', true );
			wp_enqueue_script( 'slgf_imagesloaded' );

			break;
		} //end of if
	} //end of foreach
}
/** For the_title function */
// add_action('wp', 'slgf_js_css_load_function');

add_filter( 'the_title', 'slgf_convac_lite_untitled' );
function slgf_convac_lite_untitled( $title ) {
	if ( $title == '' ) {
		return esc_html__( 'No Title', 'simple-lightbox-gallery' );
	} else {
		return $title;
	}
}

function slgf_remove_image_box() {
	remove_meta_box( 'postimagediv', 'slgf_slider', 'side' );
}

add_action( 'do_meta_boxes', 'slgf_remove_image_box' );

/**
 * Class Defination For Lightbox Slider Pro
 */
class SLGF {
	private $admin_thumbnail_size = 150;
	private $thumbnail_size_w     = 150;
	private $thumbnail_size_h     = 150;

	public function __construct() {
		add_action( 'admin_enqueue_scripts', array( &$this, 'slgf_admin_print_scripts' ) );
		add_image_size( 'rpg_gallery_admin_thumb', $this->admin_thumbnail_size, $this->admin_thumbnail_size, true );
		add_image_size( 'rpg_gallery_thumb', $this->thumbnail_size_w, $this->thumbnail_size_h, true );
		add_shortcode( 'lightboxslider', array( &$this, 'shortcode' ) );

		if ( is_admin() ) {
			add_action( 'init', array( &$this, 'SLGF_CPT' ), 1 );
			add_action( 'add_meta_boxes', array( &$this, 'add_all_slgf_meta_boxes' ) );
			add_action( 'admin_init', array( &$this, 'add_all_slgf_meta_boxes' ), 1 );
			add_action( 'save_post', array( &$this, 'slgf_add_image_meta_box_save' ), 9, 1 );
			add_action( 'save_post', array( &$this, 'slgf_settings_meta_save' ), 9, 1 );
			add_action( 'wp_ajax_slgf_get_thumbnail', array( &$this, 'ajax_get_thumbnail_slgf' ) );
		}
	}

	// Required JS & CSS
	public function slgf_admin_print_scripts( $hook_suffix ) {
		if ( in_array( $hook_suffix, array( 'post.php', 'post-new.php' ) ) ) {
			$screen = get_current_screen();
			if ( is_object( $screen ) && 'slgf_slider' === $screen->post_type ) {
				wp_enqueue_script( 'media-upload' );
				wp_enqueue_script( 'slgf-media-uploader-js', WEBLIZAR_SLGF_PLUGIN_URL . 'js/slgf-multiple-media-uploader.js', array( 'jquery' ) );

				wp_enqueue_script('slgf-script-handle', WEBLIZAR_SLGF_PLUGIN_URL . 'js/slgf-multiple-media-uploader.js', array('jquery'), '', true);
				wp_localize_script( 'slgf-script-handle', 'slgf_vars', array(
					'slgf_nonce' => wp_create_nonce( 'slgf_nonce_action' ),
				) );


				wp_enqueue_media();
				// custom add image box css
				wp_enqueue_style( 'slgf-meta-css', WEBLIZAR_SLGF_PLUGIN_URL . 'css/rpg-meta.css' );

				// font awesome css
				wp_enqueue_style( 'font-awesome-5', WEBLIZAR_SLGF_PLUGIN_URL . 'css/all.min.css' );

				// single media uploader js
				wp_enqueue_script(
					'slgf-media-uploads',
					WEBLIZAR_SLGF_PLUGIN_URL . 'js/slgf-media-upload-script.js',
					array(
						'media-upload',
						'thickbox',
						'jquery',
					)
				);
				wp_enqueue_script( 'wp-color-picker' );
				wp_enqueue_style( 'wp-color-picker' );

				// code-mirror css & js for custom css section
				wp_enqueue_style( 'slgf_codemirror-css', WEBLIZAR_SLGF_PLUGIN_URL . 'css/codemirror/codemirror.css' );
				wp_enqueue_style( 'slgf_blackboard', WEBLIZAR_SLGF_PLUGIN_URL . 'css/codemirror/blackboard.css' );
				wp_enqueue_style( 'slgf_show-hint-css', WEBLIZAR_SLGF_PLUGIN_URL . 'css/codemirror/show-hint.css' );

				wp_enqueue_script( 'slgf_codemirror-js', WEBLIZAR_SLGF_PLUGIN_URL . 'css/codemirror/codemirror.js', array( 'jquery' ) );
				wp_enqueue_script( 'slgf_css-js', WEBLIZAR_SLGF_PLUGIN_URL . 'css/codemirror/slgf-css.js', array( 'jquery' ) );
				wp_enqueue_script( 'slgf_css-hint-js', WEBLIZAR_SLGF_PLUGIN_URL . 'css/codemirror/css-hint.js', array( 'jquery' ) );
			}
		}
	}

	// Register Custom Post Type
	
	public function SLGF_CPT() {
		$labels = array(
			'name'               => esc_html__( 'Lightbox Gallery', 'simple-lightbox-gallery' ),
			'singular_name'      => esc_html__( 'Lightbox Gallery', 'simple-lightbox-gallery' ),
			'menu_name'          => esc_html__( 'S. Lightbox Gallery', 'simple-lightbox-gallery' ),
			'parent_item_colon'  => esc_html__( 'Parent Item:', 'simple-lightbox-gallery' ),
			'all_items'          => esc_html__( 'All Galleries', 'simple-lightbox-gallery' ),
			'view_item'          => esc_html__( 'View Gallery', 'simple-lightbox-gallery' ),
			'add_new_item'       => esc_html__( 'Add New Lightbox Gallery', 'simple-lightbox-gallery' ),
			'add_new'            => esc_html__( 'Add Lightbox Gallery', 'simple-lightbox-gallery' ),
			'edit_item'          => esc_html__( 'Edit Lightbox Gallery', 'simple-lightbox-gallery' ),
			'new_item'           => esc_html__( 'New Gallery', 'simple-lightbox-gallery' ),
			'update_item'        => esc_html__( 'Update Lightbox Gallery', 'simple-lightbox-gallery' ),
			'search_items'       => esc_html__( 'Search Gallery', 'simple-lightbox-gallery' ),
			'not_found'          => esc_html__( 'No Lightbox Gallery Found', 'simple-lightbox-gallery' ),
			'not_found_in_trash' => esc_html__( 'No Lightbox Gallery found in Trash', 'simple-lightbox-gallery' ),
		);

		$args = array(
			'label'               => 'slgf_slider',
			'description'         => esc_html__( 'Free Lightbox Gallery', 'simple-lightbox-gallery' ),
			'labels'              => $labels,
			'supports'            => array( 'title', 'thumbnail' ),
			'hierarchical'        => false,
			'public'              => false,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_nav_menus'   => true,
			'show_in_admin_bar'   => true,
			'menu_position'       => 10,
			'menu_icon'           => 'dashicons-format-gallery',
			'can_export'          => true,
			'has_archive'         => true,
			'exclude_from_search' => false,
			'publicly_queryable'  => false,
			'capability_type'     => 'post',
		);
		register_post_type( 'slgf_slider', $args );
		add_filter( 'manage_edit-slgf_slider_columns', array( &$this, 'slgf_gallery_columns' ) );
		add_action( 'manage_slgf_slider_posts_custom_column', array( &$this, 'slgf_gallery_manage_columns' ), 10, 2 );
	}


	// column fields on all galleries page
	function slgf_gallery_columns( $columns ) {
		$columns = array(
			'cb'        => '<input type="checkbox" />',
			'title'     => esc_html__( 'Gallery', 'simple-lightbox-gallery' ),
			'shortcode' => esc_html__( 'Gallery Shortcode', 'simple-lightbox-gallery' ),
			'author'    => esc_html__( 'Author', 'simple-lightbox-gallery' ),
			'date'      => esc_html__( 'Date', 'simple-lightbox-gallery' ),
		);

		return $columns;
	}

	// column action fields on all galleries page
	function slgf_gallery_manage_columns( $column, $post_id ) {
		global $post;
		switch ( $column ) {
			case 'shortcode':
				echo '<input type="text" value="[SLGF id=' . esc_html($post_id) . ']" readonly="readonly" />';
				break;
			default:
				break;
		}
	}

	// all metabox generator function
	public function add_all_slgf_meta_boxes() {
		add_meta_box(
			esc_html__( 'Add Images', 'simple-lightbox-gallery' ),
			esc_html__( 'Add Images', 'simple-lightbox-gallery' ),
			array(
				&$this,
				'slgf_generate_add_image_meta_box_function',
			),
			'slgf_slider',
			'normal',
			'low'
		);
		add_meta_box(
			esc_html__( 'Apply Setting On Lightbox Gallery', 'simple-lightbox-gallery' ),
			esc_html__( 'Apply Setting On Lightbox Gallery', 'simple-lightbox-gallery' ),
			array(
				&$this,
				'slgf_settings_meta_box_function',
			),
			'slgf_slider',
			'normal',
			'low'
		);
		add_meta_box(
			esc_html__( 'Lightbox Gallery Shortcode', 'simple-lightbox-gallery' ),
			esc_html__( 'Lightbox Gallery Shortcode', 'simple-lightbox-gallery' ),
			array(
				&$this,
				'slgf_shotcode_meta_box_function',
			),
			'slgf_slider',
			'side',
			'low'
		);

		add_meta_box(
			esc_html__( 'Rate us on WordPress', 'simple-lightbox-gallery' ),
			esc_html__( 'Rate us on WordPress', 'simple-lightbox-gallery' ),
			array(
				&$this,
				'slgf_rate_us_function',
			),
			'slgf_slider',
			'side',
			'low'
		);

		wp_enqueue_style( 'font-awesome-5', WEBLIZAR_SLGF_PLUGIN_URL . 'css/all.min.css' );
	}

	/**    Rate us **/
	function slgf_rate_us_function() {   ?>
		<div style="text-align:center">
			<h3><?php esc_html_e( 'If you like our plugin then please show us some love', 'simple-lightbox-gallery' ); ?></h3>
			<a class="wrg-rate-us" style="text-align:center; text-decoration: none;font:normal 30px;" href="http://wordpress.org/plugins/simple-lightbox-gallery/#reviews" target="_blank">
				<span class="dashicons dashicons-star-filled"></span>
				<span class="dashicons dashicons-star-filled"></span>
				<span class="dashicons dashicons-star-filled"></span>
				<span class="dashicons dashicons-star-filled"></span>
				<span class="dashicons dashicons-star-filled"></span>
			</a>
			<div class="upgrade-to-pro-demo" style="text-align:center;margin-bottom:10px;margin-top:10px;">
				<a href="https://wordpress.org/plugins/simple-lightbox-gallery/#reviews" target="_new" class="button button-primary button-hero"><?php esc_html_e( 'Click Here', 'simple-lightbox-gallery' ); ?></a>
			</div>
		</div>
		<?php
	}

	/**
	 * This function display Add New Image interface
	 * Also loads all saved gallery photos into photo gallery
	 */
	public function slgf_generate_add_image_meta_box_function( $post ) {
		?>

		<div id="rpggallery_container">

			<input type="hidden" id="slgf_wl_action" name="slgf_wl_action" value="wl-save-settings">
			<input type="hidden" name="slgf_nonce" value="<?php echo esc_attr( wp_create_nonce('slgf_nonce_action') ); ?>" />
			<ul id="slgf_gallery_thumbs" class="clearfix">
				<?php
				/* Load saved photos into gallery */
				$SLGF_AllPhotosDetails = json_decode( get_post_meta( $post->ID, 'slgf_all_photos_details', true ) );
				$TotalImages           = get_post_meta( $post->ID, 'slgf_total_images_count', true );
				$i                     = 0;
				
				if ( $TotalImages ) {
					foreach ( $SLGF_AllPhotosDetails as $SLGF_SinglePhotoDetails ) {
						$name          = $SLGF_SinglePhotoDetails->slgf_image_label;
						$UniqueString  = substr( str_shuffle( 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ' ), 0, 5 );
						$url           = $SLGF_SinglePhotoDetails->slgf_image_url;
						$slgf_image_id = $SLGF_SinglePhotoDetails->slgf_image_id;
						$url1          = $SLGF_SinglePhotoDetails->slgf_12_thumb;
						$url2          = $SLGF_SinglePhotoDetails->slgf_346_thumb;
						$url3          = $SLGF_SinglePhotoDetails->slgf_12_same_size_thumb;
						$url4          = $SLGF_SinglePhotoDetails->slgf_346_same_size_thumb;
						$img_desc      = $SLGF_SinglePhotoDetails->img_desc;
						?>
						<li class="rpg-image-entry" id="rpg_img">
						<a class="gallery_remove lbsremove_bt" href="#gallery_remove" id="lbs_remove_bt"><img src="<?php echo esc_url(WEBLIZAR_SLGF_PLUGIN_URL . 'images/Close-icon-new.png'); ?>" /></a>
							<img src="<?php echo esc_url( $url ); ?>" class="rpg-meta-image" alt="">
							<input type="button" id="upload-background-<?php echo esc_attr( $UniqueString ); ?>" name="upload-background-<?php echo esc_attr( $UniqueString ); ?>" value="Upload Image" class="button-primary " onClick="weblizar_image('<?php echo esc_attr( $UniqueString ); ?>')" />
							<input type="text" id="slgf_image_label[]" name="slgf_image_label[]" value="<?php echo esc_attr( html_entity_decode( $name, ENT_QUOTES, 'UTF-8' ) ); ?>" placeholder="Enter Image Label" class="rpg_label_text">
							<input type="hidden" id="slgf_image_id[]" name="slgf_image_id[]" value="<?php echo esc_attr( $slgf_image_id ); ?>">

							<input type="text" id="slgf_image_url[]" name="slgf_image_url[]" class="rpg_label_text" value="<?php echo esc_url( $url ); ?>" readonly="readonly" style="display:none;" />
							<input type="text" id="slgf_image_url1[]" name="slgf_image_url1[]" class="rpg_label_text" value="<?php echo esc_url( $url1 ); ?>" readonly="readonly" style="display:none;" />
							<input type="text" id="slgf_image_url2[]" name="slgf_image_url2[]" class="rpg_label_text" value="<?php echo esc_url( $url2 ); ?>" readonly="readonly" style="display:none;" />
							<input type="text" id="slgf_image_url3[]" name="slgf_image_url3[]" class="rpg_label_text" value="<?php echo esc_url( $url3 ); ?>" readonly="readonly" style="display:none;" />
							<input type="text" id="slgf_image_url4[]" name="slgf_image_url4[]" class="rpg_label_text" value="<?php echo esc_url( $url4 ); ?>" readonly="readonly" style="display:none;" />


							<label><?php esc_html_e( 'Description', 'simple-lightbox-gallery' ); ?></label>
							<textarea name="img_desc[]" id="img_desc[]" placeholder="<?php esc_html_e( 'Description', 'simple-lightbox-gallery' ); ?>" class="gal_richeditbox_<?php echo esc_attr( $i ); ?>"><?php echo esc_textarea( $img_desc ); ?></textarea>
						</li>
						<?php

					} // end of foreach
				} else {
					$TotalImages = 0;
				}
				?>
			</ul>
		</div>

		<!--Add New Image Button-->
		<div class="add_del_buttons">
			<div class="rpg-image-entry add_rpg_new_image" id="slgf_gallery_upload_button" data-uploader_title="Upload Image" data-uploader_button_text="Select">
				<div class="dashicons dashicons-plus"></div>
				<p>
					<?php esc_html_e( 'Add New Images', 'simple-lightbox-gallery' ); ?>
				</p>
			</div>

			<div class="rpg-image-entry del_rpg_image" id="slgf_delete_all_button">
				<div class="dashicons dashicons-trash"></div>
				<p>
					<?php esc_html_e( 'Delete All', 'simple-lightbox-gallery' ); ?>
				</p>
			</div>

		</div>

		<div style="clear:left;"></div>

		<p><strong><?php esc_html_e( 'Tips', 'simple-lightbox-gallery' ); ?>
				:</strong> <?php esc_html_e( 'Plugin crop images with same size thumbnails', 'simple-lightbox-gallery' ); ?>
			. <?php esc_html_e( 'So please upload all gallery images using Add New Image button', 'simple-lightbox-gallery' ); ?>
			. <?php esc_html_e( 'Do not use or add pre uploaded images which are uploaded previously using Media or Post or Page', 'simple-lightbox-gallery' ); ?>
			.</p><?php esc_html_e( 'Show Us Some Love', 'simple-lightbox-gallery' ); ?>
		(<?php esc_html_e( 'Rate Us', 'simple-lightbox-gallery' ); ?>) &nbsp;
		<a class="wrg-rate-us2" style="text-align:center; text-decoration: none;font:normal 30px;" href="<?php echo esc_url('http://wordpress.org/plugins/simple-lightbox-gallery/#reviews'); ?>" target="_blank">
			<span class="dashicons dashicons-star-filled"></span>
			<span class="dashicons dashicons-star-filled"></span>
			<span class="dashicons dashicons-star-filled"></span>
			<span class="dashicons dashicons-star-filled"></span>
			<span class="dashicons dashicons-star-filled"></span>
		</a>

		<?php
	}

	/**
	 * This function display Add New Image interface
	 * Also loads all saved gallery photos into Lightbox gallery
	 */
	public function slgf_settings_meta_box_function( $post ) {
		require_once 'simple-lightbox-slider-setting-metabox.php';
	}

	public function slgf_shotcode_meta_box_function() {
		?>
		<p><?php esc_html_e( 'Use below shortcode in any Page/Post to publish your photo gallery', WEBLIZAR_SLGF_PLUGIN_URL ); ?></p>
		<input id="slgf_shortcode_field" readonly="readonly" type="text" value="<?php echo esc_attr('[SLGF id=' . get_the_ID() . ']'); ?>">
		<button id="slgf_btn_copy" type="button" class="wp-core-ui button-primary"><?php esc_html_e( 'Copy', WEBLIZAR_SLGF_PLUGIN_URL ); ?></button>

		<?php
	}

	public function admin_thumb( $id ) {
		// echo $id;
		$image        = wp_get_attachment_image_src( $id, 'lightboxslider_admin_medium', true );
		$image1       = wp_get_attachment_image_src( $id, 'slgf_12_thumb', true );
		$image2       = wp_get_attachment_image_src( $id, 'slgf_346_thumb', true );
		$image3       = wp_get_attachment_image_src( $id, 'slgf_12_same_size_thumb', true );
		$image4       = wp_get_attachment_image_src( $id, 'slgf_346_same_size_thumb', true );
		$UniqueString = substr( str_shuffle( 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ' ), 0, 5 );
		?>
		<li class="rpg-image-entry" id="rpg_img">
			<a class="gallery_remove lbsremove_bt" href="#gallery_remove" id="lbs_remove_bt">
			<img src="<?php echo esc_url(WEBLIZAR_SLGF_PLUGIN_URL . 'images/Close-icon-new.png'); ?>" /></a>
			<img src="<?php echo esc_url( $image[0] ); ?>" class="rpg-meta-image" alt="">
			<input type="button" id="upload-background-<?php echo esc_attr( $UniqueString ); ?>" name="upload-background-<?php echo esc_attr( $UniqueString ); ?>" value="Upload Image" class="button-primary " onClick="weblizar_image('<?php echo esc_attr( $UniqueString ); ?>')" />
			<input type="text" id="slgf_image_label[]" name="slgf_image_label[]" placeholder="Enter Image Label" class="rpg_label_text">
			<input type="hidden" id="slgf_image_id[]" name="slgf_image_id[]" value="<?php echo esc_attr( $id ); ?>">
			<input type="text" id="slgf_image_url[]" name="slgf_image_url[]" class="rpg_label_text" value="<?php echo esc_url( $image[0] ); ?>" readonly="readonly" style="display:none;" />
			<input type="text" id="slgf_image_url1[]" name="slgf_image_url1[]" class="rpg_label_text" value="<?php echo esc_url( $image1[0] ); ?>" readonly="readonly" style="display:none;" />
			<input type="text" id="slgf_image_url2[]" name="slgf_image_url2[]" class="rpg_label_text" value="<?php echo esc_url( $image2[0] ); ?>" readonly="readonly" style="display:none;" />
			<input type="text" id="slgf_image_url3[]" name="slgf_image_url3[]" class="rpg_label_text" value="<?php echo esc_url( $image3[0] ); ?>" readonly="readonly" style="display:none;" />
			<input type="text" id="slgf_image_url4[]" name="slgf_image_url4[]" class="rpg_label_text" value="<?php echo esc_url( $image4[0] ); ?>" readonly="readonly" style="display:none;" />
			<label><?php esc_html_e( 'Description', 'simple-lightbox-gallery' ); ?></label>
			<textarea name="img_desc[]" id="img_desc[]" placeholder="<?php esc_html_e( 'Description', 'simple-lightbox-gallery' ); ?>" class="gal_richeditbox_<?php echo esc_attr( $id ); ?>"></textarea>

		</li>
		<?php
	}

	public function ajax_get_thumbnail_slgf() {
		// Check if our nonce is set.
		if ( ! isset( $_POST['slgf_nonce'] ) ) {
			die( 'Missing nonce.' );
		}
	
		// Verify that the nonce is valid.
		if ( ! wp_verify_nonce( $_POST['slgf_nonce'], 'slgf_nonce_action' ) ) {
			die( 'Invalid nonce.' );
		}
	
		echo esc_html( $this->admin_thumb( $_POST['imageid'] ) );
		die;
	}

	public function slgf_add_image_meta_box_save( $PostID ) {
		
		if ( isset( $PostID ) && isset( $_POST['slgf_wl_action'] ) ) {
			$TotalImages = isset( $_POST['slgf_image_url'] ) ? count($_POST['slgf_image_url']) : '';
			$ImagesArray = array();			
			if ( $TotalImages ) {
				for ( $i = 0; $i < $TotalImages; $i++ ) {
					$url         = esc_url_raw( $_POST['slgf_image_url'][ $i ] );
					$url1        = esc_url_raw( $_POST['slgf_image_url1'][ $i ] );
					$url2        = esc_url_raw( $_POST['slgf_image_url2'][ $i ] );
					$url3        = esc_url_raw( $_POST['slgf_image_url3'][ $i ] );
					$url4        = esc_url_raw( $_POST['slgf_image_url4'][ $i ] );
					$img_desc    = sanitize_text_field( $_POST['img_desc'][ $i ] );
					$image_id    = sanitize_text_field( $_POST['slgf_image_id'][ $i ] );
					$image_label = sanitize_text_field( $_POST['slgf_image_label'][ $i ] );

					$ImagesArray[] = array(
						'slgf_image_label'         => $image_label,
						'slgf_image_id'            => $image_id,
						'slgf_image_url'           => $url,
						'slgf_12_thumb'            => $url1,
						'slgf_346_thumb'           => $url2,
						'slgf_12_same_size_thumb'  => $url3,
						'slgf_346_same_size_thumb' => $url4,
						'img_desc'                 => $img_desc,
					);
				}				
				update_post_meta( $PostID, 'slgf_all_photos_details', json_encode( $ImagesArray ) );
				update_post_meta( $PostID, 'slgf_total_images_count', $TotalImages );
			} else {
				$TotalImages = 0;
				update_post_meta( $PostID, 'slgf_total_images_count', $TotalImages );
				$ImagesArray = array();
				update_post_meta( $PostID, 'slgf_all_photos_details', json_encode( $ImagesArray ) );
			}
		}
		// die;
	}
	// save settings meta box values
	public function slgf_settings_meta_save( $PostID ) {
		if ( isset( $PostID ) && isset( $_POST['slgf_save_action'] ) && isset( $_POST['security'] ) ) {
			if ( ! wp_verify_nonce( $_POST['security'], 'nonce_save_settings_option' ) ) {
				die();
			}
			$SLGF_Show_Gallery_Title  = sanitize_text_field( $_POST['wl-show-gallery-title'] );
			$SLGF_Show_Image_Label    = sanitize_text_field( $_POST['wl-show-image-label'] );
			$SLGF_Hover_Animation     = sanitize_text_field( $_POST['wl-hover-animation'] );
			$SLGF_Gallery_Layout      = sanitize_text_field( $_POST['wl-gallery-layout'] );
			$SLGF_Thumbnail_Layout    = sanitize_text_field( $_POST['wl-thumbnail-layout'] );
			$SLGF_Hover_Color         = sanitize_hex_color( $_POST['wl-hover-color'] );
			$SLGF_Text_BG_Color       = sanitize_hex_color( $_POST['wl-text-bg-color'] );
			$SLGF_Text_Color          = isset( $_POST['wl-text-color'] ) ? sanitize_hex_color( $_POST['wl-text-color'] ) : '';
			$lk_show_img_desc         = sanitize_text_field( $_POST['lk_show_img_desc'] );
			$SLGF_Hover_Color_Opacity = sanitize_text_field( $_POST['wl-hover-color-opacity'] );
			$SLGF_Font_Style          = sanitize_text_field( $_POST['wl-font-style'] );
			$SLGF_Box_Shadow          = sanitize_text_field( $_POST['wl-box-Shadow'] );
			$SLGF_Custom_CSS          = wp_strip_all_tags( $_POST['wl-custom-css'] );
			$SLGF_open_link = isset($_POST['SLGF_open_link']) ? sanitize_text_field($_POST['SLGF_open_link']) : null;
			$SLGF_label_Color         = sanitize_hex_color( $_POST['SLGF_label_Color'] );
			$SLGF_desc_font_Color     = sanitize_hex_color( $_POST['SLGF_desc_font_Color'] );
			$SLGF_btn_Color           = sanitize_hex_color( $_POST['SLGF_btn_Color'] );
			$SLGF_btn_font_Color      = sanitize_hex_color( $_POST['SLGF_btn_font_Color'] );
			$SLGF_button_title        = sanitize_text_field( $_POST['SLGF_button_title'] );
			$SLGF_Light_Box           = sanitize_text_field( $_POST['SLGF_Light_Box'] );
			$SLGF_title_Color         = sanitize_hex_color( $_POST['SLGF_title_Color'] );
			
			$SLGF_DefaultSettingsArray = json_encode( 
				array(
					'SLGF_Show_Gallery_Title'  => $SLGF_Show_Gallery_Title,
					'SLGF_Show_Image_Label'    => $SLGF_Show_Image_Label,
					'SLGF_Hover_Animation'     => $SLGF_Hover_Animation,
					'SLGF_Gallery_Layout'      => $SLGF_Gallery_Layout,
					'SLGF_Thumbnail_Layout'    => $SLGF_Thumbnail_Layout,
					'SLGF_Hover_Color'         => $SLGF_Hover_Color,
					'SLGF_Text_BG_Color'       => $SLGF_Text_BG_Color,
					'SLGF_Text_Color'          => $SLGF_Text_Color,
					'SLGF_Hover_Color_Opacity' => $SLGF_Hover_Color_Opacity,
					'SLGF_Font_Style'          => $SLGF_Font_Style,
					'lk_show_img_desc'         => $lk_show_img_desc,
					'SLGF_Box_Shadow'          => $SLGF_Box_Shadow,
					'SLGF_Custom_CSS'          => $SLGF_Custom_CSS,
					'SLGF_open_link'           => $SLGF_open_link,
					'SLGF_label_Color'         => $SLGF_label_Color,
					'SLGF_desc_font_Color'     => $SLGF_desc_font_Color,
					'SLGF_btn_Color'           => $SLGF_btn_Color,
					'SLGF_btn_font_Color'      => $SLGF_btn_font_Color,
					'SLGF_button_title'        => $SLGF_button_title,
					'SLGF_Light_Box'           => $SLGF_Light_Box,
					'SLGF_title_Color'         => $SLGF_title_Color,
				)
			);

			$SLGF_Gallery_Settings = 'SLGF_Gallery_Settings_' . $PostID;
			update_post_meta( $PostID, $SLGF_Gallery_Settings, $SLGF_DefaultSettingsArray );
		}
	}
}

/**
 * Initialize Class with Object
 */
$SLGF = new SLGF();

/**
 * Lightbox Slider Pro Short Code [SLGF]
 */
require_once 'simple-lightbox-slider-shortcode.php';

/**
 * Hex Color code to RGB Color Code converter function
 */
if ( ! function_exists( 'SLGF_RPGhex2rgb' ) ) {
	function SLGF_RPGhex2rgb( $hex ) {
		$hex = str_replace( '#', '', $hex );

		if ( strlen( $hex ) == 3 ) {
			$r = hexdec( substr( $hex, 0, 1 ) . substr( $hex, 0, 1 ) );
			$g = hexdec( substr( $hex, 1, 1 ) . substr( $hex, 1, 1 ) );
			$b = hexdec( substr( $hex, 2, 1 ) . substr( $hex, 2, 1 ) );
		} else {
			$r = hexdec( substr( $hex, 0, 2 ) );
			$g = hexdec( substr( $hex, 2, 2 ) );
			$b = hexdec( substr( $hex, 4, 2 ) );
		}
		$rgb = array( $r, $g, $b );

		return $rgb; // returns an array with the rgb values
	}
}

add_action( 'media_buttons', 'add_slgf_custom_button' );
function add_slgf_custom_button() {
    $context     = null;
    $img         = plugins_url( '/images/Photos-icon.png', __FILE__ );
    $container_id = 'SLGF';
    $context     .= '<a class="button button-primary thickbox" title="' . esc_attr__( 'Select Lightbox Gallery to insert into post', 'simple-lightbox-gallery' ) . '" href="#TB_inline?width=400&inlineId=' . esc_attr( $container_id ) . '">
    <span class="wp-media-buttons-icon" style="background: url(' . esc_url( $img ) . '); background-repeat: no-repeat; background-position: left bottom;"></span>
    ' . esc_html__( 'Simple Lightbox Gallery Shortcode', 'simple-lightbox-gallery' ) . '</a>';

    echo wp_kses( $context, array(
        'a' => array(
            'class' => array(),
            'title' => array(),
            'href' => array(),
        ),
        'span' => array(
            'class' => array(),
            'style' => array(),
        ),
    ) );
}

add_action( 'admin_footer', 'add_slgf_inline_popup_content' );
function add_slgf_inline_popup_content() {
	?>

	<?php
	wp_register_script( 'slg_script', false );
	wp_enqueue_script( 'slg_script' );
	$js = ' ';
	ob_start();
	?>
	jQuery(document).ready(function () {
		jQuery('#slgfgalleryinsert').on('click', function () {
			var id = $('#slgf-gallery-select option:selected').val();			
			window.send_to_editor('<p>[SLGF id=' + id + ']</p>');
			tb_remove();
		})
	});
	<?php
	$js .= ob_get_clean();
	wp_add_inline_script( 'slg_script', $js );
	?>

	<div id="SLGF" style="display:none;">
		<h3>
			<?php esc_html_e( 'Select Lightbox Gallery To Insert Into Post', 'simple-lightbox-gallery' ); ?>
		</h3>
		<?php
		$all_posts = wp_count_posts( 'slgf_slider' )->publish;
		$args      = array(
			'post_type'      => 'slgf_slider',
			'posts_per_page' => $all_posts,
		);
		global $rpg_galleries;
		$rpg_galleries = new WP_Query( $args );
		if ( $rpg_galleries->have_posts() ) {
			?>
			<select id="slgf-gallery-select">
			<?php
			while ( $rpg_galleries->have_posts() ) :
				$rpg_galleries->the_post();
				?>
				<option value="<?php echo esc_attr( get_the_ID() ); ?>"><?php echo esc_html( get_the_title() ); ?></option>
			<?php endwhile; ?>
			</select>
			<button class='button primary' id='slgfgalleryinsert'>
				<?php esc_html_e( 'Insert Gallery Shortcode', 'simple-lightbox-gallery' ); ?>
			</button>
			</button>
			<?php
		} else {
			esc_html_e( 'No Gallery Found', 'simple-lightbox-gallery' );
		}
		?>
	</div>
	<?php
}

// Add settings link on plugin page
$slgf_plugin_name = plugin_basename( __FILE__ );
add_filter( "plugin_action_links_$slgf_plugin_name", 'as_settings_link_slgf' );
function as_settings_link_slgf( $links ) {
	$as_settings_link1 = '<a href="https://weblizar.com/lightbox-slider-pro/" style="font-weight:700; color:#e35400" target="_blank">' . esc_html__( 'Get Premium', 'simple-lightbox-gallery' ) . '</a>';
	$as_settings_link2 = '<a href="edit.php?post_type=slgf_slider">' . esc_html__( 'Settings', 'simple-lightbox-gallery' ) . '</a>';
	array_unshift( $links, $as_settings_link1, $as_settings_link2 );

	return $links;
}
?>