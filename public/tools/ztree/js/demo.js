/**
 * 这里的js只是一个示例，需要结合具体环境去修改代码。pop.diyPage是一个弹窗，可以替代为自己的弹窗。
 */
$(".edit_authority").click(function() {
    var src = "/tools/ztree/ztree.html?id=" + $(this).parents("tr").data("id");
    var iframe = '<iframe marginwidth="0" id="iframeId" marginheight="0" frameborder="0" ' +
        'scrolling="yes"  width="300" height="300" src="' + src + '"></iframe>';
    pop.diyPage({
        title: "权限设置",
        html: iframe
    });
});
$(window).bind('message', function(event) {
    var msg = event.originalEvent.data;
    if (msg != 'close' && msg.substr(0, 1) != '{') return;
    var obj = JSON.parse(msg);
    if (obj.cmd != 'authority') return;
    http.ajax('/?m=Admin&c=Admin&a=save_role', obj.data, function(res) {
        if (res.code == 0) {
            pop.msg("保存成功", function() {
                pop.closeAll()
            });
        }
    });
});