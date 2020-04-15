
---
title: "Quick TeamCity Build Status with AngularJS"

date: "2016-05-16T20:07:11"

featured_image: "http://logicalgenetics.com/wp-content/uploads/2016/05/BuildStatus.png"
---


So, this isn't supposed to be the ultimate guide to AngularJS or anything like that - I'm not even using the latest version - this is just some notes on my return to *The World of the View Model* after a couple of years away from WPF. Yeah, that's right, I just said WPF while talking about Javascript development. They may be different technologies from different eras: one may be the last hurrah of bloated fat-client development and the other may be the latest and greatest addition to the achingly-cool, tie dyed hemp tool belt of the Single Page App hipster, but under the hood they're very very similar. Put that in your e-pipe and vape it, designer-bearded UX developers!

<a href="http://logicalgenetics.com/quick-teamcity-build-status-with-angularjs/buildstatus/"><img src="/images/quick-teamcity-build-status-with-angularjs/BuildStatus.png"/></a>

Anyway, when I started, I knew *nothing* about SPA development. I'd last done JavaScript several years ago and never really used it as a *real* language. I still contend that JavaScript isn't a real language (give me Scala or C# any day of the week) but you can't ignore the fact that this is how user interfaces are developed these days... so, yeah, I started with a <a href="https://www.youtube.com/watch?v=i9MHigUZKEM">tutorial on YouTube</a>.

I decided to do an <a href="http://logicalgenetics.com/dan-dan-the-kanban-man/">old radiators</a> are coded in WPF, which looks awesome on the big TVs dotted around the office, but doesn't translate well for remote workers.

There is no sunshine and there are no rainbows in this article. I found javascript to be a hateful language, filled with boilerplate and confusion. Likewise, though TeamCity is doubtless the best enterprise CI platform on planet earth, the REST APIs are pretty painful to consume. With that in mind, let's get into the weeds and see how this thing works...

## Enable cross-site scripting (CORS) on your Team City server

You can't hit a server from a web page unless that server is the server that served the web page you're hitting the server with... unless of course you tell the server you want to hit that the web page you want to hit it with, served from a different server, is allowed to hit it. Got that? Thought so. This is all because of a really logical thing called "Cross Origin Resource Sharing", which you can enable pretty easily in TeamCity as long as you have admin permissions.

Check out **Administration -> Server Administration -> Diagnostics -> Internal Properties**.  From there you should be able to edit, or at least get the location of the **internal.properties** file.  Weirdly, if the file doesn't exist, there is no option to edit, so you have to go and create the file.  Since my TeamCity server is running on a Windows box, I created the new file here: 

```C:\ProgramData\JetBrains\TeamCity\config\internal.properties ```

and added the following:

```rest.cors.origins=*```

You might want to be a little more selective on who you allow to access the server this way - I guess it depends on how secure your network is, how many clients access the dashboard and so on.

## Tool Chain

This article is about AngularJS and it's about TeamCity. It's not about NPM or Bower or any of that nonsense. I'm not going to minify my code or use to crazy new-fangled pseudo-cosmic CSS. So setting up the build environment for me was pretty easy: create a folder, add a file called "index.html", fire up the fantastic <a href="http://fenixwebserver.com/">Fenix Web Server</a> and configure it to serve up the folder we just created. Awesome.

If you're already confused, or if you just want to play with the code, you can download the lot from GitHib:  <a href="https://github.com/DanteLore/teamcity-status-with-angular">https://github.com/DanteLore/teamcity-status-with-angular</a>

## I promise to do my best

Hopefully you've watched the video I linked above, so you know the basics of an AngularJS app. If not, do so now. Then maybe Google around the subject of promises and http requests in AngularJS. Done that? OK, good. 

Web requests take a while to run. In a normal app you might fetch them on another thread but not in JavaScript. JavaScript is all about callbacks. A Promise is basically a callback that promises to get called some time in the future. They are actually pretty cool, and they form the *spinal column* of the build status app. This is because the TeamCity API is so annoying. Let me explain why. In order to find out the status (OK or broken) and state (running, finished) of each build configuration you need to make roughly six trillion HTTP requests as follows:

<ol>
	<li>Fetch a list of the build configurations in the system. These are called "Build Types" in the API and have properties like "name", "project" and "id"</li>
	<li>For each Build Type, make a REST request to get information on the latest running Build with a matching type ID. This will give you the "name", "id" and "status" of the last *finished *build for the given Build Type.</li>
	<li>Fetch a list of the *currently running* builds.</li>
	<li>Use the list of finished builds and the list of running builds to create a set of status tiles (more on this later)</li>
	<li>Add the tiles to the angular $scope and let the UI render them</li>
</ol>

Here's how that looks in code. Hopefully not too much more complicated than above!
```javascript
buildFactory.getBuilds()
	.then(function(responses) {
		$scope.buildResponses = responses
			.filter(function(r) { return (r.status == 200 &amp;&amp; r.data.build.length > 0)})
			.map(function(r){ return r.data.build[0] })
	})
	.then(buildFactory.getRunningBuilds)
	.then(function(data) {
		$scope.runningBuilds = data.data.build.map(function(row) { return row.buildTypeId })
	})
	.then(function() {
		$scope.builds = $scope.buildResponses.map(function(b) { return buildFactory.decodeBuild(b, $scope.runningBuilds); });
	})
	.then(function() {
		$scope.tiles = buildFactory.generateTiles($scope.builds)
	})
	.then(function() {
		$scope.statusVisible = false;
	});

```
Most of the REST access has been squirrelled away into a factory. And yes, our build server *is *called "tc" and guest access *is *allowed to the REST APIs and I *have *enabled CORS too... because sometimes productivity is more important than security!
```javascript
angular.module('buildApp').factory('buildFactory', function($http) {
	var factory = {};
	  
	var getBuildTypes = function() {
		return $http.get('http://tc/guestAuth/app/rest/buildTypes?locator=start:0,count:100');
	};
	
	var getBuildStatus = function(id) {
		return $http.get('http://tc/guestAuth/app/rest/builds?locator=buildType:' + id + ',start:0,count:1&amp;fields=build(id,status,state,buildType(name,id,projectName))');
	};
	
	factory.getRunningBuilds = function() {
		return $http.get('http://tc/guestAuth/app/rest/builds?locator=running:true');
	};

// etc

```
## Grouping and Tiles

We have over 100 builds. Good teams have lots of builds. Not too many, just lots. Every product (basically every team) has CI builds, release/packaging builds, continuous deployment builds, continuous test builds, metrics builds... we have a lot of builds. Builds are good.

But a screen with 100+ builds on it means very little. This is an information radiator, not a formal report. So, I use a simple (but messy) algorithm to convert a big list of Builds into a smaller list of Tiles:

<ol>
	<li>Take the broken builds (hopefully not many) and turn each one into a Tile</li>
	<li>Take the successful builds and group them by "project" (basically the category, which is basically the team or product name)</li>
	<li>Turn each group of successful builds into a Tile, using the "project" as the tile name</li>
	<li>Mark any "running" build with a flag so we can give feedback in the UI</li>
</ol>

<a href="http://logicalgenetics.com/quick-teamcity-build-status-with-angularjs/buildstatus2/"><img src="/images/quick-teamcity-build-status-with-angularjs/BuildStatus2.png"/></a>

## Displaying It

Not much very exciting here. I used <a href="https://bootswatch.com/cyborg/">derivative of Bootstrap</a> to make the UI look nice. I bound some content to the View Model and that's about it.  Download the code and have a look if you like.

Here's my index.html (which shows all the libraries I used):
```html
<html ng-app="buildApp">
<head>
  <title>Build Status</title>
  
  <link href="https://bootswatch.com/cyborg/bootstrap.min.css" rel="stylesheet">
  <!--link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet"-->
</head>

<body>
  
  

  <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.5/angular.min.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.5/angular-route.js"></script>
  <script src="https://code.jquery.com/jquery-2.2.3.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
  <script src="utils.js"></script>
  <script src="app.js"></script>
  <script src="build-factory.js"></script>
</body>
</html>

```
Here's the "view" HTML for the list (in "templates/list.html").  I love the Angular way of specifying Views and Controllers by the way. Note the cool animated CSS for the "in progress" icon.
```html

  <style>
	.glyphicon-refresh-animate {
		-animation: spin 1s infinite linear;
		-webkit-animation: spin2 1s infinite linear;
	}

	@-webkit-keyframes spin2 {
		from { -webkit-transform: rotate(0deg);}
		to { -webkit-transform: rotate(360deg);}
	}

	@keyframes spin {
		from { transform: scale(1) rotate(0deg);}
		to { transform: scale(1) rotate(360deg);}
	}
  </style>
  
	
		# Build Status <small>from TeamCity</small>
	
	  
    
		
    		
        		
               <h5><span ng-class="getGlyphClass(tile)" aria-hidden="true"></span>&amp;nbsp;&amp;nbsp;&amp;nbsp;{{ tile.name | limitTo:32 }}{{tile.name.length > 32 ? '...' : ''}} &amp;nbsp; {{ tile.buildCount > 0 ? '(' + tile.buildCount + ')' : ''}} </h5>
               {{ tile.project }}
              
        	
    	
    
	


  
  <nav class="navbar navbar-default navbar-fixed-bottom">
  
    
		<input type="text" ng-model="nameFilter"/>&amp;nbsp;&amp;nbsp;<span class="glyphicon glyphicon-filter" aria-hidden="true"></span>&amp;nbsp;&amp;nbsp;
		<span class="glyphicon glyphicon-refresh glyphicon-refresh-animate" ng-hide="!statusVisible"></span>
	
  
</nav>
```
## That's about it!

I think I summarized how I feel about this project in the introduction. It looks cool and the MVC MVVM ViewModel vibe is a good one. The data binding is simple and works very well. All my gripes are with JavaScript as a language really. I want Linq-style methods and I want classes and objects with sensible scope. I want less syntactic nonsense, maybe the odd => every now and again. I think some or all of that is possible with libraries and new language specs... but I want it without any effort!

One thing I will say: that whole page is less than 300 lines of code. That's pretty darned cool.

<a href="https://github.com/DanteLore/teamcity-status-with-angular">Feel free to download and use the app however you like</a> - just bung in a link to this page!

<a href="http://logicalgenetics.com/quick-teamcity-build-status-with-angularjs/buildstatus/"><img src="/images/quick-teamcity-build-status-with-angularjs/BuildStatus.png"/></a>