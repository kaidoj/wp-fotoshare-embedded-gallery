<?php
/**
 * Core functionality for the Fotoshare Embedded Gallery plugin
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Main Fotoshare Gallery class
 */
class Fotoshare_Gallery {
    /**
     * Constructor
     */
    public function __construct() {
        // Nothing to do here yet
    }

    /**
     * Get gallery data by ID
     *
     * @param int $post_id Gallery post ID
     * @return array|bool Gallery data or false if not found
     */
    public static function get_gallery_data($post_id) {
        $post = get_post($post_id);
        
        if (!$post || 'fotoshare_gallery' !== $post->post_type) {
            return false;
        }
        
        return array(
            'id'          => $post_id,
            'title'       => get_the_title($post_id),
            'url'         => get_post_meta($post_id, '_fotoshare_url', true),
            'password'    => get_post_meta($post_id, '_fotoshare_password', true),
        );
    }

    /**
     * Verify gallery password
     *
     * @param int $gallery_id Gallery post ID
     * @param string $password Password to verify
     * @return bool True if password is correct
     */
    public static function verify_password($gallery_id, $password) {
        $stored_password = get_post_meta($gallery_id, '_fotoshare_password', true);
        return $stored_password === $password;
    }
}