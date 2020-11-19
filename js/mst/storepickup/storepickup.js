var StorepickupIndex = Class.create(); 
StorepickupIndex.prototype = {
    initialize: function(latitude, longtitude, zoom_val, id_map){
        this.defaultLatlng = new google.maps.LatLng(latitude, longtitude);
        this.markerArr = [];
        this.myOptions = {
            zoom: zoom_val,
            center: this.defaultLatlng,
            mapTypeId: google.maps.MapTypeId.ROADMAP
        };
        this.map = new google.maps.Map(document.getElementById(id_map), this.myOptions);
        this.bounds = new google.maps.LatLngBounds();
		
    },
    
    placeStoreMarker: function(point, store_info, store_id, image,zoomLevel, infoWindow, x , latitude , longtitude ){                
        var marker;
        if(image){
            marker = new google.maps.Marker({
                position: point,
                map: this.map,
                icon: image,
                store_id :store_id 
            });
        }
        else {
            marker = new google.maps.Marker({
                position: point,
                map: this.map,
                store_id :store_id 
            });
        }
        this.markerArr.push(marker);
        google.maps.event.addListener(marker, 'click', function(event) {
            infoWindow.setContent(store_info);
            infoWindow.setPosition(event.latLng);
			this.map.setCenter(event.latLng);            
            infoWindow.open(this.map, marker);
            if(zoomLevel!=0){
                /* this.map.setZoom(zoomLevel);
				console.log ( point ); */
				if($('select_store_id') != undefined) {
				$('select_store_id').value = store_id;
				$('select_store_lat').value = latitude;
				$('select_store_lon').value = longtitude;
				$('detail-selected-store').update( store_info );
				}
				
				
            }
        }.bind(this));
    },
    extendStoreBound: function(marker){
        this.bounds.extend(marker);
    },
    setFitStoreBounds: function (){
		//google.maps.event.trigger(this.map, "resize"); // edit by david responsive
        this.map.fitBounds(this.bounds);
    },
	setFitStoreBoundsOne: function (){
		//google.maps.event.trigger(this.map, "resize"); // edit by david responsive
        this.map.setCenter(this.bounds.getCenter());
    },
    
    
}

var InfostorePopup = Class.create();
InfostorePopup.prototype = {
    initialize: function(store_id,html, zoom, point , latitude, longtitude ){
        this.store_id = store_id;
        this.html = html;
        this.point = point;
        this.zoom = zoom;
        this.latitude = latitude;
        this.longtitude = longtitude;
    }
}
