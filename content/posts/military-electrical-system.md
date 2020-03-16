
---
title: "Military Electrical System"
date: 2009-01-12T08:55:00
draft: False
---

Spent some time yesterday night working out what most of the components actually <span style="font-style: italic;">do</span>.  Here are my findings, along with a new version of the military circuit diagram.

<a href="http://danandtheduke.co.uk/uploaded_images/LandRoverTransistorised24v-736485.png"><img src="http://danandtheduke.co.uk/uploaded_images/LandRoverTransistorised24v-736480.png"/></a><span style="font-weight: bold;font-size:130%;" >6-Way lighting switch</span>
Note that the lighting switch is not supplied via the ignition switch, so some lights will work when the ignition is off.

Supply to the light switch is direct from the battery, via the infra red relay.

Six connections:
1. Stop lamp, hazard switch, aux relay (NY)
2. IR Switch (UB)
3. Tail Lamps (RB)
4. Side lamps, dash illum (RY)
5. Convoy lamp (RY)
6. IR Relay (NP)

Six modes of operation (unconfirmed):
1. Off
2. (T) Tail lamps
3. (ST) Side and tail lamps
4. (HST) Head, side and tail lamps
5. (Conv) Convoy lamp
6. (S. Conv) Convoy and side lamps?

<span style="font-weight: bold;font-size:130%;" >Infra Red Switch and Relay</span>
Used to switch off entire lighting system when using IR lights.  Controls supply to main lighting switch.

IR relay activated by IR switch.  Supply direct from battery.  Feeds main lighting switch.

IR switch supplied directly from battery.  When in "Headlights Normal" position, activates head lights (via flash and dip switch), auxiliary relay and fog relay (pins 1, 2 and 3 connected).  When in "Infra-Red" position, links lighting switch to headlight flash and dip switch (pins 1 and 5 connected).

<span style="font-weight: bold;font-size:130%;" >Auxiliary Relay</span>
The aux relay activates things which need to be on when the ignition is on AND the light switch says it's OK!

Activated by lighting switch, on same supply as stop lights and hazard switch (for hazards)
Supply from ignition switch
Supplies headlight flash switch, horn, reverse lamps and hazard switch (for indicators).

<span style="font-weight: bold;font-size:130%;" >Fog Relay</span>
When lighting switch enables the aux relay AND headlights are on, enables supply to fog lamp switch and thus fogs.

<span style="font-weight: bold;font-size:130%;" >Hazard Switch and Flasher</span>
Supply for indicators comes from aux relay (so they'll work if the ignition is on and the lighting switch allows).
When hazard switch is OFF, power supplied to flasher pin 49 (the indicator supply).
Flashing output from the flasher 49a feeds the indicator switch and thus the indicators.

Supply for the hazard lights comes from the lighting switch (so they'll work if the lighting switch allows, but regardless of ignition switch position).
When hazard switch is ON, power supplied to flasher pin 30b (the hazard supply).  Pin 49a of the flasher is connected directly to the indicators, bypassing the indicator switch.

<a href="http://www.lrforum.com/forum/index.php?showtopic=30584&view=findpost&p=291850">More info on hazard switch and flasher here</a>.