jQuery.noConflict();
(function($) { 
  $(function() {
    // ready to roll

    // init social connect dialog
    $(".social_connect_form").dialog({ autoOpen: false, modal: true, resizable: false, maxHeight: 400, maxWidth: 600 });
    $(".social_connect_already_connected_form").dialog({ autoOpen: false, modal: true, resizable: false, maxHeight: 400, maxWidth: 600 });
    $('.social_connect_wordpress_form').dialog({ autoOpen: false, modal: true, resizable: false, maxHeight: 400, maxWidth: 600 });
    
    $(".social_connect_login").click(function() {
      if($(".social_connect_already_connected_form").length) {
        $(".social_connect_already_connected_form").dialog('open');
      } else {
        $(".social_connect_form").dialog('open');
      }
    });

    $(".social_connect_already_connected_form_not_you").click(function() {
      // delete 'already connected' dialog
      $(".social_connect_already_connected_form").dialog('close');
      $(".social_connect_already_connected_form").remove();
      
      // show main connect dialog
      $(".social_connect_form").dialog('open');
    });

    $(".social_connect_already_connected_user_another").click(function() {
      // hide 'already connected' dialog
      $(".social_connect_already_connected_form").dialog('close');
      // show main connect dialog
      $(".social_connect_form").dialog('open');
    });
    
    $(".social_connect_login_facebook").click(function() {
      var facebook_auth = $('.social_connect_facebook_auth');
      var client_id = facebook_auth.attr('client_id');
      var redirect_uri = facebook_auth.attr('redirect_uri');
      
      if(client_id == "") {
        alert("Social Connect plugin has not been configured for this provider")
        } else {
          window.open('https://graph.facebook.com/oauth/authorize?client_id=' + client_id + '&redirect_uri=' + redirect_uri + '&scope=email',
            '','scrollbars=no,menubar=no,height=400,width=800,resizable=yes,toolbar=no,status=no');
        }
    });

    $(".social_connect_login_twitter").click(function() {
      var google_auth = $('.social_connect_twitter_auth');
      var redirect_uri = google_auth.attr('redirect_uri');
      
      window.open(redirect_uri,'','scrollbars=no,menubar=no,height=400,width=800,resizable=yes,toolbar=no,status=no');
    });
    
    $(".social_connect_login_google").click(function() {
      var google_auth = $('.social_connect_google_auth');
      var redirect_uri = google_auth.attr('redirect_uri');
      
      window.open(redirect_uri,'','scrollbars=no,menubar=no,height=400,width=800,resizable=yes,toolbar=no,status=no');
    });

    $(".social_connect_login_wordpress").click(function() {
      $(".social_connect_form").dialog('close');
      $(".social_connect_wordpress_form").dialog('open');     
    });
    
    
    $(".social_connect_wordpress_proceed").click(function() {
      var wordpress_auth = $('.social_connect_wordpress_auth');
      var redirect_uri = wordpress_auth.attr('redirect_uri');
      redirect_uri = redirect_uri + "?wordpress_blog_url=" + encodeURIComponent($('.wordpress_blog_url').val());
      
      window.open(redirect_uri,'','scrollbars=yes,menubar=no,height=400,width=800,resizable=yes,toolbar=no,status=no');
    });
    
  });
})(jQuery);


window.wp_social_connect = function(config) {
  var form_id = '#loginform';
  
  if(!jQuery('#loginform').length) {
    // if register form exists, just use that
    if(jQuery('#registerform').length) {
      form_id = '#registerform';
    } else {
      // create the login form
      var login_uri = jQuery("#social_connect_login_form_uri").attr('href');
      jQuery('body').append("<form id='loginform' method='post' action='" + login_uri + "'></form>");
      jQuery('#loginform').append("<input type='hidden' id='redirect_to' name='redirect_to' value='" + window.location.href + "'>");
    }
  }
  
  jQuery.each(config, function(key, value) { 
    jQuery("#" + key).remove();
    jQuery(form_id).append("<input type='hidden' id='" + key + "' name='" + key + "' value='" + value + "'>");
  });  

  jQuery(form_id).submit();
}
