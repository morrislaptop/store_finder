/* (c) 2007 Witold Rugowski http://nhw.pl

v0.1 - 2007-02-09 - Initial release, tested in FF, some quirks in IE
v0.2 - 2007-03-27 - Improved release - drag&drop, changed icon
v0.3 - 2007-05-15 - Fixed blocked drag&drop
*/
var marker;
var map;

var opt = {};
opt.draggable = true;
opt.clickable = false;
opt.dragCrossMove = true;

function load(point) {
	if (GBrowserIsCompatible()) {
		// init
		map = new GMap2(document.getElementById("map"));
		map.setCenter(point, 16);
		map.enableScrollWheelZoom();

		// add controls
		map.addControl(new GLargeMapControl())
		map.addControl(new GMapTypeControl())

		// add marker
		marker = new GMarker(point, opt);
		map.addOverlay(marker);

		// add dragging
		GEvent.addListener(marker, 'dragend', function() {
			setForm();
		});

		// add clicking
		GEvent.addListener(map, "click", function (overlay, point) {
			marker.setLatLng(point);
			setForm();
		});
	}
}

function setForm() {
	$("#StoreLat").val(marker.getLatLng().lat());
	$("#StoreLon").val(marker.getLatLng().lng());
}