
---
title: "Thatcham Trains"
date: 2016-06-22T13:24:04
draft: False
---

This is the final article in my brief series on the National Rail API.  As usual, the code can be found on github:

<a href="https://github.com/DanteLore/national-rail">https://github.com/DanteLore/national-rail</a>
# The Idea
https://twitter.com/ThatchamTrains/status/745593012290461696

There are a million and one different websites and apps which will tell you the next direct train from London Paddington to Thatcham (or between any other two railway stations) but all those apps are very general. You have to struggle through the crowds on the Circle Line while selecting the stations from drop-downs and clicking "Submit", for example. Wouldn't it be good if there was a simple way to see the information you need without any user input? Even better, what if you could get notifications when the direct trains are delayed or cancelled?

Enter stage left, the Twitter API. This article is all about a simple *mash-up* of the National Rail and twitter APIs to show information on direct trains between London and Thatcham. You can use it for other stations too - it's all in the command line parameters.

People who live in Thatcham can use my twitter feed <a href="https://twitter.com/ThatchamTrains">@ThatchamTrains</a> or you can set up your own feed and run the python script to populate it with the stations you're interested in.

https://twitter.com/ThatchamTrains/status/745592675378823168
https://twitter.com/ThatchamTrains/status/745557198038675456

The script also sends direct messages if the trains are more than 15 minutes late or cancelled.
# Using the script
I host my instance of the script on my raspberry pi, which is small, cheap, quiet and can be left on 24x7 without much hassle. These instructions are therefore specific to setup on the pi, but the script will work on Windows and other version of Linux too.

1. Install the python libraries you need. You may already have these installed.

```bash
$ sudo easy_install argparse
$ sudo easy_install requests
$ sudo easy_install xmltodict
$ sudo easy_install flask

```
2. Get a twitter account and a set of API keys by following the <a href="https://dev.twitter.com/oauth/overview">steps on the Twitter developers page</a>. You'll need four magic strings in total, which you pass to the script as command line parameters.

3. Get a national rail API key <a href="http://www.nationalrail.co.uk/100296.aspx">from their website</a>. You just need one key for this API, which is nice!

4. Clone the source and run the script using the three commands below... simples!

```bash
$ git clone https://github.com/DanteLore/national-rail.git
$ cd national-rail
$ python twitterrail.py --rail-key YOUR_NATIONAL_RAIL_KEY --consumer-key YOUR_CUST_KEY --consumer-secret YOUR_CUST_SECRET --access-token YOUR_ACCESS_TOKEN --access-token-secret YOUR_ACCESS_TOKEN_SECRET --users YourTwitterName --forever

```
When run with the --forever option, the script will query the NR API and post to twitter every 5 minutes. Note that there are some basic checks to prevent annoying behaviour and duplicate messages. You can specify one or more usernames who you'd like to receive direct messages when there are delays and cancellations; note that only users who follow you can receive DMs on twitter.

You can use other stations by specifying the three character station codes (CRS) for "home" and "work" on the command line. Here are the command line options:

```bash
$ python twitterrail.py --help

usage: twitterrail.py [-h] [--home HOME] [--work WORK] [--users USERS]
[--forever] --rail-key RAIL_KEY --consumer-key
CONSUMER_KEY --consumer-secret CONSUMER_SECRET
--access-token ACCESS_TOKEN --access-token-secret
ACCESS_TOKEN_SECRET

Tweeting about railways

optional arguments:
-h, --help            show this help message and exit
--home HOME           Home station CRS (default "THA")
--work WORK           Work station CRS (default "PAD")
--users USERS         Users to DM (comma separated)
--forever             Use this switch to run the script forever (once ever 5 mins)
--rail-key RAIL_KEY   API Key for National Rail
--consumer-key CONSUMER_KEY
Consumer Key for Twitter
--consumer-secret CONSUMER_SECRET
Consumer Secret for Twitter
--access-token ACCESS_TOKEN
Access Token for Twitter
--access-token-secret ACCESS_TOKEN_SECRET
Access Token Secret for Twitter

```

# The Code
There's not much to say about the code, since I've covered the National Rail API in graphic detail in a <a href="http://logicalgenetics.com/live-train-route-animation/">previous adventures</a> with the API is that this time I did unit testing.

There's a fair bit of business logic in the twitter app: rules about when to post and when to be quiet, duplicate message detection and all sorts of time- and data-based rules which can't be tested using real data. It's also pretty bad form to test code like this against a live API, so I mocked out the NR query and the Twitter API and wrote a small suite of tests to check that the behaviour is right.

Like I said, all the code is on GitHub, so I won't bang on about it here.

<a href="https://github.com/DanteLore/national-rail">https://github.com/DanteLore/national-rail</a>