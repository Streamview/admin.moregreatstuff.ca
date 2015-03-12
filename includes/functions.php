<?php
function isLive () {
	if (gethostname() != 'airportruns') {
		return true;
	}
}

function isDev () {
    return !isLive();
}

function isValidEmail ($email) {
	return filter_var($email, FILTER_VALIDATE_EMAIL);
}
?>