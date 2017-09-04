function drawLineChart(netid) {
    var jsonData = $.ajax({
	url: '/ajaxCb.php?action=getNetStats&id='+netid,
	method: 'GET',
	dataType: 'json',
	success: function(result) {
	    renderGraph(result.labels,result.points);
	}
    });
}

function renderGraph(labels, points) {
    ctx = document.getElementById("netChart").getContext("2d");

    var lineChartData = {
	labels: labels,
        datasets: [{
            label: "Views",
            fillColor: "rgba(220,220,220,0.2)",
            strokeColor: "rgba(220,220,220,1)",
            pointColor: "rgba(220,220,220,1)",
            pointStrokeColor: "#fff",
            pointHighlightFill: "#fff",
            pointHighlightStroke: "rgba(220,220,220,1)",
            data: points,
        }]
    };

    var lineChartOptions = {
    };

    var myChart = new Chart(ctx, {
	type: 'line',
	data: lineChartData,
	options: lineChartOptions
    });
}
