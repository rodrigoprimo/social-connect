jQuery.noConflict();
(function($) { 
  $(function() {
    // ready to roll
    
    // init social connect dialog
    $(".social_connect_form").dialog({ autoOpen: false, modal: true, resizable: false, maxHeight: 400, maxWidth: 600 });
    
    $(".social_connect_login").click(function() {
      $(".social_connect_form").dialog('open');
    });

    $(".socal_connect_login_facebook").click(function() {
      var facebook_auth = $('.social_connect_facebook_auth');
      var client_id = facebook_auth.attr('client_id');
      var redirect_uri = facebook_auth.attr('redirect_uri');
      
      if(client_id == "") {
        alert("Social Connect plugin has not been configured for this provider")
        } else {
          window.open('https://graph.facebook.com/oauth/authorize?client_id=' + client_id + '&redirect_uri=' + redirect_uri,
            '','scrollbars=no,menubar=no,height=400,width=800,resizable=yes,toolbar=no,status=no');
        }
        
    });
  });
})(jQuery);
