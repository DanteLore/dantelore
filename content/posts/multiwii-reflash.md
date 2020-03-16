
---
title: "MultiWii Reflash"
date: 2012-12-06T22:55:30
draft: False
---

Got home from work and started working on this here MultiWii.  This time I finally built up the courage to flash the thing with some new software. [<img src="http://logicalgenetics.com/wp-content/uploads/2012/12/MultiWiiInProgress.jpg"/>](http://logicalgenetics.com/wp-content/uploads/2012/12/MultiWiiInProgress.jpg)

It took me ages to find any documentation on the HobbyKing MultiWii 328P 2.1 board. There are scores of different hardware versions of the Multi Wii controller, so google tends to point you in odd directions. Anyway, it turns out there's a ["Files" tab on the HobbyKing page](http://www.hobbyking.com/hobbyking/store/__27033__MultiWii_328P_Flight_Controller_w_FTDI_DSM2_Port.html) which has a load of useful stuff.

There's a [code snippet](http://www.hobbyking.com/hobbyking/store/uploads/471221464X331045X6.txt). These actually contradict each other quite seriously. I can only assume that HobbyKing have put quite a few versions of this board out and the settings are different. Here are the only changes I made...
<ul>
	<li><strong>TAKE THE PROPS OFF!</strong></li>
	<li>Download and install the [Arduino 1.0+ IDE](http://arduino.cc/en/Main/Software).</li>
	<li>Download the[ MultiWii software](http://code.google.com/p/multiwii/downloads/detail?name=MultiWii_2_1.zip&amp;can=2&amp;q=) from their website.</li>
	<li>Run the Arduino IDE. Open the file "<strong>\MultiWii_2_1\MultiWii_2_1.ino</strong>" this will cause all of the files in the folder to be opened up in tabs in the IDE.</li>
	<li>Add the following lines to <strong>config.h</strong> at the end of the *Combined IMU Boards* section. Leave everything else in this section commented out.</li>
</ul>
[sourcecode language="C"]#define HK_MultiWii_328P   // HobbyKing MultiWii[/sourcecode]
<ul>
	<li>Add this block to <strong>def.h</strong>. I added it around line 924, at the end of all the board specific configuration blocks.</li>
</ul>
[sourcecode language="C"]#if defined(HK_MultiWii_328P )
  #define I2C_SPEED 400000L
  #define ITG3200
  #define HMC5883
  #define BMA180
  #define BMP085
  #define ACC_ORIENTATION(X, Y, Z) {accADC[ROLL]  = -X; accADC[PITCH]  = -Y; accADC[YAW]  =  Z;}
  #define GYRO_ORIENTATION(X, Y, Z) {gyroADC[ROLL] =  Y; gyroADC[PITCH] = -X; gyroADC[YAW] = -Z;}
  #define MAG_ORIENTATION(X, Y, Z) {magADC[ROLL]  =  X; magADC[PITCH]  =  Y; magADC[YAW]  = Z;}
  #undef INTERNAL_I2C_PULLUPS
#endif[/sourcecode]
<blockquote>I can't guarantee this these settings will work. If your 'copter decapitates you as a result of using them it's not my fault!</blockquote>
<ul>
	<li>Choose the correct board type from the Arduino IDE menu (see screenshot)</li>
</ul>
[<img src="http://logicalgenetics.com/wp-content/uploads/2012/12/Arduino1.png"/>](http://logicalgenetics.com/wp-content/uploads/2012/12/Arduino1.png)
<ul>
	<li>Click "Verify" (the tick) on the Arduino IDE tool bar. There shouldn't be any errors in the message window. If there are, you may have mis-copied the code or chosen the wrong board type.</li>
	<li>Choose the correct Serial Port from the Arduino IDE menu (the port that isn't there when the cable is connected and is there when it is!)</li>
	<li>Click "Upload" on the toolbar. This will upload the code to the Multi Wii, which will then reboot. Once it has rebooted, test everything three times before putting the props back on!</li>
</ul>
I also calibrated the magnetometer properly (RTFM, Dan!) and tweaked the throttle minimum to stop the blades running with the throttle is all the way down. This involved editing config.h, finding the "Motor minthrottle" section and setting the minimum value to 1020.  This works well with my Turnigy Plush 18A controllers and DT750 outrunners. Looking forward to testing all these changes out tomorrow, if I can find some gloves and a jumper to wear.

[UPDATE]  So far [this is the best PID tuning guide I have found](http://www.rcgroups.com/forums/showthread.php?t=1375728) - though it doesn't really explain PID control very well, it does give a decent practical guide to tuning a multirotor.