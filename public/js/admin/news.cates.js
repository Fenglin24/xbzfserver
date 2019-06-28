/**
 * 分类管理
 * 注意点击修改时，弹出的下拉菜单id为pid，合并时复用了它，其实这个pid合并的时候应该是new_cid
 * @type {Object}
 */
var newsCates = {
	initPage: function() {
		var self = this;
		self.tree = $('#tree');
		self.dialog = $('#dialog_tpl');
		self.tableDom = $('#mainTable tbody');
		self.trDom = self.tableDom.find('tr').remove();
		self.initData(self);
		self.initBtns(self);
	},
	initBtns: function(self) {
		$('.add').click(function(){
			var clickDom = $(this);
			self.initRootCates(self);
			self.setDialogData(self, clickDom, 'add');
			pop.dialog({
				title: '添加分类',
				str: self.dialog.html(),
				btns: ['提交', '取消'],
				area: [400],
				id: 'addCateForm',
				callback: [
					function() {
						self.saveCate(self, 'addCateForm');
					},
					function() {}
				]
			});
		});
		self.tableDom.on('click', '.merge', function(event){
			var clickDom = $(this);
			if (clickDom.parents('tr').data('pid') > 0) {
				self.initAllCates(self);
			} else {
				self.initRootCates(self);
			}
			self.setDialogData(self, clickDom, 'merge');
			
			pop.dialog({
				title: '合并分类',
				str: self.dialog.html(),
				btns: ['提交', '取消'],
				area: [400],
				id: 'mergeCateForm',
				callback: [
					function() {
						self.mergeCate(self, 'mergeCateForm', clickDom);
					},
					function() {}
				]
			});
		});
		self.tableDom.on('click', '.edit', function(event) {
			var clickDom = $(this);
			self.initRootCates(self);
			self.setDialogData(self, clickDom, 'edit', clickDom);
			pop.dialog({
				title: '编辑分类',
				str: self.dialog.html(),
				btns: ['提交', '取消'],
				area: [400],
				id: 'editCateForm',
				callback: [
					function() {
						self.saveCate(self, 'editCateForm');
					},
					function() {}
				]
			});
		});
		self.tableDom.on('click', '.delete', function(event) {
			var cid = $(this).parents('tr').data('id');
			pop.confirm('确认删除？', function(){
				var data = {cid: cid};
				http.ajax(baseURL + '?s=admin/news/delete_cate', data, function(res){
					if (res.code == 0) {
						location.reload();
					}
				});
			});
				
		});
	},
	mergeCate: function(self, id, clickDom) {
		var fDom = $('#' + id);
		var my_pid = clickDom.parents('li').find('.cate').data('pid');
		var selected_pid = fDom.find('#pid option:selected').val();
		if (my_pid == selected_pid) {
			pop.closeAll();
			return;
		}
		var data = {
			'cid': fDom.find('#cid').val(),
			'pid': fDom.find('#pid').val()
		};
		
		http.ajax(baseURL + '?s=admin/news/merge_cate', data, function(res){
			if (res.code == 0) {
				location.reload();
			}
		});
	},
	saveCate: function(self, id) {
		var fDom = $('#' + id);
		var data = {
			'cid': fDom.find('#cid').val(),
			'name': fDom.find('#name').val(),
			'pid': fDom.find('#pid').val(),
			'module_id' : 1,
		};
		if (data.name == '') {
			pop.alert('名称不能为空', function() {
				fDom.find('#name').focus();
			});
			return false;
		}
		http.ajax(baseURL + '?s=admin/news/save_cate', data, function(res) {
			if (res.code == 0) {
				if (data.cid > 0) {
					var dom = self.tableDom.find('tr[data-id="' + data.cid + '"]');
					dom.find('.name').text(data.name);
					dom.data('pid', data.pid);
				} else {
					location.reload();
				}

				pop.closeAll();
			}
		});
	},
	initRootCates: function(self) {
		var dom = self.dialog.find('#pid');
		dom.find('option:gt(0)').remove();
		self.tableDom.find('tr[data-pid="0"]').each(function(){
			var cid = $(this).data('id');
			var pid = $(this).data('pid');
			var name = $(this).find('.name').text();
			dom.append('<option value="' + cid + '" data-pid="'+pid+'">' + name + '</option>');
		});
	},
	initAllCates: function(self) {
		var dom = self.dialog.find('#pid');
		dom.find('option:gt(0)').remove();
		self.tableDom.find('tr').each(function(){
			var cid = $(this).data('id');
			var pid = $(this).data('pid');
			var name = $(this).find('.name').text();
			if (pid > 0) {
				name = ' ┣ ' + name;
				dom.append('<option value="' + cid + '" data-pid="'+pid+'">' + name + '</option>');
			} else {
				dom.append('<option value="' + cid + '" data-pid="0">' + name + '</option>');
			}
		});
	},
	setDialogData: function(self, clickDom, type) {
		var cid = 0;
		var pid = 0;
		var name = '';
		if (type == 'add') { // 添加时clickDom无效
		} else {
			var pDom = clickDom.parents('tr');
			cid = pDom.data('id');
			pid = pDom.data('pid');
			name = pDom.find('.name').text();
		}
		self.dialog.find('#cid').val(cid);
		self.dialog.find('#name').attr('value', name); // 用val无法改变html内容
		self.dialog.find('#pid').removeAttr('disabled');
		self.dialog.find('#pid option').removeAttr('selected').removeAttr('disabled');
		self.dialog.find('#pid option[value="' + pid + '"]').attr('selected', 'selected');
		if (type == 'merge') {
			if (pid > 0) { // 可以将一个二级分类合并到一级分类下
				// self.dialog.find('#pid option[data-pid="0"]').attr('disabled', 'disabled');
			}
			self.dialog.find('#pid option[value="'+pid+'"],#pid option[value="'+cid+'"]').attr('disabled', 'disabled');
			self.dialog.find('div.name').hide();
		} else if (type == 'edit') {
			self.dialog.find('#pid').attr('disabled', 'disabled');
			self.dialog.find('div.name').show();
		} else if (type == 'add') {
			self.dialog.find('div.name').show();
		}
	},
	initData: function(self) {
		http.ajax(baseURL + '?s=admin/news/get_cates', {}, function(res) {
			if (res.code == 0) {
				var cates = res.data;
				var data = [];
				var index = 0;
				for (var rootcid in cates) {
					var cate = cates[rootcid];
					var tr = self.trDom.clone();
					tr.css('font-weight', 'bold');
					tr.addClass('info');
					tr.attr('data-id', rootcid);
					tr.attr('data-pid', cate.info.pid);
					tr.find('.index').text(++index);
					tr.find('.cid').text(rootcid);
					tr.find('.name').text(cate.info.name);
					tr.find('.articlenum').text(cate.articlenum);
					self.tableDom.append(tr);
					var children = cate['child'];
					for (var childcid in children) {
						var child = children[childcid];
						var tr = self.trDom.clone();
						tr.attr('data-id', childcid);
						tr.attr('data-pid', child.info.pid);
						tr.find('.index').text(++index);
						tr.find('.cid').text(childcid);
						tr.find('.name').text(child.info.name);
						tr.find('.articlenum').text(child.articlenum);
						tr.find('.childPre').text(' ┣ ');
						self.tableDom.append(tr);
					}
				}
				self.checkDeleteBtn(self);
			}
		});
	}, 
	checkDeleteBtn: function(self) {
		self.tableDom.find('tr').each(function(){
			var articlenum = $(this).find('.articlenum').text();
			if (articlenum > 0) {
				$(this).find('.delete').attr('disabled', 'disabled');
				if ($(this).data('pid') > 0) {
					self.tableDom.find('tr[data-id="'+$(this).data('pid')+'"]').find('.delete').attr('disabled', 'disabled');
				}
			}
		});
	}
};

$(function() {
	newsCates.initPage();
});