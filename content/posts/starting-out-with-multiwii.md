
---
title: "Starting out with MultiWii"

date: "2012-12-05T12:19:38"

featured_image: "http://logicalgenetics.com/wp-content/uploads/2012/12/MultiWii1.png"
---


Last night I unpacked my new MultiWii controller and plugged it into my PC.  I <a href="http://www.hobbyking.com/hobbyking/store/__27033__MultiWii_328P_Flight_Controller_w_FTDI_DSM2_Port.html">bought it from Hobby King</a> and it comes ready to fly out of the box - configured for an X-quad, which is perfect for my carbon H-copter.

The Multi-Wii board attracted me because it comes with so many sensors out of the box.  Barometric pressure, compass, gyros and accelerometers as well as the option to add GPS in future.  That's compared to *just gyros* on the KK board.  I love the KK board to bits - it's a great board for line-of sight flying, allowing a fair amount of acrobatics and some nimble and fast flight.  It's scary flying FPV with the KK though.  I managed a good FPV flight at the weekend (see video later) but it would be great to push more of the stability control onto the 'copter for less stressful remote piloting.

I didn't get the board hooked up to the quad or the receiver last night, just plugged it into the PC and fired up the Java tool.<a href="http://logicalgenetics.com/wp-content/uploads/2012/12/MultiWii1.png"><img src="http://logicalgenetics.com/wp-content/uploads/2012/12/MultiWii1.png"/></a>

I have to say I was very impressed with the board and the UI tool.  The tool shows a live trace from all of the on-board sensors and a 3D model of the 'copter which moves in real-time.  Every single setting is configurable, including the PID terms, throttle travel, behaviour of the auxiliary "switch" channels and so on.

Hardware wise, the barometric pressure sensor was the star - it responds to changes in height of about 10cm, which is spookily accurate!  The only worry I have is the effect of wind on this sensor - I am wondering whether a wind shield is going to be needed for breezy days.

Down sides so far:  The GUI is a bit painful to use.  About 25% of mouse clicks are ignored or lost (I think because the refresh loop for the graph is running and the app is polling for user input).  Also, the numeric values are editted using very very very small sliders.  It would be much simpler to just enter the text!  Maybe these things annoy me more because I do that sort of thing for a living...

Expect more soon!  In the mean time, here's an FPV video from this weekend...

[embedplusvideo height="465" width="584" standard="http://www.youtube.com/v/sKRGBYjTcPQ?fs=1&amp;hd=1" vars="ytid=sKRGBYjTcPQ&amp;width=584&amp;height=465&amp;start=&amp;stop=&amp;rs=w&amp;hd=1&amp;autoplay=0&amp;react=1&amp;chapters=&amp;notes=" id="ep9639" /]