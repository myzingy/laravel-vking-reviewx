/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, {
/******/ 				configurable: false,
/******/ 				enumerable: true,
/******/ 				get: getter
/******/ 			});
/******/ 		}
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "";
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = 35);
/******/ })
/************************************************************************/
/******/ ({

/***/ 35:
/***/ (function(module, exports, __webpack_require__) {

__webpack_require__(36);
module.exports = __webpack_require__(37);


/***/ }),

/***/ 36:
/***/ (function(module, exports) {

/**
 * Created by vking on 2017/9/8.
 */
(function ($) {
    var base_url = 'http://laravel.vking/';
    var config = {
        dom_id: "oneday_thread",
        appid: "",
        page_id: "",
        page_url: location.href,
        user_id: "",
        user_name: "",
        target_id: "",
        target_sku: "",
        view: "" //def list + form ||  no
    };
    if (typeof oneConfig != 'undefined') {
        for (var i in config) {
            if (typeof oneConfig[i] != 'undefined') {
                config[i] = oneConfig[i];
            }
        }
    }
    if (config.view != 'no') {
        var $d = document.getElementById(config.dom_id);
        var src = base_url + 'review?';
        src += encodeURIComponent(JSON.stringify(config));
        $d.innerHTML = '<iframe id="dsq-app8967" name="dsq-app8967" allowtransparency="true" frameborder="0"' + ' scrolling="no" tabindex="0" title="oneday" width="100%" src="' + src + '" horizontalscrolling="no" verticalscrolling="no"></iframe>';
        var OnMessage = function OnMessage(e) {
            console.log("OnMessage", e);
            if (typeof e.data.oneday != 'undefined') {
                document.getElementById('dsq-app8967').height = e.data.height;
            }
        };
        if (window.addEventListener) {
            // all browsers except IE before version 9
            window.addEventListener("message", OnMessage, false);
        } else {
            if (window.attachEvent) {
                // IE before version 9
                window.attachEvent("onmessage", OnMessage);
            }
        }
    }
    if (typeof jQuery == 'undefined') {
        //https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js
        console.log('jquery is undefined!!!');
        (function (d, s, id) {
            var js,
                fjs = d.getElementsByTagName(s)[0];
            if (d.getElementById(id)) return;
            js = d.createElement(s);js.id = id;
            js.src = "https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js";
            fjs.parentNode.insertBefore(js, fjs);
        })(document, 'script', 'oneday-review-jssdk');
    }
    window.onedayReview = function (fun) {
        if (typeof jQuery == 'undefined') {
            console.log('jquery on loading...');
            setTimeout(function () {
                onedayReview(fun);
            }, 500);
            return;
        }
        console.log('jquery on loaded!');
        var url = base_url + "api/getReviewTotal";
        var data = {};
        jQuery.ajax({
            url: url,
            data: data,
            jsonp: 'callback',
            dataType: 'jsonp',
            success: function success(json) {
                if (fun) fun(json);
            }
        });
    };
})();

/***/ }),

/***/ 37:
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ })

/******/ });