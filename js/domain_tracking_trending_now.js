(function($, Drupal) {
  Drupal.behaviors.domain_tracking_trending_now = {
    attach: function(context, settings)
    {
      if (context === document) {

        console.log(drupalSettings.name)


          console.log('running');


      }
    }
  }
} (jQuery, Drupal));

