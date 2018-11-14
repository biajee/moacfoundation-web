//百度地图
var BaiduMap = {
        map: null,
        local: null,
        config: null,
        center: null,
        zoom: 17,
        initMap: function (config) {
            this.config = config;
            this.center = new BMap.Point(config.lng, config.lat);//定义一个中心点坐标
            this.createMap();//创建地图
            this.setMapEvent();//设置地图事件
            this.addMapControl();//向地图添加控件
            this.searchNearby('交通');
        },

        //创建地图函数：
        createMap: function () {
            var map = new BMap.Map(this.config.map);//在百度地图容器中创建一个地图
            if (this.config.lng > 0)
                map.centerAndZoom(this.center, this.config.zoom);//设定地图的中心点和坐标并将地图显示在地图容器中
            else
                map.centerAndZoom("天津", 11);
            this.local = new BMap.LocalSearch(map, {
                pageCapacity: 5,
                renderOptions: {map: map, panel: this.config.result, selectFirstResult: false}
            });
            this.map = map;//将map变量存储在全局
        },
        //附近搜索
        searchNearby: function (tag) {
            this.local.searchNearby(tag, this.center, 2000);
        },
        setMapEvent: function () {
            var map = this.map;
            map.enableDragging();//启用地图拖拽事件，默认启用(可不写)
            //map.enableScrollWheelZoom();//启用地图滚轮放大缩小
            map.enableDoubleClickZoom();//启用鼠标双击放大，默认启用(可不写)
            map.enableKeyboard();//启用键盘上下左右键移动地图
        },

        //地图控件添加函数：
        addMapControl: function () {
            var map = this.map;
            //向地图中添加缩放控件
            var ctrl_nav = new BMap.NavigationControl({anchor: BMAP_ANCHOR_TOP_LEFT, type: BMAP_NAVIGATION_CONTROL_LARGE});
            map.addControl(ctrl_nav);
            //向地图中添加缩略图控件
            var ctrl_ove = new BMap.OverviewMapControl({anchor: BMAP_ANCHOR_BOTTOM_RIGHT, isOpen: 1});
            map.addControl(ctrl_ove);
            //向地图中添加比例尺控件
            var ctrl_sca = new BMap.ScaleControl({anchor: BMAP_ANCHOR_BOTTOM_LEFT});
            map.addControl(ctrl_sca);
        },

        //创建marker
        addMarker : function (option) {
            var myThis = this;
            var map = this.map;
            var point = new BMap.Point(option.lng, option.lat);
            var marker = new BMap.Marker(point);
            var label = new BMap.Label(option.label, {"offset": new BMap.Size(16, -24)});

            if (typeof(option.animate) != 'undefined')
                marker.setAnimation(BMAP_ANIMATION_BOUNCE);
            map.addOverlay(marker);
            if (typeof(option.label) != 'undefined') {
                marker.setLabel(label);
                label.setStyle({
                    padding: '5px',
                    borderColor: "#808080",
                    color: "#555",
                    cursor: "pointer",
                    opacity: 0.8,
                    borderRadius: '3px',
                    boxShadow: '1px 1px 3px #999'
                });
            }

        },
//创建InfoWindow
    createInfoWindow: function (option) {
        var iw = new BMap.InfoWindow("<b class='iw_poi_title' title='" + option.title + "'>" + option.title + "</b><div class='iw_poi_content'>" + option.content + "</div>");
        return iw;
    },
    showInfoWindow: function(pos, iw) {
        var point = new BMap.Point(pos.lng, pos.lat);
        this.map.openInfoWindow(iw, point);
    },
//创建一个Icon
    createIcon : function () {
        var icon = new BMap.Icon("http://app.baidu.com/map/images/us_mk_icon.png", new BMap.Size(320, 120), {
            imageOffset: new BMap.Size(0, 0),
            infoWindowOffset: new BMap.Size(5, 1),
            offset: new BMap.Size(0, 16)
        })
        return icon;
    },
    clearOverlays: function() {
        this.map.clearOverlays();
    }
}

