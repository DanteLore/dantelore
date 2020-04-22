
---
title: "Serial on Raspberry Pi Arch Linux"

date: "2013-09-01T19:56:13"

featured_image: "/images/gear48.png"
---


So the new version of Arch Linux doesn't have runlevels, rc.d or any of that nonsense any more.  It just has **systemd**.  Super simple if you know how to use it, but a right pain in the backside if you don't.

I have a little serial GPS module hooked up to my Raspberry Pi via the hardware serial port (ttyAMA0). My old instructions for getting this to work aren't much use any more.  Here's the new procedure for getting serial data with the minimum of fuss:

**1. Disable serial output during boot**

Edit **/boot/cmdline.txt** using your favourite editor.  I like nano these days.

```bash
sudo nano /boot/cmdline.txt

```
Remove all chunks of text that mention ttyAMA0 but leave the rest of the line intact.  Bits to remove look like:

```bash
console=ttyAMA0,115200 kgdboc=ttyAMA0,115200

```
**2. Disable the console on the serial port**

This was the new bit for me. The process used to involve commenting out a line in **/etc/innitab** but that file is long gone.

Systemd uses links in /etc to decide what to start up, so once you find the right one, removing it is easy.  You can find the files associated with consoles by doing:

```bash
ls /etc/systemd/system/getty.target.wants/

```
One of the entries clearly refers to ttyAMA0.  It can be removed using the following command:

```bash
sudo systemd disable serial-getty@ttyAMA0.service

```
**3. Check you're getting data**

I used minicom for this as it's very simple to use.  First of all, make sure you plug in your device (with the power off, if you're as clumsy as me!).

```bash
sudo pacman -S minicom

```

```bash
minicom -b 4800 -o -D /dev/ttyAMA0

```
You should see a lovely stream of data.  I my case it was a screen full of NMEA sentences.  Great stuff!