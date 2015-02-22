$(document).ready(function ($) {
    charts();
});

function chart()
			{
				/* ---------- Chart with points ---------- */
				if($("#stats-chart2").length)
				{
					var pageviews = [[1, 5+randNum()], [1.5, 10+randNum()], [2, 15+randNum()], [2.5, 20+randNum()],[3, 25+randNum()],[3.5, 30+randNum()],[4, 35+randNum()],[4.5, 40+randNum()],[5, 45+randNum()],[5.5, 50+randNum()],[6, 55+randNum()],[6.5, 60+randNum()],[7, 65+randNum()],[7.5, 70+randNum()],[8, 75+randNum()],[8.5, 80+randNum()],[9, 85+randNum()],[9.5, 90+randNum()],[10, 85+randNum()],[10.5, 80+randNum()],[11, 75+randNum()],[11.5, 80+randNum()],[12, 75+randNum()],[12.5, 70+randNum()],[13, 65+randNum()],[13.5, 75+randNum()],[14,80+randNum()],[14.5, 85+randNum()],[15, 90+randNum()], [15.5, 95+randNum()], [16, 5+randNum()], [16.5, 15+randNum()], [17, 15+randNum()], [17.5, 10+randNum()], [18, 15+randNum()], [18.5, 20+randNum()],[19, 25+randNum()],[19.5, 30+randNum()],[20, 35+randNum()],[20.5, 40+randNum()],[21, 45+randNum()],[21.5, 50+randNum()],[22, 55+randNum()],[22.5, 60+randNum()],[23, 65+randNum()],[23.5, 70+randNum()],[24, 75+randNum()],[24.5, 80+randNum()],[25, 85+randNum()],[25.5, 90+randNum()],[26, 85+randNum()],[26.5, 80+randNum()],[27, 75+randNum()],[27.5, 80+randNum()],[28, 75+randNum()],[28.5, 70+randNum()],[29, 65+randNum()],[29.5, 75+randNum()],[30,80+randNum()]];
					var visits = [[1, randNum2()-10], [2, randNum2()-10], [3, randNum2()-10], [4, randNum2()],[5, randNum2()],[6, 4+randNum2()],[7, 5+randNum2()],[8, 6+randNum2()],[9, 6+randNum2()],[10, 8+randNum2()],[11, 9+randNum2()],[12, 10+randNum2()],[13,11+randNum2()],[14, 12+randNum2()],[15, 13+randNum2()],[16, 14+randNum2()],[17, 15+randNum2()],[18, 15+randNum2()],[19, 16+randNum2()],[20, 17+randNum2()],[21, 18+randNum2()],[22, 19+randNum2()],[23, 20+randNum2()],[24, 21+randNum2()],[25, 14+randNum2()],[26, 24+randNum2()],[27,25+randNum2()],[28, 26+randNum2()],[29, 27+randNum2()], [30, 31+randNum2()]];
					var visitors = [[1, 5+randNum3()], [2, 10+randNum3()], [3, 15+randNum3()], [4, 20+randNum3()],[5, 25+randNum3()],[6, 30+randNum3()],[7, 35+randNum3()],[8, 40+randNum3()],[9, 45+randNum3()],[10, 50+randNum3()],[11, 55+randNum3()],[12, 60+randNum3()],[13, 65+randNum3()],[14, 70+randNum3()],[15, 75+randNum3()],[16, 80+randNum3()],[17, 85+randNum3()],[18, 90+randNum3()],[19, 85+randNum3()],[20, 80+randNum3()],[21, 75+randNum3()],[22, 80+randNum3()],[23, 75+randNum3()],[24, 70+randNum3()],[25, 65+randNum3()],[26, 75+randNum3()],[27,80+randNum3()],[28, 85+randNum3()],[29, 90+randNum3()], [30, 95+randNum3()]];
					var newVisitors = [[1, randNum4()-10], [2, randNum4()-10], [3, randNum4()-10], [4, randNum4()],[5, randNum4()],[6, 4+randNum4()],[7, 5+randNum4()],[8, 6+randNum4()],[9, 6+randNum4()],[10, 8+randNum4()],[11, 9+randNum4()],[12, 10+randNum4()],[13,11+randNum4()],[14, 12+randNum4()],[15, 13+randNum4()],[16, 14+randNum4()],[17, 15+randNum4()],[18, 15+randNum4()],[19, 16+randNum4()],[20, 17+randNum4()],[21, 18+randNum4()],[22, 19+randNum4()],[23, 20+randNum4()],[24, 21+randNum4()],[25, 14+randNum4()],[26, 24+randNum4()],[27,25+randNum4()],[28, 26+randNum4()],[29, 27+randNum4()], [30, 31+randNum4()]];

					console.log(pageviews);
					console.log(visits);
					console.log(visitors);
					console.log(newVisitors);

					var plot = $.plot($("#stats-chart2"),
							[ { data: visitors,
								label: "KW",
								lines: { show: true,
									fill: false,
									lineWidth: 2
								},
								shadowSize: 0
							}, {
								data: pageviews,
								bars: { show: true,
									fill: false,
									barWidth: 0.1,
									align: "center",
									lineWidth: 5,
								}
							}
							], {

								grid: { hoverable: true,
									clickable: true,
									tickColor: "rgba(255,255,255,0.05)",
									borderWidth: 0
								},
								legend: {
									show: false
								},
								colors: ["rgba(255,255,255,0.8)", "rgba(255,255,255,0.6)", "rgba(255,255,255,0.4)", "rgba(255,255,255,0.2)"],
								xaxis: {ticks:15, tickDecimals: 0, color: "rgba(255,255,255,0.8)" },
								yaxis: {ticks:5, tickDecimals: 0, color: "rgba(255,255,255,0.8)" },
							});

					/*
					 [ { data: visitors, label: "Visits"}], {
					 series: {
					 lines: { show: true,
					 lineWidth: 2
					 },
					 points: { show: true,
					 lineWidth: 2
					 },
					 shadowSize: 0
					 },
					 grid: { hoverable: true,
					 clickable: true,
					 tickColor: "rgba(255,255,255,0.025)",
					 borderWidth: 0
					 },
					 legend: {
					 show: false
					 },
					 colors: ["rgba(255,255,255,0.8)", "rgba(255,255,255,0.6)", "rgba(255,255,255,0.4)", "rgba(255,255,255,0.2)"],
					 xaxis: {ticks:15, tickDecimals: 0},
					 yaxis: {ticks:5, tickDecimals: 0},
					 });
					 */



					function showTooltip(x, y, contents) {
						$('<div id="tooltip">' + contents + '</div>').css( {
							position: 'absolute',
							display: 'none',
							top: y + 5,
							left: x + 5,
							border: '1px solid #fdd',
							padding: '2px',
							'background-color': '#dfeffc',
							opacity: 0.80
						}).appendTo("body").fadeIn(200);
					}

					var previousPoint = null;
					$("#stats-chart2").bind("plothover", function (event, pos, item) {
						$("#x").text(pos.x.toFixed(2));
						$("#y").text(pos.y.toFixed(2));

						if (item) {
							if (previousPoint != item.dataIndex) {
								previousPoint = item.dataIndex;

								$("#tooltip").remove();
								var x = item.datapoint[0].toFixed(2),
										y = item.datapoint[1].toFixed(2);

								showTooltip(item.pageX, item.pageY,
										item.series.label + " of " + x + " = " + y);
							}
						}
						else {
							$("#tooltip").remove();
							previousPoint = null;
						}
					});

				}

				

				/* ---------- Chart with points ---------- */
				$('.flot-y1-axis').css('color','#ccc');
				$('.flot-x1-axis').css('color','#ccc');

}

function randNumFB(){
   return ((Math.floor( Math.random()* (1+40-20) ) ) + 20);
}