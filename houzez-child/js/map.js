$ = jQuery;

var map;
var poly;

function DrawControl(controlDiv, map, locations) {
    var controlUI = document.createElement('div');
    controlUI.setAttribute("id", "draw");
    controlUI.style.backgroundColor = '#fff';
    controlUI.style.border = '2px solid #fff';
    controlUI.style.borderRadius = '3px';
    controlUI.style.boxShadow = '0 2px 6px rgba(0,0,0,.3)';
    controlUI.style.cursor = 'pointer';
    controlUI.style.marginBottom = '10px';
    controlUI.style.marginRight = '10px';
    controlUI.style.textAlign = 'center';
    controlDiv.appendChild(controlUI);

    var controlText = document.createElement('div');
    controlText.style.color = 'rgb(25,25,25)';
    controlText.style.fontFamily = 'Roboto,Arial,sans-serif';
    controlText.style.fontSize = '16px';
    controlText.style.lineHeight = '38px';
    controlText.style.paddingLeft = '10px';
    controlText.style.paddingRight = '10px';
    controlText.innerHTML = 'Draw';
    controlUI.appendChild(controlText);

    controlUI.addEventListener('click', function() {
    	disable();

    	$('#draw').hide();
    	$('#remove').hide();

    	poly.setMap(null);

	    google.maps.event.addDomListener(map.getDiv(), 'mousedown', function(e){
	    	drawFreeHand(locations);
	    });
    });

}

function RemoveControl(controlDiv, map, locations) {
	var controlUI = document.createElement('div');
	controlUI.setAttribute("id", "remove");
	controlUI.style.backgroundColor = '#fff';
	controlUI.style.border = '2px solid #fff';
	controlUI.style.borderRadius = '3px';
	controlUI.style.boxShadow = '0 2px 6px rgba(0,0,0,.3)';
	controlUI.style.cursor = 'pointer';
	controlUI.style.marginBottom = '10px';
	controlUI.style.marginRight = '10px';
	controlUI.style.textAlign = 'center';
	controlDiv.appendChild(controlUI);

	var controlText = document.createElement('div');
	controlText.style.color = 'rgb(25,25,25)';
	controlText.style.fontFamily = 'Roboto,Arial,sans-serif';
	controlText.style.fontSize = '16px';
	controlText.style.lineHeight = '38px';
	controlText.style.paddingLeft = '10px';
	controlText.style.paddingRight = '10px';
	controlText.innerHTML = 'Remove Outline';
	controlUI.appendChild(controlText);

	controlUI.addEventListener('click', function() {
		$('#remove').hide();

		poly.setMap(null);

		var id_arr = [];

		bounds = map.getBounds();
		
		for (var i = 0; i < locations.length; i++) {
			if (bounds.contains(new google.maps.LatLng(locations[i][0], locations[i][1]))) {
				id_arr.push(locations[i][3]);
			}
		}

		getListing(id_arr);
    });

	
}

function drawFreeHand(locations)
{
    poly = new google.maps.Polyline({
    		map: map,
    		fillColor: 'transparent',
			strokeColor: '#ff0000',
			strokeWeight: 1,
    		clickable: false
    	});
    
    var move = google.maps.event.addListener(map, 'mousemove', function(e){
        poly.getPath().push(e.latLng);
    });
    
    google.maps.event.addListenerOnce(map, 'mouseup', function(e){
        google.maps.event.removeListener(move);
        var path = poly.getPath();

        poly.setMap(null);

        poly = new google.maps.Polygon({
    		map: map,
    		path: path,
    		fillColor: 'transparent',
			strokeColor: '#ff0000',
			strokeWeight: 2
    	});
        
        google.maps.event.clearListeners(map.getDiv(), 'mousedown');

        var id_arr = [];

        for (var i = 0; i < locations.length; i++) {
        	if (google.maps.geometry.poly.containsLocation(new google.maps.LatLng(locations[i][0], locations[i][1]), poly))
        		id_arr.push(locations[i][3]);
        }

       	getListing(id_arr);
        
        enable();

        $('#draw').show();
    	$('#remove').show();
    });
}

function disable(){
	map.setOptions({
		draggable: false, 
		zoomControl: false, 
		scrollwheel: false, 
		disableDoubleClickZoom: false
	});
}

function enable(){
	map.setOptions({
		draggable: true, 
		zoomControl: true, 
		scrollwheel: true, 
		disableDoubleClickZoom: true
	});
}

function getListing(id_arr) {
	$.ajax({
        type: 'POST',
        url: '/wp-json/v1/houzez_map_listing',
        dataType: 'JSON',
        data: {ids: id_arr},
        success: function(data) {
        	container = '';

       		container += '<div id="properties_module_section" class="houzez-module property-item-module">';
    		container += '<div id="properties_module_container">';
    		container += '<div id="module_properties" class="property-listing grid-view grid-view-2-col">';

       		content = '';

        	if (data.length > 0) {
        		var limit = (data.length > 10) ? 10: data.length;

        		content += '<div class="draw-search-result"><h2>Search Results</h2>';
        		content += '<span>Showing ' + limit + ' of ' + data.length + ' Homes</span></div>';

        		content += container;

        		for (var i = 0; i < limit; i++)
        			content += data[i];

        		content += '</div></div></div>';
        	} else {
        		content += container;
        		content += '<div class="item-wrap"><h4 class="not-found">Sorry! No Results Found</h4></div>';
        		content += '</div></div></div>';
        	}

        	$('.listing-area').empty();
        	$('.listing-area').append(content);
        }
    });
}

function initMap(locations) {  
	map = new google.maps.Map(document.getElementById('map'), {
		zoom: 10,
		center: new google.maps.LatLng(39.6, 2.95),
		gestureHandling: 'greedy',
		fullscreenControl: false,
		streetViewControl: false,
		scaleControl: false,
		zoomControl: true,
	    zoomControlOptions: {
	        position: google.maps.ControlPosition.BOTTOM_RIGHT
	    },
		mapTypeControl: true,
		mapTypeControlOptions: {
			position: google.maps.ControlPosition.TOP_RIGHT,
			style: google.maps.MapTypeControlStyle.DROPDOWN_MENU
		}
	});

	var geoArr = [];
	var geoURL = 'https://nominatim.openstreetmap.org/search.php?q=New+York&polygon_geojson=1&format=json';

	$.getJSON(geoURL, function(data) {
		var geojson = data[0]['geojson']['coordinates'][0][0];

		var geoArr = [];

		for (var i = 0; i < geojson.length; i++) {
			geoArr.push({
				lat: geojson[i][1],
				lng: geojson[i][0]
			});
		}

		poly = new google.maps.Polygon({
			paths: geoArr,
			fillColor: 'transparent',
			strokeColor: '#ff0000',
			strokeWeight: 2
        });

        poly.setMap(map);
	});

	var markers = [];
	var current_marker;

	for (var i = 0; i < locations.length; i++) {
		markers[i] = new RichMarker({
			position: new google.maps.LatLng(locations[i][0], locations[i][1]),
			map: map,
			draggable: false,
			content: '<div><div id="' + locations[i][3] + '" class="label_content">' + locations[i][2] + '</div></div>'
		});
	}

	google.maps.event.addListener(map, 'idle', function() {
		var id_arr = [];

		bounds = map.getBounds();
		
		for (var i = 0; i < markers.length; i++) {
			current_marker = markers[i];

			if (bounds.contains(current_marker.getPosition())) {
				var content = current_marker.content;
				var start = content.search('"') + 1;
				var end = content.indexOf('"', start);
				var id = content.substring(start, end);

				id_arr.push(id);
			}
		}

		if (id_arr.length == 0 && $('#map').css('display') == 'none') {
			for (var i = 0; i < locations.length; i++)
				id_arr.push(locations[i][3]);
		}

		getListing(id_arr);
	});

	var markerCluster = new MarkerClusterer(map, markers,
            {imagePath: 'https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/m'});

	var drawControlDiv = document.createElement('div');
    var drawControl = new DrawControl(drawControlDiv, map, locations);
    drawControlDiv.index = 1;
    map.controls[google.maps.ControlPosition.BOTTOM_RIGHT].push(drawControlDiv);

    var removeControlDiv = document.createElement('div');
    var removeControl = new RemoveControl(removeControlDiv, map, locations);
    removeControlDiv.index = 2;
    map.controls[google.maps.ControlPosition.BOTTOM_RIGHT].push(removeControlDiv);
}

$(document).ready(function() {
	var url_string = window.location;
	var url = new URL(url_string);

	var status = url.searchParams.get('status');
	var city = url.searchParams.get('city');
	var lifestyle = url.searchParams.get('lifestyle');
	var region = url.searchParams.get('region');
	var type = url.searchParams.get('type');
	var currency = url.searchParams.get('currency');
	var min_price = url.searchParams.get('min-price');	
	var max_price = url.searchParams.get('max-price');
	
	$.ajax({
        type: 'GET',
        url: '/wp-json/v1/houzez_map_search',
        dataType: 'JSON',
        data: {
        	status: status,
        	city: city,
        	lifestyle: lifestyle,
        	region: region,
        	type: type,
        	currency: currency,
        	min_price: min_price,
        	max_price: max_price
        },
        success: function(data) {
        	var locations = [];

        	for (var i = 0; i < data['location'].length; i++) {
        		locations.push(data['location'][i].split(','));
        		locations[i][2] = data['price'][i];
        		locations[i][3] = data['id'][i];
        	}

			initMap(locations);
        }
    });

    $(document).on("mouseover", '.draw-search .prop_addon', function() {
        var id = $(this).attr('id').substring(3);
    });
});