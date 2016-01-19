(function (window, undefined) {
    var readyList, rootjQuery, core_strundefined = typeof undefined, document = window.document, location = window.location, _jQuery = window.jQuery, _$ = window.$, class2type = {}, core_deletedIds = [], core_version = '1.9.1', core_concat = core_deletedIds.concat, core_push = core_deletedIds.push, core_slice = core_deletedIds.slice, core_indexOf = core_deletedIds.indexOf, core_toString = class2type.toString, core_hasOwn = class2type.hasOwnProperty, core_trim = core_version.trim, jQuery = function (selector, context) {
            return new jQuery.fn.init(selector, context, rootjQuery);
        }, core_pnum = /[+-]?(?:\d*\.|)\d+(?:[eE][+-]?\d+|)/.source, core_rnotwhite = /\S+/g, rtrim = /^[\s\uFEFF\xA0]+|[\s\uFEFF\xA0]+$/g, rquickExpr = /^(?:(<[\w\W]+>)[^>]*|#([\w-]*))$/, rsingleTag = /^<(\w+)\s*\/?>(?:<\/\1>|)$/, rvalidchars = /^[\],:{}\s]*$/, rvalidbraces = /(?:^|:|,)(?:\s*\[)+/g, rvalidescape = /\\(?:["\\\/bfnrt]|u[\da-fA-F]{4})/g, rvalidtokens = /"[^"\\\r\n]*"|true|false|null|-?(?:\d+\.|)\d+(?:[eE][+-]?\d+|)/g, rmsPrefix = /^-ms-/, rdashAlpha = /-([\da-z])/gi, fcamelCase = function (all, letter) {
            return letter.toUpperCase();
        }, completed = function (event) {
            if (document.addEventListener || event.type === 'load' || document.readyState === 'complete') {
                detach();
                jQuery.ready();
            }
        }, detach = function () {
            if (document.addEventListener) {
                document.removeEventListener('DOMContentLoaded', completed, false);
                window.removeEventListener('load', completed, false);
            } else {
                document.detachEvent('onreadystatechange', completed);
                window.detachEvent('onload', completed);
            }
        };
    jQuery.fn = jQuery.prototype = {
        jquery: core_version,
        constructor: jQuery,
        init: function (selector, context, rootjQuery) {
            var match, elem;
            if (!selector) {
                return this;
            }
            if (typeof selector === 'string') {
                if (selector.charAt(0) === '<' && selector.charAt(selector.length - 1) === '>' && selector.length >= 3) {
                    match = [
                        null,
                        selector,
                        null
                    ];
                } else {
                    match = rquickExpr.exec(selector);
                }
                if (match && (match[1] || !context)) {
                    if (match[1]) {
                        context = context instanceof jQuery ? context[0] : context;
                        jQuery.merge(this, jQuery.parseHTML(match[1], context && context.nodeType ? context.ownerDocument || context : document, true));
                        if (rsingleTag.test(match[1]) && jQuery.isPlainObject(context)) {
                            for (match in context) {
                                if (jQuery.isFunction(this[match])) {
                                    this[match](context[match]);
                                } else {
                                    this.attr(match, context[match]);
                                }
                            }
                        }
                        return this;
                    } else {
                        elem = document.getElementById(match[2]);
                        if (elem && elem.parentNode) {
                            if (elem.id !== match[2]) {
                                return rootjQuery.find(selector);
                            }
                            this.length = 1;
                            this[0] = elem;
                        }
                        this.context = document;
                        this.selector = selector;
                        return this;
                    }
                } else if (!context || context.jquery) {
                    return (context || rootjQuery).find(selector);
                } else {
                    return this.constructor(context).find(selector);
                }
            } else if (selector.nodeType) {
                this.context = this[0] = selector;
                this.length = 1;
                return this;
            } else if (jQuery.isFunction(selector)) {
                return rootjQuery.ready(selector);
            }
            if (selector.selector !== undefined) {
                this.selector = selector.selector;
                this.context = selector.context;
            }
            return jQuery.makeArray(selector, this);
        },
        selector: '',
        length: 0,
        size: function () {
            return this.length;
        },
        toArray: function () {
            return core_slice.call(this);
        },
        get: function (num) {
            return num == null ? this.toArray() : num < 0 ? this[this.length + num] : this[num];
        },
        pushStack: function (elems) {
            var ret = jQuery.merge(this.constructor(), elems);
            ret.prevObject = this;
            ret.context = this.context;
            return ret;
        },
        each: function (callback, args) {
            return jQuery.each(this, callback, args);
        },
        ready: function (fn) {
            jQuery.ready.promise().done(fn);
            return this;
        },
        slice: function () {
            return this.pushStack(core_slice.apply(this, arguments));
        },
        first: function () {
            return this.eq(0);
        },
        last: function () {
            return this.eq(-1);
        },
        eq: function (i) {
            var len = this.length, j = +i + (i < 0 ? len : 0);
            return this.pushStack(j >= 0 && j < len ? [this[j]] : []);
        },
        map: function (callback) {
            return this.pushStack(jQuery.map(this, function (elem, i) {
                return callback.call(elem, i, elem);
            }));
        },
        end: function () {
            return this.prevObject || this.constructor(null);
        },
        push: core_push,
        sort: [].sort,
        splice: [].splice
    };
    jQuery.fn.init.prototype = jQuery.fn;
    jQuery.extend = jQuery.fn.extend = function () {
        var src, copyIsArray, copy, name, options, clone, target = arguments[0] || {}, i = 1, length = arguments.length, deep = false;
        if (typeof target === 'boolean') {
            deep = target;
            target = arguments[1] || {};
            i = 2;
        }
        if (typeof target !== 'object' && !jQuery.isFunction(target)) {
            target = {};
        }
        if (length === i) {
            target = this;
            --i;
        }
        for (; i < length; i++) {
            if ((options = arguments[i]) != null) {
                for (name in options) {
                    src = target[name];
                    copy = options[name];
                    if (target === copy) {
                        continue;
                    }
                    if (deep && copy && (jQuery.isPlainObject(copy) || (copyIsArray = jQuery.isArray(copy)))) {
                        if (copyIsArray) {
                            copyIsArray = false;
                            clone = src && jQuery.isArray(src) ? src : [];
                        } else {
                            clone = src && jQuery.isPlainObject(src) ? src : {};
                        }
                        target[name] = jQuery.extend(deep, clone, copy);
                    } else if (copy !== undefined) {
                        target[name] = copy;
                    }
                }
            }
        }
        return target;
    };
    jQuery.extend({
        noConflict: function (deep) {
            if (window.$ === jQuery) {
                window.$ = _$;
            }
            if (deep && window.jQuery === jQuery) {
                window.jQuery = _jQuery;
            }
            return jQuery;
        },
        isReady: false,
        readyWait: 1,
        holdReady: function (hold) {
            if (hold) {
                jQuery.readyWait++;
            } else {
                jQuery.ready(true);
            }
        },
        ready: function (wait) {
            if (wait === true ? --jQuery.readyWait : jQuery.isReady) {
                return;
            }
            if (!document.body) {
                return setTimeout(jQuery.ready);
            }
            jQuery.isReady = true;
            if (wait !== true && --jQuery.readyWait > 0) {
                return;
            }
            readyList.resolveWith(document, [jQuery]);
            if (jQuery.fn.trigger) {
                jQuery(document).trigger('ready').off('ready');
            }
        },
        isFunction: function (obj) {
            return jQuery.type(obj) === 'function';
        },
        isArray: Array.isArray || function (obj) {
            return jQuery.type(obj) === 'array';
        },
        isWindow: function (obj) {
            return obj != null && obj == obj.window;
        },
        isNumeric: function (obj) {
            return !isNaN(parseFloat(obj)) && isFinite(obj);
        },
        type: function (obj) {
            if (obj == null) {
                return String(obj);
            }
            return typeof obj === 'object' || typeof obj === 'function' ? class2type[core_toString.call(obj)] || 'object' : typeof obj;
        },
        isPlainObject: function (obj) {
            if (!obj || jQuery.type(obj) !== 'object' || obj.nodeType || jQuery.isWindow(obj)) {
                return false;
            }
            try {
                if (obj.constructor && !core_hasOwn.call(obj, 'constructor') && !core_hasOwn.call(obj.constructor.prototype, 'isPrototypeOf')) {
                    return false;
                }
            } catch (e) {
                return false;
            }
            var key;
            for (key in obj) {
            }
            return key === undefined || core_hasOwn.call(obj, key);
        },
        isEmptyObject: function (obj) {
            var name;
            for (name in obj) {
                return false;
            }
            return true;
        },
        error: function (msg) {
            throw new Error(msg);
        },
        parseHTML: function (data, context, keepScripts) {
            if (!data || typeof data !== 'string') {
                return null;
            }
            if (typeof context === 'boolean') {
                keepScripts = context;
                context = false;
            }
            context = context || document;
            var parsed = rsingleTag.exec(data), scripts = !keepScripts && [];
            if (parsed) {
                return [context.createElement(parsed[1])];
            }
            parsed = jQuery.buildFragment([data], context, scripts);
            if (scripts) {
                jQuery(scripts).remove();
            }
            return jQuery.merge([], parsed.childNodes);
        },
        parseJSON: function (data) {
            if (window.JSON && window.JSON.parse) {
                return window.JSON.parse(data);
            }
            if (data === null) {
                return data;
            }
            if (typeof data === 'string') {
                data = jQuery.trim(data);
                if (data) {
                    if (rvalidchars.test(data.replace(rvalidescape, '@').replace(rvalidtokens, ']').replace(rvalidbraces, ''))) {
                        return new Function('return ' + data)();
                    }
                }
            }
            jQuery.error('Invalid JSON: ' + data);
        },
        parseXML: function (data) {
            var xml, tmp;
            if (!data || typeof data !== 'string') {
                return null;
            }
            try {
                if (window.DOMParser) {
                    tmp = new DOMParser();
                    xml = tmp.parseFromString(data, 'text/xml');
                } else {
                    xml = new ActiveXObject('Microsoft.XMLDOM');
                    xml.async = 'false';
                    xml.loadXML(data);
                }
            } catch (e) {
                xml = undefined;
            }
            if (!xml || !xml.documentElement || xml.getElementsByTagName('parsererror').length) {
                jQuery.error('Invalid XML: ' + data);
            }
            return xml;
        },
        noop: function () {
        },
        globalEval: function (data) {
            if (data && jQuery.trim(data)) {
                (window.execScript || function (data) {
                    window['eval'].call(window, data);
                })(data);
            }
        },
        camelCase: function (string) {
            return string.replace(rmsPrefix, 'ms-').replace(rdashAlpha, fcamelCase);
        },
        nodeName: function (elem, name) {
            return elem.nodeName && elem.nodeName.toLowerCase() === name.toLowerCase();
        },
        each: function (obj, callback, args) {
            var value, i = 0, length = obj.length, isArray = isArraylike(obj);
            if (args) {
                if (isArray) {
                    for (; i < length; i++) {
                        value = callback.apply(obj[i], args);
                        if (value === false) {
                            break;
                        }
                    }
                } else {
                    for (i in obj) {
                        value = callback.apply(obj[i], args);
                        if (value === false) {
                            break;
                        }
                    }
                }
            } else {
                if (isArray) {
                    for (; i < length; i++) {
                        value = callback.call(obj[i], i, obj[i]);
                        if (value === false) {
                            break;
                        }
                    }
                } else {
                    for (i in obj) {
                        value = callback.call(obj[i], i, obj[i]);
                        if (value === false) {
                            break;
                        }
                    }
                }
            }
            return obj;
        },
        trim: core_trim && !core_trim.call('\uFEFF\xA0') ? function (text) {
            return text == null ? '' : core_trim.call(text);
        } : function (text) {
            return text == null ? '' : (text + '').replace(rtrim, '');
        },
        makeArray: function (arr, results) {
            var ret = results || [];
            if (arr != null) {
                if (isArraylike(Object(arr))) {
                    jQuery.merge(ret, typeof arr === 'string' ? [arr] : arr);
                } else {
                    core_push.call(ret, arr);
                }
            }
            return ret;
        },
        inArray: function (elem, arr, i) {
            var len;
            if (arr) {
                if (core_indexOf) {
                    return core_indexOf.call(arr, elem, i);
                }
                len = arr.length;
                i = i ? i < 0 ? Math.max(0, len + i) : i : 0;
                for (; i < len; i++) {
                    if (i in arr && arr[i] === elem) {
                        return i;
                    }
                }
            }
            return -1;
        },
        merge: function (first, second) {
            var l = second.length, i = first.length, j = 0;
            if (typeof l === 'number') {
                for (; j < l; j++) {
                    first[i++] = second[j];
                }
            } else {
                while (second[j] !== undefined) {
                    first[i++] = second[j++];
                }
            }
            first.length = i;
            return first;
        },
        grep: function (elems, callback, inv) {
            var retVal, ret = [], i = 0, length = elems.length;
            inv = !!inv;
            for (; i < length; i++) {
                retVal = !!callback(elems[i], i);
                if (inv !== retVal) {
                    ret.push(elems[i]);
                }
            }
            return ret;
        },
        map: function (elems, callback, arg) {
            var value, i = 0, length = elems.length, isArray = isArraylike(elems), ret = [];
            if (isArray) {
                for (; i < length; i++) {
                    value = callback(elems[i], i, arg);
                    if (value != null) {
                        ret[ret.length] = value;
                    }
                }
            } else {
                for (i in elems) {
                    value = callback(elems[i], i, arg);
                    if (value != null) {
                        ret[ret.length] = value;
                    }
                }
            }
            return core_concat.apply([], ret);
        },
        guid: 1,
        proxy: function (fn, context) {
            var args, proxy, tmp;
            if (typeof context === 'string') {
                tmp = fn[context];
                context = fn;
                fn = tmp;
            }
            if (!jQuery.isFunction(fn)) {
                return undefined;
            }
            args = core_slice.call(arguments, 2);
            proxy = function () {
                return fn.apply(context || this, args.concat(core_slice.call(arguments)));
            };
            proxy.guid = fn.guid = fn.guid || jQuery.guid++;
            return proxy;
        },
        access: function (elems, fn, key, value, chainable, emptyGet, raw) {
            var i = 0, length = elems.length, bulk = key == null;
            if (jQuery.type(key) === 'object') {
                chainable = true;
                for (i in key) {
                    jQuery.access(elems, fn, i, key[i], true, emptyGet, raw);
                }
            } else if (value !== undefined) {
                chainable = true;
                if (!jQuery.isFunction(value)) {
                    raw = true;
                }
                if (bulk) {
                    if (raw) {
                        fn.call(elems, value);
                        fn = null;
                    } else {
                        bulk = fn;
                        fn = function (elem, key, value) {
                            return bulk.call(jQuery(elem), value);
                        };
                    }
                }
                if (fn) {
                    for (; i < length; i++) {
                        fn(elems[i], key, raw ? value : value.call(elems[i], i, fn(elems[i], key)));
                    }
                }
            }
            return chainable ? elems : bulk ? fn.call(elems) : length ? fn(elems[0], key) : emptyGet;
        },
        now: function () {
            return new Date().getTime();
        }
    });
    jQuery.ready.promise = function (obj) {
        if (!readyList) {
            readyList = jQuery.Deferred();
            if (document.readyState === 'complete') {
                setTimeout(jQuery.ready);
            } else if (document.addEventListener) {
                document.addEventListener('DOMContentLoaded', completed, false);
                window.addEventListener('load', completed, false);
            } else {
                document.attachEvent('onreadystatechange', completed);
                window.attachEvent('onload', completed);
                var top = false;
                try {
                    top = window.frameElement == null && document.documentElement;
                } catch (e) {
                }
                if (top && top.doScroll) {
                    (function doScrollCheck() {
                        if (!jQuery.isReady) {
                            try {
                                top.doScroll('left');
                            } catch (e) {
                                return setTimeout(doScrollCheck, 50);
                            }
                            detach();
                            jQuery.ready();
                        }
                    }());
                }
            }
        }
        return readyList.promise(obj);
    };
    jQuery.each('Boolean Number String Function Array Date RegExp Object Error'.split(' '), function (i, name) {
        class2type['[object ' + name + ']'] = name.toLowerCase();
    });
    function isArraylike(obj) {
        var length = obj.length, type = jQuery.type(obj);
        if (jQuery.isWindow(obj)) {
            return false;
        }
        if (obj.nodeType === 1 && length) {
            return true;
        }
        return type === 'array' || type !== 'function' && (length === 0 || typeof length === 'number' && length > 0 && length - 1 in obj);
    }
    rootjQuery = jQuery(document);
    var optionsCache = {};
    function createOptions(options) {
        var object = optionsCache[options] = {};
        jQuery.each(options.match(core_rnotwhite) || [], function (_, flag) {
            object[flag] = true;
        });
        return object;
    }
    jQuery.Callbacks = function (options) {
        options = typeof options === 'string' ? optionsCache[options] || createOptions(options) : jQuery.extend({}, options);
        var firing, memory, fired, firingLength, firingIndex, firingStart, list = [], stack = !options.once && [], fire = function (data) {
                memory = options.memory && data;
                fired = true;
                firingIndex = firingStart || 0;
                firingStart = 0;
                firingLength = list.length;
                firing = true;
                for (; list && firingIndex < firingLength; firingIndex++) {
                    if (list[firingIndex].apply(data[0], data[1]) === false && options.stopOnFalse) {
                        memory = false;
                        break;
                    }
                }
                firing = false;
                if (list) {
                    if (stack) {
                        if (stack.length) {
                            fire(stack.shift());
                        }
                    } else if (memory) {
                        list = [];
                    } else {
                        self.disable();
                    }
                }
            }, self = {
                add: function () {
                    if (list) {
                        var start = list.length;
                        (function add(args) {
                            jQuery.each(args, function (_, arg) {
                                var type = jQuery.type(arg);
                                if (type === 'function') {
                                    if (!options.unique || !self.has(arg)) {
                                        list.push(arg);
                                    }
                                } else if (arg && arg.length && type !== 'string') {
                                    add(arg);
                                }
                            });
                        }(arguments));
                        if (firing) {
                            firingLength = list.length;
                        } else if (memory) {
                            firingStart = start;
                            fire(memory);
                        }
                    }
                    return this;
                },
                remove: function () {
                    if (list) {
                        jQuery.each(arguments, function (_, arg) {
                            var index;
                            while ((index = jQuery.inArray(arg, list, index)) > -1) {
                                list.splice(index, 1);
                                if (firing) {
                                    if (index <= firingLength) {
                                        firingLength--;
                                    }
                                    if (index <= firingIndex) {
                                        firingIndex--;
                                    }
                                }
                            }
                        });
                    }
                    return this;
                },
                has: function (fn) {
                    return fn ? jQuery.inArray(fn, list) > -1 : !!(list && list.length);
                },
                empty: function () {
                    list = [];
                    return this;
                },
                disable: function () {
                    list = stack = memory = undefined;
                    return this;
                },
                disabled: function () {
                    return !list;
                },
                lock: function () {
                    stack = undefined;
                    if (!memory) {
                        self.disable();
                    }
                    return this;
                },
                locked: function () {
                    return !stack;
                },
                fireWith: function (context, args) {
                    args = args || [];
                    args = [
                        context,
                        args.slice ? args.slice() : args
                    ];
                    if (list && (!fired || stack)) {
                        if (firing) {
                            stack.push(args);
                        } else {
                            fire(args);
                        }
                    }
                    return this;
                },
                fire: function () {
                    self.fireWith(this, arguments);
                    return this;
                },
                fired: function () {
                    return !!fired;
                }
            };
        return self;
    };
    jQuery.extend({
        Deferred: function (func) {
            var tuples = [
                    [
                        'resolve',
                        'done',
                        jQuery.Callbacks('once memory'),
                        'resolved'
                    ],
                    [
                        'reject',
                        'fail',
                        jQuery.Callbacks('once memory'),
                        'rejected'
                    ],
                    [
                        'notify',
                        'progress',
                        jQuery.Callbacks('memory')
                    ]
                ], state = 'pending', promise = {
                    state: function () {
                        return state;
                    },
                    always: function () {
                        deferred.done(arguments).fail(arguments);
                        return this;
                    },
                    then: function () {
                        var fns = arguments;
                        return jQuery.Deferred(function (newDefer) {
                            jQuery.each(tuples, function (i, tuple) {
                                var action = tuple[0], fn = jQuery.isFunction(fns[i]) && fns[i];
                                deferred[tuple[1]](function () {
                                    var returned = fn && fn.apply(this, arguments);
                                    if (returned && jQuery.isFunction(returned.promise)) {
                                        returned.promise().done(newDefer.resolve).fail(newDefer.reject).progress(newDefer.notify);
                                    } else {
                                        newDefer[action + 'With'](this === promise ? newDefer.promise() : this, fn ? [returned] : arguments);
                                    }
                                });
                            });
                            fns = null;
                        }).promise();
                    },
                    promise: function (obj) {
                        return obj != null ? jQuery.extend(obj, promise) : promise;
                    }
                }, deferred = {};
            promise.pipe = promise.then;
            jQuery.each(tuples, function (i, tuple) {
                var list = tuple[2], stateString = tuple[3];
                promise[tuple[1]] = list.add;
                if (stateString) {
                    list.add(function () {
                        state = stateString;
                    }, tuples[i ^ 1][2].disable, tuples[2][2].lock);
                }
                deferred[tuple[0]] = function () {
                    deferred[tuple[0] + 'With'](this === deferred ? promise : this, arguments);
                    return this;
                };
                deferred[tuple[0] + 'With'] = list.fireWith;
            });
            promise.promise(deferred);
            if (func) {
                func.call(deferred, deferred);
            }
            return deferred;
        },
        when: function (subordinate) {
            var i = 0, resolveValues = core_slice.call(arguments), length = resolveValues.length, remaining = length !== 1 || subordinate && jQuery.isFunction(subordinate.promise) ? length : 0, deferred = remaining === 1 ? subordinate : jQuery.Deferred(), updateFunc = function (i, contexts, values) {
                    return function (value) {
                        contexts[i] = this;
                        values[i] = arguments.length > 1 ? core_slice.call(arguments) : value;
                        if (values === progressValues) {
                            deferred.notifyWith(contexts, values);
                        } else if (!--remaining) {
                            deferred.resolveWith(contexts, values);
                        }
                    };
                }, progressValues, progressContexts, resolveContexts;
            if (length > 1) {
                progressValues = new Array(length);
                progressContexts = new Array(length);
                resolveContexts = new Array(length);
                for (; i < length; i++) {
                    if (resolveValues[i] && jQuery.isFunction(resolveValues[i].promise)) {
                        resolveValues[i].promise().done(updateFunc(i, resolveContexts, resolveValues)).fail(deferred.reject).progress(updateFunc(i, progressContexts, progressValues));
                    } else {
                        --remaining;
                    }
                }
            }
            if (!remaining) {
                deferred.resolveWith(resolveContexts, resolveValues);
            }
            return deferred.promise();
        }
    });
    jQuery.support = function () {
        var support, all, a, input, select, fragment, opt, eventName, isSupported, i, div = document.createElement('div');
        div.setAttribute('className', 't');
        div.innerHTML = '  <link/><table></table><a href=\'/a\'>a</a><input type=\'checkbox\'/>';
        all = div.getElementsByTagName('*');
        a = div.getElementsByTagName('a')[0];
        if (!all || !a || !all.length) {
            return {};
        }
        select = document.createElement('select');
        opt = select.appendChild(document.createElement('option'));
        input = div.getElementsByTagName('input')[0];
        a.style.cssText = 'top:1px;float:left;opacity:.5';
        support = {
            getSetAttribute: div.className !== 't',
            leadingWhitespace: div.firstChild.nodeType === 3,
            tbody: !div.getElementsByTagName('tbody').length,
            htmlSerialize: !!div.getElementsByTagName('link').length,
            style: /top/.test(a.getAttribute('style')),
            hrefNormalized: a.getAttribute('href') === '/a',
            opacity: /^0.5/.test(a.style.opacity),
            cssFloat: !!a.style.cssFloat,
            checkOn: !!input.value,
            optSelected: opt.selected,
            enctype: !!document.createElement('form').enctype,
            html5Clone: document.createElement('nav').cloneNode(true).outerHTML !== '<:nav></:nav>',
            boxModel: document.compatMode === 'CSS1Compat',
            deleteExpando: true,
            noCloneEvent: true,
            inlineBlockNeedsLayout: false,
            shrinkWrapBlocks: false,
            reliableMarginRight: true,
            boxSizingReliable: true,
            pixelPosition: false
        };
        input.checked = true;
        support.noCloneChecked = input.cloneNode(true).checked;
        select.disabled = true;
        support.optDisabled = !opt.disabled;
        try {
            delete div.test;
        } catch (e) {
            support.deleteExpando = false;
        }
        input = document.createElement('input');
        input.setAttribute('value', '');
        support.input = input.getAttribute('value') === '';
        input.value = 't';
        input.setAttribute('type', 'radio');
        support.radioValue = input.value === 't';
        input.setAttribute('checked', 't');
        input.setAttribute('name', 't');
        fragment = document.createDocumentFragment();
        fragment.appendChild(input);
        support.appendChecked = input.checked;
        support.checkClone = fragment.cloneNode(true).cloneNode(true).lastChild.checked;
        if (div.attachEvent) {
            div.attachEvent('onclick', function () {
                support.noCloneEvent = false;
            });
            div.cloneNode(true).click();
        }
        for (i in {
                submit: true,
                change: true,
                focusin: true
            }) {
            div.setAttribute(eventName = 'on' + i, 't');
            support[i + 'Bubbles'] = eventName in window || div.attributes[eventName].expando === false;
        }
        div.style.backgroundClip = 'content-box';
        div.cloneNode(true).style.backgroundClip = '';
        support.clearCloneStyle = div.style.backgroundClip === 'content-box';
        jQuery(function () {
            var container, marginDiv, tds, divReset = 'padding:0;margin:0;border:0;display:block;box-sizing:content-box;-moz-box-sizing:content-box;-webkit-box-sizing:content-box;', body = document.getElementsByTagName('body')[0];
            if (!body) {
                return;
            }
            container = document.createElement('div');
            container.style.cssText = 'border:0;width:0;height:0;position:absolute;top:0;left:-9999px;margin-top:1px';
            body.appendChild(container).appendChild(div);
            div.innerHTML = '<table><tr><td></td><td>t</td></tr></table>';
            tds = div.getElementsByTagName('td');
            tds[0].style.cssText = 'padding:0;margin:0;border:0;display:none';
            isSupported = tds[0].offsetHeight === 0;
            tds[0].style.display = '';
            tds[1].style.display = 'none';
            support.reliableHiddenOffsets = isSupported && tds[0].offsetHeight === 0;
            div.innerHTML = '';
            div.style.cssText = 'box-sizing:border-box;-moz-box-sizing:border-box;-webkit-box-sizing:border-box;padding:1px;border:1px;display:block;width:4px;margin-top:1%;position:absolute;top:1%;';
            support.boxSizing = div.offsetWidth === 4;
            support.doesNotIncludeMarginInBodyOffset = body.offsetTop !== 1;
            if (window.getComputedStyle) {
                support.pixelPosition = (window.getComputedStyle(div, null) || {}).top !== '1%';
                support.boxSizingReliable = (window.getComputedStyle(div, null) || { width: '4px' }).width === '4px';
                marginDiv = div.appendChild(document.createElement('div'));
                marginDiv.style.cssText = div.style.cssText = divReset;
                marginDiv.style.marginRight = marginDiv.style.width = '0';
                div.style.width = '1px';
                support.reliableMarginRight = !parseFloat((window.getComputedStyle(marginDiv, null) || {}).marginRight);
            }
            if (typeof div.style.zoom !== core_strundefined) {
                div.innerHTML = '';
                div.style.cssText = divReset + 'width:1px;padding:1px;display:inline;zoom:1';
                support.inlineBlockNeedsLayout = div.offsetWidth === 3;
                div.style.display = 'block';
                div.innerHTML = '<div></div>';
                div.firstChild.style.width = '5px';
                support.shrinkWrapBlocks = div.offsetWidth !== 3;
                if (support.inlineBlockNeedsLayout) {
                    body.style.zoom = 1;
                }
            }
            body.removeChild(container);
            container = div = tds = marginDiv = null;
        });
        all = select = fragment = opt = a = input = null;
        return support;
    }();
    var rbrace = /(?:\{[\s\S]*\}|\[[\s\S]*\])$/, rmultiDash = /([A-Z])/g;
    function internalData(elem, name, data, pvt) {
        if (!jQuery.acceptData(elem)) {
            return;
        }
        var thisCache, ret, internalKey = jQuery.expando, getByName = typeof name === 'string', isNode = elem.nodeType, cache = isNode ? jQuery.cache : elem, id = isNode ? elem[internalKey] : elem[internalKey] && internalKey;
        if ((!id || !cache[id] || !pvt && !cache[id].data) && getByName && data === undefined) {
            return;
        }
        if (!id) {
            if (isNode) {
                elem[internalKey] = id = core_deletedIds.pop() || jQuery.guid++;
            } else {
                id = internalKey;
            }
        }
        if (!cache[id]) {
            cache[id] = {};
            if (!isNode) {
                cache[id].toJSON = jQuery.noop;
            }
        }
        if (typeof name === 'object' || typeof name === 'function') {
            if (pvt) {
                cache[id] = jQuery.extend(cache[id], name);
            } else {
                cache[id].data = jQuery.extend(cache[id].data, name);
            }
        }
        thisCache = cache[id];
        if (!pvt) {
            if (!thisCache.data) {
                thisCache.data = {};
            }
            thisCache = thisCache.data;
        }
        if (data !== undefined) {
            thisCache[jQuery.camelCase(name)] = data;
        }
        if (getByName) {
            ret = thisCache[name];
            if (ret == null) {
                ret = thisCache[jQuery.camelCase(name)];
            }
        } else {
            ret = thisCache;
        }
        return ret;
    }
    function internalRemoveData(elem, name, pvt) {
        if (!jQuery.acceptData(elem)) {
            return;
        }
        var i, l, thisCache, isNode = elem.nodeType, cache = isNode ? jQuery.cache : elem, id = isNode ? elem[jQuery.expando] : jQuery.expando;
        if (!cache[id]) {
            return;
        }
        if (name) {
            thisCache = pvt ? cache[id] : cache[id].data;
            if (thisCache) {
                if (!jQuery.isArray(name)) {
                    if (name in thisCache) {
                        name = [name];
                    } else {
                        name = jQuery.camelCase(name);
                        if (name in thisCache) {
                            name = [name];
                        } else {
                            name = name.split(' ');
                        }
                    }
                } else {
                    name = name.concat(jQuery.map(name, jQuery.camelCase));
                }
                for (i = 0, l = name.length; i < l; i++) {
                    delete thisCache[name[i]];
                }
                if (!(pvt ? isEmptyDataObject : jQuery.isEmptyObject)(thisCache)) {
                    return;
                }
            }
        }
        if (!pvt) {
            delete cache[id].data;
            if (!isEmptyDataObject(cache[id])) {
                return;
            }
        }
        if (isNode) {
            jQuery.cleanData([elem], true);
        } else if (jQuery.support.deleteExpando || cache != cache.window) {
            delete cache[id];
        } else {
            cache[id] = null;
        }
    }
    jQuery.extend({
        cache: {},
        expando: 'jQuery' + (core_version + Math.random()).replace(/\D/g, ''),
        noData: {
            embed: true,
            object: 'clsid:D27CDB6E-AE6D-11cf-96B8-444553540000',
            applet: true
        },
        hasData: function (elem) {
            elem = elem.nodeType ? jQuery.cache[elem[jQuery.expando]] : elem[jQuery.expando];
            return !!elem && !isEmptyDataObject(elem);
        },
        data: function (elem, name, data) {
            return internalData(elem, name, data);
        },
        removeData: function (elem, name) {
            return internalRemoveData(elem, name);
        },
        _data: function (elem, name, data) {
            return internalData(elem, name, data, true);
        },
        _removeData: function (elem, name) {
            return internalRemoveData(elem, name, true);
        },
        acceptData: function (elem) {
            if (elem.nodeType && elem.nodeType !== 1 && elem.nodeType !== 9) {
                return false;
            }
            var noData = elem.nodeName && jQuery.noData[elem.nodeName.toLowerCase()];
            return !noData || noData !== true && elem.getAttribute('classid') === noData;
        }
    });
    jQuery.fn.extend({
        data: function (key, value) {
            var attrs, name, elem = this[0], i = 0, data = null;
            if (key === undefined) {
                if (this.length) {
                    data = jQuery.data(elem);
                    if (elem.nodeType === 1 && !jQuery._data(elem, 'parsedAttrs')) {
                        attrs = elem.attributes;
                        for (; i < attrs.length; i++) {
                            name = attrs[i].name;
                            if (!name.indexOf('data-')) {
                                name = jQuery.camelCase(name.slice(5));
                                dataAttr(elem, name, data[name]);
                            }
                        }
                        jQuery._data(elem, 'parsedAttrs', true);
                    }
                }
                return data;
            }
            if (typeof key === 'object') {
                return this.each(function () {
                    jQuery.data(this, key);
                });
            }
            return jQuery.access(this, function (value) {
                if (value === undefined) {
                    return elem ? dataAttr(elem, key, jQuery.data(elem, key)) : null;
                }
                this.each(function () {
                    jQuery.data(this, key, value);
                });
            }, null, value, arguments.length > 1, null, true);
        },
        removeData: function (key) {
            return this.each(function () {
                jQuery.removeData(this, key);
            });
        }
    });
    function dataAttr(elem, key, data) {
        if (data === undefined && elem.nodeType === 1) {
            var name = 'data-' + key.replace(rmultiDash, '-$1').toLowerCase();
            data = elem.getAttribute(name);
            if (typeof data === 'string') {
                try {
                    data = data === 'true' ? true : data === 'false' ? false : data === 'null' ? null : +data + '' === data ? +data : rbrace.test(data) ? jQuery.parseJSON(data) : data;
                } catch (e) {
                }
                jQuery.data(elem, key, data);
            } else {
                data = undefined;
            }
        }
        return data;
    }
    function isEmptyDataObject(obj) {
        var name;
        for (name in obj) {
            if (name === 'data' && jQuery.isEmptyObject(obj[name])) {
                continue;
            }
            if (name !== 'toJSON') {
                return false;
            }
        }
        return true;
    }
    jQuery.extend({
        queue: function (elem, type, data) {
            var queue;
            if (elem) {
                type = (type || 'fx') + 'queue';
                queue = jQuery._data(elem, type);
                if (data) {
                    if (!queue || jQuery.isArray(data)) {
                        queue = jQuery._data(elem, type, jQuery.makeArray(data));
                    } else {
                        queue.push(data);
                    }
                }
                return queue || [];
            }
        },
        dequeue: function (elem, type) {
            type = type || 'fx';
            var queue = jQuery.queue(elem, type), startLength = queue.length, fn = queue.shift(), hooks = jQuery._queueHooks(elem, type), next = function () {
                    jQuery.dequeue(elem, type);
                };
            if (fn === 'inprogress') {
                fn = queue.shift();
                startLength--;
            }
            hooks.cur = fn;
            if (fn) {
                if (type === 'fx') {
                    queue.unshift('inprogress');
                }
                delete hooks.stop;
                fn.call(elem, next, hooks);
            }
            if (!startLength && hooks) {
                hooks.empty.fire();
            }
        },
        _queueHooks: function (elem, type) {
            var key = type + 'queueHooks';
            return jQuery._data(elem, key) || jQuery._data(elem, key, {
                empty: jQuery.Callbacks('once memory').add(function () {
                    jQuery._removeData(elem, type + 'queue');
                    jQuery._removeData(elem, key);
                })
            });
        }
    });
    jQuery.fn.extend({
        queue: function (type, data) {
            var setter = 2;
            if (typeof type !== 'string') {
                data = type;
                type = 'fx';
                setter--;
            }
            if (arguments.length < setter) {
                return jQuery.queue(this[0], type);
            }
            return data === undefined ? this : this.each(function () {
                var queue = jQuery.queue(this, type, data);
                jQuery._queueHooks(this, type);
                if (type === 'fx' && queue[0] !== 'inprogress') {
                    jQuery.dequeue(this, type);
                }
            });
        },
        dequeue: function (type) {
            return this.each(function () {
                jQuery.dequeue(this, type);
            });
        },
        delay: function (time, type) {
            time = jQuery.fx ? jQuery.fx.speeds[time] || time : time;
            type = type || 'fx';
            return this.queue(type, function (next, hooks) {
                var timeout = setTimeout(next, time);
                hooks.stop = function () {
                    clearTimeout(timeout);
                };
            });
        },
        clearQueue: function (type) {
            return this.queue(type || 'fx', []);
        },
        promise: function (type, obj) {
            var tmp, count = 1, defer = jQuery.Deferred(), elements = this, i = this.length, resolve = function () {
                    if (!--count) {
                        defer.resolveWith(elements, [elements]);
                    }
                };
            if (typeof type !== 'string') {
                obj = type;
                type = undefined;
            }
            type = type || 'fx';
            while (i--) {
                tmp = jQuery._data(elements[i], type + 'queueHooks');
                if (tmp && tmp.empty) {
                    count++;
                    tmp.empty.add(resolve);
                }
            }
            resolve();
            return defer.promise(obj);
        }
    });
    var nodeHook, boolHook, rclass = /[\t\r\n]/g, rreturn = /\r/g, rfocusable = /^(?:input|select|textarea|button|object)$/i, rclickable = /^(?:a|area)$/i, rboolean = /^(?:checked|selected|autofocus|autoplay|async|controls|defer|disabled|hidden|loop|multiple|open|readonly|required|scoped)$/i, ruseDefault = /^(?:checked|selected)$/i, getSetAttribute = jQuery.support.getSetAttribute, getSetInput = jQuery.support.input;
    jQuery.fn.extend({
        attr: function (name, value) {
            return jQuery.access(this, jQuery.attr, name, value, arguments.length > 1);
        },
        removeAttr: function (name) {
            return this.each(function () {
                jQuery.removeAttr(this, name);
            });
        },
        prop: function (name, value) {
            return jQuery.access(this, jQuery.prop, name, value, arguments.length > 1);
        },
        removeProp: function (name) {
            name = jQuery.propFix[name] || name;
            return this.each(function () {
                try {
                    this[name] = undefined;
                    delete this[name];
                } catch (e) {
                }
            });
        },
        addClass: function (value) {
            var classes, elem, cur, clazz, j, i = 0, len = this.length, proceed = typeof value === 'string' && value;
            if (jQuery.isFunction(value)) {
                return this.each(function (j) {
                    jQuery(this).addClass(value.call(this, j, this.className));
                });
            }
            if (proceed) {
                classes = (value || '').match(core_rnotwhite) || [];
                for (; i < len; i++) {
                    elem = this[i];
                    cur = elem.nodeType === 1 && (elem.className ? (' ' + elem.className + ' ').replace(rclass, ' ') : ' ');
                    if (cur) {
                        j = 0;
                        while (clazz = classes[j++]) {
                            if (cur.indexOf(' ' + clazz + ' ') < 0) {
                                cur += clazz + ' ';
                            }
                        }
                        elem.className = jQuery.trim(cur);
                    }
                }
            }
            return this;
        },
        removeClass: function (value) {
            var classes, elem, cur, clazz, j, i = 0, len = this.length, proceed = arguments.length === 0 || typeof value === 'string' && value;
            if (jQuery.isFunction(value)) {
                return this.each(function (j) {
                    jQuery(this).removeClass(value.call(this, j, this.className));
                });
            }
            if (proceed) {
                classes = (value || '').match(core_rnotwhite) || [];
                for (; i < len; i++) {
                    elem = this[i];
                    cur = elem.nodeType === 1 && (elem.className ? (' ' + elem.className + ' ').replace(rclass, ' ') : '');
                    if (cur) {
                        j = 0;
                        while (clazz = classes[j++]) {
                            while (cur.indexOf(' ' + clazz + ' ') >= 0) {
                                cur = cur.replace(' ' + clazz + ' ', ' ');
                            }
                        }
                        elem.className = value ? jQuery.trim(cur) : '';
                    }
                }
            }
            return this;
        },
        toggleClass: function (value, stateVal) {
            var type = typeof value, isBool = typeof stateVal === 'boolean';
            if (jQuery.isFunction(value)) {
                return this.each(function (i) {
                    jQuery(this).toggleClass(value.call(this, i, this.className, stateVal), stateVal);
                });
            }
            return this.each(function () {
                if (type === 'string') {
                    var className, i = 0, self = jQuery(this), state = stateVal, classNames = value.match(core_rnotwhite) || [];
                    while (className = classNames[i++]) {
                        state = isBool ? state : !self.hasClass(className);
                        self[state ? 'addClass' : 'removeClass'](className);
                    }
                } else if (type === core_strundefined || type === 'boolean') {
                    if (this.className) {
                        jQuery._data(this, '__className__', this.className);
                    }
                    this.className = this.className || value === false ? '' : jQuery._data(this, '__className__') || '';
                }
            });
        },
        hasClass: function (selector) {
            var className = ' ' + selector + ' ', i = 0, l = this.length;
            for (; i < l; i++) {
                if (this[i].nodeType === 1 && (' ' + this[i].className + ' ').replace(rclass, ' ').indexOf(className) >= 0) {
                    return true;
                }
            }
            return false;
        },
        val: function (value) {
            var ret, hooks, isFunction, elem = this[0];
            if (!arguments.length) {
                if (elem) {
                    hooks = jQuery.valHooks[elem.type] || jQuery.valHooks[elem.nodeName.toLowerCase()];
                    if (hooks && 'get' in hooks && (ret = hooks.get(elem, 'value')) !== undefined) {
                        return ret;
                    }
                    ret = elem.value;
                    return typeof ret === 'string' ? ret.replace(rreturn, '') : ret == null ? '' : ret;
                }
                return;
            }
            isFunction = jQuery.isFunction(value);
            return this.each(function (i) {
                var val, self = jQuery(this);
                if (this.nodeType !== 1) {
                    return;
                }
                if (isFunction) {
                    val = value.call(this, i, self.val());
                } else {
                    val = value;
                }
                if (val == null) {
                    val = '';
                } else if (typeof val === 'number') {
                    val += '';
                } else if (jQuery.isArray(val)) {
                    val = jQuery.map(val, function (value) {
                        return value == null ? '' : value + '';
                    });
                }
                hooks = jQuery.valHooks[this.type] || jQuery.valHooks[this.nodeName.toLowerCase()];
                if (!hooks || !('set' in hooks) || hooks.set(this, val, 'value') === undefined) {
                    this.value = val;
                }
            });
        }
    });
    jQuery.extend({
        valHooks: {
            option: {
                get: function (elem) {
                    var val = elem.attributes.value;
                    return !val || val.specified ? elem.value : elem.text;
                }
            },
            select: {
                get: function (elem) {
                    var value, option, options = elem.options, index = elem.selectedIndex, one = elem.type === 'select-one' || index < 0, values = one ? null : [], max = one ? index + 1 : options.length, i = index < 0 ? max : one ? index : 0;
                    for (; i < max; i++) {
                        option = options[i];
                        if ((option.selected || i === index) && (jQuery.support.optDisabled ? !option.disabled : option.getAttribute('disabled') === null) && (!option.parentNode.disabled || !jQuery.nodeName(option.parentNode, 'optgroup'))) {
                            value = jQuery(option).val();
                            if (one) {
                                return value;
                            }
                            values.push(value);
                        }
                    }
                    return values;
                },
                set: function (elem, value) {
                    var values = jQuery.makeArray(value);
                    jQuery(elem).find('option').each(function () {
                        this.selected = jQuery.inArray(jQuery(this).val(), values) >= 0;
                    });
                    if (!values.length) {
                        elem.selectedIndex = -1;
                    }
                    return values;
                }
            }
        },
        attr: function (elem, name, value) {
            var hooks, notxml, ret, nType = elem.nodeType;
            if (!elem || nType === 3 || nType === 8 || nType === 2) {
                return;
            }
            if (typeof elem.getAttribute === core_strundefined) {
                return jQuery.prop(elem, name, value);
            }
            notxml = nType !== 1 || !jQuery.isXMLDoc(elem);
            if (notxml) {
                name = name.toLowerCase();
                hooks = jQuery.attrHooks[name] || (rboolean.test(name) ? boolHook : nodeHook);
            }
            if (value !== undefined) {
                if (value === null) {
                    jQuery.removeAttr(elem, name);
                } else if (hooks && notxml && 'set' in hooks && (ret = hooks.set(elem, value, name)) !== undefined) {
                    return ret;
                } else {
                    elem.setAttribute(name, value + '');
                    return value;
                }
            } else if (hooks && notxml && 'get' in hooks && (ret = hooks.get(elem, name)) !== null) {
                return ret;
            } else {
                if (typeof elem.getAttribute !== core_strundefined) {
                    ret = elem.getAttribute(name);
                }
                return ret == null ? undefined : ret;
            }
        },
        removeAttr: function (elem, value) {
            var name, propName, i = 0, attrNames = value && value.match(core_rnotwhite);
            if (attrNames && elem.nodeType === 1) {
                while (name = attrNames[i++]) {
                    propName = jQuery.propFix[name] || name;
                    if (rboolean.test(name)) {
                        if (!getSetAttribute && ruseDefault.test(name)) {
                            elem[jQuery.camelCase('default-' + name)] = elem[propName] = false;
                        } else {
                            elem[propName] = false;
                        }
                    } else {
                        jQuery.attr(elem, name, '');
                    }
                    elem.removeAttribute(getSetAttribute ? name : propName);
                }
            }
        },
        attrHooks: {
            type: {
                set: function (elem, value) {
                    if (!jQuery.support.radioValue && value === 'radio' && jQuery.nodeName(elem, 'input')) {
                        var val = elem.value;
                        elem.setAttribute('type', value);
                        if (val) {
                            elem.value = val;
                        }
                        return value;
                    }
                }
            }
        },
        propFix: {
            tabindex: 'tabIndex',
            readonly: 'readOnly',
            'for': 'htmlFor',
            'class': 'className',
            maxlength: 'maxLength',
            cellspacing: 'cellSpacing',
            cellpadding: 'cellPadding',
            rowspan: 'rowSpan',
            colspan: 'colSpan',
            usemap: 'useMap',
            frameborder: 'frameBorder',
            contenteditable: 'contentEditable'
        },
        prop: function (elem, name, value) {
            var ret, hooks, notxml, nType = elem.nodeType;
            if (!elem || nType === 3 || nType === 8 || nType === 2) {
                return;
            }
            notxml = nType !== 1 || !jQuery.isXMLDoc(elem);
            if (notxml) {
                name = jQuery.propFix[name] || name;
                hooks = jQuery.propHooks[name];
            }
            if (value !== undefined) {
                if (hooks && 'set' in hooks && (ret = hooks.set(elem, value, name)) !== undefined) {
                    return ret;
                } else {
                    return elem[name] = value;
                }
            } else {
                if (hooks && 'get' in hooks && (ret = hooks.get(elem, name)) !== null) {
                    return ret;
                } else {
                    return elem[name];
                }
            }
        },
        propHooks: {
            tabIndex: {
                get: function (elem) {
                    var attributeNode = elem.getAttributeNode('tabindex');
                    return attributeNode && attributeNode.specified ? parseInt(attributeNode.value, 10) : rfocusable.test(elem.nodeName) || rclickable.test(elem.nodeName) && elem.href ? 0 : undefined;
                }
            }
        }
    });
    boolHook = {
        get: function (elem, name) {
            var prop = jQuery.prop(elem, name), attr = typeof prop === 'boolean' && elem.getAttribute(name), detail = typeof prop === 'boolean' ? getSetInput && getSetAttribute ? attr != null : ruseDefault.test(name) ? elem[jQuery.camelCase('default-' + name)] : !!attr : elem.getAttributeNode(name);
            return detail && detail.value !== false ? name.toLowerCase() : undefined;
        },
        set: function (elem, value, name) {
            if (value === false) {
                jQuery.removeAttr(elem, name);
            } else if (getSetInput && getSetAttribute || !ruseDefault.test(name)) {
                elem.setAttribute(!getSetAttribute && jQuery.propFix[name] || name, name);
            } else {
                elem[jQuery.camelCase('default-' + name)] = elem[name] = true;
            }
            return name;
        }
    };
    if (!getSetInput || !getSetAttribute) {
        jQuery.attrHooks.value = {
            get: function (elem, name) {
                var ret = elem.getAttributeNode(name);
                return jQuery.nodeName(elem, 'input') ? elem.defaultValue : ret && ret.specified ? ret.value : undefined;
            },
            set: function (elem, value, name) {
                if (jQuery.nodeName(elem, 'input')) {
                    elem.defaultValue = value;
                } else {
                    return nodeHook && nodeHook.set(elem, value, name);
                }
            }
        };
    }
    if (!getSetAttribute) {
        nodeHook = jQuery.valHooks.button = {
            get: function (elem, name) {
                var ret = elem.getAttributeNode(name);
                return ret && (name === 'id' || name === 'name' || name === 'coords' ? ret.value !== '' : ret.specified) ? ret.value : undefined;
            },
            set: function (elem, value, name) {
                var ret = elem.getAttributeNode(name);
                if (!ret) {
                    elem.setAttributeNode(ret = elem.ownerDocument.createAttribute(name));
                }
                ret.value = value += '';
                return name === 'value' || value === elem.getAttribute(name) ? value : undefined;
            }
        };
        jQuery.attrHooks.contenteditable = {
            get: nodeHook.get,
            set: function (elem, value, name) {
                nodeHook.set(elem, value === '' ? false : value, name);
            }
        };
        jQuery.each([
            'width',
            'height'
        ], function (i, name) {
            jQuery.attrHooks[name] = jQuery.extend(jQuery.attrHooks[name], {
                set: function (elem, value) {
                    if (value === '') {
                        elem.setAttribute(name, 'auto');
                        return value;
                    }
                }
            });
        });
    }
    if (!jQuery.support.hrefNormalized) {
        jQuery.each([
            'href',
            'src',
            'width',
            'height'
        ], function (i, name) {
            jQuery.attrHooks[name] = jQuery.extend(jQuery.attrHooks[name], {
                get: function (elem) {
                    var ret = elem.getAttribute(name, 2);
                    return ret == null ? undefined : ret;
                }
            });
        });
        jQuery.each([
            'href',
            'src'
        ], function (i, name) {
            jQuery.propHooks[name] = {
                get: function (elem) {
                    return elem.getAttribute(name, 4);
                }
            };
        });
    }
    if (!jQuery.support.style) {
        jQuery.attrHooks.style = {
            get: function (elem) {
                return elem.style.cssText || undefined;
            },
            set: function (elem, value) {
                return elem.style.cssText = value + '';
            }
        };
    }
    if (!jQuery.support.optSelected) {
        jQuery.propHooks.selected = jQuery.extend(jQuery.propHooks.selected, {
            get: function (elem) {
                var parent = elem.parentNode;
                if (parent) {
                    parent.selectedIndex;
                    if (parent.parentNode) {
                        parent.parentNode.selectedIndex;
                    }
                }
                return null;
            }
        });
    }
    if (!jQuery.support.enctype) {
        jQuery.propFix.enctype = 'encoding';
    }
    if (!jQuery.support.checkOn) {
        jQuery.each([
            'radio',
            'checkbox'
        ], function () {
            jQuery.valHooks[this] = {
                get: function (elem) {
                    return elem.getAttribute('value') === null ? 'on' : elem.value;
                }
            };
        });
    }
    jQuery.each([
        'radio',
        'checkbox'
    ], function () {
        jQuery.valHooks[this] = jQuery.extend(jQuery.valHooks[this], {
            set: function (elem, value) {
                if (jQuery.isArray(value)) {
                    return elem.checked = jQuery.inArray(jQuery(elem).val(), value) >= 0;
                }
            }
        });
    });
    var rformElems = /^(?:input|select|textarea)$/i, rkeyEvent = /^key/, rmouseEvent = /^(?:mouse|contextmenu)|click/, rfocusMorph = /^(?:focusinfocus|focusoutblur)$/, rtypenamespace = /^([^.]*)(?:\.(.+)|)$/;
    function returnTrue() {
        return true;
    }
    function returnFalse() {
        return false;
    }
    jQuery.event = {
        global: {},
        add: function (elem, types, handler, data, selector) {
            var tmp, events, t, handleObjIn, special, eventHandle, handleObj, handlers, type, namespaces, origType, elemData = jQuery._data(elem);
            if (!elemData) {
                return;
            }
            if (handler.handler) {
                handleObjIn = handler;
                handler = handleObjIn.handler;
                selector = handleObjIn.selector;
            }
            if (!handler.guid) {
                handler.guid = jQuery.guid++;
            }
            if (!(events = elemData.events)) {
                events = elemData.events = {};
            }
            if (!(eventHandle = elemData.handle)) {
                eventHandle = elemData.handle = function (e) {
                    return typeof jQuery !== core_strundefined && (!e || jQuery.event.triggered !== e.type) ? jQuery.event.dispatch.apply(eventHandle.elem, arguments) : undefined;
                };
                eventHandle.elem = elem;
            }
            types = (types || '').match(core_rnotwhite) || [''];
            t = types.length;
            while (t--) {
                tmp = rtypenamespace.exec(types[t]) || [];
                type = origType = tmp[1];
                namespaces = (tmp[2] || '').split('.').sort();
                special = jQuery.event.special[type] || {};
                type = (selector ? special.delegateType : special.bindType) || type;
                special = jQuery.event.special[type] || {};
                handleObj = jQuery.extend({
                    type: type,
                    origType: origType,
                    data: data,
                    handler: handler,
                    guid: handler.guid,
                    selector: selector,
                    needsContext: selector && jQuery.expr.match.needsContext.test(selector),
                    namespace: namespaces.join('.')
                }, handleObjIn);
                if (!(handlers = events[type])) {
                    handlers = events[type] = [];
                    handlers.delegateCount = 0;
                    if (!special.setup || special.setup.call(elem, data, namespaces, eventHandle) === false) {
                        if (elem.addEventListener) {
                            elem.addEventListener(type, eventHandle, false);
                        } else if (elem.attachEvent) {
                            elem.attachEvent('on' + type, eventHandle);
                        }
                    }
                }
                if (special.add) {
                    special.add.call(elem, handleObj);
                    if (!handleObj.handler.guid) {
                        handleObj.handler.guid = handler.guid;
                    }
                }
                if (selector) {
                    handlers.splice(handlers.delegateCount++, 0, handleObj);
                } else {
                    handlers.push(handleObj);
                }
                jQuery.event.global[type] = true;
            }
            elem = null;
        },
        remove: function (elem, types, handler, selector, mappedTypes) {
            var j, handleObj, tmp, origCount, t, events, special, handlers, type, namespaces, origType, elemData = jQuery.hasData(elem) && jQuery._data(elem);
            if (!elemData || !(events = elemData.events)) {
                return;
            }
            types = (types || '').match(core_rnotwhite) || [''];
            t = types.length;
            while (t--) {
                tmp = rtypenamespace.exec(types[t]) || [];
                type = origType = tmp[1];
                namespaces = (tmp[2] || '').split('.').sort();
                if (!type) {
                    for (type in events) {
                        jQuery.event.remove(elem, type + types[t], handler, selector, true);
                    }
                    continue;
                }
                special = jQuery.event.special[type] || {};
                type = (selector ? special.delegateType : special.bindType) || type;
                handlers = events[type] || [];
                tmp = tmp[2] && new RegExp('(^|\\.)' + namespaces.join('\\.(?:.*\\.|)') + '(\\.|$)');
                origCount = j = handlers.length;
                while (j--) {
                    handleObj = handlers[j];
                    if ((mappedTypes || origType === handleObj.origType) && (!handler || handler.guid === handleObj.guid) && (!tmp || tmp.test(handleObj.namespace)) && (!selector || selector === handleObj.selector || selector === '**' && handleObj.selector)) {
                        handlers.splice(j, 1);
                        if (handleObj.selector) {
                            handlers.delegateCount--;
                        }
                        if (special.remove) {
                            special.remove.call(elem, handleObj);
                        }
                    }
                }
                if (origCount && !handlers.length) {
                    if (!special.teardown || special.teardown.call(elem, namespaces, elemData.handle) === false) {
                        jQuery.removeEvent(elem, type, elemData.handle);
                    }
                    delete events[type];
                }
            }
            if (jQuery.isEmptyObject(events)) {
                delete elemData.handle;
                jQuery._removeData(elem, 'events');
            }
        },
        trigger: function (event, data, elem, onlyHandlers) {
            var handle, ontype, cur, bubbleType, special, tmp, i, eventPath = [elem || document], type = core_hasOwn.call(event, 'type') ? event.type : event, namespaces = core_hasOwn.call(event, 'namespace') ? event.namespace.split('.') : [];
            cur = tmp = elem = elem || document;
            if (elem.nodeType === 3 || elem.nodeType === 8) {
                return;
            }
            if (rfocusMorph.test(type + jQuery.event.triggered)) {
                return;
            }
            if (type.indexOf('.') >= 0) {
                namespaces = type.split('.');
                type = namespaces.shift();
                namespaces.sort();
            }
            ontype = type.indexOf(':') < 0 && 'on' + type;
            event = event[jQuery.expando] ? event : new jQuery.Event(type, typeof event === 'object' && event);
            event.isTrigger = true;
            event.namespace = namespaces.join('.');
            event.namespace_re = event.namespace ? new RegExp('(^|\\.)' + namespaces.join('\\.(?:.*\\.|)') + '(\\.|$)') : null;
            event.result = undefined;
            if (!event.target) {
                event.target = elem;
            }
            data = data == null ? [event] : jQuery.makeArray(data, [event]);
            special = jQuery.event.special[type] || {};
            if (!onlyHandlers && special.trigger && special.trigger.apply(elem, data) === false) {
                return;
            }
            if (!onlyHandlers && !special.noBubble && !jQuery.isWindow(elem)) {
                bubbleType = special.delegateType || type;
                if (!rfocusMorph.test(bubbleType + type)) {
                    cur = cur.parentNode;
                }
                for (; cur; cur = cur.parentNode) {
                    eventPath.push(cur);
                    tmp = cur;
                }
                if (tmp === (elem.ownerDocument || document)) {
                    eventPath.push(tmp.defaultView || tmp.parentWindow || window);
                }
            }
            i = 0;
            while ((cur = eventPath[i++]) && !event.isPropagationStopped()) {
                event.type = i > 1 ? bubbleType : special.bindType || type;
                handle = (jQuery._data(cur, 'events') || {})[event.type] && jQuery._data(cur, 'handle');
                if (handle) {
                    handle.apply(cur, data);
                }
                handle = ontype && cur[ontype];
                if (handle && jQuery.acceptData(cur) && handle.apply && handle.apply(cur, data) === false) {
                    event.preventDefault();
                }
            }
            event.type = type;
            if (!onlyHandlers && !event.isDefaultPrevented()) {
                if ((!special._default || special._default.apply(elem.ownerDocument, data) === false) && !(type === 'click' && jQuery.nodeName(elem, 'a')) && jQuery.acceptData(elem)) {
                    if (ontype && elem[type] && !jQuery.isWindow(elem)) {
                        tmp = elem[ontype];
                        if (tmp) {
                            elem[ontype] = null;
                        }
                        jQuery.event.triggered = type;
                        try {
                            elem[type]();
                        } catch (e) {
                        }
                        jQuery.event.triggered = undefined;
                        if (tmp) {
                            elem[ontype] = tmp;
                        }
                    }
                }
            }
            return event.result;
        },
        dispatch: function (event) {
            event = jQuery.event.fix(event);
            var i, ret, handleObj, matched, j, handlerQueue = [], args = core_slice.call(arguments), handlers = (jQuery._data(this, 'events') || {})[event.type] || [], special = jQuery.event.special[event.type] || {};
            args[0] = event;
            event.delegateTarget = this;
            if (special.preDispatch && special.preDispatch.call(this, event) === false) {
                return;
            }
            handlerQueue = jQuery.event.handlers.call(this, event, handlers);
            i = 0;
            while ((matched = handlerQueue[i++]) && !event.isPropagationStopped()) {
                event.currentTarget = matched.elem;
                j = 0;
                while ((handleObj = matched.handlers[j++]) && !event.isImmediatePropagationStopped()) {
                    if (!event.namespace_re || event.namespace_re.test(handleObj.namespace)) {
                        event.handleObj = handleObj;
                        event.data = handleObj.data;
                        ret = ((jQuery.event.special[handleObj.origType] || {}).handle || handleObj.handler).apply(matched.elem, args);
                        if (ret !== undefined) {
                            if ((event.result = ret) === false) {
                                event.preventDefault();
                                event.stopPropagation();
                            }
                        }
                    }
                }
            }
            if (special.postDispatch) {
                special.postDispatch.call(this, event);
            }
            return event.result;
        },
        handlers: function (event, handlers) {
            var sel, handleObj, matches, i, handlerQueue = [], delegateCount = handlers.delegateCount, cur = event.target;
            if (delegateCount && cur.nodeType && (!event.button || event.type !== 'click')) {
                for (; cur != this; cur = cur.parentNode || this) {
                    if (cur.nodeType === 1 && (cur.disabled !== true || event.type !== 'click')) {
                        matches = [];
                        for (i = 0; i < delegateCount; i++) {
                            handleObj = handlers[i];
                            sel = handleObj.selector + ' ';
                            if (matches[sel] === undefined) {
                                matches[sel] = handleObj.needsContext ? jQuery(sel, this).index(cur) >= 0 : jQuery.find(sel, this, null, [cur]).length;
                            }
                            if (matches[sel]) {
                                matches.push(handleObj);
                            }
                        }
                        if (matches.length) {
                            handlerQueue.push({
                                elem: cur,
                                handlers: matches
                            });
                        }
                    }
                }
            }
            if (delegateCount < handlers.length) {
                handlerQueue.push({
                    elem: this,
                    handlers: handlers.slice(delegateCount)
                });
            }
            return handlerQueue;
        },
        fix: function (event) {
            if (event[jQuery.expando]) {
                return event;
            }
            var i, prop, copy, type = event.type, originalEvent = event, fixHook = this.fixHooks[type];
            if (!fixHook) {
                this.fixHooks[type] = fixHook = rmouseEvent.test(type) ? this.mouseHooks : rkeyEvent.test(type) ? this.keyHooks : {};
            }
            copy = fixHook.props ? this.props.concat(fixHook.props) : this.props;
            event = new jQuery.Event(originalEvent);
            i = copy.length;
            while (i--) {
                prop = copy[i];
                event[prop] = originalEvent[prop];
            }
            if (!event.target) {
                event.target = originalEvent.srcElement || document;
            }
            if (event.target.nodeType === 3) {
                event.target = event.target.parentNode;
            }
            event.metaKey = !!event.metaKey;
            return fixHook.filter ? fixHook.filter(event, originalEvent) : event;
        },
        props: 'altKey bubbles cancelable ctrlKey currentTarget eventPhase metaKey relatedTarget shiftKey target timeStamp view which'.split(' '),
        fixHooks: {},
        keyHooks: {
            props: 'char charCode key keyCode'.split(' '),
            filter: function (event, original) {
                if (event.which == null) {
                    event.which = original.charCode != null ? original.charCode : original.keyCode;
                }
                return event;
            }
        },
        mouseHooks: {
            props: 'button buttons clientX clientY fromElement offsetX offsetY pageX pageY screenX screenY toElement'.split(' '),
            filter: function (event, original) {
                var body, eventDoc, doc, button = original.button, fromElement = original.fromElement;
                if (event.pageX == null && original.clientX != null) {
                    eventDoc = event.target.ownerDocument || document;
                    doc = eventDoc.documentElement;
                    body = eventDoc.body;
                    event.pageX = original.clientX + (doc && doc.scrollLeft || body && body.scrollLeft || 0) - (doc && doc.clientLeft || body && body.clientLeft || 0);
                    event.pageY = original.clientY + (doc && doc.scrollTop || body && body.scrollTop || 0) - (doc && doc.clientTop || body && body.clientTop || 0);
                }
                if (!event.relatedTarget && fromElement) {
                    event.relatedTarget = fromElement === event.target ? original.toElement : fromElement;
                }
                if (!event.which && button !== undefined) {
                    event.which = button & 1 ? 1 : button & 2 ? 3 : button & 4 ? 2 : 0;
                }
                return event;
            }
        },
        special: {
            load: { noBubble: true },
            click: {
                trigger: function () {
                    if (jQuery.nodeName(this, 'input') && this.type === 'checkbox' && this.click) {
                        this.click();
                        return false;
                    }
                }
            },
            focus: {
                trigger: function () {
                    if (this !== document.activeElement && this.focus) {
                        try {
                            this.focus();
                            return false;
                        } catch (e) {
                        }
                    }
                },
                delegateType: 'focusin'
            },
            blur: {
                trigger: function () {
                    if (this === document.activeElement && this.blur) {
                        this.blur();
                        return false;
                    }
                },
                delegateType: 'focusout'
            },
            beforeunload: {
                postDispatch: function (event) {
                    if (event.result !== undefined) {
                        event.originalEvent.returnValue = event.result;
                    }
                }
            }
        },
        simulate: function (type, elem, event, bubble) {
            var e = jQuery.extend(new jQuery.Event(), event, {
                    type: type,
                    isSimulated: true,
                    originalEvent: {}
                });
            if (bubble) {
                jQuery.event.trigger(e, null, elem);
            } else {
                jQuery.event.dispatch.call(elem, e);
            }
            if (e.isDefaultPrevented()) {
                event.preventDefault();
            }
        }
    };
    jQuery.removeEvent = document.removeEventListener ? function (elem, type, handle) {
        if (elem.removeEventListener) {
            elem.removeEventListener(type, handle, false);
        }
    } : function (elem, type, handle) {
        var name = 'on' + type;
        if (elem.detachEvent) {
            if (typeof elem[name] === core_strundefined) {
                elem[name] = null;
            }
            elem.detachEvent(name, handle);
        }
    };
    jQuery.Event = function (src, props) {
        if (!(this instanceof jQuery.Event)) {
            return new jQuery.Event(src, props);
        }
        if (src && src.type) {
            this.originalEvent = src;
            this.type = src.type;
            this.isDefaultPrevented = src.defaultPrevented || src.returnValue === false || src.getPreventDefault && src.getPreventDefault() ? returnTrue : returnFalse;
        } else {
            this.type = src;
        }
        if (props) {
            jQuery.extend(this, props);
        }
        this.timeStamp = src && src.timeStamp || jQuery.now();
        this[jQuery.expando] = true;
    };
    jQuery.Event.prototype = {
        isDefaultPrevented: returnFalse,
        isPropagationStopped: returnFalse,
        isImmediatePropagationStopped: returnFalse,
        preventDefault: function () {
            var e = this.originalEvent;
            this.isDefaultPrevented = returnTrue;
            if (!e) {
                return;
            }
            if (e.preventDefault) {
                e.preventDefault();
            } else {
                e.returnValue = false;
            }
        },
        stopPropagation: function () {
            var e = this.originalEvent;
            this.isPropagationStopped = returnTrue;
            if (!e) {
                return;
            }
            if (e.stopPropagation) {
                e.stopPropagation();
            }
            e.cancelBubble = true;
        },
        stopImmediatePropagation: function () {
            this.isImmediatePropagationStopped = returnTrue;
            this.stopPropagation();
        }
    };
    jQuery.each({
        mouseenter: 'mouseover',
        mouseleave: 'mouseout'
    }, function (orig, fix) {
        jQuery.event.special[orig] = {
            delegateType: fix,
            bindType: fix,
            handle: function (event) {
                var ret, target = this, related = event.relatedTarget, handleObj = event.handleObj;
                if (!related || related !== target && !jQuery.contains(target, related)) {
                    event.type = handleObj.origType;
                    ret = handleObj.handler.apply(this, arguments);
                    event.type = fix;
                }
                return ret;
            }
        };
    });
    if (!jQuery.support.submitBubbles) {
        jQuery.event.special.submit = {
            setup: function () {
                if (jQuery.nodeName(this, 'form')) {
                    return false;
                }
                jQuery.event.add(this, 'click._submit keypress._submit', function (e) {
                    var elem = e.target, form = jQuery.nodeName(elem, 'input') || jQuery.nodeName(elem, 'button') ? elem.form : undefined;
                    if (form && !jQuery._data(form, 'submitBubbles')) {
                        jQuery.event.add(form, 'submit._submit', function (event) {
                            event._submit_bubble = true;
                        });
                        jQuery._data(form, 'submitBubbles', true);
                    }
                });
            },
            postDispatch: function (event) {
                if (event._submit_bubble) {
                    delete event._submit_bubble;
                    if (this.parentNode && !event.isTrigger) {
                        jQuery.event.simulate('submit', this.parentNode, event, true);
                    }
                }
            },
            teardown: function () {
                if (jQuery.nodeName(this, 'form')) {
                    return false;
                }
                jQuery.event.remove(this, '._submit');
            }
        };
    }
    if (!jQuery.support.changeBubbles) {
        jQuery.event.special.change = {
            setup: function () {
                if (rformElems.test(this.nodeName)) {
                    if (this.type === 'checkbox' || this.type === 'radio') {
                        jQuery.event.add(this, 'propertychange._change', function (event) {
                            if (event.originalEvent.propertyName === 'checked') {
                                this._just_changed = true;
                            }
                        });
                        jQuery.event.add(this, 'click._change', function (event) {
                            if (this._just_changed && !event.isTrigger) {
                                this._just_changed = false;
                            }
                            jQuery.event.simulate('change', this, event, true);
                        });
                    }
                    return false;
                }
                jQuery.event.add(this, 'beforeactivate._change', function (e) {
                    var elem = e.target;
                    if (rformElems.test(elem.nodeName) && !jQuery._data(elem, 'changeBubbles')) {
                        jQuery.event.add(elem, 'change._change', function (event) {
                            if (this.parentNode && !event.isSimulated && !event.isTrigger) {
                                jQuery.event.simulate('change', this.parentNode, event, true);
                            }
                        });
                        jQuery._data(elem, 'changeBubbles', true);
                    }
                });
            },
            handle: function (event) {
                var elem = event.target;
                if (this !== elem || event.isSimulated || event.isTrigger || elem.type !== 'radio' && elem.type !== 'checkbox') {
                    return event.handleObj.handler.apply(this, arguments);
                }
            },
            teardown: function () {
                jQuery.event.remove(this, '._change');
                return !rformElems.test(this.nodeName);
            }
        };
    }
    if (!jQuery.support.focusinBubbles) {
        jQuery.each({
            focus: 'focusin',
            blur: 'focusout'
        }, function (orig, fix) {
            var attaches = 0, handler = function (event) {
                    jQuery.event.simulate(fix, event.target, jQuery.event.fix(event), true);
                };
            jQuery.event.special[fix] = {
                setup: function () {
                    if (attaches++ === 0) {
                        document.addEventListener(orig, handler, true);
                    }
                },
                teardown: function () {
                    if (--attaches === 0) {
                        document.removeEventListener(orig, handler, true);
                    }
                }
            };
        });
    }
    jQuery.fn.extend({
        on: function (types, selector, data, fn, one) {
            var type, origFn;
            if (typeof types === 'object') {
                if (typeof selector !== 'string') {
                    data = data || selector;
                    selector = undefined;
                }
                for (type in types) {
                    this.on(type, selector, data, types[type], one);
                }
                return this;
            }
            if (data == null && fn == null) {
                fn = selector;
                data = selector = undefined;
            } else if (fn == null) {
                if (typeof selector === 'string') {
                    fn = data;
                    data = undefined;
                } else {
                    fn = data;
                    data = selector;
                    selector = undefined;
                }
            }
            if (fn === false) {
                fn = returnFalse;
            } else if (!fn) {
                return this;
            }
            if (one === 1) {
                origFn = fn;
                fn = function (event) {
                    jQuery().off(event);
                    return origFn.apply(this, arguments);
                };
                fn.guid = origFn.guid || (origFn.guid = jQuery.guid++);
            }
            return this.each(function () {
                jQuery.event.add(this, types, fn, data, selector);
            });
        },
        one: function (types, selector, data, fn) {
            return this.on(types, selector, data, fn, 1);
        },
        off: function (types, selector, fn) {
            var handleObj, type;
            if (types && types.preventDefault && types.handleObj) {
                handleObj = types.handleObj;
                jQuery(types.delegateTarget).off(handleObj.namespace ? handleObj.origType + '.' + handleObj.namespace : handleObj.origType, handleObj.selector, handleObj.handler);
                return this;
            }
            if (typeof types === 'object') {
                for (type in types) {
                    this.off(type, selector, types[type]);
                }
                return this;
            }
            if (selector === false || typeof selector === 'function') {
                fn = selector;
                selector = undefined;
            }
            if (fn === false) {
                fn = returnFalse;
            }
            return this.each(function () {
                jQuery.event.remove(this, types, fn, selector);
            });
        },
        bind: function (types, data, fn) {
            return this.on(types, null, data, fn);
        },
        unbind: function (types, fn) {
            return this.off(types, null, fn);
        },
        delegate: function (selector, types, data, fn) {
            return this.on(types, selector, data, fn);
        },
        undelegate: function (selector, types, fn) {
            return arguments.length === 1 ? this.off(selector, '**') : this.off(types, selector || '**', fn);
        },
        trigger: function (type, data) {
            return this.each(function () {
                jQuery.event.trigger(type, data, this);
            });
        },
        triggerHandler: function (type, data) {
            var elem = this[0];
            if (elem) {
                return jQuery.event.trigger(type, data, elem, true);
            }
        }
    });
    (function (window, undefined) {
        var i, cachedruns, Expr, getText, isXML, compile, hasDuplicate, outermostContext, setDocument, document, docElem, documentIsXML, rbuggyQSA, rbuggyMatches, matches, contains, sortOrder, expando = 'sizzle' + -new Date(), preferredDoc = window.document, support = {}, dirruns = 0, done = 0, classCache = createCache(), tokenCache = createCache(), compilerCache = createCache(), strundefined = typeof undefined, MAX_NEGATIVE = 1 << 31, arr = [], pop = arr.pop, push = arr.push, slice = arr.slice, indexOf = arr.indexOf || function (elem) {
                var i = 0, len = this.length;
                for (; i < len; i++) {
                    if (this[i] === elem) {
                        return i;
                    }
                }
                return -1;
            }, whitespace = '[\\x20\\t\\r\\n\\f]', characterEncoding = '(?:\\\\.|[\\w-]|[^\\x00-\\xa0])+', identifier = characterEncoding.replace('w', 'w#'), operators = '([*^$|!~]?=)', attributes = '\\[' + whitespace + '*(' + characterEncoding + ')' + whitespace + '*(?:' + operators + whitespace + '*(?:([\'"])((?:\\\\.|[^\\\\])*?)\\3|(' + identifier + ')|)|)' + whitespace + '*\\]', pseudos = ':(' + characterEncoding + ')(?:\\((([\'"])((?:\\\\.|[^\\\\])*?)\\3|((?:\\\\.|[^\\\\()[\\]]|' + attributes.replace(3, 8) + ')*)|.*)\\)|)', rtrim = new RegExp('^' + whitespace + '+|((?:^|[^\\\\])(?:\\\\.)*)' + whitespace + '+$', 'g'), rcomma = new RegExp('^' + whitespace + '*,' + whitespace + '*'), rcombinators = new RegExp('^' + whitespace + '*([\\x20\\t\\r\\n\\f>+~])' + whitespace + '*'), rpseudo = new RegExp(pseudos), ridentifier = new RegExp('^' + identifier + '$'), matchExpr = {
                ID: new RegExp('^#(' + characterEncoding + ')'),
                CLASS: new RegExp('^\\.(' + characterEncoding + ')'),
                NAME: new RegExp('^\\[name=[\'"]?(' + characterEncoding + ')[\'"]?\\]'),
                TAG: new RegExp('^(' + characterEncoding.replace('w', 'w*') + ')'),
                ATTR: new RegExp('^' + attributes),
                PSEUDO: new RegExp('^' + pseudos),
                CHILD: new RegExp('^:(only|first|last|nth|nth-last)-(child|of-type)(?:\\(' + whitespace + '*(even|odd|(([+-]|)(\\d*)n|)' + whitespace + '*(?:([+-]|)' + whitespace + '*(\\d+)|))' + whitespace + '*\\)|)', 'i'),
                needsContext: new RegExp('^' + whitespace + '*[>+~]|:(even|odd|eq|gt|lt|nth|first|last)(?:\\(' + whitespace + '*((?:-\\d)?\\d*)' + whitespace + '*\\)|)(?=[^-]|$)', 'i')
            }, rsibling = /[\x20\t\r\n\f]*[+~]/, rnative = /^[^{]+\{\s*\[native code/, rquickExpr = /^(?:#([\w-]+)|(\w+)|\.([\w-]+))$/, rinputs = /^(?:input|select|textarea|button)$/i, rheader = /^h\d$/i, rescape = /'|\\/g, rattributeQuotes = /\=[\x20\t\r\n\f]*([^'"\]]*)[\x20\t\r\n\f]*\]/g, runescape = /\\([\da-fA-F]{1,6}[\x20\t\r\n\f]?|.)/g, funescape = function (_, escaped) {
                var high = '0x' + escaped - 65536;
                return high !== high ? escaped : high < 0 ? String.fromCharCode(high + 65536) : String.fromCharCode(high >> 10 | 55296, high & 1023 | 56320);
            };
        try {
            slice.call(preferredDoc.documentElement.childNodes, 0)[0].nodeType;
        } catch (e) {
            slice = function (i) {
                var elem, results = [];
                while (elem = this[i++]) {
                    results.push(elem);
                }
                return results;
            };
        }
        function isNative(fn) {
            return rnative.test(fn + '');
        }
        function createCache() {
            var cache, keys = [];
            return cache = function (key, value) {
                if (keys.push(key += ' ') > Expr.cacheLength) {
                    delete cache[keys.shift()];
                }
                return cache[key] = value;
            };
        }
        function markFunction(fn) {
            fn[expando] = true;
            return fn;
        }
        function assert(fn) {
            var div = document.createElement('div');
            try {
                return fn(div);
            } catch (e) {
                return false;
            } finally {
                div = null;
            }
        }
        function Sizzle(selector, context, results, seed) {
            var match, elem, m, nodeType, i, groups, old, nid, newContext, newSelector;
            if ((context ? context.ownerDocument || context : preferredDoc) !== document) {
                setDocument(context);
            }
            context = context || document;
            results = results || [];
            if (!selector || typeof selector !== 'string') {
                return results;
            }
            if ((nodeType = context.nodeType) !== 1 && nodeType !== 9) {
                return [];
            }
            if (!documentIsXML && !seed) {
                if (match = rquickExpr.exec(selector)) {
                    if (m = match[1]) {
                        if (nodeType === 9) {
                            elem = context.getElementById(m);
                            if (elem && elem.parentNode) {
                                if (elem.id === m) {
                                    results.push(elem);
                                    return results;
                                }
                            } else {
                                return results;
                            }
                        } else {
                            if (context.ownerDocument && (elem = context.ownerDocument.getElementById(m)) && contains(context, elem) && elem.id === m) {
                                results.push(elem);
                                return results;
                            }
                        }
                    } else if (match[2]) {
                        push.apply(results, slice.call(context.getElementsByTagName(selector), 0));
                        return results;
                    } else if ((m = match[3]) && support.getByClassName && context.getElementsByClassName) {
                        push.apply(results, slice.call(context.getElementsByClassName(m), 0));
                        return results;
                    }
                }
                if (support.qsa && !rbuggyQSA.test(selector)) {
                    old = true;
                    nid = expando;
                    newContext = context;
                    newSelector = nodeType === 9 && selector;
                    if (nodeType === 1 && context.nodeName.toLowerCase() !== 'object') {
                        groups = tokenize(selector);
                        if (old = context.getAttribute('id')) {
                            nid = old.replace(rescape, '\\$&');
                        } else {
                            context.setAttribute('id', nid);
                        }
                        nid = '[id=\'' + nid + '\'] ';
                        i = groups.length;
                        while (i--) {
                            groups[i] = nid + toSelector(groups[i]);
                        }
                        newContext = rsibling.test(selector) && context.parentNode || context;
                        newSelector = groups.join(',');
                    }
                    if (newSelector) {
                        try {
                            push.apply(results, slice.call(newContext.querySelectorAll(newSelector), 0));
                            return results;
                        } catch (qsaError) {
                        } finally {
                            if (!old) {
                                context.removeAttribute('id');
                            }
                        }
                    }
                }
            }
            return select(selector.replace(rtrim, '$1'), context, results, seed);
        }
        isXML = Sizzle.isXML = function (elem) {
            var documentElement = elem && (elem.ownerDocument || elem).documentElement;
            return documentElement ? documentElement.nodeName !== 'HTML' : false;
        };
        setDocument = Sizzle.setDocument = function (node) {
            var doc = node ? node.ownerDocument || node : preferredDoc;
            if (doc === document || doc.nodeType !== 9 || !doc.documentElement) {
                return document;
            }
            document = doc;
            docElem = doc.documentElement;
            documentIsXML = isXML(doc);
            support.tagNameNoComments = assert(function (div) {
                div.appendChild(doc.createComment(''));
                return !div.getElementsByTagName('*').length;
            });
            support.attributes = assert(function (div) {
                div.innerHTML = '<select></select>';
                var type = typeof div.lastChild.getAttribute('multiple');
                return type !== 'boolean' && type !== 'string';
            });
            support.getByClassName = assert(function (div) {
                div.innerHTML = '<div class=\'hidden e\'></div><div class=\'hidden\'></div>';
                if (!div.getElementsByClassName || !div.getElementsByClassName('e').length) {
                    return false;
                }
                div.lastChild.className = 'e';
                return div.getElementsByClassName('e').length === 2;
            });
            support.getByName = assert(function (div) {
                div.id = expando + 0;
                div.innerHTML = '<a name=\'' + expando + '\'></a><div name=\'' + expando + '\'></div>';
                docElem.insertBefore(div, docElem.firstChild);
                var pass = doc.getElementsByName && doc.getElementsByName(expando).length === 2 + doc.getElementsByName(expando + 0).length;
                support.getIdNotName = !doc.getElementById(expando);
                docElem.removeChild(div);
                return pass;
            });
            Expr.attrHandle = assert(function (div) {
                div.innerHTML = '<a href=\'#\'></a>';
                return div.firstChild && typeof div.firstChild.getAttribute !== strundefined && div.firstChild.getAttribute('href') === '#';
            }) ? {} : {
                href: function (elem) {
                    return elem.getAttribute('href', 2);
                },
                type: function (elem) {
                    return elem.getAttribute('type');
                }
            };
            if (support.getIdNotName) {
                Expr.find['ID'] = function (id, context) {
                    if (typeof context.getElementById !== strundefined && !documentIsXML) {
                        var m = context.getElementById(id);
                        return m && m.parentNode ? [m] : [];
                    }
                };
                Expr.filter['ID'] = function (id) {
                    var attrId = id.replace(runescape, funescape);
                    return function (elem) {
                        return elem.getAttribute('id') === attrId;
                    };
                };
            } else {
                Expr.find['ID'] = function (id, context) {
                    if (typeof context.getElementById !== strundefined && !documentIsXML) {
                        var m = context.getElementById(id);
                        return m ? m.id === id || typeof m.getAttributeNode !== strundefined && m.getAttributeNode('id').value === id ? [m] : undefined : [];
                    }
                };
                Expr.filter['ID'] = function (id) {
                    var attrId = id.replace(runescape, funescape);
                    return function (elem) {
                        var node = typeof elem.getAttributeNode !== strundefined && elem.getAttributeNode('id');
                        return node && node.value === attrId;
                    };
                };
            }
            Expr.find['TAG'] = support.tagNameNoComments ? function (tag, context) {
                if (typeof context.getElementsByTagName !== strundefined) {
                    return context.getElementsByTagName(tag);
                }
            } : function (tag, context) {
                var elem, tmp = [], i = 0, results = context.getElementsByTagName(tag);
                if (tag === '*') {
                    while (elem = results[i++]) {
                        if (elem.nodeType === 1) {
                            tmp.push(elem);
                        }
                    }
                    return tmp;
                }
                return results;
            };
            Expr.find['NAME'] = support.getByName && function (tag, context) {
                if (typeof context.getElementsByName !== strundefined) {
                    return context.getElementsByName(name);
                }
            };
            Expr.find['CLASS'] = support.getByClassName && function (className, context) {
                if (typeof context.getElementsByClassName !== strundefined && !documentIsXML) {
                    return context.getElementsByClassName(className);
                }
            };
            rbuggyMatches = [];
            rbuggyQSA = [':focus'];
            if (support.qsa = isNative(doc.querySelectorAll)) {
                assert(function (div) {
                    div.innerHTML = '<select><option selected=\'\'></option></select>';
                    if (!div.querySelectorAll('[selected]').length) {
                        rbuggyQSA.push('\\[' + whitespace + '*(?:checked|disabled|ismap|multiple|readonly|selected|value)');
                    }
                    if (!div.querySelectorAll(':checked').length) {
                        rbuggyQSA.push(':checked');
                    }
                });
                assert(function (div) {
                    div.innerHTML = '<input type=\'hidden\' i=\'\'/>';
                    if (div.querySelectorAll('[i^=\'\']').length) {
                        rbuggyQSA.push('[*^$]=' + whitespace + '*(?:""|\'\')');
                    }
                    if (!div.querySelectorAll(':enabled').length) {
                        rbuggyQSA.push(':enabled', ':disabled');
                    }
                    div.querySelectorAll('*,:x');
                    rbuggyQSA.push(',.*:');
                });
            }
            if (support.matchesSelector = isNative(matches = docElem.matchesSelector || docElem.mozMatchesSelector || docElem.webkitMatchesSelector || docElem.oMatchesSelector || docElem.msMatchesSelector)) {
                assert(function (div) {
                    support.disconnectedMatch = matches.call(div, 'div');
                    matches.call(div, '[s!=\'\']:x');
                    rbuggyMatches.push('!=', pseudos);
                });
            }
            rbuggyQSA = new RegExp(rbuggyQSA.join('|'));
            rbuggyMatches = new RegExp(rbuggyMatches.join('|'));
            contains = isNative(docElem.contains) || docElem.compareDocumentPosition ? function (a, b) {
                var adown = a.nodeType === 9 ? a.documentElement : a, bup = b && b.parentNode;
                return a === bup || !!(bup && bup.nodeType === 1 && (adown.contains ? adown.contains(bup) : a.compareDocumentPosition && a.compareDocumentPosition(bup) & 16));
            } : function (a, b) {
                if (b) {
                    while (b = b.parentNode) {
                        if (b === a) {
                            return true;
                        }
                    }
                }
                return false;
            };
            sortOrder = docElem.compareDocumentPosition ? function (a, b) {
                var compare;
                if (a === b) {
                    hasDuplicate = true;
                    return 0;
                }
                if (compare = b.compareDocumentPosition && a.compareDocumentPosition && a.compareDocumentPosition(b)) {
                    if (compare & 1 || a.parentNode && a.parentNode.nodeType === 11) {
                        if (a === doc || contains(preferredDoc, a)) {
                            return -1;
                        }
                        if (b === doc || contains(preferredDoc, b)) {
                            return 1;
                        }
                        return 0;
                    }
                    return compare & 4 ? -1 : 1;
                }
                return a.compareDocumentPosition ? -1 : 1;
            } : function (a, b) {
                var cur, i = 0, aup = a.parentNode, bup = b.parentNode, ap = [a], bp = [b];
                if (a === b) {
                    hasDuplicate = true;
                    return 0;
                } else if (!aup || !bup) {
                    return a === doc ? -1 : b === doc ? 1 : aup ? -1 : bup ? 1 : 0;
                } else if (aup === bup) {
                    return siblingCheck(a, b);
                }
                cur = a;
                while (cur = cur.parentNode) {
                    ap.unshift(cur);
                }
                cur = b;
                while (cur = cur.parentNode) {
                    bp.unshift(cur);
                }
                while (ap[i] === bp[i]) {
                    i++;
                }
                return i ? siblingCheck(ap[i], bp[i]) : ap[i] === preferredDoc ? -1 : bp[i] === preferredDoc ? 1 : 0;
            };
            hasDuplicate = false;
            [
                0,
                0
            ].sort(sortOrder);
            support.detectDuplicates = hasDuplicate;
            return document;
        };
        Sizzle.matches = function (expr, elements) {
            return Sizzle(expr, null, null, elements);
        };
        Sizzle.matchesSelector = function (elem, expr) {
            if ((elem.ownerDocument || elem) !== document) {
                setDocument(elem);
            }
            expr = expr.replace(rattributeQuotes, '=\'$1\']');
            if (support.matchesSelector && !documentIsXML && (!rbuggyMatches || !rbuggyMatches.test(expr)) && !rbuggyQSA.test(expr)) {
                try {
                    var ret = matches.call(elem, expr);
                    if (ret || support.disconnectedMatch || elem.document && elem.document.nodeType !== 11) {
                        return ret;
                    }
                } catch (e) {
                }
            }
            return Sizzle(expr, document, null, [elem]).length > 0;
        };
        Sizzle.contains = function (context, elem) {
            if ((context.ownerDocument || context) !== document) {
                setDocument(context);
            }
            return contains(context, elem);
        };
        Sizzle.attr = function (elem, name) {
            var val;
            if ((elem.ownerDocument || elem) !== document) {
                setDocument(elem);
            }
            if (!documentIsXML) {
                name = name.toLowerCase();
            }
            if (val = Expr.attrHandle[name]) {
                return val(elem);
            }
            if (documentIsXML || support.attributes) {
                return elem.getAttribute(name);
            }
            return ((val = elem.getAttributeNode(name)) || elem.getAttribute(name)) && elem[name] === true ? name : val && val.specified ? val.value : null;
        };
        Sizzle.error = function (msg) {
            throw new Error('Syntax error, unrecognized expression: ' + msg);
        };
        Sizzle.uniqueSort = function (results) {
            var elem, duplicates = [], i = 1, j = 0;
            hasDuplicate = !support.detectDuplicates;
            results.sort(sortOrder);
            if (hasDuplicate) {
                for (; elem = results[i]; i++) {
                    if (elem === results[i - 1]) {
                        j = duplicates.push(i);
                    }
                }
                while (j--) {
                    results.splice(duplicates[j], 1);
                }
            }
            return results;
        };
        function siblingCheck(a, b) {
            var cur = b && a, diff = cur && (~b.sourceIndex || MAX_NEGATIVE) - (~a.sourceIndex || MAX_NEGATIVE);
            if (diff) {
                return diff;
            }
            if (cur) {
                while (cur = cur.nextSibling) {
                    if (cur === b) {
                        return -1;
                    }
                }
            }
            return a ? 1 : -1;
        }
        function createInputPseudo(type) {
            return function (elem) {
                var name = elem.nodeName.toLowerCase();
                return name === 'input' && elem.type === type;
            };
        }
        function createButtonPseudo(type) {
            return function (elem) {
                var name = elem.nodeName.toLowerCase();
                return (name === 'input' || name === 'button') && elem.type === type;
            };
        }
        function createPositionalPseudo(fn) {
            return markFunction(function (argument) {
                argument = +argument;
                return markFunction(function (seed, matches) {
                    var j, matchIndexes = fn([], seed.length, argument), i = matchIndexes.length;
                    while (i--) {
                        if (seed[j = matchIndexes[i]]) {
                            seed[j] = !(matches[j] = seed[j]);
                        }
                    }
                });
            });
        }
        getText = Sizzle.getText = function (elem) {
            var node, ret = '', i = 0, nodeType = elem.nodeType;
            if (!nodeType) {
                for (; node = elem[i]; i++) {
                    ret += getText(node);
                }
            } else if (nodeType === 1 || nodeType === 9 || nodeType === 11) {
                if (typeof elem.textContent === 'string') {
                    return elem.textContent;
                } else {
                    for (elem = elem.firstChild; elem; elem = elem.nextSibling) {
                        ret += getText(elem);
                    }
                }
            } else if (nodeType === 3 || nodeType === 4) {
                return elem.nodeValue;
            }
            return ret;
        };
        Expr = Sizzle.selectors = {
            cacheLength: 50,
            createPseudo: markFunction,
            match: matchExpr,
            find: {},
            relative: {
                '>': {
                    dir: 'parentNode',
                    first: true
                },
                ' ': { dir: 'parentNode' },
                '+': {
                    dir: 'previousSibling',
                    first: true
                },
                '~': { dir: 'previousSibling' }
            },
            preFilter: {
                ATTR: function (match) {
                    match[1] = match[1].replace(runescape, funescape);
                    match[3] = (match[4] || match[5] || '').replace(runescape, funescape);
                    if (match[2] === '~=') {
                        match[3] = ' ' + match[3] + ' ';
                    }
                    return match.slice(0, 4);
                },
                CHILD: function (match) {
                    match[1] = match[1].toLowerCase();
                    if (match[1].slice(0, 3) === 'nth') {
                        if (!match[3]) {
                            Sizzle.error(match[0]);
                        }
                        match[4] = +(match[4] ? match[5] + (match[6] || 1) : 2 * (match[3] === 'even' || match[3] === 'odd'));
                        match[5] = +(match[7] + match[8] || match[3] === 'odd');
                    } else if (match[3]) {
                        Sizzle.error(match[0]);
                    }
                    return match;
                },
                PSEUDO: function (match) {
                    var excess, unquoted = !match[5] && match[2];
                    if (matchExpr['CHILD'].test(match[0])) {
                        return null;
                    }
                    if (match[4]) {
                        match[2] = match[4];
                    } else if (unquoted && rpseudo.test(unquoted) && (excess = tokenize(unquoted, true)) && (excess = unquoted.indexOf(')', unquoted.length - excess) - unquoted.length)) {
                        match[0] = match[0].slice(0, excess);
                        match[2] = unquoted.slice(0, excess);
                    }
                    return match.slice(0, 3);
                }
            },
            filter: {
                TAG: function (nodeName) {
                    if (nodeName === '*') {
                        return function () {
                            return true;
                        };
                    }
                    nodeName = nodeName.replace(runescape, funescape).toLowerCase();
                    return function (elem) {
                        return elem.nodeName && elem.nodeName.toLowerCase() === nodeName;
                    };
                },
                CLASS: function (className) {
                    var pattern = classCache[className + ' '];
                    return pattern || (pattern = new RegExp('(^|' + whitespace + ')' + className + '(' + whitespace + '|$)')) && classCache(className, function (elem) {
                        return pattern.test(elem.className || typeof elem.getAttribute !== strundefined && elem.getAttribute('class') || '');
                    });
                },
                ATTR: function (name, operator, check) {
                    return function (elem) {
                        var result = Sizzle.attr(elem, name);
                        if (result == null) {
                            return operator === '!=';
                        }
                        if (!operator) {
                            return true;
                        }
                        result += '';
                        return operator === '=' ? result === check : operator === '!=' ? result !== check : operator === '^=' ? check && result.indexOf(check) === 0 : operator === '*=' ? check && result.indexOf(check) > -1 : operator === '$=' ? check && result.slice(-check.length) === check : operator === '~=' ? (' ' + result + ' ').indexOf(check) > -1 : operator === '|=' ? result === check || result.slice(0, check.length + 1) === check + '-' : false;
                    };
                },
                CHILD: function (type, what, argument, first, last) {
                    var simple = type.slice(0, 3) !== 'nth', forward = type.slice(-4) !== 'last', ofType = what === 'of-type';
                    return first === 1 && last === 0 ? function (elem) {
                        return !!elem.parentNode;
                    } : function (elem, context, xml) {
                        var cache, outerCache, node, diff, nodeIndex, start, dir = simple !== forward ? 'nextSibling' : 'previousSibling', parent = elem.parentNode, name = ofType && elem.nodeName.toLowerCase(), useCache = !xml && !ofType;
                        if (parent) {
                            if (simple) {
                                while (dir) {
                                    node = elem;
                                    while (node = node[dir]) {
                                        if (ofType ? node.nodeName.toLowerCase() === name : node.nodeType === 1) {
                                            return false;
                                        }
                                    }
                                    start = dir = type === 'only' && !start && 'nextSibling';
                                }
                                return true;
                            }
                            start = [forward ? parent.firstChild : parent.lastChild];
                            if (forward && useCache) {
                                outerCache = parent[expando] || (parent[expando] = {});
                                cache = outerCache[type] || [];
                                nodeIndex = cache[0] === dirruns && cache[1];
                                diff = cache[0] === dirruns && cache[2];
                                node = nodeIndex && parent.childNodes[nodeIndex];
                                while (node = ++nodeIndex && node && node[dir] || (diff = nodeIndex = 0) || start.pop()) {
                                    if (node.nodeType === 1 && ++diff && node === elem) {
                                        outerCache[type] = [
                                            dirruns,
                                            nodeIndex,
                                            diff
                                        ];
                                        break;
                                    }
                                }
                            } else if (useCache && (cache = (elem[expando] || (elem[expando] = {}))[type]) && cache[0] === dirruns) {
                                diff = cache[1];
                            } else {
                                while (node = ++nodeIndex && node && node[dir] || (diff = nodeIndex = 0) || start.pop()) {
                                    if ((ofType ? node.nodeName.toLowerCase() === name : node.nodeType === 1) && ++diff) {
                                        if (useCache) {
                                            (node[expando] || (node[expando] = {}))[type] = [
                                                dirruns,
                                                diff
                                            ];
                                        }
                                        if (node === elem) {
                                            break;
                                        }
                                    }
                                }
                            }
                            diff -= last;
                            return diff === first || diff % first === 0 && diff / first >= 0;
                        }
                    };
                },
                PSEUDO: function (pseudo, argument) {
                    var args, fn = Expr.pseudos[pseudo] || Expr.setFilters[pseudo.toLowerCase()] || Sizzle.error('unsupported pseudo: ' + pseudo);
                    if (fn[expando]) {
                        return fn(argument);
                    }
                    if (fn.length > 1) {
                        args = [
                            pseudo,
                            pseudo,
                            '',
                            argument
                        ];
                        return Expr.setFilters.hasOwnProperty(pseudo.toLowerCase()) ? markFunction(function (seed, matches) {
                            var idx, matched = fn(seed, argument), i = matched.length;
                            while (i--) {
                                idx = indexOf.call(seed, matched[i]);
                                seed[idx] = !(matches[idx] = matched[i]);
                            }
                        }) : function (elem) {
                            return fn(elem, 0, args);
                        };
                    }
                    return fn;
                }
            },
            pseudos: {
                not: markFunction(function (selector) {
                    var input = [], results = [], matcher = compile(selector.replace(rtrim, '$1'));
                    return matcher[expando] ? markFunction(function (seed, matches, context, xml) {
                        var elem, unmatched = matcher(seed, null, xml, []), i = seed.length;
                        while (i--) {
                            if (elem = unmatched[i]) {
                                seed[i] = !(matches[i] = elem);
                            }
                        }
                    }) : function (elem, context, xml) {
                        input[0] = elem;
                        matcher(input, null, xml, results);
                        return !results.pop();
                    };
                }),
                has: markFunction(function (selector) {
                    return function (elem) {
                        return Sizzle(selector, elem).length > 0;
                    };
                }),
                contains: markFunction(function (text) {
                    return function (elem) {
                        return (elem.textContent || elem.innerText || getText(elem)).indexOf(text) > -1;
                    };
                }),
                lang: markFunction(function (lang) {
                    if (!ridentifier.test(lang || '')) {
                        Sizzle.error('unsupported lang: ' + lang);
                    }
                    lang = lang.replace(runescape, funescape).toLowerCase();
                    return function (elem) {
                        var elemLang;
                        do {
                            if (elemLang = documentIsXML ? elem.getAttribute('xml:lang') || elem.getAttribute('lang') : elem.lang) {
                                elemLang = elemLang.toLowerCase();
                                return elemLang === lang || elemLang.indexOf(lang + '-') === 0;
                            }
                        } while ((elem = elem.parentNode) && elem.nodeType === 1);
                        return false;
                    };
                }),
                target: function (elem) {
                    var hash = window.location && window.location.hash;
                    return hash && hash.slice(1) === elem.id;
                },
                root: function (elem) {
                    return elem === docElem;
                },
                focus: function (elem) {
                    return elem === document.activeElement && (!document.hasFocus || document.hasFocus()) && !!(elem.type || elem.href || ~elem.tabIndex);
                },
                enabled: function (elem) {
                    return elem.disabled === false;
                },
                disabled: function (elem) {
                    return elem.disabled === true;
                },
                checked: function (elem) {
                    var nodeName = elem.nodeName.toLowerCase();
                    return nodeName === 'input' && !!elem.checked || nodeName === 'option' && !!elem.selected;
                },
                selected: function (elem) {
                    if (elem.parentNode) {
                        elem.parentNode.selectedIndex;
                    }
                    return elem.selected === true;
                },
                empty: function (elem) {
                    for (elem = elem.firstChild; elem; elem = elem.nextSibling) {
                        if (elem.nodeName > '@' || elem.nodeType === 3 || elem.nodeType === 4) {
                            return false;
                        }
                    }
                    return true;
                },
                parent: function (elem) {
                    return !Expr.pseudos['empty'](elem);
                },
                header: function (elem) {
                    return rheader.test(elem.nodeName);
                },
                input: function (elem) {
                    return rinputs.test(elem.nodeName);
                },
                button: function (elem) {
                    var name = elem.nodeName.toLowerCase();
                    return name === 'input' && elem.type === 'button' || name === 'button';
                },
                text: function (elem) {
                    var attr;
                    return elem.nodeName.toLowerCase() === 'input' && elem.type === 'text' && ((attr = elem.getAttribute('type')) == null || attr.toLowerCase() === elem.type);
                },
                first: createPositionalPseudo(function () {
                    return [0];
                }),
                last: createPositionalPseudo(function (matchIndexes, length) {
                    return [length - 1];
                }),
                eq: createPositionalPseudo(function (matchIndexes, length, argument) {
                    return [argument < 0 ? argument + length : argument];
                }),
                even: createPositionalPseudo(function (matchIndexes, length) {
                    var i = 0;
                    for (; i < length; i += 2) {
                        matchIndexes.push(i);
                    }
                    return matchIndexes;
                }),
                odd: createPositionalPseudo(function (matchIndexes, length) {
                    var i = 1;
                    for (; i < length; i += 2) {
                        matchIndexes.push(i);
                    }
                    return matchIndexes;
                }),
                lt: createPositionalPseudo(function (matchIndexes, length, argument) {
                    var i = argument < 0 ? argument + length : argument;
                    for (; --i >= 0;) {
                        matchIndexes.push(i);
                    }
                    return matchIndexes;
                }),
                gt: createPositionalPseudo(function (matchIndexes, length, argument) {
                    var i = argument < 0 ? argument + length : argument;
                    for (; ++i < length;) {
                        matchIndexes.push(i);
                    }
                    return matchIndexes;
                })
            }
        };
        for (i in {
                radio: true,
                checkbox: true,
                file: true,
                password: true,
                image: true
            }) {
            Expr.pseudos[i] = createInputPseudo(i);
        }
        for (i in {
                submit: true,
                reset: true
            }) {
            Expr.pseudos[i] = createButtonPseudo(i);
        }
        function tokenize(selector, parseOnly) {
            var matched, match, tokens, type, soFar, groups, preFilters, cached = tokenCache[selector + ' '];
            if (cached) {
                return parseOnly ? 0 : cached.slice(0);
            }
            soFar = selector;
            groups = [];
            preFilters = Expr.preFilter;
            while (soFar) {
                if (!matched || (match = rcomma.exec(soFar))) {
                    if (match) {
                        soFar = soFar.slice(match[0].length) || soFar;
                    }
                    groups.push(tokens = []);
                }
                matched = false;
                if (match = rcombinators.exec(soFar)) {
                    matched = match.shift();
                    tokens.push({
                        value: matched,
                        type: match[0].replace(rtrim, ' ')
                    });
                    soFar = soFar.slice(matched.length);
                }
                for (type in Expr.filter) {
                    if ((match = matchExpr[type].exec(soFar)) && (!preFilters[type] || (match = preFilters[type](match)))) {
                        matched = match.shift();
                        tokens.push({
                            value: matched,
                            type: type,
                            matches: match
                        });
                        soFar = soFar.slice(matched.length);
                    }
                }
                if (!matched) {
                    break;
                }
            }
            return parseOnly ? soFar.length : soFar ? Sizzle.error(selector) : tokenCache(selector, groups).slice(0);
        }
        function toSelector(tokens) {
            var i = 0, len = tokens.length, selector = '';
            for (; i < len; i++) {
                selector += tokens[i].value;
            }
            return selector;
        }
        function addCombinator(matcher, combinator, base) {
            var dir = combinator.dir, checkNonElements = base && dir === 'parentNode', doneName = done++;
            return combinator.first ? function (elem, context, xml) {
                while (elem = elem[dir]) {
                    if (elem.nodeType === 1 || checkNonElements) {
                        return matcher(elem, context, xml);
                    }
                }
            } : function (elem, context, xml) {
                var data, cache, outerCache, dirkey = dirruns + ' ' + doneName;
                if (xml) {
                    while (elem = elem[dir]) {
                        if (elem.nodeType === 1 || checkNonElements) {
                            if (matcher(elem, context, xml)) {
                                return true;
                            }
                        }
                    }
                } else {
                    while (elem = elem[dir]) {
                        if (elem.nodeType === 1 || checkNonElements) {
                            outerCache = elem[expando] || (elem[expando] = {});
                            if ((cache = outerCache[dir]) && cache[0] === dirkey) {
                                if ((data = cache[1]) === true || data === cachedruns) {
                                    return data === true;
                                }
                            } else {
                                cache = outerCache[dir] = [dirkey];
                                cache[1] = matcher(elem, context, xml) || cachedruns;
                                if (cache[1] === true) {
                                    return true;
                                }
                            }
                        }
                    }
                }
            };
        }
        function elementMatcher(matchers) {
            return matchers.length > 1 ? function (elem, context, xml) {
                var i = matchers.length;
                while (i--) {
                    if (!matchers[i](elem, context, xml)) {
                        return false;
                    }
                }
                return true;
            } : matchers[0];
        }
        function condense(unmatched, map, filter, context, xml) {
            var elem, newUnmatched = [], i = 0, len = unmatched.length, mapped = map != null;
            for (; i < len; i++) {
                if (elem = unmatched[i]) {
                    if (!filter || filter(elem, context, xml)) {
                        newUnmatched.push(elem);
                        if (mapped) {
                            map.push(i);
                        }
                    }
                }
            }
            return newUnmatched;
        }
        function setMatcher(preFilter, selector, matcher, postFilter, postFinder, postSelector) {
            if (postFilter && !postFilter[expando]) {
                postFilter = setMatcher(postFilter);
            }
            if (postFinder && !postFinder[expando]) {
                postFinder = setMatcher(postFinder, postSelector);
            }
            return markFunction(function (seed, results, context, xml) {
                var temp, i, elem, preMap = [], postMap = [], preexisting = results.length, elems = seed || multipleContexts(selector || '*', context.nodeType ? [context] : context, []), matcherIn = preFilter && (seed || !selector) ? condense(elems, preMap, preFilter, context, xml) : elems, matcherOut = matcher ? postFinder || (seed ? preFilter : preexisting || postFilter) ? [] : results : matcherIn;
                if (matcher) {
                    matcher(matcherIn, matcherOut, context, xml);
                }
                if (postFilter) {
                    temp = condense(matcherOut, postMap);
                    postFilter(temp, [], context, xml);
                    i = temp.length;
                    while (i--) {
                        if (elem = temp[i]) {
                            matcherOut[postMap[i]] = !(matcherIn[postMap[i]] = elem);
                        }
                    }
                }
                if (seed) {
                    if (postFinder || preFilter) {
                        if (postFinder) {
                            temp = [];
                            i = matcherOut.length;
                            while (i--) {
                                if (elem = matcherOut[i]) {
                                    temp.push(matcherIn[i] = elem);
                                }
                            }
                            postFinder(null, matcherOut = [], temp, xml);
                        }
                        i = matcherOut.length;
                        while (i--) {
                            if ((elem = matcherOut[i]) && (temp = postFinder ? indexOf.call(seed, elem) : preMap[i]) > -1) {
                                seed[temp] = !(results[temp] = elem);
                            }
                        }
                    }
                } else {
                    matcherOut = condense(matcherOut === results ? matcherOut.splice(preexisting, matcherOut.length) : matcherOut);
                    if (postFinder) {
                        postFinder(null, results, matcherOut, xml);
                    } else {
                        push.apply(results, matcherOut);
                    }
                }
            });
        }
        function matcherFromTokens(tokens) {
            var checkContext, matcher, j, len = tokens.length, leadingRelative = Expr.relative[tokens[0].type], implicitRelative = leadingRelative || Expr.relative[' '], i = leadingRelative ? 1 : 0, matchContext = addCombinator(function (elem) {
                    return elem === checkContext;
                }, implicitRelative, true), matchAnyContext = addCombinator(function (elem) {
                    return indexOf.call(checkContext, elem) > -1;
                }, implicitRelative, true), matchers = [function (elem, context, xml) {
                        return !leadingRelative && (xml || context !== outermostContext) || ((checkContext = context).nodeType ? matchContext(elem, context, xml) : matchAnyContext(elem, context, xml));
                    }];
            for (; i < len; i++) {
                if (matcher = Expr.relative[tokens[i].type]) {
                    matchers = [addCombinator(elementMatcher(matchers), matcher)];
                } else {
                    matcher = Expr.filter[tokens[i].type].apply(null, tokens[i].matches);
                    if (matcher[expando]) {
                        j = ++i;
                        for (; j < len; j++) {
                            if (Expr.relative[tokens[j].type]) {
                                break;
                            }
                        }
                        return setMatcher(i > 1 && elementMatcher(matchers), i > 1 && toSelector(tokens.slice(0, i - 1)).replace(rtrim, '$1'), matcher, i < j && matcherFromTokens(tokens.slice(i, j)), j < len && matcherFromTokens(tokens = tokens.slice(j)), j < len && toSelector(tokens));
                    }
                    matchers.push(matcher);
                }
            }
            return elementMatcher(matchers);
        }
        function matcherFromGroupMatchers(elementMatchers, setMatchers) {
            var matcherCachedRuns = 0, bySet = setMatchers.length > 0, byElement = elementMatchers.length > 0, superMatcher = function (seed, context, xml, results, expandContext) {
                    var elem, j, matcher, setMatched = [], matchedCount = 0, i = '0', unmatched = seed && [], outermost = expandContext != null, contextBackup = outermostContext, elems = seed || byElement && Expr.find['TAG']('*', expandContext && context.parentNode || context), dirrunsUnique = dirruns += contextBackup == null ? 1 : Math.random() || 0.1;
                    if (outermost) {
                        outermostContext = context !== document && context;
                        cachedruns = matcherCachedRuns;
                    }
                    for (; (elem = elems[i]) != null; i++) {
                        if (byElement && elem) {
                            j = 0;
                            while (matcher = elementMatchers[j++]) {
                                if (matcher(elem, context, xml)) {
                                    results.push(elem);
                                    break;
                                }
                            }
                            if (outermost) {
                                dirruns = dirrunsUnique;
                                cachedruns = ++matcherCachedRuns;
                            }
                        }
                        if (bySet) {
                            if (elem = !matcher && elem) {
                                matchedCount--;
                            }
                            if (seed) {
                                unmatched.push(elem);
                            }
                        }
                    }
                    matchedCount += i;
                    if (bySet && i !== matchedCount) {
                        j = 0;
                        while (matcher = setMatchers[j++]) {
                            matcher(unmatched, setMatched, context, xml);
                        }
                        if (seed) {
                            if (matchedCount > 0) {
                                while (i--) {
                                    if (!(unmatched[i] || setMatched[i])) {
                                        setMatched[i] = pop.call(results);
                                    }
                                }
                            }
                            setMatched = condense(setMatched);
                        }
                        push.apply(results, setMatched);
                        if (outermost && !seed && setMatched.length > 0 && matchedCount + setMatchers.length > 1) {
                            Sizzle.uniqueSort(results);
                        }
                    }
                    if (outermost) {
                        dirruns = dirrunsUnique;
                        outermostContext = contextBackup;
                    }
                    return unmatched;
                };
            return bySet ? markFunction(superMatcher) : superMatcher;
        }
        compile = Sizzle.compile = function (selector, group) {
            var i, setMatchers = [], elementMatchers = [], cached = compilerCache[selector + ' '];
            if (!cached) {
                if (!group) {
                    group = tokenize(selector);
                }
                i = group.length;
                while (i--) {
                    cached = matcherFromTokens(group[i]);
                    if (cached[expando]) {
                        setMatchers.push(cached);
                    } else {
                        elementMatchers.push(cached);
                    }
                }
                cached = compilerCache(selector, matcherFromGroupMatchers(elementMatchers, setMatchers));
            }
            return cached;
        };
        function multipleContexts(selector, contexts, results) {
            var i = 0, len = contexts.length;
            for (; i < len; i++) {
                Sizzle(selector, contexts[i], results);
            }
            return results;
        }
        function select(selector, context, results, seed) {
            var i, tokens, token, type, find, match = tokenize(selector);
            if (!seed) {
                if (match.length === 1) {
                    tokens = match[0] = match[0].slice(0);
                    if (tokens.length > 2 && (token = tokens[0]).type === 'ID' && context.nodeType === 9 && !documentIsXML && Expr.relative[tokens[1].type]) {
                        context = Expr.find['ID'](token.matches[0].replace(runescape, funescape), context)[0];
                        if (!context) {
                            return results;
                        }
                        selector = selector.slice(tokens.shift().value.length);
                    }
                    i = matchExpr['needsContext'].test(selector) ? 0 : tokens.length;
                    while (i--) {
                        token = tokens[i];
                        if (Expr.relative[type = token.type]) {
                            break;
                        }
                        if (find = Expr.find[type]) {
                            if (seed = find(token.matches[0].replace(runescape, funescape), rsibling.test(tokens[0].type) && context.parentNode || context)) {
                                tokens.splice(i, 1);
                                selector = seed.length && toSelector(tokens);
                                if (!selector) {
                                    push.apply(results, slice.call(seed, 0));
                                    return results;
                                }
                                break;
                            }
                        }
                    }
                }
            }
            compile(selector, match)(seed, context, documentIsXML, results, rsibling.test(selector));
            return results;
        }
        Expr.pseudos['nth'] = Expr.pseudos['eq'];
        function setFilters() {
        }
        Expr.filters = setFilters.prototype = Expr.pseudos;
        Expr.setFilters = new setFilters();
        setDocument();
        Sizzle.attr = jQuery.attr;
        jQuery.find = Sizzle;
        jQuery.expr = Sizzle.selectors;
        jQuery.expr[':'] = jQuery.expr.pseudos;
        jQuery.unique = Sizzle.uniqueSort;
        jQuery.text = Sizzle.getText;
        jQuery.isXMLDoc = Sizzle.isXML;
        jQuery.contains = Sizzle.contains;
    }(window));
    var runtil = /Until$/, rparentsprev = /^(?:parents|prev(?:Until|All))/, isSimple = /^.[^:#\[\.,]*$/, rneedsContext = jQuery.expr.match.needsContext, guaranteedUnique = {
            children: true,
            contents: true,
            next: true,
            prev: true
        };
    jQuery.fn.extend({
        find: function (selector) {
            var i, ret, self, len = this.length;
            if (typeof selector !== 'string') {
                self = this;
                return this.pushStack(jQuery(selector).filter(function () {
                    for (i = 0; i < len; i++) {
                        if (jQuery.contains(self[i], this)) {
                            return true;
                        }
                    }
                }));
            }
            ret = [];
            for (i = 0; i < len; i++) {
                jQuery.find(selector, this[i], ret);
            }
            ret = this.pushStack(len > 1 ? jQuery.unique(ret) : ret);
            ret.selector = (this.selector ? this.selector + ' ' : '') + selector;
            return ret;
        },
        has: function (target) {
            var i, targets = jQuery(target, this), len = targets.length;
            return this.filter(function () {
                for (i = 0; i < len; i++) {
                    if (jQuery.contains(this, targets[i])) {
                        return true;
                    }
                }
            });
        },
        not: function (selector) {
            return this.pushStack(winnow(this, selector, false));
        },
        filter: function (selector) {
            return this.pushStack(winnow(this, selector, true));
        },
        is: function (selector) {
            return !!selector && (typeof selector === 'string' ? rneedsContext.test(selector) ? jQuery(selector, this.context).index(this[0]) >= 0 : jQuery.filter(selector, this).length > 0 : this.filter(selector).length > 0);
        },
        closest: function (selectors, context) {
            var cur, i = 0, l = this.length, ret = [], pos = rneedsContext.test(selectors) || typeof selectors !== 'string' ? jQuery(selectors, context || this.context) : 0;
            for (; i < l; i++) {
                cur = this[i];
                while (cur && cur.ownerDocument && cur !== context && cur.nodeType !== 11) {
                    if (pos ? pos.index(cur) > -1 : jQuery.find.matchesSelector(cur, selectors)) {
                        ret.push(cur);
                        break;
                    }
                    cur = cur.parentNode;
                }
            }
            return this.pushStack(ret.length > 1 ? jQuery.unique(ret) : ret);
        },
        index: function (elem) {
            if (!elem) {
                return this[0] && this[0].parentNode ? this.first().prevAll().length : -1;
            }
            if (typeof elem === 'string') {
                return jQuery.inArray(this[0], jQuery(elem));
            }
            return jQuery.inArray(elem.jquery ? elem[0] : elem, this);
        },
        add: function (selector, context) {
            var set = typeof selector === 'string' ? jQuery(selector, context) : jQuery.makeArray(selector && selector.nodeType ? [selector] : selector), all = jQuery.merge(this.get(), set);
            return this.pushStack(jQuery.unique(all));
        },
        addBack: function (selector) {
            return this.add(selector == null ? this.prevObject : this.prevObject.filter(selector));
        }
    });
    jQuery.fn.andSelf = jQuery.fn.addBack;
    function sibling(cur, dir) {
        do {
            cur = cur[dir];
        } while (cur && cur.nodeType !== 1);
        return cur;
    }
    jQuery.each({
        parent: function (elem) {
            var parent = elem.parentNode;
            return parent && parent.nodeType !== 11 ? parent : null;
        },
        parents: function (elem) {
            return jQuery.dir(elem, 'parentNode');
        },
        parentsUntil: function (elem, i, until) {
            return jQuery.dir(elem, 'parentNode', until);
        },
        next: function (elem) {
            return sibling(elem, 'nextSibling');
        },
        prev: function (elem) {
            return sibling(elem, 'previousSibling');
        },
        nextAll: function (elem) {
            return jQuery.dir(elem, 'nextSibling');
        },
        prevAll: function (elem) {
            return jQuery.dir(elem, 'previousSibling');
        },
        nextUntil: function (elem, i, until) {
            return jQuery.dir(elem, 'nextSibling', until);
        },
        prevUntil: function (elem, i, until) {
            return jQuery.dir(elem, 'previousSibling', until);
        },
        siblings: function (elem) {
            return jQuery.sibling((elem.parentNode || {}).firstChild, elem);
        },
        children: function (elem) {
            return jQuery.sibling(elem.firstChild);
        },
        contents: function (elem) {
            return jQuery.nodeName(elem, 'iframe') ? elem.contentDocument || elem.contentWindow.document : jQuery.merge([], elem.childNodes);
        }
    }, function (name, fn) {
        jQuery.fn[name] = function (until, selector) {
            var ret = jQuery.map(this, fn, until);
            if (!runtil.test(name)) {
                selector = until;
            }
            if (selector && typeof selector === 'string') {
                ret = jQuery.filter(selector, ret);
            }
            ret = this.length > 1 && !guaranteedUnique[name] ? jQuery.unique(ret) : ret;
            if (this.length > 1 && rparentsprev.test(name)) {
                ret = ret.reverse();
            }
            return this.pushStack(ret);
        };
    });
    jQuery.extend({
        filter: function (expr, elems, not) {
            if (not) {
                expr = ':not(' + expr + ')';
            }
            return elems.length === 1 ? jQuery.find.matchesSelector(elems[0], expr) ? [elems[0]] : [] : jQuery.find.matches(expr, elems);
        },
        dir: function (elem, dir, until) {
            var matched = [], cur = elem[dir];
            while (cur && cur.nodeType !== 9 && (until === undefined || cur.nodeType !== 1 || !jQuery(cur).is(until))) {
                if (cur.nodeType === 1) {
                    matched.push(cur);
                }
                cur = cur[dir];
            }
            return matched;
        },
        sibling: function (n, elem) {
            var r = [];
            for (; n; n = n.nextSibling) {
                if (n.nodeType === 1 && n !== elem) {
                    r.push(n);
                }
            }
            return r;
        }
    });
    function winnow(elements, qualifier, keep) {
        qualifier = qualifier || 0;
        if (jQuery.isFunction(qualifier)) {
            return jQuery.grep(elements, function (elem, i) {
                var retVal = !!qualifier.call(elem, i, elem);
                return retVal === keep;
            });
        } else if (qualifier.nodeType) {
            return jQuery.grep(elements, function (elem) {
                return elem === qualifier === keep;
            });
        } else if (typeof qualifier === 'string') {
            var filtered = jQuery.grep(elements, function (elem) {
                    return elem.nodeType === 1;
                });
            if (isSimple.test(qualifier)) {
                return jQuery.filter(qualifier, filtered, !keep);
            } else {
                qualifier = jQuery.filter(qualifier, filtered);
            }
        }
        return jQuery.grep(elements, function (elem) {
            return jQuery.inArray(elem, qualifier) >= 0 === keep;
        });
    }
    function createSafeFragment(document) {
        var list = nodeNames.split('|'), safeFrag = document.createDocumentFragment();
        if (safeFrag.createElement) {
            while (list.length) {
                safeFrag.createElement(list.pop());
            }
        }
        return safeFrag;
    }
    var nodeNames = 'abbr|article|aside|audio|bdi|canvas|data|datalist|details|figcaption|figure|footer|' + 'header|hgroup|mark|meter|nav|output|progress|section|summary|time|video', rinlinejQuery = / jQuery\d+="(?:null|\d+)"/g, rnoshimcache = new RegExp('<(?:' + nodeNames + ')[\\s/>]', 'i'), rleadingWhitespace = /^\s+/, rxhtmlTag = /<(?!area|br|col|embed|hr|img|input|link|meta|param)(([\w:]+)[^>]*)\/>/gi, rtagName = /<([\w:]+)/, rtbody = /<tbody/i, rhtml = /<|&#?\w+;/, rnoInnerhtml = /<(?:script|style|link)/i, manipulation_rcheckableType = /^(?:checkbox|radio)$/i, rchecked = /checked\s*(?:[^=]|=\s*.checked.)/i, rscriptType = /^$|\/(?:java|ecma)script/i, rscriptTypeMasked = /^true\/(.*)/, rcleanScript = /^\s*<!(?:\[CDATA\[|--)|(?:\]\]|--)>\s*$/g, wrapMap = {
            option: [
                1,
                '<select multiple=\'multiple\'>',
                '</select>'
            ],
            legend: [
                1,
                '<fieldset>',
                '</fieldset>'
            ],
            area: [
                1,
                '<map>',
                '</map>'
            ],
            param: [
                1,
                '<object>',
                '</object>'
            ],
            thead: [
                1,
                '<table>',
                '</table>'
            ],
            tr: [
                2,
                '<table><tbody>',
                '</tbody></table>'
            ],
            col: [
                2,
                '<table><tbody></tbody><colgroup>',
                '</colgroup></table>'
            ],
            td: [
                3,
                '<table><tbody><tr>',
                '</tr></tbody></table>'
            ],
            _default: jQuery.support.htmlSerialize ? [
                0,
                '',
                ''
            ] : [
                1,
                'X<div>',
                '</div>'
            ]
        }, safeFragment = createSafeFragment(document), fragmentDiv = safeFragment.appendChild(document.createElement('div'));
    wrapMap.optgroup = wrapMap.option;
    wrapMap.tbody = wrapMap.tfoot = wrapMap.colgroup = wrapMap.caption = wrapMap.thead;
    wrapMap.th = wrapMap.td;
    jQuery.fn.extend({
        text: function (value) {
            return jQuery.access(this, function (value) {
                return value === undefined ? jQuery.text(this) : this.empty().append((this[0] && this[0].ownerDocument || document).createTextNode(value));
            }, null, value, arguments.length);
        },
        wrapAll: function (html) {
            if (jQuery.isFunction(html)) {
                return this.each(function (i) {
                    jQuery(this).wrapAll(html.call(this, i));
                });
            }
            if (this[0]) {
                var wrap = jQuery(html, this[0].ownerDocument).eq(0).clone(true);
                if (this[0].parentNode) {
                    wrap.insertBefore(this[0]);
                }
                wrap.map(function () {
                    var elem = this;
                    while (elem.firstChild && elem.firstChild.nodeType === 1) {
                        elem = elem.firstChild;
                    }
                    return elem;
                }).append(this);
            }
            return this;
        },
        wrapInner: function (html) {
            if (jQuery.isFunction(html)) {
                return this.each(function (i) {
                    jQuery(this).wrapInner(html.call(this, i));
                });
            }
            return this.each(function () {
                var self = jQuery(this), contents = self.contents();
                if (contents.length) {
                    contents.wrapAll(html);
                } else {
                    self.append(html);
                }
            });
        },
        wrap: function (html) {
            var isFunction = jQuery.isFunction(html);
            return this.each(function (i) {
                jQuery(this).wrapAll(isFunction ? html.call(this, i) : html);
            });
        },
        unwrap: function () {
            return this.parent().each(function () {
                if (!jQuery.nodeName(this, 'body')) {
                    jQuery(this).replaceWith(this.childNodes);
                }
            }).end();
        },
        append: function () {
            return this.domManip(arguments, true, function (elem) {
                if (this.nodeType === 1 || this.nodeType === 11 || this.nodeType === 9) {
                    this.appendChild(elem);
                }
            });
        },
        prepend: function () {
            return this.domManip(arguments, true, function (elem) {
                if (this.nodeType === 1 || this.nodeType === 11 || this.nodeType === 9) {
                    this.insertBefore(elem, this.firstChild);
                }
            });
        },
        before: function () {
            return this.domManip(arguments, false, function (elem) {
                if (this.parentNode) {
                    this.parentNode.insertBefore(elem, this);
                }
            });
        },
        after: function () {
            return this.domManip(arguments, false, function (elem) {
                if (this.parentNode) {
                    this.parentNode.insertBefore(elem, this.nextSibling);
                }
            });
        },
        remove: function (selector, keepData) {
            var elem, i = 0;
            for (; (elem = this[i]) != null; i++) {
                if (!selector || jQuery.filter(selector, [elem]).length > 0) {
                    if (!keepData && elem.nodeType === 1) {
                        jQuery.cleanData(getAll(elem));
                    }
                    if (elem.parentNode) {
                        if (keepData && jQuery.contains(elem.ownerDocument, elem)) {
                            setGlobalEval(getAll(elem, 'script'));
                        }
                        elem.parentNode.removeChild(elem);
                    }
                }
            }
            return this;
        },
        empty: function () {
            var elem, i = 0;
            for (; (elem = this[i]) != null; i++) {
                if (elem.nodeType === 1) {
                    jQuery.cleanData(getAll(elem, false));
                }
                while (elem.firstChild) {
                    elem.removeChild(elem.firstChild);
                }
                if (elem.options && jQuery.nodeName(elem, 'select')) {
                    elem.options.length = 0;
                }
            }
            return this;
        },
        clone: function (dataAndEvents, deepDataAndEvents) {
            dataAndEvents = dataAndEvents == null ? false : dataAndEvents;
            deepDataAndEvents = deepDataAndEvents == null ? dataAndEvents : deepDataAndEvents;
            return this.map(function () {
                return jQuery.clone(this, dataAndEvents, deepDataAndEvents);
            });
        },
        html: function (value) {
            return jQuery.access(this, function (value) {
                var elem = this[0] || {}, i = 0, l = this.length;
                if (value === undefined) {
                    return elem.nodeType === 1 ? elem.innerHTML.replace(rinlinejQuery, '') : undefined;
                }
                if (typeof value === 'string' && !rnoInnerhtml.test(value) && (jQuery.support.htmlSerialize || !rnoshimcache.test(value)) && (jQuery.support.leadingWhitespace || !rleadingWhitespace.test(value)) && !wrapMap[(rtagName.exec(value) || [
                        '',
                        ''
                    ])[1].toLowerCase()]) {
                    value = value.replace(rxhtmlTag, '<$1></$2>');
                    try {
                        for (; i < l; i++) {
                            elem = this[i] || {};
                            if (elem.nodeType === 1) {
                                jQuery.cleanData(getAll(elem, false));
                                elem.innerHTML = value;
                            }
                        }
                        elem = 0;
                    } catch (e) {
                    }
                }
                if (elem) {
                    this.empty().append(value);
                }
            }, null, value, arguments.length);
        },
        replaceWith: function (value) {
            var isFunc = jQuery.isFunction(value);
            if (!isFunc && typeof value !== 'string') {
                value = jQuery(value).not(this).detach();
            }
            return this.domManip([value], true, function (elem) {
                var next = this.nextSibling, parent = this.parentNode;
                if (parent) {
                    jQuery(this).remove();
                    parent.insertBefore(elem, next);
                }
            });
        },
        detach: function (selector) {
            return this.remove(selector, true);
        },
        domManip: function (args, table, callback) {
            args = core_concat.apply([], args);
            var first, node, hasScripts, scripts, doc, fragment, i = 0, l = this.length, set = this, iNoClone = l - 1, value = args[0], isFunction = jQuery.isFunction(value);
            if (isFunction || !(l <= 1 || typeof value !== 'string' || jQuery.support.checkClone || !rchecked.test(value))) {
                return this.each(function (index) {
                    var self = set.eq(index);
                    if (isFunction) {
                        args[0] = value.call(this, index, table ? self.html() : undefined);
                    }
                    self.domManip(args, table, callback);
                });
            }
            if (l) {
                fragment = jQuery.buildFragment(args, this[0].ownerDocument, false, this);
                first = fragment.firstChild;
                if (fragment.childNodes.length === 1) {
                    fragment = first;
                }
                if (first) {
                    table = table && jQuery.nodeName(first, 'tr');
                    scripts = jQuery.map(getAll(fragment, 'script'), disableScript);
                    hasScripts = scripts.length;
                    for (; i < l; i++) {
                        node = fragment;
                        if (i !== iNoClone) {
                            node = jQuery.clone(node, true, true);
                            if (hasScripts) {
                                jQuery.merge(scripts, getAll(node, 'script'));
                            }
                        }
                        callback.call(table && jQuery.nodeName(this[i], 'table') ? findOrAppend(this[i], 'tbody') : this[i], node, i);
                    }
                    if (hasScripts) {
                        doc = scripts[scripts.length - 1].ownerDocument;
                        jQuery.map(scripts, restoreScript);
                        for (i = 0; i < hasScripts; i++) {
                            node = scripts[i];
                            if (rscriptType.test(node.type || '') && !jQuery._data(node, 'globalEval') && jQuery.contains(doc, node)) {
                                if (node.src) {
                                    jQuery.ajax({
                                        url: node.src,
                                        type: 'GET',
                                        dataType: 'script',
                                        async: false,
                                        global: false,
                                        'throws': true
                                    });
                                } else {
                                    jQuery.globalEval((node.text || node.textContent || node.innerHTML || '').replace(rcleanScript, ''));
                                }
                            }
                        }
                    }
                    fragment = first = null;
                }
            }
            return this;
        }
    });
    function findOrAppend(elem, tag) {
        return elem.getElementsByTagName(tag)[0] || elem.appendChild(elem.ownerDocument.createElement(tag));
    }
    function disableScript(elem) {
        var attr = elem.getAttributeNode('type');
        elem.type = (attr && attr.specified) + '/' + elem.type;
        return elem;
    }
    function restoreScript(elem) {
        var match = rscriptTypeMasked.exec(elem.type);
        if (match) {
            elem.type = match[1];
        } else {
            elem.removeAttribute('type');
        }
        return elem;
    }
    function setGlobalEval(elems, refElements) {
        var elem, i = 0;
        for (; (elem = elems[i]) != null; i++) {
            jQuery._data(elem, 'globalEval', !refElements || jQuery._data(refElements[i], 'globalEval'));
        }
    }
    function cloneCopyEvent(src, dest) {
        if (dest.nodeType !== 1 || !jQuery.hasData(src)) {
            return;
        }
        var type, i, l, oldData = jQuery._data(src), curData = jQuery._data(dest, oldData), events = oldData.events;
        if (events) {
            delete curData.handle;
            curData.events = {};
            for (type in events) {
                for (i = 0, l = events[type].length; i < l; i++) {
                    jQuery.event.add(dest, type, events[type][i]);
                }
            }
        }
        if (curData.data) {
            curData.data = jQuery.extend({}, curData.data);
        }
    }
    function fixCloneNodeIssues(src, dest) {
        var nodeName, e, data;
        if (dest.nodeType !== 1) {
            return;
        }
        nodeName = dest.nodeName.toLowerCase();
        if (!jQuery.support.noCloneEvent && dest[jQuery.expando]) {
            data = jQuery._data(dest);
            for (e in data.events) {
                jQuery.removeEvent(dest, e, data.handle);
            }
            dest.removeAttribute(jQuery.expando);
        }
        if (nodeName === 'script' && dest.text !== src.text) {
            disableScript(dest).text = src.text;
            restoreScript(dest);
        } else if (nodeName === 'object') {
            if (dest.parentNode) {
                dest.outerHTML = src.outerHTML;
            }
            if (jQuery.support.html5Clone && src.innerHTML && !jQuery.trim(dest.innerHTML)) {
                dest.innerHTML = src.innerHTML;
            }
        } else if (nodeName === 'input' && manipulation_rcheckableType.test(src.type)) {
            dest.defaultChecked = dest.checked = src.checked;
            if (dest.value !== src.value) {
                dest.value = src.value;
            }
        } else if (nodeName === 'option') {
            dest.defaultSelected = dest.selected = src.defaultSelected;
        } else if (nodeName === 'input' || nodeName === 'textarea') {
            dest.defaultValue = src.defaultValue;
        }
    }
    jQuery.each({
        appendTo: 'append',
        prependTo: 'prepend',
        insertBefore: 'before',
        insertAfter: 'after',
        replaceAll: 'replaceWith'
    }, function (name, original) {
        jQuery.fn[name] = function (selector) {
            var elems, i = 0, ret = [], insert = jQuery(selector), last = insert.length - 1;
            for (; i <= last; i++) {
                elems = i === last ? this : this.clone(true);
                jQuery(insert[i])[original](elems);
                core_push.apply(ret, elems.get());
            }
            return this.pushStack(ret);
        };
    });
    function getAll(context, tag) {
        var elems, elem, i = 0, found = typeof context.getElementsByTagName !== core_strundefined ? context.getElementsByTagName(tag || '*') : typeof context.querySelectorAll !== core_strundefined ? context.querySelectorAll(tag || '*') : undefined;
        if (!found) {
            for (found = [], elems = context.childNodes || context; (elem = elems[i]) != null; i++) {
                if (!tag || jQuery.nodeName(elem, tag)) {
                    found.push(elem);
                } else {
                    jQuery.merge(found, getAll(elem, tag));
                }
            }
        }
        return tag === undefined || tag && jQuery.nodeName(context, tag) ? jQuery.merge([context], found) : found;
    }
    function fixDefaultChecked(elem) {
        if (manipulation_rcheckableType.test(elem.type)) {
            elem.defaultChecked = elem.checked;
        }
    }
    jQuery.extend({
        clone: function (elem, dataAndEvents, deepDataAndEvents) {
            var destElements, node, clone, i, srcElements, inPage = jQuery.contains(elem.ownerDocument, elem);
            if (jQuery.support.html5Clone || jQuery.isXMLDoc(elem) || !rnoshimcache.test('<' + elem.nodeName + '>')) {
                clone = elem.cloneNode(true);
            } else {
                fragmentDiv.innerHTML = elem.outerHTML;
                fragmentDiv.removeChild(clone = fragmentDiv.firstChild);
            }
            if ((!jQuery.support.noCloneEvent || !jQuery.support.noCloneChecked) && (elem.nodeType === 1 || elem.nodeType === 11) && !jQuery.isXMLDoc(elem)) {
                destElements = getAll(clone);
                srcElements = getAll(elem);
                for (i = 0; (node = srcElements[i]) != null; ++i) {
                    if (destElements[i]) {
                        fixCloneNodeIssues(node, destElements[i]);
                    }
                }
            }
            if (dataAndEvents) {
                if (deepDataAndEvents) {
                    srcElements = srcElements || getAll(elem);
                    destElements = destElements || getAll(clone);
                    for (i = 0; (node = srcElements[i]) != null; i++) {
                        cloneCopyEvent(node, destElements[i]);
                    }
                } else {
                    cloneCopyEvent(elem, clone);
                }
            }
            destElements = getAll(clone, 'script');
            if (destElements.length > 0) {
                setGlobalEval(destElements, !inPage && getAll(elem, 'script'));
            }
            destElements = srcElements = node = null;
            return clone;
        },
        buildFragment: function (elems, context, scripts, selection) {
            var j, elem, contains, tmp, tag, tbody, wrap, l = elems.length, safe = createSafeFragment(context), nodes = [], i = 0;
            for (; i < l; i++) {
                elem = elems[i];
                if (elem || elem === 0) {
                    if (jQuery.type(elem) === 'object') {
                        jQuery.merge(nodes, elem.nodeType ? [elem] : elem);
                    } else if (!rhtml.test(elem)) {
                        nodes.push(context.createTextNode(elem));
                    } else {
                        tmp = tmp || safe.appendChild(context.createElement('div'));
                        tag = (rtagName.exec(elem) || [
                            '',
                            ''
                        ])[1].toLowerCase();
                        wrap = wrapMap[tag] || wrapMap._default;
                        tmp.innerHTML = wrap[1] + elem.replace(rxhtmlTag, '<$1></$2>') + wrap[2];
                        j = wrap[0];
                        while (j--) {
                            tmp = tmp.lastChild;
                        }
                        if (!jQuery.support.leadingWhitespace && rleadingWhitespace.test(elem)) {
                            nodes.push(context.createTextNode(rleadingWhitespace.exec(elem)[0]));
                        }
                        if (!jQuery.support.tbody) {
                            elem = tag === 'table' && !rtbody.test(elem) ? tmp.firstChild : wrap[1] === '<table>' && !rtbody.test(elem) ? tmp : 0;
                            j = elem && elem.childNodes.length;
                            while (j--) {
                                if (jQuery.nodeName(tbody = elem.childNodes[j], 'tbody') && !tbody.childNodes.length) {
                                    elem.removeChild(tbody);
                                }
                            }
                        }
                        jQuery.merge(nodes, tmp.childNodes);
                        tmp.textContent = '';
                        while (tmp.firstChild) {
                            tmp.removeChild(tmp.firstChild);
                        }
                        tmp = safe.lastChild;
                    }
                }
            }
            if (tmp) {
                safe.removeChild(tmp);
            }
            if (!jQuery.support.appendChecked) {
                jQuery.grep(getAll(nodes, 'input'), fixDefaultChecked);
            }
            i = 0;
            while (elem = nodes[i++]) {
                if (selection && jQuery.inArray(elem, selection) !== -1) {
                    continue;
                }
                contains = jQuery.contains(elem.ownerDocument, elem);
                tmp = getAll(safe.appendChild(elem), 'script');
                if (contains) {
                    setGlobalEval(tmp);
                }
                if (scripts) {
                    j = 0;
                    while (elem = tmp[j++]) {
                        if (rscriptType.test(elem.type || '')) {
                            scripts.push(elem);
                        }
                    }
                }
            }
            tmp = null;
            return safe;
        },
        cleanData: function (elems, acceptData) {
            var elem, type, id, data, i = 0, internalKey = jQuery.expando, cache = jQuery.cache, deleteExpando = jQuery.support.deleteExpando, special = jQuery.event.special;
            for (; (elem = elems[i]) != null; i++) {
                if (acceptData || jQuery.acceptData(elem)) {
                    id = elem[internalKey];
                    data = id && cache[id];
                    if (data) {
                        if (data.events) {
                            for (type in data.events) {
                                if (special[type]) {
                                    jQuery.event.remove(elem, type);
                                } else {
                                    jQuery.removeEvent(elem, type, data.handle);
                                }
                            }
                        }
                        if (cache[id]) {
                            delete cache[id];
                            if (deleteExpando) {
                                delete elem[internalKey];
                            } else if (typeof elem.removeAttribute !== core_strundefined) {
                                elem.removeAttribute(internalKey);
                            } else {
                                elem[internalKey] = null;
                            }
                            core_deletedIds.push(id);
                        }
                    }
                }
            }
        }
    });
    var iframe, getStyles, curCSS, ralpha = /alpha\([^)]*\)/i, ropacity = /opacity\s*=\s*([^)]*)/, rposition = /^(top|right|bottom|left)$/, rdisplayswap = /^(none|table(?!-c[ea]).+)/, rmargin = /^margin/, rnumsplit = new RegExp('^(' + core_pnum + ')(.*)$', 'i'), rnumnonpx = new RegExp('^(' + core_pnum + ')(?!px)[a-z%]+$', 'i'), rrelNum = new RegExp('^([+-])=(' + core_pnum + ')', 'i'), elemdisplay = { BODY: 'block' }, cssShow = {
            position: 'absolute',
            visibility: 'hidden',
            display: 'block'
        }, cssNormalTransform = {
            letterSpacing: 0,
            fontWeight: 400
        }, cssExpand = [
            'Top',
            'Right',
            'Bottom',
            'Left'
        ], cssPrefixes = [
            'Webkit',
            'O',
            'Moz',
            'ms'
        ];
    function vendorPropName(style, name) {
        if (name in style) {
            return name;
        }
        var capName = name.charAt(0).toUpperCase() + name.slice(1), origName = name, i = cssPrefixes.length;
        while (i--) {
            name = cssPrefixes[i] + capName;
            if (name in style) {
                return name;
            }
        }
        return origName;
    }
    function isHidden(elem, el) {
        elem = el || elem;
        return jQuery.css(elem, 'display') === 'none' || !jQuery.contains(elem.ownerDocument, elem);
    }
    function showHide(elements, show) {
        var display, elem, hidden, values = [], index = 0, length = elements.length;
        for (; index < length; index++) {
            elem = elements[index];
            if (!elem.style) {
                continue;
            }
            values[index] = jQuery._data(elem, 'olddisplay');
            display = elem.style.display;
            if (show) {
                if (!values[index] && display === 'none') {
                    elem.style.display = '';
                }
                if (elem.style.display === '' && isHidden(elem)) {
                    values[index] = jQuery._data(elem, 'olddisplay', css_defaultDisplay(elem.nodeName));
                }
            } else {
                if (!values[index]) {
                    hidden = isHidden(elem);
                    if (display && display !== 'none' || !hidden) {
                        jQuery._data(elem, 'olddisplay', hidden ? display : jQuery.css(elem, 'display'));
                    }
                }
            }
        }
        for (index = 0; index < length; index++) {
            elem = elements[index];
            if (!elem.style) {
                continue;
            }
            if (!show || elem.style.display === 'none' || elem.style.display === '') {
                elem.style.display = show ? values[index] || '' : 'none';
            }
        }
        return elements;
    }
    jQuery.fn.extend({
        css: function (name, value) {
            return jQuery.access(this, function (elem, name, value) {
                var len, styles, map = {}, i = 0;
                if (jQuery.isArray(name)) {
                    styles = getStyles(elem);
                    len = name.length;
                    for (; i < len; i++) {
                        map[name[i]] = jQuery.css(elem, name[i], false, styles);
                    }
                    return map;
                }
                return value !== undefined ? jQuery.style(elem, name, value) : jQuery.css(elem, name);
            }, name, value, arguments.length > 1);
        },
        show: function () {
            return showHide(this, true);
        },
        hide: function () {
            return showHide(this);
        },
        toggle: function (state) {
            var bool = typeof state === 'boolean';
            return this.each(function () {
                if (bool ? state : isHidden(this)) {
                    jQuery(this).show();
                } else {
                    jQuery(this).hide();
                }
            });
        }
    });
    jQuery.extend({
        cssHooks: {
            opacity: {
                get: function (elem, computed) {
                    if (computed) {
                        var ret = curCSS(elem, 'opacity');
                        return ret === '' ? '1' : ret;
                    }
                }
            }
        },
        cssNumber: {
            columnCount: true,
            fillOpacity: true,
            fontWeight: true,
            lineHeight: true,
            opacity: true,
            orphans: true,
            widows: true,
            zIndex: true,
            zoom: true
        },
        cssProps: { 'float': jQuery.support.cssFloat ? 'cssFloat' : 'styleFloat' },
        style: function (elem, name, value, extra) {
            if (!elem || elem.nodeType === 3 || elem.nodeType === 8 || !elem.style) {
                return;
            }
            var ret, type, hooks, origName = jQuery.camelCase(name), style = elem.style;
            name = jQuery.cssProps[origName] || (jQuery.cssProps[origName] = vendorPropName(style, origName));
            hooks = jQuery.cssHooks[name] || jQuery.cssHooks[origName];
            if (value !== undefined) {
                type = typeof value;
                if (type === 'string' && (ret = rrelNum.exec(value))) {
                    value = (ret[1] + 1) * ret[2] + parseFloat(jQuery.css(elem, name));
                    type = 'number';
                }
                if (value == null || type === 'number' && isNaN(value)) {
                    return;
                }
                if (type === 'number' && !jQuery.cssNumber[origName]) {
                    value += 'px';
                }
                if (!jQuery.support.clearCloneStyle && value === '' && name.indexOf('background') === 0) {
                    style[name] = 'inherit';
                }
                if (!hooks || !('set' in hooks) || (value = hooks.set(elem, value, extra)) !== undefined) {
                    try {
                        style[name] = value;
                    } catch (e) {
                    }
                }
            } else {
                if (hooks && 'get' in hooks && (ret = hooks.get(elem, false, extra)) !== undefined) {
                    return ret;
                }
                return style[name];
            }
        },
        css: function (elem, name, extra, styles) {
            var num, val, hooks, origName = jQuery.camelCase(name);
            name = jQuery.cssProps[origName] || (jQuery.cssProps[origName] = vendorPropName(elem.style, origName));
            hooks = jQuery.cssHooks[name] || jQuery.cssHooks[origName];
            if (hooks && 'get' in hooks) {
                val = hooks.get(elem, true, extra);
            }
            if (val === undefined) {
                val = curCSS(elem, name, styles);
            }
            if (val === 'normal' && name in cssNormalTransform) {
                val = cssNormalTransform[name];
            }
            if (extra === '' || extra) {
                num = parseFloat(val);
                return extra === true || jQuery.isNumeric(num) ? num || 0 : val;
            }
            return val;
        },
        swap: function (elem, options, callback, args) {
            var ret, name, old = {};
            for (name in options) {
                old[name] = elem.style[name];
                elem.style[name] = options[name];
            }
            ret = callback.apply(elem, args || []);
            for (name in options) {
                elem.style[name] = old[name];
            }
            return ret;
        }
    });
    if (window.getComputedStyle) {
        getStyles = function (elem) {
            return window.getComputedStyle(elem, null);
        };
        curCSS = function (elem, name, _computed) {
            var width, minWidth, maxWidth, computed = _computed || getStyles(elem), ret = computed ? computed.getPropertyValue(name) || computed[name] : undefined, style = elem.style;
            if (computed) {
                if (ret === '' && !jQuery.contains(elem.ownerDocument, elem)) {
                    ret = jQuery.style(elem, name);
                }
                if (rnumnonpx.test(ret) && rmargin.test(name)) {
                    width = style.width;
                    minWidth = style.minWidth;
                    maxWidth = style.maxWidth;
                    style.minWidth = style.maxWidth = style.width = ret;
                    ret = computed.width;
                    style.width = width;
                    style.minWidth = minWidth;
                    style.maxWidth = maxWidth;
                }
            }
            return ret;
        };
    } else if (document.documentElement.currentStyle) {
        getStyles = function (elem) {
            return elem.currentStyle;
        };
        curCSS = function (elem, name, _computed) {
            var left, rs, rsLeft, computed = _computed || getStyles(elem), ret = computed ? computed[name] : undefined, style = elem.style;
            if (ret == null && style && style[name]) {
                ret = style[name];
            }
            if (rnumnonpx.test(ret) && !rposition.test(name)) {
                left = style.left;
                rs = elem.runtimeStyle;
                rsLeft = rs && rs.left;
                if (rsLeft) {
                    rs.left = elem.currentStyle.left;
                }
                style.left = name === 'fontSize' ? '1em' : ret;
                ret = style.pixelLeft + 'px';
                style.left = left;
                if (rsLeft) {
                    rs.left = rsLeft;
                }
            }
            return ret === '' ? 'auto' : ret;
        };
    }
    function setPositiveNumber(elem, value, subtract) {
        var matches = rnumsplit.exec(value);
        return matches ? Math.max(0, matches[1] - (subtract || 0)) + (matches[2] || 'px') : value;
    }
    function augmentWidthOrHeight(elem, name, extra, isBorderBox, styles) {
        var i = extra === (isBorderBox ? 'border' : 'content') ? 4 : name === 'width' ? 1 : 0, val = 0;
        for (; i < 4; i += 2) {
            if (extra === 'margin') {
                val += jQuery.css(elem, extra + cssExpand[i], true, styles);
            }
            if (isBorderBox) {
                if (extra === 'content') {
                    val -= jQuery.css(elem, 'padding' + cssExpand[i], true, styles);
                }
                if (extra !== 'margin') {
                    val -= jQuery.css(elem, 'border' + cssExpand[i] + 'Width', true, styles);
                }
            } else {
                val += jQuery.css(elem, 'padding' + cssExpand[i], true, styles);
                if (extra !== 'padding') {
                    val += jQuery.css(elem, 'border' + cssExpand[i] + 'Width', true, styles);
                }
            }
        }
        return val;
    }
    function getWidthOrHeight(elem, name, extra) {
        var valueIsBorderBox = true, val = name === 'width' ? elem.offsetWidth : elem.offsetHeight, styles = getStyles(elem), isBorderBox = jQuery.support.boxSizing && jQuery.css(elem, 'boxSizing', false, styles) === 'border-box';
        if (val <= 0 || val == null) {
            val = curCSS(elem, name, styles);
            if (val < 0 || val == null) {
                val = elem.style[name];
            }
            if (rnumnonpx.test(val)) {
                return val;
            }
            valueIsBorderBox = isBorderBox && (jQuery.support.boxSizingReliable || val === elem.style[name]);
            val = parseFloat(val) || 0;
        }
        return val + augmentWidthOrHeight(elem, name, extra || (isBorderBox ? 'border' : 'content'), valueIsBorderBox, styles) + 'px';
    }
    function css_defaultDisplay(nodeName) {
        var doc = document, display = elemdisplay[nodeName];
        if (!display) {
            display = actualDisplay(nodeName, doc);
            if (display === 'none' || !display) {
                iframe = (iframe || jQuery('<iframe frameborder=\'0\' width=\'0\' height=\'0\'/>').css('cssText', 'display:block !important')).appendTo(doc.documentElement);
                doc = (iframe[0].contentWindow || iframe[0].contentDocument).document;
                doc.write('<!doctype html><html><body>');
                doc.close();
                display = actualDisplay(nodeName, doc);
                iframe.detach();
            }
            elemdisplay[nodeName] = display;
        }
        return display;
    }
    function actualDisplay(name, doc) {
        var elem = jQuery(doc.createElement(name)).appendTo(doc.body), display = jQuery.css(elem[0], 'display');
        elem.remove();
        return display;
    }
    jQuery.each([
        'height',
        'width'
    ], function (i, name) {
        jQuery.cssHooks[name] = {
            get: function (elem, computed, extra) {
                if (computed) {
                    return elem.offsetWidth === 0 && rdisplayswap.test(jQuery.css(elem, 'display')) ? jQuery.swap(elem, cssShow, function () {
                        return getWidthOrHeight(elem, name, extra);
                    }) : getWidthOrHeight(elem, name, extra);
                }
            },
            set: function (elem, value, extra) {
                var styles = extra && getStyles(elem);
                return setPositiveNumber(elem, value, extra ? augmentWidthOrHeight(elem, name, extra, jQuery.support.boxSizing && jQuery.css(elem, 'boxSizing', false, styles) === 'border-box', styles) : 0);
            }
        };
    });
    if (!jQuery.support.opacity) {
        jQuery.cssHooks.opacity = {
            get: function (elem, computed) {
                return ropacity.test((computed && elem.currentStyle ? elem.currentStyle.filter : elem.style.filter) || '') ? 0.01 * parseFloat(RegExp.$1) + '' : computed ? '1' : '';
            },
            set: function (elem, value) {
                var style = elem.style, currentStyle = elem.currentStyle, opacity = jQuery.isNumeric(value) ? 'alpha(opacity=' + value * 100 + ')' : '', filter = currentStyle && currentStyle.filter || style.filter || '';
                style.zoom = 1;
                if ((value >= 1 || value === '') && jQuery.trim(filter.replace(ralpha, '')) === '' && style.removeAttribute) {
                    style.removeAttribute('filter');
                    if (value === '' || currentStyle && !currentStyle.filter) {
                        return;
                    }
                }
                style.filter = ralpha.test(filter) ? filter.replace(ralpha, opacity) : filter + ' ' + opacity;
            }
        };
    }
    jQuery(function () {
        if (!jQuery.support.reliableMarginRight) {
            jQuery.cssHooks.marginRight = {
                get: function (elem, computed) {
                    if (computed) {
                        return jQuery.swap(elem, { display: 'inline-block' }, curCSS, [
                            elem,
                            'marginRight'
                        ]);
                    }
                }
            };
        }
        if (!jQuery.support.pixelPosition && jQuery.fn.position) {
            jQuery.each([
                'top',
                'left'
            ], function (i, prop) {
                jQuery.cssHooks[prop] = {
                    get: function (elem, computed) {
                        if (computed) {
                            computed = curCSS(elem, prop);
                            return rnumnonpx.test(computed) ? jQuery(elem).position()[prop] + 'px' : computed;
                        }
                    }
                };
            });
        }
    });
    if (jQuery.expr && jQuery.expr.filters) {
        jQuery.expr.filters.hidden = function (elem) {
            return elem.offsetWidth <= 0 && elem.offsetHeight <= 0 || !jQuery.support.reliableHiddenOffsets && (elem.style && elem.style.display || jQuery.css(elem, 'display')) === 'none';
        };
        jQuery.expr.filters.visible = function (elem) {
            return !jQuery.expr.filters.hidden(elem);
        };
    }
    jQuery.each({
        margin: '',
        padding: '',
        border: 'Width'
    }, function (prefix, suffix) {
        jQuery.cssHooks[prefix + suffix] = {
            expand: function (value) {
                var i = 0, expanded = {}, parts = typeof value === 'string' ? value.split(' ') : [value];
                for (; i < 4; i++) {
                    expanded[prefix + cssExpand[i] + suffix] = parts[i] || parts[i - 2] || parts[0];
                }
                return expanded;
            }
        };
        if (!rmargin.test(prefix)) {
            jQuery.cssHooks[prefix + suffix].set = setPositiveNumber;
        }
    });
    var r20 = /%20/g, rbracket = /\[\]$/, rCRLF = /\r?\n/g, rsubmitterTypes = /^(?:submit|button|image|reset|file)$/i, rsubmittable = /^(?:input|select|textarea|keygen)/i;
    jQuery.fn.extend({
        serialize: function () {
            return jQuery.param(this.serializeArray());
        },
        serializeArray: function () {
            return this.map(function () {
                var elements = jQuery.prop(this, 'elements');
                return elements ? jQuery.makeArray(elements) : this;
            }).filter(function () {
                var type = this.type;
                return this.name && !jQuery(this).is(':disabled') && rsubmittable.test(this.nodeName) && !rsubmitterTypes.test(type) && (this.checked || !manipulation_rcheckableType.test(type));
            }).map(function (i, elem) {
                var val = jQuery(this).val();
                return val == null ? null : jQuery.isArray(val) ? jQuery.map(val, function (val) {
                    return {
                        name: elem.name,
                        value: val.replace(rCRLF, '\r\n')
                    };
                }) : {
                    name: elem.name,
                    value: val.replace(rCRLF, '\r\n')
                };
            }).get();
        }
    });
    jQuery.param = function (a, traditional) {
        var prefix, s = [], add = function (key, value) {
                value = jQuery.isFunction(value) ? value() : value == null ? '' : value;
                s[s.length] = encodeURIComponent(key) + '=' + encodeURIComponent(value);
            };
        if (traditional === undefined) {
            traditional = jQuery.ajaxSettings && jQuery.ajaxSettings.traditional;
        }
        if (jQuery.isArray(a) || a.jquery && !jQuery.isPlainObject(a)) {
            jQuery.each(a, function () {
                add(this.name, this.value);
            });
        } else {
            for (prefix in a) {
                buildParams(prefix, a[prefix], traditional, add);
            }
        }
        return s.join('&').replace(r20, '+');
    };
    function buildParams(prefix, obj, traditional, add) {
        var name;
        if (jQuery.isArray(obj)) {
            jQuery.each(obj, function (i, v) {
                if (traditional || rbracket.test(prefix)) {
                    add(prefix, v);
                } else {
                    buildParams(prefix + '[' + (typeof v === 'object' ? i : '') + ']', v, traditional, add);
                }
            });
        } else if (!traditional && jQuery.type(obj) === 'object') {
            for (name in obj) {
                buildParams(prefix + '[' + name + ']', obj[name], traditional, add);
            }
        } else {
            add(prefix, obj);
        }
    }
    jQuery.each(('blur focus focusin focusout load resize scroll unload click dblclick ' + 'mousedown mouseup mousemove mouseover mouseout mouseenter mouseleave ' + 'change select submit keydown keypress keyup error contextmenu').split(' '), function (i, name) {
        jQuery.fn[name] = function (data, fn) {
            return arguments.length > 0 ? this.on(name, null, data, fn) : this.trigger(name);
        };
    });
    jQuery.fn.hover = function (fnOver, fnOut) {
        return this.mouseenter(fnOver).mouseleave(fnOut || fnOver);
    };
    var ajaxLocParts, ajaxLocation, ajax_nonce = jQuery.now(), ajax_rquery = /\?/, rhash = /#.*$/, rts = /([?&])_=[^&]*/, rheaders = /^(.*?):[ \t]*([^\r\n]*)\r?$/gm, rlocalProtocol = /^(?:about|app|app-storage|.+-extension|file|res|widget):$/, rnoContent = /^(?:GET|HEAD)$/, rprotocol = /^\/\//, rurl = /^([\w.+-]+:)(?:\/\/([^\/?#:]*)(?::(\d+)|)|)/, _load = jQuery.fn.load, prefilters = {}, transports = {}, allTypes = '*/'.concat('*');
    try {
        ajaxLocation = location.href;
    } catch (e) {
        ajaxLocation = document.createElement('a');
        ajaxLocation.href = '';
        ajaxLocation = ajaxLocation.href;
    }
    ajaxLocParts = rurl.exec(ajaxLocation.toLowerCase()) || [];
    function addToPrefiltersOrTransports(structure) {
        return function (dataTypeExpression, func) {
            if (typeof dataTypeExpression !== 'string') {
                func = dataTypeExpression;
                dataTypeExpression = '*';
            }
            var dataType, i = 0, dataTypes = dataTypeExpression.toLowerCase().match(core_rnotwhite) || [];
            if (jQuery.isFunction(func)) {
                while (dataType = dataTypes[i++]) {
                    if (dataType[0] === '+') {
                        dataType = dataType.slice(1) || '*';
                        (structure[dataType] = structure[dataType] || []).unshift(func);
                    } else {
                        (structure[dataType] = structure[dataType] || []).push(func);
                    }
                }
            }
        };
    }
    function inspectPrefiltersOrTransports(structure, options, originalOptions, jqXHR) {
        var inspected = {}, seekingTransport = structure === transports;
        function inspect(dataType) {
            var selected;
            inspected[dataType] = true;
            jQuery.each(structure[dataType] || [], function (_, prefilterOrFactory) {
                var dataTypeOrTransport = prefilterOrFactory(options, originalOptions, jqXHR);
                if (typeof dataTypeOrTransport === 'string' && !seekingTransport && !inspected[dataTypeOrTransport]) {
                    options.dataTypes.unshift(dataTypeOrTransport);
                    inspect(dataTypeOrTransport);
                    return false;
                } else if (seekingTransport) {
                    return !(selected = dataTypeOrTransport);
                }
            });
            return selected;
        }
        return inspect(options.dataTypes[0]) || !inspected['*'] && inspect('*');
    }
    function ajaxExtend(target, src) {
        var deep, key, flatOptions = jQuery.ajaxSettings.flatOptions || {};
        for (key in src) {
            if (src[key] !== undefined) {
                (flatOptions[key] ? target : deep || (deep = {}))[key] = src[key];
            }
        }
        if (deep) {
            jQuery.extend(true, target, deep);
        }
        return target;
    }
    jQuery.fn.load = function (url, params, callback) {
        if (typeof url !== 'string' && _load) {
            return _load.apply(this, arguments);
        }
        var selector, response, type, self = this, off = url.indexOf(' ');
        if (off >= 0) {
            selector = url.slice(off, url.length);
            url = url.slice(0, off);
        }
        if (jQuery.isFunction(params)) {
            callback = params;
            params = undefined;
        } else if (params && typeof params === 'object') {
            type = 'POST';
        }
        if (self.length > 0) {
            jQuery.ajax({
                url: url,
                type: type,
                dataType: 'html',
                data: params
            }).done(function (responseText) {
                response = arguments;
                self.html(selector ? jQuery('<div>').append(jQuery.parseHTML(responseText)).find(selector) : responseText);
            }).complete(callback && function (jqXHR, status) {
                self.each(callback, response || [
                    jqXHR.responseText,
                    status,
                    jqXHR
                ]);
            });
        }
        return this;
    };
    jQuery.each([
        'ajaxStart',
        'ajaxStop',
        'ajaxComplete',
        'ajaxError',
        'ajaxSuccess',
        'ajaxSend'
    ], function (i, type) {
        jQuery.fn[type] = function (fn) {
            return this.on(type, fn);
        };
    });
    jQuery.each([
        'get',
        'post'
    ], function (i, method) {
        jQuery[method] = function (url, data, callback, type) {
            if (jQuery.isFunction(data)) {
                type = type || callback;
                callback = data;
                data = undefined;
            }
            return jQuery.ajax({
                url: url,
                type: method,
                dataType: type,
                data: data,
                success: callback
            });
        };
    });
    jQuery.extend({
        active: 0,
        lastModified: {},
        etag: {},
        ajaxSettings: {
            url: ajaxLocation,
            type: 'GET',
            isLocal: rlocalProtocol.test(ajaxLocParts[1]),
            global: true,
            processData: true,
            async: true,
            contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
            accepts: {
                '*': allTypes,
                text: 'text/plain',
                html: 'text/html',
                xml: 'application/xml, text/xml',
                json: 'application/json, text/javascript'
            },
            contents: {
                xml: /xml/,
                html: /html/,
                json: /json/
            },
            responseFields: {
                xml: 'responseXML',
                text: 'responseText'
            },
            converters: {
                '* text': window.String,
                'text html': true,
                'text json': jQuery.parseJSON,
                'text xml': jQuery.parseXML
            },
            flatOptions: {
                url: true,
                context: true
            }
        },
        ajaxSetup: function (target, settings) {
            return settings ? ajaxExtend(ajaxExtend(target, jQuery.ajaxSettings), settings) : ajaxExtend(jQuery.ajaxSettings, target);
        },
        ajaxPrefilter: addToPrefiltersOrTransports(prefilters),
        ajaxTransport: addToPrefiltersOrTransports(transports),
        ajax: function (url, options) {
            if (typeof url === 'object') {
                options = url;
                url = undefined;
            }
            options = options || {};
            var parts, i, cacheURL, responseHeadersString, timeoutTimer, fireGlobals, transport, responseHeaders, s = jQuery.ajaxSetup({}, options), callbackContext = s.context || s, globalEventContext = s.context && (callbackContext.nodeType || callbackContext.jquery) ? jQuery(callbackContext) : jQuery.event, deferred = jQuery.Deferred(), completeDeferred = jQuery.Callbacks('once memory'), statusCode = s.statusCode || {}, requestHeaders = {}, requestHeadersNames = {}, state = 0, strAbort = 'canceled', jqXHR = {
                    readyState: 0,
                    getResponseHeader: function (key) {
                        var match;
                        if (state === 2) {
                            if (!responseHeaders) {
                                responseHeaders = {};
                                while (match = rheaders.exec(responseHeadersString)) {
                                    responseHeaders[match[1].toLowerCase()] = match[2];
                                }
                            }
                            match = responseHeaders[key.toLowerCase()];
                        }
                        return match == null ? null : match;
                    },
                    getAllResponseHeaders: function () {
                        return state === 2 ? responseHeadersString : null;
                    },
                    setRequestHeader: function (name, value) {
                        var lname = name.toLowerCase();
                        if (!state) {
                            name = requestHeadersNames[lname] = requestHeadersNames[lname] || name;
                            requestHeaders[name] = value;
                        }
                        return this;
                    },
                    overrideMimeType: function (type) {
                        if (!state) {
                            s.mimeType = type;
                        }
                        return this;
                    },
                    statusCode: function (map) {
                        var code;
                        if (map) {
                            if (state < 2) {
                                for (code in map) {
                                    statusCode[code] = [
                                        statusCode[code],
                                        map[code]
                                    ];
                                }
                            } else {
                                jqXHR.always(map[jqXHR.status]);
                            }
                        }
                        return this;
                    },
                    abort: function (statusText) {
                        var finalText = statusText || strAbort;
                        if (transport) {
                            transport.abort(finalText);
                        }
                        done(0, finalText);
                        return this;
                    }
                };
            deferred.promise(jqXHR).complete = completeDeferred.add;
            jqXHR.success = jqXHR.done;
            jqXHR.error = jqXHR.fail;
            s.url = ((url || s.url || ajaxLocation) + '').replace(rhash, '').replace(rprotocol, ajaxLocParts[1] + '//');
            s.type = options.method || options.type || s.method || s.type;
            s.dataTypes = jQuery.trim(s.dataType || '*').toLowerCase().match(core_rnotwhite) || [''];
            if (s.crossDomain == null) {
                parts = rurl.exec(s.url.toLowerCase());
                s.crossDomain = !!(parts && (parts[1] !== ajaxLocParts[1] || parts[2] !== ajaxLocParts[2] || (parts[3] || (parts[1] === 'http:' ? 80 : 443)) != (ajaxLocParts[3] || (ajaxLocParts[1] === 'http:' ? 80 : 443))));
            }
            if (s.data && s.processData && typeof s.data !== 'string') {
                s.data = jQuery.param(s.data, s.traditional);
            }
            inspectPrefiltersOrTransports(prefilters, s, options, jqXHR);
            if (state === 2) {
                return jqXHR;
            }
            fireGlobals = s.global;
            if (fireGlobals && jQuery.active++ === 0) {
                jQuery.event.trigger('ajaxStart');
            }
            s.type = s.type.toUpperCase();
            s.hasContent = !rnoContent.test(s.type);
            cacheURL = s.url;
            if (!s.hasContent) {
                if (s.data) {
                    cacheURL = s.url += (ajax_rquery.test(cacheURL) ? '&' : '?') + s.data;
                    delete s.data;
                }
                if (s.cache === false) {
                    s.url = rts.test(cacheURL) ? cacheURL.replace(rts, '$1_=' + ajax_nonce++) : cacheURL + (ajax_rquery.test(cacheURL) ? '&' : '?') + '_=' + ajax_nonce++;
                }
            }
            if (s.ifModified) {
                if (jQuery.lastModified[cacheURL]) {
                    jqXHR.setRequestHeader('If-Modified-Since', jQuery.lastModified[cacheURL]);
                }
                if (jQuery.etag[cacheURL]) {
                    jqXHR.setRequestHeader('If-None-Match', jQuery.etag[cacheURL]);
                }
            }
            if (s.data && s.hasContent && s.contentType !== false || options.contentType) {
                jqXHR.setRequestHeader('Content-Type', s.contentType);
            }
            jqXHR.setRequestHeader('Accept', s.dataTypes[0] && s.accepts[s.dataTypes[0]] ? s.accepts[s.dataTypes[0]] + (s.dataTypes[0] !== '*' ? ', ' + allTypes + '; q=0.01' : '') : s.accepts['*']);
            for (i in s.headers) {
                jqXHR.setRequestHeader(i, s.headers[i]);
            }
            if (s.beforeSend && (s.beforeSend.call(callbackContext, jqXHR, s) === false || state === 2)) {
                return jqXHR.abort();
            }
            strAbort = 'abort';
            for (i in {
                    success: 1,
                    error: 1,
                    complete: 1
                }) {
                jqXHR[i](s[i]);
            }
            transport = inspectPrefiltersOrTransports(transports, s, options, jqXHR);
            if (!transport) {
                done(-1, 'No Transport');
            } else {
                jqXHR.readyState = 1;
                if (fireGlobals) {
                    globalEventContext.trigger('ajaxSend', [
                        jqXHR,
                        s
                    ]);
                }
                if (s.async && s.timeout > 0) {
                    timeoutTimer = setTimeout(function () {
                        jqXHR.abort('timeout');
                    }, s.timeout);
                }
                try {
                    state = 1;
                    transport.send(requestHeaders, done);
                } catch (e) {
                    if (state < 2) {
                        done(-1, e);
                    } else {
                        throw e;
                    }
                }
            }
            function done(status, nativeStatusText, responses, headers) {
                var isSuccess, success, error, response, modified, statusText = nativeStatusText;
                if (state === 2) {
                    return;
                }
                state = 2;
                if (timeoutTimer) {
                    clearTimeout(timeoutTimer);
                }
                transport = undefined;
                responseHeadersString = headers || '';
                jqXHR.readyState = status > 0 ? 4 : 0;
                if (responses) {
                    response = ajaxHandleResponses(s, jqXHR, responses);
                }
                if (status >= 200 && status < 300 || status === 304) {
                    if (s.ifModified) {
                        modified = jqXHR.getResponseHeader('Last-Modified');
                        if (modified) {
                            jQuery.lastModified[cacheURL] = modified;
                        }
                        modified = jqXHR.getResponseHeader('etag');
                        if (modified) {
                            jQuery.etag[cacheURL] = modified;
                        }
                    }
                    if (status === 204) {
                        isSuccess = true;
                        statusText = 'nocontent';
                    } else if (status === 304) {
                        isSuccess = true;
                        statusText = 'notmodified';
                    } else {
                        isSuccess = ajaxConvert(s, response);
                        statusText = isSuccess.state;
                        success = isSuccess.data;
                        error = isSuccess.error;
                        isSuccess = !error;
                    }
                } else {
                    error = statusText;
                    if (status || !statusText) {
                        statusText = 'error';
                        if (status < 0) {
                            status = 0;
                        }
                    }
                }
                jqXHR.status = status;
                jqXHR.statusText = (nativeStatusText || statusText) + '';
                if (isSuccess) {
                    deferred.resolveWith(callbackContext, [
                        success,
                        statusText,
                        jqXHR
                    ]);
                } else {
                    deferred.rejectWith(callbackContext, [
                        jqXHR,
                        statusText,
                        error
                    ]);
                }
                jqXHR.statusCode(statusCode);
                statusCode = undefined;
                if (fireGlobals) {
                    globalEventContext.trigger(isSuccess ? 'ajaxSuccess' : 'ajaxError', [
                        jqXHR,
                        s,
                        isSuccess ? success : error
                    ]);
                }
                completeDeferred.fireWith(callbackContext, [
                    jqXHR,
                    statusText
                ]);
                if (fireGlobals) {
                    globalEventContext.trigger('ajaxComplete', [
                        jqXHR,
                        s
                    ]);
                    if (!--jQuery.active) {
                        jQuery.event.trigger('ajaxStop');
                    }
                }
            }
            return jqXHR;
        },
        getScript: function (url, callback) {
            return jQuery.get(url, undefined, callback, 'script');
        },
        getJSON: function (url, data, callback) {
            return jQuery.get(url, data, callback, 'json');
        }
    });
    function ajaxHandleResponses(s, jqXHR, responses) {
        var firstDataType, ct, finalDataType, type, contents = s.contents, dataTypes = s.dataTypes, responseFields = s.responseFields;
        for (type in responseFields) {
            if (type in responses) {
                jqXHR[responseFields[type]] = responses[type];
            }
        }
        while (dataTypes[0] === '*') {
            dataTypes.shift();
            if (ct === undefined) {
                ct = s.mimeType || jqXHR.getResponseHeader('Content-Type');
            }
        }
        if (ct) {
            for (type in contents) {
                if (contents[type] && contents[type].test(ct)) {
                    dataTypes.unshift(type);
                    break;
                }
            }
        }
        if (dataTypes[0] in responses) {
            finalDataType = dataTypes[0];
        } else {
            for (type in responses) {
                if (!dataTypes[0] || s.converters[type + ' ' + dataTypes[0]]) {
                    finalDataType = type;
                    break;
                }
                if (!firstDataType) {
                    firstDataType = type;
                }
            }
            finalDataType = finalDataType || firstDataType;
        }
        if (finalDataType) {
            if (finalDataType !== dataTypes[0]) {
                dataTypes.unshift(finalDataType);
            }
            return responses[finalDataType];
        }
    }
    function ajaxConvert(s, response) {
        var conv2, current, conv, tmp, converters = {}, i = 0, dataTypes = s.dataTypes.slice(), prev = dataTypes[0];
        if (s.dataFilter) {
            response = s.dataFilter(response, s.dataType);
        }
        if (dataTypes[1]) {
            for (conv in s.converters) {
                converters[conv.toLowerCase()] = s.converters[conv];
            }
        }
        for (; current = dataTypes[++i];) {
            if (current !== '*') {
                if (prev !== '*' && prev !== current) {
                    conv = converters[prev + ' ' + current] || converters['* ' + current];
                    if (!conv) {
                        for (conv2 in converters) {
                            tmp = conv2.split(' ');
                            if (tmp[1] === current) {
                                conv = converters[prev + ' ' + tmp[0]] || converters['* ' + tmp[0]];
                                if (conv) {
                                    if (conv === true) {
                                        conv = converters[conv2];
                                    } else if (converters[conv2] !== true) {
                                        current = tmp[0];
                                        dataTypes.splice(i--, 0, current);
                                    }
                                    break;
                                }
                            }
                        }
                    }
                    if (conv !== true) {
                        if (conv && s['throws']) {
                            response = conv(response);
                        } else {
                            try {
                                response = conv(response);
                            } catch (e) {
                                return {
                                    state: 'parsererror',
                                    error: conv ? e : 'No conversion from ' + prev + ' to ' + current
                                };
                            }
                        }
                    }
                }
                prev = current;
            }
        }
        return {
            state: 'success',
            data: response
        };
    }
    jQuery.ajaxSetup({
        accepts: { script: 'text/javascript, application/javascript, application/ecmascript, application/x-ecmascript' },
        contents: { script: /(?:java|ecma)script/ },
        converters: {
            'text script': function (text) {
                jQuery.globalEval(text);
                return text;
            }
        }
    });
    jQuery.ajaxPrefilter('script', function (s) {
        if (s.cache === undefined) {
            s.cache = false;
        }
        if (s.crossDomain) {
            s.type = 'GET';
            s.global = false;
        }
    });
    jQuery.ajaxTransport('script', function (s) {
        if (s.crossDomain) {
            var script, head = document.head || jQuery('head')[0] || document.documentElement;
            return {
                send: function (_, callback) {
                    script = document.createElement('script');
                    script.async = true;
                    if (s.scriptCharset) {
                        script.charset = s.scriptCharset;
                    }
                    script.src = s.url;
                    script.onload = script.onreadystatechange = function (_, isAbort) {
                        if (isAbort || !script.readyState || /loaded|complete/.test(script.readyState)) {
                            script.onload = script.onreadystatechange = null;
                            if (script.parentNode) {
                                script.parentNode.removeChild(script);
                            }
                            script = null;
                            if (!isAbort) {
                                callback(200, 'success');
                            }
                        }
                    };
                    head.insertBefore(script, head.firstChild);
                },
                abort: function () {
                    if (script) {
                        script.onload(undefined, true);
                    }
                }
            };
        }
    });
    var oldCallbacks = [], rjsonp = /(=)\?(?=&|$)|\?\?/;
    jQuery.ajaxSetup({
        jsonp: 'callback',
        jsonpCallback: function () {
            var callback = oldCallbacks.pop() || jQuery.expando + '_' + ajax_nonce++;
            this[callback] = true;
            return callback;
        }
    });
    jQuery.ajaxPrefilter('json jsonp', function (s, originalSettings, jqXHR) {
        var callbackName, overwritten, responseContainer, jsonProp = s.jsonp !== false && (rjsonp.test(s.url) ? 'url' : typeof s.data === 'string' && !(s.contentType || '').indexOf('application/x-www-form-urlencoded') && rjsonp.test(s.data) && 'data');
        if (jsonProp || s.dataTypes[0] === 'jsonp') {
            callbackName = s.jsonpCallback = jQuery.isFunction(s.jsonpCallback) ? s.jsonpCallback() : s.jsonpCallback;
            if (jsonProp) {
                s[jsonProp] = s[jsonProp].replace(rjsonp, '$1' + callbackName);
            } else if (s.jsonp !== false) {
                s.url += (ajax_rquery.test(s.url) ? '&' : '?') + s.jsonp + '=' + callbackName;
            }
            s.converters['script json'] = function () {
                if (!responseContainer) {
                    jQuery.error(callbackName + ' was not called');
                }
                return responseContainer[0];
            };
            s.dataTypes[0] = 'json';
            overwritten = window[callbackName];
            window[callbackName] = function () {
                responseContainer = arguments;
            };
            jqXHR.always(function () {
                window[callbackName] = overwritten;
                if (s[callbackName]) {
                    s.jsonpCallback = originalSettings.jsonpCallback;
                    oldCallbacks.push(callbackName);
                }
                if (responseContainer && jQuery.isFunction(overwritten)) {
                    overwritten(responseContainer[0]);
                }
                responseContainer = overwritten = undefined;
            });
            return 'script';
        }
    });
    var xhrCallbacks, xhrSupported, xhrId = 0, xhrOnUnloadAbort = window.ActiveXObject && function () {
            var key;
            for (key in xhrCallbacks) {
                xhrCallbacks[key](undefined, true);
            }
        };
    function createStandardXHR() {
        try {
            return new window.XMLHttpRequest();
        } catch (e) {
        }
    }
    function createActiveXHR() {
        try {
            return new window.ActiveXObject('Microsoft.XMLHTTP');
        } catch (e) {
        }
    }
    jQuery.ajaxSettings.xhr = window.ActiveXObject ? function () {
        return !this.isLocal && createStandardXHR() || createActiveXHR();
    } : createStandardXHR;
    xhrSupported = jQuery.ajaxSettings.xhr();
    jQuery.support.cors = !!xhrSupported && 'withCredentials' in xhrSupported;
    xhrSupported = jQuery.support.ajax = !!xhrSupported;
    if (xhrSupported) {
        jQuery.ajaxTransport(function (s) {
            if (!s.crossDomain || jQuery.support.cors) {
                var callback;
                return {
                    send: function (headers, complete) {
                        var handle, i, xhr = s.xhr();
                        if (s.username) {
                            xhr.open(s.type, s.url, s.async, s.username, s.password);
                        } else {
                            xhr.open(s.type, s.url, s.async);
                        }
                        if (s.xhrFields) {
                            for (i in s.xhrFields) {
                                xhr[i] = s.xhrFields[i];
                            }
                        }
                        if (s.mimeType && xhr.overrideMimeType) {
                            xhr.overrideMimeType(s.mimeType);
                        }
                        if (!s.crossDomain && !headers['X-Requested-With']) {
                            headers['X-Requested-With'] = 'XMLHttpRequest';
                        }
                        try {
                            for (i in headers) {
                                xhr.setRequestHeader(i, headers[i]);
                            }
                        } catch (err) {
                        }
                        xhr.send(s.hasContent && s.data || null);
                        callback = function (_, isAbort) {
                            var status, responseHeaders, statusText, responses;
                            try {
                                if (callback && (isAbort || xhr.readyState === 4)) {
                                    callback = undefined;
                                    if (handle) {
                                        xhr.onreadystatechange = jQuery.noop;
                                        if (xhrOnUnloadAbort) {
                                            delete xhrCallbacks[handle];
                                        }
                                    }
                                    if (isAbort) {
                                        if (xhr.readyState !== 4) {
                                            xhr.abort();
                                        }
                                    } else {
                                        responses = {};
                                        status = xhr.status;
                                        responseHeaders = xhr.getAllResponseHeaders();
                                        if (typeof xhr.responseText === 'string') {
                                            responses.text = xhr.responseText;
                                        }
                                        try {
                                            statusText = xhr.statusText;
                                        } catch (e) {
                                            statusText = '';
                                        }
                                        if (!status && s.isLocal && !s.crossDomain) {
                                            status = responses.text ? 200 : 404;
                                        } else if (status === 1223) {
                                            status = 204;
                                        }
                                    }
                                }
                            } catch (firefoxAccessException) {
                                if (!isAbort) {
                                    complete(-1, firefoxAccessException);
                                }
                            }
                            if (responses) {
                                complete(status, statusText, responses, responseHeaders);
                            }
                        };
                        if (!s.async) {
                            callback();
                        } else if (xhr.readyState === 4) {
                            setTimeout(callback);
                        } else {
                            handle = ++xhrId;
                            if (xhrOnUnloadAbort) {
                                if (!xhrCallbacks) {
                                    xhrCallbacks = {};
                                    jQuery(window).unload(xhrOnUnloadAbort);
                                }
                                xhrCallbacks[handle] = callback;
                            }
                            xhr.onreadystatechange = callback;
                        }
                    },
                    abort: function () {
                        if (callback) {
                            callback(undefined, true);
                        }
                    }
                };
            }
        });
    }
    var fxNow, timerId, rfxtypes = /^(?:toggle|show|hide)$/, rfxnum = new RegExp('^(?:([+-])=|)(' + core_pnum + ')([a-z%]*)$', 'i'), rrun = /queueHooks$/, animationPrefilters = [defaultPrefilter], tweeners = {
            '*': [function (prop, value) {
                    var end, unit, tween = this.createTween(prop, value), parts = rfxnum.exec(value), target = tween.cur(), start = +target || 0, scale = 1, maxIterations = 20;
                    if (parts) {
                        end = +parts[2];
                        unit = parts[3] || (jQuery.cssNumber[prop] ? '' : 'px');
                        if (unit !== 'px' && start) {
                            start = jQuery.css(tween.elem, prop, true) || end || 1;
                            do {
                                scale = scale || '.5';
                                start = start / scale;
                                jQuery.style(tween.elem, prop, start + unit);
                            } while (scale !== (scale = tween.cur() / target) && scale !== 1 && --maxIterations);
                        }
                        tween.unit = unit;
                        tween.start = start;
                        tween.end = parts[1] ? start + (parts[1] + 1) * end : end;
                    }
                    return tween;
                }]
        };
    function createFxNow() {
        setTimeout(function () {
            fxNow = undefined;
        });
        return fxNow = jQuery.now();
    }
    function createTweens(animation, props) {
        jQuery.each(props, function (prop, value) {
            var collection = (tweeners[prop] || []).concat(tweeners['*']), index = 0, length = collection.length;
            for (; index < length; index++) {
                if (collection[index].call(animation, prop, value)) {
                    return;
                }
            }
        });
    }
    function Animation(elem, properties, options) {
        var result, stopped, index = 0, length = animationPrefilters.length, deferred = jQuery.Deferred().always(function () {
                delete tick.elem;
            }), tick = function () {
                if (stopped) {
                    return false;
                }
                var currentTime = fxNow || createFxNow(), remaining = Math.max(0, animation.startTime + animation.duration - currentTime), temp = remaining / animation.duration || 0, percent = 1 - temp, index = 0, length = animation.tweens.length;
                for (; index < length; index++) {
                    animation.tweens[index].run(percent);
                }
                deferred.notifyWith(elem, [
                    animation,
                    percent,
                    remaining
                ]);
                if (percent < 1 && length) {
                    return remaining;
                } else {
                    deferred.resolveWith(elem, [animation]);
                    return false;
                }
            }, animation = deferred.promise({
                elem: elem,
                props: jQuery.extend({}, properties),
                opts: jQuery.extend(true, { specialEasing: {} }, options),
                originalProperties: properties,
                originalOptions: options,
                startTime: fxNow || createFxNow(),
                duration: options.duration,
                tweens: [],
                createTween: function (prop, end) {
                    var tween = jQuery.Tween(elem, animation.opts, prop, end, animation.opts.specialEasing[prop] || animation.opts.easing);
                    animation.tweens.push(tween);
                    return tween;
                },
                stop: function (gotoEnd) {
                    var index = 0, length = gotoEnd ? animation.tweens.length : 0;
                    if (stopped) {
                        return this;
                    }
                    stopped = true;
                    for (; index < length; index++) {
                        animation.tweens[index].run(1);
                    }
                    if (gotoEnd) {
                        deferred.resolveWith(elem, [
                            animation,
                            gotoEnd
                        ]);
                    } else {
                        deferred.rejectWith(elem, [
                            animation,
                            gotoEnd
                        ]);
                    }
                    return this;
                }
            }), props = animation.props;
        propFilter(props, animation.opts.specialEasing);
        for (; index < length; index++) {
            result = animationPrefilters[index].call(animation, elem, props, animation.opts);
            if (result) {
                return result;
            }
        }
        createTweens(animation, props);
        if (jQuery.isFunction(animation.opts.start)) {
            animation.opts.start.call(elem, animation);
        }
        jQuery.fx.timer(jQuery.extend(tick, {
            elem: elem,
            anim: animation,
            queue: animation.opts.queue
        }));
        return animation.progress(animation.opts.progress).done(animation.opts.done, animation.opts.complete).fail(animation.opts.fail).always(animation.opts.always);
    }
    function propFilter(props, specialEasing) {
        var value, name, index, easing, hooks;
        for (index in props) {
            name = jQuery.camelCase(index);
            easing = specialEasing[name];
            value = props[index];
            if (jQuery.isArray(value)) {
                easing = value[1];
                value = props[index] = value[0];
            }
            if (index !== name) {
                props[name] = value;
                delete props[index];
            }
            hooks = jQuery.cssHooks[name];
            if (hooks && 'expand' in hooks) {
                value = hooks.expand(value);
                delete props[name];
                for (index in value) {
                    if (!(index in props)) {
                        props[index] = value[index];
                        specialEasing[index] = easing;
                    }
                }
            } else {
                specialEasing[name] = easing;
            }
        }
    }
    jQuery.Animation = jQuery.extend(Animation, {
        tweener: function (props, callback) {
            if (jQuery.isFunction(props)) {
                callback = props;
                props = ['*'];
            } else {
                props = props.split(' ');
            }
            var prop, index = 0, length = props.length;
            for (; index < length; index++) {
                prop = props[index];
                tweeners[prop] = tweeners[prop] || [];
                tweeners[prop].unshift(callback);
            }
        },
        prefilter: function (callback, prepend) {
            if (prepend) {
                animationPrefilters.unshift(callback);
            } else {
                animationPrefilters.push(callback);
            }
        }
    });
    function defaultPrefilter(elem, props, opts) {
        var prop, index, length, value, dataShow, toggle, tween, hooks, oldfire, anim = this, style = elem.style, orig = {}, handled = [], hidden = elem.nodeType && isHidden(elem);
        if (!opts.queue) {
            hooks = jQuery._queueHooks(elem, 'fx');
            if (hooks.unqueued == null) {
                hooks.unqueued = 0;
                oldfire = hooks.empty.fire;
                hooks.empty.fire = function () {
                    if (!hooks.unqueued) {
                        oldfire();
                    }
                };
            }
            hooks.unqueued++;
            anim.always(function () {
                anim.always(function () {
                    hooks.unqueued--;
                    if (!jQuery.queue(elem, 'fx').length) {
                        hooks.empty.fire();
                    }
                });
            });
        }
        if (elem.nodeType === 1 && ('height' in props || 'width' in props)) {
            opts.overflow = [
                style.overflow,
                style.overflowX,
                style.overflowY
            ];
            if (jQuery.css(elem, 'display') === 'inline' && jQuery.css(elem, 'float') === 'none') {
                if (!jQuery.support.inlineBlockNeedsLayout || css_defaultDisplay(elem.nodeName) === 'inline') {
                    style.display = 'inline-block';
                } else {
                    style.zoom = 1;
                }
            }
        }
        if (opts.overflow) {
            style.overflow = 'hidden';
            if (!jQuery.support.shrinkWrapBlocks) {
                anim.always(function () {
                    style.overflow = opts.overflow[0];
                    style.overflowX = opts.overflow[1];
                    style.overflowY = opts.overflow[2];
                });
            }
        }
        for (index in props) {
            value = props[index];
            if (rfxtypes.exec(value)) {
                delete props[index];
                toggle = toggle || value === 'toggle';
                if (value === (hidden ? 'hide' : 'show')) {
                    continue;
                }
                handled.push(index);
            }
        }
        length = handled.length;
        if (length) {
            dataShow = jQuery._data(elem, 'fxshow') || jQuery._data(elem, 'fxshow', {});
            if ('hidden' in dataShow) {
                hidden = dataShow.hidden;
            }
            if (toggle) {
                dataShow.hidden = !hidden;
            }
            if (hidden) {
                jQuery(elem).show();
            } else {
                anim.done(function () {
                    jQuery(elem).hide();
                });
            }
            anim.done(function () {
                var prop;
                jQuery._removeData(elem, 'fxshow');
                for (prop in orig) {
                    jQuery.style(elem, prop, orig[prop]);
                }
            });
            for (index = 0; index < length; index++) {
                prop = handled[index];
                tween = anim.createTween(prop, hidden ? dataShow[prop] : 0);
                orig[prop] = dataShow[prop] || jQuery.style(elem, prop);
                if (!(prop in dataShow)) {
                    dataShow[prop] = tween.start;
                    if (hidden) {
                        tween.end = tween.start;
                        tween.start = prop === 'width' || prop === 'height' ? 1 : 0;
                    }
                }
            }
        }
    }
    function Tween(elem, options, prop, end, easing) {
        return new Tween.prototype.init(elem, options, prop, end, easing);
    }
    jQuery.Tween = Tween;
    Tween.prototype = {
        constructor: Tween,
        init: function (elem, options, prop, end, easing, unit) {
            this.elem = elem;
            this.prop = prop;
            this.easing = easing || 'swing';
            this.options = options;
            this.start = this.now = this.cur();
            this.end = end;
            this.unit = unit || (jQuery.cssNumber[prop] ? '' : 'px');
        },
        cur: function () {
            var hooks = Tween.propHooks[this.prop];
            return hooks && hooks.get ? hooks.get(this) : Tween.propHooks._default.get(this);
        },
        run: function (percent) {
            var eased, hooks = Tween.propHooks[this.prop];
            if (this.options.duration) {
                this.pos = eased = jQuery.easing[this.easing](percent, this.options.duration * percent, 0, 1, this.options.duration);
            } else {
                this.pos = eased = percent;
            }
            this.now = (this.end - this.start) * eased + this.start;
            if (this.options.step) {
                this.options.step.call(this.elem, this.now, this);
            }
            if (hooks && hooks.set) {
                hooks.set(this);
            } else {
                Tween.propHooks._default.set(this);
            }
            return this;
        }
    };
    Tween.prototype.init.prototype = Tween.prototype;
    Tween.propHooks = {
        _default: {
            get: function (tween) {
                var result;
                if (tween.elem[tween.prop] != null && (!tween.elem.style || tween.elem.style[tween.prop] == null)) {
                    return tween.elem[tween.prop];
                }
                result = jQuery.css(tween.elem, tween.prop, '');
                return !result || result === 'auto' ? 0 : result;
            },
            set: function (tween) {
                if (jQuery.fx.step[tween.prop]) {
                    jQuery.fx.step[tween.prop](tween);
                } else if (tween.elem.style && (tween.elem.style[jQuery.cssProps[tween.prop]] != null || jQuery.cssHooks[tween.prop])) {
                    jQuery.style(tween.elem, tween.prop, tween.now + tween.unit);
                } else {
                    tween.elem[tween.prop] = tween.now;
                }
            }
        }
    };
    Tween.propHooks.scrollTop = Tween.propHooks.scrollLeft = {
        set: function (tween) {
            if (tween.elem.nodeType && tween.elem.parentNode) {
                tween.elem[tween.prop] = tween.now;
            }
        }
    };
    jQuery.each([
        'toggle',
        'show',
        'hide'
    ], function (i, name) {
        var cssFn = jQuery.fn[name];
        jQuery.fn[name] = function (speed, easing, callback) {
            return speed == null || typeof speed === 'boolean' ? cssFn.apply(this, arguments) : this.animate(genFx(name, true), speed, easing, callback);
        };
    });
    jQuery.fn.extend({
        fadeTo: function (speed, to, easing, callback) {
            return this.filter(isHidden).css('opacity', 0).show().end().animate({ opacity: to }, speed, easing, callback);
        },
        animate: function (prop, speed, easing, callback) {
            var empty = jQuery.isEmptyObject(prop), optall = jQuery.speed(speed, easing, callback), doAnimation = function () {
                    var anim = Animation(this, jQuery.extend({}, prop), optall);
                    doAnimation.finish = function () {
                        anim.stop(true);
                    };
                    if (empty || jQuery._data(this, 'finish')) {
                        anim.stop(true);
                    }
                };
            doAnimation.finish = doAnimation;
            return empty || optall.queue === false ? this.each(doAnimation) : this.queue(optall.queue, doAnimation);
        },
        stop: function (type, clearQueue, gotoEnd) {
            var stopQueue = function (hooks) {
                var stop = hooks.stop;
                delete hooks.stop;
                stop(gotoEnd);
            };
            if (typeof type !== 'string') {
                gotoEnd = clearQueue;
                clearQueue = type;
                type = undefined;
            }
            if (clearQueue && type !== false) {
                this.queue(type || 'fx', []);
            }
            return this.each(function () {
                var dequeue = true, index = type != null && type + 'queueHooks', timers = jQuery.timers, data = jQuery._data(this);
                if (index) {
                    if (data[index] && data[index].stop) {
                        stopQueue(data[index]);
                    }
                } else {
                    for (index in data) {
                        if (data[index] && data[index].stop && rrun.test(index)) {
                            stopQueue(data[index]);
                        }
                    }
                }
                for (index = timers.length; index--;) {
                    if (timers[index].elem === this && (type == null || timers[index].queue === type)) {
                        timers[index].anim.stop(gotoEnd);
                        dequeue = false;
                        timers.splice(index, 1);
                    }
                }
                if (dequeue || !gotoEnd) {
                    jQuery.dequeue(this, type);
                }
            });
        },
        finish: function (type) {
            if (type !== false) {
                type = type || 'fx';
            }
            return this.each(function () {
                var index, data = jQuery._data(this), queue = data[type + 'queue'], hooks = data[type + 'queueHooks'], timers = jQuery.timers, length = queue ? queue.length : 0;
                data.finish = true;
                jQuery.queue(this, type, []);
                if (hooks && hooks.cur && hooks.cur.finish) {
                    hooks.cur.finish.call(this);
                }
                for (index = timers.length; index--;) {
                    if (timers[index].elem === this && timers[index].queue === type) {
                        timers[index].anim.stop(true);
                        timers.splice(index, 1);
                    }
                }
                for (index = 0; index < length; index++) {
                    if (queue[index] && queue[index].finish) {
                        queue[index].finish.call(this);
                    }
                }
                delete data.finish;
            });
        }
    });
    function genFx(type, includeWidth) {
        var which, attrs = { height: type }, i = 0;
        includeWidth = includeWidth ? 1 : 0;
        for (; i < 4; i += 2 - includeWidth) {
            which = cssExpand[i];
            attrs['margin' + which] = attrs['padding' + which] = type;
        }
        if (includeWidth) {
            attrs.opacity = attrs.width = type;
        }
        return attrs;
    }
    jQuery.each({
        slideDown: genFx('show'),
        slideUp: genFx('hide'),
        slideToggle: genFx('toggle'),
        fadeIn: { opacity: 'show' },
        fadeOut: { opacity: 'hide' },
        fadeToggle: { opacity: 'toggle' }
    }, function (name, props) {
        jQuery.fn[name] = function (speed, easing, callback) {
            return this.animate(props, speed, easing, callback);
        };
    });
    jQuery.speed = function (speed, easing, fn) {
        var opt = speed && typeof speed === 'object' ? jQuery.extend({}, speed) : {
                complete: fn || !fn && easing || jQuery.isFunction(speed) && speed,
                duration: speed,
                easing: fn && easing || easing && !jQuery.isFunction(easing) && easing
            };
        opt.duration = jQuery.fx.off ? 0 : typeof opt.duration === 'number' ? opt.duration : opt.duration in jQuery.fx.speeds ? jQuery.fx.speeds[opt.duration] : jQuery.fx.speeds._default;
        if (opt.queue == null || opt.queue === true) {
            opt.queue = 'fx';
        }
        opt.old = opt.complete;
        opt.complete = function () {
            if (jQuery.isFunction(opt.old)) {
                opt.old.call(this);
            }
            if (opt.queue) {
                jQuery.dequeue(this, opt.queue);
            }
        };
        return opt;
    };
    jQuery.easing = {
        linear: function (p) {
            return p;
        },
        swing: function (p) {
            return 0.5 - Math.cos(p * Math.PI) / 2;
        }
    };
    jQuery.timers = [];
    jQuery.fx = Tween.prototype.init;
    jQuery.fx.tick = function () {
        var timer, timers = jQuery.timers, i = 0;
        fxNow = jQuery.now();
        for (; i < timers.length; i++) {
            timer = timers[i];
            if (!timer() && timers[i] === timer) {
                timers.splice(i--, 1);
            }
        }
        if (!timers.length) {
            jQuery.fx.stop();
        }
        fxNow = undefined;
    };
    jQuery.fx.timer = function (timer) {
        if (timer() && jQuery.timers.push(timer)) {
            jQuery.fx.start();
        }
    };
    jQuery.fx.interval = 13;
    jQuery.fx.start = function () {
        if (!timerId) {
            timerId = setInterval(jQuery.fx.tick, jQuery.fx.interval);
        }
    };
    jQuery.fx.stop = function () {
        clearInterval(timerId);
        timerId = null;
    };
    jQuery.fx.speeds = {
        slow: 600,
        fast: 200,
        _default: 400
    };
    jQuery.fx.step = {};
    if (jQuery.expr && jQuery.expr.filters) {
        jQuery.expr.filters.animated = function (elem) {
            return jQuery.grep(jQuery.timers, function (fn) {
                return elem === fn.elem;
            }).length;
        };
    }
    jQuery.fn.offset = function (options) {
        if (arguments.length) {
            return options === undefined ? this : this.each(function (i) {
                jQuery.offset.setOffset(this, options, i);
            });
        }
        var docElem, win, box = {
                top: 0,
                left: 0
            }, elem = this[0], doc = elem && elem.ownerDocument;
        if (!doc) {
            return;
        }
        docElem = doc.documentElement;
        if (!jQuery.contains(docElem, elem)) {
            return box;
        }
        if (typeof elem.getBoundingClientRect !== core_strundefined) {
            box = elem.getBoundingClientRect();
        }
        win = getWindow(doc);
        return {
            top: box.top + (win.pageYOffset || docElem.scrollTop) - (docElem.clientTop || 0),
            left: box.left + (win.pageXOffset || docElem.scrollLeft) - (docElem.clientLeft || 0)
        };
    };
    jQuery.offset = {
        setOffset: function (elem, options, i) {
            var position = jQuery.css(elem, 'position');
            if (position === 'static') {
                elem.style.position = 'relative';
            }
            var curElem = jQuery(elem), curOffset = curElem.offset(), curCSSTop = jQuery.css(elem, 'top'), curCSSLeft = jQuery.css(elem, 'left'), calculatePosition = (position === 'absolute' || position === 'fixed') && jQuery.inArray('auto', [
                    curCSSTop,
                    curCSSLeft
                ]) > -1, props = {}, curPosition = {}, curTop, curLeft;
            if (calculatePosition) {
                curPosition = curElem.position();
                curTop = curPosition.top;
                curLeft = curPosition.left;
            } else {
                curTop = parseFloat(curCSSTop) || 0;
                curLeft = parseFloat(curCSSLeft) || 0;
            }
            if (jQuery.isFunction(options)) {
                options = options.call(elem, i, curOffset);
            }
            if (options.top != null) {
                props.top = options.top - curOffset.top + curTop;
            }
            if (options.left != null) {
                props.left = options.left - curOffset.left + curLeft;
            }
            if ('using' in options) {
                options.using.call(elem, props);
            } else {
                curElem.css(props);
            }
        }
    };
    jQuery.fn.extend({
        position: function () {
            if (!this[0]) {
                return;
            }
            var offsetParent, offset, parentOffset = {
                    top: 0,
                    left: 0
                }, elem = this[0];
            if (jQuery.css(elem, 'position') === 'fixed') {
                offset = elem.getBoundingClientRect();
            } else {
                offsetParent = this.offsetParent();
                offset = this.offset();
                if (!jQuery.nodeName(offsetParent[0], 'html')) {
                    parentOffset = offsetParent.offset();
                }
                parentOffset.top += jQuery.css(offsetParent[0], 'borderTopWidth', true);
                parentOffset.left += jQuery.css(offsetParent[0], 'borderLeftWidth', true);
            }
            return {
                top: offset.top - parentOffset.top - jQuery.css(elem, 'marginTop', true),
                left: offset.left - parentOffset.left - jQuery.css(elem, 'marginLeft', true)
            };
        },
        offsetParent: function () {
            return this.map(function () {
                var offsetParent = this.offsetParent || document.documentElement;
                while (offsetParent && !jQuery.nodeName(offsetParent, 'html') && jQuery.css(offsetParent, 'position') === 'static') {
                    offsetParent = offsetParent.offsetParent;
                }
                return offsetParent || document.documentElement;
            });
        }
    });
    jQuery.each({
        scrollLeft: 'pageXOffset',
        scrollTop: 'pageYOffset'
    }, function (method, prop) {
        var top = /Y/.test(prop);
        jQuery.fn[method] = function (val) {
            return jQuery.access(this, function (elem, method, val) {
                var win = getWindow(elem);
                if (val === undefined) {
                    return win ? prop in win ? win[prop] : win.document.documentElement[method] : elem[method];
                }
                if (win) {
                    win.scrollTo(!top ? val : jQuery(win).scrollLeft(), top ? val : jQuery(win).scrollTop());
                } else {
                    elem[method] = val;
                }
            }, method, val, arguments.length, null);
        };
    });
    function getWindow(elem) {
        return jQuery.isWindow(elem) ? elem : elem.nodeType === 9 ? elem.defaultView || elem.parentWindow : false;
    }
    jQuery.each({
        Height: 'height',
        Width: 'width'
    }, function (name, type) {
        jQuery.each({
            padding: 'inner' + name,
            content: type,
            '': 'outer' + name
        }, function (defaultExtra, funcName) {
            jQuery.fn[funcName] = function (margin, value) {
                var chainable = arguments.length && (defaultExtra || typeof margin !== 'boolean'), extra = defaultExtra || (margin === true || value === true ? 'margin' : 'border');
                return jQuery.access(this, function (elem, type, value) {
                    var doc;
                    if (jQuery.isWindow(elem)) {
                        return elem.document.documentElement['client' + name];
                    }
                    if (elem.nodeType === 9) {
                        doc = elem.documentElement;
                        return Math.max(elem.body['scroll' + name], doc['scroll' + name], elem.body['offset' + name], doc['offset' + name], doc['client' + name]);
                    }
                    return value === undefined ? jQuery.css(elem, type, extra) : jQuery.style(elem, type, value, extra);
                }, type, chainable ? margin : undefined, chainable, null);
            };
        });
    });
    if (typeof define === 'function' && define.amd) {
        define('jquery/jquery.min', [], function () {
            return jQuery;
        });
    }
}(window));

define('jquery', ['jquery/jquery.min'], function ( main ) { return main; });

define('common/common.tpl', [], function () {
    return '<!--* @ignore* @file error.tpl* @author mySunShinning(441984145@qq.com)*         yangbinYB(1033371745@qq.com)* @time 14-12-7--><!-- target: Loading --><div class="tuzhi-loading">&nbsp;</div><!-- /target --><!-- target: Error --><div class="tuzhi-error"><div class="tuzhi-error-msg">${msg}</div></div><!-- /target -->';
});

(function (root) {
    function extend(target, source) {
        for (var key in source) {
            if (source.hasOwnProperty(key)) {
                target[key] = source[key];
            }
        }
        return target;
    }
    function Stack() {
        this.raw = [];
        this.length = 0;
    }
    Stack.prototype = {
        push: function (elem) {
            this.raw[this.length++] = elem;
        },
        pop: function () {
            if (this.length > 0) {
                var elem = this.raw[--this.length];
                this.raw.length = this.length;
                return elem;
            }
        },
        top: function () {
            return this.raw[this.length - 1];
        },
        bottom: function () {
            return this.raw[0];
        },
        find: function (condition) {
            var index = this.length;
            while (index--) {
                var item = this.raw[index];
                if (condition(item)) {
                    return item;
                }
            }
        }
    };
    var guidIndex = 178245;
    function generateGUID() {
        return '___' + guidIndex++;
    }
    function inherits(subClass, superClass) {
        var F = new Function();
        F.prototype = superClass.prototype;
        subClass.prototype = new F();
        subClass.prototype.constructor = subClass;
    }
    var HTML_ENTITY = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            '\'': '&#39;'
        };
    function htmlFilterReplacer(c) {
        return HTML_ENTITY[c];
    }
    var DEFAULT_FILTERS = {
            html: function (source) {
                return source.replace(/[&<>"']/g, htmlFilterReplacer);
            },
            url: encodeURIComponent,
            raw: function (source) {
                return source;
            }
        };
    function stringLiteralize(source) {
        return '"' + source.replace(/\x5C/g, '\\\\').replace(/"/g, '\\"').replace(/\x0A/g, '\\n').replace(/\x09/g, '\\t').replace(/\x0D/g, '\\r') + '"';
    }
    function regexpLiteral(source) {
        return source.replace(/[\^\[\]\$\(\)\{\}\?\*\.\+]/g, function (c) {
            return '\\' + c;
        });
    }
    function stringFormat(source) {
        var args = arguments;
        return source.replace(/\{([0-9]+)\}/g, function (match, index) {
            return args[index - 0 + 1];
        });
    }
    var RENDER_STRING_DECLATION = 'var r="";';
    var RENDER_STRING_ADD_START = 'r+=';
    var RENDER_STRING_ADD_END = ';';
    var RENDER_STRING_RETURN = 'return r;';
    if (typeof navigator !== 'undefined' && /msie\s*([0-9]+)/i.test(navigator.userAgent) && RegExp.$1 - 0 < 8) {
        RENDER_STRING_DECLATION = 'var r=[],ri=0;';
        RENDER_STRING_ADD_START = 'r[ri++]=';
        RENDER_STRING_RETURN = 'return r.join("");';
    }
    function toGetVariableLiteral(name) {
        name = name.replace(/^\s*\*/, '');
        return stringFormat('gv({0},["{1}"])', stringLiteralize(name), name.replace(/\[['"]?([^'"]+)['"]?\]/g, function (match, name) {
            return '.' + name;
        }).split('.').join('","'));
    }
    function parseTextBlock(source, open, close, greedy, onInBlock, onOutBlock) {
        var closeLen = close.length;
        var texts = source.split(open);
        var level = 0;
        var buf = [];
        for (var i = 0, len = texts.length; i < len; i++) {
            var text = texts[i];
            if (i) {
                var openBegin = 1;
                level++;
                while (1) {
                    var closeIndex = text.indexOf(close);
                    if (closeIndex < 0) {
                        buf.push(level > 1 && openBegin ? open : '', text);
                        break;
                    }
                    level = greedy ? level - 1 : 0;
                    buf.push(level > 0 && openBegin ? open : '', text.slice(0, closeIndex), level > 0 ? close : '');
                    text = text.slice(closeIndex + closeLen);
                    openBegin = 0;
                    if (level === 0) {
                        break;
                    }
                }
                if (level === 0) {
                    onInBlock(buf.join(''));
                    onOutBlock(text);
                    buf = [];
                }
            } else {
                text && onOutBlock(text);
            }
        }
        if (level > 0 && buf.length > 0) {
            onOutBlock(open);
            onOutBlock(buf.join(''));
        }
    }
    function compileVariable(source, engine, forText) {
        var code = [];
        var options = engine.options;
        var toStringHead = '';
        var toStringFoot = '';
        var wrapHead = '';
        var wrapFoot = '';
        var defaultFilter;
        if (forText) {
            toStringHead = 'ts(';
            toStringFoot = ')';
            wrapHead = RENDER_STRING_ADD_START;
            wrapFoot = RENDER_STRING_ADD_END;
            defaultFilter = options.defaultFilter;
        }
        parseTextBlock(source, options.variableOpen, options.variableClose, 1, function (text) {
            if (forText && text.indexOf('|') < 0 && defaultFilter) {
                text += '|' + defaultFilter;
            }
            var filterCharIndex = text.indexOf('|');
            var variableName = (filterCharIndex > 0 ? text.slice(0, filterCharIndex) : text).replace(/^\s+/, '').replace(/\s+$/, '');
            var filterSource = filterCharIndex > 0 ? text.slice(filterCharIndex + 1) : '';
            var variableRawValue = variableName.indexOf('*') === 0;
            var variableCode = [
                    variableRawValue ? '' : toStringHead,
                    toGetVariableLiteral(variableName),
                    variableRawValue ? '' : toStringFoot
                ];
            if (filterSource) {
                filterSource = compileVariable(filterSource, engine);
                var filterSegs = filterSource.split('|');
                for (var i = 0, len = filterSegs.length; i < len; i++) {
                    var seg = filterSegs[i];
                    if (/^\s*([a-z0-9_-]+)(\((.*)\))?\s*$/i.test(seg)) {
                        variableCode.unshift('fs["' + RegExp.$1 + '"](');
                        if (RegExp.$3) {
                            variableCode.push(',', RegExp.$3);
                        }
                        variableCode.push(')');
                    }
                }
            }
            code.push(wrapHead, variableCode.join(''), wrapFoot);
        }, function (text) {
            code.push(wrapHead, forText ? stringLiteralize(text) : text, wrapFoot);
        });
        return code.join('');
    }
    function TextNode(value, engine) {
        this.value = value;
        this.engine = engine;
    }
    TextNode.prototype = {
        getRendererBody: function () {
            var value = this.value;
            var options = this.engine.options;
            if (!value || options.strip && /^\s*$/.test(value)) {
                return '';
            }
            return compileVariable(value, this.engine, 1);
        },
        clone: function () {
            return this;
        }
    };
    function Command(value, engine) {
        this.value = value;
        this.engine = engine;
        this.children = [];
        this.cloneProps = [];
    }
    Command.prototype = {
        addChild: function (node) {
            this.children.push(node);
        },
        open: function (context) {
            var parent = context.stack.top();
            parent && parent.addChild(this);
            context.stack.push(this);
        },
        close: function (context) {
            if (context.stack.top() === this) {
                context.stack.pop();
            }
        },
        getRendererBody: function () {
            var buf = [];
            var children = this.children;
            for (var i = 0; i < children.length; i++) {
                buf.push(children[i].getRendererBody());
            }
            return buf.join('');
        },
        clone: function () {
            var node = new this.constructor(this.value, this.engine);
            for (var i = 0, l = this.children.length; i < l; i++) {
                node.addChild(this.children[i].clone());
            }
            for (var i = 0, l = this.cloneProps.length; i < l; i++) {
                var prop = this.cloneProps[i];
                node[prop] = this[prop];
            }
            return node;
        }
    };
    function autoCloseCommand(context, CommandType) {
        var stack = context.stack;
        var closeEnd = CommandType ? stack.find(function (item) {
                return item instanceof CommandType;
            }) : stack.bottom();
        if (closeEnd) {
            var node;
            while ((node = stack.top()) !== closeEnd) {
                if (!node.autoClose) {
                    throw new Error(node.type + ' must be closed manually: ' + node.value);
                }
                node.autoClose(context);
            }
            closeEnd.close(context);
        }
        return closeEnd;
    }
    var RENDERER_BODY_START = '' + 'data=data||{};' + 'var v={},fs=engine.filters,hg=typeof data.get=="function",' + 'gv=function(n,ps){' + 'var p=ps[0],d=v[p];' + 'if(d==null){' + 'if(hg){return data.get(n);}' + 'd=data[p];' + '}' + 'for(var i=1,l=ps.length;i<l;i++)if(d!=null)d = d[ps[i]];' + 'return d;' + '},' + 'ts=function(s){' + 'if(typeof s==="string"){return s;}' + 'if(s==null){s="";}' + 'return ""+s;' + '};';
    ;
    function TargetCommand(value, engine) {
        if (!/^\s*([a-z0-9\/_-]+)\s*(\(\s*master\s*=\s*([a-z0-9\/_-]+)\s*\))?\s*/i.test(value)) {
            throw new Error('Invalid ' + this.type + ' syntax: ' + value);
        }
        this.master = RegExp.$3;
        this.name = RegExp.$1;
        Command.call(this, value, engine);
        this.blocks = {};
    }
    inherits(TargetCommand, Command);
    function BlockCommand(value, engine) {
        if (!/^\s*([a-z0-9\/_-]+)\s*$/i.test(value)) {
            throw new Error('Invalid ' + this.type + ' syntax: ' + value);
        }
        this.name = RegExp.$1;
        Command.call(this, value, engine);
        this.cloneProps = ['name'];
    }
    inherits(BlockCommand, Command);
    function ImportCommand(value, engine) {
        if (!/^\s*([a-z0-9\/_-]+)\s*$/i.test(value)) {
            throw new Error('Invalid ' + this.type + ' syntax: ' + value);
        }
        this.name = RegExp.$1;
        Command.call(this, value, engine);
        this.cloneProps = [
            'name',
            'state',
            'blocks'
        ];
        this.blocks = {};
    }
    inherits(ImportCommand, Command);
    function VarCommand(value, engine) {
        if (!/^\s*([a-z0-9_]+)\s*=([\s\S]*)$/i.test(value)) {
            throw new Error('Invalid ' + this.type + ' syntax: ' + value);
        }
        this.name = RegExp.$1;
        this.expr = RegExp.$2;
        Command.call(this, value, engine);
        this.cloneProps = [
            'name',
            'expr'
        ];
    }
    inherits(VarCommand, Command);
    function FilterCommand(value, engine) {
        if (!/^\s*([a-z0-9_-]+)\s*(\(([\s\S]*)\))?\s*$/i.test(value)) {
            throw new Error('Invalid ' + this.type + ' syntax: ' + value);
        }
        this.name = RegExp.$1;
        this.args = RegExp.$3;
        Command.call(this, value, engine);
        this.cloneProps = [
            'name',
            'args'
        ];
    }
    inherits(FilterCommand, Command);
    function UseCommand(value, engine) {
        if (!/^\s*([a-z0-9\/_-]+)\s*(\(([\s\S]*)\))?\s*$/i.test(value)) {
            throw new Error('Invalid ' + this.type + ' syntax: ' + value);
        }
        this.name = RegExp.$1;
        this.args = RegExp.$3;
        Command.call(this, value, engine);
        this.cloneProps = [
            'name',
            'args'
        ];
    }
    inherits(UseCommand, Command);
    function ForCommand(value, engine) {
        var rule = new RegExp(stringFormat('^\\s*({0}[\\s\\S]+{1})\\s+as\\s+{0}([0-9a-z_]+){1}\\s*(,\\s*{0}([0-9a-z_]+){1})?\\s*$', regexpLiteral(engine.options.variableOpen), regexpLiteral(engine.options.variableClose)), 'i');
        if (!rule.test(value)) {
            throw new Error('Invalid ' + this.type + ' syntax: ' + value);
        }
        this.list = RegExp.$1;
        this.item = RegExp.$2;
        this.index = RegExp.$4;
        Command.call(this, value, engine);
        this.cloneProps = [
            'list',
            'item',
            'index'
        ];
    }
    inherits(ForCommand, Command);
    function IfCommand(value, engine) {
        Command.call(this, value, engine);
    }
    inherits(IfCommand, Command);
    function ElifCommand(value, engine) {
        IfCommand.call(this, value, engine);
    }
    inherits(ElifCommand, IfCommand);
    function ElseCommand(value, engine) {
        Command.call(this, value, engine);
    }
    inherits(ElseCommand, IfCommand);
    var TargetState = {
            READING: 1,
            READED: 2,
            APPLIED: 3,
            READY: 4
        };
    ImportCommand.prototype.applyMaster = TargetCommand.prototype.applyMaster = function (masterName) {
        if (this.state >= TargetState.APPLIED) {
            return 1;
        }
        var blocks = this.blocks;
        function replaceBlock(node) {
            var children = node.children;
            if (children instanceof Array) {
                for (var i = 0, len = children.length; i < len; i++) {
                    var child = children[i];
                    if (child instanceof BlockCommand && blocks[child.name]) {
                        child = children[i] = blocks[child.name];
                    }
                    replaceBlock(child);
                }
            }
        }
        var master = this.engine.targets[masterName];
        if (master && master.applyMaster(master.master)) {
            this.children = master.clone().children;
            replaceBlock(this);
            this.state = TargetState.APPLIED;
            return 1;
        }
    };
    TargetCommand.prototype.isReady = function () {
        if (this.state >= TargetState.READY) {
            return 1;
        }
        var engine = this.engine;
        var readyState = 1;
        function checkReadyState(node) {
            for (var i = 0, len = node.children.length; i < len; i++) {
                var child = node.children[i];
                if (child instanceof ImportCommand) {
                    var target = engine.targets[child.name];
                    readyState = readyState && target && target.isReady(engine);
                } else if (child instanceof Command) {
                    checkReadyState(child);
                }
            }
        }
        if (this.applyMaster(this.master)) {
            checkReadyState(this);
            readyState && (this.state = TargetState.READY);
            return readyState;
        }
    };
    TargetCommand.prototype.getRenderer = function () {
        if (this.renderer) {
            return this.renderer;
        }
        if (this.isReady()) {
            var realRenderer = new Function('data', 'engine', [
                    RENDERER_BODY_START,
                    RENDER_STRING_DECLATION,
                    this.getRendererBody(),
                    RENDER_STRING_RETURN
                ].join('\n'));
            var engine = this.engine;
            this.renderer = function (data) {
                return realRenderer(data, engine);
            };
            return this.renderer;
        }
        return null;
    };
    function addTargetToContext(target, context) {
        context.target = target;
        var engine = context.engine;
        var name = target.name;
        if (engine.targets[name]) {
            switch (engine.options.namingConflict) {
            case 'override':
                engine.targets[name] = target;
                context.targets.push(name);
            case 'ignore':
                break;
            default:
                throw new Error('Target exists: ' + name);
            }
        } else {
            engine.targets[name] = target;
            context.targets.push(name);
        }
    }
    TargetCommand.prototype.open = function (context) {
        autoCloseCommand(context);
        Command.prototype.open.call(this, context);
        this.state = TargetState.READING;
        addTargetToContext(this, context);
    };
    VarCommand.prototype.open = UseCommand.prototype.open = function (context) {
        context.stack.top().addChild(this);
    };
    BlockCommand.prototype.open = function (context) {
        Command.prototype.open.call(this, context);
        (context.imp || context.target).blocks[this.name] = this;
    };
    ElifCommand.prototype.open = function (context) {
        var elseCommand = new ElseCommand();
        elseCommand.open(context);
        var ifCommand = autoCloseCommand(context, IfCommand);
        ifCommand.addChild(this);
        context.stack.push(this);
    };
    ElseCommand.prototype.open = function (context) {
        var ifCommand = autoCloseCommand(context, IfCommand);
        ifCommand.addChild(this);
        context.stack.push(this);
    };
    ImportCommand.prototype.open = function (context) {
        this.parent = context.stack.top();
        this.target = context.target;
        Command.prototype.open.call(this, context);
        this.state = TargetState.READING;
        context.imp = this;
    };
    UseCommand.prototype.close = VarCommand.prototype.close = function () {
    };
    ImportCommand.prototype.close = function (context) {
        Command.prototype.close.call(this, context);
        this.state = TargetState.READED;
        context.imp = null;
    };
    TargetCommand.prototype.close = function (context) {
        Command.prototype.close.call(this, context);
        this.state = this.master ? TargetState.READED : TargetState.APPLIED;
        context.target = null;
    };
    ImportCommand.prototype.autoClose = function (context) {
        var parentChildren = this.parent.children;
        parentChildren.push.apply(parentChildren, this.children);
        this.children.length = 0;
        for (var key in this.blocks) {
            this.target.blocks[key] = this.blocks[key];
        }
        this.blocks = {};
        this.close(context);
    };
    UseCommand.prototype.beforeOpen = ImportCommand.prototype.beforeOpen = VarCommand.prototype.beforeOpen = ForCommand.prototype.beforeOpen = FilterCommand.prototype.beforeOpen = BlockCommand.prototype.beforeOpen = IfCommand.prototype.beforeOpen = TextNode.prototype.beforeAdd = function (context) {
        if (context.stack.bottom()) {
            return;
        }
        var target = new TargetCommand(generateGUID(), context.engine);
        target.open(context);
    };
    ImportCommand.prototype.getRendererBody = function () {
        this.applyMaster(this.name);
        return Command.prototype.getRendererBody.call(this);
    };
    UseCommand.prototype.getRendererBody = function () {
        return stringFormat('{0}engine.render({2},{{3}}){1}', RENDER_STRING_ADD_START, RENDER_STRING_ADD_END, stringLiteralize(this.name), compileVariable(this.args, this.engine).replace(/(^|,)\s*([a-z0-9_]+)\s*=/gi, function (match, start, argName) {
            return (start || '') + stringLiteralize(argName) + ':';
        }));
    };
    VarCommand.prototype.getRendererBody = function () {
        if (this.expr) {
            return stringFormat('v[{0}]={1};', stringLiteralize(this.name), compileVariable(this.expr, this.engine));
        }
        return '';
    };
    IfCommand.prototype.getRendererBody = function () {
        return stringFormat('if({0}){{1}}', compileVariable(this.value, this.engine), Command.prototype.getRendererBody.call(this));
    };
    ElseCommand.prototype.getRendererBody = function () {
        return stringFormat('}else{{0}', Command.prototype.getRendererBody.call(this));
    };
    ForCommand.prototype.getRendererBody = function () {
        return stringFormat('' + 'var {0}={1};' + 'if({0} instanceof Array)' + 'for (var {4}=0,{5}={0}.length;{4}<{5};{4}++){v[{2}]={4};v[{3}]={0}[{4}];{6}}' + 'else if(typeof {0}==="object")' + 'for(var {4} in {0}){v[{2}]={4};v[{3}]={0}[{4}];{6}}', generateGUID(), compileVariable(this.list, this.engine), stringLiteralize(this.index || generateGUID()), stringLiteralize(this.item), generateGUID(), generateGUID(), Command.prototype.getRendererBody.call(this));
    };
    FilterCommand.prototype.getRendererBody = function () {
        var args = this.args;
        return stringFormat('{2}fs[{5}]((function(){{0}{4}{1}})(){6}){3}', RENDER_STRING_DECLATION, RENDER_STRING_RETURN, RENDER_STRING_ADD_START, RENDER_STRING_ADD_END, Command.prototype.getRendererBody.call(this), stringLiteralize(this.name), args ? ',' + compileVariable(args, this.engine) : '');
    };
    var commandTypes = {};
    function addCommandType(name, Type) {
        commandTypes[name] = Type;
        Type.prototype.type = name;
    }
    addCommandType('target', TargetCommand);
    addCommandType('block', BlockCommand);
    addCommandType('import', ImportCommand);
    addCommandType('use', UseCommand);
    addCommandType('var', VarCommand);
    addCommandType('for', ForCommand);
    addCommandType('if', IfCommand);
    addCommandType('elif', ElifCommand);
    addCommandType('else', ElseCommand);
    addCommandType('filter', FilterCommand);
    function Engine(options) {
        this.options = {
            commandOpen: '<!--',
            commandClose: '-->',
            commandSyntax: /^\s*(\/)?([a-z]+)\s*(?::([\s\S]*))?$/,
            variableOpen: '${',
            variableClose: '}',
            defaultFilter: 'html'
        };
        this.config(options);
        this.targets = {};
        this.filters = extend({}, DEFAULT_FILTERS);
    }
    Engine.prototype.config = function (options) {
        extend(this.options, options);
    };
    Engine.prototype.compile = Engine.prototype.parse = function (source) {
        if (source) {
            var targetNames = parseSource(source, this);
            if (targetNames.length) {
                return this.targets[targetNames[0]].getRenderer();
            }
        }
        return new Function('return ""');
    };
    Engine.prototype.getRenderer = function (name) {
        var target = this.targets[name];
        if (target) {
            return target.getRenderer();
        }
    };
    Engine.prototype.render = function (name, data) {
        var renderer = this.getRenderer(name);
        if (renderer) {
            return renderer(data);
        }
        return '';
    };
    Engine.prototype.addFilter = function (name, filter) {
        if (typeof filter === 'function') {
            this.filters[name] = filter;
        }
    };
    function parseSource(source, engine) {
        var commandOpen = engine.options.commandOpen;
        var commandClose = engine.options.commandClose;
        var commandSyntax = engine.options.commandSyntax;
        var stack = new Stack();
        var analyseContext = {
                engine: engine,
                targets: [],
                stack: stack,
                target: null
            };
        var textBuf = [];
        function flushTextBuf() {
            var text;
            if (textBuf.length > 0 && (text = textBuf.join(''))) {
                var textNode = new TextNode(text, engine);
                textNode.beforeAdd(analyseContext);
                stack.top().addChild(textNode);
                textBuf = [];
                if (engine.options.strip && analyseContext.current instanceof Command) {
                    textNode.value = text.replace(/^[\x20\t\r]*\n/, '');
                }
                analyseContext.current = textNode;
            }
        }
        var NodeType;
        parseTextBlock(source, commandOpen, commandClose, 0, function (text) {
            var match = commandSyntax.exec(text);
            if (match && (NodeType = commandTypes[match[2].toLowerCase()]) && typeof NodeType === 'function') {
                flushTextBuf();
                var currentNode = analyseContext.current;
                if (engine.options.strip && currentNode instanceof TextNode) {
                    currentNode.value = currentNode.value.replace(/\r?\n[\x20\t]*$/, '\n');
                }
                if (match[1]) {
                    currentNode = autoCloseCommand(analyseContext, NodeType);
                } else {
                    currentNode = new NodeType(match[3], engine);
                    if (typeof currentNode.beforeOpen === 'function') {
                        currentNode.beforeOpen(analyseContext);
                    }
                    currentNode.open(analyseContext);
                }
                analyseContext.current = currentNode;
            } else if (!/^\s*\/\//.test(text)) {
                textBuf.push(commandOpen, text, commandClose);
            }
            NodeType = null;
        }, function (text) {
            textBuf.push(text);
        });
        flushTextBuf();
        autoCloseCommand(analyseContext);
        return analyseContext.targets;
    }
    var etpl = new Engine();
    etpl.Engine = Engine;
    if (typeof exports === 'object' && typeof module === 'object') {
        exports = module.exports = etpl;
    } else if (typeof define === 'function' && define.amd) {
        define('etpl/main', [], etpl);
    } else {
        root.etpl = etpl;
    }
}(this));

define('etpl', ['etpl/main'], function ( main ) { return main; });

define('common/common', [
    'require',
    './common.tpl',
    'etpl'
], function (require) {
    var tpl = require('./common.tpl');
    var etpl = require('etpl');
    function init() {
        etpl.compile(tpl);
    }
    var myScrollEvents = function (myScroll, pullUpAction, pullDownAction) {
        document.addEventListener('touchmove', function (e) {
            e.preventDefault();
        }, false);
        var pullDownEl = document.getElementById('pullDown');
        var pullDownOffset = pullDownEl.offsetHeight;
        var pullUpEl = document.getElementById('pullUp');
        var pullUpOffset = pullUpEl.offsetHeight;
        myScroll.on('scroll', function () {
            if (this.y > 5 && !pullDownEl.className.match('flip')) {
                pullDownEl.className = 'flip';
                pullDownEl.querySelector('.pullDownLabel').innerHTML = '\u91CA\u653E\u540E\u52A0\u8F7D\u6700\u65B0';
                this.minScrollY = 0;
            } else if (this.y < 5 && pullDownEl.className.match('flip')) {
                pullDownEl.className = '';
                pullDownEl.querySelector('.pullDownLabel').innerHTML = '\u4E0B\u62C9\u52A0\u8F7D\u66F4\u591A';
                this.minScrollY = -pullDownOffset;
            } else if (this.y < this.maxScrollY - 5 && !pullUpEl.className.match('flip')) {
                pullUpEl.className = 'flip';
                pullUpEl.querySelector('.pullUpLabel').innerHTML = '\u91CA\u653E\u540E\u52A0\u8F7D\u6700\u65B0';
                this.maxScrollY = this.maxScrollY;
            } else if (this.y > this.maxScrollY + 5 && pullUpEl.className.match('flip')) {
                pullUpEl.className = '';
                pullUpEl.querySelector('.pullUpLabel').innerHTML = '\u4E0A\u62C9\u52A0\u8F7D\u66F4\u591A';
                this.maxScrollY = pullUpOffset;
            }
        });
        myScroll.on('scrollEnd', function () {
            if (pullDownEl.className.match('flip')) {
                pullDownEl.className = 'loading';
                pullDownEl.querySelector('.pullDownLabel').innerHTML = '\u6B63\u5728\u52AA\u529B\u52A0\u8F7D\u4E2D...';
                pullDownAction();
            } else if (pullUpEl.className.match('flip')) {
                pullUpEl.className = 'loading';
                pullUpEl.querySelector('.pullUpLabel').innerHTML = '\u6B63\u5728\u52AA\u529B\u52A0\u8F7D\u4E2D...';
                pullUpAction();
            }
        });
        myScroll.on('refresh', function () {
            if (pullDownEl.className.match('loading')) {
                pullDownEl.className = 'hidden';
                pullDownEl.querySelector('.pullDownLabel').innerHTML = '\u4E0B\u62C9\u52A0\u8F7D\u66F4\u591A';
            } else if (pullUpEl.className.match('loading')) {
                pullUpEl.className = 'hidden';
                pullUpEl.querySelector('.pullUpLabel').innerHTML = '\u4E0A\u62C9\u52A0\u8F7D\u66F4\u591A';
            }
        });
    };
    var COOKIES = {
            getCookie: function (name) {
                var arr, reg = new RegExp('(^| )' + name + '=([^;]*)(;|$)');
                if (arr = document.cookie.match(reg))
                    return unescape(arr[2]);
                else
                    return null;
            },
            delCookie: function (name) {
                var exp = new Date();
                exp.setTime(exp.getTime() - 1);
                var cval = getCookie(name);
                if (cval != null)
                    document.cookie = name + '=' + cval + ';expires=' + exp.toGMTString();
            },
            setCookie: function (name, value, time) {
                var strsec = this.getsec(time);
                var exp = new Date();
                exp.setTime(exp.getTime() + strsec * 1);
                document.cookie = name + '=' + escape(value) + ';expires=' + exp.toGMTString();
            },
            getsec: function (str) {
                alert(str);
                var str1 = str.substring(1, str.length) * 1;
                var str2 = str.substring(0, 1);
                if (str2 == 's') {
                    return str1 * 1000;
                } else if (str2 == 'h') {
                    return str1 * 60 * 60 * 1000;
                } else if (str2 == 'd') {
                    return str1 * 24 * 60 * 60 * 1000;
                }
            }
        };
    return {
        init: init,
        myScrollEvents: myScrollEvents,
        COOKIES: COOKIES
    };
});

define('moment/moment', [
    'require',
    'exports',
    'module'
], function (require, exports, module) {
    (function (undefined) {
        var moment, VERSION = '2.7.0', globalScope = typeof global !== 'undefined' ? global : this, oldGlobalMoment, round = Math.round, i, YEAR = 0, MONTH = 1, DATE = 2, HOUR = 3, MINUTE = 4, SECOND = 5, MILLISECOND = 6, languages = {}, momentProperties = {
                _isAMomentObject: null,
                _i: null,
                _f: null,
                _l: null,
                _strict: null,
                _tzm: null,
                _isUTC: null,
                _offset: null,
                _pf: null,
                _lang: null
            }, hasModule = typeof module !== 'undefined' && module.exports, aspNetJsonRegex = /^\/?Date\((\-?\d+)/i, aspNetTimeSpanJsonRegex = /(\-)?(?:(\d*)\.)?(\d+)\:(\d+)(?:\:(\d+)\.?(\d{3})?)?/, isoDurationRegex = /^(-)?P(?:(?:([0-9,.]*)Y)?(?:([0-9,.]*)M)?(?:([0-9,.]*)D)?(?:T(?:([0-9,.]*)H)?(?:([0-9,.]*)M)?(?:([0-9,.]*)S)?)?|([0-9,.]*)W)$/, formattingTokens = /(\[[^\[]*\])|(\\)?(Mo|MM?M?M?|Do|DDDo|DD?D?D?|ddd?d?|do?|w[o|w]?|W[o|W]?|Q|YYYYYY|YYYYY|YYYY|YY|gg(ggg?)?|GG(GGG?)?|e|E|a|A|hh?|HH?|mm?|ss?|S{1,4}|X|zz?|ZZ?|.)/g, localFormattingTokens = /(\[[^\[]*\])|(\\)?(LT|LL?L?L?|l{1,4})/g, parseTokenOneOrTwoDigits = /\d\d?/, parseTokenOneToThreeDigits = /\d{1,3}/, parseTokenOneToFourDigits = /\d{1,4}/, parseTokenOneToSixDigits = /[+\-]?\d{1,6}/, parseTokenDigits = /\d+/, parseTokenWord = /[0-9]*['a-z\u00A0-\u05FF\u0700-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]+|[\u0600-\u06FF\/]+(\s*?[\u0600-\u06FF]+){1,2}/i, parseTokenTimezone = /Z|[\+\-]\d\d:?\d\d/gi, parseTokenT = /T/i, parseTokenTimestampMs = /[\+\-]?\d+(\.\d{1,3})?/, parseTokenOrdinal = /\d{1,2}/, parseTokenOneDigit = /\d/, parseTokenTwoDigits = /\d\d/, parseTokenThreeDigits = /\d{3}/, parseTokenFourDigits = /\d{4}/, parseTokenSixDigits = /[+-]?\d{6}/, parseTokenSignedNumber = /[+-]?\d+/, isoRegex = /^\s*(?:[+-]\d{6}|\d{4})-(?:(\d\d-\d\d)|(W\d\d$)|(W\d\d-\d)|(\d\d\d))((T| )(\d\d(:\d\d(:\d\d(\.\d+)?)?)?)?([\+\-]\d\d(?::?\d\d)?|\s*Z)?)?$/, isoFormat = 'YYYY-MM-DDTHH:mm:ssZ', isoDates = [
                [
                    'YYYYYY-MM-DD',
                    /[+-]\d{6}-\d{2}-\d{2}/
                ],
                [
                    'YYYY-MM-DD',
                    /\d{4}-\d{2}-\d{2}/
                ],
                [
                    'GGGG-[W]WW-E',
                    /\d{4}-W\d{2}-\d/
                ],
                [
                    'GGGG-[W]WW',
                    /\d{4}-W\d{2}/
                ],
                [
                    'YYYY-DDD',
                    /\d{4}-\d{3}/
                ]
            ], isoTimes = [
                [
                    'HH:mm:ss.SSSS',
                    /(T| )\d\d:\d\d:\d\d\.\d+/
                ],
                [
                    'HH:mm:ss',
                    /(T| )\d\d:\d\d:\d\d/
                ],
                [
                    'HH:mm',
                    /(T| )\d\d:\d\d/
                ],
                [
                    'HH',
                    /(T| )\d\d/
                ]
            ], parseTimezoneChunker = /([\+\-]|\d\d)/gi, proxyGettersAndSetters = 'Date|Hours|Minutes|Seconds|Milliseconds'.split('|'), unitMillisecondFactors = {
                'Milliseconds': 1,
                'Seconds': 1000,
                'Minutes': 60000,
                'Hours': 3600000,
                'Days': 86400000,
                'Months': 2592000000,
                'Years': 31536000000
            }, unitAliases = {
                ms: 'millisecond',
                s: 'second',
                m: 'minute',
                h: 'hour',
                d: 'day',
                D: 'date',
                w: 'week',
                W: 'isoWeek',
                M: 'month',
                Q: 'quarter',
                y: 'year',
                DDD: 'dayOfYear',
                e: 'weekday',
                E: 'isoWeekday',
                gg: 'weekYear',
                GG: 'isoWeekYear'
            }, camelFunctions = {
                dayofyear: 'dayOfYear',
                isoweekday: 'isoWeekday',
                isoweek: 'isoWeek',
                weekyear: 'weekYear',
                isoweekyear: 'isoWeekYear'
            }, formatFunctions = {}, relativeTimeThresholds = {
                s: 45,
                m: 45,
                h: 22,
                dd: 25,
                dm: 45,
                dy: 345
            }, ordinalizeTokens = 'DDD w W M D d'.split(' '), paddedTokens = 'M D H h m s w W'.split(' '), formatTokenFunctions = {
                M: function () {
                    return this.month() + 1;
                },
                MMM: function (format) {
                    return this.lang().monthsShort(this, format);
                },
                MMMM: function (format) {
                    return this.lang().months(this, format);
                },
                D: function () {
                    return this.date();
                },
                DDD: function () {
                    return this.dayOfYear();
                },
                d: function () {
                    return this.day();
                },
                dd: function (format) {
                    return this.lang().weekdaysMin(this, format);
                },
                ddd: function (format) {
                    return this.lang().weekdaysShort(this, format);
                },
                dddd: function (format) {
                    return this.lang().weekdays(this, format);
                },
                w: function () {
                    return this.week();
                },
                W: function () {
                    return this.isoWeek();
                },
                YY: function () {
                    return leftZeroFill(this.year() % 100, 2);
                },
                YYYY: function () {
                    return leftZeroFill(this.year(), 4);
                },
                YYYYY: function () {
                    return leftZeroFill(this.year(), 5);
                },
                YYYYYY: function () {
                    var y = this.year(), sign = y >= 0 ? '+' : '-';
                    return sign + leftZeroFill(Math.abs(y), 6);
                },
                gg: function () {
                    return leftZeroFill(this.weekYear() % 100, 2);
                },
                gggg: function () {
                    return leftZeroFill(this.weekYear(), 4);
                },
                ggggg: function () {
                    return leftZeroFill(this.weekYear(), 5);
                },
                GG: function () {
                    return leftZeroFill(this.isoWeekYear() % 100, 2);
                },
                GGGG: function () {
                    return leftZeroFill(this.isoWeekYear(), 4);
                },
                GGGGG: function () {
                    return leftZeroFill(this.isoWeekYear(), 5);
                },
                e: function () {
                    return this.weekday();
                },
                E: function () {
                    return this.isoWeekday();
                },
                a: function () {
                    return this.lang().meridiem(this.hours(), this.minutes(), true);
                },
                A: function () {
                    return this.lang().meridiem(this.hours(), this.minutes(), false);
                },
                H: function () {
                    return this.hours();
                },
                h: function () {
                    return this.hours() % 12 || 12;
                },
                m: function () {
                    return this.minutes();
                },
                s: function () {
                    return this.seconds();
                },
                S: function () {
                    return toInt(this.milliseconds() / 100);
                },
                SS: function () {
                    return leftZeroFill(toInt(this.milliseconds() / 10), 2);
                },
                SSS: function () {
                    return leftZeroFill(this.milliseconds(), 3);
                },
                SSSS: function () {
                    return leftZeroFill(this.milliseconds(), 3);
                },
                Z: function () {
                    var a = -this.zone(), b = '+';
                    if (a < 0) {
                        a = -a;
                        b = '-';
                    }
                    return b + leftZeroFill(toInt(a / 60), 2) + ':' + leftZeroFill(toInt(a) % 60, 2);
                },
                ZZ: function () {
                    var a = -this.zone(), b = '+';
                    if (a < 0) {
                        a = -a;
                        b = '-';
                    }
                    return b + leftZeroFill(toInt(a / 60), 2) + leftZeroFill(toInt(a) % 60, 2);
                },
                z: function () {
                    return this.zoneAbbr();
                },
                zz: function () {
                    return this.zoneName();
                },
                X: function () {
                    return this.unix();
                },
                Q: function () {
                    return this.quarter();
                }
            }, lists = [
                'months',
                'monthsShort',
                'weekdays',
                'weekdaysShort',
                'weekdaysMin'
            ];
        function dfl(a, b, c) {
            switch (arguments.length) {
            case 2:
                return a != null ? a : b;
            case 3:
                return a != null ? a : b != null ? b : c;
            default:
                throw new Error('Implement me');
            }
        }
        function defaultParsingFlags() {
            return {
                empty: false,
                unusedTokens: [],
                unusedInput: [],
                overflow: -2,
                charsLeftOver: 0,
                nullInput: false,
                invalidMonth: null,
                invalidFormat: false,
                userInvalidated: false,
                iso: false
            };
        }
        function deprecate(msg, fn) {
            var firstTime = true;
            function printMsg() {
                if (moment.suppressDeprecationWarnings === false && typeof console !== 'undefined' && console.warn) {
                    console.warn('Deprecation warning: ' + msg);
                }
            }
            return extend(function () {
                if (firstTime) {
                    printMsg();
                    firstTime = false;
                }
                return fn.apply(this, arguments);
            }, fn);
        }
        function padToken(func, count) {
            return function (a) {
                return leftZeroFill(func.call(this, a), count);
            };
        }
        function ordinalizeToken(func, period) {
            return function (a) {
                return this.lang().ordinal(func.call(this, a), period);
            };
        }
        while (ordinalizeTokens.length) {
            i = ordinalizeTokens.pop();
            formatTokenFunctions[i + 'o'] = ordinalizeToken(formatTokenFunctions[i], i);
        }
        while (paddedTokens.length) {
            i = paddedTokens.pop();
            formatTokenFunctions[i + i] = padToken(formatTokenFunctions[i], 2);
        }
        formatTokenFunctions.DDDD = padToken(formatTokenFunctions.DDD, 3);
        function Language() {
        }
        function Moment(config) {
            checkOverflow(config);
            extend(this, config);
        }
        function Duration(duration) {
            var normalizedInput = normalizeObjectUnits(duration), years = normalizedInput.year || 0, quarters = normalizedInput.quarter || 0, months = normalizedInput.month || 0, weeks = normalizedInput.week || 0, days = normalizedInput.day || 0, hours = normalizedInput.hour || 0, minutes = normalizedInput.minute || 0, seconds = normalizedInput.second || 0, milliseconds = normalizedInput.millisecond || 0;
            this._milliseconds = +milliseconds + seconds * 1000 + minutes * 60000 + hours * 3600000;
            this._days = +days + weeks * 7;
            this._months = +months + quarters * 3 + years * 12;
            this._data = {};
            this._bubble();
        }
        function extend(a, b) {
            for (var i in b) {
                if (b.hasOwnProperty(i)) {
                    a[i] = b[i];
                }
            }
            if (b.hasOwnProperty('toString')) {
                a.toString = b.toString;
            }
            if (b.hasOwnProperty('valueOf')) {
                a.valueOf = b.valueOf;
            }
            return a;
        }
        function cloneMoment(m) {
            var result = {}, i;
            for (i in m) {
                if (m.hasOwnProperty(i) && momentProperties.hasOwnProperty(i)) {
                    result[i] = m[i];
                }
            }
            return result;
        }
        function absRound(number) {
            if (number < 0) {
                return Math.ceil(number);
            } else {
                return Math.floor(number);
            }
        }
        function leftZeroFill(number, targetLength, forceSign) {
            var output = '' + Math.abs(number), sign = number >= 0;
            while (output.length < targetLength) {
                output = '0' + output;
            }
            return (sign ? forceSign ? '+' : '' : '-') + output;
        }
        function addOrSubtractDurationFromMoment(mom, duration, isAdding, updateOffset) {
            var milliseconds = duration._milliseconds, days = duration._days, months = duration._months;
            updateOffset = updateOffset == null ? true : updateOffset;
            if (milliseconds) {
                mom._d.setTime(+mom._d + milliseconds * isAdding);
            }
            if (days) {
                rawSetter(mom, 'Date', rawGetter(mom, 'Date') + days * isAdding);
            }
            if (months) {
                rawMonthSetter(mom, rawGetter(mom, 'Month') + months * isAdding);
            }
            if (updateOffset) {
                moment.updateOffset(mom, days || months);
            }
        }
        function isArray(input) {
            return Object.prototype.toString.call(input) === '[object Array]';
        }
        function isDate(input) {
            return Object.prototype.toString.call(input) === '[object Date]' || input instanceof Date;
        }
        function compareArrays(array1, array2, dontConvert) {
            var len = Math.min(array1.length, array2.length), lengthDiff = Math.abs(array1.length - array2.length), diffs = 0, i;
            for (i = 0; i < len; i++) {
                if (dontConvert && array1[i] !== array2[i] || !dontConvert && toInt(array1[i]) !== toInt(array2[i])) {
                    diffs++;
                }
            }
            return diffs + lengthDiff;
        }
        function normalizeUnits(units) {
            if (units) {
                var lowered = units.toLowerCase().replace(/(.)s$/, '$1');
                units = unitAliases[units] || camelFunctions[lowered] || lowered;
            }
            return units;
        }
        function normalizeObjectUnits(inputObject) {
            var normalizedInput = {}, normalizedProp, prop;
            for (prop in inputObject) {
                if (inputObject.hasOwnProperty(prop)) {
                    normalizedProp = normalizeUnits(prop);
                    if (normalizedProp) {
                        normalizedInput[normalizedProp] = inputObject[prop];
                    }
                }
            }
            return normalizedInput;
        }
        function makeList(field) {
            var count, setter;
            if (field.indexOf('week') === 0) {
                count = 7;
                setter = 'day';
            } else if (field.indexOf('month') === 0) {
                count = 12;
                setter = 'month';
            } else {
                return;
            }
            moment[field] = function (format, index) {
                var i, getter, method = moment.fn._lang[field], results = [];
                if (typeof format === 'number') {
                    index = format;
                    format = undefined;
                }
                getter = function (i) {
                    var m = moment().utc().set(setter, i);
                    return method.call(moment.fn._lang, m, format || '');
                };
                if (index != null) {
                    return getter(index);
                } else {
                    for (i = 0; i < count; i++) {
                        results.push(getter(i));
                    }
                    return results;
                }
            };
        }
        function toInt(argumentForCoercion) {
            var coercedNumber = +argumentForCoercion, value = 0;
            if (coercedNumber !== 0 && isFinite(coercedNumber)) {
                if (coercedNumber >= 0) {
                    value = Math.floor(coercedNumber);
                } else {
                    value = Math.ceil(coercedNumber);
                }
            }
            return value;
        }
        function daysInMonth(year, month) {
            return new Date(Date.UTC(year, month + 1, 0)).getUTCDate();
        }
        function weeksInYear(year, dow, doy) {
            return weekOfYear(moment([
                year,
                11,
                31 + dow - doy
            ]), dow, doy).week;
        }
        function daysInYear(year) {
            return isLeapYear(year) ? 366 : 365;
        }
        function isLeapYear(year) {
            return year % 4 === 0 && year % 100 !== 0 || year % 400 === 0;
        }
        function checkOverflow(m) {
            var overflow;
            if (m._a && m._pf.overflow === -2) {
                overflow = m._a[MONTH] < 0 || m._a[MONTH] > 11 ? MONTH : m._a[DATE] < 1 || m._a[DATE] > daysInMonth(m._a[YEAR], m._a[MONTH]) ? DATE : m._a[HOUR] < 0 || m._a[HOUR] > 23 ? HOUR : m._a[MINUTE] < 0 || m._a[MINUTE] > 59 ? MINUTE : m._a[SECOND] < 0 || m._a[SECOND] > 59 ? SECOND : m._a[MILLISECOND] < 0 || m._a[MILLISECOND] > 999 ? MILLISECOND : -1;
                if (m._pf._overflowDayOfYear && (overflow < YEAR || overflow > DATE)) {
                    overflow = DATE;
                }
                m._pf.overflow = overflow;
            }
        }
        function isValid(m) {
            if (m._isValid == null) {
                m._isValid = !isNaN(m._d.getTime()) && m._pf.overflow < 0 && !m._pf.empty && !m._pf.invalidMonth && !m._pf.nullInput && !m._pf.invalidFormat && !m._pf.userInvalidated;
                if (m._strict) {
                    m._isValid = m._isValid && m._pf.charsLeftOver === 0 && m._pf.unusedTokens.length === 0;
                }
            }
            return m._isValid;
        }
        function normalizeLanguage(key) {
            return key ? key.toLowerCase().replace('_', '-') : key;
        }
        function makeAs(input, model) {
            return model._isUTC ? moment(input).zone(model._offset || 0) : moment(input).local();
        }
        extend(Language.prototype, {
            set: function (config) {
                var prop, i;
                for (i in config) {
                    prop = config[i];
                    if (typeof prop === 'function') {
                        this[i] = prop;
                    } else {
                        this['_' + i] = prop;
                    }
                }
            },
            _months: 'January_February_March_April_May_June_July_August_September_October_November_December'.split('_'),
            months: function (m) {
                return this._months[m.month()];
            },
            _monthsShort: 'Jan_Feb_Mar_Apr_May_Jun_Jul_Aug_Sep_Oct_Nov_Dec'.split('_'),
            monthsShort: function (m) {
                return this._monthsShort[m.month()];
            },
            monthsParse: function (monthName) {
                var i, mom, regex;
                if (!this._monthsParse) {
                    this._monthsParse = [];
                }
                for (i = 0; i < 12; i++) {
                    if (!this._monthsParse[i]) {
                        mom = moment.utc([
                            2000,
                            i
                        ]);
                        regex = '^' + this.months(mom, '') + '|^' + this.monthsShort(mom, '');
                        this._monthsParse[i] = new RegExp(regex.replace('.', ''), 'i');
                    }
                    if (this._monthsParse[i].test(monthName)) {
                        return i;
                    }
                }
            },
            _weekdays: 'Sunday_Monday_Tuesday_Wednesday_Thursday_Friday_Saturday'.split('_'),
            weekdays: function (m) {
                return this._weekdays[m.day()];
            },
            _weekdaysShort: 'Sun_Mon_Tue_Wed_Thu_Fri_Sat'.split('_'),
            weekdaysShort: function (m) {
                return this._weekdaysShort[m.day()];
            },
            _weekdaysMin: 'Su_Mo_Tu_We_Th_Fr_Sa'.split('_'),
            weekdaysMin: function (m) {
                return this._weekdaysMin[m.day()];
            },
            weekdaysParse: function (weekdayName) {
                var i, mom, regex;
                if (!this._weekdaysParse) {
                    this._weekdaysParse = [];
                }
                for (i = 0; i < 7; i++) {
                    if (!this._weekdaysParse[i]) {
                        mom = moment([
                            2000,
                            1
                        ]).day(i);
                        regex = '^' + this.weekdays(mom, '') + '|^' + this.weekdaysShort(mom, '') + '|^' + this.weekdaysMin(mom, '');
                        this._weekdaysParse[i] = new RegExp(regex.replace('.', ''), 'i');
                    }
                    if (this._weekdaysParse[i].test(weekdayName)) {
                        return i;
                    }
                }
            },
            _longDateFormat: {
                LT: 'h:mm A',
                L: 'MM/DD/YYYY',
                LL: 'MMMM D YYYY',
                LLL: 'MMMM D YYYY LT',
                LLLL: 'dddd, MMMM D YYYY LT'
            },
            longDateFormat: function (key) {
                var output = this._longDateFormat[key];
                if (!output && this._longDateFormat[key.toUpperCase()]) {
                    output = this._longDateFormat[key.toUpperCase()].replace(/MMMM|MM|DD|dddd/g, function (val) {
                        return val.slice(1);
                    });
                    this._longDateFormat[key] = output;
                }
                return output;
            },
            isPM: function (input) {
                return (input + '').toLowerCase().charAt(0) === 'p';
            },
            _meridiemParse: /[ap]\.?m?\.?/i,
            meridiem: function (hours, minutes, isLower) {
                if (hours > 11) {
                    return isLower ? 'pm' : 'PM';
                } else {
                    return isLower ? 'am' : 'AM';
                }
            },
            _calendar: {
                sameDay: '[Today at] LT',
                nextDay: '[Tomorrow at] LT',
                nextWeek: 'dddd [at] LT',
                lastDay: '[Yesterday at] LT',
                lastWeek: '[Last] dddd [at] LT',
                sameElse: 'L'
            },
            calendar: function (key, mom) {
                var output = this._calendar[key];
                return typeof output === 'function' ? output.apply(mom) : output;
            },
            _relativeTime: {
                future: 'in %s',
                past: '%s ago',
                s: 'a few seconds',
                m: 'a minute',
                mm: '%d minutes',
                h: 'an hour',
                hh: '%d hours',
                d: 'a day',
                dd: '%d days',
                M: 'a month',
                MM: '%d months',
                y: 'a year',
                yy: '%d years'
            },
            relativeTime: function (number, withoutSuffix, string, isFuture) {
                var output = this._relativeTime[string];
                return typeof output === 'function' ? output(number, withoutSuffix, string, isFuture) : output.replace(/%d/i, number);
            },
            pastFuture: function (diff, output) {
                var format = this._relativeTime[diff > 0 ? 'future' : 'past'];
                return typeof format === 'function' ? format(output) : format.replace(/%s/i, output);
            },
            ordinal: function (number) {
                return this._ordinal.replace('%d', number);
            },
            _ordinal: '%d',
            preparse: function (string) {
                return string;
            },
            postformat: function (string) {
                return string;
            },
            week: function (mom) {
                return weekOfYear(mom, this._week.dow, this._week.doy).week;
            },
            _week: {
                dow: 0,
                doy: 6
            },
            _invalidDate: 'Invalid date',
            invalidDate: function () {
                return this._invalidDate;
            }
        });
        function loadLang(key, values) {
            values.abbr = key;
            if (!languages[key]) {
                languages[key] = new Language();
            }
            languages[key].set(values);
            return languages[key];
        }
        function unloadLang(key) {
            delete languages[key];
        }
        function getLangDefinition(key) {
            var i = 0, j, lang, next, split, get = function (k) {
                    if (!languages[k] && hasModule) {
                        try {
                            require('./lang/' + k);
                        } catch (e) {
                        }
                    }
                    return languages[k];
                };
            if (!key) {
                return moment.fn._lang;
            }
            if (!isArray(key)) {
                lang = get(key);
                if (lang) {
                    return lang;
                }
                key = [key];
            }
            while (i < key.length) {
                split = normalizeLanguage(key[i]).split('-');
                j = split.length;
                next = normalizeLanguage(key[i + 1]);
                next = next ? next.split('-') : null;
                while (j > 0) {
                    lang = get(split.slice(0, j).join('-'));
                    if (lang) {
                        return lang;
                    }
                    if (next && next.length >= j && compareArrays(split, next, true) >= j - 1) {
                        break;
                    }
                    j--;
                }
                i++;
            }
            return moment.fn._lang;
        }
        function removeFormattingTokens(input) {
            if (input.match(/\[[\s\S]/)) {
                return input.replace(/^\[|\]$/g, '');
            }
            return input.replace(/\\/g, '');
        }
        function makeFormatFunction(format) {
            var array = format.match(formattingTokens), i, length;
            for (i = 0, length = array.length; i < length; i++) {
                if (formatTokenFunctions[array[i]]) {
                    array[i] = formatTokenFunctions[array[i]];
                } else {
                    array[i] = removeFormattingTokens(array[i]);
                }
            }
            return function (mom) {
                var output = '';
                for (i = 0; i < length; i++) {
                    output += array[i] instanceof Function ? array[i].call(mom, format) : array[i];
                }
                return output;
            };
        }
        function formatMoment(m, format) {
            if (!m.isValid()) {
                return m.lang().invalidDate();
            }
            format = expandFormat(format, m.lang());
            if (!formatFunctions[format]) {
                formatFunctions[format] = makeFormatFunction(format);
            }
            return formatFunctions[format](m);
        }
        function expandFormat(format, lang) {
            var i = 5;
            function replaceLongDateFormatTokens(input) {
                return lang.longDateFormat(input) || input;
            }
            localFormattingTokens.lastIndex = 0;
            while (i >= 0 && localFormattingTokens.test(format)) {
                format = format.replace(localFormattingTokens, replaceLongDateFormatTokens);
                localFormattingTokens.lastIndex = 0;
                i -= 1;
            }
            return format;
        }
        function getParseRegexForToken(token, config) {
            var a, strict = config._strict;
            switch (token) {
            case 'Q':
                return parseTokenOneDigit;
            case 'DDDD':
                return parseTokenThreeDigits;
            case 'YYYY':
            case 'GGGG':
            case 'gggg':
                return strict ? parseTokenFourDigits : parseTokenOneToFourDigits;
            case 'Y':
            case 'G':
            case 'g':
                return parseTokenSignedNumber;
            case 'YYYYYY':
            case 'YYYYY':
            case 'GGGGG':
            case 'ggggg':
                return strict ? parseTokenSixDigits : parseTokenOneToSixDigits;
            case 'S':
                if (strict) {
                    return parseTokenOneDigit;
                }
            case 'SS':
                if (strict) {
                    return parseTokenTwoDigits;
                }
            case 'SSS':
                if (strict) {
                    return parseTokenThreeDigits;
                }
            case 'DDD':
                return parseTokenOneToThreeDigits;
            case 'MMM':
            case 'MMMM':
            case 'dd':
            case 'ddd':
            case 'dddd':
                return parseTokenWord;
            case 'a':
            case 'A':
                return getLangDefinition(config._l)._meridiemParse;
            case 'X':
                return parseTokenTimestampMs;
            case 'Z':
            case 'ZZ':
                return parseTokenTimezone;
            case 'T':
                return parseTokenT;
            case 'SSSS':
                return parseTokenDigits;
            case 'MM':
            case 'DD':
            case 'YY':
            case 'GG':
            case 'gg':
            case 'HH':
            case 'hh':
            case 'mm':
            case 'ss':
            case 'ww':
            case 'WW':
                return strict ? parseTokenTwoDigits : parseTokenOneOrTwoDigits;
            case 'M':
            case 'D':
            case 'd':
            case 'H':
            case 'h':
            case 'm':
            case 's':
            case 'w':
            case 'W':
            case 'e':
            case 'E':
                return parseTokenOneOrTwoDigits;
            case 'Do':
                return parseTokenOrdinal;
            default:
                a = new RegExp(regexpEscape(unescapeFormat(token.replace('\\', '')), 'i'));
                return a;
            }
        }
        function timezoneMinutesFromString(string) {
            string = string || '';
            var possibleTzMatches = string.match(parseTokenTimezone) || [], tzChunk = possibleTzMatches[possibleTzMatches.length - 1] || [], parts = (tzChunk + '').match(parseTimezoneChunker) || [
                    '-',
                    0,
                    0
                ], minutes = +(parts[1] * 60) + toInt(parts[2]);
            return parts[0] === '+' ? -minutes : minutes;
        }
        function addTimeToArrayFromToken(token, input, config) {
            var a, datePartArray = config._a;
            switch (token) {
            case 'Q':
                if (input != null) {
                    datePartArray[MONTH] = (toInt(input) - 1) * 3;
                }
                break;
            case 'M':
            case 'MM':
                if (input != null) {
                    datePartArray[MONTH] = toInt(input) - 1;
                }
                break;
            case 'MMM':
            case 'MMMM':
                a = getLangDefinition(config._l).monthsParse(input);
                if (a != null) {
                    datePartArray[MONTH] = a;
                } else {
                    config._pf.invalidMonth = input;
                }
                break;
            case 'D':
            case 'DD':
                if (input != null) {
                    datePartArray[DATE] = toInt(input);
                }
                break;
            case 'Do':
                if (input != null) {
                    datePartArray[DATE] = toInt(parseInt(input, 10));
                }
                break;
            case 'DDD':
            case 'DDDD':
                if (input != null) {
                    config._dayOfYear = toInt(input);
                }
                break;
            case 'YY':
                datePartArray[YEAR] = moment.parseTwoDigitYear(input);
                break;
            case 'YYYY':
            case 'YYYYY':
            case 'YYYYYY':
                datePartArray[YEAR] = toInt(input);
                break;
            case 'a':
            case 'A':
                config._isPm = getLangDefinition(config._l).isPM(input);
                break;
            case 'H':
            case 'HH':
            case 'h':
            case 'hh':
                datePartArray[HOUR] = toInt(input);
                break;
            case 'm':
            case 'mm':
                datePartArray[MINUTE] = toInt(input);
                break;
            case 's':
            case 'ss':
                datePartArray[SECOND] = toInt(input);
                break;
            case 'S':
            case 'SS':
            case 'SSS':
            case 'SSSS':
                datePartArray[MILLISECOND] = toInt(('0.' + input) * 1000);
                break;
            case 'X':
                config._d = new Date(parseFloat(input) * 1000);
                break;
            case 'Z':
            case 'ZZ':
                config._useUTC = true;
                config._tzm = timezoneMinutesFromString(input);
                break;
            case 'dd':
            case 'ddd':
            case 'dddd':
                a = getLangDefinition(config._l).weekdaysParse(input);
                if (a != null) {
                    config._w = config._w || {};
                    config._w['d'] = a;
                } else {
                    config._pf.invalidWeekday = input;
                }
                break;
            case 'w':
            case 'ww':
            case 'W':
            case 'WW':
            case 'd':
            case 'e':
            case 'E':
                token = token.substr(0, 1);
            case 'gggg':
            case 'GGGG':
            case 'GGGGG':
                token = token.substr(0, 2);
                if (input) {
                    config._w = config._w || {};
                    config._w[token] = toInt(input);
                }
                break;
            case 'gg':
            case 'GG':
                config._w = config._w || {};
                config._w[token] = moment.parseTwoDigitYear(input);
            }
        }
        function dayOfYearFromWeekInfo(config) {
            var w, weekYear, week, weekday, dow, doy, temp, lang;
            w = config._w;
            if (w.GG != null || w.W != null || w.E != null) {
                dow = 1;
                doy = 4;
                weekYear = dfl(w.GG, config._a[YEAR], weekOfYear(moment(), 1, 4).year);
                week = dfl(w.W, 1);
                weekday = dfl(w.E, 1);
            } else {
                lang = getLangDefinition(config._l);
                dow = lang._week.dow;
                doy = lang._week.doy;
                weekYear = dfl(w.gg, config._a[YEAR], weekOfYear(moment(), dow, doy).year);
                week = dfl(w.w, 1);
                if (w.d != null) {
                    weekday = w.d;
                    if (weekday < dow) {
                        ++week;
                    }
                } else if (w.e != null) {
                    weekday = w.e + dow;
                } else {
                    weekday = dow;
                }
            }
            temp = dayOfYearFromWeeks(weekYear, week, weekday, doy, dow);
            config._a[YEAR] = temp.year;
            config._dayOfYear = temp.dayOfYear;
        }
        function dateFromConfig(config) {
            var i, date, input = [], currentDate, yearToUse;
            if (config._d) {
                return;
            }
            currentDate = currentDateArray(config);
            if (config._w && config._a[DATE] == null && config._a[MONTH] == null) {
                dayOfYearFromWeekInfo(config);
            }
            if (config._dayOfYear) {
                yearToUse = dfl(config._a[YEAR], currentDate[YEAR]);
                if (config._dayOfYear > daysInYear(yearToUse)) {
                    config._pf._overflowDayOfYear = true;
                }
                date = makeUTCDate(yearToUse, 0, config._dayOfYear);
                config._a[MONTH] = date.getUTCMonth();
                config._a[DATE] = date.getUTCDate();
            }
            for (i = 0; i < 3 && config._a[i] == null; ++i) {
                config._a[i] = input[i] = currentDate[i];
            }
            for (; i < 7; i++) {
                config._a[i] = input[i] = config._a[i] == null ? i === 2 ? 1 : 0 : config._a[i];
            }
            config._d = (config._useUTC ? makeUTCDate : makeDate).apply(null, input);
            if (config._tzm != null) {
                config._d.setUTCMinutes(config._d.getUTCMinutes() + config._tzm);
            }
        }
        function dateFromObject(config) {
            var normalizedInput;
            if (config._d) {
                return;
            }
            normalizedInput = normalizeObjectUnits(config._i);
            config._a = [
                normalizedInput.year,
                normalizedInput.month,
                normalizedInput.day,
                normalizedInput.hour,
                normalizedInput.minute,
                normalizedInput.second,
                normalizedInput.millisecond
            ];
            dateFromConfig(config);
        }
        function currentDateArray(config) {
            var now = new Date();
            if (config._useUTC) {
                return [
                    now.getUTCFullYear(),
                    now.getUTCMonth(),
                    now.getUTCDate()
                ];
            } else {
                return [
                    now.getFullYear(),
                    now.getMonth(),
                    now.getDate()
                ];
            }
        }
        function makeDateFromStringAndFormat(config) {
            if (config._f === moment.ISO_8601) {
                parseISO(config);
                return;
            }
            config._a = [];
            config._pf.empty = true;
            var lang = getLangDefinition(config._l), string = '' + config._i, i, parsedInput, tokens, token, skipped, stringLength = string.length, totalParsedInputLength = 0;
            tokens = expandFormat(config._f, lang).match(formattingTokens) || [];
            for (i = 0; i < tokens.length; i++) {
                token = tokens[i];
                parsedInput = (string.match(getParseRegexForToken(token, config)) || [])[0];
                if (parsedInput) {
                    skipped = string.substr(0, string.indexOf(parsedInput));
                    if (skipped.length > 0) {
                        config._pf.unusedInput.push(skipped);
                    }
                    string = string.slice(string.indexOf(parsedInput) + parsedInput.length);
                    totalParsedInputLength += parsedInput.length;
                }
                if (formatTokenFunctions[token]) {
                    if (parsedInput) {
                        config._pf.empty = false;
                    } else {
                        config._pf.unusedTokens.push(token);
                    }
                    addTimeToArrayFromToken(token, parsedInput, config);
                } else if (config._strict && !parsedInput) {
                    config._pf.unusedTokens.push(token);
                }
            }
            config._pf.charsLeftOver = stringLength - totalParsedInputLength;
            if (string.length > 0) {
                config._pf.unusedInput.push(string);
            }
            if (config._isPm && config._a[HOUR] < 12) {
                config._a[HOUR] += 12;
            }
            if (config._isPm === false && config._a[HOUR] === 12) {
                config._a[HOUR] = 0;
            }
            dateFromConfig(config);
            checkOverflow(config);
        }
        function unescapeFormat(s) {
            return s.replace(/\\(\[)|\\(\])|\[([^\]\[]*)\]|\\(.)/g, function (matched, p1, p2, p3, p4) {
                return p1 || p2 || p3 || p4;
            });
        }
        function regexpEscape(s) {
            return s.replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&');
        }
        function makeDateFromStringAndArray(config) {
            var tempConfig, bestMoment, scoreToBeat, i, currentScore;
            if (config._f.length === 0) {
                config._pf.invalidFormat = true;
                config._d = new Date(NaN);
                return;
            }
            for (i = 0; i < config._f.length; i++) {
                currentScore = 0;
                tempConfig = extend({}, config);
                tempConfig._pf = defaultParsingFlags();
                tempConfig._f = config._f[i];
                makeDateFromStringAndFormat(tempConfig);
                if (!isValid(tempConfig)) {
                    continue;
                }
                currentScore += tempConfig._pf.charsLeftOver;
                currentScore += tempConfig._pf.unusedTokens.length * 10;
                tempConfig._pf.score = currentScore;
                if (scoreToBeat == null || currentScore < scoreToBeat) {
                    scoreToBeat = currentScore;
                    bestMoment = tempConfig;
                }
            }
            extend(config, bestMoment || tempConfig);
        }
        function parseISO(config) {
            var i, l, string = config._i, match = isoRegex.exec(string);
            if (match) {
                config._pf.iso = true;
                for (i = 0, l = isoDates.length; i < l; i++) {
                    if (isoDates[i][1].exec(string)) {
                        config._f = isoDates[i][0] + (match[6] || ' ');
                        break;
                    }
                }
                for (i = 0, l = isoTimes.length; i < l; i++) {
                    if (isoTimes[i][1].exec(string)) {
                        config._f += isoTimes[i][0];
                        break;
                    }
                }
                if (string.match(parseTokenTimezone)) {
                    config._f += 'Z';
                }
                makeDateFromStringAndFormat(config);
            } else {
                config._isValid = false;
            }
        }
        function makeDateFromString(config) {
            parseISO(config);
            if (config._isValid === false) {
                delete config._isValid;
                moment.createFromInputFallback(config);
            }
        }
        function makeDateFromInput(config) {
            var input = config._i, matched = aspNetJsonRegex.exec(input);
            if (input === undefined) {
                config._d = new Date();
            } else if (matched) {
                config._d = new Date(+matched[1]);
            } else if (typeof input === 'string') {
                makeDateFromString(config);
            } else if (isArray(input)) {
                config._a = input.slice(0);
                dateFromConfig(config);
            } else if (isDate(input)) {
                config._d = new Date(+input);
            } else if (typeof input === 'object') {
                dateFromObject(config);
            } else if (typeof input === 'number') {
                config._d = new Date(input);
            } else {
                moment.createFromInputFallback(config);
            }
        }
        function makeDate(y, m, d, h, M, s, ms) {
            var date = new Date(y, m, d, h, M, s, ms);
            if (y < 1970) {
                date.setFullYear(y);
            }
            return date;
        }
        function makeUTCDate(y) {
            var date = new Date(Date.UTC.apply(null, arguments));
            if (y < 1970) {
                date.setUTCFullYear(y);
            }
            return date;
        }
        function parseWeekday(input, language) {
            if (typeof input === 'string') {
                if (!isNaN(input)) {
                    input = parseInt(input, 10);
                } else {
                    input = language.weekdaysParse(input);
                    if (typeof input !== 'number') {
                        return null;
                    }
                }
            }
            return input;
        }
        function substituteTimeAgo(string, number, withoutSuffix, isFuture, lang) {
            return lang.relativeTime(number || 1, !!withoutSuffix, string, isFuture);
        }
        function relativeTime(milliseconds, withoutSuffix, lang) {
            var seconds = round(Math.abs(milliseconds) / 1000), minutes = round(seconds / 60), hours = round(minutes / 60), days = round(hours / 24), years = round(days / 365), args = seconds < relativeTimeThresholds.s && [
                    's',
                    seconds
                ] || minutes === 1 && ['m'] || minutes < relativeTimeThresholds.m && [
                    'mm',
                    minutes
                ] || hours === 1 && ['h'] || hours < relativeTimeThresholds.h && [
                    'hh',
                    hours
                ] || days === 1 && ['d'] || days <= relativeTimeThresholds.dd && [
                    'dd',
                    days
                ] || days <= relativeTimeThresholds.dm && ['M'] || days < relativeTimeThresholds.dy && [
                    'MM',
                    round(days / 30)
                ] || years === 1 && ['y'] || [
                    'yy',
                    years
                ];
            args[2] = withoutSuffix;
            args[3] = milliseconds > 0;
            args[4] = lang;
            return substituteTimeAgo.apply({}, args);
        }
        function weekOfYear(mom, firstDayOfWeek, firstDayOfWeekOfYear) {
            var end = firstDayOfWeekOfYear - firstDayOfWeek, daysToDayOfWeek = firstDayOfWeekOfYear - mom.day(), adjustedMoment;
            if (daysToDayOfWeek > end) {
                daysToDayOfWeek -= 7;
            }
            if (daysToDayOfWeek < end - 7) {
                daysToDayOfWeek += 7;
            }
            adjustedMoment = moment(mom).add('d', daysToDayOfWeek);
            return {
                week: Math.ceil(adjustedMoment.dayOfYear() / 7),
                year: adjustedMoment.year()
            };
        }
        function dayOfYearFromWeeks(year, week, weekday, firstDayOfWeekOfYear, firstDayOfWeek) {
            var d = makeUTCDate(year, 0, 1).getUTCDay(), daysToAdd, dayOfYear;
            d = d === 0 ? 7 : d;
            weekday = weekday != null ? weekday : firstDayOfWeek;
            daysToAdd = firstDayOfWeek - d + (d > firstDayOfWeekOfYear ? 7 : 0) - (d < firstDayOfWeek ? 7 : 0);
            dayOfYear = 7 * (week - 1) + (weekday - firstDayOfWeek) + daysToAdd + 1;
            return {
                year: dayOfYear > 0 ? year : year - 1,
                dayOfYear: dayOfYear > 0 ? dayOfYear : daysInYear(year - 1) + dayOfYear
            };
        }
        function makeMoment(config) {
            var input = config._i, format = config._f;
            if (input === null || format === undefined && input === '') {
                return moment.invalid({ nullInput: true });
            }
            if (typeof input === 'string') {
                config._i = input = getLangDefinition().preparse(input);
            }
            if (moment.isMoment(input)) {
                config = cloneMoment(input);
                config._d = new Date(+input._d);
            } else if (format) {
                if (isArray(format)) {
                    makeDateFromStringAndArray(config);
                } else {
                    makeDateFromStringAndFormat(config);
                }
            } else {
                makeDateFromInput(config);
            }
            return new Moment(config);
        }
        moment = function (input, format, lang, strict) {
            var c;
            if (typeof lang === 'boolean') {
                strict = lang;
                lang = undefined;
            }
            c = {};
            c._isAMomentObject = true;
            c._i = input;
            c._f = format;
            c._l = lang;
            c._strict = strict;
            c._isUTC = false;
            c._pf = defaultParsingFlags();
            return makeMoment(c);
        };
        moment.suppressDeprecationWarnings = false;
        moment.createFromInputFallback = deprecate('moment construction falls back to js Date. This is ' + 'discouraged and will be removed in upcoming major ' + 'release. Please refer to ' + 'https://github.com/moment/moment/issues/1407 for more info.', function (config) {
            config._d = new Date(config._i);
        });
        function pickBy(fn, moments) {
            var res, i;
            if (moments.length === 1 && isArray(moments[0])) {
                moments = moments[0];
            }
            if (!moments.length) {
                return moment();
            }
            res = moments[0];
            for (i = 1; i < moments.length; ++i) {
                if (moments[i][fn](res)) {
                    res = moments[i];
                }
            }
            return res;
        }
        moment.min = function () {
            var args = [].slice.call(arguments, 0);
            return pickBy('isBefore', args);
        };
        moment.max = function () {
            var args = [].slice.call(arguments, 0);
            return pickBy('isAfter', args);
        };
        moment.utc = function (input, format, lang, strict) {
            var c;
            if (typeof lang === 'boolean') {
                strict = lang;
                lang = undefined;
            }
            c = {};
            c._isAMomentObject = true;
            c._useUTC = true;
            c._isUTC = true;
            c._l = lang;
            c._i = input;
            c._f = format;
            c._strict = strict;
            c._pf = defaultParsingFlags();
            return makeMoment(c).utc();
        };
        moment.unix = function (input) {
            return moment(input * 1000);
        };
        moment.duration = function (input, key) {
            var duration = input, match = null, sign, ret, parseIso;
            if (moment.isDuration(input)) {
                duration = {
                    ms: input._milliseconds,
                    d: input._days,
                    M: input._months
                };
            } else if (typeof input === 'number') {
                duration = {};
                if (key) {
                    duration[key] = input;
                } else {
                    duration.milliseconds = input;
                }
            } else if (!!(match = aspNetTimeSpanJsonRegex.exec(input))) {
                sign = match[1] === '-' ? -1 : 1;
                duration = {
                    y: 0,
                    d: toInt(match[DATE]) * sign,
                    h: toInt(match[HOUR]) * sign,
                    m: toInt(match[MINUTE]) * sign,
                    s: toInt(match[SECOND]) * sign,
                    ms: toInt(match[MILLISECOND]) * sign
                };
            } else if (!!(match = isoDurationRegex.exec(input))) {
                sign = match[1] === '-' ? -1 : 1;
                parseIso = function (inp) {
                    var res = inp && parseFloat(inp.replace(',', '.'));
                    return (isNaN(res) ? 0 : res) * sign;
                };
                duration = {
                    y: parseIso(match[2]),
                    M: parseIso(match[3]),
                    d: parseIso(match[4]),
                    h: parseIso(match[5]),
                    m: parseIso(match[6]),
                    s: parseIso(match[7]),
                    w: parseIso(match[8])
                };
            }
            ret = new Duration(duration);
            if (moment.isDuration(input) && input.hasOwnProperty('_lang')) {
                ret._lang = input._lang;
            }
            return ret;
        };
        moment.version = VERSION;
        moment.defaultFormat = isoFormat;
        moment.ISO_8601 = function () {
        };
        moment.momentProperties = momentProperties;
        moment.updateOffset = function () {
        };
        moment.relativeTimeThreshold = function (threshold, limit) {
            if (relativeTimeThresholds[threshold] === undefined) {
                return false;
            }
            relativeTimeThresholds[threshold] = limit;
            return true;
        };
        moment.lang = function (key, values) {
            var r;
            if (!key) {
                return moment.fn._lang._abbr;
            }
            if (values) {
                loadLang(normalizeLanguage(key), values);
            } else if (values === null) {
                unloadLang(key);
                key = 'en';
            } else if (!languages[key]) {
                getLangDefinition(key);
            }
            r = moment.duration.fn._lang = moment.fn._lang = getLangDefinition(key);
            return r._abbr;
        };
        moment.langData = function (key) {
            if (key && key._lang && key._lang._abbr) {
                key = key._lang._abbr;
            }
            return getLangDefinition(key);
        };
        moment.isMoment = function (obj) {
            return obj instanceof Moment || obj != null && obj.hasOwnProperty('_isAMomentObject');
        };
        moment.isDuration = function (obj) {
            return obj instanceof Duration;
        };
        for (i = lists.length - 1; i >= 0; --i) {
            makeList(lists[i]);
        }
        moment.normalizeUnits = function (units) {
            return normalizeUnits(units);
        };
        moment.invalid = function (flags) {
            var m = moment.utc(NaN);
            if (flags != null) {
                extend(m._pf, flags);
            } else {
                m._pf.userInvalidated = true;
            }
            return m;
        };
        moment.parseZone = function () {
            return moment.apply(null, arguments).parseZone();
        };
        moment.parseTwoDigitYear = function (input) {
            return toInt(input) + (toInt(input) > 68 ? 1900 : 2000);
        };
        extend(moment.fn = Moment.prototype, {
            clone: function () {
                return moment(this);
            },
            valueOf: function () {
                return +this._d + (this._offset || 0) * 60000;
            },
            unix: function () {
                return Math.floor(+this / 1000);
            },
            toString: function () {
                return this.clone().lang('en').format('ddd MMM DD YYYY HH:mm:ss [GMT]ZZ');
            },
            toDate: function () {
                return this._offset ? new Date(+this) : this._d;
            },
            toISOString: function () {
                var m = moment(this).utc();
                if (0 < m.year() && m.year() <= 9999) {
                    return formatMoment(m, 'YYYY-MM-DD[T]HH:mm:ss.SSS[Z]');
                } else {
                    return formatMoment(m, 'YYYYYY-MM-DD[T]HH:mm:ss.SSS[Z]');
                }
            },
            toArray: function () {
                var m = this;
                return [
                    m.year(),
                    m.month(),
                    m.date(),
                    m.hours(),
                    m.minutes(),
                    m.seconds(),
                    m.milliseconds()
                ];
            },
            isValid: function () {
                return isValid(this);
            },
            isDSTShifted: function () {
                if (this._a) {
                    return this.isValid() && compareArrays(this._a, (this._isUTC ? moment.utc(this._a) : moment(this._a)).toArray()) > 0;
                }
                return false;
            },
            parsingFlags: function () {
                return extend({}, this._pf);
            },
            invalidAt: function () {
                return this._pf.overflow;
            },
            utc: function () {
                return this.zone(0);
            },
            local: function () {
                this.zone(0);
                this._isUTC = false;
                return this;
            },
            format: function (inputString) {
                var output = formatMoment(this, inputString || moment.defaultFormat);
                return this.lang().postformat(output);
            },
            add: function (input, val) {
                var dur;
                if (typeof input === 'string' && typeof val === 'string') {
                    dur = moment.duration(isNaN(+val) ? +input : +val, isNaN(+val) ? val : input);
                } else if (typeof input === 'string') {
                    dur = moment.duration(+val, input);
                } else {
                    dur = moment.duration(input, val);
                }
                addOrSubtractDurationFromMoment(this, dur, 1);
                return this;
            },
            subtract: function (input, val) {
                var dur;
                if (typeof input === 'string' && typeof val === 'string') {
                    dur = moment.duration(isNaN(+val) ? +input : +val, isNaN(+val) ? val : input);
                } else if (typeof input === 'string') {
                    dur = moment.duration(+val, input);
                } else {
                    dur = moment.duration(input, val);
                }
                addOrSubtractDurationFromMoment(this, dur, -1);
                return this;
            },
            diff: function (input, units, asFloat) {
                var that = makeAs(input, this), zoneDiff = (this.zone() - that.zone()) * 60000, diff, output;
                units = normalizeUnits(units);
                if (units === 'year' || units === 'month') {
                    diff = (this.daysInMonth() + that.daysInMonth()) * 43200000;
                    output = (this.year() - that.year()) * 12 + (this.month() - that.month());
                    output += (this - moment(this).startOf('month') - (that - moment(that).startOf('month'))) / diff;
                    output -= (this.zone() - moment(this).startOf('month').zone() - (that.zone() - moment(that).startOf('month').zone())) * 60000 / diff;
                    if (units === 'year') {
                        output = output / 12;
                    }
                } else {
                    diff = this - that;
                    output = units === 'second' ? diff / 1000 : units === 'minute' ? diff / 60000 : units === 'hour' ? diff / 3600000 : units === 'day' ? (diff - zoneDiff) / 86400000 : units === 'week' ? (diff - zoneDiff) / 604800000 : diff;
                }
                return asFloat ? output : absRound(output);
            },
            from: function (time, withoutSuffix) {
                return moment.duration(this.diff(time)).lang(this.lang()._abbr).humanize(!withoutSuffix);
            },
            fromNow: function (withoutSuffix) {
                return this.from(moment(), withoutSuffix);
            },
            calendar: function (time) {
                var now = time || moment(), sod = makeAs(now, this).startOf('day'), diff = this.diff(sod, 'days', true), format = diff < -6 ? 'sameElse' : diff < -1 ? 'lastWeek' : diff < 0 ? 'lastDay' : diff < 1 ? 'sameDay' : diff < 2 ? 'nextDay' : diff < 7 ? 'nextWeek' : 'sameElse';
                return this.format(this.lang().calendar(format, this));
            },
            isLeapYear: function () {
                return isLeapYear(this.year());
            },
            isDST: function () {
                return this.zone() < this.clone().month(0).zone() || this.zone() < this.clone().month(5).zone();
            },
            day: function (input) {
                var day = this._isUTC ? this._d.getUTCDay() : this._d.getDay();
                if (input != null) {
                    input = parseWeekday(input, this.lang());
                    return this.add({ d: input - day });
                } else {
                    return day;
                }
            },
            month: makeAccessor('Month', true),
            startOf: function (units) {
                units = normalizeUnits(units);
                switch (units) {
                case 'year':
                    this.month(0);
                case 'quarter':
                case 'month':
                    this.date(1);
                case 'week':
                case 'isoWeek':
                case 'day':
                    this.hours(0);
                case 'hour':
                    this.minutes(0);
                case 'minute':
                    this.seconds(0);
                case 'second':
                    this.milliseconds(0);
                }
                if (units === 'week') {
                    this.weekday(0);
                } else if (units === 'isoWeek') {
                    this.isoWeekday(1);
                }
                if (units === 'quarter') {
                    this.month(Math.floor(this.month() / 3) * 3);
                }
                return this;
            },
            endOf: function (units) {
                units = normalizeUnits(units);
                return this.startOf(units).add(units === 'isoWeek' ? 'week' : units, 1).subtract('ms', 1);
            },
            isAfter: function (input, units) {
                units = typeof units !== 'undefined' ? units : 'millisecond';
                return +this.clone().startOf(units) > +moment(input).startOf(units);
            },
            isBefore: function (input, units) {
                units = typeof units !== 'undefined' ? units : 'millisecond';
                return +this.clone().startOf(units) < +moment(input).startOf(units);
            },
            isSame: function (input, units) {
                units = units || 'ms';
                return +this.clone().startOf(units) === +makeAs(input, this).startOf(units);
            },
            min: deprecate('moment().min is deprecated, use moment.min instead. https://github.com/moment/moment/issues/1548', function (other) {
                other = moment.apply(null, arguments);
                return other < this ? this : other;
            }),
            max: deprecate('moment().max is deprecated, use moment.max instead. https://github.com/moment/moment/issues/1548', function (other) {
                other = moment.apply(null, arguments);
                return other > this ? this : other;
            }),
            zone: function (input, keepTime) {
                var offset = this._offset || 0;
                if (input != null) {
                    if (typeof input === 'string') {
                        input = timezoneMinutesFromString(input);
                    }
                    if (Math.abs(input) < 16) {
                        input = input * 60;
                    }
                    this._offset = input;
                    this._isUTC = true;
                    if (offset !== input) {
                        if (!keepTime || this._changeInProgress) {
                            addOrSubtractDurationFromMoment(this, moment.duration(offset - input, 'm'), 1, false);
                        } else if (!this._changeInProgress) {
                            this._changeInProgress = true;
                            moment.updateOffset(this, true);
                            this._changeInProgress = null;
                        }
                    }
                } else {
                    return this._isUTC ? offset : this._d.getTimezoneOffset();
                }
                return this;
            },
            zoneAbbr: function () {
                return this._isUTC ? 'UTC' : '';
            },
            zoneName: function () {
                return this._isUTC ? 'Coordinated Universal Time' : '';
            },
            parseZone: function () {
                if (this._tzm) {
                    this.zone(this._tzm);
                } else if (typeof this._i === 'string') {
                    this.zone(this._i);
                }
                return this;
            },
            hasAlignedHourOffset: function (input) {
                if (!input) {
                    input = 0;
                } else {
                    input = moment(input).zone();
                }
                return (this.zone() - input) % 60 === 0;
            },
            daysInMonth: function () {
                return daysInMonth(this.year(), this.month());
            },
            dayOfYear: function (input) {
                var dayOfYear = round((moment(this).startOf('day') - moment(this).startOf('year')) / 86400000) + 1;
                return input == null ? dayOfYear : this.add('d', input - dayOfYear);
            },
            quarter: function (input) {
                return input == null ? Math.ceil((this.month() + 1) / 3) : this.month((input - 1) * 3 + this.month() % 3);
            },
            weekYear: function (input) {
                var year = weekOfYear(this, this.lang()._week.dow, this.lang()._week.doy).year;
                return input == null ? year : this.add('y', input - year);
            },
            isoWeekYear: function (input) {
                var year = weekOfYear(this, 1, 4).year;
                return input == null ? year : this.add('y', input - year);
            },
            week: function (input) {
                var week = this.lang().week(this);
                return input == null ? week : this.add('d', (input - week) * 7);
            },
            isoWeek: function (input) {
                var week = weekOfYear(this, 1, 4).week;
                return input == null ? week : this.add('d', (input - week) * 7);
            },
            weekday: function (input) {
                var weekday = (this.day() + 7 - this.lang()._week.dow) % 7;
                return input == null ? weekday : this.add('d', input - weekday);
            },
            isoWeekday: function (input) {
                return input == null ? this.day() || 7 : this.day(this.day() % 7 ? input : input - 7);
            },
            isoWeeksInYear: function () {
                return weeksInYear(this.year(), 1, 4);
            },
            weeksInYear: function () {
                var weekInfo = this._lang._week;
                return weeksInYear(this.year(), weekInfo.dow, weekInfo.doy);
            },
            get: function (units) {
                units = normalizeUnits(units);
                return this[units]();
            },
            set: function (units, value) {
                units = normalizeUnits(units);
                if (typeof this[units] === 'function') {
                    this[units](value);
                }
                return this;
            },
            lang: function (key) {
                if (key === undefined) {
                    return this._lang;
                } else {
                    this._lang = getLangDefinition(key);
                    return this;
                }
            }
        });
        function rawMonthSetter(mom, value) {
            var dayOfMonth;
            if (typeof value === 'string') {
                value = mom.lang().monthsParse(value);
                if (typeof value !== 'number') {
                    return mom;
                }
            }
            dayOfMonth = Math.min(mom.date(), daysInMonth(mom.year(), value));
            mom._d['set' + (mom._isUTC ? 'UTC' : '') + 'Month'](value, dayOfMonth);
            return mom;
        }
        function rawGetter(mom, unit) {
            return mom._d['get' + (mom._isUTC ? 'UTC' : '') + unit]();
        }
        function rawSetter(mom, unit, value) {
            if (unit === 'Month') {
                return rawMonthSetter(mom, value);
            } else {
                return mom._d['set' + (mom._isUTC ? 'UTC' : '') + unit](value);
            }
        }
        function makeAccessor(unit, keepTime) {
            return function (value) {
                if (value != null) {
                    rawSetter(this, unit, value);
                    moment.updateOffset(this, keepTime);
                    return this;
                } else {
                    return rawGetter(this, unit);
                }
            };
        }
        moment.fn.millisecond = moment.fn.milliseconds = makeAccessor('Milliseconds', false);
        moment.fn.second = moment.fn.seconds = makeAccessor('Seconds', false);
        moment.fn.minute = moment.fn.minutes = makeAccessor('Minutes', false);
        moment.fn.hour = moment.fn.hours = makeAccessor('Hours', true);
        moment.fn.date = makeAccessor('Date', true);
        moment.fn.dates = deprecate('dates accessor is deprecated. Use date instead.', makeAccessor('Date', true));
        moment.fn.year = makeAccessor('FullYear', true);
        moment.fn.years = deprecate('years accessor is deprecated. Use year instead.', makeAccessor('FullYear', true));
        moment.fn.days = moment.fn.day;
        moment.fn.months = moment.fn.month;
        moment.fn.weeks = moment.fn.week;
        moment.fn.isoWeeks = moment.fn.isoWeek;
        moment.fn.quarters = moment.fn.quarter;
        moment.fn.toJSON = moment.fn.toISOString;
        extend(moment.duration.fn = Duration.prototype, {
            _bubble: function () {
                var milliseconds = this._milliseconds, days = this._days, months = this._months, data = this._data, seconds, minutes, hours, years;
                data.milliseconds = milliseconds % 1000;
                seconds = absRound(milliseconds / 1000);
                data.seconds = seconds % 60;
                minutes = absRound(seconds / 60);
                data.minutes = minutes % 60;
                hours = absRound(minutes / 60);
                data.hours = hours % 24;
                days += absRound(hours / 24);
                data.days = days % 30;
                months += absRound(days / 30);
                data.months = months % 12;
                years = absRound(months / 12);
                data.years = years;
            },
            weeks: function () {
                return absRound(this.days() / 7);
            },
            valueOf: function () {
                return this._milliseconds + this._days * 86400000 + this._months % 12 * 2592000000 + toInt(this._months / 12) * 31536000000;
            },
            humanize: function (withSuffix) {
                var difference = +this, output = relativeTime(difference, !withSuffix, this.lang());
                if (withSuffix) {
                    output = this.lang().pastFuture(difference, output);
                }
                return this.lang().postformat(output);
            },
            add: function (input, val) {
                var dur = moment.duration(input, val);
                this._milliseconds += dur._milliseconds;
                this._days += dur._days;
                this._months += dur._months;
                this._bubble();
                return this;
            },
            subtract: function (input, val) {
                var dur = moment.duration(input, val);
                this._milliseconds -= dur._milliseconds;
                this._days -= dur._days;
                this._months -= dur._months;
                this._bubble();
                return this;
            },
            get: function (units) {
                units = normalizeUnits(units);
                return this[units.toLowerCase() + 's']();
            },
            as: function (units) {
                units = normalizeUnits(units);
                return this['as' + units.charAt(0).toUpperCase() + units.slice(1) + 's']();
            },
            lang: moment.fn.lang,
            toIsoString: function () {
                var years = Math.abs(this.years()), months = Math.abs(this.months()), days = Math.abs(this.days()), hours = Math.abs(this.hours()), minutes = Math.abs(this.minutes()), seconds = Math.abs(this.seconds() + this.milliseconds() / 1000);
                if (!this.asSeconds()) {
                    return 'P0D';
                }
                return (this.asSeconds() < 0 ? '-' : '') + 'P' + (years ? years + 'Y' : '') + (months ? months + 'M' : '') + (days ? days + 'D' : '') + (hours || minutes || seconds ? 'T' : '') + (hours ? hours + 'H' : '') + (minutes ? minutes + 'M' : '') + (seconds ? seconds + 'S' : '');
            }
        });
        function makeDurationGetter(name) {
            moment.duration.fn[name] = function () {
                return this._data[name];
            };
        }
        function makeDurationAsGetter(name, factor) {
            moment.duration.fn['as' + name] = function () {
                return +this / factor;
            };
        }
        for (i in unitMillisecondFactors) {
            if (unitMillisecondFactors.hasOwnProperty(i)) {
                makeDurationAsGetter(i, unitMillisecondFactors[i]);
                makeDurationGetter(i.toLowerCase());
            }
        }
        makeDurationAsGetter('Weeks', 604800000);
        moment.duration.fn.asMonths = function () {
            return (+this - this.years() * 31536000000) / 2592000000 + this.years() * 12;
        };
        moment.lang('en', {
            ordinal: function (number) {
                var b = number % 10, output = toInt(number % 100 / 10) === 1 ? 'th' : b === 1 ? 'st' : b === 2 ? 'nd' : b === 3 ? 'rd' : 'th';
                return number + output;
            }
        });
        function makeGlobal(shouldDeprecate) {
            if (typeof ender !== 'undefined') {
                return;
            }
            oldGlobalMoment = globalScope.moment;
            if (shouldDeprecate) {
                globalScope.moment = deprecate('Accessing Moment through the global scope is ' + 'deprecated, and will be removed in an upcoming ' + 'release.', moment);
            } else {
                globalScope.moment = moment;
            }
        }
        if (hasModule) {
            module.exports = moment;
        }
    }.call(this));
});

define('moment', ['moment/moment'], function ( main ) { return main; });

define('m/sight/list.tpl', [], function () {
    return '<!--* @ignore* @file list.tpl* @author fanyy* @time 16-1-15--><!-- target: returnSightNearbyList --><!-- for: ${list} as ${item} --><li class="sight_item" style="background-image: url(${item.image})"><a class="sight_link" href="/m/sight/guide?id=${item.id}"><div class="img_mask"></div><div class="title">${item.name}</div><div class="detail"><div class="distance"><i class="location"></i><span class="number">${item.dis}</span><span class="text">${item.dis_unit}</span></div><div class="like"><span class="number fr">${item.param1}</span><i class="content fr"></i><i class="ver fr"></i><span class="number fr">${item.param3}</span><i class="collect fr"></i></div></div></a></li><!-- /for --><!-- /target --><!-- target: returnGuideNearbyList --><!-- for: ${list} as ${item} --><li class="item"><a href="/m/sight/landscape?id=${item.id}" class="item_link"><div class="bg_img" style="background-image: url(${item.image})"></div><div class="content-box"><div class="title"><span class="name">${item.name}</span><span class="location"></span><span class="dis">${item.dis}</span><span class="dis_unit">${item.dis_unit}</span></div><div class="content">${item.content}</div></div><div class="headset-box"><div class="headset"></div><div class="time">${item.audio_len}</div></div></a></li><!-- /for --><!-- /target -->';
});

define('common/config', [], function () {
    var rootUrl = '' + window.location.protocol + '//' + window.location.host;
    var version = '/api/1.0';
    var URL = {
            VERSION: version,
            ROOT: rootUrl,
            SIGHT_LIST: rootUrl + version + '/search/label',
            LANDSCAPE_LIST: rootUrl + '/m/sightapi/landscape'
        };
    return { URL: URL };
});

define('common/global', ['jquery'], function () {
    var $ = require('jquery');
    var _cache = {};
    var cache = {
            set: function (key, val) {
                _cache[key] = val;
                return this;
            },
            get: function (key) {
                return _cache[key];
            },
            clear: function () {
                _cache = {};
                return this;
            },
            remove: function (key) {
                if (key) {
                    delete _cache[key];
                }
                return this;
            }
        };
    function init() {
        $(document).ready(function () {
        });
        var global = window.GLOBAL || {};
        for (var key in global) {
            cache.set(key, global[key]);
        }
        return cache;
    }
    return init();
});

define('saber-emitter/emitter', [], function () {
    function Emitter() {
    }
    var proto = Emitter.prototype;
    proto._getEvents = function () {
        if (!this._events) {
            this._events = {};
        }
        return this._events;
    };
    proto._getMaxListeners = function () {
        if (isNaN(this.maxListeners)) {
            this.maxListeners = 10;
        }
        return this.maxListeners;
    };
    proto.on = function (event, listener) {
        var events = this._getEvents();
        var maxListeners = this._getMaxListeners();
        events[event] = events[event] || [];
        var currentListeners = events[event].length;
        if (currentListeners >= maxListeners && maxListeners !== 0) {
            throw new RangeError('Warning: possible Emitter memory leak detected. ' + currentListeners + ' listeners added.');
        }
        events[event].push(listener);
        return this;
    };
    proto.once = function (event, listener) {
        var me = this;
        function on() {
            me.off(event, on);
            listener.apply(this, arguments);
        }
        on.listener = listener;
        this.on(event, on);
        return this;
    };
    proto.off = function (event, listener) {
        var events = this._getEvents();
        if (0 === arguments.length) {
            this._events = {};
            return this;
        }
        var listeners = events[event];
        if (!listeners) {
            return this;
        }
        if (1 === arguments.length) {
            delete events[event];
            return this;
        }
        var cb;
        for (var i = 0; i < listeners.length; i++) {
            cb = listeners[i];
            if (cb === listener || cb.listener === listener) {
                listeners.splice(i, 1);
                break;
            }
        }
        return this;
    };
    proto.emit = function (event) {
        var events = this._getEvents();
        var listeners = events[event];
        var args = Array.prototype.slice.call(arguments, 1);
        if (listeners) {
            listeners = listeners.slice(0);
            for (var i = 0, len = listeners.length; i < len; i++) {
                listeners[i].apply(this, args);
            }
        }
        return this;
    };
    proto.listeners = function (event) {
        var events = this._getEvents();
        return events[event] || [];
    };
    proto.setMaxListeners = function (number) {
        this.maxListeners = number;
        return this;
    };
    Emitter.mixin = function (obj) {
        for (var key in Emitter.prototype) {
            obj[key] = Emitter.prototype[key];
        }
        return obj;
    };
    return Emitter;
});

define('saber-emitter', ['saber-emitter/emitter'], function ( main ) { return main; });

define('common/XEmitter', [
    'require',
    'saber-emitter',
    'jquery'
], function (require) {
    var Emitter = require('saber-emitter');
    var $ = require('jquery');
    Emitter.mixin = function (target) {
        target = $.type(target) === 'function' && target.prototype;
        var proto = Emitter.prototype;
        for (var p in proto) {
            if (proto.hasOwnProperty(p)) {
                target[p] = proto[p];
            }
        }
    };
    function XEmitter() {
    }
    XEmitter.prototype = { constructor: XEmitter };
    Emitter.mixin(XEmitter);
    XEmitter.mixin = Emitter.mixin;
    return XEmitter;
});

define('common/Remoter', [
    'require',
    './config',
    'jquery',
    'common/global',
    './XEmitter'
], function (require) {
    var config = require('./config');
    var $ = require('jquery');
    var global = require('common/global');
    var XEmitter = require('./XEmitter');
    function Remoter(urlName) {
        this.opt = {};
        this.opt.url = urlName;
        if (!config.URL[urlName]) {
            throw [
                '[err]',
                'url:',
                urlName,
                'undefined'
            ].join(' ');
        }
    }
    Remoter.hooks = { token: global.get('token') };
    Remoter.prototype = {
        constructor: Remoter,
        remote: function (method, data) {
            var me = this;
            if (typeof method !== 'string') {
                data = method;
                method = 'post';
            }
            data = $.extend({}, Remoter.hooks, data);
            return $.ajax({
                url: config.URL[me.opt.url],
                type: method || 'post',
                async: true,
                data: method === 'get' ? $.param(data) : data,
                dataType: 'json',
                success: function (data) {
                    var status = +data.status;
                    if (status < 1025) {
                        if (status === 0) {
                            me.emit('success', data.data);
                        } else if (status === 302) {
                            window.location.href = data.data.url;
                        } else if (status === 101) {
                            me.emit('success', {
                                imgCode: true,
                                status: data.status,
                                data: data.data
                            });
                        } else {
                            me.emit('fail', data.statusInfo);
                        }
                    } else if (status > 1024 && status < 99999) {
                        me.emit('success', {
                            status: status,
                            statusInfo: data.statusInfo,
                            bizError: true
                        });
                    }
                },
                error: function (e) {
                    me.emit('error');
                }
            });
        },
        jsonp: function (data) {
            var me = this;
            return $.jsonp({
                url: config.URL[me.opt.url],
                callback: 'callback',
                data: data,
                success: function (data) {
                    me.emit('success', data);
                },
                error: function () {
                    me.emit('error');
                }
            });
        }
    };
    XEmitter.mixin(Remoter);
    return Remoter;
});

(function (window, document, Math) {
    var rAF = window.requestAnimationFrame || window.webkitRequestAnimationFrame || window.mozRequestAnimationFrame || window.oRequestAnimationFrame || window.msRequestAnimationFrame || function (callback) {
            window.setTimeout(callback, 1000 / 60);
        };
    var utils = function () {
            var me = {};
            var _elementStyle = document.createElement('div').style;
            var _vendor = function () {
                    var vendors = [
                            't',
                            'webkitT',
                            'MozT',
                            'msT',
                            'OT'
                        ], transform, i = 0, l = vendors.length;
                    for (; i < l; i++) {
                        transform = vendors[i] + 'ransform';
                        if (transform in _elementStyle)
                            return vendors[i].substr(0, vendors[i].length - 1);
                    }
                    return false;
                }();
            function _prefixStyle(style) {
                if (_vendor === false)
                    return false;
                if (_vendor === '')
                    return style;
                return _vendor + style.charAt(0).toUpperCase() + style.substr(1);
            }
            me.getTime = Date.now || function getTime() {
                return new Date().getTime();
            };
            me.extend = function (target, obj) {
                for (var i in obj) {
                    target[i] = obj[i];
                }
            };
            me.addEvent = function (el, type, fn, capture) {
                el.addEventListener(type, fn, !!capture);
            };
            me.removeEvent = function (el, type, fn, capture) {
                el.removeEventListener(type, fn, !!capture);
            };
            me.prefixPointerEvent = function (pointerEvent) {
                return window.MSPointerEvent ? 'MSPointer' + pointerEvent.charAt(9).toUpperCase() + pointerEvent.substr(10) : pointerEvent;
            };
            me.momentum = function (current, start, time, lowerMargin, wrapperSize, deceleration) {
                var distance = current - start, speed = Math.abs(distance) / time, destination, duration;
                deceleration = deceleration === undefined ? 0.0006 : deceleration;
                destination = current + speed * speed / (2 * deceleration) * (distance < 0 ? -1 : 1);
                duration = speed / deceleration;
                if (destination < lowerMargin) {
                    destination = wrapperSize ? lowerMargin - wrapperSize / 2.5 * (speed / 8) : lowerMargin;
                    distance = Math.abs(destination - current);
                    duration = distance / speed;
                } else if (destination > 0) {
                    destination = wrapperSize ? wrapperSize / 2.5 * (speed / 8) : 0;
                    distance = Math.abs(current) + destination;
                    duration = distance / speed;
                }
                return {
                    destination: Math.round(destination),
                    duration: duration
                };
            };
            var _transform = _prefixStyle('transform');
            me.extend(me, {
                hasTransform: _transform !== false,
                hasPerspective: _prefixStyle('perspective') in _elementStyle,
                hasTouch: 'ontouchstart' in window,
                hasPointer: window.PointerEvent || window.MSPointerEvent,
                hasTransition: _prefixStyle('transition') in _elementStyle
            });
            me.isBadAndroid = /Android /.test(window.navigator.appVersion) && !/Chrome\/\d/.test(window.navigator.appVersion);
            me.extend(me.style = {}, {
                transform: _transform,
                transitionTimingFunction: _prefixStyle('transitionTimingFunction'),
                transitionDuration: _prefixStyle('transitionDuration'),
                transitionDelay: _prefixStyle('transitionDelay'),
                transformOrigin: _prefixStyle('transformOrigin')
            });
            me.hasClass = function (e, c) {
                var re = new RegExp('(^|\\s)' + c + '(\\s|$)');
                return re.test(e.className);
            };
            me.addClass = function (e, c) {
                if (me.hasClass(e, c)) {
                    return;
                }
                var newclass = e.className.split(' ');
                newclass.push(c);
                e.className = newclass.join(' ');
            };
            me.removeClass = function (e, c) {
                if (!me.hasClass(e, c)) {
                    return;
                }
                var re = new RegExp('(^|\\s)' + c + '(\\s|$)', 'g');
                e.className = e.className.replace(re, ' ');
            };
            me.offset = function (el) {
                var left = -el.offsetLeft, top = -el.offsetTop;
                while (el = el.offsetParent) {
                    left -= el.offsetLeft;
                    top -= el.offsetTop;
                }
                return {
                    left: left,
                    top: top
                };
            };
            me.preventDefaultException = function (el, exceptions) {
                for (var i in exceptions) {
                    if (exceptions[i].test(el[i])) {
                        return true;
                    }
                }
                return false;
            };
            me.extend(me.eventType = {}, {
                touchstart: 1,
                touchmove: 1,
                touchend: 1,
                mousedown: 2,
                mousemove: 2,
                mouseup: 2,
                pointerdown: 3,
                pointermove: 3,
                pointerup: 3,
                MSPointerDown: 3,
                MSPointerMove: 3,
                MSPointerUp: 3
            });
            me.extend(me.ease = {}, {
                quadratic: {
                    style: 'cubic-bezier(0.25, 0.46, 0.45, 0.94)',
                    fn: function (k) {
                        return k * (2 - k);
                    }
                },
                circular: {
                    style: 'cubic-bezier(0.1, 0.57, 0.1, 1)',
                    fn: function (k) {
                        return Math.sqrt(1 - --k * k);
                    }
                },
                back: {
                    style: 'cubic-bezier(0.175, 0.885, 0.32, 1.275)',
                    fn: function (k) {
                        var b = 4;
                        return (k = k - 1) * k * ((b + 1) * k + b) + 1;
                    }
                },
                bounce: {
                    style: '',
                    fn: function (k) {
                        if ((k /= 1) < 1 / 2.75) {
                            return 7.5625 * k * k;
                        } else if (k < 2 / 2.75) {
                            return 7.5625 * (k -= 1.5 / 2.75) * k + 0.75;
                        } else if (k < 2.5 / 2.75) {
                            return 7.5625 * (k -= 2.25 / 2.75) * k + 0.9375;
                        } else {
                            return 7.5625 * (k -= 2.625 / 2.75) * k + 0.984375;
                        }
                    }
                },
                elastic: {
                    style: '',
                    fn: function (k) {
                        var f = 0.22, e = 0.4;
                        if (k === 0) {
                            return 0;
                        }
                        if (k == 1) {
                            return 1;
                        }
                        return e * Math.pow(2, -10 * k) * Math.sin((k - f / 4) * (2 * Math.PI) / f) + 1;
                    }
                }
            });
            me.tap = function (e, eventName) {
                var ev = document.createEvent('Event');
                ev.initEvent(eventName, true, true);
                ev.pageX = e.pageX;
                ev.pageY = e.pageY;
                e.target.dispatchEvent(ev);
            };
            me.click = function (e) {
                var target = e.target, ev;
                if (!/(SELECT|INPUT|TEXTAREA)/i.test(target.tagName)) {
                    ev = document.createEvent('MouseEvents');
                    ev.initMouseEvent('click', true, true, e.view, 1, target.screenX, target.screenY, target.clientX, target.clientY, e.ctrlKey, e.altKey, e.shiftKey, e.metaKey, 0, null);
                    ev._constructed = true;
                    target.dispatchEvent(ev);
                }
            };
            return me;
        }();
    function IScroll(el, options) {
        this.wrapper = typeof el == 'string' ? document.querySelector(el) : el;
        this.scroller = this.wrapper.children[0];
        this.scrollerStyle = this.scroller.style;
        this.options = {
            resizeScrollbars: true,
            mouseWheelSpeed: 20,
            snapThreshold: 0.334,
            startX: 0,
            startY: 0,
            scrollY: true,
            directionLockThreshold: 5,
            momentum: true,
            bounce: true,
            bounceTime: 600,
            bounceEasing: '',
            preventDefault: true,
            preventDefaultException: { tagName: /^(INPUT|TEXTAREA|BUTTON|SELECT)$/ },
            HWCompositing: true,
            useTransition: true,
            useTransform: true
        };
        for (var i in options) {
            this.options[i] = options[i];
        }
        this.translateZ = this.options.HWCompositing && utils.hasPerspective ? ' translateZ(0)' : '';
        this.options.useTransition = utils.hasTransition && this.options.useTransition;
        this.options.useTransform = utils.hasTransform && this.options.useTransform;
        this.options.eventPassthrough = this.options.eventPassthrough === true ? 'vertical' : this.options.eventPassthrough;
        this.options.preventDefault = !this.options.eventPassthrough && this.options.preventDefault;
        this.options.scrollY = this.options.eventPassthrough == 'vertical' ? false : this.options.scrollY;
        this.options.scrollX = this.options.eventPassthrough == 'horizontal' ? false : this.options.scrollX;
        this.options.freeScroll = this.options.freeScroll && !this.options.eventPassthrough;
        this.options.directionLockThreshold = this.options.eventPassthrough ? 0 : this.options.directionLockThreshold;
        this.options.bounceEasing = typeof this.options.bounceEasing == 'string' ? utils.ease[this.options.bounceEasing] || utils.ease.circular : this.options.bounceEasing;
        this.options.resizePolling = this.options.resizePolling === undefined ? 60 : this.options.resizePolling;
        if (this.options.tap === true) {
            this.options.tap = 'tap';
        }
        if (this.options.shrinkScrollbars == 'scale') {
            this.options.useTransition = false;
        }
        this.options.invertWheelDirection = this.options.invertWheelDirection ? -1 : 1;
        if (this.options.probeType == 3) {
            this.options.useTransition = false;
        }
        this.x = 0;
        this.y = 0;
        this.directionX = 0;
        this.directionY = 0;
        this._events = {};
        this._init();
        this.refresh();
        this.scrollTo(this.options.startX, this.options.startY);
        this.enable();
    }
    IScroll.prototype = {
        version: '5.1.3',
        _init: function () {
            this._initEvents();
            if (this.options.scrollbars || this.options.indicators) {
                this._initIndicators();
            }
            if (this.options.mouseWheel) {
                this._initWheel();
            }
            if (this.options.snap) {
                this._initSnap();
            }
            if (this.options.keyBindings) {
                this._initKeys();
            }
        },
        destroy: function () {
            this._initEvents(true);
            this._execEvent('destroy');
        },
        _transitionEnd: function (e) {
            if (e.target != this.scroller || !this.isInTransition) {
                return;
            }
            this._transitionTime();
            if (!this.resetPosition(this.options.bounceTime)) {
                this.isInTransition = false;
                this._execEvent('scrollEnd');
            }
        },
        _start: function (e) {
            if (utils.eventType[e.type] != 1) {
                if (e.button !== 0) {
                    return;
                }
            }
            if (!this.enabled || this.initiated && utils.eventType[e.type] !== this.initiated) {
                return;
            }
            if (this.options.preventDefault && !utils.isBadAndroid && !utils.preventDefaultException(e.target, this.options.preventDefaultException)) {
                e.preventDefault();
            }
            var point = e.touches ? e.touches[0] : e, pos;
            this.initiated = utils.eventType[e.type];
            this.moved = false;
            this.distX = 0;
            this.distY = 0;
            this.directionX = 0;
            this.directionY = 0;
            this.directionLocked = 0;
            this._transitionTime();
            this.startTime = utils.getTime();
            if (this.options.useTransition && this.isInTransition) {
                this.isInTransition = false;
                pos = this.getComputedPosition();
                this._translate(Math.round(pos.x), Math.round(pos.y));
                this._execEvent('scrollEnd');
            } else if (!this.options.useTransition && this.isAnimating) {
                this.isAnimating = false;
                this._execEvent('scrollEnd');
            }
            this.startX = this.x;
            this.startY = this.y;
            this.absStartX = this.x;
            this.absStartY = this.y;
            this.pointX = point.pageX;
            this.pointY = point.pageY;
            this._execEvent('beforeScrollStart');
        },
        _move: function (e) {
            if (!this.enabled || utils.eventType[e.type] !== this.initiated) {
                return;
            }
            if (this.options.preventDefault) {
                e.preventDefault();
            }
            var point = e.touches ? e.touches[0] : e, deltaX = point.pageX - this.pointX, deltaY = point.pageY - this.pointY, timestamp = utils.getTime(), newX, newY, absDistX, absDistY;
            this.pointX = point.pageX;
            this.pointY = point.pageY;
            this.distX += deltaX;
            this.distY += deltaY;
            absDistX = Math.abs(this.distX);
            absDistY = Math.abs(this.distY);
            if (timestamp - this.endTime > 300 && (absDistX < 10 && absDistY < 10)) {
                return;
            }
            if (!this.directionLocked && !this.options.freeScroll) {
                if (absDistX > absDistY + this.options.directionLockThreshold) {
                    this.directionLocked = 'h';
                } else if (absDistY >= absDistX + this.options.directionLockThreshold) {
                    this.directionLocked = 'v';
                } else {
                    this.directionLocked = 'n';
                }
            }
            if (this.directionLocked == 'h') {
                if (this.options.eventPassthrough == 'vertical') {
                    e.preventDefault();
                } else if (this.options.eventPassthrough == 'horizontal') {
                    this.initiated = false;
                    return;
                }
                deltaY = 0;
            } else if (this.directionLocked == 'v') {
                if (this.options.eventPassthrough == 'horizontal') {
                    e.preventDefault();
                } else if (this.options.eventPassthrough == 'vertical') {
                    this.initiated = false;
                    return;
                }
                deltaX = 0;
            }
            deltaX = this.hasHorizontalScroll ? deltaX : 0;
            deltaY = this.hasVerticalScroll ? deltaY : 0;
            newX = this.x + deltaX;
            newY = this.y + deltaY;
            if (newX > 0 || newX < this.maxScrollX) {
                newX = this.options.bounce ? this.x + deltaX / 3 : newX > 0 ? 0 : this.maxScrollX;
            }
            if (newY > 0 || newY < this.maxScrollY) {
                newY = this.options.bounce ? this.y + deltaY / 3 : newY > 0 ? 0 : this.maxScrollY;
            }
            this.directionX = deltaX > 0 ? -1 : deltaX < 0 ? 1 : 0;
            this.directionY = deltaY > 0 ? -1 : deltaY < 0 ? 1 : 0;
            if (!this.moved) {
                this._execEvent('scrollStart');
            }
            this.moved = true;
            this._translate(newX, newY);
            if (timestamp - this.startTime > 300) {
                this.startTime = timestamp;
                this.startX = this.x;
                this.startY = this.y;
                if (this.options.probeType == 1) {
                    this._execEvent('scroll');
                }
            }
            if (this.options.probeType > 1) {
                this._execEvent('scroll');
            }
        },
        _end: function (e) {
            if (!this.enabled || utils.eventType[e.type] !== this.initiated) {
                return;
            }
            if (this.options.preventDefault && !utils.preventDefaultException(e.target, this.options.preventDefaultException)) {
                e.preventDefault();
            }
            var point = e.changedTouches ? e.changedTouches[0] : e, momentumX, momentumY, duration = utils.getTime() - this.startTime, newX = Math.round(this.x), newY = Math.round(this.y), distanceX = Math.abs(newX - this.startX), distanceY = Math.abs(newY - this.startY), time = 0, easing = '';
            this.isInTransition = 0;
            this.initiated = 0;
            this.endTime = utils.getTime();
            if (this.resetPosition(this.options.bounceTime)) {
                return;
            }
            this.scrollTo(newX, newY);
            if (!this.moved) {
                if (this.options.tap) {
                    utils.tap(e, this.options.tap);
                }
                if (this.options.click) {
                    utils.click(e);
                }
                this._execEvent('scrollCancel');
                return;
            }
            if (this._events.flick && duration < 200 && distanceX < 100 && distanceY < 100) {
                this._execEvent('flick');
                return;
            }
            if (this.options.momentum && duration < 300) {
                momentumX = this.hasHorizontalScroll ? utils.momentum(this.x, this.startX, duration, this.maxScrollX, this.options.bounce ? this.wrapperWidth : 0, this.options.deceleration) : {
                    destination: newX,
                    duration: 0
                };
                momentumY = this.hasVerticalScroll ? utils.momentum(this.y, this.startY, duration, this.maxScrollY, this.options.bounce ? this.wrapperHeight : 0, this.options.deceleration) : {
                    destination: newY,
                    duration: 0
                };
                newX = momentumX.destination;
                newY = momentumY.destination;
                time = Math.max(momentumX.duration, momentumY.duration);
                this.isInTransition = 1;
            }
            if (this.options.snap) {
                var snap = this._nearestSnap(newX, newY);
                this.currentPage = snap;
                time = this.options.snapSpeed || Math.max(Math.max(Math.min(Math.abs(newX - snap.x), 1000), Math.min(Math.abs(newY - snap.y), 1000)), 300);
                newX = snap.x;
                newY = snap.y;
                this.directionX = 0;
                this.directionY = 0;
                easing = this.options.bounceEasing;
            }
            if (newX != this.x || newY != this.y) {
                if (newX > 0 || newX < this.maxScrollX || newY > 0 || newY < this.maxScrollY) {
                    easing = utils.ease.quadratic;
                }
                this.scrollTo(newX, newY, time, easing);
                return;
            }
            this._execEvent('scrollEnd');
        },
        _resize: function () {
            var that = this;
            clearTimeout(this.resizeTimeout);
            this.resizeTimeout = setTimeout(function () {
                that.refresh();
            }, this.options.resizePolling);
        },
        resetPosition: function (time) {
            var x = this.x, y = this.y;
            time = time || 0;
            if (!this.hasHorizontalScroll || this.x > 0) {
                x = 0;
            } else if (this.x < this.maxScrollX) {
                x = this.maxScrollX;
            }
            if (!this.hasVerticalScroll || this.y > 0) {
                y = 0;
            } else if (this.y < this.maxScrollY) {
                y = this.maxScrollY;
            }
            if (x == this.x && y == this.y) {
                return false;
            }
            this.scrollTo(x, y, time, this.options.bounceEasing);
            return true;
        },
        disable: function () {
            this.enabled = false;
        },
        enable: function () {
            this.enabled = true;
        },
        refresh: function () {
            var rf = this.wrapper.offsetHeight;
            this.wrapperWidth = this.wrapper.clientWidth;
            this.wrapperHeight = this.wrapper.clientHeight;
            this.scrollerWidth = this.scroller.offsetWidth;
            this.scrollerHeight = this.scroller.offsetHeight;
            this.maxScrollX = this.wrapperWidth - this.scrollerWidth;
            this.maxScrollY = this.wrapperHeight - this.scrollerHeight;
            this.hasHorizontalScroll = this.options.scrollX && this.maxScrollX < 0;
            this.hasVerticalScroll = this.options.scrollY && this.maxScrollY < 0;
            if (!this.hasHorizontalScroll) {
                this.maxScrollX = 0;
                this.scrollerWidth = this.wrapperWidth;
            }
            if (!this.hasVerticalScroll) {
                this.maxScrollY = 0;
                this.scrollerHeight = this.wrapperHeight;
            }
            this.endTime = 0;
            this.directionX = 0;
            this.directionY = 0;
            this.wrapperOffset = utils.offset(this.wrapper);
            this._execEvent('refresh');
            this.resetPosition();
        },
        on: function (type, fn) {
            if (!this._events[type]) {
                this._events[type] = [];
            }
            this._events[type].push(fn);
        },
        off: function (type, fn) {
            if (!this._events[type]) {
                return;
            }
            var index = this._events[type].indexOf(fn);
            if (index > -1) {
                this._events[type].splice(index, 1);
            }
        },
        _execEvent: function (type) {
            if (!this._events[type]) {
                return;
            }
            var i = 0, l = this._events[type].length;
            if (!l) {
                return;
            }
            for (; i < l; i++) {
                this._events[type][i].apply(this, [].slice.call(arguments, 1));
            }
        },
        scrollBy: function (x, y, time, easing) {
            x = this.x + x;
            y = this.y + y;
            time = time || 0;
            this.scrollTo(x, y, time, easing);
        },
        scrollTo: function (x, y, time, easing) {
            easing = easing || utils.ease.circular;
            this.isInTransition = this.options.useTransition && time > 0;
            if (!time || this.options.useTransition && easing.style) {
                this._transitionTimingFunction(easing.style);
                this._transitionTime(time);
                this._translate(x, y);
            } else {
                this._animate(x, y, time, easing.fn);
            }
        },
        scrollToElement: function (el, time, offsetX, offsetY, easing) {
            el = el.nodeType ? el : this.scroller.querySelector(el);
            if (!el) {
                return;
            }
            var pos = utils.offset(el);
            pos.left -= this.wrapperOffset.left;
            pos.top -= this.wrapperOffset.top;
            if (offsetX === true) {
                offsetX = Math.round(el.offsetWidth / 2 - this.wrapper.offsetWidth / 2);
            }
            if (offsetY === true) {
                offsetY = Math.round(el.offsetHeight / 2 - this.wrapper.offsetHeight / 2);
            }
            pos.left -= offsetX || 0;
            pos.top -= offsetY || 0;
            pos.left = pos.left > 0 ? 0 : pos.left < this.maxScrollX ? this.maxScrollX : pos.left;
            pos.top = pos.top > 0 ? 0 : pos.top < this.maxScrollY ? this.maxScrollY : pos.top;
            time = time === undefined || time === null || time === 'auto' ? Math.max(Math.abs(this.x - pos.left), Math.abs(this.y - pos.top)) : time;
            this.scrollTo(pos.left, pos.top, time, easing);
        },
        _transitionTime: function (time) {
            time = time || 0;
            this.scrollerStyle[utils.style.transitionDuration] = time + 'ms';
            if (!time && utils.isBadAndroid) {
                this.scrollerStyle[utils.style.transitionDuration] = '0.001s';
            }
            if (this.indicators) {
                for (var i = this.indicators.length; i--;) {
                    this.indicators[i].transitionTime(time);
                }
            }
        },
        _transitionTimingFunction: function (easing) {
            this.scrollerStyle[utils.style.transitionTimingFunction] = easing;
            if (this.indicators) {
                for (var i = this.indicators.length; i--;) {
                    this.indicators[i].transitionTimingFunction(easing);
                }
            }
        },
        _translate: function (x, y) {
            if (this.options.useTransform) {
                this.scrollerStyle[utils.style.transform] = 'translate(' + x + 'px,' + y + 'px)' + this.translateZ;
            } else {
                x = Math.round(x);
                y = Math.round(y);
                this.scrollerStyle.left = x + 'px';
                this.scrollerStyle.top = y + 'px';
            }
            this.x = x;
            this.y = y;
            if (this.indicators) {
                for (var i = this.indicators.length; i--;) {
                    this.indicators[i].updatePosition();
                }
            }
        },
        _initEvents: function (remove) {
            var eventType = remove ? utils.removeEvent : utils.addEvent, target = this.options.bindToWrapper ? this.wrapper : window;
            eventType(window, 'orientationchange', this);
            eventType(window, 'resize', this);
            if (this.options.click) {
                eventType(this.wrapper, 'click', this, true);
            }
            if (!this.options.disableMouse) {
                eventType(this.wrapper, 'mousedown', this);
                eventType(target, 'mousemove', this);
                eventType(target, 'mousecancel', this);
                eventType(target, 'mouseup', this);
            }
            if (utils.hasPointer && !this.options.disablePointer) {
                eventType(this.wrapper, utils.prefixPointerEvent('pointerdown'), this);
                eventType(target, utils.prefixPointerEvent('pointermove'), this);
                eventType(target, utils.prefixPointerEvent('pointercancel'), this);
                eventType(target, utils.prefixPointerEvent('pointerup'), this);
            }
            if (utils.hasTouch && !this.options.disableTouch) {
                eventType(this.wrapper, 'touchstart', this);
                eventType(target, 'touchmove', this);
                eventType(target, 'touchcancel', this);
                eventType(target, 'touchend', this);
            }
            eventType(this.scroller, 'transitionend', this);
            eventType(this.scroller, 'webkitTransitionEnd', this);
            eventType(this.scroller, 'oTransitionEnd', this);
            eventType(this.scroller, 'MSTransitionEnd', this);
        },
        getComputedPosition: function () {
            var matrix = window.getComputedStyle(this.scroller, null), x, y;
            if (this.options.useTransform) {
                matrix = matrix[utils.style.transform].split(')')[0].split(', ');
                x = +(matrix[12] || matrix[4]);
                y = +(matrix[13] || matrix[5]);
            } else {
                x = +matrix.left.replace(/[^-\d.]/g, '');
                y = +matrix.top.replace(/[^-\d.]/g, '');
            }
            return {
                x: x,
                y: y
            };
        },
        _initIndicators: function () {
            var interactive = this.options.interactiveScrollbars, customStyle = typeof this.options.scrollbars != 'string', indicators = [], indicator;
            var that = this;
            this.indicators = [];
            if (this.options.scrollbars) {
                if (this.options.scrollY) {
                    indicator = {
                        el: createDefaultScrollbar('v', interactive, this.options.scrollbars),
                        interactive: interactive,
                        defaultScrollbars: true,
                        customStyle: customStyle,
                        resize: this.options.resizeScrollbars,
                        shrink: this.options.shrinkScrollbars,
                        fade: this.options.fadeScrollbars,
                        listenX: false
                    };
                    this.wrapper.appendChild(indicator.el);
                    indicators.push(indicator);
                }
                if (this.options.scrollX) {
                    indicator = {
                        el: createDefaultScrollbar('h', interactive, this.options.scrollbars),
                        interactive: interactive,
                        defaultScrollbars: true,
                        customStyle: customStyle,
                        resize: this.options.resizeScrollbars,
                        shrink: this.options.shrinkScrollbars,
                        fade: this.options.fadeScrollbars,
                        listenY: false
                    };
                    this.wrapper.appendChild(indicator.el);
                    indicators.push(indicator);
                }
            }
            if (this.options.indicators) {
                indicators = indicators.concat(this.options.indicators);
            }
            for (var i = indicators.length; i--;) {
                this.indicators.push(new Indicator(this, indicators[i]));
            }
            function _indicatorsMap(fn) {
                for (var i = that.indicators.length; i--;) {
                    fn.call(that.indicators[i]);
                }
            }
            if (this.options.fadeScrollbars) {
                this.on('scrollEnd', function () {
                    _indicatorsMap(function () {
                        this.fade();
                    });
                });
                this.on('scrollCancel', function () {
                    _indicatorsMap(function () {
                        this.fade();
                    });
                });
                this.on('scrollStart', function () {
                    _indicatorsMap(function () {
                        this.fade(1);
                    });
                });
                this.on('beforeScrollStart', function () {
                    _indicatorsMap(function () {
                        this.fade(1, true);
                    });
                });
            }
            this.on('refresh', function () {
                _indicatorsMap(function () {
                    this.refresh();
                });
            });
            this.on('destroy', function () {
                _indicatorsMap(function () {
                    this.destroy();
                });
                delete this.indicators;
            });
        },
        _initWheel: function () {
            utils.addEvent(this.wrapper, 'wheel', this);
            utils.addEvent(this.wrapper, 'mousewheel', this);
            utils.addEvent(this.wrapper, 'DOMMouseScroll', this);
            this.on('destroy', function () {
                utils.removeEvent(this.wrapper, 'wheel', this);
                utils.removeEvent(this.wrapper, 'mousewheel', this);
                utils.removeEvent(this.wrapper, 'DOMMouseScroll', this);
            });
        },
        _wheel: function (e) {
            if (!this.enabled) {
                return;
            }
            e.preventDefault();
            e.stopPropagation();
            var wheelDeltaX, wheelDeltaY, newX, newY, that = this;
            if (this.wheelTimeout === undefined) {
                that._execEvent('scrollStart');
            }
            clearTimeout(this.wheelTimeout);
            this.wheelTimeout = setTimeout(function () {
                that._execEvent('scrollEnd');
                that.wheelTimeout = undefined;
            }, 400);
            if ('deltaX' in e) {
                if (e.deltaMode === 1) {
                    wheelDeltaX = -e.deltaX * this.options.mouseWheelSpeed;
                    wheelDeltaY = -e.deltaY * this.options.mouseWheelSpeed;
                } else {
                    wheelDeltaX = -e.deltaX;
                    wheelDeltaY = -e.deltaY;
                }
            } else if ('wheelDeltaX' in e) {
                wheelDeltaX = e.wheelDeltaX / 120 * this.options.mouseWheelSpeed;
                wheelDeltaY = e.wheelDeltaY / 120 * this.options.mouseWheelSpeed;
            } else if ('wheelDelta' in e) {
                wheelDeltaX = wheelDeltaY = e.wheelDelta / 120 * this.options.mouseWheelSpeed;
            } else if ('detail' in e) {
                wheelDeltaX = wheelDeltaY = -e.detail / 3 * this.options.mouseWheelSpeed;
            } else {
                return;
            }
            wheelDeltaX *= this.options.invertWheelDirection;
            wheelDeltaY *= this.options.invertWheelDirection;
            if (!this.hasVerticalScroll) {
                wheelDeltaX = wheelDeltaY;
                wheelDeltaY = 0;
            }
            if (this.options.snap) {
                newX = this.currentPage.pageX;
                newY = this.currentPage.pageY;
                if (wheelDeltaX > 0) {
                    newX--;
                } else if (wheelDeltaX < 0) {
                    newX++;
                }
                if (wheelDeltaY > 0) {
                    newY--;
                } else if (wheelDeltaY < 0) {
                    newY++;
                }
                this.goToPage(newX, newY);
                return;
            }
            newX = this.x + Math.round(this.hasHorizontalScroll ? wheelDeltaX : 0);
            newY = this.y + Math.round(this.hasVerticalScroll ? wheelDeltaY : 0);
            if (newX > 0) {
                newX = 0;
            } else if (newX < this.maxScrollX) {
                newX = this.maxScrollX;
            }
            if (newY > 0) {
                newY = 0;
            } else if (newY < this.maxScrollY) {
                newY = this.maxScrollY;
            }
            this.scrollTo(newX, newY, 0);
            if (this.options.probeType > 1) {
                this._execEvent('scroll');
            }
        },
        _initSnap: function () {
            this.currentPage = {};
            if (typeof this.options.snap == 'string') {
                this.options.snap = this.scroller.querySelectorAll(this.options.snap);
            }
            this.on('refresh', function () {
                var i = 0, l, m = 0, n, cx, cy, x = 0, y, stepX = this.options.snapStepX || this.wrapperWidth, stepY = this.options.snapStepY || this.wrapperHeight, el;
                this.pages = [];
                if (!this.wrapperWidth || !this.wrapperHeight || !this.scrollerWidth || !this.scrollerHeight) {
                    return;
                }
                if (this.options.snap === true) {
                    cx = Math.round(stepX / 2);
                    cy = Math.round(stepY / 2);
                    while (x > -this.scrollerWidth) {
                        this.pages[i] = [];
                        l = 0;
                        y = 0;
                        while (y > -this.scrollerHeight) {
                            this.pages[i][l] = {
                                x: Math.max(x, this.maxScrollX),
                                y: Math.max(y, this.maxScrollY),
                                width: stepX,
                                height: stepY,
                                cx: x - cx,
                                cy: y - cy
                            };
                            y -= stepY;
                            l++;
                        }
                        x -= stepX;
                        i++;
                    }
                } else {
                    el = this.options.snap;
                    l = el.length;
                    n = -1;
                    for (; i < l; i++) {
                        if (i === 0 || el[i].offsetLeft <= el[i - 1].offsetLeft) {
                            m = 0;
                            n++;
                        }
                        if (!this.pages[m]) {
                            this.pages[m] = [];
                        }
                        x = Math.max(-el[i].offsetLeft, this.maxScrollX);
                        y = Math.max(-el[i].offsetTop, this.maxScrollY);
                        cx = x - Math.round(el[i].offsetWidth / 2);
                        cy = y - Math.round(el[i].offsetHeight / 2);
                        this.pages[m][n] = {
                            x: x,
                            y: y,
                            width: el[i].offsetWidth,
                            height: el[i].offsetHeight,
                            cx: cx,
                            cy: cy
                        };
                        if (x > this.maxScrollX) {
                            m++;
                        }
                    }
                }
                this.goToPage(this.currentPage.pageX || 0, this.currentPage.pageY || 0, 0);
                if (this.options.snapThreshold % 1 === 0) {
                    this.snapThresholdX = this.options.snapThreshold;
                    this.snapThresholdY = this.options.snapThreshold;
                } else {
                    this.snapThresholdX = Math.round(this.pages[this.currentPage.pageX][this.currentPage.pageY].width * this.options.snapThreshold);
                    this.snapThresholdY = Math.round(this.pages[this.currentPage.pageX][this.currentPage.pageY].height * this.options.snapThreshold);
                }
            });
            this.on('flick', function () {
                var time = this.options.snapSpeed || Math.max(Math.max(Math.min(Math.abs(this.x - this.startX), 1000), Math.min(Math.abs(this.y - this.startY), 1000)), 300);
                this.goToPage(this.currentPage.pageX + this.directionX, this.currentPage.pageY + this.directionY, time);
            });
        },
        _nearestSnap: function (x, y) {
            if (!this.pages.length) {
                return {
                    x: 0,
                    y: 0,
                    pageX: 0,
                    pageY: 0
                };
            }
            var i = 0, l = this.pages.length, m = 0;
            if (Math.abs(x - this.absStartX) < this.snapThresholdX && Math.abs(y - this.absStartY) < this.snapThresholdY) {
                return this.currentPage;
            }
            if (x > 0) {
                x = 0;
            } else if (x < this.maxScrollX) {
                x = this.maxScrollX;
            }
            if (y > 0) {
                y = 0;
            } else if (y < this.maxScrollY) {
                y = this.maxScrollY;
            }
            for (; i < l; i++) {
                if (x >= this.pages[i][0].cx) {
                    x = this.pages[i][0].x;
                    break;
                }
            }
            l = this.pages[i].length;
            for (; m < l; m++) {
                if (y >= this.pages[0][m].cy) {
                    y = this.pages[0][m].y;
                    break;
                }
            }
            if (i == this.currentPage.pageX) {
                i += this.directionX;
                if (i < 0) {
                    i = 0;
                } else if (i >= this.pages.length) {
                    i = this.pages.length - 1;
                }
                x = this.pages[i][0].x;
            }
            if (m == this.currentPage.pageY) {
                m += this.directionY;
                if (m < 0) {
                    m = 0;
                } else if (m >= this.pages[0].length) {
                    m = this.pages[0].length - 1;
                }
                y = this.pages[0][m].y;
            }
            return {
                x: x,
                y: y,
                pageX: i,
                pageY: m
            };
        },
        goToPage: function (x, y, time, easing) {
            easing = easing || this.options.bounceEasing;
            if (x >= this.pages.length) {
                x = this.pages.length - 1;
            } else if (x < 0) {
                x = 0;
            }
            if (y >= this.pages[x].length) {
                y = this.pages[x].length - 1;
            } else if (y < 0) {
                y = 0;
            }
            var posX = this.pages[x][y].x, posY = this.pages[x][y].y;
            time = time === undefined ? this.options.snapSpeed || Math.max(Math.max(Math.min(Math.abs(posX - this.x), 1000), Math.min(Math.abs(posY - this.y), 1000)), 300) : time;
            this.currentPage = {
                x: posX,
                y: posY,
                pageX: x,
                pageY: y
            };
            this.scrollTo(posX, posY, time, easing);
        },
        next: function (time, easing) {
            var x = this.currentPage.pageX, y = this.currentPage.pageY;
            x++;
            if (x >= this.pages.length && this.hasVerticalScroll) {
                x = 0;
                y++;
            }
            this.goToPage(x, y, time, easing);
        },
        prev: function (time, easing) {
            var x = this.currentPage.pageX, y = this.currentPage.pageY;
            x--;
            if (x < 0 && this.hasVerticalScroll) {
                x = 0;
                y--;
            }
            this.goToPage(x, y, time, easing);
        },
        _initKeys: function (e) {
            var keys = {
                    pageUp: 33,
                    pageDown: 34,
                    end: 35,
                    home: 36,
                    left: 37,
                    up: 38,
                    right: 39,
                    down: 40
                };
            var i;
            if (typeof this.options.keyBindings == 'object') {
                for (i in this.options.keyBindings) {
                    if (typeof this.options.keyBindings[i] == 'string') {
                        this.options.keyBindings[i] = this.options.keyBindings[i].toUpperCase().charCodeAt(0);
                    }
                }
            } else {
                this.options.keyBindings = {};
            }
            for (i in keys) {
                this.options.keyBindings[i] = this.options.keyBindings[i] || keys[i];
            }
            utils.addEvent(window, 'keydown', this);
            this.on('destroy', function () {
                utils.removeEvent(window, 'keydown', this);
            });
        },
        _key: function (e) {
            if (!this.enabled) {
                return;
            }
            var snap = this.options.snap, newX = snap ? this.currentPage.pageX : this.x, newY = snap ? this.currentPage.pageY : this.y, now = utils.getTime(), prevTime = this.keyTime || 0, acceleration = 0.25, pos;
            if (this.options.useTransition && this.isInTransition) {
                pos = this.getComputedPosition();
                this._translate(Math.round(pos.x), Math.round(pos.y));
                this.isInTransition = false;
            }
            this.keyAcceleration = now - prevTime < 200 ? Math.min(this.keyAcceleration + acceleration, 50) : 0;
            switch (e.keyCode) {
            case this.options.keyBindings.pageUp:
                if (this.hasHorizontalScroll && !this.hasVerticalScroll) {
                    newX += snap ? 1 : this.wrapperWidth;
                } else {
                    newY += snap ? 1 : this.wrapperHeight;
                }
                break;
            case this.options.keyBindings.pageDown:
                if (this.hasHorizontalScroll && !this.hasVerticalScroll) {
                    newX -= snap ? 1 : this.wrapperWidth;
                } else {
                    newY -= snap ? 1 : this.wrapperHeight;
                }
                break;
            case this.options.keyBindings.end:
                newX = snap ? this.pages.length - 1 : this.maxScrollX;
                newY = snap ? this.pages[0].length - 1 : this.maxScrollY;
                break;
            case this.options.keyBindings.home:
                newX = 0;
                newY = 0;
                break;
            case this.options.keyBindings.left:
                newX += snap ? -1 : 5 + this.keyAcceleration >> 0;
                break;
            case this.options.keyBindings.up:
                newY += snap ? 1 : 5 + this.keyAcceleration >> 0;
                break;
            case this.options.keyBindings.right:
                newX -= snap ? -1 : 5 + this.keyAcceleration >> 0;
                break;
            case this.options.keyBindings.down:
                newY -= snap ? 1 : 5 + this.keyAcceleration >> 0;
                break;
            default:
                return;
            }
            if (snap) {
                this.goToPage(newX, newY);
                return;
            }
            if (newX > 0) {
                newX = 0;
                this.keyAcceleration = 0;
            } else if (newX < this.maxScrollX) {
                newX = this.maxScrollX;
                this.keyAcceleration = 0;
            }
            if (newY > 0) {
                newY = 0;
                this.keyAcceleration = 0;
            } else if (newY < this.maxScrollY) {
                newY = this.maxScrollY;
                this.keyAcceleration = 0;
            }
            this.scrollTo(newX, newY, 0);
            this.keyTime = now;
        },
        _animate: function (destX, destY, duration, easingFn) {
            var that = this, startX = this.x, startY = this.y, startTime = utils.getTime(), destTime = startTime + duration;
            function step() {
                var now = utils.getTime(), newX, newY, easing;
                if (now >= destTime) {
                    that.isAnimating = false;
                    that._translate(destX, destY);
                    if (!that.resetPosition(that.options.bounceTime)) {
                        that._execEvent('scrollEnd');
                    }
                    return;
                }
                now = (now - startTime) / duration;
                easing = easingFn(now);
                newX = (destX - startX) * easing + startX;
                newY = (destY - startY) * easing + startY;
                that._translate(newX, newY);
                if (that.isAnimating) {
                    rAF(step);
                }
                if (that.options.probeType == 3) {
                    that._execEvent('scroll');
                }
            }
            this.isAnimating = true;
            step();
        },
        handleEvent: function (e) {
            switch (e.type) {
            case 'touchstart':
            case 'pointerdown':
            case 'MSPointerDown':
            case 'mousedown':
                this._start(e);
                break;
            case 'touchmove':
            case 'pointermove':
            case 'MSPointerMove':
            case 'mousemove':
                this._move(e);
                break;
            case 'touchend':
            case 'pointerup':
            case 'MSPointerUp':
            case 'mouseup':
            case 'touchcancel':
            case 'pointercancel':
            case 'MSPointerCancel':
            case 'mousecancel':
                this._end(e);
                break;
            case 'orientationchange':
            case 'resize':
                this._resize();
                break;
            case 'transitionend':
            case 'webkitTransitionEnd':
            case 'oTransitionEnd':
            case 'MSTransitionEnd':
                this._transitionEnd(e);
                break;
            case 'wheel':
            case 'DOMMouseScroll':
            case 'mousewheel':
                this._wheel(e);
                break;
            case 'keydown':
                this._key(e);
                break;
            case 'click':
                if (!e._constructed) {
                    e.preventDefault();
                    e.stopPropagation();
                }
                break;
            }
        }
    };
    function createDefaultScrollbar(direction, interactive, type) {
        var scrollbar = document.createElement('div'), indicator = document.createElement('div');
        if (type === true) {
            scrollbar.style.cssText = 'position:absolute;z-index:9999';
            indicator.style.cssText = '-webkit-box-sizing:border-box;-moz-box-sizing:border-box;box-sizing:border-box;position:absolute;background:rgba(0,0,0,0.5);border:1px solid rgba(255,255,255,0.9);border-radius:3px';
        }
        indicator.className = 'iScrollIndicator';
        if (direction == 'h') {
            if (type === true) {
                scrollbar.style.cssText += ';height:7px;left:2px;right:2px;bottom:0';
                indicator.style.height = '100%';
            }
            scrollbar.className = 'iScrollHorizontalScrollbar';
        } else {
            if (type === true) {
                scrollbar.style.cssText += ';width:7px;bottom:2px;top:2px;right:1px';
                indicator.style.width = '100%';
            }
            scrollbar.className = 'iScrollVerticalScrollbar';
        }
        scrollbar.style.cssText += ';overflow:hidden';
        if (!interactive) {
            scrollbar.style.pointerEvents = 'none';
        }
        scrollbar.appendChild(indicator);
        return scrollbar;
    }
    function Indicator(scroller, options) {
        this.wrapper = typeof options.el == 'string' ? document.querySelector(options.el) : options.el;
        this.wrapperStyle = this.wrapper.style;
        this.indicator = this.wrapper.children[0];
        this.indicatorStyle = this.indicator.style;
        this.scroller = scroller;
        this.options = {
            listenX: true,
            listenY: true,
            interactive: false,
            resize: true,
            defaultScrollbars: false,
            shrink: false,
            fade: false,
            speedRatioX: 0,
            speedRatioY: 0
        };
        for (var i in options) {
            this.options[i] = options[i];
        }
        this.sizeRatioX = 1;
        this.sizeRatioY = 1;
        this.maxPosX = 0;
        this.maxPosY = 0;
        if (this.options.interactive) {
            if (!this.options.disableTouch) {
                utils.addEvent(this.indicator, 'touchstart', this);
                utils.addEvent(window, 'touchend', this);
            }
            if (!this.options.disablePointer) {
                utils.addEvent(this.indicator, utils.prefixPointerEvent('pointerdown'), this);
                utils.addEvent(window, utils.prefixPointerEvent('pointerup'), this);
            }
            if (!this.options.disableMouse) {
                utils.addEvent(this.indicator, 'mousedown', this);
                utils.addEvent(window, 'mouseup', this);
            }
        }
        if (this.options.fade) {
            this.wrapperStyle[utils.style.transform] = this.scroller.translateZ;
            this.wrapperStyle[utils.style.transitionDuration] = utils.isBadAndroid ? '0.001s' : '0ms';
            this.wrapperStyle.opacity = '0';
        }
    }
    Indicator.prototype = {
        handleEvent: function (e) {
            switch (e.type) {
            case 'touchstart':
            case 'pointerdown':
            case 'MSPointerDown':
            case 'mousedown':
                this._start(e);
                break;
            case 'touchmove':
            case 'pointermove':
            case 'MSPointerMove':
            case 'mousemove':
                this._move(e);
                break;
            case 'touchend':
            case 'pointerup':
            case 'MSPointerUp':
            case 'mouseup':
            case 'touchcancel':
            case 'pointercancel':
            case 'MSPointerCancel':
            case 'mousecancel':
                this._end(e);
                break;
            }
        },
        destroy: function () {
            if (this.options.interactive) {
                utils.removeEvent(this.indicator, 'touchstart', this);
                utils.removeEvent(this.indicator, utils.prefixPointerEvent('pointerdown'), this);
                utils.removeEvent(this.indicator, 'mousedown', this);
                utils.removeEvent(window, 'touchmove', this);
                utils.removeEvent(window, utils.prefixPointerEvent('pointermove'), this);
                utils.removeEvent(window, 'mousemove', this);
                utils.removeEvent(window, 'touchend', this);
                utils.removeEvent(window, utils.prefixPointerEvent('pointerup'), this);
                utils.removeEvent(window, 'mouseup', this);
            }
            if (this.options.defaultScrollbars) {
                this.wrapper.parentNode.removeChild(this.wrapper);
            }
        },
        _start: function (e) {
            var point = e.touches ? e.touches[0] : e;
            e.preventDefault();
            e.stopPropagation();
            this.transitionTime();
            this.initiated = true;
            this.moved = false;
            this.lastPointX = point.pageX;
            this.lastPointY = point.pageY;
            this.startTime = utils.getTime();
            if (!this.options.disableTouch) {
                utils.addEvent(window, 'touchmove', this);
            }
            if (!this.options.disablePointer) {
                utils.addEvent(window, utils.prefixPointerEvent('pointermove'), this);
            }
            if (!this.options.disableMouse) {
                utils.addEvent(window, 'mousemove', this);
            }
            this.scroller._execEvent('beforeScrollStart');
        },
        _move: function (e) {
            var point = e.touches ? e.touches[0] : e, deltaX, deltaY, newX, newY, timestamp = utils.getTime();
            if (!this.moved) {
                this.scroller._execEvent('scrollStart');
            }
            this.moved = true;
            deltaX = point.pageX - this.lastPointX;
            this.lastPointX = point.pageX;
            deltaY = point.pageY - this.lastPointY;
            this.lastPointY = point.pageY;
            newX = this.x + deltaX;
            newY = this.y + deltaY;
            this._pos(newX, newY);
            if (this.scroller.options.probeType == 1 && timestamp - this.startTime > 300) {
                this.startTime = timestamp;
                this.scroller._execEvent('scroll');
            } else if (this.scroller.options.probeType > 1) {
                this.scroller._execEvent('scroll');
            }
            e.preventDefault();
            e.stopPropagation();
        },
        _end: function (e) {
            if (!this.initiated) {
                return;
            }
            this.initiated = false;
            e.preventDefault();
            e.stopPropagation();
            utils.removeEvent(window, 'touchmove', this);
            utils.removeEvent(window, utils.prefixPointerEvent('pointermove'), this);
            utils.removeEvent(window, 'mousemove', this);
            if (this.scroller.options.snap) {
                var snap = this.scroller._nearestSnap(this.scroller.x, this.scroller.y);
                var time = this.options.snapSpeed || Math.max(Math.max(Math.min(Math.abs(this.scroller.x - snap.x), 1000), Math.min(Math.abs(this.scroller.y - snap.y), 1000)), 300);
                if (this.scroller.x != snap.x || this.scroller.y != snap.y) {
                    this.scroller.directionX = 0;
                    this.scroller.directionY = 0;
                    this.scroller.currentPage = snap;
                    this.scroller.scrollTo(snap.x, snap.y, time, this.scroller.options.bounceEasing);
                }
            }
            if (this.moved) {
                this.scroller._execEvent('scrollEnd');
            }
        },
        transitionTime: function (time) {
            time = time || 0;
            this.indicatorStyle[utils.style.transitionDuration] = time + 'ms';
            if (!time && utils.isBadAndroid) {
                this.indicatorStyle[utils.style.transitionDuration] = '0.001s';
            }
        },
        transitionTimingFunction: function (easing) {
            this.indicatorStyle[utils.style.transitionTimingFunction] = easing;
        },
        refresh: function () {
            this.transitionTime();
            if (this.options.listenX && !this.options.listenY) {
                this.indicatorStyle.display = this.scroller.hasHorizontalScroll ? 'block' : 'none';
            } else if (this.options.listenY && !this.options.listenX) {
                this.indicatorStyle.display = this.scroller.hasVerticalScroll ? 'block' : 'none';
            } else {
                this.indicatorStyle.display = this.scroller.hasHorizontalScroll || this.scroller.hasVerticalScroll ? 'block' : 'none';
            }
            if (this.scroller.hasHorizontalScroll && this.scroller.hasVerticalScroll) {
                utils.addClass(this.wrapper, 'iScrollBothScrollbars');
                utils.removeClass(this.wrapper, 'iScrollLoneScrollbar');
                if (this.options.defaultScrollbars && this.options.customStyle) {
                    if (this.options.listenX) {
                        this.wrapper.style.right = '8px';
                    } else {
                        this.wrapper.style.bottom = '8px';
                    }
                }
            } else {
                utils.removeClass(this.wrapper, 'iScrollBothScrollbars');
                utils.addClass(this.wrapper, 'iScrollLoneScrollbar');
                if (this.options.defaultScrollbars && this.options.customStyle) {
                    if (this.options.listenX) {
                        this.wrapper.style.right = '2px';
                    } else {
                        this.wrapper.style.bottom = '2px';
                    }
                }
            }
            var r = this.wrapper.offsetHeight;
            if (this.options.listenX) {
                this.wrapperWidth = this.wrapper.clientWidth;
                if (this.options.resize) {
                    this.indicatorWidth = Math.max(Math.round(this.wrapperWidth * this.wrapperWidth / (this.scroller.scrollerWidth || this.wrapperWidth || 1)), 8);
                    this.indicatorStyle.width = this.indicatorWidth + 'px';
                } else {
                    this.indicatorWidth = this.indicator.clientWidth;
                }
                this.maxPosX = this.wrapperWidth - this.indicatorWidth;
                if (this.options.shrink == 'clip') {
                    this.minBoundaryX = -this.indicatorWidth + 8;
                    this.maxBoundaryX = this.wrapperWidth - 8;
                } else {
                    this.minBoundaryX = 0;
                    this.maxBoundaryX = this.maxPosX;
                }
                this.sizeRatioX = this.options.speedRatioX || this.scroller.maxScrollX && this.maxPosX / this.scroller.maxScrollX;
            }
            if (this.options.listenY) {
                this.wrapperHeight = this.wrapper.clientHeight;
                if (this.options.resize) {
                    this.indicatorHeight = Math.max(Math.round(this.wrapperHeight * this.wrapperHeight / (this.scroller.scrollerHeight || this.wrapperHeight || 1)), 8);
                    this.indicatorStyle.height = this.indicatorHeight + 'px';
                } else {
                    this.indicatorHeight = this.indicator.clientHeight;
                }
                this.maxPosY = this.wrapperHeight - this.indicatorHeight;
                if (this.options.shrink == 'clip') {
                    this.minBoundaryY = -this.indicatorHeight + 8;
                    this.maxBoundaryY = this.wrapperHeight - 8;
                } else {
                    this.minBoundaryY = 0;
                    this.maxBoundaryY = this.maxPosY;
                }
                this.maxPosY = this.wrapperHeight - this.indicatorHeight;
                this.sizeRatioY = this.options.speedRatioY || this.scroller.maxScrollY && this.maxPosY / this.scroller.maxScrollY;
            }
            this.updatePosition();
        },
        updatePosition: function () {
            var x = this.options.listenX && Math.round(this.sizeRatioX * this.scroller.x) || 0, y = this.options.listenY && Math.round(this.sizeRatioY * this.scroller.y) || 0;
            if (!this.options.ignoreBoundaries) {
                if (x < this.minBoundaryX) {
                    if (this.options.shrink == 'scale') {
                        this.width = Math.max(this.indicatorWidth + x, 8);
                        this.indicatorStyle.width = this.width + 'px';
                    }
                    x = this.minBoundaryX;
                } else if (x > this.maxBoundaryX) {
                    if (this.options.shrink == 'scale') {
                        this.width = Math.max(this.indicatorWidth - (x - this.maxPosX), 8);
                        this.indicatorStyle.width = this.width + 'px';
                        x = this.maxPosX + this.indicatorWidth - this.width;
                    } else {
                        x = this.maxBoundaryX;
                    }
                } else if (this.options.shrink == 'scale' && this.width != this.indicatorWidth) {
                    this.width = this.indicatorWidth;
                    this.indicatorStyle.width = this.width + 'px';
                }
                if (y < this.minBoundaryY) {
                    if (this.options.shrink == 'scale') {
                        this.height = Math.max(this.indicatorHeight + y * 3, 8);
                        this.indicatorStyle.height = this.height + 'px';
                    }
                    y = this.minBoundaryY;
                } else if (y > this.maxBoundaryY) {
                    if (this.options.shrink == 'scale') {
                        this.height = Math.max(this.indicatorHeight - (y - this.maxPosY) * 3, 8);
                        this.indicatorStyle.height = this.height + 'px';
                        y = this.maxPosY + this.indicatorHeight - this.height;
                    } else {
                        y = this.maxBoundaryY;
                    }
                } else if (this.options.shrink == 'scale' && this.height != this.indicatorHeight) {
                    this.height = this.indicatorHeight;
                    this.indicatorStyle.height = this.height + 'px';
                }
            }
            this.x = x;
            this.y = y;
            if (this.scroller.options.useTransform) {
                this.indicatorStyle[utils.style.transform] = 'translate(' + x + 'px,' + y + 'px)' + this.scroller.translateZ;
            } else {
                this.indicatorStyle.left = x + 'px';
                this.indicatorStyle.top = y + 'px';
            }
        },
        _pos: function (x, y) {
            if (x < 0) {
                x = 0;
            } else if (x > this.maxPosX) {
                x = this.maxPosX;
            }
            if (y < 0) {
                y = 0;
            } else if (y > this.maxPosY) {
                y = this.maxPosY;
            }
            x = this.options.listenX ? Math.round(x / this.sizeRatioX) : this.scroller.x;
            y = this.options.listenY ? Math.round(y / this.sizeRatioY) : this.scroller.y;
            this.scroller.scrollTo(x, y);
        },
        fade: function (val, hold) {
            if (hold && !this.visible) {
                return;
            }
            clearTimeout(this.fadeTimeout);
            this.fadeTimeout = null;
            var time = val ? 250 : 500, delay = val ? 0 : 300;
            val = val ? '1' : '0';
            this.wrapperStyle[utils.style.transitionDuration] = time + 'ms';
            this.fadeTimeout = setTimeout(function (val) {
                this.wrapperStyle.opacity = val;
                this.visible = +val;
            }.bind(this, val), delay);
        }
    };
    IScroll.utils = utils;
    if (typeof define === 'function' && typeof define.amd === 'object' && define.amd) {
        define('common/iscroll', [], function () {
            return IScroll;
        });
    } else if (typeof module !== 'undefined' && module.exports) {
        module.exports = IScroll;
    } else {
        window.IScroll = IScroll;
    }
}(window, document, Math));

define('m/sight/guide', [
    'require',
    'jquery',
    'common/common',
    'moment',
    'etpl',
    './list.tpl',
    'common/Remoter',
    'common/iscroll'
], function (require) {
    var $ = require('jquery');
    var common = require('common/common');
    var moment = require('moment');
    var etpl = require('etpl');
    var tpl = require('./list.tpl');
    var Remoter = require('common/Remoter');
    var getLandscapeList = new Remoter('LANDSCAPE_LIST');
    var IScroll = require('common/iscroll');
    var myScroll, pullDownEl, pullDownOffset, pullUpEl, pullUpOffset, generatedCount = 0;
    var currentPage = 1;
    var html = '';
    var sightId = $('#wrapper').attr('sightId');
    function init(acenter, alist) {
        common.init();
        htmlContainer = $('#landscape_list');
        etpl.compile(tpl);
        bindEvents.init();
    }
    var bindEvents = {
            init: function () {
                this.initMap();
                this.inintScroll();
            },
            initMap: function () {
                map = new AMap.Map('container', { resizeEnable: true });
                map.plugin('AMap.Geolocation', function () {
                    geolocation = new AMap.Geolocation({
                        enableHighAccuracy: true,
                        timeout: 10000,
                        buttonOffset: new AMap.Pixel(10, 20),
                        zoomToAccuracy: true,
                        buttonPosition: 'RB'
                    });
                    map.addControl(geolocation);
                    geolocation.getCurrentPosition();
                    AMap.event.addListener(geolocation, 'complete', bindEvents.onComplete);
                    AMap.event.addListener(geolocation, 'error', bindEvents.onError);
                });
            },
            onComplete: function (data) {
                params = {
                    page: currentPage,
                    sightId: sightId,
                    x: data.position.getLng(),
                    y: data.position.getLat()
                };
                getRemoteList.getNearByList(params);
            },
            onError: function (data) {
                $('#landscape_list').html('<div class="error-text">\u554A\u54E6\uFF0C\u5B9A\u4F4D\u5931\u8D25!<a href="javascript:void(0)" class="refresh-btn"  onclick="location.reload();">&nbsp</a></div>');
            },
            inintScroll: function () {
                myScroll = new IScroll('#wrapper', {
                    probeType: 2,
                    bindToWrapper: true,
                    scrollY: true,
                    mouseWheel: true,
                    click: true
                });
                common.myScrollEvents(myScroll, bindEvents.pullUpAction, bindEvents.pullDownAction);
                setTimeout(function () {
                    document.getElementById('wrapper').style.left = '0';
                }, 800);
                document.addEventListener('touchmove', function (e) {
                    e.preventDefault();
                }, false);
            },
            pullUpAction: function () {
                currentPage++;
                params.page = currentPage;
                getRemoteList.getNearByList(params);
                myScroll.refresh();
            },
            pullDownAction: function () {
                getRemoteList.getNearByList(params);
                myScroll.refresh();
            }
        };
    var getRemoteList = {
            getNearByList: function (data) {
                getLandscapeList.remote(data);
                getLandscapeList.on('success', function (data) {
                    ajaxCallbackfun(data, 'returnGuideNearbyList');
                });
            }
        };
    var ajaxCallbackfun = function (data, tpl) {
        if (data.bizError) {
            renderError(data);
        } else {
            var pullUpEl = document.getElementById('pullUp');
            if (!data.length && currentPage == 1) {
                htmlContainer.html(etpl.render('Error', { msg: '\u5F53\u524D\u6CA1\u6709\u6570\u636E\u54DF,\u8BD5\u8BD5\u522B\u7684\u666F\u70B9\u5427' }));
                return;
            }
            if (!data.length && currentPage > 1) {
                pullUpEl.className = '';
                pullUpEl.querySelector('.pullUpLabel').innerHTML = '\u5168\u90E8\u52A0\u8F7D\u5B8C\u6BD5';
            } else {
                renderHTML(tpl, data);
                setTimeout(function () {
                    myScroll.refresh();
                }, 0);
            }
        }
    };
    var renderHTML = function (tpl, data) {
        if (currentPage == 1) {
            html = etpl.render(tpl, { list: data });
        } else {
            html = html + etpl.render(tpl, { list: data });
        }
        htmlContainer.html(html);
    };
    var renderError = function (data) {
        htmlContainer.render(etpl.render('Error', { msg: data.statusInfo }));
    };
    return { init: init };
});