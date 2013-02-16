$(document).ready(function() {
    $('nav ul li').mouseenter(function() {
        $(this).children('ul').slideDown(250);
    });
    
    $('nav ul li').mouseleave(function() {
        $(this).children('ul').slideUp(250);
    }); 
    
    $('#side-menu dl').not('.active').children('dd').toggle(0);
    
    $('#side-menu dl dt').click(function() {
        $(this).parent().children('dd').animate({
            'height': 'toggle',
            'padding-top': 'toggle',
            'padding-bottom': 'toggle'
        }, 250);
    });
});