/**
 * @ignore
 * @file map.js
 * @author fanyy(1178223444@qq.com)
 * @time 15-1-11
 */

define(function(require) {
    var $ = require('jquery');
    require('common/extra/jplayer/jquery.jplayer');
    require('common/extra/jplayer/jplayer.playlist');
    var features = [];
    var infoWindow;

    function init(acenter, alist) {
        center = acenter;
        list = alist;
        bindEvents.init();
    }
    var bindEvents = {
        init: function() {
            this.initMap();
            this.initPlayer();
        },
        initMap: function() {
            map = new AMap.Map('mapBox', {
                view: new AMap.View2D({
                    zoom: 14,
                    rotation: 0
                }),
                resizeEnable: true,
                lang: 'zh_cn',
                level: 10
            });
            map.setCenter(center);
            map.setDefaultCursor('default');
            new AMap.TileLayer.Traffic({
                map: map,
                zIndex: 2
            });
            this.loadFeatures();
            map.setFitView();
            map.plugin([
                'AMap.ToolBar',
                'AMap.Scale'
            ], function() {
                map.addControl(new AMap.ToolBar());
                map.addControl(new AMap.Scale());
            });
        },
        initFeatures: function() {
            for (var i = 0; i < list.length; i++) {
                var item = {
                    type: 'Marker',
                    name: list[i].name,
                    desc: list[i].content,
                    color: 'red',
                    icon: 'cir',
                    offset: {
                        x: -9,
                        y: -31
                    },
                    lnglat: {
                        lng: list[i].x,
                        lat: list[i].y
                    },
                    title: list[i].name,
                    mp3: '/audio/' + list[i].audio
                };
                features.push(item);
            };
        },
        loadFeatures: function() {
            this.initFeatures();
            for (var feature, data, i = 0, len = features.length, j, jl, path; i < len; i++) {
                data = features[i];
                switch (data.type) {
                    case 'Marker':
                        feature = new AMap.Marker({
                            position: new AMap.LngLat(data.lnglat.lng, data.lnglat.lat),
                            zIndex: 100,
                            extData: data,
                            offset: new AMap.Pixel(data.offset.x, data.offset.y),
                            title: data.name,
                            content: '<div class="icon icon-' + data.icon + ' icon-' + data.icon + '-' + data.color + '"></div><div class="icon-name">' + data.name + '</div><div class="voice"></div>'
                        });
                        feature.setMap(map);
                        break;
                    case 'Polyline':
                        for (j = 0, jl = data.lnglat.length, path = []; j < jl; j++) {
                            path.push(new AMap.LngLat(data.lnglat[j].lng, data.lnglat[j].lat));
                        }
                        feature = new AMap.Polyline({
                            map: map,
                            path: path,
                            extData: data,
                            zIndex: 2,
                            strokeWeight: data.strokeWeight,
                            strokeColor: data.strokeColor,
                            strokeOpacity: data.strokeOpacity
                        });
                        break;
                    case 'Polygon':
                        for (j = 0, jl = data.lnglat.length, path = []; j < jl; j++) {
                            path.push(new AMap.LngLat(data.lnglat[j].lng, data.lnglat[j].lat));
                        }
                        feature = new AMap.Polygon({
                            map: map,
                            path: path,
                            extData: data,
                            zIndex: 1,
                            strokeWeight: data.strokeWeight,
                            strokeColor: data.strokeColor,
                            strokeOpacity: data.strokeOpacity,
                            fillColor: data.fillColor,
                            fillOpacity: data.fillOpacity
                        });
                        break;
                    default:
                        feature = null;
                }
                if (feature) {
                    AMap.event.addListener(feature, 'click', this.mapFeatureClick);
                }
            }
        },
        mapFeatureClick: function(e) {
            if (!infoWindow) {
                infoWindow = new AMap.InfoWindow({
                    autoMove: true
                });
            }
            var extData = e.target.getExtData();
            $('.jp-title').html(extData.name);
            infoWindow.setContent('<h5>' + extData.name + '</h5><div>' + extData.desc + '</div>');
            infoWindow.open(map, e.lnglat);
        },
        initPlayer: function() {
            var myPlaylist = new jPlayerPlaylist({
                jPlayer: '#jPlayerBox',
                cssSelectorAncestor: '#jp_container_1'
            }, features, {
                playlistOptions: {
                    enableRemoveControls: true
                },
                swfPath: '../common/extra/jplayer',
                supplied: 'mp3',
                useStateClassSkin: true,
                autoBlur: false,
                smoothPlayBar: true,
                keyEnabled: true,
                audioFullScreen: true
            });
        }
    };
    return {
        init: init
    };
});
