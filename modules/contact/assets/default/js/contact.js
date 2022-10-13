imsContact = {
    contact: function (form_id) {
        $("#" + form_id).validate({
            submitHandler: function () {
                var fData = $("#" + form_id).serializeArray();

                loading('show');

                $.ajax({
                    type: "POST",
                    url: ROOT + "ajax.php",
                    data: {"m": "contact", "f": "contact", "data": fData, "lang_cur": lang}
                }).done(function (string) {
                    var data = JSON.parse(string);

                    loading('hide');

                    if (data.ok == 1) {
                        $('#' + form_id)[0].reset();
                        alert(data.mess);
                    } else {
                        alert(data.mess);
                    }
                });
                //e.preventDefault(); //STOP default action
                //e.unbind(); //unbind. to stop multiple form submit.
                return false;
            },
            rules: {
                full_name: {
                    required: true
                },
                phone: {
                    // required: true
                },
                address: {
                    // required: true
                },
                email: {
                    required: true,
                    email: true
                },
                title: {
                    // required: true
                },
                content: {
                    required: true
                }
            },
            messages: {
                full_name: get_lang('err_valid_input'),
                phone: get_lang('err_valid_input'),
                address: get_lang('err_valid_input'),
                email: lang_js['err_invalid_email'],
                title: get_lang('err_valid_input'),
                content: get_lang('err_valid_input')
            }
        });
    },
    
	initialize : function(arr_markers, map_canvas_id) {
		var timeRefresh = 0;
		var formHiddenId = 'form-filter-hidden';
		var marker;
		var mapZoom = 17;
        var map_canvas_id = (map_canvas_id) ? map_canvas_id : 'map_canvas';
		var iMarker = DIR_IMAGE+'marker.png';
		var iMarkerCur = DIR_IMAGE+'marker_cur.png';
		var infoBubble = new InfoBubble({
			shadowStyle: 0,
			padding: 10,
			borderWidth: 1,
			borderStyle: 'solid',
			borderColor: '#000',
			borderRadius: 5,
			backgroundColor: 'rgba(255,255,255,0.8)',
			arrowSize: 5,
			arrowPosition: '10%'
			//minWidth: 370,
			//maxWidth: 370
		});
		
		var eventBubbleOpen = 'click';
		var eventBubbleClose = 'click';
		
		//setTimeout setup
		var autoCallGeo;
		var autoCallMarker;
		//End
	
		var markers = {};
	
		var stockholm = new google.maps.LatLng(10.7596327,106.6571219);
		var mapOptions = {
			zoom: 0,
			mapTypeId: google.maps.MapTypeId.ROADMAP,
			center: stockholm
		};
	
		var map = new google.maps.Map(document.getElementById(map_canvas_id), mapOptions);
						
		var geocoder = new google.maps.Geocoder();
		
		//Current location
		//prepareGeolocation();
		//End
		
		//doLoad =========================================
		function doLoad() {
			map.setZoom(mapZoom);
			centerMap();			
			loadMarkers();
		}
		
		//centerMap =========================================
		function centerMap() {
			
            //var address = $('#'+map_canvas_id).data('centerMap');
//			geocoder.geocode( { 'address': address}, function(results, status) {
//                if (status == google.maps.GeocoderStatus.OK) {
//                        map.setCenter(results[0].geometry.location);
//                        map.fitBounds(results[0].geometry.bounds);
//                }/* else {
//                        alert("Geocode was not successful for the following reason: " + status);
//                }*/
//			});
            var latlng = {lat: parseFloat($('#'+map_canvas_id).data('centermaplat')), lng: parseFloat($('#'+map_canvas_id).data('centermaplng'))};
            map.setCenter(latlng);
//			var geocoder = new google.maps.Geocoder();
//            geocoder.geocode( { 'location': latlng}, function(results, status) {
//                if (status == google.maps.GeocoderStatus.OK) {
//                        map.setCenter(results[0].geometry.location);
//                        map.fitBounds(results[0].geometry.bounds);
//                }/* else {
//                        alert("Geocode was not successful for the following reason: " + status);
//                }*/
//			});
		}
		
		//loadMarkers ==========================================
		function loadMarkers() {
			
			infoBubble.close();

            clearTimeout(autoCallGeo);
            clearTimeout(autoCallMarker);

            if(arr_markers) {					
                //Add or update user on map
                var i = 0;
                $.each(arr_markers, function(id, row){						
                    if(markers[id]) {
                        //Update markers in arr_user
                        //alert('update: '+id);
                        //console.log('update: '+id);
                        markers[id].setPosition(new google.maps.LatLng(row.map_latitude, row.map_longitude));
                        //End
                    } else {
                        i++;

                        markers[id] = (new google.maps.Marker({
                            position: new google.maps.LatLng(row.map_latitude, row.map_longitude),
                            map: map,
                            icon: {
                                url: (row.marker_icon) ? row.marker_icon : iMarker,
                                scaledSize: new google.maps.Size(28, 38)
                            },
                            draggable: false,
                            animation: google.maps.Animation.DROP
                        }));

                        //attachInfoBubble
                        var e_h_id = $('#'+formHiddenId+' input[name="h_id"]');
                        var h_id = e_h_id.val();
                        if(h_id) {
                            if(e_h_id.data('popup')) {
                                google.maps.event.addListener(markers[id], 'click', function() {
                                    popup(e_h_id.data('popup'));
                                });
                            }										
                        } else {
                            row.map_info_window = 'hotel';
                            attachInfoBubble(markers[id], row);										
                        }
                        //End
                    }	
                });
                //End
            }
            return false;
		}
	
		//attachInfoBubble ==========================================
		function attachInfoBubble(marker, row) {
			var html_id = row.user_id;
			var typeBubble = (row.map_info_window) ? row.map_info_window : 'default';
			
			html_id = 'infowd-content-'+html_id;
			google.maps.event.addListener(marker, eventBubbleOpen, function() {
			//google.maps.event.addListener(marker, 'mouseover', function() {
				infoBubble.setContent('<div id="'+html_id+'" class="infowd-content infowd-content-'+typeBubble+'">'+row.map_information+'</div>');
				
				updateBubbleStyles(typeBubble);
				
				/*if (!infoBubble.isOpen()) {
					infoBubble.open(map, marker);
				}*/
				infoBubble.open(map, marker);
			});
			/*google.maps.event.addListener(marker, 'mouseout', function() {
				infoBubble.close(map, marker);
			});*/
		}
		
		function updateBubbleStyles(typeBubble) {
			
			switch(typeBubble)
			{
				case "hotel":
					infoBubble.setMinWidth(370);
					infoBubble.setMaxWidth(370);
					break;
				default:
					infoBubble.setMinWidth(0);
					infoBubble.setMaxWidth(0);
					break;
			}
		}
		
		//Close infoWindow when click map
		google.maps.event.addListener(map, eventBubbleClose, function() {
			//infoBubble.setContent('');
			infoBubble.close();
		});
		//End
	
		google.maps.event.addListenerOnce(map, 'tilesloaded', doLoad);
	}
};