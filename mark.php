<!DOCTYPE html>
<html>
  <head>
    <style>
       /* Set the size of the div element that contains the map */
      #map {
        height: 400px;  /* The height is 400 pixels */
        width: 100%;  /* The width is the width of the web page */
       }
    </style>
  </head>
  <body>
    <h3>My Google Maps Demo</h3>
    <!--The div element for the map -->
    <div id="map"></div>
    <script>
// Initialize and add the map
function initMap() {
  // The location of Uluru
  var uluru = {lat: -37.7276, lng: 144.9066};
  // The map, centered at Uluru
  var map = new google.maps.Map(
      document.getElementById('map'), {zoom: 7, center: uluru, mapTypeId:'terrain'});
  // The marker, positioned at Uluru
  var marker = new google.maps.Marker({position: uluru, icon:getCircle(12), map: map,title: '[Essendon Airport]'});
  //信息窗口
        var contentString = '<div id="content">'+
            '<div id="siteNotice">'+
            '</div>'+
            '<h1 id="firstHeading" class="firstHeading">[Essendon Airport]</h1>'+
            '<div id="bodyContent">'
            +
            'Location:'+'[-25.363],[131.044]<br>'
            +
            'Total observed rainfall amount:[53.8]<br>'
            +
            'Total observed days:[31(100.00%)]<br>'
            +
            'Average per day:[1.736]<br>'
            +'</div>'+
            '</div>';

        var infowindow = new google.maps.InfoWindow({
          content: contentString
        });
        marker.addListener('click', function() {
          infowindow.open(map, marker);
        });
}

 function getCircle(magnitude) {
        return {
          path: google.maps.SymbolPath.CIRCLE,
          fillColor: 'blue',
          fillOpacity: 0.8,//用来设置填充颜色透明度（范围：0 - 1） 
          scale: 6+magnitude>12?12:5+magnitude,
          strokeColor: 'white',
          strokeWeight:.5
        };
      }

       
    </script>
    <!--Load the API from the specified URL
    * The async attribute allows the browser to render the page while the API loads
    * The key parameter will contain your own API key (which is not needed for this tutorial)
    * The callback parameter executes the initMap() function
    -->
    <script async defer
    src="http://maps.google.cn/maps/api/js?v=3.20&region=cn&language=zh-CN&key=AIzaSyBAJqbpqO_x9RYrprUVQU8HOdj59T0tSSs&callback=initMap">
    </script>
  </body>
</html>