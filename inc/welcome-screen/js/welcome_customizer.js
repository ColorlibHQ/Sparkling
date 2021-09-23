(function (api) {
  // Extends our custom "sparkling-pro-section" section.
  api.sectionConstructor['sparkling-recomended-section'] = api.Section.extend({
    // No events for this type of section.
    attachEvents: function () {},

    // Always make the section active.
    isContextuallyActive: function () {
      return true;
    },
  });
})(wp.customize);

jQuery(document).ready(function () {
  jQuery('.sparkling-dismiss-required-action').on('click', function () {
    var id = jQuery(this).attr('id'),
      action = jQuery(this).attr('data-action');
    jQuery.ajax({
      type: 'GET',
      data: { action: 'sparkling_dismiss_required_action', id: id, todo: action },
      dataType: 'html',
      url: sparklingWelcomeScreenObject.ajaxurl,
      beforeSend: function (data, settings) {
        jQuery('#' + id)
          .parent()
          .append('<div id="temp_load" style="text-align:center"><img src="' + sparklingWelcomeScreenObject.template_directory + '/inc/welcome-screen/img/ajax-loader.gif" /></div>');
      },
      success: function (data) {
        var container = jQuery('#' + data)
          .parent()
          .parent();
        var index = container.next().data('index');
        jQuery('.sparkling-actions-count .current-index').text(index);
        container.slideToggle().remove();
        if (jQuery('.recomended-actions_container > .epsilon-required-actions').length == 0) {
          jQuery('#accordion-section-sparkling_recomended-section .sparkling-actions-count').remove();

          if (jQuery('.recomended-actions_container > .epsilon-recommended-plugins').length == 0) {
            jQuery('.recomended-actions_container .succes').removeClass('hide');
            jQuery('#accordion-section-sparkling_recomended-section .section-title').text(jQuery('#accordion-section-sparkling_recomended-section .section-title').data('social'));
          } else {
            jQuery('#accordion-section-sparkling_recomended-section .section-title').text(jQuery('#accordion-section-sparkling_recomended-section .section-title').data('plugin_text'));
          }
        }
      },
      error: function (jqXHR, textStatus, errorThrown) {
        console.log(jqXHR + ' :: ' + textStatus + ' :: ' + errorThrown);
      },
    });
  });

  jQuery('.sparkling-recommended-plugin-button').on('click', function () {
    var id = jQuery(this).attr('id'),
      action = jQuery(this).attr('data-action');
    jQuery.ajax({
      type: 'GET',
      data: { action: 'sparkling_dismiss_recommended_plugins', id: id, todo: action },
      dataType: 'html',
      url: sparklingWelcomeScreenObject.ajaxurl,
      beforeSend: function (data, settings) {
        jQuery('#' + id)
          .parent()
          .append('<div id="temp_load" style="text-align:center"><img src="' + sparklingWelcomeScreenObject.template_directory + '/inc/welcome-screen/img/ajax-loader.gif" /></div>');
      },
      success: function (data) {
        var container = jQuery('#' + data)
          .parent()
          .parent();
        var index = container.next().data('index');
        jQuery('.sparkling-actions-count .current-index').text(index);
        container.slideToggle().remove();

        if (jQuery('.recomended-actions_container > .epsilon-recommended-plugins').length == 0) {
          jQuery('.recomended-actions_container .succes').removeClass('hide');
          jQuery('#accordion-section-sparkling_recomended-section .section-title').text(jQuery('#accordion-section-sparkling_recomended-section .section-title').data('social'));
        }
      },
      error: function (jqXHR, textStatus, errorThrown) {
        console.log(jqXHR + ' :: ' + textStatus + ' :: ' + errorThrown);
      },
    });
  });

  jQuery('#set_page_automatic').on('click', function (evt) {
    evt.preventDefault();
    var parent = jQuery(this).parent().parent();
    var container = jQuery(this).parent().parent().parent();

    jQuery.ajax({
      type: 'GET',
      data: { action: 'sparkling_set_frontpage' },
      dataType: 'html',
      url: sparklingWelcomeScreenObject.ajaxurl,
      beforeSend: function (data, settings) {
        parent.append('<div id="temp_load" style="text-align:center"><img src="' + sparklingWelcomeScreenObject.template_directory + '/inc/welcome-screen/img/ajax-loader.gif" /></div>');
      },
      success: function (data) {
        var index = container.next().data('index');
        jQuery('.sparkling-actions-count .current-index').text(index);
        container.slideToggle().remove();
        if (jQuery('.recomended-actions_container .epsilon-recommeded-actions-container').length == 0) {
          jQuery('.recomended-actions_container .succes').removeClass('hide');
          jQuery('#accordion-section-sparkling_recomended-section .sparkling-actions-count').remove();
          jQuery('#accordion-section-sparkling_recomended-section .section-title').text(jQuery('#accordion-section-sparkling_recomended-section .section-title').data('succes'));
        }
        wp.customize.previewer.refresh();
      },
      error: function (jqXHR, textStatus, errorThrown) {
        console.log(jqXHR + ' :: ' + textStatus + ' :: ' + errorThrown);
      },
    });
  });
});
