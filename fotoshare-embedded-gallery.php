<?php
/**
 * Plugin Name: Fotoshare Embedded Gallery
 * Plugin URI: https://github.com/kaidoj/wp-fotoshare-embedded-gallery
 * Description: A plugin that allows password-protected embedding of Fotoshare galleries
 * Version: 1.0.2
 * Author: KaidoJ
 * Text Domain: fotoshare-embedded-gallery
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('FOTOSHARE_GALLERY_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('FOTOSHARE_GALLERY_PLUGIN_URL', plugin_dir_url(__FILE__));
define('FOTOSHARE_GALLERY_VERSION', '1.0.2');

// Include required files
require_once FOTOSHARE_GALLERY_PLUGIN_PATH . 'includes/class-fotoshare-gallery.php';
require_once FOTOSHARE_GALLERY_PLUGIN_PATH . 'includes/class-fotoshare-gallery-shortcode.php';

// Initialize the plugin
function fotoshare_gallery_init() {
    // Register custom post type
    add_action('init', 'fotoshare_gallery_register_post_type');
    
    // Load text domain for translations
    load_plugin_textdomain('fotoshare-embedded-gallery', false, dirname(plugin_basename(__FILE__)) . '/languages');
}
add_action('plugins_loaded', 'fotoshare_gallery_init');

// Register Fotoshare Gallery custom post type
function fotoshare_gallery_register_post_type() {
    $labels = array(
        'name'               => _x('Fotoshare Galleries', 'post type general name', 'fotoshare-embedded-gallery'),
        'singular_name'      => _x('Fotoshare Gallery', 'post type singular name', 'fotoshare-embedded-gallery'),
        'menu_name'          => _x('Fotoshare Galleries', 'admin menu', 'fotoshare-embedded-gallery'),
        'name_admin_bar'     => _x('Fotoshare Gallery', 'add new on admin bar', 'fotoshare-embedded-gallery'),
        'add_new'            => _x('Add New', 'gallery', 'fotoshare-embedded-gallery'),
        'add_new_item'       => __('Add New Gallery', 'fotoshare-embedded-gallery'),
        'new_item'           => __('New Gallery', 'fotoshare-embedded-gallery'),
        'edit_item'          => __('Edit Gallery', 'fotoshare-embedded-gallery'),
        'view_item'          => __('View Gallery', 'fotoshare-embedded-gallery'),
        'all_items'          => __('All Galleries', 'fotoshare-embedded-gallery'),
        'search_items'       => __('Search Galleries', 'fotoshare-embedded-gallery'),
        'parent_item_colon'  => __('Parent Galleries:', 'fotoshare-embedded-gallery'),
        'not_found'          => __('No galleries found.', 'fotoshare-embedded-gallery'),
        'not_found_in_trash' => __('No galleries found in Trash.', 'fotoshare-embedded-gallery')
    );

    $args = array(
        'labels'             => $labels,
        'description'        => __('Password-protected Fotoshare embedded galleries', 'fotoshare-embedded-gallery'),
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array('slug' => 'fotoshare-gallery'),
        'capability_type'    => 'post',
        'has_archive'        => false,
        'hierarchical'       => false,
        'menu_position'      => null,
        'menu_icon'          => 'dashicons-format-gallery',
        'supports'           => array('title', 'editor'),
        // Add this to exclude from Yoast SEO
        'wpseo_sitemap_exclude' => true,
    );

    register_post_type('fotoshare_gallery', $args);
}

// Add settings page to the menu
function fotoshare_gallery_add_settings_page() {
    add_submenu_page(
        'edit.php?post_type=fotoshare_gallery',
        __('Fotoshare Gallery Settings', 'fotoshare-embedded-gallery'),
        __('Settings', 'fotoshare-embedded-gallery'),
        'manage_options',
        'fotoshare-gallery-settings',
        'fotoshare_gallery_settings_page'
    );
}
add_action('admin_menu', 'fotoshare_gallery_add_settings_page');

// Register plugin settings
function fotoshare_gallery_register_settings() {
    register_setting('fotoshare_gallery_settings', 'fotoshare_gallery_options');
    
    add_settings_section(
        'fotoshare_gallery_text_section',
        __('Text Settings', 'fotoshare-embedded-gallery'),
        'fotoshare_gallery_text_section_callback',
        'fotoshare_gallery_settings'
    );
    
    add_settings_field(
        'fotoshare_gallery_title_text',
        __('Form Title', 'fotoshare-embedded-gallery'),
        'fotoshare_gallery_text_field_callback',
        'fotoshare_gallery_settings',
        'fotoshare_gallery_text_section',
        array(
            'id' => 'title_text',
            'default' => 'Vaata galeriid'
        )
    );
    
    add_settings_field(
        'fotoshare_gallery_password_label',
        __('Password Field Label', 'fotoshare-embedded-gallery'),
        'fotoshare_gallery_text_field_callback',
        'fotoshare_gallery_settings',
        'fotoshare_gallery_text_section',
        array(
            'id' => 'password_label',
            'default' => 'Sisesta parool'
        )
    );
    
    add_settings_field(
        'fotoshare_gallery_password_placeholder',
        __('Password Field Placeholder', 'fotoshare-embedded-gallery'),
        'fotoshare_gallery_text_field_callback',
        'fotoshare_gallery_settings',
        'fotoshare_gallery_text_section',
        array(
            'id' => 'password_placeholder',
            'default' => 'Sisesta galerii parool'
        )
    );
    
    add_settings_field(
        'fotoshare_gallery_submit_button',
        __('Submit Button Text', 'fotoshare-embedded-gallery'),
        'fotoshare_gallery_text_field_callback',
        'fotoshare_gallery_settings',
        'fotoshare_gallery_text_section',
        array(
            'id' => 'submit_button',
            'default' => 'Vaata galeriid'
        )
    );
    
    add_settings_field(
        'fotoshare_gallery_error_message',
        __('Error Message', 'fotoshare-embedded-gallery'),
        'fotoshare_gallery_text_field_callback',
        'fotoshare_gallery_settings',
        'fotoshare_gallery_text_section',
        array(
            'id' => 'error_message',
            'default' => 'Vale parool. Palun proovi uuesti.'
        )
    );
    
    add_settings_field(
        'fotoshare_gallery_no_galleries',
        __('No Galleries Message', 'fotoshare-embedded-gallery'),
        'fotoshare_gallery_text_field_callback',
        'fotoshare_gallery_settings',
        'fotoshare_gallery_text_section',
        array(
            'id' => 'no_galleries',
            'default' => 'Ühtegi galeriid pole loodud. Palun lisage galeriid administreerimispaneelis.'
        )
    );
    
    // Add styling section
    add_settings_section(
        'fotoshare_gallery_style_section',
        __('Style Settings', 'fotoshare-embedded-gallery'),
        'fotoshare_gallery_style_section_callback',
        'fotoshare_gallery_settings'
    );
    
    add_settings_field(
        'fotoshare_gallery_button_color',
        __('Button Color', 'fotoshare-embedded-gallery'),
        'fotoshare_gallery_color_field_callback',
        'fotoshare_gallery_settings',
        'fotoshare_gallery_style_section',
        array(
            'id' => 'button_color',
            'default' => '#4a90e2'
        )
    );
    
    add_settings_field(
        'fotoshare_gallery_button_hover_color',
        __('Button Hover Color', 'fotoshare-embedded-gallery'),
        'fotoshare_gallery_color_field_callback',
        'fotoshare_gallery_settings',
        'fotoshare_gallery_style_section',
        array(
            'id' => 'button_hover_color',
            'default' => '#3a80d2'
        )
    );
    
    add_settings_field(
        'fotoshare_gallery_box_background_color',
        __('Login Box Background Color', 'fotoshare-embedded-gallery'),
        'fotoshare_gallery_color_field_callback',
        'fotoshare_gallery_settings',
        'fotoshare_gallery_style_section',
        array(
            'id' => 'box_background_color',
            'default' => '#ffffff'
        )
    );
    
    add_settings_field(
        'fotoshare_gallery_text_color',
        __('Text Color', 'fotoshare-embedded-gallery'),
        'fotoshare_gallery_color_field_callback',
        'fotoshare_gallery_settings',
        'fotoshare_gallery_style_section',
        array(
            'id' => 'text_color',
            'default' => '#333333'
        )
    );
    
    add_settings_field(
        'fotoshare_gallery_label_color',
        __('Label Text Color', 'fotoshare-embedded-gallery'),
        'fotoshare_gallery_color_field_callback',
        'fotoshare_gallery_settings',
        'fotoshare_gallery_style_section',
        array(
            'id' => 'label_color',
            'default' => '#333333'
        )
    );
}
add_action('admin_init', 'fotoshare_gallery_register_settings');

// Settings page section callback
function fotoshare_gallery_text_section_callback() {
    echo '<p>' . __('Customize the text displayed on the gallery access form.', 'fotoshare-embedded-gallery') . '</p>';
}

// Style settings section callback
function fotoshare_gallery_style_section_callback() {
    echo '<p>' . __('Customize the appearance of the gallery access form.', 'fotoshare-embedded-gallery') . '</p>';
}

// Settings page field callback
function fotoshare_gallery_text_field_callback($args) {
    $options = get_option('fotoshare_gallery_options', array());
    $id = $args['id'];
    $default = $args['default'];
    $value = isset($options[$id]) ? $options[$id] : $default;
    
    echo '<input type="text" id="fotoshare_gallery_' . esc_attr($id) . '" name="fotoshare_gallery_options[' . esc_attr($id) . ']" value="' . esc_attr($value) . '" class="regular-text">';
}

// Color picker field callback
function fotoshare_gallery_color_field_callback($args) {
    $options = get_option('fotoshare_gallery_options', array());
    $id = $args['id'];
    $default = $args['default'];
    $value = isset($options[$id]) ? $options[$id] : $default;
    
    echo '<input type="color" id="fotoshare_gallery_' . esc_attr($id) . '" name="fotoshare_gallery_options[' . esc_attr($id) . ']" value="' . esc_attr($value) . '" class="fotoshare-color-picker">';
    echo '<input type="text" id="fotoshare_gallery_' . esc_attr($id) . '_text" value="' . esc_attr($value) . '" class="fotoshare-color-text" readonly>';
}

// Settings page render function
function fotoshare_gallery_settings_page() {
    if (!current_user_can('manage_options')) {
        return;
    }
    
    ?>
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
        <form action="options.php" method="post">
            <?php
            settings_fields('fotoshare_gallery_settings');
            do_settings_sections('fotoshare_gallery_settings');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

// Helper function to get text options with defaults
function fotoshare_gallery_get_text($key, $default = '') {
    $options = get_option('fotoshare_gallery_options', array());
    return isset($options[$key]) && !empty($options[$key]) ? $options[$key] : $default;
}

// Register meta box for gallery settings
function fotoshare_gallery_register_meta_boxes() {
    add_meta_box(
        'fotoshare-gallery-settings',
        __('Gallery Settings', 'fotoshare-embedded-gallery'),
        'fotoshare_gallery_settings_callback',
        'fotoshare_gallery',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'fotoshare_gallery_register_meta_boxes');

// Meta box callback function
function fotoshare_gallery_settings_callback($post) {
    // Add a nonce field for security
    wp_nonce_field('fotoshare_gallery_save_meta', 'fotoshare_gallery_nonce');

    // Get the current values if they exist
    $fotoshare_url = get_post_meta($post->ID, '_fotoshare_url', true);
    $password = get_post_meta($post->ID, '_fotoshare_password', true);
    
    ?>
    <p>
        <label for="fotoshare_url"><?php _e('Fotoshare URL:', 'fotoshare-embedded-gallery'); ?></label><br>
        <input type="url" id="fotoshare_url" name="fotoshare_url" value="<?php echo esc_url($fotoshare_url); ?>" class="widefat" placeholder="https://example.com/embed/gallery" required>
        <span class="description"><?php _e('Enter the full URL to your Fotoshare gallery', 'fotoshare-embedded-gallery'); ?></span>
    </p>
    
    <p>
        <label for="fotoshare_password"><?php _e('Access Password:', 'fotoshare-embedded-gallery'); ?></label><br>
        <input type="text" id="fotoshare_password" name="fotoshare_password" value="<?php echo esc_attr($password); ?>" class="widefat" required>
        <span class="description"><?php _e('Password required to view this gallery', 'fotoshare-embedded-gallery'); ?></span>
    </p>
    <?php
}

// Save meta box data
function fotoshare_gallery_save_meta($post_id) {
    // Check if our nonce is set and verify it
    if (!isset($_POST['fotoshare_gallery_nonce']) || !wp_verify_nonce($_POST['fotoshare_gallery_nonce'], 'fotoshare_gallery_save_meta')) {
        return $post_id;
    }

    // Check for autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return $post_id;
    }

    // Check permissions
    if ('fotoshare_gallery' === $_POST['post_type']) {
        if (!current_user_can('edit_post', $post_id)) {
            return $post_id;
        }
    }

    // Save the data
    if (isset($_POST['fotoshare_url'])) {
        update_post_meta($post_id, '_fotoshare_url', sanitize_url($_POST['fotoshare_url']));
    }
    
    if (isset($_POST['fotoshare_password'])) {
        update_post_meta($post_id, '_fotoshare_password', sanitize_text_field($_POST['fotoshare_password']));
    }
}
add_action('save_post', 'fotoshare_gallery_save_meta');

/**
 * Global Fotoshare Gallery Shortcode
 * 
 * Usage: [fotoshare_gallery]
 * This will display a password form and show the gallery matching the entered password.
 */
function fotoshare_embedded_gallery_shortcode($atts) {
    $atts = shortcode_atts(array(
        'title' => fotoshare_gallery_get_text('title_text', 'Vaata galeriid'),
    ), $atts, 'fotoshare_gallery');
    
    // Get all published galleries
    $galleries = get_posts(array(
        'post_type' => 'fotoshare_gallery',
        'post_status' => 'publish',
        'posts_per_page' => -1,
    ));
    
    if (empty($galleries)) {
        return '<div class="fotoshare-error-message">' . fotoshare_gallery_get_text('no_galleries', 'Ühtegi galeriid pole loodud. Palun lisage galeriid administreerimispaneelis.') . '</div>';
    }
    
    // Generate unique form ID
    $form_id = 'fotoshare-gallery-form-' . uniqid();
    $nonce_action = 'fotoshare_verify_access';
    $nonce_field = wp_nonce_field($nonce_action, 'fotoshare_nonce', true, false);
    
    // Check if form was submitted and process it
    $gallery_content = '';
    $error_message = '';
    
    if (isset($_POST['fotoshare_password']) && isset($_POST['fotoshare_nonce']) && wp_verify_nonce($_POST['fotoshare_nonce'], $nonce_action)) {
        // Sanitize and trim password to prevent accidental spaces causing issues
        $submitted_password = trim(sanitize_text_field($_POST['fotoshare_password']));
        
        // Additional security check - ensure password is not empty after sanitization
        if (!empty($submitted_password)) {
            $matching_gallery = null;
            
            // Find gallery with matching password
            foreach ($galleries as $gallery) {
                $gallery_password = get_post_meta($gallery->ID, '_fotoshare_password', true);
                
                if ($gallery_password === $submitted_password) {
                    $matching_gallery = $gallery;
                    break;
                }
            }
            
            if ($matching_gallery) {
                // Password matches a gallery, display it
                $gallery_id = $matching_gallery->ID;
                $fotoshare_url = get_post_meta($gallery_id, '_fotoshare_url', true);
                
                ob_start();
                ?>
                <div class="fotoshare-gallery-container">
                    <iframe src="<?php echo esc_url($fotoshare_url); ?>" class="fotoshare-iframe" frameborder="0" allowfullscreen></iframe>
                </div>
                <?php
                $gallery_content = ob_get_clean();
                
                // Set a cookie for this gallery using hash of sanitized password
                setcookie(
                    'fotoshare_gallery_' . absint($gallery_id), 
                    md5($submitted_password), 
                    time() + 86400, 
                    COOKIEPATH, 
                    COOKIE_DOMAIN,
                    is_ssl(), // Secure if on HTTPS
                    true // HttpOnly for added security
                );
            } else {
                $error_message = fotoshare_gallery_get_text('error_message', 'Vale parool. Palun proovi uuesti.');
            }
        } else {
            $error_message = fotoshare_gallery_get_text('error_message', 'Vale parool. Palun proovi uuesti.');
        }
    }
    
    // Check for cookies to see if any gallery is already authenticated
    foreach ($galleries as $gallery) {
        $gallery_id = absint($gallery->ID);
        $cookie_name = 'fotoshare_gallery_' . $gallery_id;
        
        if (isset($_COOKIE[$cookie_name])) {
            // Sanitize the cookie value
            $cookie_value = sanitize_text_field($_COOKIE[$cookie_name]);
            $gallery_password = get_post_meta($gallery_id, '_fotoshare_password', true);
            
            if ($cookie_value === md5($gallery_password)) {
                // User has already authenticated for this gallery
                $fotoshare_url = get_post_meta($gallery_id, '_fotoshare_url', true);
                
                ob_start();
                ?>
                <div class="fotoshare-gallery-container">
                    <iframe src="<?php echo esc_url($fotoshare_url); ?>" class="fotoshare-iframe" frameborder="0" allowfullscreen></iframe>
                </div>
                <?php
                $gallery_content = ob_get_clean();
                break;
            }
        }
    }
    
    // If a gallery is displayed, return its content
    if (!empty($gallery_content)) {
        return $gallery_content;
    }
    
    // Otherwise show the form
    ob_start();
    ?>
    <div class="fotoshare-password-form-container">
        <h2><?php echo esc_html($atts['title']); ?></h2>
        
        <?php if (!empty($error_message)): ?>
            <p class="fotoshare-error"><?php echo esc_html($error_message); ?></p>
        <?php endif; ?>
        
        <form id="<?php echo esc_attr($form_id); ?>" class="fotoshare-password-form" method="post">
            <?php echo $nonce_field; ?>
            
            <div class="fotoshare-form-field">
                <label for="fotoshare_password_input"><?php echo esc_html(fotoshare_gallery_get_text('password_label', 'Sisesta parool')); ?></label>
                <input type="password" id="fotoshare_password_input" name="fotoshare_password" required placeholder="<?php echo esc_attr(fotoshare_gallery_get_text('password_placeholder', 'Sisesta galerii parool')); ?>">
            </div>
            
            <div class="fotoshare-form-submit">
                <input type="submit" value="<?php echo esc_attr(fotoshare_gallery_get_text('submit_button', 'Vaata galeriid')); ?>">
            </div>
        </form>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('fotoshare_gallery', 'fotoshare_embedded_gallery_shortcode');

// Enqueue scripts and styles for the frontend
function fotoshare_gallery_enqueue_scripts() {
    wp_enqueue_style('fotoshare-gallery-style', FOTOSHARE_GALLERY_PLUGIN_URL . 'assets/css/style.css', array(), FOTOSHARE_GALLERY_VERSION);
    wp_enqueue_script('fotoshare-gallery-script', FOTOSHARE_GALLERY_PLUGIN_URL . 'assets/js/script.js', array('jquery'), FOTOSHARE_GALLERY_VERSION, true);
    
    // Add inline CSS for custom colors
    $options = get_option('fotoshare_gallery_options', array());
    
    // Button colors
    $button_color = isset($options['button_color']) && !empty($options['button_color']) ? $options['button_color'] : '#4a90e2';
    $button_hover_color = isset($options['button_hover_color']) && !empty($options['button_hover_color']) ? $options['button_hover_color'] : '#3a80d2';
    
    // New background and text colors
    $box_background_color = isset($options['box_background_color']) && !empty($options['box_background_color']) ? $options['box_background_color'] : '#ffffff';
    $text_color = isset($options['text_color']) && !empty($options['text_color']) ? $options['text_color'] : '#333333';
    $label_color = isset($options['label_color']) && !empty($options['label_color']) ? $options['label_color'] : '#333333';
    
    $custom_css = "
        /* Login form container background */
        .fotoshare-password-form-container {
            background-color: {$box_background_color};
            color: {$text_color};
        }
        
        /* Form title color */
        .fotoshare-password-form-container h2 {
            color: {$text_color};
        }
        
        /* Label text color */
        .fotoshare-form-field label {
            color: {$label_color};
        }
        
        /* Button colors */
        .fotoshare-form-submit input[type=\"submit\"] {
            background-color: {$button_color};
        }
        .fotoshare-form-submit input[type=\"submit\"]:hover {
            background-color: {$button_hover_color};
        }
    ";
    wp_add_inline_style('fotoshare-gallery-style', $custom_css);
}
add_action('wp_enqueue_scripts', 'fotoshare_gallery_enqueue_scripts');

// Enqueue admin scripts for the settings page
function fotoshare_gallery_admin_scripts($hook) {
    // Only load on our settings page
    if ($hook != 'fotoshare_gallery_page_fotoshare-gallery-settings') {
        return;
    }
    
    wp_enqueue_script('fotoshare-gallery-admin-script', FOTOSHARE_GALLERY_PLUGIN_URL . 'assets/js/admin.js', array('jquery'), FOTOSHARE_GALLERY_VERSION, true);
    
    // Add inline CSS for the color picker display
    $admin_css = "
        .fotoshare-color-picker {
            vertical-align: middle;
            margin-right: 10px;
        }
        .fotoshare-color-text {
            width: 80px;
            vertical-align: middle;
            border: 1px solid #ddd;
            background-color: #f9f9f9;
            padding: 3px 5px;
        }
    ";
    wp_add_inline_style('wp-admin', $admin_css);
}
add_action('admin_enqueue_scripts', 'fotoshare_gallery_admin_scripts');

// Create necessary directories and files on activation
function fotoshare_gallery_activate() {
    // Create directories
    $dirs = array(
        FOTOSHARE_GALLERY_PLUGIN_PATH . 'includes',
        FOTOSHARE_GALLERY_PLUGIN_PATH . 'assets/css',
        FOTOSHARE_GALLERY_PLUGIN_PATH . 'assets/js',
    );
    
    foreach ($dirs as $dir) {
        if (!file_exists($dir)) {
            wp_mkdir_p($dir);
        }
    }
    
    // Initialize default settings
    $default_options = array(
        'title_text' => 'Vaata galeriid',
        'password_label' => 'Sisesta parool',
        'password_placeholder' => 'Sisesta galerii parool',
        'submit_button' => 'Vaata galeriid',
        'error_message' => 'Vale parool. Palun proovi uuesti.',
        'no_galleries' => 'Ühtegi galeriid pole loodud. Palun lisage galeriid administreerimispaneelis.'
    );
    
    // Only set default options if they don't exist yet
    if (!get_option('fotoshare_gallery_options')) {
        add_option('fotoshare_gallery_options', $default_options);
    }
    
    // Flush rewrite rules for custom post types
    flush_rewrite_rules();
}
register_activation_hook(__FILE__, 'fotoshare_gallery_activate');

// Clean up on deactivation
function fotoshare_gallery_deactivate() {
    // Flush rewrite rules for custom post types
    flush_rewrite_rules();
}
register_deactivation_hook(__FILE__, 'fotoshare_gallery_deactivate');

/**
 * Exclude Fotoshare Gallery post type from Yoast SEO
 */

// Exclude fotoshare_gallery post type from Yoast SEO sitemaps
function fotoshare_gallery_exclude_from_yoast_sitemap($excluded_posts_types) {
    $excluded_posts_types[] = 'fotoshare_gallery';
    return $excluded_posts_types;
}
add_filter('wpseo_sitemap_exclude_post_type', 'fotoshare_gallery_exclude_from_yoast_sitemap');

// Remove fotoshare_gallery from Yoast SEO metabox
function fotoshare_gallery_remove_from_yoast_metabox($post_types) {
    if (isset($post_types['fotoshare_gallery'])) {
        unset($post_types['fotoshare_gallery']);
    }
    return $post_types;
}
add_filter('wpseo_accessible_post_types', 'fotoshare_gallery_remove_from_yoast_metabox');

// Remove fotoshare_gallery from Yoast SEO columns
function fotoshare_gallery_remove_from_yoast_columns($post_types) {
    if (isset($post_types['fotoshare_gallery'])) {
        unset($post_types['fotoshare_gallery']);
    }
    return $post_types;
}
add_filter('wpseo_seo_score_post_types', 'fotoshare_gallery_remove_from_yoast_columns');
add_filter('wpseo_readability_score_post_types', 'fotoshare_gallery_remove_from_yoast_columns');