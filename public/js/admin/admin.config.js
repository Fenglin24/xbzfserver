var adminConfig = {
	initPage: function() {
		var self = this;
		self.initBtns(self);
	},
	initBtns: function(self) {
		self.initEditBtn(self);
		// self.initDeleteBtn(self);
	},
	initEditBtn: function(self) {
		$('.edit').click(function() {
			var trDom = $(this).parents('tr');
			var id = trDom.attr('data-id');
			var form = $('#dialog_tpl');
			form.find('#id').attr('value', id);
			form.find('#value').text(trDom.find('.value').text());
			form.find('.comment').text(trDom.find('.comment').text());
			form.find('.help').text(trDom.find('.help').text());
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
				value: form.find('#value').val()
			};
			http.ajax(baseURL + '?s=admin/config/save', data, function(res) {
				if (res.code == 0) {
					$('#mainTable').find('tr[data-id="'+data.id+'"]').find('.value').text(res.data.value);
					pop.closeAll();
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
				http.ajax(baseURL + '?s=admin/config/delete', data, function(res) {
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
	adminConfig.initPage();
});