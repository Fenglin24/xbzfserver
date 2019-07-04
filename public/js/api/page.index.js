var apiObject = {
	initPage: function() {
		var self = this;
		self.baseURL = document.location.origin + '/';
		self.returnsDom = $('#returns');
		self.returnDataDom = $('#returnData');
		self.apiDom = $('#api');
		self.urlDom = $('#URL');
		self.paramsDom = $('#params');
		self.paramsTpl = $('#params_tpl').clone().css('display', '');
		$('#params_tpl').remove();
		self.returnsTpl = $('#returns_tpl').clone().css('display', '');
		$('#returns_tpl').remove();
		self.initHtmlData(self);
		self.initBtn(self);
	},
	initHtmlData: function(self) {
		$.ajax({
			type: 'POST',
			dataType: 'json',
			url: '?s=api/api/getApiList',
			data: {},
			success: function(res) {
				self.api = res.api;
				var dom = self.apiDom;
				for (var key in self.api) {
					var item = self.api[key];
					var html = '<option value="' + key + '" disabled="disabled">' + item.name + '</option>';
					dom.append(html);
					for (var action in item.api) {
						var html = '<option value="' + action + '">　' + item.api[action].name + '</option>';
						dom.append(html);
					}
				}
				self.baseURL = document.location.origin + res.baseURL;
				self.apiDom.change(function() {
					self.returnDataDom.empty();
					self.setParams(self);
					self.setReturn(self);
					self.setUrl(self, $(this).val());
				}).change();
			},
			error: function() {
				alert('error!');
			}
		});

	},
	// 选择一个API以后，把URL初始化
	setUrl: function(self, action) {
		var api = self.api;
		var url = self.baseURL + '?s=';
		var controller = $('#api option:selected').prevAll('option[disabled="disabled"]').val();
		var thisApi = api[controller];
		if (thisApi.hasOwnProperty('module')) {
			url += thisApi.module + '/' + controller + '/' + action;
		}
		url = '<a target="_blank" href="' + url + '">' + url + '</a>'
		self.urlDom.html(url);
	},
	// 选择一个API以后，把参数列表初始化
	setParams: function(self) {
		var api = self.api;
		var k1 = $('#api option:selected').prevAll('option[disabled="disabled"]').val();
		var k2 = $('#api').val();
		var params = api[k1]['api'][k2]['params'];
		$('#apiInfo').text(api[k1]['api'][k2].hasOwnProperty('info') ? api[k1]['api'][k2].info : '');
		self.paramsDom.empty();
		for (var key in params) {
			var item = self.paramsTpl.clone();
			item.find('th').text(key);
			item.find('input.param').attr('name', key);
			if (typeof(params[key]) == 'string') {
				item.find('.info').text(params[key]);
				if (window.localStorage && window.localStorage.hasOwnProperty(key)) {
					item.find('input.param').val(localStorage[key]);
				}
			} else {
				item.find('.info').text(params[key]['desc']);
				item.find('td.paramTD').empty().html(params[key]['html']);
			}

			self.paramsDom.append(item);
		}

	},
	// 选择一个API以后，把返回值说明初始化
	setReturn: function(self) {
		var api = self.api;
		var k1 = $('#api option:selected').prevAll('option[disabled="disabled"]').val();
		var k2 = $('#api').val();
		var returns = api[k1]['api'][k2]['returns'];
		self.returnsDom.empty();
		self.returnDataDom.empty();
		for (var key in returns) {
			var item = self.returnsTpl.clone();
			item.find('th').text(key);
			item.find('.info').text(returns[key]);
			self.returnsDom.append(item);
		}
	},
	// 提交等所有按钮事件绑定
	initBtn: function(self) {
		$('#submit').click(function() {
			self.returnDataDom.empty();
			var url = self.urlDom.text();
			var data = {};
			$('#params .param').each(function() {
				var key = $(this).attr('name');
				var inputType = $(this).attr('type');
				if (inputType == 'text') {
					var value = $(this).val();
					data[key] = value;
					if (window.localStorage) {
						localStorage[key] = value;
					}
				} else if (inputType == 'file') {

				}
			});
			$.ajax({
				type: 'POST',
				dataType: 'json',
				url: url,
				data: data,
				success: function(res) {
					self.setReturnData(self, res);
					if (res.code == 0) {
						var s = self.getQueryString('s', url);
						if (s == 'index/api/login' ||
							s == 'index/api/autologin') {
							$('#loginInfo').text('已登录用户ID: ' + res.data.uid);
						} else if (s == 'index/api/logout') {
							$('#loginInfo').text('未登录');
						}
					} else {
						console.log(res.msg);
					}
				},
				error: function() {
					alert('error!');
				}
			});
		});
	},
	setReturnData: function(self, res) {
		var options = {
			dom: '#returnData', //对应容器的css选择器
			singleTab: "  ", //单个tab
			tabSize: 4, //缩进数量
			quoteKeys: true, //key是否用双引号包含
			imgCollapsed: "/assets/js/jsonFormater/images/Collapsed.gif", //收起的图片路径
			imgExpanded: "/assets/js/jsonFormater/images/Expanded.gif", //展开的图片路径
			isCollapsible: true //是否支持展开收起
		};
		var jf = new JsonFormater(options); //创建对象
		jf.doFormat(res); //格式化json
	},
	getQueryString: function(name, href) {
		if (href) {
			var arr = href.split('?');
			if (arr.length >= 2) {
				href = '?' + arr[1];
			}
		}
		var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)");
		var search_href = href ? href : window.location.search;
		var r = search_href.substr(1).match(reg);
		if (r != null) return unescape(r[2]);
		return null;
	}
};

$(function() {
	apiObject.initPage();
});