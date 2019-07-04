/**
 * 开发：张雅荣 QQ:1635517277
 * 修正：丁华能 QQ:67444242
 * 公司：燃果创意
 * 网址：www.rungo.net
 * 用法：配置两个URL： getAuthListUrl，getRoleAuthInfoUrl即可。两个接口返回键js目录中的json文件
 * 调用这个页面的js实例在js/demo.js中。
 * 开发日期：2017.02.24
 */
$(function() {
	// 获取权限列表，用于初始化树节点的ajax接口
	var getAuthListUrl = getQueryString('getAuthListUrl');
	// 获取本角色拥有权限，用于选中某些树形节点的ajax接口
	var getRoleAuthInfoUrl = getQueryString('getRoleAuthInfoUrl');

	var userId = parseInt(getQueryString('id'), 10);
    if (userId > 0) {
    	initZtreePage(userId, getAuthListUrl, getRoleAuthInfoUrl);
    }
    function setBtnDisabledInSeconds(dom, seconds) {
    	dom.attr('disabled', 'disabled');
    	setTimeout(function(){
    		dom.removeAttr('disabled');
    	}, seconds*1000);
    }
	function initZtreePage(userId, getAuthListUrl, getRoleAuthInfoUrl) {
	    initPageByAjaxGetZnodesData(userId);
	    function initPageByAjaxGetZnodesData(userId) {
	        $.ajax({
	            type: "GET",
	            url: getAuthListUrl,
	            data: {},
	            dataType: "json",
	            success: function(res) {
	                if (res.code != 0) {
	                    alert(res.msg);
	                    return;
	                }
	                var list = res.data;
	                var zNodes = getzNodes(list);
	                var zTreeObject = initZtreeNodes(zNodes, userId); // 初始化树形节点
	                setZtreeNodesChecked(zTreeObject, userId); // 设置某些节点为已选中状态
	            	$('#btn_sure').click(function(){
				    	getCheckedNodesData(zTreeObject, userId);
				    	setBtnDisabledInSeconds($(this), 3);
				    });
	            }
	        });
	    }

	    function getzNodes(list) {
	    	var zNodes = [];
	        for (var i in list) {
	            var authority = list[i];
	            zNodes.push({
	                id: authority.id,
	                pId: authority.pid,
	                name: authority.name,
	                open: true
	            });
	        }
	        return zNodes;
	    }

	    function getCheckedNodesData(zTreeObject, userId) {
	        var nodes = zTreeObject.getCheckedNodes(true);
	        var authority = {};
	        for (var i in nodes) {
	            var node = nodes[i];
	            if (node.check_Child_State == 2) { // 有子菜单且全选
	                authority[node.id] = {
	                    pid: '',
	                    value: 2
	                };
	            } else if (node.check_Child_State == 1) { // 有子菜单，但部分选择
	                var item = {};
	                authority[node.id] = {
	                    pid: '',
	                    value: 1,
	                    child: {}
	                };
	                var childrenNodes = zTreeObject.getNodesByParam('pId', node.id);
	                for (var j in childrenNodes) {
	                    var childrenNode = childrenNodes[j];
	                    if (childrenNode.checked) {
	                        authority[node.id]['child'][childrenNode.id] = {
	                            pid: node.id,
	                            value: 1
	                        };
	                    }
	                }
	            } else if (node.check_Child_State == -1 && node.pId == null) { // 没有子菜单
	            	authority[node.id] = {
	                    pid: '',
	                    value: 2
	                };
	            }
	        }
	        var data = {
	            'cmd': 'authority',
	            'data': { id: userId, authority: authority }
	        };
	        window.parent.postMessage(JSON.stringify(data), '*');
	    }

	    function initZtreeNodes(zNodes, userId) {
	        var setting = {
	            view: {
	                selectedMulti: false
	            },
	            check: {
	                enable: true
	            },
	            data: {
	                simpleData: {
	                    enable: true
	                }
	            },
	            callback: {

	            }
	        };
	    	var zTreeObject = $.fn.zTree.init($("#treeDemo"), setting, zNodes);
	        return zTreeObject;
	    }

	    
	    function setZtreeNodesChecked(zTreeObject, userId) {
	        $.ajax({
	            type: "GET",
	            url: getRoleAuthInfoUrl,
	            data: {
	                id: userId
	            },
	            dataType: "json",
	            success: function(res) {
	                if (res.code != 0) {
	                    alert(res.msg);
	                    return;
	                }
	                var authority = res.data.authority;
	                if (!authority) return;
	                for (var parentName in authority) {
	                    var parent_key_code = parentName;
	                    var parentNode = authority[parentName];
	                    var pId = parentNode.pid;
	                    var value = parentNode.value;
	                    var pNode = zTreeObject.getNodeByParam('id', parent_key_code);
	                    if (value != '') {
	                        zTreeObject.checkNode(pNode, true, (pId == '' && value == 2));
	                    }
	                    var childrenNodes = parentNode['child'];
	                    for (var childName in childrenNodes) {
	                        var child_key_code = childName;
	                        var childNode = childrenNodes[childName];
	                        if (childNode['value'] == 1) {
	                            var cNode = zTreeObject.getNodeByParam('id', child_key_code, pNode);
	                            zTreeObject.checkNode(cNode, true);
	                        }
	                    }
	                }
	            }
	        });
	    }
	}

	function getQueryString(name) {
        var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)");
        var r = window.location.search.substr(1).match(reg);
        if (r != null) return unescape(r[2]);
        return null;
    }
	    
});
