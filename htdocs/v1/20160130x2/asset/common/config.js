define('common/config', [], function () {
    var rootUrl = '' + window.location.protocol + '//' + window.location.host;
    var version = '/api/1.1';
    var URL = {
            VERSION: version,
            ROOT: rootUrl,
            SIGHT_LIST: '/m/sightapi/nearSight',
            NAV_LIST: '/sight/index',
            LANDSCAPE_LIST: '/sight/landscape',
            FOOD_LIST: '/sight/food',
            TOPIC_LIST: '/sight/topic',
            SPECIALTY_LIST: '/sight/specialty',
            BOOK_LIST: '/sight/book',
            VIDEO_LIST: '/sight/video',
            CITY_NAV_LIST: '/city/index',
            CITY_LANDSCAPE_LIST: '/city/landscape',
            CITY_FOOD_LIST: '/city/food',
            CITY_TOPIC_LIST: '/city/topic',
            CITY_SPECIALTY_LIST: '/city/specialty',
            CITY_BOOK_LIST: '/city/book',
            CITY_VIDEO_LIST: '/city/video',
            FOOD_TOPIC: '/food/topic',
            FOOD_SHOP: '/food/shop',
            SPECIALTY_TOPIC: '/specialty/topic',
            SPECIALTY_PRODUCT: '/specialty/product'
        };
    return { URL: URL };
});