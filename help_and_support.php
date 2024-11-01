<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}?>
<div>
    <center><h2 class="head_title"><?php esc_html_e( 'Simple Lightbox Gallery', 'simple-lightbox-gallery' ); ?></h2></center>
    <center>
        <h3 class="head_desc"><?php esc_html_e( 'plugin is allow users to view larger versions of images,', 'simple-lightbox-gallery' ); ?><?php esc_html_e( 'simple slide shows and Gallery view with grid layout', 'simple-lightbox-gallery' ); ?>
            .</h3>
    </center>
</div>

<p class="well"><?php esc_html_e( 'Rate Us', 'simple-lightbox-gallery' ); ?></p>
<h4 class="para"><?php esc_html_e( 'If you are enjoying using our', 'simple-lightbox-gallery' ); ?> <b><?php esc_html_e( 'Simple Lightbox Gallery', 'simple-lightbox-gallery' ); ?>
    </b> <?php esc_html_e( 'plugin and find it useful', 'simple-lightbox-gallery' ); ?>
    , <?php esc_html_e( 'then please consider writing a positive feedback', 'simple-lightbox-gallery' ); ?>
    . <?php esc_html_e( 'Your feedback will help us to encourage and support the plugin continued development and better user support', 'simple-lightbox-gallery' ); ?>
    .
</h4>
<div class="star_rate">
    <a class="acl-rate-us" href="https://wordpress.org/plugins/simple-lightbox-gallery/#reviews" target="_blank">
        <span class="dashicons dashicons-star-filled"></span>
        <span class="dashicons dashicons-star-filled"></span>
        <span class="dashicons dashicons-star-filled"></span>
        <span class="dashicons dashicons-star-filled"></span>
        <span class="dashicons dashicons-star-filled"></span>
    </a>
</div>
<p class="well"><?php esc_html_e( 'Share Us Your Suggestion', 'simple-lightbox-gallery' ); ?></p>
<h4 class="para"><?php esc_html_e( 'If you have any suggestion or features in your mind then please share us', 'simple-lightbox-gallery' ); ?>
    . <?php esc_html_e( 'We will try our best to add them in this plugin', 'simple-lightbox-gallery' ); ?>.</h4>

<p class="well"><?php esc_html_e( 'Language Contribution', 'simple-lightbox-gallery' ); ?></p>
<h4 class="para"><?php esc_html_e( 'Translate this plugin into your language', 'simple-lightbox-gallery' ); ?></h4>
<h4 class="para"><span class="list_point"><?php esc_html_e( 'Question', 'simple-lightbox-gallery' ); ?></span>
    : <?php esc_html_e( 'How to convert Plugin into My Language ', 'simple-lightbox-gallery' ); ?>?</h4>
<h4 class="para"><span class="list_point"><?php esc_html_e( 'Answer', 'simple-lightbox-gallery' ); ?></span>
    : <?php esc_html_e( 'Contact as to', 'simple-lightbox-gallery' ); ?>
     <?php esc_html_e( 'lizarweb@gmail.com', 'simple-lightbox-gallery' ); ?><?php esc_html_e( ' for translate this plugin into your language', 'simple-lightbox-gallery' ); ?>.</h4>

<div class="chang_url">

    <h2><?php esc_html_e( "Change Old Server Image URL", 'simple-lightbox-gallery' ); ?>
    </h2>
    <form action="" method="post">
        <?php $nonce = wp_create_nonce( 'nonce_sslgfchangeurl_option' ); ?>
                         <input type="hidden" name="security" value="<?php echo esc_attr( $nonce ); ?>">
        <input type="submit" value="Change image URL" name="slgfchangeurl" class="btn btn-primary btn-lg">

        <h6>
            <b><?php esc_html_e( "Note :", 'simple-lightbox-gallery' ); ?></b> <?php esc_html_e( "Use this option after import", 'simple-lightbox-gallery' ); ?> 
            <b><?php esc_html_e( "Simple Lightbox Gallery", 'simple-lightbox-gallery' ); ?></b> <?php esc_html_e( "to change old server image url to new server image url", 'simple-lightbox-gallery' ); ?>.
        </h6>
    </form>
</div>

<?php
if ( isset( $_REQUEST['slgfchangeurl'] ) && isset( $_REQUEST['security'] )) {
    if ( ! wp_verify_nonce( $_POST['security'], 'nonce_sslgfchangeurl_option' ) ) {
        die();}
    $all_posts = wp_count_posts( 'slgf_slider' )->publish;
    $args      = array( 'post_type' => 'slgf_slider', 'posts_per_page' => $all_posts );
    global $rpg_galleries;
    $rpg_galleries = new WP_Query( $args );

    while ( $rpg_galleries->have_posts() ) : $rpg_galleries->the_post();

        $SLGF_Id               = get_the_ID();
        $SLGF_AllPhotosDetails = json_decode( get_post_meta( $SLGF_Id, 'slgf_all_photos_details', true ) );
        $TotalImages           = get_post_meta( $SLGF_Id, 'slgf_total_images_count', true );
        if ( $TotalImages ) {
            foreach ( $SLGF_AllPhotosDetails as $SLGF_SinglePhotoDetails ) {
                
                $name    = $SLGF_SinglePhotoDetails->slgf_image_label;
                $url     = $SLGF_SinglePhotoDetails->slgf_image_url;
                $url1    = $SLGF_SinglePhotoDetails->slgf_12_thumb;
                $url2    = $SLGF_SinglePhotoDetails->slgf_346_thumb;
                $url3    = $SLGF_SinglePhotoDetails->slgf_12_same_size_thumb;
                $url4    = $SLGF_SinglePhotoDetails->slgf_346_same_size_thumb;
                $img_desc = $SLGF_SinglePhotoDetails->img_desc;
                

                $upload_dir = wp_upload_dir();
                $data       = $url;
                if ( strpos( $data, 'uploads' ) !== false ) {
                    list( $oteher_path, $image_path ) = explode( "uploads", $data );
                    $url = $upload_dir['baseurl'] . $image_path;
                }

                $data = $url1;
                if ( strpos( $data, 'uploads' ) !== false ) {
                    list( $oteher_path, $image_path ) = explode( "uploads", $data );
                    $url1 = $upload_dir['baseurl'] . $image_path;
                }

                $data = $url2;
                if ( strpos( $data, 'uploads' ) !== false ) {
                    list( $oteher_path, $image_path ) = explode( "uploads", $data );
                    $url2 = $upload_dir['baseurl'] . $image_path;
                }

                $data = $url3;
                if ( strpos( $data, 'uploads' ) !== false ) {
                    list( $oteher_path, $image_path ) = explode( "uploads", $data );
                    $url3 = $upload_dir['baseurl'] . $image_path;
                }

                $data = $url4;
                if ( strpos( $data, 'uploads' ) !== false ) {
                    list( $oteher_path, $image_path ) = explode( "uploads", $data );
                    $url4 = $upload_dir['baseurl'] . $image_path;
                }

                $ImagesArray[] = array(
                    'slgf_image_label'         => $name,
                    'slgf_image_url'           => $url,
                    'slgf_12_thumb'            => $url1,
                    'slgf_346_thumb'           => $url2,
                    'slgf_12_same_size_thumb'  => $url3,
                    'slgf_346_same_size_thumb' => $url4,
					'img_desc'           	   => $img_desc
                );
            }
            update_post_meta( $SLGF_Id, 'slgf_all_photos_details', serialize( $ImagesArray ) );
            $ImagesArray = "";
        }
    endwhile;
}
?>
