# WP Additional Contributors

Enhance your WordPress posts with multiple contributors! This plugin allows you to assign and display multiple contributors to a single post, perfect for collaborative content where multiple authors have contributed to an article.

## Demo - https://www.loom.com/share/3e749cdbd09243e0b158d6635a7ccb30?sid=804f4d7c-232a-4d2c-a165-d05e5f9c0085
## Download Plugin - https://github.com/vanshk141999/wac-plugin/releases/tag/v1.0.0

## Features

- Add multiple contributors to any post
- Customizable contributor display box
- Show/hide contributor avatars
- Display contributor bios and website links
- Configurable display order of contributor information
- Author archive pages include posts where they're listed as contributors
- Fully responsive design
- Clean and modern UI

## Requirements

- WordPress 6.7 or higher
- PHP 7.4 or higher

## Installation

1. Clone the repository
2. `composer i` to install the php packages
3. `vendor/bin/phpcs -ps . --standard=phpcs.xml` to run phpcs

## Usage

### Adding Contributors

1. Edit any post
2. Look for the "Contributors" meta box in the post editor
3. Select the users you want to add as contributors
4. Customize the contributor box title if desired
5. Update/publish your post

### Display Options

You can customize how contributors are displayed:

- Show/hide avatars
- Show/hide contributor names
- Show/hide biographical info
- Show/hide website links
- Arrange the display order of elements

## Development

### Coding Standards

This plugin follows the [WordPress Coding Standards](https://developer.wordpress.org/coding-standards/wordpress-coding-standards/). To contribute, please ensure your code adheres to these standards.

### Testing

The plugin includes a test suite. To run the tests:

1. Install PHPUnit
2. Run `sh bin/install-wp-tests.sh wp-test root '' localhost 6.7` to set up the testing environment
3. Run `vendor/bin/phpunit` to execute the tests

## Credits

This plugin uses:

- wp-jquery-ui-sortable - jQuery UI Sortable for WordPress to sort the order of elements in global settings - http://jqueryui.com/demos/sortable
- Lucide Icons - https://lucide.dev/icons/

## License

GPL v3.0 or later

## Changelog

### 1.0.0

- Initial release
- Add multiple contributors to posts
- Customizable contributor display
- Author archive integration
- Responsive design implementation
