/**
 * Created by waqasriaz on 02/10/15.
 */

jQuery(document).ready(function ($) {
    "use strict";

    if ( typeof HOUZEZ_ajaxcalls_vars !== "undefined" ) {
        var houzezMap;
        var lastClickedMarker;
        var markers = new Array();
        var markerCluster = null;
        var current_marker = 0;
        var has_compare = $('#compare-controller').length;

        var custom_fields = HOUZEZ_ajaxcalls_vars.custom_fields;
        var search_custom_fields = $.parseJSON(custom_fields);
        var markerPricePins = HOUZEZ_ajaxcalls_vars.markerPricePins;

        var ajaxurl = HOUZEZ_ajaxcalls_vars.admin_url+ 'admin-ajax.php';
        var login_sending = HOUZEZ_ajaxcalls_vars.login_loading;
        var userID = HOUZEZ_ajaxcalls_vars.user_id;
        var houzez_reCaptcha = HOUZEZ_ajaxcalls_vars.houzez_reCaptcha;
        var login_redirect_type = HOUZEZ_ajaxcalls_vars.redirect_type
        var login_redirect = HOUZEZ_ajaxcalls_vars.login_redirect
        var prop_lat = HOUZEZ_ajaxcalls_vars.property_lat;
        var prop_lng = HOUZEZ_ajaxcalls_vars.property_lng;
        var autosearch_text = HOUZEZ_ajaxcalls_vars.autosearch_text;
        var paypal_connecting = HOUZEZ_ajaxcalls_vars.paypal_connecting;
        var mollie_connecting = HOUZEZ_ajaxcalls_vars.mollie_connecting;
        var process_loader_refresh = HOUZEZ_ajaxcalls_vars.process_loader_refresh;
        var process_loader_spinner = HOUZEZ_ajaxcalls_vars.process_loader_spinner;
        var process_loader_circle = HOUZEZ_ajaxcalls_vars.process_loader_circle;
        var process_loader_cog = HOUZEZ_ajaxcalls_vars.process_loader_cog;
        var success_icon = HOUZEZ_ajaxcalls_vars.success_icon;
        var confirm_message = HOUZEZ_ajaxcalls_vars.confirm;
        var confirm_featured = HOUZEZ_ajaxcalls_vars.confirm_featured;
        var confirm_featured_remove = HOUZEZ_ajaxcalls_vars.confirm_featured_remove;
        var confirm_relist = HOUZEZ_ajaxcalls_vars.confirm_relist;
        var is_singular_property = HOUZEZ_ajaxcalls_vars.is_singular_property;
        var property_map = HOUZEZ_ajaxcalls_vars.property_map;
        var property_map_street = HOUZEZ_ajaxcalls_vars.property_map_street;
        var currency_symb = HOUZEZ_ajaxcalls_vars.currency_symbol;
        var advanced_search_price_range_min = parseInt( HOUZEZ_ajaxcalls_vars.advanced_search_widget_min_price );
        var advanced_search_price_range_max = parseInt( HOUZEZ_ajaxcalls_vars.advanced_search_widget_max_price );
        var advanced_search_price_range_min_rent = parseInt( HOUZEZ_ajaxcalls_vars.advanced_search_min_price_range_for_rent );
        var advanced_search_price_range_max_rent = parseInt( HOUZEZ_ajaxcalls_vars.advanced_search_max_price_range_for_rent );
        var advanced_search_widget_min_area = parseInt( HOUZEZ_ajaxcalls_vars.advanced_search_widget_min_area );
        var advanced_search_widget_max_area = parseInt( HOUZEZ_ajaxcalls_vars.advanced_search_widget_max_area );
        var advanced_search_price_slide = HOUZEZ_ajaxcalls_vars.advanced_search_price_slide;
        var fave_page_template = HOUZEZ_ajaxcalls_vars.fave_page_template;
        var fave_prop_featured = HOUZEZ_ajaxcalls_vars.prop_featured;
        var featured_listings_none = HOUZEZ_ajaxcalls_vars.featured_listings_none;
        var prop_sent_for_approval = HOUZEZ_ajaxcalls_vars.prop_sent_for_approval;
        var houzez_rtl = HOUZEZ_ajaxcalls_vars.houzez_rtl;
        var infoboxClose = HOUZEZ_ajaxcalls_vars.infoboxClose;
        var clusterIcon = HOUZEZ_ajaxcalls_vars.clusterIcon;
        var paged = HOUZEZ_ajaxcalls_vars.paged;
        var sort_by = HOUZEZ_ajaxcalls_vars.sort_by;
        var google_map_style = HOUZEZ_ajaxcalls_vars.google_map_style;

        if(google_map_style!='') {
            var google_map_style = JSON.parse ( google_map_style );
        }
        var googlemap_default_zoom = HOUZEZ_ajaxcalls_vars.googlemap_default_zoom;
        var googlemap_pin_cluster = HOUZEZ_ajaxcalls_vars.googlemap_pin_cluster;
        var googlemap_zoom_cluster = HOUZEZ_ajaxcalls_vars.googlemap_zoom_cluster;
        var map_icons_path = HOUZEZ_ajaxcalls_vars.map_icons_path;
        var google_map_needed = HOUZEZ_ajaxcalls_vars.google_map_needed;
        var simple_logo = HOUZEZ_ajaxcalls_vars.simple_logo;
        var transportation = HOUZEZ_ajaxcalls_vars.transportation;
        var supermarket = HOUZEZ_ajaxcalls_vars.supermarket;
        var schools = HOUZEZ_ajaxcalls_vars.schools;
        var libraries = HOUZEZ_ajaxcalls_vars.libraries;
        var pharmacies = HOUZEZ_ajaxcalls_vars.pharmacies;
        var hospitals = HOUZEZ_ajaxcalls_vars.hospitals;
        var currency_position = HOUZEZ_ajaxcalls_vars.currency_position;
        var currency_updating_msg = HOUZEZ_ajaxcalls_vars.currency_updating_msg;
        var submission_currency = HOUZEZ_ajaxcalls_vars.submission_currency;
        var wire_transfer_text = HOUZEZ_ajaxcalls_vars.wire_transfer_text;
        var direct_pay_thanks = HOUZEZ_ajaxcalls_vars.direct_pay_thanks;
        var direct_payment_title = HOUZEZ_ajaxcalls_vars.direct_payment_title;
        var direct_payment_button = HOUZEZ_ajaxcalls_vars.direct_payment_button;
        var direct_payment_details = HOUZEZ_ajaxcalls_vars.direct_payment_details;
        var measurement_unit = HOUZEZ_ajaxcalls_vars.measurement_unit;
        var measurement_updating_msg = HOUZEZ_ajaxcalls_vars.measurement_updating_msg;
        var thousands_separator = HOUZEZ_ajaxcalls_vars.thousands_separator;
        var rent_status_for_price_range = HOUZEZ_ajaxcalls_vars.for_rent_price_range;
        var current_tempalte = HOUZEZ_ajaxcalls_vars.current_tempalte;
        var not_found = HOUZEZ_ajaxcalls_vars.not_found;
        var property_detail_top = HOUZEZ_ajaxcalls_vars.property_detail_top;
        var keyword_search_field = HOUZEZ_ajaxcalls_vars.keyword_search_field;
        var keyword_autocomplete = HOUZEZ_ajaxcalls_vars.keyword_autocomplete;
        var template_thankyou = HOUZEZ_ajaxcalls_vars.template_thankyou;
        var direct_pay_text = HOUZEZ_ajaxcalls_vars.direct_pay_text;
        var search_result_page = HOUZEZ_ajaxcalls_vars.search_result_page;
        var houzez_default_radius = HOUZEZ_ajaxcalls_vars.houzez_default_radius;
        var enable_radius_search = HOUZEZ_ajaxcalls_vars.enable_radius_search;
        var enable_radius_search_halfmap = HOUZEZ_ajaxcalls_vars.enable_radius_search_halfmap;
        var houzez_primary_color = HOUZEZ_ajaxcalls_vars.houzez_primary_color;
        var houzez_geocomplete_country = HOUZEZ_ajaxcalls_vars.geocomplete_country;
        var houzez_logged_in = HOUZEZ_ajaxcalls_vars.houzez_logged_in;
        var ipinfo_location = HOUZEZ_ajaxcalls_vars.ipinfo_location;
        var delete_property_loading = HOUZEZ_ajaxcalls_vars.delete_property;
        var delete_property_confirmation = HOUZEZ_ajaxcalls_vars.delete_confirmation;

        var compare_button_url = HOUZEZ_ajaxcalls_vars.compare_button_url;
        var compare_page_not_found = HOUZEZ_ajaxcalls_vars.compare_page_not_found;

        if( houzez_rtl == 'yes' ) {
            houzez_rtl = true;
        } else {
            houzez_rtl = false;
        }

        if( google_map_needed == 'yes' ) {

            var placesIDs              = new Array();
            var transportationsMarkers = new Array();
            var supermarketsMarkers    = new Array();
            var schoolsMarkers         = new Array();
            var librariesMarkers       = new Array();
            var pharmaciesMarkers      = new Array();
            var hospitalsMarkers       = new Array();

            var drgflag = true;
            var houzez_is_mobile = false;
            if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
                drgflag = false;
                houzez_is_mobile = true;
            }

            var houzezMapoptions = {
                zoom: parseInt(googlemap_default_zoom),
                mapTypeId: google.maps.MapTypeId.ROADMAP,
                styles: google_map_style,
                zoomControl: false,
                mapTypeControl: false,
                streetViewControl: false,
                overviewMapControl: false,
                zoomControlOptions: {
                    style: google.maps.ZoomControlStyle.SMALL,
                    position: google.maps.ControlPosition.RIGHT_TOP
                },
                streetViewControlOptions: {
                    position: google.maps.ControlPosition.RIGHT_TOP
                }
            };

            var houzezHeaderMapOptions = {
                maxZoom: 20,
                disableDefaultUI: true,
                scroll:{x:$(window).scrollLeft(),y:$(window).scrollTop()},
                zoom: parseInt(googlemap_default_zoom),
                mapTypeId: google.maps.MapTypeId.ROADMAP,
                styles: google_map_style,
                //draggable: drgflag,
            };



            if( document.getElementById('houzez-listing-map') ){
                houzezMap = new google.maps.Map(document.getElementById('houzez-listing-map'), houzezHeaderMapOptions);
                if( houzez_is_mobile ) {
                    houzezMap.setOptions({
                        gestureHandling: 'cooperative',
                    });
                } else {
                    houzezMap.setOptions({
                        scrollwheel: false,
                    });
                }
            } else if( document.getElementById('mapViewHalfListings') ) {
                houzezMap = new google.maps.Map(document.getElementById('mapViewHalfListings'), houzezMapoptions);
                if( houzez_is_mobile ) {
                    houzezMap.setOptions({
                        gestureHandling: 'cooperative',
                    });
                } else {
                    houzezMap.setOptions({
                        scrollwheel: false,
                    });
                }
            }

            var infobox = new InfoBox({
                disableAutoPan: true, //false
                maxWidth: 275,
                alignBottom: true,
                pixelOffset: new google.maps.Size(-122, -48),
                zIndex: null,
                closeBoxMargin: "0 0 -16px -16px",
                closeBoxURL: infoboxClose,
                infoBoxClearance: new google.maps.Size(1, 1),
                isHidden: false,
                pane: "floatPane",
                enableEventPropagation: false
            });
            var poiInfo = new InfoBox({
                disableAutoPan: false,
                maxWidth: 250,
                pixelOffset: new google.maps.Size(-72, -70),
                zIndex: null,
                boxStyle: {
                    'background' : '#ffffff',
                    'opacity'    : 1,
                    'padding'    : '6px',
                    'box-shadow' : '0 1px 2px 0 rgba(0, 0, 0, 0.12)',
                    'width'      : '145px',
                    'text-align' : 'center',
                    'border-radius' : '4px'
                },
                closeBoxMargin: "28px 26px 0px 0px",
                closeBoxURL: "",
                infoBoxClearance: new google.maps.Size(1, 1),
                pane: "floatPane",
                enableEventPropagation: false
            });

            var iw = new google.maps.InfoWindow();

            var houzezGetPOIs = function( position, poiMap, poiType) {
                var service   = new google.maps.places.PlacesService( poiMap );
                var bounds    = poiMap.getBounds();
                var types     = new Array();

                switch(poiType) {
                    case 'transportations':
                        types = ['bus_station', 'subway_station', 'train_station', 'airport'];
                        break;
                    case 'supermarkets':
                        types = ['grocery_or_supermarket', 'shopping_mall'];
                        break;
                    case 'schools':
                        types = ['school', 'university'];
                        break;
                    case 'libraries':
                        types = ['library'];
                        break;
                    case 'pharmacies':
                        types = ['pharmacy'];
                        break;
                    case 'hospitals':
                        types = ['hospital'];
                        break;
                }

                service.nearbySearch({
                    location: position,
                    bounds: bounds,
                    radius: 2000,
                    types: types
                }, function poiCallback( results, status ) {
                    if ( status === google.maps.places.PlacesServiceStatus.OK ) {
                        for ( var i = 0; i < results.length; i++ ) {
                            if( jQuery.inArray (results[i].place_id, placesIDs ) == -1 ) {
                                houzezCreatePOI( results[i], poiMap, poiType );
                                placesIDs.push( results[i].place_id );
                            }
                        }
                    }
                });
            }

            var houzezCreatePOI = function( place, poiMap, type ) {
                var marker;

                switch(type) {
                    case 'transportations':
                        marker = new google.maps.Marker({
                            map: poiMap,
                            position: place.geometry.location,
                            icon: map_icons_path+'transportation.png'
                        });
                        transportationsMarkers.push(marker);
                        break;
                    case 'supermarkets':
                        marker = new google.maps.Marker({
                            map: poiMap,
                            position: place.geometry.location,
                            icon: map_icons_path+'supermarket.png'
                        });
                        supermarketsMarkers.push(marker);
                        break;
                    case 'schools':
                        marker = new google.maps.Marker({
                            map: poiMap,
                            position: place.geometry.location,
                            icon: map_icons_path+'school.png'
                        });
                        schoolsMarkers.push(marker);
                        break;
                    case 'libraries':
                        marker = new google.maps.Marker({
                            map: poiMap,
                            position: place.geometry.location,
                            icon: map_icons_path+'libraries.png'
                        });
                        librariesMarkers.push(marker);
                        break;
                    case 'pharmacies':
                        marker = new google.maps.Marker({
                            map: poiMap,
                            position: place.geometry.location,
                            icon: map_icons_path+'pharmacy.png'
                        });
                        pharmaciesMarkers.push(marker);
                        break;
                    case 'hospitals':
                        marker = new google.maps.Marker({
                            map: poiMap,
                            position: place.geometry.location,
                            icon: map_icons_path+'hospital.png'
                        });
                        hospitalsMarkers.push(marker);
                        break;
                }

                google.maps.event.addListener(marker, 'mouseover', function() {
                    poiInfo.setContent(place.name);
                    poiInfo.open(poiMap, this);
                });
                google.maps.event.addListener(marker, 'mouseout', function() {
                    poiInfo.open(null,null);
                });
            }

            var houzezTooglePOIs = function(poiMap, type) {
                for(var i = 0; i < type.length; i++) {
                    if(type[i].getMap() != null) {
                        type[i].setMap(null);
                    } else {
                        type[i].setMap(poiMap);
                    }
                }
            }

            var houzezPoiControls = function( controlDiv, poiMap, center) {
                controlDiv.style.clear = 'both';

                // Set CSS for transportations POI
                var transportationUI = document.createElement('div');
                transportationUI.id = 'transportation';
                transportationUI.class = 'transportation';
                transportationUI.title = transportation;
                controlDiv.appendChild(transportationUI);
                var transportationIcon = document.createElement('div');
                transportationIcon.id = 'transportationIcon';
                transportationIcon.innerHTML = '<div class="icon"><img src="'+map_icons_path+'transportation-panel-icon.png" alt=""></div><span>'+transportation+'</span>';
                transportationUI.appendChild(transportationIcon);


                // Set CSS for supermarkets POI
                var supermarketsUI = document.createElement('div');
                supermarketsUI.id = 'supermarkets';
                supermarketsUI.title = supermarket;
                controlDiv.appendChild(supermarketsUI);
                var supermarketsIcon = document.createElement('div');
                supermarketsIcon.id = 'supermarketsIcon';
                supermarketsIcon.innerHTML = '<div class="icon"><img src="'+map_icons_path+'supermarket-panel-icon.png" alt=""></div><span>'+supermarket+'</span>';
                supermarketsUI.appendChild(supermarketsIcon);

                // Set CSS for schools POI
                var schoolsUI = document.createElement('div');
                schoolsUI.id = 'schools';
                schoolsUI.title = schools;
                controlDiv.appendChild(schoolsUI);
                var schoolsIcon = document.createElement('div');
                schoolsIcon.id = 'schoolsIcon';
                schoolsIcon.innerHTML = '<div class="icon"><img src="'+map_icons_path+'school-panel-icon.png" alt=""></div><span>'+schools+'</span>';
                schoolsUI.appendChild(schoolsIcon);

                // Set CSS for libraries POI
                var librariesUI = document.createElement('div');
                librariesUI.id = 'libraries';
                librariesUI.title = libraries;
                controlDiv.appendChild(librariesUI);
                var librariesIcon = document.createElement('div');
                librariesIcon.id = 'librariesIcon';
                librariesIcon.innerHTML = '<div class="icon"><img src="'+map_icons_path+'libraries-panel-icon.png" alt=""></div><span>'+libraries+'</span>';
                librariesUI.appendChild(librariesIcon);

                // Set CSS for pharmacies POI
                var pharmaciesUI = document.createElement('div');
                pharmaciesUI.id = 'pharmacies';
                pharmaciesUI.title = pharmacies;
                controlDiv.appendChild(pharmaciesUI);
                var pharmaciesIcon = document.createElement('div');
                pharmaciesIcon.id = 'pharmaciesIcon';
                pharmaciesIcon.innerHTML = '<div class="icon"><img src="'+map_icons_path+'pharmacy-panel-icon.png" alt=""></div><span>'+pharmacies+'</span>';
                pharmaciesUI.appendChild(pharmaciesIcon);

                // Set CSS for hospitals POI
                var hospitalsUI = document.createElement('div');
                hospitalsUI.id = 'hospitals';
                hospitalsUI.title = hospitals;
                controlDiv.appendChild(hospitalsUI);
                var hospitalsIcon = document.createElement('div');
                hospitalsIcon.id = 'hospitalsIcon';
                hospitalsIcon.innerHTML = '<div class="icon"><img src="'+map_icons_path+'hospital-panel-icon.png" alt=""></div><span>'+hospitals+'</span>';
                hospitalsUI.appendChild(hospitalsIcon);

                transportationUI.addEventListener('click', function() {
                    var transportationUI_ = this;
                    if($(this).hasClass('active')) {
                        $(this).removeClass('active');

                        houzezTooglePOIs( poiMap, transportationsMarkers );
                    } else {
                        $(this).addClass('active');

                        houzezGetPOIs(center, poiMap, 'transportations');
                        houzezTooglePOIs(poiMap, transportationsMarkers);
                    }
                    google.maps.event.addListener(poiMap, 'bounds_changed', function() {
                        if($(transportationUI_).hasClass('active')) {
                            var newCenter = poiMap.getCenter();
                            houzezGetPOIs(newCenter, poiMap, 'transportations');
                        }
                    });
                });
                supermarketsUI.addEventListener('click', function() {
                    var supermarketsUI_ = this;
                    if($(this).hasClass('active')) {
                        $(this).removeClass('active');

                        houzezTooglePOIs(poiMap, supermarketsMarkers);
                    } else {
                        $(this).addClass('active');

                        houzezGetPOIs(center, poiMap, 'supermarkets');
                        houzezTooglePOIs(poiMap, supermarketsMarkers);
                    }
                    google.maps.event.addListener(poiMap, 'bounds_changed', function() {
                        if($(supermarketsUI_).hasClass('active')) {
                            var newCenter = poiMap.getCenter();
                            houzezGetPOIs(newCenter, poiMap, 'supermarkets');
                        }
                    });
                });
                schoolsUI.addEventListener('click', function() {
                    var schoolsUI_ = this;
                    if($(this).hasClass('active')) {
                        $(this).removeClass('active');

                        houzezTooglePOIs(poiMap, schoolsMarkers);
                    } else {
                        $(this).addClass('active');

                        houzezGetPOIs(center, poiMap, 'schools');
                        houzezTooglePOIs(poiMap, schoolsMarkers);
                    }
                    google.maps.event.addListener(poiMap, 'bounds_changed', function() {
                        if($(schoolsUI_).hasClass('active')) {
                            var newCenter = poiMap.getCenter();
                            houzezGetPOIs(newCenter, poiMap, 'schools');
                        }
                    });
                });
                librariesUI.addEventListener('click', function() {
                    var librariesUI_ = this;
                    if($(this).hasClass('active')) {
                        $(this).removeClass('active');

                        houzezTooglePOIs(poiMap, librariesMarkers);
                    } else {
                        $(this).addClass('active');

                        houzezGetPOIs(center, poiMap, 'libraries');
                        houzezTooglePOIs(poiMap, librariesMarkers);
                    }
                    google.maps.event.addListener(poiMap, 'bounds_changed', function() {
                        if($(librariesUI_).hasClass('active')) {
                            var newCenter = poiMap.getCenter();
                            houzezGetPOIs(newCenter, poiMap, 'libraries');
                        }
                    });
                });
                pharmaciesUI.addEventListener('click', function() {
                    var pharmaciesUI_ = this;
                    if($(this).hasClass('active')) {
                        $(this).removeClass('active');

                        houzezTooglePOIs(poiMap, pharmaciesMarkers);
                    } else {
                        $(this).addClass('active');

                        houzezGetPOIs(center, poiMap, 'pharmacies');
                        houzezTooglePOIs(poiMap, pharmaciesMarkers);
                    }
                    google.maps.event.addListener(poiMap, 'bounds_changed', function() {
                        if($(pharmaciesUI_).hasClass('active')) {
                            var newCenter = poiMap.getCenter();
                            houzezGetPOIs(newCenter, poiMap, 'pharmacies');
                        }
                    });
                });
                hospitalsUI.addEventListener('click', function() {
                    var hospitalsUI_ = this;
                    if($(this).hasClass('active')) {
                        $(this).removeClass('active');

                        houzezTooglePOIs(poiMap, hospitalsMarkers);
                    } else {
                        $(this).addClass('active');

                        houzezGetPOIs(center, poiMap, 'hospitals');
                        houzezTooglePOIs(poiMap, hospitalsMarkers);
                    }
                    google.maps.event.addListener(poiMap, 'bounds_changed', function() {
                        if($(hospitalsUI_).hasClass('active')) {
                            var newCenter = poiMap.getCenter();
                            houzezGetPOIs(newCenter, poiMap, 'hospitals');
                        }
                    });
                });
            }

            var houzezSetPOIControls = function(poiMap, center) {
                var poiControlDiv = document.createElement('div');
                var poiControl = new houzezPoiControls( poiControlDiv, poiMap, center);

                poiControlDiv.index = 1;
                poiControlDiv.style['padding-left'] = '10px';
                poiMap.controls[google.maps.ControlPosition.LEFT_BOTTOM].push(poiControlDiv);
            }

            //Hover State map
            var houzezMapHover = function() {
                $("body").on("mouseenter", '.gm-marker', function(){
                    var id = $(this).attr("data-id");
                    $(".gm-marker[data-id="+ id +"]" ).addClass("hover-state");
                }).on("mouseleave", '.gm-marker', function(){
                    var id = $(this).attr("data-id");
                        $(".gm-marker[data-id="+ id +"]" ).removeClass("hover-state");
                });

                $("body").on("click", '.gm-marker', function(){
                    $(lastClickedMarker).removeClass("active");
                    $(this).addClass("active");
                    lastClickedMarker = $(this);
                });
            }
            houzezMapHover();
            

            // Remore Map Loader
            var remove_map_loader = function() {
                google.maps.event.addListener(houzezMap, 'tilesloaded', function() {
                    jQuery('#houzez-map-loading').hide();
                });
            }


            // Header Map Parallax
            var houzez_map_parallax = function() {
                var offset = $(houzezMap.getDiv()).offset();

                houzezMap.panBy(((houzezHeaderMapOptions.scroll.x-offset.left)/3),((houzezHeaderMapOptions.scroll.y-offset.top)/3));
                google.maps.event.addDomListener(window, 'scroll', function(){
                    var scrollY = $(window).scrollTop(),
                        scrollX = $(window).scrollLeft(),
                        scroll = houzezMap.get('scroll');
                    if(scroll){
                        houzezMap.panBy(-((scroll.x-scrollX)/3),-((scroll.y-scrollY)/3));
                    }
                    houzezMap.set('scroll',{x:scrollX,y:scrollY})

                });
            }

            // Fit Bounds
            var houzez_map_bounds = function() {
                houzezMap.fitBounds( markers.reduce(function(bounds, marker ) {
                    return bounds.extend( marker.getPosition() );
                }, new google.maps.LatLngBounds()));
            }

            // Marker Cluster
            var houzez_markerCluster = function() {
                
                if(googlemap_pin_cluster != 'no') {
                    var zoom_level = 16;
                    googlemap_zoom_cluster = parseInt(googlemap_zoom_cluster);
                    if(googlemap_zoom_cluster) {
                        zoom_level = googlemap_zoom_cluster;
                    }
                    markerCluster = new MarkerClusterer( houzezMap, markers, {
                        maxZoom: zoom_level,
                        gridSize: 60,
                        styles: [
                            {
                                url: clusterIcon,
                                width: 48,
                                height: 48,
                                textColor: "#fff"
                            }
                        ]
                    });
                }
            }

        } // End google_map_needed


        /* ------------------------------------------------------------------------ */
        /*  COMPARE PANEL
         /* ------------------------------------------------------------------------ */
        if( has_compare == 1) {

            var compare_panel = function () {
                $('.panel-btn').on('click', function () {
                    if ($('.compare-panel').hasClass('panel-open')) {
                        $('.compare-panel').removeClass('panel-open');
                    } else {
                        $('.compare-panel').addClass('panel-open');
                    }
                });
            }

            var compare_panel_close = function () {
                if ($('.compare-panel').hasClass('panel-open')) {
                    $('.compare-panel').removeClass('panel-open');
                }
            }

            var compare_panel_open = function () {
                $('.compare-panel').addClass('panel-open');
            }


            var houzez_compare_listing = function() {
                $('.compare-property').click(function (e) {
                    e.preventDefault();
                    var $this = $(this);

                    var prop_id = $this.attr('data-propid');

                    var data_ap = {action: 'houzez_compare_add_property', prop_id: prop_id};

                    $this.find('i.fa-plus').addClass('fa-spin');

                    $.post(ajaxurl, data_ap, function (response) {

                        var data_ub = {action: 'houzez_compare_update_basket'};

                        $this.find('i.fa-plus').removeClass('fa-spin');

                        $.post(ajaxurl, data_ub, function (response) {

                            $('div#compare-properties-basket').replaceWith(response);

                            compare_panel();
                            compare_panel_open();

                        });

                    });

                    return;

                }); // end .compare-property
            }
            houzez_compare_listing();

            // Delete single item from basket
            $(document).on('click', '#compare-properties-basket .compare-property-remove', function (e) {
                e.preventDefault();

                var property_id = jQuery(this).parent().attr('property-id');

                $(this).parent().block({
                    message: '<i class="' + process_loader_spinner + '"></i>', css: {
                        border: 'none',
                        backgroundColor: 'none',
                        fontSize: '16px',
                    },
                });

                var data_ap = {action: 'houzez_compare_add_property', prop_id: property_id};
                $.post(ajaxurl, data_ap, function (response) {

                    var data_ub = {action: 'houzez_compare_update_basket'};
                    $.post(ajaxurl, data_ub, function (response) {

                        $('div#compare-properties-basket').replaceWith(response);
                        compare_panel();

                    });

                });

                return;
            }); // End Delete compare

            // Show / Hide category details
            jQuery(document).on('click', '.compare-properties-button', function () {

                if (compare_button_url != "") {
                    window.location.href = compare_button_url;
                } else {
                    alert(compare_page_not_found);
                }
                return false;
            });
        } // has compare

        /*
         *  Print Property
         * *************************************** */
        if( $('.houzez-print').length > 0 ) {
            $('.houzez-print').click(function (e) {
                e.preventDefault();
                var propID, printWindow;

                propID = $(this).attr('data-propid');

                printWindow = window.open('', 'Print Me', 'width=700 ,height=842');
                $.ajax({
                    type: 'POST',
                    url: ajaxurl,
                    data: {
                        'action': 'houzez_create_print',
                        'propid': propID,
                    },
                    success: function (data) {
                        printWindow.document.write(data);
                        printWindow.document.close();
                        printWindow.focus();
                        /*setTimeout(function(){
                         printWindow.print();
                         }, 2000);
                         printWindow.close();*/
                    },
                    error: function (xhr, status, error) {
                        var err = eval("(" + xhr.responseText + ")");
                        console.log(err.Message);
                    }

                });
            });
        }

        /*
         *  Visual Composer Stretch row
         * *************************************** */
        if( houzez_rtl ) {
            var visual_composer_stretch_row = function () {

                var $elements = $('[data-vc-full-width="true"]');
                $.each($elements, function (key, item) {
                    var $el = $(this);
                    $el.addClass('vc_hidden');

                    var $el_full = $el.next('.vc_row-full-width');
                    var el_margin_left = parseInt($el.css('margin-left'), 10);
                    var el_margin_right = parseInt($el.css('margin-right'), 10);
                    var offset = 0 - $el_full.offset().left - el_margin_left;
                    var width = $(window).width();
                    $el.css({
                        'position': 'relative',
                        'left': offset,
                        'right': offset,
                        'box-sizing': 'border-box',
                        'width': $(window).width()
                    });
                    if (!$el.data('vcStretchContent')) {
                        var padding = (-1 * offset);
                        if (0 > padding) {
                            padding = 0;
                        }
                        var paddingRight = width - padding - $el_full.width() + el_margin_left + el_margin_right;
                        if (0 > paddingRight) {
                            paddingRight = 0;
                        }
                        $el.css({'padding-left': padding + 'px', 'padding-right': paddingRight + 'px'});
                    }
                    $el.attr("data-vc-full-width-init", "true");
                    $el.removeClass('vc_hidden');
                });
            }
            visual_composer_stretch_row();

            $(window).resize(function () {
                visual_composer_stretch_row();
            });
        }

        /* ------------------------------------------------------------------------ */
        /*  Property page layout cookies
         /* ------------------------------------------------------------------------ */
        var view_btn = $('.view-btn ');
        if( view_btn.length > 0 ) {
            view_btn.click(function () {
                $.removeCookie('properties-layout');
                $.removeCookie('layout-btn');

                if ($(this).hasClass('btn-list')) {
                    $.cookie('properties-layout', 'list-view');
                    $.cookie('layout-btn', 'btn-list');

                } else if ($(this).hasClass('btn-grid')) {
                    $.cookie('properties-layout', 'grid-view');
                    $.cookie('layout-btn', 'btn-grid');

                } else if ($(this).hasClass('btn-grid-3-col')) {
                    $.cookie('properties-layout', 'grid-view-3-col');
                    $.cookie('layout-btn', 'btn-grid-3-col');

                } else {

                }
            });

            if ($.cookie('properties-layout') != 'undefined') {
                if ($.cookie('properties-layout') == 'list-view' && fave_page_template != 'template-search.php' && fave_page_template != 'user_dashboard_favorites.php') {
                    $('.property-listing').removeClass('grid-view grid-view-3-col');
                    $('.property-listing').addClass('list-view');

                } else if ($.cookie('properties-layout') == 'grid-view' && fave_page_template != 'template-search.php' && fave_page_template != 'user_dashboard_favorites.php') {
                    $('.property-listing').removeClass('list-view grid-view grid-view-3-col');
                    $('.property-listing').addClass('grid-view');

                } else if ($.cookie('properties-layout') == 'grid-view-3-col' && fave_page_template != 'template-search.php' && fave_page_template != 'user_dashboard_favorites.php') {
                    $('.property-listing').removeClass('list-view grid-view');
                    $('.property-listing').addClass('grid-view grid-view-3-col');
                }
            }

            if ($.cookie('layout-btn') != 'undefined') {
                if ($.cookie('layout-btn') == 'btn-list') {
                    $('.view-btn').removeClass('active');
                    $('.view-btn.btn-list').addClass('active');

                } else if ($.cookie('layout-btn') == 'btn-grid') {
                    $('.view-btn').removeClass('active');
                    $('.view-btn.btn-grid').addClass('active');

                } else if ($.cookie('layout-btn') == 'btn-grid-3-col') {
                    $('.view-btn').removeClass('active');
                    $('.view-btn.btn-grid-3-col').addClass('active');
                }
            }
        }


        /* ------------------------------------------------------------------------ */
        /*  ADD COMMA TO VALUE
         /* ------------------------------------------------------------------------ */
        var addCommas = function(nStr) {
            nStr += '';
            var x = nStr.split('.');
            var x1 = x[0];
            var x2 = x.length > 1 ? '.' + x[1] : '';
            var rgx = /(\d+)(\d{3})/;
            while (rgx.test(x1)) {
                x1 = x1.replace(rgx, '$1' + thousands_separator + '$2');
            }
            return x1 + x2;
        }

        /*--------------------------------------------------------------------------
         *  Property Module Ajax Pagination
         * -------------------------------------------------------------------------*/
        var properties_module_section = $('#properties_module_section');
        if( properties_module_section.length > 0 ) {

            var properties_module_container = $('#properties_module_container');
            var paginationLink = $('.property-item-module ul.pagination li a');
            var fave_loader = $('.fave-svg-loader');

            $("body").on('click', '.fave-load-more a', function(e) {
                e.preventDefault();
                var $this = $(this);
                $this.prepend('<i class="fa-left ' + process_loader_spinner + '"></i>');
                var $wrap = $this.closest('#properties_module_section').find('#module_properties');
                var prop_limit = $this.data('prop-limit');
                var paged = $this.data('paged');
                var grid_style = $this.data('grid-style');
                var type = $this.data('type');
                var status = $this.data('status');
                var state = $this.data('state');
                var city = $this.data('city');
                var area = $this.data('area');
                var label = $this.data('label');
                var user_role = $this.data('user-role');
                var featured_prop = $this.data('featured-prop');
                var offset = $this.data('offset');
                var sortby = $this.data('sortby');

                $.ajax({
                    type: 'POST',
                    url: ajaxurl,
                    data: {
                        'action': 'houzez_loadmore_properties',
                        'prop_limit': prop_limit,
                        'paged': paged,
                        'grid_style': grid_style,
                        'type': type,
                        'status': status,
                        'state': state,
                        'city': city,
                        'area': area,
                        'label': label,
                        'user_role': user_role,
                        'featured_prop': featured_prop,
                        'sort_by': sortby,
                        'offset': offset
                    },
                    success: function (data) { //alert(data); //return;
                        if(data == 'no_result') {
                             $this.closest('#properties_module_section').find('.fave-load-more').fadeOut('fast').remove();
                             return;
                        }
                        var $wrap = $this.closest('#properties_module_section').find('#module_properties');
                        $wrap.append(data);
                        $this.data("paged", paged+1);
                        $this.find('i').remove();

                        houzez_init_add_favorite();
                        houzez_init_remove_favorite();
                        $('[data-toggle="tooltip"]').tooltip();

                        if( has_compare == 1) {
                            houzez_compare_listing();
                        }

                    },
                    error: function (xhr, status, error) {
                        var err = eval("(" + xhr.responseText + ")");
                        console.log(err.Message);
                    }

                });

            }); 

        }


        /*--------------------------------------------------------------------------
         *  Change advanced search price
         * -------------------------------------------------------------------------*/
        var fave_property_status_changed = function(prop_status, $form) {

            if( prop_status ==  HOUZEZ_ajaxcalls_vars.for_rent ) {
                $form.find('.prices-for-all').addClass('hide');
                $form.find('.prices-for-all select').attr('disabled','disabled');
                $form.find('.prices-only-for-rent').removeClass('hide');
                $form.find('.prices-only-for-rent select').removeAttr('disabled','disabled');
                $form.find('.prices-only-for-rent select').selectpicker('refresh');
            } else {
                $form.find('.prices-only-for-rent').addClass('hide');
                $form.find('.prices-only-for-rent select').attr('disabled','disabled');
                $form.find('.prices-for-all').removeClass('hide');
                $form.find('.prices-for-all select').removeAttr('disabled','disabled');
            }
        }

        $('select[name="status"]').change(function(e){
            var selected_status = $(this).val();
            var $form = $(this).parents('form');
            fave_property_status_changed(selected_status, $form);
        });
        /* On page load ( as on search page ) */
        var selected_status_header_search = $('select[name="status"]').val();
        if( selected_status_header_search == HOUZEZ_ajaxcalls_vars.for_rent || selected_status_header_search == '' || selected_status_header_search == undefined){
            var $form = $('.advanced-search, .widget_houzez_advanced_search');
            fave_property_status_changed(selected_status_header_search, $form);
        }

        // Mobile Advanced Search
        $('.advanced-search-mobile #selected_status_mobile').change(function(e){
            var selected_status = $(this).val();
            var $form = $(this).parents('form');
            fave_property_status_changed(selected_status, $form);
        });
        /* On page load ( as on search page ) */
        var selected_status_header_search = $('.advanced-search-mobile #selected_status_mobile').val();
        if( selected_status_header_search == HOUZEZ_ajaxcalls_vars.for_rent || selected_status_header_search == '' ){
            var $form = $('.advanced-search-mobile');
            fave_property_status_changed(selected_status_header_search, $form);
        }

        // For search module
        $('.advanced-search-module #selected_status_module').change(function(e){
            var selected_status = $(this).val();
            var $form = $(this).parents('form');
            fave_property_status_changed(selected_status, $form);
        });
        var selected_status_module_search = $('.advanced-search-module #selected_status_module').val();
        if( selected_status_module_search == HOUZEZ_ajaxcalls_vars.for_rent || selected_status_module_search == '' ){
            var $form = $('.advanced-search-module');
            fave_property_status_changed(selected_status_module_search, $form);
        }

        /*--------------------------------------------------------------------------
         *  Save Search
         * -------------------------------------------------------------------------*/
        $("#save_search_click").click(function(e) {
            e.preventDefault();

            var $this = $(this);
            var $from = $('.save_search_form');

            if( parseInt( userID, 10 ) === 0 ) {
                $('#pop-login').modal('show');
            } else {
                $.ajax({
                    url: ajaxurl,
                    data: $from.serialize(),
                    method: $from.attr('method'),
                    dataType: 'JSON',

                    beforeSend: function () {
                        $this.children('i').remove();
                        $this.prepend('<i class="fa-left ' + process_loader_spinner + '"></i>');
                    },
                    success: function (response) {
                        if (response.success) {
                            $('#save_search_click').addClass('saved');
                        }
                    },
                    error: function (xhr, status, error) {
                        var err = eval("(" + xhr.responseText + ")");
                        console.log(err.Message);
                    },
                    complete: function () {
                        $this.children('i').removeClass(process_loader_spinner);
                    }
                });
            }

        });

        /*--------------------------------------------------------------------------
         * Delete Search
         * --------------------------------------------------------------------------*/
        $('.remove-search').click(function(e) {
            e.preventDefault();
            var $this = $(this);
            var prop_id = $this.data('propertyid');
            var removeBlock = $this.parents('.saved-search-block');

            if (confirm(confirm_message)) {
                $.ajax({
                    url: ajaxurl,
                    dataType: 'JSON',
                    method: 'POST',
                    data: {
                        'action': 'houzez_delete_search',
                        'property_id': prop_id
                    },
                    beforeSend: function () {
                        $this.children('i').remove();
                        $this.prepend('<i class="' + process_loader_spinner + '"></i>');
                    },
                    success: function (res) {
                        if (res.success) {
                            removeBlock.remove();
                        }
                    },
                    error: function (xhr, status, error) {
                        var err = eval("(" + xhr.responseText + ")");
                        console.log(err.Message);
                    }
                });
            }
        });

        /*--------------------------------------------------------------------------
         *  Property Agent Contact Form
         * -------------------------------------------------------------------------*/
        $( '.agent_contact_form').click(function(e) {
            e.preventDefault();

            var $this = $(this);
            var $form = $this.parents( 'form' );
            var $result = $form.find('.form_messages');

            $.ajax({
                url: ajaxurl,
                data: $form.serialize(),
                method: $form.attr('method'),
                dataType: "JSON",

                beforeSend: function( ) {
                    $this.children('i').remove();
                    $this.prepend('<i class="fa-left '+process_loader_spinner+'"></i>');
                },
                success: function(response) {
                    if( response.success ) {
                        $result.empty().append(response.msg);
                        $form.find('input').val('');
                        $form.find('textarea').val('');
                    } else {
                        $result.empty().append(response.msg);
                        $this.children('i').removeClass(process_loader_spinner);
                    }
                    if(houzez_reCaptcha == 1) {
                        houzezReCaptchaReset();
                    }
                },
                error: function(xhr, status, error) {
                    var err = eval("(" + xhr.responseText + ")");
                    console.log(err.Message);
                },
                complete: function(){
                    $this.children('i').removeClass(process_loader_spinner);
                    $this.children('i').addClass(success_icon);
                }
            });
        });

        /*--------------------------------------------------------------------------
         *   Contact agent form on agent detail page
         * -------------------------------------------------------------------------*/

        $('#agent_detail_contact_btn').click(function(e) {
            e.preventDefault();
            var current_element = $(this);
            var $this = $(this);
            var $form = $this.parents( 'form' );

            jQuery.ajax({
                type: 'post',
                url: ajaxurl,
                data: $form.serialize(),
                method: $form.attr('method'),
                dataType: "JSON",

                beforeSend: function( ) {
                    current_element.children('i').remove();
                    current_element.prepend('<i class="fa-left '+process_loader_spinner+'"></i>');
                },
                success: function( res ) {
                    current_element.children('i').removeClass(process_loader_spinner);
                    if( res.success ) {
                        $('#form_messages').empty().append(res.msg);
                        current_element.children('i').addClass(success_icon);
                    } else {
                        $('#form_messages').empty().append(res.msg);
                    }
                    if(houzez_reCaptcha == 1) {
                        houzezReCaptchaReset();
                    }
                },
                error: function(xhr, status, error) {
                    var err = eval("(" + xhr.responseText + ")");
                    console.log(err.Message);
                }

            });
        });

        /*--------------------------------------------------------------------------
         *  Property Schedule Contact Form
         * -------------------------------------------------------------------------*/
        $( '.schedule_contact_form').click(function(e) {
            e.preventDefault();

            var $this = $(this);
            var $form = $this.parents( 'form' );
            var $result = $form.find('.form_messages');

            $.ajax({
                url: ajaxurl,
                data: $form.serialize(),
                method: $form.attr('method'),
                dataType: "JSON",

                beforeSend: function( ) {
                    $this.children('i').remove();
                    $this.prepend('<i class="fa-left '+process_loader_spinner+'"></i>');
                },
                success: function(response) {
                    if( response.success ) {
                        $result.empty().append(response.msg);
                        $form.find('input').val('');
                        $form.find('textarea').val('');
                    } else {
                        $result.empty().append(response.msg);
                    }
                },
                error: function(xhr, status, error) {
                    var err = eval("(" + xhr.responseText + ")");
                    console.log(err.Message);
                },
                complete: function(){
                    $this.children('i').removeClass(process_loader_spinner);
                    $this.children('i').addClass(success_icon);
                }
            });

            // var name = $('#name').val();
        });

        /*--------------------------------------------------------------------------
         *   Resend Property For approval - only for per listing
         * -------------------------------------------------------------------------*/
        $('.resend-for-approval-perlisting').click(function (e) {
            e.preventDefault();

            var prop_id = $(this).attr('data-propid');
            resend_for_approval_perlisting( prop_id, $(this) );
            $(this).unbind( "click" );
        });

        var resend_for_approval_perlisting = function( prop_id, currentDiv ) {

            $.ajax({
                type: 'POST',
                url: ajaxurl,
                dataType: 'JSON',
                data: {
                    'action' : 'houzez_resend_for_approval_perlisting',
                    'propid' : prop_id
                },
                success: function ( res ) {

                    if( res.success ) {
                        currentDiv.parent().empty().append('<span class="label-success label">'+res.msg+'</span>');
                    } else {
                        currentDiv.parent().empty().append('<div class="alert alert-danger">'+res.msg+'</div>');
                    }

                },
                error: function(xhr, status, error) {
                    var err = eval("(" + xhr.responseText + ")");
                    console.log(err.Message);
                }

            });//end ajax
        }

        /*--------------------------------------------------------------------------
         *   Add or remove favorites
         * -------------------------------------------------------------------------*/
        var houzez_init_add_favorite = function() {
            $(".add_fav").click(function () {
                var curnt = $(this).children('i');
                var propID = $(this).attr('data-propid');
                add_to_favorite( propID, curnt );
            });
        }
        houzez_init_add_favorite();

        var houzez_init_remove_favorite = function() {
            $(".remove_fav").click(function () {
                var curnt = $(this);
                var propID = $(this).attr('data-propid');
                add_to_favorite( propID, curnt );
                var itemWrap = curnt.parents('.item-wrap').remove();
            });
        }
        houzez_init_remove_favorite();

        var add_to_favorite = function ( propID, curnt ) {
            if( parseInt( userID, 10 ) === 0 ) {
                $('#pop-login').modal('show');
            } else {
                jQuery.ajax({
                    type: 'post',
                    url: ajaxurl,
                    dataType: 'json',
                    data: {
                        'action': 'houzez_add_to_favorite',
                        'property_id': propID
                    },
                    beforeSend: function( ) {
                        curnt.addClass('faa-pulse animated');
                    },
                    success: function( data ) {
                        if( data.added ) {
                            curnt.removeClass('fa-heart-o').addClass('fa-heart');
                        } else {
                            curnt.removeClass('fa-heart').addClass('fa-heart-o');
                        }
                        curnt.removeClass('faa-pulse animated');
                    },
                    error: function(xhr, status, error) {
                        var err = eval("(" + xhr.responseText + ")");
                        console.log(err.Message);
                    }
                });
            } // End else
        }

        /* ------------------------------------------------------------------------ */
        /*  Fave login and regsiter
         /* ------------------------------------------------------------------------ */
        $('.fave-login-button').click(function(e){
            e.preventDefault();
            var currnt = $(this);
            houzez_login( currnt );
        });

        $('.fave-register-button').click(function(e){
            e.preventDefault();
            var currnt = $(this);
            houzez_register( currnt );
        });

        var houzez_login = function( currnt ) {
            var $form = currnt.parents('form');
            var $messages = currnt.parents('.login-block').find('.houzez_messages');

            $.ajax({
                type: 'post',
                url: ajaxurl,
                dataType: 'json',
                data: $form.serialize(),
                beforeSend: function () {
                    $messages.empty().append('<p class="success text-success"> '+ login_sending +'</p>');
                },
                success: function( response ) {
                    if( response.success ) {
                        $messages.empty().append('<p class="success text-success"><i class="fa fa-check"></i> '+ response.msg +'</p>');
                        if( login_redirect_type == 'same_page' ) {
                            window.location.reload();
                        } else {
                            window.location.href = login_redirect;
                        }

                    } else {
                        $messages.empty().append('<p class="error text-danger"><i class="fa fa-close"></i> '+ response.msg +'</p>');
                    }
                    if(houzez_reCaptcha == 1) {
                        houzezReCaptchaReset();
                    }
                },
                error: function(xhr, status, error) {
                    var err = eval("(" + xhr.responseText + ")");
                    console.log(err.Message);
                }
            })

        } // end houzez_login

        var houzez_register = function ( currnt ) {

            var $form = currnt.parents('form');
            var $messages = currnt.parents('.class-for-register-msg').find('.houzez_messages_register');
            
            $.ajax({
                type: 'post',
                url: ajaxurl,
                dataType: 'json',
                data: $form.serialize(),
                beforeSend: function () {
                    $messages.empty().append('<p class="success text-success"> '+ login_sending +'</p>');
                },
                success: function( response ) {
                    if( response.success ) {
                        $messages.empty().append('<p class="success text-success"><i class="fa fa-check"></i> '+ response.msg +'</p>');
                        
                        window.location.href = "https://" + window.location.hostname + '/my-profile';
                    } else {
                        $messages.empty().append('<p class="error text-danger"><i class="fa fa-close"></i> '+ response.msg +'</p>');
                    }
                    if(houzez_reCaptcha == 1) {
                        houzezReCaptchaReset();
                    }
                },
                error: function(xhr, status, error) {
                    var err = eval("(" + xhr.responseText + ")");
                    console.log(err.Message);
                }
            });
        }

        /* ------------------------------------------------------------------------ */
        /*  Reset Password
         /* ------------------------------------------------------------------------ */
        $( '#houzez_forgetpass').click(function(){
            var user_login = $('#user_login_forgot').val(),
                security    = $('#fave_resetpassword_security').val();

            $.ajax({
                type: 'post',
                url: ajaxurl,
                dataType: 'json',
                data: {
                    'action': 'houzez_reset_password',
                    'user_login': user_login,
                    'security': security
                },
                beforeSend: function () {
                    $('#houzez_msg_reset').empty().append('<p class="success text-success"> '+ login_sending +'</p>');
                },
                success: function( response ) {
                    if( response.success ) {
                        $('#houzez_msg_reset').empty().append('<p class="success text-success"><i class="fa fa-check"></i> '+ response.msg +'</p>');
                    } else {
                        $('#houzez_msg_reset').empty().append('<p class="error text-danger"><i class="fa fa-close"></i> '+ response.msg +'</p>');
                    }
                },
                error: function(xhr, status, error) {
                    var err = eval("(" + xhr.responseText + ")");
                    console.log(err.Message);
                }
            });

        });


        if( $('#houzez_reset_password').length > 0 ) {
            $('#houzez_reset_password').click( function(e) {
                e.preventDefault();

                var $this = $(this);
                var rg_login = $('input[name="rp_login"]').val();
                var rp_key = $('input[name="rp_key"]').val();
                var pass1 = $('input[name="pass1"]').val();
                var pass2 = $('input[name="pass2"]').val();
                var security = $('input[name="fave_resetpassword_security"]').val();

                $.ajax({
                    type: 'post',
                    url: ajaxurl,
                    dataType: 'json',
                    data: {
                        'action': 'houzez_reset_password_2',
                        'rq_login': rg_login,
                        'password': pass1,
                        'confirm_pass': pass2,
                        'rp_key': rp_key,
                        'security': security
                    },
                    beforeSend: function( ) {
                        $this.children('i').remove();
                        $this.prepend('<i class="fa-left '+process_loader_spinner+'"></i>');
                    },
                    success: function(data) {
                        if( data.success ) {
                            jQuery('#password_reset_msgs').empty().append('<p class="success text-success"><i class="fa fa-check"></i> '+ data.msg +'</p>');
                            jQuery('#oldpass, #newpass, #confirmpass').val('');
                        } else {
                            jQuery('#password_reset_msgs').empty().append('<p class="error text-danger"><i class="fa fa-close"></i> '+ data.msg +'</p>');
                        }
                    },
                    error: function(errorThrown) {

                    },
                    complete: function(){
                        $this.children('i').removeClass(process_loader_spinner);
                    }

                });

            } );
        }

        /* ------------------------------------------------------------------------ */
        /*  Paypal single listing payment
         /* ------------------------------------------------------------------------ */
        $('#houzez_complete_order').click(function(e) {
            e.preventDefault();
            var hform, payment_gateway, houzez_listing_price, property_id, is_prop_featured, is_prop_upgrade;

            payment_gateway = $("input[name='houzez_payment_type']:checked").val();
            is_prop_featured = $("input[name='featured_pay']").val();
            is_prop_upgrade = $("input[name='is_upgrade']").val();

            property_id = $('#houzez_property_id').val();
            houzez_listing_price = $('#houzez_listing_price').val();

            if( payment_gateway == 'paypal' ) {
                fave_processing_modal( paypal_connecting );
                fave_paypal_payment( property_id, is_prop_featured, is_prop_upgrade);

            } else if ( payment_gateway == 'stripe' ) {
                hform = $(this).parents('form');
                if( is_prop_featured === '1' ) {
                    hform.find('.houzez_stripe_simple_featured button').trigger( "click" );
                } else {
                    hform.find('.houzez_stripe_simple button').trigger("click");
                }
            } else if ( payment_gateway == 'direct_pay' ) {
                fave_processing_modal( direct_pay_text );
                direct_bank_transfer(property_id, houzez_listing_price);
            }
            return;

        });

        var fave_processing_modal = function ( msg ) {
            var process_modal ='<div class="modal fade" id="fave_modal" tabindex="-1" role="dialog" aria-labelledby="faveModalLabel" aria-hidden="true"><div class="modal-dialog"><div class="modal-content"><div class="modal-body houzez_messages_modal">'+msg+'</div></div></div></div></div>';
            jQuery('body').append(process_modal);
            jQuery('#fave_modal').modal();
        }

        var fave_processing_modal_close = function ( ) {
            jQuery('#fave_modal').modal('hide');
        }


        /* ------------------------------------------------------------------------ */
        /*  Paypal payment function
         /* ------------------------------------------------------------------------ */
        var fave_paypal_payment = function( property_id, is_prop_featured, is_prop_upgrade ) {

            $.ajax({
                type: 'post',
                url: ajaxurl,
                data: {
                    'action': 'houzez_property_paypal_payment',
                    'prop_id': property_id,
                    'is_prop_featured': is_prop_featured,
                    'is_prop_upgrade': is_prop_upgrade,
                },
                success: function( response ) {
                    window.location.href = response;
                },
                error: function(xhr, status, error) {
                    var err = eval("(" + xhr.responseText + ")");
                    console.log(err.Message);
                }
            });
        }

        /* ------------------------------------------------------------------------ */
        /*  Select Membership payment
         /* ------------------------------------------------------------------------ */

        var houzez_membership_data = function(currnt) {
            var payment_gateway = $("input[name='houzez_payment_type']:checked").val();
            var houzez_package_price = $("input[name='houzez_package_price']").val();
            var houzez_package_id    = $("input[name='houzez_package_id']").val();
            var houzez_package_name  = $("#houzez_package_name").text();

            if( payment_gateway == 'paypal' ) {
                fave_processing_modal( paypal_connecting );
                if ($('#paypal_package_recurring').is(':checked')) {
                    houzez_recuring_paypal_package_payment( houzez_package_price, houzez_package_name, houzez_package_id );
                } else {
                    houzez_paypal_package_payment( houzez_package_price, houzez_package_name, houzez_package_id );
                }

            } else if( payment_gateway == 'mollie' ) {
                fave_processing_modal( mollie_connecting );
                houzez_mollie_package_payment( houzez_package_price, houzez_package_name, houzez_package_id );

            } else if ( payment_gateway == 'stripe' ) {
                var hform = currnt.parents('form');
                hform.find('.houzez_stripe_membership button').trigger( "click" );

            } else if ( payment_gateway == 'direct_pay' ) {
                fave_processing_modal( direct_pay_text );
                direct_bank_transfer_package( houzez_package_id, houzez_package_price, houzez_package_name );

            } else if ( payment_gateway == '2checkout' ) {
                //return false;

            } else if ( payment_gateway == 'bitcoin' ) {
                window.open($('.payment-bitcoin').closest('.radio').next().val(), '_blank');
            } else if ( payment_gateway == 'googlepay' ) {
                
            } else if ( payment_gateway == 'applepay' ) {
                
            } else {
                fave_processing_modal( direct_pay_text );
                houzez_free_membership_package(  houzez_package_id );
            }

            return false;
        }

        var houzez_register_user_with_membership = function ( currnt ) {

            var $form = currnt.parents('form');
            var $messages = currnt.parents('.class-for-register-msg').find('.houzez_messages_register');

            $.ajax({
                type: 'post',
                url: ajaxurl,
                dataType: 'json',
                data: $form.serialize(),
                beforeSend: function () {
                    $messages.empty().append('<p class="success text-success"> '+ login_sending +'</p>');
                },
                success: function( response ) {
                    if( response.success ) {
                        houzez_membership_data(currnt);
                    } else {
                        $messages.empty().append('<p class="error text-danger"><i class="fa fa-close"></i> '+ response.msg +'</p>');
                    }
                },
                error: function(xhr, status, error) {
                    var err = eval("(" + xhr.responseText + ")");
                    console.log(err.Message);
                }
            });
        }

        $('#houzez_complete_membership').click( function(e) {
            e.preventDefault();
            var currnt = $(this);
            if( houzez_logged_in == 'no' ) {
                houzez_register_user_with_membership( currnt );
                return;
            }
            houzez_membership_data(currnt);
        } );

        var houzez_paypal_package_payment = function( houzez_package_price, houzez_package_name, houzez_package_id ) {

            $.ajax({
                type: 'POST',
                url: ajaxurl,
                data: {
                    'action'    : 'houzez_paypal_package_payment',
                    'houzez_package_price' : houzez_package_price,
                    'houzez_package_name'  : houzez_package_name,
                    'houzez_package_id'  : houzez_package_id
                },
                success: function (data) {
                    window.location.href = data;
                },
                error: function(xhr, status, error) {
                    var err = eval("(" + xhr.responseText + ")");
                    console.log(err.Message);
                }
            });
        }

        var houzez_recuring_paypal_package_payment = function(  houzez_package_price, houzez_package_name, houzez_package_id  ) {

            jQuery.ajax({
                type: 'POST',
                url: ajaxurl,
                data: {
                    'action'               : 'houzez_recuring_paypal_package_payment',
                    'houzez_package_name'  : houzez_package_name,
                    'houzez_package_id'    : houzez_package_id,
                    'houzez_package_price' : houzez_package_price
                },
                success: function (data) {
                    window.location.href = data;
                },
                error: function(xhr, status, error) {
                    var err = eval("(" + xhr.responseText + ")");
                    console.log(err.Message);
                }
            });
        }

        var houzez_mollie_package_payment = function( houzez_package_price, houzez_package_name, houzez_package_id ) {

            $.ajax({
                type: 'POST',
                url: ajaxurl,
                data: {
                    'action'    : 'houzez_mollie_package_payment',
                    'houzez_package_price' : houzez_package_price,
                    'houzez_package_name'  : houzez_package_name,
                    'houzez_package_id'  : houzez_package_id
                },
                success: function (data) {
                    window.location.href = data;
                },
                error: function(xhr, status, error) {
                    var err = eval("(" + xhr.responseText + ")");
                    console.log(err.Message);
                }
            });
        }

        var direct_bank_transfer_package = function( houzez_package_id, houzez_package_price, houzez_package_name ) {

            jQuery.ajax({
                type: 'POST',
                url: ajaxurl,
                data: {
                    'action'           : 'houzez_direct_pay_package',
                    'selected_package' : houzez_package_id,
                },
                success: function (data) {
                    window.location.href = data;

                },
                error: function (errorThrown) {}
            });
        }

        /*--------------------------------------------------------------------------
         *   Houzez Free Membership Package
         * -------------------------------------------------------------------------*/
        var houzez_free_membership_package = function ( houzez_package_id ) {
            jQuery.ajax({
                type: 'POST',
                url: ajaxurl,
                data: {
                    'action'           : 'houzez_free_membership_package',
                    'selected_package' : houzez_package_id,
                },
                success: function (data) {
                    window.location.href = data;

                },
                error: function (errorThrown) {}
            });
        }

        /*--------------------------------------------------------------------------
         *   Resend Property For approval - only for membership
         * -------------------------------------------------------------------------*/
        $('.resend-for-approval').click(function (e) {
            e.preventDefault();

            if (confirm(confirm_relist)) {
                var prop_id = $(this).attr('data-propid');
                resend_for_approval(prop_id, $(this));
                $(this).unbind("click");
            }
        });

        var resend_for_approval = function( prop_id, currentDiv ) {

            $.ajax({
                type: 'POST',
                url: ajaxurl,
                dataType: 'JSON',
                data: {
                    'action' : 'houzez_resend_for_approval',
                    'propid' : prop_id
                },
                success: function ( res ) {

                    if( res.success ) {
                        currentDiv.parent().empty().append('<span class="label-success label">'+res.msg+'</span>');
                        var total_listings = parseInt(jQuery('.listings_remainings').text(), 10);
                        jQuery('.listings_remainings').text(total_listings - 1);
                    } else {
                        currentDiv.parent().empty().append('<div class="alert alert-danger">'+res.msg+'</div>');
                    }

                },
                error: function(xhr, status, error) {
                    var err = eval("(" + xhr.responseText + ")");
                    console.log(err.Message);
                }

            });//end ajax
        }

        /*--------------------------------------------------------------------------
         *   Make Property Featured - only for membership
         * -------------------------------------------------------------------------*/
        /*$('.make-prop-featured').click(function (e) {
            e.preventDefault();

            if (confirm(confirm_featured)) {
                var prop_id = $(this).attr('data-propid');
                var prop_type = $(this).attr('data-proptype');
                make_prop_featured(prop_id, $(this), prop_type);
                $(this).unbind("click");
            }
        });

        var make_prop_featured = function( prop_id, currentDiv, prop_type ) {

            $.ajax({
                type: 'POST',
                url: ajaxurl,
                dataType: 'JSON',
                data: {
                    'action' : 'houzez_make_prop_featured',
                    'propid' : prop_id,
                    'prop_type': prop_type
                },
                success: function ( res ) {

                    if( res.success ) {
                        var prnt = currentDiv.parents('.item-wrap');
                        prnt.find('.item-thumb').append('<span class="label-featured label">'+fave_prop_featured+'</span>');
                        currentDiv.remove();
                        window.location.reload();
                        var total_featured_listings = parseInt(jQuery('.featured_listings_remaining').text(), 10);
                        jQuery('.featured_listings_remaining').text(total_featured_listings - 1);
                    } else {
                        currentDiv.parent().empty().append('<div class="alert alert-danger">'+featured_listings_none+'</div>');
                    }

                },
                error: function(xhr, status, error) {
                    var err = eval("(" + xhr.responseText + ")");
                    console.log(err.Message);
                }

            });//end ajax
        }*/

        /*--------------------------------------------------------------------------
         *   Make Property Featured - only for membership
         * -------------------------------------------------------------------------*/
        $('.remove-prop-featured').click(function (e) {
            e.preventDefault();

            if (confirm(confirm_featured_remove)) {
                var prop_id = $(this).attr('data-propid');
                remove_prop_featured(prop_id, $(this));
                $(this).unbind("click");
            }
        });

        var remove_prop_featured = function( prop_id, currentDiv ) {

            $.ajax({
                type: 'POST',
                url: ajaxurl,
                dataType: 'JSON',
                data: {
                    'action' : 'houzez_remove_prop_featured',
                    'propid' : prop_id
                },
                success: function ( res ) {
                    if( res.success ) {
                        var prnt = currentDiv.parents('.item-wrap');
                        prnt.find('.label-featured').remove();
                        currentDiv.remove();
                        window.location.reload();
                        //currentDiv.parent().empty().append('<div class="alert alert-success">'+featured_listings_none+'</div>');
                    }
                },
                error: function(xhr, status, error) {
                    var err = eval("(" + xhr.responseText + ")");
                    console.log(err.Message);
                }

            });//end ajax
        }

        /* ------------------------------------------------------------------------ */
        /*  Wire Transfer per listing payment
         /* ------------------------------------------------------------------------ */
        var direct_bank_transfer = function( prop_id, listing_price ) {
            var is_featured = $('input[name="featured_pay"]').val();
            var is_upgrade = $('input[name="is_upgrade"]').val();

            jQuery.ajax({
                type: 'POST',
                url: ajaxurl,
                data: {
                    'action'        : 'houzez_direct_pay_per_listing',
                    'prop_id'       : prop_id,
                    'is_featured'   : is_featured,
                    'is_upgrade'    : is_upgrade
                },
                success: function (data) {
                    window.location.href = data;
                },
                error: function (errorThrown) {}
            });

        }


        /*--------------------------------------------------------------------------
         *  Social Logins
         * -------------------------------------------------------------------------*/
        $('.yahoo-login').click(function () {
            var current = $(this);
            houzez_login_via_yahoo( current );
        });

        var houzez_login_via_yahoo = function ( current ) {
            var $form = current.parents('form');
            var $messages = current.parents('.login-block').find('.houzez_messages');

            $.ajax({
                type: 'POST',
                url: ajaxurl,
                data: {
                    'action' : 'houzez_yahoo_login'
                },
                beforeSend: function () {
                    $messages.empty().append('<p class="success text-success"> '+ login_sending +'</p>');
                },
                success: function (data) {
                    window.location.href = data;
                },
                error: function(xhr, status, error) {
                    var err = eval("(" + xhr.responseText + ")");
                    console.log(err.Message);
                }
            });
        }

        $('.google-login').click(function () {
            var current = $(this);
            houzez_login_via_google( current );
        });

        var houzez_login_via_google = function ( current ) {
            var $form = current.parents('form');
            var $messages = current.parents('.login-block').find('.houzez_messages');

            $.ajax({
                type: 'POST',
                url: ajaxurl,
                data: {
                    'action' : 'houzez_google_login_oauth'
                },
                beforeSend: function () {
                    $messages.empty().append('<p class="success text-success"> '+ login_sending +'</p>');
                },
                success: function (data) { 
                    window.location.href = data;
                },
                error: function(xhr, status, error) {
                    var err = eval("(" + xhr.responseText + ")");
                    console.log(err.Message);
                }
            });
        }

        $('.facebook-login').click(function () {
            var current = $(this);
            houzez_login_via_facebook( current );
        });

        var houzez_login_via_facebook = function ( current ) {
            var $form = current.parents('form');
            var $messages = current.parents('.login-block').find('.houzez_messages');

            $.ajax({
                type: 'POST',
                url: ajaxurl,
                data: {
                    'action' : 'houzez_facebook_login_oauth'
                },
                beforeSend: function () {
                    $messages.empty().append('<p class="success text-success"> '+ login_sending +'</p>');
                },
                success: function (data) { 
                    window.location.href = data;
                },
                error: function(xhr, status, error) {
                    var err = eval("(" + xhr.responseText + ")");
                    console.log(err.Message);
                }
            });
        }

        /*--------------------------------------------------------------------------
         *  Invoice Filter
         * -------------------------------------------------------------------------*/
        $('#invoice_status, #invoice_type').change(function() {
            houzez_invoices_filter();
        });

        $('#startDate, #endDate').focusout(function() {
            houzez_invoices_filter();
        })

        var houzez_invoices_filter = function() {
            var inv_status = $('#invoice_status').val(),
                inv_type   = $('#invoice_type').val(),
                startDate  = $('#startDate').val(),
                endDate  = $('#endDate').val();

            $.ajax({
                url: ajaxurl,
                dataType: 'json',
                type: 'POST',
                data: {
                    'action': 'houzez_invoices_ajax_search',
                    'invoice_status': inv_status,
                    'invoice_type'  : inv_type,
                    'startDate'     : startDate,
                    'endDate'       : endDate
                },
                success: function(res) { //alert(res);
                    if(res.success) {
                        $('#invoices_content').empty().append( res.result );
                        $( '#invoices_total_price').empty().append( res.total_price );
                    }
                },
                error: function(xhr, status, error) {
                    var err = eval("(" + xhr.responseText + ")");
                    console.log(err.Message);
                }
            });
        }

        var infoWindowStyle = function() {
            google.maps.event.addListener(iw, 'domready', function() {

               // Reference to the DIV which receives the contents of the infowindow using jQuery
               var iwOuter = $('.gm-style-iw');

               /* The DIV we want to change is above the .gm-style-iw DIV.
                * So, we use jQuery and create a iwBackground variable,
                * and took advantage of the existing reference to .gm-style-iw for the previous DIV with .prev().
                */
               var iwBackground = iwOuter.prev();

               // Remove the background shadow DIV
               iwBackground.children(':nth-child(2)').css({'display' : 'none'});

               // Remove the white background DIV
               iwBackground.children(':nth-child(4)').css({'display' : 'none'});

            });
        }

        /*--------------------------------------------------------------------------
         *  Houzez Add Marker
         * -------------------------------------------------------------------------*/
         var houzezAddMarkers_old = function( props, map ) {

            $.each(props, function(i, prop) {

                var latlng = new google.maps.LatLng(prop.lat,prop.lng);

                var prop_title = prop.data ? prop.data.post_title : prop.title;

                var marker_url = prop.icon;
                var marker_size = new google.maps.Size( 42, 53 );
                if( window.devicePixelRatio > 1.5 ) {
                    if ( prop.retinaIcon ) {
                        marker_url = prop.retinaIcon;
                        marker_size = new google.maps.Size( 42, 53 );
                    }
                }

                var marker_icon = {
                    url: marker_url,
                    size: marker_size,
                    scaledSize: new google.maps.Size( 42, 53 ),
                    origin: new google.maps.Point( 0, 0 ),
                    /*anchor: new google.maps.Point( 7, 53 )*/
                };

                var marker = new google.maps.Marker({
                    position: latlng,
                    map: map,
                    icon: marker_icon,
                    draggable: false,
                    flat: true,
                    title: prop_title,
                    animation: google.maps.Animation.DROP,
                    //title: 'marker-'+prop.sanitizetitle
                });

                
                
                var propMeta = prop.prop_meta;
                if( propMeta == null ) {
                    propMeta = '';
                }

                var infoboxContent = document.createElement("div");
                infoboxContent.className = 'property-item item-grid map-info-box map-info-box-v1';
                infoboxContent.innerHTML = '' +
                    '<div class="figure-block">' +
                    '<figure class="item-thumb">' +
                    '<div class="price hide-on-list">' +
                    '<span class="item-price">'+prop.price+'</span>' +
                    '</div>' +
                    '<a href="'+prop.url+'" class="hover-effect" tabindex="0">' + prop.thumbnail + '</a>' +
                    '</figure>' +
                    '</div>' +
                    '<div class="item-body">' +
                    '<div class="body-left">' +
                    '<div class="info-row">' +
                    '<h2><a target="_blank" href="'+prop.url+'">'+prop_title+'</a></h2>' +
                    '<h4>'+prop.address+'</h4>' +
                    '</div>' +
                    '<div class="table-list full-width info-row">' +
                    '<div class="cell">' +
                    '<div class="info-row amenities">' + propMeta +
                    '<p>'+prop.type+'</p>' +
                    '</div>' +
                    '</div>' +
                    '</div>' +
                    '</div>' +
                    '</div>';

                google.maps.event.addListener(marker, 'click', (function (marker, i) {
                    return function () {
                        var scale = Math.pow(2, map.getZoom());
                        //alert(scale);
                        var offsety = ( (100 / scale) || 0 );
                        var projection = map.getProjection();
                        var markerPosition = marker.getPosition();
                        var markerScreenPosition = projection.fromLatLngToPoint(markerPosition);
                        var pointHalfScreenAbove = new google.maps.Point(markerScreenPosition.x, markerScreenPosition.y - offsety);
                        var aboveMarkerLatLng = projection.fromPointToLatLng(pointHalfScreenAbove);
                        
                        infobox.setContent(infoboxContent);
                        infobox.open(map, marker);
                        map.setCenter(aboveMarkerLatLng);

                    }
                })(marker, i));


                markers.push(marker);
                console.log(markers);
            });
        }

        var houzezAddMarkers = function( props, map ) {
              
            infoWindowStyle();

            var oms = new OverlappingMarkerSpiderfier(map, { markersWontMove: true, markersWontHide: true, circleFootSeparation: 60, keepSpiderfied: true });  

            $.each(props, function(i, prop) {

               
                var latlng = new google.maps.LatLng(prop.lat,prop.lng);

                var prop_title = prop.data ? prop.data.post_title : prop.title;
                

                if( markerPricePins == 'yes' ) {
                    var pricePin = '<div data-id="'+prop.id+'" class="gm-marker gm-marker-color-'+prop.term_id+'"><div class="gm-marker-price">'+prop.pricePin+'</div></div>';
            
                    var marker = new RichMarker({
                      map: map,
                      position: latlng,
                      draggable: true,
                      flat: true,
                      anchor: RichMarkerPosition.MIDDLE,
                      content: pricePin
                    });

                } else {
                    var marker_url = prop.icon;
                    var marker_size = new google.maps.Size( 44, 56 );
                    if( window.devicePixelRatio > 1.5 ) {
                        if ( prop.retinaIcon ) {
                            marker_url = prop.retinaIcon;
                            marker_size = new google.maps.Size( 44, 56 );
                        }
                    }

                    var marker_icon = {
                        url: marker_url,
                        size: marker_size,
                        scaledSize: new google.maps.Size( 44, 56 ),
                    };

                    var marker = new google.maps.Marker({
                        position: latlng,
                        map: map,
                        icon: marker_icon,
                        draggable: false,
                        title: prop_title,
                        animation: google.maps.Animation.DROP,
                    });
                }


                var propMeta = prop.prop_meta;
                if( propMeta == null ) {
                    propMeta = '';
                }

                var infoboxContent = document.createElement("div");
                infoboxContent.className = 'property-item item-grid map-info-box';
                infoboxContent.innerHTML = '' +
                    '<div class="figure-block">' +
                    '<figure class="item-thumb">' +
                    '<div class="price hide-on-list">' +
                    '<span class="item-price">'+prop.price+'</span>' +
                    '</div>' +
                    '<a href="'+prop.url+'" class="hover-effect" tabindex="0">' + prop.thumbnail + '</a>' +
                    '</figure>' +
                    '</div>' +
                    '<div class="item-body">' +
                    '<div class="body-left">' +
                    '<div class="info-row">' +
                    '<h2><a href="'+prop.url+'">'+prop_title+'</a></h2>' +
                    '<h4>'+prop.address+'</h4>' +
                    '</div>' +
                    '<div class="table-list full-width info-row">' +
                    '<div class="cell">' +
                    '<div class="info-row amenities">' + propMeta +
                    '<p>'+prop.type+'</p>' +
                    '</div>' +
                    '</div>' +
                    '</div>' +
                    '</div>' +
                    '</div>';


                    google.maps.event.addListener(marker, 'click', (function (marker, i) {
                        return function () {
                            var scale = Math.pow(2, map.getZoom());
                            
                            var offsety = ( (100 / scale) || 0 );
                            var projection = map.getProjection();
                            var markerPosition = marker.getPosition();
                            var markerScreenPosition = projection.fromLatLngToPoint(markerPosition);
                            var pointHalfScreenAbove = new google.maps.Point(markerScreenPosition.x, markerScreenPosition.y - offsety);
                            var aboveMarkerLatLng = projection.fromPointToLatLng(pointHalfScreenAbove);
                            
                            iw.setContent(infoboxContent);
                            iw.open(map, marker);
                            map.setCenter(aboveMarkerLatLng);

                        }
                    })(marker, i));

                    var openedWindows = new Array();
                    var closeOpenedWindows = function() {
                        while ( 0 < openedWindows.length ) {
                            var windowToClose = openedWindows.pop();
                            windowToClose.close();
                        }
                    };

                    oms.addMarker(marker);
                
                markers.push(marker);
            }); // End $.each

        } // End Add Marker


        var houzezAddMarkerSimple = function( props, map ) {
              
            
            $.each(props, function(i, prop) {

               
                var latlng = new google.maps.LatLng(prop.lat,prop.lng);

                var prop_title = prop.data ? prop.data.post_title : prop.title;
                
                var marker_url = prop.icon;
                var marker_size = new google.maps.Size( 44, 56 );
                if( window.devicePixelRatio > 1.5 ) {
                    if ( prop.retinaIcon ) {
                        marker_url = prop.retinaIcon;
                        marker_size = new google.maps.Size( 44, 56 );
                    }
                }

                if( markerPricePins == 'yes' ) {
                    var pricePin = '<div class="gm-marker gm-marker-color-'+prop.term_id+'"><div class="gm-marker-price">'+prop.pricePin+'</div></div>';
            
                    var marker = new RichMarker({
                      map: map,
                      position: latlng,
                      draggable: true,
                      flat: true,
                      anchor: RichMarkerPosition.MIDDLE,
                      content: pricePin
                    });

                } else {
                    var marker_icon = {
                        url: marker_url,
                        size: marker_size,
                        scaledSize: new google.maps.Size( 44, 56 ),
                    };

                    var marker = new google.maps.Marker({
                        position: latlng,
                        map: map,
                        icon: marker_icon,
                        draggable: false,
                        title: prop_title,
                        animation: google.maps.Animation.DROP,
                    });
                }
                
                markers.push(marker);
            });
        }

        /*--------------------------------------------------------------------------
         *  Header Map
         * -------------------------------------------------------------------------*/

        var houzez_map_zoomin = function(houzezMap) {
            google.maps.event.addDomListener(document.getElementById('listing-mapzoomin'), 'click', function () {
                var current= parseInt( houzezMap.getZoom(),10);
                console.log(current);
                current++;
                if(current > 20){
                    current = 20;
                }
                console.log('=='+current+' ++ ');
                houzezMap.setZoom(current);
            });
        }

        var houzez_map_zoomout = function(houzezMap) {
            google.maps.event.addDomListener(document.getElementById('listing-mapzoomout'), 'click', function () {
                var current= parseInt( houzezMap.getZoom(),10);
                console.log(current);
                current--;
                if(current < 0){
                    current = 0;
                }
                console.log('=='+current+' -- ');
                houzezMap.setZoom(current);
            });
        }

        if( document.getElementById('listing-mapzoomin') ) {
            houzez_map_zoomin(houzezMap);
        }
        if( document.getElementById('listing-mapzoomout') ) {
            houzez_map_zoomout(houzezMap);
        }

        var houzez_change_map_type = function(map_type){

            if(map_type==='roadmap'){
                houzezMap.setMapTypeId(google.maps.MapTypeId.ROADMAP);
            }else if(map_type==='satellite'){
                houzezMap.setMapTypeId(google.maps.MapTypeId.SATELLITE);
            }else if(map_type==='hybrid'){
                houzezMap.setMapTypeId(google.maps.MapTypeId.HYBRID);
            }else if(map_type==='terrain'){
                houzezMap.setMapTypeId(google.maps.MapTypeId.TERRAIN);
            }
            return false;
        }

        $('.houzezMapType').on('click', function(){
            var maptype = $(this).data('maptype');
            houzez_change_map_type(maptype);
        });



        var houzez_map_next = function() {
            current_marker++;
            if ( current_marker > markers.length ){
                current_marker = 1;
            }
            while( markers[current_marker-1].visible===false ){
                current_marker++;
                if ( current_marker > markers.length ){
                    current_marker = 1;
                }
            }
            if( houzezMap.getZoom() < 15 ){
                houzezMap.setZoom(15);
            }
            console.log(current_marker-1);
            google.maps.event.trigger( markers[current_marker-1], 'click' );

        }

        var houzez_map_prev = function() {
            current_marker--;
            if (current_marker < 1){
                current_marker = markers.length;
            }
            while( markers[current_marker-1].visible===false ){
                current_marker--;
                if ( current_marker > markers.length ){
                    current_marker = 1;
                }
            }
            if( houzezMap.getZoom() < 15 ){
                houzezMap.setZoom(15);
            }
            console.log(current_marker-1);
            google.maps.event.trigger( markers[current_marker-1], 'click');
        }

        $('#houzez-gmap-next').on('click', function(){
            houzez_map_next();
        });


        $('#houzez-gmap-prev').on('click', function(){
            houzez_map_prev();
        });


        var houzez_map_search_field = function (mapInput) {

            var searchBox = new google.maps.places.SearchBox(mapInput);
            houzezMap.controls[google.maps.ControlPosition.TOP_LEFT].push(mapInput);

            // Bias the SearchBox results towards current map's viewport.
            houzezMap.addListener('bounds_changed', function() {
                searchBox.setBounds(houzezMap.getBounds());
            });

            var markers_location = [];
            // Listen for the event fired when the user selects a prediction and retrieve
            // more details for that place.
            searchBox.addListener('places_changed', function() {
                var places = searchBox.getPlaces();

                if (places.length == 0) {
                    return;
                }

                // Clear out the old markers.
                markers_location.forEach(function(marker) {
                    marker.setMap(null);
                });
                markers_location = [];

                // For each place, get the icon, name and location.
                var bounds = new google.maps.LatLngBounds();
                places.forEach(function(place) {
                    var icon = {
                        url: place.icon,
                        size: new google.maps.Size(71, 71),
                        origin: new google.maps.Point(0, 0),
                        anchor: new google.maps.Point(17, 34),
                        scaledSize: new google.maps.Size(25, 25)
                    };

                    // Create a marker for each place.
                    markers_location.push(new google.maps.Marker({
                        map: houzezMap,
                        icon: icon,
                        title: place.name,
                        position: place.geometry.location
                    }));

                    if (place.geometry.viewport) {
                        // Only geocodes have viewport.
                        bounds.union(place.geometry.viewport);
                    } else {
                        bounds.extend(place.geometry.location);
                    }
                });
                houzezMap.fitBounds(bounds);
            });
        }

        if( document.getElementById('google-map-search') ) {
            var mapInput = document.getElementById('google-map-search');
            houzez_map_search_field(mapInput);
        }


        var reloadMarkers = function() {
            // Loop through markers and set map to null for each
            for (var i=0; i<markers.length; i++) {

                markers[i].setMap(null);
            }
            // Reset the markers array
            markers = [];
        }


        var houzezGeoLocation = function( map ) {

            // get my location useing HTML5 geolocation

            var googleGeoProtocol = true;
            var isChrome = !!window.chrome && !!window.chrome.webstore;

            if ( isChrome ) {

                if (document.location.protocol === 'http:' && ipinfo_location != 0 ) {

                    googleGeoProtocol = false;

                }

            }

            if ( googleGeoProtocol ) {

                if ( navigator.geolocation ) {

                    navigator.geolocation.getCurrentPosition( function( position ) {

                        var pos = {
                            lat: position.coords.latitude,
                            lng: position.coords.longitude
                        };

                        var geocoder = new google.maps.Geocoder;
                        //var infowindow = new google.maps.InfoWindow;

                        // var latLng   = new google.maps.LatLng( position.coords.latitude, position.coords.longitude );

                        geocoder.geocode({'location': pos}, function(results, status) {
                            if (status === 'OK') {
                                if (results[1]) {
                                    console.log( results[1] );
                                    // map.setZoom(11);
                                    var marker = new google.maps.Marker({
                                        position: pos,
                                        map: map
                                    });
                                    /*infowindow.setContent(results[1].formatted_address);
                                     infowindow.open(map, marker);*/
                                } else {
                                    window.alert('No results found');
                                }
                            } else {
                                window.alert('Geocoder failed due to: ' + status);
                            }
                        });


                        // alert( 'icon : ' + clusterIcon );

                        var circle = new google.maps.Circle({
                            radius: 10 * 200,
                            center: pos,
                            map: map,
                            //icon: clusterIcon,
                            fillColor: houzez_primary_color,
                            fillOpacity: 0.1,
                            strokeColor: houzez_primary_color,
                            strokeOpacity: 0.3
                        });

                        // circle.bindTo('center', marker, 'position');
                        map.fitBounds( circle.getBounds() );
                        // map.setCenter(pos);

                    }, function() {

                        handleLocationError(true, map, map.getCenter());

                    });

                }

            } else {

                $.getJSON('http://ipinfo.io', function(data){
                    // console.log(data);
                    var localtion = data.loc;
                    var localtion = localtion.split(",");

                    var localtion = {
                        lat: localtion[0] * 1,
                        lng: localtion[1] * 1
                    };

                    var circle = new google.maps.Circle({
                        radius: 10 * 100,
                        center: localtion,
                        map: map,
                        icon: clusterIcon,
                        fillColor: houzez_primary_color,
                        fillOpacity: 0.2,
                        strokeColor: houzez_primary_color,
                        strokeOpacity: 0.6
                    });

                    // circle.bindTo('center', marker, 'position');
                    map.fitBounds( circle.getBounds() );

                    var marker=new google.maps.Marker({
                        position    :localtion,
                        animation   :google.maps.Animation.DROP,
                        // icon: clusterIcon,
                        map: map
                    });
                    map.setCenter(localtion);
                });

            }

        }

        if( document.getElementById('houzez-gmap-location') ){
            google.maps.event.addDomListener(document.getElementById('houzez-gmap-location'), 'click', function () {
                houzezGeoLocation( houzezMap );
            });
        }


        var houzezLatLng = function ( keyword ) {

            var geocoder = new google.maps.Geocoder();
            geocoder.geocode( { 'address': keyword }, function(results, status) {

                if (status == 'OK') {
                    return results[0].geometry.location;
                }
            });

        }

        var half_map_ajax_pagi = function() {
            $('.half_map_ajax_pagi a').click(function(e){
                e.preventDefault();
                var current_page = $(this).data('houzepagi');
                var current_form = $('form#half_map_search_form');
                var form_widget = $(this).parents('.widget_houzez_advanced_search');
                houzez_search_on_change(current_form, form_widget, current_page);
            })
            return false;
        }


        var houzez_header_listing_map = function(keyword, country, state, location, area, status, type, label, property_id, bedrooms, bathrooms, min_price, max_price, min_area, max_area, features, publish_date, search_lat, search_long, search_radius, search_location, use_radius, currency, custom_fields_array ) {
            var headerMapSecurity = $('#securityHouzezHeaderMap').val();
            var initial_city = HOUZEZ_ajaxcalls_vars.header_map_selected_city;

            $('.map-notfound').remove();

            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: ajaxurl,
                data: {
                    'action': 'houzez_header_map_listings',
                    'initial_city': initial_city,
                    'keyword': keyword,
                    'country': country,
                    'state': state,
                    'location': location,
                    'area': area,
                    'status': status,
                    'type': type,
                    'label': label,
                    'property_id': property_id,
                    'bedrooms': bedrooms,
                    'bathrooms': bathrooms,
                    'min_price': min_price,
                    'max_price': max_price,
                    'currency': currency,
                    'min_area': min_area,
                    'max_area': max_area,
                    'features': features,
                    'publish_date': publish_date,
                    'search_lat': search_lat,
                    'search_long': search_long,
                    'use_radius': use_radius,
                    'search_location': search_location,
                    'search_radius': search_radius,
                    'custom_fields_values': custom_fields_array,
                    'security': headerMapSecurity
                },
                beforeSend: function() {
                    $('#houzez-map-loading').show();
                },
                success: function(data) { 

                    //if(data.getProperties === true) { alert(JSON.stringify(data.properties)); } return;

                    
                    remove_map_loader();
                    houzez_map_parallax();

                    if(data.getProperties === true) {

                        reloadMarkers();
                        houzezAddMarkers( data.properties, houzezMap );
                        houzez_map_bounds();
                        houzez_markerCluster();

                        $('#houzez-map-loading').hide();

                    } else {
                        reloadMarkers();
                        $('#houzez-listing-map').append('<div class="map-notfound">'+not_found+'</div>');
                    }

                },
                error: function(xhr, ajaxOptions, thrownError) {
                    console.log(xhr.status);
                    console.log(xhr.responseText);
                    console.log(thrownError);
                }
            });
        }


        var houzez_half_map_listings = function(keyword, country, state, location, area, status, type, label, property_id, bedrooms, bathrooms, min_price, max_price, min_area, max_area, features, publish_date, search_no_posts, search_lat, search_long, search_radius, search_location, use_radius, currency, custom_fields_array, sort_half_map, current_page ) {
            var headerMapSecurity = $('#securityHouzezHeaderMap').val();
            var ajax_container = $('#houzez_ajax_container');
            var total_results = $('.map-module-half .tabs-title span');

            if( current_page != undefined ) {
                paged = current_page;
            }

            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: ajaxurl,
                data: {
                    'action': 'houzez_half_map_listings',
                    'keyword': keyword,
                    'location': location,
                    'country': country,
                    'state': state,
                    'area': area,
                    'status': status,
                    'type': type,
                    'label': label,
                    'property_id': property_id,
                    'bedrooms': bedrooms,
                    'bathrooms': bathrooms,
                    'min_price': min_price,
                    'max_price': max_price,
                    'currency': currency,
                    'min_area': min_area,
                    'max_area': max_area,
                    'features': features,
                    'publish_date': publish_date,
                    'search_lat': search_lat,
                    'search_long': search_long,
                    'use_radius': use_radius,
                    'search_location': search_location,
                    'search_radius': search_radius,
                    'sort_half_map': sort_half_map,
                    'custom_fields_values': custom_fields_array,
                    'security': headerMapSecurity,

                    'paged': paged,
                    'post_per_page': search_no_posts
                },
                beforeSend: function() {
                    $('#houzez-map-loading').show();
                    ajax_container.empty().append(''
                        +'<div class="list-loading">'
                        +'<div class="list-loading-bar"></div>'
                        +'<div class="list-loading-bar"></div>'
                        +'<div class="list-loading-bar"></div>'
                        +'<div class="list-loading-bar"></div>'
                        +'</div>'
                    );
                },
                success: function(data) { //alert(JSON.stringify(data.query)); return;

                    if ( data.query != '' ) {
                        $( 'input[name="search_args"]' ).val( data.query );
                    }
                    $('.map-notfound').remove();
                    remove_map_loader();

                    if(data.getProperties === true) { //alert(JSON.stringify(data.propHtml)); return false;

                        reloadMarkers();
                        houzezAddMarkers( data.properties, houzezMap );

                        houzez_map_bounds();
                        houzez_markerCluster();

                        ajax_container.empty().html(data.propHtml);
                        total_results.empty().html(data.total_results);
                        half_map_ajax_pagi();

                        if( !houzez_is_mobile ) {
                            houzez_infobox_trigger();
                        }
                        houzez_init_add_favorite();
                        houzez_init_remove_favorite();
                        $('[data-toggle="tooltip"]').tooltip();

                        if( has_compare == 1) {
                            houzez_compare_listing();
                        }

                        $('#houzez-map-loading').hide();

                    } else {
                        reloadMarkers();
                        $('#mapViewHalfListings').append('<div class="map-notfound">'+not_found+'</div>');
                        ajax_container.empty().html('<div class="map-notfound">'+not_found+'</div>');
                        total_results.empty().html(data.total_results);
                    }
                    return false;
                },
                error: function(xhr, ajaxOptions, thrownError) {
                    console.log(xhr.status);
                    console.log(xhr.responseText);
                    console.log(thrownError);
                }
            });
            return false;
        }

        var houzez_search_on_change = function (current_form, form_widget, current_page,  min_price_onchange_status, max_price_onchange_status, only_city, only_state, only_country ) {
            var country, currency, state, location, area, status, type, label, property_id, bedrooms, bathrooms, min_price, max_price, min_area, max_area, keyword, publish_date, search_lat, search_long, search_radius, search_location, use_radius, features;

            if( min_price_onchange_status != null && max_price_onchange_status != null ) {
                min_price = min_price_onchange_status;
                max_price = max_price_onchange_status;
            } else {
                if (form_widget.hasClass('widget') || advanced_search_price_slide != 0 ) {
                    min_price = current_form.find('input[name="min-price"]').val();
                    max_price = current_form.find('input[name="max-price"]').val();
                } else {
                    min_price = current_form.find('select[name="min-price"]:not(:disabled)').val();
                    max_price = current_form.find('select[name="max-price"]:not(:disabled)').val();
                }
            }

            state = current_form.find('select[name="state"]').val();
            location = current_form.find('select[name="location"]').val();
            if (location == '' || location == null || typeof location == 'undefined' ) {
                location = 'all';
            }

            if( only_city != 'yes' ) {
                area = current_form.find('select[name="area"]').val();
            }

            if( only_state == 'yes' ) {
                area = '';
                location = 'all';
            }

            if( only_country == 'yes' ) {
                state = '';
                area = '';
                location = 'all';
            }

            country   = current_form.find('select[name="country"]').val();
            currency   = current_form.find('select[name="currency"]').val();
            status    = current_form.find('select[name="status"]').val();
            type      = current_form.find('select[name="type"]').val();
            bedrooms  = current_form.find('select[name="bedrooms"]').val();
            bathrooms = current_form.find('select[name="bathrooms"]').val();
            label     = current_form.find('select[name="label"]').val();
            property_id     = current_form.find('input[name="property_id"]').val();
            min_area  = current_form.find('input[name="min-area"]').val();
            max_area  = current_form.find('input[name="max-area"]').val();
            keyword   = current_form.find('input[name="keyword"]').val();
            publish_date   = current_form.find('input[name="publish_date"]').val();
            features = current_form.find('.features-list input[type=checkbox]:checked').map(function(_, el) {
                return $(el).val();
            }).toArray();

            //Radius Search
            search_lat  = current_form.find('input[name="lat"]').val();
            search_long  = current_form.find('input[name="lng"]').val();
            search_location   = current_form.find('input[name="search_location"]').val();

            if(current_tempalte == 'template/property-listings-map.php') {
                search_radius = current_form.find('input[name="search_radius"]').val();
            } else {
                search_radius = current_form.find('select[name="radius"]').val();
            }

            if( $(current_form.find('input[name="use_radius"]')).is(':checked') ) {
                use_radius = 'on';
            } else {
                use_radius = 'off';
            }

            var custom_fields_array = [];
            /*===================== Custom Fileds ===============================*/
            $.each(search_custom_fields, function(key, value){
                        
                    //custom_fields_array.push(current_form.find('input[name="'+key+'"]').val());
                    custom_fields_array.push(current_form.find('.'+key).val());

            });
            
            /*================== End Custom Fields ================================*/

            if(googlemap_pin_cluster != 'no') {
                markerCluster.clearMarkers();
            }

            if(current_tempalte == 'template/property-listings-map.php') {
                var sort_half_map = $("#houzez_sort_half_map").val();
                houzez_half_map_listings(keyword, country, state, location, area, status, type, label, property_id, bedrooms, bathrooms, min_price, max_price, min_area, max_area, features, publish_date, search_no_posts, search_lat, search_long, search_radius, search_location, use_radius, currency, custom_fields_array, sort_half_map, current_page );
            } else {
                houzez_header_listing_map(keyword, country, state, location, area, status, type, label, property_id, bedrooms, bathrooms, min_price, max_price, min_area, max_area, features, publish_date, search_lat, search_long, search_radius, search_location, use_radius, currency, custom_fields_array );
            }
            return false;
        }


        var populate_state_dropdown = function(current_form, hload) {
            var country;
            country  = current_form.find('select[name="country"] option:selected').val();

            if( country != '' && country != undefined ) {

                if(hload != 'houzez_on_load') {
                    current_form.find('select[name="location"], select[name="area"], select[name="state"]').selectpicker('val', '');
                }
                current_form.find('select[name="state"] option').each(function () {
                    var stateCountry = $(this).data('parentcountry');

                    if (typeof stateCountry  !== "undefined") {
                        stateCountry = stateCountry.toUpperCase();
                    }

                    if( $(this).val() != '' ) {
                        $(this).css('display', 'none');
                    }
                    if (stateCountry == country) {
                        $(this).css('display', 'block');
                    }
                });
            } else if( hload == 'houzez_on_load' ) {
                
            } else {
                current_form.find('select[name="location"], select[name="area"], select[name="state"]').selectpicker('val', '');
                current_form.find('select[name="state"] option').each(function () {
                    $(this).css('display', 'block');
                });
                current_form.find('select[name="area"] option').each(function () {
                    $(this).css('display', 'block');
                });
            }
            current_form.find('select[name="location"], select[name="area"], select[name="state"]').selectpicker('refresh');
        }

        var populate_city_dropdown = function(current_form, hload) {
            var state;
            state  = current_form.find('select[name="state"] option:selected').val();

            if( state != '' && state != undefined ) { 

                if(hload != 'houzez_on_load') {
                    current_form.find('select[name="location"], select[name="area"]').selectpicker('val', '');
                }
                current_form.find('select[name="location"] option').each(function () {
                    var cityState = $(this).data('parentstate');

                    if( $(this).val() != '' ) {
                        $(this).css('display', 'none');
                    }
                    if (cityState == state) {
                        $(this).css('display', 'block');
                    }
                });
            } else if(hload == 'houzez_on_load') { 
                
            } else { 
                current_form.find('select[name="location"], select[name="area"]').selectpicker('val', '');
                current_form.find('select[name="location"] option').each(function () {
                    $(this).css('display', 'block');
                });
                current_form.find('select[name="area"] option').each(function () {
                    $(this).css('display', 'block');
                });
            }
            current_form.find('select[name="location"], select[name="area"]').selectpicker('refresh');
        }

        var populate_area_dropdown = function(current_form, hload) {
            var city;
            city  = current_form.find('select[name="location"] option:selected').val();
           
            if( city != '' && city != undefined ) { 
                
                if(hload != 'houzez_on_load') {
                    current_form.find('select[name="area"]').selectpicker('val', '');
                }
                current_form.find('select[name="area"] option').each(function () {
                    var areaCity = $(this).data('parentcity');
                    if( $(this).val() != '' ) {
                        $(this).css('display', 'none');
                    }
                    if (areaCity == city) {
                        $(this).css('display', 'block');
                    }
                });
            } else { 
                current_form.find('select[name="area"]').selectpicker('val', '');
                current_form.find('select[name="area"] option').each(function () {
                    $(this).css('display', 'block');
                });
            }
            current_form.find('select[name="area"]').selectpicker('refresh');
        }

        var select_areas_on_load = $('.advance-search-header, .map-module-half, .widget_houzez_advanced_search').find('form');
        if (select_areas_on_load.length > 0) {
            populate_area_dropdown(select_areas_on_load, 'houzez_on_load');
            populate_city_dropdown(select_areas_on_load, 'houzez_on_load');
            populate_state_dropdown(select_areas_on_load, 'houzez_on_load');
        }

        if($("#houzez-listing-map").length > 0 || $('#mapViewHalfListings').length > 0 ) {

            /*===================== Custom Fileds ===============================*/
            $.each(search_custom_fields, function(key, value) {
                
                $('.'+key).on('change', function() {
                    var current_form = $(this).parents('form');
                    var form_widget = $(this).parents('.widget_houzez_advanced_search');
                    houzez_search_on_change(current_form, form_widget, current_page);
                });
                console.log(key+' : '+value);  
            });
            /*================== End Custom Fields ================================*/

            var current_page = 0;
            $('select[name="sort_half_map"], select[name="currency"], select[name="area"], select[name="label"], select[name="bedrooms"], select[name="bathrooms"], select[name="min-price"], select[name="max-price"], input[name="min-price"], input[name="max-price"], input[name="min-area"], input[name="max-area"], select[name="type"], input[name="property_id"]').on('change', function() {
                var current_form = $(this).parents('form');
                var form_widget = $(this).parents('.widget_houzez_advanced_search');
                houzez_search_on_change(current_form, form_widget, current_page );
            });

            $('input[name="keyword"]').on('change', function() {
                var current_form = $(this).parents('form');
                var form_widget = $(this).parents('.widget_houzez_advanced_search');

                setTimeout(function(){
                    houzez_search_on_change(current_form, form_widget, current_page );
                },100);
            });

            $("input.search_location").geocomplete({
                details: "form",
                country: houzez_geocomplete_country,
                geocodeAfterResult: true
            }).bind("geocode:result", function(event, result){
                var current_form = $(this).parents('form');
                var form_widget = $(this).parents('.widget_houzez_advanced_search');
                houzez_search_on_change(current_form, form_widget, current_page);
                console.log(result);
            });

            $( '#half_map_update').on('click', function(e) {
                e.preventDefault();
                var current_form = $(this).parents('form');
                var form_widget = $(this).parents('.widget_houzez_advanced_search');
                houzez_search_on_change(current_form, form_widget, current_page);
            });

            $('select[name="radius"]').on('change', function() {
                var current_form = $(this).parents('form');
                var form_widget = $(this).parents('.widget_houzez_advanced_search');
                houzez_search_on_change(current_form, form_widget, current_page);
            });

            $('select[name="country"]').on('change', function() {
                var current_form = $(this).parents('form');
                var form_widget = $(this).parents('.widget_houzez_advanced_search');
                var only_country = 'yes';
                houzez_search_on_change(current_form, form_widget, current_page, '', '', '', '', only_country);
                populate_state_dropdown(current_form);
            })

            $('select[name="state"]').on('change', function() {
                var current_form = $(this).parents('form');
                var form_widget = $(this).parents('.widget_houzez_advanced_search');
                var only_state = 'yes';
                houzez_search_on_change(current_form, form_widget, current_page, '', '', '', only_state);
                populate_city_dropdown(current_form);
            });

            $('select[name="location"]').on('change', function() {
                var current_form = $(this).parents('form');
                var form_widget = $(this).parents('.widget_houzez_advanced_search');
                var only_city = 'yes';
                houzez_search_on_change(current_form, form_widget, current_page, '', '', only_city );
                populate_area_dropdown(current_form);
            });

            $('input[name="feature[]"]').on('change', function() {
                var current_form = $(this).parents('form');
                var form_widget = $(this).parents('.widget_houzez_advanced_search');
                houzez_search_on_change(current_form, form_widget, current_page);
            })

            $(".search-date").on("change", function(e) {
                //alert($(this).val());
                var current_form = $(this).parents('form');
                var form_widget = $(this).parents('.widget_houzez_advanced_search');
                houzez_search_on_change(current_form, form_widget, current_page);
            });

            $('select[name="status"]').on('change', function() {
                var current_form = $(this).parents('form');
                var form_widget = $(this).parents('.widget_houzez_advanced_search');

                var search_status = $(this).val();
                if( search_status == rent_status_for_price_range ) {
                    if(advanced_search_price_slide != 0) {
                        houzez_search_on_change(current_form, form_widget, current_page, advanced_search_price_range_min_rent, advanced_search_price_range_max_rent );
                    } else {
                        houzez_search_on_change(current_form, form_widget, current_page);
                    }
                } else {
                    if(advanced_search_price_slide != 0) {
                        houzez_search_on_change(current_form, form_widget, current_page, advanced_search_price_range_min, advanced_search_price_range_max );
                    } else {
                        houzez_search_on_change(current_form, form_widget, current_page );
                    }
                }

            })

            if(current_tempalte == 'template/property-listings-map.php') {

                var sort_by = HOUZEZ_ajaxcalls_vars.sort_by;
                var state = HOUZEZ_ajaxcalls_vars.search_state;
                var country = HOUZEZ_ajaxcalls_vars.search_country;
                var keyword = HOUZEZ_ajaxcalls_vars.search_keyword;
                var location = HOUZEZ_ajaxcalls_vars.search_city;
                var features = HOUZEZ_ajaxcalls_vars.search_feature;
                var area = HOUZEZ_ajaxcalls_vars.search_area;
                var status = HOUZEZ_ajaxcalls_vars.search_status;
                var type = HOUZEZ_ajaxcalls_vars.search_type;
                var label = HOUZEZ_ajaxcalls_vars.search_label;
                var property_id = HOUZEZ_ajaxcalls_vars.search_property_id;
                var bedrooms = HOUZEZ_ajaxcalls_vars.search_bedrooms;
                var bathrooms = HOUZEZ_ajaxcalls_vars.search_bathrooms;
                var min_price = HOUZEZ_ajaxcalls_vars.search_min_price;
                var max_price = HOUZEZ_ajaxcalls_vars.search_max_price;
                var currency = HOUZEZ_ajaxcalls_vars.search_currency;
                var min_area = HOUZEZ_ajaxcalls_vars.search_min_area;
                var max_area = HOUZEZ_ajaxcalls_vars.search_max_area;
                var publish_date = HOUZEZ_ajaxcalls_vars.search_publish_date;
                var search_no_posts = HOUZEZ_ajaxcalls_vars.search_no_posts;

                var search_lat = HOUZEZ_ajaxcalls_vars.search_lat;
                var search_long = HOUZEZ_ajaxcalls_vars.search_long;
                var search_radius = HOUZEZ_ajaxcalls_vars.search_radius;
                var search_location = HOUZEZ_ajaxcalls_vars.search_location;
                var use_radius = HOUZEZ_ajaxcalls_vars.use_radius;

                var custom_fields_array = [];
                /*===================== Custom Fileds ===============================*/
                $.each(search_custom_fields, function(key, value){
                        
                    custom_fields_array.push(value);

                });

                houzez_half_map_listings(keyword, country, state, location, area, status, type, label, property_id, bedrooms, bathrooms, min_price, max_price, min_area, max_area, features, publish_date, search_no_posts, search_lat, search_long, search_radius, search_location, use_radius, currency, custom_fields_array, sort_by );

            } else {
                houzez_header_listing_map();
            }
        } else {
            $('select[name="country"]').on('change', function() {
                var current_form = $(this).parents('form');
                populate_state_dropdown(current_form);
            })

            $('select[name="location"]').on('change', function() {
                var current_form = $(this).parents('form');
                populate_area_dropdown(current_form);
            });

            $('select[name="state"]').on('change', function() {
                var current_form = $(this).parents('form');
                populate_city_dropdown(current_form);
            });

            if( $("input.search_location").length > 0 ) {
                $("input.search_location").geocomplete({
                    details: "form",
                    country: houzez_geocomplete_country,
                    geocodeAfterResult: true
                });
            }
        }


        /* ------------------------------------------------------------------------ */
        /*  RANGE SLIDER
         /* ------------------------------------------------------------------------ */
        var price_range_main_search = function( min_price, max_price ) {
            $(".price-range-advanced").slider({
                range: true,
                min: min_price,
                max: max_price,
                values: [min_price, max_price],
                slide: function (event, ui) {
                    if( currency_position == 'after' ) {
                        var min_price_range = addCommas(ui.values[0]) + currency_symb;
                        var max_price_range = addCommas(ui.values[1]) + currency_symb;
                    } else {
                        var min_price_range = currency_symb + addCommas(ui.values[0]);
                        var max_price_range = currency_symb + addCommas(ui.values[1]);
                    }
                    $(".min-price-range-hidden").val( min_price_range );
                    $(".max-price-range-hidden").val( max_price_range );

                    $(".min-price-range").text( min_price_range );
                    $(".max-price-range").text( max_price_range );
                },
                stop: function( event, ui ) {

                    if($("#houzez-listing-map").length > 0 || $('#mapViewHalfListings').length > 0 ) {
                        var current_page = 0;
                        var current_form = $(this).parents('form');
                        var form_widget = $(this).parents('form');
                        houzez_search_on_change(current_form, form_widget, current_page);
                    }
                }
            });

            if( currency_position == 'after' ) {
                var min_price_range = addCommas($(".price-range-advanced").slider("values", 0)) + currency_symb;
                var max_price_range = addCommas($(".price-range-advanced").slider("values", 1)) + currency_symb;
            } else {
                var min_price_range = currency_symb + addCommas($(".price-range-advanced").slider("values", 0));
                var max_price_range = currency_symb + addCommas($(".price-range-advanced").slider("values", 1));
            }
            $(".min-price-range-hidden").val(min_price_range);
            $(".max-price-range-hidden").val(max_price_range);

            $(".min-price-range").text(min_price_range);
            $(".max-price-range").text(max_price_range);
        }

        if($( ".price-range-advanced").length > 0 ) {
            price_range_main_search( advanced_search_price_range_min, advanced_search_price_range_max );
        }
        $('.houzez-adv-price-range select[name="status"]').on('change', function(){
            var search_status = $(this).val();
            if( search_status == rent_status_for_price_range ) {
                price_range_main_search(advanced_search_price_range_min_rent, advanced_search_price_range_max_rent);
            } else {
                price_range_main_search( advanced_search_price_range_min, advanced_search_price_range_max );
            }
        });

        /* On page load ( as on search page ) */
        var selected_status_adv_search = $('.houzez-adv-price-range select[name="status"]').val();
        if( selected_status_adv_search == rent_status_for_price_range ){
            price_range_main_search(advanced_search_price_range_min_rent, advanced_search_price_range_max_rent);
        }

        var price_range_widget = function(min_price, max_price) {
            $("#slider-price").slider({
                range: true,
                min: min_price,
                max: max_price,
                values: [min_price, max_price],
                slide: function (event, ui) {

                    if( currency_position == 'after' ) {
                        $("#min-price").val( addCommas(ui.values[0]) + currency_symb );
                        $("#max-price").val( addCommas(ui.values[1]) + currency_symb );
                    } else {
                        $("#min-price").val( currency_symb + addCommas(ui.values[0]));
                        $("#max-price").val( currency_symb + addCommas(ui.values[1]));
                    }
                },
                stop: function( event, ui ) {

                    if($("#houzez-listing-map").length > 0 ) {
                        var current_form = $(this).parents('form');
                        var form_widget = $(this).parents('.widget_houzez_advanced_search');
                        houzez_search_on_change(current_form, form_widget);
                    }
                }
            });


            if( currency_position == 'after' ) {
                $("#min-price").val(addCommas($("#slider-price").slider("values", 0)) + currency_symb);
                $("#max-price").val(addCommas($("#slider-price").slider("values", 1)) + currency_symb);
            } else {
                $("#min-price").val(currency_symb + addCommas($("#slider-price").slider("values", 0)));
                $("#max-price").val(currency_symb + addCommas($("#slider-price").slider("values", 1)));
            }
        }

        if($( "#slider-price").length >0) {
            price_range_widget( advanced_search_price_range_min, advanced_search_price_range_max );
        }

        $('#widget_status').on('change', function(){
            var search_status = $(this).val();
            if( search_status == rent_status_for_price_range ) {
                price_range_widget(advanced_search_price_range_min_rent, advanced_search_price_range_max_rent);
            } else {
                price_range_widget( advanced_search_price_range_min, advanced_search_price_range_max );
            }
        });

        /* On page load ( as on search page ) */
        var selected_status_widget_search = $('#widget_status').val();
        if( selected_status_widget_search == rent_status_for_price_range ){
            price_range_widget(advanced_search_price_range_min_rent, advanced_search_price_range_max_rent);
        }


        if($( "#slider-size").length >0) {
            $("#slider-size").slider({
                range: true,
                min: advanced_search_widget_min_area,
                max: advanced_search_widget_max_area,
                values: [advanced_search_widget_min_area, advanced_search_widget_max_area],
                slide: function (event, ui) {
                    $("#min-size").val(ui.values[0] +' '+measurement_unit);
                    $("#max-size").val(ui.values[1] +' '+measurement_unit);
                },
                stop: function( event, ui ) {

                    if($("#houzez-listing-map").length > 0 ) {
                        var current_page = 0;
                        var current_form = $(this).parents('form');
                        var form_widget = $(this).parents('.widget_houzez_advanced_search');
                        houzez_search_on_change(current_form, form_widget, current_page );
                    }
                }
            });
            $("#min-size").val($("#slider-size").slider("values", 0) +' '+measurement_unit);
            $("#max-size").val($("#slider-size").slider("values", 1) +' '+measurement_unit);
        }

        var radius_search_slider = function(default_radius) {
            $("#radius-range-slider").slider(
                {
                    value: default_radius,
                    min: 0,
                    max: 100,
                    step: 1,
                    slide: function (event, ui) {
                        $("#radius-range-text").html(ui.value);
                        $("#radius-range-value").val(ui.value);
                    },
                    stop: function( event, ui ) {

                        if($("#houzez-listing-map").length > 0 || $('#mapViewHalfListings').length > 0 ) {
                            var current_page = 0;
                            var current_form = $(this).parents('form');
                            var form_widget = $(this).parents('form');
                            houzez_search_on_change(current_form, form_widget, current_page);
                        }
                    }
                }
            );

            $("#radius-range-text").html($('#radius-range-slider').slider('value'));
            $("#radius-range-value").val($('#radius-range-slider').slider('value'));
        }

        if($( "#radius-range-slider").length >0) {
            radius_search_slider(houzez_default_radius);
        }

        var houzez_infobox_trigger = function() {
            $('.infobox_trigger').each(function(i) {
                $(this).on('mouseenter', function() {
                    if(houzezMap) {
                        if( houzezMap.getZoom() < 15 ){
                            houzezMap.setZoom(15);
                        }
                        google.maps.event.trigger(markers[i], 'click');
                        
                    }
                });
                $(this).on('mouseleave', function() {
                    infobox.open(null,null);
                    
                });
            });
            return false;
        }


        /*--------------------------------------------------------------------------
         *  Currency Switcher
         * -------------------------------------------------------------------------*/
        var currencySwitcherList = $('#houzez-currency-switcher-list');
        if( currencySwitcherList.length > 0 ) {

            $('#houzez-currency-switcher-list > li').click(function(e) {
                e.stopPropagation();
                currencySwitcherList.slideUp( 200 );

                var selectedCurrencyCode = $(this).data( 'currency-code' );

                if ( selectedCurrencyCode ) {

                    $('#houzez-selected-currency span').html( selectedCurrencyCode );
                    $('#houzez-switch-to-currency').val( selectedCurrencyCode );
                    var security = $('#currency_switch_security').val();
                    var houzez_switch_to_currency = $('#houzez-switch-to-currency').val();
                    fave_processing_modal('<i class="'+process_loader_spinner+'"></i> '+currency_updating_msg);

                    $.ajax({
                        url: ajaxurl,
                        dataType: 'JSON',
                        method: 'POST',
                        data: {
                            'action' : 'houzez_currency_converter',
                            'currency_converter' : houzez_switch_to_currency,
                            'security' : security
                        },
                        success: function (res) {
                            if( res.success ) {
                                window.location.reload();
                            } else {
                                console.log( res );
                            }
                        },
                        error: function (xhr, status, error) {
                            var err = eval("(" + xhr.responseText + ")");
                            console.log(err.Message);
                        }
                    });

                }

            });

            $('#houzez-selected-currency').click(function(e){
                currencySwitcherList.slideToggle( 200 );
                e.stopPropagation();
            });

            $('html').click(function() {
                currencySwitcherList.slideUp( 100 );
            });
        }


        /*--------------------------------------------------------------------------
         *  Area Switcher
         * -------------------------------------------------------------------------*/
        var areaSwitcherList = $('#houzez-area-switcher-list');
        if( areaSwitcherList.length > 0 ) {

            $('#houzez-area-switcher-list > li').click(function(e) {
                e.stopPropagation();
                areaSwitcherList.slideUp( 200 );

                var selectedAreaCode = $(this).data( 'area-code' );
                var houzez_switch_area_text = $('#houzez_switch_area_text').val();

                if ( selectedAreaCode ) {

                    $('#houzez-selected-area span').html( houzez_switch_area_text );
                    $('#houzez-switch-to-area').val( selectedAreaCode );
                    var security = $('#area_switch_security').val();
                    var houzez_switch_to_area = $('#houzez-switch-to-area').val();
                    fave_processing_modal('<i class="'+process_loader_spinner+'"></i> '+measurement_updating_msg);

                    $.ajax({
                        url: ajaxurl,
                        dataType: 'JSON',
                        method: 'POST',
                        data: {
                            'action' : 'houzez_switch_area',
                            'switch_to_area' : houzez_switch_to_area,
                            'security' : security
                        },
                        success: function (res) {
                            if( res.success ) {
                                window.location.reload();
                            } else {
                                console.log( res );
                            }
                        },
                        error: function (xhr, status, error) {
                            var err = eval("(" + xhr.responseText + ")");
                            console.log(err.Message);
                        }
                    });

                }

            });

            $('#houzez-selected-area').click(function(e){
                areaSwitcherList.slideToggle( 200 );
                e.stopPropagation();
            });

            $('html').click(function() {
                areaSwitcherList.slideUp( 100 );
            });
        }

        /*--------------------------------------------------------------------------
         *  AutoComplete Search
         * -------------------------------------------------------------------------*/
        if( keyword_autocomplete != 0 ) {
            var houzezAutoComplete = function () {

                var ajaxCount = 0;
                var auto_complete_container = $('.auto-complete');
                var lastLenght = 0;

                $('input[name="keyword"]').keyup(function(){

                    var $this = $( this );
                    var $form = $this.parents( 'form');
                    var auto_complete_container = $form.find( '.auto-complete' );
                    var keyword = $( this ).val();
                    keyword = $.trim( keyword );
                    var currentLenght = keyword.length;

                    if ( currentLenght >= 2 && currentLenght != lastLenght ) {

                        lastLenght = currentLenght;
                        auto_complete_container.fadeIn();

                        $.ajax({
                            type: 'POST',
                            url: ajaxurl,
                            data: {
                                'action': 'houzez_get_auto_complete_search',
                                'key': keyword,
                                //'nonce' : HOUZEZ_ajaxcalls_vars.houzez_autoComplete_nonce
                            },
                            beforeSend: function( ) {
                                ajaxCount++;
                                if ( ajaxCount == 1 ) {
                                    auto_complete_container.html('<div class="result"><p><i class="fa fa-spinner fa-spin fa-fw"></i> '+autosearch_text+ '</p></div>');
                                }
                            },
                            success: function(data) {
                                ajaxCount--;
                                if ( ajaxCount == 0 ) {
                                    auto_complete_container.show();
                                    if( data != '' ) {
                                        auto_complete_container.empty().html(data).bind();
                                    }
                                }
                            },
                            error: function(errorThrown) {
                                ajaxCount--;
                                if ( ajaxCount == 0 ) {
                                    auto_complete_container.html('<div class="result"><p><i class="fa fa-spinner fa-spin fa-fw"></i> '+autosearch_text+ ' </p></div>');
                                }
                            }
                        });

                    } else {
                        if ( currentLenght != lastLenght ) {
                            auto_complete_container.fadeOut();
                        }
                    }

                });
                auto_complete_container.on( 'click', 'li', function (){
                    $('input[name="keyword"]').val( $( this ).data( 'text' ) );
                    auto_complete_container.fadeOut();
                }).bind();
            }
            houzezAutoComplete();
        }


        /*---------------------------------------------------------------------------
         *
         * Messaging system
         * -------------------------------------------------------------------------*/

        /*
         * Property Thread Form
         * -----------------------------*/
        $( '.start_thread_form').click(function(e) {

            e.preventDefault();

            var $this = $(this);
            var $form = $this.parents( 'form' );
            var $result = $form.find('.form_messages');

            $.ajax({
                url: ajaxurl,
                data: $form.serialize(),
                method: $form.attr('method'),
                dataType: "JSON",

                beforeSend: function( ) {
                    $this.children('i').remove();
                    $this.prepend('<i class="fa-left '+process_loader_spinner+'"></i>');
                },
                success: function(response) {
                    if( response.success ) {
                        $result.empty().append(response.msg);
                        $form.find('input').val('');
                        $form.find('textarea').val('');
                    } else {
                        $result.empty().append(response.msg);
                    }
                },
                error: function(xhr, status, error) {
                    var err = eval("(" + xhr.responseText + ")");
                    console.log(err.Message);
                },
                complete: function(){
                    $this.children('i').removeClass(process_loader_spinner);
                    $this.children('i').addClass(success_icon);
                }
            });

        });


        /*
         * Property Message Notifications
         * -----------------------------*/
        var houzez_message_notifications = function () {

            $.ajax({
                url: ajaxurl,
                data: {
                    action : 'houzez_chcek_messages_notifications'
                },
                method: "POST",
                dataType: "JSON",

                beforeSend: function( ) {
                    // code here...
                },
                success: function(response) {
                    if( response.success ) {
                        if ( response.notification ) {
                            $( '.user-alert' ).show();
                            $( '.msg-alert' ).show();
                        } else {
                            $( '.user-alert' ).hide();
                            $( '.msg-alert' ).hide();
                        }
                    }
                }
            });

        };

        $( document ).ready(function() {
            houzez_message_notifications();
            setInterval(function() { houzez_message_notifications(); }, 180000);
        });


        /*
         * Property Thread Message Form
         * -----------------------------*/
        $( '.start_thread_message_form').click(function(e) {

            e.preventDefault();

            var $this = $(this);
            var $form = $this.parents( 'form' );
            var $result = $form.find('.form_messages');

            $.ajax({
                url: ajaxurl,
                data: $form.serialize(),
                method: $form.attr('method'),
                dataType: "JSON",

                beforeSend: function( ) {
                    $this.children('i').remove();
                    $this.prepend('<i class="fa-left '+process_loader_spinner+'"></i>');
                },
                success: function( response ) {
                    window.location.replace( response.url );
                },
                complete: function(){
                    $this.children('i').removeClass(process_loader_spinner);
                    $this.children('i').addClass(success_icon);
                }
            });

        });

        var agency_listings_ajax_pagi = function() {
            $('body.single-houzez_agency ul.pagination li a').click(function(e){
                e.preventDefault();
                var current_page = $(this).data('houzepagi');
                var agency_id_pagi = $('#agency_id_pagi').val();

                var ajax_container = $('#houzez_ajax_container');

                $.ajax({
                    url: ajaxurl,
                    method: 'POST',
                    data: {
                        'action': 'houzez_ajax_agency_filter',
                        'paged': current_page,
                        'agency_id': agency_id_pagi
                    },
                    beforeSend: function( ) {
                        ajax_container.empty().append(''
                            +'<div class="list-loading">'
                            +'<div class="list-loading-bar"></div>'
                            +'<div class="list-loading-bar"></div>'
                            +'<div class="list-loading-bar"></div>'
                            +'<div class="list-loading-bar"></div>'
                            +'</div>'
                        );
                    },
                    success: function( response ) {
                        ajax_container.empty().html(response);
                        agency_listings_ajax_pagi();
                    },
                    complete: function(){
                    },
                    error: function (xhr, status, error) {
                        var err = eval("(" + xhr.responseText + ")");
                        console.log(err.Message);
                    }
                });

            })
            return false;
        }

        if($('body.single-houzez_agency').length > 0 ) {
            agency_listings_ajax_pagi();
        }


        /*--------------------------------------------------------------------------
         *  Delete property
         * -------------------------------------------------------------------------*/
        $( 'a.delete-property' ).on( 'click', function (){
            var r = confirm(delete_property_confirmation);
            if (r == true) {

                var $this = $( this );
                var propID = $this.data('id');
                var propNonce = $this.data('nonce');

                fave_processing_modal( delete_property_loading );

                $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    url: ajaxurl,
                    data: {
                        'action': 'houzez_delete_property',
                        'prop_id': propID,
                        'security': propNonce
                    },
                    success: function(data) {
                        if ( data.success == true ) {
                            window.location.reload();
                        } else {
                            jQuery('#fave_modal').modal('hide');
                            alert( data.reason );
                        }
                    },
                    error: function(errorThrown) {

                    }
                });

            }
        });

        /*--------------------------------------------------------------------------
         *  Single Property
         * -------------------------------------------------------------------------*/
        if( is_singular_property == "yes" ) {
            var houzezSlidesToShow = 0;
            if( property_detail_top == 'v3' ) {
                houzezSlidesToShow = '8';
            } else {
                houzezSlidesToShow = '11';
            }

            var gallery_autoplay = HOUZEZ_ajaxcalls_vars.gallery_autoplay;

            if( gallery_autoplay === '1' ) {
                gallery_autoplay = true;
            } else {
                gallery_autoplay = false;
            }

            var detail_slider = $('.detail-slider');
            var detail_slider_nav = $('.detail-slider-nav');
            var slidesPerPage = 4; //globaly define number of elements per page
            var syncedSecondary = true;
            var slider_speed = 1200;

            var houzez_detail_slider_main_settings = function () {
                return {
                    stopOnHover:true,
                    items: 1,
                    rtl: houzez_rtl,
                    margin: 0,
                    nav: true,
                    dots: false,
                    loop:false,
                    navText : ["<i class='fa fa-angle-left'></i>","<i class='fa fa-angle-right'></i>"],
                    autoplay: gallery_autoplay,
                    autoplayHoverPause:true,
                    smartSpeed: slider_speed,
                    autoplaySpeed: slider_speed,
                    responsiveRefreshRate : 200
                    //rewindNav: true
                }
            };
            var houzez_detail_slider_nav_settings = function () {
                return {
                    margin: 1,
                    //items: houzezSlidesToShow,
                    center: false,
                    nav: false,
                    rtl: houzez_rtl,
                    dots: false,
                    loop:false,
                    navText : ["<i class='fa fa-angle-left'></i>","<i class='fa fa-angle-right'></i>"],
                    autoplay: false,
                    smartSpeed: 800,
                    autoplaySpeed: 800,
                    responsiveRefreshRate : 10,
                    responsive: {

                        0: {
                            items: 5
                        },
                        767: {
                            items: 7
                        },
                        992: {
                            items: 9
                        },
                        1199: {
                            items: houzezSlidesToShow
                        }

                    }
                }
            };

            var property_detail_slideshow = function () {

                detail_slider.owlCarousel(houzez_detail_slider_main_settings()).on('changed.owl.carousel', syncPosition);

                detail_slider_nav.on('initialized.owl.carousel', function () {
                    detail_slider_nav.find(".owl-item").eq(0).addClass("current");
                }).owlCarousel(houzez_detail_slider_nav_settings())/*.on('changed.owl.carousel', syncPosition2)*/;

                function syncPosition(el) {
                    //if you set loop to false, you have to restore this next line
                    var current = el.item.index;

                    detail_slider_nav.find(".owl-item").removeClass("current").eq(current).addClass("current");
                    var onscreen = detail_slider_nav.find('.owl-item.active').length - 1;
                    var start = detail_slider_nav.find('.owl-item.active').first().index();
                    var end = detail_slider_nav.find('.owl-item.active').last().index();

                    if (current > end) {
                        detail_slider_nav.data('owl.carousel').to(current, 100, true);
                    }
                    if (current < start) {
                        detail_slider_nav.data('owl.carousel').to(current - onscreen, 100, true);
                    }
                }

                function syncPosition2(el) {
                    if(syncedSecondary) {
                        var number = el.item.index;
                        detail_slider.data('owl.carousel').to(number, 100, true);
                    }
                }

                detail_slider_nav.on("click", ".owl-item", function(e){
                    e.preventDefault();
                    var number = $(this).index();
                    detail_slider.data('owl.carousel').to(number, slider_speed, true);
                });

            };
            property_detail_slideshow();
        }

        if( is_singular_property == 'yes') {

            $('#property-rating').rating({
                step: 0.5,
                showClear: false
                //starCaptions: {1: 'Very Poor', 2: 'Poor', 3: 'Ok', 4: 'Good', 5: 'Very Good'},
                //starCaptionClasses: {1: 'text-danger', 2: 'text-warning', 3: 'text-info', 4: 'text-primary', 5: 'text-success'}

            });

            //     rating-display-only
            $('.rating-display-only').rating({disabled: true, showClear: false});

            /*--------------------------------------------------------------------------
             *  Property Rating
             * -------------------------------------------------------------------------*/
            $( '.property_rating').click(function(e) {

                e.preventDefault();

                var $this = $(this);
                var $form = $this.parents( 'form' );
                var $result = $form.find('.form_messages');

                $.ajax({
                    url: ajaxurl,
                    data: $form.serialize(),
                    method: $form.attr('method'),
                    dataType: "JSON",

                    beforeSend: function( ) {
                        $this.children('i').remove();
                        $this.prepend('<i class="fa-left '+process_loader_spinner+'"></i>');
                    },
                    success: function( response ) {
                        window.location.reload();
                    },
                    complete: function(){
                        $this.children('i').removeClass(process_loader_spinner);
                        $this.children('i').addClass(success_icon);
                    }
                });

            });

            // tabs Height
            var tabsHeight = function() {
                var gallery_tab = $(".detail-media #gallery");
                var tab_content = $(".detail-media .tab-content");
                var map_tab = $("#singlePropertyMap,#street-map");

                var map_tab_height = map_tab.outerHeight();
                var gallery_tab_height = gallery_tab.outerHeight();
                var tab_content_height = tab_content.outerHeight();

                if(gallery_tab.is(':visible')){
                    map_tab.css('min-height',gallery_tab_height);
                    //alert(gallery_tab_height);
                }else{
                    map_tab.css('min-height',map_tab_height);
                    //alert($(".detail-media #gallery").outerHeight());

                }
            };

            $(window).on('load',function(){
                tabsHeight();
            });
            $(window).on('resize',function(){
                //alert(jQuery("#gallery").height());
                tabsHeight();
            }); // End tabs height

            var map = null;
            var streetCount = 0;
            var panorama = null;
            var fenway = new google.maps.LatLng(prop_lat, prop_lng);
            var mapOptions = {
                center: fenway,
                zoom: 15,
                //scrollwheel: false,
                gestureHandling: 'cooperative',
                styles: google_map_style,
                //mapTypeId: 'satellite'
            };
            var panoramaOptions = {
                position: fenway,
                pov: {
                    heading: 34,
                    pitch: 10
                }
            };

            // Map and street view
            if( property_map != 0 ) {
                

                var initialize = function () {
                    map = new google.maps.Map(document.getElementById('singlePropertyMap'), mapOptions);
                    if( houzez_is_mobile ) {
                        map.setOptions({
                            gestureHandling: 'cooperative',
                        });
                    } else {
                        map.setOptions({
                            scrollwheel: false,
                        });
                    }

                    var propsSecurity = $('#securityHouzezMap').val();

                    $.ajax({
                        type: 'POST',
                        dataType: 'json',
                        url: ajaxurl,
                        data: {
                            'action': 'houzez_get_single_property',
                            'prop_id': $('#prop_id').val(),
                            'security': propsSecurity
                        },
                        success: function(data) {

                            if(data.getprops === true) {
                                houzezAddMarkers(data.props, map);
                                houzezSetPOIControls(map, map.getCenter());
                            }
                        },
                        error: function(errorThrown) {

                        }
                    });

                };
                $('a[href="#gallery"]').on('shown.bs.tab', function () {
                    setTimeout(tabsHeight,500);
                });
                $('a[href="#singlePropertyMap"]').on('shown.bs.tab', function () {
                    google.maps.event.trigger(map, "resize");
                    map.setCenter(fenway);
                });
                $('a[href="#street-map"]').on('shown.bs.tab', function () {

                    streetCount += 1;
                    if(streetCount <= 1) {
                        panorama = new google.maps.StreetViewPanorama(document.getElementById('street-map'), panoramaOptions);
                    }

                });
                
                if($('#singlePropertyMap').length > 0 ) {
                    google.maps.event.addDomListener(window, 'load', initialize);
                }


                //Google Map section
                var MapSection = function () {
                    map = new google.maps.Map(document.getElementById('singlePropertyMapSection'), mapOptions);
                    if( houzez_is_mobile ) {
                        map.setOptions({
                            gestureHandling: 'cooperative',
                        });
                    } else {
                        map.setOptions({
                            scrollwheel: false,
                        });
                    }

                    var propsSecurity = $('#securityHouzezMap').val();

                    $.ajax({
                        type: 'POST',
                        dataType: 'json',
                        url: ajaxurl,
                        data: {
                            'action': 'houzez_get_single_property',
                            'prop_id': $('#prop_id').val(),
                            'security': propsSecurity
                        },
                        success: function(data) {
                            if( google_map_style !== '' ) {
                                var styles =  google_map_style;
                                map.setOptions({styles: styles});
                            }

                            if(data.getprops === true) {
                                houzezAddMarkerSimple(data.props, map);
                                houzezSetPOIControls(map, map.getCenter());
                            }
                        },
                        error: function(errorThrown) {

                        }
                    });

                };
                if($('#singlePropertyMapSection').length > 0 ) {
                    google.maps.event.addDomListener(window, 'load', MapSection);
                }


            }// End map and street


            //
            $(".houzez-gallery-prop-v2:first a[rel^='prettyPhoto']").prettyPhoto({
                animation_speed:'normal',
                slideshow:5000,
                autoplay_slideshow: false,
                allow_resize: true,
                keyboard_shortcuts: true,
                theme: 'pp_default' /* pp_default / light_rounded / dark_rounded / light_square / dark_square / facebook */
            });

        }


    }// typeof HOUZEZ_ajaxcalls_vars

}); // end document ready