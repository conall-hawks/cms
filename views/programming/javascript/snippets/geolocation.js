// Create an array named 'geolocation' with relevant location data; resolved via Google's service.
document.onload = setLocation();
function setLocation(){
	navigator.geolocation.getCurrentPosition(function(position){
		$.getJSON('https://maps.googleapis.com/maps/api/geocode/json?latlng=' + position.coords.latitude + ',' + position.coords.longitude + '&sensor=true', function(data){
			window.geolocation = [], window.geolocation['latitude'] = position.coords.latitude, window.geolocation['longitude'] = position.coords.longitude;
			for(var i = 0; i < data.results[0].address_components.length; i++){
				window.geolocation[data.results[0].address_components[i].types[0]] = data.results[0].address_components[i].long_name;
				console.log("window.geolocation['" + data.results[0].address_components[i].types[0] + "'] = '" + data.results[0].address_components[i].long_name + "';");
			}
		});
	});
}