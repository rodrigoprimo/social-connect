jQuery.noConflict();
(function($) { 
  $(function() {
    // ready to roll
    var _social_connect_wordpress_form = $($('.social_connect_wordpress_form')[0]);
    _social_connect_wordpress_form.dialog({ autoOpen: false, modal: true, dialogClass: 'social-connect-dialog', resizable: false, maxHeight: 400, width:350, maxWidth: 600 });

    var _social_connect_openid_form = $($('.social_connect_openid_form')[0]);
    _social_connect_openid_form.dialog({ autoOpen: false, modal: true, dialogClass: 'social-connect-dialog', resizable: false, maxHeight: 400, width:350, maxWidth: 600 });

	var closedialog;
    var _is_already_connected = $(".social_connect_already_connected_form")[0];
    if(_is_already_connected) {
      var _social_connect_already_connected_form = $(_is_already_connected);
      var _already_connected_provider = _social_connect_already_connected_form.attr('provider');
      _social_connect_already_connected_form.dialog({ 
							autoOpen: false, 
							modal: true, 
							dialogClass: 'social-connect-dialog', 
							resizable: false, 
							maxHeight: 400, 
							maxWidth: 600,
					        open: function(){closedialog = 1;$(document).bind('click', overlay_click_close);},
					        focus: function(){closedialog = 0;},
					        close: function(){$(document).unbind('click', overlay_click_close);}
						});
    }
    
    var _do_google_connect = function() {
      var google_auth = $('.social_connect_google_auth');
      var redirect_uri = google_auth.attr('redirect_uri');
      
      window.open(redirect_uri,'','scrollbars=no,menubar=no,height=400,width=800,resizable=yes,toolbar=no,status=no');
    };

    var _do_yahoo_connect = function() {
      var yahoo_auth = $('.social_connect_yahoo_auth');
      var redirect_uri = yahoo_auth.attr('redirect_uri');
      
      window.open(redirect_uri,'','scrollbars=no,menubar=no,height=400,width=800,resizable=yes,toolbar=no,status=no');
    };

    var _do_openid_connect = function(e) {
      var openid_auth = $('.social_connect_openid_auth');
      var redirect_uri = openid_auth.attr('redirect_uri');
      var context = $(e.target).parents('.social_connect_openid_form')[0];
      var blog_name = $('.openid_url', context).val();
      var blog_url = "http://" + blog_name;
      redirect_uri = redirect_uri + "?openid_url=" + encodeURIComponent(blog_url);
	
      window.open(redirect_uri,'','scrollbars=yes,menubar=no,height=400,width=800,resizable=yes,toolbar=no,status=no');
    };

    var _do_twitter_connect = function() {
      var twitter_auth = $('.social_connect_twitter_auth');
      var redirect_uri = twitter_auth.attr('redirect_uri');
    
      window.open(redirect_uri,'','scrollbars=no,menubar=no,height=400,width=800,resizable=yes,toolbar=no,status=no');
    };

    var _do_liveid_connect = function() {
      var liveid_auth = $('.social_connect_liveid_auth');
      var appid = liveid_auth.attr('appid');      
      var sec_algo = liveid_auth.attr('sec_algo');
      var redirect_uri = liveid_auth.attr('redirect_uri');
    
      if(appid == "" || sec_algo == "") {
        alert("Social Connect plugin has not been configured for this provider")
      } else {
        window.open('http://login.live.com/wlogin.srf?appid=' + appid + '&alg=' + sec_algo,
            '','scrollbars=no,menubar=no,height=500,width=900,resizable=yes,toolbar=no,status=no');
      }
    };

    var _do_wordpress_connect = function(e) {

      var wordpress_auth = $('.social_connect_wordpress_auth');
      var redirect_uri = wordpress_auth.attr('redirect_uri');
      var context = $(e.target).parents('.social_connect_wordpress_form')[0];
      var blog_name = $('.wordpress_blog_url', context).val();
      var blog_url = "http://" + blog_name + ".wordpress.com";
      redirect_uri = redirect_uri + "?wordpress_blog_url=" + encodeURIComponent(blog_url);
	
      window.open(redirect_uri,'','scrollbars=yes,menubar=no,height=400,width=800,resizable=yes,toolbar=no,status=no');
    };
    
    var _do_facebook_connect = function() {
      var facebook_auth = $('.social_connect_facebook_auth');
      var client_id = facebook_auth.attr('client_id');
      var redirect_uri = facebook_auth.attr('redirect_uri');
    
      if(client_id == "") {
        alert("Social Connect plugin has not been configured for this provider")
      } else {
        window.open('https://graph.facebook.com/oauth/authorize?client_id=' + client_id + '&redirect_uri=' + redirect_uri + '&scope=email',
            '','scrollbars=no,menubar=no,height=400,width=800,resizable=yes,toolbar=no,status=no');
      }
    };

	// Close dialog if open and user clicks anywhere outside of it
	function overlay_click_close() {
		if (closedialog) {
			_social_connect_already_connected_form.dialog('close');
		}
		closedialog = 1;
	}

    $(".social_connect_already_connected_form_not_you").click(function() {
      _is_already_connected = false;
      _social_connect_already_connected_form.dialog('close');
      _social_connect_already_connected_form.remove();
    });

    $(".social_connect_already_connected_user_another").click(function() {
      _is_already_connected = false;
      _social_connect_already_connected_form.dialog('close');
      _social_connect_already_connected_form.remove();
    });
    
    $(".social_connect_login_facebook").click(function() {
      if(_is_already_connected) {
        _social_connect_already_connected_form.dialog('open');
		closedialog = 0;
      } else {
        _do_facebook_connect();
      }
    });

    $(".social_connect_login_continue_facebook").click(function() {
      _do_facebook_connect();
    });
    
    $(".social_connect_login_twitter").click(function() {
      if(_is_already_connected) {
        _social_connect_already_connected_form.dialog('open');
		closedialog = 0;
      } else {
        _do_twitter_connect();
      }  
    });
    
    $(".social_connect_login_continue_twitter").click(function() {
      _do_twitter_connect();
    });

    $(".social_connect_login_liveid").click(function() {
      if(_is_already_connected) {
        _social_connect_already_connected_form.dialog('open');
		closedialog = 0;
      } else {
        _do_liveid_connect();
      }  
    });
    
    $(".social_connect_login_continue_liveid").click(function() {
      _do_liveid_connect();
    });
    
    $(".social_connect_login_google").click(function() {
      if(_is_already_connected) {
        _social_connect_already_connected_form.dialog('open');
		closedialog = 0;
      } else {
        _do_google_connect();
      }
    });

    $(".social_connect_login_continue_google").click(function() {
      _do_google_connect();
    });

    $(".social_connect_login_yahoo").click(function() {
      if(_is_already_connected) {
        _social_connect_already_connected_form.dialog('open');
		closedialog = 0;
      } else {
        _do_yahoo_connect();
      }
    });

    $(".social_connect_login_continue_yahoo").click(function() {
      _do_yahoo_connect();
    });

    $(".social_connect_login_openid").click(function() {
      if(_is_already_connected) {
        _social_connect_already_connected_form.dialog('open');
		closedialog = 0;
      } else {
        _social_connect_openid_form.dialog('open');
      }
    });

    $(".social_connect_openid_proceed").click(function(e) {  
      _do_openid_connect(e);
    });
    
    $(".social_connect_login_wordpress").click(function() {
      if(_is_already_connected) {
        _social_connect_already_connected_form.dialog('open');
		closedialog = 0;
      } else {
        _social_connect_wordpress_form.dialog('open');     
      }
    });
    
    
    $(".social_connect_wordpress_proceed").click(function(e) {
      _do_wordpress_connect(e);
    });
    
  });
})(jQuery);


window.wp_social_connect = function(config) {
  jQuery('#loginform').unbind('submit.simplemodal-login');

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

  if(jQuery("#simplemodal-login-form").length) {
    var current_url = window.location.href;
    jQuery("#redirect_to").remove();
    jQuery(form_id).append("<input type='hidden' id='redirect_to' name='redirect_to' value='" + current_url + "'>");
  }

  jQuery(form_id).submit();
}
