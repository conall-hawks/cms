// Turns a string into a lat/lng
// getCoordsFromAddress('new york');
// window.coords['new york']
function getCoordsFromAddress(address){
	if(typeof window.coords === 'object' && typeof window.coords[address] === 'object'){
		return window.coords[address];
	}else{
		if(typeof getCoordsFromAddress.getGoogleMapsScript !== 'boolean') getCoordsFromAddress.getGoogleMapsScript = true;
		var geoStart = setInterval(function(){
			if(typeof google === 'object' && typeof google.maps === 'object'){
				clearInterval(geoStart);
				var geocoder = new google.maps.Geocoder();
				geocoder.geocode({'address': address}, function(results, status){
					if(status == google.maps.GeocoderStatus.OK){
						var latitude = results[0].geometry.location.lat();
						var longitude = results[0].geometry.location.lng(); 
						if(typeof window.coords !== 'object') window.coords = {};
						window.coords[address] = [latitude, longitude];
						console.log('window.coords[\'' + address + '\'] = [' + latitude + ', ' + longitude + '];');
					}
				});
			}else if(getCoordsFromAddress.getGoogleMapsScript === true){
				getCoordsFromAddress.getGoogleMapsScript = false;
				$.getScript('http://maps.google.com/maps/api/js?sensor=false');
			}
		}, 1000);
	}
}