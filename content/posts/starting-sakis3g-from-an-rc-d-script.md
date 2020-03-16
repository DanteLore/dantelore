
---
title: "Starting sakis3g from an rc.d script"
date: 2012-10-13T11:10:11
draft: False
---

Have been working on The Duke's brain today.  Needed to get the mobile internet connection to start automatically when the Raspberry Pi boots.  I'm using a Huawei E220 dongle with a Giff Gaff SIM. The easiest way to get it connected to the internet is to use the fantastic <a href="http://www.sakis3g.org/">sakis3g script</a>.

<a href="http://logicalgenetics.com/wp-content/uploads/2012/10/IMG_7010.jpg"><img src="http://logicalgenetics.com/wp-content/uploads/2012/10/IMG_7010-300x200.jpg"/></a>The best way to get anything to run at startup is with an rc script, but even after searching for some time I couldn't find any examples.  So here's one I crafted myself, in case anyone finds it useful. Obviously you'll have to change the "ARGS" line to set the correct APN and so on for your connection.  You'll also need to copy the sakis3g script itself to /usr/sbin.

<strong>/etc/rc.d/sakis:</strong>

[sourcecode language="bash"]
#!/bin/bash

. /etc/rc.conf
. /etc/rc.d/functions

ARGS=&quot;APN=\&quot;CUSTOM_APN\&quot; CUSTOM_APN=\&quot;giffgaff.com\&quot; FORCE_APN=\&quot;giffgaff.com\&quot; APN_USER=\&quot;giffgaff\&quot; APN_PASS=\&quot;password\&quot;&quot;

PID=$(get_pid sakis)

case &quot;$1&quot; in
 start)
   stat_busy &quot;Starting sakis&quot;
   [ -z &quot;$PID&quot; ] &amp;&amp; /usr/sbin/sakis3g connect $ARGS &amp;&gt;/dev/null
   if [ $? = 0 ]; then
     add_daemon sakis
     stat_done
   else
     stat_fail
     exit 1
   fi
   ;;
 stop)
   stat_busy &quot;Stopping sakis&quot;
   /usr/sbin/sakis3g disconnect &amp;&gt;/dev/null
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
   echo &quot;usage: $0 {start|stop|restart}&quot;
esac
[/sourcecode]

This can be started from rc.conf by adding "sakis" toi your daemons line.

<strong>/etc/rc.conf</strong>

[sourcecode language="bash"]
DAEMONS=(!hwclock syslog-ng @network @net-profiles @dhcpd @openntpd @netfs @crond @sshd @samba @sakis)
[/sourcecode]

There are some downsides to using sakis3g for connecting to the internet though - especially for my project.  I have both a 3g and a WiFi dongle attached to the RaspberryPi, but sakis sets up the routes in such a way once you're connected via the mobile network, you always route that way.  Even though the wireless is faster, when I run a package upgrade or download a file it comes over 3g, not WiFi.  Worse than that, if you stop sakis, it doesn't restore your routes, so you can't connect to the internet at all without issuing some route commands.