$j.fn.cycle.defaults.autoSelector = '.cycle-slideshow';


var loaded_vesthemesettings = true;
(function($) {
    $(document).ready( function (){

        // $.fn.cycle.defaults.autoSelector = '.cycle-slideshow';

/**
             *
             * Automatic apply  OWL carousel
             */
             if($(".owl-carousel-play").length > 0 ) {
                $(".owl-carousel-play .owl-carousel").each( function(){
                    var items_desktop = $(this).data( 'slide-desktop' );
                    var items_desktop_small = $(this).data( 'slide-desktop-small' );
                    var items_tablet = $(this).data( 'slide-tablet' );
                    var items_tablet_small = $(this).data( 'slide-tablet-small' );
                    var items_mobile = $(this).data( 'slide-mobile' );
                    var items_custom = $(this).data( 'slide-custom' );
                    var lazyload = $(this).data( 'lazyload' );
                    var navigation_text = $(this).data( 'navigation-text' );

                //Desktop
                if(items_desktop && items_desktop != "false" && items_desktop != "0") {
                    items_desktop = JSON.parse("["+items_desktop+"]");
                } else if(items_desktop == "false" || items_desktop == "0") {
                    items_desktop = false;
                } else {
                    items_desktop = [1199,4];
                }
                //Desktop Small
                if(items_desktop_small && items_desktop_small != "false" && items_desktop_small != "0") {
                    items_desktop_small = JSON.parse("["+items_desktop_small+"]");
                } else if(items_desktop_small == "false" || items_desktop_small == "0") {
                    items_desktop_small = false;
                } else {
                    items_desktop_small = [979,3];
                }
                //Tablet
                if(items_tablet && items_tablet != "false" && items_tablet != "0") {
                    items_tablet = JSON.parse("["+items_tablet+"]");
                } else if(items_tablet == "false" || items_tablet == "0") {
                    items_tablet = false;
                } else {
                    items_tablet = [768,2];
                }
                //Tablet Small
                if(items_tablet_small && items_tablet_small != "false" && items_tablet_small != "0") {
                    items_tablet_small = JSON.parse("["+items_tablet_small+"]");
                } else if(items_tablet_small == "false" || items_tablet_small == "0") {
                    items_tablet_small = false;
                } else {
                    items_tablet_small = false;
                }
                //Mobile
                if(items_mobile && items_mobile != "false" && items_mobile != "0") {
                    items_mobile = JSON.parse("["+items_mobile+"]");
                } else if(items_mobile == "false" || items_mobile == "0") {
                    items_mobile = false;
                } else {
                    items_mobile = [479,1];
                }
                //Custom
                if(items_custom && items_custom != "false" && items_custom != "0") {
                    items_custom = JSON.parse("["+items_custom+"]");
                } else if(items_custom == "false" || items_custom == "0") {
                    items_custom = false;
                } else {
                    items_desktop = false;
                }


                //Custom
                if(lazyload && lazyload != "false" && lazyload != "0") {
                    lazyload = true;
                } else if(lazyload == "false" || lazyload == "0") {
                    lazyload = false;
                } else {
                    lazyload = false;
                }

                if(navigation_text==''){
                    navigation_text = ["prev 1","next 2"];
                }

                var config = {
                    navigation : $(this).data( 'navigation' ), // Show next and prev buttons
                    slideSpeed : $(this).data( 'slide-speed' ),
                    paginationSpeed : 400,
                    pagination : $(this).data( 'pagination' ),
                    autoPlay : $(this).data( 'auto' ),
                    lazyLoad: lazyload,
                    responsive: true,
                    autoWidth: false,
                    autoHeight: true,
                    itemsDesktop : items_desktop,
                    itemsDesktopSmall : items_desktop_small,
                    itemsTablet : items_tablet,
                    itemsTabletSmall : items_tablet_small,
                    itemsMobile : items_mobile,
                    itemsCustom : items_custom,
                    navigationText: navigation_text,
                };

                var owl = $(this);
                if( $(this).data('slide-default') == 1 ){
                    config.singleItem = true;
                } else {
                    config.items = $(this).data( 'slide-default' );
                }
                $(this).owlCarousel( config );
                $('.owl-left',$(this).parent()).click(function(){
                    owl.trigger('owl.prev');
                    return false;
                });
                $('.owl-right',$(this).parent()).click(function(){
                    owl.trigger('owl.next');
                    return false;
                });
            } );
}
        /**
         *
         * Automatic apply  Simple Easy Tabs
         */
         if($(".easytab-play .easytab").length > 0) {
            $(".easytab-play .easytab").each( function(){
                var tab_active_class = $(this).data( 'tab-active-class' );
                var panel_active_class = $(this).data( 'panel-active-class' );
                var animate = $(this).data( 'animate' );
                var animate_speed = $(this).data( 'speed' );
                var collapse = $(this).data( 'collapsible' );
                var is_cycle = $(this).data( 'cycle' );
                var default_tab = $(this).data( 'default-tab' );
                var transition_in = $(this).data( 'in-transition' );
                var transition_out = $(this).data( 'out-transition' );
                var transition_in_easing = $(this).data( 'in-easing' );
                var transition_out_easing = $(this).data( 'out-easing' );

                if(tab_active_class == "" || tab_active_class == null || typeof(tab_active_class) == 'undefined') {
                    tab_active_class = "active";
                }
                if(panel_active_class == "" || panel_active_class == null || typeof(panel_active_class) == 'undefined') {
                    panel_active_class = "active";
                }
                if(animate == "" || animate == null || typeof(animate) == 'undefined' || animate == 1 || animate == "true") {
                    animate = true;
                } else {
                    animate = false;
                }
                if(animate_speed == "" || animate_speed == null || typeof(animate_speed) == 'undefined') {
                    animate_speed = "normal";
                }
                if(collapse == "" || collapse == null || typeof(collapse) == 'undefined' || collapse == 0 || collapse == "false") {
                    collapse = false;
                } else {
                    collapse = true;
                }
                if(is_cycle == "" || is_cycle == null || typeof(is_cycle) == 'undefined' || is_cycle == 0 || is_cycle == "false") {
                    is_cycle = false;
                } else {
                    is_cycle  = true;
                }

                if(default_tab == "" || default_tab == null || typeof(default_tab) == 'undefined') {
                    default_tab = 'li:first-child';
                }

                if(transition_in == "" || transition_in == null || typeof(transition_in) == 'undefined') {
                    transition_in = 'fadeIn';
                }

                if(transition_in_easing == "" || transition_in_easing == null || typeof(transition_in_easing) == 'undefined') {
                    transition_in_easing = 'swing';
                }

                if(transition_out == "" || transition_out == null || typeof(transition_out) == 'undefined') {
                    transition_out = 'fadeOut';
                }

                if(transition_out_easing == "" || transition_out_easing == null || typeof(transition_out_easing) == 'undefined') {
                    transition_out_easing = 'swing';
                }

                var config = {
                    animate : animate,
                    animationSpeed: animate_speed,
                    collapsible: collapse,
                    panelActiveClass : panel_active_class,
                    tabActiveClass : tab_active_class,
                    cycle : is_cycle,
                    defaultTab: default_tab,
                    transitionIn: transition_in,
                    transitionInEasing: transition_in_easing,
                    transitionOut: transition_out,
                    transitionOutEasing: transition_out_easing,
                    updateHash: false
                };

                var easytab = $(this);

                if($(this).find(".nav-tabs .tab").length > 1) {

                   $(this).easytabs(config);

                } else if($(this).find(".nav-tabs .tab").length > 0) {
                   $(this).find(".nav-tabs .tab").first().addClass("active");
                   if($(this).find(".nav-tabs .tab > a").length > 0){
                       $(this).find(".nav-tabs .tab > a").first().addClass("active");
                       $(this).find(".nav-tabs .tab > a").first().click(function(e){
                        e.preventDefault();
                        return false;
                       });
                   }
                   if($(this).find(".panel-container > div").length > 0){
                        $(this).find(".panel-container > div").first().show();
                   }
                }

            });
}
/*Init Bootstrap Tab */
if($(".bootstap-tab-play").length > 0) {

    $(".bootstap-tab-play").each( function(){
        var default_tab = $(this).data("active");
        $(this).find("a").click(function (e) {
          e.preventDefault()
          $(this).tab('show')
      })

        if(default_tab && $(this).find(default_tab).length > 0) {
            $(this).find(default_tab).tab('show')
        }
    });

}
/*Init Color Box Popup*/
if($(".colorbox-play").length > 0) {
    $(".colorbox-play").each( function(){
        var popup_width = $(this).data( 'width' );
        var popup_height = $(this).data( 'height' );
        var popup_init_width = $(this).data( 'initial-width' );
        var popup_init_height = $(this).data( 'initial-height' );
        var popup_max_width = $(this).data( 'max-width' );
        var popup_max_height = $(this).data( 'max-height' );
        var opacity = $(this).data( 'opacity' );
        var rel = $(this).data( 'group' );
        var slideshow = $(this).data( 'slideshow' );
        var auto_open = $(this).data( 'auto-open' );
        var previous = $(this).data( 'previous' );
        var next = $(this).data( 'next' );
        var reposition = $(this).data( 'reposition' );

        if(popup_width == "" || popup_width == null || typeof(popup_width) == 'undefined') {
            popup_width = false;
        }

        if(popup_height == "" || popup_height == null || typeof(popup_height) == 'undefined') {
            popup_height = false;
        }

        if(popup_init_width == "" || popup_init_width == null || typeof(popup_init_width) == 'undefined') {
            popup_init_width = 300;
        }

        if(popup_init_height == "" || popup_init_height == null || typeof(popup_init_height) == 'undefined') {
            popup_init_height = 100;
        }

        if(popup_max_width == "" || popup_max_width == null || typeof(popup_max_width) == 'undefined') {
            popup_max_width = false;
        }

        if(popup_max_height == "" || popup_max_height == null || typeof(popup_max_height) == 'undefined') {
            popup_max_height = false;
        }

        if(opacity == "" || opacity == null || typeof(opacity) == 'undefined') {
            opacity = 0.85;
        }

        if(rel == "" || rel == null || typeof(rel) == 'undefined') {
            rel = false;
        }

        if(slideshow == "" || slideshow == null || typeof(slideshow) == 'undefined' || slideshow == 0 || slideshow == "false") {
            slideshow = false;
        } else {
            slideshow  = true;
        }

        if(auto_open == "" || auto_open == null || typeof(auto_open) == 'undefined' || auto_open == 0 || auto_open == "false") {
            auto_open = false;
        } else {
            auto_open  = true;
        }

        if(reposition == "" || reposition == null || typeof(reposition) == 'undefined' || reposition == 0 || reposition == "false") {
            reposition = false;
        } else {
            reposition  = true;
        }

        if(previous == "" || previous == null || typeof(previous) == 'undefined') {
            previous = 'previous';
        }

        if(next == "" || next == null || typeof(next) == 'undefined') {
            next = 'previous';
        }

        var config = {
            width : popup_width,
            height: popup_height,
            overlayClose: true,
            opacity : opacity,
            rel : rel,
            slideshow : slideshow,
            open: auto_open,
            previous: previous,
            next: next,
            reposition: reposition,
            initialWidth: popup_init_width,
            initialHeight: popup_init_height,
            maxWidth: popup_max_width,
            maxHeight: popup_max_height
        };

        $(this).colorbox( config );

    });

}

/*Init Fancy Box Popup*/
if($(".fancybox-play").length > 0) {
 $(".fancybox-play").each( function(){
    var popup_width = $(this).data( 'width' );
    var popup_height = $(this).data( 'height' );
    var show_nav_arrows = $(this).data( 'show-nav-arrows' );
    var show_close_button = $(this).data( 'show-close-button' );
    var title_show = $(this).data( 'show-title' );
    var overlay_show = $(this).data( 'show-overlay' );
    var opacity = $(this).data( 'opacity' );
    var title_position = $(this).data( 'title-position' );
    var slideshow = $(this).data( 'slideshow' );
    var modal = $(this).data( 'modal' );
    var overlay_opacity = $(this).data( 'overlay-opacity' );
    var overlay_color = $(this).data( 'overlay-color' );
    var format_title = $(this).data( 'format-title' );
    var ftype = $(this).data( 'ftype' );
    var scrolling = $(this).data( 'ftype' );
    var hideOnContentClick = $(this).data( 'hide-on-content-click' );
    var autoSize = $(this).data( 'autoSize' );
    var padding = $(this).data( 'padding' );

    if(popup_width == "" || popup_width == null || typeof(popup_width) == 'undefined') {
        popup_width = '60%';
    }

    if(popup_height == "" || popup_height == null || typeof(popup_height) == 'undefined') {
        popup_height = '80%';
    }

    if(opacity == "" || opacity == null || typeof(opacity) == 'undefined') {
        opacity = false;
    }

    if(show_nav_arrows == "" || show_nav_arrows == null || typeof(show_nav_arrows) == 'undefined' || show_nav_arrows == 0 || show_nav_arrows == "false") {
        show_nav_arrows = false;
    } else {
        show_nav_arrows  = true;
    }

    if(modal == "" || modal == null || typeof(modal) == 'undefined' || modal == 0 || modal == "false") {
        modal = false;
    } else {
        modal  = true;
    }

    if(show_close_button == "" || show_close_button == null || typeof(show_close_button) == 'undefined' || show_close_button == 0 || show_close_button == "false") {
        show_close_button = false;
    } else {
        show_close_button  = true;
    }

    if(hideOnContentClick == "" || hideOnContentClick == null || typeof(hideOnContentClick) == 'undefined' || hideOnContentClick == 0 || hideOnContentClick == "false") {
        hideOnContentClick = false;
    } else {
        hideOnContentClick  = true;
    }

    if(title_show == "" || title_show == null || typeof(title_show) == 'undefined' || title_show == 0 || title_show == "false") {
        title_show = false;
    } else {
        title_show  = true;
    }

    if(overlay_show == "" || overlay_show == null || typeof(overlay_show) == 'undefined' || overlay_show == 0 || overlay_show == "false") {
        overlay_show = false;
    } else {
        overlay_show  = true;
    }

    if(scrolling == "" || scrolling == null || typeof(scrolling) == 'undefined' || scrolling == 0 || scrolling == "false") {
        scrolling = false;
    } else {
        scrolling  = true;
    }

    if(title_position == "" || title_position == null || typeof(title_position) == 'undefined') {
        title_position = 'outside';
    }

    if(overlay_opacity == "" || overlay_opacity == null || typeof(overlay_opacity) == 'undefined') {
        overlay_opacity = 0.8;
    }

    if(overlay_color == "" || overlay_color == null || typeof(overlay_color) == 'undefined') {
        overlay_color = '#666';
    }

    if(format_title == "" || format_title == null || typeof(format_title) == 'undefined') {
        format_title = null;
    }

    if(ftype == "" || ftype == null || typeof(ftype) == 'undefined') {
        ftype = 'inline';
    }

    if(autoSize == "" || autoSize == null || typeof(autoSize) == 'undefined') {
        autoSize = false;
    }

    if(padding == "" || padding == null || typeof(padding) == 'undefined') {
        padding = 0;
    }

    var config = {
        padding: padding,
        width : popup_width,
        height: popup_height,
        autoSize: autoSize,
        titleShow: title_show,
        opacity : opacity,
        titlePosition : title_position,
        modal: modal,
        showCloseButton: show_close_button,
        showNavArrows: show_nav_arrows,
        overlayShow: overlay_show,
        overlayOpacity: overlay_opacity,
        titleFormat: format_title,
        type: ftype,
        scrolling: scrolling,
        hideOnContentClick: hideOnContentClick,
        onComplete: function(){
            jQuery.fancybox.showActivity();
            jQuery('#fancybox-frame').load(function() {
                jQuery.fancybox.hideActivity();
                jQuery.fancybox.resize();
            });
        }
    };
    $(this).fancybox( config );
});

}

/*Init Accordion */
if($(".accordion-play").length > 0) {
    $(".accordion-play").each( function(){
        var default_item = $(this).data("active");

        $(this).accordion();

        if(default_item) {
            $('a[href=#]'+default_item).trigger('activate-node');
        }

    });

}

} );

$(window).ready( function(){
    if(jQuery(".scrollup").length > 0) {
            // scroll-to-top button show and hide
            jQuery(document).ready(function(){
                jQuery(window).scroll(function(){
                    if (jQuery(this).scrollTop() > 100) {
                        jQuery('.scrollup').fadeIn();
                    } else {
                        jQuery('.scrollup').fadeOut();
                    }
                });
            // scroll-to-top animate
            jQuery('.scrollup').click(function(){
                jQuery("html, body").animate({ scrollTop: 0 }, 600);
                return false;
            });
        });
        }
    });


$(window).ready( function(){
    if(jQuery(".scrollup").length > 0) {
            // scroll-to-top button show and hide
            jQuery(document).ready(function(){
                jQuery(window).scroll(function(){
                    if (jQuery(this).scrollTop() > 100) {
                        jQuery('.scrollup').fadeIn();
                    } else {
                        jQuery('.scrollup').fadeOut();
                    }
                });
            // scroll-to-top animate
            jQuery('.scrollup').click(function(){
                jQuery("html, body").animate({ scrollTop: 0 }, 600);
                return false;
            });
        });
        }
    });

var mb = parseInt($("#header").css("margin-bottom"));
var hideheight =  $(".header-language-background").height()+mb+mb;
var hh =  $(".header").height() + mb;
var updateTopbar = function(){
    var pos = $(window).scrollTop();
    if( pos > 0 && pos >= hideheight ){
        $(".keep-header .header").addClass('hide-bar');
        $(".keep-header .header").addClass( "navbar navbar-fixed-top" );

    }else {
        $(".keep-header .header").removeClass('hide-bar');
    }
}
updateTopbar();
$(window).scroll(function() {
    updateTopbar();
});


var SmartHeader = function(){
    var width = $(window).width();
    if(width<768){
        $(".header").addClass('header-mobile');
        $(".header").removeClass('header-regular');
    }else{
        $(".header").addClass('header-regular');
        $(".header").removeClass('header-mobile');
    }
}
SmartHeader();
$(window).resize(function() {
    SmartHeader();
});


})(jQuery);