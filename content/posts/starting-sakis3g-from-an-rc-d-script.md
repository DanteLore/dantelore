
---
title: "Starting sakis3g from an rc.d script"

date: "2012-10-13T11:10:11"

featured_image: "http://logicalgenetics.com/wp-content/uploads/2012/10/IMG_7010-300x200.jpg"
---


Have been working on The Duke's brain today.  Needed to get the mobile internet connection to start automatically when the Raspberry Pi boots.  I'm using a Huawei E220 dongle with a Giff Gaff SIM. The easiest way to get it connected to the internet is to use the fantastic <a href="http://www.sakis3g.org/">sakis3g script</a>.

<a href="http://logicalgenetics.com/wp-content/uploads/2012/10/IMG_7010.jpg"><img src="/images/starting-sakis3g-from-an-rc-d-script/IMG_7010-300x200.jpg"/></a>The best way to get anything to run at startup is with an rc script, but even after searching for some time I couldn't find any examples.  So here's one I crafted myself, in case anyone finds it useful. Obviously you'll have to change the "ARGS" line to set the correct APN and so on for your connection.  You'll also need to copy the sakis3g script itself to /usr/sbin.

**/etc/rc.d/sakis:**
```bash
#!/bin/bash

. /etc/rc.conf
. /etc/rc.d/functions

ARGS="APN=\"CUSTOM_APN\" CUSTOM_APN=\"giffgaff.com\" FORCE_APN=\"giffgaff.com\" APN_USER=\"giffgaff\" APN_PASS=\"password\""

PID=$(get_pid sakis)

case "$1" in
 start)
   stat_busy "Starting sakis"
   [ -z "$PID" ] &amp;&amp; /usr/sbin/sakis3g connect $ARGS &amp;>/dev/null
   if [ $? = 0 ]; then
     add_daemon sakis
     stat_done
   else
     stat_fail
     exit 1
   fi
   ;;
 stop)
   stat_busy "Stopping sakis"
   /usr/sbin/sakis3g disconnect &amp;>/dev/null
   if [ $? = 0 ]; then
     rm_daemon sakis
     stat_done
   else
     stat_fail
     exit 1
   fi
   ;;
 restart)
   $0 stop
   sleep 1
   $0 start
   ;;
 *)
   echo "usage: $0 {start|stop|restart}"
esac

```
This can be started from rc.conf by adding "sakis" toi your daemons line.

**/etc/rc.conf**
```bash
DAEMONS=(!hwclock syslog-ng @network @net-profiles @dhcpd @openntpd @netfs @crond @sshd @samba @sakis)

```
There are some downsides to using sakis3g for connecting to the internet though - especially for my project.  I have both a 3g and a WiFi dongle attached to the RaspberryPi, but sakis sets up the routes in such a way once you're connected via the mobile network, you always route that way.  Even though the wireless is faster, when I run a package upgrade or download a file it comes over 3g, not WiFi.  Worse than that, if you stop sakis, it doesn't restore your routes, so you can't connect to the internet at all without issuing some route commands.