jQuery(document).ready(function () {
  // jscs:ignore validateLineBreaks

  // Here for each comment reply link of WordPress
  jQuery('.comment-reply-link').addClass('btn btn-sm btn-default');

  // Here for the submit button of the comment reply form
  jQuery('#submit, button[type=submit], html input[type=button], input[type=reset], input[type=submit]').addClass('btn btn-default');

  // Now we'll add some classes for the WordPress default widgets - let's go
  jQuery('.widget_rss ul').addClass('media-list');

  // Add Bootstrap style for drop-downs
  jQuery('.postform').addClass('form-control');

  // Add Bootstrap styling for tables
  jQuery('table#wp-calendar').addClass('table table-striped');

  jQuery('#submit, .tagcloud, button[type=submit], .comment-reply-link, .widget_rss ul, .postform, table#wp-calendar').show('fast');
});

function SparklingIsMobile() {
  return navigator.userAgent.match(/Android/i) || navigator.userAgent.match(/webOS/i) || navigator.userAgent.match(/iPhone/i) || navigator.userAgent.match(/iPod/i) || navigator.userAgent.match(/iPad/i) || navigator.userAgent.match(/BlackBerry/);
}

function generateMobileMenu() {
  var menu = jQuery('#masthead .site-navigation-inner .navbar-collapse > ul.nav');
  if (SparklingIsMobile() && jQuery(window).width() > 767) {
    menu.addClass('sparkling-mobile-menu');
  } else {
    menu.removeClass('sparkling-mobile-menu');
  }
}

// JQuery powered scroll to top
jQuery(document).ready(function () {
  //Check to see if the window is top if not then display button
  jQuery(window).on('scroll', function () {
    if (jQuery(this).scrollTop() > 100) {
      jQuery('.scroll-to-top').fadeIn();
    } else {
      jQuery('.scroll-to-top').fadeOut();
    }
  });

  //Click event to scroll to top
  jQuery('.scroll-to-top').on('click', function () {
    jQuery('html, body').animate({ scrollTop: 0 }, 800);
    return false;
  });

  jQuery('.sparkling-dropdown').on('click', function (evt) {
    jQuery(this).parent().toggleClass('open');
  });
  generateMobileMenu();
  jQuery(window).resize(function () {
    generateMobileMenu();
  });
});

// JQuery Sticy Header
jQuery(document).ready(function ($) {
  var $this, $adminbar, height;
  $this = $('.navbar-fixed-top');
  $adminbar = $('#wpadminbar');

  if (0 !== $this.length) {
    height = 0 !== $adminbar.length ? Math.abs($this.height() - $adminbar.height()) : $this.height();
    $this.parent('header').css('margin-bottom', height);
  }
});
