/**
 * Helper function to parse query string (e.g. ?param1=value&parm2=...).
 */
function parseQueryString(query) {
    query = query ? query : document.location.search.substring(1);
    if (!query) return {};
    var parts = query.split('&');
    var params = {};
    for (var i = 0, ii = parts.length; i < ii; ++i) {
        var param = parts[i].split('=');
        var key = param[0].toLowerCase();
        var value = param.length > 1 ? param[1] : null;
        params[decodeURIComponent(key)] = decodeURIComponent(value);
    }
    return params;
}