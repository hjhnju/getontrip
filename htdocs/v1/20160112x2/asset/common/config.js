define('common/config', [], function () {
    var rootUrl = '' + window.location.protocol + '//' + window.location.host;
    var version = '/api/1.1';
    var URL = {
            VERSION: version,
            ROOT: rootUrl,
            SIGHT_LIST: rootUrl + '/m/sightapi/nearSight',
            NAV_LIST: rootUrl + version + '/sight/index',
            LANDSCAPE_LIST: rootUrl + version + '/sight/landscape',
            FOOD_LIST: rootUrl + version + '/sight/food',
            TOPIC_LIST: rootUrl + version + '/sight/topic',
            SPECIALTY_LIST: rootUrl + version + '/sight/specialty',
            FOOD_TOPIC: rootUrl + version + '/food/topic',
            FOOD_SHOP: rootUrl + version + '/food/shop',
            SPECIALTY_TOPIC: rootUrl + version + '/specialty/topic',
            SPECIALTY_PRODUCT: rootUrl + version + '/specialty/product'
        };
    return { URL: URL };
});