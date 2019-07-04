function getFormLinesHtml(f) {　
	return f.toString().replace(/^[^\/]+\/\*!?\s?/, '').replace(/\*\/[^\/]+$/, '');
}
var modifyPasswordHtml = getFormLinesHtml(function() {
	/*
		<form  id="modify_password_dialog" 
		class="form-horizontal" 
		style="margin-top:20px;width:500px;"  
		onsubmit="return false;">
		     <div class="form-group" style="margin:10px 0;">
		        <label for="oldpassword" class="col-md-3 control-label">输入旧密码</label>
		        <div class="col-md-8">
		            <input type="password" class="form-control" id="oldpassword" placeholder="旧密码" value="">
		        </div>
		    </div>
		    
		     <div class="form-group" style="margin:10px 0;">
		        <label for="new_password" class="col-md-3 control-label">输入新密码</label>
		        <div class="col-md-8">
		            <input type="password" class="form-control" id="new_password" placeholder="新密码" value="">
		        </div>
		    </div>
		    
		     <div class="form-group" style="margin:10px 0;">
		        <label for="confirm_password" class="col-md-3 control-label">重复新密码</label>
		        <div class="col-md-8">
		            <input type="password" class="form-control" id="confirm_password" placeholder="新密码" value="">
		        </div>
		    </div>
		    
		     <div class="form-group" style="margin:30px 0 30px 0;">
		        <div class="col-md-12 text-center">
		        	<button class="btn btn-primary" id="submit_modify_password">提　交</button>
		        	　
		        	<button class="btn btn-default" id="close_modify_password">关　闭</button>
		        </div>
		    </div>
		</form>
	*/
});

var modifyNickHtml = getFormLinesHtml(function() {
	/*
				<form  id="modify_password_dialog" 
				class="form-horizontal" 
				style="margin-top:20px;width:400px;"  
				onsubmit="return false;">
				     <div class="form-group" style="margin:10px 0;">
				        <label for="nick" class="col-md-3 control-label">新昵称：</label>
				        <div class="col-md-8">
				            <input type="text" maxlength="16" class="form-control" id="nick" placeholder="" value="">
				        </div>
				    </div>
				    
				     <div class="form-group" style="margin:30px 0 30px 0;">
				        <div class="col-md-12 text-center">
				        	<button class="btn btn-primary" id="submit_modify_nick">提　交</button>
				        	　
				        	<button class="btn btn-default" id="close_modify_nick">关　闭</button>
				        </div>
				    </div>
				</form>
	*/
});

var headerPage = {
	init: function() {
		var self = this;
		self.initLeftMenuActive(self);
		self.initSearchMenuBtn(self);
		self.initModifyPassword(self);
		self.initModifyNickName(self);
	}, 
	initLeftMenuActive: function(self) {
		var href = getQueryString('s');
		if (!href) {
			return;
		}
		$('#leftAdminMenu a').each(function(){
			var s = getQueryString('s', $(this).attr('href'));
			if (!s) {
				return;
			}
			if (s.toLowerCase() == href.toLowerCase() || 
				s.toLowerCase() == getQueryString('ps')
				) {
				$(this).parent().addClass('active');
				if ($(this).parents('ul.submenu').length > 0) {
					$(this).parents('ul.submenu').parent().addClass('active open');
				}
			}
		});
	}, 
	initSearchMenuBtn: function(self) {
		var menuDom = $('#leftAdminMenu');
		$('#searchMenuBtn').keyup(function(){
			var keyword = $(this).val().trim();
			if (keyword == '') {
				menuDom.find('li').attr('data-show', 1).show();
				return;
			}
			menuDom.find('li a').each(function(){
				var text = $(this).text().trim();
				if (text.indexOf(keyword) == -1) {
					$(this).parent().attr('data-show', 0).hide();
				} else {
					$(this).parent().attr('data-show', 1).show();
				}
			});
			$('.submenu').each(function(){
				if ($(this).find('[data-show="0"]').length != $(this).find('li').length) {
					$(this).parents('li').show();
				}
			});
		});
	}, 
	checkPasswordData: function(self, data) {
		if (data.oldpassword == '') {
			pop.msg('请输入旧密码！', function() {
				$('#oldpassword').focus();
			});
			return false;
		}

		if (data.new_password == '') {
			pop.msg('请输入新密码！', function() {
				$('#new_password').focus();
			});
			return false;
		}

		if (data.confirm_password == '') {
			pop.msg('请再次输入新密码！', function() {
				$('#confirm_password').focus();
			});
			return false;
		}

		if (data.confirm_password != data.new_password) {
			pop.msg('两次输入的新密码不一致！', function() {
				$('#new_password').focus();
			});
			return false;
		}
		return true;
	},
	initModifyPassword: function(self) {
		$('.modify_password').click(function(event) {
			pop.diyPage({
				title: '修改密码',
				html: modifyPasswordHtml,
				area: [450]
			});
		});

		$('body').on('click', '#submit_modify_password', function() {
			var data = {
				oldpassword: $('#oldpassword').val(),
				new_password: $('#new_password').val(),
				confirm_password: $('#confirm_password').val()
			};
			if (self.checkPasswordData(self, data) == false) {
				return false;
			}
			data.oldpassword = md5(data.oldpassword);
			data.new_password = data.confirm_password = md5(data.new_password);
			http.ajax(baseURL + '?s=Admin/Admin/modify_password', data, function(res){
				if (res.code == '0') {
					pop.msg('密码修改成功！下次请使用新密码登陆。', function(){
						pop.closeAll();
					});
				}
			});
		});
		
		$('body').on('click', '#close_modify_password', function(){
			pop.closeAll();
		});
	}, 
	initModifyNickName: function(self) {
		$('.modify_nick').click(function(event) {
			pop.diyPage({
				title: '修改昵称',
				id: 'modifyNickDialog',
				html: modifyNickHtml,
				area: [450]
			});
			var oldNick = $('#showNickOrUserName').text();
			$('#modifyNickDialog #nick').attr('value', oldNick).val(oldNick);
		});
		
		$('body').on('click', '#submit_modify_nick', function() {
			var data = {
				nick: $('#nick').val()
			};
			http.ajax(baseURL + '?s=Admin/Admin/modify_nick', data, function(res){
				if (res.code == '0') {
					pop.msg('昵称修改成功！', function(){
						$('#showNickOrUserName').text(data.nick);
						pop.closeAll();
					}, 1);
				}
			});
		});
		
		$('body').on('click', '#close_modify_nick', function(){
			pop.closeAll();
		});
	}
};

$(function(){
	headerPage.init();
});