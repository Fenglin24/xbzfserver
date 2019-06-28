var adminIndex = {
    initPage: function() {
        var self = this;
        self.initBtns(self);
    },
    initBtns: function(self) {
        self.initAddBtn(self);
        self.initEditBtn(self);
        self.initDeleteBtn(self);
        self.initLhBtn(self, 'free_in');
        self.initLhBtn(self, 'free_view');
        self.initLhBtn(self, 'tj');
        self.initLhBtn(self, 'check');
        self.initLhBtn(self, 'top');
        self.inttxxBtn(self);
        self.initInAllbtn(self);
        self.initOutAllBtn(self);
        self.initXxAllbtn(self);
        self.initDeleteAllBtn(self);
        self.initOnChangeSelect(self);
    },
    initOnChangeSelect: function(self) {
        $('#city').change(function() {
            var data = {
                id: $(this).val()
            }
            http.ajax(baseURL + '?s=admin/cate/get_cate_area', data, function(res) {
                if (res.code == 0) {
                    //返回options
                    $('#area').html(res.data);
                }
            });
        });
        var area = $('#area').attr('data-name');
        if (area !='') {
            var data = {
                id: $('#city').val(),
                option: area,
            }
            http.ajax(baseURL + '?s=admin/cate/get_cate_area', data, function(res) {
                if (res.code == 0) {
                    //返回options
                    $('#area').html(res.data);
                }
            });
        }
    },
    initInAllbtn: function(self) {
    	$('.in').click(function() {
    		$('#mainTable input').each(function() {
    			$(this).prop('checked', 'checked');
    		});
    	});
    },
    initOutAllBtn: function(self) {
    	$('.out').click(function() {
    		$('#mainTable input').each(function() {
    			$(this).prop('checked', '');
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
        form.find('input').each(function() {
            var id = $(this).attr('id');
            var value = $.trim(trDom.find('.' + id).text());
            $(this).attr('value', value);
        });
        form.find('#id').attr('value', id);
        form.find('#role_id option').removeAttr('selected');
        form.find('#role_id option[value="' + trDom.find('.role').data('id') + '"]').attr('selected', 'selected');
    },
    initAddBtn: function(self) {
        $('.add').click(function() {
            var form = $('#dialog_tpl');
            form.find('#id').attr('value', '0');
            form.find('#username').attr('value', '');
            form.find('#nick').attr('value', '');
            form.find('#password').attr('value', '');
            form.find('#nick').attr('value', '');
            form.find('#tel').attr('value', '');
            form.find('#email').attr('value', '');
            self.initEditDialog(self);
        });
    },
    initEditDialog: function(self) {
        var form = $('#dialog_tpl');
        pop.diyPage({
            id: 'editFormPage',
            title: '编辑用户',
            html: form.html()
        });

        $('#editFormPage #submit').click(function() {
            var data = $(this).parents('form').serializeObject();
            if (data.password != '') {
                // data.password = md5(data.password);
            }
            var action = data.id > 0 ? 'update' : 'update';
            http.ajax(baseURL + '?s=admin/user/' + action, data, function(res) {
                if (res.code == 0) {
                    pop.msg('保存成功', function() {
                        location.reload();
                    });
                }
            });
        });
    },
    initDeleteBtn: function(self) {
        $('.delete').click(function() {
            var trDom = $(this).parents('tr');
            var id = trDom.attr('data-id');
            var data = { id: id };
            pop.confirm('确认删除？不可恢复！', function() {
                http.ajax(baseURL + '?s=admin/houses/delete', data, function(res) {
                    if (res.code == 0) {
                        trDom.remove();
                        pop.closeAll();
                    }
                });
            });
        });
    },
    inttxxBtn: function(self) {
    	$('.xx').click(function() {
            var buttonText = trim($(this).text());
            if (buttonText == '下线') {
                status = 2;
            } else {
                status = 1;
            }
            var trDom = $(this).parents('tr');
            var id = trDom.attr('data-id');
            var data = { 
                id: id,
                status:status
            };
            pop.confirm('确认'+buttonText, function() {
                http.ajax(baseURL + '?s=admin/houses/xx', data, function(res) {
                    if (res.code == 0) {
                        // trDom.remove();
                        location.reload();
                        // pop.closeAll();
                    }
                });
            });
        });
    },
    initDeleteAllBtn: function(self) {
    	$('.deleteall').click(function() {
    		var ids = [];
    		$('#mainTable input').each(function() {
    			if ($(this).is(':checked') == true) {
    				ids[ids.length] = $(this).val();
    			}
    		});
    		ids = ids.join(',');
    		console.log(ids);
    		if (ids == '') {
    			pop.msg('请选择房源');
    			return ;
    		}

            var data = { id: ids };
            pop.confirm('确认删除？不可恢复！', function() {
                http.ajax(baseURL + '?s=admin/houses/deleteAll', data, function(res) {
                    if (res.code == 0) {
                    location.reload();
                    }
                });
            });
    	});
    },
    initXxAllbtn:function(self) {
    	$('.xxall').click(function() {
    		var ids = [];
    		$('#mainTable input').each(function() {
    			if ($(this).is(':checked') == true) {
    				ids[ids.length] = $(this).val();
    			}
    		});
    		ids = ids.join(',');
    		console.log(ids);
    		if (ids == '') {
    			pop.msg('请选择房源');
    			return ;
    		}

            var data = { id: ids };
            pop.confirm('确认下线？不可恢复！', function() {
                http.ajax(baseURL + '?s=admin/houses/xxAll', data, function(res) {
                    if (res.code == 0) {
                    location.reload();
                    }
                });
            });
    	});
    },
    initLhBtn: function(self, key) {
        $('.'+key).click(function() {
            var trDom = $(this).parents('tr');
            var id = trDom.attr('data-id');
            var value = $(this).find('span').text();
            var data = { 
            	id:id ,
            	'value':value,
            	'key':key
            };
            http.ajax(baseURL + '?s=admin/houses/update_tag', data, function(res) {
                if (res.code == 0) {
                    // trDom.remove();
                    pop.msg('设置成功', function() {
                    location.reload();

                    })
                }
            });
        });
    }
};

$(function() {
    adminIndex.initPage();
});