/******/ (() => { // webpackBootstrap
/******/ 	var __webpack_modules__ = ([
/* 0 */,
/* 1 */
/***/ (() => {

/***
@module up
*/
window.up = {
    version: '2.6.1'
};


/***/ }),
/* 2 */
/***/ (() => {

up.mockable = function (originalFn) {
    let spy;
    const mockableFn = function () {
        return (spy || originalFn).apply(null, arguments);
    };
    mockableFn.mock = () => spy = jasmine.createSpy('mockable', originalFn);
    document.addEventListener('up:framework:reset', () => spy = null);
    return mockableFn;
};


/***/ }),
/* 3 */
/***/ (() => {

/*-
Utility functions
=================

The `up.util` module contains functions to facilitate the work with basic JavaScript
values like lists, strings or functions.

You will recognize many functions form other utility libraries like [Lodash](https://lodash.com/).
While feature parity with Lodash is not a goal of `up.util`, you might find it sufficient
to not include another library in your asset bundle.

@see url-patterns

@module up.util
*/
up.util = (function () {
    /*-
    A function that does nothing.
  
    @function up.util.noop
    @experimental
    */
    function noop() {
    }
    /*-
    A function that returns a resolved promise.
  
    @function up.util.asyncNoop
    @internal
    */
    function asyncNoop() {
        return Promise.resolve();
    }
    /*-
    Ensures that the given function can only be called a single time.
    Subsequent calls will return the return value of the first call.
  
    Note that this is a simple implementation that
    doesn't distinguish between argument lists.
  
    @function up.util.memoize
    @internal
    */
    function memoize(func) {
        let cachedValue, cached;
        return function (...args) {
            if (cached) {
                return cachedValue;
            }
            else {
                cached = true;
                return cachedValue = func.apply(this, args);
            }
        };
    }
    /*-
    Returns if the given port is the default port for the given protocol.
  
    @function up.util.isStandardPort
    @internal
    */
    function isStandardPort(protocol, port) {
        port = port.toString();
        return (((port === "") || (port === "80")) && (protocol === 'http:')) || ((port === "443") && (protocol === 'https:'));
    }
    const NORMALIZE_URL_DEFAULTS = {
        host: 'cross-domain',
    };
    /*-
    Returns a normalized version of the given URL string.
  
    Two URLs that point to the same resource should normalize to the same string.
  
    ### Comparing normalized URLs
  
    The main purpose of this function is to normalize two URLs for string comparison:
  
    ```js
    up.util.normalizeURL('http://current-host/path') === up.util.normalizeURL('/path') // => true
    ```
  
    By default the hostname is only included if it points to a different origin:
  
    ```js
    up.util.normalizeURL('http://current-host/path') // => '/path'
    up.util.normalizeURL('http://other-host/path') // => 'http://other-host/path'
    ```
  
    Relative paths are normalized to absolute paths:
  
    ```js
    up.util.normalizeURL('index.html') // => '/path/index.html'
    ```
  
    ### Excluding URL components
  
    You may pass options to exclude URL components from the normalized string:
  
    ```js
    up.util.normalizeURL('/foo?query=bar', { query: false }) => '/foo'
    up.util.normalizeURL('/bar#hash', { hash: false }) => '/bar'
    ```
  
    ### Limitations
  
    - Username and password are always omitted from the normalized URL.
    - Only `http` and `https` schemes are supported.
  
    @function up.util.normalizeURL
    @param {boolean} [options.host='cross-domain']
      Whether to include protocol, hostname and port in the normalized URL.
  
      When set to `'cross-domain'` (the default), the host is only included if it differ's from the page's hostname.
  
      The port is omitted if the port is the standard port for the given protocol, e.g. `:443` for `https://`.
    @param {boolean} [options.hash=true]
      Whether to include an `#hash` anchor in the normalized URL.
    @param {boolean} [options.search=true]
      Whether to include a `?query` string in the normalized URL.
    @param {boolean} [options.trailingSlash=true]
      Whether to include a trailing slash from the pathname.
    @return {string}
      The normalized URL.
    @experimental
    */
    function normalizeURL(urlOrAnchor, options) {
        options = newOptions(options, NORMALIZE_URL_DEFAULTS);
        const parts = parseURL(urlOrAnchor);
        let normalized = '';
        if (options.host === 'cross-domain') {
            options.host = isCrossOrigin(parts);
        }
        if (options.host) {
            normalized += parts.protocol + "//" + parts.hostname;
            // Once we drop IE11 we can just use { host }, which contains port and hostname
            // and also handles standard ports.
            // See https://developer.mozilla.org/en-US/docs/Web/API/URL/host
            if (!isStandardPort(parts.protocol, parts.port)) {
                normalized += `:${parts.port}`;
            }
        }
        let { pathname } = parts;
        if (options.trailingSlash === false && pathname !== '/') {
            pathname = pathname.replace(/\/$/, '');
        }
        normalized += pathname;
        if (options.search !== false) {
            normalized += parts.search;
        }
        if (options.hash !== false) {
            normalized += parts.hash;
        }
        return normalized;
    }
    function matchURLs(leftURL, rightURL) {
        return normalizeURL(leftURL) === normalizeURL(rightURL);
    }
    // We're calling isCrossOrigin() a lot.
    // Accessing location.protocol and location.hostname every time
    // is much slower than comparing cached strings.
    // https://jsben.ch/kBATt
    const APP_PROTOCOL = location.protocol;
    const APP_HOSTNAME = location.hostname;
    function isCrossOrigin(urlOrAnchor) {
        // If the given URL does not contain a hostname we know it cannot be cross-origin.
        // In that case we don't need to parse the URL.
        if (isString(urlOrAnchor) && (urlOrAnchor.indexOf('//') === -1)) {
            return false;
        }
        const parts = parseURL(urlOrAnchor);
        return (APP_HOSTNAME !== parts.hostname) || (APP_PROTOCOL !== parts.protocol);
    }
    /*-
    Parses the given URL into components such as hostname and path.
  
    If the given URL is not fully qualified, it is assumed to be relative
    to the current page.
  
    ### Example
  
    ```js
    let parsed = up.util.parseURL('/path?foo=value')
    parsed.pathname // => '/path'
    parsed.search // => '/?foo=value'
    parsed.hash // => ''
    ```
  
    @function up.util.parseURL
    @return {Object}
      The parsed URL as an object with
      `protocol`, `hostname`, `port`, `pathname`, `search` and `hash`
      properties.
    @stable
    */
    function parseURL(urlOrLink) {
        let link;
        if (isJQuery(urlOrLink)) {
            // In case someone passed us a $link, unwrap it
            link = up.element.get(urlOrLink);
        }
        else if (urlOrLink.pathname) {
            // If we are handed a parsed URL, just return it
            link = urlOrLink;
        }
        else {
            link = document.createElement('a');
            link.href = urlOrLink;
        }
        // In IE11 the #hostname and #port properties of unqualified URLs are empty strings.
        // We can fix this by setting the link's { href } on the link itself.
        if (!link.hostname) {
            link.href = link.href; // eslint-disable-line no-self-assign
        }
        // Some IEs don't include a leading slash in the #pathname property.
        // We have confirmed this in IE11 and earlier.
        if (link.pathname[0] !== '/') {
            // Only copy the link into an object when we need to (to change a property).
            // Note that we're parsing a lot of URLs for [up-active].
            link = pick(link, ['protocol', 'hostname', 'port', 'pathname', 'search', 'hash']);
            link.pathname = '/' + link.pathname;
        }
        return link;
    }
    /*-
    @function up.util.normalizeMethod
    @internal
    */
    function normalizeMethod(method) {
        return method ? method.toUpperCase() : 'GET';
    }
    /*-
    @function up.util.methodAllowsPayload
    @internal
    */
    function methodAllowsPayload(method) {
        return (method !== 'GET') && (method !== 'HEAD');
    }
    // Remove with IE11
    function assignPolyfill(target, ...sources) {
        for (let source of sources) {
            for (let key in source) {
                target[key] = source[key];
            }
        }
        return target;
    }
    /*-
    Merge the own properties of one or more `sources` into the `target` object.
  
    @function up.util.assign
    @param {Object} target
    @param {Array<Object>} sources...
    @stable
    */
    const assign = Object.assign || assignPolyfill;
    // Remove with IE11
    function valuesPolyfill(object) {
        return Object.keys(object).map((key) => object[key]);
    }
    /*-
    Returns an array of values of the given object.
  
    @function up.util.values
    @param {Object} object
    @return {Array<string>}
    @stable
    */
    const objectValues = Object.values || valuesPolyfill;
    function iteratee(block) {
        if (isString(block)) {
            return item => item[block];
        }
        else {
            return block;
        }
    }
    /*-
    Translate all items in an array to new array of items.
  
    @function up.util.map
    @param {Array} array
    @param {Function(element, index): any|String} block
      A function that will be called with each element and (optional) iteration index.
  
      You can also pass a property name as a String,
      which will be collected from each item in the array.
    @return {Array}
      A new array containing the result of each function call.
    @stable
    */
    function map(array, block) {
        if (array.length === 0) {
            return [];
        }
        block = iteratee(block);
        let mapped = [];
        for (let i = 0; i < array.length; i++) {
            let element = array[i];
            mapped.push(block(element, i));
        }
        return mapped;
    }
    /*-
    @function up.util.mapObject
    @internal
    */
    function mapObject(array, pairer) {
        const merger = function (object, pair) {
            object[pair[0]] = pair[1];
            return object;
        };
        return map(array, pairer).reduce(merger, {});
    }
    /*-
    Calls the given function for each element (and, optional, index)
    of the given array.
  
    @function up.util.each
    @param {Array} array
    @param {Function(element, index)} block
      A function that will be called with each element and (optional) iteration index.
    @stable
    */
    function each(array, block) {
        // note that the native Array.forEach is very slow (https://jsperf.com/fast-array-foreach)
        for (let i = 0; i < array.length; i++) {
            block(array[i], i);
        }
    }
    function eachIterator(iterator, callback) {
        let entry;
        while ((entry = iterator.next()) && !entry.done) {
            callback(entry.value);
        }
    }
    /*-
    Returns whether the given argument is `null`.
  
    @function up.util.isNull
    @param object
    @return {boolean}
    @stable
    */
    function isNull(object) {
        return object === null;
    }
    /*-
    Returns whether the given argument is `undefined`.
  
    @function up.util.isUndefined
    @param object
    @return {boolean}
    @stable
    */
    function isUndefined(object) {
        return object === undefined;
    }
    /*-
    Returns whether the given argument is not `undefined`.
  
    @function up.util.isDefined
    @param object
    @return {boolean}
    @stable
    */
    const isDefined = negate(isUndefined);
    /*-
    Returns whether the given argument is either `undefined` or `null`.
  
    Note that empty strings or zero are *not* considered to be "missing".
  
    For the opposite of `up.util.isMissing()` see [`up.util.isGiven()`](/up.util.isGiven).
  
    @function up.util.isMissing
    @param object
    @return {boolean}
    @stable
    */
    function isMissing(object) {
        return isUndefined(object) || isNull(object);
    }
    /*-
    Returns whether the given argument is neither `undefined` nor `null`.
  
    Note that empty strings or zero *are* considered to be "given".
  
    For the opposite of `up.util.isGiven()` see [`up.util.isMissing()`](/up.util.isMissing).
  
    @function up.util.isGiven
    @param object
    @return {boolean}
    @stable
    */
    const isGiven = negate(isMissing);
    // isNan = (object) ->
    //   isNumber(value) && value != +value
    /*-
    Return whether the given argument is considered to be blank.
  
    By default, this function returns `true` for:
  
    - `undefined`
    - `null`
    - Empty strings
    - Empty arrays
    - A plain object without own enumerable properties
  
    All other arguments return `false`.
  
    To check implement blank-ness checks for user-defined classes,
    see `up.util.isBlank.key`.
  
    @function up.util.isBlank
    @param value
      The value is to check.
    @return {boolean}
      Whether the value is blank.
    @stable
    */
    function isBlank(value) {
        if (isMissing(value)) {
            return true;
        }
        if (isObject(value) && value[isBlank.key]) {
            return value[isBlank.key]();
        }
        if (isString(value) || isList(value)) {
            return value.length === 0;
        }
        if (isOptions(value)) {
            return Object.keys(value).length === 0;
        }
        return false;
    }
    /*-
    This property contains the name of a method that user-defined classes
    may implement to hook into the `up.util.isBlank()` protocol.
  
    ### Example
  
    We have a user-defined `Account` class that we want to use with `up.util.isBlank()`:
  
    ```js
    class Account {
      constructor(email) {
        this.email = email
      }
  
      [up.util.isBlank.key]() {
        return up.util.isBlank(this.email)
      }
    }
    ```
  
    Note that the protocol method is not actually named `'up.util.isBlank.key'`.
    Instead it is named after the *value* of the `up.util.isBlank.key` property.
    To do so, the code sample above is using a
    [computed property name](https://medium.com/front-end-weekly/javascript-object-creation-356e504173a8)
    in square brackets.
  
    We may now use `Account` instances with `up.util.isBlank()`:
  
    ```js
    let foo = new Account('foo@foo.com')
    let bar = new Account('')
  
    console.log(up.util.isBlank(foo)) // prints false
    console.log(up.util.isBlank(bar)) // prints true
    ```
  
    @property up.util.isBlank.key
    @experimental
    */
    isBlank.key = 'up.util.isBlank';
    /*-
    Returns the given argument if the argument is [present](/up.util.isPresent),
    otherwise returns `undefined`.
  
    @function up.util.presence
    @param value
    @param {Function(value): boolean} [tester=up.util.isPresent]
      The function that will be used to test whether the argument is present.
    @return {any|undefined}
    @stable
    */
    function presence(value, tester = isPresent) {
        if (tester(value)) {
            return value;
        }
    }
    /*-
    Returns whether the given argument is not [blank](/up.util.isBlank).
  
    @function up.util.isPresent
    @param object
    @return {boolean}
    @stable
    */
    const isPresent = negate(isBlank);
    /*-
    Returns whether the given argument is a function.
  
    @function up.util.isFunction
    @param object
    @return {boolean}
    @stable
    */
    function isFunction(object) {
        return typeof (object) === 'function';
    }
    /*-
    Returns whether the given argument is a string.
  
    @function up.util.isString
    @param object
    @return {boolean}
    @stable
    */
    function isString(object) {
        return (typeof (object) === 'string') || object instanceof String;
    }
    /*-
    Returns whether the given argument is a boolean value.
  
    @function up.util.isBoolean
    @param object
    @return {boolean}
    @stable
    */
    function isBoolean(object) {
        return (typeof (object) === 'boolean') || object instanceof Boolean;
    }
    /*-
    Returns whether the given argument is a number.
  
    Note that this will check the argument's *type*.
    It will return `false` for a string like `"123"`.
  
    @function up.util.isNumber
    @param object
    @return {boolean}
    @stable
    */
    function isNumber(object) {
        return (typeof (object) === 'number') || object instanceof Number;
    }
    /*-
    Returns whether the given argument is an options hash,
  
    Differently from [`up.util.isObject()`], this returns false for
    functions, jQuery collections, promises, `FormData` instances and arrays.
  
    @function up.util.isOptions
    @param object
    @return {boolean}
    @internal
    */
    function isOptions(object) {
        return (typeof (object) === 'object') && !isNull(object) && (isUndefined(object.constructor) || (object.constructor === Object));
    }
    /*-
    Returns whether the given argument is an object.
  
    This also returns `true` for functions, which may behave like objects in JavaScript.
  
    @function up.util.isObject
    @param object
    @return {boolean}
    @stable
    */
    function isObject(object) {
        const typeOfResult = typeof (object);
        return ((typeOfResult === 'object') && !isNull(object)) || (typeOfResult === 'function');
    }
    /*-
    Returns whether the given argument is a [DOM element](https://developer.mozilla.org/de/docs/Web/API/Element).
  
    @function up.util.isElement
    @param object
    @return {boolean}
    @stable
    */
    function isElement(object) {
        return object instanceof Element;
    }
    /*-
    Returns whether the given argument is a [regular expression](https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/RegExp).
  
    @function up.util.isRegExp
    @param object
    @return {boolean}
    @internal
    */
    function isRegExp(object) {
        return object instanceof RegExp;
    }
    /*-
    Returns whether the given argument is a [jQuery collection](https://learn.jquery.com/using-jquery-core/jquery-object/).
  
    @function up.util.isJQuery
    @param object
    @return {boolean}
    @stable
    */
    function isJQuery(object) {
        return up.browser.canJQuery() && object instanceof jQuery;
    }
    /*-
    @function up.util.isElementish
    @param object
    @return {boolean}
    @internal
    */
    function isElementish(object) {
        return !!(object && (object.addEventListener || object[0]?.addEventListener));
    }
    /*-
    Returns whether the given argument is an object with a `then` method.
  
    @function up.util.isPromise
    @param object
    @return {boolean}
    @stable
    */
    function isPromise(object) {
        return isObject(object) && isFunction(object.then);
    }
    /*-
    Returns whether the given argument is an array.
  
    @function up.util.isArray
    @param object
    @return {boolean}
    @stable
    */
    // https://developer.mozilla.org/de/docs/Web/JavaScript/Reference/Global_Objects/Array/isArray
    const { isArray } = Array;
    /*-
    Returns whether the given argument is a `FormData` instance.
  
    Always returns `false` in browsers that don't support `FormData`.
  
    @function up.util.isFormData
    @param object
    @return {boolean}
    @internal
    */
    function isFormData(object) {
        return object instanceof FormData;
    }
    /*-
    Converts the given [array-like value](/up.util.isList) into an array.
  
    If the given value is already an array, it is returned unchanged.
  
    @function up.util.toArray
    @param object
    @return {Array}
    @stable
    */
    function toArray(value) {
        return isArray(value) ? value : copyArrayLike(value);
    }
    /*-
    Returns whether the given argument is an array-like value.
  
    Return true for `Array`, a
    [`NodeList`](https://developer.mozilla.org/en-US/docs/Web/API/NodeList),
     the [arguments object](https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Functions/arguments)
     or a jQuery collection.
  
    Use [`up.util.isArray()`](/up.util.isArray) to test whether a value is an actual `Array`.
  
    @function up.util.isList
    @param value
    @return {boolean}
    @stable
    */
    function isList(value) {
        return isArray(value) ||
            isNodeList(value) ||
            isArguments(value) ||
            isJQuery(value) ||
            isHTMLCollection(value);
    }
    /*-
    Returns whether the given value is a [`NodeList`](https://developer.mozilla.org/en-US/docs/Web/API/NodeList).
  
    `NodeLists` are array-like objects returned by [`document.querySelectorAll()`](https://developer.mozilla.org/en-US/docs/Web/API/Element/querySelectorAll).
  
    @function up.util.isNodeList
    @param value
    @return {boolean}
    @internal
    */
    function isNodeList(value) {
        return value instanceof NodeList;
    }
    function isHTMLCollection(value) {
        return value instanceof HTMLCollection;
    }
    /*-
    Returns whether the given value is an [arguments object](https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Functions/arguments).
  
    @function up.util.isArguments
    @param value
    @return {boolean}
    @internal
    */
    function isArguments(value) {
        return Object.prototype.toString.call(value) === '[object Arguments]';
    }
    function nullToUndefined(value) {
        if (!isNull(value)) {
            return value;
        }
    }
    /*-
    Returns the given value if it is [array-like](/up.util.isList), otherwise
    returns an array with the given value as its only element.
  
    ### Example
  
    ```js
    up.util.wrapList([1, 2, 3]) // => [1, 2, 3]
    up.util.wrapList('foo') // => ['foo']
    ```
  
    @function up.util.wrapList
    @param {any} value
    @return {Array|NodeList|jQuery}
    @experimental
    */
    function wrapList(value) {
        if (isList(value)) {
            return value;
        }
        else if (isMissing(value)) {
            return [];
        }
        else {
            return [value];
        }
    }
    /*-
    Returns a shallow copy of the given value.
  
    ### Copying protocol
  
    - By default `up.util.copy()` can copy [array-like values](/up.util.isList),
      plain objects and `Date` instances.
    - Array-like objects are copied into new arrays.
    - Unsupported types of values are returned unchanged.
    - To make the copying protocol work with user-defined class,
      see `up.util.copy.key`.
    - Immutable objects, like strings or numbers, do not need to be copied.
  
    @function up.util.copy
    @param {any} object
    @return {any}
    @stable
    */
    function copy(value) {
        if (isObject(value) && value[copy.key]) {
            value = value[copy.key]();
        }
        else if (isList(value)) {
            value = copyArrayLike(value);
        }
        else if (isOptions(value)) {
            value = assign({}, value);
        }
        return value;
    }
    function copyArrayLike(arrayLike) {
        return Array.prototype.slice.call(arrayLike);
    }
    /*-
    This property contains the name of a method that user-defined classes
    may implement to hook into the `up.util.copy()` protocol.
  
    ### Example
  
    We have a user-defined `Account` class that we want to use with `up.util.copy()`:
  
    ```js
    class Account {
      constructor(email) {
        this.email = email
      }
  
      [up.util.copy.key]() {
        return new Account(this.email)
      }
    }
    ```
  
    Note that the protocol method is not actually named `'up.util.copy.key'`.
    Instead it is named after the *value* of the `up.util.copy.key` property.
    To do so, the code sample above is using a
    [computed property name](https://medium.com/front-end-weekly/javascript-object-creation-356e504173a8)
    in square brackets.
  
    We may now use `Account` instances with `up.util.copy()`:
  
    ```
    original = new User('foo@foo.com')
  
    copy = up.util.copy(original)
    console.log(copy.email) // prints 'foo@foo.com'
  
    original.email = 'bar@bar.com' // change the original
    console.log(copy.email) // still prints 'foo@foo.com'
    ```
  
    @property up.util.copy.key
    @param {string} key
    @experimental
    */
    copy.key = 'up.util.copy';
    // Implement up.util.copy protocol for Date
    Date.prototype[copy.key] = function () { return new Date(+this); };
    //  ###**
    //  Returns a deep copy of the given array or object.
    //
    //  @function up.util.deepCopy
    //  @param {Object|Array} object
    //  @return {Object|Array}
    //  @internal
    //  ###
    //  deepCopy = (object) ->
    //    copy(object, true)
    /*-
    Creates a new object by merging together the properties from the given objects.
  
    @function up.util.merge
    @param {Array<Object>} sources...
    @return Object
    @stable
    */
    function merge(...sources) {
        return assign({}, ...sources);
    }
    /*-
    @function up.util.mergeDefined
    @param {Array<Object>} sources...
    @return Object
    @internal
    */
    function mergeDefined(...sources) {
        const result = {};
        for (let source of sources) {
            if (source) {
                for (let key in source) {
                    const value = source[key];
                    if (isDefined(value)) {
                        result[key] = value;
                    }
                }
            }
        }
        return result;
    }
    /*-
    Creates an options hash from the given argument and some defaults.
  
    The semantics of this function are confusing.
    We want to get rid of this in the future.
  
    @function up.util.options
    @param {Object} object
    @param {Object} [defaults]
    @return {Object}
    @internal
    */
    function newOptions(object, defaults) {
        if (defaults) {
            return merge(defaults, object);
        }
        else if (object) {
            return copy(object);
        }
        else {
            return {};
        }
    }
    function parseArgIntoOptions(args, argKey) {
        let options = extractOptions(args);
        if (isDefined(args[0])) {
            options = copy(options);
            options[argKey] = args[0];
        }
        return options;
    }
    /*-
    Passes each element in the given [array-like value](/up.util.isList) to the given function.
    Returns the first element for which the function returns a truthy value.
  
    If no object matches, returns `undefined`.
  
    @function up.util.find
    @param {List<T>} list
    @param {Function(value): boolean} tester
    @return {T|undefined}
    @stable
    */
    function findInList(list, tester) {
        tester = iteratee(tester);
        let match;
        for (let element of list) {
            if (tester(element)) {
                match = element;
                break;
            }
        }
        return match;
    }
    /*-
    Returns whether the given function returns a truthy value
    for any element in the given [array-like value](/up.util.isList).
  
    @function up.util.some
    @param {List} list
    @param {Function(value, index): boolean} tester
      A function that will be called with each element and (optional) iteration index.
  
    @return {boolean}
    @stable
    */
    function some(list, tester) {
        return !!findResult(list, tester);
    }
    /*-
    Consecutively calls the given function which each element
    in the given array. Returns the first truthy return value.
  
    Returned `undefined` iff the function does not return a truthy
    value for any element in the array.
  
    @function up.util.findResult
    @param {Array} array
    @param {Function(element): any} tester
      A function that will be called with each element and (optional) iteration index.
  
    @return {any|undefined}
    @experimental
    */
    function findResult(array, tester) {
        tester = iteratee(tester);
        for (let i = 0; i < array.length; i++) {
            const result = tester(array[i], i);
            if (result) {
                return result;
            }
        }
    }
    /*-
    Returns whether the given function returns a truthy value
    for all elements in the given [array-like value](/up.util.isList).
  
    @function up.util.every
    @param {List} list
    @param {Function(element, index): boolean} tester
      A function that will be called with each element and (optional) iteration index.
  
    @return {boolean}
    @experimental
    */
    function every(list, tester) {
        tester = iteratee(tester);
        let match = true;
        for (let i = 0; i < list.length; i++) {
            if (!tester(list[i], i)) {
                match = false;
                break;
            }
        }
        return match;
    }
    /*-
    Returns all elements from the given array that are
    neither `null` or `undefined`.
  
    @function up.util.compact
    @param {Array<T>} array
    @return {Array<T>}
    @stable
    */
    function compact(array) {
        return filterList(array, isGiven);
    }
    function compactObject(object) {
        return pickBy(object, isGiven);
    }
    /*-
    Returns the given array without duplicates.
  
    @function up.util.uniq
    @param {Array<T>} array
    @return {Array<T>}
    @stable
    */
    function uniq(array) {
        if (array.length < 2) {
            return array;
        }
        return setToArray(arrayToSet(array));
    }
    /*-
    This function is like [`uniq`](/up.util.uniq), accept that
    the given function is invoked for each element to generate the value
    for which uniquness is computed.
  
    @function up.util.uniqBy
    @param {Array} array
    @param {Function(value): any} array
    @return {Array}
    @experimental
    */
    function uniqBy(array, mapper) {
        if (array.length < 2) {
            return array;
        }
        mapper = iteratee(mapper);
        const seenElements = new Set();
        return filterList(array, function (elem, index) {
            const mapped = mapper(elem, index);
            if (seenElements.has(mapped)) {
                return false;
            }
            else {
                seenElements.add(mapped);
                return true;
            }
        });
    }
    /*-
    @function up.util.setToArray
    @internal
    */
    function setToArray(set) {
        const array = [];
        set.forEach(elem => array.push(elem));
        return array;
    }
    /*-
    @function up.util.arrayToSet
    @internal
    */
    function arrayToSet(array) {
        const set = new Set();
        array.forEach(elem => set.add(elem));
        return set;
    }
    /*-
    Returns all elements from the given [array-like value](/up.util.isList) that return
    a truthy value when passed to the given function.
  
    @function up.util.filter
    @param {List} list
    @param {Function(value, index): boolean} tester
    @return {Array}
    @stable
    */
    function filterList(list, tester) {
        tester = iteratee(tester);
        const matches = [];
        each(list, function (element, index) {
            if (tester(element, index)) {
                return matches.push(element);
            }
        });
        return matches;
    }
    /*-
    Returns all elements from the given [array-like value](/up.util.isList) that do not return
    a truthy value when passed to the given function.
  
    @function up.util.reject
    @param {List} list
    @param {Function(element, index): boolean} tester
    @return {Array}
    @stable
    */
    function reject(list, tester) {
        tester = negate(iteratee(tester));
        return filterList(list, tester);
    }
    /*-
    Returns the intersection of the given two arrays.
  
    Implementation is not optimized. Don't use it for large arrays.
  
    @function up.util.intersect
    @internal
    */
    function intersect(array1, array2) {
        return filterList(array1, element => contains(array2, element));
    }
    /*-
    Waits for the given number of milliseconds, the runs the given callback.
  
    Instead of `up.util.timer(0, fn)` you can also use [`up.util.task(fn)`](/up.util.task).
  
    @function up.util.timer
    @param {number} millis
    @param {Function()} callback
    @return {number}
      The ID of the scheduled timeout.
  
      You may pass this ID to `clearTimeout()` to un-schedule the timeout.
    @stable
    */
    function scheduleTimer(millis, callback) {
        return setTimeout(callback, millis);
    }
    /*-
    Pushes the given function to the [JavaScript task queue](https://jakearchibald.com/2015/tasks-microtasks-queues-and-schedules/) (also "macrotask queue").
  
    Equivalent to calling `setTimeout(fn, 0)`.
  
    Also see `up.util.microtask()`.
  
    @function up.util.task
    @param {Function()} block
    @stable
    */
    function queueTask(task) {
        return setTimeout(task);
    }
    /*-
    Pushes the given function to the [JavaScript microtask queue](https://jakearchibald.com/2015/tasks-microtasks-queues-and-schedules/).
  
    @function up.util.microtask
    @param {Function()} task
    @return {Promise}
      A promise that is resolved with the return value of `task`.
  
      If `task` throws an error, the promise is rejected with that error.
    @experimental
    */
    function queueMicrotask(task) {
        return Promise.resolve().then(task);
    }
    function abortableMicrotask(task) {
        let aborted = false;
        queueMicrotask(function () { if (!aborted) {
            return task();
        } });
        return () => aborted = true;
    }
    /*-
    Returns the last element of the given array.
  
    @function up.util.last
    @param {Array<T>} array
    @return {T}
    @stable
    */
    function last(array) {
        return array[array.length - 1];
    }
    /*-
    Returns whether the given value contains another value.
  
    If `value` is a string, this returns whether `subValue` is a sub-string of `value`.
  
    If `value` is an array, this returns whether `subValue` is an element of `value`.
  
    @function up.util.contains
    @param {Array|string} value
    @param {Array|string} subValue
    @stable
    */
    function contains(value, subValue) {
        return value.indexOf(subValue) >= 0;
    }
    /*-
    Returns whether `object`'s entries are a superset
    of `subObject`'s entries.
  
    @function up.util.objectContains
    @param {Object} object
    @param {Object} subObject
    @internal
    */
    function objectContains(object, subObject) {
        const reducedValue = pick(object, Object.keys(subObject));
        return isEqual(subObject, reducedValue);
    }
    /*-
    Returns a copy of the given object that only contains
    the given keys.
  
    @function up.util.pick
    @param {Object} object
    @param {Array} keys
    @return {Object}
    @stable
    */
    function pick(object, keys) {
        const filtered = {};
        for (let key of keys) {
            if (key in object) {
                filtered[key] = object[key];
            }
        }
        return filtered;
    }
    /*-
    Returns a copy of the given object that only contains
    properties that pass the given tester function.
  
    @function up.util.pickBy
    @param {Object} object
    @param {Function(string, string, object): boolean} tester
      A function that will be called with each property.
  
      The arguments are the property value, key and the entire object.
    @return {Object}
    @experimental
    */
    function pickBy(object, tester) {
        tester = iteratee(tester);
        const filtered = {};
        for (let key in object) {
            const value = object[key];
            if (tester(value, key, object)) {
                filtered[key] = object[key];
            }
        }
        return filtered;
    }
    /*-
    Returns a copy of the given object that contains all except
    the given keys.
  
    @function up.util.omit
    @param {Object} object
    @param {Array} keys
    @stable
    */
    function omit(object, keys) {
        return pickBy(object, (_value, key) => !contains(keys, key));
    }
    /*-
    Returns a promise that will never be resolved.
  
    @function up.util.unresolvablePromise
    @internal
    */
    function unresolvablePromise() {
        return new Promise(noop);
    }
    /*-
    Removes the given element from the given array.
  
    This changes the given array.
  
    @function up.util.remove
    @param {Array<T>} array
      The array to change.
    @param {T} element
      The element to remove.
    @return {T|undefined}
      The removed element, or `undefined` if the array didn't contain the element.
    @stable
    */
    function remove(array, element) {
        const index = array.indexOf(element);
        if (index >= 0) {
            array.splice(index, 1);
            return element;
        }
    }
    /*-
    If the given `value` is a function, calls the function with the given `args`.
    Otherwise it just returns `value`.
  
    ### Example
  
    ```js
    up.util.evalOption(5) // => 5
  
    let fn = () => 1 + 2
    up.util.evalOption(fn) // => 3
    ```
  
    @function up.util.evalOption
    @param {any} value
    @param {Array} ...args
    @return {any}
    @experimental
    */
    function evalOption(value, ...args) {
        return isFunction(value) ? value(...args) : value;
    }
    const ESCAPE_HTML_ENTITY_MAP = {
        "&": "&amp;",
        "<": "&lt;",
        ">": "&gt;",
        '"': '&quot;',
        "'": '&#x27;'
    };
    /*-
    Escapes the given string of HTML by replacing control chars with their HTML entities.
  
    @function up.util.escapeHTML
    @param {string} string
      The text that should be escaped.
    @stable
    */
    function escapeHTML(string) {
        return string.replace(/[&<>"']/g, char => ESCAPE_HTML_ENTITY_MAP[char]);
    }
    /*-
    @function up.util.escapeRegExp
    @internal
    */
    function escapeRegExp(string) {
        // From https://github.com/benjamingr/RegExp.escape
        return string.replace(/[\\^$*+?.()|[\]{}]/g, '\\$&');
    }
    /*-
    Deletes the property with the given key from the given object
    and returns its value.
  
    @function up.util.pluckKey
    @param {Object} object
    @param {string} key
    @return {any}
    @experimental
    */
    function pluckKey(object, key) {
        const value = object[key];
        delete object[key];
        return value;
    }
    function renameKey(object, oldKey, newKey) {
        return object[newKey] = pluckKey(object, oldKey);
    }
    function extractLastArg(args, tester) {
        if (tester(last(args))) {
            return args.pop();
        }
    }
    //  extractFirstArg = (args, tester) ->
    //    firstArg = args[0]
    //    if tester(firstArg)
    //      return args.shift()
    function extractCallback(args) {
        return extractLastArg(args, isFunction);
    }
    function extractOptions(args) {
        return extractLastArg(args, isOptions) || {};
    }
    //  partial = (fn, fixedArgs...) ->
    //    return (callArgs...) ->
    //      fn.apply(this, fixedArgs.concat(callArgs))
    //
    //  partialRight = (fn, fixedArgs...) ->
    //    return (callArgs...) ->
    //      fn.apply(this, callArgs.concat(fixedArgs))
    //function throttle(callback, limit) { // From https://jsfiddle.net/jonathansampson/m7G64/
    //  var wait = false                   // Initially, we're not waiting
    //  return function () {               // We return a throttled function
    //    if (!wait) {                     // If we're not waiting
    //      callback.call()                // Execute users function
    //      wait = true                    // Prevent future invocations
    //      setTimeout(function () {       // After a period of time
    //        wait = false                 // And allow future invocations
    //      }, limit)
    //    }
    //  }
    //}
    function identity(arg) {
        return arg;
    }
    //  ###**
    //  ###
    //  parsePath = (input) ->
    //    path = []
    //    pattern = /([^\.\[\]\"\']+)|\[\'([^\']+?)\'\]|\[\"([^\"]+?)\"\]|\[([^\]]+?)\]/g
    //    while match = pattern.exec(input)
    //      path.push(match[1] || match[2] || match[3] || match[4])
    //    path
    //  ###**
    //  Given an async function that will return a promise, returns a proxy function
    //  with an additional `.promise` attribute.
    //
    //  When the proxy is called, the inner function is called.
    //  The proxy's `.promise` attribute is available even before the function is called
    //  and will resolve when the inner function's returned promise resolves.
    //
    //  If the inner function does not return a promise, the proxy's `.promise` attribute
    //  will resolve as soon as the inner function returns.
    //
    //  @function up.util.previewable
    //  @internal
    //  ###
    //  previewable = (fun) ->
    //    deferred = newDeferred()
    //    preview = (args...) ->
    //      funValue = fun(args...)
    //      # If funValue is again a Promise, it will defer resolution of `deferred`
    //      # until `funValue` is resolved.
    //      deferred.resolve(funValue)
    //      funValue
    //    preview.promise = deferred.promise()
    //    preview
    /*-
    @function up.util.sequence
    @param {Array<Function()>} functions
    @return {Function()}
      A function that will call all `functions` if called.
    @internal
    */
    function sequence(functions) {
        // No need for an expensive map() if we're passed a single function.
        if (functions.length === 1) {
            return functions[0];
        }
        return () => map(functions, fn => fn());
    }
    //  ###**
    //  @function up.util.race
    //  @internal
    //  ###
    //  race = (promises...) ->
    //    raceDone = newDeferred()
    //    each promises, (promise) ->
    //      promise.then -> raceDone.resolve()
    //    raceDone.promise()
    //  ###**
    //  Returns `'left'` if the center of the given element is in the left 50% of the screen.
    //  Otherwise returns `'right'`.
    //
    //  @function up.util.horizontalScreenHalf
    //  @internal
    //  ###
    //  horizontalScreenHalf = (element) ->
    //    elementDims = element.getBoundingClientRect()
    //    elementMid = elementDims.left + 0.5 * elementDims.width
    //    screenMid = 0.5 * up.viewport.rootWidth()
    //    if elementMid < screenMid
    //      'left'
    //    else
    //      'right'
    /*-
    Flattens the given `array` a single depth level.
  
    ### Example
  
    ```js
    let nested = [1, [2, 3], [4]]
    up.util.flatten(nested) // => [1, 2, 3, 4]
  
    @function up.util.flatten
    @param {Array} array
      An array which might contain other arrays
    @return {Array}
      The flattened array
    @experimental
    */
    function flatten(array) {
        const flattened = [];
        for (let object of array) {
            if (isList(object)) {
                flattened.push(...object);
            }
            else {
                flattened.push(object);
            }
        }
        return flattened;
    }
    //  flattenObject = (object) ->
    //    result = {}
    //    for key, value of object
    //      result[key] = value
    //    result
    /*-
    Maps each element using a mapping function,
    then [flattens](/up.util.flatten) the result into a new array.
  
    @function up.util.flatMap
    @param {Array} array
    @param {Function(element)} mapping
    @return {Array}
    @experimental
    */
    function flatMap(array, block) {
        return flatten(map(array, block));
    }
    /*-
    Returns whether the given value is truthy.
  
    @function up.util.isTruthy
    @internal
    */
    function isTruthy(object) {
        return !!object;
    }
    /*-
    Sets the given callback as both fulfillment and rejection handler for the given promise.
  
    [Unlike `promise#finally()`](https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Promise/finally#Description), `up.util.always()` may change the settlement value
    of the given promise.
  
    @function up.util.always
    @internal
    */
    function always(promise, callback) {
        return promise.then(callback, callback);
    }
    //  mutedFinally = (promise, callback) ->
    //    # Use finally() instead of always() so we don't accidentally
    //    # register a rejection handler, which would prevent an "Uncaught in Exception" error.
    //    finallyDone = promise.finally(callback)
    //
    //    # Since finally's return value is itself a promise with the same state
    //    # as `promise`, we don't want to see "Uncaught in Exception".
    //    # If we didn't do this, we couldn't mute rejections in `promise`:
    //    #
    //    #     promise = new Promise(...)
    //    #     promise.finally(function() { ... })
    //    #     up.util.muteRejection(promise) // has no effect
    //    muteRejection(finallyDone)
    //
    //    # Return the original promise and *not* finally's return value.
    //    return promise
    /*-
    Registers an empty rejection handler with the given promise.
    This prevents browsers from printing "Uncaught (in promise)" to the error
    console when the promise is rejected.
  
    This is helpful for event handlers where it is clear that no rejection
    handler will be registered:
  
        up.on('submit', 'form[up-target]', (event, $form) => {
          promise = up.submit($form)
          up.util.muteRejection(promise)
        })
  
    Does nothing if passed a missing value.
  
    @function up.util.muteRejection
    @param {Promise|undefined|null} promise
    @return {Promise}
    @internal
    */
    function muteRejection(promise) {
        return promise?.catch(noop);
    }
    /*-
    @function up.util.newDeferred
    @internal
    */
    function newDeferred() {
        let resolveFn;
        let rejectFn;
        const nativePromise = new Promise(function (givenResolve, givenReject) {
            resolveFn = givenResolve;
            rejectFn = givenReject;
        });
        nativePromise.resolve = resolveFn;
        nativePromise.reject = rejectFn;
        nativePromise.promise = () => nativePromise; // just return self
        return nativePromise;
    }
    //  ###**
    //  Calls the given block. If the block throws an exception,
    //  a rejected promise is returned instead.
    //
    //  @function up.util.rejectOnError
    //  @internal
    //  ###
    //  rejectOnError = (block) ->
    //    try
    //      block()
    //    catch error
    //      Promise.reject(error)
    function asyncify(block) {
        // The side effects of this should be sync, otherwise we could
        // just do `Promise.resolve().then(block)`.
        try {
            return Promise.resolve(block());
        }
        catch (error) {
            return Promise.reject(error);
        }
    }
    //  sum = (list, block) ->
    //    block = iteratee(block)
    //    totalValue = 0
    //    for entry in list
    //      entryValue = block(entry)
    //      if isGiven(entryValue) # ignore undefined/null, like SQL would do
    //        totalValue += entryValue
    //    totalValue
    function isBasicObjectProperty(k) {
        return Object.prototype.hasOwnProperty(k); // eslint-disable-line no-prototype-builtins
    }
    /*-
    Returns whether the two arguments are equal by value.
  
    ### Comparison protocol
  
    - By default `up.util.isEqual()` can compare strings, numbers,
      [array-like values](/up.util.isList), plain objects and `Date` objects.
    - To make the copying protocol work with user-defined classes,
      see `up.util.isEqual.key`.
    - Objects without a defined comparison protocol are
      defined by reference (`===`).
  
    @function up.util.isEqual
    @param {any} a
    @param {any} b
    @return {boolean}
      Whether the arguments are equal by value.
    @experimental
    */
    function isEqual(a, b) {
        if (a?.valueOf) {
            a = a.valueOf();
        } // Date, String objects, Number objects
        if (b?.valueOf) {
            b = b.valueOf();
        } // Date, String objects, Number objects
        if (typeof (a) !== typeof (b)) {
            return false;
        }
        else if (isList(a) && isList(b)) {
            return isEqualList(a, b);
        }
        else if (isObject(a) && a[isEqual.key]) {
            return a[isEqual.key](b);
        }
        else if (isOptions(a) && isOptions(b)) {
            const aKeys = Object.keys(a);
            const bKeys = Object.keys(b);
            if (isEqualList(aKeys, bKeys)) {
                return every(aKeys, aKey => isEqual(a[aKey], b[aKey]));
            }
            else {
                return false;
            }
        }
        else {
            return a === b;
        }
    }
    /*-
    This property contains the name of a method that user-defined classes
    may implement to hook into the `up.util.isEqual()` protocol.
  
    ### Example
  
    We have a user-defined `Account` class that we want to use with `up.util.isEqual()`:
  
    ```
    class Account {
      constructor(email) {
        this.email = email
      }
  
      [up.util.isEqual.key](other) {
        return this.email === other.email
      }
    }
    ```
  
    Note that the protocol method is not actually named `'up.util.isEqual.key'`.
    Instead it is named after the *value* of the `up.util.isEqual.key` property.
    To do so, the code sample above is using a
    [computed property name](https://medium.com/front-end-weekly/javascript-object-creation-356e504173a8)
    in square brackets.
  
    We may now use `Account` instances with `up.util.isEqual()`:
  
    ```js
    let one = new User('foo@foo.com')
    let two = new User('foo@foo.com')
    let three = new User('bar@bar.com')
  
    up.util.isEqual(one, two)   // returns true
    up.util.isEqual(one, three) // returns false
    ```
  
    @property up.util.isEqual.key
    @param {string} key
    @experimental
    */
    isEqual.key = 'up.util.isEqual';
    function isEqualList(a, b) {
        return (a.length === b.length) && every(a, (elem, index) => isEqual(elem, b[index]));
    }
    function splitValues(value, separator = ' ') {
        if (isString(value)) {
            value = value.split(separator);
            value = map(value, v => v.trim());
            value = filterList(value, isPresent);
            return value;
        }
        else {
            return wrapList(value);
        }
    }
    function endsWith(string, search) {
        return string.substring(string.length - search.length) === search;
    }
    function simpleEase(x) {
        // easing: http://fooplot.com/?lang=de#W3sidHlwZSI6MCwiZXEiOiJ4PDAuNT8yKngqeDp4Kig0LXgqMiktMSIsImNvbG9yIjoiIzEzRjIxNyJ9LHsidHlwZSI6MCwiZXEiOiJzaW4oKHheMC43LTAuNSkqcGkpKjAuNSswLjUiLCJjb2xvciI6IiMxQTUyRUQifSx7InR5cGUiOjEwMDAsIndpbmRvdyI6WyItMS40NyIsIjEuNzgiLCItMC41NSIsIjEuNDUiXX1d
        // easing nice: sin((x^0.7-0.5)*pi)*0.5+0.5
        // easing performant: x < 0.5 ? 2*x*x : x*(4 - x*2)-1
        // https://jsperf.com/easings/1
        // Math.sin((Math.pow(x, 0.7) - 0.5) * Math.PI) * 0.5 + 0.5
        return x < 0.5 ? 2 * x * x : (x * (4 - (x * 2))) - 1;
    }
    function wrapValue(constructor, ...args) {
        return (args[0] instanceof constructor) ? args[0] : new constructor(...args);
    }
    //  wrapArray = (objOrArray) ->
    //    if isUndefined(objOrArray)
    //      []
    //    else if isArray(objOrArray)
    //      objOrArray
    //    else
    //      [objOrArray]
    let nextUid = 0;
    function uid() {
        return nextUid++;
    }
    /*-
    Returns a copy of the given list, in reversed order.
  
    @function up.util.reverse
    @param {List<T>} list
    @return {Array<T>}
    @internal
    */
    function reverse(list) {
        return copy(list).reverse();
    }
    //  ###**
    //  Returns a copy of the given `object` with the given `prefix` removed
    //  from its camel-cased keys.
    //
    //  @function up.util.unprefixKeys
    //  @param {Object} object
    //  @param {string} prefix
    //  @return {Object}
    //  @internal
    //  ###
    //  unprefixKeys = (object, prefix) ->
    //    unprefixed = {}
    //    prefixLength = prefix.length
    //    for key, value of object
    //      if key.indexOf(prefix) == 0
    //        key = unprefixCamelCase(key, prefixLength)
    //      unprefixed[key] = value
    //    unprefixed
    //  replaceValue = (value, matchValue, replaceValueFn) ->
    //    if value == matchValue
    //      return replaceValueFn()
    //    else
    //      return value
    function renameKeys(object, renameKeyFn) {
        const renamed = {};
        for (let key in object) {
            renamed[renameKeyFn(key)] = object[key];
        }
        return renamed;
    }
    function camelToKebabCase(str) {
        return str.replace(/[A-Z]/g, char => '-' + char.toLowerCase());
    }
    function prefixCamelCase(str, prefix) {
        return prefix + upperCaseFirst(str);
    }
    function unprefixCamelCase(str, prefix) {
        const pattern = new RegExp('^' + prefix + '(.+)$');
        let match = str.match(pattern);
        if (match) {
            return lowerCaseFirst(match[1]);
        }
    }
    function lowerCaseFirst(str) {
        return str[0].toLowerCase() + str.slice(1);
    }
    function upperCaseFirst(str) {
        return str[0].toUpperCase() + str.slice(1);
    }
    function defineGetter(object, prop, get) {
        Object.defineProperty(object, prop, { get });
    }
    function defineDelegates(object, props, targetProvider) {
        wrapList(props).forEach(function (prop) {
            Object.defineProperty(object, prop, {
                get() {
                    const target = targetProvider.call(this);
                    let value = target[prop];
                    if (isFunction(value)) {
                        value = value.bind(target);
                    }
                    return value;
                },
                set(newValue) {
                    const target = targetProvider.call(this);
                    target[prop] = newValue;
                }
            });
        });
    }
    function stringifyArg(arg) {
        let string;
        const maxLength = 200;
        let closer = '';
        if (isString(arg)) {
            string = arg.replace(/[\n\r\t ]+/g, ' ');
            string = string.replace(/^[\n\r\t ]+/, '');
            string = string.replace(/[\n\r\t ]$/, '');
            // string = "\"#{string}\""
            // closer = '"'
        }
        else if (isUndefined(arg)) {
            // JSON.stringify(undefined) is actually undefined
            string = 'undefined';
        }
        else if (isNumber(arg) || isFunction(arg)) {
            string = arg.toString();
        }
        else if (isArray(arg)) {
            string = `[${map(arg, stringifyArg).join(', ')}]`;
            closer = ']';
        }
        else if (isJQuery(arg)) {
            string = `$(${map(arg, stringifyArg).join(', ')})`;
            closer = ')';
        }
        else if (isElement(arg)) {
            string = `<${arg.tagName.toLowerCase()}`;
            for (let attr of ['id', 'name', 'class']) {
                let value = arg.getAttribute(attr);
                if (value) {
                    string += ` ${attr}="${value}"`;
                }
            }
            string += ">";
            closer = '>';
        }
        else if (isRegExp(arg)) {
            string = arg.toString();
        }
        else { // object, array
            try {
                string = JSON.stringify(arg);
            }
            catch (error) {
                if (error.name === 'TypeError') {
                    string = '(circular structure)';
                }
                else {
                    throw error;
                }
            }
        }
        if (string.length > maxLength) {
            string = `${string.substr(0, maxLength)} …`;
            string += closer;
        }
        return string;
    }
    const SPRINTF_PLACEHOLDERS = /%[oOdisf]/g;
    function secondsSinceEpoch() {
        return Math.floor(Date.now() * 0.001);
    }
    /*-
    See https://developer.mozilla.org/en-US/docs/Web/API/Console#Using_string_substitutions
  
    @function up.util.sprintf
    @internal
    */
    function sprintf(message, ...args) {
        return sprintfWithFormattedArgs(identity, message, ...args);
    }
    /*-
    @function up.util.sprintfWithFormattedArgs
    @internal
    */
    function sprintfWithFormattedArgs(formatter, message, ...args) {
        if (!message) {
            return '';
        }
        let i = 0;
        return message.replace(SPRINTF_PLACEHOLDERS, function () {
            let arg = args[i];
            arg = formatter(stringifyArg(arg));
            i += 1;
            return arg;
        });
    }
    // Remove with IE11.
    // When removed we can also remove muteRejection(), as this is the only caller.
    function allSettled(promises) {
        return Promise.all(map(promises, muteRejection));
    }
    function negate(fn) {
        return function (...args) {
            return !fn(...args);
        };
    }
    return {
        parseURL,
        normalizeURL,
        matchURLs,
        normalizeMethod,
        methodAllowsPayload,
        assign,
        assignPolyfill,
        copy,
        copyArrayLike,
        merge,
        mergeDefined,
        options: newOptions,
        parseArgIntoOptions,
        each,
        eachIterator,
        map,
        flatMap,
        mapObject,
        findResult,
        some,
        every,
        find: findInList,
        filter: filterList,
        reject,
        intersect,
        compact,
        compactObject,
        uniq,
        uniqBy,
        last,
        isNull,
        isDefined,
        isUndefined,
        isGiven,
        isMissing,
        isPresent,
        isBlank,
        presence,
        isObject,
        isFunction,
        isString,
        isBoolean,
        isNumber,
        isElement,
        isJQuery,
        isElementish,
        isPromise,
        isOptions,
        isArray,
        isFormData,
        isNodeList,
        isArguments,
        isList,
        isRegExp,
        timer: scheduleTimer,
        contains,
        objectContains,
        toArray,
        pick,
        pickBy,
        omit,
        unresolvablePromise,
        remove,
        memoize,
        pluckKey,
        renameKey,
        extractOptions,
        extractCallback,
        noop,
        asyncNoop,
        identity,
        escapeHTML,
        escapeRegExp,
        sequence,
        evalOption,
        flatten,
        isTruthy,
        newDeferred,
        always,
        muteRejection,
        asyncify,
        isBasicObjectProperty,
        isCrossOrigin,
        task: queueTask,
        microtask: queueMicrotask,
        abortableMicrotask,
        isEqual,
        splitValues,
        endsWith,
        wrapList,
        wrapValue,
        simpleEase,
        values: objectValues,
        arrayToSet,
        setToArray,
        uid,
        upperCaseFirst,
        lowerCaseFirst,
        getter: defineGetter,
        delegate: defineDelegates,
        reverse,
        prefixCamelCase,
        unprefixCamelCase,
        camelToKebabCase,
        nullToUndefined,
        sprintf,
        sprintfWithFormattedArgs,
        renameKeys,
        timestamp: secondsSinceEpoch,
        allSettled,
        negate,
    };
})();


/***/ }),
/* 4 */
/***/ (() => {

up.error = (function () {
    const u = up.util;
    function build(message, props = {}) {
        if (u.isArray(message)) {
            message = u.sprintf(...message);
        }
        const error = new Error(message);
        u.assign(error, props);
        return error;
    }
    // Custom error classes is hard when we transpile to ES5.
    // Hence we create a class-like construct.
    // See https://webcodr.io/2018/04/why-custom-errors-in-javascript-with-babel-are-broken/
    function errorInterface(name, init = build) {
        const fn = function (...args) {
            const error = init(...args);
            error.name = name;
            return error;
        };
        fn.is = error => error.name === name;
        fn.async = (...args) => Promise.reject(fn(...args));
        return fn;
    }
    const failed = errorInterface('up.Failed');
    // Emulate the exception that aborted fetch() would throw
    const aborted = errorInterface('AbortError', (message) => {
        return build(message || 'Aborted');
    });
    const notImplemented = errorInterface('up.NotImplemented');
    const notApplicable = errorInterface('up.NotApplicable', (change, reason) => {
        return build(`Cannot apply change: ${change} (${reason})`);
    });
    const invalidSelector = errorInterface('up.InvalidSelector', (selector) => {
        return build(`Cannot parse selector: ${selector}`);
    });
    function emitGlobal(error) {
        // Emit an ErrorEvent on window.onerror for exception tracking tools
        const { message } = error;
        up.emit(window, 'error', { message, error, log: false });
    }
    /*-
    Throws a [JavaScript error](https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Error)
    with the given message.
  
    The message may contain [substitution marks](https://developer.mozilla.org/en-US/docs/Web/API/console#Using_string_substitutions).
  
    ### Examples
  
        up.fail('Division by zero')
        up.fail('Unexpected result %o', result)
  
    @function up.fail
    @param {string} message
      A message with details about the error.
  
      The message can contain [substitution marks](https://developer.mozilla.org/en-US/docs/Web/API/console#Using_string_substitutions)
      like `%s` or `%o`.
    @param {Array<string>} vars...
      A list of variables to replace any substitution marks in the error message.
    @internal
    */
    function fail(...args) {
        throw up.error.failed(args);
    }
    return {
        fail,
        failed,
        aborted,
        invalidSelector,
        notApplicable,
        notImplemented,
        emitGlobal
    };
})();
up.fail = up.error.fail;


/***/ }),
/* 5 */
/***/ (() => {

// This object will gain properties when users load the optional unpoly-migrate.js
up.migrate = { config: {} };


/***/ }),
/* 6 */
/***/ (() => {

/*-
Browser interface
=================

We tunnel some browser APIs through this module for easier mocking in tests.

@module up.browser
*/
up.browser = (function () {
    const u = up.util;
    /*-
    Submits the given form with a full page load.
    
    For mocking in specs.
  
    @function up.browser.submitForm
    @internal
    */
    function submitForm(form) {
        form.submit();
    }
    function isIE11() {
        return 'ActiveXObject' in window; // this is undefined, but the key is set
    }
    function isEdge18() {
        // Edge 18: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/70.0.3538.102 Safari/537.36 Edge/18.19582
        // Edge 92: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/92.0.4515.159 Safari/537.36 Edg/92.0.902.78
        return u.contains(navigator.userAgent, ' Edge/');
    }
    /*-
    Returns whether this browser supports manipulation of the current URL
    via [`history.pushState`](https://developer.mozilla.org/en-US/docs/Web/API/History/pushState).
  
    When `pushState`  (e.g. through [`up.follow()`](/up.follow)), it will gracefully
    fall back to a full page load.
  
    Note that Unpoly will not use `pushState` if the initial page was loaded with
    a request method other than GET.
  
    @function up.browser.canPushState
    @return {boolean}
    @internal
    */
    function canPushState() {
        // We cannot use pushState if the initial request method is a POST for two reasons:
        //
        // 1. Unpoly replaces the initial state so it can handle the pop event when the
        //    user goes back to the initial URL later. If the initial request was a POST,
        //    Unpoly will wrongly assumed that it can restore the state by reloading with GET.
        //
        // 2. Some browsers have a bug where the initial request method is used for all
        //    subsequently pushed states. That means if the user reloads the page on a later
        //    GET state, the browser will wrongly attempt a POST request.
        //    This issue affects Safari 9 and 10 (last tested in 2017-08).
        //    Modern Firefoxes, Chromes and IE10+ don't have this behavior.
        //
        // The way that we work around this is that we don't support pushState if the
        // initial request method was anything other than GET (but allow the rest of the
        // Unpoly framework to work). This way Unpoly will fall back to full page loads until
        // the framework was booted from a GET request.
        return history.pushState && up.protocol.initialRequestMethod() === 'GET';
    }
    /*-
    Returns whether this browser supports promises.
  
    @function up.browser.canPromise
    @return {boolean}
    @internal
    */
    function canPromise() {
        return !!window.Promise;
    }
    const canFormatLog = u.negate(isIE11);
    const canPassiveEventListener = u.negate(isIE11);
    // Don't memoize so a build may publish window.jQuery after Unpoly was loaded
    function canJQuery() {
        return !!window.jQuery;
    }
    const canEval = u.memoize(function () {
        try {
            // Don't use eval() which would prevent minifiers from compressing local variables.
            return new Function('return true')();
        }
        catch {
            // With a strict CSP this will be an error like:
            // Uncaught EvalError: call to Function() blocked by CSP
            return false;
        }
    });
    // IE11: Use the browser.cookies API instead.
    function popCookie(name) {
        let value = document.cookie.match(new RegExp(name + "=(\\w+)"))?.[1];
        if (value) {
            document.cookie = name + '=;Max-Age=0;Path=/';
            return value;
        }
    }
    const getJQuery = function () {
        if (!canJQuery()) {
            up.fail('jQuery must be published as window.jQuery');
        }
        return jQuery;
    };
    /*-
    @return {boolean}
    @function up.browser.ensureConfirmed
    @param {string} options.confirm
    @param {boolean} options.preload
    @internal
    */
    function assertConfirmed(options) {
        const confirmed = !options.confirm || window.confirm(options.confirm);
        if (!confirmed) {
            throw up.error.aborted('User canceled action');
        }
        return true;
    }
    return {
        submitForm,
        canPushState,
        canFormatLog,
        canPassiveEventListener,
        canJQuery,
        canPromise,
        canEval,
        assertConfirmed,
        popCookie,
        get jQuery() { return getJQuery(); },
        isIE11,
        isEdge18,
    };
})();


/***/ }),
/* 7 */
/***/ ((__unused_webpack_module, __unused_webpack_exports, __webpack_require__) => {

__webpack_require__(8);
/*-
DOM helpers
===========

The `up.element` module offers functions for DOM manipulation and traversal.

It complements [native `Element` methods](https://www.w3schools.com/jsref/dom_obj_all.asp) and works across all [supported browsers](/up.browser).

@module up.element
*/
up.element = (function () {
    const u = up.util;
    const MATCH_FN_NAME = up.browser.isIE11() ? 'msMatchesSelector' : 'matches';
    /*-
    Returns the first descendant element matching the given selector.
  
    @function first
    @param {Element} [parent=document]
      The parent element whose descendants to search.
  
      If omitted, all elements in the `document` will be searched.
    @param {string} selector
      The CSS selector to match.
    @return {Element|undefined|null}
      The first element matching the selector.
  
      Returns `null` or `undefined` if no element macthes.
    @internal
    */
    function first(...args) {
        const selector = args.pop();
        const root = args[0] || document;
        return root.querySelector(selector);
    }
    /*-
    Returns all descendant elements matching the given selector.
  
    @function up.element.all
    @param {Element} [parent=document]
      The parent element whose descendants to search.
  
      If omitted, all elements in the `document` will be searched.
    @param {string} selector
      The CSS selector to match.
    @return {NodeList<Element>|Array<Element>}
      A list of all elements matching the selector.
  
      Returns an empty list if there are no matches.
    @stable
    */
    function all(...args) {
        const selector = args.pop();
        const root = args[0] || document;
        return root.querySelectorAll(selector);
    }
    /*-
    Returns a list of the given parent's descendants matching the given selector.
    The list will also include the parent element if it matches the selector itself.
  
    @function up.element.subtree
    @param {Element} parent
      The parent element for the search.
    @param {string} selector
      The CSS selector to match.
    @return {NodeList<Element>|Array<Element>}
      A list of all matching elements.
    @stable
    */
    function subtree(root, selector) {
        const results = [];
        if (matches(root, selector)) {
            results.push(root);
        }
        results.push(...all(root, selector));
        return results;
    }
    /*-
    Returns whether the given element is either the given root element
    or its descendants.
  
    @function isInSubtree
    @internal
    */
    function isInSubtree(root, selectorOrElement) {
        const element = getOne(selectorOrElement);
        return root.contains(element);
    }
    /*-
    Returns the first element that matches the selector by testing the element itself
    and traversing up through its ancestors in the DOM tree.
  
    @function up.element.closest
    @param {Element} element
      The element on which to start the search.
    @param {string} selector
      The CSS selector to match.
    @return {Element|null|undefined} element
      The matching element.
  
      Returns `null` or `undefined` if no element matches.
    @stable
    */
    function closest(element, selector) {
        if (element.closest) {
            return element.closest(selector);
            // If the browser doesn't support Element#closest, we mimic the behavior.
        }
        else if (matches(element, selector)) {
            return element;
        }
        else {
            return ancestor(element, selector);
        }
    }
    /*-
    Returns whether the given element matches the given CSS selector.
  
    To match against a non-standard selector like `:main`,
    use `up.fragment.matches()` instead.
  
    @function up.element.matches
    @param {Element} element
      The element to check.
    @param {string} selector
      The CSS selector to match.
    @return {boolean}
      Whether `element` matches `selector`.
    @stable
    */
    function matches(element, selector) {
        return element[MATCH_FN_NAME]?.(selector);
    }
    /*-
    @function up.element.ancestor
    @internal
    */
    function ancestor(element, selector) {
        let parentElement = element.parentElement;
        if (parentElement) {
            if (matches(parentElement, selector)) {
                return parentElement;
            }
            else {
                return ancestor(parentElement, selector);
            }
        }
    }
    function around(element, selector) {
        return getList(closest(element, selector), subtree(element, selector));
    }
    /*-
    Returns the native [Element](https://developer.mozilla.org/en-US/docs/Web/API/Element) for the given value.
  
    ### Casting rules
  
    - If given an element, returns that element.
    - If given a CSS selector string, returns the first element matching that selector.
    - If given a jQuery collection , returns the first element in the collection.
      Throws an error if the collection contains more than one element.
    - If given any other argument (`undefined`, `null`, `document`, `window`…), returns the argument unchanged.
  
    @function up.element.get
    @param {Element} [parent=document]
      The parent element whose descendants to search if `value` is a CSS selector string.
  
      If omitted, all elements in the `document` will be searched.
    @param {Element|jQuery|string} value
      The value to look up.
    @return {Element}
      The obtained `Element`.
    @stable
    */
    function getOne(...args) {
        const value = args.pop();
        if (u.isElement(value)) { // Return an element before we run any other expensive checks
            return value;
        }
        else if (u.isString(value)) {
            return first(...args, value);
        }
        else if (u.isList(value)) {
            if (value.length > 1) {
                up.fail('up.element.get(): Cannot cast multiple elements (%o) to a single element', value);
            }
            return value[0];
        }
        else {
            // undefined, null, Window, Document, DocumentFragment, ...
            return value;
        }
    }
    /*-
    Composes a list of elements from the given arguments.
  
    ### Casting rules
  
    - If given a string, returns the all elements matching that string.
    - If given any other argument, returns the argument [wrapped as a list](/up.util.wrapList).
  
    ### Example
  
    ```javascript
    $jquery = $('.jquery')                          // returns jQuery (2) [div.jquery, div.jquery]
    nodeList = document.querySelectorAll('.node')   // returns NodeList (2) [div.node, div.node]
    element = document.querySelector('.element')    // returns Element div.element
    selector = '.selector'                          // returns String '.selector'
  
    elements = up.element.list($jquery, nodeList, undefined, element, selector)
    // returns [div.jquery, div.jquery, div.node, div.node, div.element, div.selector]
    ```
  
    @function up.element.list
    @param {Array<jQuery|Element|Array<Element>|String|undefined|null>} ...args
    @return {Array<Element>}
    @internal
    */
    function getList(...args) {
        return u.flatMap(args, valueToList);
    }
    function valueToList(value) {
        if (u.isString(value)) {
            return all(value);
        }
        else {
            return u.wrapList(value);
        }
    }
    //  assertIsElement = (element) ->
    //    unless u.isElement(element)
    //      up.fail('Not an element: %o', element)
    /*-
    Removes the given element from the DOM tree.
  
    If you don't need IE11 support you may also use the built-in
    [`Element#remove()`](https://developer.mozilla.org/en-US/docs/Web/API/ChildNode/remove) to the same effect.
  
    @function up.element.remove
    @param {Element} element
      The element to remove.
    @stable
    */
    function remove(element) {
        // IE does not support Element#remove()
        let parent = element.parentNode;
        if (parent) {
            parent.removeChild(element);
        }
    }
    /*-
    Hides the given element.
  
    Also see `up.element.show()` and `up.element.toggle()`.
  
    ### Implementation
  
    The element is hidden by setting an `[hidden]` attribute.
    This effectively gives the element a `display: none` rule.
  
    To customize the CSS rule for hiding, see `[hidden]`.
  
    @function up.element.hide
    @param {Element} element
    @stable
    */
    function hide(element) {
        // Set an attribute that the user can style with custom "hidden" styles.
        // E.g. certain JavaScript components cannot initialize properly within a
        // { display: none }, as such an element has no width or height.
        element.setAttribute('hidden', '');
    }
    /*-
    Elements with this attribute are hidden from the page.
  
    While `[hidden]` is a [standard HTML attribute](https://developer.mozilla.org/en-US/docs/Web/HTML/Global_attributes/hidden)
    its default implementation is [not very useful](https://meowni.ca/hidden.is.a.lie.html).
    In particular it cannot hide elements with any `display` rule.
    Unpoly improves the default CSS styles of `[hidden]` so it can hide arbitrary elements.
  
    ## Customizing the CSS
  
    Unpoly's default styles for `[hidden]` look like this:
  
    ```css
    [hidden][hidden] {
      display: none !important;
    }
    ```
  
    You can override the CSS to hide an element in a different way, e.g. by giving it a zero height:
  
    ```css
    .my-element[hidden] {
      display: block !important;
      height: 0 !important;
    }
    ```
  
    Note that any overriding selector must have a [specificity of `(0, 2, 0)`](https://polypane.app/css-specificity-calculator/#selector=.element%5Bhidden%5D).
    Also all rules should be defined with [`!important`](https://www.w3schools.com/css/css_important.asp) to override other
    styles defined on that element.
  
    @selector [hidden]
    @experimental
    */
    /*-
    Shows the given element.
  
    Also see `up.element.hide()` and `up.element.toggle()`.
  
    ### Limitations
  
    The element is shown by removing the `[hidden]` attribute set by `up.element.hide()`.
    In case the element is hidden by an inline style (`[style="display: none"]`),
    that inline style is also removed.
  
    You may have CSS rules causing the element to remain hidden after calling `up.element.show(element)`.
    Unpoly will *not* handle such cases in order to keep this function performant. As a workaround, you may
    manually set `element.style.display = 'block'`.
  
    @function up.element.show
    @param {Element} element
    @stable
    */
    function show(element) {
        // Remove the attribute set by `up.element.hide()`.
        element.removeAttribute('hidden');
        // In case the element was manually hidden through an inline style
        // of `display: none`, we also remove that.
        if (element.style.display === 'none') {
            element.style.display = '';
        }
    }
    /*-
    Changes whether the given element is [shown](/up.element.show) or [hidden](/up.element.hide).
  
    @function up.element.toggle
    @param {Element} element
    @param {boolean} [newVisible]
      Pass `true` to show the element or `false` to hide it.
  
      If omitted, the element will be hidden if shown and shown if hidden.
    @stable
    */
    function toggle(element, newVisible) {
        if (newVisible == null) {
            newVisible = !isVisible(element);
        }
        (newVisible ? show : hide)(element);
    }
    /*-
    Adds or removes the given class from the given element.
  
    If you don't need IE11 support you may also use the built-in
    [`Element#classList.toggle(className)`](https://developer.mozilla.org/en-US/docs/Web/API/Element/classList) to the same effect.
  
    @function up.element.toggleClass
    @param {Element} element
      The element for which to add or remove the class.
    @param {string} className
      The class which should be added or removed.
    @param {Boolean} [newPresent]
      Pass `true` to add the class to the element or `false` to remove it.
  
      If omitted, the class will be added if missing and removed if present.
    @stable
    */
    function toggleClass(element, klass, newPresent) {
        const list = element.classList;
        if (newPresent == null) {
            newPresent = !list.contains(klass);
        }
        if (newPresent) {
            return list.add(klass);
        }
        else {
            return list.remove(klass);
        }
    }
    function toggleAttr(element, attr, value, newPresent) {
        if (newPresent == null) {
            newPresent = !element.hasAttribute(attr);
        }
        if (newPresent) {
            return element.setAttribute(attr, value);
        }
        else {
            return element.removeAttribute(attr);
        }
    }
    /*-
    Sets all key/values from the given object as attributes on the given element.
  
    ### Example
  
        up.element.setAttrs(element, { title: 'Tooltip', tabindex: 1 })
  
    @function up.element.setAttrs
    @param {Element} element
      The element on which to set attributes.
    @param {Object} attributes
      An object of attributes to set.
    @stable
    */
    function setAttrs(element, attrs) {
        for (let key in attrs) {
            const value = attrs[key];
            if (u.isGiven(value)) {
                element.setAttribute(key, value);
            }
            else {
                element.removeAttribute(key);
            }
        }
    }
    function setTemporaryAttrs(element, attrs) {
        const oldAttrs = {};
        for (let key of Object.keys(attrs)) {
            oldAttrs[key] = element.getAttribute(key);
        }
        setAttrs(element, attrs);
        return () => setAttrs(element, oldAttrs);
    }
    /*-
    @function up.element.metaContent
    @internal
    */
    function metaContent(name) {
        const selector = "meta" + attributeSelector('name', name);
        return first(selector)?.getAttribute('content');
    }
    /*-
    @function up.element.insertBefore
    @internal
    */
    function insertBefore(existingElement, newElement) {
        existingElement.insertAdjacentElement('beforebegin', newElement);
    }
    //  insertAfter = (existingElement, newElement) ->
    //    existingElement.insertAdjacentElement('afterend', newElement)
    /*-
    Replaces the given old element with the given new element.
  
    The old element will be removed from the DOM tree.
  
    If you don't need IE11 support you may also use the built-in
    [`Element#replaceWith()`](https://developer.mozilla.org/en-US/docs/Web/API/ChildNode/replaceWith) to the same effect.
  
    @function up.element.replace
    @param {Element} oldElement
    @param {Element} newElement
    @stable
    */
    function replace(oldElement, newElement) {
        oldElement.parentElement.replaceChild(newElement, oldElement);
    }
    /*-
    Creates an element matching the given CSS selector.
  
    The created element will not yet be attached to the DOM tree.
    Attach it with [`Element#appendChild()`](https://developer.mozilla.org/en-US/docs/Web/API/Node/appendChild)
    or use `up.element.affix()` to create an attached element.
  
    Use `up.hello()` to activate JavaScript behavior within the created element.
  
    ### Examples
  
    To create an element with a given tag name:
  
        element = up.element.createFromSelector('span')
        // element is <span></span>
  
    To create an element with a given class:
  
        element = up.element.createFromSelector('.klass')
        // element is <div class="klass"></div>
  
    To create an element with a given ID:
  
        element = up.element.createFromSelector('#foo')
        // element is <div id="foo"></div>
  
    To create an element with a given boolean attribute:
  
        element = up.element.createFromSelector('[attr]')
        // element is <div attr></div>
  
    To create an element with a given attribute value:
  
        element = up.element.createFromSelector('[attr="value"]')
        // element is <div attr="value"></div>
  
    You may also pass an object of attribute names/values as a second argument:
  
        element = up.element.createFromSelector('div', { attr: 'value' })
        // element is <div attr="value"></div>
  
    You may set the element's inner text by passing a `{ text }` option (HTML control characters will
    be escaped):
  
        element = up.element.createFromSelector('div', { text: 'inner text' })
        // element is <div>inner text</div>
  
    You may set the element's inner HTML by passing a `{ content }` option:
  
        element = up.element.createFromSelector('div', { content: '<span>inner text</span>' })
        // element is <div>inner text</div>
  
    You may set inline styles by passing an object of CSS properties as a second argument:
  
        element = up.element.createFromSelector('div', { style: { color: 'red' }})
        // element is <div style="color: red"></div>
  
    @function up.element.createFromSelector
    @param {string} selector
      The CSS selector from which to create an element.
    @param {Object} [attrs]
      An object of attributes to set on the created element.
    @param {Object} [attrs.text]
      The [text content](https://developer.mozilla.org/en-US/docs/Web/API/Node/textContent) of the created element.
    @param {Object} [attrs.content]
      The [inner HTML](https://developer.mozilla.org/en-US/docs/Web/API/Element/innerHTML) of the created element.
    @param {Object|string} [attrs.style]
      An object of CSS properties that will be set as the inline style
      of the created element. The given object may use kebab-case or camelCase keys.
  
      You may also pass a string with semicolon-separated styles.
    @return {Element}
      The created element.
    @stable
    */
    function createFromSelector(selector, attrs) {
        // Extract attribute values before we do anything else.
        // Attribute values might contain spaces, and then we would incorrectly
        // split depths at that space.
        const attrValues = [];
        const selectorWithoutAttrValues = selector.replace(/\[([\w-]+)(?:[~|^$*]?=(["'])?([^\2\]]*?)\2)?\]/g, function (_match, attrName, _quote, attrValue) {
            attrValues.push(attrValue || '');
            return `[${attrName}]`;
        });
        const depths = selectorWithoutAttrValues.split(/[ >]+/);
        let rootElement;
        let depthElement;
        let previousElement;
        for (let depthSelector of depths) {
            let tagName;
            depthSelector = depthSelector.replace(/^[\w-]+/, function (match) {
                tagName = match;
                return '';
            });
            depthElement = document.createElement(tagName || 'div');
            if (!rootElement) {
                rootElement = depthElement;
            }
            depthSelector = depthSelector.replace(/#([\w-]+)/, function (_match, id) {
                depthElement.id = id;
                return '';
            });
            depthSelector = depthSelector.replace(/\.([\w-]+)/g, function (_match, className) {
                depthElement.classList.add(className);
                return '';
            });
            // If we have stripped out attrValues at the beginning of the function,
            // they have been replaced with the attribute name only (as "[name]").
            if (attrValues.length) {
                depthSelector = depthSelector.replace(/\[([\w-]+)\]/g, function (_match, attrName) {
                    depthElement.setAttribute(attrName, attrValues.shift());
                    return '';
                });
            }
            if (depthSelector !== '') {
                throw up.error.invalidSelector(selector);
            }
            previousElement?.appendChild(depthElement);
            previousElement = depthElement;
        }
        if (attrs) {
            let value;
            if (value = u.pluckKey(attrs, 'class')) {
                for (let klass of u.wrapList(value)) {
                    rootElement.classList.add(klass);
                }
            }
            if (value = u.pluckKey(attrs, 'style')) {
                setInlineStyle(rootElement, value);
            }
            if (value = u.pluckKey(attrs, 'text')) {
                // Use .textContent instead of .innerText, since .textContent preserves line breaks.
                rootElement.textContent = value;
            }
            if (value = u.pluckKey(attrs, 'content')) {
                rootElement.innerHTML = value;
            }
            setAttrs(rootElement, attrs);
        }
        return rootElement;
    }
    /*-
    Creates an element matching the given CSS selector and attaches it to the given parent element.
  
    To create a detached element from a selector, see `up.element.createFromSelector()`.
  
    Use `up.hello()` to activate JavaScript behavior within the created element.
  
    ### Example
  
    ```js
    element = up.element.affix(document.body, '.klass')
    element.parentElement // returns document.body
    element.className // returns 'klass'
    ```
  
    @function up.element.affix
    @param {Element} parent
      The parent to which to attach the created element.
    @param {string} [position='beforeend']
      The position of the new element in relation to `parent`.
      Can be one of the following values:
  
      - `'beforebegin'`: Before `parent`, as a new sibling.
      - `'afterbegin'`: Just inside `parent`, before its first child.
      - `'beforeend'`: Just inside `parent`, after its last child.
      - `'afterend'`: After `parent`, as a new sibling.
    @param {string} selector
      The CSS selector from which to create an element.
    @param {Object} attrs
      An object of attributes to set on the created element.
    @param {Object} attrs.text
      The [text content](https://developer.mozilla.org/en-US/docs/Web/API/Node/textContent) of the created element.
    @param {Object|string} attrs.style
      An object of CSS properties that will be set as the inline style
      of the created element.
  
      The given object may use kebab-case or camelCase keys.
    @return {Element}
      The created element.
    @stable
    */
    function affix(parent, ...args) {
        let position, selector;
        const attributes = u.extractOptions(args);
        if (args.length === 2) {
            [position, selector] = args;
        }
        else {
            position = 'beforeend';
            selector = args[0];
        }
        const element = createFromSelector(selector, attributes);
        // https://developer.mozilla.org/en-US/docs/Web/API/Element/insertAdjacentElement
        parent.insertAdjacentElement(position, element);
        return element;
    }
    /*-
    Returns a CSS selector that matches the given element as good as possible.
  
    Alias for `up.fragment.toTarget()`.
  
    @function up.element.toSelector
    @param {string|Element|jQuery}
      The element for which to create a selector.
    @stable
    */
    function toSelector(...args) {
        return up.fragment.toTarget(...args);
    }
    const SINGLETON_TAG_NAMES = ['HTML', 'BODY', 'HEAD', 'TITLE'];
    const SINGLETON_PATTERN = new RegExp('\\b(' + SINGLETON_TAG_NAMES.join('|') + ')\\b', 'i');
    /*-
    @function up.element.isSingleton
    @internal
    */
    const isSingleton = up.mockable(element => matches(element, SINGLETON_TAG_NAMES.join(',')));
    function isSingletonSelector(selector) {
        return SINGLETON_PATTERN.test(selector);
    }
    function elementTagName(element) {
        return element.tagName.toLowerCase();
    }
    /*-
    @function up.element.attributeSelector
    @internal
    */
    function attributeSelector(attribute, value) {
        value = value.replace(/"/g, '\\"');
        return `[${attribute}="${value}"]`;
    }
    function trueAttributeSelector(attribute) {
        return `[${attribute}]:not([${attribute}=false])`;
    }
    function idSelector(id) {
        if (id.match(/^[a-z0-9\-_]+$/i)) {
            return `#${id}`;
        }
        else {
            return attributeSelector('id', id);
        }
    }
    /*-
    @function up.element.classSelector
    @internal
    */
    function classSelector(klass) {
        klass = klass.replace(/:/g, '\\:');
        return `.${klass}`;
    }
    /*-
    Always creates a full document with a <html> root, even if the given `html`
    is only a fragment.
  
    @function up.element.createDocumentFromHTML
    @internal
    */
    function createDocumentFromHTML(html) {
        return new DOMParser().parseFromString(html, 'text/html');
    }
    /*-
    Creates an element from the given HTML fragment.
  
    Use `up.hello()` to activate JavaScript behavior within the created element.
  
    ### Example
  
    ```js
    element = up.element.createFromHTML('<div class="foo"><span>text</span></div>')
    element.className // returns 'foo'
    element.children[0] // returns <span> element
    element.children[0].textContent // returns 'text'
    ```
  
    @function up.element.createFromHTML
    @stable
    */
    function createFromHTML(html) {
        // (1) We cannot use createDocumentFromHTML() here, since up.ResponseDoc
        //     needs to create <noscript> elements, and DOMParser cannot create those.
        // (2) We cannot use innerHTML on an anonymous element here, since up.ResponseDoc
        //     needs to create executable <script> elements and setting innerHTML will
        //     create intert <script> elements.
        // (3) Using Range#createContextualFragment() is significantly faster than setting
        //     innerHTML on Chrome. See https://jsben.ch/QQngJ
        const range = document.createRange();
        range.setStart(document.body, 0);
        const fragment = range.createContextualFragment(html.trim());
        let elements = fragment.childNodes;
        if (elements.length !== 1) {
            throw new Error('HTML must have a single root element');
        }
        return elements[0];
    }
    /*-
    @function up.element.root
    @internal
    */
    function getRoot() {
        return document.documentElement;
    }
    /*-
    Forces the browser to paint the given element now.
  
    @function up.element.paint
    @internal
    */
    function paint(element) {
        element.offsetHeight;
    }
    /*-
    @function up.element.concludeCSSTransition
    @internal
    */
    function concludeCSSTransition(element) {
        const undo = setTemporaryStyle(element, { transition: 'none' });
        // Browsers need to paint at least one frame without a transition to stop the
        // animation. In theory we could just wait until the next paint, but in case
        // someone will set another transition after us, let's force a repaint here.
        paint(element);
        return undo;
    }
    /*-
    Returns whether the given element has a CSS transition set.
  
    @function up.element.hasCSSTransition
    @return {boolean}
    @internal
    */
    function hasCSSTransition(elementOrStyleHash) {
        let styleHash;
        if (u.isOptions(elementOrStyleHash)) {
            styleHash = elementOrStyleHash;
        }
        else {
            styleHash = computedStyle(elementOrStyleHash);
        }
        const prop = styleHash.transitionProperty;
        const duration = styleHash.transitionDuration;
        // The default transition for elements is actually "all 0s ease 0s"
        // instead of "none", although that has the same effect as "none".
        const noTransition = ((prop === 'none') || ((prop === 'all') && (duration === 0)));
        return !noTransition;
    }
    /*-
    @function up.element.fixedToAbsolute
    @internal
    */
    function fixedToAbsolute(element) {
        const elementRectAsFixed = element.getBoundingClientRect();
        // Set the position to 'absolute' so it gains an offsetParent
        element.style.position = 'absolute';
        const offsetParentRect = element.offsetParent.getBoundingClientRect();
        setInlineStyle(element, {
            left: elementRectAsFixed.left - computedStyleNumber(element, 'margin-left') - offsetParentRect.left,
            top: elementRectAsFixed.top - computedStyleNumber(element, 'margin-top') - offsetParentRect.top,
            right: '',
            bottom: ''
        });
    }
    /*-
    On the given element, set attributes that are still missing.
  
    @function up.element.setMissingAttrs
    @internal
    */
    function setMissingAttrs(element, attrs) {
        for (let key in attrs) {
            setMissingAttr(element, key, attrs[key]);
        }
    }
    function setMissingAttr(element, key, value) {
        if (u.isMissing(element.getAttribute(key))) {
            element.setAttribute(key, value);
        }
    }
    /*-
    @function up.element.unwrap
    @internal
    */
    function unwrap(wrapper) {
        const parent = wrapper.parentNode;
        const wrappedNodes = u.toArray(wrapper.childNodes);
        u.each(wrappedNodes, wrappedNode => parent.insertBefore(wrappedNode, wrapper));
        parent.removeChild(wrapper);
    }
    function wrapChildren(element, wrapperSelector = 'up-wrapper') {
        let childNode;
        const wrapper = createFromSelector(wrapperSelector);
        while ((childNode = element.firstChild)) {
            wrapper.appendChild(childNode);
        }
        element.appendChild(wrapper);
        return wrapper;
    }
    //  ###**
    //  Returns the value of the given attribute on the given element, if the value is [present](/up.util.isPresent).
    //
    //  Returns `undefined` if the attribute is not set, or if it is set to an empty string.
    //
    //  @function up.element.presentAttr
    //  @param {Element} element
    //    The element from which to retrieve the attribute value.
    //  @param {string} attribute
    //    The attribute name.
    //  @return {string|undefined}
    //    The attribute value, if present.
    //  @experimental
    //  ###
    //  presentAttr = (element, attribute) ->
    //    value = element.getAttribute(attribute)
    //    u.presence(value)
    /*-
    Returns the given `attribute` value for the given `element`.
  
    If the element does not have the given attribute, it returns `undefined`.
    This is a difference to the native `Element#getAttribute()`, which [mostly returns `null` in that case](https://developer.mozilla.org/en-US/docs/Web/API/Element/getAttribute#Non-existing_attributes).
  
    If the element has the attribute but without value (e.g. '<input readonly>'>), it returns an empty string.
  
    @function up.element.attr
    @stable
    */
    function stringAttr(element, attribute) {
        return u.nullToUndefined(element.getAttribute(attribute));
    }
    /*-
    Returns the value of the given attribute on the given element, cast as a boolean value.
  
    If the attribute value cannot be cast to `true` or `false`, `undefined` is returned.
  
    ### Casting rules
  
    This function deviates from the
    [HTML Standard for boolean attributes](https://html.spec.whatwg.org/multipage/common-microsyntaxes.html#boolean-attributes)
    in order to allow `undefined` values. When an attribute is missing, Unpoly considers the value to be `undefined`
    (where the standard would assume `false`).
  
    Unpoly also allows `"true"` and `"false"` as attribute values.
  
    The table below shows return values for `up.element.booleanAttr(element, 'foo')` given different elements:
  
    | Element             | Return value |
    |---------------------|--------------|
    | `<div foo>`         | `true`       |
    | `<div foo="foo">`   | `true`       |
    | `<div foo="true">`  | `true`       |
    | `<div foo="">`      | `true`       |
    | `<div foo="false">` | `false`      |
    | `<div>`             | `undefined`  |
    | `<div foo="bar">`   | `undefined`  |
  
    @function up.element.booleanAttr
    @param {Element} element
      The element from which to retrieve the attribute value.
    @param {string} attribute
      The attribute name.
    @return {boolean|undefined}
      The cast attribute value.
    @stable
    */
    function booleanAttr(element, attribute, pass) {
        const value = stringAttr(element, attribute);
        switch (value) {
            case 'false': {
                return false;
            }
            case 'true':
            case '':
            case attribute: {
                return true;
            }
            default: {
                if (pass) {
                    return value;
                }
            }
        }
    }
    /*-
    Returns the given attribute value cast as boolean.
  
    If the attribute value cannot be cast, returns the attribute value unchanged.
  
    @function up.element.booleanOrStringAttr
    @param {Element} element
      The element from which to retrieve the attribute value.
    @param {string} attribute
      The attribute name.
    @internal
    */
    function booleanOrStringAttr(element, attribute) {
        return booleanAttr(element, attribute, true);
    }
    /*-
    Returns the value of the given attribute on the given element, cast to a number.
  
    If the attribute value cannot be cast to a number, `undefined` is returned.
  
    @function up.element.numberAttr
    @param {Element} element
      The element from which to retrieve the attribute value.
    @param {string} attribute
      The attribute name.
    @return {number|undefined}
      The cast attribute value.
    @stable
    */
    function numberAttr(element, attribute) {
        let value = element.getAttribute(attribute);
        if (value) {
            value = value.replace(/_/g, '');
            if (value.match(/^[\d.]+$/)) {
                return parseFloat(value);
            }
        }
    }
    /*-
    Reads the given attribute from the element, parsed as [JSON](https://www.json.org/).
  
    Returns `undefined` if the attribute value is [blank](/up.util.isBlank).
  
    Throws a `SyntaxError` if the attribute value is an invalid JSON string.
  
    @function up.element.jsonAttr
    @param {Element} element
      The element from which to retrieve the attribute value.
    @param {string} attribute
      The attribute name.
    @return {Object|undefined}
      The cast attribute value.
    @stable
    */
    function jsonAttr(element, attribute) {
        // The document does not respond to #getAttribute()
        let json = element.getAttribute?.(attribute)?.trim();
        if (json) {
            return JSON.parse(json);
        }
    }
    function callbackAttr(link, attr, exposedKeys = []) {
        let code = link.getAttribute(attr);
        if (code) {
            // Allow callbacks to refer to an exposed property directly instead of through `event.value`.
            const callback = up.NonceableCallback.fromString(code).toFunction('event', ...exposedKeys);
            // Emulate the behavior of the `onclick` attribute,
            // where `this` refers to the clicked element.
            return function (event) {
                const exposedValues = u.values(u.pick(event, exposedKeys));
                return callback.call(link, event, ...exposedValues);
            };
        }
    }
    function closestAttr(element, attr) {
        return closest(element, '[' + attr + ']')?.getAttribute(attr);
    }
    /*-
    Temporarily sets the inline CSS styles on the given element.
  
    Returns a function that restores the original inline styles when called.
  
    ### Example
  
        element = document.querySelector('div')
        unhide = up.element.setTemporaryStyle(element, { 'visibility': 'hidden' })
        // do things while element is invisible
        unhide()
        // element is visible again
  
    @function up.element.setTemporaryStyle
    @param {Element} element
      The element to style.
    @param {Object} styles
      An object of CSS property names and values.
    @return {Function()}
      A function that restores the original inline styles when called.
    @internal
    */
    function setTemporaryStyle(element, newStyles) {
        const oldStyles = inlineStyle(element, Object.keys(newStyles));
        setInlineStyle(element, newStyles);
        return () => setInlineStyle(element, oldStyles);
    }
    /*-
    Receives [computed CSS styles](https://developer.mozilla.org/en-US/docs/Web/API/Window/getComputedStyle)
    for the given element.
  
    ### Examples
  
    When requesting a single CSS property, its value will be returned as a string:
  
        value = up.element.style(element, 'font-size')
        // value is '16px'
  
    When requesting multiple CSS properties, the function returns an object of property names and values:
  
        value = up.element.style(element, ['font-size', 'margin-top'])
        // value is { 'font-size': '16px', 'margin-top': '10px' }
  
    @function up.element.style
    @param {Element} element
    @param {String|Array} propOrProps
      One or more CSS property names in kebab-case or camelCase.
    @return {string|object}
    @stable
    */
    function computedStyle(element, props) {
        const style = window.getComputedStyle(element);
        return extractFromStyleObject(style, props);
    }
    /*-
    Receives a [computed CSS property value](https://developer.mozilla.org/en-US/docs/Web/API/Window/getComputedStyle)
    for the given element, casted as a number.
  
    The value is casted by removing the property's [unit](https://www.w3schools.com/cssref/css_units.asp) (which is usually `px` for computed properties).
    The result is then parsed as a floating point number.
  
    Returns `undefined` if the property value is missing, or if it cannot
    be parsed as a number.
  
    ### Examples
  
    When requesting a single CSS property, its value will be returned as a string:
  
        value = up.element.style(element, 'font-size')
        // value is '16px'
  
        value = up.element.styleNumber(element, 'font-size')
        // value is 16
  
    @function up.element.styleNumber
    @param {Element} element
    @param {string} prop
      A single property name in kebab-case or camelCase.
    @return {number|undefined}
    @stable
    */
    function computedStyleNumber(element, prop) {
        const rawValue = computedStyle(element, prop);
        if (u.isGiven(rawValue)) {
            return parseFloat(rawValue);
        }
    }
    /*-
    Gets the given inline style(s) from the given element's `[style]` attribute.
  
    @function up.element.inlineStyle
    @param {Element} element
    @param {String|Array} propOrProps
      One or more CSS property names in kebab-case or camelCase.
    @return {string|object}
    @internal
    */
    function inlineStyle(element, props) {
        const { style } = element;
        return extractFromStyleObject(style, props);
    }
    function extractFromStyleObject(style, keyOrKeys) {
        if (u.isString(keyOrKeys)) {
            return style[keyOrKeys];
        }
        else { // array
            return u.pick(style, keyOrKeys);
        }
    }
    /*-
    Sets the given CSS properties as inline styles on the given element.
  
    @function up.element.setStyle
    @param {Element} element
    @param {Object} props
      One or more CSS properties with kebab-case keys or camelCase keys.
    @return {string|object}
    @stable
    */
    function setInlineStyle(element, props) {
        if (u.isString(props)) {
            element.setAttribute('style', props);
        }
        else {
            const { style } = element;
            for (let key in props) {
                let value = props[key];
                value = normalizeStyleValueForWrite(key, value);
                style[key] = value;
            }
        }
    }
    function normalizeStyleValueForWrite(key, value) {
        if (u.isMissing(value)) {
            value = '';
        }
        else if (CSS_LENGTH_PROPS.has(key.toLowerCase().replace(/-/, ''))) {
            value = cssLength(value);
        }
        return value;
    }
    const CSS_LENGTH_PROPS = u.arrayToSet([
        'top', 'right', 'bottom', 'left',
        'padding', 'paddingtop', 'paddingright', 'paddingbottom', 'paddingleft',
        'margin', 'margintop', 'marginright', 'marginbottom', 'marginleft',
        'borderwidth', 'bordertopwidth', 'borderrightwidth', 'borderbottomwidth', 'borderleftwidth',
        'width', 'height',
        'maxwidth', 'maxheight',
        'minwidth', 'minheight',
    ]);
    /*-
    Converts the given value to a CSS length value, adding a `px` unit if required.
  
    @function cssLength
    @internal
    */
    function cssLength(obj) {
        if (u.isNumber(obj) || (u.isString(obj) && /^\d+$/.test(obj))) {
            return obj.toString() + "px";
        }
        else {
            return obj;
        }
    }
    /*-
    Returns whether the given element is currently visible.
  
    An element is considered visible if it consumes space in the document.
    Elements with `{ visibility: hidden }` or `{ opacity: 0 }` are considered visible, since they still consume space in the layout.
  
    Elements not attached to the DOM are considered hidden.
  
    @function up.element.isVisible
    @param {Element} element
      The element to check.
    @return {boolean}
    @stable
    */
    function isVisible(element) {
        // From https://github.com/jquery/jquery/blame/9cb162f6b62b6d4403060a0f0d2065d3ae96bbcc/src/css/hiddenVisibleSelectors.js#L12
        return !!(element.offsetWidth || element.offsetHeight || element.getClientRects().length);
    }
    function upAttrs(element) {
        const upAttributePattern = /^up-/;
        const attrs = {};
        for (let attribute of element.attributes) {
            const { name } = attribute;
            if (name.match(upAttributePattern)) {
                attrs[name] = attribute.value;
            }
        }
        return attrs;
    }
    /*-
    Returns whether the given element has been removed from the DOM tree.
  
    @function up.element.isDetached
    @param {Element} element
    @return {boolean}
    @stable
    */
    function isDetached(element) {
        return (element !== document) && !getRoot().contains(element);
    }
    return {
        all,
        subtree,
        isInSubtree,
        closest,
        closestAttr,
        matches,
        ancestor,
        around,
        get: getOne,
        list: getList,
        remove,
        toggle,
        toggleClass,
        hide,
        show,
        metaContent,
        replace,
        insertBefore,
        createFromSelector,
        setAttrs,
        setTemporaryAttrs,
        affix,
        toSelector,
        idSelector,
        classSelector,
        isSingleton,
        isSingletonSelector,
        attributeSelector,
        trueAttributeSelector,
        tagName: elementTagName,
        createDocumentFromHTML,
        createFromHTML,
        get root() { return getRoot(); },
        paint,
        concludeCSSTransition,
        hasCSSTransition,
        fixedToAbsolute,
        setMissingAttrs,
        setMissingAttr,
        unwrap,
        wrapChildren,
        // presentAttr: presentAttr # experimental
        attr: stringAttr,
        booleanAttr,
        numberAttr,
        jsonAttr,
        callbackAttr,
        booleanOrStringAttr,
        setTemporaryStyle,
        style: computedStyle,
        styleNumber: computedStyleNumber,
        inlineStyle,
        setStyle: setInlineStyle,
        isVisible,
        upAttrs,
        toggleAttr,
        isDetached
    };
})();


/***/ }),
/* 8 */
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin


/***/ }),
/* 9 */
/***/ (() => {

const u = up.util;
up.Record = class Record {
    keys() {
        throw 'Return an array of keys';
    }
    defaults(_options) {
        return {};
    }
    constructor(options) {
        u.assign(this, this.defaults(options), this.attributes(options));
    }
    attributes(source = this) {
        return u.pick(source, this.keys());
    }
    [u.copy.key]() {
        return this.variant();
    }
    variant(changes = {}) {
        return new this.constructor(u.merge(this.attributes(), changes));
    }
    [u.isEqual.key](other) {
        return (this.constructor === other.constructor) && u.isEqual(this.attributes(), other.attributes());
    }
};


/***/ }),
/* 10 */
/***/ (() => {

const u = up.util;
up.Config = class Config {
    constructor(blueprintFn = (() => ({}))) {
        this.blueprintFn = blueprintFn;
        this.reset();
    }
    reset() {
        u.assign(this, this.blueprintFn());
    }
};


/***/ }),
/* 11 */
/***/ (() => {

const u = up.util;
/*-
@class up.Cache
@internal
*/
up.Cache = class Cache {
    /*-
    @constructor up.Cache
    @param {number|Function(): number} [config.size]
      Maximum number of cache entries.
      Set to `undefined` to not limit the cache size.
    @param {number|Function(): number} [config.expiry]
      The number of milliseconds after which a cache entry
      will be discarded.
    @param {string} [config.logPrefix]
      A prefix for log entries printed by this cache object.
    @param {Function(entry): string} [config.key]
      A function that takes an argument and returns a string key
      for storage. If omitted, `toString()` is called on the argument.
    @param {Function(entry): boolean} [config.cacheable]
      A function that takes a potential cache entry and returns whether
      this entry  can be stored in the hash. If omitted, all entries are considered
      cacheable.
    @internal
    */
    constructor(config = {}) {
        this.config = config;
        this.store = this.config.store || new up.store.Memory();
    }
    size() {
        return this.store.size();
    }
    maxSize() {
        return u.evalOption(this.config.size);
    }
    expiryMillis() {
        return u.evalOption(this.config.expiry);
    }
    normalizeStoreKey(key) {
        if (this.config.key) {
            return this.config.key(key);
        }
        else {
            return key.toString();
        }
    }
    isEnabled() {
        return (this.maxSize() !== 0) && (this.expiryMillis() !== 0);
    }
    clear() {
        this.store.clear();
    }
    log(...args) {
        if (this.config.logPrefix) {
            args[0] = `[${this.config.logPrefix}] ${args[0]}`;
            up.puts('up.Cache', ...args);
        }
    }
    keys() {
        return this.store.keys();
    }
    each(fn) {
        u.each(this.keys(), key => {
            const entry = this.store.get(key);
            fn(key, entry.value, entry.timestamp);
        });
    }
    makeRoomForAnotherEntry() {
        if (this.hasRoomForAnotherEntry()) {
            return;
        }
        let oldestKey;
        let oldestTimestamp;
        this.each(function (key, request, timestamp) {
            if (!oldestTimestamp || (oldestTimestamp > timestamp)) {
                oldestKey = key;
                oldestTimestamp = timestamp;
            }
        });
        if (oldestKey) {
            this.store.remove(oldestKey);
        }
    }
    hasRoomForAnotherEntry() {
        const maxSize = this.maxSize();
        return !maxSize || (this.size() < maxSize);
    }
    alias(oldKey, newKey) {
        const value = this.get(oldKey, { silent: true });
        if (u.isDefined(value)) {
            this.set(newKey, value);
        }
    }
    timestamp() {
        return (new Date()).valueOf();
    }
    set(key, value) {
        if (this.isEnabled()) {
            this.makeRoomForAnotherEntry();
            const storeKey = this.normalizeStoreKey(key);
            const entry = {
                timestamp: this.timestamp(),
                value
            };
            this.store.set(storeKey, entry);
        }
    }
    remove(key) {
        const storeKey = this.normalizeStoreKey(key);
        this.store.remove(storeKey);
    }
    isFresh(entry) {
        const millis = this.expiryMillis();
        if (millis) {
            const timeSinceTouch = this.timestamp() - entry.timestamp;
            return timeSinceTouch < millis;
        }
        else {
            return true;
        }
    }
    get(key, options = {}) {
        const storeKey = this.normalizeStoreKey(key);
        let entry = this.store.get(storeKey);
        if (entry) {
            if (this.isFresh(entry)) {
                if (!options.silent) {
                    this.log("Cache hit for '%s'", key);
                }
                return entry.value;
            }
            else {
                if (!options.silent) {
                    this.log("Discarding stale cache entry for '%s'", key);
                }
                this.remove(key);
            }
        }
        else {
            if (!options.silent) {
                this.log("Cache miss for '%s'", key);
            }
        }
    }
};


/***/ }),
/* 12 */
/***/ (() => {

up.Rect = class Rect extends up.Record {
    keys() {
        return [
            'left',
            'top',
            'width',
            'height'
        ];
    }
    get bottom() {
        return this.top + this.height;
    }
    get right() {
        return this.left + this.width;
    }
    static fromElement(element) {
        return new (this)(element.getBoundingClientRect());
    }
};


/***/ }),
/* 13 */
/***/ (() => {

const e = up.element;
// Gives `<body>` a right padding in the width of a scrollbar.
// Also gives elements anchored to the right side of the screen
// an increased `right`.
//
// This is to prevent the body and elements from jumping when we add the
// modal overlay, which has its own scroll bar.
// This is screwed up, but Bootstrap does the same.
up.BodyShifter = class BodyShifter {
    constructor() {
        this.unshiftFns = [];
        this.reset();
    }
    reset() {
        this.unshiftNow();
        this.shiftCount = 0;
    }
    shift() {
        this.shiftCount++;
        if (this.shiftCount > 1) {
            return;
        }
        // Remember whether the root viewport has a visible scrollbar at rest.
        // It will disappear when we set overflow-y: hidden below.
        const scrollbarTookSpace = up.viewport.rootHasReducedWidthFromScrollbar();
        // Even if root viewport has no scroll bar, we still want to give overflow-y: hidden
        // to the <body> element. Otherwise the user could scroll the underlying page by
        // scrolling over the dimmed backdrop (observable with touch emulation in Chrome DevTools).
        // Note that some devices don't show a vertical scrollbar at rest for a viewport, even
        // when it can be scrolled.
        const overflowElement = up.viewport.rootOverflowElement();
        this.changeStyle(overflowElement, { overflowY: 'hidden' });
        // If the scrollbar never took space away from the main viewport's client width,
        // we do not need to run the code below that would pad it on the right.
        if (!scrollbarTookSpace) {
            return;
        }
        const { body } = document;
        const scrollbarWidth = up.viewport.scrollbarWidth();
        const bodyRightPadding = e.styleNumber(body, 'paddingRight');
        const bodyRightShift = scrollbarWidth + bodyRightPadding;
        this.changeStyle(body, { paddingRight: bodyRightShift });
        for (let anchor of up.viewport.anchoredRight()) {
            const elementRight = e.styleNumber(anchor, 'right');
            const elementRightShift = scrollbarWidth + elementRight;
            this.changeStyle(anchor, { right: elementRightShift });
        }
    }
    changeStyle(element, styles) {
        this.unshiftFns.push(e.setTemporaryStyle(element, styles));
    }
    unshift() {
        this.shiftCount--;
        if (this.shiftCount == 0) {
            this.unshiftNow();
        }
    }
    unshiftNow() {
        let unshiftFn;
        while (unshiftFn = this.unshiftFns.pop()) {
            unshiftFn();
        }
    }
};


/***/ }),
/* 14 */
/***/ (() => {

const u = up.util;
up.Change = class Change {
    constructor(options) {
        this.options = options;
    }
    notApplicable(reason) {
        return up.error.notApplicable(this, reason);
    }
    execute() {
        throw up.error.notImplemented();
    }
    onFinished() {
        return this.options.onFinished?.();
    }
    // Values we want to keep:
    // - false (no update)
    // - string (forced update)
    // Values we want to override:
    // - true (do update with defaults)
    improveHistoryValue(existingValue, newValue) {
        if ((existingValue === false) || u.isString(existingValue)) {
            return existingValue;
        }
        else {
            return newValue;
        }
    }
};


/***/ }),
/* 15 */
/***/ (() => {

const u = up.util;
const e = up.element;
up.Change.Addition = class Addition extends up.Change {
    constructor(options) {
        super(options);
        this.responseDoc = options.responseDoc;
        this.acceptLayer = options.acceptLayer;
        this.dismissLayer = options.dismissLayer;
        this.eventPlans = options.eventPlans || [];
    }
    handleLayerChangeRequests() {
        if (this.layer.isOverlay()) {
            // The server may send an HTTP header `X-Up-Accept-Layer: value`
            this.tryAcceptLayerFromServer();
            this.abortWhenLayerClosed();
            // A close condition { acceptLocation: '/path' } might have been
            // set when the layer was opened.
            this.layer.tryAcceptForLocation();
            this.abortWhenLayerClosed();
            // The server may send an HTTP header `X-Up-Dismiss-Layer: value`
            this.tryDismissLayerFromServer();
            this.abortWhenLayerClosed();
            // A close condition { dismissLocation: '/path' } might have been
            // set when the layer was opened.
            this.layer.tryDismissForLocation();
            this.abortWhenLayerClosed();
        }
        // On the server we support up.layer.emit('foo'), which sends:
        //
        //     X-Up-Events: [{ layer: 'current', type: 'foo'}]
        //
        // We must set the current layer to @layer so { layer: 'current' } will emit on
        // the layer that is being updated, instead of the front layer.
        //
        // A listener to such a server-sent event might also close the layer.
        this.layer.asCurrent(() => {
            for (let eventPlan of this.eventPlans) {
                up.emit(eventPlan);
                this.abortWhenLayerClosed();
            }
        });
    }
    tryAcceptLayerFromServer() {
        // When accepting without a value, the server will send X-Up-Accept-Layer: null
        if (u.isDefined(this.acceptLayer) && this.layer.isOverlay()) {
            this.layer.accept(this.acceptLayer);
        }
    }
    tryDismissLayerFromServer() {
        // When dismissing without a value, the server will send X-Up-Dismiss-Layer: null
        if (u.isDefined(this.dismissLayer) && this.layer.isOverlay()) {
            this.layer.dismiss(this.dismissLayer);
        }
    }
    abortWhenLayerClosed() {
        if (this.layer.isClosed()) {
            // Wind up the call stack. Whoever has closed the layer will also clean up
            // elements, handlers, etc.
            throw up.error.aborted('Layer was closed');
        }
    }
    setSource({ oldElement, newElement, source }) {
        // (1) When the server responds with an error, or when the request method is not
        //     reloadable (not GET), we keep the same source as before.
        // (2) Don't set a source if someone tries to 'keep' when opening a new layer
        if (source === 'keep') {
            source = (oldElement && up.fragment.source(oldElement));
        }
        // (1) Don't set a source if { false } is passed.
        // (2) Don't set a source if the element HTML already has an [up-source] attribute.
        if (source) {
            e.setMissingAttr(newElement, 'up-source', u.normalizeURL(source, { hash: false }));
        }
    }
};


/***/ }),
/* 16 */
/***/ (() => {

up.Change.Removal = class Removal extends up.Change {
};


/***/ }),
/* 17 */
/***/ (() => {

const e = up.element;
up.Change.DestroyFragment = class DestroyFragment extends up.Change.Removal {
    constructor(options) {
        super(options);
        this.layer = up.layer.get(options) || up.layer.current;
        this.element = this.options.element;
        this.animation = this.options.animation;
        this.log = this.options.log;
    }
    async execute() {
        // Destroying a fragment is a sync function.
        //
        // A variant of the logic below can also be found in up.Change.UpdateLayer.
        // Updating (swapping) a fragment also involves destroying the old version,
        // but the logic needs to be interwoven with the insertion logic for the new
        // version.
        // Save the parent because we emit up:fragment:destroyed on the parent
        // after removing @element.
        this.parent = this.element.parentNode;
        // The destroying fragment gets an .up-destroying class so we can
        // recognize elements that are being destroyed but are still playing out their
        // removal animation.
        up.fragment.markAsDestroying(this.element);
        if (up.motion.willAnimate(this.element, this.animation, this.options)) {
            // If we're animating, we resolve *before* removing the element.
            // The destroy animation will then play out, but the destroying
            // element is ignored by all up.fragment.* functions.
            this.emitDestroyed();
            await this.animate();
            this.wipe();
            this.onFinished();
        }
        else {
            // If we're not animating, we can remove the element before emitting up:fragment:destroyed.
            this.wipe();
            this.emitDestroyed();
            this.onFinished();
        }
    }
    animate() {
        return up.motion.animate(this.element, this.animation, this.options);
    }
    wipe() {
        this.layer.asCurrent(() => {
            up.syntax.clean(this.element, { layer: this.layer });
            if (up.browser.canJQuery()) {
                // jQuery elements store internal attributes in a global cache.
                // We need to remove the element via jQuery or we will leak memory.
                // See https://makandracards.com/makandra/31325-how-to-create-memory-leaks-in-jquery
                jQuery(this.element).remove();
            }
            else {
                e.remove(this.element);
            }
        });
    }
    emitDestroyed() {
        // Emits up:fragment:destroyed.
        up.fragment.emitDestroyed(this.element, { parent: this.parent, log: this.log });
    }
};


/***/ }),
/* 18 */
/***/ (() => {

up.Change.OpenLayer = class OpenLayer extends up.Change.Addition {
    constructor(options) {
        super(options);
        this.target = options.target;
        this.origin = options.origin;
        this.baseLayer = options.baseLayer;
        // Don't extract too many @properties from @options, since listeners
        // to up:layer:open may modify layer options.
    }
    preflightProps() {
        // We assume that the server will respond with our target.
        // Hence this change will always be applicable.
        return {
            // We associate this request to our current layer so up:request events
            // may be emitted on something more specific than the document.
            layer: this.baseLayer,
            mode: this.options.mode,
            context: this.buildLayer().context,
            // The target will always exist in the current page, since
            // we're opening a new layer that will match the target.
            target: this.target
        };
    }
    bestPreflightSelector() {
        // We assume that the server will respond with our target.
        return this.target;
    }
    execute(responseDoc, onApplicable) {
        if (this.target === ':none') {
            this.content = document.createElement('up-none');
        }
        else {
            this.content = responseDoc.select(this.target);
        }
        if (!this.content || this.baseLayer.isClosed()) {
            throw this.notApplicable();
        }
        onApplicable();
        up.puts('up.render()', `Opening element "${this.target}" in new overlay`);
        this.options.title = this.improveHistoryValue(this.options.title, responseDoc.getTitle());
        if (this.emitOpenEvent().defaultPrevented) {
            // We cannot use @abortWhenLayerClosed() here,
            // because the layer is not even in the stack yet.
            throw up.error.aborted('Open event was prevented');
        }
        // Make sure that the baseLayer layer doesn't already have a child layer.
        // Note that this cannot be prevented with { peel: false }!
        // We don't wait for the peeling to finish.
        this.baseLayer.peel();
        // Change the stack sync. Don't wait for peeling to finish.
        this.layer = this.buildLayer();
        up.layer.stack.push(this.layer);
        this.layer.createElements(this.content);
        this.layer.setupHandlers();
        // Change history before compilation, so new fragments see the new location.
        this.handleHistory();
        // Remember where the element came from to support up.reload(element).
        this.setSource({ newElement: this.content, source: this.options.source });
        // Unwrap <noscript> tags
        responseDoc.finalizeElement(this.content);
        // Compile the entire layer, not just the user content.
        // E.g. [up-dismiss] in the layer elements needs to go through a macro.
        up.hello(this.layer.element, { layer: this.layer, origin: this.origin });
        // The server may trigger multiple signals that may cause the layer to close:
        //
        // - Close the layer directly through X-Up-Accept-Layer or X-Up-Dismiss-Layer
        // - Emit an event with X-Up-Events, to which a listener may close the layer
        // - Update the location to a URL for which { acceptLocation } or { dismissLocation }
        //   will close the layer.
        //
        // Note that @handleLayerChangeRequests() also calls throws an up.error.aborted
        // if any of these options cause the layer to close.
        this.handleLayerChangeRequests();
        // Don't wait for the open animation to finish.
        // Otherwise a popup would start to open and only reveal itself after the animation.
        this.handleScroll();
        this.layer.startOpenAnimation().then(() => {
            // A11Y: Place the focus on the overlay element and setup a focus circle.
            // However, don't change focus if the layer has been closed while the animation was running.
            if (this.layer.isOpen()) {
                this.handleFocus();
            }
            // Run callbacks for callers that need to know when animations are done.
            this.onFinished();
        });
        // Emit up:layer:opened to indicate that the layer was opened successfully.
        // This is a good time for listeners to manipulate the overlay optics.
        this.layer.opening = false;
        this.emitOpenedEvent();
        // In case a listener to up:layer:opened immediately dimisses the new layer,
        // reject the promise returned by up.layer.open().
        this.abortWhenLayerClosed();
        // Resolve the promise with the layer instance, so callers can do:
        //
        //     layer = await up.layer.open(...)
        //
        // Don't wait to animations to finish:
        return new up.RenderResult({
            layer: this.layer,
            fragments: [this.content]
        });
    }
    buildLayer() {
        // We need to mark the layer as { opening: true } so its topmost swappable element
        // does not resolve from the :layer pseudo-selector. Since :layer is a part of
        // up.fragment.config.mainTargets and :main is a part of fragment.config.autoHistoryTargets,
        // this would otherwise cause auto-history for *every* overlay regardless of initial target.
        const buildOptions = { ...this.options, opening: true };
        const beforeNew = optionsWithLayerDefaults => {
            return this.options = up.RenderOptions.finalize(optionsWithLayerDefaults);
        };
        return up.layer.build(buildOptions, beforeNew);
    }
    handleHistory() {
        if (this.layer.history === 'auto') {
            this.layer.history = up.fragment.hasAutoHistory(this.content);
        }
        this.layer.parent.saveHistory();
        // For the initial fragment insertion we always update history, even if the layer
        // does not have visible history ({ history } attribute). This ensures that a
        // layer always has a #location.
        this.layer.updateHistory(this.options);
    }
    handleFocus() {
        this.baseLayer.overlayFocus?.moveToBack();
        this.layer.overlayFocus.moveToFront();
        const fragmentFocus = new up.FragmentFocus({
            fragment: this.content,
            layer: this.layer,
            autoMeans: ['autofocus', 'layer']
        });
        fragmentFocus.process(this.options.focus);
    }
    handleScroll() {
        const scrollingOptions = {
            ...this.options,
            fragment: this.content,
            layer: this.layer,
            autoMeans: ['hash', 'layer']
        };
        const scrolling = new up.FragmentScrolling(scrollingOptions);
        scrolling.process(this.options.scroll);
    }
    emitOpenEvent() {
        // The initial up:layer:open event is emitted on the document, since the layer
        // element has not been attached yet and there is no obvious element it should
        // be emitted on. We don't want to emit it on @layer.parent.element since users
        // might confuse this with the event for @layer.parent itself opening.
        //
        // There is no @layer.onOpen() handler to accompany the DOM event.
        return up.emit('up:layer:open', {
            origin: this.origin,
            baseLayer: this.baseLayer,
            layerOptions: this.options,
            log: "Opening new overlay"
        });
    }
    emitOpenedEvent() {
        return this.layer.emit('up:layer:opened', {
            origin: this.origin,
            callback: this.layer.callback('onOpened'),
            log: `Opened new ${this.layer}`
        });
    }
};


/***/ }),
/* 19 */
/***/ (() => {

const u = up.util;
const e = up.element;
up.Change.UpdateLayer = class UpdateLayer extends up.Change.Addition {
    constructor(options) {
        options = up.RenderOptions.finalize(options);
        super(options);
        this.layer = options.layer;
        this.target = options.target;
        this.placement = options.placement;
        this.context = options.context;
        this.parseSteps();
    }
    preflightProps() {
        // This will throw up.error.notApplicable() if { target } cannot
        // be found in { layer }.
        this.matchPreflight();
        return {
            layer: this.layer,
            mode: this.layer.mode,
            context: u.merge(this.layer.context, this.context),
            target: this.bestPreflightSelector(),
        };
    }
    bestPreflightSelector() {
        this.matchPreflight();
        return u.map(this.steps, 'selector').join(', ') || ':none';
    }
    execute(responseDoc, onApplicable) {
        this.responseDoc = responseDoc;
        // For each step, find a step.alternative that matches in both the current page
        // and the response document.
        this.matchPostflight();
        onApplicable();
        // Don't log @target since that does not include hungry elements
        up.puts('up.render()', `Updating "${this.bestPreflightSelector()}" in ${this.layer}`);
        this.options.title = this.improveHistoryValue(this.options.title, this.responseDoc.getTitle());
        // Make sure only the first step will have scroll-related options.
        this.setScrollAndFocusOptions();
        if (this.options.saveScroll) {
            up.viewport.saveScroll({ layer: this.layer });
        }
        if (this.options.peel) {
            this.layer.peel();
        }
        // Layer#peel() will manipulate the stack sync.
        // We don't wait for the peeling animation to finish.
        u.assign(this.layer.context, this.context);
        if (this.options.history === 'auto') {
            this.options.history = this.hasAutoHistory();
        }
        // Change history before compilation, so new fragments see the new location.
        if (this.options.history) {
            this.layer.updateHistory(this.options); // layer location changed event soll hier nicht mehr fliegen
        }
        // The server may trigger multiple signals that may cause the layer to close:
        //
        // - Close the layer directly through X-Up-Accept-Layer or X-Up-Dismiss-Layer
        // - Event an event with X-Up-Events, to which a listener may close the layer
        // - Update the location to a URL for which { acceptLocation } or { dismissLocation }
        //   will close the layer.
        //
        // Note that @handleLayerChangeRequests() also throws an up.error.aborted
        // if any of these options cause the layer to close.
        this.handleLayerChangeRequests();
        const swapPromises = this.steps.map(step => this.executeStep(step));
        Promise.all(swapPromises).then(() => {
            this.abortWhenLayerClosed();
            // Run callback for callers that need to know when animations are done.
            return this.onFinished();
        });
        // Don't wait for animations to finish.
        return new up.RenderResult({
            layer: this.layer,
            fragments: u.map(this.steps, 'newElement')
        });
    }
    async executeStep(step) {
        // Remember where the element came from to support up.reload(element).
        this.setSource(step);
        switch (step.placement) {
            case 'swap': {
                let keepPlan = this.findKeepPlan(step);
                if (keepPlan) {
                    // Since we're keeping the element that was requested to be swapped,
                    // there is nothing left to do here, except notify event listeners.
                    up.fragment.emitKept(keepPlan);
                    this.handleFocus(step.oldElement, step);
                    // Our caller expects a promise
                    await this.handleScroll(step.oldElement, step);
                }
                else {
                    // This needs to happen before up.syntax.clean() below.
                    // Otherwise we would run destructors for elements we want to keep.
                    this.transferKeepableElements(step);
                    const parent = step.oldElement.parentNode;
                    const morphOptions = {
                        ...step,
                        beforeStart() {
                            up.fragment.markAsDestroying(step.oldElement);
                        },
                        afterInsert: () => {
                            this.responseDoc.finalizeElement(step.newElement);
                            step.keepPlans.forEach(this.reviveKeepable);
                            // In the case of [up-keep] descendants, keepable elements are now transferred
                            // to step.newElement, leaving a clone in their old DOM Position.
                            // up.hello() is aware of step.keepPlans and will not compile kept elements a second time.
                            up.hello(step.newElement, step);
                        },
                        beforeDetach: () => {
                            // In the case of [up-keep] descendants, keepable elements have been replaced
                            // with a clone in step.oldElement. However, since that clone was never compiled,
                            // it does not have destructors registered. Hence we will not clean the clone
                            // unnecessarily.
                            up.syntax.clean(step.oldElement, { layer: this.layer });
                        },
                        afterDetach() {
                            e.remove(step.oldElement); // clean up jQuery data
                            up.fragment.emitDestroyed(step.oldElement, { parent, log: false });
                        },
                        scrollNew: () => {
                            this.handleFocus(step.newElement, step);
                            // up.morph() expects { scrollNew } to return a promise.
                            return this.handleScroll(step.newElement, step);
                        }
                    };
                    await up.morph(step.oldElement, step.newElement, step.transition, morphOptions);
                }
                break;
            }
            case 'content': {
                let oldWrapper = e.wrapChildren(step.oldElement);
                // oldWrapper.appendTo(step.oldElement)
                let newWrapper = e.wrapChildren(step.newElement);
                let wrapperStep = {
                    ...step,
                    placement: 'swap',
                    oldElement: oldWrapper,
                    newElement: newWrapper,
                    focus: false
                };
                await this.executeStep(wrapperStep);
                e.unwrap(newWrapper);
                // Unwrapping will destroy focus, so we need to handle it again.
                await this.handleFocus(step.oldElement, step);
                break;
            }
            case 'before':
            case 'after': {
                // We're either appending or prepending. No keepable elements must be honored.
                // Text nodes are wrapped in an <up-wrapper> container so we can
                // animate them and measure their position/size for scrolling.
                // This is not possible for container-less text nodes.
                let wrapper = e.wrapChildren(step.newElement);
                // Note that since we're prepending/appending instead of replacing,
                // newElement will not actually be inserted into the DOM, only its children.
                let position = step.placement === 'before' ? 'afterbegin' : 'beforeend';
                step.oldElement.insertAdjacentElement(position, wrapper);
                this.responseDoc.finalizeElement(wrapper);
                up.hello(wrapper, step);
                this.handleFocus(wrapper, step);
                // Reveal element that was being prepended/appended.
                // Since we will animate (not morph) it's OK to allow animation of scrolling
                // if options.scrollBehavior is given.
                await this.handleScroll(wrapper, step);
                // Since we're adding content instead of replacing, we'll only
                // animate newElement instead of morphing between oldElement and newElement
                await up.animate(wrapper, step.transition, step);
                // Remove the wrapper now that is has served it purpose
                await e.unwrap(wrapper);
                break;
            }
            default: {
                up.fail('Unknown placement: %o', step.placement);
            }
        }
    }
    // Returns a object detailling a keep operation iff the given element is [up-keep] and
    // we can find a matching partner in newElement. Otherwise returns undefined.
    //
    // @param {Element} options.oldElement
    // @param {Element} options.newElement
    // @param {boolean} options.keep
    // @param {boolean} options.descendantsOnly
    findKeepPlan(options) {
        // Going back in history uses keep: false
        if (!options.keep) {
            return;
        }
        const { oldElement, newElement } = options;
        // We support these attribute forms:
        //
        // - up-keep             => match element itself
        // - up-keep="true"      => match element itself
        // - up-keep="false"     => don't keep
        // - up-keep=".selector" => match .selector
        let partnerSelector = e.booleanOrStringAttr(oldElement, 'up-keep');
        if (partnerSelector) {
            if (partnerSelector === true) {
                partnerSelector = '&';
            }
            const lookupOpts = { layer: this.layer, origin: oldElement };
            let partner;
            if (options.descendantsOnly) {
                // Since newElement is from a freshly parsed HTML document, we could use
                // up.element functions to match the selector. However, since we also want
                // to use custom selectors like ":main" or "&" we use up.fragment.get().
                partner = up.fragment.get(newElement, partnerSelector, lookupOpts);
            }
            else {
                partner = up.fragment.subtree(newElement, partnerSelector, lookupOpts)[0];
            }
            if (partner && e.matches(partner, '[up-keep]')) {
                const plan = {
                    oldElement,
                    newElement: partner,
                    newData: up.syntax.data(partner) // the parsed up-data attribute of the element we will discard
                };
                if (!up.fragment.emitKeep(plan).defaultPrevented) {
                    return plan;
                }
            }
        }
    }
    // This will find all [up-keep] descendants in oldElement, overwrite their partner
    // element in newElement and leave a visually identical clone in oldElement for a later transition.
    // Returns an array of keepPlans.
    transferKeepableElements(step) {
        const keepPlans = [];
        if (step.keep) {
            for (let keepable of step.oldElement.querySelectorAll('[up-keep]')) {
                let keepPlan = this.findKeepPlan({ ...step, oldElement: keepable, descendantsOnly: true });
                if (keepPlan) {
                    // plan.oldElement is now keepable
                    this.hibernateKeepable(keepPlan);
                    // Replace keepable with its clone so it looks good in a transition between
                    // oldElement and newElement. Note that keepable will still point to the same element
                    // after the replacement, which is now detached.
                    const keepableClone = keepable.cloneNode(true);
                    e.replace(keepable, keepableClone);
                    // Since we're going to swap the entire oldElement and newElement containers afterwards,
                    // replace the matching element with keepable so it will eventually return to the DOM.
                    e.replace(keepPlan.newElement, keepable);
                    keepPlans.push(keepPlan);
                }
            }
        }
        step.keepPlans = keepPlans;
    }
    parseSteps() {
        this.steps = [];
        // up.fragment.expandTargets() was already called by up.Change.FromContent
        for (let simpleTarget of u.splitValues(this.target, ',')) {
            if (simpleTarget !== ':none') {
                const expressionParts = simpleTarget.match(/^(.+?)(?::(before|after))?$/);
                if (!expressionParts) {
                    throw up.error.invalidSelector(simpleTarget);
                }
                // Each step inherits all options of this change.
                const step = {
                    ...this.options,
                    selector: expressionParts[1],
                    placement: expressionParts[2] || this.placement || 'swap'
                };
                this.steps.push(step);
            }
        }
    }
    hibernateKeepable(keepPlan) {
        let viewports = up.viewport.subtree(keepPlan.oldElement);
        keepPlan.revivers = viewports.map(function (viewport) {
            let { scrollTop, scrollLeft } = viewport;
            return () => u.assign(viewport, { scrollTop, scrollLeft });
        });
    }
    reviveKeepable(keepPlan) {
        for (let reviver of keepPlan.revivers) {
            reviver();
        }
    }
    matchPreflight() {
        if (this.matchedPreflight) {
            return;
        }
        for (let step of this.steps) {
            const finder = new up.FragmentFinder(step);
            // Try to find fragments matching step.selector within step.layer.
            // Note that step.oldElement might already have been set by @parseSteps().
            step.oldElement || (step.oldElement = finder.find());
            if (!step.oldElement) {
                throw this.notApplicable(`Could not find element "${this.target}" in current page`);
            }
        }
        this.resolveOldNesting();
        this.matchedPreflight = true;
    }
    matchPostflight() {
        if (this.matchedPostflight) {
            return;
        }
        this.matchPreflight();
        for (let step of this.steps) {
            // The responseDoc has no layers.
            let newElement = this.responseDoc.select(step.selector);
            if (newElement) {
                step.newElement = newElement;
            }
            else {
                throw this.notApplicable(`Could not find element "${this.target}" in server response`);
            }
        }
        // Only when we have a match in the required selectors, we
        // append the optional steps for [up-hungry] elements.
        if (this.options.hungry) {
            this.addHungrySteps();
        }
        //    # Remove steps when their oldElement is nested inside the oldElement
        //    # of another step.
        this.resolveOldNesting();
        this.matchedPostflight = true;
    }
    addHungrySteps() {
        // Find all [up-hungry] fragments within @layer
        const hungries = up.fragment.all(up.radio.hungrySelector(), this.options);
        for (let oldElement of hungries) {
            const selector = up.fragment.toTarget(oldElement);
            const newElement = this.responseDoc.select(selector);
            if (newElement) {
                const transition = e.booleanOrStringAttr(oldElement, 'transition');
                const step = { selector, oldElement, newElement, transition, placement: 'swap' };
                this.steps.push(step);
            }
        }
    }
    containedByRivalStep(steps, candidateStep) {
        return u.some(steps, function (rivalStep) {
            return (rivalStep !== candidateStep) &&
                ((rivalStep.placement === 'swap') || (rivalStep.placement === 'content')) &&
                rivalStep.oldElement.contains(candidateStep.oldElement);
        });
    }
    resolveOldNesting() {
        let compressed = u.uniqBy(this.steps, 'oldElement');
        compressed = u.reject(compressed, step => this.containedByRivalStep(compressed, step));
        this.steps = compressed;
    }
    setScrollAndFocusOptions() {
        this.steps.forEach((step, i) => {
            // Since up.motion will call @handleScrollAndFocus() after each fragment,
            // and we only have a single scroll position and focus, only scroll/focus  for the first step.
            if (i > 0) {
                step.scroll = false;
                step.focus = false;
            }
            if ((step.placement === 'swap') || (step.placement === 'content')) {
                // We cannot animate scrolling when we're morphing between two elements.
                step.scrollBehavior = 'auto';
                // Store the focused element's selector, scroll position and selection range in an up.FocusCapsule
                // for later restoration.
                //
                // We might need to preserve focus in a fragment that is not the first step.
                // However, only a single step can include the focused element, or none.
                this.focusCapsule || (this.focusCapsule = up.FocusCapsule.preserveWithin(step.oldElement));
            }
        });
    }
    handleFocus(fragment, step) {
        const fragmentFocus = new up.FragmentFocus({
            ...step,
            fragment,
            layer: this.layer,
            focusCapsule: this.focusCapsule,
            autoMeans: up.fragment.config.autoFocus,
        });
        return fragmentFocus.process(step.focus);
    }
    handleScroll(fragment, step) {
        const scrolling = new up.FragmentScrolling({
            ...step,
            fragment,
            layer: this.layer,
            autoMeans: up.fragment.config.autoScroll
        });
        return scrolling.process(step.scroll);
    }
    hasAutoHistory() {
        const oldFragments = u.map(this.steps, 'oldElement');
        return u.some(oldFragments, oldFragment => up.fragment.hasAutoHistory(oldFragment));
    }
};


/***/ }),
/* 20 */
/***/ (() => {

const u = up.util;
up.Change.CloseLayer = class CloseLayer extends up.Change.Removal {
    constructor(options) {
        super(options);
        this.verb = options.verb;
        this.layer = up.layer.get(options);
        this.origin = options.origin;
        this.value = options.value;
        this.preventable = options.preventable ?? true;
    }
    execute() {
        // Closing a layer is a sync function.
        if (!this.layer.isOpen()) {
            return Promise.resolve();
        }
        up.browser.assertConfirmed(this.options);
        // Abort all pending requests targeting the layer we're now closing.
        up.network.abort(request => request.layer === this.layer);
        if (this.emitCloseEvent().defaultPrevented && this.preventable) {
            throw up.error.aborted('Close event was prevented');
        }
        // Remember the parent, which will no longer be accessible once we
        // remove @layer from the @stack.
        const { parent } = this.layer;
        // Close any child-layers we might have.
        // We don't wait for peeling to finish, since changes that affect the
        // layer stack should happen sync:
        this.layer.peel();
        // Remove ourselves from the layer stack.
        this.layer.stack.remove(this.layer);
        // Restore the history of the parent layer we just uncovered.
        parent.restoreHistory();
        this.handleFocus(parent);
        this.layer.teardownHandlers();
        this.layer.destroyElements(this.options); // this will also pass the { onFinished } option
        this.emitClosedEvent(parent);
    }
    emitCloseEvent() {
        // The close event is emitted on the layer that is about to close.
        return this.layer.emit(this.buildEvent(`up:layer:${this.verb}`), {
            callback: this.layer.callback(`on${u.upperCaseFirst(this.verb)}`),
            log: [`Will ${this.verb} ${this.layer} with value %o`, this.value]
        });
    }
    emitClosedEvent(formerParent) {
        const verbPast = `${this.verb}ed`;
        const verbPastUpperCaseFirst = u.upperCaseFirst(verbPast);
        // layer.emit({ ensureBubbles: true }) will automatically emit a second event on document
        // because the layer is detached. We do not want to emit it on the parent layer where users
        // might confuse it with an event for the parent layer itself. Since @layer.element
        // is now detached, the event will no longer bubble up to the document where global
        // event listeners can receive it. So we explicitly emit the event a second time
        // on the document.
        return this.layer.emit(this.buildEvent(`up:layer:${verbPast}`), {
            // Set up.layer.current to the parent of the closed layer, which is now likely
            // to be the front layer.
            baseLayer: formerParent,
            callback: this.layer.callback(`on${verbPastUpperCaseFirst}`),
            ensureBubbles: true,
            log: [`${verbPastUpperCaseFirst} ${this.layer} with value %o`, this.value]
        });
    }
    buildEvent(name) {
        return up.event.build(name, {
            layer: this.layer,
            value: this.value,
            origin: this.origin
        });
    }
    handleFocus(formerParent) {
        // A11Y: Stop trapping focus in the layer that's about to close
        this.layer.overlayFocus.teardown();
        // A11Y: Start trapping focus in the parent layer that is being promoted to front.
        formerParent.overlayFocus?.moveToFront();
        // A11Y: Focus the element that originally opened this layer.
        let newFocusElement = this.layer.origin || formerParent.element;
        newFocusElement.focus({ preventScroll: true });
    }
};


/***/ }),
/* 21 */
/***/ (() => {

const u = up.util;
up.Change.FromContent = class FromContent extends up.Change {
    constructor(options) {
        super(options);
        // If we're rendering a fragment from a { url }, options.layer will already
        // be an array of up.Layer objects, set by up.Change.FromURL. It looks up the
        // layer eagerly because in case of { layer: 'origin' } (default for navigation)
        // the { origin } element may get removed while the request was in flight.
        // From that given array we need to remove layers that have been closed while
        // the request was in flight.
        //
        // If we're rendering a framgent from local content ({ document, fragment, content }),
        // options.layer will be a layer name like "current" and needs to be looked up.
        this.layers = u.filter(up.layer.getAll(this.options), this.isRenderableLayer);
        // Only extract options required for step building, since #execute() will be called with an
        // postflightOptions argument once the response is received and has provided refined
        // options.
        this.origin = this.options.origin;
        this.preview = this.options.preview;
        this.mode = this.options.mode;
        // When we're swapping elements in origin's layer, we can be choose a fallback
        // replacement zone close to the origin instead of looking up a selector in the
        // entire layer (where it might match unrelated elements).
        if (this.origin) {
            this.originLayer = up.layer.get(this.origin);
        }
    }
    isRenderableLayer(layer) {
        return (layer === 'new') || layer.isOpen();
    }
    getPlans() {
        if (!this.plans) {
            this.plans = [];
            if (this.options.fragment) {
                // ResponseDoc allows to pass innerHTML as { fragment }, but then it also
                // requires a { target }. We use a target that matches the parsed { fragment }.
                this.options.target = this.getResponseDoc().rootSelector();
            }
            // First seek { target } in all layers, then seek { fallback } in all layers.
            this.expandIntoPlans(this.layers, this.options.target);
            this.expandIntoPlans(this.layers, this.options.fallback);
        }
        return this.plans;
    }
    expandIntoPlans(layers, targets) {
        for (let layer of layers) {
            // An abstract selector like :main may expand into multiple
            // concrete selectors, like ['main', '.content'].
            for (let target of this.expandTargets(targets, layer)) {
                // Any plans we add will inherit all properties from @options
                const props = { ...this.options, target, layer, placement: this.defaultPlacement() };
                const change = layer === 'new' ? new up.Change.OpenLayer(props) : new up.Change.UpdateLayer(props);
                this.plans.push(change);
            }
        }
    }
    expandTargets(targets, layer) {
        return up.fragment.expandTargets(targets, { layer, mode: this.mode, origin: this.origin });
    }
    execute() {
        // Preloading from local content is a no-op.
        if (this.options.preload) {
            return Promise.resolve();
        }
        return this.seekPlan(this.executePlan.bind(this)) || this.postflightTargetNotApplicable();
    }
    executePlan(matchedPlan) {
        return matchedPlan.execute(this.getResponseDoc(), this.onPlanApplicable.bind(this, matchedPlan));
    }
    onPlanApplicable(plan) {
        let primaryPlan = this.getPlans()[0];
        if (plan !== primaryPlan) {
            up.puts('up.render()', 'Could not match primary target (%s). Updating a fallback target (%s).', primaryPlan.target, plan.target);
        }
    }
    getResponseDoc() {
        if (!this.preview && !this.responseDoc) {
            const docOptions = u.pick(this.options, ['target', 'content', 'fragment', 'document', 'html', 'cspNonces']);
            up.migrate.handleResponseDocOptions?.(docOptions);
            // If neither { document } nor { fragment } source is given, we assume { content }.
            if (this.defaultPlacement() === 'content') {
                // When processing { content }, ResponseDoc needs a { target }
                // to create a matching element.
                docOptions.target = this.firstExpandedTarget(docOptions.target);
            }
            this.responseDoc = new up.ResponseDoc(docOptions);
        }
        return this.responseDoc;
    }
    defaultPlacement() {
        if (!this.options.document && !this.options.fragment) {
            return 'content';
        }
    }
    // When the user provided a { content } we need an actual CSS selector for
    // which up.ResponseDoc can create a matching element.
    firstExpandedTarget(target) {
        return this.expandTargets(target || ':main', this.layers[0])[0];
    }
    // Returns information about the change that is most likely before the request was dispatched.
    // This might change postflight if the response does not contain the desired target.
    preflightProps(opts = {}) {
        const getPlanProps = plan => plan.preflightProps();
        return this.seekPlan(getPlanProps) || opts.optional || this.preflightTargetNotApplicable();
    }
    preflightTargetNotApplicable() {
        this.targetNotApplicable('Could not find target in current page');
    }
    postflightTargetNotApplicable() {
        this.targetNotApplicable('Could not find common target in current page and response');
    }
    targetNotApplicable(reason) {
        if (this.getPlans().length) {
            const planTargets = u.uniq(u.map(this.getPlans(), 'target'));
            const humanizedLayerOption = up.layer.optionToString(this.options.layer);
            up.fail(reason + " (tried selectors %o in %s)", planTargets, humanizedLayerOption);
        }
        else if (this.layers.length) {
            up.fail('No target selector given');
        }
        else {
            up.fail('Layer %o does not exist', this.options.layer);
        }
    }
    seekPlan(fn) {
        for (let plan of this.getPlans()) {
            try {
                // A return statement stops iteration of a vanilla for loop,
                // but would not stop an u.each() or Array#forEach().
                return fn(plan);
            }
            catch (error) {
                // Re-throw any unexpected type of error
                if (!up.error.notApplicable.is(error)) {
                    throw error;
                }
            }
        }
    }
};


/***/ }),
/* 22 */
/***/ (() => {

const u = up.util;
up.Change.FromURL = class FromURL extends up.Change {
    constructor(options) {
        super(options);
        // Look up layers *before* we make the request.
        // In case of { layer: 'origin' } (default for navigation) the { origin }
        // element may get removed while the request was in flight, making
        // up.Change.FromContent#execute() fail with "layer { origin } does not exist".
        this.options.layer = up.layer.getAll(this.options);
        // Since up.layer.getAll() already normalizes layer options,
        // we don't need to normalize again in up.Change.FromContent.
        this.options.normalizeLayerOptions = false;
        // We keep all failKeys in our successOptions, nothing will use them.
        this.successOptions = this.options;
        // deriveFailOptions() will merge shared keys and (unsuffixed) failKeys.
        this.failOptions = up.RenderOptions.deriveFailOptions(this.successOptions);
    }
    execute() {
        let newPageReason = this.newPageReason();
        if (newPageReason) {
            up.puts('up.render()', newPageReason);
            up.network.loadPage(this.options);
            // Prevent our caller from executing any further code, since we're already
            // navigating away from this JavaScript environment.
            return u.unresolvablePromise();
        }
        const promise = this.makeRequest();
        if (this.options.preload) {
            return promise;
        }
        // Use always() since onRequestSettled() will decide whether the promise
        // will be fulfilled or rejected.
        return u.always(promise, responseOrError => this.onRequestSettled(responseOrError));
    }
    newPageReason() {
        // Rendering content from cross-origin URLs is out of scope for Unpoly.
        // We still allow users to call up.render() with a cross-origin URL, but
        // we will then make a full-page request.
        if (u.isCrossOrigin(this.options.url)) {
            return 'Loading cross-origin content in new page';
        }
        // Unpoly may have been booted without suppport for history.pushState.
        // E.g. when the initial page was loaded from a POST response.
        // In this case we make a full page load in hopes to reboot with
        // pushState support.
        if (!up.browser.canPushState()) {
            return 'Loading content in new page to restore history support';
        }
    }
    makeRequest() {
        const successAttrs = this.preflightPropsForRenderOptions(this.successOptions);
        const failAttrs = this.preflightPropsForRenderOptions(this.failOptions, { optional: true });
        const requestAttrs = u.merge(this.successOptions, // contains preflight keys relevant for the request, e.g. { url, method, solo }
        successAttrs, // contains meta information for an successful update, e.g. { layer, mode, context, target }
        u.renameKeys(failAttrs, up.fragment.failKey) // contains meta information for a failed update, e.g. { failTarget }
        );
        this.request = up.request(requestAttrs);
        // The request is also a promise for its response.
        return this.request;
    }
    preflightPropsForRenderOptions(renderOptions, requestAttributesOptions) {
        const preview = new up.Change.FromContent({ ...renderOptions, preview: true });
        // #preflightProps() will return meta information about the change that is most
        // likely before the request was dispatched.
        // This might change postflight if the response does not contain the desired target.
        return preview.preflightProps(requestAttributesOptions);
    }
    onRequestSettled(response) {
        this.response = response;
        if (!(response instanceof up.Response)) {
            // value is up.error.aborted() or another fatal error that can never
            // be used as a fragment update. At this point up:request:aborted or up:request:fatal
            // have already been emitted by up.Request.
            throw response;
        }
        else if (this.isSuccessfulResponse()) {
            return this.updateContentFromResponse(['Loaded fragment from successful response to %s', this.request.description], this.successOptions);
        }
        else {
            const log = ['Loaded fragment from failed response to %s (HTTP %d)', this.request.description, this.response.status];
            // Although updateContentFromResponse() will fulfill with a successful replacement of options.failTarget,
            // we still want to reject the promise that's returned to our API client. Hence we throw.
            throw this.updateContentFromResponse(log, this.failOptions);
        }
    }
    isSuccessfulResponse() {
        return (this.successOptions.fail === false) || this.response.ok;
    }
    // buildEvent(type, props) {
    //   const defaultProps = { request: this.request, response: this.response, renderOptions: this.options }
    //   return up.event.build(type, u.merge(defaultProps, props))
    // }
    updateContentFromResponse(log, renderOptions) {
        // Allow listeners to inspect the response and either prevent the fragment change
        // or manipulate change options. An example for when this is useful is a maintenance
        // page with its own layout, that cannot be loaded as a fragment and must be loaded
        // with a full page load.
        this.request.assertEmitted('up:fragment:loaded', {
            callback: this.options.onLoaded,
            response: this.response,
            log,
            renderOptions,
        });
        // The response might carry some updates for our change options,
        // like a server-set location, or server-sent events.
        this.augmentOptionsFromResponse(renderOptions);
        return new up.Change.FromContent(renderOptions).execute();
    }
    augmentOptionsFromResponse(renderOptions) {
        const responseURL = this.response.url;
        let serverLocation = responseURL;
        let hash = this.request.hash;
        if (hash) {
            renderOptions.hash = hash;
            serverLocation += hash;
        }
        const isReloadable = (this.response.method === 'GET');
        if (isReloadable) {
            // Remember where we got the fragment from so we can up.reload() it later.
            renderOptions.source = this.improveHistoryValue(renderOptions.source, responseURL);
        }
        else {
            // Keep the source of the previous fragment (e.g. the form that was submitted into failure).
            renderOptions.source = this.improveHistoryValue(renderOptions.source, 'keep');
            // Since the current URL is not retrievable over the GET-only address bar,
            // we can only provide history if a location URL is passed as an option.
            renderOptions.history = !!renderOptions.location;
        }
        renderOptions.location = this.improveHistoryValue(renderOptions.location, serverLocation);
        renderOptions.title = this.improveHistoryValue(renderOptions.title, this.response.title);
        renderOptions.eventPlans = this.response.eventPlans;
        let serverTarget = this.response.target;
        if (serverTarget) {
            renderOptions.target = serverTarget;
        }
        renderOptions.document = this.response.text;
        renderOptions.acceptLayer = this.response.acceptLayer;
        renderOptions.dismissLayer = this.response.dismissLayer;
        // Don't require a target match if the server wants to close the overlay and doesn't send content.
        // However the server is still free to send matching HTML. It would be used if the root layer is updated.
        if (!renderOptions.document && (u.isDefined(renderOptions.acceptLayer) || u.isDefined(renderOptions.dismissLayer))) {
            renderOptions.target = ':none';
        }
        // If the server has provided an update to our context via the X-Up-Context
        // response header, merge it into our existing { context } option.
        renderOptions.context = u.merge(renderOptions.context, this.response.context);
        renderOptions.cspNonces = this.response.cspNonces;
    }
};


/***/ }),
/* 23 */
/***/ (() => {

const u = up.util;
const e = up.element;
up.CompilerPass = class CompilerPass {
    constructor(root, compilers, options = {}) {
        this.root = root;
        this.compilers = compilers;
        // Exclude all elements that are descendants of the subtrees we want to keep.
        // The exclusion process is very expensive (in one case compiling 100 slements
        // took 1.5s because of this). That's why we only do it if (1) options.skipSubtrees
        // was given and (2) there is an [up-keep] element in root.
        this.skipSubtrees = options.skip;
        if (!this.skipSubtrees.length || !this.root.querySelector('[up-keep]')) {
            this.skipSubtrees = undefined;
        }
        // (1) If a caller has already looked up the layer we don't want to look it up again.
        // (2) Ddefault to the current layer in case the user manually compiles a detached element.
        this.layer = options.layer || up.layer.get(this.root) || up.layer.current;
        this.errors = [];
    }
    run() {
        up.puts('up.hello()', "Compiling fragment %o", this.root);
        // If we're compiling a fragment in a background layer, we want
        // up.layer.current to resolve to that background layer, not the front layer.
        this.layer.asCurrent(() => {
            for (let compiler of this.compilers) {
                this.runCompiler(compiler);
            }
        });
        if (this.errors.length) {
            throw up.error.failed('Errors while compiling', { errors: this.errors });
        }
    }
    runCompiler(compiler) {
        const matches = this.select(compiler.selector);
        if (!matches.length) {
            return;
        }
        if (!compiler.isDefault) {
            up.puts('up.hello()', 'Compiling "%s" on %d element(s)', compiler.selector, matches.length);
        }
        if (compiler.batch) {
            this.compileBatch(compiler, matches);
        }
        else {
            for (let match of matches) {
                this.compileOneElement(compiler, match);
            }
        }
        return up.migrate.postCompile?.(matches, compiler);
    }
    compileOneElement(compiler, element) {
        const elementArg = compiler.jQuery ? up.browser.jQuery(element) : element;
        const compileArgs = [elementArg];
        // Do not retrieve and parse [up-data] unless the compiler function
        // expects a second argument. Note that we must pass data for an argument
        // count of 0, since then the function might take varargs.
        if (compiler.length !== 1) {
            const data = up.syntax.data(element);
            compileArgs.push(data);
        }
        const result = this.applyCompilerFunction(compiler, element, compileArgs);
        let destructorOrDestructors = this.destructorPresence(result);
        if (destructorOrDestructors) {
            up.destructor(element, destructorOrDestructors);
        }
    }
    compileBatch(compiler, elements) {
        const elementsArgs = compiler.jQuery ? up.browser.jQuery(elements) : elements;
        const compileArgs = [elementsArgs];
        // Do not retrieve and parse [up-data] unless the compiler function
        // expects a second argument. Note that we must pass data for an argument
        // count of 0, since then the function might take varargs.
        if (compiler.length !== 1) {
            const dataList = u.map(elements, up.syntax.data);
            compileArgs.push(dataList);
        }
        const result = this.applyCompilerFunction(compiler, elements, compileArgs);
        if (this.destructorPresence(result)) {
            up.fail('Compilers with { batch: true } cannot return destructors');
        }
    }
    applyCompilerFunction(compiler, elementOrElements, compileArgs) {
        try {
            return compiler.apply(elementOrElements, compileArgs);
        }
        catch (error) {
            this.errors.push(error);
            up.log.error('up.hello()', 'While compiling %o: %o', elementOrElements, error);
            up.error.emitGlobal(error);
        }
    }
    destructorPresence(result) {
        // Check if the result value looks like a destructor to filter out
        // unwanted implicit returns in CoffeeScript.
        if (u.isFunction(result) || (u.isArray(result) && (u.every(result, u.isFunction)))) {
            return result;
        }
    }
    select(selector) {
        let matches = e.subtree(this.root, u.evalOption(selector));
        if (this.skipSubtrees) {
            matches = u.reject(matches, (match) => this.isInSkippedSubtree(match));
        }
        return matches;
    }
    isInSkippedSubtree(element) {
        let parent;
        if (u.contains(this.skipSubtrees, element)) {
            return true;
        }
        else if ((parent = element.parentElement)) {
            return this.isInSkippedSubtree(parent);
        }
        else {
            return false;
        }
    }
};


/***/ }),
/* 24 */
/***/ (() => {

const u = up.util;
const e = up.element;
up.CSSTransition = class CSSTransition {
    constructor(element, lastFrameKebab, options) {
        this.element = element;
        this.lastFrameKebab = lastFrameKebab;
        this.lastFrameKeysKebab = Object.keys(this.lastFrameKebab);
        if (u.some(this.lastFrameKeysKebab, key => key.match(/A-Z/))) {
            up.fail('Animation keys must be kebab-case');
        }
        this.finishEvent = options.finishEvent;
        this.duration = options.duration;
        this.easing = options.easing;
        this.finished = false;
    }
    start() {
        if (this.lastFrameKeysKebab.length === 0) {
            this.finished = true;
            // If we have nothing to animate, we will never get a transitionEnd event
            // and the returned promise will never resolve.
            return Promise.resolve();
        }
        this.deferred = u.newDeferred();
        this.pauseOldTransition();
        this.startTime = new Date();
        this.startFallbackTimer();
        this.listenToFinishEvent();
        this.listenToTransitionEnd();
        this.startMotion();
        return this.deferred.promise();
    }
    listenToFinishEvent() {
        if (this.finishEvent) {
            this.stopListenToFinishEvent = up.on(this.element, this.finishEvent, this.onFinishEvent.bind(this));
        }
    }
    onFinishEvent(event) {
        // don't waste time letting the event bubble up the DOM
        event.stopPropagation();
        this.finish();
    }
    startFallbackTimer() {
        const timingTolerance = 100;
        this.fallbackTimer = u.timer((this.duration + timingTolerance), () => {
            this.finish();
        });
    }
    stopFallbackTimer() {
        clearTimeout(this.fallbackTimer);
    }
    listenToTransitionEnd() {
        this.stopListenToTransitionEnd = up.on(this.element, 'transitionend', this.onTransitionEnd.bind(this));
    }
    onTransitionEnd(event) {
        // Check if the transitionend event was caused by our own transition,
        // and not by some other transition that happens to affect this element.
        if (event.target !== this.element) {
            return;
        }
        // Check if we are receiving a late transitionEnd event
        // from a previous CSS transition.
        const elapsed = new Date() - this.startTime;
        if (elapsed <= (0.25 * this.duration)) {
            return;
        }
        const completedPropertyKebab = event.propertyName;
        if (!u.contains(this.lastFrameKeysKebab, completedPropertyKebab)) {
            return;
        }
        this.finish();
    }
    finish() {
        // Make sure that any queued events won't finish multiple times.
        if (this.finished) {
            return;
        }
        this.finished = true;
        this.stopFallbackTimer();
        this.stopListenToFinishEvent?.();
        this.stopListenToTransitionEnd?.();
        // Cleanly finish our own transition so the old transition
        // (or any other transition set right after that) will be able to take effect.
        e.concludeCSSTransition(this.element);
        this.resumeOldTransition();
        this.deferred.resolve();
    }
    pauseOldTransition() {
        const oldTransition = e.style(this.element, [
            'transitionProperty',
            'transitionDuration',
            'transitionDelay',
            'transitionTimingFunction'
        ]);
        if (e.hasCSSTransition(oldTransition)) {
            // Freeze the previous transition at its current place, by setting the currently computed,
            // animated CSS properties as inline styles. Transitions on all properties will not be frozen,
            // since that would involve setting every single CSS property as an inline style.
            if (oldTransition.transitionProperty !== 'all') {
                const oldTransitionProperties = oldTransition.transitionProperty.split(/\s*,\s*/);
                const oldTransitionFrameKebab = e.style(this.element, oldTransitionProperties);
                this.setOldTransitionTargetFrame = e.setTemporaryStyle(this.element, oldTransitionFrameKebab);
            }
            // Stop the existing CSS transition so it does not emit transitionEnd events
            this.setOldTransition = e.concludeCSSTransition(this.element);
        }
    }
    resumeOldTransition() {
        this.setOldTransitionTargetFrame?.();
        this.setOldTransition?.();
    }
    startMotion() {
        e.setStyle(this.element, {
            transitionProperty: Object.keys(this.lastFrameKebab).join(', '),
            transitionDuration: `${this.duration}ms`,
            transitionTimingFunction: this.easing
        });
        e.setStyle(this.element, this.lastFrameKebab);
    }
};


/***/ }),
/* 25 */
/***/ (() => {

const u = up.util;
up.DestructorPass = class DestructorPass {
    constructor(fragment, options) {
        this.fragment = fragment;
        this.options = options;
        this.errors = [];
    }
    run() {
        for (let cleanable of this.selectCleanables()) {
            let destructors = u.pluckKey(cleanable, 'upDestructors');
            if (destructors) {
                for (let destructor of destructors) {
                    this.applyDestructorFunction(destructor, cleanable);
                }
            }
            cleanable.classList.remove('up-can-clean');
        }
        if (this.errors.length) {
            throw up.error.failed('Errors while destroying', { errors: this.errors });
        }
    }
    selectCleanables() {
        // fragment functions usually ignore elements that are being destroyed
        const selectOptions = { ...this.options, destroying: true };
        return up.fragment.subtree(this.fragment, '.up-can-clean', selectOptions);
    }
    applyDestructorFunction(destructor, element) {
        try {
            destructor();
        }
        catch (error) {
            this.errors.push(error);
            up.log.error('up.destroy()', 'While destroying %o: %o', element, error);
            up.error.emitGlobal(error);
        }
    }
};


/***/ }),
/* 26 */
/***/ (() => {

const u = up.util;
const e = up.element;
up.EventEmitter = class EventEmitter extends up.Record {
    keys() {
        return [
            'target',
            'event',
            'baseLayer',
            'callback',
            'log',
            'ensureBubbles'
        ];
    }
    emit() {
        this.logEmission();
        if (this.baseLayer) {
            this.baseLayer.asCurrent(() => this.dispatchEvent());
        }
        else {
            this.dispatchEvent();
        }
        return this.event;
    }
    dispatchEvent() {
        this.target.dispatchEvent(this.event);
        if (this.ensureBubbles && e.isDetached(this.target)) {
            document.dispatchEvent(this.event);
        }
        this.callback?.(this.event);
    }
    assertEmitted() {
        const event = this.emit();
        if (event.defaultPrevented) {
            throw up.error.aborted(`Event ${event.type} was prevented`);
        }
    }
    logEmission() {
        if (!up.log.isEnabled()) {
            return;
        }
        let message = this.log;
        let messageArgs;
        if (u.isArray(message)) {
            [message, ...messageArgs] = message;
        }
        else {
            messageArgs = [];
        }
        const { type } = this.event;
        if (u.isString(message)) {
            up.puts(type, message, ...messageArgs);
        }
        else if (message !== false) {
            up.puts(type, `Event ${type}`);
        }
    }
    static fromEmitArgs(args, defaults = {}) {
        // Event-emitting functions are crazy overloaded:
        //
        // - up.emit([target], eventType, [eventProps])
        // - up.emit([target], eventPlan) # eventPlan must contain { type } property
        // - up.emit([target], event, [emitDetails]) # emitDetails may contain options like { layer } or { callback }
        //
        // Hence the insane argument parsing logic seen below.
        //
        // We begin by removing an options hash from the end of the argument list.
        // This might be an object of event properties, which might or might contain a
        // { type } property for the event type. In case we are passed a pre-built
        // Event object, the hash will contain emission that options that cannot be
        // carried by the event object, such as { layer } or { callback }.
        let options = u.extractOptions(args);
        // Event-emitting functions may instantiate their up.EventEmitter with preconfigured
        // defaults. E.g. up.Layer#emit() will set the default { layer: this }.
        options = u.merge(defaults, options);
        // If we are passed an element or layer as a first argument, this is the event
        // target. We remove it from the argument list and store it in options.
        if (u.isElementish(args[0])) {
            options.target = e.get(args.shift());
        }
        else if (args[0] instanceof up.Layer) {
            options.layer = args.shift();
        }
        // Setting a { layer } is a shorthand to (1) emit the event on the layer's
        // element and (2) to set up.layer.current to that layer during emission.
        let layer;
        if (options.layer) {
            layer = up.layer.get(options.layer);
            if (options.target == null) {
                options.target = layer.element;
            }
            if (options.baseLayer == null) {
                options.baseLayer = layer;
            }
        }
        // Setting { baseLayer } will fix up.layer.current to that layer during emission.
        // In case we get a layer name like 'root' (instead of an up.Layer object) we look
        // up the actual up.Layer object.
        if (options.baseLayer) {
            options.baseLayer = up.layer.get(options.baseLayer);
        }
        if (u.isString(options.target)) {
            options.target = up.fragment.get(options.target, { layer: options.layer });
        }
        else if (!options.target) {
            // If no element is given, we emit the event on the document.
            options.target = document;
        }
        if (args[0]?.preventDefault) {
            // In this branch we receive an Event object that was already built:
            // up.emit([target], event, [emitOptions])
            options.event = args[0];
            if (options.log == null) {
                options.log = args[0].log;
            }
        }
        else if (u.isString(args[0])) {
            // In this branch we receive an Event type and props object.
            // The props object may also include options for the emission, such as
            // { layer }, { target }, { baseLayer } or { log }.
            // up.emit([target], eventType, [eventPropsAndEmitOptions])
            options.event = up.event.build(args[0], options);
        }
        else {
            // In this branch we receive an object that contains the event type as a { type } property:
            // up.emit([target, { type: 'foo', prop: 'value' }
            options.event = up.event.build(options);
        }
        return new (this)(options);
    }
};


/***/ }),
/* 27 */
/***/ (() => {

const u = up.util;
const e = up.element;
up.EventListener = class EventListener extends up.Record {
    keys() {
        return [
            'element',
            'eventType',
            'selector',
            'callback',
            'jQuery',
            'guard',
            'baseLayer',
            'passive',
            'once',
            'beforeBoot',
        ];
    }
    constructor(attributes) {
        super(attributes);
        this.key = this.constructor.buildKey(attributes);
        this.isDefault = up.framework.evaling;
        // We don't usually run up.on() listeners before Unpoly has booted.
        // This is done so incompatible code is not called on browsers that don't support Unpoly.
        // Listeners that do need to run before Unpoly boots can pass { beforeBoot: true } to override.
        // We also default to { beforeBoot: true } for framework events that are emitted
        // before booting.
        this.beforeBoot ?? (this.beforeBoot = this.eventType.indexOf('up:framework:') === 0);
        // Need to store the bound nativeCallback function because addEventListener()
        // and removeEventListener() need to see the exact same reference.
        this.nativeCallback = this.nativeCallback.bind(this);
    }
    bind() {
        var _a;
        const map = ((_a = this.element).upEventListeners || (_a.upEventListeners = {}));
        if (map[this.key]) {
            up.fail('up.on(): The %o callback %o cannot be registered more than once', this.eventType, this.callback);
        }
        map[this.key] = this;
        this.element.addEventListener(...this.addListenerArgs());
    }
    addListenerArgs() {
        const args = [this.eventType, this.nativeCallback];
        if (this.passive && up.browser.canPassiveEventListener()) {
            args.push({ passive: true });
        }
        return args;
    }
    unbind() {
        let map = this.element.upEventListeners;
        if (map) {
            delete map[this.key];
        }
        this.element.removeEventListener(...this.addListenerArgs());
    }
    nativeCallback(event) {
        if (up.framework.beforeBoot && !this.beforeBoot) {
            return;
        }
        // Once we drop IE11 support we can forward the { once } option
        // to Element#addEventListener().
        if (this.once) {
            this.unbind();
        }
        // 1. Since we're listing on `document`, event.currentTarget is now `document`.
        // 2. event.target is the element that received an event, which might be a
        //    child of `selector`.
        // 3. There is only a single event bubbling up the DOM, so we are only called once.
        let element = event.target;
        if (this.selector) {
            element = e.closest(element, u.evalOption(this.selector));
        }
        if (this.guard && !this.guard(event)) {
            return;
        }
        if (element) {
            const elementArg = this.jQuery ? up.browser.jQuery(element) : element;
            const args = [event, elementArg];
            // Do not retrieve and parse [up-data] unless the listener function
            // expects a third argument. Note that we must pass data for an argument
            // count of 0, since then the function might take varargs.
            const expectedArgCount = this.callback.length;
            if (expectedArgCount !== 1 && expectedArgCount !== 2) {
                const data = up.syntax.data(element);
                args.push(data);
            }
            const applyCallback = this.callback.bind(element, ...args);
            if (this.baseLayer) {
                // Unpoly will usually set up.layer.current when emitting an event.
                // But Unpoly-unaware code will not set up.layer.current when emitting events.
                // Hence layerInstance.on('click') will use this to set layer.current to layerInstance.
                this.baseLayer.asCurrent(applyCallback);
            }
            else {
                applyCallback();
            }
        }
    }
    static fromElement(attributes) {
        let map = attributes.element.upEventListeners;
        if (map) {
            const key = this.buildKey(attributes);
            return map[key];
        }
    }
    static buildKey(attributes) {
        var _a;
        // Give the callback function a numeric identifier so it
        // can become part of the upEventListeners key.
        (_a = attributes.callback).upUid || (_a.upUid = u.uid());
        return [
            attributes.eventType,
            attributes.selector,
            attributes.callback.upUid
        ].join('|');
    }
    static allNonDefault(element) {
        let map = element.upEventListeners;
        if (map) {
            const listeners = u.values(map);
            return u.reject(listeners, 'isDefault');
        }
        else {
            return [];
        }
    }
};


/***/ }),
/* 28 */
/***/ (() => {

const u = up.util;
up.EventListenerGroup = class EventListenerGroup extends up.Record {
    keys() {
        return [
            'elements',
            'eventTypes',
            'selector',
            'callback',
            'jQuery',
            'guard',
            'baseLayer',
            'passive',
            'once',
            'beforeBoot',
        ];
    }
    bind() {
        const unbindFns = [];
        this.eachListenerAttributes(function (attrs) {
            const listener = new up.EventListener(attrs);
            listener.bind();
            return unbindFns.push(listener.unbind.bind(listener));
        });
        return u.sequence(unbindFns);
    }
    eachListenerAttributes(fn) {
        for (let element of this.elements) {
            for (let eventType of this.eventTypes) {
                fn(this.listenerAttributes(element, eventType));
            }
        }
    }
    listenerAttributes(element, eventType) {
        return { ...this.attributes(), element, eventType };
    }
    unbind() {
        this.eachListenerAttributes(function (attrs) {
            let listener = up.EventListener.fromElement(attrs);
            if (listener) {
                listener.unbind();
            }
        });
    }
    /*
    Constructs a new up.EventListenerGroup from arguments with many different combinations:
  
        [[elements], eventTypes, [selector], [options], callback]
  
    @function up.EventListenerGroup.fromBindArgs
    @internal
    */
    static fromBindArgs(args, defaults) {
        args = u.copy(args);
        // A callback function is given in all arg variants.
        const callback = args.pop();
        // The user can pass an element (or the document, or the window) as the
        // first argument. If omitted, the listener will bind to the document.
        let elements;
        if (args[0].addEventListener) {
            elements = [args.shift()];
        }
        else if (u.isJQuery(args[0]) || (u.isList(args[0]) && args[0][0].addEventListener)) {
            elements = args.shift();
        }
        else {
            elements = [document];
        }
        // Event names are given in all arg variants
        let eventTypes = u.splitValues(args.shift());
        let fixTypes = up.migrate.fixEventTypes;
        if (fixTypes) {
            eventTypes = fixTypes(eventTypes);
        }
        const options = u.extractOptions(args);
        // A selector is given if the user wants to delegate events.
        // It might be undefined.
        const selector = args[0];
        const attributes = { elements, eventTypes, selector, callback, ...options, ...defaults };
        return new (this)(attributes);
    }
};


/***/ }),
/* 29 */
/***/ (() => {

const u = up.util;
const e = up.element;
up.FieldObserver = class FieldObserver {
    constructor(fieldOrFields, options, callback) {
        this.scheduleValues = this.scheduleValues.bind(this);
        this.isNewValues = this.isNewValues.bind(this);
        this.callback = callback;
        this.fields = e.list(fieldOrFields);
        this.delay = options.delay;
        this.batch = options.batch;
    }
    start() {
        this.scheduledValues = null;
        this.processedValues = this.readFieldValues();
        this.currentTimer = undefined;
        this.callbackRunning = false;
        // Although (depending on the browser) we only need/receive either input or change,
        // we always bind to both events in case another script manually triggers it.
        this.unbind = up.on(this.fields, 'input change', () => this.check());
    }
    stop() {
        this.unbind();
        this.cancelTimer();
    }
    cancelTimer() {
        clearTimeout(this.currentTimer);
        this.currentTimer = undefined;
    }
    scheduleTimer() {
        this.cancelTimer();
        this.currentTimer = u.timer(this.delay, () => {
            this.currentTimer = undefined;
            this.requestCallback();
        });
    }
    scheduleValues(values) {
        this.scheduledValues = values;
        this.scheduleTimer();
    }
    isNewValues(values) {
        return !u.isEqual(values, this.processedValues) && !u.isEqual(this.scheduledValues, values);
    }
    async requestCallback() {
        if ((this.scheduledValues !== null) && !this.currentTimer && !this.callbackRunning) {
            const diff = this.changedValues(this.processedValues, this.scheduledValues);
            this.processedValues = this.scheduledValues;
            this.scheduledValues = null;
            this.callbackRunning = true;
            const callbackReturnValues = [];
            if (this.batch) {
                callbackReturnValues.push(this.callback(diff));
            }
            else {
                for (let name in diff) {
                    const value = diff[name];
                    callbackReturnValues.push(this.callback(value, name));
                }
            }
            await u.allSettled(callbackReturnValues);
            this.callbackRunning = false;
            this.requestCallback();
        }
    }
    changedValues(previous, next) {
        const changes = {};
        let keys = Object.keys(previous);
        keys = keys.concat(Object.keys(next));
        keys = u.uniq(keys);
        for (let key of keys) {
            const previousValue = previous[key];
            const nextValue = next[key];
            if (!u.isEqual(previousValue, nextValue)) {
                changes[key] = nextValue;
            }
        }
        return changes;
    }
    readFieldValues() {
        return up.Params.fromFields(this.fields).toObject();
    }
    check() {
        const values = this.readFieldValues();
        if (this.isNewValues(values)) {
            this.scheduleValues(values);
        }
    }
};


/***/ }),
/* 30 */
/***/ (() => {

const e = up.element;
const PRESERVE_KEYS = ['selectionStart', 'selectionEnd', 'scrollLeft', 'scrollTop'];
function transferProps(from, to) {
    for (let key of PRESERVE_KEYS) {
        try {
            to[key] = from[key];
        }
        catch (error) {
            // Safari throws a TypeError when accessing { selectionStart }
            // from a focused <input type="submit">. We ignore it.
        }
    }
}
function focusedElementWithin(scopeElement) {
    const focusedElement = document.activeElement;
    if (e.isInSubtree(scopeElement, focusedElement)) {
        return focusedElement;
    }
}
up.FocusCapsule = class FocusCapsule extends up.Record {
    keys() {
        return ['selector', 'oldElement'].concat(PRESERVE_KEYS);
    }
    restore(scope, options) {
        if (!this.wasLost()) {
            // If the old element was never detached (e.g. because it was kept),
            // and still has focus, we don't need to do anything.
            return;
        }
        let rediscoveredElement = e.get(scope, this.selector);
        if (rediscoveredElement) {
            // Firefox needs focus-related props to be set *before* we focus the element
            transferProps(this, rediscoveredElement);
            up.focus(rediscoveredElement, options);
            // Signals callers that we could restore
            return true;
        }
    }
    static preserveWithin(oldElement) {
        let focusedElement = focusedElementWithin(oldElement);
        if (focusedElement) {
            const plan = { oldElement, selector: up.fragment.toTarget(focusedElement) };
            transferProps(focusedElement, plan);
            return new (this)(plan);
        }
    }
    wasLost() {
        return !focusedElementWithin(this.oldElement);
    }
};


/***/ }),
/* 31 */
/***/ (() => {

const u = up.util;
up.FragmentProcessor = class FragmentProcessor extends up.Record {
    keys() {
        return [
            'fragment',
            'autoMeans',
            'origin',
            'layer'
        ];
    }
    process(opt) {
        // Expose this additional method so subclasses can implement default values.
        return this.tryProcess(opt);
    }
    tryProcess(opt) {
        if (u.isArray(opt)) {
            return u.find(opt, opt => this.tryProcess(opt));
        }
        if (u.isFunction(opt)) {
            return this.tryProcess(opt(this.fragment, this.attributes()));
        }
        if (u.isElement(opt)) {
            return this.processElement();
        }
        if (u.isString(opt)) {
            if (opt === 'auto') {
                return this.tryProcess(this.autoMeans);
            }
            let match = opt.match(/^(.+?)-if-(.+?)$/);
            if (match) {
                return this.resolveCondition(match[2]) && this.process(match[1]);
            }
        }
        return this.processPrimitive(opt);
    }
    resolveCondition(condition) {
        if (condition === 'main') {
            return up.fragment.contains(this.fragment, ':main');
        }
    }
    findSelector(selector) {
        const lookupOpts = { layer: this.layer, origin: this.origin };
        // Prefer selecting a descendant of @fragment, but if not possible search through @fragment's entire layer
        let match = up.fragment.get(this.fragment, selector, lookupOpts) || up.fragment.get(selector, lookupOpts);
        if (match) {
            return match;
        }
        else {
            up.warn('up.render()', 'Could not find an element matching "%s"', selector);
            // Return undefined so { focus: 'auto' } will try the next option from { autoMeans }
        }
    }
};


/***/ }),
/* 32 */
/***/ (() => {

const DESCENDANT_SELECTOR = /^([^ >+(]+) (.+)$/;
up.FragmentFinder = class FragmentFinder {
    constructor(options) {
        this.options = options;
        this.origin = this.options.origin;
        this.selector = this.options.selector;
        this.layer = this.options.layer;
    }
    find() {
        return this.findAroundOrigin() || this.findInLayer();
    }
    findAroundOrigin() {
        if (this.origin && up.fragment.config.matchAroundOrigin && !up.element.isDetached(this.origin)) {
            return this.findClosest() || this.findInVicinity();
        }
    }
    findClosest() {
        return up.fragment.closest(this.origin, this.selector, this.options);
    }
    findInVicinity() {
        let parts = this.selector.match(DESCENDANT_SELECTOR);
        if (parts) {
            let parent = up.fragment.closest(this.origin, parts[1], this.options);
            if (parent) {
                return up.fragment.getDumb(parent, parts[2]);
            }
        }
    }
    findInLayer() {
        return up.fragment.getDumb(this.selector, this.options);
    }
};


/***/ }),
/* 33 */
/***/ (() => {

const u = up.util;
const e = up.element;
const PREVENT_SCROLL_OPTIONS = { preventScroll: true };
up.FragmentFocus = class FragmentFocus extends up.FragmentProcessor {
    keys() {
        return super.keys().concat([
            'hash',
            'focusCapsule'
        ]);
    }
    processPrimitive(opt) {
        switch (opt) {
            case 'keep':
                return this.restoreFocus();
            case 'target':
            case true:
                return this.focusElement(this.fragment);
            case 'layer':
                return this.focusElement(this.layer.getFocusElement());
            case 'main':
                return this.focusSelector(':main');
            case 'hash':
                return this.focusHash();
            case 'autofocus':
                return this.autofocus();
            default:
                if (u.isString(opt)) {
                    return this.focusSelector(opt);
                }
        }
    }
    processElement(element) {
        return this.focusElement(element);
    }
    resolveCondition(condition) {
        if (condition === 'lost') {
            return this.wasFocusLost();
        }
        else {
            return super.resolveCondition(condition);
        }
    }
    focusSelector(selector) {
        let match = this.findSelector(selector);
        if (match) {
            return this.focusElement(match);
        }
    }
    restoreFocus() {
        return this.focusCapsule?.restore(this.fragment, PREVENT_SCROLL_OPTIONS);
    }
    autofocus() {
        let autofocusElement = e.subtree(this.fragment, '[autofocus]')[0];
        if (autofocusElement) {
            up.focus(autofocusElement, PREVENT_SCROLL_OPTIONS);
            return true;
        }
    }
    focusElement(element) {
        up.viewport.makeFocusable(element);
        up.focus(element, PREVENT_SCROLL_OPTIONS);
        return true;
    }
    focusHash() {
        let hashTarget = up.viewport.firstHashTarget(this.hash, { layer: this.layer });
        if (hashTarget) {
            return this.focusElement(hashTarget);
        }
    }
    wasFocusLost() {
        return this.focusCapsule?.wasLost();
    }
};


/***/ }),
/* 34 */
/***/ (() => {

const e = up.element;
const u = up.util;
up.FragmentPolling = class FragmentPolling {
    constructor(fragment) {
        this.options = {};
        this.state = 'initialized';
        this.setFragment(fragment);
    }
    static forFragment(fragment) {
        return fragment.upPolling || (fragment.upPolling = new this(fragment));
    }
    onPollAttributeObserved() {
        this.start();
    }
    onFragmentDestroyed() {
        // The element may come back (when it is swapped) or or may not come back (when it is destroyed).
        // If it does come back, `onPollAttributeObserved()` will restart the polling.
        this.stop();
    }
    start() {
        if (this.state !== 'started') {
            this.state = 'started';
            this.scheduleReload();
        }
    }
    stop() {
        if (this.state === 'started') {
            clearTimeout(this.reloadTimer);
            this.state = 'stopped';
        }
    }
    forceStart(options) {
        u.assign(this.options, options);
        this.forceStarted = true;
        this.start();
    }
    forceStop() {
        this.stop();
        this.forceStarted = false;
    }
    scheduleReload(delay = this.getInterval()) {
        this.reloadTimer = setTimeout(() => this.reload(), delay);
    }
    reload() {
        // The setTimeout(doReload) callback might already be scheduled
        // before the polling stopped.
        if (this.state !== 'started') {
            return;
        }
        if (up.radio.shouldPoll(this.fragment)) {
            let reloadOptions = {
                url: this.options.url,
                guardEvent: up.event.build('up:fragment:poll', { log: 'Polling fragment' })
            };
            u.always(up.reload(this.fragment, reloadOptions), (result) => this.onReloaded(result));
        }
        else {
            up.puts('[up-poll]', 'Polling is disabled');
            // Reconsider after 10 seconds at most
            let reconsiderDisabledDelay = Math.min(10 * 1000, this.getInterval());
            this.scheduleReload(reconsiderDisabledDelay);
        }
    }
    onReloaded(result) {
        // Transfer this instance to the new fragment.
        // We can remove this in case we don't implement forced start/stop.
        let newFragment = result?.fragments?.[0];
        if (newFragment) {
            // No need to scheduleReload() in this branch:
            // (1) Either the new fragment also has an [up-poll] and we have already
            //     started in #onPollAttributeObserved().
            // (2) Or we are force-started and we will start in #onFragmentSwapped().
            this.onFragmentSwapped(newFragment);
        }
        else {
            this.scheduleReload();
        }
    }
    onFragmentSwapped(newFragment) {
        // Transfer this polling to the new instance
        newFragment.upPolling = this;
        delete this.fragment.upPolling;
        this.setFragment(newFragment);
        if (this.state === 'stopped' && this.forceStarted) {
            // When polling was started programmatically through up.fragment.startPolling()
            // we don't require the updated fragment to have an [up-poll] attribute to
            // continue polling.
            this.start();
        }
    }
    setFragment(newFragment) {
        this.fragment = newFragment;
        up.destructor(newFragment, () => this.onFragmentDestroyed());
    }
    getInterval() {
        return this.options.interval ?? e.numberAttr(this.fragment, 'up-interval') ?? up.radio.config.pollInterval;
    }
};


/***/ }),
/* 35 */
/***/ (() => {

const u = up.util;
up.FragmentScrolling = class FragmentScrolling extends up.FragmentProcessor {
    keys() {
        return super.keys().concat([
            'hash',
            'mode',
            'revealTop',
            'revealMax',
            'revealSnap',
            'scrollBehavior',
            'scrollSpeed'
        ]);
    }
    constructor(options) {
        up.migrate.handleScrollOptions?.(options);
        super(options);
    }
    process(opt) {
        // If no option can be applied, return a fulfilled promise to
        // satisfy our signature as an async function.
        return super.process(opt) || Promise.resolve();
    }
    processPrimitive(opt) {
        switch (opt) {
            case 'reset':
                // If the user has passed { scroll: 'top' } we scroll to the top all
                // viewports that are either containing or are contained by element.
                return this.reset();
            case 'layer':
                return this.revealLayer();
            case 'main':
                return this.revealSelector(':main');
            case 'restore':
                return this.restore();
            case 'hash':
                return this.hash && up.viewport.revealHash(this.hash, this.attributes());
            case 'target':
            case 'reveal':
            case true:
                return this.revealElement(this.fragment);
            default:
                if (u.isString(opt)) {
                    return this.revealSelector(opt);
                }
        }
    }
    processElement(element) {
        return this.revealElement(element);
    }
    revealElement(element) {
        return up.reveal(element, this.attributes());
    }
    revealSelector(selector) {
        let match = this.findSelector(selector);
        if (match) {
            return this.revealElement(match);
        }
    }
    revealLayer() {
        // Reveal the layer's box instead of the layer's element.
        // If the layer has its own viewport, like a modal, revealing the box will
        // scroll the layer viewport. Revealing the layer element would scroll
        // the main document viewport.
        return this.revealElement(this.layer.getBoxElement());
    }
    reset() {
        return up.viewport.resetScroll({ ...this.attributes(), around: this.fragment });
    }
    restore() {
        return up.viewport.restoreScroll({ ...this.attributes(), around: this.fragment });
    }
};


/***/ }),
/* 36 */
/***/ (() => {

const u = up.util;
const e = up.element;
up.HTMLWrapper = class HTMLWrapper {
    constructor(tagName) {
        this.tagName = tagName;
        const openTag = `<${this.tagName}[^>]*>`;
        const closeTag = `</${this.tagName}>`;
        const innerHTML = "(.|\\s)*?";
        this.pattern = new RegExp(openTag + innerHTML + closeTag, 'ig');
        this.attrName = `up-wrapped-${this.tagName}`;
    }
    strip(html) {
        return html.replace(this.pattern, '');
    }
    wrap(html) {
        return html.replace(this.pattern, this.wrapMatch.bind(this));
    }
    wrapMatch(match) {
        this.didWrap = true;
        // Use a tag that may exist in both <head> and <body>.
        // If we wrap a <head>-contained <script> tag in a <div>, Chrome will
        // move that <div> to the <body>.
        return '<meta name="' + this.attrName + '" value="' + u.escapeHTML(match) + '">';
    }
    unwrap(element) {
        if (!this.didWrap) {
            return;
        }
        for (let wrappedChild of element.querySelectorAll(`meta[name='${this.attrName}']`)) {
            const originalHTML = wrappedChild.getAttribute('value');
            const restoredElement = e.createFromHTML(originalHTML);
            e.replace(wrappedChild, restoredElement);
        }
    }
};


/***/ }),
/* 37 */
/***/ (() => {

const e = up.element;
const u = up.util;
/*-
Each layer has an `up.Layer` instance.

Most functions in the `up.layer` package interact with the [current layer](/up.layer.current).
For example, `up.layer.dismiss()` is a shortcut for `up.layer.current.dismiss()`.

`up.layer.current` is set to the right layer in compilers and most events,
even if that layer is not the frontmost layer. E.g. if you're compiling a fragment for a background layer, `up.layer.current` will be
the background layer during compilation.

@class up.Layer
@parent up.layer
*/
up.Layer = class Layer extends up.Record {
    /*-
    This layer's outmost element.
  
    ### Example
  
    ```js
    let rootLayer = up.layer.root
    let overlay = await up.layer.open()
  
    rootLayer.element // returns <body>
    overlay.element   // returns <up-modal>
    ```
  
    @property up.Layer#element
    @param {Element} element
    @stable
    */
    /*-
    Whether fragment updates within this layer can affect browser history and window title.
  
    If a layer does not have visible history, its desendant layers cannot have history either.
  
    @property up.Layer#history
    @param {boolean} history
    @stable
    */
    /*-
    This layer's mode which governs its appearance and behavior.
  
    @see layer-terminology
  
    @property up.Layer#mode
    @param {string} mode
    @stable
    */
    /*-
    This layer's [context](/context).
  
    ### Example
  
    You may access the context properties like a regular JavaScript object.
  
    ```js
    let layer = up.layer.current
    layer.context.message = 'Please select a contact'
    console.log(layer.context) // logs "{ message: 'Please select a contact' }"
    ```
  
    @property up.Layer#context
    @param {Object} context
      The context object.
  
      If no context has been set an empty object is returned.
    @experimental
    */
    keys() {
        return [
            'element',
            'stack',
            'history',
            'mode',
            'context',
            'lastScrollTops'
        ];
    }
    defaults() {
        return {
            context: {},
            lastScrollTops: new up.Cache({ size: 30, key: up.history.normalizeURL })
        };
    }
    constructor(options = {}) {
        super(options);
        if (!this.mode) {
            throw "missing { mode } option";
        }
    }
    setupHandlers() {
        up.link.convertClicks(this);
    }
    teardownHandlers() { }
    // no-op for overriding
    mainTargets() {
        return up.layer.mainTargets(this.mode);
    }
    /*-
    Synchronizes this layer with the rest of the page.
  
    For instance, a popup overlay will re-calculate its position arounds its anchoring element.
  
    You only need to call this method after DOM changes unknown to Unpoly have brought
    overlays out of alignment with the rest of the page.
  
    @function up.Layer#sync
    @experimental
    */
    sync() {
        // no-op so users can blindly sync without knowing the current mode
    }
    /*-
    [Closes this overlay](/closing-overlays) with an accepting intent,
    e.g. when a change was confirmed or when a value was selected.
  
    To dismiss a layer *without* an accepting intent, use `up.Layer#dismiss()` instead.
  
    @function up.Layer#accept
    @param {any} [value]
      The acceptance value that will be passed to `{ onAccepted }` callbacks.
  
      If there isn't an acceptance value, omit this argument.
      If you need to pass options without an acceptance value, pass `null`:
  
      ```js
      up.layer.accept(null, { animation: 'move-to-bottom' })
      ```
    @param {string} [options.confirm]
      A message the user needs to confirm before the overlay is closed.
    @param {boolean} [options.preventable=true]
      Whether the closing can be prevented by an event listener.
    @param {string|Function(Element, Object)} [options.animation]
      The [animation](/up.animate) to use for closing this layer.
  
      Defaults to the close animation configured for this layer mode.
    @param {number} [options.duration]
      The duration for the close animation in milliseconds.
    @param {number} [options.easing]
      The [timing function](https://developer.mozilla.org/en-US/docs/Web/CSS/transition-timing-function)
      that controls the acceleration of the close animation.
    @param {Function} [options.onFinished]
      A callback that will run when the elements have been removed from the DOM.
  
      If the layer has a close animation, the callback will run after the animation has finished.
    @stable
    */
    accept() {
        throw up.error.notImplemented();
    }
    /*-
    [Closes this overlay](/closing-overlays) *without* an accepting intent,
    e.g. when a "Cancel" button was clicked.
  
    To close an overlay with an accepting intent, use `up.Layer#accept()` instead.
  
    @function up.Layer#dismiss
    @param {any} [value]
      The dismissal value that will be passed to `{ onDismissed }` callbacks.
  
      If there isn't an acceptance value, omit this argument.
      If you need to pass options without a dismissal value, pass `null`:
  
      ```js
      up.layer.dismiss(null, { animation: 'move-to-bottom' })
      ```
    @param {Object} [options]
      See options for `up.Layer#accept()`.
    @stable
    */
    dismiss() {
        throw up.error.notImplemented();
    }
    /*-
    [Dismisses](/up.Layer.prototype.dismiss) all descendant overlays,
    making this layer the [frontmost layer](/up.layer.front) in the [layer stack](/up.layer.stack).
  
    Descendant overlays will be dismissed with value `':peel'`.
  
    @function up.Layer#peel
    @param {Object} options
      See options for `up.Layer#accept()`.
    @stable
    */
    peel(options) {
        this.stack.peel(this, options);
    }
    evalOption(option) {
        return u.evalOption(option, this);
    }
    /*-
    Returns whether this layer is the [current layer](/up.layer.current).
  
    @function up.Layer#isCurrent
    @return {boolean}
    @stable
    */
    isCurrent() {
        return this.stack.isCurrent(this);
    }
    /*-
    Returns whether this layer is the [frontmost layer](/up.layer.front).
  
    @function up.Layer#isFront
    @return {boolean}
    @stable
    */
    isFront() {
        return this.stack.isFront(this);
    }
    /*-
    Returns whether this layer is the [root layer](/up.layer.root).
  
    @function up.Layer#isRoot
    @return {boolean}
    @stable
    */
    isRoot() {
        return this.stack.isRoot(this);
    }
    /*-
    Returns whether this layer is *not* the [root layer](/up.layer.root).
  
    @function up.Layer#isOverlay
    @return {boolean}
    @stable
    */
    isOverlay() {
        return this.stack.isOverlay(this);
    }
    /*-
    Returns whether this layer is still part of the [layer stack](/up.layer.stack).
  
    A layer is considered "closed" immediately after it has been [dismissed](/up.Layer.prototype.dismiss)
    or [accepted](/up.Layer.prototype.dismiss). If the closing is animated, a layer may be considered "closed" while
    closing animation is still playing.
  
    @function up.Layer#isOpen
    @return {boolean}
    @stable
    */
    isOpen() {
        return this.stack.isOpen(this);
    }
    /*-
    Returns whether this layer is no longer part of the [layer stack](/up.layer.stack).
  
    A layer is considered "closed" immediately after it has been [dismissed](/up.Layer.prototype.dismiss)
    or [accepted](/up.Layer.prototype.dismiss). If the closing is animated, a layer may be considered "closed" while
    closing animation is still playing.
  
    @function up.Layer#isClosed
    @return {boolean}
    @stable
    */
    isClosed() {
        return this.stack.isClosed(this);
    }
    /*-
    Returns this layer's parent layer.
  
    The parent layer is the layer that opened this layer. It is visually in the background of this layer.
  
    Returns `undefined` for the [root layer](/up.layer.root).
  
    @property up.Layer#parent
    @param {up.Layer} parent
    @stable
    */
    get parent() {
        return this.stack.parentOf(this);
    }
    /*-
    Returns this layer's child layer.
  
    The child layer is the layer that was opened on top of this layer. It visually overlays this layer.
  
    Returns `undefined` if this layer has not opened a child layer.
  
    A layer can have at most one child layer. Opening an overlay on a layer with an existing child will
    first dismiss the existing child before replacing it with the new child.
  
    @property up.Layer#child
    @return {up.Layer} child
    @stable
    */
    get child() {
        return this.stack.childOf(this);
    }
    /*-
    Returns an array of this layer's ancestor layers.
  
    The array elements are ordered by distance to this layer.
    The first element is this layer's direct parent. The last element
    is the [root layer](/up.layer.root).
  
    @property up.Layer#ancestors
    @return {Array<up.Layer>} ancestors
    @stable
    */
    get ancestors() {
        return this.stack.ancestorsOf(this);
    }
    /*-
    Returns an array of this layer's descendant layers, with the closest descendants listed first.
  
    Descendant layers are all layers that visually overlay this layer.
  
    The array elements are ordered by distance to this layer.
    The first element is this layer's direct child. The last element
    is the [frontmost layer](/up.layer.front).
  
    @property up.Layer#descendants
    @return {Array<up.Layer>} descendants
    @stable
    */
    get descendants() {
        return this.stack.descendantsOf(this);
    }
    /*-
    Returns the zero-based position of this layer in the [layer stack](/up.layer.stack).
  
    The [root layer](/up.layer.root) has an index of `0`, its child overlay has an index of `1`, and so on.
  
    @property up.Layer#index
    @return {number} index
    @stable
    */
    get index() {
        return this.stack.indexOf(this);
    }
    getContentElement() {
        return this.contentElement || this.element;
    }
    getBoxElement() {
        return this.boxElement || this.element;
    }
    getFocusElement() {
        return this.getBoxElement();
    }
    getFirstSwappableElement() {
        throw up.error.notImplemented();
    }
    /*-
    Returns whether the given `element` is contained by this layer.
  
    Note that this will always return `false` for elements in [descendant](/up.Layer.prototype.descendants) overlays,
    even if the descendant overlay's element is nested into the DOM tree of this layer.
  
    @function up.Layer#contains
    @param {Element} element
    @return {boolean}
    @stable
    */
    contains(element) {
        // Test that the closest parent is the element and not another layer with elements nested
        // into this layer's element.
        return e.closest(element, up.layer.anySelector()) === this.element;
    }
    /*-
    Listens to a [DOM event](https://developer.mozilla.org/en-US/docs/Web/API/Document_Object_Model/Events) that originated
    on an element [contained](/up.Layer.prototype.contains) by this layer.
  
    This will ignore events emitted on elements in [descendant](/up.Layer.prototype.descendants) overlays,
    even if the descendant overlay's element is nested into the DOM tree of this layer.
  
    The arguments for this function are the same as for `up.on()`.
  
    ### Example
  
        let rootLayer = up.layer.root
        let overlay = await up.layer.open()
  
        rootLayer.on('foo', (event) => console.log('Listener called'))
  
        rootLayer.emit('foo') // logs "Listener called"
        overlay.emit('foo')   // listener is not called
  
    ### Most Unpoly events have a layer reference
  
    Whenever possible Unpoly will emit its events on associated layers instead of `document`.
    This way you can listen to events on one layer without receiving events from other layers.
  
    E.g. to listen to all [requests](/up.request) originating from a given layer:
  
        let rootLayer = up.layer.root
        let rootLink = rootLayer.affix('a[href=/foo]')
  
        let overlay = await up.layer.open()
        let overlayLink = overlay.affix('a[href=/bar]')
  
        rootLayer.on('up:request:load', (event) => console.log('Listener called'))
  
        up.follow(rootLink)    // logs "Listener called"
        up.follow(overlayLink) // listener is not called
  
    @function up.Layer#on
  
    @param {string} types
      A space-separated list of event types to bind to.
  
    @param {string|Function(): string} [selector]
      The selector of an element on which the event must be triggered.
  
      Omit the selector to listen to all events of the given type, regardless
      of the event target.
  
      If the selector is not known in advance you may also pass a function
      that returns the selector. The function is evaluated every time
      an event with the given type is observed.
  
    @param {boolean} [options.passive=false]
      Whether to register a [passive event listener](https://developers.google.com/web/updates/2016/06/passive-event-listeners).
  
      A passive event listener may not call `event.preventDefault()`.
      This in particular may improve the frame rate when registering
      `touchstart` and `touchmove` events.
  
    @param {boolean} [options.once=true]
      Whether the listener should run at most once.
  
      If `true` the listener will automatically be unbound
      after the first invocation.
  
    @param {Function(event, [element], [data])} listener
      The listener function that should be called.
  
      The function takes the affected element as the second argument.
      If the element has an [`up-data`](/up-data) attribute, its value is parsed as JSON
      and passed as a third argument.
  
    @return {Function()}
      A function that unbinds the event listeners when called.
  
    @stable
    */
    on(...args) {
        return this.buildEventListenerGroup(args).bind();
    }
    /*-
    Unbinds an event listener previously bound with `up.Layer#on()`.
  
    @function up.Layer#off
    @param {string} events
    @param {string|Function(): string} [selector]
    @param {Function(event, [element], [data])} listener
      The listener function to unbind.
  
      Note that you must pass a reference to the same function reference
      that was passed to `up.Layer#on()` earlier.
    @stable
    */
    off(...args) {
        return this.buildEventListenerGroup(args).unbind();
    }
    buildEventListenerGroup(args) {
        return up.EventListenerGroup.fromBindArgs(args, {
            guard: (event) => this.containsEventTarget(event),
            elements: [this.element],
            baseLayer: this
        });
    }
    containsEventTarget(event) {
        // Since the root layer will receive events emitted on descendant layers
        // we need to manually check whether the event target is contained
        // by this layer.
        return this.contains(event.target);
    }
    wasHitByMouseEvent(event) {
        const hittableElement = document.elementFromPoint(event.clientX, event.clientY);
        return !hittableElement || this.contains(hittableElement);
    }
    buildEventEmitter(args) {
        return up.EventEmitter.fromEmitArgs(args, { layer: this });
    }
    /*-
    [Emits](/up.emit) an event on [this layer's element](/up.Layer.prototype.element).
  
    The value of [up.layer.current](/up.layer.current) will be set to the this layer
    while event listeners are running.
  
    ### Example
  
        let rootLayer = up.layer.root
        let overlay = await up.layer.open()
  
        rootLayer.on('foo', (event) => console.log('Listener called'))
  
        rootLayer.emit('foo') // logs "Listener called"
        overlay.emit('foo')   // listener is not called
  
    @function up.Layer#emit
    @param {Element|jQuery} [target=this.element]
      The element on which the event is triggered.
  
      If omitted, the event will be emitted on the [this layer's element](/up.Layer.prototype.element).
    @param {string} eventType
      The event type, e.g. `my:event`.
    @param {Object} [props={}]
      A list of properties to become part of the event object that will be passed to listeners.
    @param {string|Array} [props.log]
      A message to print to the [log](/up.log) when the event is emitted.
  
      Pass `false` to not log this event emission.
    @param {Element|jQuery} [props.target=this.element]
      The element on which the event is triggered.
  
      Alternatively the target element may be passed as the first argument.
    @stable
    */
    emit(...args) {
        return this.buildEventEmitter(args).emit();
    }
    isDetached() {
        return e.isDetached(this.element);
    }
    saveHistory() {
        if (this.isHistoryVisible()) {
            this.savedTitle = document.title;
            this.savedLocation = up.history.location;
        }
    }
    restoreHistory() {
        if (!this.showsLiveHistory()) {
            return;
        }
        if (this.savedLocation) {
            // We cannot use the `this.title` setter as that does not
            // push a state if `newLocation === this.savedLocation`.
            up.history.push(this.savedLocation);
        }
        if (this.savedTitle) {
            document.title = this.savedTitle;
        }
    }
    /*-
    Temporarily changes the [current layer](/up.layer.current) while the given
    function is running.
  
    Calls the given function and restores the original current layer when the function
    terminates.
  
    @param {Function()} fn
      The synchronous function to call.
  
      Async functions are not supported.
    @function up.Layer#asCurrent
    @experimental
    */
    asCurrent(fn) {
        return this.stack.asCurrent(this, fn);
    }
    updateHistory(options) {
        if (u.isString(options.title)) {
            this.title = options.title;
        }
        if (u.isString(options.location)) {
            this.location = options.location;
        }
    }
    isHistoryVisible() {
        // If an ancestor layer was opened with the wish to not affect history, this
        // child layer must not affect it either, regardless of its @history setting.
        return this.history && (this.isRoot() || this.parent.isHistoryVisible());
    }
    showsLiveHistory() {
        return this.isHistoryVisible() && this.isFront() && (up.history.config.enabled || this.isRoot());
    }
    /*-
    This layer's window title.
  
    If the [frontmost layer](/up.layer.front) does not have [visible history](/up.Layer.prototype.history),
    the browser window will show the title of an ancestor layer.
    This property will return the title the layer would use if it had visible history.
  
    If this layer does not [affect browser history](/up.Layer.prototype.history), this property will
    still return the title the layer would otherwise use.
  
    When this layer opens a child layer with visible history, the browser window will change to the child
    layer's title. When the child layer is closed, this layer's title will be restored.
  
    @property up.Layer#title
    @param {string} title
    @experimental
    */
    get title() {
        if (this.showsLiveHistory()) {
            // Allow Unpoly-unaware code to set the document title directly.
            // This will implicitey change the front layer's title.
            return document.title;
        }
        else {
            return this.savedTitle;
        }
    }
    set title(title) {
        this.savedTitle = title;
        if (this.showsLiveHistory()) {
            document.title = title;
        }
    }
    /*-
    This layer's location URL.
  
    If the layer has [no visible history](/up.Layer.prototype.history), this property
    still returns the URL of the content in the overlay. In this case
    the browser's address bar will show the location of an ancestor layer.
  
    When this layer opens a child layer with visible history, the browser URL will change to the child
    layer's location. When the child layer is closed, this layer's location will be restored.
  
    @property up.Layer#location
    @param {string} location
    @experimental
    */
    get location() {
        if (this.showsLiveHistory()) {
            // Allow Unpoly-unaware code to use the pushState API directly.
            // This will implicitly change the front layer's location.
            return up.history.location;
        }
        else {
            return this.savedLocation;
        }
    }
    set location(location) {
        const previousLocation = this.savedLocation;
        location = up.history.normalizeURL(location);
        if (previousLocation !== location) {
            this.savedLocation = location;
            this.emit('up:layer:location:changed', { location, log: false });
            if (this.showsLiveHistory()) {
                up.history.push(location);
            }
        }
    }
    selector(part) {
        return this.constructor.selector(part);
    }
    static selector(_part) {
        throw up.error.notImplemented();
    }
    toString() {
        throw up.error.notImplemented();
    }
    /*-
    Creates an element with the given `selector` and appends it to this layer's
    [outmost element](/up.Layer.prototype.element).
  
    Also see `up.element.affix()`.
  
    ### Example
  
    ```js
    layer = up.layer.open()
    element = layer.affix(.klass')
    element.parentElement // returns 'main'
    element.className // returns 'klass'
    ```
  
    @function up.Layer#affix
    @param {string} selector
      The CSS selector from which to create an element.
    @param {Object} attrs
      An object of attributes to set on the created element.
    @param {Object} attrs.text
      The [text content](https://developer.mozilla.org/en-US/docs/Web/API/Node/textContent) of the created element.
    @param {Object} attrs.style
      An object of CSS properties that will be set as the inline style
      of the created element.
  
      The given object may use kebab-case or camelCase keys.
    @experimental
    */
    affix(...args) {
        return e.affix(this.getFirstSwappableElement(), ...args);
    }
    [u.isEqual.key](other) {
        return (this.constructor === other.constructor) && (this.element === other.element);
    }
};


/***/ }),
/* 38 */
/***/ (() => {

const e = up.element;
const u = up.util;
/*-
@class up.Layer
*/
up.Layer.Overlay = class Overlay extends up.Layer {
    /*-
    The link or form element that opened this overlay.
  
    @property up.Layer#origin
    @param {Element} origin
    @stable
    */
    /*-
    The [size](/customizing-overlays#overlay-sizes) of this overlay.
  
    Returns a string like `'medium'` or `'large'`.
  
    @property up.Layer#size
    @param {Element} size
    @stable
    */
    /*-
    The [position](/customizing-overlays#popup-position) of this popup overlay.
  
    Returns a string like `'top'` or `'left'`.
  
    @property up.Layer#position
    @param {Element} align
    @stable
    */
    /*-
    The [alignment](/customizing-overlays#popup-position) of this popup overlay.
  
    Returns a string like `'left'` or `'right'`.
  
    @property up.Layer#align
    @param {Element} align
    @stable
    */
    keys() {
        return super.keys().concat([
            'position',
            'align',
            'size',
            'origin',
            'class',
            'backdrop',
            'openAnimation',
            'closeAnimation',
            'openDuration',
            'closeDuration',
            'openEasing',
            'closeEasing',
            'backdropOpenAnimation',
            'backdropCloseAnimation',
            'dismissable',
            'dismissLabel',
            'dismissAriaLabel',
            'onOpened',
            'onAccept',
            'onAccepted',
            'onDismiss',
            'onDismissed',
            'acceptEvent',
            'dismissEvent',
            'acceptLocation',
            'dismissLocation',
            'opening' // internal flag to know that the layer is being opened
        ]);
    }
    constructor(options) {
        super(options);
        if (this.dismissable === true) {
            this.dismissable = ['button', 'key', 'outside'];
        }
        else if (this.dismissable === false) {
            this.dismissable = [];
        }
        else {
            this.dismissable = u.splitValues(this.dismissable);
        }
        if (this.acceptLocation) {
            this.acceptLocation = new up.URLPattern(this.acceptLocation);
        }
        if (this.dismissLocation) {
            this.dismissLocation = new up.URLPattern(this.dismissLocation);
        }
    }
    callback(name) {
        // Only binds the callback to the layer instance.
        // Note if the callback was created by an UJS attribute like [up-on-accepted], the
        // callback is already bound to the origin element to mimic the behavior of built-in
        // handler attributes like [onclick]. In that case our additional bind() will have
        // no effect.
        //
        // The up.layer.current value within a callback is controlled by the event
        // emission in up.Change.OpenLayer and up.Change.CloseLayer
        let fn = this[name];
        if (fn) {
            return fn.bind(this);
        }
    }
    createElement(parentElement) {
        this.nesting || (this.nesting = this.suggestVisualNesting());
        const elementAttrs = u.compactObject(u.pick(this, ['align', 'position', 'size', 'class', 'nesting']));
        this.element = this.affixPart(parentElement, null, elementAttrs);
    }
    createBackdropElement(parentElement) {
        this.backdropElement = this.affixPart(parentElement, 'backdrop');
    }
    createViewportElement(parentElement) {
        // Give the viewport element an [up-viewport] attribute so it will be found
        // by up.viewport.get().
        this.viewportElement = this.affixPart(parentElement, 'viewport', { 'up-viewport': '' });
    }
    createBoxElement(parentElement) {
        this.boxElement = this.affixPart(parentElement, 'box');
    }
    createContentElement(parentElement, content) {
        this.contentElement = this.affixPart(parentElement, 'content');
        this.contentElement.appendChild(content);
    }
    createDismissElement(parentElement) {
        this.dismissElement = this.affixPart(parentElement, 'dismiss', {
            'up-dismiss': '":button"',
            'aria-label': this.dismissAriaLabel
        });
        // Since the dismiss button already has an accessible [aria-label]
        // we hide the "X" label from screen readers.
        return e.affix(this.dismissElement, 'span[aria-hidden="true"]', { text: this.dismissLabel });
    }
    affixPart(parentElement, part, options = {}) {
        return e.affix(parentElement, this.selector(part), options);
    }
    static selector(part) {
        return u.compact(['up', this.mode, part]).join('-');
    }
    suggestVisualNesting() {
        const { parent } = this;
        if (this.mode === parent.mode) {
            return 1 + parent.suggestVisualNesting();
        }
        else {
            return 0;
        }
    }
    setupHandlers() {
        super.setupHandlers();
        this.overlayFocus = new up.OverlayFocus(this);
        if (this.supportsDismissMethod('button')) {
            this.createDismissElement(this.getBoxElement());
        }
        if (this.supportsDismissMethod('outside')) {
            // If this overlay has its own viewport, a click outside the frame will hit
            // the viewport and not the parent element.
            if (this.viewportElement) {
                up.on(this.viewportElement, 'up:click', event => {
                    // Don't react when a click into the overlay frame bubbles to the viewportElement
                    if (event.target === this.viewportElement) {
                        this.onOutsideClicked(event, true);
                    }
                });
            }
            else {
                // Only bind to the parent if there's not already a viewport.
                // This prevents issues with other overlay libs appending elements to document.body,
                // but overlaying this overlay with a huge z-index. Clicking such a foreign overlay
                // would close this layer, as Unpoly considers it to be on the root layer (our parent).2
                this.unbindParentClicked = this.parent.on('up:click', (event, element) => {
                    // When our origin is clicked again, halt the click event
                    // We achieve this by halting the click event.
                    const originClicked = this.origin && this.origin.contains(element);
                    this.onOutsideClicked(event, originClicked);
                });
            }
        }
        if (this.supportsDismissMethod('key')) {
            this.unbindEscapePressed = up.event.onEscape(event => this.onEscapePressed(event));
        }
        // <a up-accept="value">OK</a>
        this.registerClickCloser('up-accept', (value, closeOptions) => {
            this.accept(value, closeOptions);
        });
        // <a up-dismiss="value">Cancel</a>
        this.registerClickCloser('up-dismiss', (value, closeOptions) => {
            this.dismiss(value, closeOptions);
        });
        up.migrate.registerLayerCloser?.(this);
        // let { userId } = await up.layer.open({ acceptEvent: 'user:show' })
        // registerEventCloser() will fill in this and arguments.
        this.registerEventCloser(this.acceptEvent, this.accept);
        this.registerEventCloser(this.dismissEvent, this.dismiss);
    }
    onOutsideClicked(event, halt) {
        if (halt) {
            up.event.halt(event);
        }
        this.dismiss(':outside', { origin: event.target });
    }
    onEscapePressed(event) {
        // All overlays listen to the Escape key being pressed, but only the front layer
        // should react. Note that we're using the *front* layer, not the *current* layer.
        // The current layer might be in the visual background, e.g. if a fragment is being
        // compiled in a background layer.
        if (this.isFront()) {
            let field = up.form.focusedField();
            if (field) {
                // Allow screen reader users to get back to a state where they can dismiss the
                // modal with escape.
                field.blur();
            }
            else if (this.supportsDismissMethod('key')) {
                up.event.halt(event);
                this.dismiss(':key');
            }
        }
    }
    registerClickCloser(attribute, closeFn) {
        let selector = `[${attribute}]`;
        // Allow the fallbacks to be both vanilla links and Unpoly [up-target] links
        this.on('up:click', selector, function (event) {
            // Since we're defining this handler on up.Overlay, we will not prevent
            // a link from being followed on the root layer.
            up.event.halt(event);
            const origin = e.closest(event.target, selector);
            const value = e.jsonAttr(origin, attribute);
            const closeOptions = { origin };
            const parser = new up.OptionsParser(closeOptions, origin);
            parser.booleanOrString('animation');
            parser.string('easing');
            parser.number('duration');
            parser.string('confirm');
            closeFn(value, closeOptions);
        });
    }
    registerEventCloser(eventTypes, closeFn) {
        if (!eventTypes) {
            return;
        }
        return this.on(eventTypes, event => {
            event.preventDefault();
            closeFn.call(this, event);
        });
    }
    tryAcceptForLocation() {
        this.tryCloseForLocation(this.acceptLocation, this.accept);
    }
    tryDismissForLocation() {
        this.tryCloseForLocation(this.dismissLocation, this.dismiss);
    }
    tryCloseForLocation(urlPattern, closeFn) {
        let location, resolution;
        if (urlPattern && (location = this.location) && (resolution = urlPattern.recognize(location))) {
            // resolution now contains named capture groups, e.g. when
            // '/decks/:deckId/cards/:cardId' is matched against
            // '/decks/123/cards/456' resolution is { deckId: 123, cardId: 456 }.
            const closeValue = { ...resolution, location };
            closeFn.call(this, closeValue);
        }
    }
    teardownHandlers() {
        super.teardownHandlers();
        this.unbindParentClicked?.();
        this.unbindEscapePressed?.();
        this.overlayFocus.teardown();
    }
    /*-
    Destroys the elements that make up this overlay.
  
    @function up.Layer.prototype.destroyElements
    @param {string|Function(Element, Object)} [options.animation=this.closeAnimation]
    @param {number} [options.duration=this.closeDuration]
    @param {string} [options.easing=this.closeEasing]
    @param {Function} [options.onFinished]
      A callback that will run when the elements have been removed from the DOM.
      If the destruction is animated, the callback will run after the animation has finished.
    @return {Promise}
      A resolved promise.
    @internal
    */
    destroyElements(options) {
        const animation = () => {
            return this.startCloseAnimation(options);
        };
        const onFinished = () => {
            this.onElementsRemoved(); // callback for layer implementations that need to clean up
            options.onFinished?.(); // callback for callers of up.layer.dismiss/accept()
        };
        // Do not re-use `options`, or we would call startCloseAnimation(animation: startCloseAnimation)!
        const destroyOptions = { ...options, animation, onFinished, log: false };
        up.destroy(this.element, destroyOptions);
    }
    onElementsRemoved() { }
    // optional callback
    startAnimation(options = {}) {
        const boxDone = up.animate(this.getBoxElement(), options.boxAnimation, options);
        // If we don't animate the box, we don't animate the backdrop
        let backdropDone;
        if (this.backdrop && !up.motion.isNone(options.boxAnimation)) {
            backdropDone = up.animate(this.backdropElement, options.backdropAnimation, options);
        }
        // Promise.all() ignores non-Thenables in the given array
        return Promise.all([boxDone, backdropDone]);
    }
    startOpenAnimation(options = {}) {
        return this.startAnimation({
            boxAnimation: options.animation ?? this.evalOption(this.openAnimation),
            backdropAnimation: 'fade-in',
            easing: options.easing || this.openEasing,
            duration: options.duration || this.openDuration
        }).then(() => {
            return this.wasEverVisible = true;
        });
    }
    startCloseAnimation(options = {}) {
        const boxAnimation = this.wasEverVisible && (options.animation ?? this.evalOption(this.closeAnimation));
        return this.startAnimation({
            boxAnimation,
            backdropAnimation: 'fade-out',
            easing: options.easing || this.closeEasing,
            duration: options.duration || this.closeDuration
        });
    }
    accept(value = null, options = {}) {
        return this.executeCloseChange('accept', value, options);
    }
    dismiss(value = null, options = {}) {
        return this.executeCloseChange('dismiss', value, options);
    }
    supportsDismissMethod(method) {
        return u.contains(this.dismissable, method);
    }
    executeCloseChange(verb, value, options) {
        options = { ...options, verb, value, layer: this };
        return new up.Change.CloseLayer(options).execute();
    }
    getFirstSwappableElement() {
        return this.getContentElement().children[0];
    }
    toString() {
        return `${this.mode} overlay`;
    }
};


/***/ }),
/* 39 */
/***/ (() => {

up.Layer.OverlayWithTether = class OverlayWithTether extends up.Layer.Overlay {
    createElements(content) {
        if (!this.origin) {
            up.fail('Missing { origin } option');
        }
        // We first construct an un-started Tether object so we can
        // ask for its parent element.
        this.tether = new up.Tether({
            anchor: this.origin,
            align: this.align,
            position: this.position
        });
        this.createElement(this.tether.parent);
        this.createContentElement(this.element, content);
        this.tether.start(this.element);
    }
    onElementsRemoved() {
        this.tether.stop();
    }
    sync() {
        // In case some async code calls #sync() on a layer that was already closed,
        // don't run the code below that might re-attach the overlay.
        if (this.isOpen()) {
            if (this.isDetached() || this.tether.isDetached()) {
                // If our tether parent and anchor is gone, the best thing we can
                // do now is to dismiss ourselves and have a consistent layer stack.
                this.dismiss(':detached', {
                    animation: false,
                    preventable: false // since we're cleaning up a broken stack, don't allow user intervention
                });
            }
            else {
                // The fragment update might have moved elements around.
                // This is a good moment to sync our position relative to the anchor.
                this.tether.sync();
            }
        }
    }
};


/***/ }),
/* 40 */
/***/ (() => {

var _a;
up.Layer.OverlayWithViewport = (_a = class OverlayWithViewport extends up.Layer.Overlay {
        // For stubbing in tests
        static getParentElement() {
            // Always make a fresh lookup of the <body>, since the <body>
            // might be swapped out with a new element.
            return document.body;
        }
        /*-
        @function up.Layer.OverlayWithViewport#openNow
        @param {Element} options.content
        @internal
        */
        createElements(content) {
            this.shiftBody();
            this.createElement(this.constructor.getParentElement());
            if (this.backdrop) {
                this.createBackdropElement(this.element);
            }
            this.createViewportElement(this.element);
            this.createBoxElement(this.viewportElement);
            this.createContentElement(this.boxElement, content);
        }
        onElementsRemoved() {
            this.unshiftBody();
        }
        shiftBody() {
            this.constructor.bodyShifter.shift();
        }
        unshiftBody() {
            this.constructor.bodyShifter.unshift();
        }
        sync() {
            // A swapping of <body> might have removed this overlay from the DOM, so we
            // attach it again.
            //
            // We also check #isOpen() in case some async code calls #sync() on a layer
            // that was already closed. In that case don't run the code below that might
            // re-attach the overlay.
            if (this.isDetached() && this.isOpen()) {
                this.constructor.getParentElement().appendChild(this.element);
            }
        }
    },
    _a.bodyShifter = new up.BodyShifter(),
    _a);


/***/ }),
/* 41 */
/***/ (() => {

var _a;
const u = up.util;
const e = up.element;
up.Layer.Root = (_a = class Root extends up.Layer {
        constructor(options) {
            super(options);
            this.setupHandlers();
        }
        get element() {
            // Let's talk about our choice of @element for the root layer.
            //
            // 1. We don't want to use `document`, since that is for our global event bus.
            //    For instance, take a look how up.Change.CloseLayer emits the up:layer:dismiss
            //    event first on `@layer.element`, then on `document`.
            //    Also `document` is not really an element, just an event target.
            // 2. We want but cannot use <body> element. Since Unpoly boots before
            //    the DOM is ready, document.body is still undefined. We also cannot delay
            //    booting until the DOM is ready, since by then all user-defined event listeners
            //    and compilers will have registered.
            // 3. That leaves the <html> element, which is available before the DOM is ready
            //    on Chrome, Firefox, IE11, Safari.
            // 4. A nice benefit of using <html> is that up.fragment.get('html', layer: 'root')
            //    will yield a result.
            //
            // We always return the current <body> instead of caching it,
            // since the developer might replace it with a new version.
            return e.root;
        }
        getFirstSwappableElement() {
            return document.body;
        }
        static selector() {
            return 'html';
        }
        setupHandlers() {
            // When we reset the framework during tests, we might re-initialize this
            // layer with the same <html> element. In this case we do not want to
            // setup handlers more than once.
            if (!this.element.upHandlersApplied) {
                this.element.upHandlersApplied = true;
                super.setupHandlers();
            }
        }
        sync() {
            // In case a fragment update has swapped the <html> element we need to re-apply
            // event handlers to the new <html> element.
            this.setupHandlers();
        }
        accept() {
            this.cannotCloseRoot();
        }
        dismiss() {
            this.cannotCloseRoot();
        }
        cannotCloseRoot() {
            throw up.error.failed('Cannot close the root layer');
        }
        reset() {
            u.assign(this, this.defaults());
        }
        toString() {
            return "root layer";
        }
    },
    _a.mode = 'root',
    _a);


/***/ }),
/* 42 */
/***/ (() => {

var _a;
up.Layer.Modal = (_a = class Modal extends up.Layer.OverlayWithViewport {
    },
    _a.mode = 'modal',
    _a);


/***/ }),
/* 43 */
/***/ (() => {

var _a;
up.Layer.Popup = (_a = class Popup extends up.Layer.OverlayWithTether {
    },
    _a.mode = 'popup',
    _a);


/***/ }),
/* 44 */
/***/ (() => {

var _a;
up.Layer.Drawer = (_a = class Drawer extends up.Layer.OverlayWithViewport {
    },
    _a.mode = 'drawer',
    _a);


/***/ }),
/* 45 */
/***/ (() => {

var _a;
up.Layer.Cover = (_a = class Cover extends up.Layer.OverlayWithViewport {
    },
    _a.mode = 'cover',
    _a);


/***/ }),
/* 46 */
/***/ (() => {

const u = up.util;
const e = up.element;
up.LayerLookup = class LayerLookup {
    constructor(stack, ...args) {
        this.stack = stack;
        const options = u.parseArgIntoOptions(args, 'layer');
        // Options normalization might change `options` relevant to the lookup:
        // (1) It will default { layer } to 'origin' if an { origin } element is given.
        // (2) It will also lookup a string { baseLayer }.
        // (3) It will set the default layer to 'current' if nothing matches.
        if (options.normalizeLayerOptions !== false) {
            up.layer.normalizeOptions(options);
        }
        this.values = u.splitValues(options.layer);
        this.origin = options.origin;
        this.baseLayer = options.baseLayer || this.originLayer() || this.stack.current;
        if (u.isString(this.baseLayer)) {
            // The { baseLayer } option may itself be a string like "parent".
            // In this case we look it up using a new up.LayerLookup instance, using
            // up.layer.current as the { baseLayer } for that second lookup.
            const recursiveOptions = { ...options, baseLayer: this.stack.current, normalizeLayerOptions: false };
            this.baseLayer = new this.constructor(this.stack, this.baseLayer, recursiveOptions).first();
        }
    }
    originLayer() {
        if (this.origin) {
            return this.forElement(this.origin);
        }
    }
    first() {
        return this.all()[0];
    }
    all() {
        let results = u.flatMap(this.values, value => this.resolveValue(value));
        results = u.compact(results);
        results = u.uniq(results);
        return results;
    }
    forElement(element) {
        element = e.get(element); // unwrap jQuery
        return u.find(this.stack.reversed(), layer => layer.contains(element));
    }
    forIndex(value) {
        return this.stack[value];
    }
    resolveValue(value) {
        if (value instanceof up.Layer) {
            return value;
        }
        if (u.isNumber(value)) {
            return this.forIndex(value);
        }
        if (/^\d+$/.test(value)) {
            return this.forIndex(Number(value));
        }
        if (u.isElementish(value)) {
            return this.forElement(value);
        }
        switch (value) {
            case 'any':
                // Return all layers, but prefer a layer that's either the current
                // layer, or closer to the front.
                return [this.baseLayer, ...this.stack.reversed()];
            case 'current':
                return this.baseLayer;
            case 'closest':
                return this.stack.selfAndAncestorsOf(this.baseLayer);
            case 'parent':
                return this.baseLayer.parent;
            case 'ancestor':
            case 'ancestors':
                return this.baseLayer.ancestors;
            case 'child':
                return this.baseLayer.child;
            case 'descendant':
            case 'descendants':
                return this.baseLayer.descendants;
            case 'new':
                return 'new'; // pass-through
            case 'root':
                return this.stack.root;
            case 'overlay':
            case 'overlays':
                return u.reverse(this.stack.overlays);
            case 'front':
                return this.stack.front;
            case 'origin':
                return this.originLayer();
            default:
                return up.fail("Unknown { layer } option: %o", value);
        }
    }
};


/***/ }),
/* 47 */
/***/ (() => {

const u = up.util;
up.LayerStack = class LayerStack extends Array {
    constructor() {
        super();
        // When TypeScript transpiles to ES5, there is an issue with this constructor always creating
        // a `this` of type `Array` instead of `LayerStack`. The transpiled code looks like this:
        //
        //     function LayerStack() {
        //       let this = Array.call(this) || this
        //     }
        //
        // And since Array() returns a value, this returns the new this.
        // The official TypeScript recommendation is to use setProtoTypeOf() after calling super:
        // https://github.com/Microsoft/TypeScript/wiki/FAQ#why-doesnt-extending-built-ins-like-error-array-and-map-work
        Object.setPrototypeOf(this, up.LayerStack.prototype);
        this.currentOverrides = [];
        this.push(this.buildRoot());
    }
    buildRoot() {
        return up.layer.build({ mode: 'root', stack: this });
    }
    remove(layer) {
        u.remove(this, layer);
    }
    peel(layer, options) {
        // We will dismiss descendants closer to the front first to prevent
        // recursive calls of peel().
        const descendants = u.reverse(layer.descendants);
        // Callers expect the effects of peel() to manipulate the layer stack sync.
        // Because of this we will dismiss alle descendants sync rather than waiting
        // for each descendant to finish its closing animation.
        const dismissOptions = { ...options, preventable: false };
        for (let descendant of descendants) {
            descendant.dismiss(':peel', dismissOptions);
        }
    }
    reset() {
        this.peel(this.root, { animation: false });
        this.currentOverrides = [];
        this.root.reset();
    }
    isOpen(layer) {
        return layer.index >= 0;
    }
    isClosed(layer) {
        return !this.isOpen(layer);
    }
    parentOf(layer) {
        return this[layer.index - 1];
    }
    childOf(layer) {
        return this[layer.index + 1];
    }
    ancestorsOf(layer) {
        // Return closest ancestors first
        return u.reverse(this.slice(0, layer.index));
    }
    selfAndAncestorsOf(layer) {
        // Order for layer.closest()
        return [layer, ...layer.ancestors];
    }
    descendantsOf(layer) {
        return this.slice(layer.index + 1);
    }
    isRoot(layer) {
        return this[0] === layer;
    }
    isOverlay(layer) {
        return !this.isRoot(layer);
    }
    isCurrent(layer) {
        return this.current === layer;
    }
    isFront(layer) {
        return this.front === layer;
    }
    get(...args) {
        return this.getAll(...args)[0];
    }
    getAll(...args) {
        return new up.LayerLookup(this, ...args).all();
    }
    sync() {
        for (let layer of this) {
            layer.sync();
        }
    }
    asCurrent(layer, fn) {
        try {
            this.currentOverrides.push(layer);
            return fn();
        }
        finally {
            this.currentOverrides.pop();
        }
    }
    reversed() {
        return u.reverse(this);
    }
    dismissOverlays(value = null, options = {}) {
        options.dismissable = false;
        for (let overlay of u.reverse(this.overlays)) {
            overlay.dismiss(value, options);
        }
    }
    // Used by up.util.reverse() and specs
    [u.copy.key]() {
        return u.copyArrayLike(this);
    }
    get count() {
        return this.length;
    }
    get root() {
        return this[0];
    }
    get overlays() {
        return this.root.descendants;
    }
    get current() {
        // Event listeners and compilers will push into @currentOverrides
        // to temporarily set up.layer.current to the layer they operate in.
        return u.last(this.currentOverrides) || this.front;
    }
    get front() {
        return u.last(this);
    }
};


/***/ }),
/* 48 */
/***/ (() => {

up.LinkFeedbackURLs = class LinkFeedbackURLs {
    constructor(link) {
        const normalize = up.feedback.normalizeURL;
        // A link with an unsafe method will never be higlighted with .up-current.
        this.isSafe = up.link.isSafe(link);
        if (this.isSafe) {
            const href = link.getAttribute('href');
            if (href && (href !== '#')) {
                this.href = normalize(href);
            }
            const upHREF = link.getAttribute('up-href');
            if (upHREF) {
                this.upHREF = normalize(upHREF);
            }
            const alias = link.getAttribute('up-alias');
            if (alias) {
                this.aliasPattern = new up.URLPattern(alias, normalize);
            }
        }
    }
    isCurrent(normalizedLocation) {
        // It is important to return false instead of a falsey value.
        // up.feedback feeds the return value to element.toggleClass(), which would use a default for undefined.
        return this.isSafe && !!((this.href && (this.href === normalizedLocation)) ||
            (this.upHREF && (this.upHREF === normalizedLocation)) ||
            (this.aliasPattern && this.aliasPattern.test(normalizedLocation, false)));
    }
};


/***/ }),
/* 49 */
/***/ (() => {

const u = up.util;
const e = up.element;
up.LinkPreloader = class LinkPreloader {
    constructor() {
        this.considerPreload = this.considerPreload.bind(this);
    }
    observeLink(link) {
        // If the link has an unsafe method (like POST) and is hence not preloadable,
        // prevent up.link.preload() from blowing up by not observing the link (even if
        // the user uses [up-preload] everywhere).
        if (up.link.isSafe(link)) {
            this.on(link, 'mouseenter', event => this.considerPreload(event, true));
            this.on(link, 'mousedown touchstart', event => this.considerPreload(event));
            this.on(link, 'mouseleave', event => this.stopPreload(event));
        }
    }
    on(link, eventTypes, callback) {
        up.on(link, eventTypes, { passive: true }, callback);
    }
    considerPreload(event, applyDelay) {
        const link = event.target;
        if (link !== this.currentLink) {
            this.reset();
            this.currentLink = link;
            // Don't preload when the user is holding down CTRL or SHIFT.
            if (up.link.shouldFollowEvent(event, link)) {
                if (applyDelay) {
                    this.preloadAfterDelay(link);
                }
                else {
                    this.preloadNow(link);
                }
            }
        }
    }
    stopPreload(event) {
        if (event.target === this.currentLink) {
            return this.reset();
        }
    }
    reset() {
        if (!this.currentLink) {
            return;
        }
        clearTimeout(this.timer);
        // Only abort if the request is still preloading.
        // If the user has clicked on the link while the request was in flight,
        // and then unhovered the link, we do not abort the navigation.
        if (this.currentRequest?.preload) {
            this.currentRequest.abort();
        }
        this.currentLink = undefined;
        this.currentRequest = undefined;
    }
    preloadAfterDelay(link) {
        const delay = e.numberAttr(link, 'up-delay') ?? up.link.config.preloadDelay;
        this.timer = u.timer(delay, () => this.preloadNow(link));
    }
    preloadNow(link) {
        // Don't preload if the link was removed from the DOM while we were waiting for the timer.
        if (e.isDetached(link)) {
            this.reset();
            return;
        }
        const onQueued = request => { return this.currentRequest = request; };
        up.log.muteUncriticalRejection(up.link.preload(link, { onQueued }));
        this.queued = true;
    }
};


/***/ }),
/* 50 */
/***/ (() => {

const u = up.util;
const e = up.element;
up.MotionController = class MotionController {
    constructor(name) {
        this.activeClass = `up-${name}`;
        this.dataKey = `up-${name}-finished`;
        this.selector = `.${this.activeClass}`;
        this.finishEvent = `up:${name}:finish`;
        this.finishCount = 0;
        this.clusterCount = 0;
    }
    /*-
    Finishes all animations in the given elements' ancestors and
    descendants, then calls the given function.
  
    The function is expected to return a promise that is fulfilled when
    the animation ends. The function is also expected to listen to
    `this.finishEvent` and instantly skip to the last frame
    when the event is observed.
  
    The animation is tracked so it can be
    [`finished`](/up.MotionController.finish) later.
  
    @function startFunction
    @param {Element|List<Element>} cluster
      A list of elements that will be affected by the motion.
    @param {Function(): Promise} startMotion
    @param {Object} [memory.trackMotion=true]
    @return {Promise}
      A promise that fulfills when the animation ends.
    */
    async startFunction(cluster, startMotion, memory = {}) {
        cluster = e.list(cluster);
        // Some motions might reject after starting. E.g. a scrolling animation
        // will reject when the user scrolls manually during the animation. For
        // the purpose of this controller, we just want to know when the animation
        // has setteld, regardless of whether it was resolved or rejected.
        const mutedAnimator = () => up.log.muteUncriticalRejection(startMotion());
        // Callers can pass an options hash `memory` in which we store a { trackMotion }
        // property. With this we can prevent tracking the same motion multiple times.
        // This is an issue when composing a transition from two animations, or when
        // using another transition from within a transition function.
        memory.trackMotion = memory.trackMotion ?? up.motion.isEnabled();
        if (memory.trackMotion === false) {
            // Since we don't want recursive tracking or finishing, we could run
            // the animator() now. However, since the else branch is async, we push
            // the animator into the microtask queue to be async as well.
            await u.microtask(mutedAnimator);
        }
        else {
            memory.trackMotion = false;
            await this.finish(cluster);
            let promise = this.whileForwardingFinishEvent(cluster, mutedAnimator);
            // Attach the modified promise to the cluster's elements
            this.markCluster(cluster, promise);
            promise = promise.then(() => this.unmarkCluster(cluster));
            // Return the original promise that is still running
            return await promise;
        }
    }
    /*-
    Finishes all animations in the given elements' ancestors and
    descendants, then calls `motion.start()`.
  
    Also listens to `this.finishEvent` on the given elements.
    When this event is observed, calls `motion.finish()`.
  
    @function startMotion
    @param {Element|List<Element>} cluster
    @param {up.Motion} motion
    @param {Object} [memory.trackMotion=true]
    */
    startMotion(cluster, motion, memory = {}) {
        const start = () => motion.start();
        const finish = () => motion.finish();
        const unbindFinish = up.on(cluster, this.finishEvent, finish);
        let promise = this.startFunction(cluster, start, memory);
        promise = promise.then(unbindFinish);
        return promise;
    }
    /*-
    @function finish
    @param {List<Element>} [elements]
      If no element is given, finishes all animations in the documnet.
      If an element is given, only finishes animations in its subtree and ancestors.
    @return {Promise} A promise that fulfills when animations have finished.
    */
    finish(elements) {
        this.finishCount++;
        if ((this.clusterCount === 0) || !up.motion.isEnabled()) {
            return Promise.resolve();
        }
        elements = this.expandFinishRequest(elements);
        const allFinished = u.map(elements, this.finishOneElement.bind(this));
        return Promise.all(allFinished);
    }
    expandFinishRequest(elements) {
        if (elements) {
            return u.flatMap(elements, el => e.list(e.closest(el, this.selector), e.all(el, this.selector)));
        }
        else {
            // If no reference elements were given, we finish every matching
            // element on the screen.
            return e.all(this.selector);
        }
    }
    isActive(element) {
        return element.classList.contains(this.activeClass);
    }
    finishOneElement(element) {
        // Animating code is expected to listen to this event, fast-forward
        // the animation and resolve their promise. All built-ins like
        // `up.animate`, `up.morph`, or `up.scroll` behave that way.
        this.emitFinishEvent(element);
        // If animating code ignores the event, we cannot force the animation to
        // finish from here. We will wait for the animation to end naturally before
        // starting the next animation.
        return this.whenElementFinished(element);
    }
    emitFinishEvent(element, eventAttrs = {}) {
        eventAttrs = { target: element, log: false, ...eventAttrs };
        return up.emit(this.finishEvent, eventAttrs);
    }
    whenElementFinished(element) {
        // There are some cases related to element ghosting where an element
        // has the class, but not the data value. In that case simply return
        // a resolved promise.
        return element[this.dataKey] || Promise.resolve();
    }
    markCluster(cluster, promise) {
        this.clusterCount++;
        for (let element of cluster) {
            element.classList.add(this.activeClass);
            element[this.dataKey] = promise;
        }
    }
    unmarkCluster(cluster) {
        this.clusterCount--;
        for (let element of cluster) {
            element.classList.remove(this.activeClass);
            delete element[this.dataKey];
        }
    }
    whileForwardingFinishEvent(cluster, fn) {
        if (cluster.length < 2) {
            return fn();
        }
        const doForward = (event) => {
            if (!event.forwarded) {
                for (let element of cluster) {
                    if (element !== event.target && this.isActive(element)) {
                        this.emitFinishEvent(element, { forwarded: true });
                    }
                }
            }
        };
        // Forward the finish event to the ghost that is actually animating
        const unbindFinish = up.on(cluster, this.finishEvent, doForward);
        // Our own pseudo-animation finishes when the actual animation on $ghost finishes
        return fn().then(unbindFinish);
    }
    async reset() {
        await this.finish();
        this.finishCount = 0;
        this.clusterCount = 0;
    }
};


/***/ }),
/* 51 */
/***/ (() => {

const u = up.util;
const e = up.element;
up.NonceableCallback = class NonceableCallback {
    constructor(script, nonce) {
        this.script = script;
        this.nonce = nonce;
    }
    static fromString(string) {
        let match = string.match(/^(nonce-([^\s]+)\s)?(.*)$/);
        return new this(match[3], match[2]);
    }
    /*-
    Replacement for `new Function()` that can take a nonce to work with a strict Content Security Policy.
  
    It also prints an error when a strict CSP is active, but user supplies no nonce.
  
    ### Examples
  
    ```js
    new up.NonceableCallback('1 + 2', 'secret').toFunction()
    ```
  
    @function up.NonceableCallback#toFunction
    @internal
    */
    toFunction(...argNames) {
        if (up.browser.canEval()) {
            return new Function(...argNames, this.script);
        }
        else if (this.nonce) {
            // Don't return a bound function so callers can re-bind to a different this.
            let callbackThis = this;
            return function (...args) {
                return callbackThis.runAsNoncedFunction(this, argNames, args);
            };
        }
        else {
            return this.cannotRun.bind(this);
        }
    }
    toString() {
        return `nonce-${this.nonce} ${this.script}`;
    }
    cannotRun() {
        throw new Error(`Your Content Security Policy disallows inline JavaScript (${this.script}). See https://unpoly.com/csp for solutions.`);
    }
    runAsNoncedFunction(thisArg, argNames, args) {
        let wrappedScript = `
      try {
        up.noncedEval.value = (function(${argNames.join(',')}) {
          ${this.script}
        }).apply(up.noncedEval.thisArg, up.noncedEval.args)
      } catch (error) {
        up.noncedEval.error = error
      }
    `;
        let script;
        try {
            up.noncedEval = { args, thisArg: thisArg };
            script = up.element.affix(document.body, 'script', { nonce: this.nonce, text: wrappedScript });
            if (up.noncedEval.error) {
                throw up.noncedEval.error;
            }
            else {
                return up.noncedEval.value;
            }
        }
        finally {
            up.noncedEval = undefined;
            if (script) {
                up.element.remove(script);
            }
        }
    }
    allowedBy(allowedNonces) {
        return this.nonce && u.contains(allowedNonces, this.nonce);
    }
    static adoptNonces(element, allowedNonces) {
        if (!allowedNonces?.length) {
            return;
        }
        // Looking up a nonce requires a DOM query.
        // For performance reasons we only do this when we're actually rewriting
        // a nonce, and only once per response.
        const getPageNonce = u.memoize(up.protocol.cspNonce);
        u.each(up.protocol.config.nonceableAttributes, (attribute) => {
            let matches = e.subtree(element, `[${attribute}^="nonce-"]`);
            u.each(matches, (match) => {
                let attributeValue = match.getAttribute(attribute);
                let callback = this.fromString(attributeValue);
                let warn = (message, ...args) => up.log.warn('up.render()', `Cannot use callback [${attribute}="${attributeValue}"]: ${message}`, ...args);
                if (!callback.allowedBy(allowedNonces)) {
                    // Don't rewrite a nonce that the browser would have rejected.
                    return warn("Callback's CSP nonce (%o) does not match response header (%o)", callback.nonce, allowedNonces);
                }
                // Replace the nonce with that of the current page.
                // This will allow the handler to run via #toFunction().
                let pageNonce = getPageNonce();
                if (!pageNonce) {
                    return warn("Current page's CSP nonce is unknown");
                }
                callback.nonce = pageNonce;
                match.setAttribute(attribute, callback.toString());
            });
        });
    }
};


/***/ }),
/* 52 */
/***/ (() => {

const u = up.util;
const e = up.element;
up.OptionsParser = class OptionsParser {
    constructor(options, element, parserOptions) {
        this.options = options;
        this.element = element;
        this.fail = parserOptions?.fail;
    }
    string(key, keyOptions) {
        this.parse(e.attr, key, keyOptions);
    }
    boolean(key, keyOptions) {
        this.parse(e.booleanAttr, key, keyOptions);
    }
    number(key, keyOptions) {
        this.parse(e.numberAttr, key, keyOptions);
    }
    booleanOrString(key, keyOptions) {
        this.parse(e.booleanOrStringAttr, key, keyOptions);
    }
    json(key, keyOptions) {
        this.parse(e.jsonAttr, key, keyOptions);
    }
    parse(attrValueFn, key, keyOptions = {}) {
        const attrNames = u.wrapList(keyOptions.attr ?? this.attrNameForKey(key));
        // Below we will only set @options[key] = value if value is defined.
        // Setting undefined values would throw of up.RenderOptionsAssembler in up.render().
        let value = this.options[key];
        if (this.element) {
            for (let attrName of attrNames) {
                value ?? (value = attrValueFn(this.element, attrName));
            }
        }
        value ?? (value = keyOptions.default);
        let normalizeFn = keyOptions.normalize;
        if (normalizeFn) {
            value = normalizeFn(value);
        }
        if (u.isDefined(value)) {
            this.options[key] = value;
        }
        let failKey;
        if ((keyOptions.fail || this.fail) && (failKey = up.fragment.failKey(key))) {
            const failAttrNames = u.compact(u.map(attrNames, this.deriveFailAttrName));
            const failKeyOptions = {
                ...keyOptions,
                attr: failAttrNames,
                fail: false
            };
            this.parse(attrValueFn, failKey, failKeyOptions);
        }
    }
    deriveFailAttrName(attr) {
        if (attr.indexOf('up-') === 0) {
            return `up-fail-${attr.slice(3)}`;
        }
    }
    attrNameForKey(option) {
        return `up-${u.camelToKebabCase(option)}`;
    }
};


/***/ }),
/* 53 */
/***/ (() => {

const e = up.element;
const u = up.util;
up.OverlayFocus = class OverlayFocus {
    constructor(layer) {
        this.layer = layer;
        this.focusElement = this.layer.getFocusElement();
    }
    moveToFront() {
        if (this.enabled) {
            return;
        }
        this.enabled = true;
        this.untrapFocus = up.on('focusin', event => this.onFocus(event));
        this.unsetAttrs = e.setTemporaryAttrs(this.focusElement, {
            // Make layer.element focusable.
            // It would be slightly nicer to give it [tabindex=-1] to make it focusable through JS,
            // but remove it from the keyboard tab sequence. However, then we would need additional
            // code to prevent an infinite loop between focus traps in an overlay that has no
            // focusable elements.
            'tabindex': '0',
            // Make screen readers speak "dialog field" as we focus layer.element.
            'role': 'dialog',
            // Tell modern screen readers to make all elements outside layer.element's subtree inert.
            'aria-modal': 'true'
        });
        this.focusTrapBefore = e.affix(this.focusElement, 'beforebegin', 'up-focus-trap[tabindex=0]');
        this.focusTrapAfter = e.affix(this.focusElement, 'afterend', 'up-focus-trap[tabindex=0]');
    }
    moveToBack() {
        this.teardown();
    }
    teardown() {
        if (!this.enabled) {
            return;
        }
        this.enabled = false;
        this.untrapFocus();
        // Remove [aria-modal] attribute to not confuse screen readers with multiple
        // mutually exclusive [aria-modal] layers.
        this.unsetAttrs();
        e.remove(this.focusTrapBefore);
        e.remove(this.focusTrapAfter);
    }
    onFocus(event) {
        const { target } = event;
        // Ignore focus events triggered by this method.
        if (this.processingFocusEvent) {
            return;
        }
        this.processingFocusEvent = true;
        if (target === this.focusTrapBefore) {
            // User shift-tabbed from the first focusable element in the overlay.
            // Focus pierced through the layer the the beginning.
            // We want to wrap around and focus the end of the overlay.
            this.focusEnd();
        }
        else if ((target === this.focusTrapAfter) || !this.layer.contains(target)) {
            // User tabbed from the last focusable element in the overlay
            // OR user moved their virtual cursor on an element outside the layer.
            // We want to to trap focus and focus the start of the overlay.
            this.focusStart();
        }
        this.processingFocusEvent = false;
    }
    focusStart(focusOptions) {
        // Focusing the overlay element with its [role=dialog] will read out
        // "dialog field" in many screen readers.
        up.focus(this.focusElement, focusOptions);
    }
    focusEnd() {
        // The end will usually be the dismiss button, if there is one.
        // Otherwise it will be the last focusable element.
        // We focus on the box element since focusing on the layer container
        // would include the viewport, which is focusable due to scroll bars.
        this.focusLastDescendant(this.layer.getBoxElement()) || this.focusStart();
    }
    focusLastDescendant(element) {
        // Don't use forEach since we need to break out of the loop with `return`
        for (let child of u.reverse(element.children)) {
            if (up.viewport.tryFocus(child) || this.focusLastDescendant(child)) {
                return true;
            }
        }
    }
};


/***/ }),
/* 54 */
/***/ (() => {

const u = up.util;
const e = up.element;
/*-
The `up.Params` class offers a consistent API to read and manipulate request parameters
independent of their type.

Request parameters are used in [form submissions](/up.Params.fromForm) and
[URLs](/up.Params.fromURL). Methods like `up.submit()` or `up.replace()` accept
request parameters as a `{ params }` option.

### Supported parameter types

The following types of parameter representation are supported:

1. An object like `{ email: 'foo@bar.com' }`
2. A query string like `'email=foo%40bar.com'`
3. An array of `{ name, value }` objects like `[{ name: 'email', value: 'foo@bar.com' }]`
4. A [FormData](https://developer.mozilla.org/en-US/docs/Web/API/FormData) object.
   On IE 11 and Edge, `FormData` payloads require a [polyfill for `FormData#entries()`](https://github.com/jimmywarting/FormData).

@class up.Params
@parent up.form
*/
up.Params = class Params {
    /*-
    Constructs a new `up.Params` instance.
  
    @constructor up.Params
    @param {Object|Array|string|up.Params} [params]
      An existing list of params with which to initialize the new `up.Params` object.
  
      The given params value may be of any [supported type](/up.Params).
    @return {up.Params}
    @experimental
    */
    constructor(raw) {
        this.clear();
        this.addAll(raw);
    }
    /*-
    Removes all params from this object.
  
    @function up.Params#clear
    @experimental
    */
    clear() {
        this.entries = [];
    }
    [u.copy.key]() {
        return new up.Params(this);
    }
    /*-
    Returns an object representation of this `up.Params` instance.
  
    The returned value is a simple JavaScript object with properties
    that correspond to the key/values in the given `params`.
  
    ### Example
  
        var params = new up.Params('foo=bar&baz=bam')
        var object = params.toObject()
  
        // object is now: {
        //   foo: 'bar',
        //   baz: 'bam'
        // ]
  
    @function up.Params#toObject
    @return {Object}
    @experimental
    */
    toObject() {
        const obj = {};
        for (let entry of this.entries) {
            const { name, value } = entry;
            if (!u.isBasicObjectProperty(name)) {
                if (this.isArrayKey(name)) {
                    obj[name] || (obj[name] = []);
                    obj[name].push(value);
                }
                else {
                    obj[name] = value;
                }
            }
        }
        return obj;
    }
    /*-
    Returns an array representation of this `up.Params` instance.
  
    The returned value is a JavaScript array with elements that are objects with
    `{ key }` and `{ value }` properties.
  
    ### Example
  
        var params = new up.Params('foo=bar&baz=bam')
        var array = params.toArray()
  
        // array is now: [
        //   { name: 'foo', value: 'bar' },
        //   { name: 'baz', value: 'bam' }
        // ]
  
    @function up.Params#toArray
    @return {Array}
    @experimental
    */
    toArray() {
        return this.entries;
    }
    /*-
    Returns a [`FormData`](https://developer.mozilla.org/en-US/docs/Web/API/FormData) representation
    of this `up.Params` instance.
  
    ### Example
  
        var params = new up.Params('foo=bar&baz=bam')
        var formData = params.toFormData()
  
        formData.get('foo') // 'bar'
        formData.get('baz') // 'bam'
  
    @function up.Params#toFormData
    @return {FormData}
    @experimental
    */
    toFormData() {
        const formData = new FormData();
        for (let entry of this.entries) {
            formData.append(entry.name, entry.value);
        }
        if (!formData.entries) {
            // If this browser cannot inspect FormData with the #entries()
            // iterator, assign the original array for inspection by specs.
            formData.originalArray = this.entries;
        }
        return formData;
    }
    /*-
    Returns an [query string](https://en.wikipedia.org/wiki/Query_string) for this `up.Params` instance.
  
    The keys and values in the returned query string will be [percent-encoded](https://developer.mozilla.org/en-US/docs/Glossary/percent-encoding).
    Non-primitive values (like [`File`](https://developer.mozilla.org/en-US/docs/Web/API/File) will be omitted from
    the retuned query string.
  
    ### Example
  
        var params = new up.Params({ foo: 'bar', baz: 'bam' })
        var query = params.toQuery()
  
        // query is now: 'foo=bar&baz=bam'
  
    @function up.Params#toQuery
    @param {Object|FormData|string|Array|undefined} params
      the params to convert
    @return {string}
      a query string built from the given params
    @experimental
    */
    toQuery() {
        let parts = u.map(this.entries, this.arrayEntryToQuery.bind(this));
        parts = u.compact(parts);
        return parts.join('&');
    }
    arrayEntryToQuery(entry) {
        const { value } = entry;
        // We cannot transpot a binary value in a query string.
        if (this.isBinaryValue(value)) {
            return;
        }
        let query = encodeURIComponent(entry.name);
        // There is a subtle difference when encoding blank values:
        // 1. An undefined or null value is encoded to `key` with no equals sign
        // 2. An empty string value is encoded to `key=` with an equals sign but no value
        if (u.isGiven(value)) {
            query += "=";
            query += encodeURIComponent(value);
        }
        return query;
    }
    /*-
    Returns whether the given value cannot be encoded into a query string.
  
    We will have `File` values in our params when we serialize a form with a file input.
    These entries will be filtered out when converting to a query string.
  
    @function up.Params#isBinaryValue
    @internal
    */
    isBinaryValue(value) {
        return value instanceof Blob;
    }
    hasBinaryValues() {
        const values = u.map(this.entries, 'value');
        return u.some(values, this.isBinaryValue);
    }
    /*-
    Builds an URL string from the given base URL and
    this `up.Params` instance as a [query string](https://en.wikipedia.org/wiki/Query_string).
  
    The base URL may or may not already contain a query string. The
    additional query string will be joined with an `&` or `?` character accordingly.
  
    @function up.Params#toURL
    @param {string} base
      The base URL that will be prepended to this `up.Params` object as a query string.
    @return {string}
      The built URL.
    @experimental
    */
    toURL(base) {
        let parts = [base, this.toQuery()];
        parts = u.filter(parts, u.isPresent);
        const separator = u.contains(base, '?') ? '&' : '?';
        return parts.join(separator);
    }
    /*-
    Adds a new entry with the given `name` and `value`.
  
    An `up.Params` instance can hold multiple entries with the same name.
    To overwrite all existing entries with the given `name`, use `up.Params#set()` instead.
  
    ### Example
  
        var params = new up.Params()
        params.add('foo', 'fooValue')
  
        var foo = params.get('foo')
        // foo is now 'fooValue'
  
    @function up.Params#add
    @param {string} name
      The name of the new entry.
    @param {any} value
      The value of the new entry.
    @experimental
    */
    add(name, value) {
        this.entries.push({ name, value });
    }
    /*-
    Adds all entries from the given list of params.
  
    The given params value may be of any [supported type](/up.Params).
  
    @function up.Params#addAll
    @param {Object|Array|string|up.Params|undefined} params
    @experimental
    */
    addAll(raw) {
        if (u.isMissing(raw)) {
            // nothing to do
        }
        else if (raw instanceof this.constructor) {
            this.entries.push(...raw.entries);
        }
        else if (u.isArray(raw)) {
            // internal use for copying
            this.entries.push(...raw);
        }
        else if (u.isString(raw)) {
            this.addAllFromQuery(raw);
        }
        else if (u.isFormData(raw)) {
            this.addAllFromFormData(raw);
        }
        else if (u.isObject(raw)) {
            this.addAllFromObject(raw);
        }
        else {
            up.fail("Unsupport params type: %o", raw);
        }
    }
    addAllFromObject(object) {
        for (let key in object) {
            const value = object[key];
            const valueElements = u.isArray(value) ? value : [value];
            for (let valueElement of valueElements) {
                this.add(key, valueElement);
            }
        }
    }
    addAllFromQuery(query) {
        for (let part of query.split('&')) {
            if (part) {
                let [name, value] = part.split('=');
                name = decodeURIComponent(name);
                // There are three forms we need to handle:
                // (1) foo=bar should become { name: 'foo', bar: 'bar' }
                // (2) foo=    should become { name: 'foo', bar: '' }
                // (3) foo     should become { name: 'foo', bar: null }
                if (u.isGiven(value)) {
                    value = decodeURIComponent(value);
                }
                else {
                    value = null;
                }
                this.add(name, value);
            }
        }
    }
    addAllFromFormData(formData) {
        // IE11: Remove eachIterator and just use for .. of
        u.eachIterator(formData.entries(), value => {
            this.add(...value);
        });
    }
    /*-
    Sets the `value` for the entry with given `name`.
  
    An `up.Params` instance can hold multiple entries with the same name.
    All existing entries with the given `name` are [deleted](/up.Params.prototype.delete) before the
    new entry is set. To add a new entry even if the `name` is taken, use `up.Params#add()`.
  
    @function up.Params#set
    @param {string} name
      The name of the entry to set.
    @param {any} value
      The new value of the entry.
    @experimental
    */
    set(name, value) {
        this.delete(name);
        this.add(name, value);
    }
    /*-
    Deletes all entries with the given `name`.
  
    @function up.Params#delete
    @param {string} name
    @experimental
    */
    delete(name) {
        this.entries = u.reject(this.entries, this.matchEntryFn(name));
    }
    matchEntryFn(name) {
        return entry => entry.name === name;
    }
    /*-
    Returns the first param value with the given `name` from the given `params`.
  
    Returns `undefined` if no param value with that name is set.
  
    If the `name` denotes an array field (e.g. `foo[]`), *all* param values with the given `name`
    are returned as an array. If no param value with that array name is set, an empty
    array is returned.
  
    To always return a single value use `up.Params#getFirst()` instead.
    To always return an array of values use `up.Params#getAll()` instead.
  
    ### Example
  
        var params = new up.Params({ foo: 'fooValue', bar: 'barValue' })
        var params = new up.Params([
          { name: 'foo', value: 'fooValue' }
          { name: 'bar[]', value: 'barValue1' }
          { name: 'bar[]', value: 'barValue2' })
        ]})
  
        var foo = params.get('foo')
        // foo is now 'fooValue'
  
        var bar = params.get('bar')
        // bar is now ['barValue1', 'barValue2']
  
    @function up.Params#get
    @param {string} name
    @experimental
    */
    get(name) {
        if (this.isArrayKey(name)) {
            return this.getAll(name);
        }
        else {
            return this.getFirst(name);
        }
    }
    /*-
    Returns the first param value with the given `name`.
  
    Returns `undefined` if no param value with that name is set.
  
    @function up.Params#getFirst
    @param {string} name
    @return {any}
      The value of the param with the given name.
    @experimental
    */
    getFirst(name) {
        const entry = u.find(this.entries, this.matchEntryFn(name));
        return entry?.value;
    }
    /*-
    Returns an array of all param values with the given `name`.
  
    Returns an empty array if no param value with that name is set.
  
    @function up.Params#getAll
    @param {string} name
    @return {Array}
      An array of all values with the given name.
    @experimental
    */
    getAll(name) {
        if (this.isArrayKey(name)) {
            return this.getAll(name);
        }
        else {
            const entries = u.map(this.entries, this.matchEntryFn(name));
            return u.map(entries, 'value');
        }
    }
    isArrayKey(key) {
        return u.endsWith(key, '[]');
    }
    [u.isBlank.key]() {
        return this.entries.length === 0;
    }
    /*-
    Constructs a new `up.Params` instance from the given `<form>`.
  
    The returned params may be passed as `{ params }` option to
    `up.request()` or `up.replace()`.
  
    The constructed `up.Params` will include exactly those form values that would be
    included in a regular form submission. In particular:
  
    - All `<input>` types are suppported
    - Field values are usually strings, but an `<input type="file">` will produce
      [`File`](https://developer.mozilla.org/en-US/docs/Web/API/File) values.
    - An `<input type="radio">` or `<input type="checkbox">` will only be added if they are `[checked]`.
    - An `<select>` will only be added if at least one value is `[checked]`.
    - If passed a `<select multiple>` or `<input type="file" multiple>`, all selected values are added.
      If passed a `<select multiple>`, all selected values are added.
    - Fields that are `[disabled]` are ignored
    - Fields without a `[name]` attribute are ignored.
  
    ### Example
  
    Given this HTML form:
  
        <form>
          <input type="text" name="email" value="foo@bar.com">
          <input type="password" name="pass" value="secret">
        </form>
  
    This would serialize the form into an array representation:
  
        var params = up.Params.fromForm('input[name=email]')
        var email = params.get('email') // email is now 'foo@bar.com'
        var pass = params.get('pass') // pass is now 'secret'
  
    @function up.Params.fromForm
    @param {Element|jQuery|string} form
      A `<form>` element or a selector that matches a `<form>` element.
    @return {up.Params}
      A new `up.Params` instance with values from the given form.
    @experimental
    */
    static fromForm(form) {
        // If passed a selector, up.fragment.get() will prefer a match on the current layer.
        form = up.fragment.get(form);
        return this.fromFields(up.form.fields(form));
    }
    /*-
    Constructs a new `up.Params` instance from one or more
    [HTML form field](https://www.w3schools.com/html/html_form_elements.asp).
  
    The constructed `up.Params` will include exactly those form values that would be
    included for the given fields in a regular form submission. If a given field wouldn't
    submit a value (like an unchecked `<input type="checkbox">`, nothing will be added.
  
    See `up.Params.fromForm()` for more details and examples.
  
    @function up.Params.fromFields
    @param {Element|List<Element>|jQuery} fields
    @return {up.Params}
    @experimental
    */
    static fromFields(fields) {
        const params = new (this)();
        for (let field of u.wrapList(fields)) {
            params.addField(field);
        }
        return params;
    }
    /*-
    Adds params from the given [HTML form field](https://www.w3schools.com/html/html_form_elements.asp).
  
    The added params will include exactly those form values that would be
    included for the given field in a regular form submission. If the given field wouldn't
      submit a value (like an unchecked `<input type="checkbox">`, nothing will be added.
  
    See `up.Params.fromForm()` for more details and examples.
  
    @function up.Params#addField
    @param {Element|jQuery} field
    @experimental
    */
    addField(field) {
        field = e.get(field); // unwrap jQuery
        // Input fields are excluded from form submissions if they have no [name]
        // or when they are [disabled].
        let name = field.name;
        if (name && !field.disabled) {
            const { tagName } = field;
            const { type } = field;
            if (tagName === 'SELECT') {
                for (let option of field.querySelectorAll('option')) {
                    if (option.selected) {
                        this.add(name, option.value);
                    }
                }
            }
            else if ((type === 'checkbox') || (type === 'radio')) {
                if (field.checked) {
                    this.add(name, field.value);
                }
            }
            else if (type === 'file') {
                // The value of an input[type=file] is the local path displayed in the form.
                // The actual File objects are in the #files property.
                for (let file of field.files) {
                    this.add(name, file);
                }
            }
            else {
                return this.add(name, field.value);
            }
        }
    }
    [u.isEqual.key](other) {
        return (this.constructor === other.constructor) && u.isEqual(this.entries, other.entries);
    }
    /*-
    Constructs a new `up.Params` instance from the given URL's
    [query string](https://en.wikipedia.org/wiki/Query_string).
  
    Constructs an empty `up.Params` instance if the given URL has no query string.
  
    ### Example
  
        var params = up.Params.fromURL('http://foo.com?foo=fooValue&bar=barValue')
        var foo = params.get('foo')
        // foo is now: 'fooValue'
  
    @function up.Params.fromURL
    @param {string} url
      The URL from which to extract the query string.
    @return {string|undefined}
      The given URL's query string, or `undefined` if the URL has no query component.
    @experimental
    */
    static fromURL(url) {
        const params = new (this)();
        const urlParts = u.parseURL(url);
        let query = urlParts.search;
        if (query) {
            query = query.replace(/^\?/, '');
            params.addAll(query);
        }
        return params;
    }
    /*-
    Returns the given URL without its [query string](https://en.wikipedia.org/wiki/Query_string).
  
    ### Example
  
        var url = up.Params.stripURL('http://foo.com?key=value')
        // url is now: 'http://foo.com'
  
    @function up.Params.stripURL
    @param {string} url
      A URL (with or without a query string).
    @return {string}
      The given URL without its query string.
    @experimental
    */
    static stripURL(url) {
        return u.normalizeURL(url, { search: false });
    }
};


/***/ }),
/* 55 */
/***/ (() => {

const e = up.element;
const TRANSITION_DELAY = 300;
up.ProgressBar = class ProgressBar {
    constructor() {
        this.step = 0;
        this.element = e.affix(document.body, 'up-progress-bar');
        this.element.style.transition = `width ${TRANSITION_DELAY}ms ease-out`;
        this.moveTo(0);
        // The element must be painted at width: 0 before we apply the target width.
        // If the first paint sees the bar at the target width, we don't get an animated transition.
        up.element.paint(this.element);
        this.width = 31;
        this.nextStep();
    }
    nextStep() {
        let diff;
        if (this.width < 80) {
            if (Math.random() < 0.15) {
                // Sometimes the bar grows quickly by (7..12) percent.
                diff = 7 + (5 * Math.random());
            }
            else {
                // Most of the time the bar progresses by (1.5..2) percent.
                diff = 1.5 + (0.5 * Math.random());
            }
        }
        else {
            // Above 80% completion we grow the bar more slowly,
            // using a formula that can never reach 100%.
            diff = 0.13 * (100 - this.width) * Math.random();
        }
        this.moveTo(this.width + diff);
        this.step++;
        // Steps occur less frequent the longer we wait for the server.
        const nextStepDelay = TRANSITION_DELAY + (this.step * 40);
        this.timeout = setTimeout(this.nextStep.bind(this), nextStepDelay);
    }
    moveTo(width) {
        this.width = width;
        this.element.style.width = `${width}vw`;
    }
    destroy() {
        clearTimeout(this.timeout);
        e.remove(this.element);
    }
    conclude() {
        clearTimeout(this.timeout);
        this.moveTo(100);
        setTimeout(this.destroy.bind(this), TRANSITION_DELAY);
    }
};


/***/ }),
/* 56 */
/***/ (() => {

const u = up.util;
up.RenderOptions = (function () {
    const GLOBAL_DEFAULTS = {
        hungry: true,
        keep: true,
        source: true,
        saveScroll: true,
        fail: 'auto'
    };
    const PRELOAD_OVERRIDES = {
        solo: false,
        confirm: false,
        feedback: false
    };
    // These properties are used before the request is sent.
    // Hence there cannot be a failVariant.
    const PREFLIGHT_KEYS = [
        'url',
        'method',
        'origin',
        'headers',
        'params',
        'cache',
        'clearCache',
        'fallback',
        'solo',
        'confirm',
        'feedback',
        'origin',
        'baseLayer',
        'fail',
    ];
    // These properties are used between success options and fail options.
    // There's a lot of room to think differently about what should be shared and what
    // should explictely be set separately for both cases. An argument can always be
    // that it's either convenient to share, or better to be explicit.
    //
    // Generally we have decided to share:
    //
    // - Options that are relevant before the request is sent (e.g. { url } or { solo }).
    // - Options that change how we think about the entire rendering operation.
    //   E.g. if we always want to see a server response, we set { fallback: true }.
    //
    // Generally we have decided to not share:
    //
    // - Layer-related options (e.g. target layer or options for a new layer)
    // - Options that change focus. The user might focus a specific element from a success element,
    //   like { focus: '.result', failFocus: '.errors' }.
    // - Options that change focus. The user might scroll to a specific element from a success element,
    //   like { reveal: '.result', failReaveal: '.errors' }.
    const SHARED_KEYS = PREFLIGHT_KEYS.concat([
        'keep',
        'hungry',
        'history',
        'source',
        'saveScroll',
        'navigate' // Also set navigate defaults for fail options
    ]);
    const CONTENT_KEYS = [
        'url',
        'content',
        'fragment',
        'document'
    ];
    // preprocess() will leave out properties for which there may be a better default
    // later, in particular from the layer config in up.Change.OpenLayer.
    const LATE_KEYS = [
        'history',
        'focus',
        'scroll'
    ];
    function navigateDefaults(options) {
        if (options.navigate) {
            return up.fragment.config.navigateOptions;
        }
    }
    function preloadOverrides(options) {
        if (options.preload) {
            return PRELOAD_OVERRIDES;
        }
    }
    function preprocess(options) {
        up.migrate.preprocessRenderOptions?.(options);
        const defaults = u.merge(GLOBAL_DEFAULTS, navigateDefaults(options));
        return u.merge(
        // Leave out properties for which there may be a better default later, in particular
        // from the layer config in up.Change.OpenLayer. If we merged it now we could
        // not distinguish a user option (which always has highest priority) with a
        // default that may be overridden by the layer config. If there is no better default
        // later, the original defaults will be applied in finalize().
        u.omit(defaults, LATE_KEYS), 
        // Remember the defaults in a { default } prop so we can re-use it
        // later in deriveFailOptions() and finalize().
        { defaults }, options, preloadOverrides(options));
    }
    function finalize(preprocessedOptions, lateDefaults) {
        return u.merge(preprocessedOptions.defaults, lateDefaults, preprocessedOptions);
    }
    function assertContentGiven(options) {
        if (!u.some(CONTENT_KEYS, contentKey => u.isGiven(options[contentKey]))) {
            // up.layer.open() should open an empty layer without a content key.
            if (options.defaultToEmptyContent) {
                options.content = '';
            }
            else {
                up.fail('up.render() needs either { ' + CONTENT_KEYS.join(', ') + ' } option');
            }
        }
    }
    function failOverrides(options) {
        const overrides = {};
        for (let key in options) {
            // Note that up.fragment.successKey(key) only returns a value
            // if the given key is prefixed with "fail".
            const value = options[key];
            let unprefixed = up.fragment.successKey(key);
            if (unprefixed) {
                overrides[unprefixed] = value;
            }
        }
        return overrides;
    }
    function deriveFailOptions(preprocessedOptions) {
        return u.merge(preprocessedOptions.defaults, u.pick(preprocessedOptions, SHARED_KEYS), failOverrides(preprocessedOptions));
    }
    return {
        preprocess,
        finalize,
        assertContentGiven,
        deriveFailOptions,
    };
})();


/***/ }),
/* 57 */
/***/ (() => {

/*-
Instances of `up.RenderResult` describe the effects of [rendering](/up.render).

It is returned by functions like `up.render()` or `up.navigate()`:

```js
let result = await up.render('.target', content: 'foo')
console.log(result.fragments) // => [<div class="target">...</div>]
console.log(result.layer)     // => up.Layer.Root
```

@class up.RenderResult
@parent up.fragment
*/
up.RenderResult = class RenderResult extends up.Record {
    /*-
    An array of fragments that were inserted.
  
    @property up.RenderResult#fragments
    @param {Array<Element>} fragments
    @stable
    */
    /*-
    The updated [layer](/up.layer).
  
    @property up.RenderResult#layer
    @param {up.Layer} layer
    @stable
    */
    keys() {
        return [
            'fragments',
            'layer',
        ];
    }
};


/***/ }),
/* 58 */
/***/ (() => {

const u = up.util;
/*-
A normalized description of an [HTTP request](/up.request).

You can queue a request using the `up.request()` method:

```js
let request = up.request('/foo')
console.log(request.url)

// A request object is also a promise for its response
let response = await request
console.log(response.text)
```

@class up.Request
@parent up.network
*/
up.Request = class Request extends up.Record {
    /*-
    The HTTP method for the request.
  
    @property up.Request#method
    @param {string} method
    @stable
    */
    /*-
    The URL for the request.
  
    @property up.Request#url
    @param {string} url
    @stable
    */
    /*-
    The [hash component](https://en.wikipedia.org/wiki/URI_fragment) of this request's URL.
  
    The `{ hash }` property is automatically extracted from the given URL:
  
    ```js
    let request = up.request({ url: '/path#section' })
    request.url // => '/path'
    request.hash // => '#section'
    ```
  
    @property up.Request#hash
    @param {string} hash
    @stable
    */
    /*-
    [Parameters](/up.Params) that should be sent as the request's payload.
  
    @property up.Request#params
    @param {Object|FormData|string|Array} params
    @stable
    */
    /*-
    The CSS selector targeted by this request.
  
    The selector will be sent as an `X-Up-Target` header.
  
    @property up.Request#target
    @param {string} target
    @stable
    */
    /*-
    The CSS selector targeted by this request in case the server responds
    with an [error code](/server-errors).
  
    The selector will be sent as an `X-Up-Fail-Target` header.
  
    @property up.Request#failTarget
    @param {string} failTarget
    @stable
    */
    /*-
    An object of additional HTTP headers.
  
    Unpoly will by default send a number of custom request headers.
    See `up.protocol` and `up.network.config.requestMetaKeys` for details.
  
    @property up.Request#headers
    @param {Object} headers
    @stable
    */
    /*-
    A timeout in milliseconds.
  
    If the request is queued due to [many concurrent requests](/up.network.config#config.concurrency),
    the timeout will not include the time spent waiting in the queue.
  
    @property up.Request#timeout
    @param {Object|undefined} timeout
    @stable
    */
    /*-
    Whether to wrap non-standard HTTP methods in a POST request.
  
    If this is set, methods other than GET and POST will be converted to a `POST` request
    and carry their original method as a `_method` parameter. This is to [prevent unexpected redirect behavior](https://makandracards.com/makandra/38347).
  
    Defaults to [`up.network.config`](/up.network.config#config.wrapMethod).
  
    @property up.Request#wrapMethod
    @param {boolean} wrapMethod
    @stable
    */
    /*-
    The [context](/context) of the layer targeted by this request.
  
    The context object will be sent as an `X-Up-Context` header.
  
    @property up.Request#context
    @param {Object} context
    @experimental
    */
    /*-
    The [context](/context) of the layer targeted by this request in case the server responds with an [error code](/server-errors).
  
    The context object will be sent as an `X-Up-Fail-Context` header.
  
    @property up.Request#failContext
    @param {Object} failContext
    @experimental
    */
    /*-
    The [layer](/up.layer) targeted by this request.
  
    Setting the `{ layer }` property will automatically derive `{ context }` and `{ mode }` properties.
  
    To prevent memory leaks, this property is removed shortly after the response is received.
  
    @property up.Request#layer
    @param {up.Layer} layer
    @experimental
    */
    /*-
    The [layer](/up.layer) targeted by this request in case the server responds with an [error code](/server-errors).
  
    Setting the `{ failLayer }` property will automatically derive `{ failContext }` and `{ failMode }` properties.
  
    To prevent memory leaks, this property is removed shortly after the response is received.
  
    @property up.Request#failLayer
    @param {up.Layer} layer
    @experimental
    */
    /*-
    The element that triggered the request.
  
    For example, when this request was triggered by a click on a link, the link
    element is set as the `{ origin }`.
  
    To prevent memory leaks, this property is removed shortly after the response is received.
  
    @property up.Request#origin
    @param {Element} origin
    @experimental
    */
    /*-
    The [mode](/up.Layer.prototype.mode) of the layer targeted by this request.
  
    The value will be sent as an `X-Up-Mode` header.
  
    @property up.Request#mode
    @param {string} mode
    @stable
    */
    /*-
    The [mode](/up.Layer.prototype.mode) of the layer targeted by this request in case the server responds with an [error code](/server-errors).
  
    The value will be sent as an `X-Up-Fail-Mode` header.
  
    @property up.Request#failMode
    @param {string} failMode
    @stable
    */
    /*-
    The format in which the [request params](/up.Request.prototype.params) will be encoded.
  
    @property up.Request#contentType
    @param {string} contentType
    @stable
    */
    /*-
    The payload that the request will encode into its body.
  
    By default Unpoly will build a payload from the given `{ params }` option.
  
    @property up.Request#payload
    @param {string} payload
    @stable
    */
    /*-
    @property up.Request#preload
    @param {boolean} preload
    @experimental
    */
    keys() {
        return [
            // 'signal',
            'method',
            'url',
            'hash',
            'params',
            'target',
            'failTarget',
            'headers',
            'timeout',
            'preload',
            'cache',
            'clearCache',
            // While requests are queued or in flight we keep the layer they're targeting.
            // If that layer is closed we will cancel all pending requests targeting that layer.
            // Note that when opening a new layer, this { layer } attribute will be the set to
            // the current layer. The { mode } and { failMode } attributes will belong to the
            // new layer being opened.
            'layer',
            'mode',
            'context',
            'failLayer',
            'failMode',
            'failContext',
            'origin',
            'solo',
            'queueTime',
            'wrapMethod',
            'contentType',
            'payload',
            'onQueued'
        ];
    }
    /*-
    Creates a new `up.Request` object.
  
    This will not actually send the request over the network. For that use `up.request()`.
  
    @constructor up.Request
    @param {string} attrs.url
    @param {string} [attrs.method='get']
    @param {up.Params|string|Object|Array} [attrs.params]
    @param {string} [attrs.target]
    @param {string} [attrs.failTarget]
    @param {Object<string, string>} [attrs.headers]
    @param {number} [attrs.timeout]
    @internal
    */
    constructor(options) {
        super(options);
        this.params = new up.Params(this.params); // copies, which we want
        this.headers || (this.headers = {});
        if (this.preload) {
            // Preloading requires caching.
            this.cache = true;
        }
        if (this.wrapMethod == null) {
            this.wrapMethod = up.network.config.wrapMethod;
        }
        // Normalize a first time to get a normalized cache key.
        this.normalizeForCaching();
        if (!options.basic) {
            const layerLookupOptions = { origin: this.origin };
            // Calling up.layer.get() will give us:
            //
            // (1) Resolution of strings like 'current' to an up.Layer instance
            // (2) Default of origin's layer
            // (3) Default of up.layer.current
            //
            // up.layer.get('new') will return 'new' unchanged, but I'm not sure
            // if any code actually calls up.request({ ..., layer: 'new' }).
            // In up.Change.OpenLayer we connect requests to the base layer we're stacking upon.
            this.layer = up.layer.get(this.layer, layerLookupOptions);
            this.failLayer = up.layer.get(this.failLayer || this.layer, layerLookupOptions);
            this.context || (this.context = this.layer.context || {}); // @layer might be "new", so we default to {}
            this.failContext || (this.failContext = this.failLayer.context || {}); // @failLayer might be "new", so we default to {}
            this.mode || (this.mode = this.layer.mode);
            this.failMode || (this.failMode = this.failLayer.mode);
            // This up.Request object is also promise for its up.Response.
            // We delegate all promise-related methods (then, catch, finally) to an internal
            // deferred object.
            this.deferred = u.newDeferred();
            this.state = 'new';
        }
    }
    followState(sourceRequest) {
        u.delegate(this, ['deferred', 'state', 'preload'], () => sourceRequest);
    }
    normalizeForCaching() {
        this.method = u.normalizeMethod(this.method);
        this.extractHashFromURL();
        this.transferParamsToURL();
        // This consistently strips the hostname from same-origin requests.
        this.url = u.normalizeURL(this.url);
    }
    evictExpensiveAttrs() {
        // We want to allow up:request:loaded events etc. to still access the properties that
        // we are about to evict, so we wait for one more frame. It shouldn't matter for GC.
        u.task(() => {
            // While the request is still in flight, we require the target layer
            // to be able to cancel it when the layers gets closed. We now
            // evict this property, since response.request.layer.element will
            // prevent the layer DOM tree from garbage collection while the response
            // is cached by up.network.
            this.layer = undefined;
            this.failLayer = undefined;
            // We want to provide the triggering element as { origin } to the function
            // providing the CSRF function. We now evict this property, since
            // response.request.origin will prevent its (now maybe detached) DOM tree
            // from garbage collection while the response is cached by up.network.
            return this.origin = undefined;
        });
    }
    // Don't evict properties that may be part of our @cacheKey()!
    extractHashFromURL() {
        let match = this.url?.match(/^([^#]*)(#.+)$/);
        if (match) {
            this.url = match[1];
            // Remember the #hash for later revealing.
            return this.hash = match[2];
        }
    }
    transferParamsToURL() {
        if (!this.url || this.allowsPayload() || u.isBlank(this.params)) {
            return;
        }
        // GET methods are not allowed to have a payload, so we transfer { params } params to the URL.
        this.url = this.params.toURL(this.url);
        // Now that we have transfered the params into the URL, we delete them from the { params } option.
        this.params.clear();
    }
    isSafe() {
        return up.network.isSafeMethod(this.method);
    }
    allowsPayload() {
        return u.methodAllowsPayload(this.method);
    }
    will302RedirectWithGET() {
        return this.isSafe() || (this.method === 'POST');
    }
    willCache() {
        if (this.cache === 'auto') {
            return up.network.config.autoCache(this);
        }
        else {
            return this.cache;
        }
    }
    runQueuedCallbacks() {
        u.always(this, () => this.evictExpensiveAttrs());
        this.onQueued?.(this);
    }
    // @signal?.addEventListener('abort', => @abort())
    load() {
        // If the request was aborted before it was sent (e.g. because it was queued)
        // we don't send it.
        if (this.state !== 'new') {
            return;
        }
        this.state = 'loading';
        // Convert from XHR's callback-based API to up.Request's promise-based API
        this.xhr = new up.Request.XHRRenderer(this).buildAndSend({
            onload: () => this.onXHRLoad(),
            onerror: () => this.onXHRError(),
            ontimeout: () => this.onXHRTimeout(),
            onabort: () => this.onXHRAbort()
        });
    }
    /*-
    Loads this request object as a full-page request, replacing the entire browser environment
    with a new page from the server response.
  
    The full-page request will be loaded with the [URL](/up.Request.prototype.url),
    [method](/up.Request.prototype.method) and [params](/up.Request.prototype.params)
    from this request object.
    Properties that are not possible in a full-page request (such as custom HTTP headers)
    will be ignored.
  
    ### Example
  
    ```javascript
    let request = await up.request('/path')
  
    try {
      let response = await request('/path')
    } catch (result) {
      if (result.name === 'AbortError') {
        console.log('Request was aborted.')
      }
    }
  
    request.abort()
    ```
  
    @function up.Request#loadPage
    @experimental
    */
    loadPage() {
        // This method works independently of @state, since it is often
        // a fallback for a request that cannot be processed as a fragment update
        // (see up:fragment:loaded event).
        // Abort all pending requests so their callbacks won't run
        // while we're already navigating away.
        up.network.abort();
        new up.Request.FormRenderer(this).buildAndSubmit();
    }
    onXHRLoad() {
        const response = this.extractResponseFromXHR();
        const log = ['Server responded HTTP %d to %s %s (%d characters)', response.status, this.method, this.url, response.text.length];
        this.emit('up:request:loaded', { request: response.request, response, log });
        this.respondWith(response);
    }
    onXHRError() {
        // Neither XHR nor fetch() provide any meaningful error message.
        // Hence we ignore the passed ProgressEvent and use our own error message.
        const log = 'Fatal error during request';
        this.deferred.reject(up.error.failed(log));
        this.emit('up:request:fatal', { log });
    }
    onXHRTimeout() {
        // We treat a timeout like a client-side abort (which it is).
        this.setAbortedState('Requested timed out');
    }
    onXHRAbort() {
        // Use the default message that callers of request.abort() would also get.
        this.setAbortedState();
    }
    /*-
    Aborts this request.
  
    The request's promise will reject with an error object that has `{ name: 'AbortError' }`.
  
    ### Example
  
    ```javascript
    let request = await up.request('/path')
  
    try {
      let response = await request('/path')
    } catch (result) {
      if (result.name === 'AbortError') {
        console.log('Request was aborted.')
      }
    }
  
    request.abort()
    ```
  
    @function up.Request#abort
    @experimental
    */
    abort() {
        // setAbortedState() must be called before xhr.abort(), since xhr's event handlers
        // will call setAbortedState() a second time, without a message.
        if (this.setAbortedState() && this.xhr) {
            this.xhr.abort();
        }
    }
    setAbortedState(reason = ["Request to %s %s was aborted", this.method, this.url]) {
        if ((this.state !== 'new') && (this.state !== 'loading')) {
            return;
        }
        this.state = 'aborted';
        this.emit('up:request:aborted', { log: reason });
        this.deferred.reject(up.error.aborted(reason));
        // Return true so callers know we didn't return early without actually aborting anything.
        return true;
    }
    respondWith(response) {
        if (this.state !== 'loading') {
            return;
        }
        this.state = 'loaded';
        if (response.ok) {
            return this.deferred.resolve(response);
        }
        else {
            return this.deferred.reject(response);
        }
    }
    csrfHeader() {
        return up.protocol.csrfHeader();
    }
    csrfParam() {
        return up.protocol.csrfParam();
    }
    // Returns a csrfToken if this request requires it
    csrfToken() {
        if (!this.isSafe() && !this.isCrossOrigin()) {
            return up.protocol.csrfToken();
        }
    }
    isCrossOrigin() {
        return u.isCrossOrigin(this.url);
    }
    extractResponseFromXHR() {
        const responseAttrs = {
            method: this.method,
            url: this.url,
            request: this,
            xhr: this.xhr,
            text: this.xhr.responseText,
            status: this.xhr.status,
            title: up.protocol.titleFromXHR(this.xhr),
            target: up.protocol.targetFromXHR(this.xhr),
            acceptLayer: up.protocol.acceptLayerFromXHR(this.xhr),
            dismissLayer: up.protocol.dismissLayerFromXHR(this.xhr),
            eventPlans: up.protocol.eventPlansFromXHR(this.xhr),
            context: up.protocol.contextFromXHR(this.xhr),
            clearCache: up.protocol.clearCacheFromXHR(this.xhr)
        };
        let methodFromResponse = up.protocol.methodFromXHR(this.xhr);
        let urlFromResponse = up.protocol.locationFromXHR(this.xhr);
        if (urlFromResponse) {
            // On browsers other than IE11 we can ask the XHR object for its { responseURL },
            // which contains the final URL after redirects. The server may also use the
            // custom X-Up-Location header to signal the final URL for all browsers.
            //
            // Unfortunately we cannot ask the XHR object for its response method.
            // The server may use the custom X-Up-Method for that. If that header is missing
            // AND the URLs changed between request and response, we assume GET.
            if (!methodFromResponse && !u.matchURLs(responseAttrs.url, urlFromResponse)) {
                methodFromResponse = 'GET';
            }
            responseAttrs.url = urlFromResponse;
        }
        if (methodFromResponse) {
            responseAttrs.method = methodFromResponse;
        }
        return new up.Response(responseAttrs);
    }
    cacheKey() {
        return JSON.stringify([
            this.method,
            this.url,
            this.params.toQuery(),
            // If we send a meta prop to the server it must also become part of our cache key,
            // given that server might send a different response based on these props.
            this.metaProps()
        ]);
    }
    // Returns an object like { target: '...', mode: '...' } that will
    // (1) be sent to the server so it can optimize responses and
    // (2) become part of our @cacheKey().
    metaProps() {
        const props = {};
        for (let key of u.evalOption(up.network.config.requestMetaKeys, this)) {
            const value = this[key];
            if (u.isGiven(value)) {
                props[key] = value;
            }
        }
        return props;
    }
    buildEventEmitter(args) {
        // We prefer emitting request-related events on the targeted layer.
        // This way listeners can observe event-related events on a given layer.
        // This request has an optional { layer } attribute, which is used by
        // EventEmitter.
        return up.EventEmitter.fromEmitArgs(args, {
            layer: this.layer,
            request: this,
            origin: this.origin
        });
    }
    emit(...args) {
        return this.buildEventEmitter(args).emit();
    }
    assertEmitted(...args) {
        this.buildEventEmitter(args).assertEmitted();
    }
    get description() {
        return this.method + ' ' + this.url;
    }
};
// A request is also a promise ("thenable") for its response.
u.delegate(up.Request.prototype, ['then', 'catch', 'finally'], function () { return this.deferred; });
up.Request.tester = function (condition) {
    if (u.isFunction(condition)) {
        return condition;
    }
    else if (condition instanceof this) {
        return (request) => condition === request;
    }
    else if (u.isString(condition)) {
        let pattern = new up.URLPattern(condition);
        return (request) => pattern.test(request.url);
    }
    else { // boolean, truthy/falsy values
        return (_request) => condition;
    }
};


/***/ }),
/* 59 */
/***/ (() => {

let u = up.util;
up.Request.Cache = class Cache extends up.Cache {
    maxSize() {
        return up.network.config.cacheSize;
    }
    expiryMillis() {
        return up.network.config.cacheExpiry;
    }
    normalizeStoreKey(request) {
        return u.wrapValue(up.Request, request).cacheKey();
    }
    //  get: (request) ->
    //    request = up.Request.wrap(request)
    //    candidates = [request]
    //
    //    if target = request.target
    //      unless /^html[\[$]/.test(target)
    //        # Since <html> is the root tag, a request for the `html` selector
    //        # will contain all other selectors.
    //        candidates.push(request.variant(target: 'html'))
    //
    //      unless /[^, >#](html|meta|body|title|style|script)[\[\.,# >$]/.test(target)
    //        # Although <body> is not the root tag, we consider it the selector developers
    //        # will use when they want to replace the entire page. Hence we consider it
    //        # a suitable match for all other selectors, excluding `html`.
    //        candidates.push(request.variant(target: 'body'))
    //
    //    u.findResult candidates, (candidate) => super(candidate)
    clear(condition = true) {
        let tester = up.Request.tester(condition);
        this.each((key, request) => {
            if (tester(request)) {
                // It is generally not a great idea to manipulate the list we're iterating over,
                // but the implementation of up.Cache#each copies keys before iterating.
                this.store.remove(key);
            }
        });
    }
};


/***/ }),
/* 60 */
/***/ (() => {

const u = up.util;
up.Request.Queue = class Queue {
    constructor(options = {}) {
        this.concurrency = options.concurrency ?? (() => up.network.config.concurrency);
        this.badResponseTime = options.badResponseTime ?? (() => up.network.config.badResponseTime);
        this.reset();
    }
    reset() {
        this.queuedRequests = [];
        this.currentRequests = [];
        clearTimeout(this.checkSlowTimout);
        this.emittedSlow = false;
    }
    get allRequests() {
        return this.currentRequests.concat(this.queuedRequests);
    }
    asap(request) {
        request.runQueuedCallbacks();
        u.always(request, responseOrError => this.onRequestSettled(request, responseOrError));
        // When considering whether a request is "slow", we're measing the duration between { queueTime }
        // and the moment when the request gets settled. Note that when setSlowTimer() occurs, it will
        // make its own check whether a request in the queue is considered slow.
        request.queueTime = new Date();
        this.setSlowTimer();
        if (this.hasConcurrencyLeft()) {
            this.sendRequestNow(request);
        }
        else {
            this.queueRequest(request);
        }
    }
    // Changes a preload request to a non-preload request.
    // Does not change the request's position in the queue.
    // Does nothing if the given request is not a preload request.
    promoteToForeground(request) {
        if (request.preload) {
            request.preload = false;
            return this.setSlowTimer();
        }
    }
    setSlowTimer() {
        const badResponseTime = u.evalOption(this.badResponseTime);
        this.checkSlowTimout = u.timer(badResponseTime, () => this.checkSlow());
    }
    hasConcurrencyLeft() {
        const maxConcurrency = u.evalOption(this.concurrency);
        return (maxConcurrency === -1) || (this.currentRequests.length < maxConcurrency);
    }
    isBusy() {
        return this.currentRequests.length > 0;
    }
    queueRequest(request) {
        // Queue the request at the end of our FIFO queue.
        this.queuedRequests.push(request);
    }
    pluckNextRequest() {
        // We always prioritize foreground requests over preload requests.
        // Only when there is no foreground request left in the queue we will send a preload request.
        // Note that if a queued preload request is requested without { preload: true } we will
        // promote it to the foreground (see @promoteToForeground()).
        let request = u.find(this.queuedRequests, request => !request.preload);
        request || (request = this.queuedRequests[0]);
        return u.remove(this.queuedRequests, request);
    }
    sendRequestNow(request) {
        if (request.emit('up:request:load', { log: ['Loading %s %s', request.method, request.url] }).defaultPrevented) {
            request.abort('Prevented by event listener');
        }
        else {
            // Since up:request:load listeners may have mutated properties used in
            // the request's cache key ({ url, method, params }), we need to normalize
            // again. Normalizing e.g. moves the params into the URL for GET requests.
            request.normalizeForCaching();
            this.currentRequests.push(request);
            request.load();
        }
    }
    onRequestSettled(request, responseOrError) {
        u.remove(this.currentRequests, request);
        if ((responseOrError instanceof up.Response) && responseOrError.ok) {
            up.network.registerAliasForRedirect(request, responseOrError);
        }
        // Check if we can emit up:request:recover after a previous up:request:late event.
        this.checkSlow();
        u.microtask(() => this.poke());
    }
    poke() {
        let request;
        if (this.hasConcurrencyLeft() && (request = this.pluckNextRequest())) {
            return this.sendRequestNow(request);
        }
    }
    // Aborting a request will cause its promise to reject, which will also uncache it
    abort(conditions = true) {
        let tester = up.Request.tester(conditions);
        for (let list of [this.currentRequests, this.queuedRequests]) {
            const abortableRequests = u.filter(list, tester);
            abortableRequests.forEach(function (abortableRequest) {
                abortableRequest.abort();
                // Avoid changing the list we're iterating over.
                u.remove(list, abortableRequest);
            });
        }
    }
    abortExcept(excusedRequest, additionalConditions = true) {
        const excusedCacheKey = excusedRequest.cacheKey();
        this.abort(queuedRequest => (queuedRequest.cacheKey() !== excusedCacheKey) && u.evalOption(additionalConditions, queuedRequest));
    }
    checkSlow() {
        const currentSlow = this.isSlow();
        if (this.emittedSlow !== currentSlow) {
            this.emittedSlow = currentSlow;
            if (currentSlow) {
                up.emit('up:request:late', { log: 'Server is slow to respond' });
            }
            else {
                up.emit('up:request:recover', { log: 'Slow requests were loaded' });
            }
        }
    }
    isSlow() {
        const now = new Date();
        const delay = u.evalOption(this.badResponseTime);
        const allForegroundRequests = u.reject(this.allRequests, 'preload');
        // If badResponseTime is 200, we're scheduling the checkSlow() timer after 200 ms.
        // The request must be slow when checkSlow() is called, or we will never look
        // at it again. Since the JavaScript setTimeout() is inaccurate, we allow a request
        // to "be slow" a few ms earlier than actually configured.
        const timerTolerance = 1;
        return u.some(allForegroundRequests, request => (now - request.queueTime) >= (delay - timerTolerance));
    }
};


/***/ }),
/* 61 */
/***/ (() => {

const u = up.util;
const e = up.element;
// In HTML5, forms may only have a GET or POST method.
// There were several proposals to extend this to PUT, DELETE, etc.
// but they have all been abandoned.
const HTML_FORM_METHODS = ['GET', 'POST'];
up.Request.FormRenderer = class FormRenderer {
    constructor(request) {
        this.request = request;
    }
    buildAndSubmit() {
        this.params = u.copy(this.request.params);
        let action = this.request.url;
        let { method } = this.request;
        // GET forms cannot have an URL with a query section in their [action] attribute.
        // The query section would be overridden by the serialized input values on submission.
        const paramsFromQuery = up.Params.fromURL(action);
        this.params.addAll(paramsFromQuery);
        action = up.Params.stripURL(action);
        if (!u.contains(HTML_FORM_METHODS, method)) {
            // HTML forms can only have a GET or POST method. Other HTTP methods will be converted
            // to a `POST` request and carry their original method as a `_method` parameter.
            method = up.protocol.wrapMethod(method, this.params);
        }
        this.form = e.affix(document.body, 'form.up-request-loader', { method, action });
        // We only need an [enctype] attribute if the user has explicitly
        // requested one. If none is given, we can use the browser's default
        // [enctype]. Binary values cannot be sent by this renderer anyway, so
        // we don't need to default to multipart/form-data in this case.
        let contentType = this.request.contentType;
        if (contentType) {
            this.form.setAttribute('enctype', contentType);
        }
        let csrfParam, csrfToken;
        if ((csrfParam = this.request.csrfParam()) && (csrfToken = this.request.csrfToken())) {
            this.params.add(csrfParam, csrfToken);
        }
        // @params will be undefined for GET requests, since we have already
        // transfered all params to the URL during normalize().
        u.each(this.params.toArray(), this.addField.bind(this));
        up.browser.submitForm(this.form);
    }
    addField(attrs) {
        e.affix(this.form, 'input[type=hidden]', attrs);
    }
};


/***/ }),
/* 62 */
/***/ (() => {

const CONTENT_TYPE_URL_ENCODED = 'application/x-www-form-urlencoded';
const CONTENT_TYPE_FORM_DATA = 'multipart/form-data';
const u = up.util;
up.Request.XHRRenderer = class XHRRenderer {
    constructor(request) {
        this.request = request;
    }
    buildAndSend(handlers) {
        this.xhr = new XMLHttpRequest();
        // We copy params since we will modify them below.
        // This would confuse API clients and cache key logic in up.network.
        this.params = u.copy(this.request.params);
        // IE11 explodes it we're setting an undefined timeout property
        if (this.request.timeout) {
            this.xhr.timeout = this.request.timeout;
        }
        // The XMLHttpRequest method must be opened before we can add headers to it.
        this.xhr.open(this.getMethod(), this.request.url);
        // Add information about the response's intended use so the server may
        // customize or shorten its response.
        const metaProps = this.request.metaProps();
        for (let key in metaProps) {
            this.addHeader(up.protocol.headerize(key), metaProps[key]);
        }
        for (let header in this.request.headers) {
            this.addHeader(header, this.request.headers[header]);
        }
        let csrfHeader, csrfToken;
        if ((csrfHeader = this.request.csrfHeader()) && (csrfToken = this.request.csrfToken())) {
            this.addHeader(csrfHeader, csrfToken);
        }
        this.addHeader(up.protocol.headerize('version'), up.version);
        // The { contentType } will be missing in case of a FormData payload.
        // In this case the browser will choose a content-type with MIME boundary,
        // like: multipart/form-data; boundary=----WebKitFormBoundaryHkiKAbOweEFUtny8
        let contentType = this.getContentType();
        if (contentType) {
            this.addHeader('Content-Type', contentType);
        }
        u.assign(this.xhr, handlers);
        this.xhr.send(this.getPayload());
        return this.xhr;
    }
    getMethod() {
        // By default HTTP methods other than `GET` or `POST` will be converted into a `POST`
        // request and carry their original method as a `_method` parameter. This is to
        // [prevent unexpected redirect behavior](https://makandracards.com/makandra/38347)
        // if the server redirects with 302 (Rails default) instead of 303.
        if (!this.method) {
            this.method = this.request.method;
            if (this.request.wrapMethod && !this.request.will302RedirectWithGET()) {
                this.method = up.protocol.wrapMethod(this.method, this.params);
            }
        }
        return this.method;
    }
    getContentType() {
        this.finalizePayload();
        return this.contentType;
    }
    getPayload() {
        this.finalizePayload();
        return this.payload;
    }
    addHeader(header, value) {
        if (u.isOptions(value) || u.isArray(value)) {
            value = JSON.stringify(value);
        }
        this.xhr.setRequestHeader(header, value);
    }
    finalizePayload() {
        if (this.payloadFinalized) {
            return;
        }
        this.payloadFinalized = true;
        this.payload = this.request.payload;
        this.contentType = this.request.contentType;
        // (1) If a user sets { payload } we also expect them to set a { contentType }.
        //     In that case we don't change anything.
        // (2) We don't send a { contentType } or { payload } for GET requests.
        if (!this.payload && this.request.allowsPayload()) {
            // Determine the effective Content-Type by looking at our params values.
            if (!this.contentType) {
                this.contentType = this.params.hasBinaryValues() ? CONTENT_TYPE_FORM_DATA : CONTENT_TYPE_URL_ENCODED;
            }
            // Serialize our payload
            if (this.contentType === CONTENT_TYPE_FORM_DATA) {
                // The effective Content-Type header will look like
                // multipart/form-data; boundary=----WebKitFormBoundaryHkiKAbOweEFUtny8
                // When we send a FormData payload the browser will automatically
                // chooose a boundary and set the payload.
                this.contentType = null;
                this.payload = this.params.toFormData();
            }
            else {
                // Only in form submissions %-encoded spaces are sent as a plus characater ("+")
                this.payload = this.params.toQuery().replace(/%20/g, '+');
            }
        }
    }
};


/***/ }),
/* 63 */
/***/ (() => {

/*-
A response to an [HTTP request](/up.request).

### Example

    up.request('/foo').then(function(response) {
      console.log(response.status) // 200
      console.log(response.text)   // "<html><body>..."
    })

@class up.Response
@parent up.network
*/
up.Response = class Response extends up.Record {
    /*-
    The HTTP method used for the request that produced this response.
  
    This is usually the HTTP method used by the initial request, but if the server
    redirected multiple requests may have been involved. In this case this property reflects
    the method used by the last request.
  
    If the response's URL changed from the request's URL,
    Unpoly will assume a redirect and set the method to `GET`.
    Also see the `X-Up-Method` header.
  
    @property up.Response#method
    @param {string} method
    @stable
    */
    /*-
    The URL used for the response.
  
    This is usually the requested URL, or the final URL after the server redirected.
  
    On Internet Explorer 11 this property is only set when the server sends an `X-Up-Location` header.
  
    @property up.Response#url
    @param {string} url
    @stable
    */
    /*-
    The response body as a `string`.
  
    @property up.Response#text
    @param {string} text
    @stable
    */
    /*-
    The response's
    [HTTP status code](https://en.wikipedia.org/wiki/List_of_HTTP_status_codes)
    as a `number`.
  
    A successful response will usually have a `200` or `201' status code.
  
    @property up.Response#status
    @param {number} status
    @stable
    */
    /*-
    The original [request](/up.Request) that triggered this response.
  
    @property up.Response#request
    @param {up.Request} request
    @experimental
    */
    /*-
    The [`XMLHttpRequest`](https://developer.mozilla.org/en-US/docs/Web/API/XMLHttpRequest)
    object that was used to create this response.
  
    @property up.Response#xhr
    @param {XMLHttpRequest} xhr
    @experimental
    */
    /*-
    A [document title pushed by the server](/X-Up-Title).
  
    If the server pushed no title via HTTP header, this will be `undefined`.
  
    @property up.Response#title
    @param {string} [title]
    @experimental
    */
    /*-
    A [render target pushed by the server](/X-Up-Target).
  
    If the server pushed no title via HTTP header, this will be `undefined`.
  
    @property up.Response#target
    @param {string} [target]
    @experimental
    */
    /*-
    Changes to the current [context](/context) as [set by the server](/X-Up-Context).
  
    @property up.Response#context
    @experimental
    */
    keys() {
        return [
            'method',
            'url',
            'text',
            'status',
            'request',
            'xhr',
            'target',
            'title',
            'acceptLayer',
            'dismissLayer',
            'eventPlans',
            'context',
            'clearCache',
            'headers' // custom headers to for synthetic reponses without { xhr } property
        ];
    }
    defaults() {
        return { headers: {} };
    }
    /*-
    Returns whether the server responded with a 2xx HTTP status.
  
    @property up.Response#ok
    @param {boolean} ok
    @stable
    */
    get ok() {
        // 0 is falsy in JavaScript
        return this.status && ((this.status >= 200) && (this.status <= 299));
    }
    /*-
    Returns the HTTP header value with the given name.
  
    The search for the header name is case-insensitive.
  
    Returns `undefined` if the given header name was not included in the response.
  
    @function up.Response#getHeader
    @param {string} name
    @return {string|undefined} value
    @experimental
    */
    getHeader(name) {
        return this.headers[name] || this.xhr?.getResponseHeader(name);
    }
    /*-
    The response's [content-type](https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Content-Type).
  
    @property up.Response#contentType
    @param {string} contentType
    @experimental
    */
    get contentType() {
        return this.getHeader('Content-Type');
    }
    get cspNonces() {
        return up.protocol.cspNoncesFromHeader(this.getHeader('Content-Security-Policy'));
    }
    /*-
    The response body parsed as a JSON string.
  
    The parsed JSON object is cached with the response object,
    so multiple accesses will call `JSON.parse()` only once.
  
    ### Example
  
        response = await up.request('/profile.json')
        console.log("User name is " + response.json.name)
  
    @property up.Response#json
    @param {Object} json
    @stable
    */
    get json() {
        return this.parsedJSON || (this.parsedJSON = JSON.parse(this.text));
    }
};


/***/ }),
/* 64 */
/***/ (() => {

const u = up.util;
const e = up.element;
up.ResponseDoc = class ResponseDoc {
    constructor(options) {
        // We wrap <noscript> tags into a <div> for two reasons:
        //
        // (1) IE11 and Edge cannot find <noscript> tags with jQuery or querySelector() or
        //     getElementsByTagName() when the tag was created by DOMParser. This is a bug.
        //     https://developer.microsoft.com/en-us/microsoft-edge/platform/issues/12453464/
        //
        // (2) The children of a <nonscript> tag are expected to be a verbatim text node
        //     in a scripting-capable browser. However, due to rules in the DOMParser spec,
        //     the children are parsed into actual DOM nodes. This confuses libraries that
        //     work with <noscript> tags, such as lazysizes.
        //     http://w3c.github.io/DOM-Parsing/#dom-domparser-parsefromstring
        //
        // We will unwrap the wrapped <noscript> tags when a fragment is requested with
        // #first(), and only in the requested fragment.
        this.noscriptWrapper = new up.HTMLWrapper('noscript');
        // We strip <script> tags from the HTML.
        // If you need a fragment update to call JavaScript code, call it from a compiler
        // ([Google Analytics example](https://makandracards.com/makandra/41488-using-google-analytics-with-unpoly)).
        this.scriptWrapper = new up.HTMLWrapper('script');
        this.root =
            this.parseDocument(options) ||
                this.parseFragment(options) ||
                this.parseContent(options);
        this.cspNonces = options.cspNonces;
    }
    parseDocument(options) {
        return this.parse(options.document, e.createDocumentFromHTML);
    }
    parseContent(options) {
        // Parsing { inner } is the last option we try. It should always succeed in case someone
        // tries `up.layer.open()` without any args. Hence we set the innerHTML to an empty string.
        let content = options.content || '';
        const target = options.target || up.fail("must pass a { target } when passing { content }");
        // Conjure an element that will later match options.target in @select()
        const matchingElement = e.createFromSelector(target);
        if (u.isString(content)) {
            content = this.wrapHTML(content);
            // Don't use e.createFromHTML() here, since content may be a text node.
            matchingElement.innerHTML = content;
        }
        else {
            matchingElement.appendChild(content);
        }
        return matchingElement;
    }
    parseFragment(options) {
        return this.parse(options.fragment);
    }
    parse(value, parseFn = e.createFromHTML) {
        if (u.isString(value)) {
            value = this.wrapHTML(value);
            value = parseFn(value);
        }
        return value;
    }
    rootSelector() {
        return up.fragment.toTarget(this.root);
    }
    wrapHTML(html) {
        html = this.noscriptWrapper.wrap(html);
        if (up.fragment.config.runScripts) {
            // <script> tags instantiated by DOMParser are inert and will not run
            // when appended. So we wrap them, then unwrap once attach. This will
            // cause the script to run.
            html = this.scriptWrapper.wrap(html);
        }
        else {
            html = this.scriptWrapper.strip(html);
        }
        return html;
    }
    getTitle() {
        // Cache since multiple plans will query this.
        // Use a flag so we can cache an empty result.
        if (!this.titleParsed) {
            this.title = this.root.querySelector("head title")?.textContent;
            this.titleParsed = true;
        }
        return this.title;
    }
    select(selector) {
        // Use up.fragment.subtree() instead of up.element.subtree()
        // so we can support the non-standard :has() selector.
        // We need to disable layer matching with { layer: 'any' } since
        // our detached document is not part of the layer stack.
        return up.fragment.subtree(this.root, selector, { layer: 'any' })[0];
    }
    finalizeElement(element) {
        // Restore <noscript> tags so they become available to compilers.
        this.noscriptWrapper.unwrap(element);
        // Rewrite per-request CSP nonces to match that of the current page.
        up.NonceableCallback.adoptNonces(element, this.cspNonces);
        // Restore <script> so they will run.
        this.scriptWrapper.unwrap(element);
    }
};


/***/ }),
/* 65 */
/***/ (() => {

const e = up.element;
const u = up.util;
up.RevealMotion = class RevealMotion {
    constructor(element, options = {}) {
        this.element = element;
        this.options = options;
        const viewportConfig = up.viewport.config;
        this.viewport = e.get(this.options.viewport) || up.viewport.get(this.element);
        this.obstructionsLayer = up.layer.get(this.viewport);
        this.snap = this.options.snap ?? this.options.revealSnap ?? viewportConfig.revealSnap;
        this.padding = this.options.padding ?? this.options.revealPadding ?? viewportConfig.revealPadding;
        this.top = this.options.top ?? this.options.revealTop ?? viewportConfig.revealTop;
        this.max = this.options.max ?? this.options.revealMax ?? viewportConfig.revealMax;
        this.topObstructions = viewportConfig.fixedTop;
        this.bottomObstructions = viewportConfig.fixedBottom;
    }
    start() {
        const viewportRect = this.getViewportRect(this.viewport);
        const elementRect = up.Rect.fromElement(this.element);
        if (this.max) {
            const maxPixels = u.evalOption(this.max, this.element);
            elementRect.height = Math.min(elementRect.height, maxPixels);
        }
        this.addPadding(elementRect);
        this.substractObstructions(viewportRect);
        // Cards test (topics dropdown) throw an error when we also fail at zero
        if (viewportRect.height < 0) {
            return up.error.failed.async('Viewport has no visible area');
        }
        const originalScrollTop = this.viewport.scrollTop;
        let newScrollTop = originalScrollTop;
        if (this.top || (elementRect.height > viewportRect.height)) {
            // Element is either larger than the viewport,
            // or the user has explicitly requested for the element to align at top
            // => Scroll the viewport so the first element row is the first viewport row
            const diff = elementRect.top - viewportRect.top;
            newScrollTop += diff;
        }
        else if (elementRect.top < viewportRect.top) {
            // Element fits within viewport, but sits too high
            // => Scroll up (reduce scrollY), so the element comes down
            newScrollTop -= (viewportRect.top - elementRect.top);
        }
        else if (elementRect.bottom > viewportRect.bottom) {
            // Element fits within viewport, but sits too low
            // => Scroll down (increase scrollY), so the element comes up
            newScrollTop += (elementRect.bottom - viewportRect.bottom);
        }
        else {
            // Element is fully visible within viewport.
            // Do nothing.
        }
        if (u.isNumber(this.snap) && (newScrollTop < this.snap) && (elementRect.top < (0.5 * viewportRect.height))) {
            newScrollTop = 0;
        }
        if (newScrollTop !== originalScrollTop) {
            return this.scrollTo(newScrollTop);
        }
        else {
            return Promise.resolve();
        }
    }
    scrollTo(newScrollTop) {
        this.scrollMotion = new up.ScrollMotion(this.viewport, newScrollTop, this.options);
        return this.scrollMotion.start();
    }
    getViewportRect() {
        if (up.viewport.isRoot(this.viewport)) {
            // Other than an element with overflow-y, the document viewport
            // stretches to the full height of its contents. So we create a viewport
            // sized to the usuable screen area.
            return new up.Rect({
                left: 0,
                top: 0,
                width: up.viewport.rootWidth(),
                height: up.viewport.rootHeight()
            });
        }
        else {
            return up.Rect.fromElement(this.viewport);
        }
    }
    addPadding(elementRect) {
        elementRect.top -= this.padding;
        elementRect.height += 2 * this.padding;
    }
    selectObstructions(selectors) {
        return up.fragment.all(selectors.join(','), { layer: this.obstructionsLayer });
    }
    substractObstructions(viewportRect) {
        for (let obstruction of this.selectObstructions(this.topObstructions)) {
            let obstructionRect = up.Rect.fromElement(obstruction);
            let diff = obstructionRect.bottom - viewportRect.top;
            if (diff > 0) {
                viewportRect.top += diff;
                viewportRect.height -= diff;
            }
        }
        for (let obstruction of this.selectObstructions(this.bottomObstructions)) {
            let obstructionRect = up.Rect.fromElement(obstruction);
            let diff = viewportRect.bottom - obstructionRect.top;
            if (diff > 0) {
                viewportRect.height -= diff;
            }
        }
    }
    finish() {
        this.scrollMotion?.finish();
    }
};


/***/ }),
/* 66 */
/***/ (() => {

const u = up.util;
// We want to make the default speed mimic Chrome's smooth scrolling behavior.
// We also want to keep the default value in up.viewport.config.scrollSpeed to be 1.
// For our calculation in #animationFrame() we need to multiply it with this factor.
const SPEED_CALIBRATION = 0.065;
up.ScrollMotion = class ScrollMotion {
    constructor(scrollable, targetTop, options = {}) {
        this.scrollable = scrollable;
        this.targetTop = targetTop;
        // The option for up.scroll() is { behavior }, but coming
        // from up.replace() it's { scrollBehavior }.
        this.behavior = options.behavior ?? options.scrollBehavior ?? 'auto';
        // The option for up.scroll() is { behavior }, but coming
        // from up.replace() it's { scrollSpeed }.
        this.speed = (options.speed ?? options.scrollSpeed ?? up.viewport.config.scrollSpeed) * SPEED_CALIBRATION;
    }
    start() {
        return new Promise((resolve, reject) => {
            this.resolve = resolve;
            this.reject = reject;
            if ((this.behavior === 'smooth') && up.motion.isEnabled()) {
                this.startAnimation();
            }
            else {
                this.finish();
            }
        });
    }
    startAnimation() {
        this.startTime = Date.now();
        this.startTop = this.scrollable.scrollTop;
        this.topDiff = this.targetTop - this.startTop;
        // We're applying a square root to become slower for small distances
        // and faster for large distances.
        this.duration = Math.sqrt(Math.abs(this.topDiff)) / this.speed;
        requestAnimationFrame(() => this.animationFrame());
    }
    animationFrame() {
        if (this.settled) {
            return;
        }
        const currentTime = Date.now();
        const timeElapsed = currentTime - this.startTime;
        const timeFraction = Math.min(timeElapsed / this.duration, 1);
        this.frameTop = this.startTop + (u.simpleEase(timeFraction) * this.topDiff);
        // When we're very close to the target top, finish the animation
        // directly to deal with rounding errors.
        if (Math.abs(this.targetTop - this.frameTop) < 0.3) {
            this.finish();
        }
        else {
            this.scrollable.scrollTop = this.frameTop;
            requestAnimationFrame(() => this.animationFrame());
        }
    }
    abort(reason) {
        this.settled = true;
        this.reject(up.error.aborted(reason));
    }
    finish() {
        // In case we're animating with emulation, cancel the next scheduled frame
        this.settled = true;
        // Setting the { scrollTop } prop will also finish a native scrolling
        // animation in Firefox and Chrome.
        this.scrollable.scrollTop = this.targetTop;
        this.resolve();
    }
};


/***/ }),
/* 67 */
/***/ (() => {

const e = up.element;
const u = up.util;
up.Selector = class Selector {
    constructor(selectors, filters = []) {
        this.selectors = selectors;
        this.filters = filters;
        // If the user has set config.mainTargets = [] then a selector :main
        // will resolve to an empty array.
        this.unionSelector = this.selectors.join(',') || 'match-none';
    }
    matches(element) {
        return e.matches(element, this.unionSelector) && this.passesFilter(element);
    }
    closest(element) {
        let parentElement;
        if (this.matches(element)) {
            return element;
        }
        else if (parentElement = element.parentElement) {
            return this.closest(parentElement);
        }
    }
    passesFilter(element) {
        return u.every(this.filters, filter => filter(element));
    }
    descendants(root) {
        // There's a requirement that prior selectors must match first.
        // The background here is that up.fragment.config.mainTargets may match multiple
        // elements in a layer (like .container and body), but up.fragment.get(':main') should
        // prefer to match .container.
        //
        // To respect this priority we do not join @selectors into a single, comma-separated
        // CSS selector, but rather make one query per selector and concatenate the results.
        const results = u.flatMap(this.selectors, selector => e.all(root, selector));
        return u.filter(results, element => this.passesFilter(element));
    }
    subtree(root) {
        const results = [];
        if (this.matches(root)) {
            results.push(root);
        }
        results.push(...this.descendants(root));
        return results;
    }
};


/***/ }),
/* 68 */
/***/ (() => {

const u = up.util;
up.store || (up.store = {});
up.store.Memory = class Memory {
    constructor() {
        this.data = {};
    }
    clear() {
        this.data = {};
    }
    get(key) {
        return this.data[key];
    }
    set(key, value) {
        this.data[key] = value;
    }
    remove(key) {
        delete this.data[key];
    }
    keys() {
        return Object.keys(this.data);
    }
    size() {
        return this.keys().length;
    }
    values() {
        return u.values(this.data);
    }
};


/***/ }),
/* 69 */
/***/ (() => {

//#
// Store implementation backed by window.sessionStorage
// ====================================================
//
// This improves plain sessionStorage access in several ways:
//
// - Falls back to in-memory storage if window.sessionStorage is not available (see below).
// - Allows to store other types of values than just strings.
// - Allows to store structured values.
// - Allows to invalidate existing data by incrementing a version number on the server.
//
// On sessionStorage availability
// ------------------------------
//
// All supported browsers have sessionStorage, but the property is `null`
// in private browsing mode in Safari and the default Android webkit browser.
// See https://makandracards.com/makandra/32865-sessionstorage-per-window-browser-storage
//
// Also Chrome explodes upon access of window.sessionStorage when
// user blocks third-party cookies and site data and this page is embedded
// as an <iframe>. See https://bugs.chromium.org/p/chromium/issues/detail?id=357625
//
up.store.Session = class Session extends up.store.Memory {
    constructor(rootKey) {
        super();
        this.rootKey = rootKey;
        this.loadFromSessionStorage();
    }
    clear() {
        super.clear();
        this.saveToSessionStorage();
    }
    set(key, value) {
        super.set(key, value);
        this.saveToSessionStorage();
    }
    remove(key) {
        super.remove(key);
        this.saveToSessionStorage();
    }
    loadFromSessionStorage() {
        try {
            let raw = sessionStorage?.getItem(this.rootKey);
            if (raw) {
                this.data = JSON.parse(raw);
            }
        }
        catch (error) {
            // window.sessionStorage not supported (see class comment)
            // or JSON syntax error. In this case we keep the initial {}
            // from up.store.Memory constructor
        }
    }
    saveToSessionStorage() {
        const json = JSON.stringify(this.data);
        try {
            return sessionStorage?.setItem(this.rootKey, json);
        }
        catch (error) {
            // window.sessionStorage not supported (see class comment).
            // We do nothing and only keep data in-memory.
        }
    }
};


/***/ }),
/* 70 */
/***/ (() => {

const u = up.util;
const e = up.element;
up.Tether = class Tether {
    constructor(options) {
        up.migrate.handleTetherOptions?.(options);
        this.anchor = options.anchor;
        this.align = options.align;
        this.position = options.position;
        this.alignAxis = (this.position === 'top') || (this.position === 'bottom') ? 'horizontal' : 'vertical';
        this.viewport = up.viewport.get(this.anchor);
        // The document viewport is <html> on some browsers, and we cannot attach children to that.
        this.parent = this.viewport === e.root ? document.body : this.viewport;
        // If the offsetParent is within the viewport (or is the viewport) we can simply
        // `position: absolute` and it will move as the viewport scrolls, without JavaScript.
        // If not however, we have no choice but to move it on every scroll event.
        this.syncOnScroll = !this.viewport.contains(this.anchor.offsetParent);
    }
    start(element) {
        this.element = element;
        this.element.style.position = 'absolute';
        this.setOffset(0, 0);
        this.sync();
        this.changeEventSubscription('on');
    }
    stop() {
        this.changeEventSubscription('off');
    }
    changeEventSubscription(fn) {
        let doScheduleSync = this.scheduleSync.bind(this);
        up[fn](window, 'resize', doScheduleSync);
        if (this.syncOnScroll) {
            up[fn](this.viewport, 'scroll', doScheduleSync);
        }
    }
    scheduleSync() {
        clearTimeout(this.syncTimer);
        return this.syncTimer = u.task(this.sync.bind(this));
    }
    isDetached() {
        return e.isDetached(this.parent) || e.isDetached(this.anchor);
    }
    sync() {
        const elementBox = this.element.getBoundingClientRect();
        const elementMargin = {
            top: e.styleNumber(this.element, 'marginTop'),
            right: e.styleNumber(this.element, 'marginRight'),
            bottom: e.styleNumber(this.element, 'marginBottom'),
            left: e.styleNumber(this.element, 'marginLeft')
        };
        const anchorBox = this.anchor.getBoundingClientRect();
        let left;
        let top;
        switch (this.alignAxis) {
            case 'horizontal': { // position is 'top' or 'bottom'
                switch (this.position) {
                    case 'top':
                        top = anchorBox.top - elementMargin.bottom - elementBox.height;
                        break;
                    // element
                    // -------
                    // margin
                    // -------
                    // anchor
                    case 'bottom':
                        top = anchorBox.top + anchorBox.height + elementMargin.top;
                        break;
                }
                // anchor
                // ------
                // margin
                // ------
                // element
                switch (this.align) {
                    case 'left':
                        // anchored to anchor's left, grows to the right
                        left = anchorBox.left + elementMargin.left;
                        break;
                    // mg | element
                    // ------------
                    // anchor
                    case 'center':
                        // anchored to anchor's horizontal center, grows equally to left/right
                        left = anchorBox.left + (0.5 * (anchorBox.width - elementBox.width));
                        break;
                    // e l e m e n t
                    // -------------
                    //    anchor
                    case 'right':
                        // anchored to anchor's right, grows to the left
                        left = (anchorBox.left + anchorBox.width) - elementBox.width - elementMargin.right;
                        break;
                    // element | mg
                    // ------------
                    //       anchor
                }
                break;
            }
            case 'vertical': { // position is 'left' or 'right'
                switch (this.align) {
                    case 'top':
                        // anchored to the top, grows to the bottom
                        top = anchorBox.top + elementMargin.top;
                        break;
                    //  margin | anchor
                    // --------|
                    // element |
                    case 'center':
                        // anchored to anchor's vertical center, grows equally to left/right
                        top = anchorBox.top + (0.5 * (anchorBox.height - elementBox.height));
                        break;
                    //  ele |
                    //  men | anchor
                    //    t |
                    case 'bottom':
                        // anchored to the bottom, grows to the top
                        top = (anchorBox.top + anchorBox.height) - elementBox.height - elementMargin.bottom;
                        break;
                    // element |
                    // ------- |
                    //  margin | anchor
                }
                switch (this.position) {
                    case 'left':
                        left = anchorBox.left - elementMargin.right - elementBox.width;
                        break;
                    // element | margin | anchor
                    case 'right':
                        left = anchorBox.left + anchorBox.width + elementMargin.left;
                        break;
                    // anchor | margin | element
                }
                break;
            }
        }
        if (u.isDefined(left) || u.isDefined(top)) {
            this.moveTo(left, top);
        }
        else {
            up.fail('Invalid tether constraints: %o', this.describeConstraints());
        }
    }
    describeConstraints() {
        return { position: this.position, align: this.align };
    }
    moveTo(targetLeft, targetTop) {
        const elementBox = this.element.getBoundingClientRect();
        this.setOffset((targetLeft - elementBox.left) + this.offsetLeft, (targetTop - elementBox.top) + this.offsetTop);
    }
    setOffset(left, top) {
        this.offsetLeft = left;
        this.offsetTop = top;
        e.setStyle(this.element, { left, top });
    }
};


/***/ }),
/* 71 */
/***/ (() => {

const u = up.util;
up.URLPattern = class URLPattern {
    constructor(fullPattern, normalizeURL = u.normalizeURL) {
        this.normalizeURL = normalizeURL;
        this.groups = [];
        const positiveList = [];
        const negativeList = [];
        for (let pattern of u.splitValues(fullPattern)) {
            if (pattern[0] === '-') {
                negativeList.push(pattern.substring(1));
            }
            else {
                positiveList.push(pattern);
            }
        }
        this.positiveRegexp = this.buildRegexp(positiveList, true);
        this.negativeRegexp = this.buildRegexp(negativeList, false);
    }
    buildRegexp(list, capture) {
        if (!list.length) {
            return;
        }
        list = list.map((url) => {
            // If the current browser location is multiple directories deep (e.g. /foo/bar),
            // a leading asterisk would be normalized to /foo/*. So we prepend a slash.
            if (url[0] === '*') {
                url = '/' + url;
            }
            url = this.normalizeURL(url);
            url = u.escapeRegExp(url);
            return url;
        });
        let reCode = list.join('|');
        reCode = reCode.replace(/\\\*/g, '.*?');
        reCode = reCode.replace(/(:|\\\$)([a-z][\w-]*)/ig, (match, type, name) => {
            // It's \\$ instead of $ because we do u.escapeRegExp above
            if (type === '\\$') {
                if (capture) {
                    this.groups.push({ name, cast: Number });
                }
                return '(\\d+)';
            }
            else {
                if (capture) {
                    this.groups.push({ name, cast: String });
                }
                return '([^/?#]+)';
            }
        });
        return new RegExp('^(?:' + reCode + ')$');
    }
    // This method is performance-sensitive. It's called for every link in an [up-nav]
    // after every fragment update.
    test(url, doNormalize = true) {
        if (doNormalize) {
            url = this.normalizeURL(url);
        }
        // Use RegExp#test() instead of RegExp#recognize() as building match groups is expensive,
        // and we only need to know whether the URL matches (true / false).
        return this.positiveRegexp.test(url) && !this.isExcluded(url);
    }
    recognize(url, doNormalize = true) {
        if (doNormalize) {
            url = this.normalizeURL(url);
        }
        let match = this.positiveRegexp.exec(url);
        if (match && !this.isExcluded(url)) {
            const resolution = {};
            this.groups.forEach((group, groupIndex) => {
                let value = match[groupIndex + 1];
                if (value) {
                    return resolution[group.name] = group.cast(value);
                }
            });
            return resolution;
        }
    }
    isExcluded(url) {
        return this.negativeRegexp?.test(url);
    }
};


/***/ }),
/* 72 */
/***/ (() => {

/*-
Framework initialization
========================

The `up.framework` module lets you customize Unpoly's [initialization sequence](/install#initialization).

@see up.boot
@see script[up-boot=manual]
@see up.framework.isSupported

@module up.framework
*/
up.framework = (function () {
    // Event                          up.framework.readyState   document.readyState
    // ------------------------------------------------------------------------------------------------------
    // Browser starts parsing HTML    -                         loading
    // Unpoly script is running       evaling                   loading (if sync) or interactive (if defered)
    // ... submodules are running     evaling                   loading (if sync) or interactive (if defered)
    // User scripts are running       configuring               loading (if sync) or interactive (if defered)
    // DOMContentLoaded               configuring => booting    interactive
    // Initial page is compiling      booting                   interactive
    // Document resources loaded      booted                    complete
    let readyState = 'evaling'; // evaling => configuring => booting => booted
    /*-
    Resets Unpoly to the state when it was booted.
    All custom event handlers, animations, etc. that have been registered
    will be discarded.
  
    Emits event [`up:framework:reset`](/up:framework:reset).
  
    @function up.framework.reset
    @internal
    */
    function emitReset() {
        up.emit('up:framework:reset', { log: false });
    }
    /*-
    This event is [emitted](/up.emit) when Unpoly is [reset](/up.framework.reset) during unit tests.
  
    @event up:framework:reset
    @internal
    */
    /*-
    Manually boots the Unpoly framework.
  
    It is not usually necessary to call `up.boot()` yourself. When you load [Unpoly's JavaScript file](/install),
    Unpoly will automatically boot on [`DOMContentLoaded`](https://developer.mozilla.org/en-US/docs/Web/API/Window/DOMContentLoaded_event).
    There are only two cases when you would boot manually:
  
    - When you load Unpoly with `<script async>`
    - When you explicitly ask to manually boot by loading Unpoly with [`<script up-boot="manual">`](/script-up-boot-manual).
  
    Before you manually boot, Unpoly should be configured and compilers should be registered.
    Booting will cause Unpoly to [compile](/up.hello) the initial page.
  
    Unpoly will refuse to boot if the current browser is [not supported](/up.framework.isSupported).
    This leaves you with a classic server-side application on legacy browsers.
  
    @function up.boot
    @experimental
    */
    function boot() {
        if (readyState !== 'configuring') {
            // In an app with a lot of async script the user may attempt to boot us twice.
            console.error('Unpoly has already booted');
            return;
        }
        // This is called synchronously after all Unpoly modules have been parsed
        // and executed. We cannot delay booting until the DOM is ready, since by then
        // all user-defined event listeners and compilers will have registered.
        // Note that any non-async scripts after us will delay DOMContentLoaded.
        let supportIssue = up.framework.supportIssue();
        if (!supportIssue) {
            // Change the state in case any user-provided compiler calls up.boot().
            // up.boot() is a no-op unless readyState === 'configuring'.
            readyState = 'booting';
            up.emit('up:framework:boot', { log: false });
            readyState = 'booted';
        }
        else {
            console.error("Unpoly cannot boot: %s", supportIssue);
        }
    }
    function mustBootManually() {
        let unpolyScript = document.currentScript;
        // If we're is loaded via <script async>, there are no guarantees
        // when we're called or when subsequent scripts that configure Unpoly
        // have executed
        if (unpolyScript?.async) {
            return true;
        }
        // If we're loaded with <script up-boot="manual"> the user explicitly
        // requested to boot Unpoly manually.
        if (unpolyScript?.getAttribute('up-boot') === 'manual') {
            return true;
        }
        // If we're loaded this late, someone loads us dynamically.
        // We don't know when subsequent scripts that configure Unpoly
        // have executed.
        if (document.readyState === 'complete') {
            return true;
        }
    }
    /*-
    Prevent Unpoly from booting automatically.
  
    By default Unpoly [automatically boots](/install#initialization)
    on [`DOMContentLoaded`](https://developer.mozilla.org/en-US/docs/Web/API/Window/DOMContentLoaded_event).
    To prevent this, add an `[up-boot="manual"]` attribute to the `<script>` element
    that loads Unpoly:
  
    ```html
    <script src="unpoly.js" up-boot="manual"></script>
    ```
    You may then call `up.boot()` to manually boot Unpoly at a later time.
  
    ### Browser support
  
    To use this feature in Internet Explorer 11 you need a polyfill for `document.currentScript`.
  
    @selector script[up-boot=manual]
    @experimental
    */
    function onEvaled() {
        up.emit('up:framework:evaled', { log: false });
        if (mustBootManually()) {
            console.debug('Call up.boot() after you have configured Unpoly');
        }
        else {
            // (1) On DOMContentLoaded we know that all non-[async] scripts have executed.
            // (2) Deferred scripts execute after the DOM was parsed (document.readyState === 'interactive'),
            //     but before DOMContentLoaded. That's why we must *not* boot synchonously when
            //     document.readyState === 'interactive'. We must wait until DOMContentLoaded, when we know that
            //     subsequent users scripts have executed and (possibly) configured Unpoly.
            // (3) There are no guarantees when [async] scripts execute. These must boot Unpoly manually.
            document.addEventListener('DOMContentLoaded', boot);
        }
        // After this line user scripts may run and configure Unpoly, add compilers, etc.
        readyState = 'configuring';
    }
    function startExtension() {
        if (readyState !== 'configuring') {
            throw new Error('Unpoly extensions must be loaded before booting');
        }
        readyState = 'evaling';
    }
    function stopExtension() {
        readyState = 'configuring';
    }
    /*-
    Returns whether Unpoly can boot in the current browser.
  
    If this returns `false` Unpoly will prevent itself from [booting](/up.boot)
    and will not [compile](/up.compiler) the initial page.
    This leaves you with a classic server-side application.
  
    ### Browser support
  
    Unpoly aims to supports all modern browsers.
  
    #### Chrome, Firefox, Edge, Safari
  
    Full support.
  
    #### Internet Explorer 11
  
    Full support with a `Promise` polyfill like [es6-promise](https://github.com/stefanpenner/es6-promise) (2.4 KB).\
    Support may be removed when Microsoft retires IE11 in [June 2022](https://blogs.windows.com/windowsexperience/2021/05/19/the-future-of-internet-explorer-on-windows-10-is-in-microsoft-edge/).
  
    #### Internet Explorer 10 or lower
  
    Unpoly will not boot or [run compilers](/up.compiler),
    leaving you with a classic server-side application.
  
    @function up.framework.isSupported
    @stable
    */
    function isSupported() {
        return !supportIssue();
    }
    function supportIssue() {
        if (!up.browser.canPromise()) {
            return "Browser doesn't support promises";
        }
        if (document.compatMode === 'BackCompat') {
            return 'Browser is in quirks mode (missing DOCTYPE?)';
        }
        if (up.browser.isEdge18()) {
            return 'Edge 18 or lower is unsupported';
        }
    }
    return {
        onEvaled,
        boot,
        startExtension,
        stopExtension,
        reset: emitReset,
        get evaling() { return readyState === 'evaling'; },
        get booted() { return readyState === 'booted'; },
        get beforeBoot() { return readyState !== 'booting' && readyState !== 'booted'; },
        isSupported,
        supportIssue,
    };
})();
up.boot = up.framework.boot;


/***/ }),
/* 73 */
/***/ (() => {

/*-
Events
======

This module contains functions to [emit](/up.emit) and [observe](/up.on) DOM events.

While the browser also has built-in functions to work with events,
you will find Unpoly's functions to be very concise and feature-rich.

### Events emitted by Unpoly

Most Unpoly features emit events that are prefixed with `up:`.

Unpoly's own events are documented in their respective modules, for example:

| Event                 | Module             |
|-----------------------|--------------------|
| `up:link:follow`      | `up.link`          |
| `up:form:submit`      | `up.form`          |
| `up:layer:open`       | `up.layer`         |
| `up:request:late`     | `up.network`       |

@see up.on
@see up.emit

@module up.event
*/
up.event = (function () {
    const u = up.util;
    const e = up.element;
    function reset() {
        // Resets the list of registered event listeners to the
        // moment when the framework was booted.
        for (let globalElement of [window, document, e.root, document.body]) {
            for (let listener of up.EventListener.allNonDefault(globalElement)) {
                listener.unbind();
            }
        }
    }
    /*-
    Listens to a [DOM event](https://developer.mozilla.org/en-US/docs/Web/API/Document_Object_Model/Events)
    on `document` or a given element.
  
    `up.on()` has some quality of life improvements over
    [`Element#addEventListener()`](https://developer.mozilla.org/en-US/docs/Web/API/EventTarget/addEventListener):
  
    - You may pass a selector for [event delegation](https://davidwalsh.name/event-delegate).
    - The event target is automatically passed as a second argument.
    - Your event listener will not be called when Unpoly has not [booted](/up.boot) in an unsupported browser
    - You may register a listener to multiple events by passing a space-separated list of event name (e.g. `"click mousedown"`)
    - You may register a listener to multiple elements in a single `up.on()` call, by passing a [list](/up.util.isList) of elements.
    - You use an [`[up-data]`](/up-data) attribute to [attach structured data](/up.on#attaching-structured-data)
      to observed elements. If an `[up-data]` attribute is set, its value will automatically be
      parsed as JSON and passed as a third argument.
  
    ### Basic example
  
    The code below will call the listener when a `<a>` is clicked
    anywhere in the `document`:
  
    ```js
    up.on('click', 'a', function(event, element) {
      console.log("Click on a link %o", element)
    })
    ```
  
    You may also bind the listener to a given element instead of `document`:
  
    ```js
    var form = document.querySelector('form')
    up.on(form, 'click', function(event, form) {
      console.log("Click within %o", form)
    })
    ```
  
    ### Event delegation
  
    You may pass both an element and a selector
    for [event delegation](https://davidwalsh.name/event-delegate).
  
    The example below registers a single event listener to the given `form`,
    but only calls the listener when the clicked element is a `select` element:
  
    ```
    var form = document.querySelector('form')
    up.on(form, 'click', 'select', function(event, select) {
      console.log("Click on select %o within %o", select, form)
    })
    ```
  
    ### Attaching structured data
  
    In case you want to attach structured data to the event you're observing,
    you can serialize the data to JSON and put it into an `[up-data]` attribute:
  
    ```html
    <span class='person' up-data='{ "age": 18, "name": "Bob" }'>Bob</span>
    <span class='person' up-data='{ "age": 22, "name": "Jim" }'>Jim</span>
    ```
  
    The JSON will be parsed and handed to your event handler as a third argument:
  
    ```js
    up.on('click', '.person', function(event, element, data) {
      console.log("This is %o who is %o years old", data.name, data.age)
    })
    ```
  
    ### Unbinding an event listener
  
    `up.on()` returns a function that unbinds the event listeners when called:
  
    ```js
    // Define the listener
    var listener =  function(event) { ... }
  
    // Binding the listener returns an unbind function
    var unbind = up.on('click', listener)
  
    // Unbind the listener
    unbind()
    ```
  
    There is also a function [`up.off()`](/up.off) which you can use for the same purpose:
  
    ```js
    // Define the listener
    var listener =  function(event) { ... }
  
    // Bind the listener
    up.on('click', listener)
  
    // Unbind the listener
    up.off('click', listener)
    ```
  
    ### Binding to multiple elements
  
    You may register a listener to multiple elements in a single `up.on()` call, by passing a [list](/up.util.isList) of elements:
  
    ```js
    let allForms = document.querySelectorAll('form')
    up.on(allForms, 'submit', function(event, form) {
      console.log('Submitting form %o', form)
    })
    ```
  
    ### Binding to multiple event types
  
    You may register a listener to multiple event types by passing a space-separated list of event types:
  
    ```js
    let element = document.querySelector(...)
    up.on(element, 'mouseenter mouseleave', function(event) {
      console.log('Mouse entered or left')
    })
    ```
  
    @function up.on
  
    @param {Element|jQuery} [element=document]
      The element on which to register the event listener.
  
      If no element is given, the listener is registered on the `document`.
  
    @param {string|Array<string>} types
      The event types to bind to.
  
      Multiple event types may be passed as either a space-separated string
      or as an array of types.
  
    @param {string|Function():string} [selector]
      The selector of an element on which the event must be triggered.
  
      Omit the selector to listen to all events of the given type, regardless
      of the event target.
  
      If the selector is not known in advance you may also pass a function
      that returns the selector. The function is evaluated every time
      an event with the given type is observed.
  
    @param {boolean} [options.passive=false]
      Whether to register a [passive event listener](https://developers.google.com/web/updates/2016/06/passive-event-listeners).
  
      A passive event listener may not call `event.preventDefault()`.
      This in particular may improve the frame rate when registering
      `touchstart` and `touchmove` events.
  
    @param {boolean} [options.once=true]
      Whether the listener should run at most once.
  
      If `true` the listener will automatically be unbound
      after the first invocation.
  
    @param {Function(event, [element], [data])} listener
      The listener function that should be called.
  
      The function takes the affected element as a second argument.
      If the element has an [`up-data`](/up-data) attribute, its value is parsed as JSON
      and passed as a third argument.
  
    @return {Function()}
      A function that unbinds the event listeners when called.
  
    @stable
    */
    function on(...args) {
        return buildListenerGroup(args).bind();
    }
    /*-
    Listens to an event on `document` or a given element.
    The event handler is called with the event target as a
    [jQuery collection](https://learn.jquery.com/using-jquery-core/jquery-object/).
  
    If you're not using jQuery, use `up.on()` instead, which calls
    event handlers with a native element.
  
    ### Example
  
    ```
    up.$on('click', 'a', function(event, $link) {
      console.log("Click on a link with destination %s", $element.attr('href'))
    })
    ```
  
    @function up.$on
    @param {Element|jQuery} [element=document]
      The element on which to register the event listener.
  
      If no element is given, the listener is registered on the `document`.
    @param {string} events
      A space-separated list of event names to bind to.
    @param {string} [selector]
      The selector of an element on which the event must be triggered.
      Omit the selector to listen to all events with that name, regardless
      of the event target.
    @param {boolean} [options.passive=false]
      Whether to register a [passive event listener](https://developers.google.com/web/updates/2016/06/passive-event-listeners).
  
      A passive event listener may not call `event.preventDefault()`.
      This in particular may improve the frame rate when registering
      `touchstart` and `touchmove` events.
    @param {Function(event, [element], [data])} listener
      The listener function that should be called.
  
      The function takes the affected element as the first argument).
      If the element has an [`up-data`](/up-data) attribute, its value is parsed as JSON
      and passed as a second argument.
    @return {Function()}
      A function that unbinds the event listeners when called.
    @stable
    */
    function $on(...args) {
        return buildListenerGroup(args, { jQuery: true }).bind();
    }
    /*-
    Unbinds an event listener previously bound with `up.on()`.
  
    ### Example
  
    Let's say you are listing to clicks on `.button` elements:
  
    ```js
    var listener = function() { ... }
    up.on('click', '.button', listener)
    ```
  
    You can stop listening to these events like this:
  
    ```js
    up.off('click', '.button', listener)
    ```
  
    @function up.off
    @param {Element|jQuery} [element=document]
    @param {string|Function(): string} events
    @param {string} [selector]
    @param {Function(event, [element], [data])} listener
      The listener function to unbind.
  
      Note that you must pass a reference to the same function reference
      that was passed to `up.on()` earlier.
    @stable
    */
    function off(...args) {
        return buildListenerGroup(args).unbind();
    }
    function buildListenerGroup(args, options) {
        return up.EventListenerGroup.fromBindArgs(args, options);
    }
    function buildEmitter(args) {
        return up.EventEmitter.fromEmitArgs(args);
    }
    /*-
    Emits a event with the given name and properties.
  
    The event will be triggered as an event on `document` or on the given element.
  
    Other code can subscribe to events with that name using
    [`Element#addEventListener()`](https://developer.mozilla.org/en-US/docs/Web/API/EventTarget/addEventListener)
    or [`up.on()`](/up.on).
  
    ### Example
  
    ```js
    up.on('my:event', function(event) {
      console.log(event.foo)
    })
  
    up.emit('my:event', { foo: 'bar' })
    // Prints "bar" to the console
    ```
  
    @function up.emit
    @param {Element|jQuery} [target=document]
      The element on which the event is triggered.
  
      If omitted, the event will be emitted on the `document`.
    @param {string} eventType
      The event type, e.g. `my:event`.
    @param {Object} [props={}]
      A list of properties to become part of the event object that will be passed to listeners.
    @param {up.Layer|string|number} [props.layer]
      The [layer](/up.layer) on which to emit this event.
  
      If this property is set, the event will be emitted on the [layer's outmost element](/up.Layer.prototype.element).
      Also [up.layer.current](/up.layer.current) will be set to the given layer while event listeners
      are running.
    @param {string|Array} [props.log]
      A message to print to the [log](/up.log) when the event is emitted.
  
      Pass `false` to not log this event emission.
    @param {Element|jQuery} [props.target=document]
      The element on which the event is triggered.
  
      Alternatively the target element may be passed as the first argument.
    @return {Event}
      The emitted event object.
    @stable
    */
    function emit(...args) {
        return buildEmitter(args).emit();
    }
    /*-
    Builds an event with the given type and properties.
  
    The returned event is not [emitted](/up.emit).
  
    ### Example
  
    ```js
    let event = up.event.build('my:event', { foo: 'bar' })
    console.log(event.type)              // logs "my:event"
    console.log(event.foo)               // logs "bar"
    console.log(event.defaultPrevented)  // logs "false"
    up.emit(event)                       // emits the event
    ```
  
    @function up.event.build
    @param {string} [type]
      The event type.
  
      May also be passed as a property `{ type }`.
    @param {Object} [props={}]
      An object with event properties.
    @param {string} [props.type]
      The event type.
  
      May also be passed as a first string argument.
    @return {Event}
    @experimental
    */
    function build(...args) {
        const props = u.extractOptions(args);
        const type = args[0] || props.type || up.fail('Expected event type to be passed as string argument or { type } property');
        const event = document.createEvent('Event');
        event.initEvent(type, true, true); // name, bubbles, cancelable
        u.assign(event, u.omit(props, ['type', 'target']));
        // IE11 does not set { defaultPrevented: true } after #preventDefault()
        // was called on a custom event.
        // See discussion here: https://stackoverflow.com/questions/23349191
        if (up.browser.isIE11()) {
            const originalPreventDefault = event.preventDefault;
            event.preventDefault = function () {
                // Even though we're swapping out defaultPrevented() with our own implementation,
                // we still need to call the original method to trigger the forwarding of up:click.
                originalPreventDefault.call(event);
                return u.getter(event, 'defaultPrevented', () => true);
            };
        }
        return event;
    }
    /*-
    [Emits](/up.emit) the given event and throws an `AbortError` if it was prevented.
  
    @function up.event.assertEmitted
    @param {string} eventType
    @param {Object} eventProps
    @param {string|Array} [eventProps.message]
    @return {Event}
    @internal
    */
    function assertEmitted(...args) {
        return buildEmitter(args).assertEmitted();
    }
    /*-
    Registers an event listener to be called when the user
    presses the `Escape` key.
  
    ### Example
  
    ```js
    up.event.onEscape(function(event) {
      console.log('Escape pressed!')
    })
    ```
    @function up.event.onEscape
    @param {Function(Event)} listener
      The listener function that will be called when `Escape` is pressed.
    @function
    @experimental
    */
    function onEscape(listener) {
        return on('keydown', function (event) {
            if (wasEscapePressed(event)) {
                return listener(event);
            }
        });
    }
    /*-
    Returns whether the given keyboard event involved the ESC key.
  
    @function up.util.wasEscapePressed
    @param {Event} event
    @internal
    */
    function wasEscapePressed(event) {
        const { key } = event;
        // IE/Edge use 'Esc', other browsers use 'Escape'
        return (key === 'Escape') || (key === 'Esc');
    }
    /*-
    Prevents the event from being processed further.
  
    In detail:
  
    - It prevents the event from bubbling up the DOM tree.
    - It prevents other event handlers bound on the same element.
    - It prevents the event's default action.
  
    ### Example
  
    ```js
    up.on('click', 'link.disabled', function(event) {
      up.event.halt(event)
    })
    ```
  
    @function up.event.halt
    @param {Event} event
    @stable
    */
    function halt(event) {
        event.stopImmediatePropagation();
        event.preventDefault();
    }
    const keyModifiers = ['metaKey', 'shiftKey', 'ctrlKey', 'altKey'];
    /*-
    @function up.event.isUnmodified
    @internal
    */
    function isUnmodified(event) {
        return (u.isUndefined(event.button) || (event.button === 0)) &&
            !u.some(keyModifiers, modifier => event[modifier]);
    }
    function fork(originalEvent, newType, copyKeys = []) {
        const newEvent = up.event.build(newType, u.pick(originalEvent, copyKeys));
        newEvent.originalEvent = originalEvent; // allow users to access other props through event.originalEvent.prop
        ['stopPropagation', 'stopImmediatePropagation', 'preventDefault'].forEach(function (key) {
            const originalMethod = newEvent[key];
            return newEvent[key] = function () {
                originalEvent[key]();
                return originalMethod.call(newEvent);
            };
        });
        // If the source event was already prevented, the forked event should also be.
        if (originalEvent.defaultPrevented) {
            newEvent.preventDefault();
        }
        return newEvent;
    }
    /*-
    Emits the given event when this link is clicked.
  
    When the emitted event's default' is prevented, the original `click` event's default is also prevented.
  
    You may use this attribute to emit events when clicking on areas that are no hyperlinks,
    by setting it on an `<a>` element without a `[href]` attribute.
  
    ### Example
  
    This hyperlink will emit an `user:select` event when clicked:
  
    ```html
    <a href='/users/5'
      up-emit='user:select'
      up-emit-props='{ "id": 5, "firstName": "Alice" }'>
      Alice
    </a>
  
    <script>
      up.on('a', 'user:select', function(event) {
        console.log(event.firstName) // logs "Alice"
        event.preventDefault()       // will prevent the link from being followed
      })
    </script>
    ```
  
    @selector a[up-emit]
    @param up-emit
      The type of the event to be emitted.
    @param [up-emit-props='{}']
      The event properties, serialized as JSON.
    @stable
    */
    function executeEmitAttr(event, element) {
        if (!isUnmodified(event)) {
            return;
        }
        const eventType = e.attr(element, 'up-emit');
        const eventProps = e.jsonAttr(element, 'up-emit-props');
        const forkedEvent = fork(event, eventType);
        u.assign(forkedEvent, eventProps);
        up.emit(element, forkedEvent);
    }
    //  abortable = ->
    //    signal = document.createElement('up-abort-signal')
    //    abort = -> up.emit(signal, 'abort')
    //    [abort, signal]
    on('up:click', 'a[up-emit]', executeEmitAttr);
    on('up:framework:reset', reset);
    return {
        on,
        $on,
        off,
        build,
        emit,
        assertEmitted,
        onEscape,
        halt,
        isUnmodified,
        fork,
        keyModifiers
    };
})();
up.on = up.event.on;
up.$on = up.event.$on;
up.off = up.event.off;
up.$off = up.event.off; // it's the same as up.off()
up.emit = up.event.emit;


/***/ }),
/* 74 */
/***/ (() => {

/*-
Server protocol
===============

You rarely need to change server-side code to use Unpoly. You don't need
to provide a JSON API, or add extra routes for AJAX requests. The server simply renders
a series of full HTML pages, like it would without Unpoly.

There is an **optional** protocol your server may use to exchange additional information
when Unpoly is [updating fragments](/up.link). The protocol mostly works by adding
additional HTTP headers (like `X-Up-Target`) to requests and responses.

While the protocol can help you optimize performance and handle some edge cases,
implementing it is **entirely optional**. For instance, `unpoly.com` itself is a static site
that uses Unpoly on the frontend and doesn't even have an active server component.

### Existing implementations

You should be able to implement the protocol in a very short time.

There are existing implementations for various web frameworks:

- [Ruby on Rails](/install/ruby)
- [Roda](https://github.com/adam12/roda-unpoly)
- [Rack](https://github.com/adam12/rack-unpoly) (Sinatra, Padrino, Hanami, Cuba, ...)
- [Phoenix](https://elixirforum.com/t/unpoly-a-framework-like-turbolinks/3614/15) (Elixir)
- [PHP](https://github.com/webstronauts/php-unpoly) (Symfony, Laravel, Stack)

@module up.protocol
*/
up.protocol = (function () {
    const u = up.util;
    const e = up.element;
    const headerize = function (camel) {
        const header = camel.replace(/(^.|[A-Z])/g, char => '-' + char.toUpperCase());
        return 'X-Up' + header;
    };
    const extractHeader = function (xhr, shortHeader, parseFn = u.identity) {
        let value = xhr.getResponseHeader(headerize(shortHeader));
        if (value) {
            return parseFn(value);
        }
    };
    /*-
    This request header contains the current Unpoly version to mark this request as a fragment update.
  
    Server-side code may check for the presence of an `X-Up-Version` header to
    distinguish [fragment updates](/up.link) from full page loads.
  
    The `X-Up-Version` header is guaranteed to be set for all [requests made through Unpoly](/up.request).
  
    ### Example
  
    ```http
    X-Up-Version: 1.0.0
    ```
  
    @header X-Up-Version
    @stable
    */
    /*-
    This request header contains the CSS selector targeted for a successful fragment update.
  
    Server-side code is free to optimize its response by only rendering HTML
    that matches the selector. For example, you might prefer to not render an
    expensive sidebar if the sidebar is not targeted.
  
    Unpoly will usually update a different selector in case the request fails.
    This selector is sent as a second header, `X-Up-Fail-Target`.
  
    The user may choose to not send this header by configuring
    `up.network.config.requestMetaKeys`.
  
    ### Example
  
    ```http
    X-Up-Target: .menu
    X-Up-Fail-Target: body
    ```
  
    ### Changing the render target from the server
  
    The server may change the render target context by including a CSS selector as an `X-Up-Target` header
    in its response.
  
    ```http
    Content-Type: text/html
    X-Up-Target: .selector-from-server
  
    <div class="selector-from-server">
      ...
    </div>
    ```
  
    The frontend will use the server-provided target for both successful (HTTP status `200 OK`)
    and failed (status `4xx` or `5xx`) responses.
  
    The server may also set a target of `:none` to have the frontend render nothing.
    In this case no response body is required:
  
    ```http
    Content-Type: text/html
    X-Up-Target: :none
    ```
  
    @header X-Up-Target
    @stable
    */
    /*-
    This request header contains the CSS selector targeted for a failed fragment update.
  
    A fragment update is considered *failed* if the server responds with a status code other than 2xx,
    but still renders HTML.
  
    Server-side code is free to optimize its response to a failed request by only rendering HTML
    that matches the provided selector. For example, you might prefer to not render an
    expensive sidebar if the sidebar is not targeted.
  
    The user may choose to not send this header by configuring
    `up.network.config.requestMetaKeys`.
  
    ### Example
  
    ```http
    X-Up-Target: .menu
    X-Up-Fail-Target: body
    ```
  
    ### Signaling failed form submissions
  
    When [submitting a form via AJAX](/form-up-submit)
    Unpoly needs to know whether the form submission has failed (to update the form with
    validation errors) or succeeded (to update the `[up-target]` selector).
  
    For Unpoly to be able to detect a failed form submission, the response must be
    return a non-2xx HTTP status code. We recommend to use either
    400 (bad request) or 422 (unprocessable entity).
  
    To do so in [Ruby on Rails](http://rubyonrails.org/), pass a [`:status` option to `render`](http://guides.rubyonrails.org/layouts_and_rendering.html#the-status-option):
  
    ```ruby
    class UsersController < ApplicationController
  
      def create
        user_params = params[:user].permit(:email, :password)
        @user = User.new(user_params)
        if @user.save?
          sign_in @user
        else
          render 'form', status: :bad_request
        end
      end
  
    end
    ```
  
    @header X-Up-Fail-Target
    @stable
    */
    /*-
    This request header contains the targeted layer's [mode](/up.layer.mode).
  
    Server-side code is free to render different HTML for different modes.
    For example, you might prefer to not render a site navigation for overlays.
  
    The user may choose to not send this header by configuring
    `up.network.config.requestMetaKeys`.
  
    ### Example
  
    ```http
    X-Up-Mode: drawer
    ```
  
    @header X-Up-Mode
    @stable
    */
    /*-
    This request header contains the [mode](/up.layer.mode) of the layer
    targeted for a failed fragment update.
  
    A fragment update is considered *failed* if the server responds with a
    status code other than 2xx, but still renders HTML.
  
    Server-side code is free to render different HTML for different modes.
    For example, you might prefer to not render a site navigation for overlays.
  
    The user may choose to not send this header by configuring
    `up.network.config.requestMetaKeys`.
  
    ### Example
  
    ```http
    X-Up-Mode: drawer
    X-Up-Fail-Mode: root
    ```
  
    @header X-Up-Fail-Mode
    @stable
    */
    function parseClearCacheValue(value) {
        switch (value) {
            case 'true':
                return true;
            case 'false':
                return false;
            default:
                return value;
        }
    }
    function clearCacheFromXHR(xhr) {
        return extractHeader(xhr, 'clearCache', parseClearCacheValue);
    }
    /*-
    The server may send this optional response header to control which previously cached responses should be [uncached](/up.cache.clear) after this response.
  
    The value of this header is a [URL pattern](/url-patterns) matching responses that should be uncached.
  
    For example, to uncache all responses to URLs starting with `/notes/`:
  
    ```http
    X-Up-Clear-Cache: /notes/*
    ```
  
    ### Overriding the client-side default
  
    If the server does not send an `X-Up-Clear-Cache` header, Unpoly will [clear the entire cache](/up.network.config#config.clearCache) after a non-GET request.
  
    You may force Unpoly to *keep* the cache after a non-GET request:
  
    ```http
    X-Up-Clear-Cache: false
    ```
  
    You may also force Unpoly to *clear* the cache after a GET request:
  
    ```http
    X-Up-Clear-Cache: *
    ```
  
    @header X-Up-Clear-Cache
    @stable
    */
    /*-
    This request header contains a timestamp of an existing fragment that is being [reloaded](/up.reload).
  
    The timestamp must be explicitly set by the user as an `[up-time]` attribute on the fragment.
    It should indicate the time when the fragment's underlying data was last changed.
  
    See `[up-time]` for a detailed example.
  
    ### Format
  
    The time is encoded is the number of seconds elapsed since the [Unix epoch](https://en.wikipedia.org/wiki/Unix_time).
  
    For instance, a modification date of December 23th, 1:40:18 PM UTC would produce the following header:
  
    ```http
    X-Up-Target: .unread-count
    X-Up-Reload-From-Time: 1608730818
    ```
  
    If no timestamp is known, Unpoly will send a value of zero (`X-Up-Reload-From-Time: 0`).
  
    @header X-Up-Reload-From-Time
    @stable
    */
    function contextFromXHR(xhr) {
        return extractHeader(xhr, 'context', JSON.parse);
    }
    /*-
    This request header contains the targeted layer's [context](/context), serialized as JSON.
  
    The user may choose to not send this header by configuring
    `up.network.config.requestMetaKeys`.
  
    ### Example
  
    ```http
    X-Up-Context: { "lives": 3 }
    ```
  
    ### Updating context from the server
  
    The server may update the layer context by sending a `X-Up-Context` response header with
    changed key/value pairs:
  
    ```http
    Content-Type: text/html
    X-Up-Context: { "lives": 2 }
  
    <html>
      ...
    </html>
    ```
  
    Upon seeing the response header, Unpoly will assign the server-provided context object to
    the layer's context object, adding or replacing keys as needed.
  
    Client-side context keys not mentioned in the response will remain unchanged.
    There is no explicit protocol to *remove* keys from the context, but the server may send a key
    with a `null` value to effectively remove a key.
  
    The frontend will use the server-provided context upates for both successful (HTTP status `200 OK`)
    and failed (status `4xx` or `5xx`) responses.  If no `X-Up-Context` response header is set,
    the updating layer's context will not be changed.
  
    It is recommended that the server only places changed key/value pairs into the `X-Up-Context`
    response header, and not echo the entire context object. Otherwise any client-side changes made while
    the request was in flight will get overridden by the server-provided context.
  
    @header X-Up-Context
    @experimental
    */
    /*-
    This request header contains the [context](/context) of the layer
    targeted for a failed fragment update, serialized as JSON.
  
    A fragment update is considered *failed* if the server responds with a
    status code other than 2xx, but still renders HTML.
  
    Server-side code is free to render different HTML for different contexts.
    For example, you might prefer to not render a site navigation for overlays.
  
    The user may choose to not send this header by configuring
    `up.network.config.requestMetaKeys`.
  
    ### Example
  
    ```http
    X-Up-Fail-Context: { "context": "Choose a company contact" }
    ```
  
    @header X-Up-Fail-Context
    @experimental
    */
    /*-
    @function up.protocol.methodFromXHR
    @internal
    */
    function methodFromXHR(xhr) {
        return extractHeader(xhr, 'method', u.normalizeMethod);
    }
    /*-
    The server may set this optional response header to change the browser location after a fragment update.
  
    Without this header Unpoly will set the browser location to the response URL, which is usually sufficient.
  
    When setting `X-Up-Location` it is recommended to also set `X-Up-Method`. If no `X-Up-Method` header is given
    and the response's URL changed from the request's URL, Unpoly will assume a redirect and set the
    method to `GET`.
  
    ### Internet Explorer 11
  
    There is an edge case on Internet Explorer 11, where Unpoly cannot detect the final URL after a redirect.
    You can fix this edge case by delivering `X-Up-Location` and `X-Up-Method` headers with the *last* response
    in a series of redirects.
  
    The **simplest implementation** is to set these headers for every request.
  
    ### Example
  
    ```http
    X-Up-Location: /current-url
    X-Up-Method: GET
    ```
  
    @header X-Up-Location
    @stable
    */
    /*-
    The server may set this optional response header to change the HTTP method after a fragment update.
  
    Without this header Unpoly will assume a `GET` method if the response's URL changed from the request's URL,
  
    ### Example
  
    ```http
    X-Up-Location: /current-url
    X-Up-Method: GET
    ```
  
    @header X-Up-Method
    @stable
    */
    /*-
    The server may set this optional response header to change the document title after a fragment update.
  
    Without this header Unpoly will extract the `<title>` from the server response.
  
    This header is useful when you [optimize your response](/X-Up-Target) to not render
    the application layout unless targeted. Since your optimized response
    no longer includes a `<title>`, you can instead use this HTTP header to pass the document title.
  
    ### Example
  
    ```http
    X-Up-Title: Playlist browser
    ```
  
    @header X-Up-Title
    @stable
    */
    /*-
    This request header contains the `[name]` of a [form field being validated](/input-up-validate).
  
    When seeing this header, the server is expected to validate (but not save)
    the form submission and render a new copy of the form with validation errors.
    See the documentation for [`input[up-validate]`](/input-up-validate) for more information
    on how server-side validation works in Unpoly.
  
    The server is free to respond with any HTTP status code, regardless of the validation result.
    Unpoly will always consider a validation request to be successful, even if the
    server responds with a non-200 status code. This is in contrast to [regular form submissions](/form-up-submit),
    [where a non-200 status code will often update a different element](/server-errors).
  
    ### Example
  
    Assume we have an auto-validating form field:
  
    ```html
    <fieldset>
      <input name="email" up-validate>
    </fieldset>
    ```
  
    When the input is changed, Unpoly will submit the form with an additional header:
  
    ```html
    X-Up-Validate: email
    ```
  
    @header X-Up-Validate
    @stable
    */
    function eventPlansFromXHR(xhr) {
        return extractHeader(xhr, 'events', JSON.parse);
    }
    /*-
    The server may set this response header to [emit events](/up.emit) with the
    requested [fragment update](/a-up-follow).
  
    The header value is a [JSON](https://en.wikipedia.org/wiki/JSON) array.
    Each element in the array is a JSON object representing an event to be emitted
    on the `document`.
  
    The object property `{ "type" }` defines the event's [type](https://developer.mozilla.org/en-US/docs/Web/API/Event/type). Other properties become properties of the emitted
    event object.
  
    ### Example
  
    ```http
    Content-Type: text/html
    X-Up-Events: [{ "type": "user:created", "id": 5012 }, { "type": "signup:completed" }]
    ...
  
    <html>
      ...
    </html>
    ```
  
    ### Emitting an event on a layer
  
    Instead of emitting an event on the `document`, the server may also choose to
    [emit the event on the layer being updated](/up.layer.emit). To do so, add a property
    `{ "layer": "current" }` to the JSON object of an event:
  
    ```http
    Content-Type: text/html
    X-Up-Events: [{ "type": "user:created", "name:" "foobar", "layer": "current" }]
    ...
  
    <html>
      ...
    </html>
    ```
  
    @header X-Up-Events
    @stable
    */
    function acceptLayerFromXHR(xhr) {
        // Even if acceptance has no value, the server will send
        // X-Up-Accept-Layer: null
        return extractHeader(xhr, 'acceptLayer', JSON.parse);
    }
    /*-
    The server may set this response header to [accept](/up.layer.accept) the targeted overlay
    in response to a fragment update.
  
    Upon seeing the header, Unpoly will cancel the fragment update and accept the layer instead.
    If the root layer is targeted, the header is ignored and the fragment is updated with
    the response's HTML content.
  
    The header value is the acceptance value serialized as a JSON object.
    To accept an overlay without value, set the header value to the string `null`.
  
    ### Example
  
    The response below will accept the targeted overlay with the value `{user_id: 1012 }`:
  
    ```http
    Content-Type: text/html
    X-Up-Accept-Layer: {"user_id": 1012}
  
    <html>
      ...
    </html>
    ```
  
    ### Rendering content
  
    The response may contain `text/html` content. If the root layer is targeted,
    the `X-Up-Accept-Layer` header is ignored and the fragment is updated with
    the response's HTML content.
  
    If you know that an overlay will be closed don't want to render HTML,
    have the server change the render target to `:none`:
  
    ```http
    Content-Type: text/html
    X-Up-Accept-Layer: {"user_id": 1012}
    X-Up-Target: :none
    ```
  
    @header X-Up-Accept-Layer
    @stable
    */
    function dismissLayerFromXHR(xhr) {
        // Even if dismissal has no value, the server will send
        // X-Up-Dismiss-Layer: null
        return extractHeader(xhr, 'dismissLayer', JSON.parse);
    }
    /*-
    The server may set this response header to [dismiss](/up.layer.dismiss) the targeted overlay
    in response to a fragment update.
  
    Upon seeing the header, Unpoly will cancel the fragment update and dismiss the layer instead.
    If the root layer is targeted, the header is ignored and the fragment is updated with
    the response's HTML content.
  
    The header value is the dismissal value serialized as a JSON object.
    To accept an overlay without value, set the header value to the string `null`.
  
    ### Example
  
    The response below will dismiss the targeted overlay without a dismissal value:
  
    ```http
    HTTP/1.1 200 OK
    Content-Type: text/html
    X-Up-Dismiss-Layer: null
  
    <html>
      ...
    </html>
    ```
  
    ### Rendering content
  
    The response may contain `text/html` content. If the root layer is targeted,
    the `X-Up-Dismiss-Layer` header is ignored and the fragment is updated with
    the response's HTML content.
  
    If you know that an overlay will be closed don't want to render HTML,
    have the server change the render target to `:none`:
  
    ```http
    HTTP/1.1 200 OK
    Content-Type: text/html
    X-Up-Dismiss-Layer: {"user_id": 1012}
    X-Up-Target: :none
    ```
  
    @header X-Up-Dismiss-Layer
    @stable
    */
    /*-
    Server-side companion libraries like unpoly-rails set this cookie so we
    have a way to detect the request method of the initial page load.
    There is no JavaScript API for this.
  
    @function up.protocol.initialRequestMethod
    @internal
    */
    const initialRequestMethod = u.memoize(function () {
        return u.normalizeMethod(up.browser.popCookie('_up_method'));
    });
    /*-
    The server may set this optional cookie to echo the HTTP method of the initial request.
  
    If the initial page was loaded with a non-`GET` HTTP method, Unpoly prefers to make a full
    page load when you try to update a fragment. Once the next page was loaded with a `GET` method,
    Unpoly will again update fragments.
  
    This fixes two edge cases you might or might not care about:
  
    1. Unpoly replaces the initial page state so it can later restore it when the user
       goes back to that initial URL. However, if the initial request was a POST,
       Unpoly will wrongly assume that it can restore the state by reloading with GET.
    2. Some browsers have a bug where the initial request method is used for all
       subsequently pushed states. That means if the user reloads the page on a later
       GET state, the browser will wrongly attempt a POST request.
       This issue affects Safari 9-12 (last tested in 2019-03).
       Modern Firefoxes, Chromes and IE10+ don't have this behavior.
  
    In order to allow Unpoly to detect the HTTP method of the initial page load,
    the server must set a cookie:
  
    ```http
    Set-Cookie: _up_method=POST
    ```
  
    When Unpoly boots it will look for this cookie and configure itself accordingly.
    The cookie is then deleted in order to not affect following requests.
  
    The **simplest implementation** is to set this cookie for every request that is neither
    `GET` nor an [Unpoly request](/X-Up-Version). For all other requests
    an existing `_up_method` cookie should be deleted.
  
    @cookie _up_method
    @stable
    */
    /*-
    @function up.protocol.locationFromXHR
    @internal
    */
    function locationFromXHR(xhr) {
        // We prefer the X-Up-Location header to xhr.responseURL.
        // If the server redirected to a new location, Unpoly-related headers
        // will be encoded in the request's query params like this:
        //
        //     /redirect-target?_up[target]=.foo
        //
        // To prevent these these `_up` params from showing up in the browser URL,
        // the X-Up-Location header will omit these params while `xhr.responseURL`
        // will still contain them.
        return extractHeader(xhr, 'location') || xhr.responseURL;
    }
    /*-
    @function up.protocol.titleFromXHR
    @internal
    */
    function titleFromXHR(xhr) {
        return extractHeader(xhr, 'title');
    }
    /*-
    @function up.protocol.targetFromXHR
    @internal
    */
    function targetFromXHR(xhr) {
        return extractHeader(xhr, 'target');
    }
    /*-
    Configures strings used in the optional [server protocol](/up.protocol).
  
    @property up.protocol.config
  
    @param {string} [config.csrfHeader='X-CSRF-Token']
      The name of the HTTP header that will include the
      [CSRF token](https://en.wikipedia.org/wiki/Cross-site_request_forgery#Synchronizer_token_pattern)
      for AJAX requests.
  
    @param {string|Function(): string} [config.csrfParam]
      The `name` of the hidden `<input>` used for sending a
      [CSRF token](https://en.wikipedia.org/wiki/Cross-site_request_forgery#Synchronizer_token_pattern) when
      submitting a default, non-AJAX form. For AJAX request the token is sent as an
      [HTTP header](/up.protocol.config#config.csrfHeader instead.
  
      The parameter name can be configured as a string or as function that returns the parameter name.
      If no name is set, no token will be sent.
  
      Defaults to the `content` attribute of a `<meta>` tag named `csrf-param`:
  
      ```html
      <meta name="csrf-param" content="authenticity_token" />
      ```
  
    @param {string|Function(): string} [config.csrfToken]
      The [CSRF token](https://en.wikipedia.org/wiki/Cross-site_request_forgery#Synchronizer_token_pattern)
      to send for unsafe requests. The token will be sent as either a HTTP header (for AJAX requests)
      or hidden form `<input>` (for default, non-AJAX form submissions).
  
      The token can either be configured as a string or as function that returns the token.
      If no token is set, no token will be sent.
  
      Defaults to the `content` attribute of a `<meta>` tag named `csrf-token`:
  
      ```
      <meta name='csrf-token' content='secret12345'>
      ```
  
    @param {string|Function(): string} [config.cspNonce]
      A [CSP script nonce](https://content-security-policy.com/nonce/)
      for the initial page that [booted](/up.boot) Unpoly.
  
      The nonce let Unpoly run JavaScript in HTML attributes like
      [`[up-on-loaded]`](/a-up-follow#up-on-loaded) or [`[up-on-accepted]`](/a-up-layer-new#up-on-accepted).
      See [Working with a strict Content Security Policy](/csp).
  
      The nonce can either be configured as a string or as function that returns the nonce.
  
      Defaults to the `content` attribute of a `<meta>` tag named `csp-nonce`:
  
      ```
      <meta name='csrf-token' content='secret98765'>
      ```
  
    @param {string} [config.methodParam='_method']
      The name of request parameter containing the original request method when Unpoly needs to wrap
      the method.
  
      Methods must be wrapped when making a [full page request](/up.network.loadPage) with a methods other
      than GET or POST. In this case Unpoly will make a POST request with the original request method
      in a form parameter named `_method`:
  
      ```http
      POST /test HTTP/1.1
      Host: example.com
      Content-Type: application/x-www-form-urlencoded
      Content-Length: 11
  
      _method=PUT
      ```
    @stable
    */
    const config = new up.Config(() => ({
        methodParam: '_method',
        csrfParam() { return e.metaContent('csrf-param'); },
        csrfToken() { return e.metaContent('csrf-token'); },
        cspNonce() { return e.metaContent('csp-nonce'); },
        csrfHeader: 'X-CSRF-Token',
        nonceableAttributes: ['up-observe', 'up-on-accepted', 'up-on-dismissed', 'up-on-loaded', 'up-on-finished', 'up-observe'],
    }));
    function csrfHeader() {
        return u.evalOption(config.csrfHeader);
    }
    function csrfParam() {
        return u.evalOption(config.csrfParam);
    }
    function csrfToken() {
        return u.evalOption(config.csrfToken);
    }
    function cspNonce() {
        return u.evalOption(config.cspNonce);
    }
    function cspNoncesFromHeader(cspHeader) {
        let nonces = [];
        if (cspHeader) {
            let parts = cspHeader.split(/\s*;\s*/);
            for (let part of parts) {
                if (part.indexOf('script-src') === 0) {
                    let noncePattern = /'nonce-([^']+)'/g;
                    let match;
                    while (match = noncePattern.exec(part)) {
                        nonces.push(match[1]);
                    }
                }
            }
        }
        return nonces;
    }
    function wrapMethod(method, params) {
        params.add(config.methodParam, method);
        return 'POST';
    }
    function reset() {
        config.reset();
    }
    up.on('up:framework:reset', reset);
    return {
        config,
        reset,
        locationFromXHR,
        titleFromXHR,
        targetFromXHR,
        methodFromXHR,
        acceptLayerFromXHR,
        contextFromXHR,
        dismissLayerFromXHR,
        eventPlansFromXHR,
        clearCacheFromXHR,
        csrfHeader,
        csrfParam,
        csrfToken,
        cspNonce,
        initialRequestMethod,
        headerize,
        wrapMethod,
        cspNoncesFromHeader,
    };
})();


/***/ }),
/* 75 */
/***/ (() => {

/*-
Logging
=======

Unpoly can print debugging information to the [browser console](https://developer.chrome.com/docs/devtools/console/), e.g.:

- Which [events](/up.event) are called
- When we're [making requests to the network](/up.request)
- Which [compilers](/up.syntax) are applied to which elements

@see up.log.enable
@see up.log.disable

@module up.log
*/
up.log = (function () {
    const sessionStore = new up.store.Session('up.log');
    /*-
    Configures the logging output on the developer console.
  
    @property up.log.config
    @param {boolean} [config.enabled=false]
      Whether Unpoly will print debugging information to the developer console.
  
      Debugging information includes which elements are being [compiled](/up.syntax)
      and which [events](/up.event) are being emitted.
      Note that errors will always be printed, regardless of this setting.
    @param {boolean} [config.banner=true]
      Print the Unpoly banner to the developer console.
    @param {boolean} [config.format=!isIE11]
      Format output using CSS.
    @stable
    */
    const config = new up.Config(() => ({
        enabled: sessionStore.get('enabled'),
        banner: true,
        format: up.browser.canFormatLog()
    }));
    function reset() {
        config.reset();
    }
    //  ###**
    //  Prints a debugging message to the browser console.
    //
    //  @function up.log.debug
    //  @param {string} message
    //  @param {Array} ...args
    //  @internal
    //  ###
    //  printToDebug = (message, args...) ->
    //    if config.enabled && message
    //      console.debug(prefix(message), args...)
    /*-
    Prints a logging message to the browser console.
  
    @function up.puts
    @param {string} message
    @param {Array} ...args
    @internal
    */
    function printToStandard(...args) {
        if (config.enabled) {
            printToStream('log', ...args);
        }
    }
    /*-
    @function up.warn
    @internal
    */
    const printToWarn = (...args) => printToStream('warn', ...args);
    /*-
    @function up.log.error
    @internal
    */
    const printToError = (...args) => printToStream('error', ...args);
    function printToStream(stream, trace, message, ...args) {
        if (message) {
            if (config.format) {
                args.unshift(''); // Reset
                args.unshift('color: #666666; padding: 1px 3px; border: 1px solid #bbbbbb; border-radius: 2px; font-size: 90%; display: inline-block');
                message = `%c${trace}%c ${message}`;
            }
            else {
                message = `[${trace}] ${message}`;
            }
            console[stream](message, ...args);
        }
    }
    function printBanner() {
        if (!config.banner) {
            return;
        }
        // The ASCII art looks broken in code since we need to escape backslashes
        const logo = " __ _____  ___  ___  / /_ __\n" +
            `/ // / _ \\/ _ \\/ _ \\/ / // /  ${up.version}\n` +
            "\\___/_//_/ .__/\\___/_/\\_. / \n" +
            "        / /            / /\n\n";
        let text = "";
        if (!up.migrate.loaded) {
            text += "Load unpoly-migrate.js to enable deprecated APIs.\n\n";
        }
        if (config.enabled) {
            text += "Call `up.log.disable()` to disable logging for this session.";
        }
        else {
            text += "Call `up.log.enable()` to enable logging for this session.";
        }
        const color = 'color: #777777';
        if (config.format) {
            console.log('%c' + logo + '%c' + text, 'font-family: monospace;' + color, color);
        }
        else {
            console.log(logo + text);
        }
    }
    up.on('up:framework:boot', printBanner);
    up.on('up:framework:reset', reset);
    function setEnabled(value) {
        sessionStore.set('enabled', value);
        config.enabled = value;
    }
    /*-
    Starts printing debugging information to the developer console.
  
    Debugging information includes which elements are being [compiled](/up.syntax)
    and which [events](/up.event) are being emitted.
  
    Errors will always be printed, regardless of this setting.
  
    @function up.log.enable
    @stable
    */
    function enable() {
        setEnabled(true);
    }
    /*-
    Stops printing debugging information to the developer console.
  
    Errors will still be printed, even with logging disabled.
  
    @function up.log.disable
    @stable
    */
    function disable() {
        setEnabled(false);
    }
    /*-
    Registers an empty rejection handler in case the given promise
    rejects with an AbortError or a failed up.Response.
  
    This prevents browsers from printing "Uncaught (in promise)" to the error
    console when the promise is rejected.
  
    This is helpful for event handlers where it is clear that no rejection
    handler will be registered:
  
    ```js
    up.on('submit', 'form[up-target]', (event, form) => {
      promise = up.submit(form)
      up.util.muteRejection(promise)
    })
    ```
  
    @function up.log.muteUncriticalRejection
    @param {Promise} promise
    @return {Promise}
    @internal
    */
    function muteUncriticalRejection(promise) {
        return promise.catch(function (error) {
            if ((typeof error !== 'object') || ((error.name !== 'AbortError') && !(error instanceof up.RenderResult) && !(error instanceof up.Response))) {
                throw error;
            }
        });
    }
    return {
        puts: printToStandard,
        error: printToError,
        warn: printToWarn,
        config,
        enable,
        disable,
        muteUncriticalRejection,
        isEnabled() { return config.enabled; },
    };
})();
up.puts = up.log.puts;
up.warn = up.log.warn;


/***/ }),
/* 76 */
/***/ (() => {

/*-
Custom JavaScript
=================

The `up.syntax` package lets you pair HTML elements with JavaScript behavior.

@see legacy-scripts

@see up.compiler
@see [up-data]
@see up.macro

@module up.syntax
*/
up.syntax = (function () {
    const u = up.util;
    const e = up.element;
    const SYSTEM_MACRO_PRIORITIES = {
        '[up-back]': -100,
        '[up-content]': -200,
        '[up-drawer]': -200,
        '[up-modal]': -200,
        '[up-cover]': -200,
        '[up-popup]': -200,
        '[up-tooltip]': -200,
        '[up-dash]': -200,
        '[up-expand]': -300,
        '[data-method]': -400,
        '[data-confirm]': -400, // converts [data-conform] to [up-confirm] only if link has followable [up-*] attributes
    };
    let compilers = [];
    let macros = [];
    /*-
    Registers a function to be called when an element with
    the given selector is inserted into the DOM.
  
    Use compilers to activate your custom Javascript behavior on matching
    elements.
  
    You should migrate your [`DOMContentLoaded`](https://developer.mozilla.org/en-US/docs/Web/API/Window/DOMContentLoaded_event)
    callbacks to compilers. This will make sure they run both at page load and
    when a new fragment is inserted later.
    See [Making JavaScripts work with fragment updates](/legacy-scripts) for advice
    on migrating legacy scripts.
  
    It will also organize your JavaScript snippets by selector.
  
    ### Example
  
    This compiler will insert the current time into a
    `<div class='current-time'></div>`:
  
    ```js
    up.compiler('.current-time', function(element) {
      var now = new Date()
      element.textContent = now.toString()
    })
    ```
  
    The compiler function will be called once for each matching element when
    the page loads, or when a matching fragment is [inserted](/up.replace) later.
  
    ### Integrating JavaScript libraries
  
    `up.compiler()` is a great way to integrate JavaScript libraries.
    Let's say your JavaScript plugin wants you to call `lightboxify()`
    on links that should open a lightbox. You decide to
    do this for all links with an `lightbox` class:
  
    ```html
    <a href="river.png" class="lightbox">River</a>
    <a href="ocean.png" class="lightbox">Ocean</a>
    ```
  
    This JavaScript will do exactly that:
  
    ```js
    up.compiler('a.lightbox', function(element) {
      lightboxify(element)
    })
    ```
  
    ### Cleaning up after yourself
  
    If your compiler returns a function, Unpoly will use this as a *destructor* to
    clean up if the element leaves the DOM. Note that in Unpoly the same DOM and JavaScript environment
    will persist through many page loads, so it's important to not create
    [memory leaks](https://makandracards.com/makandra/31325-how-to-create-memory-leaks-in-jquery).
  
    You should clean up after yourself whenever your compilers have global
    side effects, like a [`setInterval`](https://developer.mozilla.org/en-US/docs/Web/API/WindowTimers/setInterval)
    or [event handlers bound to the document root](/up.on).
  
    Here is a version of `.current-time` that updates
    the time every second, and cleans up once it's done. Note how it returns
    a function that calls `clearInterval`:
  
    ```js
    up.compiler('.current-time', function(element) {
      let update = () => element.textContent = new Date().toString()
      setInterval(update, 1000)
      return () => clearInterval(update)
    })
    ```
  
    If we didn't clean up after ourselves, we would have many ticking intervals
    operating on detached DOM elements after we have created and removed a couple
    of `<clock>` elements.
  
    An alternative way to register a destructor function is `up.destructor()`.
  
    ### Passing parameters to a compiler
  
    Use the `[up-data]` attribute to attach structured data to a DOM element.
    The data will be parsed and passed to your compiler function.
  
    Alternatively your compiler may access attributes for the compiled element
    via the standard [`Element#getAttribute()`](https://developer.mozilla.org/en-US/docs/Web/API/Element/getAttribute)
    method.
  
    Unpoly also provides utility functions to read an element attribute and
    cast it to a given type:
  
    - `up.element.booleanAttr(element, attr)`
    - `up.element.numberAttr(element, attr)`
    - `up.element.jsonAttr(element, attr)`
  
    @function up.compiler
    @param {string} selector
      The selector to match.
    @param {number} [options.priority=0]
      The priority of this compiler.
  
      Compilers with a higher priority are run first.
      Two compilers with the same priority are run in the order they were registered.
    @param {boolean} [options.batch=false]
      If set to `true` and a fragment insertion contains multiple
      elements matching `selector`, the `compiler` function is only called once
      with all these elements.
    @param {Function(element, data)} compiler
      The function to call when a matching element is inserted.
  
      The function takes the new element as the first argument.
      If the element has an [`up-data`](/up-data) attribute, its value is parsed as JSON
      and passed as a second argument.
  
      The function may return a destructor function that cleans the compiled
      object before it is removed from the DOM. The destructor is supposed to
      [clear global state](/up.compiler#cleaning-up-after-yourself)
      such as timeouts and event handlers bound to the document.
      The destructor is *not* expected to remove the element from the DOM, which
      is already handled by [`up.destroy()`](/up.destroy).
    @stable
    */
    function registerCompiler(...args) {
        const compiler = buildCompiler(args);
        return insertCompiler(compilers, compiler);
    }
    /*-
    Registers a function to be called when an element with
    the given selector is inserted into the DOM. The function is called
    with each matching element as a
    [jQuery object](https://learn.jquery.com/using-jquery-core/jquery-object/).
  
    If you're not using jQuery, use `up.compiler()` instead, which calls
    the compiler function with a native element.
  
    ### Example
  
    This jQuery compiler will insert the current time into a
    `<div class='current-time'></div>`:
  
    ```js
    up.$compiler('.current-time', function($element) {
      var now = new Date()
      $element.text(now.toString())
    })
    ```
  
    @function up.$compiler
    @param {string} selector
      The selector to match.
    @param {Object} [options]
      See [`options` argument for `up.compiler()`](/up.compiler#parameters).
    @param {Function($element, data)} compiler
      The function to call when a matching element is inserted.
  
      See [`compiler` argument for `up.compiler()`](/up.compiler#parameters).
      @stable
    */
    function registerJQueryCompiler(...args) {
        const compiler = registerCompiler(...args);
        compiler.jQuery = true;
    }
    /*-
    Registers a [compiler](/up.compiler) that is run before all other compilers.
  
    A macro lets you set UJS attributes that will be compiled afterwards.
  
    If you want default attributes for *every* link and form, consider customizing your
    [navigation options](/navigation).
  
    ### Example
  
    You will sometimes find yourself setting the same combination of UJS attributes again and again:
  
    ```html
    <a href="/page1" up-layer="new modal" up-class="warning" up-animation="shake">Page 1</a>
    <a href="/page1" up-layer="new modal" up-class="warning" up-animation="shake">Page 1</a>
    <a href="/page1" up-layer="new modal" up-class="warning" up-animation="shake">Page 1</a>
    ```
  
    We would much rather define a new `[smooth-link]` attribute that let's us
    write the same links like this:
  
    ```html
    <a href="/page1" smooth-link>Page 1</a>
    <a href="/page2" smooth-link>Page 2</a>
    <a href="/page3" smooth-link>Page 3</a>
    ```
  
    We can define the `[content-link]` attribute by registering a macro that
    sets the `[up-target]`, `[up-transition]` and `[up-duration]` attributes for us:
  
    ```js
    up.macro('[smooth-link]', function(link) {
      link.setAttribute('up-target', '.content')
      link.setAttribute('up-transition', 'cross-fade')
      link.setAttribute('up-duration', '300')
    })
    ```
  
    @function up.macro
    @param {string} selector
      The selector to match.
    @param {Object} options
      See options for [`up.compiler()`](/up.compiler).
    @param {Function(element, data)} macro
      The function to call when a matching element is inserted.
  
      See [`up.compiler()`](/up.compiler#parameters) for details.
    @stable
    */
    function registerMacro(...args) {
        const macro = buildCompiler(args);
        if (up.framework.evaling) {
            macro.priority = detectSystemMacroPriority(macro.selector) ||
                up.fail('Unregistered priority for system macro %o', macro.selector);
        }
        return insertCompiler(macros, macro);
    }
    /*-
    Registers a [compiler](/up.compiler) that is run before all other compilers.
    The compiler function is called with each matching element as a
    [jQuery object](https://learn.jquery.com/using-jquery-core/jquery-object/).
  
    If you're not using jQuery, use `up.macro()` instead, which calls
    the macro function with a native element.
  
    ### Example
  
    ```js
    up.$macro('[content-link]', function($link) {
      $link.attr(
        'up-target': '.content',
        'up-transition': 'cross-fade',
        'up-duration':'300'
      )
    })
    ```
  
    @function up.$macro
    @param {string} selector
      The selector to match.
    @param {Object} options
      See [`options` argument for `up.compiler()`](/up.compiler#parameters).
    @param {Function(element, data)} macro
      The function to call when a matching element is inserted.
  
      See [`compiler` argument for `up.compiler()`](/up.compiler#parameters).
    @stable
    */
    function registerJQueryMacro(...args) {
        const macro = registerMacro(...args);
        macro.jQuery = true;
        return macro;
    }
    function detectSystemMacroPriority(macroSelector) {
        macroSelector = u.evalOption(macroSelector);
        for (let substr in SYSTEM_MACRO_PRIORITIES) {
            const priority = SYSTEM_MACRO_PRIORITIES[substr];
            if (macroSelector.indexOf(substr) >= 0) {
                return priority;
            }
        }
    }
    const parseCompilerArgs = function (args) {
        const selector = args.shift();
        const callback = args.pop();
        const options = u.extractOptions(args);
        return [selector, options, callback];
    };
    function buildCompiler(args) {
        let [selector, options, callback] = parseCompilerArgs(args);
        options = u.options(options, {
            selector,
            isDefault: up.framework.evaling,
            priority: 0,
            batch: false,
            jQuery: false
        });
        return u.assign(callback, options);
    }
    function insertCompiler(queue, newCompiler) {
        if (up.framework.booted) {
            up.puts('up.compiler()', 'Compiler %s was registered after booting Unpoly. Compiler will run for future fragments.', newCompiler.selector);
        }
        let existingCompiler;
        let index = 0;
        while ((existingCompiler = queue[index]) && (existingCompiler.priority >= newCompiler.priority)) {
            index += 1;
        }
        queue.splice(index, 0, newCompiler);
        return newCompiler;
    }
    /*-
    Applies all compilers on the given element and its descendants.
  
    Unlike [`up.hello()`](/up.hello), this doesn't emit any events.
  
    @function up.syntax.compile
    @param {Array<Element>} [options.skip]
      A list of elements whose subtrees should not be compiled.
    @internal
    */
    function compile(fragment, options) {
        const orderedCompilers = macros.concat(compilers);
        const pass = new up.CompilerPass(fragment, orderedCompilers, options);
        pass.run();
    }
    /*-
    Registers a function to be called when the given element
    is [destroyed](/up.destroy).
  
    ### Example
  
    ```js
    up.compiler('.current-time', function(element) {
      let update = () => element.textContent = new Date().toString()
      setInterval(update, 1000)
      up.destructor(element, () => clearInterval(update))
    })
    ```
  
    An alternative way to register a destructor function is to
    [`return` it from your compiler function](/up.compiler#cleaning-up-after-yourself).
  
    @function up.destructor
    @param {Element} element
    @param {Function|Array<Function>} destructor
      One or more destructor functions.
    @stable
    */
    function registerDestructor(element, destructor) {
        let destructors = element.upDestructors;
        if (!destructors) {
            destructors = [];
            element.upDestructors = destructors;
            element.classList.add('up-can-clean');
        }
        if (u.isArray(destructor)) {
            destructors.push(...destructor);
        }
        else {
            destructors.push(destructor);
        }
    }
    /*-
    Runs any destructor on the given fragment and its descendants in the same layer.
  
    Unlike [`up.destroy()`](/up.destroy), this does not emit any events
    and does not remove the element from the DOM.
  
    @function up.syntax.clean
    @param {Element} fragment
    @param {up.Layer} options.layer
    @internal
    */
    function clean(fragment, options = {}) {
        new up.DestructorPass(fragment, options).run();
    }
    /*-
    Returns the given element's `[up-data]`, parsed as a JavaScript object.
  
    Returns `undefined` if the element has no `[up-data]` attribute.
  
    ### Example
  
    You have an element with JSON data serialized into an `up-data` attribute:
  
    ```html
    <span class='person' up-data='{ "age": 18, "name": "Bob" }'>Bob</span>
    ```
  
    Calling `up.syntax.data()` will deserialize the JSON string into a JavaScript object:
  
    ```js
    up.syntax.data('.person') // returns { age: 18, name: 'Bob' }
    ```
  
    @function up.data
    @param {string|Element|jQuery} element
      The element for which to return data.
    @return
      The JSON-decoded value of the `up-data` attribute.
  
      Returns `undefined` if the element has no (or an empty) `up-data` attribute.
    @stable
    */
    /*-
    Attaches structured data to an element, to be consumed by a compiler.
  
    If an element with an `[up-data]` attribute enters the DOM,
    Unpoly will parse the JSON and pass the resulting object to any matching
    [`up.compiler()`](/up.compiler) functions.
  
    ### Example
  
    For instance, a container for a [Google Map](https://developers.google.com/maps/documentation/javascript/tutorial)
    might attach the location and names of its marker pins:
  
    ```html
    <div class='google-map' up-data='[
      { "lat": 48.36, "lng": 10.99, "title": "Friedberg" },
      { "lat": 48.75, "lng": 11.45, "title": "Ingolstadt" }
    ]'></div>
    ```
  
    The JSON will be parsed and handed to your compiler as a second argument:
  
    ```js
    up.compiler('.google-map', function(element, pins) {
      var map = new google.maps.Map(element)
      pins.forEach(function(pin) {
        var position = new google.maps.LatLng(pin.lat, pin.lng)
        new google.maps.Marker({
          position: position,
          map: map,
          title: pin.title
        })
      })
    })
    ```
  
    Similarly, when an event is triggered on an element annotated with
    [`up-data`], the parsed object will be passed to any matching
    [`up.on()`](/up.on) handlers.
  
    ```js
    up.on('click', '.google-map', function(event, element, pins) {
      console.log("There are %d pins on the clicked map", pins.length)
    })
    ```
  
    @selector [up-data]
    @param up-data
      A serialized JSON string
    @stable
    */
    function readData(element) {
        // If passed a selector, up.fragment.get() will prefer a match on the current layer.
        element = up.fragment.get(element);
        return e.jsonAttr(element, 'up-data') || {};
    }
    /*
    Resets the list of registered compiler directives to the
    moment when the framework was booted.
    */
    function reset() {
        compilers = u.filter(compilers, 'isDefault');
        macros = u.filter(macros, 'isDefault');
    }
    up.on('up:framework:reset', reset);
    return {
        compiler: registerCompiler,
        macro: registerMacro,
        $compiler: registerJQueryCompiler,
        $macro: registerJQueryMacro,
        destructor: registerDestructor,
        compile,
        clean,
        data: readData
    };
})();
up.compiler = up.syntax.compiler;
up.$compiler = up.syntax.$compiler;
up.destructor = up.syntax.destructor;
up.macro = up.syntax.macro;
up.$macro = up.syntax.$macro;
up.data = up.syntax.data;


/***/ }),
/* 77 */
/***/ (() => {

/*-
History
========

The `up.history` module helps you work with the browser history.

@see up.history.location
@see up:location:changed

@module up.history
*/
up.history = (function () {
    const u = up.util;
    const e = up.element;
    /*-
    Configures behavior when the user goes back or forward in browser history.
  
    @property up.history.config
    @param {Array} [config.restoreTargets=[]]
      A list of possible CSS selectors to [replace](/up.render) when the user goes back or forward in history.
  
      If more than one target is configured, the first selector matching both the current page and server response will be updated.
  
      If nothing is configured, the `<body>` element will be replaced.
    @param {boolean} [config.enabled=true]
      Defines whether [fragment updates](/up.render) will update the browser's current URL.
  
      If set to `false` Unpoly will never change the browser URL.
    @param {boolean} [config.enabled=true]
      Whether to restore the known scroll positions
      when the user goes back or forward in history.
    @stable
    */
    const config = new up.Config(() => ({
        enabled: true,
        // Prefer restoring the body instead of :main, in case the last fragment update
        // changed the page layout. See https://github.com/unpoly/unpoly/issues/237.
        restoreTargets: ['body']
    }));
    /*-
    Returns a normalized URL for the previous history entry.
  
    Only history entries added by Unpoly functions will be considered.
  
    @property up.history.previousLocation
    @param {string} previousLocation
    @experimental
    */
    let previousLocation;
    let nextPreviousLocation;
    function reset() {
        config.reset();
        previousLocation = undefined;
        nextPreviousLocation = undefined;
        trackCurrentLocation();
    }
    const DEFAULT_NORMALIZE_OPTIONS = { hash: true };
    function normalizeURL(url, options) {
        // The reason why we this takes an { options } object is that
        // isCurrentLocation() ignores a trailing slash. This is used to check whether
        // we're already at the given URL before pushing a history state.
        options = u.merge(DEFAULT_NORMALIZE_OPTIONS, options);
        return u.normalizeURL(url, options);
    }
    /*-
    Returns a normalized URL for the current browser location.
  
    The returned URL is an absolute pathname like `"/path"` without a hostname or port.
    It will include a `#hash` fragment and query string, if present.
  
    Note that if the current [layer](/up.layer) does not have [visible history](/up.Layer.prototype.history),
    the browser's address bar will show the location of an ancestor layer.
    To get the location of the current layer, use `up.layer.location`.
  
    @property up.history.location
    @param {string} location
    @experimental
    */
    function currentLocation(normalizeOptions) {
        return normalizeURL(location.href, normalizeOptions);
    }
    /*-
    Remembers the current URL so we can use previousURL on pop.
  
    @function observeNewURL
    @internal
    */
    function trackCurrentLocation() {
        const url = currentLocation();
        if (nextPreviousLocation !== url) {
            previousLocation = nextPreviousLocation;
            nextPreviousLocation = url;
        }
    }
    trackCurrentLocation();
    // Some web frameworks care about a trailing slash, some consider it optional.
    // Only for the equality test ("is this the current URL?") we consider it optional.
    // Note that we inherit { hash: true } from DEFAULT_NORMALIZE_OPTIONS.
    const ADDITIONAL_NORMALIZE_OPTIONS_FOR_COMPARISON = { trailingSlash: false };
    /*-
    Returns whether the given URL matches the [current browser location](/up.history.location).
  
    ### Examples
  
    ```js
    location.hostname // => '/path'
  
    up.history.isLocation('/path') // => true
    up.history.isLocation('/path?query') // => false
    up.history.isLocation('/path#hash') // => false
    up.history.isLocation('/other') // => false
    ```
  
    The given URL is [normalized](/up.util.normalizeURL), so any URL string pointing to the browser location
    will match:
  
    ```js
    location.hostname // => '/current-host'
    location.pathname // => '/foo'
  
    up.history.isLocation('/foo') // => true
    up.history.isLocation('http://current-host/foo') // => true
    up.history.isLocation('http://otgher-host/foo') // => false
    ```
  
    @function up.history.isLocation
    @param {string} url
      The URL to compare against the current browser location.
  
      This can be a either an absolute pathname (`/path`), a relative filename (`index.html`) or a fully qualified URL (`https://...`).
    @param {boolean} [options.hash=true]
      Whether to consider `#hash` fragments in the given or current URLs.
  
      When set to `false` this function will consider the URLs `/foo#one` and `/foo#two` to be equal.
    @return {boolean}
    @experimental
    */
    function isLocation(url, options) {
        options = u.merge(ADDITIONAL_NORMALIZE_OPTIONS_FOR_COMPARISON, options);
        return normalizeURL(url, options) === currentLocation(options);
    }
    /*-
    Replaces the current history entry and updates the
    browser's location bar with the given URL.
  
    When the user navigates to the replaced history entry at a later time,
    Unpoly will [`replace`](/up.replace) the document body with
    the body from that URL.
  
    Note that functions like [`up.replace()`](/up.replace) or
    [`up.submit()`](/up.submit) will automatically update the
    browser's location bar for you.
  
    @function up.history.replace
    @param {string} url
    @internal
    */
    function replace(url, options = {}) {
        url = normalizeURL(url);
        if (manipulate('replaceState', url) && (options.event !== false)) {
            emit('up:location:changed', { url, reason: 'replace', log: `Replaced state for ${url}` });
        }
    }
    /*-
    Adds a new history entry and updates the browser's
    address bar with the given URL.
  
    When the user restores the new history entry later,
    Unpoly will replace a selector from `up.history.config.restoreTargets` with the body from that URL.
  
    Note that [fragment navigation](/navigation) will automatically update the
    browser's location bar for you.
  
    Does not add a history entry if the the given URL is already the current browser location.
  
    Emits event `up:location:changed`.
  
    @function up.history.push
    @param {string} url
      The URL for the history entry to be added.
    @experimental
    */
    function push(url) {
        url = normalizeURL(url);
        if (!isLocation(url) && manipulate('pushState', url)) {
            up.emit('up:location:changed', { url, reason: 'push', log: `Advanced to location ${url}` });
        }
    }
    /*-
    This event is [emitted](/up.emit) after the browser's address bar was updated with a new URL.
  
    There may be several reasons why the browser location was changed:
  
    - A fragment update changes history through [navigation](/navigation) or rendering with `{ history: true }`.
    - The user uses the back or forward buttons in their browser UI.
    - Programmatic calls to `up.history.push()`.
  
    When a [layer](/up.layer) has no [visible history](/up.Layer.prototype.history), following a link
    will not cause the browser's address bar to be updated. In this case no `up:location:changed` event will be emitted.
    However, a `up:layer:location:changed` will be emitted even if the address bar did not change.
  
    @event up:location:changed
    @param {string} event.url
      The URL for the history entry after the change.
    @param {string} event.reason
      The action that caused this change in [history state](https://developer.mozilla.org/en-US/docs/Web/API/History/state).
  
      The value of this property is either `'push'`, `'pop'` or `'replace'`.
    @stable
    */
    function manipulate(method, url) {
        if (config.enabled) {
            const state = buildState();
            window.history[method](state, '', url);
            trackCurrentLocation();
            // Signal that manipulation was successful
            return true;
        }
    }
    function buildState() {
        return { up: {} };
    }
    async function restoreStateOnPop(state) {
        if (state?.up) {
            // The earlier URL has now been restored by the browser. This cannot be prevented.
            let url = currentLocation();
            await up.render({
                url,
                history: true,
                // (1) While the browser has already restored the earlier URL, we must still
                //     pass it to render() so the current layer can track the new URL.
                // (2) Since we're passing the current URL, up.history.push() will not add another state.
                // (2) Pass the current URL to ensure that this exact URL is being rendered
                //     and not something derived from the up.Response.
                location: url,
                // Don't replace elements in a modal that might still be open
                // We will close all overlays and update the root layer.
                peel: true,
                layer: 'root',
                target: config.restoreTargets,
                cache: true,
                scroll: 'restore',
                // Since the URL was already changed by the browser, don't save scroll state.
                saveScroll: false
            });
            url = currentLocation();
            emit('up:location:changed', { url, reason: 'pop', log: `Restored location ${url}` });
        }
        else {
            up.puts('pop', 'Ignoring a state not pushed by Unpoly (%o)', state);
        }
    }
    function onPop(event) {
        trackCurrentLocation();
        up.viewport.saveScroll({ location: previousLocation });
        const { state } = event;
        restoreStateOnPop(state);
    }
    function emit(...args) {
        const historyLayer = u.find(up.layer.stack.reversed(), 'history');
        return historyLayer.emit(...args);
    }
    function register() {
        window.addEventListener('popstate', onPop);
        // Unpoly replaces the initial page state so it can later restore it when the user
        // goes back to that initial URL. However, if the initial request was a POST,
        // Unpoly will wrongly assume that it can restore the state by reloading with GET.
        if (up.protocol.initialRequestMethod() === 'GET') {
            // Replace the vanilla state of the initial page load with an Unpoly-enabled state
            replace(currentLocation(), { event: false });
        }
    }
    up.on('up:framework:boot', function () {
        if ('jasmine' in window) {
            // Can't delay this in tests.
            register();
        }
        else {
            // Defeat an unnecessary popstate that some browsers trigger
            // on pageload (Safari, Chrome < 34).
            // We should check in 2023 if we can remove this.
            setTimeout(register, 100);
        }
    });
    /*-
    Changes the link's destination so it points to the previous URL.
  
    Note that this will *not* call `location.back()`, but will set
    the link's `[up-href]` attribute to the actual, previous URL.
  
    If no previous URL is known, the link will not be changed.
  
    ### Example
  
    This link ...
  
    ```html
    <a href="/default" up-back>
      Go back
    </a>
    ```
  
    ... will be transformed to:
  
    ```html
    <a href="/default" up-href="/previous-page" up-scroll="restore" up-follow>
      Go back
    </a>
    ```
  
    @selector a[up-back]
    @stable
    */
    up.macro('a[up-back], [up-href][up-back]', function (link) {
        if (previousLocation) {
            e.setMissingAttrs(link, {
                'up-href': previousLocation,
                'up-scroll': 'restore'
            });
            link.removeAttribute('up-back');
            up.link.makeFollowable(link);
        }
    });
    up.on('up:framework:reset', reset);
    return {
        config,
        push,
        replace,
        get location() { return currentLocation(); },
        get previousLocation() { return previousLocation; },
        normalizeURL,
        isLocation
    };
})();


/***/ }),
/* 78 */
/***/ ((__unused_webpack_module, __unused_webpack_exports, __webpack_require__) => {

__webpack_require__(79);
const u = up.util;
const e = up.element;
/*-
Fragment API
===========

The `up.fragment` module offers a high-level JavaScript API to work with DOM elements.

A fragment is an element with some additional properties that are useful in the context of
a server-rendered web application:

- Fragments are [identified by a CSS selector](/up.fragment.toTarget), like a `.class` or `#id`.
- Fragments are usually updated by a [link](/a-up-follow) for [form](/form-up-submit) that targets their selector.
  When the server renders HTML with a matching element, the fragment is swapped with a new version.
- As fragments enter the page they are automatically [compiled](/up.compiler) to activate JavaScript behavior.
- Fragment changes may be [animated](/up.motion).
- Fragments are placed on a [layer](/up.layer) that is isolated from other layers.
  Unpoly features will only see or change fragments from the [current layer](/up.layer.current)
  unless you [explicitly target another layer](/layer-option).
- Fragments [know the URL from where they were loaded](/up.fragment.source).
  They can be [reloaded](/up.reload) or [polled periodically](/up-poll).

For low-level DOM utilities that complement the browser's native API, see `up.element`.

@see navigation
@see focus-option
@see csp

@see up.render
@see up.navigate
@see up.destroy
@see up.reload
@see up.fragment.get
@see up.hello

@module up.fragment
*/
up.fragment = (function () {
    /*-
    Configures defaults for fragment updates.
  
    @property up.fragment.config
  
    @param {Array<string>} [config.mainTargets=['[up-main]', 'main', ':layer']]
      An array of CSS selectors matching default render targets.
  
      When no other render target is given, Unpoly will update the first selector matching both
      the current page and the server response.
  
      When [navigating](/navigation) to a main target, Unpoly will automatically
      [reset scroll positions](/scroll-option) and
      [update the browser history](/up.render#options.history).
  
      This property is aliased as [`up.layer.config.any.mainTargets`](/up.layer.config#config.any.mainTargets).
  
    @param {Array<string|RegExp>} [config.badTargetClasses]
      An array of class names that should be ignored when
      [deriving a target selector from a fragment](/up.fragment.toTarget).
  
      The class names may also be passed as a regular expression.
  
    @param {Object} [config.navigateOptions]
      An object of default options to apply when [navigating](/navigation).
  
    @param {boolean} [config.matchAroundOrigin]
      Whether to match an existing fragment around the triggered link.
  
      If set to `false` Unpoly will replace the first fragment
      matching the given target selector in the link's [layer](/up.layer).
  
    @param {Array<string>} [config.autoHistoryTargets]
      When an updated fragments contain an element matching one of the given CSS selectors, history will be updated with `{ history: 'auto' }`.
  
      By default Unpoly will auto-update history when updating a [main target](#config.mainTargets).
  
    @param {boolean|string|Function(Element)} [config.autoScroll]
      How to scroll after updating a fragment with `{ scroll: 'auto' }`.
  
      See [scroll option](/scroll-option) for a list of allowed values.
  
      The default configuration tries, in this order:
  
      - If the URL has a `#hash`, scroll to the hash.
      - If updating a [main target](/up-main), reset scroll positions.
  
    @param {boolean|string|Function(Element)} [config.autoFocus]
      How to focus when updating a fragment with `{ focus: 'auto' }`.
  
      See [focus option](/focus-option) for a list of allowed values.
  
      The default configuration tries, in this order:
  
      - Focus a `#hash` in the URL.
      - Focus an `[autofocus]` element in the new fragment.
      - If focus was lost with the old fragment, focus the new fragment.
      - If updating a [main target](/up-main), focus the new fragment.
  
    @param {boolean} [config.runScripts=false]
      Whether to execute `<script>` tags in updated fragments.
  
      Scripts will load asynchronously, with no guarantee of execution order.
  
      If you set this to `true`, mind that the `<body>` element is a default
      [main target](/up-main). If you are including your global application scripts
      at the end of your `<body>`
      for performance reasons, swapping the `<body>` will re-execute these scripts.
      In that case you must configure a different main target that does not include
      your application scripts.
  
    @stable
    */
    const config = new up.Config(() => ({
        badTargetClasses: [/^up-/],
        // These defaults will be set to both success and fail options
        // if { navigate: true } is given.
        navigateOptions: {
            solo: true,
            feedback: true,
            cache: 'auto',
            fallback: true,
            focus: 'auto',
            scroll: 'auto',
            history: 'auto',
            peel: true // UpdateLayer/OpenLayer
        },
        matchAroundOrigin: true,
        runScripts: false,
        autoHistoryTargets: [':main'],
        autoFocus: ['hash', 'autofocus', 'main-if-main', 'target-if-lost'],
        autoScroll: ['hash', 'layer-if-main']
    }));
    // Users who are not using layers will prefer settings default targets
    // as up.fragment.config.mainTargets instead of up.layer.config.any.mainTargets.
    u.delegate(config, 'mainTargets', () => up.layer.config.any);
    function reset() {
        config.reset();
    }
    /*-
    Returns the URL the given element was retrieved from.
  
    If the given element was never directly updated, but part of a larger fragment update,
    the closest known source of an ancestor element is returned.
  
    ### Example
  
    In the HTML below, the element `#one` was loaded from the URL `/foo`:
  
    ```html
    <div id="one" up-source"/foo">
    <div id="two">...</div>
    </div>
    ```
  
    We can now ask for the source of an element:
  
    ```javascript
    up.fragment.source('#two') // returns '/foo'
    ```
  
    @function up.fragment.source
    @param {Element|string} element
      The element or CSS selector for which to look up the source URL.
    @return {string|undefined}
    @stable
    */
    function sourceOf(element, options = {}) {
        element = getSmart(element, options);
        return e.closestAttr(element, 'up-source');
    }
    /*-
    Returns a timestamp for the last modification of the content in the given element.
  
    @function up.fragment.time
    @param {Element} element
    @return {string}
    @internal
    */
    function timeOf(element) {
        return e.closestAttr(element, 'up-time') || '0';
    }
    /*-
    Sets the time when the fragment's underlying data was last changed.
  
    This can be used to avoid rendering unchanged HTML when [reloading](/up.reload)
    a fragment. This saves <b>CPU time</b> and reduces the <b>bandwidth cost</b> for a
    request/response exchange to **~1 KB**.
  
    ## Example
  
    Let's say we display a list of recent messages.
    We use the `[up-poll]` attribute to reload the `.messages` fragment every 30 seconds:
  
    ```html
    <div class="messages" up-poll>
    ...
    </div>
    ```
  
    The list is now always up to date. But most of the time there will not be new messages,
    and we waste resources sending the same unchanged HTML from the server.
  
    We can improve this by setting an `[up-time]` attribute and the message list.
    The attribute value is the time of the most recent message.
  
    The time is encoded as the number of seconds since [Unix epoch](https://en.wikipedia.org/wiki/Unix_time).
    When, for instance, the last message in a list was received from December 24th, 1:51:46 PM UTC,
    we use the following HTML:
  
    ```html
    <div class="messages" up-time="1608730818" up-poll>
    ...
    </div>
    ```
  
    When reloading Unpoly will echo the `[up-time]` timestamp in an `X-Up-Reload-From-Time` header:
  
    ```http
    X-Up-Reload-From-Time: 1608730818
    ```
  
    The server can compare the time from the request with the time of the last data update.
    If no more recent data is available, the server can render nothing and respond with
    an [`X-Up-Target: :none`](/X-Up-Target) header.
  
    Here is an example with [unpoly-rails](https://unpoly.com/install/ruby):
  
    ```ruby
    class MessagesController < ApplicationController
  
      def index
        if up.reload_from_time == current_user.last_message_at
          up.render_nothing
        else
          @messages = current_user.messages.order(time: :desc).to_a
          render 'index'
        end
      end
  
    end
    ```
  
    @selector [up-time]
    @param {string} up-time
      The number of seconds between the [Unix epoch](https://en.wikipedia.org/wiki/Unix_time).
      and the time when the element's underlying data was last changed.
    @experimental
    */
    /*-
    Sets this element's source URL for [reloading](/up.reload) and [polling](/up-poll)
  
    When an element is reloaded, Unpoly will make a request from the URL
    that originally brought the element into the DOM. You may use `[up-source]` to
    use another URL instead.
  
    ### Example
  
    Assume an application layout with an unread message counter.
    You use `[up-poll]` to refresh the counter every 30 seconds.
  
    By default this would make a request to the URL that originally brought the
    counter element into the DOM. To save the server from rendering a lot of
    unused HTML, you may poll from a different URL like so:
  
    ```html
    <div class="unread-count" up-poll up-source="/unread-count">
      2 new messages
    </div>
    ```
  
    @selector [up-source]
    @param {string} up-source
      The URL from which to reload this element.
    @stable
    */
    /*-
    Replaces elements on the current page with matching elements from a server response or HTML string.
  
    The current and new elements must both match the same CSS selector.
    The selector is either given as `{ target }` option,
    or a [main target](/up-main) is used as default.
  
    See the [fragment placement](/fragment-placement) selector for many examples for how you can target content.
  
    This function has many options to enable scrolling, focus, request cancelation and other side
    effects. These options are all disabled by default and must be opted into one-by-one. To enable
    defaults that a user would expects for navigation (like clicking a link),
    pass [`{ navigate: true }`](#options.navigate) or use `up.navigate()` instead.
  
    ### Passing the new fragment
  
    The new fragment content can be passed as one of the following options:
  
    - [`{ url }`](#options.url) fetches and renders content from the server
    - [`{ document }`](#options.document) renders content from a given HTML document string or partial document
    - [`{ fragment }`](#options.fragment) renders content from a given HTML string that only contains the new fragment
    - [`{ content }`](#options.content) replaces the targeted fragment's inner HTML with the given HTML string
  
    ### Example
  
    Let's say your current HTML looks like this:
  
    ```html
    <div class="one">old one</div>
    <div class="two">old two</div>
    ```
  
    We now replace the second `<div>` by targeting its CSS class:
  
    ```js
    up.render({ target: '.two', url: '/new' })
    ```
  
    The server renders a response for `/new`:
  
    ```html
    <div class="one">new one</div>
    <div class="two">new two</div>
    ```
  
    Unpoly looks for the selector `.two` in the response and [implants](/up.extract) it into
    the current page. The current page now looks like this:
  
    ```html
    <div class="one">old one</div>
    <div class="two">new two</div>
    ```
  
    Note how only `.two` has changed. The update for `.one` was
    discarded, since it didn't match the selector.
  
    ### Events
  
    Unpoly will emit events at various stages of the rendering process:
  
    - `up:fragment:destroyed`
    - `up:fragment:loaded`
    - `up:fragment:inserted`
  
    @function up.render
  
    @param {string|Element|jQuery|Array<string>} [target]
      The CSS selector to update.
  
      If omitted a [main target](/up-main) will be rendered.
  
      You may also pass a DOM element or jQuery element here, in which case a selector
      will be [inferred from the element attributes](/up.fragment.toTarget). The given element
      will also be used as [`{ origin }`](#options.origin) for the fragment update.
  
      You may also pass an array of selector alternatives. The first selector
      matching in both old and new content will be used.
  
      Instead of passing the target as the first argument, you may also pass it as
      a [´{ target }`](#options.target) option..
  
    @param {string|Element|jQuery|Array<string>} [options.target]
      The CSS selector to update.
  
      See documentation for the [`target`](#target) parameter.
  
    @param {string|boolean} [options.fallback=false]
      Specifies behavior if the [target selector](/up.render#options.target) is missing from the current page or the server response.
  
      If set to a CSS selector string, Unpoly will attempt to replace that selector instead.
  
      If set to `true` Unpoly will attempt to replace a [main target](/up-main) instead.
  
      If set to `false` Unpoly will immediately reject the render promise.
  
    @param {boolean} [options.navigate=false]
      Whether this fragment update is considered [navigation](/navigation).
  
    @param {string} [options.url]
      The URL to fetch from the server.
  
      Instead of making a server request, you may also pass an existing HTML string as
      [`{ document }`](#options.document), [`{ fragment }`](#options.fragment) or
      [`{ content }`](#options.content) option.
  
    @param {string} [options.method='get']
      The HTTP method to use for the request.
  
      Common values are `'get'`, `'post'`, `'put'`, `'patch'` and `'delete`'.
      The value is case insensitive.
  
    @param {Object|FormData|string|Array} [options.params]
      Additional [parameters](/up.Params) that should be sent as the request's
      [query string](https://en.wikipedia.org/wiki/Query_string) or payload.
  
      When making a `GET` request to a URL with a query string, the given `{ params }` will be added
      to the query parameters.
  
    @param {Object} [options.headers={}]
      An object with additional request headers.
  
      Note that Unpoly will by default send a number of custom request headers.
      E.g. the `X-Up-Target` header includes the targeted CSS selector.
      See `up.protocol` and `up.network.config.requestMetaKeys` for details.
  
    @param {string|Element} [options.content]
      The new [inner HTML](https://developer.mozilla.org/en-US/docs/Web/API/Element/innerHTML)
      for the fragment.
  
    @param {string|Element} [options.fragment]
      A string of HTML comprising *only* the new fragment's [outer HTML](https://developer.mozilla.org/en-US/docs/Web/API/Element/outerHTML).
  
      The `{ target }` selector will be derived from the root element in the given
      HTML:
  
      ```js
      // This will update .foo
      up.render({ fragment: '<div class=".foo">inner</div>' })
      ```
  
      If your HTML string contains other fragments that will not be rendered, use
      the [`{ document }`](#options.document) option instead.
  
      If your HTML string comprises only the new fragment's [inner HTML](https://developer.mozilla.org/en-US/docs/Web/API/Element/innerHTML),
      consider the [`{ content }`](#options.content) option.
  
    @param {string|Element|Document} [options.document]
      A string of HTML containing the new fragment.
  
      The string may contain other HTML, but only the element matching the
      `{ target }` selector will be extracted and placed into the page.
      Other elements will be discarded.
  
      If your HTML string comprises only the new fragment, consider the [`{ fragment }`](#options.fragment)
      option instead. With `{ fragment }` you don't need to pass a `{ target }`, since
      Unpoly can derive it from the root element in the given HTML.
  
      If your HTML string comprises only the new fragment's [inner HTML](https://developer.mozilla.org/en-US/docs/Web/API/Element/innerHTML),
      consider the [`{ content }`](#options.content) option.
  
    @param {string} [options.fail='auto']
      How to render a server response with an error code.
  
      Any HTTP status code other than 2xx is considered an error code.
  
      See [handling server errors](/server-errors) for details.
  
    @param {boolean|string} [options.history]
      Whether the browser URL and window title will be updated.
  
      If set to `true`, the history will always be updated, using the title and URL from
      the server response, or from given `{ title }` and `{ location }` options.
  
      If set to `'auto'` history will be updated if the `{ target }` matches
      a selector in `up.fragment.config.autoHistoryTargets`. By default this contains all
      [main targets](/up-main).
  
      If set to `false`, the history will remain unchanged.
  
    @param {string} [options.title]
      An explicit document title to use after rendering.
  
      By default the title is extracted from the response's `<title>` tag.
      You may also pass `{ title: false }` to explicitly prevent the title from being updated.
  
      Note that the browser's window title will only be updated it you also
      pass a [`{ history }`](#options.history) option.
  
    @param {string} [options.location]
      An explicit URL to use after rendering.
  
      By default Unpoly will use the `{ url }` or the final URL after the server redirected.
      You may also pass `{ location: false }` to explicitly prevent the URL from being updated.
  
      Note that the browser's URL will only be updated it you also
      pass a [`{ history }`](#options.history) option.
  
    @param {string} [options.transition]
      The name of an [transition](/up.motion) to morph between the old and few fragment.
  
      If you are [prepending or appending content](/fragment-placement#appending-or-prepending-content),
      use the `{ animation }` option instead.
  
    @param {string} [options.animation]
      The name of an [animation](/up.motion) to reveal a new fragment when
      [prepending or appending content](/fragment-placement#appending-or-prepending-content).
  
      If you are replacing content (the default), use the `{ transition }` option instead.
  
    @param {number} [options.duration]
      The duration of the transition or animation (in millisconds).
  
    @param {string} [options.easing]
      The timing function that accelerates the transition or animation.
  
      See [MDN documentation](https://developer.mozilla.org/en-US/docs/Web/CSS/transition-timing-function)
      for a list of available timing functions.
  
    @param {boolean} [options.cache]
      Whether to read from and write to the [cache](/up.request#caching).
  
      With `{ cache: true }` Unpoly will try to re-use a cached response before connecting
      to the network. If no cached response exists, Unpoly will make a request and cache
      the server response.
  
      Also see [`up.request({ cache })`](/up.request#options.cache).
  
    @param {boolean|string} [options.clearCache]
      Whether existing [cache](/up.request#caching) entries will be [cleared](/up.cache.clear) with this request.
  
      Defaults to the result of `up.network.config.clearCache`, which
      defaults to clearing the entire cache after a non-GET request.
  
      To only uncache some requests, pass an [URL pattern](/url-patterns) that matches requests to uncache.
      You may also pass a function that accepts an existing `up.Request` and returns a boolean value.
  
    @param {boolean|string|Function(request): boolean} [options.solo]
      With `{ solo: true }` Unpoly will [abort](/up.network.abort) all other requests before laoding the new fragment.
  
      To only abort some requests, pass an [URL pattern](/url-patterns) that matches requests to abort.
      You may also pass a function that accepts an existing `up.Request` and returns a boolean value.
  
    @param {Element|jQuery} [options.origin]
      The element that triggered the change.
  
      When multiple elements in the current page match the `{ target }`,
      Unpoly will replace an element in the [origin's vicinity](/fragment-placement).
  
      The origin's selector will be substituted for `:origin` in a target selector.
  
    @param {string|up.Layer|Element} [options.layer='origin current']
      The [layer](/up.layer) in which to match and render the fragment.
  
      See [layer option](/layer-option) for a list of allowed values.
  
      To [open the fragment in a new overlay](/opening-overlays), pass `{ layer: 'new' }`.
      In this case options for `up.layer.open()` may also be used.
  
    @param {boolean} [options.peel]
      Whether to close overlays obstructing the updated layer when the fragment is updated.
  
      This is only relevant when updating a layer that is not the [frontmost layer](/up.layer.front).
  
    @param {Object} [options.context]
      An object that will be merged into the [context](/context) of the current layer once the fragment is rendered.
  
    @param {boolean} [options.keep=true]
      Whether [`[up-keep]`](/up-keep) elements will be preserved in the updated fragment.
  
    @param {boolean} [options.hungry=true]
      Whether [`[up-hungry]`](/up-hungry) elements outside the updated fragment will also be updated.
  
    @param {boolean|string|Element|Function} [options.scroll]
      How to scroll after the new fragment was rendered.
  
      See [scroll option](/scroll-option) for a list of allowed values.
  
    @param {boolean} [options.saveScroll=true]
      Whether to save scroll positions before updating the fragment.
  
      Saved scroll positions can later be restored with [`{ scroll: 'restore' }`](/scroll-option#restoring-scroll-positions).
  
    @param {boolean|string|Element|Function} [options.focus]
      What to focus after the new fragment was rendered.
  
      See [focus option](/focus-option) for a list of allowed values.
  
    @param {string} [options.confirm]
      A message the user needs to confirm before fragments are updated.
  
      The message will be shown as a [native browser prompt](https://developer.mozilla.org/en-US/docs/Web/API/Window/prompt).
  
      If the user does not confirm the render promise will reject and no fragments will be updated.
  
    @param {boolean|Element} [options.feedback]
      Whether to give the [`{ origin }`](#options.origin) element an `.up-active` class
      while loading and rendering content.
  
      May also pass an element which should receive the `.up-active` class.
  
    @param {Function(Event)} [options.onLoaded]
      A callback that will be run when when the server responds with new HTML,
      but before the HTML is rendered.
  
      The callback argument is a preventable `up:fragment:loaded` event.
  
    @param {Function()} [options.onFinished]
      A callback that will be run when all animations have concluded and
      elements were removed from the DOM tree.
  
    @return {Promise<up.RenderResult>}
      A promise that fulfills when the page has been updated.
  
      If the update is animated, the promise will be resolved *before* the existing element was
      removed from the DOM tree. The old element will be marked with the `.up-destroying` class
      and removed once the animation finishes. To run code after the old element was removed,
      pass an `{ onFinished }` callback.
  
      The promise will fulfill with an `up.RenderResult` that contains
      references to the updated fragments and layer.
  
    @stable
    */
    const render = up.mockable((...args) => {
        // Convert thrown errors into rejected promises.
        // Convert non-promise values into a resolved promise.
        return u.asyncify(function () {
            let options = parseTargetAndOptions(args);
            options = up.RenderOptions.preprocess(options);
            up.browser.assertConfirmed(options);
            let guardEvent = u.pluckKey(options, 'guardEvent');
            if (guardEvent) {
                // Allow guard event handlers to manipulate render options for the default behavior.
                //
                // Note that we have removed { guardEvent } from options to not recursively define
                // guardEvent.renderOptions.guardEvent. This would cause an infinite loop for event
                // listeners that prevent the default and re-render.
                guardEvent.renderOptions = options;
                up.event.assertEmitted(guardEvent, { target: options.origin });
            }
            up.RenderOptions.assertContentGiven(options);
            return (options.url ? renderRemoteContent : renderLocalContent)(options);
        });
    });
    function renderRemoteContent(options) {
        // Rendering a remote URL is an async operation.
        // We give feedback (.up-active) while the fragment is loading.
        let execute = () => new up.Change.FromURL(options).execute();
        return up.feedback.aroundForOptions(options, execute);
    }
    function renderLocalContent(options) {
        // When we have a given { url }, the { solo } option is honored by up.request().
        // But up.request() is never called when we have local content given as { document },
        // { content } or { fragment }. Hence we abort here.
        up.network.mimicLocalRequest(options);
        // (1) No need to give feedback as local changes are sync.
        // (2) Value will be converted to a fulfilled promise by up.util.asyncify() in render().
        return new up.Change.FromContent(options).execute();
    }
    /*-
    [Navigates](/navigation) to the given URL by updating a major fragment in the current page.
  
    `up.navigate()` will mimic a click on a vanilla `<a href>` link to satisfy user expectations
    regarding scrolling, focus, request cancelation and [many other side effects](/navigation).
  
    Instead of calling `up.navigate()` you may also call `up.render({ navigate: true }`) option
    for the same effect.
  
    @function up.navigate
    @param {string|Element|jQuery} [target]
      The CSS selector to update.
  
      If omitted a [main target](/up-main) will be rendered.
  
      You can also pass a DOM element or jQuery element here, in which case a selector
      will be [inferred from the element attributes](/up.fragment.toTarget). The given element
      will also be set as the `{ origin }` option.
  
      Instead of passing the target as the first argument, you may also pass it as
      [´{ target }` option](/up.render#options.target).
    @param {Object} [options]
      See options for `up.render()`.
    @return {Promise<up.RenderResult>}
      A promise that fulfills when the page has been updated.
  
      For details, see return value for `up.render()`.
    @stable
    */
    const navigate = up.mockable((...args) => {
        const options = parseTargetAndOptions(args);
        return render({ ...options, navigate: true });
    });
    /*-
    This event is [emitted](/up.emit) when the server responds with the HTML, before
    the HTML is used to [change a fragment](/up.render).
  
    Event listeners may call `event.preventDefault()` on an `up:fragment:loaded` event
    to prevent any changes to the DOM and browser history. This is useful to detect
    an entirely different page layout (like a maintenance page or fatal server error)
    which should be open with a full page load:
  
    ```js
    up.on('up:fragment:loaded', (event) => {
      let isMaintenancePage = event.response.getHeader('X-Maintenance')
  
      if (isMaintenancePage) {
        // Prevent the fragment update and don't update browser history
        event.preventDefault()
  
        // Make a full page load for the same request.
        event.request.loadPage()
      }
    })
    ```
  
    Instead of preventing the update, listeners may also access the `event.renderOptions` object
    to mutate options to the `up.render()` call that will process the server response.
  
    @event up:fragment:loaded
  
    @param event.preventDefault()
      Event listeners may call this method to prevent the fragment change.
  
    @param {up.Request} event.request
      The original request to the server.
  
    @param {up.Response} event.response
      The server response.
  
    @param {Element} [event.origin]
      The link or form element that caused the fragment update.
  
    @param {Object} event.renderOptions
      Options for the `up.render()` call that will process the server response.
    @stable
    */
    /*-
    Elements with an `up-keep` attribute will be persisted during
    [fragment updates](/up.fragment).
  
    The element you're keeping should have an umambiguous class name, ID or `[up-id]`
    attribute so Unpoly can find its new position within the page update.
  
    Emits events [`up:fragment:keep`](/up:fragment:keep) and [`up:fragment:kept`](/up:fragment:kept).
  
    ### Example
  
    The following `<audio>` element will be persisted through fragment
    updates as long as the responses contain an element matching `#player`:
  
  
    ```html
    <audio id="player" up-keep src="song.mp3"></audio>
    ```
  
    ### Controlling if an element will be kept
  
    Unpoly will **only** keep an existing element if:
  
    - The existing element has an `up-keep` attribute
    - The response contains an element matching the CSS selector of the existing element
    - The matching element *also* has an `up-keep` attribute
    - The [`up:fragment:keep`](/up:fragment:keep) event that is [emitted](/up.emit) on the existing element
    is not prevented by a event listener.
  
    Let's say we want only keep an `<audio>` element as long as it plays
    the same song (as identified by the tag's `src` attribute).
  
    On the client we can achieve this by listening to an `up:keep:fragment` event
    and preventing it if the `src` attribute of the old and new element differ:
  
    ```js
    up.compiler('audio', function(element) {
      element.addEventListener('up:fragment:keep', function(event) {
        if element.getAttribute('src') !== event.newElement.getAttribute('src') {
          event.preventDefault()
        }
      })
    })
    ```
  
    If we don't want to solve this on the client, we can achieve the same effect
    on the server. By setting the value of the `up-keep` attribute we can
    define the CSS selector used for matching elements.
  
    ```html
    <audio up-keep="audio[src='song.mp3']" src="song.mp3"></audio>
    ```
  
    Now, if a response no longer contains an `<audio src="song.mp3">` tag, the existing
    element will be destroyed and replaced by a fragment from the response.
  
    @selector [up-keep]
    @param up-on-keep
      Code to run before an existing element is kept during a page update.
  
      The code may use the variables `event` (see `up:fragment:keep`),
      `this` (the old fragment), `newFragment` and `newData`.
    @stable
    */
    /*-
    This event is [emitted](/up.emit) before an existing element is [kept](/up-keep) during
    a page update.
  
    Event listeners can call `event.preventDefault()` on an `up:fragment:keep` event
    to prevent the element from being persisted. If the event is prevented, the element
    will be replaced by a fragment from the response.
  
    @event up:fragment:keep
    @param event.preventDefault()
      Event listeners may call this method to prevent the element from being preserved.
    @param {Element} event.target
      The fragment that will be kept.
    @param {Element} event.newFragment
      The discarded element.
    @param {Object} event.newData
      The value of the [`up-data`](/up-data) attribute of the discarded element,
      parsed as a JSON object.
    @stable
    */
    /*-
    This event is [emitted](/up.emit) when an existing element has been [kept](/up-keep)
    during a page update.
  
    Event listeners can inspect the discarded update through `event.newElement`
    and `event.newData` and then modify the preserved element when necessary.
  
    @event up:fragment:kept
    @param {Element} event.target
      The fragment that has been kept.
    @param {Element} event.newFragment
      The discarded fragment.
    @param {Object} event.newData
      The value of the [`up-data`](/up-data) attribute of the discarded fragment,
      parsed as a JSON object.
    @stable
    */
    /*-
    Manually compiles a page fragment that has been inserted into the DOM
    by external code.
  
    All registered [compilers](/up.compiler) and [macros](/up.macro) will be called
    with matches in the given `element`.
  
    **As long as you manipulate the DOM using Unpoly, you will never
    need to call `up.hello()`.** You only need to use `up.hello()` if the
    DOM is manipulated without Unpoly' involvement, e.g. by setting
    the `innerHTML` property:
  
    ```html
    element = document.createElement('div')
    element.innerHTML = '... HTML that needs to be activated ...'
    up.hello(element)
    ```
  
    This function emits the [`up:fragment:inserted`](/up:fragment:inserted)
    event.
  
    @function up.hello
    @param {Element|jQuery} element
    @param {Element|jQuery} [options.origin]
    @return {Element}
      The compiled element
    @stable
    */
    function hello(element, options = {}) {
        // If passed a selector, up.fragment.get() will prefer a match on the current layer.
        element = getSmart(element);
        // Callers may pass descriptions of child elements that were [kept](/up-keep)
        // as { options.keepPlans }. For these elements up.hello() emits an event
        // up:fragment:kept instead of up:fragment:inserted.
        //
        // We will also pass an array of kept child elements to up.hello() as { skip }
        // so they won't be compiled a second time.
        const keepPlans = options.keepPlans || [];
        const skip = keepPlans.map(function (plan) {
            emitFragmentKept(plan);
            return plan.oldElement; // the kept element
        });
        up.syntax.compile(element, { skip, layer: options.layer });
        emitFragmentInserted(element, options);
        return element;
    }
    /*-
    When any page fragment has been [inserted or updated](/up.replace),
    this event is [emitted](/up.emit) on the fragment.
  
    If you're looking to run code when a new fragment matches
    a selector, use `up.compiler()` instead.
  
    ### Example
  
    ```js
    up.on('up:fragment:inserted', function(event, fragment) {
      console.log("Looks like we have a new %o!", fragment)
    })
    ```
  
    @event up:fragment:inserted
    @param {Element} event.target
      The fragment that has been inserted or updated.
    @stable
    */
    function emitFragmentInserted(element, options) {
        return up.emit(element, 'up:fragment:inserted', {
            log: ['Inserted fragment %o', element],
            origin: options.origin
        });
    }
    function emitFragmentKeep(keepPlan) {
        const log = ['Keeping fragment %o', keepPlan.oldElement];
        const callback = e.callbackAttr(keepPlan.oldElement, 'up-on-keep', ['newFragment', 'newData']);
        return emitFromKeepPlan(keepPlan, 'up:fragment:keep', { log, callback });
    }
    function emitFragmentKept(keepPlan) {
        const log = ['Kept fragment %o', keepPlan.oldElement];
        return emitFromKeepPlan(keepPlan, 'up:fragment:kept', { log });
    }
    function emitFromKeepPlan(keepPlan, eventType, emitDetails) {
        const keepable = keepPlan.oldElement;
        const event = up.event.build(eventType, {
            newFragment: keepPlan.newElement,
            newData: keepPlan.newData
        });
        return up.emit(keepable, event, emitDetails);
    }
    function emitFragmentDestroyed(fragment, options) {
        const log = options.log ?? ['Destroyed fragment %o', fragment];
        const parent = options.parent || document;
        return up.emit(parent, 'up:fragment:destroyed', { fragment, parent, log });
    }
    function isDestroying(element) {
        return !!e.closest(element, '.up-destroying');
    }
    const isNotDestroying = u.negate(isDestroying);
    /*-
    Returns the first fragment matching the given selector.
  
    This function differs from `document.querySelector()` and `up.element.get()`:
  
    - This function only selects elements in the [current layer](/up.layer.current).
      Pass a `{ layer }`option to match elements in other layers.
    - This function ignores elements that are being [destroyed](/up.destroy) or that are being
      removed by a [transition](/up.morph).
    - This function prefers to match elements in the vicinity of a given `{ origin }` element (optional).
    - This function supports non-standard CSS selectors like `:main` and `:has()`.
  
    If no element matches these conditions, `undefined` is returned.
  
    ### Example: Matching a selector in a layer
  
    To select the first element with the selector `.foo` on the [current layer](/up.layer.current):
  
    ```js
    let foo = up.fragment.get('.foo')
    ```
  
    You may also pass a `{ layer }` option to match elements within another layer:
  
    ```js
    let foo = up.fragment.get('.foo', { layer: 'any' })
    ```
  
    ### Example: Matching the descendant of an element
  
    To only select in the descendants of an element, pass a root element as the first argument:
  
    ```js
    let container = up.fragment.get('.container')
    let fooInContainer = up.fragment.get(container, '.foo')
    ```
  
    ### Example: Matching around an origin element
  
    When processing a user interaction, it is often helpful to match elements around the link
    that's being clicked or the form field that's being changed. In this case you may pass
    the triggering element as `{ origin }` element.
  
    Assume the following HTML:
  
    ```html
    <div class="element"></div>
    <div class="element">
    <a href="..."></a>
    </div>
    ```
  
    When processing an event for the `<a href"...">` you can pass the link element
    as `{ origin }` to match the closest element in the link's ancestry:
  
    ```js
    let link = event.target
    up.fragment.get('.element') // returns the first .element
    up.fragment.get('.element', { origin: link }) // returns the second .element
    ```
  
    When the link's does not have an ancestor matching `.element`,
    Unpoly will search the entire layer for `.element`.
  
    ### Example: Matching an origin sibling
  
    When processing a user interaction, it is often helpful to match elements
    within the same container as the the link that's being clicked or the form field that's
    being changed.
  
    Assume the following HTML:
  
    ```html
    <div class="element" id="one">
      <div class="inner"></div>
    </div>
    <div class="element" id="two">
      <a href="..."></a>
      <div class="inner"></div>
    </div>
    ```
  
    When processing an event for the `<a href"...">` you can pass the link element
    as `{ origin }` to match within the link's container:
  
    ```js
    let link = event.target
    up.fragment.get('.element .inner') // returns the first .inner
    up.fragment.get('.element .inner', { origin: link }) // returns the second .inner
    ```
  
    Note that when the link's `.element` container does not have a child `.inner`,
    Unpoly will search the entire layer for `.element .inner`.
  
    ### Similar features
  
    - The [`.up-destroying`](/up-destroying) class is assigned to elements during their removal animation.
    - The [`up.element.get()`](/up.element.get) function simply returns the first element matching a selector
    without filtering by layer or destruction state.
  
    @function up.fragment.get
    @param {Element|jQuery} [root=document]
      The root element for the search. Only the root's children will be matched.
  
      May be omitted to search through all elements in the `document`.
    @param {string} selector
      The selector to match.
    @param {string} [options.layer='current']
      The layer in which to select elements.
  
      See `up.layer.get()` for a list of supported layer values.
  
      If a root element was passed as first argument, this option is ignored and the
      root element's layer is searched.
    @param {string|Element|jQuery} [options.origin]
      An second element or selector that can be referenced as `&` in the first selector.
    @return {Element|undefined}
      The first matching element, or `undefined` if no such element matched.
    @stable
    */
    function getSmart(...args) {
        const options = u.extractOptions(args);
        const selector = args.pop();
        const root = args[0];
        if (u.isElementish(selector)) {
            // up.fragment.get(root: Element, element: Element, [options]) should just return element.
            // The given root and options are ignored. We also don't check if it's destroying.
            // We do use e.get() to unwrap a jQuery collection.
            return e.get(selector);
        }
        if (root) {
            // We don't match around { origin } if we're given a root for the search.
            return getDumb(root, selector, options);
        }
        // If we don't have a root element we will use a context-sensitive lookup strategy
        // that tries to match elements in the vicinity of { origin } before going through
        // the entire layer.
        return new up.FragmentFinder({
            selector,
            origin: options.origin,
            layer: options.layer
        }).find();
    }
    function getDumb(...args) {
        return getAll(...args)[0];
    }
    const CSS_HAS_SUFFIX_PATTERN = /:has\(([^)]+)\)$/;
    /*-
    Returns all elements matching the given selector, but
    ignores elements that are being [destroyed](/up.destroy) or that are being
    removed by a [transition](/up.morph).
  
    By default this function only selects elements in the [current layer](/up.layer.current).
    Pass a `{ layer }`option to match elements in other layers. See `up.layer.get()` for a list
    of supported layer values.
  
    Returns an empty list if no element matches these conditions.
  
    ### Example
  
    To select all elements with the selector `.foo` on the [current layer](/up.layer.current):
  
    ```js
    let foos = up.fragment.all('.foo')
    ```
  
    You may also pass a `{ layer }` option to match elements within another layer:
  
    ```js
    let foos = up.fragment.all('.foo', { layer: 'any' })
    ```
  
    To select in the descendants of an element, pass a root element as the first argument:
  
    ```js
    var container = up.fragment.get('.container')
    var foosInContainer = up.fragment.all(container, '.foo')
    ```
  
    ### Similar features
  
    - The [`.up-destroying`](/up-destroying) class is assigned to elements during their removal animation.
    - The [`up.element.all()`](/up.element.get) function simply returns the all elements matching a selector
      without further filtering.
  
    @function up.fragment.all
  
    @param {Element|jQuery} [root=document]
      The root element for the search. Only the root's children will be matched.
  
      May be omitted to search through all elements in the given [layer](#options.layer).
  
    @param {string} selector
      The selector to match.
  
    @param {string} [options.layer='current']
      The layer in which to select elements.
  
      See `up.layer.get()` for a list of supported layer values.
  
      If a root element was passed as first argument, this option is ignored and the
      root element's layer is searched.
  
    @param {string|Element|jQuery} [options.origin]
      An second element or selector that can be referenced as `&` in the first selector:
  
      var input = document.querySelector('input.email')
      up.fragment.get('fieldset:has(&)', { origin: input }) // returns the <fieldset> containing input
  
    @return {Element|undefined}
      The first matching element, or `undefined` if no such element matched.
    @stable
    */
    function getAll(...args) {
        const options = u.extractOptions(args);
        let selector = args.pop();
        const root = args[0];
        // (1) up.fragment.all(rootElement, selector) should find selector within
        //     the descendants of rootElement.
        // (2) up.fragment.all(selector) should find selector within the current layer.
        // (3) up.fragment.all(selector, { layer }) should find selector within the given layer(s).
        selector = parseSelector(selector, root, options);
        return selector.descendants(root || document);
    }
    /*-
    Your target selectors may use this pseudo-selector
    to replace an element with an descendant matching the given selector.
  
    ### Example
  
    `up.render('div:has(span)', { url: '...' })`  replaces the first `<div>` elements with at least one `<span>` among its descendants:
  
    ```html
    <div>
      <span>Will be replaced</span>
    </div>
    <div>
      Will NOT be replaced
    </div>
    ```
  
    ### Compatibility
  
    `:has()` is supported by target selectors like `a[up-target]` and `up.render({ target })`.
  
    As a [level 4 CSS selector](https://drafts.csswg.org/selectors-4/#relational),
    `:has()` [has yet to be implemented](https://caniuse.com/#feat=css-has)
    in native browser functions like [`document.querySelectorAll()`](https://developer.mozilla.org/en-US/docs/Web/API/Element/querySelectorAll).
  
    You can also use [`:has()` in jQuery](https://api.jquery.com/has-selector/).
  
    @selector :has()
    @stable
    */
    /*-
    Returns a list of the given parent's descendants matching the given selector.
    The list will also include the parent element if it matches the selector itself.
  
    @function up.fragment.subtree
    @param {Element} parent
      The parent element for the search.
    @param {string} selector
      The CSS selector to match.
    @param {up.Layer|string|Element} [options.layer]
    @return {NodeList<Element>|Array<Element>}
      A list of all matching elements.
    @experimental
    */
    function getSubtree(element, selector, options = {}) {
        selector = parseSelector(selector, element, options);
        return selector.subtree(element);
    }
    function contains(element, selector) {
        return getSubtree(element, selector).length > 0;
    }
    /*-
    Returns the first element that matches the selector by testing the element itself
    and traversing up through ancestors in element's layers.
  
    `up.fragment.closest()` will only match elements in the same [layer](/up.layer) as
    the given element. To match ancestors regardless of layers, use `up.element.closest()`.
  
    @function up.fragment.closest
    @param {Element} element
      The element on which to start the search.
    @param {string} selector
      The CSS selector to match.
    @return {Element|null|undefined} element
      The matching element.
  
      Returns `null` or `undefined` if no element matches in the same layer.
    @experimental
    */
    function closest(element, selector, options) {
        element = e.get(element);
        selector = parseSelector(selector, element, options);
        return selector.closest(element);
    }
    /*-
    Destroys the given element or selector.
  
    All [`up.compiler()`](/up.compiler) destructors, if any, are called.
    The element is then removed from the DOM.
  
    Emits events [`up:fragment:destroyed`](/up:fragment:destroyed).
  
    ### Animating the removal
  
    You may animate the element's removal by passing an option like `{ animate: 'fade-out' }`.
    Unpoly ships with a number of [predefined animations](/up.animate#named-animations) and
    you may so define [custom animations](/up.animation).
  
    If the element's removal is animated, the element will remain in the DOM until after the animation
    has completed. While the animation is running the element will be given the `.up-destroying` class.
    The element will also be given the `[aria-hidden]` attribute to remove it from
    the accessibility tree.
  
    Elements that are about to be destroyed (but still animating) are ignored by all
    functions for fragment lookup:
  
    - `up.fragment.all()`
    - `up.fragment.get()`
    - `up.fragment.closest()`
  
    @function up.destroy
    @param {string|Element|jQuery} target
    @param {string|Function(element, options): Promise} [options.animation='none']
      The animation to use before the element is removed from the DOM.
    @param {number} [options.duration]
      The duration of the animation. See [`up.animate()`](/up.animate).
    @param {string} [options.easing]
      The timing function that controls the animation's acceleration. See [`up.animate()`](/up.animate).
    @param {Function} [options.onFinished]
      A callback that is run when any animations are finished and the element was removed from the DOM.
    @return undefined
    @stable
    */
    function destroy(...args) {
        const options = parseTargetAndOptions(args);
        if (options.element = getSmart(options.target, options)) {
            new up.Change.DestroyFragment(options).execute();
        }
        return up.migrate.formerlyAsync?.('up.destroy()');
    }
    function parseTargetAndOptions(args) {
        const options = u.parseArgIntoOptions(args, 'target');
        if (u.isElement(options.target)) {
            options.origin || (options.origin = options.target);
        }
        return options;
    }
    /*-
    Elements are assigned the `.up-destroying` class before they are [destroyed](/up.destroy)
    or while they are being removed by a [transition](/up.morph).
  
    If the removal is [animated](/up.destroy#animating-the-removal),
    the class is assigned before the animation starts.
  
    Elements that are about to be destroyed (but still animating) are ignored by all
    functions for fragment lookup:
  
    - `up.fragment.all()`
    - `up.fragment.get()`
    - `up.fragment.closest()`
  
    @selector .up-destroying
    @stable
    */
    function markFragmentAsDestroying(element) {
        element.classList.add('up-destroying');
        element.setAttribute('aria-hidden', 'true');
    }
    /*-
    This event is [emitted](/up.emit) after a page fragment was [destroyed](/up.destroy) and removed from the DOM.
  
    If the destruction is animated, this event is emitted after the animation has ended.
  
    The event is emitted on the parent element of the fragment that was removed.
  
    @event up:fragment:destroyed
    @param {Element} event.fragment
      The detached element that has been removed from the DOM.
    @param {Element} event.parent
      The former parent element of the fragment that has now been detached from the DOM.
    @param {Element} event.target
      The former parent element of the fragment that has now been detached from the DOM.
    @stable
    */
    /*-
    Replaces the given element with a fresh copy fetched from the server.
  
    By default, reloading is not considered a [user navigation](/navigation) and e.g. will not update
    the browser location. You may change this with `{ navigate: true }`.
  
    ### Example
  
    ```js
    up.on('new-mail', function() { up.reload('.inbox') })
    ```
  
    ### Controlling the URL that is reloaded
  
    Unpoly remembers [the URL from which a fragment was loaded](/up.fragment.source),
    so you don't usually need to pass a URL when reloading.
  
    To reload from another URL, pass a `{ url }` option or set an `[up-source]` attribute
    on the element being reloaded or its ancestors.
  
    ### Skipping updates when nothing changed
  
    You may use the `[up-time]` attribute to avoid rendering unchanged HTML when reloading
    a fragment. See `[up-time]` for a detailed example.
  
    @function up.reload
    @param {string|Element|jQuery} [target]
      The element that should be reloaded.
  
      If omitted, an element matching a selector in `up.fragment.config.mainTargets`
      will be reloaded.
    @param {Object} [options]
      See options for `up.render()`.
    @param {string} [options.url]
      The URL from which to reload the fragment.
      This defaults to the URL from which the fragment was originally loaded.
    @param {string} [options.navigate=false]
      Whether the reloading constitutes a [user navigation](/navigation).
    @stable
    */
    function reload(...args) {
        const options = parseTargetAndOptions(args);
        options.target || (options.target = ':main');
        const element = getSmart(options.target, options);
        options.url || (options.url = sourceOf(element));
        options.headers || (options.headers = {});
        options.headers[up.protocol.headerize('reloadFromTime')] = timeOf(element);
        return render(options);
    }
    /*-
    Fetches this given URL with JavaScript and [replaces](/up.replace) the
    [current layer](/up.layer.current)'s [main element](/up.fragment.config#config.mainTargets)
    with a matching fragment from the server response.
  
    ### Example
  
    This would replace the current page with the response for `/users`:
  
    ```js
    up.visit('/users')
    ```
  
    @function up.visit
    @param {string} url
      The URL to visit.
    @param {Object} [options]
      See options for `up.render()`.
    @param {up.Layer|string|number} [options.layer='current']
    @stable
    */
    function visit(url, options) {
        return navigate({ ...options, url });
    }
    function successKey(key) {
        return u.unprefixCamelCase(key, 'fail');
    }
    function failKey(key) {
        if (!key.match(/^fail[A-Z]/)) {
            return u.prefixCamelCase(key, 'fail');
        }
    }
    /*-
    Returns a CSS selector that matches the given element as good as possible.
  
    To build the selector, the following element properties are used in decreasing
    order of priority:
  
    - The element's `[up-id]` attribute
    - The element's `[id]` attribute
    - The element's `[name]` attribute
    - The element's `[class]` names, ignoring `up.fragment.config.badTargetClasses`.
    - The element's tag name
  
    ### Example
  
    ```js
    element = up.element.createFromHTML('<span class="klass">...</span>')
    selector = up.fragment.toTarget(element) // returns '.klass'
    ```
  
    @function up.fragment.toTarget
    @param {string|Element|jQuery}
      The element for which to create a selector.
    @stable
    */
    function toTarget(element) {
        if (u.isString(element)) {
            return element;
        }
        // In case we're called called with a jQuery collection
        element = e.get(element);
        let value;
        if (e.isSingleton(element)) {
            return e.tagName(element);
        }
        else if (value = element.getAttribute("up-id")) {
            return e.attributeSelector('up-id', value);
        }
        else if (value = element.getAttribute("id")) {
            return e.idSelector(value);
        }
        else if (value = element.getAttribute("name")) {
            return e.tagName(element) + e.attributeSelector('name', value);
        }
        else if (value = u.presence(u.filter(element.classList, isGoodClassForTarget))) {
            let selector = '';
            for (let goodClass of value) {
                selector += e.classSelector(goodClass);
            }
            return selector;
        }
        else {
            return e.tagName(element);
        }
    }
    /*-
    Sets an unique identifier for this element.
  
    This identifier is used by `up.fragment.toTarget()`
    to create a CSS selector that matches this element precisely.
  
    If the element already has other attributes that make a good identifier,
    like a good `[id]` or `[class]` attribute, it is not necessary to
    also set `[up-id]`.
  
    ### Example
  
    Take this element:
  
    ```html
    <a href="/">Homepage</a>
    ```
  
    Unpoly cannot generate a good CSS selector for this element:
  
    ```js
    up.fragment.toTarget(element)
    // returns 'a'
    ```
  
    We can improve this by assigning an `[up-id]`:
  
    ```html
    <a href="/" up-id="link-to-home">Open user 4</a>
    ```
  
    The attribute value is used to create a better selector:
  
    ```js
    up.fragment.toTarget(element)
    // returns '[up-id="link-to-home"]'
    ```
  
    @selector [up-id]
    @param up-id
      A string that uniquely identifies this element.
    @stable
    */
    function isGoodClassForTarget(klass) {
        function matchesPattern(pattern) {
            if (u.isRegExp(pattern)) {
                return pattern.test(klass);
            }
            else {
                return pattern === klass;
            }
        }
        return !u.some(config.badTargetClasses, matchesPattern);
    }
    function resolveOriginReference(target, options = {}) {
        const { origin } = options;
        return target.replace(/&|:origin\b/, function (match) {
            if (origin) {
                return toTarget(origin);
            }
            else {
                up.fail('Missing { origin } element to resolve "%s" reference (found in %s)', match, target);
            }
        });
    }
    function expandTargets(targets, options = {}) {
        const { layer } = options;
        if (layer !== 'new' && !(layer instanceof up.Layer)) {
            up.fail('Must pass an up.Layer as { layer } option, but got %o', layer);
        }
        // Copy the list since targets might be a jQuery collection, and this does not support shift or push.
        targets = u.copy(u.wrapList(targets));
        const expanded = [];
        while (targets.length) {
            const target = targets.shift();
            if (target === ':main' || target === true) {
                const mode = layer === 'new' ? options.mode : layer.mode;
                targets.unshift(...up.layer.mainTargets(mode));
            }
            else if (target === ':layer') {
                // Discard this target for new layers, which don't have a first-swappable-element.
                // Also don't && the layer check into the `else if` condition above, or it will
                // be returned as a verbatim string below.
                if (layer !== 'new' && !layer.opening) {
                    targets.unshift(layer.getFirstSwappableElement());
                }
            }
            else if (u.isElementish(target)) {
                expanded.push(toTarget(target));
            }
            else if (u.isString(target)) {
                expanded.push(resolveOriginReference(target, options));
            }
            else {
                // @buildPlans() might call us with { target: false } or { target: nil }
                // In that case we don't add a plan.
            }
        }
        return u.uniq(expanded);
    }
    function parseSelector(selector, element, options = {}) {
        const filters = [];
        if (!options.destroying) {
            filters.push(isNotDestroying);
        }
        // Some up.fragment function center around an element, like closest() or matches().
        options.layer || (options.layer = element);
        const layers = up.layer.getAll(options);
        if (options.layer !== 'any' && !(element && e.isDetached(element))) {
            filters.push(match => u.some(layers, layer => layer.contains(match)));
        }
        let expandedTargets = up.fragment.expandTargets(selector, { ...options, layer: layers[0] });
        expandedTargets = expandedTargets.map(function (target) {
            target = target.replace(CSS_HAS_SUFFIX_PATTERN, function (match, descendantSelector) {
                filters.push(element => element.querySelector(descendantSelector));
                return '';
            });
            return target || '*';
        });
        return new up.Selector(expandedTargets, filters);
    }
    function hasAutoHistory(fragment) {
        if (contains(fragment, config.autoHistoryTargets)) {
            return true;
        }
        else {
            up.puts('up.render()', "Will not auto-update history because fragment doesn't contain a selector from up.fragment.config.autoHistoryTargets");
            return false;
        }
    }
    /*-
    A pseudo-selector that matches the layer's main target.
  
    Main targets are default render targets.
    When no other render target is given, Unpoly will try to find and replace a main target.
  
    In most app layouts the main target should match the primary content area.
    The default main targets are:
  
    - any element with an `[up-main]` attribute
    - the HTML5 [`<main>`](https://developer.mozilla.org/en-US/docs/Web/HTML/Element/main) element
    - the current layer's [topmost swappable element](/layer)
  
    You may configure main target selectors in `up.fragment.config.mainTargets`.
  
    ### Example
  
    ```js
    up.render(':main', { url: '/page2' })
    ```
  
    @selector :main
    @experimental
    */
    /*-
    Updates this element when no other render target is given.
  
    ### Example
  
    Many links simply replace the main content element in your application layout.
  
    Unpoly lets you mark this elements as a default target using the `[up-main]` attribute:
  
    ```html
    <body>
      <div class="layout">
        <div class="layout--side">
          ...
        </div>
        <div class="layout--content" up-main>
         ...
        </div>
      </div>
    </body>
    ```
  
    Once a main target is configured, you no longer need `[up-target]` in a link.\
    Use `[up-follow]` and the `[up-main]` element will be replaced:
  
    ```html
    <a href="/foo" up-follow>...</a>
    ```
  
    If you want to update something more specific, you can still use `[up-target]`:
  
    ```html
    <a href="/foo" up-target=".profile">...</a>
    ```
  
    Instead of assigning `[up-main]` you may also configure an existing selector in `up.fragment.config.mainTargets`:
  
    ```js
    up.fragment.config.mainTargets.push('.layout--content')
    ```
  
    Overlays can use different main targets
    ---------------------------------------
  
    Overlays often use a different default selector, e.g. to exclude a navigation bar.
  
    To define a different main target for an overlay, set the [layer mode](/layer-terminology) as the
    value of the `[up-main]` attribute:
  
    ```html
    <body>
      <div class="layout" up-main="root">
        <div class="layout--side">
          ...
        </div>
        <div class="layout--content" up-main="modal">
          ...
        </div>
      </div>
    </body>
    ```
  
    Instead of assigning `[up-main]` you may also configure layer-specific targets in `up.layer.config`:
  
    ```js
    up.layer.config.popup.mainTargets.push('.menu')              // for popup overlays
    up.layer.config.drawer.mainTargets.push('.menu')             // for drawer overlays
    up.layer.config.overlay.mainTargets.push('.layout--content') // for all overlay modes
    ```
  
    @selector [up-main]
    @param [up-main]
    A space-separated list of [layer modes](/layer-terminology) for which to use this main target.
  
    Omit the attribute value to define a main target for *all* layer modes.
  
    To use a different main target for all overlays (but not the root layer), set `[up-main=overlay]`.
    @stable
    */
    /*-
    To make a server request without changing a fragment, use the `:none` selector.
  
    ### Example
  
    ```html
    <a href="/ping" up-target=":none">Ping server</a>
    ```
  
    @selector :none
    @experimental
    */
    /*-
    Your target selectors may use this pseudo-selector
    to reference the element that triggered the change.
  
    The origin element is automatically set to a link that is being [followed](/a-up-follow)
    or form that is being [submitted](/form-up-submit). When updating fragments
    programmatically through `up.render()` you may pass an origin element as an `{ origin }` option.
  
    Even without using an `:origin` reference, the
    [origin is considered](/fragment-placement#interaction-origin-is-considered)
    when matching fragments in the current page.
  
    ### Shorthand
  
    Instead of `:origin` you may also use the ampersand character (`&`).
  
    You may be familiar with the ampersand from the [Sass](https://sass-lang.com/documentation/file.SASS_REFERENCE.html#parent-selector)
    CSS preprocessor.
  
    @selector :origin
    @experimental
    */
    /*-
    Your target selectors may use this pseudo-selector
    to replace the layer's topmost swappable element.
  
    The topmost swappable element is the first child of the layer's container element.
    For the [root layer](/up.layer.root) it is the `<body>` element. For an overlay
    it is the target with which the overlay was opened with.
  
    In canonical usage the topmost swappable element is often a [main element](/up-main).
  
    ### Example
  
    The following will replace the `<body>` element in the root layer,
    and the topmost swappable element in an overlay:
  
    ```js
    up.render(':layer', { url: '/page2' })
    ```
  
    @selector :layer
    @experimental
    */
    /*-
    Returns whether the given element matches the given CSS selector.
  
    Other than `up.element.matches()` this function supports non-standard selectors
    like `:main` or `:layer`.
  
    @function up.fragment.matches
    @param {Element} fragment
    @param {string|Array<string>} selectorOrSelectors
    @param {string|up.Layer} [options.layer]
      The layer for which to match.
  
      Pseudo-selectors like `:main` may expand to different selectors
      in different layers.
    @param {string|up.Layer} [options.mode]
      Required if `{ layer: 'new' }` is passed.
    @return {boolean}
    @experimental
    */
    function matches(element, selector, options = {}) {
        element = e.get(element);
        selector = parseSelector(selector, element, options);
        return selector.matches(element);
    }
    up.on('up:framework:boot', function () {
        const { body } = document;
        body.setAttribute('up-source', u.normalizeURL(location.href, { hash: false }));
        hello(body);
        if (!up.browser.canPushState()) {
            return up.warn('Cannot push history changes. Next fragment update will load in a new page.');
        }
    });
    up.on('up:framework:reset', reset);
    return {
        config,
        reload,
        destroy,
        render,
        navigate,
        get: getSmart,
        getDumb,
        all: getAll,
        subtree: getSubtree,
        contains,
        closest,
        source: sourceOf,
        hello,
        visit,
        markAsDestroying: markFragmentAsDestroying,
        emitInserted: emitFragmentInserted,
        emitDestroyed: emitFragmentDestroyed,
        emitKeep: emitFragmentKeep,
        emitKept: emitFragmentKept,
        successKey,
        failKey,
        expandTargets,
        toTarget,
        matches,
        hasAutoHistory
    };
})();
up.reload = up.fragment.reload;
up.destroy = up.fragment.destroy;
up.render = up.fragment.render;
up.navigate = up.fragment.navigate;
up.hello = up.fragment.hello;
up.visit = up.fragment.visit;
/*-
Returns the current [context](/context).

This is aliased as `up.layer.context`.

@property up.context
@param {Object} context
  The context object.

  If no context has been set an empty object is returned.
@experimental
*/
u.delegate(up, 'context', () => up.layer.current);


/***/ }),
/* 79 */
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin


/***/ }),
/* 80 */
/***/ ((__unused_webpack_module, __unused_webpack_exports, __webpack_require__) => {

__webpack_require__(81);
/*-
Scrolling
=========

The `up.viewport` module controls the scroll position and focus within scrollable containers ("viewports").

The default viewport for any web application is the main document. An application may
define additional viewports by giving the CSS property `{ overflow-y: scroll }` to any `<div>`.

Also see documentation for the [scroll option](/scroll-option) and [focus option](/focus-option).

@see scroll-option
@see scroll-tuning

@see up.reveal
@see [up-fixed=top]

@module up.viewport
*/
up.viewport = (function () {
    const u = up.util;
    const e = up.element;
    const f = up.fragment;
    /*-
    Configures defaults for scrolling.
  
    @property up.viewport.config
    @param {Array} [config.viewportSelectors]
      An array of CSS selectors that match viewports.
    @param {Array} [config.fixedTop]
      An array of CSS selectors that find elements fixed to the
      top edge of the screen (using `position: fixed`).
  
      See [`[up-fixed="top"]`](/up-fixed-top) for details.
    @param {Array} [config.fixedBottom]
      An array of CSS selectors that match elements fixed to the
      bottom edge of the screen (using `position: fixed`).
  
      See [`[up-fixed="bottom"]`](/up-fixed-bottom) for details.
    @param {Array} [config.anchoredRight]
      An array of CSS selectors that find elements anchored to the
      right edge of the screen (using `right:0` with `position: fixed` or `position: absolute`).
  
      See [`[up-anchored="right"]`](/up-anchored-right) for details.
    @param {number} [config.revealSnap]
      When [revealing](/up.reveal) elements, Unpoly will scroll an viewport
      to the top when the revealed element is closer to the viewport's top edge
      than `config.revealSnap`.
  
      Set to `0` to disable snapping.
    @param {number} [config.revealPadding]
      The desired padding between a [revealed](/up.reveal) element and the
      closest [viewport](/up.viewport) edge (in pixels).
    @param {number} [config.revealMax]
      A number indicating how many top pixel rows of a high element to [reveal](/up.reveal).
  
      Defaults to 50% of the available window height.
  
      You may set this to `false` to always reveal as much of the element as the viewport allows.
  
      You may also pass a function that receives an argument `{ viewportRect, elementRect }` and returns
      a maximum height in pixel. Each given rectangle has properties `{ top, right, buttom, left, width, height }`.
    @param {number} [config.revealTop=false]
      Whether to always scroll a [revealing](/up.reveal) element to the top.
  
      By default Unpoly will scroll as little as possible to make the element visible.
    @param {number} [config.scrollSpeed=1]
      The speed of the scrolling motion when [scrolling](/up.reveal) with `{ behavior: 'smooth' }`.
  
      The default value (`1`) roughly corresponds to the speed of Chrome's
      [native smooth scrolling](https://developer.mozilla.org/en-US/docs/Web/API/ScrollToOptions/behavior).
    @stable
    */
    const config = new up.Config(() => ({
        viewportSelectors: ['[up-viewport]', '[up-fixed]'],
        fixedTop: ['[up-fixed~=top]'],
        fixedBottom: ['[up-fixed~=bottom]'],
        anchoredRight: ['[up-anchored~=right]', '[up-fixed~=top]', '[up-fixed~=bottom]', '[up-fixed~=right]'],
        revealSnap: 200,
        revealPadding: 0,
        revealTop: false,
        revealMax() { return 0.5 * window.innerHeight; },
        scrollSpeed: 1
    }));
    const scrollingController = new up.MotionController('scrolling');
    function reset() {
        config.reset();
        scrollingController.reset();
    }
    /*-
    Scrolls the given viewport to the given Y-position.
  
    A "viewport" is an element that has scrollbars, e.g. `<body>` or
    a container with `overflow-x: scroll`.
  
    ### Example
  
    This will scroll a `<div class="main">...</div>` to a Y-position of 100 pixels:
  
        up.scroll('.main', 100)
  
    ### Animating the scrolling motion
  
    The scrolling can (optionally) be animated.
  
        up.scroll('.main', 100, { behavior: 'smooth' })
  
    If the given viewport is already in a scroll animation when `up.scroll()`
    is called a second time, the previous animation will instantly jump to the
    last frame before the next animation is started.
  
    @function up.scroll
    @param {string|Element|jQuery} viewport
      The container element to scroll.
    @param {number} scrollPos
      The absolute number of pixels to set the scroll position to.
    @param {string}[options.behavior='instant']
      When set to `'instant'`, this will immediately scroll to the new position.
  
      When set to `'smooth'`, this will scroll smoothly to the new position.
    @param {number}[options.speed]
      The speed of the scrolling motion when scrolling with `{ behavior: 'smooth' }`.
  
      Defaults to `up.viewport.config.scrollSpeed`.
    @return {Promise}
      A promise that will be fulfilled when the scrolling ends.
    @internal
    */
    function scroll(viewport, scrollTop, options = {}) {
        viewport = f.get(viewport, options);
        const motion = new up.ScrollMotion(viewport, scrollTop, options);
        scrollingController.startMotion(viewport, motion, options);
    }
    /*-
    @function up.viewport.anchoredRight
    @internal
    */
    function anchoredRight() {
        const selector = config.anchoredRight.join(',');
        return f.all(selector, { layer: 'root' });
    }
    /*-
    Scrolls the given element's viewport so the first rows of the
    element are visible for the user.
  
    ### Fixed elements obstructing the viewport
  
    Many applications have a navigation bar fixed to the top or bottom,
    obstructing the view on an element.
  
    You can make `up.reveal()` aware of these fixed elements
    so it can scroll the viewport far enough so the revealed element is fully visible.
    To make `up.reveal()` aware of fixed elements you can either:
  
    - give the element an attribute [`up-fixed="top"`](/up-fixed-top) or [`up-fixed="bottom"`](/up-fixed-bottom)
    - [configure default options](/up.viewport.config) for `fixedTop` or `fixedBottom`
  
    @function up.reveal
  
    @param {string|Element|jQuery} element
      The element to reveal.
  
    @param {number} [options.scrollSpeed=1]
      The speed of the scrolling motion when scrolling with `{ behavior: 'smooth' }`.
  
      The default value (`1`) roughly corresponds to the speed of Chrome's
      [native smooth scrolling](https://developer.mozilla.org/en-US/docs/Web/API/ScrollToOptions/behavior).
  
      Defaults to `up.viewport.config.scrollSpeed`.
  
    @param {string} [options.revealSnap]
      When the the revealed element would be closer to the viewport's top edge
      than this value, Unpoly will scroll the viewport to the top.
  
      Set to `0` to disable snapping.
  
      Defaults to `up.viewport.config.revealSnap`.
  
    @param {string|Element|jQuery} [options.viewport]
      The scrolling element to scroll.
  
      Defaults to the [given element's viewport](/up.viewport.get).
  
    @param {boolean} [options.top]
      Whether to scroll the viewport so that the first element row aligns
      with the top edge of the viewport.
  
      Defaults to `up.viewport.config.revealTop`.
  
    @param {string}[options.behavior='instant']
      When set to `'instant'`, this will immediately scroll to the new position.
  
      When set to `'smooth'`, this will scroll smoothly to the new position.
  
    @param {number}[options.speed]
      The speed of the scrolling motion when scrolling with `{ behavior: 'smooth' }`.
  
      Defaults to `up.viewport.config.scrollSpeed`.
  
    @param {number} [options.padding]
      The desired padding between the revealed element and the
      closest [viewport](/up.viewport) edge (in pixels).
  
      Defaults to `up.viewport.config.revealPadding`.
  
    @param {number|boolean} [options.snap]
      Whether to snap to the top of the viewport if the new scroll position
      after revealing the element is close to the top edge.
  
      Defaults to `up.viewport.config.revealSnap`.
  
    @return {Promise}
      A promise that fulfills when the element is revealed.
  
      When the scrolling is animated with `{ behavior: 'smooth' }`, the promise
      fulfills when the animation is finished.
  
      When the scrolling is not animated, the promise will fulfill
      in the next [microtask](https://jakearchibald.com/2015/tasks-microtasks-queues-and-schedules/).
  
    @stable
    */
    function reveal(element, options) {
        // copy options, since we will mutate it below (options.layer = ...).
        options = u.options(options);
        element = f.get(element, options);
        // Now that we have looked up the element with an option like { layer: 'any' },
        // the only layer relevant from here on is the element's layer.
        if (!(options.layer = up.layer.get(element))) {
            return up.error.failed.async('Cannot reveal a detached element');
        }
        if (options.peel) {
            options.layer.peel();
        }
        const motion = new up.RevealMotion(element, options);
        return scrollingController.startMotion(element, motion, options);
    }
    /*-
    Focuses the given element.
  
    Focusing an element will also [reveal](/up.reveal) it, unless `{ preventScroll: true }` is passed.
  
    @function up.focus
  
    @param {string|Element|jQuery} element
      The element to focus.
  
    @param {[options.preventScroll=false]}
      Whether to prevent changes to the acroll position.
  
    @experimental
    */
    function doFocus(element, options = {}) {
        // First focus without scrolling, since we're going to use our custom scrolling
        // logic below.
        if (up.browser.isIE11()) {
            // IE11 does not support the { preventScroll } option for Element#focus().
            const viewport = closest(element);
            const oldScrollTop = viewport.scrollTop;
            element.focus();
            viewport.scrollTop = oldScrollTop;
        }
        else {
            element.focus({ preventScroll: true });
        }
        if (!options.preventScroll) {
            // Use up.reveal() which scrolls far enough to ignore fixed nav bars
            // obstructing the focused element.
            return reveal(element);
        }
    }
    function tryFocus(element, options) {
        doFocus(element, options);
        return element === document.activeElement;
    }
    function isNativelyFocusable(element) {
        // IE11: In modern browsers we can check if element.tabIndex >= 0.
        // But IE11 returns 0 for all elements, including <div> etc.
        return e.matches(element, 'a[href], button, textarea, input, select');
    }
    function makeFocusable(element) {
        // (1) Element#tabIndex is -1 for all non-interactive elements,
        //     whether or not the element has an [tabindex=-1] attribute.
        // (2) Element#tabIndex is 0 for interactive elements, like links,
        //     inputs or buttons. [up-clickable] elements also get a [tabindex=0].
        //     to participate in the regular tab order.
        if (!element.hasAttribute('tabindex') && !isNativelyFocusable(element)) {
            element.setAttribute('tabindex', '-1');
            // A11Y: OK to hide the focus ring of a non-interactive element.
            element.classList.add('up-focusable-content');
        }
    }
    /*-
    [Reveals](/up.reveal) an element matching the given `#hash` anchor.
  
    Other than the default behavior found in browsers, `up.revealHash()` works with
    [multiple viewports](/up-viewport) and honors [fixed elements](/up-fixed-top) obstructing the user's
    view of the viewport.
  
    When the page loads initially, this function is automatically called with the hash from
    the current URL.
  
    If no element matches the given `#hash` anchor, a resolved promise is returned.
  
    ### Example
  
        up.revealHash('#chapter2')
  
    @function up.viewport.revealHash
    @param {string} hash
    @internal
    */
    function revealHash(hash = location.hash, options) {
        let match = firstHashTarget(hash, options);
        if (match) {
            return up.reveal(match, { top: true });
        }
    }
    function allSelector() {
        // On Edge the document viewport can be changed from CSS
        return [rootSelector(), ...config.viewportSelectors].join(',');
    }
    /*-
    Returns the scrolling container for the given element.
  
    Returns the [document's scrolling element](/up.viewport.root)
    if no closer viewport exists.
  
    @function up.viewport.get
    @param {string|Element|jQuery} target
    @return {Element}
    @experimental
    */
    function closest(target, options = {}) {
        const element = f.get(target, options);
        // Use up.element.closest() which searches across layer boundaries.
        // It is OK to find a viewport in a parent layer. Layers without its
        // own viewport (like popups) are scrolled by the parent layer's viewport.
        return e.closest(element, allSelector());
    }
    /*-
    Returns a list of all the viewports contained within the
    given selector or element.
  
    If the given element is itself a viewport, the element is included
    in the returned list.
  
    @function up.viewport.subtree
    @param {string|Element|jQuery} target
    @param {Object} options
    @return List<Element>
    @internal
    */
    function getSubtree(element, options = {}) {
        element = f.get(element, options);
        return e.subtree(element, allSelector());
    }
    /*-
    Returns a list of all viewports that are either contained within
    the given element or that are ancestors of the given element.
  
    This is relevant when updating a fragment with `{ scroll: 'restore' | 'reset' }`.
    In tht case we restore / reset the scroll tops of all viewports around the fragment.
  
    @function up.viewport.around
    @param {string|Element|jQuery} element
    @param {Object} options
    @return List<Element>
    @internal
    */
    function getAround(element, options = {}) {
        element = f.get(element, options);
        return e.around(element, allSelector());
    }
    /*-
    Returns a list of all the viewports on the current layer.
  
    @function up.viewport.all
    @internal
    */
    function getAll(options = {}) {
        return f.all(allSelector(), options);
    }
    function rootSelector() {
        // The spec says this should be <html> in standards mode
        // and <body> in quirks mode. However, it is currently (2018-07)
        // always <body> in Webkit browsers (not Blink). Luckily Webkit
        // also supports document.scrollingElement.
        let element;
        if ((element = document.scrollingElement)) {
            return element.tagName;
        }
        else {
            // IE11
            return 'html';
        }
    }
    /*-
    Return the [scrolling element](https://developer.mozilla.org/en-US/docs/Web/API/document/scrollingElement)
    for the browser's main content area.
  
    @function up.viewport.root
    @return {Element}
    @experimental
    */
    function getRoot() {
        return document.querySelector(rootSelector());
    }
    function rootWidth() {
        // This should happen on the <html> element, regardless of document.scrollingElement
        return e.root.clientWidth;
    }
    function rootHeight() {
        // This should happen on the <html> element, regardless of document.scrollingElement
        return e.root.clientHeight;
    }
    function isRoot(element) {
        return e.matches(element, rootSelector());
    }
    /*-
    Returns whether the root viewport is currently showing a vertical scrollbar.
  
    Note that this returns `false` if the root viewport scrolls vertically but the browser
    shows no visible scroll bar at rest, e.g. on mobile devices that only overlay a scroll
    indicator while scrolling.
  
    @function up.viewport.rootHasReducedWidthFromScrollbar
    @internal
    */
    function rootHasReducedWidthFromScrollbar() {
        // We could also check if scrollHeight > offsetHeight for the document viewport.
        // However, we would also need to check overflow-y for that element.
        // Also we have no control whether developers set the property on <body> or <html>.
        // https://tylercipriani.com/blog/2014/07/12/crossbrowser-javascript-scrollbar-detection/
        return window.innerWidth > document.documentElement.offsetWidth;
    }
    /*-
    Returns the element that controls the `overflow-y` behavior for the
    [document viewport](/up.viewport.root()).
  
    @function up.viewport.rootOverflowElement
    @internal
    */
    function rootOverflowElement() {
        const { body } = document;
        const html = document.documentElement;
        const element = u.find([html, body], wasChosenAsOverflowingElement);
        return element || getRoot();
    }
    /*-
    Returns whether the given element was chosen as the overflowing
    element by the developer.
  
    We have no control whether developers set the property on <body> or
    <html>. The developer also won't know what is going to be the
    [scrolling element](/up.viewport.root) on the user's browser.
  
    @function wasChosenAsOverflowingElement
    @internal
    */
    function wasChosenAsOverflowingElement(element) {
        const overflowY = e.style(element, 'overflow-y');
        return overflowY === 'auto' || overflowY === 'scroll';
    }
    /*-
    Returns the width of a scrollbar.
  
    This only runs once per page load.
  
    @function up.viewport.scrollbarWidth
    @internal
    */
    const scrollbarWidth = u.memoize(function () {
        // This is how Bootstrap does it also:
        // https://github.com/twbs/bootstrap/blob/c591227602996c542b9fd0cb65cff3cc9519bdd5/dist/js/bootstrap.js#L1187
        const outerStyle = {
            position: 'absolute',
            top: '0',
            left: '0',
            width: '100px',
            height: '100px',
            overflowY: 'scroll'
        };
        const outer = up.element.affix(document.body, '[up-viewport]', { style: outerStyle });
        const width = outer.offsetWidth - outer.clientWidth;
        up.element.remove(outer);
        return width;
    });
    function scrollTopKey(viewport) {
        return up.fragment.toTarget(viewport);
    }
    /*-
    @function up.viewport.fixedElements
    @internal
    */
    function fixedElements(root = document) {
        const queryParts = ['[up-fixed]'].concat(config.fixedTop).concat(config.fixedBottom);
        return root.querySelectorAll(queryParts.join(','));
    }
    /*-
    Saves the top scroll positions of all viewports in the current layer.
  
    The scroll positions will be associated with the current URL.
    They can later be restored by calling [`up.viewport.restoreScroll()`](/up.viewport.restoreScroll)
    at the same URL, or by following a link with an [`[scroll="restore"]`](/scroll-option#restoring-scroll-positions)
    attribute.
  
    Unpoly automatically saves scroll positions before [navigating](/navigation).
    You will rarely need to call this function yourself.
  
    @function up.viewport.saveScroll
    @param {string} [options.location]
      The URL for which to save scroll positions.
      If omitted, the current browser location is used.
    @param {string} [options.layer]
      The layer for which to save scroll positions.
      If omitted, positions for the current layer will be saved.
    @param {Object<string, number>} [options.tops]
      An object mapping viewport selectors to vertical scroll positions in pixels.
    @experimental
    */
    function saveScroll(...args) {
        const [viewports, options] = parseOptions(args);
        const url = options.location || options.layer.location;
        if (url) {
            const tops = options.tops ?? getScrollTops(viewports);
            options.layer.lastScrollTops.set(url, tops);
        }
    }
    /*-
    Returns a hash with scroll positions.
  
    Each key in the hash is a viewport selector. The corresponding
    value is the viewport's top scroll position:
  
        getScrollTops()
        => { '.main': 0, '.sidebar': 73 }
  
    @function up.viewport.getScrollTops
    @return Object<string, number>
    @internal
    */
    function getScrollTops(viewports) {
        return u.mapObject(viewports, viewport => [scrollTopKey(viewport), viewport.scrollTop]);
    }
    /*-
    Restores [previously saved](/up.viewport.saveScroll) scroll positions of viewports
    viewports configured in `up.viewport.config.viewportSelectors`.
  
    Unpoly automatically restores scroll positions when the user presses the back button.
    You can disable this behavior by setting [`up.history.config.restoreScroll = false`](/up.history.config).
  
    @function up.viewport.restoreScroll
    @param {Element} [viewport]
    @param {up.Layer|string} [options.layer]
      The layer on which to restore scroll positions.
    @return {Promise}
      A promise that will be fulfilled once scroll positions have been restored.
    @experimental
    */
    function restoreScroll(...args) {
        const [viewports, options] = parseOptions(args);
        const url = options.layer.location;
        const scrollTopsForURL = options.layer.lastScrollTops.get(url) || {};
        up.puts('up.viewport.restoreScroll()', 'Restoring scroll positions for URL %s to %o', url, scrollTopsForURL);
        return setScrollTops(viewports, scrollTopsForURL);
    }
    function parseOptions(args) {
        const options = u.copy(u.extractOptions(args));
        options.layer = up.layer.get(options);
        let viewports;
        if (args[0]) {
            viewports = [closest(args[0], options)];
        }
        else if (options.around) {
            // This is relevant when updating a fragment with { scroll: 'restore' | 'reset' }.
            // In tht case we restore / reset the scroll tops of all viewports around the fragment.
            viewports = getAround(options.around, options);
        }
        else {
            viewports = getAll(options);
        }
        return [viewports, options];
    }
    function resetScroll(...args) {
        const [viewports, _options] = parseOptions(args);
        return setScrollTops(viewports, {});
    }
    function setScrollTops(viewports, tops) {
        const allScrollPromises = u.map(viewports, function (viewport) {
            const key = scrollTopKey(viewport);
            const scrollTop = tops[key] || 0;
            return scroll(viewport, scrollTop, { duration: 0 });
        });
        return Promise.all(allScrollPromises);
    }
    function absolutize(element, options = {}) {
        const viewport = closest(element);
        const viewportRect = viewport.getBoundingClientRect();
        const originalRect = element.getBoundingClientRect();
        const boundsRect = new up.Rect({
            left: originalRect.left - viewportRect.left,
            top: originalRect.top - viewportRect.top,
            width: originalRect.width,
            height: originalRect.height
        });
        // Allow the caller to run code before we start shifting elements around.
        options.afterMeasure?.();
        e.setStyle(element, {
            // If the element had a layout context before, make sure the
            // ghost will have layout context as well (and vice versa).
            position: element.style.position === 'static' ? 'static' : 'relative',
            top: 'auto',
            right: 'auto',
            bottom: 'auto',
            left: 'auto',
            width: '100%',
            height: '100%'
        }); // stretch to the <up-bounds> height we set below
        // Wrap the ghost in another container so its margin can expand
        // freely. If we would position the element directly (old implementation),
        // it would gain a layout context which cannot be crossed by margins.
        const bounds = e.createFromSelector('up-bounds');
        // Insert the bounds object before our element, then move element into it.
        e.insertBefore(element, bounds);
        bounds.appendChild(element);
        const moveBounds = function (diffX, diffY) {
            boundsRect.left += diffX;
            boundsRect.top += diffY;
            return e.setStyle(bounds, boundsRect);
        };
        // Position the bounds initially
        moveBounds(0, 0);
        // In theory, element should not have moved visually. However, element
        // (or a child of element) might collapse its margin against a previous
        // sibling element, and now that it is absolute it does not have the
        // same sibling. So we manually correct element's top position so it aligns
        // with the previous top position.
        const newElementRect = element.getBoundingClientRect();
        moveBounds(originalRect.left - newElementRect.left, originalRect.top - newElementRect.top);
        u.each(fixedElements(element), e.fixedToAbsolute);
        return {
            bounds,
            moveBounds
        };
    }
    /*-
    Marks this element as a scrolling container ("viewport").
  
    Apply this attribute if your app uses a custom panel layout with fixed positioning
    instead of scrolling `<body>`. As an alternative you can also push a selector
    matching your custom viewport to the `up.viewport.config.viewportSelectors` array.
  
    [`up.reveal()`](/up.reveal) will always try to scroll the viewport closest
    to the element that is being revealed. By default this is the `<body>` element.
  
    ### Example
  
    Here is an example for a layout for an e-mail client, showing a list of e-mails
    on the left side and the e-mail text on the right side:
  
    ```css
    .side {
      position: fixed;
      top: 0;
      bottom: 0;
      left: 0;
      width: 100px;
      overflow-y: scroll;
    }
  
    .main {
      position: fixed;
      top: 0;
      bottom: 0;
      left: 100px;
      right: 0;
      overflow-y: scroll;
    }
    ```
  
    This would be the HTML (notice the `up-viewport` attribute):
  
    ```html
    <div class=".side" up-viewport>
      <a href="/emails/5001" up-target=".main">Re: Your invoice</a>
      <a href="/emails/2023" up-target=".main">Quote for services</a>
      <a href="/emails/9002" up-target=".main">Fwd: Room reservation</a>
    </div>
  
    <div class="main" up-viewport>
      <h1>Re: Your Invoice</h1>
      <p>
        Lorem ipsum dolor sit amet, consetetur sadipscing elitr.
        Stet clita kasd gubergren, no sea takimata sanctus est.
      </p>
    </div>
    ```
  
    @selector [up-viewport]
    @stable
    */
    /*-
    Marks this element as being fixed to the top edge of the screen
    using `position: fixed`.
  
    When [following a fragment link](/a-up-follow), the viewport is scrolled
    so the targeted element becomes visible. By using this attribute you can make
    Unpoly aware of fixed elements that are obstructing the viewport contents.
    Unpoly will then scroll the viewport far enough that the revealed element is fully visible.
  
    Instead of using this attribute,
    you can also configure a selector in `up.viewport.config.fixedTop`.
  
    ### Example
  
        <div class="top-nav" up-fixed="top">...</div>
  
    @selector [up-fixed=top]
    @stable
    */
    /*-
    Marks this element as being fixed to the bottom edge of the screen
    using `position: fixed`.
  
    When [following a fragment link](/a-up-follow), the viewport is scrolled
    so the targeted element becomes visible. By using this attribute you can make
    Unpoly aware of fixed elements that are obstructing the viewport contents.
    Unpoly will then scroll the viewport far enough that the revealed element is fully visible.
  
    Instead of using this attribute,
    you can also configure a selector in `up.viewport.config.fixedBottom`.
  
    ### Example
  
        <div class="bottom-nav" up-fixed="bottom">...</div>
  
    @selector [up-fixed=bottom]
    @stable
    */
    /*-
    Marks this element as being anchored to the right edge of the screen,
    typically fixed navigation bars.
  
    Since [overlays](/up.layer) hide the document scroll bar,
    elements anchored to the right appear to jump when the dialog opens or
    closes. Applying this attribute to anchored elements will make Unpoly
    aware of the issue and adjust the `right` property accordingly.
  
    You should give this attribute to layout elements
    with a CSS of `right: 0` with `position: fixed` or `position:absolute`.
  
    Instead of giving this attribute to any affected element,
    you can also configure a selector in `up.viewport.config.anchoredRight`.
  
    ### Example
  
    Here is the CSS for a navigation bar that is anchored to the top edge of the screen:
  
    ```css
    .top-nav {
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
    }
    ```
  
    By adding an `up-anchored="right"` attribute to the element, we can prevent the
    `right` edge from jumping when an [overlay](/up.layer) opens or closes:
  
    ```html
    <div class="top-nav" up-anchored="right">...</div>
    ```
  
    @selector [up-anchored=right]
    @stable
    */
    /*-
    @function up.viewport.firstHashTarget
    @internal
    */
    function firstHashTarget(hash, options = {}) {
        if (hash = pureHash(hash)) {
            const selector = [
                // Match an <* id="hash">
                e.attributeSelector('id', hash),
                // Match an <a name="hash">
                'a' + e.attributeSelector('name', hash)
            ].join(',');
            return f.get(selector, options);
        }
    }
    /*-
    Returns `'foo'` if the hash is `'#foo'`.
  
    @function pureHash
    @internal
    */
    function pureHash(value) {
        return value?.replace(/^#/, '');
    }
    let userScrolled = false;
    up.on('scroll', { once: true, beforeBoot: true }, () => userScrolled = true);
    up.on('up:framework:boot', function () {
        // When the initial URL contains an #anchor link, the browser will automatically
        // reveal a matching fragment. We want to override that behavior with our own,
        // so we can honor configured obstructions. Since we cannot disable the automatic
        // browser behavior we need to ensure our code runs after it.
        //
        // In Chrome, when reloading, the browser behavior happens before DOMContentLoaded.
        // However, when we follow a link with an #anchor URL, the browser
        // behavior happens *after* DOMContentLoaded. Hence we wait one more task.
        u.task(function () {
            // If the user has scrolled while the page was loading, we will
            // not reset their scroll position by revealing the #anchor fragment.
            if (!userScrolled) {
                return revealHash();
            }
        });
    });
    up.on(window, 'hashchange', () => revealHash());
    up.on('up:framework:reset', reset);
    return {
        reveal,
        revealHash,
        firstHashTarget,
        scroll,
        config,
        get: closest,
        subtree: getSubtree,
        around: getAround,
        all: getAll,
        rootSelector,
        get root() { return getRoot(); },
        rootWidth,
        rootHeight,
        rootHasReducedWidthFromScrollbar,
        rootOverflowElement,
        isRoot,
        scrollbarWidth,
        saveScroll,
        restoreScroll,
        resetScroll,
        anchoredRight,
        absolutize,
        focus: doFocus,
        tryFocus,
        makeFocusable,
    };
})();
up.focus = up.viewport.focus;
up.scroll = up.viewport.scroll;
up.reveal = up.viewport.reveal;


/***/ }),
/* 81 */
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin


/***/ }),
/* 82 */
/***/ (() => {

/*-
Animation
=========
  
When you [update a page fragment](/up.link) you can animate the change.

You can add an attribute [`[up-transition]`](/a-up-transition) to your
links or forms to smoothly fade out the old element while fading in the new element:

```html
<a href="/users"
  up-target=".list"
  up-transition="cross-fade">
  Show users
</a>
```

### Transitions vs. animations

When we morph between an old and a new element, we call it a *transition*.
In contrast, when we animate a new element without simultaneously removing an
old element, we call it an *animation*.

An example for an animation is opening a new overlay. We can animate the appearance
of the dialog by adding an [`[up-animation]`](/a-up-layer-new#up-animation) attribute to the opening link:

```html
<a href="/users"
  up-target=".list"
  up-layer="new"
  up-animation="move-from-top">
  Show users
</a>
```

### Which animations are available?

Unpoly ships with a number of [predefined transitions](/up.morph#named-transitions)
and [predefined animations](/up.animate#named-animations).

You can define custom animations using `up.transition()` and
`up.animation()`.

@see motion-tuning

@see a[up-transition]
@see up.animation
@see up.transition

@module up.motion
*/
up.motion = (function () {
    const u = up.util;
    const e = up.element;
    let namedAnimations = {};
    let namedTransitions = {};
    const motionController = new up.MotionController('motion');
    /*-
    Sets default options for animations and transitions.
  
    @property up.motion.config
    @param {number} [config.duration=175]
      The default duration for all animations and transitions (in milliseconds).
    @param {string} [config.easing='ease']
      The default timing function that controls the acceleration of animations and transitions.
  
      See [MDN documentation](https://developer.mozilla.org/en-US/docs/Web/CSS/transition-timing-function)
      for a list of pre-defined timing functions.
    @param {boolean} [config.enabled]
      Whether animation is enabled.
  
      By default animations are enabled, unless the user has configured their
      system to [minimize non-essential motion](https://developer.mozilla.org/en-US/docs/Web/CSS/@media/prefers-reduced-motion).
  
      Set this to `false` to disable animation globally.
      This can be useful in full-stack integration tests.
    @stable
    */
    const config = new up.Config(() => ({
        duration: 175,
        easing: 'ease',
        enabled: !matchMedia('(prefers-reduced-motion: reduce)').matches
    }));
    function pickDefault(registry) {
        return u.pickBy(registry, value => value.isDefault);
    }
    function reset() {
        motionController.reset();
        namedAnimations = pickDefault(namedAnimations);
        namedTransitions = pickDefault(namedTransitions);
        config.reset();
    }
    /*-
    Returns whether Unpoly will perform animations.
  
    Set [`up.motion.config.enabled = false`](/up.motion.config#config.enabled) in order to disable animations globally.
  
    @function up.motion.isEnabled
    @return {boolean}
    @stable
    */
    function isEnabled() {
        return config.enabled;
    }
    /*-
    Applies the given animation to the given element.
  
    ### Example
  
    ```js
    up.animate('.warning', 'fade-in')
    ```
  
    You can pass additional options:
  
    ```js
    up.animate('.warning', 'fade-in', {
      duration: 250,
      easing: 'linear'
    })
    ```
  
    ### Named animations
  
    The following animations are pre-defined:
  
    | `fade-in`          | Changes the element's opacity from 0% to 100% |
    | `fade-out`         | Changes the element's opacity from 100% to 0% |
    | `move-to-top`      | Moves the element upwards until it exits the screen at the top edge |
    | `move-from-top`    | Moves the element downwards from beyond the top edge of the screen until it reaches its current position |
    | `move-to-bottom`   | Moves the element downwards until it exits the screen at the bottom edge |
    | `move-from-bottom` | Moves the element upwards from beyond the bottom edge of the screen until it reaches its current position |
    | `move-to-left`     | Moves the element leftwards until it exists the screen at the left edge  |
    | `move-from-left`   | Moves the element rightwards from beyond the left edge of the screen until it reaches its current position |
    | `move-to-right`    | Moves the element rightwards until it exists the screen at the right edge  |
    | `move-from-right`  | Moves the element leftwards from beyond the right  edge of the screen until it reaches its current position |
    | `none`             | An animation that has no visible effect. Sounds useless at first, but can save you a lot of `if` statements. |
  
    You can define additional named animations using [`up.animation()`](/up.animation).
  
    ### Animating CSS properties directly
  
    By passing an object instead of an animation name, you can animate
    the CSS properties of the given element:
  
    ```js
    var warning = document.querySelector('.warning')
    warning.style.opacity = 0
    up.animate(warning, { opacity: 1 })
    ```
  
    CSS properties must be given in `kebab-case`, not `camelCase`.
  
    ### Multiple animations on the same element
  
    Unpoly doesn't allow more than one concurrent animation on the same element.
  
    If you attempt to animate an element that is already being animated,
    the previous animation will instantly jump to its last frame before
    the new animation begins.
  
    @function up.animate
    @param {Element|jQuery|string} element
      The element to animate.
    @param {string|Function(element, options): Promise|Object} animation
      Can either be:
  
      - The animation's name
      - A function performing the animation
      - An object of CSS attributes describing the last frame of the animation (using kebeb-case property names)
    @param {number} [options.duration=300]
      The duration of the animation, in milliseconds.
    @param {string} [options.easing='ease']
      The timing function that controls the animation's acceleration.
  
      See [MDN documentation](https://developer.mozilla.org/en-US/docs/Web/CSS/transition-timing-function)
      for a list of pre-defined timing functions.
    @return {Promise}
      A promise for the animation's end.
    @stable
    */
    function animate(element, animation, options) {
        // If passed a selector, up.fragment.get() will prefer a match on the current layer.
        element = up.fragment.get(element);
        options = u.options(options);
        const animationFn = findAnimationFn(animation);
        // willAnimate() also sets a default { duration } and { easing }.
        const willRun = willAnimate(element, animation, options);
        if (willRun) {
            // up.puts 'up.animate()', Animating %o with animation %o', element, animation
            const runNow = () => animationFn(element, options);
            return motionController.startFunction(element, runNow, options);
        }
        else {
            return skipAnimate(element, animation);
        }
    }
    function willAnimate(element, animationOrTransition, options) {
        applyConfig(options);
        return isEnabled() && !isNone(animationOrTransition) && (options.duration > 0) && !e.isSingleton(element);
    }
    function skipAnimate(element, animation) {
        if (u.isOptions(animation)) {
            // If we are given the final animation frame as an object of CSS properties,
            // the best we can do is to set the final frame without animation.
            e.setStyle(element, animation);
        }
        // Signal that the animation is already done.
        return Promise.resolve();
    }
    /*-
    Animates the given element's CSS properties using CSS transitions.
  
    Does not track the animation, nor does it finishes existing animations
    (use `up.motion.animate()` for that). It does, however, listen to the motionController's
    finish event.
  
    @function animateNow
    @param {Element|jQuery|string} element
      The element to animate.
    @param {Object} lastFrame
      The CSS properties that should be transitioned to.
    @param {number} [options.duration=300]
      The duration of the animation, in milliseconds.
    @param {string} [options.easing='ease']
      The timing function that controls the animation's acceleration.
      See [MDN documentation](https://developer.mozilla.org/en-US/docs/Web/CSS/transition-timing-function)
      for a list of pre-defined timing functions.
    @return {Promise}
      A promise that fulfills when the animation ends.
    @internal
    */
    function animateNow(element, lastFrame, options) {
        options = { ...options, finishEvent: motionController.finishEvent };
        const cssTransition = new up.CSSTransition(element, lastFrame, options);
        return cssTransition.start();
    }
    function applyConfig(options) {
        options.easing || (options.easing = config.easing);
        options.duration || (options.duration = config.duration);
    }
    function findNamedAnimation(name) {
        return namedAnimations[name] || up.fail("Unknown animation %o", name);
    }
    /*-
    Completes [animations](/up.animate) and [transitions](/up.morph).
  
    If called without arguments, all animations on the screen are completed.
    If given an element (or selector), animations on that element and its children
    are completed.
  
    Animations are completed by jumping to the last animation frame instantly.
    Promises returned by animation and transition functions instantly settle.
  
    Emits the `up:motion:finish` event that is handled by `up.animate()`.
  
    Does nothing if there are no animation to complete.
  
    @function up.motion.finish
    @param {Element|jQuery|string} [element]
      The element around which to finish all animations.
    @return {Promise}
      A promise that fulfills when animations and transitions have finished.
    @stable
    */
    function finish(element) {
        return motionController.finish(element);
    }
    /*-
    This event is emitted on an animating element by `up.motion.finish()` to
    request the animation to instantly finish and skip to the last frame.
  
    Promises returned by animation and transition functions are expected
    to settle.
  
    Animations started by `up.animate()` already handle this event.
  
    @event up:motion:finish
    @param {Element} event.target
      The animating element.
    @stable
    */
    /*-
    Performs an animated transition between the `source` and `target` elements.
  
    Transitions are implement by performing two animations in parallel,
    causing `source` to disappear and the `target` to appear.
  
    - `target` is [inserted before](https://developer.mozilla.org/en-US/docs/Web/API/Node/insertBefore) `source`
    - `source` is removed from the [document flow](https://developer.mozilla.org/en-US/docs/Learn/CSS/CSS_layout/Positioning) with `position: absolute`.
       It will be positioned over its original place in the flow that is now occupied by `target`.
    - Both `source` and `target` are animated in parallel
    - `source` is removed from the DOM
  
    ### Named transitions
  
    The following transitions are pre-defined:
  
    | `cross-fade` | Fades out the first element. Simultaneously fades in the second element. |
    | `move-up`    | Moves the first element upwards until it exits the screen at the top edge. Simultaneously moves the second element upwards from beyond the bottom edge of the screen until it reaches its current position. |
    | `move-down`  | Moves the first element downwards until it exits the screen at the bottom edge. Simultaneously moves the second element downwards from beyond the top edge of the screen until it reaches its current position. |
    | `move-left`  | Moves the first element leftwards until it exists the screen at the left edge. Simultaneously moves the second element leftwards from beyond the right  edge of the screen until it reaches its current position. |
    | `move-right` | Moves the first element rightwards until it exists the screen at the right edge. Simultaneously moves the second element rightwards from beyond the left edge of the screen until it reaches its current position. |
    | `none`       | A transition that has no visible effect. Sounds useless at first, but can save you a lot of `if` statements. |
  
    You can define additional named transitions using [`up.transition()`](/up.transition).
    
    You can also compose a transition from two [named animations](/up.animation).
    separated by a slash character (`/`):
    
    - `move-to-bottom/fade-in`
    - `move-to-left/move-from-top`
  
    ### Implementation details
  
    During a transition both the old and new element occupy
    the same position on the screen.
  
    Since the CSS layout flow will usually not allow two elements to
    overlay the same space, Unpoly:
  
    - The old and new elements are cloned
    - The old element is removed from the layout flow using `display: hidden`
    - The new element is hidden, but still leaves space in the layout flow by setting `visibility: hidden`
    - The clones are [absolutely positioned](https://developer.mozilla.org/en-US/docs/Web/CSS/position#Absolute_positioning)
      over the original elements.
    - The transition is applied to the cloned elements.
      At no point will the hidden, original elements be animated.
    - When the transition has finished, the clones are removed from the DOM and the new element is shown.
      The old element remains hidden in the DOM.
  
    @function up.morph
    @param {Element|jQuery|string} source
    @param {Element|jQuery|string} target
    @param {Function(oldElement, newElement)|string} transition
    @param {number} [options.duration=300]
      The duration of the animation, in milliseconds.
    @param {string} [options.easing='ease']
      The timing function that controls the transition's acceleration.
  
      See [MDN documentation](https://developer.mozilla.org/en-US/docs/Web/CSS/transition-timing-function)
      for a list of pre-defined timing functions.
    @param {boolean} [options.reveal=false]
      Whether to reveal the new element by scrolling its parent viewport.
    @return {Promise}
      A promise that fulfills when the transition ends.
    @stable
    */
    function morph(oldElement, newElement, transitionObject, options) {
        options = u.options(options);
        applyConfig(options);
        // If passed a selector, up.fragment.get() will prefer a match on the current layer.
        oldElement = up.fragment.get(oldElement);
        newElement = up.fragment.get(newElement);
        const transitionFn = findTransitionFn(transitionObject);
        const willMorph = willAnimate(oldElement, transitionFn, options);
        // Remove callbacks from our options hash in case transitionFn calls morph() recursively.
        // If we passed on these callbacks, we might call destructors, events, etc. multiple times.
        const beforeStart = u.pluckKey(options, 'beforeStart') || u.noop;
        const afterInsert = u.pluckKey(options, 'afterInsert') || u.noop;
        const beforeDetach = u.pluckKey(options, 'beforeDetach') || u.noop;
        const afterDetach = u.pluckKey(options, 'afterDetach') || u.noop;
        // Callback to scroll newElement into position before we start the enter animation.
        const scrollNew = u.pluckKey(options, 'scrollNew') || u.asyncNoop;
        beforeStart();
        if (willMorph) {
            if (motionController.isActive(oldElement) && (options.trackMotion === false)) {
                return transitionFn(oldElement, newElement, options);
            }
            up.puts('up.morph()', 'Morphing %o to %o with transition %O', oldElement, newElement, transitionObject);
            const viewport = up.viewport.get(oldElement);
            const scrollTopBeforeReveal = viewport.scrollTop;
            const oldRemote = up.viewport.absolutize(oldElement, {
                // Because the insertion will shift elements visually, we must delay insertion
                // until absolutize() has measured the bounding box of the old element.
                afterMeasure() {
                    e.insertBefore(oldElement, newElement);
                    afterInsert();
                }
            });
            const trackable = async function () {
                // (1) Scroll newElement into position before we start the enter animation.
                // (2) The return value of scrollNew() may or may not be a promise, so we convert
                //     it to a promise by wrapping it in Promise.resolve().
                await scrollNew();
                // Since we have scrolled the viewport (containing both oldElement and newElement),
                // we must shift the old copy so it looks like it it is still sitting
                // in the same position.
                const scrollTopAfterReveal = viewport.scrollTop;
                oldRemote.moveBounds(0, scrollTopAfterReveal - scrollTopBeforeReveal);
                await transitionFn(oldElement, newElement, options);
                beforeDetach();
                e.remove(oldRemote.bounds);
                afterDetach();
            };
            return motionController.startFunction([oldElement, newElement], trackable, options);
        }
        else {
            beforeDetach();
            // Swapping the elements directly with replaceWith() will cause
            // jQuery to remove all data attributes, which we use to store destructors
            swapElementsDirectly(oldElement, newElement);
            afterInsert();
            afterDetach();
            return scrollNew();
        }
    }
    function findTransitionFn(object) {
        if (isNone(object)) {
            return undefined;
        }
        else if (u.isFunction(object)) {
            return object;
        }
        else if (u.isArray(object)) {
            return composeTransitionFn(...object);
        }
        else if (u.isString(object)) {
            let namedTransition;
            if (object.indexOf('/') >= 0) { // Compose a transition from two animation names
                return composeTransitionFn(...object.split('/'));
            }
            else if (namedTransition = namedTransitions[object]) {
                return findTransitionFn(namedTransition);
            }
        }
        else {
            return up.fail("Unknown transition %o", object);
        }
    }
    function composeTransitionFn(oldAnimation, newAnimation) {
        // A composition of two null-animations is a null-transform
        // and should be skipped.
        if (!isNone(oldAnimation) && !isNone(newAnimation)) {
            const oldAnimationFn = findAnimationFn(oldAnimation) || u.asyncNoop;
            const newAnimationFn = findAnimationFn(newAnimation) || u.asyncNoop;
            return (oldElement, newElement, options) => Promise.all([
                oldAnimationFn(oldElement, options),
                newAnimationFn(newElement, options)
            ]);
        }
    }
    function findAnimationFn(object) {
        if (isNone(object)) {
            return undefined;
        }
        else if (u.isFunction(object)) {
            return object;
        }
        else if (u.isString(object)) {
            return findNamedAnimation(object);
        }
        else if (u.isOptions(object)) {
            return (element, options) => animateNow(element, object, options);
        }
        else {
            return up.fail('Unknown animation %o', object);
        }
    }
    // Have a separate function so we can mock it in specs.
    const swapElementsDirectly = up.mockable(function (oldElement, newElement) {
        e.replace(oldElement, newElement);
    });
    /*-
    Defines a named transition that [morphs](/up.morph) from one element to another.
  
    ### Example
  
    Here is the definition of the pre-defined `cross-fade` animation:
  
    ```js
    up.transition('cross-fade', (oldElement, newElement, options) ->
      Promise.all([
        up.animate(oldElement, 'fade-out', options),
        up.animate(newElement, 'fade-in', options)
      ])
    )
    ```
  
    It is recommended that your transitions use [`up.animate()`](/up.animate),
    passing along the `options` that were passed to you.
  
    If you choose to *not* use `up.animate()` and roll your own
    logic instead, your code must honor the following contract:
  
    1. It must honor the options `{ duration, easing }` if given.
    2. It must *not* remove any of the given elements from the DOM.
    3. It returns a promise that is fulfilled when the transition has ended.
    4. If during the animation an event `up:motion:finish` is emitted on
       either element, the transition instantly jumps to the last frame
       and resolves the returned promise.
  
    Calling [`up.animate()`](/up.animate) with an object argument
    will take care of all these points.
  
    @function up.transition
    @param {string} name
    @param {Function(oldElement, newElement, options): Promise|Array} transition
    @stable
    */
    function registerTransition(name, transition) {
        const fn = findTransitionFn(transition);
        fn.isDefault = up.framework.evaling;
        namedTransitions[name] = fn;
    }
    /*-
    Defines a named animation.
  
    Here is the definition of the pre-defined `fade-in` animation:
  
    ```js
    up.animation('fade-in', function(element, options) {
      element.style.opacity = 0
      up.animate(element, { opacity: 1 }, options)
    })
    ```
  
    It is recommended that your definitions always end by calling
    calling [`up.animate()`](/up.animate) with an object argument, passing along
    the `options` that were passed to you.
  
    If you choose to *not* use `up.animate()` and roll your own
    animation code instead, your code must honor the following contract:
  
    1. It must honor the options `{ duration, easing }`, if given.
    2. It must *not* remove any of the given elements from the DOM.
    3. It returns a promise that is fulfilled when the transition has ended
    4. If during the animation an event `up:motion:finish` is emitted on
       the given element, the transition instantly jumps to the last frame
       and resolves the returned promise.
  
    Calling [`up.animate()`](/up.animate) with an object argument
    will take care of all these points.
  
    @function up.animation
    @param {string} name
    @param {Function(element, options): Promise} animation
    @stable
    */
    function registerAnimation(name, animation) {
        const fn = findAnimationFn(animation);
        fn.isDefault = up.framework.evaling;
        namedAnimations[name] = fn;
    }
    up.on('up:framework:boot', function () {
        // Explain to the user why animations aren't working.
        // E.g. the user might have disabled animations in her OS.
        if (!isEnabled()) {
            up.puts('up.motion', 'Animations are disabled');
        }
    });
    /*-
    Returns whether the given animation option will cause the animation
    to be skipped.
  
    @function up.motion.isNone
    @internal
    */
    function isNone(animationOrTransition) {
        // false, undefined, '', null and the string "none" are all ways to skip animations
        return !animationOrTransition || animationOrTransition === 'none';
    }
    function registerOpacityAnimation(name, from, to) {
        registerAnimation(name, function (element, options) {
            element.style.opacity = 0;
            e.setStyle(element, { opacity: from });
            return animateNow(element, { opacity: to }, options);
        });
    }
    registerOpacityAnimation('fade-in', 0, 1);
    registerOpacityAnimation('fade-out', 1, 0);
    function translateCSS(dx, dy) {
        return { transform: `translate(${dx}px, ${dy}px)` };
    }
    function untranslatedBox(element) {
        e.setStyle(element, translateCSS(0, 0));
        return element.getBoundingClientRect();
    }
    function registerMoveAnimations(direction, boxToTransform) {
        const animationToName = `move-to-${direction}`;
        const animationFromName = `move-from-${direction}`;
        registerAnimation(animationToName, function (element, options) {
            const box = untranslatedBox(element);
            const transform = boxToTransform(box);
            return animateNow(element, transform, options);
        });
        registerAnimation(animationFromName, function (element, options) {
            const box = untranslatedBox(element);
            const transform = boxToTransform(box);
            e.setStyle(element, transform);
            return animateNow(element, translateCSS(0, 0), options);
        });
    }
    registerMoveAnimations('top', function (box) {
        const travelDistance = box.top + box.height;
        return translateCSS(0, -travelDistance);
    });
    registerMoveAnimations('bottom', function (box) {
        const travelDistance = up.viewport.rootHeight() - box.top;
        return translateCSS(0, travelDistance);
    });
    registerMoveAnimations('left', function (box) {
        const travelDistance = box.left + box.width;
        return translateCSS(-travelDistance, 0);
    });
    registerMoveAnimations('right', function (box) {
        const travelDistance = up.viewport.rootWidth() - box.left;
        return translateCSS(travelDistance, 0);
    });
    registerTransition('cross-fade', ['fade-out', 'fade-in']);
    registerTransition('move-left', ['move-to-left', 'move-from-right']);
    registerTransition('move-right', ['move-to-right', 'move-from-left']);
    registerTransition('move-up', ['move-to-top', 'move-from-bottom']);
    registerTransition('move-down', ['move-to-bottom', 'move-from-top']);
    /*-
    [Follows](/a-up-follow) this link and swaps in the new fragment
    with an animated transition.
  
    Note that transitions are not possible when replacing the `body`
    element.
  
    ### Example
  
    ```html
    <a href="/page2"
      up-target=".story"
      up-transition="move-left">
      Next page
    </a>
    ```
  
    @selector a[up-transition]
    @params-note
      All attributes for `a[up-follow]` may also be used.
    @param [up-transition]
      The name of a [predefined transition](/up.morph#named-transitions).
    @param [up-fail-transition]
      The transition to use when the server responds with an error code.
  
      @see server-errors
    @stable
    */
    /*-
    [Submits](/form-up-submit) this form and swaps in the new fragment
    with an animated transition.
  
    ### Example
  
    ```html
    <form action="/tasks"
      up-target=".content"
      up-transition="cross-fade">
      ...
    </form>
    ```
  
    @selector form[up-transition]
    @params-note
      All attributes for `form[up-submit]` may also be used.
    @param [up-transition]
      The name of a [predefined transition](/up.morph#named-transitions).
    @param [up-fail-transition]
      The transition to use when the server responds with an error code.
  
      @see server-errors
    @stable
    */
    up.on('up:framework:reset', reset);
    return {
        morph,
        animate,
        finish,
        finishCount() { return motionController.finishCount; },
        transition: registerTransition,
        animation: registerAnimation,
        config,
        isEnabled,
        isNone,
        willAnimate,
        swapElementsDirectly
    };
})();
up.transition = up.motion.transition;
up.animation = up.motion.animation;
up.morph = up.motion.morph;
up.animate = up.motion.animate;


/***/ }),
/* 83 */
/***/ ((__unused_webpack_module, __unused_webpack_exports, __webpack_require__) => {

__webpack_require__(84);
const u = up.util;
/*-
Network requests
================

Unpoly ships with an optimized HTTP client for fast and effective
communication with your server-side app.

While you can use the browser's native `fetch()` function,
Unpoly's `up.request()` has a number of convenience features:

- Requests may be [cached](/up.request#options.cache) to reuse responses and enable [preloading](/a-up-preload).
- Requests send [additional HTTP headers](/up.protocol) that the server may use to optimize its response.
  For example, when updating a [fragment](/up.fragment), the fragment's selector is automatically sent
  as an `X-Up-Target` header. The server may choose to only render the targeted fragment.
- Useful events like `up:request:loaded` or `up:request:late` are emitted throughout the request/response
  lifecycle.
- When too many requests are sent concurrently, excessive requests are [queued](/up.network.config#config.concurrency).
  This prevents exhausting the user's bandwidth and limits race conditions in end-to-end tests.
- A very concise API requiring zero boilerplate code.

@see up.request
@see up.Response
@see up:request:late

@module up.network
*/
up.network = (function () {
    /*-
    Sets default options for this package.
  
    @property up.network.config
  
    @param {number} [config.concurrency=4]
      The maximum number of concurrently loading requests.
  
      Additional requests are queued. [Preload](/a-up-preload) requests are
      always queued behind non-preload requests.
  
      You might find it useful to set the request concurrency `1` in end-to-end tests
      to prevent race conditions.
  
      Note that your browser might impose its own request limit
      regardless of what you configure here.
  
    @param {boolean} [config.wrapMethod]
      Whether to wrap non-standard HTTP methods in a POST request.
  
      If this is set, methods other than GET and POST will be converted to a `POST` request
      and carry their original method as a `_method` parameter. This is to [prevent unexpected redirect behavior](https://makandracards.com/makandra/38347).
  
      If you disable method wrapping, make sure that your server always redirects with
      with a 303 status code (rather than 302).
  
    @param {number} [config.cacheSize=70]
      The maximum number of responses to cache.
  
      If the size is exceeded, the oldest responses will be dropped from the cache.
  
    @param {number} [config.cacheExpiry=300000]
      The number of milliseconds until a cached response expires.
  
      Defaults to 5 minutes.
  
    @param {number} [config.badDownlink=0.6]
      The connection's minimum effective bandwidth estimate required
      to prevent Unpoly from [reducing requests](/up.network.shouldReduceRequests).
  
      The value is given in megabits per second. Higher is better.
  
    @param {number} [config.badRTT=0.6]
      The connection's maximum effective round-trip time required
      to prevent Unpoly from [reducing requests](/up.network.shouldReduceRequests).
  
      The value is given in milliseconds. Lower is better.
  
    @param {number} [config.badResponseTime=400]
      How long the proxy waits until emitting the [`up:request:late` event](/up:request:late).
  
      Requests exceeding this response time will also cause a [progress bar](/up.network.config#config.progressBar)
      to appear at the top edge of the screen.
  
      This metric is *not* considered for the decision to
      [reduce requests](/up.network.shouldReduceRequests).
  
      The value is given in milliseconds.
  
    @param {Function(up.Request): boolean} [config.autoCache]
      Whether to cache the given request with `{ cache: 'auto' }`.
  
      By default Unpoly will auto-cache requests with safe HTTP methods.
  
    @param {Function(up.Request, up.Response)} config.clearCache
      Whether to [clear the cache](/up.cache.clear) after the given request and response.
  
      By default Unpoly will clear the entire cache after a request with an unsafe HTTP method.
  
    @param {Array<string>|Function(up.Request): Array<string>} [config.requestMetaKeys]
      An array of request property names
      that are sent to the server as [HTTP headers](/up.protocol).
  
      The server may return an optimized response based on these properties,
      e.g. by omitting a navigation bar that is not targeted.
  
      ### Cacheability considerations
  
      Two requests with different `requestMetaKeys` are considered cache misses when [caching](/up.request) and
      [preloading](/a-up-preload). To **improve cacheability**, you may set
      `up.network.config.requestMetaKeys` to a shorter list of property keys.
  
      ### Available fields
  
      The default configuration is `['target', 'failTarget', 'mode', 'failMode', 'context', 'failContext']`.
      This means the following properties are sent to the server:
  
      | Request property         | Request header      |
      |--------------------------|---------------------|
      | `up.Request#target`      | `X-Up-Target`       |
      | `up.Request#failTarget`  | `X-Up-Fail-Target`  |
      | `up.Request#context`     | `X-Up-Context`      |
      | `up.Request#failContext` | `X-Up-Fail-Context` |
      | `up.Request#mode`        | `X-Up-Mode`         |
      | `up.Request#failMode`    | `X-Up-Fail-Mode`    |
  
      ### Per-route configuration
  
      You may also configure a function that accepts an [`up.Request`](/up.Request) and returns
      an array of request property names that are sent to the server.
  
      With this you may send different request properties for different URLs:
  
      ```javascript
      up.network.config.requestMetaKeys = function(request) {
        if (request.url == '/search') {
          // The server optimizes responses on the /search route.
          return ['target', 'failTarget']
        } else {
          // The server doesn't optimize any other route,
          // so configure maximum cacheability.
          return []
        }
      }
      ```
  
    @param {boolean|Function(): boolean} [config.progressBar]
      Whether to show a progress bar for [late requests](/up:request:late).
  
      The progress bar is implemented as a single `<up-progress-bar>` element.
      Unpoly will automatically insert and remove this element as requests
      are [late](/up:request:late) or [recovered](/up:request:recover).
  
      The default appearance is a simple blue bar at the top edge of the screen.
      You may customize the style using CSS:
  
      ```css
      up-progress-bar {
        background-color: red;
      }
      ```
  
    @stable
    */
    const config = new up.Config(() => ({
        concurrency: 4,
        wrapMethod: true,
        cacheSize: 70,
        cacheExpiry: 1000 * 60 * 5,
        badDownlink: 0.6,
        badRTT: 750,
        badResponseTime: 400,
        // 2G 66th percentile: RTT >= 1400 ms, downlink <=  70 Kbps
        // 3G 50th percentile: RTT >=  270 ms, downlink <= 700 Kbps
        autoCache(request) { return request.isSafe(); },
        clearCache(request, _response) { return !request.isSafe(); },
        requestMetaKeys: ['target', 'failTarget', 'mode', 'failMode', 'context', 'failContext'],
        progressBar: true
    }));
    const queue = new up.Request.Queue();
    const cache = new up.Request.Cache();
    let progressBar = null;
    /*-
    Returns an earlier request [matching](/up.network.config#config.requestMetaKeys) the given request options.
  
    Returns `undefined` if the given request is not currently cached.
  
    Note that `up.request()` will only write to the cache with `{ cache: true }`.
  
    ### Example
  
    ```
    let request = up.cache.get({ url: '/foo' })
  
    if (request) {
      let response = await request
      console.log("Response is %o", response)
    } else {
      console.log("The path /foo has not been requested before!")
    }
    ```
  
    @function up.cache.get
    @param {Object} requestOptions
      The request options to match against the cache.
  
      See `options` for `up.request()` for documentation.
  
      The user may configure `up.network.config.requestMetaKeys` to define
      which request options are relevant for cache matching.
    @return {up.Request|undefined}
      The cached request.
    @experimental
    */
    /*-
    Removes all [cache](/up.request#caching) entries.
  
    To only remove some cache entries, pass a [URL pattern](/url-patterns):
  
    ```js
    up.cache.clear('/users/*')
    ```
  
    ### Other reasons the cache may clear
  
    By default Unpoly automatically clears the entire cache whenever it processes
    a request with an non-GET HTTP method. To customize this rule, use `up.network.config.clearCache`.
  
    The server may also clear the cache by sending an [`X-Up-Clear-Cache`](/X-Up-Clear-Cache) header.
  
    @function up.cache.clear
    @param {string} [pattern]
      A [URL pattern](/url-patterns) matching cache entries that should be cleared.
  
      If omitted, the entire cache is cleared.
    @stable
    */
    /*-
    Makes the cache assume that `newRequest` has the same response as the
    already cached `oldRequest`.
  
    Unpoly uses this internally when the user redirects from `/old` to `/new`.
    In that case, both `/old` and `/new` will cache the same response from `/new`.
  
    @function up.cache.alias
    @param {Object} oldRequest
      The earlier [request options](/up.request).
    @param {Object} newRequest
      The new [request options](/up.request).
    @experimental
    */
    /*-
    Manually stores a request in the cache.
  
    Future calls to `up.request()` will try to re-use this request before
    making a new request.
  
    @function up.cache.set
    @param {string} request.url
    @param {string} [request.method='GET']
    @param {string} [request.target='body']
    @param {up.Request} request
      The request to cache. The cache is also a promise for the response.
    @internal
    */
    /*-
    Manually removes the given request from the cache.
  
    You can also [configure](/up.network.config) when
    cache entries expire automatically.
  
    @function up.cache.remove
    @param {Object} requestOptions
      The request options for which to remove cached requests.
  
      See `options` for `up.request()` for documentation.
    @experimental
    */
    function reset() {
        abortRequests();
        queue.reset();
        config.reset();
        cache.clear();
        progressBar?.destroy();
        progressBar = null;
    }
    /*-
    Makes an AJAX request to the given URL.
  
    Returns an `up.Request` object which contains information about the request.
    This request object is also a promise for an `up.Response` that contains
    the response text, headers, etc.
  
    ### Example
  
    ```js
    let request = up.request('/search', { params: { query: 'sunshine' } })
    console.log('We made a request to', request.url)
  
    let response = await request
    console.log('The response text is', response.text)
    ```
  
    ### Error handling
  
    The returned promise will fulfill with an `up.Response` when the server
    responds with an HTTP status of 2xx (like `200`).
  
    When the server responds with an HTTP error code (like `422` or `500`), the promise
    will *reject* with `up.Response`.
  
    When the request fails from a fatal error (like a timeout or loss of connectivity),
    the promise will reject with an [`Error`](https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Error) object.
  
    Here is an example for a complete control flow that handles both HTTP error codes
    and fatal errors:
  
    ```js
    try {
      let response = await up.request('/search', { params: { query: 'sunshine' } })
      console.log('Successful response with text:', response.text)
    } catch (e) {
      if (e instanceof up.Response) {
        console.log('Server responded with HTTP status %s and text %s', e.status, e.text)
      } else {
        console.log('Fatal error during request:', e.message)
      }
    }
    ```
  
    ### Caching
  
    You may cache responses by passing a `{ cache }` option. Responses for a cached
    request will resolve instantly.
  
    By default the cache cleared after making a request with an unsafe HTTP method.
  
    You can configure caching with the [`up.network.config`](/up.network.config) property.
  
    @function up.request
  
    @param {string} [url]
      The URL for the request.
  
      Instead of passing the URL as a string argument, you can also pass it as an `{ url }` option.
  
    @param {string} [options.url]
      The URL for the request.
  
    @param {string} [options.method='GET']
      The HTTP method for the request.
  
    @param {Object|up.Params|string|Array} [options.params={}]
      [Parameters](/up.Params) that should be sent as the request's
      [query string](https://en.wikipedia.org/wiki/Query_string) or payload.
  
      When making a `GET` request to a URL with a query string, the given `{ params }` will be added
      to the query parameters.
  
    @param {boolean} [options.cache=false]
      Whether to read from and write to the [cache](/up.request#caching).
  
      With `{ cache: true }` Unpoly will try to re-use a cached response before connecting
      to the network. If no cached response exists, Unpoly will make a request and cache
      the server response.
  
      With `{ cache: 'auto' }` Unpoly will use the cache only if `up.network.config.autoCache`
      returns `true` for this request.
  
      With `{ cache: false }` (the default) Unpoly will always make a network request.
  
    @param {boolean|string} [options.clearCache]
      Whether to [clear](/up.cache.clear) the [cache](/up.cache.get) after this request.
  
      Defaults to the result of `up.network.config.clearCache`, which
      defaults to clearing the entire cache after a non-GET request.
  
      You may also pass a [URL pattern](/url-patterns) to only uncache matching responses.
  
    @param {boolean|string|Function} [options.solo]
      With `{ solo: true }` Unpoly will [abort](/up.network.abort) all other requests before making this new request.
  
      To only abort some requests, pass an [URL pattern](/url-patterns) that matches requests to abort.
      You may also pass a function that accepts an existing `up.Request` and returns a boolean value.
  
    @param {Object} [options.headers={}]
      An object of additional HTTP headers.
  
      Note that Unpoly will by default send a number of custom request headers.
      See `up.protocol` and `up.network.config.requestMetaKeys` for details.
  
    @param {boolean} [options.wrapMethod]
      Whether to wrap non-standard HTTP methods in a POST request.
  
      If this is set, methods other than GET and POST will be converted to a `POST` request
      and carry their original method as a `_method` parameter. This is to [prevent unexpected redirect behavior](https://makandracards.com/makandra/38347).
  
      Defaults to [`up.network.config`](/up.network.config#config.wrapMethod).
  
    @param {string} [options.timeout]
      A timeout in milliseconds.
  
      If the request is queued due to [many concurrent requests](/up.network.config#config.concurrency),
      the timeout will not include the time spent waiting in the queue.
  
    @param {string} [options.target='body']
      The CSS selector that will be sent as an `X-Up-Target` header.
  
    @param {string} [options.failTarget='body']
      The CSS selector that will be sent as an `X-Up-Fail-Target` header.
  
    @param {string} [options.layer='current']
      The [layer](/up.layer) this request is associated with.
  
      If this request is intended to update an existing fragment, this is that fragment's layer.
  
      If this request is intended to [open an overlay](/opening-overlays),
      the associated layer is the future overlay's parent layer.
  
    @param {string} [options.failLayer='current']
      The [layer](/up.layer) this request is associated with if the server [sends a HTTP status code](/server-errors).
  
    @param {Element} [options.origin]
      The DOM element that caused this request to be sent, e.g. a hyperlink or form element.
  
    @param {Element} [options.contentType]
      The format in which to encode the request params.
  
      Allowed values are `application/x-www-form-urlencoded` and `multipart/form-data`.
      Only `multipart/form-data` can transport binary data.
  
      If this option is omitted Unpoly will prefer `application/x-www-form-urlencoded`,
      unless request params contains binary data.
  
    @param {string} [options.payload]
      A custom payload for this request.
  
      By default Unpoly will build a payload from the given `{ params }` option.
      Therefore this option is not required when making a standard link or form request to a server
      that renders HTML.
  
      A use case for this option is talking to a JSON API that expects requests with a `application/json` payload.
  
      If a `{ payload }` option is given you must also pass a `{ contentType }`.
  
    @return {up.Request}
      An object with information about the request.
  
      The request object is also a promise for its `up.Response`.
  
    @stable
    */
    function makeRequest(...args) {
        const request = new up.Request(parseRequestOptions(args));
        useCachedRequest(request) || queueRequest(request);
        handleSolo(request);
        return request;
    }
    function mimicLocalRequest(options) {
        handleSolo(options);
        // We cannot consult config.clearCache since there is no up.Request
        // for a local update.
        let clearCache = options.clearCache;
        if (clearCache) {
            cache.clear(clearCache);
        }
    }
    function handleSolo(requestOrOptions) {
        let solo = requestOrOptions.solo;
        if (solo && isBusy()) {
            up.puts('up.request()', 'Change with { solo } option will abort other requests');
            // The { solo } option may also contain a function.
            // This way users can excempt some requests from being solo-aborted
            // by configuring up.fragment.config.navigateOptions.
            if (requestOrOptions instanceof up.Request) {
                queue.abortExcept(requestOrOptions, solo);
            }
            else {
                abortRequests(solo);
            }
        }
    }
    function parseRequestOptions(args) {
        const options = u.extractOptions(args);
        if (!options.url) {
            options.url = args[0];
        }
        up.migrate.handleRequestOptions?.(options);
        return options;
    }
    function useCachedRequest(request) {
        // If we have an existing promise matching this new request,
        // we use it unless `request.cache` is explicitly set to `false`.
        let cachedRequest;
        if (request.willCache() && (cachedRequest = cache.get(request))) {
            up.puts('up.request()', 'Re-using previous request to %s %s', request.method, request.url);
            // Check if we need to upgrade a cached background request to a foreground request.
            // This might affect whether we're going to emit an up:request:late event further
            // down. Consider this case:
            //
            // - User preloads a request (1). We have a cache miss and connect to the network.
            //   This will never trigger `up:request:late`, because we only track foreground requests.
            // - User loads the same request (2) in the foreground (no preloading).
            //   We have a cache hit and receive the earlier request that is still preloading.
            //   Now we *should* trigger `up:request:late`.
            // - The request (1) finishes. This triggers `up:request:recover`.
            if (!request.preload) {
                queue.promoteToForeground(cachedRequest);
            }
            // We cannot simply return `cachedRequest`, since that might have a different #hash property.
            // While two requests with a different #hash have the same cache key, they are
            // not the same object.
            //
            // What we do instead is have `request` follow the state of `cachedRequest`'s exchange.
            request.followState(cachedRequest);
            return true;
        }
    }
    // If no existing promise is available, we queue a network request.
    function queueRequest(request) {
        if (request.preload && !request.isSafe()) {
            up.fail('Will not preload request to %s', request.description);
        }
        handleCaching(request);
        queue.asap(request);
        return true;
    }
    function handleCaching(request) {
        if (request.willCache()) {
            // Cache the request for calls for calls with the same URL, method, params
            // and target. See up.Request#cacheKey().
            cache.set(request, request);
        }
        return u.always(request, function (response) {
            // Three places can request the cache to be cleared or kept:
            // (1) The server via X-Up-Clear-Cache header, found in response.clearCache
            // (2) The interaction via { clearCache } option, found in request.clearCache
            // (3) The default in up.network.config.clearCache({ request, response })
            let clearCache = response.clearCache ?? request.clearCache ?? config.clearCache(request, response);
            if (clearCache) {
                cache.clear(clearCache);
            }
            // (1) Re-cache a cacheable request in case we cleared the cache above
            // (2) An un-cacheable request should still update an existing cache entry
            //     (written by a earlier, cacheable request with the same cache key)
            //     since the later response will be fresher.
            if (request.willCache() || cache.get(request)) {
                cache.set(request, request);
            }
            if (!response.ok) {
                // Uncache failed requests. We have no control over the server,
                // and another request with the same properties might succeed.
                cache.remove(request);
            }
        });
    }
    /*-
    Returns whether Unpoly is currently waiting for a [request](/up.request) to finish.
  
    @function up.network.isBusy
    @return {boolean}
    @stable
    */
    function isBusy() {
        return queue.isBusy();
    }
    /*-
     Returns whether Unpoly is *not* currently waiting for a [request](/up.request) to finish.
  
     @function up.network.isIdle
     @return {boolean}
     @stable
     */
    const isIdle = u.negate(isBusy);
    /*-
    Makes a full-page request, replacing the entire browser environment with a new page from the server response.
  
    Also see `up.Request#loadPage()`.
  
    @function up.network.loadPage
    @param {string} options.url
      The URL to load.
    @param {string} [options.method='get']
      The method for the request.
  
      Methods other than GET or POST will be [wrapped](/up.protocol.config#config.methodParam) in a POST request.
    @param {Object|Array|FormData|string} [options.params]
    @experimental
    */
    function loadPage(requestsAttrs) {
        new up.Request(requestsAttrs).loadPage();
    }
    /*-
    Returns whether optional requests should be avoided where possible.
  
    We assume the user wants to avoid requests if either of following applies:
  
    - The user has enabled data saving in their browser ("Lite Mode" in Chrome for Android).
    - The connection's effective round-trip time is longer than `up.network.config.badRTT`.
    - The connection's effective bandwidth estimate is less than `up.network.config.badDownlink`.
  
    By default Unpoly will disable [preloading](/a-up-preload) and [polling](/up-poll) if requests
    should be avoided.
  
    @function up.network.shouldReduceRequests
    @return {boolean}
      Whether requests should be avoided where possible.
    @experimental
    */
    function shouldReduceRequests() {
        // Browser support for navigator.connection: https://caniuse.com/?search=networkinformation
        let netInfo = navigator.connection;
        if (netInfo) {
            // API for NetworkInformation#downlink: https://developer.mozilla.org/en-US/docs/Web/API/NetworkInformation/downlink
            // API for NetworkInformation#rtt:      https://developer.mozilla.org/en-US/docs/Web/API/NetworkInformation/rtt
            // API for NetworkInformation#saveData: https://developer.mozilla.org/en-US/docs/Web/API/NetworkInformation/saveData
            return netInfo.saveData ||
                (netInfo.rtt && (netInfo.rtt > config.badRTT)) ||
                (netInfo.downlink && (netInfo.downlink < config.badDownlink));
        }
    }
    /*-
    Aborts pending [requests](/up.request).
  
    The event `up:request:aborted` will be emitted.
  
    The promise returned by `up.request()` will be rejected with an exception named `AbortError`:
  
        try {
          let response = await up.request('/path')
          console.log(response.text)
        } catch (err) {
          if (err.name == 'AbortError') {
            console.log('Request was aborted')
          }
        }
  
    ### Examples
  
    Without arguments, this will abort all pending requests:
  
    ```js
    up.network.abort()
    ```
  
    To abort a given `up.Request` object, pass it as the first argument:
  
    ```js
    let request = up.request('/path')
    up.network.abort(request)
    ```
  
    To abort all requests matching a condition, pass a function that takes a request
    and returns a boolean value. Unpoly will abort all request for which the given
    function returns `true`. E.g. to abort all requests with a HTTP method as `GET`:
  
    ```js
    up.network.abort((request) => request.method == 'GET')
    ```
  
    @function up.network.abort
    @param {up.Request|boolean|Function(up.Request): boolean} [matcher=true]
      If this argument is omitted, all pending requests are aborted.
    @stable
    */
    function abortRequests(...args) {
        queue.abort(...args);
    }
    /*-
    This event is [emitted](/up.emit) when an [AJAX request](/up.request)
    was [aborted](/up.network.abort).
  
    The event is emitted on the layer that caused the request.
  
    @event up:request:aborted
  
    @param {up.Request} event.request
      The aborted request.
  
    @param {up.Layer} [event.layer]
      The [layer](/up.layer) this request is associated with.
  
      If this request was intended to update an existing fragment, this is that fragment's layer.
  
      If this request was intended to [open an overlay](/opening-overlays),
      the associated layer is the future overlay's parent layer.
  
    @param {Element} [event.origin]
      The link or form element that caused the request.
  
    @param event.preventDefault()
  
    @experimental
    */
    /*-
    This event is [emitted](/up.emit) when [AJAX requests](/up.request)
    are taking long to finish.
  
    By default Unpoly will wait 400 ms for an AJAX request to finish
    before emitting `up:request:late`. You may configure this delay like this:
  
    ```js
    up.network.config.badResponseTime = 1000 // milliseconds
    ```
  
    Once all responses have been received, an [`up:request:recover`](/up:request:recover)
    will be emitted.
  
    Note that if additional requests are made while Unpoly is already busy
    waiting, **no** additional `up:request:late` events will be triggered.
  
    ### Loading indicators
  
    By default the `up:request:late` event will cause a [progress bar](/up.network.config#config.progressBar)
    to appear at the top edge of the screen.
  
    If you don't like the default progress bar, you can [listen](/up.on) to the `up:request:late`
    and [`up:request:recover`](/up:request:recover) events to implement a custom
    loading indicator that appears during long-running requests.
  
    To build a custom loading indicator, please an element like this in your application layout:
  
    ```html
    <loading-indicator>Please wait!</loading-indicator>
    ```
  
    Now add a [compiler](/up.compiler) that hides the `<loading-indicator>` element
    while there are no long-running requests:
  
    ```js
    // Disable the default progress bar
    up.network.config.progressBar = false
  
    up.compiler('loading-indicator', function(indicator) {
      function show() { up.element.show(indicator) }
      function hide() { up.element.hide(indicator) }
  
      hide()
  
      return [
        up.on('up:request:late', show),
        up.on('up:request:recover', hide)
      ]
    })
    ```
  
    @event up:request:late
    @stable
    */
    /*-
    This event is [emitted](/up.emit) when [AJAX requests](/up.request)
    have [taken long to finish](/up:request:late), but have finished now.
  
    See [`up:request:late`](/up:request:late) for more documentation on
    how to use this event for implementing a spinner that shows during
    long-running requests.
  
    @event up:request:recover
    @stable
    */
    /*-
    This event is [emitted](/up.emit) before an [AJAX request](/up.request)
    is sent over the network.
  
    The event is emitted on the layer that caused the request.
  
    @event up:request:load
    @param {up.Request} event.request
      The request to be sent.
    @param {up.Layer} [event.layer]
      The [layer](/up.layer) this request is associated with.
  
      If this request is intended to update an existing fragment, this is that fragment's layer.
  
      If this request is intended to [open an overlay](/opening-overlays),
      the associated layer is the future overlay's parent layer.
    @param {Element} [event.origin]
      The link or form element that caused the request.
    @param event.preventDefault()
      Event listeners may call this method to prevent the request from being sent.
    @stable
    */
    function registerAliasForRedirect(request, response) {
        if (request.cache && response.url && request.url !== response.url) {
            const newRequest = request.variant({
                method: response.method,
                url: response.url
            });
            cache.alias(request, newRequest);
        }
    }
    /*-
    This event is [emitted](/up.emit) when the response to an [AJAX request](/up.request)
    has been received.
  
    Note that this event will also be emitted when the server signals an
    error with an HTTP status like `500`. Only if the request
    encounters a fatal error (like a loss of network connectivity),
    [`up:request:fatal`](/up:request:fatal) is emitted instead.
  
    The event is emitted on the layer that caused the request.
  
    @event up:request:loaded
  
    @param {up.Request} event.request
      The request.
  
    @param {up.Response} event.response
      The response that was received from the server.
  
    @param {up.Layer} [event.layer]
      The [layer](/up.layer) this request is associated with.
  
      If this request is intended to update an existing fragment, this is that fragment's layer.
  
      If this request is intended to [open an overlay](/opening-overlays),
      the associated layer is the future overlay's parent layer.
  
    @param {Element} [event.origin]
      The link or form element that caused the request.
  
    @stable
    */
    /*-
    This event is [emitted](/up.emit) when an [AJAX request](/up.request)
    encounters fatal error like a timeout or loss of network connectivity.
  
    Note that this event will *not* be emitted when the server produces an
    error message with an HTTP status like `500`. When the server can produce
    any response, [`up:request:loaded`](/up:request:loaded) is emitted instead.
  
    The event is emitted on the layer that caused the request.
  
    @event up:request:fatal
  
    @param {up.Request} event.request
      The failed request.
  
    @param {up.Layer} [event.layer]
      The [layer](/up.layer) this request is associated with.
  
      If this request was intended to update an existing fragment, this is that fragment's layer.
  
      If this request was intended to [open an overlay](/opening-overlays),
      the associated layer is the future overlay's parent layer.
  
    @param {Element} [event.origin]
      The link or form element that caused the request.
  
    @stable
    */
    function isSafeMethod(method) {
        return u.contains(['GET', 'OPTIONS', 'HEAD'], u.normalizeMethod(method));
    }
    function onLate() {
        if (u.evalOption(config.progressBar)) {
            progressBar = new up.ProgressBar();
        }
    }
    function onRecover() {
        progressBar?.conclude();
    }
    up.on('up:request:late', onLate);
    up.on('up:request:recover', onRecover);
    up.on('up:framework:reset', reset);
    return {
        request: makeRequest,
        cache,
        isIdle,
        isBusy,
        isSafeMethod,
        config,
        abort: abortRequests,
        registerAliasForRedirect,
        queue,
        shouldReduceRequests,
        mimicLocalRequest,
        loadPage,
    };
})();
up.request = up.network.request;
up.cache = up.network.cache;


/***/ }),
/* 84 */
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin


/***/ }),
/* 85 */
/***/ ((__unused_webpack_module, __unused_webpack_exports, __webpack_require__) => {

__webpack_require__(86);
const u = up.util;
const e = up.element;
/*-
Layers
======

Unpoly allows you to [open page fragments in an overlay](/opening-overlays). Overlays may be stacked infinitely.

A variety of [overlay modes](/layer-terminology) are supported,
such as modal dialogs, popup overlays or drawers. You may [customize their appearance and behavior](/customizing-overlays).

Layers are isolated, meaning a screen in one layer will not accidentally see elements
or events from another layer. For instance, [fragment links](/up.link) will only update elements from the [current layer](/up.layer.current)
unless you [explicitly target another layer](/layer-option).

Overlays allow you to break up a complex screen into [subinteractions](/subinteractions).
Subinteractions take place in overlays and may span one or many pages while the original screen remains open in the background.
Once the subinteraction is *done*, the overlay is closed and a result value is communicated back to the parent layer.

@see layer-terminology
@see layer-option
@see opening-overlays
@see closing-overlays
@see subinteractions
@see customizing-overlays
@see context

@see a[up-layer=new]
@see up.layer.current
@see up.layer.on
@see up.layer.ask

@module up.layer
*/
up.layer = (function () {
    const LAYER_CLASSES = [
        up.Layer.Root,
        up.Layer.Modal,
        up.Layer.Popup,
        up.Layer.Drawer,
        up.Layer.Cover
    ];
    /*-
    Configures default attributes for new overlays.
  
    All options for `up.layer.open()` may be configured.
    The configuration will also be used for `a[up-layer=new]` links.
  
    Defaults are configured separately for each [layer mode](/layer-terminology):
  
    | Object                    | Effect                       |
    |---------------------------|------------------------------|
    | `up.layer.config.root`    | Defaults for the root layer  |
    | `up.layer.config.modal`   | Defaults for modal overlays  |
    | `up.layer.config.drawer`  | Defaults for drawer overlays |
    | `up.layer.config.popup`   | Defaults for popup overlays  |
    | `up.layer.config.cover`   | Defaults for cover overlays  |
  
    For convenience you may configure options that affect all layer modes
    or all overlay modes:
  
    | Object                    | Effect                       |
    |---------------------------|------------------------------|
    | `up.layer.config.any`     | Defaults for all layers      |
    | `up.layer.config.overlay` | Defaults for all overlays    |
  
    Options configured in such a way are inherited.
    E.g. when you open a new drawer overlay, defaults from `up.layer.config.drawer`,
    `up.layer.config.overlay` and `up.layer.config.any` will be used (in decreasing priority).
  
    ### Example
  
    To make all modal overlays move in from beyond the top edge of the screen:
  
    ```js
    up.layer.config.modal.openAnimation = 'move-from-top'
    ```
  
    To configure an additional [main target](/up-main)
    for overlay of any mode:
  
    ```js
    up.layer.config.overlay.mainTargets.unshift('.content')
    ```
  
    ### Configuration inheritance
  
    @property up.layer.config
  
    @param {string} [config.mode='modal']
      The default [mode](/layer-terminology) used when opening a new overlay.
  
    @param {object} config.any
      Defaults for all layer modes.
  
    @param {Array<string>} config.any.mainTargets
      An array of CSS selectors matching default render targets.
  
      This is an alias for `up.fragment.config.mainTargets`.
  
    @param {object} config.root
      Defaults for the [root layer](/layer-terminology).
  
      Inherits from `up.layer.config.any`.
  
    @param {object} config.root.mainTargets
  
    @param {object} config.overlay
      Defaults for all [overlays](/layer-terminology).
  
      In addition to the options documented here,
      all options for `up.layer.open()` may also be configured.
  
      Inherits from `up.layer.config.any`.
  
    @param {string|Function} config.overlay.openAnimation
      The opening animation.
  
    @param {number} config.overlay.openDuration
      The duration of the opening animation.
  
    @param {string} config.overlay.openEasing
      The easing function for the opening animation.
  
    @param {string|Function} config.overlay.closeAnimation
      The closing animation.
  
    @param {number} config.overlay.closeDuration
      The duration of the closing animation.
  
    @param {string} config.overlay.closeEasing
      The easing function for the opening animation.
  
    @param {string} config.overlay.dismissLabel
      The symbol for the dismiss icon in the top-right corner.
  
    @param {string} config.overlay.dismissAriaLabel
      The accessibility label for the dismiss icon in the top-right corner.
  
    @param {string|boolean} config.overlay.history='auto'
      Whether the layer's location or title will be visible in the browser's
      address bar and window title.
  
      If set to `'auto'`, the overlay will render history if its initial fragment
      is an [auto history target](/up.fragment.config#config.autoHistoryTargets).
  
      If set to `true`, the overlay will always render history.
      If set to `false`, the overlay will never render history.
  
    @param {string} [config.overlay.class]
      An HTML class for the overlay's container element.
  
      See [overlay classes](/customizing-overlays#overlay-classes).
  
    @param {object} config.modal
      Defaults for [modal overlays](/layer-terminology).
  
      Inherits from `up.layer.config.overlay` and `up.layer.config.any`.
  
    @param {object} config.cover
      Defaults for [cover overlays](/layer-terminology).
  
      Inherits from `up.layer.config.overlay` and `up.layer.config.any`.
  
    @param {object} config.drawer
      Defaults for [drawer overlays](/layer-terminology).
  
      Inherits from `up.layer.config.overlay` and `up.layer.config.any`.
  
    @param {object} config.popup
      Defaults for [popup overlays](/layer-terminology).
  
      Inherits from `up.layer.config.overlay` and `up.layer.config.any`.
  
    @stable
    */
    const config = new up.Config(function () {
        const newConfig = {
            mode: 'modal',
            any: {
                mainTargets: [
                    "[up-main='']",
                    'main',
                    ':layer' // this is <body> for the root layer
                ]
            },
            root: {
                mainTargets: ['[up-main~=root]'],
                history: true
            },
            overlay: {
                mainTargets: ['[up-main~=overlay]'],
                openAnimation: 'fade-in',
                closeAnimation: 'fade-out',
                dismissLabel: '×',
                dismissAriaLabel: 'Dismiss dialog',
                dismissable: true,
                history: 'auto'
            },
            cover: {
                mainTargets: ['[up-main~=cover]']
            },
            drawer: {
                mainTargets: ['[up-main~=drawer]'],
                backdrop: true,
                position: 'left',
                size: 'medium',
                openAnimation(layer) {
                    switch (layer.position) {
                        case 'left': return 'move-from-left';
                        case 'right': return 'move-from-right';
                    }
                },
                closeAnimation(layer) {
                    switch (layer.position) {
                        case 'left': return 'move-to-left';
                        case 'right': return 'move-to-right';
                    }
                }
            },
            modal: {
                mainTargets: ['[up-main~=modal]'],
                backdrop: true,
                size: 'medium'
            },
            popup: {
                mainTargets: ['[up-main~=popup]'],
                position: 'bottom',
                size: 'medium',
                align: 'left',
                dismissable: 'outside key'
            }
        };
        for (let Class of LAYER_CLASSES) {
            newConfig[Class.mode].Class = Class;
        }
        return newConfig;
    });
    /*-
    A list of layers that are currently open.
  
    The first element in the list is the [root layer](/up.layer.root).
    The last element is the [frontmost layer](/up.layer.front).
  
    @property up.layer.stack
    @param {List<up.Layer>} stack
    @stable
    */
    let stack = null;
    let handlers = [];
    function mainTargets(mode) {
        return u.flatMap(modeConfigs(mode), 'mainTargets');
    }
    /*
    Returns an array of config objects that apply to the given mode name.
  
    The config objects are in descending order of specificity.
    */
    function modeConfigs(mode) {
        if (mode === 'root') {
            return [config.root, config.any];
        }
        else {
            return [config[mode], config.overlay, config.any];
        }
    }
    function normalizeOptions(options) {
        up.migrate.handleLayerOptions?.(options);
        if (u.isGiven(options.layer)) { // might be the number 0, which is falsy
            let match = String(options.layer).match(/^(new|shatter|swap)( (\w+))?/);
            if (match) {
                options.layer = 'new';
                const openMethod = match[1];
                const shorthandMode = match[3];
                // The mode may come from one of these sources:
                // (1) As { mode } option
                // (2) As a { layer } short hand like { layer: 'new popup' }
                // (3) As the default in config.mode
                options.mode || (options.mode = shorthandMode || config.mode);
                if (openMethod === 'swap') {
                    // If an overlay is already open, we replace that with a new overlay.
                    // If we're on the root layer, we open an overlay.
                    if (up.layer.isOverlay()) {
                        options.baseLayer = 'parent';
                    }
                }
                else if (openMethod === 'shatter') {
                    // Dismiss all overlays and open a new overlay.
                    options.baseLayer = 'root';
                }
            }
        }
        else {
            // If no options.layer is given we still want to avoid updating "any" layer.
            // Other options might have a hint for a more appropriate layer.
            if (options.mode) {
                // If user passes a { mode } option without a { layer } option
                // we assume they want to open a new layer.
                options.layer = 'new';
            }
            else if (u.isElementish(options.target)) {
                // If we are targeting an actual Element or jQuery collection (and not
                // a selector string) we operate in that element's layer.
                options.layer = stack.get(options.target, { normalizeLayerOptions: false });
            }
            else if (options.origin) {
                // Links update their own layer by default.
                options.layer = 'origin';
            }
            else {
                // If nothing is given, we assume the current layer
                options.layer = 'current';
            }
        }
        if (!options.context) {
            options.context = {};
        }
        // Remember the layer that was current when the request was made,
        // so changes with `{ layer: 'new' }` will know what to stack on.
        // Note if options.baseLayer is given, up.layer.get('current', options) will
        // return the resolved version of that.
        options.baseLayer = stack.get('current', { ...options, normalizeLayerOptions: false });
    }
    function build(options, beforeNew) {
        const { mode } = options;
        const { Class } = config[mode];
        // modeConfigs() returns the most specific options first,
        // but in merge() below later args override keys from earlier args.
        const configs = u.reverse(modeConfigs(mode));
        let handleDeprecatedConfig = up.migrate.handleLayerConfig;
        if (handleDeprecatedConfig) {
            configs.forEach(handleDeprecatedConfig);
        }
        options = u.mergeDefined(...configs, { mode, stack }, options);
        if (beforeNew) {
            options = beforeNew(options);
        }
        return new Class(options);
    }
    function openCallbackAttr(link, attr) {
        return e.callbackAttr(link, attr, ['layer']);
    }
    function closeCallbackAttr(link, attr) {
        return e.callbackAttr(link, attr, ['layer', 'value']);
    }
    function reset() {
        config.reset();
        stack.reset();
        handlers = u.filter(handlers, 'isDefault');
    }
    /*-
    [Opens a new overlay](/opening-overlays).
  
    Opening a layer is considered [navigation](/navigation) by default.
  
    ### Example
  
    ```js
    let layer = await up.layer.open({ url: '/contacts' })
    console.log(layer.mode) // logs "modal"
    ```
  
    @function up.layer.open
  
    @param {Object} [options]
      All [render options](/up.render) may be used.
  
      You may configure default layer attributes in `up.layer.config`.
  
    @param {string} [options.layer="new"]
      Whether to stack the new overlay or replace existing overlays.
  
      See [replacing existing overlays](/opening-overlays#replacing-existing-overlays).
  
    @param {string} [options.mode]
      The kind of overlay to open.
  
      See [available layer modes](/layer-terminology#available-modes).
  
    @param {string} [options.size]
      The size of the overlay.
  
      Supported values are `'small'`, `'medium'`, `'large'` and `'grow'`:
      See [overlay sizes](/customizing-overlays#overlay-sizes) for details.
  
    @param {string} [options.class]
      An optional HTML class for the overlay's container element.
  
      See [overlay classes](/customizing-overlays#overlay-classes).
  
    @param {boolean|string|Array<string>} [options.dismissable=true]
      How the overlay may be [dismissed](/closing-overlays) by the user.
  
      Supported values are `'key'`, `'outside'` and `'button'`.
      See [customizing dismiss controls](/closing-overlays#customizing-dismiss-controls)
      for details.
  
      You may enable multiple dismiss controls by passing an array or
      a space-separated string.
  
      Passing `true` or `false` will enable or disable all dismiss controls.
  
    @param {boolean|string} [options.history]
      Whether history of the overlay content is visible.
  
      If set to `true` the overlay location and title will be shown in browser UI.
  
      If set to `'auto'` history will be visible if the initial overlay
      content matches a [main target](/up-main).
  
    @param {string|Function} [options.animation]
      The opening animation.
  
    @param {Function(Event)} [options.onOpened]
      A function that is called when the overlay was inserted into the DOM.
  
      The function argument is an `up:layer:opened` event.
  
      The overlay may still play an opening animation when this function is called.
      To be called when the opening animation is done, pass an
      [`{ onFinished }`](/up.render#options.onFinished) option.
  
    @param {Function(Event)} [options.onAccepted]
      A function that is called when the overlay was [accepted](/closing-overlays).
  
      The function argument is an `up:layer:accepted` event.
  
    @param {Function(Event)} [options.onDismissed]
      A function that is called when the overlay was [dismissed](/closing-overlays).
  
      The function argument is an `up:layer:dismissed` event.
  
    @param {string|Array<string>} [options.acceptEvent]
      One or more event types that will cause this overlay to automatically be
      [accepted](/closing-overlays) when a matching event occurs within the overlay.
  
      The [overlay result value](/closing-overlays#overlay-result-values)
      is the event object that caused the overlay to close.
  
      See [Closing when an event is emitted](/closing-overlays#closing-when-an-event-is-emitted).
  
    @param {string|Array<string>} [options.dismissEvent]
      One or more event types that will cause this overlay to automatically be
      [dismissed](/closing-overlays) when a matching event occurs within the overlay.
  
      The [overlay result value](/closing-overlays#overlay-result-values)
      is the event object that caused the overlay to close.
  
      See [Closing when an event is emitted](/closing-overlays#closing-when-an-event-is-emitted).
  
    @param {string|Array<string>} [options.acceptLocation]
      One or more [URL patterns](/url-patterns) that will cause this overlay to automatically be
      [accepted](/closing-overlays) when the overlay reaches a matching [location](/up.layer.location).
  
      The [overlay result value](/closing-overlays#overlay-result-values)
      is an object of [named segments matches](/url-patterns#capturing-named-segments) captured
      by the URL pattern.
  
      See [Closing when a location is reached](/closing-overlays#closing-when-a-location-is-reached).
  
    @param {string|Array<string>} [options.dismissLocation]
      One or more [URL patterns](/url-patterns) that will cause this overlay to automatically be
      [dismissed](/closing-overlays) when the overlay reaches a matching [location](/up.layer.location).
  
      The [overlay result value](/closing-overlays#overlay-result-values)
      is an object of [named segments matches](/url-patterns#capturing-named-segments) captured
      by the URL pattern.
  
      See [Closing when a location is reached](/closing-overlays#closing-when-a-location-is-reached).
  
    @param {Object} [options.context={}]
      The initial [context](/up.layer.context) object for the new overlay.
  
    @param {string} [options.position]
      The position of the popup relative to the `{ origin }` element that opened
      the overlay.
  
      Supported values are `'top'`,  `'right'`,  `'bottom'` and  `'left'`.
  
      See [popup position](/customizing-overlays#popup-position).
  
    @param {string} [options.align]
      The alignment of the popup within its `{ position }`.
  
      Supported values are `'top'`,  `'right'`, `'center'`, `'bottom'` and  `'left'`.
  
      See [popup position](/customizing-overlays#popup-position).
  
    @return {Promise<up.Layer>}
      A promise for the `up.Layer` object that models the new overlay.
  
      The promise will be resolved once the overlay was placed into the DOM.
  
    @stable
    */
    async function open(options) {
        options = u.options(options, {
            layer: 'new',
            defaultToEmptyContent: true,
            navigate: true
        });
        // Even if we are given { content } we need to pipe this through up.render()
        // since a lot of options processing is happening there.
        let result = await up.render(options);
        return result.layer;
    }
    /*-
    This event is emitted before an overlay is opened.
  
    The overlay is not yet part of the [layer stack](/up.layer.stack) and has not yet been placed
    in the DOM. Listeners may prevent this event to prevent the overlay from opening.
  
    The event is emitted on the `document`.
  
    ### Changing layer options
  
    Listeners may inspect and manipulate options for the overlay that is about to open.
  
    For example, to give overlays the CSS class `.warning` if the initial URL contains
    the word `"confirm"`:
  
    ```js
    up.on('up:layer:open', function(event) {
      if (event.layerOptions.url.includes('confirm')) {
        event.layerOptions.class = 'warning'
      }
    })
    ```
  
    @event up:layer:open
    @param {Object} event.layerOptions
      Options for the overlay that is about to open.
  
      Listeners may inspect and change the options.
      All options for `up.layer.open()` may be used.
    @param {Element} event.origin
      The link element that is opening the overlay.
    @param event.preventDefault()
      Event listeners may call this method to prevent the overlay from opening.
    @stable
    */
    /*-
    This event is emitted after a new overlay was placed into the DOM.
  
    The event is emitted right before the opening animation starts. Because the overlay
    has not been rendered by the browser, this makes it a good occasion to
    [customize overlay elements](/customizing-overlays#customizing-overlay-elements):
  
    ```js
    up.on('up:layer:opened', function(event) {
      if (isChristmas()) {
        up.element.affix(event.layer.element, '.santa-hat', text: 'Merry Christmas!')
      }
    })
    ```
  
    @event up:layer:opened
    @param {Element} event.origin
      The link element that is opening the overlay.
    @param {up.Layer} event.layer
      The [layer object](/up.Layer) that is opening.
    @stable
    */
    /*-
    This event is emitted after a layer's [location property](/up.Layer.prototype.location)
    has changed value.
  
    This event is also emitted when a layer [without visible history](/up.Layer.prototype.history)
    has reached a new location.
  
    @param {string} event.location
      The new location URL.
    @event up:layer:location:changed
    @experimental
    */
    /*-
    Opens an overlay and returns a promise for its [acceptance](/closing-overlays).
  
    It's useful to think of overlays as [promises](https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Promise)
    which may either be **fulfilled (accepted)** or **rejected (dismissed)**.
  
    ### Example
  
    Instead of using `up.layer.open()` and passing callbacks, you may use `up.layer.ask()`.
    `up.layer.ask()` returns a promise for the acceptance value, which you can `await`:
  
    ```js
    let user = await up.layer.ask({ url: '/users/new' })
    console.log("New user is " + user)
    ```
  
    @see closing-overlays
  
    @function up.layer.ask
  
    @param {Object} options
      See options for `up.layer.open()`.
  
    @return {Promise}
      A promise that will settle when the overlay closes.
  
      When the overlay was accepted, the promise will fulfill with the overlay's acceptance value.
  
      When the overlay was dismissed, the promise will reject with the overlay's dismissal value.
  
    @stable
    */
    function ask(options) {
        return new Promise(function (resolve, reject) {
            options = {
                ...options,
                onAccepted: (event) => resolve(event.value),
                onDismissed: (event) => reject(event.value)
            };
            open(options);
        });
    }
    function anySelector() {
        return u.map(LAYER_CLASSES, Class => Class.selector()).join(',');
    }
    function optionToString(option) {
        if (u.isString(option)) {
            return `layer "${option}"`;
        }
        else {
            return option.toString();
        }
    }
    /*-
    [Follows](/a-up-follow) this link and [opens the result in a new overlay](/opening-overlays).
  
    ### Example
  
    ```html
    <a href="/menu" up-layer="new">Open menu</a>
    ```
  
    @selector a[up-layer=new]
  
    @params-note
      All attributes for `a[up-follow]` may also be used.
  
      You may configure default layer attributes in `up.layer.config`.
  
    @param {string} [up-layer="new"]
      Whether to stack the new overlay onto the current layer or replace existing overlays.
  
      See [replacing existing overlays](/opening-overlays#replacing-existing-overlays).
  
    @param [up-mode]
      The kind of overlay to open.
  
      See [available layer modes](/layer-terminology#available-modes).
  
    @param [up-size]
      The size of the overlay.
  
      See [overlay sizes](/customizing-overlays#overlay-sizes) for details.
  
    @param [up-class]
      An optional HTML class for the overlay's container element.
  
      See [overlay classes](/customizing-overlays#overlay-classes).
  
    @param [up-history]
      Whether history of the overlay content is visible.
  
      If set to `true` the overlay location and title will be shown in browser UI.
  
      If set to `'auto'` history will be visible if the initial overlay
      content matches a [main target](/up-main).
  
    @param [up-dismissable]
      How the overlay may be [dismissed](/closing-overlays) by the user.
  
      See [customizing dismiss controls](/closing-overlays#customizing-dismiss-controls)
      for details.
  
      You may enable multiple dismiss controls by passing a space-separated string.
  
      Passing `true` or `false` will enable or disable all dismiss controls.
  
    @param [up-animation]
      The name of the opening animation.
  
    @param [up-on-opened]
      A JavaScript snippet that is called when the overlay was inserted into the DOM.
  
      The snippet runs in the following scope:
  
      | Expression | Value                                    |
      |------------|------------------------------------------|
      | `this`     | The link that opened the overlay         |
      | `layer`    | An `up.Layer` object for the new overlay |
      | `event`    | An `up:layer:opened` event               |
  
    @param [up-on-accepted]
      A JavaScript snippet that is called when the overlay was [accepted](/closing-overlays).
  
      The snippet runs in the following scope:
  
      | Expression | Value                                         |
      |------------|-----------------------------------------------|
      | `this`     | The link that originally opened the overlay   |
      | `layer`    | An `up.Layer` object for the accepted overlay |
      | `value`    | The overlay's [acceptance value](/closing-overlays#overlay-result-values) |
      | `event`    | An `up:layer:accepted` event                  |
  
      With a strict Content Security Policy [additional rules apply](/csp).
  
    @param [up-on-dismissed]
      A JavaScript snippet that is called when the overlay was [dismissed](/closing-overlays).
  
      The snippet runs in the following scope:
  
      | Expression | Value                                          |
      |------------|------------------------------------------------|
      | `this`     | The link that originally opened the overlay    |
      | `layer`    | An `up.Layer` object for the dismissed overlay |
      | `value`    | The overlay's [dismissal value](/closing-overlays#overlay-result-values) |
      | `event`    | An `up:layer:dismissed` event                   |
  
      With a strict Content Security Policy [additional rules apply](/csp).
  
    @param [up-accept-event]
      One or more space-separated event types that will cause this overlay to automatically be
      [accepted](/closing-overlays) when a matching event occurs within the overlay.
  
      The [overlay result value](/closing-overlays#overlay-result-values)
      is the event object that caused the overlay to close.
  
      See [Closing when an event is emitted](/closing-overlays#closing-when-an-event-is-emitted).
  
    @param [up-dismiss-event]
      One or more space-separated event types that will cause this overlay to automatically be
      [dismissed](/closing-overlays) when a matching event occurs within the overlay.
  
      The [overlay result value](/closing-overlays#overlay-result-values)
      is the event object that caused the overlay to close.
  
      See [Closing when an event is emitted](/closing-overlays#closing-when-an-event-is-emitted).
  
    @param [up-accept-location]
      One or more space-separated [URL patterns](/url-patterns) that will cause this overlay to automatically be
      [accepted](/closing-overlays) when the overlay reaches a matching [location](/up.layer.location).
  
      The [overlay result value](/closing-overlays#overlay-result-values)
      is an object of [named segments matches](/url-patterns#capturing-named-segments) captured
      by the URL pattern.
  
      See [Closing when a location is reached](/closing-overlays#closing-when-a-location-is-reached).
  
    @param [up-dismiss-location]
      One or more space-separated [URL patterns](/url-patterns) that will cause this overlay to automatically be
      [dismissed](/closing-overlays) when the overlay reaches a matching [location](/up.layer.location).
  
      The [overlay result value](/closing-overlays#overlay-result-values)
      is an object of [named segments matches](/url-patterns#capturing-named-segments) captured
      by the URL pattern.
  
      See [Closing when a location is reached](/closing-overlays#closing-when-a-location-is-reached).
  
    @param [up-context]
      The new overlay's [context](/up.layer.context) object, encoded as JSON.
  
    @param [up-position]
      The position of the popup relative to the `{ origin }` element that opened
      the overlay.
  
      Supported values are `top`,  `right`,  `bottom` and  `left`.
  
      See [popup position](/customizing-overlays#popup-position).
  
    @param [up-align]
      The alignment of the popup within its `{ position }`.
  
      Supported values are `top`,  `right`, `center`, `bottom` and  `left`.
  
      See [popup position](/customizing-overlays#popup-position).
  
    @stable
    */
    /*-
    [Dismisses](/closing-overlays) the [current layer](/up.layer.current) when the link is clicked.
  
    The JSON value of the `[up-accept]` attribute becomes the overlay's
    [dismissal value](/closing-overlays#overlay-result-values).
  
    ### Example
  
    ```html
    <a href='/dashboard' up-dismiss>Close</a>
    ```
  
    ### Fallback for the root layer
  
    The link's `[href]` will only be followed when this link is clicked in the [root layer](/up.layer).
    In an overlay the `click` event's default action is prevented.
  
    You can also omit the `[href]` attribute to make a link that only works in overlays.
  
    @selector a[up-dismiss]
    @param [up-dismiss]
      The overlay's [dismissal value](/closing-overlays#overlay-result-values) as a JSON string.
    @param [up-confirm]
      A message the user needs to confirm before the layer is closed.
    @param [up-animation]
      The overlay's close animation.
  
      Defaults to overlay's [preconfigured close animation](/up.layer.config).
    @param [up-duration]
      The close animation's duration in milliseconds.
    @param [up-easing]
      The close animation's easing function.
    @stable
    */
    /*-
    [Accepts](/closing-overlays) the [current layer](/up.layer.current) when the link is clicked.
  
    The JSON value of the `[up-accept]` attribute becomes the overlay's
    [acceptance value](/closing-overlays#overlay-result-values).
  
    ### Example
  
    ```html
    <a href='/users/5' up-accept='{ "id": 5 }'>Choose user #5</a>
    ```
  
    ### Fallback for the root layer
  
    The link's `[href]` will only be followed when this link is clicked in the [root layer](/up.layer).
    In an overlay the `click` event's default action is prevented.
  
    You can also omit the `[href]` attribute to make a link that only works in overlays.
  
    @selector a[up-accept]
    @param [up-accept]
      The overlay's [acceptance value](/closing-overlays#overlay-result-values) as a JSON string.
    @param [up-confirm]
      A message the user needs to confirm before the layer is closed.
    @param [up-duration]
      The close animation's duration in milliseconds.
    @param [up-easing]
      The close animation's easing function.
    @stable
    */
    up.on('up:fragment:destroyed', function () {
        stack.sync();
    });
    up.on('up:framework:evaled', function () {
        // Due to circular dependencies we must delay initialization of the stack until all of
        // Unpoly's submodules have been evaled. We cannot delay initialization until up:framework:boot,
        // since by then user scripts have run and event listeners will no longer register as "default".
        stack = new up.LayerStack();
    });
    up.on('up:framework:reset', reset);
    const api = {
        config,
        mainTargets,
        open,
        build,
        ask,
        normalizeOptions,
        openCallbackAttr,
        closeCallbackAttr,
        anySelector,
        optionToString,
        get stack() { return stack; }
    };
    /*-
    Returns the current layer in the [layer stack](/up.layer.stack).
  
    The *current* layer is usually the [frontmost layer](/up.layer.front).
    There are however some cases where the current layer is a layer in the background:
  
    - While an element in a background layer is being [compiled](/up.compiler).
    - While an Unpoly event like `up:request:loaded` is being triggered from a background layer.
    - While an event listener bound to a background layer using `up.Layer#on()` is being called.
  
    To temporarily change the current layer from your own code, use `up.Layer#asCurrent()`.
  
    ### Remembering the current layer
  
    Most functions in the `up.layer` package affect the current layer. E.g. `up.layer.dismiss()`
    is shorthand for `up.layer.current.dismiss()`.
  
    As described above `up.layer.current` is set to the right layer in compilers and most events,
    even if that layer is not the frontmost layer.
  
    If you have async code, the current layer may change when your callback is called.
    To address this you may retrieve the current layer for later reference:
  
    ```js
    function dismissCurrentLayerIn(seconds) {
      let savedLayer = up.layer.current // returns an up.Layer object
      let dismiss = () => savedLayer.dismiss()
      setTimeout(dismiss, seconds * 1000)
    }
  
    dismissCurrentLayerIn(10) //
    ```
  
    @property up.layer.current
    @param {up.Layer} current
    @stable
    */
    /*-
    Returns the number of layers in the [layer stack](/up.layer.stack).
  
    The count includes the [root layer](/up.layer.root).
    Hence a page with a single overlay would return a count of 2.
  
    @property up.layer.count
    @param {number} count
      The number of layers in the stack.
    @stable
    */
    /*-
    Returns an `up.Layer` object for the given [layer option](/layer-option).
  
    @function up.layer.get
    @param {string|up.Layer|number} [layer='current']
      The [layer option](/layer-option) to look up.
    @return {up.Layer|undefined}
      The layer matching the given option.
  
      If no layer matches, `undefined` is returned.
    @stable
    */
    /*-
    Returns an array of `up.Layer` objects matching the given [layer option](/layer-option).
  
    @function up.layer.getAll
    @param {string|up.Layer|number} [layer='current']
      The [layer option](/layer-option) to look up.
    @return {Array<up.Layer>}
    @experimental
    */
    /*-
    Returns the [root layer](/layer-terminology).
  
    The root layer represents the initial page before any overlay was [opened](/opening-overlays).
    The root layer always exists and cannot be closed.
  
    @property up.layer.root
    @param {up.Layer} root
    @stable
    */
    /*-
    Returns an array of all [overlays](/layer-terminology).
  
    If no overlay is open, an empty array is returned.
  
    To get an array of *all* layers including the [root layer](/up.layer.root),
    use `up.layer.stack`.
  
    @property up.layer.overlays
    @param {Array<up.Layer>} overlays
    @stable
    */
    /*-
    Returns the frontmost layer in the [layer stack](/up.layer.stack).
  
    The frontmost layer is the layer directly facing the user. If an overlay is
    stacked on top of the frontmost layer, that overlay becomes the new frontmost layer.
  
    In most cases you don't want to refer to the frontmost layer,
    but to the [current layer](/up.layer.current) instead.
  
    @property up.layer.front
    @param {up.Layer} front
    @stable
    */
    /*-
    [Dismisses](/up.layer.dismiss) all overlays.
  
    Afterwards the only remaining layer will be the [root layer](/up.layer.root).
  
    @function up.layer.dismissOverlays
    @param {any} [value]
      The dismissal value.
    @param {Object} [options]
      See options for `up.layer.dismiss()`.
    @stable
    */
    u.delegate(api, [
        'get',
        'getAll',
        'root',
        'overlays',
        'current',
        'front',
        'sync',
        'count',
        'dismissOverlays'
    ], () => stack);
    /*-
    [Accepts](/closing-overlays) the [current layer](/up.layer.current).
  
    This is a shortcut for `up.layer.current.accept()`.
    See `up.Layer#accept()` for more documentation.
  
    @function up.layer.accept
    @param {any} [value]
    @param {Object} [options]
    @stable
    */
    /*-
    This event is emitted before a layer is [accepted](/closing-overlays).
  
    The event is emitted on the [element of the layer](/up.layer.element) that is about to close.
  
    @event up:layer:accept
    @param {up.Layer} event.layer
      The layer that is about to close.
    @param {Element} [event.value]
      The overlay's [acceptance value](/closing-overlays#overlay-result-values).
    @param {Element} [event.origin]
      The element that is causing the layer to close.
    @param event.preventDefault()
      Event listeners may call this method to prevent the overlay from closing.
    @stable
    */
    /*-
    This event is emitted after a layer was [accepted](/closing-overlays).
  
    The event is emitted on the [layer's](/up.layer.element) when the close animation
    is starting. If the layer has no close animaton and was already removed from the DOM,
    the event is emitted a second time on the `document`.
  
    @event up:layer:accepted
    @param {up.Layer} event.layer
      The layer that was closed.
    @param {Element} [event.value]
      The overlay's [acceptance value](/closing-overlays#overlay-result-values).
    @param {Element} [event.origin]
      The element that has caused the layer to close.
    @stable
    */
    /*-
    [Dismisses](/closing-overlays) the [current layer](/up.layer.current).
  
    This is a shortcut for `up.layer.current.dismiss()`.
    See `up.Layer#dismiss()` for more documentation.
  
    @function up.layer.dismiss
    @param {any} [value]
    @param {Object} [options]
    @stable
    */
    /*-
    This event is emitted before a layer is [dismissed](/closing-overlays).
  
    The event is emitted on the [element of the layer](/up.layer.element) that is about to close.
  
    @event up:layer:dismiss
    @param {up.Layer} event.layer
      The layer that is about to close.
    @param {Element} [event.value]
      The overlay's [dismissal value](/closing-overlays#overlay-result-values).
    @param {Element} [event.origin]
      The element that is causing the layer to close.
    @param event.preventDefault()
      Event listeners may call this method to prevent the overlay from closing.
    @stable
    */
    /*-
    This event is emitted after a layer was [dismissed](/closing-overlays).
  
    The event is emitted on the [layer's](/up.layer.element) when the close animation
    is starting. If the layer has no close animaton and was already removed from the DOM,
    the event is emitted a second time on the `document`.
  
    @event up:layer:dismissed
    @param {up.Layer} event.layer
      The layer that was closed.
    @param {Element} [event.value]
      The overlay's [dismissal value](/closing-overlays#overlay-result-values).
    @param {Element} [event.origin]
      The element that has caused the layer to close.
    @stable
    */
    /*-
    Returns whether the [current layer](/up.layer.current) is the [root layer](/up.layer.root).
  
    This is a shortcut for `up.layer.current.isRoot()`.
    See `up.Layer#isRoot()` for more documentation..
  
    @function up.layer.isRoot
    @return {boolean}
    @stable
    */
    /*-
    Returns whether the [current layer](/up.layer.current) is *not* the [root layer](/up.layer.root).
  
    This is a shortcut for `up.layer.current.isOverlay()`.
    See `up.Layer#isOverlay()` for more documentation.
  
    @function up.layer.isOverlay
    @return {boolean}
    @stable
    */
    /*-
    Returns whether the [current layer](/up.layer.current) is the [frontmost layer](/up.layer.front).
  
    This is a shortcut for `up.layer.current.isFront()`.
    See `up.Layer#isFront()` for more documentation.
  
    @function up.layer.isFront
    @return {boolean}
    @stable
    */
    /*-
    Listens to a [DOM event](https://developer.mozilla.org/en-US/docs/Web/API/Document_Object_Model/Events)
    that originated on an element [contained](/up.Layer.prototype.contains) by the [current layer](/up.layer.current).
  
    This is a shortcut for `up.layer.current.on()`.
    See `up.Layer#on()` for more documentation.
  
    @function up.layer.on
    @param {string} types
      A space-separated list of event types to bind to.
    @param {string|Function(): string} [selector]
      The selector of an element on which the event must be triggered.
    @param {Object} [options]
    @param {Function(event, [element], [data])} listener
      The listener function that should be called.
    @return {Function()}
      A function that unbinds the event listeners when called.
    @stable
    */
    /*-
    Unbinds an event listener previously bound to the [current layer](/up.layer.current).
  
    This is a shortcut for `up.layer.current.off()`.
    See `up.Layer#off()` for more documentation.
  
    @function up.layer.off
    @param {string} events
    @param {string|Function(): string} [selector]
    @param {Function(event, [element], [data])} listener
      The listener function to unbind.
    @stable
    */
    /*-
    [Emits](/up.emit) an event on the [current layer](/up.layer.current)'s [element](/up.layer.element).
  
    This is a shortcut for `up.layer.current.emit()`.
    See `up.Layer#emit()` for more documentation.
  
    @function up.layer.emit
    @param {string} eventType
    @param {Object} [props={}]
    @stable
    */
    /*-
    Returns the parent layer of the [current layer](/up.layer.current).
  
    This is a shortcut for `up.layer.current.parent`.
    See `up.Layer#parent` for more documentation.
  
    @property up.layer.parent
    @param {up.Layer} parent
    @stable
    */
    /*-
    Whether fragment updates within the [current layer](/up.layer.current)
    can affect browser history and window title.
  
    This is a shortcut for `up.layer.current.history`.
    See `up.Layer#history` for more documentation.
  
    @property up.layer.history
    @param {boolean} history
    @stable
    */
    /*-
    The location URL of the [current layer](/up.layer.current).
  
    This is a shortcut for `up.layer.current.location`.
    See `up.Layer#location` for more documentation.
  
    @property up.layer.location
    @param {string} location
    @stable
    */
    /*-
    The [current layer](/up.layer.current)'s [mode](/up.layer.mode)
    which governs its appearance and behavior.
  
    @property up.layer.mode
    @param {string} mode
    @stable
    */
    /*-
    The [context](/context) of the [current layer](/up.layer.current).
  
    This is aliased as `up.context`.
  
    @property up.layer.context
    @param {string} context
      The context object.
  
      If no context has been set an empty object is returned.
    @experimental
    */
    /*-
    The outmost element of the [current layer](/up.layer.current).
  
    This is a shortcut for `up.layer.current.element`.
    See `up.Layer#element` for more documentation.
  
    @property up.layer.element
    @param {Element} element
    @stable
    */
    /*-
    The outmost element of the [current layer](/up.layer.current).
  
    This is a shortcut for `up.layer.current.element`.
    See `up.Layer#element` for more documentation.
  
    @property up.layer.element
    @param {Element} element
    @stable
    */
    /*-
    Returns whether the given `element` is contained by the [current layer](/up.layer.current).
  
    This is a shortcut for `up.layer.current.contains(element)`.
    See `up.Layer#contains` for more documentation.
  
    @function up.layer.contains
    @param {Element} element
    @stable
    */
    /*-
    The [size](/customizing-overlays#overlay-sizes) of the [current layer](/up.layer.current).
  
    This is a shortcut for `up.layer.current.size`.
    See `up.Layer#size` for more documentation.
  
    @property up.layer.size
    @param {string} size
    @stable
    */
    /*-
    Creates an element with the given `selector` and appends it to the [current layer's](/up.layer.current)
    [outmost element](/up.Layer.prototype.element).
  
    This is a shortcut for `up.layer.current.affix(selector)`.
    See `up.Layer#affix` for more documentation.
  
    @function up.layer.affix
    @param {Element} element
    @param {string} selector
    @param {Object} attrs
    @experimental
    */
    u.delegate(api, [
        'accept',
        'dismiss',
        'isRoot',
        'isOverlay',
        'isFront',
        'on',
        'off',
        'emit',
        'parent',
        'history',
        'location',
        'mode',
        'context',
        'element',
        'contains',
        'size',
        'affix'
    ], () => stack.current);
    return api;
})();


/***/ }),
/* 86 */
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin


/***/ }),
/* 87 */
/***/ ((__unused_webpack_module, __unused_webpack_exports, __webpack_require__) => {

__webpack_require__(88);
/*-
Linking to fragments
====================

The `up.link` module lets you build links that update fragments instead of entire pages.

### Motivation

In a traditional web application, the entire page is destroyed and re-created when the
user follows a link:

![Traditional page flow](/images/tutorial/fragment_flow_vanilla.svg){:width="620" class="picture has_border is_sepia has_padding"}

This makes for an unfriendly experience:

- State changes caused by AJAX updates get lost during the page transition.
- Unsaved form changes get lost during the page transition.
- The JavaScript VM is reset during the page transition.
- If the page layout is composed from multiple scrollable containers
  (e.g. a pane view), the scroll positions get lost during the page transition.
- The user sees a "flash" as the browser loads and renders the new page,
  even if large portions of the old and new page are the same (navigation, layout, etc.).

Unpoly fixes this by letting you annotate links with an [`[up-target]`](/a-up-follow#up-target)
attribute. The value of this attribute is a CSS selector that indicates which page
fragment to update. The server **still renders full HTML pages**, but we only use
the targeted fragments and discard the rest:

![Unpoly page flow](/images/tutorial/fragment_flow_unpoly.svg){:width="620" class="picture has_border is_sepia has_padding"}

With this model, following links feels smooth. All DOM state outside the updated fragment is preserved.
Pages also load much faster since the DOM, CSS and JavaScript environments do not need to be
destroyed and recreated for every request.


### Example

Let's say we are rendering three pages with a tabbed navigation to switch between screens:

Your HTML could look like this:

```html
<nav>
  <a href="/pages/a">A</a>
  <a href="/pages/b">B</a>
  <a href="/pages/b">C</a>
</nav>

<article>
  Page A
</article>
```

Since we only want to update the `<article>` tag, we annotate the links
with an `up-target` attribute:

```html
<nav>
  <a href="/pages/a" up-target="article">A</a>
  <a href="/pages/b" up-target="article">B</a>
  <a href="/pages/b" up-target="article">C</a>
</nav>
```

Note that instead of `article` you can use any other CSS selector like `#main .article`.

With these [`[up-target]`](/a-up-follow#up-target) annotations Unpoly only updates the targeted part of the screen.
The JavaScript environment will persist and the user will not see a white flash while the
new page is loading.

@see fragment-placement
@see handling-everything
@see server-errors

@see a[up-follow]
@see a[up-instant]
@see a[up-preload]
@see up.follow

@module up.link
*/
up.link = (function () {
    const u = up.util;
    const e = up.element;
    const linkPreloader = new up.LinkPreloader();
    let lastMousedownTarget = null;
    // Links with attribute-provided HTML are always followable.
    const LINKS_WITH_LOCAL_HTML = ['a[up-content]', 'a[up-fragment]', 'a[up-document]'];
    // Links with remote HTML are followable if there is one additional attribute
    // suggesting "follow me through Unpoly".
    const LINKS_WITH_REMOTE_HTML = ['a[href]', '[up-href]'];
    const ATTRIBUTES_SUGGESTING_FOLLOW = ['[up-follow]', '[up-target]', '[up-layer]', '[up-transition]', '[up-preload]', '[up-instant]'];
    function combineFollowableSelectors(elementSelectors, attributeSelectors) {
        return u.flatMap(elementSelectors, elementSelector => attributeSelectors.map(attributeSelector => elementSelector + attributeSelector));
    }
    /*-
    Configures defaults for link handling.
  
    In particular you can configure Unpoly to handle [all links on the page](/handling-everything)
    without requiring developers to set `[up-...]` attributes.
  
    @property up.link.config
  
    @param {Array<string>} config.followSelectors
      An array of CSS selectors matching links that will be [followed through Unpoly](/a-up-follow).
  
      You can customize this property to automatically follow *all* links on a page without requiring an `[up-follow]` attribute.
      See [Handling all links and forms](/handling-everything).
  
    @param {Array<string>} config.noFollowSelectors
      Exceptions to `config.followSelectors`.
  
      Matching links will *not* be [followed through Unpoly](/a-up-follow), even if they match `config.followSelectors`.
  
      By default Unpoly excludes:
  
      - Links with an `[up-follow=false]` attribute.
      - Links with a cross-origin `[href]`.
      - Links with a `[target]` attribute (to target an iframe or open new browser tab).
      - Links with a `[rel=download]` attribute.
      - Links with an `[href]` attribute starting with `javascript:`.
      - Links with an `[href="#"]` attribute that don't also have local HTML
        in an `[up-document]`, `[up-fragment]` or `[up-content]` attribute.
  
    @param {Array<string>} config.instantSelectors
      An array of CSS selectors matching links that are [followed on `mousedown`](/a-up-instant)
      instead of on `click`.
  
      You can customize this property to follow *all* links on `mousedown` without requiring an `[up-instant]` attribute.
      See [Handling all links and forms](/handling-everything).
  
    @param {Array<string>} config.noInstantSelectors
      Exceptions to `config.followSelectors`.
  
      Matching links will *not* be [followed through Unpoly](/a-up-follow), even if they match `config.followSelectors`.
  
      By default Unpoly excludes:
  
      - Links with an `[up-instant=false]` attribute.
      - Links that are [not followable](#config.noFollowSelectors).
  
    @param {Array<string>} config.preloadSelectors
      An array of CSS selectors matching links that are [preloaded on hover](/a-up-preload).
  
      You can customize this property to preload *all* links on `mousedown` without requiring an `[up-preload]` attribute.
      See [Handling all links and forms](/handling-everything).
  
    @param {Array<string>} config.noPreloadSelectors
      Exceptions to `config.preloadSelectors`.
  
      Matching links will *not* be [preloaded on hover](/a-up-preload), even if they match `config.preloadSelectors`.
  
      By default Unpoly excludes:
  
      - Links with an `[up-preload=false]` attribute.
      - Links that are [not followable](#config.noFollowSelectors).
      - When the link destination [cannot be cached](/up.network.config#config.autoCache).
  
    @param {number} [config.preloadDelay=75]
      The number of milliseconds to wait before [`[up-preload]`](/a-up-preload)
      starts preloading.
  
    @param {boolean|string} [config.preloadEnabled='auto']
      Whether Unpoly will load [preload requests](/a-up-preload).
  
      With the default setting (`"auto"`) Unpoly will load preload requests
      unless `up.network.shouldReduceRequests()` detects a poor connection.
  
      If set to `true`, Unpoly will always load preload links.
  
      If set to `false`, Unpoly will never preload links.
  
    @param {Array<string>} [config.clickableSelectors]
      A list of CSS selectors matching elements that should behave like links or buttons.
  
      @see [up-clickable]
    @stable
    */
    const config = new up.Config(() => ({
        followSelectors: combineFollowableSelectors(LINKS_WITH_REMOTE_HTML, ATTRIBUTES_SUGGESTING_FOLLOW).concat(LINKS_WITH_LOCAL_HTML),
        // (1) We don't want to follow <a href="#anchor"> links without a path. Instead
        //     we will let the browser change the current location's anchor and up.reveal()
        //     on hashchange to scroll past obstructions.
        // (2) We want to follow links with [href=#] only if they have a local source of HTML
        //     through [up-content], [up-fragment] or [up-document].
        //     Many web developers are used to give JavaScript-handled links an [href="#"]
        //     attribute. Also frameworks like Bootstrap only style links if they have an [href].
        // (3) We don't want to handle <a href="javascript:foo()"> links.
        noFollowSelectors: ['[up-follow=false]', 'a[download]', 'a[target]', 'a[href^="#"]:not([up-content]):not([up-fragment]):not([up-document])', 'a[href^="javascript:"]'],
        instantSelectors: ['[up-instant]'],
        noInstantSelectors: ['[up-instant=false]', '[onclick]'],
        preloadSelectors: combineFollowableSelectors(LINKS_WITH_REMOTE_HTML, ['[up-preload]']),
        noPreloadSelectors: ['[up-preload=false]'],
        clickableSelectors: LINKS_WITH_LOCAL_HTML.concat(['[up-emit]', '[up-accept]', '[up-dismiss]', '[up-clickable]']),
        preloadDelay: 90,
        // true | false | 'auto'
        preloadEnabled: 'auto'
    }));
    function fullFollowSelector() {
        return config.followSelectors.join(',');
    }
    function fullPreloadSelector() {
        return config.preloadSelectors.join(',');
    }
    function fullInstantSelector() {
        return config.instantSelectors.join(',');
    }
    function fullClickableSelector() {
        return config.clickableSelectors.join(',');
    }
    /*-
    Returns whether the link was explicitly marked up as not followable,
    e.g. through `[up-follow=false]`.
  
    This differs from `config.followSelectors` in that we want users to configure
    simple selectors, but let users make exceptions. We also have a few built-in
    exceptions of our own, e.g. to never follow an `<a href="javascript:...">` link.
  
    @function isFollowDisabled
    @param {Element} link
    @return {boolean}
    */
    function isFollowDisabled(link) {
        return e.matches(link, config.noFollowSelectors.join(',')) || u.isCrossOrigin(link);
    }
    function isPreloadDisabled(link) {
        return !up.browser.canPushState() ||
            e.matches(link, config.noPreloadSelectors.join(',')) ||
            isFollowDisabled(link) ||
            !willCache(link);
    }
    function willCache(link) {
        // Instantiate a lightweight request with basic link attributes needed for the cache-check.
        const options = parseRequestOptions(link);
        if (options.url) {
            if (options.cache == null) {
                options.cache = 'auto';
            }
            options.basic = true;
            const request = new up.Request(options);
            return request.willCache();
        }
    }
    function isInstantDisabled(link) {
        return e.matches(link, config.noInstantSelectors.join(',')) || isFollowDisabled(link);
    }
    function reset() {
        lastMousedownTarget = null;
        config.reset();
        linkPreloader.reset();
    }
    /*-
    Follows the given link with JavaScript and updates a fragment with the server response.
  
    By default the layer's [main element](/up-main)
    will be replaced. Attributes like `a[up-target]`
    or `a[up-layer]` will be honored.
  
    Following a link is considered [navigation](/navigation) by default.
  
    Emits the event `up:link:follow`.
  
    ### Examples
  
    Assume we have a link with an `a[up-target]` attribute:
  
    ```html
    <a href="/users" up-target=".main">Users</a>
    ```
  
    Calling `up.follow()` with this link will replace the page's `.main` fragment
    as if the user had clicked on the link:
  
    ```js
    var link = document.querySelector('a')
    up.follow(link)
    ```
  
    @function up.follow
  
    @param {Element|jQuery|string} link
      The link to follow.
  
    @param {Object} [options]
      [Render options](/up.render) that should be used for following the link.
  
      Unpoly will parse render options from the given link's attributes
      like `[up-target]` or `[up-transition]`. See `a[up-follow]` for a list
      of supported attributes.
  
      You may pass this additional `options` object to supplement or override
      options parsed from the link attributes.
  
    @param {boolean} [options.navigate=true]
      Whether this fragment update is considered [navigation](/navigation).
  
      Setting this to `false` will disable most defaults, causing
      Unpoly to render a fragment without side-effects like updating history
      or scrolling.
  
    @return {Promise<up.RenderResult>}
      A promise that will be fulfilled when the link destination
      has been loaded and rendered.
  
    @stable
    */
    const follow = up.mockable(function (link, options) {
        return up.render(followOptions(link, options));
    });
    function parseRequestOptions(link, options) {
        options = u.options(options);
        const parser = new up.OptionsParser(options, link);
        options.url = followURL(link, options);
        options.method = followMethod(link, options);
        parser.json('headers');
        parser.json('params');
        parser.booleanOrString('cache');
        parser.booleanOrString('clearCache');
        parser.booleanOrString('solo');
        parser.string('contentType', { attr: ['enctype', 'up-content-type'] });
        return options;
    }
    /*-
    Parses the [render](/up.render) options that would be used to
    [follow](/up.follow) the given link, but does not render.
  
    ### Example
  
    Given a link with some `[up-...]` attributes:
  
    ```html
    <a href="/foo" up-target=".content" up-layer="new">...</a>
    ```
  
    We can parse the link's render options like this:
  
    ```js
    let link = document.querySelector('a[href="/foo"]')
    let options = up.link.followOptions(link)
    // => { url: '/foo', method: 'GET', target: '.content', layer: 'new', ... }
    ```
  
    @function up.link.followOptions
    @param {Element|jQuery|string} link
      The link to follow.
    @return {Object}
    @stable
    */
    function followOptions(link, options) {
        // If passed a selector, up.fragment.get() will prefer a match on the current layer.
        link = up.fragment.get(link);
        // Request options
        options = parseRequestOptions(link, options);
        const parser = new up.OptionsParser(options, link, { fail: true });
        // Feedback options
        parser.boolean('feedback');
        // Fragment options
        parser.boolean('fail');
        if (parser.options.origin == null) {
            parser.options.origin = link;
        }
        parser.boolean('navigate', { default: true });
        parser.string('confirm');
        parser.string('target');
        parser.booleanOrString('fallback');
        parser.parse(((link, attrName) => e.callbackAttr(link, attrName, ['request', 'response', 'renderOptions'])), 'onLoaded'); // same
        parser.string('content');
        parser.string('fragment');
        parser.string('document');
        // Layer options
        parser.boolean('peel');
        parser.string('layer');
        parser.string('baseLayer');
        parser.json('context');
        parser.string('mode');
        parser.string('align');
        parser.string('position');
        parser.string('class');
        parser.string('size');
        parser.booleanOrString('dismissable');
        parser.parse(up.layer.openCallbackAttr, 'onOpened');
        parser.parse(up.layer.closeCallbackAttr, 'onAccepted');
        parser.parse(up.layer.closeCallbackAttr, 'onDismissed');
        parser.string('acceptEvent');
        parser.string('dismissEvent');
        parser.string('acceptLocation');
        parser.string('dismissLocation');
        parser.booleanOrString('history');
        // Viewport options
        parser.booleanOrString('focus');
        parser.boolean('saveScroll');
        parser.booleanOrString('scroll');
        parser.boolean('revealTop');
        parser.number('revealMax');
        parser.number('revealPadding');
        parser.number('revealSnap');
        parser.string('scrollBehavior');
        // History options
        // { history } is actually a boolean, but we keep the deprecated string
        // variant which should now be passed as { location }.
        parser.booleanOrString('history');
        parser.booleanOrString('location');
        parser.booleanOrString('title');
        // Motion options
        parser.booleanOrString('animation');
        parser.booleanOrString('transition');
        parser.string('easing');
        parser.number('duration');
        up.migrate.parseFollowOptions?.(parser);
        // This is the event that may be prevented to stop the follow.
        // up.form.submit() changes this to be up:form:submit instead.
        // The guardEvent will also be assigned a { renderOptions } property in up.render()
        if (!options.guardEvent) {
            options.guardEvent = up.event.build('up:link:follow', { log: 'Following link' });
        }
        return options;
    }
    /*-
    This event is [emitted](/up.emit) when a link is [followed](/up.follow) through Unpoly.
  
    The event is emitted on the `<a>` element that is being followed.
  
    ### Changing render options
  
    Listeners may inspect and manipulate [render options](/up.render) for the coming fragment update.
  
    The code below will open all form-contained links in an overlay, as to not
    lose the user's form data:
  
    ```js
    up.on('up:link:follow', function(event, link) {
      if (link.closest('form')) {
        event.renderOptions.layer = 'new'
      }
    })
    ```
  
    @event up:link:follow
    @param {Element} event.target
      The link element that will be followed.
    @param {Object} event.renderOptions
      An object with [render options](/up.render) for the coming fragment update.
  
      Listeners may inspect and modify these options.
    @param event.preventDefault()
      Event listeners may call this method to prevent the link from being followed.
    @stable
    */
    /*-
    Preloads the given link.
  
    When the link is clicked later, the response will already be [cached](/up.request#caching),
    making the interaction feel instant.
  
    @function up.link.preload
    @param {string|Element|jQuery} link
      The element or selector whose destination should be preloaded.
    @param {Object} options
      See options for `up.follow()`.
    @return {Promise}
      A promise that will be fulfilled when the request was loaded and cached.
  
      When preloading is [disabled](/up.link.config#config.preloadEnabled) the promise
      rejects with an `AbortError`.
    @stable
    */
    function preload(link, options) {
        // If passed a selector, up.fragment.get() will match in the current layer.
        link = up.fragment.get(link);
        if (!shouldPreload()) {
            return up.error.failed.async('Link preloading is disabled');
        }
        const guardEvent = up.event.build('up:link:preload', { log: ['Preloading link %o', link] });
        return follow(link, { ...options, guardEvent, preload: true });
    }
    function shouldPreload() {
        const setting = config.preloadEnabled;
        if (setting === 'auto') {
            // Since connection.effectiveType might change during a session we need to
            // re-evaluate the value every time.
            return !up.network.shouldReduceRequests();
        }
        return setting;
    }
    /*-
    This event is [emitted](/up.emit) before a link is [preloaded](/a-up-preload).
  
    @event up:link:preload
    @param {Element} event.target
      The link element that will be preloaded.
    @param event.preventDefault()
      Event listeners may call this method to prevent the link from being preloaded.
    @stable
    */
    /*-
    Returns the HTTP method that should be used when following the given link.
  
    Looks at the link's `up-method` or `data-method` attribute.
    Defaults to `"get"`.
  
    @function up.link.followMethod
    @param link
    @param options.method {string}
    @internal
    */
    function followMethod(link, options = {}) {
        return u.normalizeMethod(options.method || link.getAttribute('up-method') || link.getAttribute('data-method'));
    }
    function followURL(link, options = {}) {
        const url = options.url || link.getAttribute('up-href') || link.getAttribute('href');
        // Developers sometimes make a <a href="#"> to give a JavaScript interaction standard
        // link behavior (like keyboard navigation or default styles). However, we don't want to
        // consider this  a link with remote content, and rather honor [up-content], [up-document]
        // and [up-fragment] attributes.
        if (url !== '#') {
            return url;
        }
    }
    /*-
    Returns whether the given link will be [followed](/up.follow) by Unpoly
    instead of making a full page load.
  
    By default Unpoly will follow links if the element has
    one of the following attributes:
  
    - `[up-follow]`
    - `[up-target]`
    - `[up-layer]`
    - `[up-mode]`
    - `[up-transition]`
    - `[up-content]`
    - `[up-fragment]`
    - `[up-document]`
  
    To make additional elements followable, see `up.link.config.followSelectors`.
  
    @function up.link.isFollowable
    @param {Element|jQuery|string} link
      The link to check.
    @stable
    */
    function isFollowable(link) {
        link = up.fragment.get(link);
        return e.matches(link, fullFollowSelector()) && !isFollowDisabled(link);
    }
    /*-
    Makes sure that the given link will be [followed](/up.follow)
    by Unpoly instead of making a full page load.
  
    If the link is not already [followable](/up.link.isFollowable), the link
    will receive an `a[up-follow]` attribute.
  
    @function up.link.makeFollowable
    @param {Element|jQuery|string} link
      The element or selector for the link to make followable.
    @experimental
    */
    function makeFollowable(link) {
        if (!isFollowable(link)) {
            link.setAttribute('up-follow', '');
        }
    }
    function makeClickable(link) {
        if (e.matches(link, 'a[href], button')) {
            return;
        }
        e.setMissingAttrs(link, {
            tabindex: '0',
            role: 'link',
            'up-clickable': '' // Get pointer pointer from link.css
        });
        link.addEventListener('keydown', function (event) {
            if ((event.key === 'Enter') || (event.key === 'Space')) {
                return forkEventAsUpClick(event);
            }
        });
    }
    /*-
    Enables keyboard interaction for elements that should behave like links or buttons.
  
    The element will be focusable and screen readers will announce it as a link.
  
    Also see [`up.link.config.clickableSelectors`](/up.link.config#config.clickableSelectors).
  
    @selector [up-clickable]
    @experimental
    */
    up.macro(fullClickableSelector, makeClickable);
    function shouldFollowEvent(event, link) {
        // Users may configure up.link.config.followSelectors.push('a')
        // and then opt out individual links with [up-follow=false].
        if (event.defaultPrevented || isFollowDisabled(link)) {
            return false;
        }
        // If user clicked on a child link of $link, or in an <input> within an [up-expand][up-href]
        // we want those other elements handle the click.
        const betterTargetSelector = `a, [up-href], ${up.form.fieldSelector()}`;
        const betterTarget = e.closest(event.target, betterTargetSelector);
        return !betterTarget || (betterTarget === link);
    }
    function isInstant(linkOrDescendant) {
        const element = e.closest(linkOrDescendant, fullInstantSelector());
        // Allow users to configure up.link.config.instantSelectors.push('a')
        // but opt out individual links with [up-instant=false].
        return element && !isInstantDisabled(element);
    }
    /*-
    Provide an `up:click` event that improves on standard click
    in several ways:
  
    - It is emitted on mousedown for [up-instant] elements
    - It is not emitted if the element has disappeared (or was overshadowed)
      between mousedown and click. This can happen if mousedown creates a layer
      over the element, or if a mousedown handler removes a handler.
  
    Stopping an up:click event will also stop the underlying event.
  
    Also see docs for `up:click`.
  
    @function up.link.convertClicks
    @param {up.Layer} layer
    @internal
    */
    function convertClicks(layer) {
        layer.on('click', function (event, element) {
            // We never handle events for the right mouse button,
            // or when Shift/CTRL/Meta/ALT is pressed
            if (!up.event.isUnmodified(event)) {
                return;
            }
            // (1) Instant links should not have a `click` event.
            //     This would trigger the browsers default follow-behavior and possibly activate JS libs.
            // (2) A11Y: We also need to check whether the [up-instant] behavior did trigger on mousedown.
            //     Keyboard navigation will not necessarily trigger a mousedown event.
            if (isInstant(element) && lastMousedownTarget) {
                up.event.halt(event);
                // In case mousedown has created a layer over the click coordinates,
                // Chrome will emit an event with { target: document.body } on click.
                // Ignore that event and only process if we would still hit the
                // expect layers at the click coordinates.
            }
            else if (layer.wasHitByMouseEvent(event) && !didUserDragAway(event)) {
                forkEventAsUpClick(event);
            }
            // In case the user switches input modes.
            return lastMousedownTarget = null;
        });
        layer.on('mousedown', function (event, element) {
            // We never handle events for the right mouse button,
            // or when Shift/CTRL/Meta/ALT is pressed
            if (!up.event.isUnmodified(event)) {
                return;
            }
            lastMousedownTarget = event.target;
            if (isInstant(element)) {
                // A11Y: Keyboard navigation will not necessarily trigger a mousedown event.
                // We also don't want to listen to the enter key, since some screen readers
                // use the enter key for something else.
                forkEventAsUpClick(event);
            }
        });
    }
    function didUserDragAway(clickEvent) {
        return lastMousedownTarget && (lastMousedownTarget !== clickEvent.target);
    }
    function forkEventAsUpClick(originalEvent) {
        let forwardedProps = ['clientX', 'clientY', 'button', ...up.event.keyModifiers];
        const newEvent = up.event.fork(originalEvent, 'up:click', forwardedProps);
        up.emit(originalEvent.target, newEvent, { log: false });
    }
    /*-
    A `click` event that honors the [`[up-instant]`](/a-up-instant) attribute.
  
    This event is generally emitted when an element is clicked. However, for elements
    with an [`[up-instant]`](/a-up-instant) attribute this event is emitted on `mousedown` instead.
  
    This is useful to listen to links being activated, without needing to know whether
    a link is `[up-instant]`.
  
    ### Example
  
    Assume we have two links, one of which is `[up-instant]`:
  
    ```html
    <a href="/one">Link 1</a>
    <a href="/two" up-instant>Link 2</a>
    ```
  
    The following event listener will be called when *either* link is activated:
  
    ```js
    document.addEventListener('up:click', function(event) {
      ...
    })
    ```
  
    ### Cancelation
  
    You may cancel an `up:click` event using `event.preventDefault()`.
  
    Canceling `up:click` on a hyperlink will prevent any Unpoly from [following](/a-up-follow) that link.
  
    The underlying `click` or `mousedown` event will also be canceled.
  
    ### Accessibility
  
    If the user activates an element using their keyboard, the `up:click` event will be emitted
    when the key is pressed even if the element has an `[up-instant]` attribute.
  
    ### Only unmodified clicks are considered
  
    To prevent overriding native browser behavior, the `up:click` is only emitted for unmodified clicks.
  
    In particular, it is not emitted when the user holds `Shift`, `CTRL` or `Meta` while clicking.
    Neither it is emitted when the user clicks with a secondary mouse button.
  
    @event up:click
    @param {Element} event.target
      The clicked element.
    @param {Event} event.originalEvent
      The underlying `click` or `mousedown` event.
    @param event.preventDefault()
      Prevents this event and also the original `click` or `mousedown` event.
    @stable
    */
    /*-
    Returns whether the given link has a [safe](https://www.w3.org/Protocols/rfc2616/rfc2616-sec9.html#sec9.1.1)
    HTTP method like `GET`.
  
    @function up.link.isSafe
    @param {Element} link
    @return {boolean}
    @stable
    */
    function isSafe(link) {
        const method = followMethod(link);
        return up.network.isSafeMethod(method);
    }
    /*-
    [Follows](/up.follow) this link with JavaScript and updates a fragment with the server response.
  
    Following a link is considered [navigation](/navigation) by default.
  
    ### Example
  
    This will update the fragment `<div class="content">` with the same element
    fetched from `/posts/5`:
  
    ```html
    <a href="/posts/5" up-follow up-target=".content">Read post</a>
    ```
  
    If no `[up-target]` attribute is set, the [main target](/up-main) is updated.
  
    ### Advanced fragment changes
  
    See [fragment placement](/fragment-placement) for advanced use cases
    like updating multiple fragments or appending content to an existing element.
  
    ### Short notation
  
    You may omit the `[up-follow]` attribute if the link has one of the following attributes:
  
    - `[up-target]`
    - `[up-layer]`
    - `[up-transition]`
    - `[up-content]`
    - `[up-fragment]`
    - `[up-document]`
  
    Such a link will still be followed through Unpoly.
  
    ### Following all links automatically
  
    You can configure Unpoly to follow *all* links on a page without requiring an `[up-follow]` attribute.
  
    See [Handling all links and forms](/handling-everything).
  
    ### Preventing Unpoly from following links
  
    To prevent Unpoly from following an `a[up-follow]` link, use one of the following options:
  
    - Prevent the `up:link:follow` event on the link element
    - Prevent the `up:click` event on the link element
    - Set an `[up-follow=false]` attribute on the link element
  
    @selector a[up-follow]
  
    @param [up-navigate='true']
      Whether this fragment update is considered [navigation](/navigation).
  
      Setting this to `false` will disable most defaults documented below,
      causing Unpoly to render a fragment without side-effects like updating history
      or scrolling.
  
    @param [href]
      The URL to fetch from the server.
  
      Instead of making a server request, you may also pass an existing HTML string as
      `[up-document]` or `[up-content]` attribute.
  
    @param [up-target]
      The CSS selector to update.
  
      If omitted a [main target](/up-main) will be rendered.
  
    @param [up-fallback='true']
      Specifies behavior if the [target selector](/up.render#options.target) is missing from the current page or the server response.
  
      If set to a CSS selector, Unpoly will attempt to replace that selector instead.
  
      If set to `true` Unpoly will attempt to replace a [main target](/up-main) instead.
  
      If set to `false` Unpoly will immediately reject the render promise.
  
    @param [up-method='get']
      The HTTP method to use for the request.
  
      Common values are `get`, `post`, `put`, `patch` and `delete`. The value is case insensitive.
  
      The HTTP method may also be passed as an `[data-method]` attribute.
  
      By default, methods other than `get` or `post` will be converted into a `post` request, and carry
      their original method as a configurable [`_method` parameter](/up.protocol.config#config.methodParam).
  
    @param [up-params]
      A JSON object with additional [parameters](/up.Params) that should be sent as the request's
      [query string](https://en.wikipedia.org/wiki/Query_string) or payload.
  
      When making a `GET` request to a URL with a query string, the given `{ params }` will be added
      to the query parameters.
  
    @param [up-headers]
      A JSON object with additional request headers.
  
      Note that Unpoly will by default send a number of custom request headers.
      E.g. the `X-Up-Target` header includes the targeted CSS selector.
      See `up.protocol` and `up.network.config.requestMetaKeys` for details.
  
    @param [up-content]
      A string for the fragment's new [inner HTML](https://developer.mozilla.org/en-US/docs/Web/API/Element/innerHTML).
  
      If your HTML string also contains the fragment's [outer HTML](https://developer.mozilla.org/en-US/docs/Web/API/Element/outerHTML),
      consider the `[up-fragment]` attribute instead.
  
    @param [up-fragment]
      A string of HTML comprising *only* the new fragment's
      [outer HTML](https://developer.mozilla.org/en-US/docs/Web/API/Element/outerHTML).
  
      The `[up-target]` selector will be derived from the root element in the given
      HTML:
  
      ```html
      <!-- This will update .foo -->
      <a up-fragment='&lt;div class=".foo"&gt;inner&lt;/div&gt;'>Click me</a>
      ```
  
      If your HTML string contains other fragments that will not be rendered, use
      the `[up-document]` attribute instead.
  
      If your HTML string comprises only the new fragment's [inner HTML](https://developer.mozilla.org/en-US/docs/Web/API/Element/innerHTML),
      consider the `[up-content]` attribute instead.
  
    @param [up-document]
      A string of HTML containing the new fragment.
  
      The string may contain other HTML, but only the element matching the
      `[up-target]` selector will be extracted and placed into the page.
      Other elements will be discarded.
  
      If your HTML string comprises only the new fragment, consider the `[up-fragment]` attribute
      instead. With `[up-fragment]` you don't need to pass a `[up-target]`, since
      Unpoly can derive it from the root element in the given HTML.
  
      If your HTML string comprises only the new fragment's [inner HTML](https://developer.mozilla.org/en-US/docs/Web/API/Element/innerHTML),
      consider the `[up-content]` attribute.
  
    @param [up-fail='auto']
      How to render a server response with an error code.
  
      Any HTTP status code other than 2xx is considered an error code.
  
      See [handling server errors](/server-errors) for details.
  
    @param [up-history='auto']
      Whether the browser URL and window title will be updated.
  
      If set to `true`, the history will always be updated, using the title and URL from
      the server response, or from given `[up-title]` and `[up-location]` attributes.
  
      If set to `auto` history will be updated if the `[up-target]` matches
      a selector in `up.fragment.config.autoHistoryTargets`. By default this contains all
      [main targets](/up-main).
  
      If set to `false`, the history will remain unchanged.
  
    @param [up-title]
      An explicit document title to use after rendering.
  
      By default the title is extracted from the response's `<title>` tag.
      You may also set `[up-title=false]` to explicitly prevent the title from being updated.
  
      Note that the browser's window title will only be updated it you also
      set an `[up-history]` attribute.
  
    @param [up-location]
      An explicit URL to use after rendering.
  
      By default Unpoly will use the link's `[href]` or the final URL after the server redirected.
      You may also set `[up-location=false]` to explicitly prevent the URL from being updated.
  
      Note that the browser's URL will only be updated it you also
      set an `[up-history]` attribute.
  
    @param [up-transition]
      The name of an [transition](/up.motion) to morph between the old and few fragment.
  
      If you are [prepending or appending content](/fragment-placement#appending-or-prepending-content),
      use the `[up-animation]` attribute instead.
  
    @param [up-animation]
      The name of an [animation](/up.motion) to reveal a new fragment when
      [prepending or appending content](/fragment-placement#appending-or-prepending-content).
  
      If you are replacing content (the default), use the `[up-transition]` attribute instead.
  
    @param [up-duration]
      The duration of the transition or animation (in millisconds).
  
    @param [up-easing]
      The timing function that accelerates the transition or animation.
  
      See [MDN documentation](https://developer.mozilla.org/en-US/docs/Web/CSS/transition-timing-function)
      for a list of available timing functions.
  
    @param [up-cache='auto']
      Whether to read from and write to the [cache](/up.request#caching).
  
      With `[up-cache=true]` Unpoly will try to re-use a cached response before connecting
      to the network. If no cached response exists, Unpoly will make a request and cache
      the server response.
  
      With `[up-cache=auto]` Unpoly will use the cache only if `up.network.config.autoCache`
      returns `true` for the request.
  
      With `[up-cache=false]` Unpoly will always make a network request.
  
      Also see [`up.request({ cache })`](/up.request#options.cache).
  
    @param [up-clear-cache]
      Whether existing [cache](/up.request#caching) entries will be cleared with this request.
  
      By default a non-GET request will clear the entire cache.
      You may also pass a [URL pattern](/url-patterns) to only clear matching requests.
  
      Also see [`up.request({ clearCache })`](/up.request#options.clearCache) and `up.network.config.clearCache`.
  
    @param [up-solo='true']
      With `[up-solo=true]` Unpoly will [abort](/up.network.abort) all other requests before laoding the new fragment.
  
      To only abort some requests, pass an [URL pattern](/url-patterns) that matches requests to abort.
  
    @param [up-layer='origin current']
      The [layer](/up.layer) in which to match and render the fragment.
  
      See [layer option](/layer-option) for a list of allowed values.
  
      To [open the fragment in a new overlay](/opening-overlays), pass `[up-layer=new]`.
      In this case attributes for `a[up-layer=new]` may also be used.
  
    @param [up-peel='true']
      Whether to close overlays obstructing the updated layer when the fragment is updated.
  
      This is only relevant when updating a layer that is not the [frontmost layer](/up.layer.front).
  
    @param [up-context]
      A JSON object that will be merged into the [context](/context)
      of the current layer once the fragment is rendered.
  
    @param [up-scroll='auto']
      How to scroll after the new fragment was rendered.
  
      See [scroll option](/scroll-option) for a list of allowed values.
  
    @param [up-save-scroll]
      Whether to save scroll positions before updating the fragment.
  
      Saved scroll positions can later be restored with [`[up-scroll=restore]`](/scroll-option#restoring-scroll-positions).
  
    @param [up-focus='auto']
      What to focus after the new fragment was rendered.
  
      See [focus option](/focus-option) for a list of allowed values.
  
    @param [up-confirm]
      A message the user needs to confirm before fragments are updated.
  
      The message will be shown as a [native browser prompt](https://developer.mozilla.org/en-US/docs/Web/API/Window/prompt).
  
      If the user does not confirm the render promise will reject and no fragments will be updated.
  
    @param [up-feedback='true']
      Whether to give the link an `.up-active` class
      while loading and rendering content.
  
    @param [up-on-loaded]
      A JavaScript snippet that is called when when the server responds with new HTML,
      but before the HTML is rendered.
  
      The callback argument is a preventable `up:fragment:loaded` event.
  
      With a strict Content Security Policy [additional rules apply](/csp).
  
    @param [up-on-finished]
      A JavaScript snippet that is called when all animations have concluded and
      elements were removed from the DOM tree.
  
      With a strict Content Security Policy [additional rules apply](/csp).
  
    @stable
    */
    up.on('up:click', fullFollowSelector, function (event, link) {
        if (shouldFollowEvent(event, link)) {
            up.event.halt(event);
            up.log.muteUncriticalRejection(follow(link));
        }
    });
    /*-
    Follows this link on `mousedown` instead of `click`.
  
    This will save precious milliseconds that otherwise spent
    on waiting for the user to release the mouse button. Since an
    AJAX request will be triggered right way, the interaction will
    appear faster.
  
    Note that using `[up-instant]` will prevent a user from canceling a
    click by moving the mouse away from the link. However, for
    navigation actions this isn't needed. E.g. popular operation
    systems switch tabs on `mousedown` instead of `click`.
  
    ### Example
  
        <a href="/users" up-follow up-instant>User list</a>
  
    ### Accessibility
  
    If the user activates an element using their keyboard, the `up:click` event will be emitted
    on `click`, even if the element has an `[up-instant]` attribute.
  
    @selector a[up-instant]
    @stable
    */
    /*-
    Add an `[up-expand]` attribute to any element to enlarge the click area of a
    descendant link.
  
    `[up-expand]` honors all the Unppoly attributes in expanded links, like
    `a[up-target]`, `a[up-instant]` or `a[up-preload]`.
  
    ### Example
  
        <div class="notification" up-expand>
          Record was saved!
          <a href="/records">Close</a>
        </div>
  
    In the example above, clicking anywhere within `.notification` element
    would [follow](/up.follow) the *Close* link.
  
    ### Elements with multiple contained links
  
    If a container contains more than one link, you can set the value of the
    `up-expand` attribute to a CSS selector to define which link should be expanded:
  
        <div class="notification" up-expand=".close">
          Record was saved!
          <a class="details" href="/records/5">Details</a>
          <a class="close" href="/records">Close</a>
        </div>
  
    ### Limitations
  
    `[up-expand]` has some limitations for advanced browser users:
  
    - Users won't be able to right-click the expanded area to open a context menu
    - Users won't be able to `CTRL`+click the expanded area to open a new tab
  
    To overcome these limitations, consider nesting the entire clickable area in an actual `<a>` tag.
    [It's OK to put block elements inside an anchor tag](https://makandracards.com/makandra/43549-it-s-ok-to-put-block-elements-inside-an-a-tag).
  
    @selector [up-expand]
    @param [up-expand]
      A CSS selector that defines which containing link should be expanded.
  
      If omitted, the first link in this element will be expanded.
    @stable
    */
    up.macro('[up-expand]', function (area) {
        const selector = area.getAttribute('up-expand') || 'a, [up-href]';
        let childLink = e.get(area, selector);
        if (childLink) {
            const areaAttrs = e.upAttrs(childLink);
            if (!areaAttrs['up-href']) {
                areaAttrs['up-href'] = childLink.getAttribute('href');
            }
            e.setMissingAttrs(area, areaAttrs);
            makeFollowable(area);
            // We could also consider making the area clickable, via makeClickable().
            // However, since the original link is already present within the area,
            // we would not add accessibility benefits. We might also confuse screen readers
            // with a nested link.
        }
    });
    /*-
    Preloads this link when the user hovers over it.
  
    When the link is clicked later the response will already be cached,
    making the interaction feel instant.
  
    @selector a[up-preload]
    @param [up-delay]
      The number of milliseconds to wait between hovering
      and preloading. Increasing this will lower the load in your server,
      but will also make the interaction feel less instant.
  
      Defaults to `up.link.config.preloadDelay`.
    @stable
    */
    up.compiler(fullPreloadSelector, function (link) {
        if (!isPreloadDisabled(link)) {
            linkPreloader.observeLink(link);
        }
    });
    up.on('up:framework:reset', reset);
    return {
        follow,
        followOptions,
        preload,
        makeFollowable,
        makeClickable,
        isSafe,
        isFollowable,
        shouldFollowEvent,
        followMethod,
        convertClicks,
        config,
        combineFollowableSelectors
    };
})();
up.follow = up.link.follow;


/***/ }),
/* 88 */
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin


/***/ }),
/* 89 */
/***/ (() => {

/*-
Forms
=====

The `up.form` module helps you work with non-trivial forms.

@see form[up-submit]
@see form[up-validate]
@see input[up-switch]
@see form[up-autosubmit]

@module up.form
*/
up.form = (function () {
    const u = up.util;
    const e = up.element;
    const ATTRIBUTES_SUGGESTING_SUBMIT = ['[up-submit]', '[up-target]', '[up-layer]', '[up-transition]'];
    /*-
    Sets default options for form submission and validation.
  
    @property up.form.config
  
    @param {number} [config.observeDelay=0]
      The number of miliseconds to wait before [`up.observe()`](/up.observe) runs the callback
      after the input value changes. Use this to limit how often the callback
      will be invoked for a fast typist.
  
    @param {Array<string>} [config.submitSelectors]
      An array of CSS selectors matching forms that will be [submitted through Unpoly](/form-up-submit).
  
      You can configure Unpoly to handle *all* forms on a page without requiring an `[up-submit]` attribute:
  
      ```js
      up.form.config.submitSelectors.push('form')
      ```
  
      Individual forms may opt out with an `[up-submit=false]` attribute.
      You may configure additional exceptions in `config.noSubmitSelectors`.
  
    @param {Array<string>} [config.noSubmitSelectors]
      Exceptions to `config.submitSelectors`.
  
      Matching forms will *not* be [submitted through Unpoly](/form-up-submit), even if they match `config.submitSelectors`.
  
    @param {Array<string>} [config.validateTargets=['[up-fieldset]:has(&)', 'fieldset:has(&)', 'label:has(&)', 'form:has(&)']]
      An array of CSS selectors that are searched around a form field
      that wants to [validate](/up.validate).
  
      The first matching selector will be updated with the validation messages from the server.
  
      By default this looks for a `<fieldset>`, `<label>` or `<form>`
      around the validating input field.
  
    @param {string} [config.fieldSelectors]
      An array of CSS selectors that represent form fields, such as `input` or `select`.
  
    @param {string} [config.submitButtonSelectors]
      An array of CSS selectors that represent submit buttons, such as `input[type=submit]`.
  
    @stable
     */
    const config = new up.Config(() => ({
        validateTargets: ['[up-fieldset]:has(:origin)', 'fieldset:has(:origin)', 'label:has(:origin)', 'form:has(:origin)'],
        fieldSelectors: ['select', 'input:not([type=submit]):not([type=image])', 'button[type]:not([type=submit])', 'textarea'],
        submitSelectors: up.link.combineFollowableSelectors(['form'], ATTRIBUTES_SUGGESTING_SUBMIT),
        noSubmitSelectors: ['[up-submit=false]', '[target]'],
        submitButtonSelectors: ['input[type=submit]', 'input[type=image]', 'button[type=submit]', 'button:not([type])'],
        observeDelay: 0
    }));
    let abortScheduledValidate;
    function fullSubmitSelector() {
        return config.submitSelectors.join(',');
    }
    function reset() {
        config.reset();
    }
    /*-
     @function up.form.fieldSelector
     @internal
     */
    function fieldSelector(suffix = '') {
        return config.fieldSelectors.map(field => field + suffix).join(',');
    }
    /*-
    Returns a list of form fields within the given element.
  
    You can configure what Unpoly considers a form field by adding CSS selectors to the
    `up.form.config.fieldSelectors` array.
  
    If the given element is itself a form field, a list of that given element is returned.
  
    @function up.form.fields
    @param {Element|jQuery} root
      The element to scan for contained form fields.
  
      If the element is itself a form field, a list of that element is returned.
    @return {NodeList<Element>|Array<Element>}
  
    @experimental
    */
    function findFields(root) {
        root = e.get(root); // unwrap jQuery
        let fields = e.subtree(root, fieldSelector());
        // If findFields() is called with an entire form, gather fields outside the form
        // element that are associated with the form (through <input form="id-of-form">, which
        // is an HTML feature.)
        if (e.matches(root, 'form[id]')) {
            const outsideFieldSelector = fieldSelector(e.attributeSelector('form', root.getAttribute('id')));
            const outsideFields = e.all(outsideFieldSelector);
            fields.push(...outsideFields);
            fields = u.uniq(fields);
        }
        return fields;
    }
    /*-
    @function up.form.submittingButton
    @param {Element} form
    @internal
    */
    function submittingButton(form) {
        const selector = submitButtonSelector();
        const focusedElement = document.activeElement;
        if (focusedElement && e.matches(focusedElement, selector) && form.contains(focusedElement)) {
            return focusedElement;
        }
        else {
            // If no button is focused, we assume the first button in the form.
            return e.get(form, selector);
        }
    }
    /*-
    @function up.form.submitButtonSelector
    @internal
    */
    function submitButtonSelector() {
        return config.submitButtonSelectors.join(',');
    }
    /*-
    Submits a form via AJAX and updates a page fragment with the response.
  
    Instead of loading a new page, the form is submitted via AJAX.
    The response is parsed for a CSS selector and the matching elements will
    replace corresponding elements on the current page.
  
    The unobtrusive variant of this is the `form[up-submit]` selector.
    See its documentation to learn how form submissions work in Unpoly.
  
    Submitting a form is considered [navigation](/navigation).
  
    Emits the event [`up:form:submit`](/up:form:submit).
  
    ### Example
  
    ```js
    up.submit('form.new-user', { target: '.main' })
    ```
  
    @function up.submit
  
    @param {Element|jQuery|string} form
      The form to submit.
  
      If the argument points to an element that is not a form,
      Unpoly will search its ancestors for the [closest](/up.fragment.closest) form.
  
    @param {Object} [options]
      [Render options](/up.render) that should be used for submitting the form.
  
      Unpoly will parse render options from the given form's attributes
      like `[up-target]` or `[up-transition]`. See `form[up-submit]` for a list
      of supported attributes.
  
      You may pass this additional `options` object to supplement or override
      options parsed from the form attributes.
  
    @param {boolean} [options.navigate=true]
      Whether this fragment update is considered [navigation](/navigation).
  
      Setting this to `false` will disable most defaults.
  
    @return {Promise<up.RenderResult>}
      A promise that will be fulfilled when the server response was rendered.
  
    @stable
    */
    const submit = up.mockable((form, options) => {
        return up.render(submitOptions(form, options));
    });
    /*-
    Parses the [render](/up.render) options that would be used to
    [submit](/up.submit) the given form, but does not render.
  
    ### Example
  
    Given a form element:
  
    ```html
    <form action="/foo" method="post" up-target=".content">
    ...
    </form>
    ```
  
    We can parse the link's render options like this:
  
    ```js
    let form = document.querySelector('form')
    let options = up.form.submitOptions(form)
    // => { url: '/foo', method: 'POST', target: '.content', ... }
    ```
  
    @param {Element|jQuery|string} form
      The form for which to parse render option.
    @param {Object} [options]
      Additional options for the form submission.
  
      Will override any attribute values set on the given form element.
  
      See `up.render()` for detailed documentation of individual option properties.
    @function up.form.submitOptions
    @return {Object}
    @stable
    */
    function submitOptions(form, options) {
        form = getForm(form);
        options = parseBasicOptions(form, options);
        let parser = new up.OptionsParser(options, form);
        parser.string('failTarget', { default: up.fragment.toTarget(form) });
        // The guardEvent will also be assigned an { renderOptions } property in up.render()
        options.guardEvent || (options.guardEvent = up.event.build('up:form:submit', {
            submitButton: options.submitButton,
            params: options.params,
            log: 'Submitting form'
        }));
        // Now that we have extracted everything form-specific into options, we can call
        // up.link.followOptions(). This will also parse the myriads of other options
        // that are possible on both <form> and <a> elements.
        u.assign(options, up.link.followOptions(form, options));
        return options;
    }
    // This was extracted from submitOptions().
    // Validation needs to submit a form without options intended for the final submission,
    // like [up-scroll], [up-confirm], etc.
    function parseBasicOptions(form, options) {
        options = u.options(options);
        form = getForm(form);
        const parser = new up.OptionsParser(options, form);
        // Parse params from form fields.
        const params = up.Params.fromForm(form);
        options.submitButton || (options.submitButton = submittingButton(form));
        if (options.submitButton) {
            // Submit buttons with a [name] attribute will add to the params.
            // Note that addField() will only add an entry if the given button has a [name] attribute.
            params.addField(options.submitButton);
            // Submit buttons may have [formmethod] and [formaction] attribute
            // that override [method] and [action] attribute from the <form> element.
            options.method || (options.method = options.submitButton.getAttribute('formmethod'));
            options.url || (options.url = options.submitButton.getAttribute('formaction'));
        }
        params.addAll(options.params);
        options.params = params;
        parser.string('url', { attr: 'action', default: up.fragment.source(form) });
        parser.string('method', {
            attr: ['up-method', 'data-method', 'method'],
            default: 'GET',
            normalize: u.normalizeMethod
        });
        if (options.method === 'GET') {
            // Only for GET forms, browsers discard all query params from the form's [action] URL.
            // The URLs search part will be replaced with the serialized form data.
            // See design/query-params-in-form-actions/cases.html for
            // a demo of vanilla browser behavior.
            options.url = up.Params.stripURL(options.url);
        }
        return options;
    }
    /*-
    This event is [emitted](/up.emit) when a form is [submitted](/up.submit) through Unpoly.
  
    The event is emitted on the `<form>` element.
  
    When the form is being [validated](/input-up-validate), this event is not emitted.
    Instead an `up:form:validate` event is emitted.
  
    ### Changing render options
  
    Listeners may inspect and manipulate [render options](/up.render) for the coming fragment update.
  
    The code below will use a custom [transition](/a-up-transition)
    when a form submission [fails](/server-errors):
  
    ```js
    up.on('up:form:submit', function(event, form) {
      event.renderOptions.failTransition = 'shake'
    })
    ```
  
    @event up:form:submit
    @param {Element} event.target
      The `<form>` element that will be submitted.
    @param {up.Params} event.params
      The [form parameters](/up.Params) that will be send as the form's request payload.
    @param {Element} [event.submitButton]
      The button used to submit the form.
    @param {Object} event.renderOptions
      An object with [render options](/up.render) for the fragment update.
  
      Listeners may inspect and modify these options.
    @param event.preventDefault()
      Event listeners may call this method to prevent the form from being submitted.
    @stable
    */
    // MacOS does not focus buttons on click.
    // That means that submittingButton() cannot rely on document.activeElement.
    // See https://github.com/unpoly/unpoly/issues/103
    up.on('up:click', submitButtonSelector, function (event, button) {
        // Don't mess with focus unless we know that we're going to handle the form.
        // https://groups.google.com/g/unpoly/c/wsiATxepVZk
        const form = getForm(button);
        if (form && isSubmittable(form)) {
            button.focus();
        }
    });
    /*-
    Observes form fields and runs a callback when a value changes.
  
    This is useful for observing text fields while the user is typing.
  
    The unobtrusive variant of this is the [`[up-observe]`](/input-up-observe) attribute.
  
    ### Example
  
    The following would print to the console whenever an input field changes:
  
    ```js
    up.observe('input.query', function(value) {
      console.log('Query is now %o', value)
    })
    ```
  
    Instead of a single form field, you can also pass multiple fields,
    a `<form>` or any container that contains form fields.
    The callback will be run if any of the given fields change:
  
    ```js
    up.observe('form', function(value, name) {
     console.log('The value of %o is now %o', name, value)
    })
    ```
  
    You may also pass the `{ batch: true }` option to receive all
    changes since the last callback in a single object:
  
    ```js
    up.observe('form', { batch: true }, function(diff) {
     console.log('Observed one or more changes: %o', diff)
    })
    ```
  
    @function up.observe
    @param {string|Element|Array<Element>|jQuery} elements
      The form fields that will be observed.
  
      You can pass one or more fields, a `<form>` or any container that contains form fields.
      The callback will be run if any of the given fields change.
    @param {boolean} [options.batch=false]
      If set to `true`, the `onChange` callback will receive multiple
      detected changes in a single diff object as its argument.
    @param {number} [options.delay=up.form.config.observeDelay]
      The number of miliseconds to wait before executing the callback
      after the input value changes. Use this to limit how often the callback
      will be invoked for a fast typist.
    @param {Function(value, name): string} onChange
      The callback to run when the field's value changes.
  
      If given as a function, it receives two arguments (`value`, `name`).
      `value` is a string with the new attribute value and `string` is the name
      of the form field that changed. If given as a string, it will be evaled as
      JavaScript code in a context where (`value`, `name`) are set.
  
      A long-running callback function may return a promise that settles when
      the callback completes. In this case the callback will not be called again while
      it is already running.
    @return {Function()}
      A destructor function that removes the observe watch when called.
    @stable
    */
    function observe(elements, ...args) {
        elements = e.list(elements);
        const fields = u.flatMap(elements, findFields);
        const unnamedFields = u.reject(fields, 'name');
        if (unnamedFields.length) {
            // (1) We do not need to exclude the unnamed fields for up.FieldObserver, since that
            //     parses values with up.Params.fromFields(), and that ignores unnamed fields.
            // (2) Only warn, don't crash. There are some legitimate cases for having unnamed
            //     a mix of named and unnamed fields in a form, and we don't want to prevent
            //     <form up-observe> in that case.
            up.warn('up.observe()', 'Will not observe fields without a [name]: %o', unnamedFields);
        }
        const callback = u.extractCallback(args) || observeCallbackFromElement(elements[0]) || up.fail('up.observe: No change callback given');
        const options = u.extractOptions(args);
        options.delay = options.delay ?? e.numberAttr(elements[0], 'up-delay') ?? config.observeDelay;
        const observer = new up.FieldObserver(fields, options, callback);
        observer.start();
        return () => observer.stop();
    }
    function observeCallbackFromElement(element) {
        let rawCallback = element.getAttribute('up-observe');
        if (rawCallback) {
            return up.NonceableCallback.fromString(rawCallback).toFunction('value', 'name');
        }
    }
    /*-
    [Observes](/up.observe) a field or form and submits the form when a value changes.
  
    Both the form and the changed field will be assigned a CSS class [`.up-active`](/form.up-active)
    while the autosubmitted form is processing.
  
    The unobtrusive variant of this is the [`[up-autosubmit]`](/form-up-autosubmit) attribute.
  
    @function up.autosubmit
    @param {string|Element|jQuery} target
      The field or form to observe.
    @param {Object} [options]
      See options for [`up.observe()`](/up.observe)
    @return {Function()}
      A destructor function that removes the observe watch when called.
    @stable
    */
    function autosubmit(target, options) {
        return observe(target, options, () => submit(target));
    }
    function findValidateTarget(element, options) {
        let givenTarget;
        const container = getContainer(element);
        if (u.isElementish(options.target)) {
            return up.fragment.toTarget(options.target);
        }
        else if (givenTarget = options.target || element.getAttribute('up-validate') || container.getAttribute('up-validate')) {
            return givenTarget;
        }
        else if (e.matches(element, 'form')) {
            // If element is the form, we cannot find a better validate target than this.
            return up.fragment.toTarget(element);
        }
        else {
            return findValidateTargetFromConfig(element, options) || up.fail('Could not find validation target for %o (tried defaults %o)', element, config.validateTargets);
        }
    }
    function findValidateTargetFromConfig(element, options) {
        // for the first selector that has a match in the field's layer.
        const layer = up.layer.get(element);
        return u.findResult(config.validateTargets, function (defaultTarget) {
            if (up.fragment.get(defaultTarget, { ...options, layer })) {
                // We want to return the selector, *not* the element. If we returned the element
                // and derive a selector from that, any :has() expression would be lost.
                return defaultTarget;
            }
        });
    }
    /*-
    Performs a server-side validation of a form field.
  
    `up.validate()` submits the given field's form with an additional `X-Up-Validate`
    HTTP header. Upon seeing this header, the server is expected to validate (but not save)
    the form submission and render a new copy of the form with validation errors.
  
    The unobtrusive variant of this is the [`input[up-validate]`](/input-up-validate) selector.
    See the documentation for [`input[up-validate]`](/input-up-validate) for more information
    on how server-side validation works in Unpoly.
  
    ### Example
  
    ```js
    up.validate('input[name=email]', { target: '.email-errors' })
    ```
  
    @function up.validate
    @param {string|Element|jQuery} field
      The form field to validate.
    @param {Object} [options]
      Additional [submit options](/up.submit#options) that should be used for
      submitting the form for validation.
  
      You may pass this `options` object to supplement or override the defaults
      from `up.submit()`.
    @param {string|Element|jQuery} [options.target]
      The element that will be [updated](/up.render) with the validation results.
  
      By default the closest [validate target](/up.form.config#config.validateTargets)
      around the given `field` is updated.
    @return {Promise}
      A promise that fulfills when the server-side
      validation is received and the form was updated.
    @stable
    */
    function validate(field, options) {
        // If passed a selector, up.fragment.get() will prefer a match on the current layer.
        field = up.fragment.get(field);
        options = parseBasicOptions(field, options);
        options.origin = field;
        options.target = findValidateTarget(field, options);
        options.focus = 'keep';
        // The protocol doesn't define whether the validation results in a status code.
        // Hence we use the same options for both success and failure.
        options.fail = false;
        // Make sure the X-Up-Validate header is present, so the server-side
        // knows that it should not persist the form submission
        options.headers || (options.headers = {});
        options.headers[up.protocol.headerize('validate')] = field.getAttribute('name') || ':unknown';
        // The guardEvent will also be assigned a { renderOptions } attribute in up.render()
        options.guardEvent = up.event.build('up:form:validate', { field, log: 'Validating form' });
        return up.render(options);
    }
    /*-
    This event is emitted before a field is being [validated](/input-up-validate).
  
    @event up:form:validate
    @param {Element} event.field
      The form field that has been changed and caused the validated request.
    @param {Object} event.renderOptions
      An object with [render options](/up.render) for the fragment update
      that will show the validation results.
  
      Listeners may inspect and modify these options.
    @param event.preventDefault()
      Event listeners may call this method to prevent the validation request
      being sent to the server.
    @stable
    */
    function switcherValues(field) {
        let value;
        let meta;
        if (e.matches(field, 'input[type=checkbox]')) {
            if (field.checked) {
                value = field.value;
                meta = ':checked';
            }
            else {
                meta = ':unchecked';
            }
        }
        else if (e.matches(field, 'input[type=radio]')) {
            const form = getContainer(field);
            const groupName = field.getAttribute('name');
            const checkedButton = form.querySelector(`input[type=radio]${e.attributeSelector('name', groupName)}:checked`);
            if (checkedButton) {
                meta = ':checked';
                value = checkedButton.value;
            }
            else {
                meta = ':unchecked';
            }
        }
        else {
            value = field.value;
        }
        const values = [];
        if (u.isPresent(value)) {
            values.push(value);
            values.push(':present');
        }
        else {
            values.push(':blank');
        }
        if (u.isPresent(meta)) {
            values.push(meta);
        }
        return values;
    }
    /*-
    Shows or hides a target selector depending on the value.
  
    See [`input[up-switch]`](/input-up-switch) for more documentation and examples.
  
    This function does not currently have a very useful API outside
    of our use for `up-switch`'s UJS behavior, that's why it's currently
    still marked `@internal`.
  
    @function up.form.switchTargets
    @param {Element} switcher
    @param {string} [options.target]
      The target selectors to switch.
      Defaults to an `[up-switch]` attribute on the given field.
    @internal
    */
    function switchTargets(switcher, options = {}) {
        const targetSelector = options.target || options.target || switcher.getAttribute('up-switch');
        const form = getContainer(switcher);
        targetSelector || up.fail("No switch target given for %o", switcher);
        const fieldValues = switcherValues(switcher);
        for (let target of e.all(form, targetSelector)) {
            switchTarget(target, fieldValues);
        }
    }
    const switchTarget = up.mockable(function (target, fieldValues) {
        let show;
        fieldValues || (fieldValues = switcherValues(findSwitcherForTarget(target)));
        let hideValues = target.getAttribute('up-hide-for');
        if (hideValues) {
            hideValues = u.splitValues(hideValues);
            show = u.intersect(fieldValues, hideValues).length === 0;
        }
        else {
            let showValues = target.getAttribute('up-show-for');
            // If the target has neither up-show-for or up-hide-for attributes,
            // assume the user wants the target to be visible whenever anything
            // is checked or entered.
            showValues = showValues ? u.splitValues(showValues) : [':present', ':checked'];
            show = u.intersect(fieldValues, showValues).length > 0;
        }
        e.toggle(target, show);
        target.classList.add('up-switched');
    });
    function findSwitcherForTarget(target) {
        const form = getContainer(target);
        const switchers = e.all(form, '[up-switch]');
        const switcher = u.find(switchers, function (switcher) {
            const targetSelector = switcher.getAttribute('up-switch');
            return e.matches(target, targetSelector);
        });
        return switcher || up.fail('Could not find [up-switch] field for %o', target);
    }
    function getForm(elementOrTarget, fallbackSelector) {
        const element = up.fragment.get(elementOrTarget);
        // Element#form will also work if the element is outside the form with an [form=form-id] attribute
        return element.form || e.closest(element, 'form') || (fallbackSelector && e.closest(element, fallbackSelector));
    }
    function getContainer(element) {
        return getForm(element, up.layer.anySelector());
    }
    function isField(element) {
        return e.matches(element, fieldSelector());
    }
    function focusedField() {
        return u.presence(document.activeElement, isField);
    }
    /*-
    Returns whether the given form will be [submitted](/up.follow) through Unpoly
    instead of making a full page load.
  
    By default Unpoly will follow forms if the element has
    one of the following attributes:
  
    - `[up-submit]`
    - `[up-target]`
    - `[up-layer]`
    - `[up-transition]`
  
    To consider other selectors to be submittable, see `up.form.config.submitSelectors`.
  
    @function up.form.isSubmittable
    @param {Element|jQuery|string} form
      The form to check.
    @stable
    */
    function isSubmittable(form) {
        form = up.fragment.get(form);
        return e.matches(form, fullSubmitSelector()) && !isSubmitDisabled(form);
    }
    function isSubmitDisabled(form) {
        // We also don't want to handle cross-origin forms.
        // That will be handled in `up.Change.FromURL#newPageReason`.
        return e.matches(form, config.noSubmitSelectors.join(','));
    }
    /*-
    Submits this form via JavaScript and updates a fragment with the server response.
  
    The server response is searched for the selector given in `up-target`.
    The selector content is then [replaced](/up.replace) in the current page.
  
    The programmatic variant of this is the [`up.submit()`](/up.submit) function.
  
    ### Example
  
    ```html
    <form method="post" action="/users" up-submit>
      ...
    </form>
    ```
  
    ### Handling validation errors
  
    When the server was unable to save the form due to invalid params,
    it will usually re-render an updated copy of the form with
    validation messages.
  
    For Unpoly to be able to detect a failed form submission,
    the form must be re-rendered with a non-200 HTTP status code.
    We recommend to use either 400 (bad request) or
    422 (unprocessable entity).
  
    In Ruby on Rails, you can pass a
    [`:status` option to `render`](http://guides.rubyonrails.org/layouts_and_rendering.html#the-status-option)
    for this:
  
    ```ruby
    class UsersController < ApplicationController
  
      def create
        user_params = params[:user].permit(:email, :password)
        @user = User.new(user_params)
        if @user.save?
          sign_in @user
        else
          render 'form', status: :bad_request
        end
      end
  
    end
    ```
  
    You may define different option for the failure case by infixing an attribute with `fail`:
  
    ```html
    <form method="post" action="/action"
      up-target=".content"
      up-fail-target="form"
      up-scroll="auto"
      up-fail-scroll=".errors">
      ...
    </form>
    ```
  
    See [handling server errors](/server-errors) for details.
  
    Note that you can also use
    [`input[up-validate]`](/input-up-validate) to perform server-side
    validations while the user is completing fields.
  
    ### Giving feedback while the form is processing
  
    The `<form>` element will be assigned a CSS class [`.up-active`](/form.up-active) while
    the submission is loading.
  
    ### Short notation
  
    You may omit the `[up-submit]` attribute if the form has one of the following attributes:
  
    - `[up-target]`
    - `[up-layer]`
    - `[up-transition]`
  
    Such a form will still be submitted through Unpoly.
  
    ### Handling all forms automatically
  
    You can configure Unpoly to handle *all* forms on a page without requiring an `[up-submit]` attribute.
  
    See [Handling all links and forms](/handling-everything).
  
    @selector form[up-submit]
  
    @params-note
      All attributes for `a[up-follow]` may be used.
  
    @stable
    */
    up.on('submit', fullSubmitSelector, function (event, form) {
        // Users may configure up.form.config.submitSelectors.push('form')
        // and then opt out individual forms with [up-submit=false].
        if (event.defaultPrevented || isSubmitDisabled(form)) {
            return;
        }
        abortScheduledValidate?.();
        up.event.halt(event);
        up.log.muteUncriticalRejection(submit(form));
    });
    /*-
    When a form field with this attribute is changed, the form is validated on the server
    and is updated with validation messages.
  
    To validate the form, Unpoly will submit the form with an additional `X-Up-Validate` HTTP header.
    When seeing this header, the server is expected to validate (but not save)
    the form submission and render a new copy of the form with validation errors.
  
    The programmatic variant of this is the [`up.validate()`](/up.validate) function.
  
    ### Example
  
    Let's look at a standard registration form that asks for an e-mail and password:
  
    ```html
    <form action="/users">
  
      <label>
        E-mail: <input type="text" name="email" />
      </label>
  
      <label>
        Password: <input type="password" name="password" />
      </label>
  
      <button type="submit">Register</button>
  
    </form>
    ```
  
    When the user changes the `email` field, we want to validate that
    the e-mail address is valid and still available. Also we want to
    change the `password` field for the minimum required password length.
    We can do this by giving both fields an `up-validate` attribute:
  
    ```html
    <form action="/users">
  
      <label>
        E-mail: <input type="text" name="email" up-validate />
      </label>
  
      <label>
        Password: <input type="password" name="password" up-validate />
      </label>
  
      <button type="submit">Register</button>
  
    </form>
    ```
  
    Whenever a field with `up-validate` changes, the form is POSTed to
    `/users` with an additional `X-Up-Validate` HTTP header.
    When seeing this header, the server is expected to validate (but not save)
    the form submission and render a new copy of the form with validation errors.
  
    In Ruby on Rails the processing action should behave like this:
  
    ```ruby
    class UsersController < ApplicationController
  
      * This action handles POST /users
      def create
        user_params = params[:user].permit(:email, :password)
        @user = User.new(user_params)
        if request.headers['X-Up-Validate']
          @user.valid?  # run validations, but don't save to the database
          render 'form' # render form with error messages
        elsif @user.save?
          sign_in @user
        else
          render 'form', status: :bad_request
        end
      end
  
    end
    ```
  
    Note that if you're using the `unpoly-rails` gem you can simply say `up.validate?`
    instead of manually checking for `request.headers['X-Up-Validate']`.
  
    The server now renders an updated copy of the form with eventual validation errors:
  
    ```ruby
    <form action="/users">
  
      <label class="has-error">
        E-mail: <input type="text" name="email" value="foo@bar.com" />
        Has already been taken!
      </label>
  
      <button type="submit">Register</button>
  
    </form>
    ```
  
    The `<label>` around the e-mail field is now updated to have the `has-error`
    class and display the validation message.
  
    ### How validation results are displayed
  
    Although the server will usually respond to a validation with a complete,
    fresh copy of the form, Unpoly will by default not update the entire form.
    This is done in order to preserve volatile state such as the scroll position
    of `<textarea>` elements.
  
    By default Unpoly looks for a `<fieldset>`, `<label>` or `<form>`
    around the validating input field, or any element with an
    `up-fieldset` attribute.
    With the Bootstrap bindings, Unpoly will also look
    for a container with the `form-group` class.
  
    You can change this default behavior by setting `up.form.config.validateTargets`:
  
    ```js
    // Always update the entire form containing the current field ("&")
    up.form.config.validateTargets = ['form &']
    ```
  
    You can also individually override what to update by setting the `up-validate`
    attribute to a CSS selector:
  
    ```html
    <input type="text" name="email" up-validate=".email-errors">
    <span class="email-errors"></span>
    ```
  
    ### Updating dependent fields
  
    The `[up-validate]` behavior is also a great way to partially update a form
    when one fields depends on the value of another field.
  
    Let's say you have a form with one `<select>` to pick a department (sales, engineering, ...)
    and another `<select>` to pick an employeee from the selected department:
  
    ```html
    <form action="/contracts">
      <select name="department">...</select> <!-- options for all departments -->
      <select name="employeed">...</select> <!-- options for employees of selected department -->
    </form>
    ```
  
    The list of employees needs to be updated as the appartment changes:
  
    ```html
    <form action="/contracts">
      <select name="department" up-validate="[name=employee]">...</select>
      <select name="employee">...</select>
    </form>
    ```
  
    In order to update the `department` field in addition to the `employee` field, you could say
    `up-validate="&, [name=employee]"`, or simply `up-validate="form"` to update the entire form.
  
    @selector input[up-validate]
    @param up-validate
      The CSS selector to update with the server response.
  
      This defaults to a fieldset or form group around the validating field.
    @stable
    */
    /*-
    Validates this form on the server when any field changes and shows validation errors.
  
    You can configure what Unpoly considers a fieldset by adding CSS selectors to the
    `up.form.config.validateTargets` array.
  
    See `input[up-validate]` for detailed documentation.
  
    @selector form[up-validate]
    @param up-validate
      The CSS selector to update with the server response.
  
      This defaults to a fieldset or form group around the changing field.
    @stable
    */
    up.on('change', '[up-validate]', function (event) {
        // Even though [up-validate] may be used on either an entire form or an individual input,
        // the change event will trigger on a given field.
        const field = findFields(event.target)[0];
        // There is an edge case where the user is changing an input with [up-validate],
        // but blurs the input by directly clicking the submit button. In this case the
        // following events will be emitted:
        //
        // - change on the input
        // - focus on the button
        // - submit on the form
        //
        // In this case we do not want to send a validate request to the server, but
        // simply submit the form. Because this event handler does not know if a submit
        // event is about to fire, we delay the validation to the next microtask.
        // In case we receive a submit event after this, we can cancel the validation.
        abortScheduledValidate = u.abortableMicrotask(() => {
            return up.log.muteUncriticalRejection(validate(field));
        });
    });
    /*-
    Show or hide elements when a form field is set to a given value.
  
    When the controlling form field gets an `up-switch` attribute, and that form field is nested inside a `<form>`
    parent, the targets elements must also be inside that same `<form>` parent.
  
    ### Example: Select options
  
    The controlling form field gets an `up-switch` attribute with a selector for the elements to show or hide:
  
    ```html
    <select name="advancedness" up-switch=".target">
      <option value="basic">Basic parts</option>
      <option value="advanced">Advanced parts</option>
      <option value="very-advanced">Very advanced parts</option>
    </select>
    ```
  
    The target elements can use [`[up-show-for]`](/up-show-for) and [`[up-hide-for]`](/up-hide-for)
    attributes to indicate for which values they should be shown or hidden:
  
    ```html
    <div class="target" up-show-for="basic">
      only shown for advancedness = basic
    </div>
  
    <div class="target" up-hide-for="basic">
      hidden for advancedness = basic
    </div>
  
    <div class="target" up-show-for="advanced very-advanced">
      shown for advancedness = advanced or very-advanced
    </div>
    ```
  
    ### Example: Text field
  
    The controlling `<input>` gets an `up-switch` attribute with a selector for the elements to show or hide:
  
    ```html
    <input type="text" name="user" up-switch=".target">
  
    <div class="target" up-show-for="alice">
      only shown for user alice
    </div>
    ```
  
    You can also use the pseudo-values `:blank` to match an empty input value,
    or `:present` to match a non-empty input value:
  
    ```html
    <input type="text" name="user" up-switch=".target">
  
    <div class="target" up-show-for=":blank">
      please enter a username
    </div>
    ```
  
    ### Example: Checkbox
  
    For checkboxes you can match against the pseudo-values `:checked` or `:unchecked`:
  
    ```html
    <input type="checkbox" name="flag" up-switch=".target">
  
    <div class="target" up-show-for=":checked">
      only shown when checkbox is checked
    </div>
  
    <div class="target" up-show-for=":unchecked">
      only shown when checkbox is unchecked
    </div>
    ```
  
    Of course you can also match against the `value` property of the checkbox element:
  
    ```html
    <input type="checkbox" name="flag" value="active" up-switch=".target">
  
    <div class="target" up-show-for="active">
      only shown when checkbox is checked
    </div>
    ```
  
    ### Example: Radio button
  
    ```html
    <input type="radio" name="advancedness" value="basic" up-switch=".target">
    <input type="radio" name="advancedness" value="advanced" up-switch=".target">
    <input type="radio" name="advancedness" value="very-advanced" up-switch=".target">
  
    <div class="target" up-show-for="basic">
      only shown for advancedness = basic
    </div>
  
    <div class="target" up-hide-for="basic">
      hidden for advancedness = basic
    </div>
  
    <div class="target" up-show-for="advanced very-advanced">
      shown for advancedness = advanced or very-advanced
    </div>
    ```
  
    @selector input[up-switch]
    @param up-switch
      A CSS selector for elements whose visibility depends on this field's value.
    @stable
    */
    /*-
    Only shows this element if an input field with [`[up-switch]`](/input-up-switch) has one of the given values.
  
    See [`input[up-switch]`](/input-up-switch) for more documentation and examples.
  
    @selector [up-show-for]
    @param [up-show-for]
      A space-separated list of input values for which this element should be shown.
    @stable
    */
    /*-
    Hides this element if an input field with [`[up-switch]`](/input-up-switch) has one of the given values.
  
    See [`input[up-switch]`](/input-up-switch) for more documentation and examples.
  
    @selector [up-hide-for]
    @param [up-hide-for]
      A space-separated list of input values for which this element should be hidden.
    @stable
    */
    up.compiler('[up-switch]', (switcher) => {
        switchTargets(switcher);
    });
    up.on('change', '[up-switch]', (_event, switcher) => {
        switchTargets(switcher);
    });
    up.compiler('[up-show-for]:not(.up-switched), [up-hide-for]:not(.up-switched)', (element) => {
        switchTarget(element);
    });
    /*-
    Observes this field and runs a callback when a value changes.
  
    This is useful for observing text fields while the user is typing.
    If you want to submit the form after a change see [`input[up-autosubmit]`](/input-up-autosubmit).
  
    With a strict Content Security Policy [additional rules apply](/csp).
  
    The programmatic variant of this is the [`up.observe()`](/up.observe) function.
  
    ### Example
  
    The following would run a global `showSuggestions(value)` function
    whenever the `<input>` changes:
  
    ```html
    <input name="query" up-observe="showSuggestions(value)">
    ```
  
    Note that the parameter name in the markup must be called `value` or it will not work.
    The parameter name can be called whatever you want in the JavaScript, however.
  
    Also note that the function must be declared on the `window` object to work, like so:
  
    ```js
    window.showSuggestions = function(selectedValue) {
      console.log(`Called showSuggestions() with ${selectedValue}`)
    }
    ```
  
    ### Callback context
  
    The script given to `[up-observe]` runs with the following context:
  
    | Name     | Type      | Description                           |
    | -------- | --------- | ------------------------------------- |
    | `value`  | `string`  | The current value of the field        |
    | `this`   | `Element` | The form field                        |
    | `$field` | `jQuery`  | The form field as a jQuery collection |
  
    ### Observing radio buttons
  
    Multiple radio buttons with the same `[name]` (a radio button group)
    produce a single value for the form.
  
    To observe radio buttons group, use the `[up-observe]` attribute on an
    element that contains all radio button elements with a given name:
  
    ```html
    <div up-observe="formatSelected(value)">
      <input type="radio" name="format" value="html"> HTML format
      <input type="radio" name="format" value="pdf"> PDF format
      <input type="radio" name="format" value="txt"> Text format
    </div>
    ```
  
    @selector input[up-observe]
    @param up-observe
      The code to run when the field's value changes.
    @param up-delay
      The number of miliseconds to wait after a change before the code is run.
    @stable
    */
    /*-
    Observes this form and runs a callback when any field changes.
  
    This is useful for observing text fields while the user is typing.
    If you want to submit the form after a change see [`input[up-autosubmit]`](/input-up-autosubmit).
  
    With a strict Content Security Policy [additional rules apply](/csp).
  
    The programmatic variant of this is the [`up.observe()`](/up.observe) function.
  
    ### Example
  
    The would call a function `somethingChanged(value)`
    when any `<input>` within the `<form>` changes:
  
    ```html
    <form up-observe="somethingChanged(value)">
      <input name="foo">
      <input name="bar">
    </form>
    ```
  
    ### Callback context
  
    The script given to `[up-observe]` runs with the following context:
  
    | Name     | Type      | Description                           |
    | -------- | --------- | ------------------------------------- |
    | `value`  | `string`  | The current value of the field        |
    | `this`   | `Element` | The form field                        |
    | `$field` | `jQuery`  | The form field as a jQuery collection |
  
    @selector form[up-observe]
    @param up-observe
      The code to run when any field's value changes.
    @param up-delay
      The number of miliseconds to wait after a change before the code is run.
    @stable
    */
    up.compiler('[up-observe]', (formOrField) => observe(formOrField));
    /*-
    Submits this field's form when this field changes its values.
  
    Both the form and the changed field will be assigned a CSS class [`.up-active`](/form.up-active)
    while the autosubmitted form is loading.
  
    The programmatic variant of this is the [`up.autosubmit()`](/up.autosubmit) function.
  
    ### Example
  
    The following would automatically submit the form when the query is changed:
  
    ```html
    <form method="GET" action="/search">
      <input type="search" name="query" up-autosubmit>
      <input type="checkbox" name="archive"> Include archive
    </form>
    ```
  
    ### Auto-submitting radio buttons
  
    Multiple radio buttons with the same `[name]` (a radio button group)
    produce a single value for the form.
  
    To auto-submit radio buttons group, use the `[up-submit]` attribute on an
    element that contains all radio button elements with a given name:
  
    ```html
    <div up-autosubmit>
      <input type="radio" name="format" value="html"> HTML format
      <input type="radio" name="format" value="pdf"> PDF format
      <input type="radio" name="format" value="txt"> Text format
    </div>
    ```
  
    @selector input[up-autosubmit]
    @param [up-delay]
      The number of miliseconds to wait after a change before the form is submitted.
    @stable
    */
    /*-
    Submits the form when any field changes.
  
    Both the form and the field will be assigned a CSS class [`.up-active`](/form.up-active)
    while the autosubmitted form is loading.
  
    The programmatic variant of this is the [`up.autosubmit()`](/up.autosubmit) function.
  
    ### Example
  
    This will submit the form when either query or checkbox was changed:
  
    ```html
    <form method="GET" action="/search" up-autosubmit>
      <input type="search" name="query">
      <input type="checkbox" name="archive"> Include archive
    </form>
    ```
  
    @selector form[up-autosubmit]
    @param [up-delay]
      The number of miliseconds to wait after a change before the form is submitted.
    @stable
    */
    up.compiler('[up-autosubmit]', (formOrField) => autosubmit(formOrField));
    up.on('up:framework:reset', reset);
    return {
        config,
        submit,
        submitOptions,
        isSubmittable,
        observe,
        validate,
        autosubmit,
        fieldSelector,
        fields: findFields,
        focusedField,
        switchTarget
    };
})();
up.submit = up.form.submit;
up.observe = up.form.observe;
up.autosubmit = up.form.autosubmit;
up.validate = up.form.validate;


/***/ }),
/* 90 */
/***/ (() => {

/*-
Navigation feedback
===================

The `up.feedback` module adds useful CSS classes to links while they are loading,
or when they point to the current URL.

By styling these classes you may provide instant feedback to user interactions,
improving the perceived speed of your interface.


### Example

Let's say we have an `<nav>` element with two links, pointing to `/foo` and `/bar` respectively:

```html
<nav>
  <a href="/foo" up-follow>Foo</a>
  <a href="/bar" up-follow>Bar</a>
</nav>
```

By giving the navigation bar the `[up-nav]` attribute, links pointing to the current browser address are highlighted
as we navigate through the site.

If the current URL is `/foo`, the first link is automatically marked with an [`.up-current`](/a.up-current) class:

```html
<nav up-nav>
  <a href="/foo" up-follow class="up-current">Foo</a>
  <a href="/bar" up-follow>Bar</a>
</nav>
```

When the user clicks on the `/bar` link, the link will receive the [`up-active`](/a.up-active) class while it is waiting
for the server to respond:

```
<nav up-nav>
  <a href="/foo" up-follow class="up-current">Foo</a>
  <a href="/bar" up-follow class="up-active">Bar</a>
</div>
```

Once the response is received the URL will change to `/bar` and the `up-active` class is removed:

```html
<nav up-nav>
  <a href="/foo" up-follow>Foo</a>
  <a href="/bar" up-follow class="up-current">Bar</a>
</nav>
```

@see [up-nav]
@see a.up-current
@see a.up-active

@module up.feedback
*/
up.feedback = (function () {
    const u = up.util;
    const e = up.element;
    /*-
    Sets default options for this package.
  
    @property up.feedback.config
  
    @param {Array<string>} [config.currentClasses]
      An array of classes to set on [links that point the current location](/a.up-current).
  
    @param {Array<string>} [config.navSelectors]
      An array of CSS selectors that match [navigation components](/up-nav).
  
    @stable
    */
    const config = new up.Config(() => ({
        currentClasses: ['up-current'],
        navSelectors: ['[up-nav]', 'nav'],
    }));
    function reset() {
        config.reset();
        up.layer.root.feedbackLocation = null;
    }
    const CLASS_ACTIVE = 'up-active';
    const SELECTOR_LINK = 'a, [up-href]';
    function navSelector() {
        return config.navSelectors.join(',');
    }
    function normalizeURL(url) {
        if (url) {
            return u.normalizeURL(url, { trailingSlash: false, hash: false });
        }
    }
    function linkURLs(link) {
        // Check if we have computed the URLs before.
        // Computation is sort of expensive (multiplied by number of links),
        // so we cache the results in a link property
        return link.upFeedbackURLs || (link.upFeedbackURLs = new up.LinkFeedbackURLs(link));
    }
    function updateFragment(fragment) {
        const layerOption = { layer: up.layer.get(fragment) };
        if (up.fragment.closest(fragment, navSelector(), layerOption)) {
            // If the new fragment is an [up-nav], or if the new fragment is a child of an [up-nav],
            // all links in the new fragment are considered links that we need to update.
            //
            // Note that:
            //
            // - The [up-nav] element might not be part of this update.
            //   It might already be in the DOM, and only a child was updated.
            // - The fragment might be a link itself.
            // - We do not need to update sibling links of fragment that have been processed before.
            // - The fragment may be the <body> element which contains all other overlays.
            //   But we only want to update the <body>.
            const links = up.fragment.subtree(fragment, SELECTOR_LINK, layerOption);
            updateLinks(links, layerOption);
        }
        else {
            updateLinksWithinNavs(fragment, layerOption);
        }
    }
    function updateLinksWithinNavs(fragment, options) {
        const navs = up.fragment.subtree(fragment, navSelector(), options);
        const links = u.flatMap(navs, nav => e.subtree(nav, SELECTOR_LINK));
        updateLinks(links, options);
    }
    function getNormalizedLayerLocation(layer) {
        // Don't re-use layer.feedbackLocation since the current layer returns
        // location.href in case someone changed the history using the pushState API.
        return layer.feedbackLocation || normalizeURL(layer.location);
    }
    function updateLinks(links, options = {}) {
        if (!links.length) {
            return;
        }
        const layer = options.layer || up.layer.get(links[0]);
        // An overlay might not have a { location } property, e.g. if it was created
        // from local { content }. In this case we do not set .up-current.
        let layerLocation = getNormalizedLayerLocation(layer);
        if (layerLocation) {
            for (let link of links) {
                const isCurrent = linkURLs(link).isCurrent(layerLocation);
                for (let currentClass of config.currentClasses) {
                    e.toggleClass(link, currentClass, isCurrent);
                }
                e.toggleAttr(link, 'aria-current', 'page', isCurrent);
            }
        }
    }
    /*-
    @function findActivatableArea
    @param {string|Element|jQuery} element
    @internal
    */
    function findActivatableArea(element) {
        // Try to enlarge links that are expanded with [up-expand] on a surrounding container.
        // Note that the expression below is not the same as e.closest(area, SELECTOR_LINK)!
        return e.ancestor(element, SELECTOR_LINK) || element;
    }
    /*-
    Marks the given element as currently loading, by assigning the CSS class [`up-active`](/a.up-active).
  
    This happens automatically when following links or submitting forms through the Unpoly API.
    Use this function if you make custom network calls from your own JavaScript code.
  
    If the given element is a link within an [expanded click area](/up-expand),
    the class will be assigned to the expanded area.
  
    ### Example
  
        var button = document.querySelector('button')
  
        button.addEventListener('click', () => {
          up.feedback.start(button)
          up.request(...).then(() => {
            up.feedback.stop(button)
          })
        })
  
    @function up.feedback.start
    @param {Element} element
      The element to mark as active
    @internal
    */
    function start(element) {
        findActivatableArea(element).classList.add(CLASS_ACTIVE);
    }
    /*-
    Links that are currently [loading through Unpoly](/a-up-follow)
    are assigned the `.up-active` class automatically.
  
    Style `.up-active` in your CSS to improve the perceived responsiveness
    of your user interface.
  
    The `.up-active` class will be removed when the link is done loading.
  
    ### Example
  
    We have a link:
  
    ```html
    <a href="/foo" up-follow>Foo</a>
    ```
  
    The user clicks on the link. While the request is loading,
    the link has the `up-active` class:
  
    ```html
    <a href="/foo" up-follow class="up-active">Foo</a>
    ```
  
    Once the link destination has loaded and rendered, the `.up-active` class
    is removed and the [`.up-current`](/a.up-current) class is added:
  
    ```html
    <a href="/foo" up-follow class="up-current">Foo</a>
    ```
  
    @selector a.up-active
    @stable
    */
    /*-
    Forms that are currently [loading through Unpoly](/form-up-submit)
    are assigned the `.up-active` class automatically.
    Style `.up-active` in your CSS to improve the perceived responsiveness
    of your user interface.
  
    The `.up-active` class will be removed as soon as the response to the
    form submission has been received.
  
    ### Example
  
    We have a form:
  
    ```html
    <form up-target=".foo">
      <button type="submit">Submit</button>
    </form>
    ```
  
    The user clicks on the submit button. While the form is being submitted
    and waiting for the server to respond, the form has the `up-active` class:
  
    ```html
    <form up-target=".foo" class="up-active">
      <button type="submit">Submit</button>
    </form>
    ```
  
    Once the link destination has loaded and rendered, the `.up-active` class
    is removed.
  
    @selector form.up-active
    @stable
    */
    /*-
    Marks the given element as no longer loading, by removing the CSS class [`.up-active`](/a.up-active).
  
    This happens automatically when network requests initiated by the Unpoly API have completed.
    Use this function if you make custom network calls from your own JavaScript code.
  
    @function up.feedback.stop
    @param {Element} element
      The link or form that has finished loading.
    @internal
    */
    function stop(element) {
        findActivatableArea(element).classList.remove(CLASS_ACTIVE);
    }
    function around(element, fn) {
        start(element);
        const result = fn();
        u.always(result, () => stop(element));
        // Return the original promise returned by fn(), not the
        // new promise from u.always(), which cannot reject.
        return result;
    }
    function aroundForOptions(options, fn) {
        let element;
        let feedbackOpt = options.feedback;
        if (feedbackOpt) {
            if (u.isBoolean(feedbackOpt)) {
                element = options.origin;
            }
            else {
                element = feedbackOpt;
            }
        }
        if (element) {
            // In case we get passed a selector or jQuery collection as { origin }
            // or { feedback }, unwrap it with up.fragment.get().
            element = up.fragment.get(element);
            return around(element, fn);
        }
        else {
            return fn();
        }
    }
    /*-
    Marks this element as a navigation component, such as a menu or navigation bar.
  
    When a link within an `[up-nav]` element points to [its layer's location](/up.layer.location),
    it is assigned the [`.up-current`](/a.up-current) class. When the browser navigates to another location, the class is removed automatically.
  
    You may also assign `[up-nav]` to an individual link instead of an navigational container.
  
    If you don't want to manually add this attribute to every navigational element,
    you can configure selectors to automatically match your navigation components in `up.feedback.config.navSelectors`.
  
  
    ### Example
  
    Let's take a simple menu with two links. The menu has been marked with the `[up-nav]` attribute:
  
    ```html
    <div up-nav>
      <a href="/foo">Foo</a>
      <a href="/bar">Bar</a>
    </div>
    ```
  
    If the browser location changes to `/foo`, the first link is marked as `.up-current`:
  
    ```html
    <div up-nav>
      <a href="/foo" class="up-current">Foo</a>
      <a href="/bar">Bar</a>
    </div>
    ```
  
    If the browser location changes to `/bar`, the first link automatically loses its `.up-current` class. Now the second link is marked as `.up-current`:
  
    ```html
    <div up-nav>
      <a href="/foo">Foo</a>
      <a href="/bar" class="up-current">Bar</a>
    </div>
    ```
  
  
    ### When is a link "current"?
  
    When no [overlay](/up.layer) is open, the current location is the URL displayed
    in the browser's address bar. When the link in question is placed in an overlay,
    the current location is the location of that overlay, even if that
    overlay doesn't have [visible history](/up.Layer.prototype.history).
  
    A link matches the current location (and is marked as `.up-current`) if it matches either:
  
    - the link's `[href]` attribute
    - the link's `[up-href]` attribute
    - the URL pattern in the link's [`[up-alias]`](/a-up-alias) attribute
  
    Any `#hash` fragments in the link's or current URLs will be ignored.
  
    @selector [up-nav]
    @stable
    */
    /*-
    Links within `[up-nav]` may use the `[up-alias]` attribute to pass a [URL pattern](/url-patterns) for which they
    should also be highlighted as [`.up-current`](/a.up-current).
  
    ### Example
  
    The link below will be highlighted with `.up-current` at both `/profile` and `/profile/edit` locations:
  
    ```html
    <div up-nav>
      <a href="/profile" up-alias="/profile/edit">Profile</a>
    </div>
    ```
  
    To pass more than one alternative URLs, use a [URL pattern](/url-patterns).
  
    @selector a[up-alias]
    @param up-alias
      A [URL pattern](/url-patterns) with alternative URLs.
    @stable
    */
    /*-
    When a link within an `[up-nav]` element points to the current location, it is assigned the `.up-current` class.
  
    See [`[up-nav]`](/up-nav) for more documentation and examples.
  
    @selector a.up-current
    @stable
    */
    function updateLayerIfLocationChanged(layer) {
        const processedLocation = layer.feedbackLocation;
        const layerLocation = getNormalizedLayerLocation(layer.location);
        // A history change might call this function multiple times,
        // since we listen to both up:location:changed and up:layer:location:changed.
        // We also don't want to unnecessarily reprocess nav links, which is expensive.
        // For this reason we check whether the current location differs from
        // the last processed location.
        if (!processedLocation || (processedLocation !== layerLocation)) {
            layer.feedbackLocation = layerLocation;
            updateLinksWithinNavs(layer.element, { layer });
        }
    }
    function onBrowserLocationChanged() {
        const frontLayer = up.layer.front;
        // We allow Unpoly-unaware code to use the pushState API and change the
        // front layer in the process. See up.Layer.Base#location setter.
        if (frontLayer.showsLiveHistory()) {
            updateLayerIfLocationChanged(frontLayer);
        }
    }
    // Even when the modal or popup does not change history, we consider the URLs of the content it displays.
    up.on('up:location:changed', (_event) => {
        onBrowserLocationChanged();
    });
    up.on('up:fragment:inserted', (_event, newFragment) => {
        updateFragment(newFragment);
    });
    up.on('up:layer:location:changed', (event) => {
        updateLayerIfLocationChanged(event.layer);
    });
    // The framework is reset between tests
    up.on('up:framework:reset', reset);
    return {
        config,
        start,
        stop,
        around,
        aroundForOptions,
        normalizeURL,
    };
})();


/***/ }),
/* 91 */
/***/ (() => {

/*-
Passive updates
===============

This package contains functionality to passively receive updates from the server.

@see [up-hungry]
@see [up-poll]

@module up.radio
*/
up.radio = (function () {
    const u = up.util;
    /*-
    Configures defaults for passive updates.
  
    @property up.radio.config
  
    @param {Array<string>} [config.hungrySelectors]
      An array of CSS selectors that is replaced whenever a matching element is found in a response.
      These elements are replaced even when they were not targeted directly.
  
      By default this contains the [`[up-hungry]`](/up-hungry) attribute.
  
    @param {number} [config.pollInterval=30000]
      The default [polling](/up-poll] interval in milliseconds.
  
    @param {boolean|string|Function(Element)} [config.pollEnabled=true]
      Whether Unpoly will follow instructions to poll fragments, like the `[up-poll]` attribute.
  
      When set to `'auto'` Unpoly will skip polling updates while one of the following applies:
  
      - The browser tab is in the foreground
      - The fragment's layer is the [frontmost layer](/up.layer.front).
      - We should not [avoid optional requests](/up.network.shouldReduceRequests)
  
      When set to `true`, Unpoly will always allow polling.
  
      When set to `false`, Unpoly will never allow polling.
  
      You may also pass a function that accepts the polling fragment and returns `true`, `false` or `'auto'`.
  
      When an update is skipped due to polling being disabled,
      Unpoly will try to poll again after the configured interval.
  
    @stable
    */
    const config = new up.Config(() => ({
        hungrySelectors: ['[up-hungry]'],
        pollInterval: 30000,
        pollEnabled: 'auto'
    }));
    function reset() {
        config.reset();
    }
    /*-
    @function up.radio.hungrySelector
    @internal
    */
    function hungrySelector() {
        return config.hungrySelectors.join(',');
    }
    /*-
    Elements with an `[up-hungry]` attribute are updated whenever the server
    sends a matching element, even if the element isn't targeted.
  
    Use cases for this are unread message counters or notification flashes.
    Such elements often live in the layout, outside of the content area that is
    being replaced.
  
    @selector [up-hungry]
    @param [up-transition]
      The transition to use when this element is updated.
    @stable
    */
    /*-
    Starts [polling](/up-poll) the given element.
  
    The given element does not need an `[up-poll]` attribute.
  
    @function up.radio.startPolling
    @param {Element} fragment
      The fragment to reload periodically.
    @param {number} options.interval
      The reload interval in milliseconds.
  
      Defaults to `up.radio.config.pollInterval`.
    @param {string} options.url
      Defaults to the element's closest `[up-source]` attribute.
    @stable
    */
    function startPolling(fragment, options = {}) {
        up.FragmentPolling.forFragment(fragment).forceStart(options);
    }
    /*-
    Stops [polling](/up-poll) the given element.
  
    @function up.radio.stopPolling
    @param {Element} fragment
      The fragment to stop reloading.
    @stable
    */
    function stopPolling(element) {
        up.FragmentPolling.forFragment(element).forceStop();
    }
    function shouldPoll(fragment) {
        const setting = u.evalOption(config.pollEnabled, fragment);
        if (setting === 'auto') {
            return !document.hidden && !up.network.shouldReduceRequests() && up.layer.get(fragment)?.isFront?.();
        }
        return setting;
    }
    /*-
    Elements with an `[up-poll]` attribute are [reloaded](/up.reload) from the server periodically.
  
    ### Example
  
    Assume an application layout with an unread message counter.
    You can use `[up-poll]` to refresh the counter every 30 seconds:
  
    ```html
    <div class="unread-count" up-poll>
      2 new messages
    </div>
    ```
  
    ### Controlling the reload interval
  
    You may set an optional `[up-interval]` attribute to set the reload interval in milliseconds:
  
    ```html
    <div class="unread-count" up-poll up-interval="10000">
      2 new messages
    </div>
    ```
  
    If the value is omitted, a global default is used. You may configure the default like this:
  
    ```js
    up.radio.config.pollInterval = 10000
    ```
  
    ### Controlling the source URL
  
    The element will be reloaded from the URL from which it was originally loaded.
  
    To reload from another URL, set an `[up-source]` attribute on the polling element:
  
    ```html
    <div class="unread-count" up-poll up-source="/unread-count">
      2 new messages
    </div>
    ```
  
    ### Skipping updates on the client
  
    Client-side code may skip an update by preventing an `up:fragment:poll` event
    on the polling fragment.
  
    Unpoly will also choose to skip updates under certain conditions,
    e.g. when the browser tab is in the background. See `up.radio.config.pollEnabled` for details.
  
    When an update is skipped, Unpoly will try to poll again after the configured interval.
  
    ### Skipping updates on the server
  
    When polling a fragment periodically we want to avoid rendering unchanged content.
    This saves <b>CPU time</b> and reduces the <b>bandwidth cost</b> for a
    request/response exchange to **~1 KB**.
  
    To achieve this we timestamp your fragments with an `[up-time]` attribute to indicate
    when the underlying data was last changed. See `[up-time]` for a detailed example.
  
    If the server has no more recent changes, it may skip the update by responding
    with an HTTP status `304 Not Modified`.
  
    When an update is skipped, Unpoly will try to poll again after the configured interval.
  
    ### Stopping polling
  
    - The fragment from the server response no longer has an `[up-poll]` attribute.
    - Client-side code has called `up.radio.stopPolling()` with the polling element.
    - Polling was [disabled globally](/up.radio.config#config.pollEnabled).
  
    @selector [up-poll]
    @param [up-interval]
      The reload interval in milliseconds.
  
      Defaults to `up.radio.config.pollInterval`.
    @param [up-source]
      The URL from which to reload the fragment.
  
      Defaults to the closest `[up-source]` attribute of an ancestor element.
    @stable
    */
    up.compiler('[up-poll]', (fragment) => {
        up.FragmentPolling.forFragment(fragment).onPollAttributeObserved();
    });
    /*-
    This event is emitted before a [polling](/up-poll) fragment is reloaded from the server.
  
    Listener may prevent the `up:fragment:poll` event to prevent the fragment from being reloaded.
    Preventing the event will only skip a single update. It will *not* stop future polling.
  
    @event up:fragment:poll
    @param {Element} event.target
      The polling fragment.
    @param event.preventDefault()
      Event listeners may call this method to prevent the fragment from being reloaded.
    @experimental
    */
    up.on('up:framework:reset', reset);
    return {
        config,
        hungrySelector,
        startPolling,
        stopPolling,
        shouldPoll,
    };
})();


/***/ }),
/* 92 */
/***/ (() => {

/*
Play nice with Rails UJS
========================

Unpoly is mostly a superset of Rails UJS, so we convert attributes like `[data-method]` to `[up-method]´.
*/
up.rails = (function () {
    const u = up.util;
    const e = up.element;
    function isRails() {
        return window._rails_loaded || // current rails-ujs integrated with Rails 5.2+
            window.Rails || // legacy rails/rails-ujs gem
            window.jQuery?.rails; // legacy rails/jquery-ujs gem
    }
    return u.each(['method', 'confirm'], function (feature) {
        const dataAttribute = `data-${feature}`;
        const upAttribute = `up-${feature}`;
        up.macro(`a[${dataAttribute}]`, function (link) {
            if (isRails() && up.link.isFollowable(link)) {
                e.setMissingAttr(link, upAttribute, link.getAttribute(dataAttribute));
                // Remove the [data-...] attribute so links will not be
                // handled a second time after Unpoly.
                return link.removeAttribute(dataAttribute);
            }
        });
    });
})();


/***/ })
/******/ 	]);
/************************************************************************/
/******/ 	// The module cache
/******/ 	var __webpack_module_cache__ = {};
/******/ 	
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/ 		// Check if module is in cache
/******/ 		var cachedModule = __webpack_module_cache__[moduleId];
/******/ 		if (cachedModule !== undefined) {
/******/ 			return cachedModule.exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = __webpack_module_cache__[moduleId] = {
/******/ 			// no module.id needed
/******/ 			// no module.loaded needed
/******/ 			exports: {}
/******/ 		};
/******/ 	
/******/ 		// Execute the module function
/******/ 		__webpack_modules__[moduleId](module, module.exports, __webpack_require__);
/******/ 	
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	(() => {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = (exports) => {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	})();
/******/ 	
/************************************************************************/
var __webpack_exports__ = {};
// This entry need to be wrapped in an IIFE because it need to be isolated against other modules in the chunk.
(() => {
__webpack_require__(1);
__webpack_require__(2);
__webpack_require__(3);
__webpack_require__(4);
__webpack_require__(5);
__webpack_require__(6);
__webpack_require__(7);
__webpack_require__(9);
__webpack_require__(10);
__webpack_require__(11);
__webpack_require__(12);
__webpack_require__(13);
__webpack_require__(14);
__webpack_require__(15);
__webpack_require__(16);
__webpack_require__(17);
__webpack_require__(18);
__webpack_require__(19);
__webpack_require__(20);
__webpack_require__(21);
__webpack_require__(22);
__webpack_require__(23);
__webpack_require__(24);
__webpack_require__(25);
__webpack_require__(26);
__webpack_require__(27);
__webpack_require__(28);
__webpack_require__(29);
__webpack_require__(30);
__webpack_require__(31);
__webpack_require__(32);
__webpack_require__(33);
__webpack_require__(34);
__webpack_require__(35);
__webpack_require__(36);
__webpack_require__(37);
__webpack_require__(38);
__webpack_require__(39);
__webpack_require__(40);
__webpack_require__(41);
__webpack_require__(42);
__webpack_require__(43);
__webpack_require__(44);
__webpack_require__(45);
__webpack_require__(46);
__webpack_require__(47);
__webpack_require__(48);
__webpack_require__(49);
__webpack_require__(50);
__webpack_require__(51);
__webpack_require__(52);
__webpack_require__(53);
__webpack_require__(54);
__webpack_require__(55);
__webpack_require__(56);
__webpack_require__(57);
__webpack_require__(58);
__webpack_require__(59);
__webpack_require__(60);
__webpack_require__(61);
__webpack_require__(62);
__webpack_require__(63);
__webpack_require__(64);
__webpack_require__(65);
__webpack_require__(66);
__webpack_require__(67);
__webpack_require__(68);
__webpack_require__(69);
__webpack_require__(70);
__webpack_require__(71);
__webpack_require__(72);
__webpack_require__(73);
__webpack_require__(74);
__webpack_require__(75);
__webpack_require__(76);
__webpack_require__(77);
__webpack_require__(78);
__webpack_require__(80);
__webpack_require__(82);
__webpack_require__(83);
__webpack_require__(85);
__webpack_require__(87);
__webpack_require__(89);
__webpack_require__(90);
__webpack_require__(91);
__webpack_require__(92);
up.framework.onEvaled();

})();

/******/ })()
;