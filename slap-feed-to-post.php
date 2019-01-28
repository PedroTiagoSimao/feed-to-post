<?php
/**
 * Plugin Name:  SLAP - Feed to Post
 * Description:  Obtém posts de outro site. Usar o shortcode slap_get_posts_via_rest para visualizar os posts. Requere SLAP - REST API Images na fonte
 * Plugin URI:   https://slap.pt
 * Author:       Pedro Simão
 * Version:      1.0.1
 * Text Domain:  slapfeedtopost
 * License:      GPL v2 or later
 * License URI:  https://www.gnu.org/licenses/gpl-2.0.txt
 *
 * @package slapfeedtopost
 */
// Disable direct file access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function slap_scripts(){
    // wp_enqueue_style( 'slap_bootstrap_css', plugin_dir_url(__FILE__) . 'css/bootstrap.min.css' );
    // wp_enqueue_script( 'slap_bootstrap_js', plugin_dir_url(__FILE__) . 'js/bootstrap.min.js' );
}
add_action ('wp_enqueue_scripts', 'slap_scripts');

/* INICIO DE PAGINA DE OPÇÕES */

function sftp_add_options_link(){
    add_options_page('SLAP - Feed to Post', 'Feed to Post', 'manage_options', 'sftp-options', 'sftp_options_page');
}

add_action('admin_menu', 'sftp_add_options_link');

// retrieve our plugin settings from the options table
$sftp_options = get_option('sftp_settings');

function sftp_options_page(){

    global $sftp_options;

    ob_start(); ?>
	<div class="wrap">
		<h2>SLAP - Feed to Post</h2>
		<form method="post" action="options.php">
			<?php settings_fields('sftp_settings_group'); ?>

            <!-- <?php var_dump($sftp_options); ?> -->
 
			<h4><?php _e('Opções do Feed de Notícias', 'sftp_domain'); ?></h4>
            <table>
                <tr>
                    <td>SHORTCODE</td>
                    <td><input type="text" value="[slap_get_posts_via_rest]" style="width: 200px" readonly/></td>
                    <td></td>
                </tr>
                <tr>
                    <td>
                        <label class="description" for="sftp_settings[posts_feed_url]"><?php _e('Feed de Notícias', 'sftp_domain'); ?></label>
                    </td>
                    <td>
                        <input type="text" id="sftp_settings[posts_feed_url]" name="sftp_settings[posts_feed_url]" value="<?php echo $sftp_options['posts_feed_url']; ?>" style="width: 600px"/>
                    </td>
                    <td style="font-size:9px">http://URL/wp-json/wp/v2/posts?filter[posts_per_page]=-1</td>
                </tr>
                <tr>
                    <td><label class="description" for="sftp_settings[posts_to_show]"><?php _e('Número de Notícias', 'sftp_domain'); ?></label></td>
                    <td>
                        <?php $posts_to_show = $sftp_options['posts_to_show']; ?>
                        <select id="sftp_settings[posts_to_show]" name="sftp_settings[posts_to_show]">
                            <option value='3' <?php echo $posts = $posts_to_show == 3 ? 'selected' : ''; ?>>3</option>
                            <option value='6' <?php echo $posts = $posts_to_show == 6 ? 'selected' : ''; ?>>6</option>
                            <option value='9' <?php echo $posts = $posts_to_show == 9 ? 'selected' : ''; ?>>9</option>
                        </select>
                    </td>
                </tr>
            </table>
            <p>
                
                
            </p>
            <!-- <p>
                <label class="description" for="sftp_settings[posts_category]"><?php _e('Categoria Única (Deixe em branco para todas)', 'sftp_domain'); ?></label>
                <input type="text" id="sftp_settings[posts_category]" name="sftp_settings[posts_category]" value="<?php echo $sftp_options['posts_category']; ?>"/>
            </p> -->
			<p>
				
				
			</p>
 
			<p class="submit">
				<input type="submit" class="button-primary" value="<?php _e('Guardar', 'sftp_domain'); ?>" />
			</p>
 
		</form>
	</div>
	<?php
	echo ob_get_clean();
}

function sftp_register_settings() {
	// creates our settings in the options table
	register_setting('sftp_settings_group', 'sftp_settings');
}
add_action('admin_init', 'sftp_register_settings');

/* FIM DE PAGINA DE OPÇÕES */

/**
 * Get posts via REST API.
 */
function get_posts_via_rest() {

    global $sftp_options;

    // it will be the array with all posts
    $posts = array();

    // get the first website latest posts
    // $id_token = get_user_meta( 'Boss', 'auth0_id_token', true); 
    // $args = array(
    //     'headers'     => array(
    //         'Authorization' => 'Basic ' . base64_encode( "Boss:kreative@2015" ),
    //         "sslverify" => false
    //     ),
    // );

    //$remote_url = 'http://www.jornal.kreative.pt/wp-json/wp/v2/posts?filter[posts_per_page]=-1&_embed';
    $remote_url = $sftp_options['posts_feed_url'];

    $blog = wp_remote_get( $remote_url/*, $args*/ );

    $blog_posts = json_decode( $blog['body'] ); // our posts are here
        foreach( $blog_posts as $blog_post ) {

            $posts [] = $blog_post;

        }

    // if( !is_wp_error( $blog )  ) {
    //     $blog_posts = json_decode( $blog['body'] ); // our posts are here
    //     foreach( $blog_posts as $blog_post ) {

    //         $posts[ strtotime( $blog_post->date_gmt ) ] = $blog_post;

    //     }
    // }

    // $remote_url = 'http://www.jornal.kreative.pt/wp-json/wp/v2/posts';

    // $blog2 = wp_remote_get( $remote_url/*, $args*/ );

    // if( !is_wp_error( $blog2 ) && $blog2['response']['code'] == 200 ) {
    //     $blog_posts = json_decode( $blog['body'] ); // our posts are here
    //     foreach( $blog_posts as $blog_post ) {

    //         // I decided to create array like $allposts[1504838841] = Object
    //         $posts[ strtotime( $blog_post->date_gmt ) ] = $blog_post;

    //     }
    // }

    // sort array by keys in descending order
    // krsort( $posts );

    // print posts

    $months = array(
    1 => 'Janeiro',
    2 => 'Fevereiro',
    3 => 'Março',
    4 => 'Abril',
    5 => 'Maio',
    6 => 'Junho',
    7 => 'Julho',
    8 => 'Agosto',
    9 => 'Setembro',
    10 => 'Outubro',
    11 => 'Novembro',
    12 => 'Dezembro'
    );

    echo "<div class='row'>";

    $i = 0;
    foreach( $posts as $blog_post ) {
        $day = date( 'd', strtotime( $blog_post->modified ) );
        $month = idate( 'm', strtotime( $blog_post->modified ) );

        $current_month = $months[$month];

        $year = date( 'Y', strtotime( $blog_post->modified ) ); 
        echo "<div class='col s4 m4' style='margin-bottom:20px'>";
            echo "<a href='" . $blog_post->link . "' target='_blank'><h4 style='height:80px'>" . $blog_post->title->rendered . "</h4></a>";
            echo "<p>" . $day . " de " . $current_month . " de " . $year . "</p>";
            echo "<p style='text-align: justify'>" . wp_strip_all_tags($blog_post->excerpt->rendered) . "</p>";
            if($blog_post->featured_media != 0) {
                echo "<img src='" . $blog_post->better_featured_image->media_details->sizes->large->source_url . "' alt='" . $blog_post->title->rendered . "'/><br>";
            }
        echo "</div>";

        $i++;

        if($i == $sftp_options['posts_to_show']) {
            break;
        }
    }

    echo "</div>";
}
// Register as a shortcode to be used on the site.
add_shortcode( 'slap_get_posts_via_rest', 'get_posts_via_rest' );