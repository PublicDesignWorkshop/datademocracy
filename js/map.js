var layer = new L.StamenTileLayer("terrain");
var map = new L.Map("map", {
    center: new L.LatLng(33.787423, -84.372597),
    zoom: 10
});
map.addLayer(layer);