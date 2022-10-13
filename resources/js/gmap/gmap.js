function initialize() {
	var timeRefresh = 10000;
	var formHiddenId = 'form-filter-hidden';
	var marker;
	var mapZoom = 17;
	var iMarker = DIR_IMAGE+'marker.png';
	var iMarker_me = DIR_IMAGE+'marker_me.png';
	var meID = 0;
	var geoLocation = 0;
	var infoBubble = new InfoBubble({
		shadowStyle: 0,
		padding : 0,
		borderWidth : 0,
		backgroundColor : 'rgba(0,0,0,0.8)',
		minWidth: 320/*,
		maxWidth: 300*/
	});
	
	//setTimeout setup
	var autoCallGeo;
	var autoCallMarker;
	//End

	var markers = {};

	var stockholm = new google.maps.LatLng(10.7596327,106.6571219);
	var mapOptions = {
		zoom: mapZoom,
		mapTypeId: google.maps.MapTypeId.ROADMAP,
		center: stockholm
	};

	var map = new google.maps.Map(document.getElementById("map_canvas"),
					mapOptions);
	
	//Current location
	//prepareGeolocation();
	//doGeolocation();
	//End
	
	//doLoad =========================================
	function doLoad() {
    doGeolocation();
		userPositioning();
  }
	
	//doGeolocation =========================================
	function doGeolocation() {
    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(positionSuccess, positionError);
    } else {
      positionError(-1);
    }
  }
	
	//positionError ===========================================
	function positionError(err) {
    var msg;
    switch(err.code) {
      case err.UNKNOWN_ERROR:
        msg = "Unable to find your location";
        break;
      case err.PERMISSION_DENINED:
        msg = "Permission denied in finding your location";
        break;
      case err.POSITION_UNAVAILABLE:
        msg = "Your location is currently unknown";
        break;
      case err.BREAK:
        msg = "Attempt to find location took too long";
        break;
      default:
        msg = "Location detection not supported in browser";
    }
    document.getElementById('info').innerHTML = msg;
  }
	
	//positionSuccess ======================================
  function positionSuccess(position) {
		
    // Centre the map on the new location
    var coords = position.coords || position.coordinate || position;
    var latLng = new google.maps.LatLng(coords.latitude, coords.longitude);
		if(geoLocation == 0) {
			map.setCenter(latLng);
			map.setZoom(mapZoom);
			markers['me'] = (new google.maps.Marker({
				position: latLng,
				map: map,
				icon: {
					url: iMarker_me,
					scaledSize: new google.maps.Size(55, 74)
				}
			}));
		} else {
			markers['me'].setPosition(latLng);
		}
		geoLocation++;
		
		$.ajax({
			type: "POST",
			url: ROOT+"ajax.php",
			data: { "m" : "user", "f" : "positioning", "map_lat" : latLng.lat(), "map_lng" : latLng.lng(), "screen_width" : screen.width, "screen_height" : screen.height }
		}).done(function( string ) {
			var data = JSON.parse(string);
			
			//alert(data.info.user_id);
			
			if(data.info.user_id && !meID) {
				meID = data.info.user_id;
				attachInfoBubble(markers['me'], data.info);
			}
			
			//userPositioning();
			
			$('#'+formHiddenId).submit();
			
			/*setTimeout(function(){
				doGeolocation();
			},2000);*/
			
		});
    
  }
	
	//userPositioning ==========================================
	function userPositioning() {
		$('#'+formHiddenId).submit(function(e) {
			
			infoBubble.close();
			
			clearTimeout(autoCallGeo);
			clearTimeout(autoCallMarker);
			
			var fData = $('#'+formHiddenId).serializeArray();		
			$.ajax({
				type: "POST",
				url: ROOT+"ajax.php",
				data: { "m" : "user", "f" : "user_positioning", "data" : fData }
			}).done(function( string ) {
				var data = JSON.parse(string);
				if(data.ok == 1) {
					
					//Remove markers out arr_user and arr_vehicle
					$.each(markers, function(id){	
						//alert(id);
						//console.log( "del " + id );					
						if(id != 'me' && !data.arr_markers[id] && markers[id]) {
							//console.log( "del " + id );
							markers[id].setMap(null);
							delete markers[id];
						}
					});
					//End
					
					if(data.arr_markers) {					
						//Add or update user on map
						var i = 0;
							$.each(data.arr_markers, function(id, row){						
							if(markers[id]) {
								//Update markers in arr_user
								//alert('update: '+id);
								//console.log('update: '+id);
								markers[id].setPosition(new google.maps.LatLng(row.map_latitude, row.map_longitude));
								//End
							} else {
								//Add markers in arr_user
								//alert('add: '+id);
								//console.log('add: '+id);
								i++;
								
								/*autoCallMarker = setTimeout(function() {
									markers[id] = (new google.maps.Marker({
										position: new google.maps.LatLng(row.map_latitude, row.map_longitude),
										map: map,
										icon: {
											url: (row.marker_icon) ? row.marker_icon : iMarker,
											scaledSize: new google.maps.Size(27, 33)
										},
										draggable: false,
										animation: google.maps.Animation.DROP
									}));
									
									//attachInfoBubble
									attachInfoBubble(markers[id], row);
								}, i * 100);*/								
								markers[id] = (new google.maps.Marker({
									position: new google.maps.LatLng(row.map_latitude, row.map_longitude),
									map: map,
									icon: {
										url: (row.marker_icon) ? row.marker_icon : iMarker,
										scaledSize: new google.maps.Size(27, 33)
									},
									draggable: false,
									animation: google.maps.Animation.DROP
								}));
								
								//attachInfoBubble
								attachInfoBubble(markers[id], row);
								//End
							}	
						});
						//End
					}
				} 
				
				loading('hide');
				
				autoCallGeo = setTimeout(function(){
					doGeolocation();
				},timeRefresh);
				
			});
			return false;
		});		
	}

	//attachInfoBubble ==========================================
	function attachInfoBubble(marker, row) {
		//var html_id = (meID) ? 'me' : row.user_id;
		var html_id = row.user_id;
		var typeBubble = (row.map_info_window) ? row.map_info_window : 'user';
		
		html_id = 'infowd-content-'+html_id;
		google.maps.event.addListener(marker, 'click', function() {
			infoBubble.setContent('<div id="'+html_id+'" class="infowd-content infowd-content-'+typeBubble+'">'+imsTemp.info_marker(row, typeBubble)+'</div>');
			
			updateBubbleStyles(typeBubble);
			
			/*if (!infoBubble.isOpen()) {
				infoBubble.open(map, marker);
			}*/
			infoBubble.open(map, marker);
    });
	}
	
	function updateBubbleStyles(typeBubble) {
		
		switch(typeBubble)
		{
			case "driver":
			case "sale":
			case "rent":
				infoBubble.setMinWidth(350);
				infoBubble.setMinHeight(335);
				infoBubble.setMaxHeight(335);
				break;
			default:
				infoBubble.setMinWidth(350);
				infoBubble.setMinHeight(220);
				infoBubble.setMaxHeight(220);
				break;
		}
	}
	
	//Close infoWindow when click map
	google.maps.event.addListener(map, 'click', function() {
		//infoBubble.setContent('');
		infoBubble.close();
	});
	//End

	document.getElementById("map_center").onclick = function() {
		if(markers['me']) {
			map.setCenter(markers['me'].getPosition());
			//map.setZoom(mapZoom);
		}	
	};

	google.maps.event.addListenerOnce(map, 'tilesloaded', doLoad);
}