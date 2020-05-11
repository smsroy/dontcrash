var startAddr;
var endAddr;
var directionsService;
var directionsRenderer;
var map;
var geocoder;
var markers = [];

// Initializes map display
function initMap() {
  var chicago = new google.maps.LatLng( 41.8781, -87.6298); 
  map = new google.maps.Map(document.getElementById('map'), {
    mapTypeControl: false,
    center: chicago,
    zoom: 8
  });
  geocoder = new google.maps.Geocoder();

  new AutocompleteDirectionsHandler(map);
}

/**
 * @constructor
 */
function AutocompleteDirectionsHandler(map) {
  this.map = map;
  this.originPlaceId = null;
  this.destinationPlaceId = null;
  this.travelMode = 'DRIVING';
  this.directionsService = new google.maps.DirectionsService;
  this.directionsRenderer = new google.maps.DirectionsRenderer;
  this.directionsRenderer.setMap(map);

  var originInput = document.getElementById('origin-input');
  var destinationInput = document.getElementById('destination-input');
  var modeSelector = document.getElementById('mode-selector');

  var originAutocomplete = new google.maps.places.Autocomplete(originInput);
  // Specify just the place data fields that you need.
  originAutocomplete.setFields(['place_id']);

  var destinationAutocomplete =
      new google.maps.places.Autocomplete(destinationInput);
  // Specify just the place data fields that you need.
  destinationAutocomplete.setFields(['place_id']);

  this.setupClickListener('changemode-walking', 'WALKING');
  this.setupClickListener('changemode-driving', 'DRIVING');

  this.setupPlaceChangedListener(originAutocomplete, 'ORIG');
  this.setupPlaceChangedListener(destinationAutocomplete, 'DEST');

  this.map.controls[google.maps.ControlPosition.TOP_LEFT].push(originInput);
  this.map.controls[google.maps.ControlPosition.TOP_LEFT].push(
      destinationInput);
  this.map.controls[google.maps.ControlPosition.TOP_LEFT].push(modeSelector);
}

// Sets a listener on a radio button to change the filter type on Places
// Autocomplete.
AutocompleteDirectionsHandler.prototype.setupClickListener = function(
    id, mode) {
  var radioButton = document.getElementById(id);
  var me = this;

  radioButton.addEventListener('click', function() {
    me.travelMode = mode;
    me.route();
  });
};

AutocompleteDirectionsHandler.prototype.setupPlaceChangedListener = function(
    autocomplete, mode) {
  var me = this;
  autocomplete.bindTo('bounds', this.map);

  autocomplete.addListener('place_changed', function() {
    var place = autocomplete.getPlace();

    if (!place.place_id) {
      window.alert('Please select an option from the dropdown list.');
      return;
    }
    if (mode === 'ORIG') {
      me.originPlaceId = place.place_id;
    } else {
      me.destinationPlaceId = place.place_id;
    }
    me.route();
  });
};

AutocompleteDirectionsHandler.prototype.route = function() {
  if (!this.originPlaceId || !this.destinationPlaceId) {
    return;
  }
  var me = this;

  this.directionsService.route(
      {
        origin: {'placeId': this.originPlaceId},
        destination: {'placeId': this.destinationPlaceId},
        travelMode: this.travelMode
      },
      function(response, status) {
        if (status === 'OK') {
          me.directionsRenderer.setDirections(response);
          getNearbyAccidents(response);
        } else {
          window.alert('Directions request failed due to ' + status);
        }
      });
};

// Determines which lats, longs, street names to use when searching the database for accidents near the route shown
function getNearbyAccidents(response){
    console.log(JSON.stringify(response.routes[0].legs[0].steps));
    var steps = response.routes[0].legs[0].steps;

    var i;
    var j;
    var startLats = [];
    var startLongs = [];
    var endLats = [];
    var endLongs = [];
    var streetNames = [];
    
    for(i = 0; i < Object.keys(steps).length; i++){
        var instructions; 
        var streetName;
        
        try{
            var instructions = JSON.stringify(response.routes[0].legs[0].steps[i].instructions);
            if("Merge" == instructions.substring(1,6) || "Take" == instructions.substring(1,5) || "Continue" == instructions.substring(1,8)){
                streetName = instructions.split('<b>')[1].split('</b>')[0].split('<div>')[0].split(' ');
            }
            else{
                streetName = instructions.split('<b>')[2].split('</b>')[0].split('<div>')[0].split(' ');
            }
            streetName = getStreetSpec(streetName);
        }
        catch(err){
            streetName = "";
        }
        
        streetName = streetName.toUpperCase();
        
        
        console.log(instructions);
        console.log(streetName);
        
        try{
            var pathLength = Object.keys(response.routes[0].legs[0].steps[i].path).length;
            var latStart = parseFloat(parseFloat(JSON.stringify(response.routes[0].legs[0].steps[i].path[0].lat())).toFixed(3));
            var longStart = parseFloat(parseFloat(JSON.stringify(response.routes[0].legs[0].steps[i].path[0].lng())).toFixed(3));
            var latEnd = parseFloat(parseFloat(JSON.stringify(response.routes[0].legs[0].steps[i].path[pathLength - 1].lat())).toFixed(3));
            var longEnd = parseFloat(parseFloat(JSON.stringify(response.routes[0].legs[0].steps[i].path[pathLength - 1].lng())).toFixed(3));

            //based on which lat & long val is larger for the range, put it as start or end lat
            if(latStart >= latEnd){
                startLats.push(latStart-.003);
                endLats.push(latEnd+.003);
            }
            else if(latEnd > latStart){
                startLats.push(latEnd-.003);
                endLats.push(latStart+.003);
            }
        
            if(longStart >= longEnd){
                startLongs.push(longStart-.003);
                endLongs.push(longEnd+.003);
            }
            else if(longEnd > longStart){
                    startLongs.push(longEnd-.003);
                    endLongs.push(longStart+.003);
                }
                streetNames.push(streetName);
            }
            catch(err){
                console.log("Invalid path from google api");
            }
    }
    console.log("Steps arr length: "+Object.keys(steps).length);
    console.log("StreetNames: "+streetNames+"\n");
    console.log("START_LATS: "+startLats+"\nSTART_LONGS: "+startLongs+"\nEND_LATS: "+endLats+"\nEND_LONGS: "+endLongs);
    getFromDB(startLats,startLongs,endLats,endLongs,streetNames);
}

// Gets the streetname from google map direction query
function getStreetSpec(street){
    var streetName;
    if(street[0].length == 1){
        streetName = street[1] 
        if(street.length > 2){
            streetName = streetName +" "+ street[2];
        }
    }
    else{
        streetName = street[0];
        if(street.length > 1){
            streetName = streetName +" "+ street[1];
        }
    }
    return streetName;
}

// Gets the accidents near the route shown        
function getFromDB(startLats,startLongs,endLats,endLongs,streetNames){
     jQuery.ajax({
                type: "POST",
                url: '/routefinderassets/route_getaccidentsinfo.php',
                dataType: 'json',
                data: {
                    start_lats: startLats,
                    start_longs: startLongs,
                    end_lats: endLats,
                    end_longs: endLongs,
                    streets: streetNames
                },
                success: function (obj) {
                      console.log(JSON.stringify(obj));
                      setMarkers(obj,startLats,startLongs,endLats,endLongs);
                }
            });
}

// Markers displayed on map that are clickable and provide info on number of accidents near the location   
function setMarkers(JSONObj,startLats,startLongs,endLats,endLongs){
    // Clear out the old markers.
    markers.forEach(function(marker) {
        marker.setMap(null);
    });   
    infoWindow = new google.maps.InfoWindow(); 
    
    console.log("Length of markers arr: "+Object.keys(JSONObj).length);
    var i;
    var j;
    lats = [];
    longs = [];

    for(i = 0; i < Object.keys(JSONObj).length; i++){
            if(Object.keys(JSONObj[i]).length == 0){
                continue;
            }
            
            var low = 0.0;
            var medium = 0.0;
            var critical = 0.0;
            for(j = 0; j < Object.keys(JSONObj[i]).length; j++){
                severity = JSONObj[i][j].severity;
                switch(severity) {
                    case "LOW":
                        low++;
                        break;
                    case "MEDIUM":
                        medium++;
                        break;
                    case "CRITICAL":
                        critical++;
                        break;
                }
                //console.log(JSON.stringify(JSONObj[i][j].severity));
            } 
            
            //ratio of accidents severity from total number of accidents nearby
            var total = low+medium+critical;
            var lowRatio = Math.round(low/total * 100);
            var mediumRatio = Math.round(medium/total * 100);
            var criticalRatio = Math.round(critical/total * 100);
            
            var contentString = "<h4>Nearby Accident Severity Ratio</h4><p><b>LOW:</b> "+lowRatio.toString()+"%</br><b>MEDIUM:</b> "+mediumRatio.toString()+"%</br><b>CRITICAL:</b> "+criticalRatio.toString()+"%</p>";
             
            //get middle point between start and end lats & longs
            var lat = (startLats[i] - endLats[i])/2 + endLats[i];
            var long = (startLongs[i] - endLongs[i])/2 + endLongs[i];
            var label = (Object.keys(JSONObj[i]).length).toString();
            
            // Arrays for predicting accident type in machine learning log reg model
            lats.push(lat);
            longs.push(long);  
           
            var markerTemp = new google.maps.Marker({position: {lat: lat, lng: long},
                                        map: map,
                                        label: {text: label, color: '#FFFFFF'},
                                        icon: {
                                            path: google.maps.SymbolPath.CIRCLE,
                                            scale: 22,
                                            fillColor: "#EB4335",
                                            fillOpacity: 1,
                                            strokeWeight: 0.6,
                                            strokeColor: '#FFFFFF'
                                        }
                                        });

            markerTemp.contentString = contentString;

            google.maps.event.addListener(markerTemp, 'click', function() {
                infoWindow.close();
                infoWindow = new google.maps.InfoWindow();
                infoWindow.setContent(this.contentString);
                infoWindow.open(map, this);
            });

            markers.push(markerTemp);
    }
    
    getLogReg(lats,longs);
    console.log("Lats and Longs: "+lats.toString());
   
} 

// Get the logistic regression model prediction for the lat and long values near the pathway given by google maps
function getLogReg(latsArr,longsArr){
    jQuery.ajax({
        type: "POST",
        url: "https://dontcrash.web.illinois.edu/route/map",
        contentType: "application/json; charset=utf-8",
        dataType: 'json',
        data: JSON.stringify({ 
           latlist: latsArr,
           longlist: longsArr
        }),
        success: function (obj) {
            var jsonAccidentTypes = obj
            console.log("ML Accident Type Predictions: "+JSON.stringify(jsonAccidentTypes));
            modMarkerPrediction(jsonAccidentTypes.result);
            
        },
        error: function (jqXHR, exception) {
            var msg = '';
            if (jqXHR.status === 0) {
                msg = 'Not connect.\n Verify Network.';
            } else if (jqXHR.status == 404) {
                msg = 'Requested page not found. [404]';
            } else if (jqXHR.status == 500) {
                msg = 'Internal Server Error [500].';
            } else if (exception === 'parsererror') {
                msg = 'Requested JSON parse failed.';
            } else if (exception === 'timeout') {
                msg = 'Time out error.';
            } else if (exception === 'abort') {
                msg = 'Ajax request aborted.';
            } else {
                msg = 'Uncaught Error.\n' + jqXHR.responseText;
            }
            console.log(msg);
        }
    });
}

// Add machine learning logistic regression accident type prediction to each marker (each is clickable)
function modMarkerPrediction(predictions){
    count = 0;
    infoWindow = new google.maps.InfoWindow(); 
    markers.forEach(function(marker) {
        var newContentString = '<h4>Most Probable Accident Type By Severity</h4><p><b>LOW: </b>'+predictions[count++]+'</br><b>MEDIUM: </b>'+predictions[count++]+'</br><b>CRITICAL: </b>'+predictions[count++]+'</p>'+marker.contentString
        
        marker.contentString = newContentString;
        
         google.maps.event.addListener(marker, 'click', function() {
                infoWindow.close();
                infoWindow = new google.maps.InfoWindow();
                infoWindow.setContent(this.contentString);
                infoWindow.open(map, this);
            });
    });
    
    
   
}
