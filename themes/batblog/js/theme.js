$(function() {
    $("body").on("input propertychange", ".form-group", function(e) {
        $(this).toggleClass("form-group-with-value", !!$(e.target).val());
    }).on("focus", ".form-group", function() {
        $(this).addClass("form-group-with-focus");
    }).on("blur", ".form-group", function() {
        $(this).removeClass("form-group-with-focus");
    });
});

jQuery(document).ready(function($) {
    $(window).scroll(function() {    
        if ($(window).scrollTop() > 0)
            $(".navbar").addClass("is-fixed");
        else 
            $(".navbar").removeClass("is-fixed");
    });

    $("article .post-footer .pull-left a").click(function(e) {
        window.open($(this).attr('href'), "Share", "status = 1, height = 400, width = 640, resizable = 1")
        e.preventDefault();
    });
    $(window).resize(function() {
        $('.gallery > div').each(function() {
            $('.thumbnail', this).height( $(this).width() * 1 );
        });
    }).resize();
});
