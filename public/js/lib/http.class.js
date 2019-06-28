if (typeof pop == 'undefined') {
    pop = {
        msg: function(str) {
            console.log(str)
        }
    };
}
var http = {
    ajaxPostForm: function(url, jqueryFormSelector, successCallback, options, showError) {
        var defaultOptions = {type: 'POST', url: url};
        pop.loading();
        this.ajaxForm(url, jqueryFormSelector, $.extend({}, defaultOptions, options), successCallback, showError);
    },
    ajaxForm: function(url, jqueryFormSelector, options, successCallback, showError) {
        if (undefined === showError) { // 默认显示错误
            showError = true;
        }
        var defaultOptions = {
            target: null, // target element(s) to be updated with server response 
            beforeSubmit: function(formData, $form, options){
                // 这里可以做Jquery的validate
            },
            success: function(responseText, statusText, xhr, $form) {
                pop.close();
                var res = responseText;
                if (res.code != 0 && showError) {
                    pop.msg(res.msg);
                }
                successCallback && successCallback(res);
            }, 
            error: function(xhr, status, error) {
                pop.close();
                pop.msg('网络故障');
            },
            uploadProgress: function(event, position, total, percent) {
            },
            url:       '',         // override for form's 'action' attribute 
            type:      'POST',        // 'get' or 'post', override for form's 'method' attribute 
            dataType:  'json',        // 'xml', 'script', or 'json' (expected server response type) 
            clearForm: false,        // clear all form fields after successful submit 
            resetForm: false,        // reset the form after successful submit 

            // $.ajax options can be used here too, for example: 
            timeout:   30000 
        };
        jqueryFormSelector.ajaxSubmit($.extend({}, defaultOptions, options));; 
    },
    ajaxFormData: function(url, data, callback, cross, showError) {
        this.ajax(url, data, callback, 'POST', true, cross, showError);
    },
    ajaxGet: function(url, data, callback, cross, showError) {
        this.ajax(url, data, callback, 'GET', false, cross, showError);
    },
    ajaxPost: function(url, data, callback, cross, showError) {
        this.ajax(url, data, callback, 'POST', false, cross, showError);
    },
    ajaxPatch: function(url, data, callback, cross, showError) {
        this.ajax(url, data, callback, 'PATCH', false, cross, showError);
    },
    ajaxPut: function(url, data, callback, cross, showError) {
        this.ajax(url, data, callback, 'PUT', false, cross, showError);
    },
    ajaxDelete: function(url, data, callback, cross, showError) {
        this.ajax(url, data, callback, 'DELETE', false, cross, showError);
    },
    ajax: function(url, data, callback, method, multi, cross, showError) {
        if (!method) {
            method = 'POST';
        }
        if (!multi) { // 默认普通方式POST数据
            multi = false;
        }
        if (undefined === showError) { // 默认显示错误
            showError = true;
        }
        var option = {
            type: method,
            url: url,
            dataType: 'json',
            data: data
        };
        if (cross) { // 跨域时使用
            option.crossDomain = true;
            option.xhrFields = {
                withCredentials: true
            };
        }
        if (multi) { // POST文件时使用
            option.processData = false;
            option.contentType = false;
        }
        option.success = function(res) {
            if (res.code != 0 && showError) {
                pop.msg(res.msg);
            } 
            if (typeof callback == 'function') callback(res);
        };
        option.error = function(XMLHttpRequest, textStatus, errorThrown) {
            pop.msg('网络故障');
        };
        $.ajax(option);
    }
};