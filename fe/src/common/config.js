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
    var version = '/api/1.1';

    /**
     * URL对象，存储这个页面的全部URL
     *
     * @type {Object}
     */
    var URL = {
        VERSION: version,
        ROOT: rootUrl, 
        SIGHT_LIST:  '/m/sightapi/nearSight',
        NAV_LIST:  '/sight/index',
        LANDSCAPE_LIST:  '/sight/landscape',
        FOOD_LIST:  '/sight/food',
        TOPIC_LIST:  '/sight/topic',
        SPECIALTY_LIST:  '/sight/specialty', 
        BOOK_LIST:  '/sight/book',
        VIDEO_LIST:  '/sight/video', 

        CITY_NAV_LIST:  '/city/index',
        CITY_LANDSCAPE_LIST:  '/city/landscape',
        CITY_FOOD_LIST:  '/city/food',
        CITY_TOPIC_LIST:  '/city/topic',
        CITY_SPECIALTY_LIST:  '/city/specialty', 
        CITY_BOOK_LIST:  '/city/book',
        CITY_VIDEO_LIST:  '/city/video', 
      
        FOOD_TOPIC:  '/food/topic',
        FOOD_SHOP:  '/food/shop',
        SPECIALTY_TOPIC:  '/specialty/topic',
        SPECIALTY_PRODUCT:  '/specialty/product',




    };

    return {
        URL: URL
    };
});
