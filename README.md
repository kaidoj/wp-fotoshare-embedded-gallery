# Fotoshare Embedded Gallery

A WordPress plugin that allows you to create password-protected embedded galleries from Fotoshare.

## Description

This plugin enables you to embed password-protected Fotoshare galleries on your WordPress site. Visitors need to enter the correct password to view the gallery content, which is then displayed as an iframe on the page.

## Features

- Password protection for gallery access
- Customizable text and button colors via settings page
- Estonian language interface by default
- Multiple galleries with different passwords
- Cookie-based authentication remembers returning visitors
- Responsive design for all screen sizes

## Installation

1. Upload the `fotoshare-embedded-gallery` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to 'Fotoshare Galleries' in the admin menu to start creating galleries

## Usage

### Adding a Gallery

1. Navigate to "Fotoshare Galleries" → "Add New" in your WordPress admin
2. Enter a name for your gallery
3. Add the Fotoshare URL in the "Gallery Settings" section
4. Set a password for the gallery
5. Publish the gallery

### Displaying a Gallery on a Page

Use the shortcode `[fotoshare_gallery]` in any page or post where you want the password form to appear.

When visitors enter the correct password, the corresponding gallery will be displayed.

## Customization

### Text Customization

You can customize all text elements from the Settings page:

1. Go to "Fotoshare Galleries" → "Settings"
2. Modify any of the text fields:
   - Form Title
   - Password Field Label
   - Password Field Placeholder
   - Submit Button Text
   - Error Message

### Style Customization

The Settings page also allows you to customize button colors:

- Button Color - The main color of the submit button
- Button Hover Color - The color when hovering over the button

## Requirements

- WordPress 5.0 or higher
- PHP 7.0 or higher

## License

This plugin is licensed under the GPL v2 or later.

## Support

For support questions, feature requests or bug reports, please contact the plugin author.