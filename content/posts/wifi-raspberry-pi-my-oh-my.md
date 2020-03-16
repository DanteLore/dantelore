
---
title: "WiFi, Raspberry Pi, My oh my"
date: 2013-08-31T22:43:37
draft: False
---

Downloaded and installed the latest Arch Linux image for the Raspberry Pi today, only to find that the whole world has changed since I last looked at wireless connectivity!

There's no rc.d any more, there's a totally new way to manage networks (wireless and otherwise) and everything I thought I knew is wrong.  I think I'm getting old!

After hours of faffing about, it turns out I could have had this nailed with two very simple commands.

Assuming you have a recent Arch Linux, I think you just need to:

<strong>1. Use the GUI tool to connect to your WiFi</strong>
```bash
sudo wifi-menu -o

```
Select the right network and enter the password when prompted.  When you exit the application you should be connected to your router and the tool will have saved a config file to <strong>/etc/netctl/wlan0-YourNetwork</strong>

<strong>2. Change to a static IP address</strong>

Weirdly, the above step fails first time with a dhcp error.  It saves a valid config file but doesn't manage to connect.  It seems to want to bring up dhcp against eth0 not wlan0 as I'd expect/prefer.  Didn't manage to find out why this is or how to fix it (yet) so just swapped to a static IP!

Use your favorite editor (I like nano) to change the config file just generated removing...
[sourcecode]
IP=dhcp
[/sourcecode]
...and adding this in it's place...

```bash
IP=static
Address=('192.168.1.188/24')
Gateway=('192.168.1.254')
DNS=('8.8.8.8')

```
After you've done that, repeat step 1 and proceed to step 3.  Note that when you run <strong>wifi-menu</strong> the second time it will show that there is an existing config for your network.  You might also find that step 1 works and you don't need to do step 2 at all, because you might have downloaded a fixed Arch image.  In which case, I salute your fortitude!

<strong>3. Install the config</strong>

Since you probably want to reconnect the wireless after reboots, you need to install the config using the funky new "netctl" tool.
```bash
sudo netctl enable wlan0-YourNetwork

```
Now you should have a working wireless connection after reboots and a raspberry pi that works like your old one did!

<strong>Rant: Who moved my cheese?</strong>

People always moan about Windows versions that are different to old Windows versions - why Windows 8 took away the start button, why Windows Vista did security totally differently, blah blah blah...

Today I spent several hours digging around looking for something that should be easy, only to find it is indeed very easy... when you know how.

So today I'm quite happy to argue that Linux does exactly the same thing as Windows.  I like Arch Linux a lot, and I accept that it's not for newbies, but things change as often in Linux world as they do in Windows world.  On both sides of the fence, Google is your only hope if you want to keep up to date with the changing software that underpins everything you do.