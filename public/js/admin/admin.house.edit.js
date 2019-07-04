let APP_ID_HERE = "QuHxU6ypXzp37Dci84o8";
let APP_CODE_HERE = "TDu_enlm0QIblRnIl33buw";

$("#address").autocomplete({
	source: addressAC,
	minLength: 2,
	select: function (event, ui) {
		console.log("Selected: " + ui.item.value + " with LocationId " + ui.item.id);
		locationAD(ui.item.value)
		var area = ui.item.value.split(',')[1].trim()
		if(area == "Melbourne"){
			$('#area').val("Melbourne CBD");
		}else{
			$('#area').val(area);
		}

	}
});

$("#address").autocomplete({
	source: addressAC,
	minLength: 2,
	select: function (event, ui) {
		console.log("Selected: " + ui.item.value + " with LocationId " + ui.item.id);
		locationAD(ui.item.value)
		var area = ui.item.value.split(',')[1].trim()
		if(area == "Melbourne"){
			$('#area').val("Melbourne CBD");
		}else{
			$('#area').val(area);
		}

	}
});

// autocomplete using Address autocomplete
// jquery autocomplete needs 2 fields: title and value
// id holds the LocationId which can be used at a later stage to get the coordinate of the selected choice
function addressAC(query, callback) {
	$.getJSON("https://autocomplete.geocoder.api.here.com/6.2/suggest.json?query=" + query.term + "&app_id=" + APP_ID_HERE + "&app_code=" + APP_CODE_HERE+ "&country=AUS", function (data) {
		var addresses = data.suggestions;
		addresses = addresses.map(addr => {
			return {
				title: addr.label.split(",").reverse().join().trim(),
				value: addr.label.split(",").reverse().join().trim(),
				id: addr.locationId
			};
		});

		return callback(addresses);
	});
}

// geocode retrieve the latitude and longitude.
// set the latitude and longtitude to the inputbox
function locationAD(query) {
	$.getJSON("https://geocoder.api.here.com/6.2/geocode.json?gen=9&searchtext=" + query + "&app_id=" + APP_ID_HERE + "&app_code=" + APP_CODE_HERE, function (data) {
		var lati = data["Response"]["View"][0]["Result"][0]["Location"]["DisplayPosition"]["Latitude"];
		var longi = data["Response"]["View"][0]["Result"][0]["Location"]["DisplayPosition"]["Longitude"];
		$('#x').val(lati);
		$('#y').val(longi);
	});
}


// The date picker widget
$( function() {
	$( "#live_date" ).datepicker({
		dateFormat: 'yy-mm-dd'
	});
} );


// This part is for dynamically retrieve the data for school
$( "#city" ).change(function() {
	var idCity = $('option:selected', this).attr('idvalue');
	$.ajax({
		type: "POST",
		url:  "https://wx.xiaobaozufang.com/api/index/get_cate?id="+idCity,
		dataType: "json",
		success: function(res) {
			if(res.code) {
				var i;
				var optionstring;
				for (i=0;i<res.data.school.length;i++){
					optionstring += "<option value=\""+ res.data.school[i]['name'] + " \" >" + res.data.school[i]['name'] + "</option>";
					$("#school").html("<option value=''>请选择...</option> "+optionstring);
				}

				return;
			}
		},
		error: function(XMLHttpRequest, textStatus, errorThrown) {
			pop.alert('页面故障，请联系管理员！<br>故障信息:<br>' + errorThrown);
		}
	});
});

var editObj = {
	dom: null,
	initPage: function() {
		var self = this;
		self.initSubmitBtn(self);
		self.initThumbnail(self);
		self.initDetailPic1(self);
		self.initDetailPic2(self);
		self.initDetailPic3(self);
		self.initDetailPic4(self);
		self.initDetailPic5(self);
		self.initDetailPic6(self);
		self.initDetailPic7(self);
		self.initDetailPic8(self);
		self.initCate(self);
	},

	initCate:function(){
		$.ajax({
			type: "POST",
			url:  "https://wx.xiaobaozufang.com/api/index/get_cate",
			dataType: "json",
			success: function(res) {
				if(res.code) {
					var i;
					var optionstring;
					for (i=0;i<res.data.length;i++){
						optionstring += "<option idvalue=\""+ res.data[i]['id']+"\" value=\""+ res.data[i]['name'] + " \" >" + res.data[i]['name'] + "</option>";
						$("#city").html("<option value=''>请选择...</option> "+optionstring);
					}

					return;
				}
				//页面跳转
			},
			error: function(XMLHttpRequest, textStatus, errorThrown) {
				pop.alert('页面故障，请联系管理员！<br>故障信息:<br>' + errorThrown);
			}
		});
	},

	initSubmitBtn: function() {
		$('#submit').click(function(event) {
			var data = {
				// 房源id
				'id': $('#id').val(),
				'user_id': $('#user_id').val(),
				'title': $('#title').val(),
				'http': $('#http').val(),
				'price': $('#price').val(),
				'address': $('#address').val(),
				'city': $('#city').val(),
				'validity': $('#validity').val(),
				'source': $("input[name='source']:checked").val(),
				'type': $("input[name='type']:checked").val(),
				'sex': $("input[name='sex']:checked").val(),
				'pet': $("input[name='pet']:checked").val(),
				'bill': $("input[name='bill']:checked").val(),
				'live_date': $('#live_date').val(),
				'lease_term': $("input[name='lease_term']:checked").val(),
				'house_type': $("input[name='house_type']:checked").val(),
				'furniture': $("input[name='furniture']:checked").val(),
				'house_room': $("input[name='house_room']:checked").val(),
				'car': $("input[name='car']:checked").val(),
				'toilet': $("input[name='toilet']:checked").val(),
				'home': $("input[name='home']:checked").map(function() {
					return this.value;
				}).get().join(','),
				'sation': $("input[name='sation']:checked").map(function() {
					return this.value;
				}).get().join(','),
				'area': $('#area').val(),
				'school': $('#school').val(),
				'real_name': $('#real_name').val(),
				'wchat': $('#wchat').val(),
				'tel': $('#tel').val(),
				'email': $('#email').val(),
				'content': $('#content').val(),
				'x': $('#x').val(),
				'y': $('#y').val(),
				'thumnail': $('#thumbnail').val(),
				'images': $("input[name='detail']").map(function() {
					if (this.value != ""){
						return this.value;
					}
				}).get().join(','),
				'status': $('#status').val(),
			};
			if(data.title == 0) {
				pop.alert("请填写标题", function() {
					$("#title").focus();
					$('html,body').animate({
						scrollTop: 0
					}, 500);
				});
				return false;
			}
			if(data.user_id == "") {
				pop.alert("请选择一个用户，若无您的用户，请联系管理员修改", function() {
					$("#title").focus();
					$('html,body').animate({
						scrollTop: 0
					}, 500);
				});
				return false;
			}
			if(data.address == ""){
				pop.alert("请填写地址", function() {
					$("#address").focus();
					$('html,body').animate({
						scrollTop: 0
					}, 500);
				});
				return false;
			}
			if(data.price == ""){
				pop.alert("请填写租金", function() {
					$("#price").focus();
					$('html,body').animate({
						scrollTop: 0
					}, 500);
				});
				return false;
			}
			if(data.live_date == ""){
				pop.alert("请填写入住时间", function() {
					$("#live_date").focus();
					$('html,body').animate({
						scrollTop: 0
					}, 500);
				});
				return false;
			}

			if(data.school == ""){
				pop.alert("请填写校区", function() {
					$("#school").focus();
					$('html,body').animate({
						scrollTop: 0
					}, 500);
				});
				return false;
			}
			if(data.thumnail == ""){
				pop.alert("请上传封面图", function() {
					$("#thumnail").focus();
					$('html,body').animate({
						scrollTop: 0
					}, 500);
				});
				return false;
			}
			if(data.images == ""){
				pop.alert("请补充至少一个详情图在第一位置", function() {
					$('html,body').animate({
						scrollTop: 0
					}, 500);
				});
				return false;
			}
			if(data.content == ""){
				pop.alert("请补充房源描述", function() {
					$("#content").focus();
					$('html,body').animate({
						scrollTop: 0
					}, 500);
				});
				return false;
			}
			$.ajax({
				type: "POST",
				url: baseURL + "?s=admin/houses/save",
				data: data,
				dataType: "json",
				success: function(res) {
					if(res.code) {
						pop.alert(res.msg);
						return;
					}
					//页面跳转
					pop.msg("保存成功", function() {
						location.href = baseURL + "?s=admin/houses/index";
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
				url: baseURL + "?s=admin/houses/upload_thumbnail",
				type: 'POST',
				data: formData,
				dataType: "json",
				processData: false,
				contentType: false,
				success: function(res) {
					console.log(res);
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
	},
	// 详情图1
	initDetailPic1: function(self) {
		var dom = $("#detail_show1");
		var thumbnail = $("#detailpic1").val();
		if(thumbnail != "") {
			self.setDetailImg1(dom, thumbnail, '#detailpic1');
		}
		dom.find(".detail_pic1").click(function(event) {
			dom.find("input[type='file']").click();
		});
		dom.find("input[type='file']").change(function() {
			var file = $(this).get(0).files[0];
			$(this).val("");
			var formData = new FormData();
			formData.append("file", file);
			formData.append('selector', '#detailpic1');
			$.ajax({
				url: baseURL + "?s=admin/houses/upload_thumbnail",
				type: 'POST',
				data: formData,
				dataType: "json",
				processData: false,
				contentType: false,
				success: function(res) {
					if (res.code == 0) {
						self.setDetailImg1(dom, res.data.url, res.data.selector);
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
	setDetailImg1 : function(dom, url, selector) {
		dom.find(".detail_pic1").css({
			"background": "url(" + url + ") no-repeat",
			"background-size": "100% 100%"
		});
		dom.find(".detail_pic1 p").css({
			"margin-top": "-7px"
		})
		$(selector).val(url);
	},

	// 详情图2
	initDetailPic2: function(self) {
		var dom = $("#detail_show2");
		var thumbnail = $("#detailpic2").val();
		if(thumbnail != "") {
			self.setDetailImg2(dom, thumbnail, '#detailpic2');
		}
		dom.find(".detail_pic2").click(function(event) {
			dom.find("input[type='file']").click();
		});
		dom.find("input[type='file']").change(function() {
			var file = $(this).get(0).files[0];
			$(this).val("");
			var formData = new FormData();
			formData.append("file", file);
			formData.append('selector', '#detailpic2');
			$.ajax({
				url: baseURL + "?s=admin/houses/upload_thumbnail",
				type: 'POST',
				data: formData,
				dataType: "json",
				processData: false,
				contentType: false,
				success: function(res) {
					if (res.code == 0) {
						self.setDetailImg2(dom, res.data.url, res.data.selector);
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
	setDetailImg2 : function(dom, url, selector) {
		dom.find(".detail_pic2").css({
			"background": "url(" + url + ") no-repeat",
			"background-size": "100% 100%"
		});
		dom.find(".detail_pic2 p").css({
			"margin-top": "-7px"
		})
		$(selector).val(url);
	},

	// 详情图3
	initDetailPic3: function(self) {
		var dom = $("#detail_show3");
		var thumbnail = $("#detailpic3").val();
		if(thumbnail != "") {
			self.setDetailImg3(dom, thumbnail, '#detailpic3');
		}
		dom.find(".detail_pic3").click(function(event) {
			dom.find("input[type='file']").click();
		});
		dom.find("input[type='file']").change(function() {
			var file = $(this).get(0).files[0];
			$(this).val("");
			var formData = new FormData();
			formData.append("file", file);
			formData.append('selector', '#detailpic3');
			$.ajax({
				url: baseURL + "?s=admin/houses/upload_thumbnail",
				type: 'POST',
				data: formData,
				dataType: "json",
				processData: false,
				contentType: false,
				success: function(res) {
					if (res.code == 0) {
						self.setDetailImg3(dom, res.data.url, res.data.selector);
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
	setDetailImg3 : function(dom, url, selector) {
		dom.find(".detail_pic3").css({
			"background": "url(" + url + ") no-repeat",
			"background-size": "100% 100%"
		});
		dom.find(".detail_pic3 p").css({
			"margin-top": "-7px"
		})
		$(selector).val(url);
	},

	// 详情图4
	initDetailPic4: function(self) {
		var dom = $("#detail_show4");
		var thumbnail = $("#detailpic4").val();
		if(thumbnail != "") {
			self.setDetailImg4(dom, thumbnail, '#detailpic1');
		}
		dom.find(".detail_pic4").click(function(event) {
			dom.find("input[type='file']").click();
		});
		dom.find("input[type='file']").change(function() {
			var file = $(this).get(0).files[0];
			$(this).val("");
			var formData = new FormData();
			formData.append("file", file);
			formData.append('selector', '#detailpic4');
			$.ajax({
				url: baseURL + "?s=admin/houses/upload_thumbnail",
				type: 'POST',
				data: formData,
				dataType: "json",
				processData: false,
				contentType: false,
				success: function(res) {
					if (res.code == 0) {
						self.setDetailImg4(dom, res.data.url, res.data.selector);
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
	setDetailImg4 : function(dom, url, selector) {

		dom.find(".detail_pic4").css({
			"background": "url(" + url + ") no-repeat",
			"background-size": "100% 100%"
		});
		dom.find(".detail_pic4 p").css({
			"margin-top": "-7px"
		})
		$(selector).val(url);
	},

	// 详情图5
	initDetailPic5: function(self) {
		var dom = $("#detail_show5");
		var thumbnail = $("#detailpic5").val();
		if(thumbnail != "") {
			self.setDetailImg5(dom, thumbnail, '#detailpic5');
		}
		dom.find(".detail_pic5").click(function(event) {
			dom.find("input[type='file']").click();
		});
		dom.find("input[type='file']").change(function() {
			var file = $(this).get(0).files[0];
			$(this).val("");
			var formData = new FormData();
			formData.append("file", file);
			formData.append('selector', '#detailpic5');
			$.ajax({
				url: baseURL + "?s=admin/houses/upload_thumbnail",
				type: 'POST',
				data: formData,
				dataType: "json",
				processData: false,
				contentType: false,
				success: function(res) {
					if (res.code == 0) {
						self.setDetailImg5(dom, res.data.url, res.data.selector);
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
	setDetailImg5 : function(dom, url, selector) {
		dom.find(".detail_pic5").css({
			"background": "url(" + url + ") no-repeat",
			"background-size": "100% 100%"
		});
		dom.find(".detail_pic5 p").css({
			"margin-top": "-7px"
		})
		$(selector).val(url);
	},

	// 详情图6
	initDetailPic6: function(self) {
		var dom = $("#detail_show6");
		var thumbnail = $("#detailpic6").val();
		if(thumbnail != "") {
			self.setDetailImg6(dom, thumbnail, '#detailpic6');
		}
		dom.find(".detail_pic6").click(function(event) {
			dom.find("input[type='file']").click();
		});
		dom.find("input[type='file']").change(function() {
			var file = $(this).get(0).files[0];
			$(this).val("");
			var formData = new FormData();
			formData.append("file", file);
			formData.append('selector', '#detailpic6');
			$.ajax({
				url: baseURL + "?s=admin/houses/upload_thumbnail",
				type: 'POST',
				data: formData,
				dataType: "json",
				processData: false,
				contentType: false,
				success: function(res) {
					if (res.code == 0) {
						self.setDetailImg6(dom, res.data.url, res.data.selector);
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
	setDetailImg6 : function(dom, url, selector) {
		dom.find(".detail_pic6").css({
			"background": "url(" + url + ") no-repeat",
			"background-size": "100% 100%"
		});
		dom.find(".detail_pic6 p").css({
			"margin-top": "-7px"
		})
		$(selector).val(url);
	},

	// 详情图7
	initDetailPic7: function(self) {
		var dom = $("#detail_show7");
		var thumbnail = $("#detailpic7").val();
		if(thumbnail != "") {
			self.setDetailImg7(dom, thumbnail, '#detailpic7');
		}
		dom.find(".detail_pic7").click(function(event) {
			dom.find("input[type='file']").click();
		});
		dom.find("input[type='file']").change(function() {
			var file = $(this).get(0).files[0];
			$(this).val("");
			var formData = new FormData();
			formData.append("file", file);
			formData.append('selector', '#detailpic7');
			$.ajax({
				url: baseURL + "?s=admin/houses/upload_thumbnail",
				type: 'POST',
				data: formData,
				dataType: "json",
				processData: false,
				contentType: false,
				success: function(res) {
					if (res.code == 0) {
						self.setDetailImg7(dom, res.data.url, res.data.selector);
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
	setDetailImg7 : function(dom, url, selector) {
		dom.find(".detail_pic7").css({
			"background": "url(" + url + ") no-repeat",
			"background-size": "100% 100%"
		});
		dom.find(".detail_pic7 p").css({
			"margin-top": "-7px"
		})
		$(selector).val(url);
	},


	// 详情图8
	initDetailPic8: function(self) {
		var dom = $("#detail_show8");
		var thumbnail = $("#detailpic8").val();
		if(thumbnail != "") {
			self.setDetailImg8(dom, thumbnail, '#detailpic8');
		}
		dom.find(".detail_pic8").click(function(event) {
			dom.find("input[type='file']").click();
		});
		dom.find("input[type='file']").change(function() {
			var file = $(this).get(0).files[0];
			$(this).val("");
			var formData = new FormData();
			formData.append("file", file);
			formData.append('selector', '#detailpic8');
			$.ajax({
				url: baseURL + "?s=admin/houses/upload_thumbnail",
				type: 'POST',
				data: formData,
				dataType: "json",
				processData: false,
				contentType: false,
				success: function(res) {
					if (res.code == 0) {
						self.setDetailImg8(dom, res.data.url, res.data.selector);
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
	setDetailImg8 : function(dom, url, selector) {
		dom.find(".detail_pic8").css({
			"background": "url(" + url + ") no-repeat",
			"background-size": "100% 100%"
		});
		dom.find(".detail_pic8 p").css({
			"margin-top": "-7px"
		})
		$(selector).val(url);
	},

};

$(function() {
	editObj.initPage();
});


