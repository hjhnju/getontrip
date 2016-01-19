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