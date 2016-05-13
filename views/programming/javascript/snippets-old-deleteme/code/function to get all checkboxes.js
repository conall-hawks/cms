##################################################
A simple for loop which tests the checked property and appends the checked ones to a separate array. 
From there, you can process the array of checkboxesChecked further if needed.
##################################################

// pass the checkbox name to the function
function getCheckedBoxes(chkboxName) {
	var checkboxes = document.getElementsByName(chkboxName);
	var checkboxesChecked = [];
	// loop over them all
	for (var i=0; i<checkboxes.length; i++) {
		// add the checked ones into the array
		if (checkboxes[i].checked) {
			checkboxesChecked.push(checkboxes[i]);
		}
	}
	// return the array if it is non-empty, or null
	return checkboxesChecked.length > 0 ? checkboxesChecked : null;
}

##################################################
Some examples of usage:
##################################################

// Call as
var checkedBoxes = getCheckedBoxes("mycheckboxes");

##################################################
Another simple loop which does the same, except returns the value of all checkboxes as a string, instead of an array:
##################################################

// pass the checkbox name to the function
function getCheckedBoxes(chkboxName) {
	var checkboxes = document.getElementsByName(chkboxName);
	var checkboxesChecked = "";
	// loop over them all
	for (var i=0; i<checkboxes.length; i++) {
		if (checkboxes[i].checked) {
			// append comma separators
			if (checkboxesChecked) {
				checkboxesChecked += ", ";
			}
			// add the checked ones into the list
			checkboxesChecked += checkboxes[i].value;
		}
	}
	return checkboxesChecked;
}