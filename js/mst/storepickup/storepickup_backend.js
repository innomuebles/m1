var Storepickup = Class.create();
Storepickup.prototype = {
    initialize: function(latitude, longitude, zoom_val){
        
        this.stockholm = new google.maps.LatLng(latitude, longitude);
        this.zoom_val = zoom_val;       
        this.marker = null;
        this.map = null;
        google.maps.event.addDomListener(window, 'load', this.initGoogleMap.bind(this));
       
      
    },
    initGoogleMap: function(){
        var mapOptions = {
            zoom: this.zoom_val,
            mapTypeId: google.maps.MapTypeId.ROADMAP,
            center: this.stockholm
        };

        this.map = new google.maps.Map($('googleMap'),
            mapOptions);

        this.marker = new google.maps.Marker({
            map:this.map,
            draggable:true,   
            position: this.stockholm
        });
        google.maps.event.addListener(this.marker, 'dragend', function(event) {                       
            $('store_lat_val').value = event.latLng.lat();
            $('store_lon_val').value = event.latLng.lng();
            $('latitude').value = event.latLng.lat();
            $('longitude').value = event.latLng.lng();
            $('latitude').setStyle({
                background: '#FAE6B4'
            });
            $('longitude').setStyle({
                background: '#FAE6B4'
            });
        }.bind(this));
        google.maps.event.addListener(this.map, 'zoom_changed', function() {            
            var zoomLevel = this.map.getZoom();            
            $('zoom_level_val').value = zoomLevel;
            $('zoom_level').value = zoomLevel;
            $('zoom_level').setStyle({
                background: '#FAE6B4'
            });
        }.bind(this));
    }
}


