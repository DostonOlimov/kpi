<!-- Styles -->
<style>
#chartdiv {
  width: 100%;
  height: 1000px;
}

</style>

<!-- Resources -->
<script src="https://cdn.amcharts.com/lib/4/core.js"></script>
<script src="https://cdn.amcharts.com/lib/4/charts.js"></script>
<script src="https://cdn.amcharts.com/lib/4/themes/animated.js"></script>
<script>
    // // Chart
    var data =  {{ Js::from($chart_data) }};
    am4core.ready(function() {

// Themes begin
am4core.useTheme(am4themes_animated);
// Themes end

// Create chart instance
var chart = am4core.create("chartdiv", am4charts.XYChart3D);

// Add data

for (var key in data) {
        data[key].color = chart.colors.next();
    }
    chart.data = data;

var categoryAxis = chart.xAxes.push(new am4charts.CategoryAxis());
categoryAxis.dataFields.category = "year";
categoryAxis.title.text = "Bo'limlarning o'rtacha oylik ko'rsatkichlari";
categoryAxis.renderer.labels.template.visible = false;

var  valueAxis = chart.yAxes.push(new am4charts.ValueAxis());
valueAxis.title.text = "Xodimlar hisobida";

// Create series
var series = chart.series.push(new am4charts.ColumnSeries3D());
series.dataFields.valueY = "income";
series.dataFields.categoryX = "year";
series.name = "ball";
series.tooltipText = "{name}: [bold]{valueY}[/]";

var series2 = chart.series.push(new am4charts.ColumnSeries3D());
series2.dataFields.valueY = "units";
series2.dataFields.categoryX = "year";
series2.name = "Units";
series2.tooltipText = "{name}: [bold]{valueY}[/]";

// Add cursor
chart.cursor = new am4charts.XYCursor();
});
</script>
