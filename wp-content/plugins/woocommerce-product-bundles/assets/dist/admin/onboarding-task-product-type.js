/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/******/ 	// The require scope
/******/ 	const __webpack_require__ = {};
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/concatenation wrap */
/******/ 	// wrap a concatenated module body as a lazy, memoized accessor; mod is
/******/ 	// set before the body runs so re-entrant calls (require cycles) observe
/******/ 	// the partial exports like Node.js
/******/ 	__webpack_require__.cw = (body) => {
/******/ 		var mod;
/******/ 		return () => {
/******/ 			if (body) {
/******/ 				var fn = body;
/******/ 				body = 0;
/******/ 				mod = { exports: {} };
/******/ 				fn.call(mod.exports, mod, mod.exports);
/******/ 			}
/******/ 			return mod.exports;
/******/ 		};
/******/ 	};
/******/ 	
/************************************************************************/

// MODULE: ./node_modules/gridicons/dist/folder-multiple.js
var folder_multiple_namespaceFn = /*#__PURE__*/__webpack_require__.cw(function(module, exports) {
var __webpack_unused_export__;
__webpack_unused_export__ = ({value:!0}),exports.A=_default;var _react=_interopRequireDefault((external_React_namespaceFn())),_excluded=["size","onClick","icon","className"];function _interopRequireDefault(a){return a&&a.__esModule?a:{default:a}}function _extends(){return _extends=Object.assign?Object.assign.bind():function(a){for(var b,c=1;c<arguments.length;c++)for(var d in b=arguments[c],b)({}).hasOwnProperty.call(b,d)&&(a[d]=b[d]);return a},_extends.apply(null,arguments)}function _objectWithoutProperties(a,b){if(null==a)return{};var c,d,e=_objectWithoutPropertiesLoose(a,b);if(Object.getOwnPropertySymbols){var f=Object.getOwnPropertySymbols(a);for(d=0;d<f.length;d++)c=f[d],-1===b.indexOf(c)&&{}.propertyIsEnumerable.call(a,c)&&(e[c]=a[c])}return e}function _objectWithoutPropertiesLoose(a,b){if(null==a)return{};var c={};for(var d in a)if({}.hasOwnProperty.call(a,d)){if(-1!==b.indexOf(d))continue;c[d]=a[d]}return c}function _default(a){var b=a.size,c=void 0===b?24:b,d=a.onClick,e=a.icon,f=a.className,g=_objectWithoutProperties(a,_excluded),h=["gridicon","gridicons-folder-multiple",f,!1,!1,!1].filter(Boolean).join(" ");return _react["default"].createElement("svg",_extends({className:h,height:c,width:c,onClick:d},g,{xmlns:"http://www.w3.org/2000/svg",viewBox:"0 0 24 24"}),_react["default"].createElement("g",null,_react["default"].createElement("path",{d:"M4 8a2 2 0 00-2 2v10c0 1.1.9 2 2 2h14a2 2 0 002-2H4V8zm16 10H8a2 2 0 01-2-2V6a2 2 0 012-2h3a2 2 0 012 2h7a2 2 0 012 2v8a2 2 0 01-2 2z"})))}

});

// MODULE: ./node_modules/react/cjs/react-jsx-runtime.production.min.js
var react_jsx_runtime_production_min_namespaceFn = /*#__PURE__*/__webpack_require__.cw(function(module, exports) {
var __webpack_unused_export__;
/**
 * @license React
 * react-jsx-runtime.production.min.js
 *
 * Copyright (c) Facebook, Inc. and its affiliates.
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 */
var f=(external_React_namespaceFn()),k=Symbol.for("react.element"),l=Symbol.for("react.fragment"),m=Object.prototype.hasOwnProperty,n=f.__SECRET_INTERNALS_DO_NOT_USE_OR_YOU_WILL_BE_FIRED.ReactCurrentOwner,p={key:!0,ref:!0,__self:!0,__source:!0};
function q(c,a,g){var b,d={},e=null,h=null;void 0!==g&&(e=""+g);void 0!==a.key&&(e=""+a.key);void 0!==a.ref&&(h=a.ref);for(b in a)m.call(a,b)&&!p.hasOwnProperty(b)&&(d[b]=a[b]);if(c&&c.defaultProps)for(b in a=c.defaultProps,a)void 0===d[b]&&(d[b]=a[b]);return{$$typeof:k,type:c,key:e,ref:h,props:d,_owner:n.current}}__webpack_unused_export__=l;exports.jsx=q;__webpack_unused_export__=q;

});

// MODULE: ./node_modules/react/jsx-runtime.js
var jsx_runtime_namespaceFn = /*#__PURE__*/__webpack_require__.cw(function(module, exports) {


if (true) {
  module.exports = (react_jsx_runtime_production_min_namespaceFn());
} else // removed by dead control flow
{}

});

// MODULE: external "React"
var external_React_namespaceFn = /*#__PURE__*/__webpack_require__.cw(function(module, exports) {
module.exports = window["React"];
});

;// external ["wp","hooks"]
const external_wp_hooks_namespaceObject = window["wp"]["hooks"];
;// external ["wp","element"]
const external_wp_element_namespaceObject = window["wp"]["element"];
;// ./node_modules/@wordpress/icons/build-module/icon/index.mjs
// packages/icons/src/icon/index.ts

var icon_default = (0,external_wp_element_namespaceObject.forwardRef)(
  ({ icon, size = 24, ...props }, ref) => {
    return (0,external_wp_element_namespaceObject.cloneElement)(icon, {
      width: size,
      height: size,
      ...props,
      ref
    });
  }
);

//# sourceMappingURL=index.mjs.map

;// external ["wp","primitives"]
const external_wp_primitives_namespaceObject = window["wp"]["primitives"];
;// ./node_modules/react/jsx-runtime.js
jsx_runtime_namespaceFn();

;// ./node_modules/@wordpress/icons/build-module/library/chevron-right.mjs
// packages/icons/src/library/chevron-right.tsx


var chevron_right_default = /* @__PURE__ */ (0,react_jsx_runtime_production_min_namespaceFn().jsx)(external_wp_primitives_namespaceObject.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", children: /* @__PURE__ */ (0,react_jsx_runtime_production_min_namespaceFn().jsx)(external_wp_primitives_namespaceObject.Path, { d: "M10.6 6L9.4 7l4.6 5-4.6 5 1.2 1 5.4-6z" }) });

//# sourceMappingURL=chevron-right.mjs.map

;// external ["wp","i18n"]
const external_wp_i18n_namespaceObject = window["wp"]["i18n"];
;// ./node_modules/gridicons/dist/folder-multiple.js
folder_multiple_namespaceFn();

;// ./resources/js/admin/onboarding-task-product-type/index.js
/**
 * External dependencies
 */





(0,external_wp_hooks_namespaceObject.addFilter)('experimental_woocommerce_tasklist_product_types', 'woocommerce-product-bundles', productTypes => [...productTypes, {
  key: 'product-bundles',
  title: (0,external_wp_i18n_namespaceObject.__)('Product bundle', 'woocommerce-product-bundles'),
  content: (0,external_wp_i18n_namespaceObject.__)('A bundle of products.', 'woocommerce-product-bundles'),
  before: /*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsx)((folder_multiple_namespaceFn().A), {}),
  after: /*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsx)(icon_default, {
    icon: chevron_right_default
  })
}]);
/******/ })()
;