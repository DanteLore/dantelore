function oneDGA(element) {
    var element = element;
    var margin = {top: 10, right: 10, bottom: 16, left: 20};
    var width = 540 - margin.left - margin.right;
    var height = 350 - margin.top - margin.bottom;

    var xScale = d3.scale.linear().range([0, width]);
    var yScale = d3.scale.linear().range([height, 0]);

    var xAxis = d3.svg.axis()
        .scale(xScale)
        .orient("bottom")
        .ticks(10);

    var yAxis = d3.svg.axis()
        .scale(yScale)
        .orient("left")
        .ticks(10);

    var valueLine = d3.svg.line()
        .x(function(d) { return xScale(d.index); })
        .y(function(d) { return yScale(d.value); });

    var svg = d3.select(element).append("p").append("svg")
        .attr("width", width + margin.left + margin.right)
        .attr("height", height + margin.top + margin.bottom)
        .append("g")
        .attr("transform", "translate(" + margin.left + "," + margin.top + ")");

    var circle = svg.selectAll("circle");

    function initialiseChart() {
        svg.append("path")
            .attr("class", "line")
            .attr("d", valueLine(data));

        svg.append("g")
            .attr("class", "x axis")
            .attr("transform", "translate(0," + height + ")")
            .call(xAxis);

        svg.append("g")
            .attr("class", "y axis")
            .call(yAxis);
    }

    function initialiseControls() {
        var holder = d3.select(element)
            .append("div")
                .style("font-family", "courier")
                .style("font-size", "0.8em")
                .style("margin", "10px")
                .style("padding", "10px")
                .style("max-width", "500px")
                .style("background", "#f0f0f0")
                .style("border", "1px solid black");

        holder.append("button")
            .text("Reset")
            .on("click", restartSimulation);

        var p = holder.append("p")
            .text("Population Size: ")
        p.append("input")
            .attr("type", "range")
            .attr("min", "8")
            .attr("max", "64")
            .attr("value", popSize)
            .on("input", function() {
                popSize = +this.value;
                d3.select("#popSize").text(popSize);
            })
        p.append("span")
            .attr("id", "popSize")
            .text(popSize);

        p = holder.append("p")
        p.append("strong").text("Parent Selection:");
        p.append("br");

        p.append("input")
            .attr("type", "radio")
            .attr("name", "findParent")
            .attr("value", "findRandomParent")
            .attr("checked", "false")
            .on("click", function() { findParent = findRandomParent; } );
        p.append("label")
            .text(" Random Parent (single parent selected at random)");

        p.append("br");
        p.append("input")
            .attr("type", "radio")
            .attr("name", "findParent")
            .attr("value", "findBestParent")
            .attr("checked", "true")
            .on("click", function() { findParent = findBestParent; } );
        p.append("label")
            .text(" Best Parent (only the fittest gets to reproduce)");

        p = holder.append("p")
            .text("Mutation distance: ")
        p.append("input")
            .attr("type", "range")
            .attr("min", "10")
            .attr("max", "250")
            .attr("value", mutationRate)
            .on("input", function() {
                mutationRate = +this.value;
                d3.select("#mutationRate").text(mutationRate);
            })
        p.append("span")
            .attr("id", "mutationRate")
            .text(mutationRate);

        p = holder.append("p")
        p.append("strong").text("Selection Algorithm:");
        p.append("br");

        p.append("input")
            .attr("type", "radio")
            .attr("name", "selection")
            .attr("value", "randomSelection")
            .attr("checked", "true")
            .on("click", function() { selection = randomSelection; } );
        p.append("label")
            .text(" Random selection (just kill something at random)");

        p.append("br");
        p.append("input")
            .attr("type", "radio")
            .attr("name", "selection")
            .attr("value", "elitistSelection")
            .attr("checked", "false")
            .on("click", function() { selection = elitistSelection; } );
        p.append("label")
            .text(" Elitist selection (always kill the worst)");

        p.append("br");
        p.append("input")
            .attr("type", "radio")
            .attr("name", "selection")
            .attr("value", "tournamentSelection")
            .attr("checked", "true")
            .on("click", function() { selection = tournamentSelection; } );
        p.append("label")
            .text(" Selection by tournament (kill the worst of two randomly selected individuals)");

        var p = holder.append("p")
            .text("Kill count per generation: ")
        p.append("input")
            .attr("type", "range")
            .attr("min", "1")
            .attr("max", "16")
            .attr("value", killCount)
            .on("input", function() {
                killCount = +this.value;
                d3.select("#killCount").text(killCount);
            })
        p.append("span")
            .attr("id", "killCount")
            .text(killCount);
    }

    var maxY = 0;
    var minY = 0;
    function update(data, population) {
        data.splice(0, data.length - 1000)

        xScale.domain([d3.min(data, function(d) { return d.index; }), d3.max(data, function(d) { return d.index; })]);

        var min = d3.min(data, function(d) { return d.value; });
        var max = d3.max(data, function(d) { return d.value; })

        if(min < minY)
            minY = min;
        if(max > maxY)
            maxY = max;

        yScale.domain([minY, maxY]);

        svg = d3.select(element).transition();

        svg.select(".line")
            .duration(100)
            .attr("d", valueLine(data));
        svg.select(".x.axis")
            .duration(100)
            .call(xAxis);
        svg.select(".y.axis")
            .duration(100)
            .call(yAxis);

        circle = circle.data(population, function(d) { return d.id; });
        circle
            .enter()
            .append("circle")
            .transition()
            .duration(100)
            .attr("cx", function(d) {
                return xScale(d.position);
            })
            .attr("cy", function(d) {
                return yScale(d.fitness);
            })
            .style("fill", "#2ca25f")
            .attr("r", 5);

        circle
            .transition()
            .duration(100)
            .attr("cx", function(d) {
                return xScale(d.position);
            })
            .attr("cy", function(d) {
                return yScale(d.fitness);
            })
            .style("fill", "#2ca25f")
            .attr("r", 5);

        circle.exit()
            .style("fill", "#e41a1c")
            .transition()
                .duration(100)
                .attr("r", 0)
            .remove();
    };

    function fitness(x, t) {
        // Waves:           Freq   Amp          Change Freq
        return (Math.sin(x / 18)  * 1 * Math.cos(t / 47))
             + (Math.sin(x / 27)  * 2 * Math.cos(t / 105))
             + (Math.sin(x / 50)  * 1 * Math.cos(t / 28))
             + (Math.sin(x / 61)  * 1 * Math.sin(t / 33))
             + (Math.sin(x / 187) * 3 * Math.sin(t / 87))
             + (Math.cos(x / 250) * 4 * Math.sin(t / 96))
             + (Math.cos(x / 500) * 4 * Math.sin(t / 255))
    }

    function createPopulation(count) {
        pop = [];
        for(i = 0; i < count; ++i) {
            pop.push({ id: Math.random(), position: Math.floor(Math.random() * 1000), fitness: 0});
        }
        return pop;
    }

    function elitistSelection(pop) {
        var worst = pop.reduce(function(prev, current) {
            return (prev.fitness < current.fitness) ? prev : current
        });

        return pop.filter(function(p) { return p.id != worst.id; })
    }

    function tournamentSelection(pop) {
        var red = pop[Math.floor(Math.random() * pop.length)]
        var blue = pop[Math.floor(Math.random() * pop.length)]

        var loser = (red.fitness < blue.fitness) ? red : blue;

        return pop.filter(function(p) { return p.id != loser.id; })
    }

    function randomSelection(pop) {
        var unlucky = pop[Math.floor(Math.random() * pop.length)]
        return pop.filter(function(p) { return p.id != unlucky.id; })
    }

    function mutate(pos) {
        var r = -Math.log(Math.random()) * mutationRate;
        r = Math.floor(r);
        if(Math.random() >= 0.5) {
            return (pos + r) <= 1000 ? pos + r : pos - r;
        }
        else {
            return (pos - r) >= 0 ? pos - r : pos + r;
        }
    }

    function findBestParent(pop) {
        var best = pop.reduce(function(prev, current) {
            return (prev.fitness > current.fitness) ? prev : current
        });
        return best;
    }

    function findRandomParent(pop) {
        return pop[Math.floor(Math.random() * pop.length)]
    }

    function breed(pop) {
        var parent = findParent(pop);
        var pos = mutate(parent.position);
        var offspring = { id: Math.random(), position: pos, fitness: fitness(pos, t) };
        pop.push(offspring);
        return pop;
    }

    function restartSimulation() {
        population = createPopulation(popSize);
    }

    var popSize = 16;
    var killCount = 1;
    var data = [];
    var population = [];
    var mutationRate = 50;
    var selection = tournamentSelection;
    var findParent = findBestParent;

    t = 0;
    initialiseControls();
    initialiseChart();
    restartSimulation();
    (function() {
        t++;
        data = [];
        for(i = 0; i < 1000; ++i) {
            data.push({index:i, value:fitness(i, t)})
        }

        population.forEach(function(p) { p.fitness = fitness(p.position, t); })

        while(population.length > Math.max(1, popSize - killCount)) {
            population = selection(population);
        }

        while(population.length < popSize) {
            population = breed(population);
        }

        update(data, population);

        setTimeout(arguments.callee, 100);
    })();
};
