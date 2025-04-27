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
            
            $submitButton.val('Kontrollin...').addClass('loading').prop('disabled', true);
            
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