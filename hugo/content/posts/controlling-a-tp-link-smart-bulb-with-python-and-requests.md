
---
title: "Controlling a TP-Link Smart Bulb with Python and Requests"

date: "2017-12-12T08:32:28"

featured_image: "/images/gear48.png"
---


We recently added a new build status indicator in the office, using an excellent TP-Link LB130 Smart Bulb.  Though these bulbs are pretty expensive, they are super-simple to set up and control via a simple REST API.

It took quite a lot of googling to find the correct commands to send through the API to control the bulb, but once we'd found the answer, it was incredibly simple.
[video loop="on" width="1920" height="1080" mp4="http://logicalgenetics.com/wp-content/uploads/2017/12/2017-12-08-16.48.37.mp4"][/video]

There are many Python libraries out there for controlling these bulbs directly via the local network, but the benefit of using the REST API is that you can control the bulb from *anywhere*.  You are also able to "discover" the bulbs associated with your TP-Link Kasa account, so you don't need to know the IP address or MAC of your bulbs.

Here's the code...
```python
import unittest
import requests
import uuid
import json
import random

USERNAME = 'your.email@address.com'
PASSWORD = 'YourPassword123'
class TpLinkApiTests(unittest.TestCase):
    def test_change_bulb_colour(self):
        # First step is to get a token by authenticating with your username (email) and password
        payload = {
            "method": "login",
            "params":
                {
                    "appType": "Kasa_Android",
                    "cloudUserName": USERNAME,
                    "cloudPassword": PASSWORD,
                    "terminalUUID": str(uuid.uuid4())
                }
        }
        response = requests.post("https://wap.tplinkcloud.com/", json=payload)
        self.assertEqual(200, response.status_code)
        obj = json.loads(response.content)
        token = obj["result"]["token"]

        # Find the bulb we want to change
        payload = {"method": "getDeviceList"}
        response = requests.post("https://wap.tplinkcloud.com?token={0}".format(token), json=payload)
        self.assertEqual(200, response.status_code)

        # The JSON returned contains a list of devices. You could filter by name etc, but here we'll just use the first
        obj = json.loads(response.content)
        bulb = obj["result"]["deviceList"][0]

        # The bulb object contains a 'regional' address for control commands
        app_server_url = bulb["appServerUrl"]
        # Also grab the bulbs ID
        device_id = bulb["deviceId"]

        # Send a command through to the bulb to change it's colour
        # This is the command for the bulb itself...
        bulb_command = {
            "smartlife.iot.smartbulb.lightingservice": {
                "transition_light_state": {
                    "on_off": 1,
                    "brightness": 100,
                    "hue": random.randint(1, 360), # Random colour
                    "saturation": 100
                }
            }
        }
        # ...which is escaped and passed within the JSON payload which we post to the API
        payload = {
            "method": "passthrough",
            "params": {
                "deviceId": device_id,
                "requestData": json.dumps(bulb_command)  # Request data needs to be escaped, it's a string!
            }
        }
        # Remember to use the app server URL, not the root one we authenticated with
        response = requests.post("{0}?token={1}".format(app_server_url, token), json=payload)
        self.assertEqual(200, response.status_code)

        # Hopefully the bulb just changed colour!
        print response.content
```
