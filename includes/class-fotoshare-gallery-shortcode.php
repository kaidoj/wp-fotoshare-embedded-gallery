<?php
/**
 * Shortcode handler for the Fotoshare Embedded Gallery plugin
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Fotoshare Gallery Shortcode Handler
 */
class Fotoshare_Gallery_Shortcode {
    /**
     * Constructor
     */
    public function __construct() {
        // Register shortcode
        add_shortcode('fotoshare_gallery', array($this, 'render_shortcode'));
    }

    /**
     * Render the shortcode
     *
     * @param array $atts Shortcode attributes
     * @return string Rendered HTML
     */
    public function render_shortcode($atts) {
        $atts = shortcode_atts(array(
            'id' => 0,
        ), $atts, 'fotoshare_gallery');
        
        $post_id = absint($atts['id']);
        if (!$post_id) {
            return '<p>' . __('Gallery ID not specified.', 'fotoshare-embedded-gallery') . '</p>';
        }
        
        // Get gallery data
        $gallery_data = Fotoshare_Gallery::get_gallery_data($post_id);
        if (!$gallery_data) {
            return '<p>' . __('Gallery not found.', 'fotoshare-embedded-gallery') . '</p>';
        }
        
        $fotoshare_url = $gallery_data['url'];
        $required_password = $gallery_data['password'];
        $welcome_text = $gallery_data['welcome_text'];
        
        // Generate unique form ID based on post ID
        $form_id = 'fotoshare-gallery-form-' . $post_id;
        
        // Set up nonce for the form
        $nonce_action = 'fotoshare_verify_' . $post_id;
        $nonce_field = wp_nonce_field($nonce_action, 'fotoshare_nonce', true, false);
        
        // Start output buffering
        ob_start();
        
        // Check if password is submitted and correct
        $password_correct = false;
        if (isset($_POST['fotoshare_password']) && isset($_POST['fotoshare_nonce']) && wp_verify_nonce($_POST['fotoshare_nonce'], $nonce_action)) {
            $submitted_password = sanitize_text_field($_POST['fotoshare_password']);
            if (Fotoshare_Gallery::verify_password($post_id, $submitted_password)) {
                $password_correct = true;
                
                // Set a cookie that expires in 24 hours
                setcookie('fotoshare_gallery_' . $post_id, md5($required_password), time() + 86400, COOKIEPATH, COOKIE_DOMAIN);
            }
        } else if (isset($_COOKIE['fotoshare_gallery_' . $post_id])) {
            // Check if the cookie is set and matches
            if ($_COOKIE['fotoshare_gallery_' . $post_id] === md5($required_password)) {
                $password_correct = true;
            }
        }
        
        if ($password_correct) {
            // Display the embedded gallery
            $this->render_gallery($fotoshare_url);
        } else {
            // Display the password form
            $this->render_password_form($post_id, $form_id, $welcome_text, $nonce_field, $nonce_action);
        }
        
        return ob_get_clean();
    }
    
    /**
     * Render the gallery iframe
     *
     * @param string $fotoshare_url The URL to the Fotoshare gallery
     */
    private function render_gallery($fotoshare_url) {
        ?>
        <div class="fotoshare-gallery-container">
            <iframe src="<?php echo esc_url($fotoshare_url); ?>" class="fotoshare-iframe" frameborder="0" allowfullscreen></iframe>
        </div>
        <?php
    }
    
    /**
     * Render the password form
     *
     * @param int $post_id The gallery post ID
     * @param string $form_id Unique form ID
     * @param string $welcome_text Welcome message to display
     * @param string $nonce_field The nonce field HTML
     * @param string $nonce_action The nonce action name
     */
    private function render_password_form($post_id, $form_id, $welcome_text, $nonce_field, $nonce_action) {
        ?>
        <div class="fotoshare-password-form-container">
            <div class="fotoshare-welcome-text">
                <?php echo wpautop($welcome_text); ?>
            </div>
            
            <form id="<?php echo esc_attr($form_id); ?>" class="fotoshare-password-form" method="post">
                <?php echo $nonce_field; ?>
                <div class="fotoshare-form-field">
                    <label for="fotoshare_password_input_<?php echo $post_id; ?>"><?php _e('Enter Password', 'fotoshare-embedded-gallery'); ?></label>
                    <input type="password" id="fotoshare_password_input_<?php echo $post_id; ?>" name="fotoshare_password" required placeholder="<?php esc_attr_e('Enter gallery password', 'fotoshare-embedded-gallery'); ?>">
                </div>
                <div class="fotoshare-form-submit">
                    <input type="submit" value="<?php esc_attr_e('View Gallery', 'fotoshare-embedded-gallery'); ?>">
                </div>
                <?php if (isset($_POST['fotoshare_password']) && isset($_POST['fotoshare_nonce']) && wp_verify_nonce($_POST['fotoshare_nonce'], $nonce_action)): ?>
                    <p class="fotoshare-error"><?php _e('Incorrect password. Please try again.', 'fotoshare-embedded-gallery'); ?></p>
                <?php endif; ?>
            </form>
        </div>
        <?php
    }
}

// Initialize shortcode
new Fotoshare_Gallery_Shortcode();