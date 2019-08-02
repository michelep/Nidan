function draw_net_chart(netid) {
    var jsonData = $.ajax({
	url: '/ajax/?action=net_stats&id='+netid,
	method: 'GET',
	dataType: 'json',
	success: function(result) {
	    render_graph(result);
	}
    });
}

function render_graph(data) {
    var array_data = new Array();
    var array_hosts = new Array();

    for(var i=0,len=data.length;i<len;i++) {
	array_data[i] = data[i].date;
	array_hosts[i] = data[i].hosts;
    }

    var ctx = document.getElementById('net_chart');

    var config = {
        type: 'bar',
        data: {
    	    labels: array_data,
	    datasets: [{
	        label: 'Hosts',
	        backgroundColor: window.chartColors.red,
	        borderColor: window.chartColors.red,
	        data: array_hosts,
		fill: false,
	    }]
	},
	options: {
	    responsive: true,
	    scales: {
		xAxes: [{
		    stacked: true,
		}],
		yAxes: [{
		    stacked: true
		}]
	    }
	}
    }

    var myChart = new Chart(ctx, config);
}
