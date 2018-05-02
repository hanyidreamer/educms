window.FMTrack = (function() {
    var _url = window.location.search && window.location.search.toLowerCase();
    var params = getParams(_url);
    var code = params.vcode || params.vocde;
    if (code) { putVcode(code) }

    function getParams(url) {
        var theRequest = new Object();
        if (url.indexOf("?") != -1) {
            var str = url.substr(1);
            strs = str.split("&");
            for (var i = 0; i < strs.length; i++) { theRequest[strs[i].split("=")[0]] = unescape(strs[i].split("=")[1]) }
        }
        return theRequest
    }

    function putVcode(vcode) {
        if (!vcode) {
            return
        }
        window.localStorage.vcode = vcode;
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.withCredentials = true;
        xmlhttp.open("PUT", "/api/v1/common/vcode/" + vcode);
        xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xmlhttp.send("vcode=" + vcode);
        xmlhttp.onreadystatechange = function() {
            if (xmlhttp.readyState == 4 && xmlhttp.status == 200) { window.localStorage.vcode = vcode; }
        }
    }
    return {
        getVcode: function(callback) {
            if (window.localStorage.vcode) {
                return callback(window.localStorage.vcode)
            }
            var xmlhttp = new XMLHttpRequest();
            xmlhttp.withCredentials = true;
            xmlhttp.open("GET", "/api/v1/common/vcode");
            xmlhttp.send('');
            xmlhttp.onreadystatechange = function() {
                if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                    var result = null;
                    try {
                        var obj = JSON.parse(xmlhttp.responseText);
                        if (obj.code == 0) {
                            result = obj.data.vcode;
                            window.localStorage.vcode = result;
                        }
                    } catch (e) {
                        return callback(result);
                    }
                    return callback(result);
                }
            }

            setTimeout(function() {
                callback(null);
            }, 5000);
        }
    }
})();