var adminIndex = {
    initPage: function() {
        var self = this;
        self.initBtns(self);
    },
    initBtns: function(self) {
        self.initAddBtn(self);
        self.initEditBtn(self);
        self.initDeleteBtn(self);
        self.initHotBtn(self);
        self.initInOutBtn(self);
        self.initAddXbtn(self);
        self.initEditXBtn(self);
        // self.initLhBtn(self);
    },
    initAddXbtn: function(self) {
        $('.addX').click(function() {
            var form = $('#dialog_tpl_X');
            var a = $(this).parents('tr').find('.pid').text();
            // alert(a)
            form.find('#pid').attr('value', a);
            
            self.initEditXDialog(self);
        });
    },
    initEditXBtn: function(self) {
        $('#mainTable').on('click', '.editX', function() {
            
            var trDom = $(this).parents('tr');
            var form = $('#dialog_tpl_X');
            self.setTplFormDataFromTr(form, trDom);
            var a = $(this).parents('tr').find('.pid').attr('data-pid');
             form.find('#pid').attr('value', a);
             var type = trDom.attr('data-type');
             console.log(form.find("input:radio[value='"+type+"']"))
       form.find("input:radio[value='"+type+"']").attr('checked', 'checked');

            self.initEditXDialog(self);
        });
    },
    initEditXDialog: function(self) {
        var form = $('#dialog_tpl_X');
        pop.diyPage({
            id: 'editFormPage',
            title: '编辑区域/校区',
            html: form.html()
        });

        $('#editFormPage #submit').click(function() {
            var data = $(this).parents('form').serializeObject();
            console.log(data);
            // return

            var action = data.id > 0 ? 'update' : 'update';
            http.ajax(baseURL + '?s=admin/cate/' + action, data, function(res) {
                if (res.code == 0) {
                    pop.msg('保存成功', function(){
                        location.reload();
                    });
                }
            });
        });
    },
    initInOutBtn: function(self) {
        $('.aid').click(function() {
            var textDom = trim($(this).text());
            var id = $(this).parents('tr').attr('data-id');

            if (textDom == '+') {
                $(this).text('-');
                $('#mainTable tbody').find('tr').each(function() {
                    if ($(this).attr('data-pid') == id) {
                        $(this).removeClass('hidden')
                    }
                })
            } else {
                $(this).text('+');
                $('#mainTable tbody').find('tr').each(function() {
                    if ($(this).attr('data-pid') == id) {
                        var aid = $(this).attr('data-id');
                        $('#mainTable tbody').find('tr').each(function() {
                            if ($(this).attr('data-pid') == aid) {
                                $(this).addClass('hidden')
                            }
                        })
                        $(this).addClass('hidden')
                    }
                })
            }
        })
    },
    initHotBtn: function(self) {
        $('.hotDom').click(function() {
            var data = {
                id:$(this).parents('tr').attr('data-id'),
                hot: $(this).text()
            };
            console.log(data);
            // return;
            http.ajax(baseURL + '?s=admin/cate/hot', data, function(res) {
                if (res.code == 0) {
                    pop.msg('保存成功', function(){
                        location.reload();
                    });
                }
            });
        });
    },
    initEditBtn: function(self) {
        $('.edit').click(function() {
            var trDom = $(this).parents('tr');
            var form = $('#dialog_tpl');
            self.setTplFormDataFromTr(form, trDom);
            
            self.initEditDialog(self);
        });
    },
    setTplFormDataFromTr: function(form, trDom) {
        var id = trDom.attr('data-id');
        // form.find('input').each(function(){
        //     var id = $(this).attr('id');
        //     var value = $.trim(trDom.find('.' + id).text() );
        //     $(this).attr('value', value);
        // });
        form.find('#id').attr('value', id);
        form.find('#name').attr('value', trDom.find('.name').text());
        form.find('#dsn').attr('value', trDom.find('.dsn').text());
        form.find('#pid option').removeAttr('selected');
        form.find('#pid option[value="'+trDom.find('.pid').data('pid')+'"]').attr('selected', 'selected');
    },
    initAddBtn: function(self) {
        $('.add').click(function() {
            var form = $('#dialog_tpl');
            form.find('#name').attr('value', '');
            form.find('#dsn').attr('value', '');
            
            self.initEditDialog(self);
        });
    },
    initEditDialog: function(self) {
        var form = $('#dialog_tpl');
        pop.diyPage({
            id: 'editFormPage',
            title: '编辑城市',
            html: form.html()
        });

        $('#editFormPage #submit').click(function() {
            var data = $(this).parents('form').serializeObject();
            console.log(data);
            // return

            var action = data.id > 0 ? 'update' : 'update';
            http.ajax(baseURL + '?s=admin/cate/' + action, data, function(res) {
                if (res.code == 0) {
                    pop.msg('保存成功', function(){
                        location.reload();
                    });
                }
            });
        });
    }, 
    initDeleteBtn: function(self) {
        $('.delete').click(function(){
            var trDom = $(this).parents('tr');
            var id = trDom.attr('data-id');
            var data = {id: id};
            pop.confirm('确认删除？不可恢复！', function(){
                http.ajax(baseURL + '?s=admin/cate/delete', data, function(res) {
                    if (res.code == 0) {
                        trDom.remove();
                        pop.closeAll();
                    }
                });
            });
        });
    },
    initLhBtn: function(self) {
        $('.lh').click(function(){
            var trDom = $(this).parents('tr');
            var id = trDom.attr('data-id');
            var data = {id: id};
            pop.confirm('确认拉黑？', function(){
                http.ajax(baseURL + '?s=admin/cate/lh', data, function(res) {
                    if (res.code == 0) {
                        // trDom.remove();
                        location.reload();
                    }
                });
            });
        });
    }
};

$(function() {
    adminIndex.initPage();
});