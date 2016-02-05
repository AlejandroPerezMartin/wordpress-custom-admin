<?php

add_action('wp_enqueue_scripts', 'theme_enqueue_styles');

function theme_enqueue_styles()
{
    wp_enqueue_style('parent-style', get_template_directory_uri() . '/style.css');
}

function edit_wp_menu()
{
    //-----------------------------------------------
    // Remove menu items
    //-----------------------------------------------
    remove_menu_page('edit-comments.php');
    //-----------------------------------------------
    // Add a menu item
    //-----------------------------------------------
    add_menu_page('New Comments', 'My Comments', 'manage_options', 'edit-comments.php', '', '', 6);
    //-----------------------------------------------
    // Change the menu order
    //-----------------------------------------------
    function change_menu_order($menu_order)
    {
        return array(
            'index.php',
            'themes.php',
            'edit.php',
            'upload.php'
        );
    }
    add_filter('custom_menu_order', '__return_true');
    add_filter('menu_order', 'change_menu_order');
    //-----------------------------------------------
    // Rename Posts to Articles
    //-----------------------------------------------
    global $menu;
    global $submenu;

    $menu[5][0]                 = 'Articles';
    $submenu['edit.php'][5][0]  = 'All Articles';
    $submenu['edit.php'][10][0] = 'Add an Article';
    $submenu['edit.php'][15][0] = 'Article Categories';
    $submenu['edit.php'][16][0] = 'Article Tags';
}

function change_post_labels()
{
    global $wp_post_types;

    // Get the current post labels
    $articleLabels                     = $wp_post_types['post']->labels;
    $articleLabels->name               = 'Articles';
    $articleLabels->singular_name      = 'Article';
    $articleLabels->add_new            = 'Add Articles';
    $articleLabels->add_new_item       = 'Add Articles';
    $articleLabels->edit_item          = 'Edit Articles';
    $articleLabels->new_item           = 'Articles';
    $articleLabels->view_item          = 'View Articles';
    $articleLabels->search_items       = 'Search Articles';
    $articleLabels->not_found          = 'No Articles found';
    $articleLabels->not_found_in_trash = 'No Articles found in Trash';
}

add_action('admin_menu', 'edit_wp_menu');
add_action('init', 'change_post_labels');

function customize_dashboard()
{

    // remove quick draft from dashboard
    remove_meta_box('dashboard_quick_press', 'dashboard', 'side');

    // remove welcome panel
    remove_action('welcome_panel', 'wp_welcome_panel');

    wp_add_dashboard_widget('date_dashboard_widget', // ID
        'Today date', // widget title
        'date_dashboard_widget_function' // callback
        );

}

function date_dashboard_widget_function()
{
    echo "Hello, today is " . date('l, F jS Y');
}

add_action('wp_dashboard_setup', 'customize_dashboard');


function customize_posts_listing_cols($columns)
{
    unset($columns['tags']);
    unset($columns['comments']);

    return $columns;
}

function customize_pages_listing_cols($columns)
{
    unset($columns['categories']);
    unset($columns['tags']);

    return $columns;
}

add_action('manage_posts_columns', 'customize_posts_listing_cols');
add_action('manage_posts_columns', 'customize_pages_listing_cols');

function add_help_metaboxes()
{
    $screens = array(
        'post',
        'page'
    );

    foreach ($screens as $screen) {
        add_meta_box('helping-metabox', 'Help metabox', 'add_help_metabox_callback', $screen, 'advanced', 'low');

        add_meta_box('helping-metabox-side', 'Help metabox', 'add_side_help_metabox_callback', $screen, 'side', 'high');
    }
}

function add_help_metabox_callback()
{
    echo '<p>Help text goes here!</p>';
}

function add_side_help_metabox_callback()
{
    echo '<p>Help text goes here!</p>';
}

add_action('add_meta_boxes', 'add_help_metaboxes');

function add_help_tabs()
{
    if ($screen = get_current_screen()) {
        $screen->add_help_tab(array(
            'id' => 'new_help_tab',
            'title' => 'Custom help tab',
            'content' => '<p>Help text goes here!</p>'
        ));
    }
}

add_action('in_admin_header', 'add_help_tabs');

function change_admin_footer()
{
    echo 'Custom text in the footer area';
}

add_filter('admin_footer_text', 'change_admin_footer');

// remove wordpress version from the footer
function remove_footer_version()
{
    remove_filter('update_footer', 'core_update_footer');
}

add_action('admin_menu', 'remove_footer_version');

function remove_calendar_widget()
{
    unregister_widget('WP_Widget_Calendar');
    unregister_widget('WP_Widget_Search');
    unregister_widget('WP_Widget_RSS');
    unregister_widget('WP_Widget_Recent_Comments');
}

add_action('widgets_init', 'remove_calendar_widget');

function add_editor_styles()
{
    add_editor_style('editor-style.css');
}

add_action('admin_init', 'add_editor_styles');

function add_color_schemes()
{
    wp_admin_css_color('alejandro', __('Alejandro'), get_stylesheet_directory_uri() . 'alejandro-color-scheme.css', array(
        '#07273E',
        '#14568A',
        '#D54E21',
        '#2683AE'
    ), array(
        'base' => '#e5f8ff',
        'focus' => '#fff',
        'current' => '#fff'
    ));
}

add_action('admin_init', 'add_color_schemes');

function change_login_logo()
{
?>

	<style>
		.login h1 a {
			background-image: url(<?php
    echo get_stylesheet_directory_uri() . '/image.jpg';
?>);
		}
	</style>
	<?php
}

add_action('login_enqueue_scripts', 'change_login_logo');

function change_login_logo_url()
{
    return home_url();
}

function change_login_logo_url_title()
{
    return "My logo title";
}

add_filter('login_headerurl', 'change_login_logo_url');
add_filter('login_headertitle', 'change_login_logo_url_title');

function change_login_css()
{
    wp_enqueue_style('custom-login', get_stylesheet_directory_uri() . '/login-style.css');
    wp_enqueue_script('custom-login', get_stylesheet_directory_uri() . '/login-style.js');
}

add_action('login_enqueue_scripts', 'change_login_css');

function disable_password_reset()
{
    return false;
}

add_filter('allow_password_reset', 'disable_password_reset');

function remove_admin_bar_links()
{
    global $wp_admin_bar;

    $wp_admin_bar->remove_menu('wp-logo'); // remove wordpress logo from admin bar
}

add_action('wp_before_admin_bar_render', 'remove_admin_bar_links');

function add_admin_bar_links()
{
    global $wp_admin_bar;

    $wp_admin_bar->add_menu(array(
        'id' => 'custom-menu-item',
        'title' => 'Custom bar link',
        'href' => home_url(),
        'meta' => array(
            'target' => '_blank'
        )
    )); // remove wordpress logo from admin bar
}

add_action('admin_bar_menu', 'add_admin_bar_links', 100); // 100 is the order within the bar

function admin_bar_css()
{
?>

	<style>
		#wpadminbar {
			background-color: red;
		}
	</style>
	<?php
}

add_action('admin_head', 'admin_bar_css');

function remove_post_custom_fields()
{
    remove_meta_box('slugdiv', 'post', 'normal');
    remove_meta_box('slugdiv', 'page', 'normal');
}

add_action('admin_init', 'remove_post_custom_fields');

?>
