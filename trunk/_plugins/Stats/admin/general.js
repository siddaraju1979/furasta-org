/**
 * Stats Plugin, Furasta.Org
 *
 * @author	Conor Mac Aoidh <conormacaoidh@gmail.com>
 * @version	1
 */

$(function(){
	$( '#tabs' ).tabs( );

	chart_line_total( 'today-total' );
	chart_bar( 'today-browser' );
	
});

function chart_bar( id ){
	$( '#' + id ).html( '<p>Loading.. <img src="' + window.furasta.site.url + '_inc/img/loading.gif"/></p>' );
	$.ajax({
		url : window.furasta.site.url + 'ajax.php?file=_plugins/Stats/admin/data.php&query=' + id,
		dataType : 'json',
		success : function( data ){
			var browser_chart = new Highcharts.Chart({
				chart: {
					renderTo: id,
					plotBackgroundColor: null,
					plotBorderWidth: null,
					plotShadow: false
				},
				title: {
					text: data.title
				},
				tooltip: {
					formatter: function() {
						return '<b>'+ this.point.name +'</b>: '+ this.percentage +' %';
					}
				},
				plotOptions: {
					pie: {
						allowPointSelect: true,
						cursor: 'pointer',
						dataLabels: {
							enabled: false
						},
						showInLegend: true
					}
				},
				series: [{
					type: 'pie',
					name: 'Browser share',
					data: [
						['Firefox',   45.0],
						['IE',       26.8],
						['Safari',    8.5],
						['Opera',     6.2],
						['Others',   0.7]
					]
				}]
			}
		)}

	});
}

function chart_line_total( id ){
	$( '#' + id ).html( '<p>Loading.. <img src="' + window.furasta.site.url + '_inc/img/loading.gif"/></p>' );
	$.ajax({
		url : window.furasta.site.url + 'ajax.php?file=_plugins/Stats/admin/data.php&query=' + id,
		dataType : 'json',
		success : function( data ){
			var chart = new Highcharts.Chart({
				chart: {
					renderTo: id,
					type: 'line',
					marginRight: 0,
					marginBottom: 20
				},
				title: {
					text: data.title,
					x: -20 //center
				},
				xAxis: {
					categories: data.categories
				},
				yAxis: {
					title: {
						text: 'Page Views'
					},
					plotLines: [{
						value: 0,
						width: 1,
						color: '#808080'
					}]
				},
				tooltip: {
					formatter: function() {
						return '<b>'+ this.series.name +'</b><br/>'+
						this.x +': '+ this.y;
					}
				},
				series: data.views
			});
		}
	});

}
