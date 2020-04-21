function conway(element, resolution) {

    // Equality comparison for elements
    var getKey = function(d) { return d.y * resolution + d.x; }

    var margin = {top: 10, right: 10, bottom: 10, left: 10},
        width = 540 - margin.left - margin.right,
        height = 540 - margin.top - margin.bottom;

    var xScale = d3.scale.linear().range([0, width]).domain([0, resolution]);
    var yScale = d3.scale.linear().range([height, 0]).domain([0, resolution]);

    var circle = d3.select(element).append("svg")
        .attr("width", width + margin.left + margin.right)
        .attr("height", height + margin.top + margin.bottom)
        .append("g")
        .attr("transform", "translate(" + margin.left + "," + margin.top + ")")
        .selectAll("circle");

    var data = [];

    function init() {
        data = [];
        for (var row=0; row<resolution; row++) {
            for (var col=0; col<resolution; col++) {
                var alive = 1;
                if(Math.round(Math.random() * 4) !== 0) { alive = 0; }
                var cell = { x:col, y:row, alive:alive, aliveNext:alive};
                data.push(cell);
            }
        }
    }

    function life() {
        var liveOnes = data.filter(function(n) { return n.alive > 0; });
        data.forEach(function(d){
            var neighbours = liveOnes.filter(function(n) {
                return n.alive == 1 &&
                    n.x >= d.x-1 && n.x <= d.x+1 &&
                    n.y >= d.y-1 && n.y <= d.y+1 &&
                    (n.x != d.x || n.y != d.y)
            });
            var neighbourCount = neighbours.length;

            if(d.alive == 1 && neighbourCount < 2) {
                d.aliveNext = 0;
            }
            else if(d.alive == 1 && neighbourCount > 3) {
                d.aliveNext = 0;
            }
            else if(d.alive === 0 && neighbourCount == 3){
                d.aliveNext = 1;
            }
            else {
                d.aliveNext = d.alive;
            }
        });
        data.forEach(function(d){
            d.alive = d.aliveNext;
        });
    }

    function redraw(drawData) {
        circle = circle.data(drawData, getKey);
        circle
            .enter()
            .append("circle")
            .attr("cx", function(d) {
                return xScale(d.x);
            })
            .attr("cy", function(d) {
                return yScale(d.y);
            })
            .style("fill", "#2ca25f")
            .transition()
                .duration(500)
                .attr("r", (width / resolution) / 2);

        circle.exit()
            .style("fill", "#e41a1c")
            .transition()
                .duration(350)
                .attr("r", 0)
            .remove();
    }

    var lastCount = 0;
    var bored = 20;
    var iterations = 0;
    (function() {
        var liveOnes = data.filter(function(d) { return (d.alive > 0); });

        if(lastCount == liveOnes.length){
            bored--;
        }
        lastCount = liveOnes.length;

        if(liveOnes.length === 0 || bored <= 0 || ++iterations > 6000){
            init();
            lastCount = 0;
            bored = 10;
            iterations = 0;
        }
        else {
            life();
        }

        redraw(liveOnes);

        setTimeout(arguments.callee,100);
    })();

}