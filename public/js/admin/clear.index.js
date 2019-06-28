/*
* @Author: qian 
* @Date:   2018-12-18 00:39:40
* @Last Modified by:   qian
* @Last Modified time: 2018-12-18 01:02:44
*/
var clearIndex = {
	initPage:function() {
		var self = this;
		self.initClearBtn(self);
	},
	initClearBtn: function(self) {
		$('#clear').click(function() {
			var data = {};
			http.ajax(baseURL + '?s=admin/index/c', data, function(res) {
				if (res.code == 0) {
					pop.msg('清除成功', function(){
						// location.reload();
					});
				}
			});
		});
	}
}

$(function() {
	clearIndex.initPage();
})