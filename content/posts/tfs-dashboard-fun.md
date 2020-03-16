
---
title: "TFS Dashboard Fun"
date: 2012-11-24T22:22:13
draft: False
---

It turns out it's really quite simple to get data out of Team Foundation Server.  You can do it using the built-in reporting tools, but that seems a bit boring to me.  After the Big Cheese agreed to buy us a super-sized telly to get some project propaganda into the office I decided to whack out a WPF app.

<a href="http://logicalgenetics.com/wp-content/uploads/2012/11/Burndown2.png"><img src="http://logicalgenetics.com/wp-content/uploads/2012/11/Burndown2.png"/></a>

Three useful bits of information worth recording here.  How to get information out of TFS, how to draw nice looking graphs and how to make a borderless, click-anywhere-drag style app without the windows title bar.
## Getting Information From TFS
I'm not going to go into huge depth on how I structured by queries as everyone uses TFS slightly differently.  If you've got the smarts to knock up some TFS queries - and if you don't you might need to learn anyway - then you should be good to go with these code snippets.

Get a list of sprints (iterations).  This one was a bit tricky and took some time to work out!
[sourcecode language="csharp"]
        [Test]
        public void CanGetListOfSprints()
        {
            var tpc = new TfsTeamProjectCollection(tfsUri);
            var workItemStore = (WorkItemStore)tpc.GetService(typeof(WorkItemStore));

            IEnumerable&lt;string&gt; names =
                from Node node in workItemStore.Projects[projectName].IterationRootNodes[releaseName].ChildNodes
                select node.Name;

            Assert.That(names, Is.Not.Empty);
        }
[/sourcecode]

Get a list of stories waiting for signoff by product managers.
[sourcecode language="csharp"]
        [Test]
        public void CanGetStoriesAwaitingProductManagerSignoff()
        {
            var tpc = new TfsTeamProjectCollection(tfsUri);
            var workItemStore = (WorkItemStore)tpc.GetService(typeof(WorkItemStore));

            StringBuilder queryText = new StringBuilder();
            queryText.AppendLine(@&quot;Select [ID], [Title]&quot;);
            queryText.AppendLine(@&quot;From WorkItems&quot;);
            queryText.AppendLine(@&quot;Where&quot;);
            queryText.AppendFormat(&quot; [Team Project] = '{0}'\n&quot;, projectName);
            queryText.AppendLine(@&quot;And&quot;);
            queryText.AppendLine(@&quot; [Work Item Type] = 'Product Backlog Item'&quot;);
            queryText.AppendLine(@&quot;And&quot;);
            queryText.AppendFormat(&quot; [Iteration Path] under '{0}\\{1}'\n&quot;, projectName, releaseName);
            queryText.AppendLine(@&quot;And&quot;);
            queryText.AppendLine(@&quot; [State] = 'Committed'&quot;);
            queryText.AppendLine(@&quot;And&quot;);
            queryText.AppendFormat(@&quot; [Assigned To] in ( {0} )&quot;, productManagers);

            IEnumerable&lt;string&gt; workItemNames = from WorkItem wi in workItemStore.Query(queryText.ToString())
                                                select wi.Title;

            Assert.That(workItemNames, Is.Not.Empty);
        }
[/sourcecode]

## WPF Line Charts

For the charts I used the <a href="http://wpf.codeplex.com/">WPF toolkit</a>.  It's not seen much development in recent years but it's still an easy and quick way to get a chart on screen.  Here are some XAML snippets.  

Here's the chart itself.  Two line series here.  You select the <strong>ItemsSource</strong>: list of objects corresponding to points on the line; <strong>DependentValuePath</strong>: name of a property on the object to use as the value for the Y axis; IndependentValuePath: value/label for the X axis.
[sourcecode language="xml"]
            &lt;chart:Chart x:Name=&quot;TheChart&quot; Width=&quot;1024&quot; Height=&quot;728&quot; Foreground=&quot;White&quot; FontWeight=&quot;Bold&quot; FontSize=&quot;18&quot;
                         Template=&quot;{StaticResource ChartTemplate}&quot;
                         LegendStyle=&quot;{StaticResource InvisibleStyle}&quot; &gt;
                &lt;chart:LineSeries DependentValuePath=&quot;ProjectedBurndown&quot; Foreground=&quot;White&quot; IndependentValuePath=&quot;SprintName&quot; ItemsSource=&quot;{Binding Sprints}&quot; IsSelectionEnabled=&quot;False&quot;
                               PolylineStyle=&quot;{StaticResource ProjectedLineStyle}&quot; DataPointStyle=&quot;{StaticResource ProjectedDataPointStyle}&quot;/&gt;
                &lt;chart:LineSeries DependentValuePath=&quot;ActualBurndown&quot; IndependentValuePath=&quot;SprintName&quot; ItemsSource=&quot;{Binding Sprints}&quot; IsSelectionEnabled=&quot;False&quot;
                               PolylineStyle=&quot;{StaticResource ActualLineStyle}&quot; DataPointStyle=&quot;{StaticResource ActualDataPointStyle}&quot;/&gt;
            &lt;/chart:Chart&gt;
[/sourcecode]

This one is used to hide the legend:
[sourcecode language="xml"]
        &lt;Style x:Key=&quot;InvisibleStyle&quot; TargetType=&quot;Control&quot;&gt;
            &lt;Setter Property=&quot;Width&quot; Value=&quot;0&quot; /&gt;
        &lt;/Style&gt;
[/sourcecode]

Control Template to give better control over the look of the chart area:
[sourcecode language="xml"]
        &lt;ControlTemplate TargetType=&quot;chart:Chart&quot; x:Key=&quot;ChartTemplate&quot;&gt;
            &lt;Border Background=&quot;{TemplateBinding Background}&quot; BorderBrush=&quot;{TemplateBinding BorderBrush}&quot; 
                    BorderThickness=&quot;{TemplateBinding BorderThickness}&quot; Padding=&quot;{TemplateBinding Padding}&quot;&gt;
                &lt;Grid&gt;
                    &lt;primitives:EdgePanel x:Name=&quot;ChartArea&quot; Style=&quot;{TemplateBinding ChartAreaStyle}&quot;&gt;
                        &lt;Grid Canvas.ZIndex=&quot;-1&quot; Style=&quot;{StaticResource PlotAreaStyle}&quot; /&gt;
                    &lt;/primitives:EdgePanel&gt;
                &lt;/Grid&gt;
            &lt;/Border&gt;
        &lt;/ControlTemplate&gt;
[/sourcecode]

Two styles needed to render the lines with no data points and custom colour etc:
[sourcecode language="xml"]

        &lt;Style x:Key=&quot;ProjectedDataPointStyle&quot; TargetType=&quot;Control&quot;&gt;
            &lt;Setter Property=&quot;Width&quot; Value=&quot;0&quot; /&gt;
            &lt;Setter Property=&quot;Background&quot; Value=&quot;LightGray&quot;/&gt;
        &lt;/Style&gt;
        &lt;Style TargetType=&quot;Polyline&quot; x:Key=&quot;ProjectedLineStyle&quot;&gt;
            &lt;Setter Property=&quot;StrokeThickness&quot; Value=&quot;4&quot;/&gt;
            &lt;Setter Property=&quot;StrokeDashArray&quot; Value=&quot;2,1&quot;/&gt;
        &lt;/Style&gt;
[/sourcecode]

## Borderless Click-Drag

This last one was insanely simple but I don't want to forget, so here it is.  All code is in MainWindow.xaml.  Adding these few lines gives you an app with no border that can be dragged by clicking and dragging anywhere.  I also added a double-click-to-toggle-maximised-state feature.  Use Viewboxes libreally in your XAML to make it look nice!

[sourcecode language="csharp"]
public partial class MainWindow 
    {
        public MainWindow()
        {
            DataContext = new BurndownViewModel(new Uri(&quot;http://whatnot/tfs/thingy&quot;));

            InitializeComponent();

            MouseLeftButtonDown += StartDragMove;
            MouseDoubleClick += ToggleWindowState;
        }

        private void ToggleWindowState(object sender, MouseButtonEventArgs e)
        {
            WindowState = (WindowState == WindowState.Maximized) ? WindowState.Normal : WindowState.Maximized;
        }

        private void StartDragMove(object sender, MouseButtonEventArgs e)
        {
            DragMove();
        }

        private void CloseButtonClicked(object sender, RoutedEventArgs e)
        {
            Close();
        }
    }
[/sourcecode]