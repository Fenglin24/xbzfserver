var routeIndex = {
	initPage: function() {
		var self = this;
		self.initBtns(self);
	}, 
	initBtns: function(self) {
		self.initAddRouteBtn(self);
		self.initDelRouteBtn(self);
		self.initSearchBtn(self);
	}, 
	initAddRouteBtn: function(self) {
		$('.add_route').click(function(){
            var routes = $('#from_routes').val();
            ajaxSaveRoutes(routes);
        });

        $('.addRouteBtn').click(function() {
            var routes = [$('#route_name').val()];
            ajaxSaveRoutes(routes);
        });

        function ajaxSaveRoutes(routes) {
            if (routes.length == 0) return false;
            http.ajaxPost(baseURL + '?s=admin/route/add_routes', {'routes': routes}, function(res){
                if (res.code == 0) {
                    var dom = $('#to_routes').empty();
                    for (var i in res.data) {
                        var route_name = res.data[i];
                        dom.append('<option value="'+route_name+'">'+route_name+'</option>');
                    }
                    $('#from_routes option:selected').remove();
                }
            });
        }
	}, 
	initDelRouteBtn: function(self) {
		$('.del_route').click(function(){
			var routes = $('#to_routes').val();
			http.ajax(baseURL + '?s=admin/route/del_routes', {'routes': routes}, function(res){
				if (res.code == 0) {
					var dom = $('#from_routes').empty();
					for (var i in res.data) {
						var route_name = res.data[i];
						dom.append('<option value="'+route_name+'">'+route_name+'</option>');
					}
					$('#to_routes option:selected').remove();
				}
			});
		});
	},
	initSearchBtn: function(self) {
		$('.search').keyup(function(){
			var search_value = $.trim($(this).val());
			var dom = $(this).parent().find('select');
			dom.find('option').each(function(){
				var this_value = $(this).attr('value');
				if (this_value.indexOf(search_value) == -1) {
					$(this).hide();
				} else {
					$(this).show();
				}
			});
		});
	}
}

$(function(){
	routeIndex.initPage();
});