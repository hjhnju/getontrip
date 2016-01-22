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
    //rootUrl = 'http://123.57.46.229:8321';
    var version = '/api/1.0';

    /**
     * URL对象，存储这个页面的全部URL
     *
     * @type {Object}
     */
    var URL = {
        VERSION: version,
        ROOT: rootUrl, 
        SIGHT_LIST: rootUrl  + '/m/sightapi/nearSight',
        NAV_LIST: rootUrl + version + '/sight/index',
        LANDSCAPE_LIST: rootUrl + version + '/sight/landscape',
        FOOD_LIST: rootUrl + version + '/sight/food',
        TOPIC_LIST: rootUrl + version + '/sight/topic',
        SPECIALTY_LIST: rootUrl + version + '/sight/specialty', 
        FOOD_TOPIC: rootUrl + version + '/food/topic',
        FOOD_SHOP: rootUrl + version + '/food/shop',
        SPECIALTY_TOPIC: rootUrl + version + '/specialty/topic',
        SPECIALTY_PRODUCT: rootUrl + version + '/specialty/product',




    };

    return {
        URL: URL
    };
});
