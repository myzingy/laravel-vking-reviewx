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
/******/ 	return __webpack_require__(__webpack_require__.s = 32);
/******/ })
/************************************************************************/
/******/ ({

/***/ 32:
/***/ (function(module, exports, __webpack_require__) {

__webpack_require__(33);
module.exports = __webpack_require__(34);


/***/ }),

/***/ 33:
/***/ (function(module, exports) {

/**
 * Created by vking on 2017/9/8.
 */
(function () {
    var config = {
        dom_id: "oneday_thread",
        appid: "",
        page_id: "",
        page_url: location.href,
        user_id: "",
        user_name: "",
        view: "" //def list + form+
    };
    if (typeof oneConfig != 'undefined') {
        for (var i in config) {
            if (typeof oneConfig[i] != 'undefined') {
                config[i] = oneConfig[i];
            }
        }
    }
    var $d = document.getElementById(config.dom_id);
    var src = 'http://laravel.vking/iframe?';
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
})();

/***/ }),

/***/ 34:
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ })

/******/ });