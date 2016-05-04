/**
 * Changes the chart type of Highcharts
 * @param el
 * @param id
 */
function changeChartType(el, id) {
    data = window["options"+id];
    data.chart.type = jQuery(el).attr("value");
    // Text zentrieren um falsche Anordnung bei zu langer Antwort zu fixen
    // Des Weiten wird die Ausrichtung der % Anzeige geregelt
    if ( jQuery(el).attr("value") != "bar" ){
        data.xAxis.labels.align = "center";
        data.series[0].dataLabels.x = 30;
        data.series[0].dataLabels.x = 0;
    } else {
        data.xAxis.labels.align = "right";
        data.series[0].dataLabels.x = 0;
        data.series[0].dataLabels.y = 30;
    }
    window["options"+id] = data;
    window["chart"+id] = new Highcharts.Chart(window["options"+id]);
    var fnc = window["updateData"+id];  //Use bracket notation to get a reference
    if( fnc && typeof fnc === "function" ) {  //make sure it exists and it is a function
        fnc();  //execute it
    }
}