/**
 * Fotoshare Gallery Admin JavaScript
 */

(function($) {
    'use strict';
    
    $(document).ready(function() {
        // Update color text field when color picker changes
        $('.fotoshare-color-picker').on('change', function() {
            // Get the input ID and isolate the base ID
            var inputId = $(this).attr('id');
            var baseId = inputId;
            
            // Update the associated text field with the new color value
            $('#' + baseId + '_text').val($(this).val());
        });
    });
    
})(jQuery);