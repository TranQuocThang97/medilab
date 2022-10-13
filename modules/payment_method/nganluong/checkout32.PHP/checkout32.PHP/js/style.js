$( document ).ready(function () {
    
    //heartslider
    $('#slide-tab ul.nav-tabs').owlCarousel({
        items: 4,
        loop: true,
        margin: 0,
        responsiveClass: false,
        nav: true,
        dots: false,
        autoplay: false,
        autoHeight: false,
        autoplayTimeout: 4000,
        autoplayHoverPause: true,
        navText: false,
    });
    
    // m-menu
    $('nav#menu').mmenu({
       offCanvas: {
        position: "left",
        zposition: "front"
       }
      }, 
      {
       offCanvas: {
        pageNodeType: "form"
       }
    }); 

    //heartslider
    $('.slide-index ul').owlCarousel({
        items: 1,
        loop: true,
        margin: 0,
        responsiveClass: false,
        nav: true,
        dots: false,
        autoplay: true,
        autoHeight: false,
        autoplayTimeout: 4000,
        autoplayHoverPause: true,
        navText: false,
    });

    //heartslider
    $('#content-slide ul').owlCarousel({
        loop: true,
        margin: 0,
        responsiveClass: false,
        nav: false,
        dots: false,
        autoplay: true,
        autoHeight: false,
        autoplayTimeout: 4000,
        autoplayHoverPause: true,
        navText: false,
        responsive: {
            320: {
                items: 1
            },
            360: {
                items: 1
            },
            480: {
                items: 2
            },
            533: {
                items: 2
            },
            676: {
                items: 3
            },
            1200: {
                items: 4
            },
        }
    });
    //filter-scroll
    $('.filter-scroll').slimScroll({
        height: '100px',
    });
    
});