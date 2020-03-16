
---
title: "The ASCII Speedometer"
date: 2012-10-25T19:36:48
draft: False
---

Once I'd <a href="http://logicalgenetics.com/raspberry-pi-and-mono-hello-world/">got Mono up and running</a>, the first little project I did with the Raspberry Pi was to hook up an old GPS module and use it to create a text based speedometer for the car.  It was the first step I took towards <span style="text-decoration: line-through;">making The Duke sentient</span> building an on-board computer for my Land Rover.  I was fun to do and raised a smile with the people who I told about it, so I thought I'd bung the details online.

<a href="http://logicalgenetics.com/wp-content/uploads/2012/10/105qmj5.jpg"><img src="http://logicalgenetics.com/wp-content/uploads/2012/10/105qmj5.jpg"/></a>
## The Hardware Bit
First step is to get the serial port working.  If you have a USB GPS module then it's just a case of plugging it in, but mine is a 3.3v logic-level serial module that I bought about eight years ago to use with my Gumstix in <a href="http://logicalgenetics.com/longwalk/index.php">big charity walks</a> we did back in 2004 and 2006.  It's safe to say that hooking it up to the Raspberry Pi was a much simpler affair!

There are some great guides out there about soldering up the right pins (<a href="http://benosteen.wordpress.com/2012/04/24/raspberry-pis-onboard-serial-connection/">here</a>).  I hooked up 3.3v power, ground and connected TX on the GPS to RX on the Raspberry Pi.  I used a little bit of veroboard, a 0.1" header, some ribbon cable and made the GPS pluggable.

Out of the box the Raspberry has a console running on the serial port.  You need to disable this before you can do anything with the port.  Very easy to do: Edit /etc/inittab and remove the line that refers to /dev/ttyAMA0;  Edit /boot/cmdline.txt and remove the chunks of text that refer to /dev/ttyAMA0.  After a reboot the terminal will be gone.
## Reading from the Serial Port in Mono
Right, now we're ready to write some code.  First off I wrote this simple bit of C# to test that I was getting messages. The ReadData method reads text from the serial port one character at a time, detecting end of line characters to return a string for each line. The main method loops forever reading these lines and printing them to the console if they start with the NMEA "Recommended Minimum Content" message $GPRMC.
```csharp
using System;
using System.IO.Ports;

public class Serial
{
   public static void Main()
   {
      SerialPort serial = new SerialPort("/dev/ttyAMA0", 4800);
      serial.Open();
      serial.ReadTimeout = 1000;

      while(true)
      {
         string data = ReadData(serial);
         if(!string.IsNullOrEmpty(data) &amp;&amp; data.StartsWith("$GPRMC"))
         {
             Console.WriteLine(data);
         }
      }
   }

   public static string ReadData(SerialPort serial)
   {
      byte tmpByte;
      string rxString = "";

      do
      {
         tmpByte = (byte) serial.ReadByte();
         rxString += ((char) tmpByte);
         tmpByte = (byte) serial.ReadByte();
      }while (tmpByte != 13 &amp;&amp; tmpByte != 10);

      return rxString.Trim();
   }
}

```

## Parsing the NMEA Data
I then did a bit of a Test Driven Development exercise to write a *proper* parser for NMEA messages. To do this in a test driven way I got my hands on some data files containing raw NMEA data and used that to create a *Mock* serial port reader. I could then pass these messages through my parser and test that I had managed to extract the right data.

Unit testing and using mocks was a great way to develop this part of the application. I could use recorded routes with real movement to test the parsing of speed data - since coding in a moving car seemed silly. I could also do all the coding work in Visual Studio on my Windows machine. This meant I could make the most of a nice big screen, code completion, resharper's excellent testing interface and so on, then just push the code onto the Pi when it was done; I didn't have to worry that "/dev/ttyAMA0" is "COM3" in Windows land, because I wasn't using a real serial port to do 99% of the development.

A typical test for parsing of individual messages (hand typed!):
```csharp
[TestCase("$GPRMC,005959,V,4807.038,N,11130.00,E,022.4,084.4,010101,003.1,W*4E", 48.1173, 111.5)]
public void CanParseLatLongFromRmcMessage(string input, double expectedLat, double expectedLong)
{
    NmeaParser parser = new NmeaParser();
    GpsMeasurement measurement = parser.Parse(input);

    Assert.That(measurement.Latitude, Is.EqualTo(expectedLat));
    Assert.That(measurement.Longitude, Is.EqualTo(expectedLong));
}

```
The mock serial reader class:
```csharp
public class MockSerialPortReader : IPortReader
{
    private readonly string filename;
    private readonly int sleep;

    public MockSerialPortReader(string filename, int sleep)
    {
        this.filename = filename;
        this.sleep = sleep;
    }

    public IEnumerable Lines
    {
        get
        {
            foreach (string line in File.ReadAllLines(filename))
            {
                Thread.Sleep(sleep);
                yield return line;
            }
        }
    }
}

```
A typical unit test using the mock reader:
```csharp
[Test]
public void CanGetMeasurementsFromMockReaderDataSet1()
{
    NmeaParser parser = new NmeaParser();

    IEnumerable measurements = parser.ParseFrom(new MockSerialPortReader(dataSet1, 0));
    Assert.That(measurements, Is.Not.Null);
    CollectionAssert.IsNotEmpty(measurements);
    CollectionAssert.AllItemsAreInstancesOfType(measurements, typeof(GpsMeasurement));
    CollectionAssert.AllItemsAreNotNull(measurements);

    Console.WriteLine("{0}, {1}", measurements.Last().Latitude, measurements.Last().Longitude);
}

```

## Displaying the Speedo Text
The final stage of this little mini-project was to knock up a user interface. I spent a while looking at how to get something working under X Windows, then decided to go back to the Old School and just use ASCII art.

First thing was to find a quick and dirty way to define how each big number would look. Each number is made up of a grid of characters, defined in a class:
```csharp
public class TextConstants
{
    public static readonly string[] Zero = new[]
        {
            "   000000",
            "  00000000",
            " 000    000",
            " 000    000",
            " 000    000",
            " 000    000",
            "  00000000",
            "   000000"
        };

   ...etc etc...
}

```
Writing this out then becomes an exercise in text placement:
```csharp
public void DrawSpeed(int speed)
{
    Console.Clear();

    DrawOutline();

    char[] asciiSpeed = speed.ToString("00").ToCharArray();

    int xOffset = 20;
    foreach (char c in asciiSpeed)
    {
        DrawNumber(TextConstants.For(c), xOffset, 8);
        xOffset += 15;
    }
}

private void DrawNumber(IEnumerable lines, int xOffset, int yOffset)
{
    int lineNo = 0;
    foreach (string line in lines)
    {
        WriteAt(line, xOffset, yOffset + lineNo);
        lineNo++;
    }
}

```

## Did it Work?
Well, yes! The main issue was the update speed. This is because the old GPS module outputs data very very slowly and has quite a slow refresh rate. As a speedometer it wasn't much good - it generally showed the speed I *was* doing about 15 seconds ago. As a project it worked brilliantly though.

I extended the code slightly to add some logging. This saved the location data to a set of simple, size-limited, CSV files on the Pi's flash card. I then knocked up some more code to<a href="http://logicalgenetics.com/a-trip-to-work/"> turn the measurements into a "Trip Report" using the Google Maps API</a>. Top Notch!