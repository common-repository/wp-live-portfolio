<?php
/**
 * Plugin Name: WP Live Portfolio
 * Plugin URI: http://www.eternitywebsolutions.com
 * Description: Showcase your website design work and website demos from the live URL. Plugin shows desktop, tab and mobile view of the live link. 
 * Version: 1.0.0
 * Author: Eternity Web Solutions
 * Author URI: http://www.somnathjadhav.com
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: wp-live-portfolio
**/
?>
<?php

// Custom Post Type 

function wp_custom_portfolio_type()
{
	$wp_portfolio_labels=array(
								'name' => _x('Portfolio','General Name'),
								'singular_name' => _x('Portfolio','Singular Name'),
								'menu_name' => __('Portfolio'),
								'all_items' => __('All Portfolio'),
								'add_new_item' => __('Add New Portfolio'),
								'add_new' => __('Add Portfolio'),
								'new_item' => __('New Item'),
								'edit_item' => __('Edit Portfolio'),
								'update_item' => __('Update Portfolio'),
								'view_item' => __('View Portfolio'),
								'view_items' =>  __('View Portfolio'),
								'parent_item_colon' => __('Parent Portfolio'),
								'search_items' => __('Search Portfolio'),
								'not_found' => __('Not Found'),
								'not_found_in_trash' => __('Not Found in Trash'),
								'featured_image' => __('Featured Image'),
								'set_featured_image' => __('Set Featured Image'),
								'remove_featured_image' => __('Remove Featured Image'),
								'use_featured_image' => __('Use as Featured Images'),
								'insert_into_item' => __('Insert into item'),
								'upload_to_this_item' => __('Upload to this item'),
								'items_list' => __('Items list'),
								'itens_list_navigation' => __('Items list navigation'),
								'filter_items_list' => __('Filter items list')
								);
								
	$wp_portfolio_args=array(
								'label' => __('portfolio'),
								'description' => __('Website Portfolio'),
								'labels' => $wp_portfolio_labels,
								'supports' => array('title','editor','thumbnail'),
								'taxonomies' => array('eternity_portfolio'),
								'hierarchical' => false,
								'public' => true,
								'menu_position' => 5,
								'has_archive' => true,
								'can_export' => true,
								'publicly_querable' => true,
								'show_ui' => true,
								'show_in_menu' => true,
								'show_in_nav_menus' => true,
								'show_in_admin_bar' => true,
								'exclude_from_search' => false,
								'register_meta_box_cb' => 'portfolio_url_meta_box',
								'menu_icon' => 'dashicons-format-gallery',
								'capability_type' => 'post'
								);
register_post_type('wp-portfolio',$wp_portfolio_args);		
}
add_action('init','wp_custom_portfolio_type');

// Custom Taxonomy 

function wp_portfolio_categories_taxonomy()
{
	$portfolio_categories_taxonomy=array(
								'name' => _x('Portfolio Categories','General Name'),
								'singular_name' => _x('Portfolio Category','Singular Name'),
								'all_items' => __('All Portfolio Categories'),
								'edit_item' => __('Edit Portfolio Category'),
								'update_item' => __('Update portfolio Category'),
								'add_new_item' => __('Add New portfolio Category'),
								'new_item_name' => __('New portfolio Category'),
								'menu_name' => __('Categories'),
								'parent_item_colon' => __('Parent portfolio Category'),
								'parent_item' => __('Parent portfolio Category'),
								'search_items' => __('Search portfolio Category')
								);
			$portfolio_categories_taxonomy=array(
								'name' => _x('Portfolio Categories','General Name'),
								'singular_name' => _x('Portfolio Category','Singular Name'),
								'all_items' => __('All Portfolio Categories'),
								'edit_item' => __('Edit Portfolio Category'),
								'update_item' => __('Update portfolio Category'),
								'add_new_item' => __('Add New portfolio Category'),
								'new_item_name' => __('New portfolio Category'),
								'menu_name' => __('Categories'),
								'parent_item_colon' => __('Parent portfolio Category'),
								'parent_item' => __('Parent portfolio Category'),
								'search_items' => __('Search portfolio Category')
								);					
register_taxonomy('wp-portfolio-cat',array('wp-portfolio'),array(
								'labels' => $portfolio_categories_taxonomy,
								'show_ui' => true,
								'hierarchical' => true,
								'show_admin_column' => true,
								'query_var' => true,
								'rewrite' => array('slug' => 'website-portfolio')
								));

								
}
add_action('init','wp_portfolio_categories_taxonomy');

// Edit CPT Columns 

add_filter('manage_edit-wp-portfolio_columns', 'add_my_wp_portfolio_columns');

function add_my_wp_portfolio_columns()
{
      $columns = array(
      'cb' => '&lt;input type="checkbox" />',
      'title' => __( 'Title' ),
      'Categories' => __( 'Categories' ),
      'URL' => __('URL'),
	  'date' => __('Date')
    );
	return $columns;
 
    }
/* Manage CPT Column */

add_action('manage_wp-portfolio_posts_custom_column', 'manage_wp_portfolio_columns', 10, 2);

function manage_wp_portfolio_columns($columns, $id) 
	{
        global $wpdb,$post;
        switch ($columns) 
		{
			case 'title':
					echo $id;
			break;
			
			case 'Categories' :

					$terms = get_the_terms( $id, 'wp-portfolio-cat' );

					/* If terms were found. */
					if ( !empty( $terms ) ) 
					{
						$out = array();

							/* Loop through each term, linking to the 'edit posts' page for the specific term. */
							foreach ( $terms as $term ) 
							{
								$out[] = sprintf( '<a href="%s">%s</a>',
								esc_url( add_query_arg( array( 'post_type' => $post->post_type, 'wp-portfolio-cat' => $term->slug ), 'edit.php' ) ),
								esc_html( sanitize_term_field( 'name', $term->name, $term->term_id, 'wp-portfolio-cat', 'display' ) ));
							}

						echo join( ', ', $out );
					}

					else {
						_e( '<span aria-hidden="true">—</span>' );
					}

			break;
				
			case 'URL' :

				$custom = get_post_meta($post->ID, '_portfolio_url', true);
				if(empty($custom) && isset($custom))
				  _e( '<span aria-hidden="true">—</span>' );
				  else
					echo "<a href='".get_permalink( $id )."?url=".$custom."'>".$custom."</a>";		
			break;
			
			default:
            break;
        } 
}

function portfolio_url_meta_box() {

    add_meta_box(
        'portfolio-url',
        __( 'Portfolio URL', 'wp-portfolio' ),
        'portfolio_url_meta_box_callback',
		'','side','low'
    );

}

function portfolio_url_meta_box_callback( $post ) {

    // Add a nonce field
    wp_nonce_field( 'portfolio_url_nonce', 'portfolio_url_nonce' );
    $value = get_post_meta( $post->ID, '_portfolio_url', true );
    echo '<textarea style="width:100%" id="portfolio_url" name="portfolio_url">' . esc_attr( $value ) . '</textarea>';
}

function save_portfolio_url_meta_box_data( $post_id ) {

    // Check nonce is set.
    if ( ! isset( $_POST['portfolio_url_nonce'] ) ) {
        return;
    }

    // Verify that the nonce is valid.
    if ( ! wp_verify_nonce( $_POST['portfolio_url_nonce'], 'portfolio_url_nonce' ) ) {
        return;
    }

    // If this is an autosave, our form has not been submitted, so we don't want to do anything.
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }

    // Check the user's permissions.
    if ( isset( $_POST['post_type'] ) && 'page' == $_POST['post_type'] ) {

        if ( ! current_user_can( 'edit_page', $post_id ) ) {
            return;
        }

    }
    else {

        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }
    }

    /* OK, it's safe for us to save the data now. */
    // Make sure that it is set.
    if ( ! isset( $_POST['portfolio_url'] ) ) {
        return;
    }
    // Sanitize user input.
    $my_data = esc_url( $_POST['portfolio_url'] );

    // Update the meta field in the database.
    update_post_meta( $post_id, '_portfolio_url', $my_data );
}

add_action( 'save_post', 'save_portfolio_url_meta_box_data' );

/* Register Shortcode */

add_action( 'init', 'wp_portfolio_register_shortcode_plugin_init', 10 );

function wp_portfolio_register_shortcode_plugin_init()
{
	add_shortcode( 'wp-portfolio', 'wp_live_portfolio_shortcode_callback' );
}

/* Shortcode Callback */
 
function wp_live_portfolio_shortcode_callback()
{
	 /* Enqueue JS loaded. */
	 wp_enqueue_script( 'wp-ajax-noob-wp-portfolio-script' );
	 $display = '';
	 $display .='<div class="menu-portfolio-menu-container">
				 <ul id="menu-portfolio-menu" class="menu">
				 <li>
				 <a href="#" data-id="all" class="active">All</a>
				</li>';
	
	$categories = get_categories( array(
    'orderby' => 'name',
    'order'   => 'ASC',
	'taxonomy' => 'wp-portfolio-cat') );
	
			foreach( $categories as $category ) 
			{
				 $display .='<li><a href="#" data-id="'.$category->term_id .'" class="'.$category->name .'">' . $category->name . '</a></li> ';
			} 
	
	 $display .='</ul></div>';
	
	 $display .='<div class="wp-portfolio-container" id="wp-portfolio-container" ></div>';	
	
	 return $display;
}

add_action( 'wp_enqueue_scripts', 'wp_portfolio_scripts' );

function wp_portfolio_scripts()
{
	/* Plugin DIR URL */
	wp_enqueue_style('css',plugin_dir_url(__FILE__).'css/style.css');
	$url = trailingslashit( plugin_dir_url( __FILE__ ) );
	/* JS + Localize */
	wp_register_script( 'wp-ajax-noob-wp-portfolio-script', $url . "assets/script.js", array( 'jquery' ), '1.0.0', true );
	/* Send Data as JS var via Localize Script */
	wp_localize_script( 'wp-ajax-noob-wp-portfolio-script', 'wp_portfolio_ajax_url', admin_url( 'admin-ajax.php' ) );
	wp_localize_script( 'wp-ajax', $url . "assets/script.js", array( 'jquery' ), '1.0.0', true );
	wp_enqueue_style( 'dashicons' );	 
}
/* AJAX action callback */

add_action( 'wp_ajax_wp_portfolio', 'wp_portfolio_ajax_callback' );
add_action( 'wp_ajax_nopriv_wp_portfolio', 'wp_portfolio_ajax_callback' );

/* Ajax Callback */

function wp_portfolio_ajax_callback()
{
		 
 echo '<script type="text/javascript">
			jQuery("div.portfolio-container a.portfolio-site-preview").on("click", function(){	
					var wp_portfolio_show=jQuery(this).attr("title");
					var wp_portfolio=jQuery("#menu-portfolio-menu a.active").text();

					jQuery.ajax({
							type: "POST",                 
							url: wp_portfolio_ajax_url,      
							data: {
								action     : "wp_portfolio", 
								wp_portfolio_show : wp_portfolio_show,  
								wp_portfolio :wp_portfolio
							},
							success:function( data ) {
								jQuery("#wp-portfolio-container").html( data );
								jQuery("html").css("overflow","hidden");				
							},
							error: function(){
								console.log(errorThrown); 
							}
						});
							});
			</script>'; 
	
if(isset( $_POST['wp_portfolio_show'] ) && !empty($_POST['wp_portfolio_show']))	
{
	$wp_portfolio_show=sanitize_text_field($_POST['wp_portfolio_show']);
 	$wpb_all_query = new WP_Query(array('post_type'=>'wp-portfolio', 'post_status'=>'publish', 'posts_per_page'=>6,'name' => $wp_portfolio_show));
	
			$display ='';

			$display .='<script type="text/javascript">
						jQuery("#computer").click(function(){
									jQuery(".portfolio-loader").show();
									jQuery("html").css("overflow","hidden");	
									jQuery(".portfolio-site-icon-bar").show(); 
									jQuery("#p-iframe").attr("class","p-laptop");
						 });
						 jQuery("#tablet").click(function(){ 
									jQuery("#p-iframe").attr("class","p-tablet");	 
									jQuery(".portfolio-loader").hide();
						 });
						 jQuery("#mobile").click(function(){
									jQuery("#p-iframe").attr("class","p-smartphone");
									jQuery(".portfolio-loader").hide();		 
						 });
						   jQuery("#cl").click(function(){
								   jQuery(".portfolio-loader").hide();	
								   jQuery(".portfolio-site-icon-bar").hide(); 
								   jQuery("html").css("overflow","auto");
								   jQuery(".show-website-portfolio").remove();	
						  });
						  </script>';

			if ( $wpb_all_query->have_posts() ) :	

			$display .='<div class="show-website-portfolio" id="show-website-portfolio">';

			while ( $wpb_all_query->have_posts() ) : $wpb_all_query->the_post();

			$display .='<div class="portfolio-loader">';
			$display .='<div class="portfolio-site-loading">';
			$display .='<div class="preloader">';
			$display .='<img src="' . esc_url( plugins_url( 'images/loader.gif', __FILE__ ) ) . '" > ';
			$display .='</div></div></div>';

			$display .='<div id="p-iframe" class="p-laptop">';
			$display .='<div class="p-content">';
			$display .='<iframe frameborder="0" hspace="0" allowtransparency="true" src="'. esc_url($portfolio_url=get_post_meta( get_the_ID(), "_portfolio_url", true)) .'" style="width:100%;border:none;height:100%"> </iframe>';
			$display .='</div></div>';		 

			$display .='<div class="portfolio-site-icon-bar">';
			$display .='<div class="portfolio-site-title">'. get_the_title() .'</div>';
			$display .='<div class="portfolio-site-icon">
							  <span id="computer" class="dashicons dashicons-desktop"></span> 
							  <span id="tablet" class="dashicons dashicons-tablet"></span> 
							  <span id="mobile" class="dashicons dashicons-smartphone"></span>
							  <span id="cl" class="dashicons dashicons-no"></span> 
						</div></div>';

			endwhile; 

			$display .='</div>';

			echo $display;

			wp_reset_postdata();

			endif;	
 }
	
if(isset($_POST['wp_portfolio']) && !empty($_POST['wp_portfolio']) && $_POST['wp_portfolio']=='All')
{
	$display ='';

	$display .='<div class="website-portfolio" id="website-portfolio">';

$wpb_all_query = new WP_Query(array('post_type'=>'wp-portfolio','taxonomy' => 'wp-portfolio-cat','post_status'=>'publish'));
	
		if ( $wpb_all_query->have_posts() ) : while ( $wpb_all_query->have_posts() ) : $wpb_all_query->the_post();

			$term_obj_list = get_the_terms( $post->ID, 'wp-portfolio-cat' );
			$terms_string = join(', ', wp_list_pluck($term_obj_list, 'name')); 

			$display .='<div class="portfolio-container" data-category="'. $terms_string .'">';
			$display .='<a class="portfolio-site-preview"  title="'. get_the_title() .'">';

			$display .='<div class="portfolio-website">';
			$display .=''. get_the_post_thumbnail( $post_id, 'full' ) .'';

			$display .='<div class="portfolio-overlay-website">
							 <span class="portfolio-quick-view"> Preview </span>
						</div></div></a>';

			$display .='<div class="portfolio-item-title" align="center">					
						<h3>'. get_the_title() .'</h3>				
						</div></div>';
			endwhile; 

			$display .='</div>';

			echo $display;

			wp_reset_postdata();

			endif;	
}
else
{
  	if(isset($_POST['wp_portfolio']) && !empty($_POST['wp_portfolio']))
	{	
			$wp_portfolio=sanitize_text_field($_POST['wp_portfolio']);
			$display ='';
			$display .='<div class="website-portfolio" id="website-portfolio">';

			$tax_query = array(
							array(
								'taxonomy' => 'wp-portfolio-cat',
								'terms' =>$wp_portfolio,
								'field' => 'slug'
							)
						);

		$wpb_all_query = new WP_Query(array('post_type'=>'wp-portfolio','tax_query' => $tax_query ,'post_status'=>'publish'));

		if ( $wpb_all_query->have_posts() ) : while ( $wpb_all_query->have_posts() ) : $wpb_all_query->the_post();

			$display .='<div class="portfolio-container" data-category="'. $terms_string .'">';
			$display .='<a class="portfolio-site-preview"  title="'. get_the_title() .'">';

			$display .='<div class="portfolio-website">';
			$display .=''. get_the_post_thumbnail( $post_id, 'full' ) .'';

			$display .='<div class="portfolio-overlay-website">
							 <span class="portfolio-quick-view"> Preview </span>
						</div></div></a>';

			$display .='<div class="portfolio-item-title" align="center">					
						<h3>'. get_the_title() .'</h3>				
						</div></div>';
			 endwhile; 

			$display .='</div>';

			echo $display;

			wp_reset_postdata();

			endif;	
			}	
}
	wp_die(); 
}
?>