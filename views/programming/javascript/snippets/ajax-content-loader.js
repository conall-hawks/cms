// #####################################################################################################################
// # This is a barebones AJAX system I created. Every link in the class "ajax-link" (i.e. <a class="ajax-link">) will  #
// # instead perform an XMLHTTPRequest and the response will be placed into an element with the class ".content"       #
// # (i.e. <section class="content">). This will also perform the necessary browser history manipulations so the       #
// # back/forward buttons still work as expected. Uses jQuery.                                                         #
// #####################################################################################################################

// Stores the current state.
var state = window.location.pathname + window.location.hash;

// Changes the URL when an AJAX link is clicked.
$(document).on('click', '.ajax-link', function(event){
	event.preventDefault();
	// Change the URL and create history (push a new state onto the stack).
	if(window.location.pathname !== this.pathname){
		history.pushState(null, null, this.href + this.hash);
		setState(this.href);
	}
});

// Changes the URL when the back button is clicked (a state has been popped off of the stack).
window.onpopstate = function(){
	if(window.location.pathname !== state.split('#')[0]){
		setState(window.location.href + window.location.hash);
	}
};

// Synchronizes content with a state.
function setState(link){
	if(typeof setState.content === 'undefined') setState.content = $('.content');
	setState.content.load(link, function(){
		state =  window.location.pathname + window.location.hash;
	});
}