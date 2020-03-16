
---
title: "Mapserver Revisited"
date: 2012-11-08T09:16:38
draft: False
---

Years ago, before they invented Google Earth and Bing Maps and all that, I did some work to show GPS data using Mapserver.  At work yesterday I was forced to revisit it while maintaining an aged and creaking part of our product.  It took a while to get my head back into the right state but once I'd got going I started to enjoy it again.  This time around, because I'm at work, I'm using <a href="http://spatialhorizons.com/2007/05/19/mapserver-1-10-minute-tutorial/">found here</a>.

<a href="http://logicalgenetics.com/wp-content/uploads/2012/11/mapserv-web1.jpg"><img src="http://logicalgenetics.com/wp-content/uploads/2012/11/mapserv-web1.jpg"/></a>

When I last played with Mapserver there was very little decent data.  I had to settle for a very low resolution map of the world (vmap0) and some pretty poor raster data.  Yesterday, after a little googling, I found that OS have released <a href="http://mapserver.org/tutorial/example1-1.html">reasonable online tutorials</a> too.  I spent a little time getting this to render and the results are shown here in the scaled image above and full-size image below.

I decided to go with a black background as I was thinking of a Raspberry Pi based "GPS" system to show The Duke's location on a small screen.

<a href="http://logicalgenetics.com/wp-content/uploads/2012/11/mapserv-web2.png"><img src="http://logicalgenetics.com/wp-content/uploads/2012/11/mapserv-web2.png"/></a>

Note that the image is a little jagged looking.  Turns out you can fix this by adding the following snippet into your map file (under the MAP element).  Of course, you pay a high price in processing time and image size.

[sourcecode]
OUTPUTFORMAT
NAME png
DRIVER "AGG/PNG"
MIMETYPE "image/png"
		# Change to IMAGEMODE RGBA for transparent background
IMAGEMODE RGB
EXTENSION "png"
FORMATOPTION "INTERLACE=OFF"
END
[/sourcecode]

<a href="http://logicalgenetics.com/wp-content/uploads/2012/11/mapserv-web3.jpg"><img src="http://logicalgenetics.com/wp-content/uploads/2012/11/mapserv-web3.jpg"/></a>

The benefit of Mapserver over Google or Bing maps is that it works on local data with no requirement for an internet connection. This means it's more reliable for a GPS sort of system. Of course, the downside is that there are fewer layers available and you have to do a lot of coding. Was good to play with, though, and I hope to get it working on the raspberry soon.

Here's my (large) Map file:

[sourcecode]
MAP
	IMAGETYPE      PNG
	#Whole UK would be...
	#EXTENT         0 0 660000 1230000
	# SU Grid is...
	#EXTENT		400000 100000 500000 200000
	# The top right of SU is...
	EXTENT		450000 150000 500000 200000

	FONTSET "C:\Users\Dan.Taylor\Desktop\Mapfiles\Fonts.txt"

	SIZE           6400 6400
	IMAGECOLOR     0 0 0
	CONFIG "MS_ERRORFILE" "C:\Users\Dan.Taylor\Desktop\Mapfiles\Errors.txt"
	CONFIG "CPL_DEBUG" "ON"
	CONFIG "PROJ_DEBUG" "ON"
	DEBUG 5

	SYMBOL
		NAME "triangle"
		TYPE vector
		POINTS
			0 1
			0.5 0
			1 1
			0 1
		END
		FILLED TRUE
    END

	LAYER
		NAME         Woodland
		DATA         "C:\Users\Dan.Taylor\Desktop\SU\SU_Woodland"
		STATUS       default
		TYPE         POLYGON

		CLASS
			STYLE
				COLOR 20 40 20
			END
		END
	END

	LAYER
		NAME         SurfaceWater
		DATA         "C:\Users\Dan.Taylor\Desktop\SU\SU_SurfaceWater_Area"
		STATUS       default
		TYPE         POLYGON

		CLASS
			STYLE
				COLOR 80 80 128
			END
		END
	END

	LAYER
		NAME         TidalWater
		DATA         "C:\Users\Dan.Taylor\Desktop\SU\SU_TidalWater"
		STATUS       default
		TYPE         POLYGON

		CLASS
			STYLE
				COLOR 60 60 100
			END
		END
	END

	LAYER
		NAME         Buildings
		DATA         "C:\Users\Dan.Taylor\Desktop\SU\SU_Building"
		STATUS       default
		TYPE         POLYGON

		CLASS
			STYLE
				COLOR 80 60 80
			END
		END
	END

	LAYER
		NAME         Foreshore
		DATA         "C:\Users\Dan.Taylor\Desktop\SU\SU_Foreshore"
		STATUS       default
		TYPE         POLYGON

		CLASS
			STYLE
				COLOR 80 80 128
			END
		END
	END

	LAYER
		NAME         Roads
		DATA         "C:\Users\Dan.Taylor\Desktop\SU\SU_Road"
		STATUS       default
		TYPE         LINE

		CLASSITEM	"CLASSIFICA"

		CLASS
			EXPRESSION	 "Motorway"
			STYLE
				COLOR 20 128 128
				WIDTH 8
			END
			STYLE
				COLOR 200 200 255
				WIDTH 2
			END
		END
		CLASS
			EXPRESSION	 "Primary Road"
			STYLE
				COLOR 20 128 20
				WIDTH 6
			END
			STYLE
				COLOR 200 200 0
				WIDTH 2
			END
		END
		CLASS
			EXPRESSION	 "A Road"
			STYLE
				COLOR 20 128 20
				WIDTH 6
			END
		END
		CLASS
			EXPRESSION	 "B Road"
			STYLE
				COLOR 128 20 20
				WIDTH 4
			END
		END
		CLASS
			EXPRESSION	"Private Road, Publicly Accessible"
			STYLE
				COLOR 128 128 128
				WIDTH 2
			END
		END
		CLASS
			EXPRESSION	 "Local Street"
			STYLE
				COLOR 128 128 128
				WIDTH 2
			END
		END
		CLASS
			EXPRESSION	"Pedestrianised Street"
			STYLE
				COLOR 128 128 128
				WIDTH 2
			END
		END
		CLASS
			EXPRESSION	 "Minor Road"
			STYLE
				COLOR 128 128 128
				WIDTH 2
			END
		END
	END

	SYMBOL
		NAME "station"
		TYPE ellipse
		FILLED true
		POINTS
			1 1
		END
	END

	LAYER
		NAME         RailwayStations
		DATA         "C:\Users\Dan.Taylor\Desktop\SU\SU_RailwayStation"
		STATUS       default
		TYPE         POINT
		LABELITEM    "NAME"

		CLASS
			LABEL
				TYPE truetype
				ANTIALIAS true
				FONT arial
				SIZE 10
				POSITION cr
				COLOR 255 50 50
				OUTLINECOLOR 100 0 0
				BUFFER 30
				PARTIALS false
				FORCE true
			END

			STYLE
				SYMBOL station
				COLOR 255 50 50
				OUTLINECOLOR 100 0 0
				SIZE 20
			END
		END
	END

	LAYER
		NAME         Airports
		DATA         "C:\Users\Dan.Taylor\Desktop\SU\SU_Airport"
		STATUS       default
		TYPE         POINT
		LABELITEM    "NAME"

		CLASS
			LABEL
				TYPE truetype
				ANTIALIAS true
				FONT arial
				SIZE 10
				POSITION cr
				COLOR 255 180 50
				OUTLINECOLOR 100 50 0
				BUFFER 30
				PARTIALS false
				FORCE true
			END

			STYLE
				SYMBOL "triangle"
				COLOR 255 180 50
				OUTLINECOLOR 100 50 0
				SIZE 20
			END
		END
	END

	LAYER
		NAME         NamedPlaces
		DATA         "C:\Users\Dan.Taylor\Desktop\SU\SU_NamedPlace"
		STATUS       off
		TYPE         POINT
		LABELITEM    "NAME"

		CLASS
			LABEL
				TYPE truetype
				ANTIALIAS true
				FONT arial
				SIZE 10
				POSITION cc
				COLOR 255 50 50
				OUTLINECOLOR 100 0 0
				BUFFER 30
				PARTIALS false
				FORCE true
			END

			STYLE
				SYMBOL station
				COLOR 255 50 50
				OUTLINECOLOR 100 0 0
			END
		END
	END

	LAYER
		NAME         MotorwayJunctions
		DATA         "C:\Users\Dan.Taylor\Desktop\SU\SU_MotorwayJunction"
		STATUS       default
		TYPE         POINT
		LABELITEM    "JUNCTIONNU"

		CLASS
			LABEL
				TYPE truetype
				ANTIALIAS true
				FONT arial
				SIZE 14
				POSITION cc
				COLOR 200 200 255
				OUTLINECOLOR 200 200 255
				BUFFER 30
				PARTIALS false
				FORCE true
			END

			STYLE
				SYMBOL station
				COLOR 20 128 128
				OUTLINECOLOR 200 200 255
				SIZE 30
			END
		END
	END
END
[/sourcecode]