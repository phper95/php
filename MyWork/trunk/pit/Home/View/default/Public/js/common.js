var boo_common = {
		check_email : function (email){
			var reg=/^([a-zA-Z0-9_\.\-]?)*[a-zA-Z0-9\-]+@([a-zA-Z0-9]+[_|-|.]?)*[a-zA-Z0-9]+.[a-zA-Z]{2,3}$/gi;
			return reg.test(email);
		},
		
		check_pwd : function (pwd) {
			var reg = /^[\@A-Za-z0-9\!\#\$\%\^\&\*\.\~]{6,22}$/;
			return reg.test(pwd);
		},
		getStrength : function(passwd) {
			intScore = 0;
			if (passwd.match(/[a-z]/)) { // [verified] at least one lower case letter
				intScore = (intScore+1)
			} 
			if (passwd.match(/[A-Z]/)) { // [verified] at least one upper case letter
				intScore = (intScore+2)
			} // NUMBERS
			if (passwd.match(/\d+/)) { // [verified] at least one number
				intScore = (intScore+5)
			} 
			if (passwd.match(/(\d.*\d.*\d)/)) { // [verified] at least three numbers
				intScore = (intScore+5)
			} // SPECIAL CHAR
			if (passwd.match(/[!,@#$%^&*?_~]/)) { // [verified] at least one special character
				intScore = (intScore+5)
			} 
			if (passwd.match(/([!,@#$%^&*?_~].*[!,@#$%^&*?_~])/)) { // [verified] at least two special characters
				intScore = (intScore+5)
			} // COMBOS
			if (passwd.match(/[a-z]/) && passwd.match(/[A-Z]/)) { // [verified] both upper and lower case
				intScore = (intScore+2)
			} 
			if (passwd.match(/\d/) && passwd.match(/\D/)) { // [verified] both letters and numbers
				intScore = (intScore+2)
			} // [Verified] Upper Letters, Lower Letters, numbers and special characters
			if (passwd.match(/[a-z]/) && passwd.match(/[A-Z]/) && passwd.match(/\d/) && passwd.match(/[!,@#$%^&*?_~]/)) {
				intScore = (intScore+2)
			}
			return intScore;
		},
		
		showMsg : function (msg,title,close){
			title = title || false;
			if (typeof art.dialog.list['art_show_msg'] != 'undefined'){
				art.dialog.list['art_show_msg'].title(title);
				art.dialog.list['art_show_msg'].content(msg);
			} else {
				close = typeof close == 'function' ? close : function(){return true};
				art.dialog({
					id : 'art_show_msg',
					drag: false,
				    resize: false,
				    fixed:true,
					icon: 'warning',
					title : title,
				    content: msg,
				    close : close,
				    ok : function(){return true;}
				});
			}
		}
};