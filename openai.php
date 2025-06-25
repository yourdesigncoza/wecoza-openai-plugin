<?php
/*
Plugin Name: OpenAI Chat
Description: A simple WordPress plugin to send prompts to OpenAI and display the result.
Version: 0.1
Author: Your Name
*/

if ( ! defined( 'ABSPATH' ) ) exit;

// 1) Register a settings field for the API key
add_action( 'admin_init', function() {
    register_setting( 'openai_chat_settings', 'openai_api_key' );
    add_settings_section( 'openai_chat_section', 'OpenAI Settings', null, 'openai-chat' );
    add_settings_field(
        'openai_api_key',
        'OpenAI API Key',
        function() {
            $key = esc_attr( get_option('openai_api_key', '') );
            echo "<input type='password' name='openai_api_key' value='{$key}' style='width:300px;' />";
        },
        'openai-chat',
        'openai_chat_section'
    );
});
add_action( 'admin_menu', function() {
    add_options_page( 'OpenAI Chat', 'OpenAI Chat', 'manage_options', 'openai-chat', function(){
        echo '<form method="post" action="options.php">';
        settings_fields( 'openai_chat_settings' );
        do_settings_sections( 'openai-chat' );
        submit_button();
        echo '</form>';
    });
});

// 2) Enqueue JS and localize AJAX URL + nonce
add_action( 'wp_enqueue_scripts', function(){
    wp_enqueue_script( 'openai-chat', plugin_dir_url(__FILE__).'openai.js', ['jquery'], null, true );
    wp_localize_script( 'openai-chat', 'OpenAIChat',
        ['ajax_url' => admin_url('admin-ajax.php'),
         'nonce'    => wp_create_nonce('openai_chat_nonce')]
    );
});

// 3) Shortcode to render the chat UI
add_shortcode( 'openai_chat', function(){
    ob_start(); ?>
        <div class="card tab-content flex-1 phoenix-offcanvas-container">
          <!-- Chat pane -->
          <div class="tab-pane h-100 fade active show d-flex flex-column">
            
            <!-- Header -->
            <div class="card-header p-3 d-flex justify-content-between align-items-center">
              <div class="d-flex align-items-center">
                <!-- Off-canvas toggle (for mobile) -->
                <button class="btn btn-sm text-body-tertiary d-sm-none me-3"
                        data-phoenix-toggle="offcanvas"
                        data-phoenix-target="#chat-sidebar">
                  <i class="fas fa-chevron-left"></i>
                </button>

                <h5 class="mb-0">WECOZA AI Assistant <small class="text-muted" style="font-weight: normal; margin-left: 20px;">Still In BETA!</small></h5>
              </div>

              <!-- Action buttons -->
              <div class="d-flex">
                <button class="btn btn-icon btn-phoenix-primary me-1">
                  <i class="fas fa-phone"></i>
                </button>
                <button class="btn btn-icon btn-phoenix-primary me-1">
                  <i class="fas fa-video"></i>
                </button>
                <div class="dropdown">
                  <button class="btn btn-icon btn-phoenix-primary"
                          data-bs-toggle="dropdown">
                    <i class="fas fa-ellipsis-vertical"></i>
                  </button>
                  <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="#">Settings</a></li>
                    <li><a class="dropdown-item" href="#">Help</a></li>
                  </ul>
                </div>
              </div>
            </div>

            <!-- Messages -->
            <div class="card-body flex-grow-1 p-3 overflow-auto phoenix-scrollbar">
              <!-- Bot response appended here -->
              <pre id="openai-response" class="mb-0"></pre>
            </div>

            <!-- Input -->
            <div class="card-footer p-3 border-top">
              <div class="d-flex align-items-center">
                <textarea id="openai-prompt"
                          class="form-control me-2"
                          rows="1"
                          placeholder="Type your message…"></textarea>
                <button id="openai-send"
                        class="btn btn-primary">
                  <i class="fas fa-paper-plane me-1"></i> Send
                </button>
              </div>
            </div>

          </div>
        </div>

        <!-- Off-canvas sidebar (mobile) -->
        <div class="phoenix-offcanvas phoenix-offcanvas-start"
             id="chat-sidebar">
          <div class="offcanvas-header">
            <h5 class="offcanvas-title">Chats</h5>
            <button type="button" class="btn-close"
                    data-phoenix-dismiss="offcanvas"></button>
          </div>
          <div class="offcanvas-body">
            <ul class="list-group">
              <li class="list-group-item active">OpenAI Assistant</li>
              <!-- more chat entries… -->
            </ul>
          </div>
        </div>
    <?php
    return ob_get_clean();
});

// 4) AJAX handler (both logged-in and not)
foreach ( [ 'wp_ajax', 'wp_ajax_nopriv' ] as $action ) {
    add_action( "{$action}_openai_chat", function(){
        check_ajax_referer( 'openai_chat_nonce', 'nonce' );
        $prompt = sanitize_text_field( $_POST['prompt'] ?? '' );
        $api_key = get_option( 'openai_api_key', '' );
        if ( empty($api_key) || empty($prompt) ) {
            wp_send_json_error( 'Missing API key or prompt.' );
        }

        // Call OpenAI
        $response = wp_remote_post( 'https://api.openai.com/v1/chat/completions', [
            'headers' => [
                'Authorization' => 'Bearer '. $api_key,
                'Content-Type'  => 'application/json',
            ],
            'body' => wp_json_encode([
                'model'    => 'gpt-4o-mini',
                'messages' => [
                    [ 'role'=>'user', 'content'=> $prompt ]
                ],
                'max_tokens' => 300,
            ]),
            'timeout' => 20,
        ]);

        if ( is_wp_error($response) ) {
            wp_send_json_error( $response->get_error_message() );
        }

        $body = json_decode( wp_remote_retrieve_body($response), true );
        if ( isset($body['choices'][0]['message']['content']) ) {
            wp_send_json_success( trim($body['choices'][0]['message']['content']) );
        } else {
            wp_send_json_error( 'Unexpected API response.' );
        }
    });
}
