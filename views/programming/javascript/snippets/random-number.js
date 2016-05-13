// Returns a random integer between min (included) and max (included)
// Using Math.round() will give you a non-uniform distribution!
// 
// Reference: https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Math/random
function rand(min = 0, max = 1){
	return Math.floor(Math.random() * (max - min + 1)) + min;
}