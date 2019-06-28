var adminIndex = {
	initPage: function() {
		var self = this;
		self.initBtns(self);
	},
	initBtns: function(self) {
		self.initAddBtn(self);
		self.initEditBtn(self);
		self.initDeleteBtn(self);
		self.initLhBtn(self);
	},
	initEditBtn: function(self) {
		$('.edit').click(function() {
			var trDom = $(this).parents('tr');
			var form = $('#dialog_tpl');
			self.setTplFormDataFromTr(form, trDom);
			
			self.initEditDialog(self);
		});
	},
	setTplFormDataFromTr: function(form, trDom) {
		var id = trDom.attr('data-id');
		form.find('input').each(function(){
			var id = $(this).attr('id');
			var value = $.trim(trDom.find('.' + id).text() );
			$(this).attr('value', value);
		});
		form.find('#id').attr('value', id);
		form.find('#role_id option').removeAttr('selected');
		form.find('#role_id option[value="'+trDom.find('.role').data('id')+'"]').attr('selected', 'selected');
	},
	initAddBtn: function(self) {
		$('.add').click(function() {
			var form = $('#dialog_tpl');
			form.find('#id').attr('value', '0');
			form.find('#username').attr('value', '');
			form.find('#nick').attr('value', '');
			form.find('#password').attr('value', '');
			form.find('#nick').attr('value', '');
			form.find('#tel').attr('value', '');
			form.find('#email').attr('value', '');
			self.initEditDialog(self);
		});
	},
	initEditDialog: function(self) {
		var form = $('#dialog_tpl');
		pop.diyPage({
			id: 'editFormPage',
			title: '编辑用户',
			html: form.html()
		});

		$('#editFormPage #submit').click(function() {
			var data = $(this).parents('form').serializeObject();
			if (data.password != '') {
				// data.password = md5(data.password);
			}
			var action = data.id > 0 ? 'update' : 'update';
			http.ajax(baseURL + '?s=admin/user/' + action, data, function(res) {
				if (res.code == 0) {
					pop.msg('保存成功', function(){
						location.reload();
					});
				}
			});
		});
	}, 
	initDeleteBtn: function(self) {
		$('.delete').click(function(){
			var trDom = $(this).parents('tr');
			var id = trDom.attr('data-id');
			var data = {id: id};
			pop.confirm('确认删除？不可恢复！', function(){
				http.ajax(baseURL + '?s=admin/user/delete', data, function(res) {
					if (res.code == 0) {
						trDom.remove();
						pop.closeAll();
					}
				});
			});
		});
	},
	initLhBtn: function(self) {
		$('.lh').click(function(){
			var trDom = $(this).parents('tr');
			var id = trDom.attr('data-id');
			var data = {id: id};
			pop.confirm('确认拉黑？', function(){
				http.ajax(baseURL + '?s=admin/user/lh', data, function(res) {
					if (res.code == 0) {
						// trDom.remove();
						location.reload();
					}
				});
			});
		});
	}
};

$(function() {
	adminIndex.initPage();
});