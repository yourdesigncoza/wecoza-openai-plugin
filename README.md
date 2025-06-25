Below is a simple **README.md** you can drop into the root of your repo:

```markdown
# OpenAI Chat

A lightweight WordPress plugin that lets you send prompts to OpenAI and display the AI’s response via a front-end chat UI.

---

## Features

- Registers an **OpenAI API Key** field under **Settings → OpenAI Chat**  
- Enqueues `openai.js`, which binds a “Send” button to a jQuery AJAX call  
- Provides a `[openai_chat]` shortcode to render a chat interface anywhere  
- Handles AJAX securely with a nonce and returns the AI’s reply in real time

---

## Requirements

- **WordPress** 5.0+  
- **PHP** 7.4+  
- An **OpenAI API Key** (set in the plugin settings)

---

## Installation

1. **Clone** or **upload** the plugin folder into your WordPress `wp-content/plugins/` directory.  
2. In your WordPress admin, go to **Plugins → Installed Plugins** and **Activate** “OpenAI Chat.”  
3. Go to **Settings → OpenAI Chat**, enter your **OpenAI API Key**, and save.

---

## Usage

1. In any post, page or template, add the shortcode:  
```

\[openai\_chat]

````
2. On the front end, type your prompt into the input box and click **Send**.  
3. The AI’s response will appear below the chat form via AJAX.

---

## Example

```html
<!-- In a page or widget -->
[openai_chat]
````

```php
<!-- In a PHP template -->
<?php echo do_shortcode('[openai_chat]'); ?>
```

---

## Development

* **openai.php** contains the plugin logic (settings registration, shortcode, AJAX handler)
* **openai.js** handles the front-end click event and AJAX request

Feel free to submit issues or pull requests!

---

## License

This plugin is released under the [GPLv2+](https://www.gnu.org/licenses/gpl-2.0.html).

```
```
