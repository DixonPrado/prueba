/**
 * ------------------------------------------------------------------------
 * JA System Google Map plugin
 * ------------------------------------------------------------------------
 * Copyright (C) 2004-2018 J.O.O.M Solutions Co., Ltd. All Rights Reserved.
 * @license - GNU/GPL, http://www.gnu.org/licenses/gpl.html
 * Author: J.O.O.M Solutions Co., Ltd
 * Websites: http://www.joomlart.com - http://www.joomlancers.com
 * ------------------------------------------------------------------------
 */

/**
 * USER GOOGLE MAP API VERSION 3
 * https://developers.google.com/maps/documentation/javascript/reference
*/
(function($) {
	JAWidgetMap = function (container, defaults) {
		this.initialize = function (container, defaults) {
			this.idPrefix = 'ja-widget-map';
			this.container = container;
			this.containerSV = container + '-streeview';
			this.containerR = container + '-route';
			this.containerR_height = 200;
			this.options = defaults;
			
			//
			this.context_menu = null;
			this.toolbar_control_style = null;
			this.maptype_control_style = null;
			this.GScaleControl = null;
			this.GOverviewMapControl = null;
			this.GScaleControl = null;
			this.layer = null;
			//
			
			this.createElement();
		};
		
		this.createElement = function(){
			var mapOptions = {};
			mapOptions.mapTypeId = this.getMapType(this.options.maptype);
			if(this.options.size) { 
				mapOptions.size = this.options.size; 
			}
			//tollbar
			if (this.options.toolbar_control_display == 1) {
				
				var controlPos = this.getPosition(this.options.toolbar_control_position);
				var toolbar_control_style;
				switch (this.options.toolbar_control_style) {
					case 'small':
						toolbar_control_style = google.maps.ZoomControlStyle.SMALL;
						break;
					case 'large':
						toolbar_control_style = google.maps.ZoomControlStyle.LARGE;
						break;
					default:
						toolbar_control_style = google.maps.ZoomControlStyle.DEFAULT;
						break;
				}
				this.toolbar_control_style = toolbar_control_style;
				
				mapOptions.zoomControl = true;
				mapOptions.zoomControlOptions = {
					style: toolbar_control_style,
					position: controlPos
				}
			} else {
				mapOptions.zoomControl = false;
				this.toolbar_control_style = null;
			}
			
			//maptype control
			if (this.options.maptype_control_display == 1) {
				var maptypeControlPos = this.getPosition(this.options.maptype_control_position);
				var maptype_control_style;
				switch (this.options.maptype_control_style) {
				case 'hierarchical':
					maptype_control_style = google.maps.MapTypeControlStyle.HORIZONTAL_BAR;
					break;
				case 'drop_down':
					maptype_control_style = google.maps.MapTypeControlStyle.DROPDOWN_MENU;
					break;
				default:
					maptype_control_style = google.maps.MapTypeControlStyle.DEFAULT;
					break;
				}
				this.maptype_control_style = maptype_control_style;
				
				mapOptions.mapTypeControl = true;
				mapOptions.mapTypeControlOptions = {
					style: maptype_control_style,
					position: maptypeControlPos
				}
			} else {
				mapOptions.mapTypeControl = false;
				this.maptype_control_style = null;
			}
			
			//scalse
			if (this.options.display_scale == 1) {
				mapOptions.scaleControl = true;
				mapOptions.scaleControlOptions = {
					position: google.maps.ControlPosition.BOTTOM_LEFT
				}
			} else {
				mapOptions.scaleControl = false;
				this.GScaleControl = null;
			}
			//overview
			if (this.options.display_overview == 1) {
				mapOptions.overviewMapControl = true;
				mapOptions.overviewMapControlOptions = {
					opened: true
				}
			} else {
				this.GOverviewMapControl = null;
			}
			if (this.options.scrollwheel == 'false') {
				mapOptions.scrollwheel = false;
			}
			//Map styles
			if(this.options.map_styles){
				var mapstyles = this.options.map_styles;
				if(this.options.map_styles.length >0){
					mapstyles = eval("(function(){return " + mapstyles + ";})()");
					mapOptions.styles = mapstyles;
				}
			}

			this.objMap = new google.maps.Map($('#'+this.container).get(0), mapOptions);


			//layers
			
			// Add ContextMenuControl
			if (this.options.context_menu == 1) {
				//this.context_menu = new ContextMenuControl();
				//this.objMap.addControl(this.context_menu);
			} else {
				this.context_menu = null;
			}
			
			//geo location
			this.geocoder = new google.maps.Geocoder();
			
			//direction
			this.objDirections = null;
			this.directionDisplay = null;
			if ($('#'+this.containerR).length) {
				this.objDirectionsPanel = $('#'+this.containerR).get(0);
				this.objDirections = new google.maps.DirectionsService();
				this.directionDisplay = new google.maps.DirectionsRenderer();
				this.directionDisplay.setMap(this.objMap);
				this.directionDisplay.setPanel(this.objDirectionsPanel);
			}
			
		};
		
		this.resetMap = function() {
			
		};
		
		this.setMap = function(aOptions) {
			this.resetMap();
			
			this.options = aOptions;
			this.createElement();
		};
		
		this.setCenter = function (source) {
			if(source.objMap) {
				this.objMap.setCenter(source.objMap.getCenter());
				this.objMap.setZoom(source.objMap.getZoom());
			}
		};

		this.displayMap = function () {
			var locations = null;
			if(typeof(this.options.locations) != 'undefined'
				&& this.options.locations != '{}'
			) {
				if (typeof this.options.locations == 'string')
					locations = JSON.parse(this.options.locations);
				else
					locations = (this.options.locations);
			}

			if(!locations) {
				//backward compatible with old versions
				locations = {
					"location" : {
						"0" : "New York"
					},
					"latitude" : {
						"0" : ""
					},
					"longitude" : {
						"0" : ""
					},
					"info" : {
						"0" : ""
					}
				};
				if(typeof (this.options.to_location) != 'undefined')
					locations.location["0"] = this.options.to_location;
				if(typeof (this.options.target_lat) != 'undefined')
					locations.latitude["0"] = this.options.target_lat;
				if(typeof (this.options.target_lon) != 'undefined')
					locations.longitude["0"] = this.options.target_lon;
				if(typeof (this.options.to_location_info) != 'undefined')
					locations.info["0"] = this.options.to_location_info;
				if(typeof (this.options.from_location) != 'undefined') {
					if(this.options.from_location != '') {
						locations.location["1"] = this.options.from_location;
						locations.latitude["1"] = '';
						locations.longitude["1"] = '';
						locations.info["1"] = '';
					}
				}
			}

			if(!locations) {
				alert('Please select a Location!');
				return false;
			}

			var bounds = new google.maps.LatLngBounds();
			var mapmarker = new Array();
			for(var i in locations.location) {
				var location = locations.location[i],
					latitude = parseFloat(locations.latitude[i]),
					longitude = parseFloat(locations.longitude[i]),
					info = locations.info[i],
					icon = null;
				if(typeof locations.icon != 'undefined') {
					icon = locations.icon[i];
				}

				if(this.isLatLon(latitude) && this.isLatLon(longitude)){
					if (this.options.center == 'all')
						bounds.extend(new google.maps.LatLng(latitude, longitude));
					mapmarker.push(this.showLocation2(latitude, longitude, info, icon));
				}else{
					this.showLocation(location, info);
				}
			}

			if(this.isLatLon(latitude) && this.isLatLon(longitude))
				if (this.options.center == 'last')
					this.objMap.setCenter(new google.maps.LatLng(latitude, longitude));

			if(this.isLatLon(parseFloat(locations.latitude[0])) && this.isLatLon(parseFloat(locations.longitude[0])))
				if (this.options.center == 'first')
					this.objMap.setCenter(new google.maps.LatLng(parseFloat(locations.latitude[0]), parseFloat(locations.longitude[0])));

			if (this.options.center == 'all' && parseInt(i) != 0)
				this.objMap.fitBounds(bounds);

			if (this.options.clustering) {
				var markerCluster = new MarkerClusterer(this.objMap, mapmarker,
					{imagePath: 'https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/m'});
			}

			// in case if center is lat lng value.
			if (this.options.center && this.options.center != '')
			{
				$latlng = this.options.center.split(',');
				if ($latlng.length == 2)
					this.objMap.setCenter(new google.maps.LatLng(parseFloat($latlng[0]), parseFloat($latlng[1])));
			}
			if(typeof(this.options.mode) != 'undefined' && this.options.mode == 'direction') {
				this.showDirections(locations);
			}
			
		};

		this.showLocation = function (address, info) {
			this.hideRoute();
			// hide route
			var lvZoom = this.options.zoom;
			var objMap = this.objMap;
			
			if (this.geocoder) {
				this.geocoder.geocode( { 'address': address}, function (results, status) {
					if (status != google.maps.GeocoderStatus.OK) {
						alert(address + " not found");
					} else {
						objMap.setCenter(results[0].geometry.location);
						objMap.setZoom(lvZoom);
						var marker = new google.maps.Marker({
							position: results[0].geometry.location,
							map: objMap,
							draggable: false
						});
						if(info != '') {
							var infowindow = new google.maps.InfoWindow({
								content: info
							});
							google.maps.event.addListener(marker, 'click', function() {
							infowindow.open(objMap,marker);
							});
						}
					}
				});
			}
		};

		this.showLocation2 = function (lat, lon, info, icon) {
			this.hideRoute();
			
			var lvZoom = this.options.zoom;
			var objMap = this.objMap;
			
			var point = new google.maps.LatLng(lat, lon);
			objMap.setCenter(point);
			objMap.setZoom(lvZoom);
			var marker = new google.maps.Marker({
							position: point,
							map: objMap,
							draggable: false,
							icon: icon
						});
			if(info != '') {
				var infowindow = new google.maps.InfoWindow({
					content: info
				});
				google.maps.event.addListener(marker, 'click', function() {
				infowindow.open(objMap,marker);
				});
			}
			return marker;
		};

		this.showDirections = function (locations) {
			var from = '', to = '', waypts = [];

			for(var i in locations.location) {
				var location = locations.location[i],
					latitude = parseFloat(locations.latitude[i]),
					longitude = parseFloat(locations.longitude[i]),
					info = locations.info[i];

				if(this.isLatLon(latitude) && this.isLatLon(longitude)){
					var point = new google.maps.LatLng(latitude, longitude);
				}else{
					var point = location;
				}
				if(i == "0") {
					from = point;
				}
				to = point;

				waypts.push({location: point, stopover:true});
			}


			var directionDisplay = this.directionDisplay;
			var objMap = this.objMap;
			var request = {
				origin: from,
				destination: to,
				waypoints: waypts,
				optimizeWaypoints: true,
				travelMode: google.maps.DirectionsTravelMode.DRIVING
			};
			this.objDirections.route(request, function(response, status) {
				if (status == google.maps.DirectionsStatus.OK) {
					setZoom = true;
					directionDisplay.setOptions({preserveViewport: true}); 
					directionDisplay.setDirections(response);
					directionDisplay.setMap(objMap)
				}
			});
		};
		
		this.isLatLon = function(number) {
			return (number == 0.00 || String(number) == "NaN") ? false : true;
		};

		this.showRoute = function (height) {
			if($('#'+this.containerR)) {
				$('#'+this.containerR).css('height', height);
			}
		};
		this.hideRoute = function () {
			if($('#'+this.containerR)) {
				$('#'+this.containerR).css('height', 0);
			}
		},

		this.handleNoFlash = function (errorCode) {
			if (errorCode == FLASH_UNAVAILABLE) {
				alert("Error: Flash doesn't appear to be supported by your browser");
				return;
			}
		};
		
		this.getMapType = function(type) {
			switch(type) {
				case 'satellite': maptype = google.maps.MapTypeId.SATELLITE; break;
				case 'hybrid': maptype = google.maps.MapTypeId.HYBRID; break;
				case 'physical': maptype = google.maps.MapTypeId.TERRAIN; break;
				default: maptype = google.maps.MapTypeId.ROADMAP; break;
			}
			return maptype;
		};
		
		this.getPosition = function(pos) {
			/**
			+----------------+
			+ TL    TC    TR +
			+ LT          RT +
			+                +
			+ LC          RC +
			+                +
			+ LB          RB +
			+ BL    BC    BR +
			+----------------+ 
			*/
			switch(pos) {
				case 'TL': position = google.maps.ControlPosition.TOP_LEFT; break;
				case 'TC': position = google.maps.ControlPosition.TOP_CENTER; break;
				case 'TR': position = google.maps.ControlPosition.TOP_RIGHT; break;
				
				case 'LT': position = google.maps.ControlPosition.LEFT_TOP; break;
				case 'RT': position = google.maps.ControlPosition.RIGHT_TOP; break;
				
				case 'LC': position = google.maps.ControlPosition.LEFT_CENTER; break;
				case 'RC': position = google.maps.ControlPosition.RIGHT_CENTER; break;
				
				case 'LB': position = google.maps.ControlPosition.LEFT_BOTTOM; break;
				case 'RB': position = google.maps.ControlPosition.RIGHT_BOTTOM; break;
				
				case 'BL': position = google.maps.ControlPosition.BOTTOM_LEFT; break;
				case 'BC': position = google.maps.ControlPosition.BOTTOM_CENTER; break;
				case 'BR': position = google.maps.ControlPosition.BOTTOM_RIGHT; break;
				
				default: position = google.maps.ControlPosition.TOP_RIGHT; break;
			}
			return position;
		};
		this.initialize(container, defaults);
	};
})(jQuery);
