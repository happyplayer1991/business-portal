jQuery(function ($) {
    /**
     * show google Map
     */
    var mapdata = [], mapjson;
    $('input[name="supplieraddress[]"]').each(function() {
        mapjson = {
            address: $(this).data('address'),
            latitude: $(this).data('latitude'),
            longitude: $(this).data('longitude')
        }
        mapdata.push(mapjson)
    });
    
    var map,
        infoWindow,
        id,
        name,
        address,
        point,
        infowincontent,
        strong,
        text,
        marker,
        i = 0;
    
    if(mapdata.length > 0) {
        map = new google.maps.Map(document.getElementById('map'), {
            center: new google.maps.LatLng(mapdata[0].latitude, mapdata[0].longitude),
            zoom: 12
        });
        infoWindow = new google.maps.InfoWindow;
        for(i=0; i<mapdata.length; i++) {    
            id = 1;
            name = "";
            address = mapdata[i].address;
            point = new google.maps.LatLng(
                parseFloat(mapdata[i].latitude),
                parseFloat(mapdata[i].longitude));

            infowincontent = document.createElement('div');
            strong = document.createElement('strong');
            strong.textContent = name;
            infowincontent.appendChild(strong);
            infowincontent.appendChild(document.createElement('br'));
            
            text = document.createElement('text');
            text.textContent = address;
            infowincontent.appendChild(text);
            marker = new google.maps.Marker({
                map: map,
                position: point,
                label: "A"
            });
            marker.addListener('click', function() {
                infoWindow.setContent(infowincontent);
                infoWindow.open(map, marker);
            });
        }
    }
});