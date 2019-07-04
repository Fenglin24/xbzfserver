function log() {
	var str = '';
	for (var i=0; i<arguments.length; i++) {
		if (typeof arguments[i] == 'object') {
			console.log(arguments[i])
		} else {
			str += arguments[i];
		}
	}
	if (str) {
		console.log(str);
	}
}

function trim(str) {
	return str.replace(/^\s+|\s+$/g, '');
}

function isNumber(s) {
	return !isNaN(s);
}

function intval(s) {
	s = parseInt(s, 10);
	return isNaN(s) ? 0 : s;
}

function floatval(s) {
	s = parseFloat(s);
	return isNaN(s) ? 0.0 : s;
}

function getPreveUrl() {
	var referrer = document.referrer;
	if (!referrer) return '/';
	var arr = referrer.split('//');
	if (arr.length < 2) {
		return '/';
	}
	var url = arr[1];
	var pos = url.indexOf('/');
	if (pos == -1) {
		return '/';
	}
	url = url.substr(pos);
	return url;
}

function getURL() {
	return location.pathname + location.search + location.hash;
}

function getEncodedURL() {
	return encodeURIComponent(getURL());
}

function getQueryString(name, href) {
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

function scrollToBottom() {
	var documentHeight = $(document).height();
	var windowHeight = $(window).height();
	var scrollHeight = $(document).scrollTop();
	var heightToBottom = documentHeight - windowHeight - scrollHeight;
	return heightToBottom < 3; // 滚动条距离页面底部的距离
}

function getSecretPhone(no) {
	if (no.length != 11) {
		return "";
	}
	var prev = no.substr(0, 3);
	var tail = no.substr(9, 2);
	return prev + "******" + tail;
}


function isWeixin() {
	var ua = navigator.userAgent.toLowerCase();
	if (ua.match(/MicroMessenger/i) == "micromessenger") {
		return true;
	} else {
		return false;
	}
}

function delCookie(name) { //删除cookie
	document.cookie = name + "=;expires=" + (new Date(0)).toGMTString();
}

/**
 * [setCookie js设置cookie，作用域网站根目录]
 * @param {[type]} c_name     [cookie的键值]
 * @param {[type]} value      [cookie的value]
 * @param {[type]} expires    [过期时间：秒]
 */
function setCookie(c_name, value, expires) {
	var exdate = new Date();
	exdate.setTime(exdate.getTime() + expires * 1000);
	document.cookie = c_name + "=" + encodeURIComponent(value) + ((expires == null) ? "" : ";expires=" + exdate.toGMTString()) + ';path=/';
}

function getCookie(name) {
	var result = "";
	var myCookie = document.cookie + ";";
	var searchName = name + "=";
	var startOfCookie = myCookie.indexOf(searchName);
	var endOfCookie;
	if (startOfCookie != -1) {
		startOfCookie += searchName.length;
		endOfCookie = myCookie.indexOf(";", startOfCookie);
		result = decodeURIComponent(myCookie.substring(startOfCookie, endOfCookie));
	}
	return (result);
}

function randomString(len) {　　
	len = len || 32;　　
	var $chars = 'ABCDEFGHJKMNPQRSTWXYZabcdefhijkmnprstwxyz2345678'; /****默认去掉了容易混淆的字符oOLl,9gq,Vv,Uu,I1****/ 　　
	var maxPos = $chars.length;　　
	var pwd = '';　　
	for (i = 0; i < len; i++) {　　　　
		pwd += $chars.charAt(Math.floor(Math.random() * maxPos));　　
	}　　
	return pwd;
}

function rememberSetEncodeValue(key, value) {
	if (value === null) {
		delCookie(key);
	} else {
		console.log(value)
		value = randomString(10) + base64Encode(value);
		setCookie(key, value, 10 * 365 * 24 * 3600);
	}
}

function rememberGetDecodeValue(key) {
	var value = getCookie(key);
	if (!value) return value;
	value = value.substr(10);
	value = base64Decode(value);
	return value;
}

// 将base64编码的src值转换为file对象
function base64ToFile(data) {
	data = data.split(',')[1];
	data = window.atob(data);
	var ia = new Uint8Array(data.length);
	for (var i = 0; i < data.length; i++) {
		ia[i] = data.charCodeAt(i);
	};
	// 这里生成的file相当于file表单组建选择一张图片后的document.getElementById('file').files[0]
	var file = new Blob([ia], {
		type: "image/png"
	});
	return file;
}

// 将file对象转换成base64编码的src值
function fileToBase64(file, callback) {
	var reader = new FileReader(); // 仅支持IE11，filefox，chrome，safari
	reader.onload = function(e) {
		// this.result得到图片的base64 (可以用作即时显示)
		var base64SrcValue = this.result;
		callback && callback(base64SrcValue);
	}
	reader.readAsDataURL(file);
}

function getThumbnailImage(file, toWidth, toHeight, callback) {
	fileToBase64(file, function(base64URL) {
		var img = new Image();
		img.onload = function() {
			var scale = img.height / img.width;
			var width = toWidth;
			var height = parseInt(width * scale);
			if (toHeight && height > toHeight) {
				height = toHeight;
				width = parseInt(height / scale);
			}
			var canvas = $('<canvas></canvas>');
			canvas.attr({
				width: width,
				height: height
			});
			var ctx = canvas[0].getContext('2d');
			ctx.drawImage(img, 0, 0, width, height);
			var base64Thumb = canvas[0].toDataURL('image/jpeg', 0.8);
			callback && callback(base64Thumb);
		};
		img.src = base64URL;
	});
}

// 在光标位置插入html代码，如果dom没有获取到焦点则追加到最后
function insertAtCursor(dom, html) {
	if (dom != document.activeElement) { // 如果dom没有获取到焦点，追加
		dom.innerHTML = dom.innerHTML + html;
		return;
	}
	var sel, range;
	if (window.getSelection) {
		// IE9 或 非IE浏览器
		sel = window.getSelection();
		if (sel.getRangeAt && sel.rangeCount) {
			range = sel.getRangeAt(0);
			range.deleteContents();
			// Range.createContextualFragment() would be useful here but is
			// non-standard and not supported in all browsers (IE9, for one)
			var el = document.createElement("div");
			el.innerHTML = html;
			var frag = document.createDocumentFragment(),
				node, lastNode;
			while ((node = el.firstChild)) {
				lastNode = frag.appendChild(node);
			}
			range.insertNode(frag);
			// Preserve the selection
			if (lastNode) {
				range = range.cloneRange();
				range.setStartAfter(lastNode);
				range.collapse(true);
				sel.removeAllRanges();
				sel.addRange(range);
			}
		}
	} else if (document.selection && document.selection.type != "Control") {
		// IE < 9
		document.selection.createRange().pasteHTML(html);
	}
}

// 在光标位置插入内容（仅适用于textarea文本输入框）
function insertValueAtCursor(myField, insertContent) {
	if (myField != document.activeElement) {
		myField.value = myField.value + insertContent;
		return;
	}
	//IE support
	if (document.selection) {
		myField.focus();
		var sel = document.selection.createRange();
		sel.text = insertContent;
		sel.select();
	}
	//MOZILLA/NETSCAPE support
	else if (typeof myField.selectionStart === 'number' || typeof myField.selectionEnd == 'number') {
		var startPos = myField.selectionStart;
		var endPos = myField.selectionEnd;
		console.log("startPos:" + startPos)
			// save scrollTop before insert
		var restoreTop = myField.scrollTop;
		myField.value = myField.value.substring(0, startPos) + insertContent + myField.value.substring(endPos, myField.value.length);
		if (restoreTop > 0) {
			// restore previous scrollTop
			myField.scrollTop = restoreTop;
		}
		myField.focus();
		myField.selectionStart = startPos + insertContent.length;
		myField.selectionEnd = startPos + insertContent.length;
	} else {
		console.log('OK')
		myField.value += insertContent;
		myField.focus();
	}
}

function getDateAfterDays(days) {
	var now = new Date();
	if (!/-?\d+/.test(days)) {
		return now;
	}
	now.setDate(now.getDate() + parseInt(days));
	return now.Format('yyyy-mm-dd');
}


var regExp = {
	isPhoneNumber: function(str) {
		return /^1[34578]\d{9}$/.test(str);
	}, 
	isTelephoneNumber: function(str) {
		var regExpression8 = /^(0[0-9]{2}(\-)?([2-9][0-9]{7}){1}(\-[0-9]{1,4})?$)/;
		var regExpression7 = /^(0[0-9]{3}(\-)?([2-9][0-9]{6}){1}(\-[0-9]{1,4})?$)/;
		return regExpression8.test(str) || regExpression7.test(str);
	}, 
	isEmail: function(str) {
		var regExp = /^[\w_\-\.]+@[\w_\-\.]+$/;
		return regExp.test(str);
	},
	isZipCode: function(str) {
		var regExp = /^[0-9]{6}$/;
		return regExp.test(str);
	},
	isTime: function(str) {
		var regExp = /^([0-1]\d|2[0-3]):[0-5]\d(:[0-5]\d)*$/;
		return regExp.test(str);
	},
	isDateTime: function(str) {
		var regExp = /^\d{4}\-(0\d|1[0-2])-([0-2]\d|3[0-1])\ ([0-1]\d|2[0-3]):[0-5]\d(:[0-5]\d)*$/;
		return regExp.test(str);
	}
};

var dateTime = {
	addDays: function(date, days) {
		var nd = new Date(date); // date = '2017-07-20'
		nd = nd.valueOf();
		nd = nd + days * 24 * 60 * 60 * 1000;
		nd = new Date(nd);
		//alert(nd.getFullYear() + "年" + (nd.getMonth() + 1) + "月" + nd.getDate() + "日");
		var y = nd.getFullYear();
		var m = nd.getMonth() + 1;
		var d = nd.getDate();
		if (m <= 9) m = "0" + m;
		if (d <= 9) d = "0" + d;
		var cdate = y + "-" + m + "-" + d;
		return cdate;
	},
	dateDiff: function (d1, d2) {
		var day = 24 * 60 * 60 * 1000;
		try {
			var dateArr = d1.split("-");
			var checkDate = new Date();
			checkDate.setFullYear(dateArr[0], dateArr[1] - 1, dateArr[2]);
			var checkTime = checkDate.getTime();

			var dateArr2 = d2.split("-");
			var checkDate2 = new Date();
			checkDate2.setFullYear(dateArr2[0], dateArr2[1] - 1, dateArr2[2]);
			var checkTime2 = checkDate2.getTime();

			var cha = (checkTime - checkTime2) / day;
			return cha;
		} catch (e) {
			return false;
		}
	}
};

