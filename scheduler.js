/**
 * @file
 * Attaches behaviors for the Schedule module.
 */
(function ($, Drupal) {

  "use strict";

  Drupal.behaviors.scheduleDetailsSummaries = {
    attach: function (context) {
      var $context = $(context);
      $context.find('.scheduler-form').drupalSetSummary(function (context) {
        var $context = $(context);
        var publish_on = $context.find('.field-name-publish-on input').val(),
          unpublish_on = $context.find('.field-name-unpublish-on input').val();
        var message_elements = [], message;
        if (unpublish_on || publish_on) {
          if (publish_on) {
            var publish_on_time = $context.find('.field-name-publish-on input[type="time"]').val();
            if (publish_on_time) {
              message = Drupal.t('Publish on @publish_on at @time', {'@publish_on': publish_on, '@time': publish_on_time});
            }
            else {
              message = Drupal.t('Publish on @publish_on', {'@publish_on': publish_on});
            }
            message_elements.push(message);
          }
          if (unpublish_on) {
            var unpublish_on_time = $context.find('.field-name-unpublish-on input[type="time"]').val();
            if (unpublish_on_time) {
              message = Drupal.t('Unpublish on @Unpublish at @time', {'@Unpublish': unpublish_on, '@time': unpublish_on_time});
            }
            else {
              message = Drupal.t('Unpublish on @Unpublish', {'@Unpublish': unpublish_on});
            }
            message_elements.push(message);
          }
        }
        else {
          message_elements.push(Drupal.t('No scheduling set.'));
        }
        return message_elements.join('<br />');
      });
    }
  };

})(jQuery, Drupal);
