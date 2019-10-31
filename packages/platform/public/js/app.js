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
/******/ 	return __webpack_require__(__webpack_require__.s = 0);
/******/ })
/************************************************************************/
/******/ ([
/* 0 */
/***/ (function(module, exports, __webpack_require__) {

__webpack_require__(1);
module.exports = __webpack_require__(2);


/***/ }),
/* 1 */
/***/ (function(module, exports) {

$(function () {

    new SimpleBar($('[data-role="sidebar"]')[0]);

    $('.ui.checkbox').checkbox();
    $('.ui.accordion').accordion();

    $('.ui.dropdown:not(.tag)').dropdown({
        forceSelection: false
    });

    $('.ui.dropdown.tag').each(function () {

        var selected = $(this).data('value').split(',');
        if (selected.length == 1 && selected[0] == '') {
            selected = false;
        }

        $(this).dropdown({
            forceSelection: false,
            allowAdditions: true,
            keys: {
                delimiter: 13
            }
        }).dropdown('set selected', selected);
    });

    $('.checkbox[data-toggle="checkall"]').each(function () {
        var $parent = $(this);
        var $childCheckbox = $(document).find($parent.data('selector'));

        $parent.checkbox({
            // check all children
            onChecked: function onChecked() {
                $childCheckbox.checkbox('check');
            },
            // uncheck all children
            onUnchecked: function onUnchecked() {
                $childCheckbox.checkbox('uncheck');
            }
        });

        $childCheckbox.checkbox({
            // Fire on load to set parent value
            fireOnInit: true,
            // Change parent state on each child checkbox change
            onChange: function onChange() {
                var $parentCheckbox = $parent,
                    $checkbox = $childCheckbox,
                    allChecked = true,
                    allUnchecked = true,
                    ids = [];
                // check to see if all other siblings are checked or unchecked
                $checkbox.each(function () {
                    if ($(this).checkbox('is checked')) {
                        allUnchecked = false;
                        ids.push($(this).children().first().val());
                    } else {
                        allChecked = false;
                    }
                });

                // change multiple delete form action, based on selected ids
                var form = $('form[data-type="delete-multiple"]');
                if (form.length > 0) {
                    var url = $('form[data-type="delete-multiple"]').attr('action');
                    var replaceStartFrom = url.lastIndexOf('/');
                    var newUrl = url.substr(0, replaceStartFrom) + '/' + ids.join(',');
                    $('form[data-type="delete-multiple"]').attr('action', newUrl);
                }

                // set parent checkbox state, but dont trigger its onChange callback
                if (allChecked) {
                    $parentCheckbox.checkbox('set checked');
                    form.find('[type="submit"]').removeClass('disabled');
                    //form.css('visibility', 'visible');
                } else if (allUnchecked) {
                    $parentCheckbox.checkbox('set unchecked');
                    form.find('[type="submit"]').addClass('disabled');
                    //form.css('visibility', 'hidden');
                } else {
                    $parentCheckbox.checkbox('set indeterminate');
                    form.find('[type="submit"]').removeClass('disabled');
                    //form.css('visibility', 'visible');
                }
            }
        });
    });
});

/***/ }),
/* 2 */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ })
/******/ ]);