// Escapes text
function escapeHtml(text){
	return text.replace(/[&<>"']/g, function(c){return {
		'&': '&amp;', 
		'<': '&lt;', 
		'>': '&gt;', 
		'"': '&quot;', 
		"'": '&#039;'
	}[c]});
}