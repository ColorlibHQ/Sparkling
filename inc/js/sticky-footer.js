jQuery(function($){
  $(window).on('load resize', function(){
    //Clear out previous set height and obtain full footer height
    $ht = $('#footer-area').height('auto').outerHeight();
    //Set CSS for sticky footer to work properly
    $('.sticky-footer-wrapper').css('margin', '0 auto -' + $ht + 'px');
    $('#footer-area, .sticky-footer-push').height($ht);
  });
});
