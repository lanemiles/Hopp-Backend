<?php

/*
update_option('siteurl','http://lanemiles.com/HoppApp');
update_option('home','http://lanemiles.com/HoppApp');
*/

define('DOMAIN' , 'wp_eos');

define('SH_NAME', 'wp_eos');

define('SH_VERSION', 'v1.0');

define('SH_ROOT', get_template_directory().'/');

define('SH_URL', get_template_directory_uri().'/');

include_once('includes/loader.php');
add_action('after_setup_theme', 'sh_theme_setup');

function sh_theme_setup()
{

	global $wp_version;
	
	load_theme_textdomain(SH_NAME, get_template_directory() . '/languages');

	add_editor_style();

	//ADD THUMBNAIL SUPPORT

	add_theme_support('post-thumbnails');
	add_theme_support('widgets'); 
	add_theme_support('custom-header');
    add_theme_support('custom-background');
	add_theme_support('automatic-feed-links');//Add widgets and sidebar support

	/** Register wp_nav_menus */
	if(function_exists('register_nav_menu'))
	{
		register_nav_menus(
			array(
				'main_menu' => __('Main Menu', SH_NAME),
			)
		);
	}
	
	
	if ( ! isset( $content_width ) ) $content_width = 960;

	add_image_size( '245x435' , 245, 435, true );
	add_image_size( '310x530' , 310, 530, true );
	add_image_size( '260x540' , 260, 540, true );
	add_image_size( '265x550' , 265, 550, true );
	add_image_size( '250x485' , 250, 485, true );
	add_image_size( '945x500' , 945, 500, true );
	add_image_size( '216x62' , 216, 62, true );
	add_image_size( '278x555' , 278, 555, true );
	add_image_size( '175x125' , 175, 125, true );
}

function sh_widget_init()
{

	global $wp_registered_sidebars;


	register_sidebar(array(

	  'name' => __( 'Footer Sidebar', SH_NAME ),
	  'id' => 'footer-sidebar',
	  'description' => __( 'Widgets in this area will be shown in Footer Area.', SH_NAME ),
	  'class'=>'',
	  'before_widget'=>'<div class="col-md-3"><div class="widget %s">',
	  'after_widget'=>'</div></div>',
	  'before_title' => '<h4>',
	  'after_title' => '</h4>'

	));


	$sidebars = sh_set(sh_set( get_option(SH_NAME.'_theme_options'), 'dynamic_sidebar' ) , 'dynamic_sidebar' ); 

	foreach( array_filter((array)$sidebars) as $sidebar)

	{
		if(sh_set($sidebar , 'topcopy')) break ;
		$slug = sh_slug( $sidebar ) ;
		register_sidebar( array(

			'name' => sh_set($sidebar , 'sidebar_name'),
			'id' =>  sh_set($slug , 'sidebar_name') ,
			'before_widget'=>'<div class="col-md-3"><div class="widget %s">',
			'after_widget'=>'</div></div>',
			'before_title' => '<h4>',
			'after_title' => '</h4>'

		) );		

	}

	

	update_option('wp_registered_sidebars' , $wp_registered_sidebars) ;

}

add_action( 'widgets_init', 'sh_widget_init' );
function custom_excerpt_length($length)
{
	$newlength = 100 ;
	return $newlength ;
}
add_filter('excerpt_length' , 'custom_excerpt_length') ;