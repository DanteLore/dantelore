
---
title: "Live Train Route Animation"

date: "2016-06-16T09:46:06"

featured_image: "/images/gear48.png"
---


The code for this article is available on my github, here: https://github.com/DanteLore/national-rail

Building on the <a href="http://logicalgenetics.com/train-departure-board/">Live Departures Board</a> project from the other day, I decided to try out mapping some departure data. The other article shows pretty much all the back-end code, which wasn't changed much.

<a href="http://logicalgenetics.com/live-train-route-animation/route-planner/"><img src="/images/live-train-route-animation/route-planner.gif"/></a>

The AngularJS app takes the routes of imminent departures from various stations and displays them on a <a href="http://leaflet-extras.github.io/leaflet-providers/preview/index.html">CartoDB</a>, which is free, unlike Mapbox.

<a href="http://logicalgenetics.com/live-train-route-animation/route-planner2/"><img src="/images/live-train-route-animation/route-planner2.gif"/></a>

Here's the code-behind for the Angular app:

```javascript
var mapApp = angular.module('mapApp', ['ngRoute']);

mapApp
    .config(function($routeProvider){
	    $routeProvider
		    .when('/',
		    {
		    	controller: 'MapController',
			    templateUrl: 'map.html'
		    })
		    .otherwise({redirectTo: '/'});
	})
	.controller('MapController', function($scope, $http, $timeout, $routeParams) {

        var mymap = L.map('mapid').fitBounds([ [51.3933180851, -1.24174419711], [51.5154681995, -0.174688620494] ]);
        L.tileLayer('http://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}.png', {
            attribution: '&amp;copy; <a href="http://cartodb.com/attributions">CartoDB</a>',
            subdomains: 'abcd',
            maxZoom: 19
        }).addTo(mymap);

        $scope.routeLayer = L.featureGroup().addTo(mymap);
        $scope.categoryScale = d3.scale.category10();

        $scope.doStation = function(data) {
            data.forEach(function(route){
                var color = $scope.categoryScale(route[0].crs)
                var path = []

                route.filter(function(x) {return x.latitude &amp;&amp; x.longitude}).forEach(function(station) {
                    var location = [station.latitude, station.longitude];
                    path.push(location);
                });

                var line = L.polyline(path, {
                    weight: 4,
                    color: color,
                    opacity: 0.5
                }).addTo($scope.routeLayer);

                line.snakeIn();
            });
        };

        $scope.refresh = function() {
            $scope.routeLayer.clearLayers();

            $scope.crsList.forEach(function(crs) {
                $http.get("/routes/" + crs).success($scope.doStation);
            });

            $timeout(function(){
                $scope.refresh();
            }, 10000)
        };

        $http.get("/loaded-crs").success(function(crsData) {
            $scope.crsList = crsData;

            $scope.refresh();
        })
    });

```
