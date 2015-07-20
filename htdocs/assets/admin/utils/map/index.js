(function(window, document, undefined) { 

    var keyWords =$.trim($('#txtSearch').val());
    var cityName = $.trim($('#cityName').val());
    var map = new AMap.Map('map', {
        view: new AMap.View2D({
            zoom: 14,
            rotation: 0
        }),
        resizeEnable: true,
        lang: 'zh_cn'
    });
    map.setDefaultCursor('default');

    //切换城市
    map.setCity(cityName);

    //默认搜索传过来的城市名称 
    //placeSearch();

    //为地图注册click事件获取鼠标点击出的经纬度坐标
    var clickEventListener = AMap.event.addListener(map, 'click', function(e) {
        $("#txtCoordinate").val(e.lnglat.getLng() + "," + e.lnglat.getLat());

    });
     
    $(".btn-search").click(function(event) {
        keyWords = $.trim($('#txtSearch').val()); 
        placeSearch();
    });

    

    //搜索
    function placeSearch() {
     /*   //默认增加字符串市
        if (cityName.indexOf('市') < 0&&cityName.indexOf('州')< 0&&cityName.indexOf('区')< 0) {
            cityName = cityName + '市';
        }*/  
        var MSearch;
        AMap.service(["AMap.PlaceSearch"], function() {
            MSearch = new AMap.PlaceSearch({ //构造地点查询类
                pageSize: 1,
                pageIndex: 1,
                city: cityName //城市
            });
            //关键字查询
            MSearch.search(keyWords, function(status, result) {
                if (status === 'complete' && result.info === 'OK') {
                    keywordSearch_CallBack(result);
                }
            });
        });
    }

    //关键字搜素回调函数
    function keywordSearch_CallBack(data) {
        var resultStr = "";
        var poiArr = data.poiList.pois;
        var resultCount = poiArr.length;
        for (var i = 0; i < resultCount; i++) {
            addmarker(i, poiArr[i]);
        }
        map.setFitView();
    }

    //添加marker  
    function addmarker(i, d) {
        map.clearMap();
        var lngX = d.location.getLng();
        var latY = d.location.getLat();
        var markerOption = {
            map: map,
            // icon: "http://webapi.amap.com/images/" + (i + 1) + ".png",
            position: new AMap.LngLat(lngX, latY),
            topWhenMouseOver: true

        };
        var marker = new AMap.Marker(markerOption);
        marker.setMap(map);
        $("#txtCoordinate").val(lngX + "," + latY);
    }
}(window, document));
