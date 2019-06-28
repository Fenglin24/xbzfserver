var routeName = {
	initPage: function() {
		var self = this;
		self.initBtns(self);
	},
	initBtns: function(self) {
		self.initAddBtn(self);
		self.initEditBtn(self);
		self.initDeleteBtn(self);
		self.initIgnoreBtn(self);
	},
	initIgnoreBtn: function(self) {
		$('#mainTbBody').on('change', '.ignore', function(){
			var ignore = $(this).is(':checked') ? 1 : 0;
			var data = {
				'name' : $(this).parents('tr').attr('data-id'),
				'ignore' : ignore
			};
			http.ajax(baseURL + '?s=admin/route/save_route', data, function(res) {
			});
		})
	},
	initEditBtn: function(self) {
		$('#mainTbBody').on('click', '.edit', function() {
			var tr = $(this).parents('tr');
			var form = $('#dialog_tpl');
			self.initTplFormByTr(self, form, tr);
			self.initEditDialog(self);
		});
	},
	initTplFormByTr: function(self, form, tr) {
		form.find('#name').attr('value', $.trim(tr.find('.name').text())).attr('readonly', 'readonly');
		form.find('#op_name').attr('value', $.trim(tr.find('.op_name').text()) );
		var menu_id = tr.attr('data-menu_id');
		form.find('#menu_id option').removeAttr('selected');
		form.find('#menu_id option[value="'+tr.attr('data-menu_id')+'"]').attr('selected', 'selected');
	},
	initAddBtn: function(self) {
		$('.add').click(function() {
			var form = $('#dialog_tpl');
			form.find('input[type="text"]').each(function(){
				$(this).attr('value', '');
			});
			form.find('#name').removeAttr('readonly');
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
			if (!data.name) {
				pop.msg('请填写路由！');
				return false;
			}
			http.ajax(baseURL + '?s=admin/route/save_route', data, function(res) {
				if (res.code == 0) {
					var data = res.data;
					var dom = $('#mainTbBody').find('[data-id="'+data.name+'"]');
					if (dom.length > 0) {
						dom.find('.menu_name').text(data.menu_name);
						dom.find('.name').text(data.name);
						dom.find('.op_name').text(data.op_name);
					} else {
						var dom = $('#tplTable tr').clone();
						dom.attr('data-id', data.name);
						dom.find('.menu_name').text(data.menu_name);
						dom.find('.name').text(data.name);
						dom.find('.op_name').text(data.op_name);
						$('#mainTbBody').prepend(dom);
					}
					pop.closeAll();
				}
			});
		});
	}, 
	initDeleteBtn: function(self) {
		$('#mainTbBody').on('click', '.delete', function(){
			var trDom = $(this).parents('tr');
			var name = trDom.find('.name').text().trim();
			var data = {routes: [name]};
			pop.confirm('确认删除？不可恢复！', function(){
				http.ajax(baseURL + '?s=admin/route/del_routes', data, function(res) {
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
	routeName.initPage();
});