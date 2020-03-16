
---
title: "Train Departure Board"
date: 2016-06-15T12:51:36
draft: False
---

You can find the code for this article on my github: <a href="https://github.com/DanteLore/national-rail">https://github.com/DanteLore/national-rail</a>.

Having found myself time-wealthy for a couple of weeks I've been playing around with some open data sets. One of which is the <a href="http://www.nationalrail.co.uk/100296.aspx">National Rail SOAP API</a>. It's not a new dataset, I think it's been around for a decade or so, but it seemed like a sensible thing for me to play with as I'll be on trains a lot more when I start selling my time to a new employer next month!

I live about a mile away from the local station (Thatcham) so it only takes a few minutes to get there. If a train is delayed or cancelled I'd like to know so I can have another coffee. So what I need is a live departures board, for my local station, in my house. Something like this:

<a href="http://logicalgenetics.com/train-departure-board/departures/"><img src="http://logicalgenetics.com/wp-content/uploads/2016/06/departures.png"/></a>

The UI is web based - using AngularJS again. Sadly though, the cross origin nonsense means I can't make the soap calls directly from the web client, I need a back-end to gather and store the data for use on the UI. I used Python for this because that gives me the option to (easily) run it on a Raspberry Pi, reducing power and space costs as well as noise. Python's library support is stunning, and this is another great reason to use it for small "hacks" like this one.

## SOAPing Up

SOAP is horrible. It's old, it's heavy, it's complex and worst of all it's XML based. This isn't a huge handicap though, as we can hit a SOAP service using the <a href="http://docs.python-requests.org/en/master/">requests HTTP library</a> - simply sending a POST with some XML like so:

```python
import requests
import xmltodict

xml_payload = """<?xml version="1.0"?>
<SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ns1="http://thalesgroup.com/RTTI/2016-02-16/ldb/" xmlns:ns2="http://thalesgroup.com/RTTI/2013-11-28/Token/types">
  <SOAP-ENV:Header>
    <ns2:AccessToken>
      <ns2:TokenValue>{KEY}</ns2:TokenValue>
    </ns2:AccessToken>
  </SOAP-ENV:Header>
  <SOAP-ENV:Body>
    <ns1:GetDepBoardWithDetailsRequest>
      <ns1:numRows>12</ns1:numRows>
      <ns1:crs>{CRS}</ns1:crs>
      <ns1:timeWindow>120</ns1:timeWindow>
    </ns1:GetDepBoardWithDetailsRequest>
  </SOAP-ENV:Body>
</SOAP-ENV:Envelope>
"""

# url: The URL of the service
# key: Your National Rail API key
# crs: Station code (e.g. THA or PAD)
def fetch_trains(url, key, crs):
    headers = {'content-type': 'text/xml'}
    payload = xml_payload.replace("{KEY}", key).replace("{CRS}", crs)
    response = requests.post(url, data=payload, headers=headers)

    data = xmltodict.parse(response.content)
    services = data["soap:Envelope"]["soap:Body"]["GetDepBoardWithDetailsResponse"]["GetStationBoardResult"]["lt5:trainServices"]["lt5:service"]

    for service in services:
        raw_points = service["lt5:subsequentCallingPoints"]["lt4:callingPointList"]["lt4:callingPoint"]

        calling_points = map(lambda point: {
            "crs": point["lt4:crs"],
            "name": point["lt4:locationName"],
            "st": point.get("lt4:st", "-"),
            "et": point.get("lt4:et", "-")
        }, raw_points)

        cp_string = "|".join(
                map(lambda p: "{0},{1},{2},{3}".format(p["crs"], p["name"], p["st"], p["et"]), calling_points)
        )

        yield {
            "crs": crs,
            "origin": service["lt5:origin"]["lt4:location"]["lt4:locationName"],
            "destination": service["lt5:destination"]["lt4:location"]["lt4:locationName"],
            "std": service.get("lt4:std"),
            "etd": service.get("lt4:etd"),
            "platform": service.get("lt4:platform", "-"),
            "calling_points": cp_string
        }

```
So, I take a pre-formatted XML request, add the key and station code then POST it to the API URL. Easy. The result comes back in XML which can be parsed very easily using the <a href="https://chrome.google.com/webstore/detail/postman/fhbjgbiflinjbdggehcddcbncdddomop?hl=en">Postman </a>to test the calls before translating to Python (and if you're lazy, Postman will even write the code for you!).

The Python script takes the data its gathered and stores it in an SQLite database. I'm not going to show the code because it's all in <a href="https://github.com/DanteLore/national-rail">github</a> anyway.

## Having a REST

So the data is all in a DB, now it needs to be made available to the Javascript client somehow.  To do this I created a simple REST service using the excellent <a href="http://martinfowler.com/articles/microservices.html">microservice</a> frameworks I love to use. Here's all the code you need to serve up data via REST:

```python
import argparse
import sqlite3
from flask import Flask, jsonify

app = Flask(__name__)
@app.route('/departures')
def departures():
    return jsonify(fetch_departures("departures"))
@app.route('/departures/<string:crs>')
def departures_for(crs):
    return jsonify(fetch_departures("departures", crs))
if __name__ == '__main__':
    parser = argparse.ArgumentParser(description='National Rail Data REST Server')
    parser.add_argument('--db', help='SQLite DB Name', default="../data/trains.db")
    args = parser.parse_args()
    db = args.db

    app.run(debug=True)

```
## Front End

The front end is a very simple Angular JS app. Not much point showing the code here (see <a href="https://bootswatch.com/">Bootswatch</a>.

The design is based on a real life station departures board like this:
<a href="http://logicalgenetics.com/train-departure-board/departures-750/"><img src="http://logicalgenetics.com/wp-content/uploads/2016/06/departures-750.jpg"/></a>

All in all the project took me a little over a day. A leisurely day with many interruptions from my daughters! Feel free to pull the code down and play with it - let me know what you do.

<a href="http://logicalgenetics.com/train-departure-board/2016-06-14-11-32-01/"><img src="http://logicalgenetics.com/wp-content/uploads/2016/06/2016-06-14-11.32.01.jpg"/></a>