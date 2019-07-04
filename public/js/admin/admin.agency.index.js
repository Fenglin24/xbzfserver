var adminNewsIndex = {
    initPage: function() {
        var self = this;
        self.initDeleteBtn(self);
        self.initXxBtn(self);

    },
    initDeleteBtn: function(self) {
        $('.delete').click(function(){
            var trDom = $(this).parents('tr');
            var id = trDom.attr('data-id');
            var data = {id: id};
            pop.confirm('确认删除？不可恢复', function(){
                http.ajax(baseURL + '?s=admin/agency/delete', data, function(res){
                    if (res.code == 0) {
                        trDom.remove();
                        pop.closeAll();
                    }
                });
            });
        });
    },
    initXxBtn: function(self) {
        $('.status').click(function() {
            var id = $(this).parents('tr').attr('data-id');
            var data = {
                'id': id
            };
            http.ajax(baseURL + '?s=admin/agency/xx', data, function(res){
                if (res.code == 0) {
                    location.reload();
                }
            });
        });
    }
};

$(function(){
    adminNewsIndex.initPage();
});