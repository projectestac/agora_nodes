function checkImportOptions(msg_select_option, msg_select_privacity) {
		
	var checked = 0;
	
	// gets all the input tags in frm, and their number
	var inputFields = document.getElementsByTagName('input');
	var nr_inpfields = inputFields.length;

	// traverse the inpfields elements, and adds the value of selected (checked) checkbox in selchbox
	for(var i=0; i<nr_inpfields; i++) {
		if(inputFields[i].type == 'checkbox' && inputFields[i].checked == true) {
			checked++;
		}	
	}
	
	if(checked == 0) {
		alert(msg_select_option);		
		return false;
	}
	else {
		var privacity = document.getElementById("privacity");
		var strUser = privacity.options[privacity.selectedIndex].text;
		if (strUser == '--') {
			alert(msg_select_privacity);
			return false;
		}
		return true;	
	}
	return false;
}