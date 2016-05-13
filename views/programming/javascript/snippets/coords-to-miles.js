function coords2miles(lat1, lon1, lat2, lon2){
	var radlat1 = Math.PI * lat1 / 180;
	var radlat2 = Math.PI * lat2 / 180;
	return Math.floor(Math.acos(Math.sin(radlat1) * Math.sin(radlat2) + Math.cos(radlat1) * Math.cos(radlat2) * Math.cos(Math.PI * (lon1 - lon2) / 180)) * 180 / Math.PI * 60 * 1.1515);
}