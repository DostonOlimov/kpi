<!-- Styles -->
<style>
#chartdiv {
  width: 100%;
  height: 700px;
}
</style>

<!-- Resources -->
<script src="https://cdn.amcharts.com/lib/4/core.js"></script>
<script src="https://cdn.amcharts.com/lib/4/charts.js"></script>
<script src="https://cdn.amcharts.com/lib/4/themes/animated.js"></script>
<script>
    var data =  {{ Js::from($data) }};

am4core.ready(function() {

// Themes begin
am4core.useTheme(am4themes_animated);
// Themes end

var chart = am4core.create("chartdiv", am4charts.PieChart3D);
chart.hiddenState.properties.opacity = 0; // this creates initial fade-in

chart.legend = new am4charts.Legend();
 
chart.data = data;


var series = chart.series.push(new am4charts.PieSeries3D());
series.dataFields.value = "ball";
series.dataFields.category = "name";

}); // end am4core.ready()

</script>
