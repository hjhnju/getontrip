/**
 * @ignore
 * @file config.js
 * @author mySunShinning(441984145@qq.com)
 * @time 14-11-15
 */

define(function() {

    /**
     * 页面域名获取
     * @type {string}
     */
    var rootUrl = '' + window.location.protocol + '//' + window.location.host;

    var version = '/api/1.0';

    /**
     * URL对象，存储这个页面的全部URL
     *
     * @type {Object}
     */
    var URL = {
        VERSION: version,
        ROOT: rootUrl,

        SIGHT_LIST: rootUrl + version + '/search/label',
        LANDSCAPE_LIST:rootUrl + '/m/sightapi/landscape',



    };

    return {
        URL: URL
    };
});
