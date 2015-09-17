(function(window, document, undefined) {
    var Dashboard = function() {
        var FORMATER = 'YYYY-MM-DD';

        /*
              绑定事件
         */
        var bindEvents = function() {

        };

        /*
          柱形统计图
         */
        var line = function() {
            myChart: null,
            /**
             * echarts option
             *
             * @type {Object}
             */
            option: {
                title: {
                    text: '用户注册情况和登录情况',
                    subtext: '最近一年记录'
                },
                tooltip: {
                    trigger: 'axis'
                },
                legend: {
                    data: ['注册情况', '登录情况']
                },
                toolbox: {
                    show: true,
                    feature: {
                        mark: {
                            show: true
                        },
                        dataView: {
                            show: true,
                            readOnly: false
                        },
                        magicType: {
                            show: true,
                            type: ['line', 'bar']
                        },
                        restore: {
                            show: true
                        },
                        saveAsImage: {
                            show: true
                        }
                    }
                },
                calculable: true,
                xAxis: [{
                    type: 'category',
                    data: []
                        //data: ['1月', '2月', '3月', '4月', '5月', '6月', '7月', '8月', '9月', '10月', '11月', '12月']
                }],
                yAxis: [{
                    type: 'value'
                }],
                series: [{
                    name: '蒸发量',
                    type: 'bar',
                    data: []
                        //data: [2.0, 4.9, 7.0, 23.2, 25.6, 76.7, 135.6, 162.2, 32.6, 20.0, 6.4, 3.3],
                    markPoint: {
                        data: [{
                            type: 'max',
                            name: '最大值'
                        }, {
                            type: 'min',
                            name: '最小值'
                        }]
                    },
                    markLine: {
                        data: [{
                            type: 'average',
                            name: '平均值'
                        }]
                    }
                }, {
                    name: '降水量',
                    type: 'bar',
                    data: []
                        //data: [2.6, 5.9, 9.0, 26.4, 28.7, 70.7, 175.6, 182.2, 48.7, 18.8, 6.0, 2.3],
                        /* markPoint: {
                             data: [{
                                 name: '年最高',
                                 value: 182.2,
                                 xAxis: 7,
                                 yAxis: 183,
                                 symbolSize: 18
                             }, {
                                 name: '年最低',
                                 value: 2.3,
                                 xAxis: 11,
                                 yAxis: 3
                             }]
                         },
                         markLine: {
                             data: [{
                                 type: 'average',
                                 name: '平均值'
                             }]
                         }*/
                }]
            },

            render: function(elementId, data) {
                this.initData(data);
            },
            /**
             * 初始化图表
             * @param    {Object} data 图表数据
             */
            initData: function(data) {

                this.option.xAxis[0].data = data.x;
                this.option.series[0].data = data.y;
                //计算Y轴的最大值
                var maxTemp = Math.max.apply(Math, data.y);
                var max = maxTemp;
                //个位数
                var geWei = maxTemp - ((Math.floor(maxTemp / 10)) * 10);
                //个位数大于等于5向上进5个数值
                if (geWei >= 5) {
                    max = Math.ceil((max / 10)) * 10
                }
                //个位数小于5  向上补至5
                else {
                    max = (Math.floor(maxTemp / 10)) * 10 + 5;
                }
                this.option.yAxis[0].max = max;
                this.option.yAxis[0].min = 0;
                myChart.setOption(this.option);
            }

        };

        //用户注册时间柱形图
        var initUserRegTime = function() {

            var getLine = new Remoter('/admin/userapi/getUserRegTimeLine');
            myChart = echarts.init(document.getElementById('userRegTime-line'));
            // 过渡---------------------
            myChart.showLoading({
                text: '正在努力的读取数据中...', //loading话术
            });
            getLine.on('success', function(data) {
                if (data.bizError) {
                    container.html(etpl.render('Error', {
                        msg: data.statusInfo
                    }));
                } else {
                    myChart.hideLoading();
                    line.render('userRegTime-line', data);
                }
            });
        };

        return {
            init: function() {
                this.initLine()
            },
            initLine: function() {
                initUserRegTime();
            }
        }
    }

    new List().init();

}(window, document));
