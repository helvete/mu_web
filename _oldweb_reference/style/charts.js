$(function () { 
    $('#container').highcharts({
        chart: {
            type: 'bar'
        },
        title: {
            text: 'Reset Speed'
        },
        xAxis: {
            categories: ['Apples', 'Bananas', 'Oranges']
        },
        yAxis: {
            title: {
                text: 'Days'
            }
        },
        series: [{
            data: serdat
        }]
    });
});