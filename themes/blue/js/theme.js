//Toggle hamburger class
$(function(){
    $("#mobile-btn").click(function(){ 
        $(this).toggleClass("toggled");
    });   
});

//Dropdown
$(function(){
    if ($(window).width() > 991){
        $(window).click(function() {$(".dropdown-menu").fadeOut(200);});
        $(".dropdown").click(function(){   
            $(".dropdown-menu").stop().fadeOut(200);
            $(this).children(".dropdown-menu").stop().fadeToggle(200);
        });
    }
});