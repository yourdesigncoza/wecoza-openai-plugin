jQuery(function($){
  $('#openai-send').on('click', function(){
    var prompt = $('#openai-prompt').val();
    if (!prompt) return alert('Please enter a prompt.');
    $('#openai-response').text('Loading…');
    $.post(
      OpenAIChat.ajax_url,
      {
        action: 'openai_chat',
        nonce: OpenAIChat.nonce,
        prompt: prompt
      },
      function(res) {
        if (res.success) {
          $('#openai-response').text(res.data);
        } else {
          $('#openai-response').text('Error: ' + res.data);
        }
      }
    );
  });
});
