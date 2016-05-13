// Checks to see if links work (are 200 OK); colorizes and disables invalid links.
$(document).ready(checkLinks);
function checkLinks(){
	checkLinks.link = $('a');
	for(var i = 0; i < checkLinks.link.length; i++){
		$.ajax({
			url: checkLinks.link[i].href, type: 'HEAD', error: function(){
				$(checkLinks.link[i]).attr('style', 'color: grey !important; cursor: default; pointer-events: none;');
			}
		});
	}
}