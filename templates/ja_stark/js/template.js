// Add Inview
jQuery(document).ready(function () {
  jQuery('.t4-section-inview').bind('inview', function(event, visible) {
    if (visible) {
      jQuery(this).addClass('t4-inview');
      var animateClass = jQuery(this).find('.animated').data('animated-type');
      jQuery(this).find('.animated').addClass(animateClass);
    }
  });
  // check show megamenu
  jQuery(document).find('.t4-megamenu').bind('inview',function(event,visible){
    if (visible) {
      jQuery('body').addClass('nav-open');
    }else{
      jQuery('body').removeClass('nav-open');
    }
  });
});