# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a WordPress plugin called "OpenAI Chat" that provides a simple interface for users to send prompts to OpenAI's API and display responses. The plugin uses a shortcode `[openai_chat]` to render the chat interface.

## Development Commands

This plugin has no build process, package manager, or automated testing. Development is done by directly editing the PHP and JavaScript files.

### Common Tasks
- **Activate plugin**: WordPress Admin > Plugins > OpenAI Chat > Activate
- **Configure API key**: WordPress Admin > Settings > OpenAI Chat
- **Test functionality**: Add `[openai_chat]` shortcode to any page or post

## Architecture

### Plugin Structure
```
openai.php    # Main plugin file - handles settings, shortcode, and API calls
openai.js     # Frontend JavaScript - handles user interactions and AJAX
```

### Key Components

1. **Settings Management** (openai.php:11-34)
   - API key storage using WordPress options
   - Admin settings page under Settings > OpenAI Chat

2. **Frontend Interface** (openai.php:45-126)
   - Shortcode `[openai_chat]` renders Bootstrap-based chat UI
   - JavaScript enqueued with proper localization

3. **API Integration** (openai.php:128-165)
   - AJAX handler for OpenAI API calls
   - Uses GPT-4o-mini model
   - Proper sanitization and error handling

### Integration Points
- WordPress Options API for settings
- WordPress AJAX system for frontend-backend communication
- WordPress shortcode system for rendering
- Bootstrap CSS (assumed from theme)

## Important Development Notes

### CSS Styles
- ALL CSS styles must be added to: `/opt/lampp/htdocs/wecoza/wp-content/themes/wecoza_3_child_theme/includes/css/ydcoza-styles.css`
- Do NOT create separate CSS files in the plugin directory

### Security Considerations
- Always use nonces for AJAX requests
- Sanitize all user inputs with `sanitize_text_field()`
- Validate API responses before output
- Never expose the API key in frontend code

### WordPress Hooks Used
- `admin_menu` - Register settings page
- `admin_init` - Register settings
- `wp_enqueue_scripts` - Enqueue JavaScript
- `wp_ajax_*` and `wp_ajax_nopriv_*` - AJAX handlers

## Common Modifications

### Adding New Features
When adding features, maintain the simple structure:
- PHP logic goes in `openai.php`
- JavaScript interactions go in `openai.js`
- CSS must go in the theme's `ydcoza-styles.css`

### Updating API Integration
The OpenAI API call is in the `openai_send_prompt` function (openai.php:128-165). Key parameters:
- Model: `gpt-4o-mini`
- Temperature: 0.7
- Max tokens: 1000

### Modifying the UI
The chat interface HTML is generated in the shortcode function (openai.php:45-126). It uses Bootstrap classes for styling.

## Testing

Manual testing only:
1. Ensure API key is saved in settings
2. Add shortcode to a test page
3. Submit a prompt and verify response
4. Check browser console for JavaScript errors
5. Test with both logged-in and logged-out users

## Known Limitations
- No conversation history
- No build process or asset optimization
- Basic error handling (alerts only)
- Single model configuration
- No rate limiting