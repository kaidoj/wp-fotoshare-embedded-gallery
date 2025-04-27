/**
 * Fotoshare Embedded Gallery JavaScript
 */

(function($) {
    'use strict';
    
    $(document).ready(function() {
        // Focus on password input when form loads
        $('.fotoshare-password-form input[type="password"]').first().focus();
        
        // Smooth form submission with loading state
        $('.fotoshare-password-form').on('submit', function() {
            var $submitButton = $(this).find('input[type="submit"]');
            var originalText = $submitButton.val();
            var loadingSvg = '<svg class="fotoshare-loading-spinner" width="20" height="20" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><style>.spinner{transform-origin:center;animation:spin .75s infinite linear}@keyframes spin{100%{transform:rotate(360deg)}}</style><circle class="spinner" cx="12" cy="12" r="10" fill="none" stroke="currentColor" stroke-width="3" stroke-dasharray="30 65"/></svg>';
            
            // Hide text and append SVG loader
            $submitButton.data('original-text', originalText)
                         .prop('disabled', true)
                         .addClass('loading')
                         .html(loadingSvg);
            
            // Return true to allow the form to submit normally
            return true;
        });
        
        // Make iframe responsive to window size but respect container constraints
        function adjustIframeResponsiveness() {
            var contentWidth = $('.fotoshare-gallery-container').width();
            
            // Adjust height based on screen size
            if ($(window).width() <= 768) {
                // Already handled by CSS media queries
            } else if (contentWidth < 1200 && contentWidth >= 768) {
                // Medium sized screens - moderate height
                $('.fotoshare-gallery-container').css('height', '650px');
            }
            
            // Scroll to the gallery container when it's displayed
            if ($('.fotoshare-gallery-container').length) {
                $('html, body').animate({
                    scrollTop: $('.fotoshare-gallery-container').offset().top - 100 // Scroll position with space for header
                }, 500);
            }
        }
        
        // Run on page load and window resize
        adjustIframeResponsiveness();
        $(window).on('resize', adjustIframeResponsiveness);
    });
    
})(jQuery);