# WordPress Contributors Plugin

A WordPress plugin that allows multiple authors to be displayed for a post.

## Description

The WordPress Contributors Plugin enhances your WordPress site by allowing you to assign multiple contributors to a single post. This is perfect for collaborative content where multiple authors have contributed to an article.

### Features

- Add a metabox to the post editor for selecting multiple contributors
- Display contributors with their avatars at the end of each post
- Responsive design that works on all devices
- Clean, modern UI that integrates with WordPress core styles
- Links to each contributor's author page

## Installation

1. Upload the `wp-additional-contributors` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Edit a post and use the Contributors metabox to select multiple contributors

## Usage

### Admin Side

1. When editing a post, you'll see a new metabox labeled "Contributors" in the sidebar
2. Check the boxes next to the authors you want to credit as contributors
3. Save or publish the post

### Front End

The plugin automatically displays a "Contributors" section at the end of your post content showing:

- Contributor avatars
- Contributor names
- Contributor roles
- Links to contributor author pages

## Requirements

- WordPress 5.0 or higher
- PHP 7.0 or higher

## Development

### Coding Standards

This plugin follows the [WordPress Coding Standards](https://developer.wordpress.org/coding-standards/wordpress-coding-standards/).

### Unit Testing

To run the unit tests:

1. Install PHPUnit
2. Run `phpunit` from the plugin directory

## License

GPL v2 or later

## Credits

This plugin uses the following libraries:

- WordPress Core CSS/JS for UI elements

## Changelog

### 1.0

- Initial release
