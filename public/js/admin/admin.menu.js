var adminMenu = {
	initPage: function() {
		var self = this;
		self.initBtns(self);
	},
	initBtns: function(self) {
		self.initAddBtn(self);
		self.initEditBtn(self);
		self.initDeleteBtn(self);
		self.initSequence(self);
		self.initCheckHidden(self);
	},
	initCheckHidden: function(self) {
		$('.check_hidden').change(function(){
			var id = $(this).parents('tr').data('id');
			var hidden = $(this).is(':checked') ? 1 : 0;
			var data = {
				id: id, 
				hidden: hidden
			};
			http.ajax(baseURL + '?s=admin/admin/menu_save', data, function(res) {
			});
		})
	},
	initSequence: function(self) {
		$('.sequence').change(function(){
			var data = {
				id: $(this).parents('tr').data('id'),
				sequence: $(this).val()
			};
			http.ajax(baseURL + '?s=admin/admin/menu_save', data, function(res) {
			});
		});
	},
	initEditBtn: function(self) {
		$('.edit').click(function() {
			var tr = $(this).parents('tr');
			var form = $('#dialog_tpl');
			self.initTplFormByTr(self, form, tr);
			self.initEditDialog(self);
		});
	},
	initTplFormByTr: function(self, form, tr) {
		form.find('#id').attr('value', tr.attr('data-id'));
		form.find('#icon').attr('value', tr.attr('data-icon'));
		form.find('#name').attr('value', $.trim(tr.find('.name').text()) );
		form.find('#route').attr('value', $.trim(tr.find('.route').text()) );
		form.find('#pid option').removeAttr('selected');
		form.find('#pid option[value="'+tr.data('pid')+'"]').attr('selected', 'selected');
	},
	initAddBtn: function(self) {
		$('.add').click(function() {
			var form = $('#dialog_tpl');
			form.find('input[type="text"]').each(function(){
				$(this).attr('value', '');
			});
			form.find('#id').attr('value', '0');
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
			var data = $(this).parents('form').serializeObject();
			http.ajax(baseURL + '?s=admin/admin/menu_save', data, function(res) {
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
				http.ajax(baseURL + '?s=admin/admin/menu_delete', data, function(res) {
					if (res.code == 0) {
						trDom.remove();
						pop.closeAll();
					}
				});
			});
		});
	}
};

$(function() {
	adminMenu.initPage();
});