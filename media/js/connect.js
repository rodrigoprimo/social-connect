jQuery.noConflict();
(function($) { 
  $(function() {
    // ready to roll
    $(".social_connect_form").dialog({ autoOpen: false, modal: true, resizable: false, maxHeight: 400, maxWidth: 600 });
    
    $(".social_connect_login").click(function() {
      $(".social_connect_form").dialog('open');
    });

  });
})(jQuery);
