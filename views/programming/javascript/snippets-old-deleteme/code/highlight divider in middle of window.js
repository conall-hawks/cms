//NEEDS WORK, TOO SLOW
//Highlights content divider currently in the middle of the screen
$(window).scroll($.throttle(10, function() {
	//Find the middle of the window (the "middle" here is actually 1/3rd from the top)
	var scrollMiddle = $(window).scrollTop() + ($(window).height() / 2);
	$(".contentDiv").each(function() {
		var currentDiv = $(this);
		//Find the position of each divider
		elTop = currentDiv.offset().top;
		elBtm = elTop + currentDiv.height();
		
		//Highlight the divider positioned in the middle of the window
		if (elTop < scrollMiddle && elBtm > scrollMiddle && currentDiv.height() > 50) {
			currentDiv.css("background","rgba(128, 128, 255, .125)");
			currentDiv.css("border","1px solid white");
			currentDiv.css("border","1px solid rgba(0, 255, 255, .25)");
		} else {
			currentDiv.css("background","initial");
			currentDiv.css("border","initial");
			currentDiv.css("border","initial");
		}
	});
}));