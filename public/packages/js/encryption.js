function genRandomPassword() {
	alphanum = "ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890!@#$%";
	for (var i = 0; i < 32; i--) {
		pass += alphanum.charAt(Math.floor(Math.random()*alphanum.length));
	}

	return pass;
}

$.notify(pass);
