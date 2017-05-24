jQuery(document).ready(function($){

	$('.logo-carousel').flexslider({
		minItems      : 1,
		maxItems      : 4,
		move          : 1,
		itemWidth     : 200,
		itemMargin    : 0,
		animation     : "slide",
		slideshow     : true,
		slideshowSpeed: 3000,
		directionNav  : false,
		controlNav    : false
	});

	// Testimonial
	$('.slider-arrow-controls').flexslider({
		controlNav: false
	});

	// Video Widget
	var videos = $('.video-widget');
	if ( videos.length ) {
		$.each(videos, function () {
			var play = $(this).find('.play-button'),
					pause = $(this).find('.pause-button'),
					isYoutube = $(this).hasClass('youtube');

			if ( isYoutube ) {
				var videoId = $(this).attr('data-video-id'),
						autoplay = parseInt($(this).attr('data-autoplay')),
						mute = parseInt($(this).attr('data-mute')),
						instance = $(this).YTPlayer({
							fitToBackground: true,
							videoId        : videoId,
							mute           : mute,
							playerVars     : {
								modestbranding: 0,
								autoplay      : autoplay,
								controls      : 0,
								showinfo      : 0,
								branding      : 0,
								rel           : 0,
								autohide      : 0
							}
						}),
						self = $(this);


				$(document).on('YTBGREADY', function () {
					var iframe = self.find('iframe'),
							height = iframe.height();

					if ( self.height() == 0 ) {
						//self.height(height);
					}
				});


				$(play).on('click', function (e) {
					e.preventDefault();
					var parent = $(this).parents('.video-widget'),
							instance = $(parent).data('ytPlayer').player;
					instance.playVideo();
				});

				$(pause).on('click', function (e) {
					e.preventDefault();
					var parent = $(this).parents('.video-widget'),
							instance = $(parent).data('ytPlayer').player;
					instance.pauseVideo();
				});

			} else {
				$(play).on('click', function (e) {
					e.preventDefault();
					var parent = $(this).parents('.video-widget'),
							instance = $(parent).data('vide'),
							video = instance.getVideoObject();

					video.play();
				});

				$(pause).on('click', function (e) {
					e.preventDefault();
					var parent = $(this).parents('.video-widget'),
							instance = $(parent).data('vide'),
							video = instance.getVideoObject();

					video.pause();
				});
			}
		});
	}

});

jQuery(window).load(function ($) {
	// "use strict";
	// Resetting testimonial parallax height
	if ( jQuery('.testimonial-section').length != 0 ) {
		testimonialHeight();
		setTimeout(function () {
			testimonialHeight();
		}, 3000);
	}
});

function testimonialHeight() {
	jQuery('.testimonial-section .parallax-window').css('height', jQuery('.testimonial-section .parallax-window .container').outerHeight() + 150);
	jQuery(window).trigger('resize').trigger('scroll');
}