
---
title: "People Who Like Debugging"

date: "2012-11-16T18:13:56"

featured_image: "/images/people-who-like-debugging/"
---


Somebody said something on Twitter today that made me think about a belief I hold quite strongly...
<blockquote>Never trust people who **enjoy debugging.</blockquote>
Lots of people say they do.  It's not a crime or anything.  Every single software product has bugs that need to be fixed.  Sometimes these bugs are hard to find or incredibly complex and I've seen many of the best developers I know work for days tracking down bugs that were so subtle they were almost indescribable.

People who enjoy fixing bugs get the same sort of pleasure from doing it as they do from solving Sudoku puzzles or playing chess.  It stretches your brain; allows you to discover new worlds of complexity and learn new subtleties about the tools you use.  Great.  Super.  If you like that sort of thing.

I love old Land Rovers.  Even though I know they are terrible for the environment, they aren't as safe as modern cars, they are expensive to run, noisy and uncomfortable.  The logical part of my brain knows that they shouldn't be allowed.  I know without question that we *should* all drive quiet, safe electric cars.  Yet I would do anything in my power to make sure that doesn't happen because I just love driving and working on my old Land Rover.

So what about people who love fixing bugs?  Ask them and I'm sure they'd agree that bugs are bad.  There should be no bugs.  Bugs cost money, delay projects, lead us to false conclusions and often kill people.  But if there were no bugs, there'd be no bugs to fix...

I've worked with a lot of developers in my time and I've interviewed a lot more than that.  In almost every case the ones that enjoy fixing bugs write the weirdest code.  They're the ones who shun unit testing, they pour scorn on suggestions that code should read like natural language and they laugh out loud at the foolishness of separation of concerns or the single responsibility principle.

If you're fixing a bug, you should feel bad.  If the bug is in somebody else's code you should be angry that they let it through.  If their code is so tangled and obscure that it takes you a day to find the cause of the problem, you should hate them for wasting your time.  You should refactor and add tests: clear away the brambles and make it easier for those who follow in your path... and if the code was yours, you need to think hard about what you can learn from the mess you're in.

Of course, when the bug is fixed, the code produces a better result in a hundredth of the time, you deserve a pat on the back.  You made it through the valley of death and you saved the day.  We'll all be rich!  The Earth is saved!  Woo!

...but don't loose sight of the fact that you shouldn't have had to do it.  Maybe spend some time celebrating the parts of your product that never had a bug in the first place.