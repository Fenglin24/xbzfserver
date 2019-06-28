var editObj = {
	dom: null,
	initPage: function() {
		var self = this;
		self.initSubmitBtn(self);
		self.initThumbnail(self);
	},
	initSubmitBtn: function() {
		$('#submit').click(function(event) {
			if(!ueReady) {
				pop.alert("请等待编辑器出现以后再提交");
				return false;
			}
			var data = {
				'id': $('#id').val(),
				'title': $('#title').val(),
				'type': $('#type').val(),
				'summary': $('#summary').val(),
				'content': ue.getContent()
			};
			var type = $('#type').val();
			var i = 'renting';
			// alert(type == '房东协议')
			if (type == '房东') {
				 i = 'house';
			} else if (type == '关于我们') {
				i = 'contact';
			} else if (type == '平台声明') {
				i = 'platform';
			} else if (type == '直播看房') {
				i = 'live';
			} else if (type == '房源申请') {
				i = 'sign';
			} else if (type == '闪电招租') {
				i = 'bolt';
			} else if (type == '房东协议') {
				i = 'agree';
			} else if (type == '看房服务') {
				i = 'see';
			}
			// alert(i)
			if(data.title == 0) {
				// pop.alert("请填写标题", function() {
				// 	$("#title").focus();
				// 	$('html,body').animate({
				// 		scrollTop: 0
				// 	}, 500);
				// });
				// return false;
			}
			$.ajax({
				type: "POST",
				url: baseURL + "?s=/admin/questions/save",
				data: data,
				dataType: "json",
				success: function(res) {
					if(res.code) {
						pop.alert(res.msg);
						return;
					}
					//页面跳转
					pop.msg("保存成功", function() {
						location.href = baseURL + "?s=/admin/questions/"+i+'&ps=/admin/questions/index';
					})
				},
				error: function(XMLHttpRequest, textStatus, errorThrown) {
					pop.alert('页面故障，请联系管理员！<br>故障信息:<br>' + errorThrown);
				}
			});

		});
	},
	//缩略图
	initThumbnail: function(self) {
		var dom = $("#thumbnail_show");
		var thumbnail = $("#thumbnail").val();
		if(thumbnail != "") {
			self.setThumbnailImg(dom, thumbnail, '#thumbnail');
		}
		dom.find(".thumbnail").click(function(event) {
			dom.find("input[type='file']").click();
		});
		dom.find("input[type='file']").change(function() {
			var file = $(this).get(0).files[0];
			$(this).val("");
			var formData = new FormData();
			formData.append("file", file);
			formData.append('selector', '#thumbnail');
			$.ajax({
				url: baseURL + "?s=admin/questions/upload_thumbnail",
				type: 'POST',
				data: formData,
				dataType: "json",
				processData: false,
				contentType: false,
				success: function(res) {
					if (res.code == 0) {
						self.setThumbnailImg(dom, res.data.url, res.data.selector);
					} else {
						pop.msg(res.msg);
					}
				},
				error: function(XMLHttpRequest, textStatus, errorThrown) {
					pop.alert('页面故障，请联系管理员！<br>故障信息:<br>' + errorThrown);
				}
			});

		});
	},
	setThumbnailImg : function(dom, url, selector) {
		dom.find(".thumbnail").css({
			"background": "url(" + url + ") no-repeat",
			"background-size": "100% 100%"
		});
		dom.find(".thumbnail p").css({
			"margin-top": "-7px"
		})
		$(selector).val(url);
	}

};

$(function() {
	editObj.initPage();
});