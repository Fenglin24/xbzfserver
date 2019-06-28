$(function(){
	$('.datetime').datetimepicker({
		lang: "ch", //语言选择中文
		format: "Y-m-d H:i:00", //格式化日期
		datepicker: true,
		timepicker: true, //关闭时间选项
		yearEnd: 2050, //设置最大年份
		todayButton: false //关闭选择今天按钮
	});
	$('.date').datetimepicker({
		lang: "ch", //语言选择中文
		format: "Y-m-d", //格式化日期
		timepicker: false, //关闭时间选项
		yearEnd: 2050, //设置最大年份
		todayButton: false //关闭选择今天按钮
	});
	$('.time').datetimepicker({
		lang: "ch", //语言选择中文
		datepicker: false,
		format: 'H:i:00',
		step: 5
	});
	$('.xdsoft_datetimepicker').css('z-index', 999999999);
	$('.date,.time,.datetime').unbind('mousewheel');
});