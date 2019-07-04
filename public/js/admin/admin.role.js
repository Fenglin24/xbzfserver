var adminRole = {
	initPage: function() {
		var self = this;
		self.initBtns(self);
	},
	initBtns: function(self) {
		self.initAddBtn(self);
		self.initEditBtn(self);
		self.initDeleteBtn(self);
		self.initAuthorityBtns(self);
	},
	initEditBtn: function(self) {
		$('.edit').click(function() {
			var trDom = $(this).parents('tr');
			var id = trDom.attr('data-id');
			var form = $('#dialog_tpl');
			form.find('#id').attr('value', id);
			form.find('input[type="text"]').each(function(){
				var key = $(this).attr('id');
				var value = trDom.find('.' + key).text();
				$(this).attr('value', value);
			});
			self.initEditDialog(self);
		});
	},
	initAddBtn: function(self) {
		$('.add').click(function() {
			var form = $('#dialog_tpl');
			form.find('#id').attr('value', '0');
			form.find('input[type="text"]').each(function(){
				$(this).attr('value', '');
			});
			self.initEditDialog(self);
		});
	},
	initEditDialog: function(self) {
		var form = $('#dialog_tpl');
		pop.diyPage({
			id: 'editFormPage',
			title: '编辑',
			html: form.html()
		});

		$('#editFormPage #submit').click(function() {
			var form = $(this).parents('form');
			var data = {
				id: form.find('#id').val(),
			};
			form.find('input[type="text"],select').each(function(){
				var key = $(this).attr('id');
				var value = $(this).val();
				data[key] = value;
			});
			http.ajax(baseURL + '?s=admin/admin/role_save', data, function(res) {
				if (res.code == 0) {
					location.reload();
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
				http.ajax(baseURL + '?s=admin/admin/role_delete', data, function(res) {
					if (res.code == 0) {
						trDom.remove();
						pop.closeAll();
					}
				});
			});
		});
	}, 
	initAuthorityBtns: function(self) {
		$(".edit_authority").click(function() {
			var routeTpl = $('#tip_tpl');
			var tr = $(this).parents('tr');
			var id = tr.data('id');
			var authorities = $(this).attr('data-authority').toString().split(',');
			routeTpl.find(':checkbox').removeAttr('checked');
			for (var i in authorities) {
				var group_id = authorities[i];
				routeTpl.find(':checkbox[value="'+group_id+'"]').attr('checked', 'checked');
			}
			routeTpl.find('#id').attr('value', id);
			pop.diyPage({
				id: 'editRouteGroupPage',
				title: '编辑',
				html: routeTpl.html()
			});
			var dom = $('#editRouteGroupPage');
			dom.find('#submit').click(function(){
				var id = dom.find('#id').val();
				var group_ids = [];
				dom.find(':checkbox:checked').each(function(){
					var name = $(this).val();
					group_ids.push(name);
				});
				var data = {
					id: id,
					authority: group_ids.join(',')
				}
				http.ajaxPost(baseURL+'?s=admin/admin/role_save', data, function(res){
					if (res.code == 0) {
						pop.msg('保存成功！', function(){
							var tr = $('#mainTbBody tr[data-id="'+res.data.id+'"]');
							tr.find('.edit_authority').attr('data-authority', res.data.authority);
							pop.closeAll();
						});
					}
				})
			});
		});
	}
};

$(function() {
	adminRole.initPage();
});
