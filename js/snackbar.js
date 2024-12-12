class SnackBar {
	static show(message, timeout = 3_000) {
		var snackbar = document.getElementById('snackbar');
		snackbar.innerHTML = message;
		snackbar.className = 'show';
		setTimeout(function () {
			snackbar.className = snackbar.className.replace('show', '');
		}, timeout);
	}
}
