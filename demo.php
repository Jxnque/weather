<!DOCTYPE html>
<html>
  <head>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no">
    <meta charset="utf-8">
    <title>Info Windows</title>
    <style>
      /* Always set the map height explicitly to define the size of the div
       * element that contains the map. */
      #map {
        height: 100%;
      }
      /* Optional: Makes the sample page fill the window. */
      html, body {
        height: 100%;
        margin: 0;
        padding: 0;
      }
    </style>
  </head>
  <body>
    <div id="map"></div>
    <script>

      // This example displays a marker at the center of Australia.
      // When the user clicks the marker, an info window opens.

      function initMap() {
        var uluru = {lat: -25.363, lng: 131.044};
        var map = new google.maps.Map(document.getElementById('map'), {
          zoom: 4,
          center: uluru
        });

        
      }
      //信息窗口
        var contentString = '<div id="content">'+
            '<div id="siteNotice">'+
            '</div>'+
            '<h1 id="firstHeading" class="firstHeading">Essendon Airport</h1>'+
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

        var marker = new google.maps.Marker({
          position: uluru,
          map: map,
          title: 'Uluru (Ayers Rock)'
        });
        marker.addListener('click', function() {
          infowindow.open(map, marker);
        });
    </script>
    <script async defer
    src="http://maps.google.cn/maps/api/js?v=3.20&region=cn&language=zh-CN&key=AIzaSyBAJqbpqO_x9RYrprUVQU8HOdj59T0tSSs&callback=initMap">
    </script>
  </body>
</html>

 