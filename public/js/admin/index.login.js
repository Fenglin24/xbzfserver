var indexLogin = {
	'usernameDom' : null,
	'passwordDom' : null, 
	'codeDom' : null,
	initPage: function() {
		var self = this;
		self.usernameDom = $('#username');
		self.passwordDom = $('#password');
		self.codeDom = $('#code');
		self.verifyCodeDom = $('#verify_code');
		self.verifyCodeDom.empty();
		
		self.initUserLoginInfo(self);
		self.setCode(self);
		self.initBtn(self);
	},
	initUserLoginInfo: function(self) {
		var username = rememberGetDecodeValue('un');
		var password = rememberGetDecodeValue('pd');
		if (username && password) {
			$('#rememberMe').prop('checked', true);
			var username = rememberGetDecodeValue('un');
			var password = rememberGetDecodeValue('pd');
			self.usernameDom.val(username);
			self.passwordDom.val(password);
			self.verifyCodeDom.focus();
		} else {
			self.usernameDom.focus();
		}
	},
	saveUserLoginInfo: function(self, username, password) {
		if ($('#rememberMe').is(':checked')) {
			rememberSetEncodeValue('un', username);
			rememberSetEncodeValue('pd', password);
		} else {
			rememberSetEncodeValue('un', null);
			rememberSetEncodeValue('pd', null);
		}
	},
	setCode: function(self) {
		http.ajax(baseURL + '?s=admin/index/get_code', {}, function(res){
			if (res.code == 0) {
				self.codeDom.val(res.data.code);
			}
		});
	},
	initBtn: function(self) {
		$('#loginBtn').click(function() {
			var username = $.trim(self.usernameDom.val());
			var password = self.passwordDom.val();
			var code = self.codeDom.val();
			var verify_code = self.verifyCodeDom.val();
			var data = {
				username: username,
				password: password,
				code: code,
				verify_code: verify_code
			};
			if (!self.checkSubmitData(self, data)) {
				return false;
			}
			
			self.saveUserLoginInfo(self, username, password);
			
			data.password = md5(md5(code) + md5(password));
			http.ajaxPost(baseURL + '?s=admin/index/ajax_login', data, function(res){
				if (res.code == 0) {
					location.href = '/admin';
				} else {
					$('#verifyImg').click();
				}
			});
		});
	},
	checkSubmitData: function(self, data) {
		if (data.username == '') {
			pop.msg('用户名不能为空！', function(){
				self.usernameDom.focus();
			});
			return false;
		}
		
		if (data.password == '') {
			pop.msg('密码不能为空！', function(){
				self.passwordDom.focus();
			});
			return false;
		}
		
		if (self.verifyCodeDom.length && data.verify_code == '') {
			pop.msg('请输入验证码！', function(){
				self.verifyCodeDom.focus();
			});
			return false;
		}
		
		return true;
	}
};

$(function(){
	indexLogin.initPage();
});
