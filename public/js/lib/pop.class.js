var pop = {
	close: function(index) {
		if (index) {
			layer.close(index);
		} else {
			layer.closeAll();
		}
	},
	closeAll: function(callback) {
		layer.closeAll();
		if(typeof callback == 'function') callback();
	},
	loading: function(str, seconds) {
		if (str) {
			var index = layer.msg(str, {
				icon: 16,
				shade: 0.01
			});
			if (seconds) {
				setTimeout(function(){
					layer.close(index);
				}, seconds * 1000);
			}
		} else {
			var config = seconds > 0 ? {
				time: seconds * 1000
			} : {};
			layer.load(2, config);
		}
	},
	dialog: function(opt) {
		var title = opt.title ? opt.title : '信息';
		var btns = opt.btns ? opt.btns : ['确认', '取消'];
		var area = opt.area.length >= 2 ? [opt.area[0] + 'px', opt.area[1] + 'px'] : [opt.area[0] + 'px'];
		var id = opt.id ? opt.id : 'dialog_public_layer';
		layer.confirm(opt.str, {
			id: id,
			title: title,
			area: area,
			btn: btns //按钮
		}, function() { // 确认的操作
			if (typeof opt.callback[0] == 'function') opt.callback[0]();
		}, function() {
			// 取消的操作
			if (typeof opt.callback[1] == 'function') opt.callback[1]();
		});
	},
	msg: function(str, callback, seconds) {
		if (!seconds) seconds = 1;
		layer.msg(str, {
			time: seconds * 1000,
			end: function() {
				if (typeof callback == 'function') callback();
			}
		});
	},
	alert: function(str, callback) {
		layer.open({
			content: str,
			btn: ['确定'],
			end: function() {
				if (typeof callback == 'function') callback();
			}
		});
	},
	confirm: function(str, callback) {
		layer.confirm(str, {
			icon: 3,
			title: '提示',
			btn: ['确认', '取消'] //按钮
		}, function() { // 确认的操作
			if (typeof callback == 'function') callback();
		}, function() {});
	},
	prompt: function(title, value, callback) {
		layer.prompt({
				title: title, 
				value: value,
				formType: 0
			}, 
			function(str, index){
				callback && callback(str, index);
  			}
  		);
  		$('.layui-layer-prompt .layui-layer-input').unbind('keyup').keyup(function(event){
  			if (event.keyCode == 13) {
  				$(this).parents('.layui-layer-prompt').find('.layui-layer-btn a').eq(0).click();
  			}
  		});
	},
	tips: function(str, selector) {
		layer.tips(str, selector, {
			tips: 3
		});
	},
	diyPage: function(opt) {
		var cfg = {
			'title': '信息',
			'id': 'layer_diy_page',
			'area': ['auto', 'auto']
		};
		opt = $.extend(cfg, opt);
		layer.open({
			id: opt.id,
			type: 1,
			area: opt.area,
			title: opt.title,
			content: opt.html
		});
	},
	openPage: function(opt) {
		var cfg = {
			id: 'layer_open_page',
			area: ['auto', 'auto'],
			title: '信息',
			content: ''
		};
		opt = $.extend(cfg, opt);
		layer.open({
			type: 1,
			area: opt.area,
			title: opt.title,
			content: opt.content
		});
	}
};

