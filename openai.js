jQuery(function($){
  
  function addUserMessage(message) {
    var messageHtml = '<div class="d-flex justify-content-end mb-3">' +
      '<div class="chat-message-sent">' +
        '<div class="bg-primary text-white rounded-3 p-3 shadow-sm" style="max-width: 80%;">' +
          '<p class="mb-0">' + escapeHtml(message) + '</p>' +
        '</div>' +
        '<small class="text-muted mt-1 d-block text-end">Just now</small>' +
      '</div>' +
    '</div>';
    $('#openai-messages').append(messageHtml);
    scrollToBottom();
  }
  
  function addBotMessage(message) {
    var messageHtml = '<div class="d-flex justify-content-start mb-3">' +
      '<div class="chat-message-received">' +
        '<div class="bg-light border rounded-3 p-3 shadow-sm" style="max-width: 80%;">' +
          '<div class="openai-content">' + message + '</div>' +
        '</div>' +
        '<small class="text-muted mt-1 d-block">Just now</small>' +
      '</div>' +
    '</div>';
    $('#openai-messages').append(messageHtml);
    scrollToBottom();
  }
  
  function addErrorMessage(error) {
    var messageHtml = '<div class="d-flex justify-content-center mb-3">' +
      '<div class="alert alert-danger alert-dismissible fade show" role="alert" style="max-width: 80%;">' +
        '<i class="fas fa-exclamation-triangle me-2"></i>' +
        '<strong>Error:</strong> ' + escapeHtml(error) +
        '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' +
      '</div>' +
    '</div>';
    $('#openai-messages').append(messageHtml);
    scrollToBottom();
  }
  
  function showLoading() {
    $('#openai-loading').removeClass('d-none');
    scrollToBottom();
  }
  
  function hideLoading() {
    $('#openai-loading').addClass('d-none');
  }
  
  function scrollToBottom() {
    var messagesContainer = $('#openai-messages').parent();
    messagesContainer.scrollTop(messagesContainer[0].scrollHeight);
  }
  
  function escapeHtml(text) {
    var map = {
      '&': '&amp;',
      '<': '&lt;',
      '>': '&gt;',
      '"': '&quot;',
      "'": '&#039;'
    };
    return text.replace(/[&<>"']/g, function(m) { return map[m]; });
  }
  
  // Handle send button click
  $('#openai-send').on('click', function(){
    var prompt = $('#openai-prompt').val().trim();
    if (!prompt) {
      alert('Please enter a message.');
      return;
    }
    
    // Add user message to chat
    addUserMessage(prompt);
    
    // Clear input and disable send button
    $('#openai-prompt').val('');
    $('#openai-send').prop('disabled', true);
    
    // Show loading spinner
    showLoading();
    
    // Send AJAX request
    $.post(
      OpenAIChat.ajax_url,
      {
        action: 'openai_chat',
        nonce: OpenAIChat.nonce,
        prompt: prompt
      },
      function(res) {
        hideLoading();
        $('#openai-send').prop('disabled', false);
        
        if (res.success) {
          addBotMessage(res.data);
        } else {
          addErrorMessage(res.data || 'An unexpected error occurred.');
        }
      }
    ).fail(function() {
      hideLoading();
      $('#openai-send').prop('disabled', false);
      addErrorMessage('Network error. Please check your connection and try again.');
    });
  });
  
  // Handle Enter key in textarea
  $('#openai-prompt').on('keypress', function(e) {
    if (e.which === 13 && !e.shiftKey) {
      e.preventDefault();
      $('#openai-send').click();
    }
  });
  
  // Auto-resize textarea
  $('#openai-prompt').on('input', function() {
    this.style.height = 'auto';
    this.style.height = (this.scrollHeight) + 'px';
  });
  
});
