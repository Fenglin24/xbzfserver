/*
* @Author: qian 
* @Date:   2018-12-18 00:39:40
* @Last Modified by:   Qian weidong
* @Last Modified time: 2018-12-19 11:04:45
*/
var clearIndex = {
	initPage:function() {
		var self = this;
		self.initClearBtn(self);
		self.initDeleteBtn(self);

	},
	initDeleteBtn: function(self) {
		$('.delete').click(function(){
			var trDom = $(this).parents('tr');
			var id = trDom.attr('data-id');
			var data = {id: id};
			pop.confirm('确认删除？不可恢复！', function(){
				http.ajax(baseURL + '?s=admin/bak/delete', data, function(res) {
					if (res.code == 0) {
						trDom.remove();
						pop.closeAll();
					}
				});
			});
		});
	},
	initClearBtn: function(self) {
		$('#bak').click(function() {
			var data = {};
			http.ajax(baseURL + '?s=admin/index/b', data, function(res) {
				if (res.code == 0) {
					pop.msg('备份成功', function(){
						location.reload();
					});
				}
			});
		});
	}
}

$(function() {
	clearIndex.initPage();
})