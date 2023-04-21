(function($, Drupal) {
  Drupal.behaviors.pfb_assistant = {
    attach: function(context, settings)
    {
      if (context === document) {
        console.log('pfbv4 fire.'); // Once <--

        if (settings) {
          var is_admin = drupalSettings.pfb_assistant.is_admin;
          var is_anonymous = drupalSettings.pfb_assistant.is_anonymous;
          var domain_uuid = drupalSettings.pfb_assistant.domain_uuid;
          var endpoint = drupalSettings.pfb_assistant.domain;
          var terms = drupalSettings.pfb_assistant.terms;
          var is_node = drupalSettings.pfb_assistant.is_node;
          var ip_stack_key = drupalSettings.pfb_assistant.ip_stack_key;
          var content_type = drupalSettings.pfb_assistant.content_type;
          var ip_recheck = drupalSettings.pfb_assistant.ip_recheck;
        }

        var crawlerAgentRegex = /bot|brightedge|google|baidu|bing|msn|duckduckbot|teoma|slurp|yandex/i;
        if(!is_admin && !crawlerAgentRegex.test(navigator.userAgent) && domain_uuid) {

          // Each page, load up the client information for use later on.
          var d = $.fn.deviceDetector;

          var profilebuilderv4 = new PFBConnecv4({
           domain_uuid: domain_uuid,
           client: JSON.stringify(d.getInfo()),
           categories: terms != null ? Object.values(terms) : null,
           endpoint: endpoint,
           location: JSON.stringify(location),
           cookies: JSON.stringify(getCookies()),
           pfbuuid: localStorage.getItem('pfbuuid'),
           is_node: is_node,
           content_type: content_type,
           ip_recheck: ip_recheck,
         });

          profilebuilderv4.fire();
        }
      }
    }
  }
} (jQuery, Drupal));

function getCookies() {
  var pairs = document.cookie.split(";");
  var cookies = {};
  for (var i=0; i<pairs.length; i++){
    var pair = pairs[i].split("=");
    cookies[(pair[0]+'').trim()] = unescape(pair.slice(1).join('='));
  }
  return cookies;
}
