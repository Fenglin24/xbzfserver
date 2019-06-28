var routeGroup = {
	initPage: function() {
		var self = this;
		self.initBtns(self);
	},
	initBtns: function(self) {
		self.initAddBtn(self);
		self.initEditBtn(self);
		self.initDeleteBtn(self);
		self.initAddRoute(self);
		self.initDelRoute(self);
	},
	initDelRoute: function(self) {
		$('#mainTbBody').on('click', '.del_route', function() {
			var id = $(this).parents('tr').data('id');
			var del_li = $(this).parent();
			var route_names = [];
			$(this).parents('ul').find('li').each(function(){
				var name = $(this).data('name');
				if (name == del_li.data('name')) {
					return;
				}
				route_names.push(name);
			});
			var data = {
				id: id, 
				route_names: route_names.join(',')
			};
			http.ajax(baseURL + '?s=admin/route/save_group', data, function(res) {
				if (res.code == 0) {
					del_li.remove();
				}
			});
		});
	},
	initAddRoute: function(self) {
		$('#mainTbBody').on('click', '.add_route', function() {
			var tr = $(this).parents('tr');
			var id = tr.data('id');
			var routeTpl = $('#tip_tpl');
			routeTpl.find('input:checkbox').removeAttr('checked');
			tr.find('.route_ids li').each(function(){
				var name = $(this).data('name');
				routeTpl.find('input:checkbox[value="'+name+'"]').attr('checked', 'checked');
			});
			routeTpl.find('#id').attr('value', id);
			pop.diyPage({
				id: 'editRoutePage',
				title: '编辑',
				html: routeTpl.html()
			});
			var dom = $('#editRoutePage');
			dom.find('#submit').click(function(){
				var id = dom.find('#id').val();
				var route_names = [];
				dom.find(':checkbox:checked').each(function(){
					var name = $(this).val();
					route_names.push(name);
				});
				var data = {
					id: id, 
					route_names: route_names.join(',')
				};
				http.ajax(baseURL + '?s=admin/route/save_group', data, function(res) {
					if (res.code == 0) {
						pop.closeAll();
						var tr = $('#mainTbBody').find('tr[data-id="'+id+'"]');
						tr.find('.route_ids li').remove();
						var routes = res.data.routes;
						var ul = tr.find('.route_ids');
						for (var i in routes) {
							var route = routes[i];
							ul.append('<li data-name="'+route.name+'">' +
								route.op_name + ': ' + route.name +
								'<a href="javascript:" class="del_route">'+ 
								'<i class="icon-trash"></i> 删除</a>' + 
                            	'</li>');
						}
					}
				});
			});
		});
		
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
		form.find('#id').attr('value', $.trim(tr.attr('data-id')));
		form.find('#name').attr('value', $.trim(tr.find('.name').text()));
		form.find('#menu_id option').removeAttr('selected');
		form.find('#menu_id option[value="'+tr.attr('data-menu_id')+'"]').attr('selected', 'selected');
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
			if (!data.name) {
				pop.msg('请填写路由！');
				return false;
			}
			http.ajax(baseURL + '?s=admin/route/save_group', data, function(res) {
				if (res.code == 0) {
					var data = res.data;
					var dom = $('#mainTbBody').find('[data-id="'+data.id+'"]');
					if (dom.length > 0) {
						dom.attr('data-menu_id', data.menu_id);
						dom.find('.name').text(data.name);
						dom.find('.menu_name').text(data.menu_name);
					} else {
						var dom = $('#tplTable tr').clone();
						dom.attr('data-id', data.id);
						dom.attr('data-menu_id', data.menu_id).data('menu_id', data.menu_id);
						dom.find('.menu_name').text(data.menu_name);
						dom.find('.name').text(data.name);
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
			var id = trDom.data('id');
			var data = {id: id};
			pop.confirm('确认删除？不可恢复！', function(){
				http.ajax(baseURL + '?s=admin/route/del_group', data, function(res) {
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
	routeGroup.initPage();
});