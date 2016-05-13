/* ########################################################################## */
/* #### Fancy Page Loading ################################################## */
/* ########################################################################## */

// Header box cells slide-in.
$(document).ready(function(){
	var element = $('.header-box td, .navbar-box td');
	var delay = 350;
	var time = 1000;
	element.css('left', '100%');
	element.each(function(index){
		setTimeout(function(){
			$(element[index]).css('left', '0%');
		}, delay);
		delay += 50;
	});
	setTimeout(function(){element.removeAttr('style')}, time + delay);
});

function asideSlideOut(){
	var element = $('.content-nav');
	element.css('left', '-100%');
	element.css('opacity', 0);
}

function contentSlideOut(){
	var element = $('.content-box');
	element.each(function(index){
		$(element[index]).css('left', (index % 2 ? '-150%' : '150%'));
		$(element[index]).css('opacity', 0);
	});
}

/* ########################################################################## */
/* #### Navigation Bar ###################################################### */
/* ########################################################################## */

// Throttle prevents lag.
$(window).scroll($.throttle(100, navbar));

// Only show the navbar if we need it.
function navbar(){
	if(typeof navbar.element === 'undefined'){
		navbar.element = $('.navbar');
		navbar.headerHeight = $('.header').height();
	}
	var viewOffset = $(window).scrollTop();
	
	if(viewOffset > navbar.headerHeight){
		navbar.element.addClass('navbar-show');
	}else if(navbar.element.hasClass('navbar-show')){
		navbar.element.removeClass('navbar-show');
	}
}

/* ########################################################################## */
/* #### AJAX Content Loader ################################################# */
/* ########################################################################## */

// Store the current state.
var state = window.location.pathname + window.location.hash;

// Change the URL when an AJAX link is clicked.
$(document).on('click', '.ajax-link', function(event){
	event.preventDefault();
	// Change the URL and create history.
	if(window.location.pathname !== this.pathname){
		this.href = $(this).attr('href');
		history.pushState(null, null, this.href + this.hash);
		setState(this.href);
	}
});

// Change the URL when the back button is clicked. Not sure why this needs to be wrapped in a function.
window.onpopstate = function(){
	if(window.location.pathname !== state.split('#')[0]){
		setState(window.location.href + window.location.hash);
	}
};

// Synchronize with a new state.// Synchronize with a new state.
function setState(link){
	if(typeof setState.header === 'undefined') setState.header = $('.header-h2');
	if(typeof setState.content === 'undefined') setState.content = $('.content');
	
	// Fancy transition.
	document.title += ' \u2622 Loading...';
	setState.header.text('Loading...');
	setState.header.css({'opacity': .25, 'transition': '.5s opacity'});
	setState.content.load(link, function(){
		state =  window.location.pathname + window.location.hash;
		setState.header.css('opacity', 1);
		
		// Code highlighting.
		$('code').each(function(i, block){hljs.highlightBlock(block);});
	});
}

/* ########################################################################## */
/* #### Calendar & Clock #################################################### */
/* ########################################################################## */
$(document).ready(function(){$('.calendar').text(calendar())});
function calendar(){
	calendar.date = new Date();
	calendar.year = calendar.date.getFullYear();
	calendar.month = calendar.date.getMonth();
	calendar.day = calendar.date.getDate();
	
	// Verbose month.
	switch(calendar.month){
		case 0: calendar.month = 'January'; break;
		case 1: calendar.month = 'February'; break;
		case 2: calendar.month = 'March'; break;
		case 3: calendar.month = 'April'; break;
		case 4: calendar.month = 'May'; break;
		case 5: calendar.month = 'June'; break;
		case 6: calendar.month = 'July'; break;
		case 7: calendar.month = 'August'; break;
		case 8: calendar.month = 'September'; break;
		case 9: calendar.month = 'October'; break;
		case 10: calendar.month = 'November'; break;
		case 11: calendar.month = 'December'; break;
	}
	
	// Date suffix.
	switch(calendar.day){
		case 1: calendar.day += 'st'; break;
		case 2: calendar.day += 'nd'; break;
		case 3: calendar.day += 'rd'; break;
		case 21: calendar.day += 'st'; break;
		case 22: calendar.day += 'nd'; break;
		case 23: calendar.day += 'rd'; break;
		case 31: calendar.day += 'st'; break;
		default: calendar.day += 'th'; break;
	}
	
	return calendar.month + ' ' + calendar.day + ', ' + calendar.year;
}

setInterval(function(){$('.clock').text(clock())}, 1000);
function clock(){
	clock.time = new Date();
	clock.hours = clock.time.getHours();
	clock.minutes = clock.time.getMinutes();
	clock.seconds = clock.time.getSeconds();
	
	// Leading zeroes.
	if(clock.minutes < 10) clock.minutes = '0' + clock.minutes;
	if(clock.seconds < 10) clock.seconds = '0' + clock.seconds;
	
	// Calculate ante meridiem or post meridiem.
	if(clock.hours > 12){
		clock.meridiem = 'PM';
		clock.hours -= 12;
	}else{
		clock.meridiem = 'AM';
	}
	
	// Zero hour is midnight.
	if(clock.hours == 0) clock.hours = 12;
	
	return clock.hours + ':' + clock.minutes + ':' + clock.seconds + ' ' + clock.meridiem;
}

/* ########################################################################## */
/* #### Imageboard ########################################################## */
/* ########################################################################## */
$(document).on("click", ".thumb", function(event){
	event.preventDefault();
	$(this).toggleClass("thumb-big");
	$(this).css("background-image", $(this).css("background-image").replace("/thumb", ""));
	$(this).parent().find('.post-info').toggleClass("post-info-big");
});

/* ########################################################################## */
/* #### Testing Area ######################################################## */
/* ########################################################################## */
function test(){
	if(typeof test.element !== 'object' || test.element.length < 1) test.element = $('.header td');
	var index = Math.floor(Math.random() * ((test.element.length - 1) + 1));
	test.element[index].style.animation = '.125s electrify';
	setTimeout(function(){test.element[index].removeAttribute('style')}, 130);
	setTimeout(test, Math.floor(Math.random() * (10000 - 2000 + 1)) + 2000);
}
document.onload = test();

/* ########################################################################## */
/* #### Chatbox ############################################################# */
/* ########################################################################## */

$(document).on("click", ".chat-link", function(event){
	event.preventDefault();
	$("#chat").toggle("fast");
});
/*
$(document).on("click", "#chat-submit", function(event) {
		event.preventDefault();
		if($("#chat-message").val() == '') return false;
		var message = $("#chat-message").val();
		$.post("/includes/chat/chat.php", {text: message});
		$("#chat-message").attr("value", null);
		$("#chat-message").value = null;
		document.getElementById("chat-message").value = null;
		loadLog();
});

//Load the file containing the chat log
setInterval (loadLog, 2500);
function loadLog(){
	//Scroll height before the request
	var oldScrollHeight = document.getElementById("chat-messages").scrollHeight - 20;
	$.ajax({
		url: "/includes/chat/log.html",
		success: function(html){
			//Insert chat log into the #chat-messages div
			$("#chat-messages").html(html);
			var newScrollHeight = document.getElementById("chat-messages").scrollHeight - 20;
			if(newScrollHeight > oldScrollHeight){
				//Autoscroll to bottom of div
				$("#chat-messages").animate({scrollTop: newScrollHeight}, 'normal');
			}
		}
	});
}
*/

/* ########################################################################## */
/* #### Chatbox v2########################################################### */
/* ########################################################################## */
$(document).ready(function(){
	//create a new WebSocket object.
	var wsUri = "ws://" + websocket_ip + ":9000/chat";
	websocket = new WebSocket(wsUri);
	
	websocket.onopen = function(ev) {
		$('.chat-messages').append("<div class=\"system_msg\">Connected!</div>");
	}
	
	$('form[name=chat-form]').submit(function(event){
		event.preventDefault();
		var message = $('.chat-message').val();
		var name = $('#username').text();
		
		if(name == "") return;
		if(message == "") return;
		
		var msg = {name: name, message: message};
		
		websocket.send(JSON.stringify(msg));
		$(".chat-messages").scrollTop($(".chat-messages")[0].scrollHeight);
		//for (el of document.getElementsByClassName("chat-messages")) el.scrollTop = el.scrollHeight;
	});
	
	//#### Message received from server?
	websocket.onmessage = function(ev) {
		var msg = JSON.parse(ev.data);
		var type = msg.type;
		var umsg = msg.message;
		var uname = msg.name;

		if(type == 'usermsg'){
			$('.chat-messages').append("<div><span class=\"user_name\">"+uname+"</span> : <span class=\"user_message\">"+umsg+"</span></div>");
		}
		if(type == 'system'){
			$('.chat-messages').append("<div class=\"system_msg\">"+umsg+"</div>");
		}
		
		$('.chat-message').val(''); //reset text
		
		$(".chat-messages").scrollTop($(".chat-messages")[0].scrollHeight);
		//for (el of document.getElementsByClassName("chat-messages")) el.scrollTop = el.scrollHeight;
	};
	
	websocket.onerror = function(ev){$('.chat-messages').append("<div class=\"system_error\">Error Occurred - "+ev.data+"</div>");}; 
	websocket.onclose = function(ev){$('.chat-messages').append("<div class=\"system_msg\">Connection Closed</div>");}; 
});






/* ########################################################################## */
/* #### Navigation Bar ###################################################### */
/* ########################################################################## */

// Throttle prevents lag.
$(window).scroll($.throttle(100, navbar));

// Only show the navbar if we need it.
function navbar(){
	if(typeof navbar.element === 'undefined'){
		navbar.element = $('.navbar');
		navbar.headerHeight = $('.header').height();
	}
	var viewOffset = $(window).scrollTop();
	
	if(viewOffset > navbar.headerHeight){
		navbar.element.addClass('navbar-show');
	}else if(navbar.element.hasClass('navbar-show')){
		navbar.element.removeClass('navbar-show');
	}
}

/* ########################################################################## */
/* #### Left aside link highlighter ######################################### */
/* ########################################################################## */
if(typeof highlightLink !== 'function') highlightLink = setInterval(function(){
	$('#left-box a').each(function(){
		this.href == window.location.href ? $(this).css('color', 'white') : $(this).removeAttr('style')
	});
}, 100);
/* ########################################################################## */
/* #### Top-link smooth scroll ############################################## */
/* ########################################################################## */
document.getElementById("top-link").onclick = function(event){
	event.preventDefault();
	window.scrollTo(0, 0);
}