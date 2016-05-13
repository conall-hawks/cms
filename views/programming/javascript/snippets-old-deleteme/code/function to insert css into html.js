##################################################
This function easily changes css:
##################################################

function css(selector, property, value) {
    for (var i=0; i<document.styleSheets.length;i++) {//Loop through all styles
        //Try add rule
        try { document.styleSheets[i].insertRule(selector+ ' {'+property+':'+value+'}', document.styleSheets[i].cssRules.length);
        } catch(err) {try { document.styleSheets[i].addRule(selector, property+':'+value);} catch(err) {}}//IE
    }
}


##################################################
Some examples of usage:
##################################################

<div id="box" class="boxes" onmouseover="css('#box', 'color', 'red')">Mouseover Me!</div>

Or:

<div class="boxes" onmouseover="css('.boxes', 'color', 'green')">Mouseover Me!</div>

Or:

<div class="boxes" onmouseover="css('body', 'border', '1px solid #3cc')">Mouseover Me!</div>

##################################################
The same function spread out for readability:
##################################################

function css(selector, property, value) {
	try {
		for (var i=0; i<document.styleSheets.length; i++) {
			try {
				document.styleSheets[i].insertRule(selector+ ' {'+property+':'+value+'}', document.styleSheets[i].cssRules.length);
			}
			
			catch(err) {
				try {
					document.styleSheets[i].addRule(selector, property+':'+value);
				}
				
				catch(err) {
				}
			}
		}
	}
	catch(err) {
	}
}