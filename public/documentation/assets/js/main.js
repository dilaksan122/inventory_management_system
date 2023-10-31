(function ($) {
    "use strict";

    $('.ic-doc-nav').singlePageNav();

    /* li click active class */
    $('.ic-doc-nav ul li a').on('click', function(){
        $('.ic-doc-nav ul li a').removeClass('active');
        $(this).addClass('active');
    });

    /* nav btn and menu show hide */
    function navBtn(){
        if($(window).width()<1200){

            $('.ic-nav-btn').on('click', function(e){
                e.stopPropagation();
                $('body').toggleClass('ic-nav-open');
            });
            $('.ic-left-sidebar').on('click', function(e){
                e.stopPropagation();
            });
            $('body').on('click', function(){
                $('body').removeClass('ic-nav-open');
            });
        }
    }
    navBtn();


    /* when resize window */
    $(window).on('resize', function(){
        navBtn();
    });

}(jQuery));