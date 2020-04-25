
var countdownTimerId;
var countdowns = new Array();

var oneSecInMs  = 1000;
var oneMinInMs  = 1000 * 60;
var oneHourInMs = 1000 * 60 * 60;
var oneDayInMs  = 1000 * 60 * 60 * 24;

function tick()
{      
  clearTimeout(countdownTimerId);
  countdownTimerId = setTimeout("tick()", 1000);
  
  var dateTimeNow = new Date();
  var target = new Date();
  
  for (var i = 0; i < countdowns.length; i++)
  {
    if(countdowns[i])
    {  
      target.setTime(countdowns[i].target.getTime());
      
      if(target.getTime() < dateTimeNow.getTime())
      {
        daysElement = document.getElementById(countdowns[i].name);
        daysElement.innerHTML = "Countdown complete";
    
        countdowns[i] = 0;
        continue;
      }
      
      var months = 0;
      var days = 0;
      var hours = 0;
      var mins = 0;
      var secs = 0;
      
      while(target.getFullYear() > dateTimeNow.getFullYear() || target.getMonth() - 1 > dateTimeNow.getMonth())
      {
        if(target.getMonth() == 0)
        {
          target.setMonth(11);
          target.setFullYear(target.getFullYear() - 1);
        }
        else
        {
          target.setMonth(target.getMonth() - 1);
        }
          
        months++;
      }
      
      while(target.getTime() - dateTimeNow.getTime() > oneDayInMs)
      {
        target.setTime(target.getTime() - oneDayInMs);
        days++;
      }
     
      while(target.getTime() - dateTimeNow.getTime() > oneHourInMs)
      {
        target.setTime(target.getTime() - oneHourInMs);
        hours++;
      }
     
      while(target.getTime() - dateTimeNow.getTime() > oneMinInMs)
      {
        target.setTime(target.getTime() - oneMinInMs);
        mins++;
      }
     
      while(target.getTime() - dateTimeNow.getTime() > oneSecInMs)
      {
        target.setTime(target.getTime() - oneSecInMs);
        secs++;
      }
      
      monthsElement = document.getElementById(countdowns[i].name + '.months');
      monthsElement.innerHTML = months; 
      daysElement = document.getElementById(countdowns[i].name + '.days');
      daysElement.innerHTML = days; 
      hoursElement = document.getElementById(countdowns[i].name + '.hours');
      hoursElement.innerHTML = hours; 
      minutesElement = document.getElementById(countdowns[i].name + '.minutes');
      minutesElement.innerHTML = mins; 
      secondsElement = document.getElementById(countdowns[i].name + '.seconds');
      secondsElement.innerHTML = secs; 
    }
  }
}

function countdown(name, year, month, day, hour, minute, second)
{
  var timer = new Object();
  timer.name = name;
  timer.target = new Date(year, month - 1, day, hour, minute, second);
  
  countdowns[countdowns.length] = timer;
  
  targetElement = document.getElementById(name + '.target');
  if(targetElement)
    targetElement.innerHTML = timer.target.toUTCString(); 
  
  tick();
}
