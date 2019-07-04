var adminNewsIndex = {
	initPage: function() {
		var self = this;
		self.initDeleteBtn(self);
		self.initXxBtn(self);
	}, 
	initXxBtn: function(self) {
		$('.status').click(function() {
			var id = $(this).parents('tr').attr('data-id');
			var data = {
				'id': id
			};
			http.ajax(baseURL + '?s=admin/ad/xx', data, function(res){
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
			pop.confirm('确认删除？不可恢复', function(){
				http.ajax(baseURL + '?s=admin/ad/delete', data, function(res){
					if (res.code == 0) {
						trDom.remove();
						pop.closeAll();
					}
				});
			});
		});
	}
};

$(function(){
	adminNewsIndex.initPage();
});