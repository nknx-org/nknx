<template>
    <div ref="chartdiv" class="price-chart" />
</template>

<script>
import * as am4core from "@amcharts/amcharts4/core";
import * as am4charts from "@amcharts/amcharts4/charts";
export default {
    props: ["historyData"],
    mounted() {
        const dailyHistoryPrice = this.historyData; // PRICES API GOES HERE

        console.log(dailyHistoryPrice);

        am4core.options.queue = true;
        am4core.options.onlyShowOnViewport = true;
        const chart = am4core.create(this.$refs.chartdiv, am4charts.XYChart);
        chart.paddingRight = 20;
        let nknUsd = dailyHistoryPrice.prices;
        let nknVolumes = dailyHistoryPrice.total_volumes;
        // nknUsd = nknUsd.slice(0, 30);
        // nknVolumes = nknVolumes.slice(0, 30);
        const nknPrice = [];
        for (let i = nknVolumes.length - 1; i >= 0; i--) {
            nknPrice.push({
                date: this.$moment(nknVolumes[i][0]).toDate(),
                eth: nknVolumes[i][1],
                usd: nknUsd[i][1]
            });
        }
        const data = nknPrice;
        chart.data = data;
        chart.responsive.enabled = true;

        const dateAxis = chart.xAxes.push(new am4charts.DateAxis());
        dateAxis.fontSize = 10;
        dateAxis.startLocation = 0.5;
        dateAxis.endLocation = 0.5;
        dateAxis.dateFormats.setKey("day", "dd/MM");
        dateAxis.periodChangeDateFormats.setKey("day", "dd/MM");
        dateAxis.renderer.line.strokeOpacity = 0.5;
        dateAxis.renderer.line.strokeWidth = 1;
        dateAxis.renderer.line.stroke = am4core.color("#a2a5b9");
        dateAxis.renderer.labels.template.fill = am4core.color("#a2a5b9");
        dateAxis.renderer.grid.template.location = 0.5;
        dateAxis.renderer.grid.template.strokeDasharray = "2";
        const valueAxis = chart.yAxes.push(new am4charts.ValueAxis());
        valueAxis.fontSize = 10;
        valueAxis.renderer.labels.template.fill = am4core.color("#a2a5b9");
        valueAxis.tooltip.disabled = false;
        valueAxis.renderer.minWidth = 35;
        valueAxis.renderer.grid.template.strokeDasharray = "2";
        valueAxis.renderer.line.strokeOpacity = 0.5;
        valueAxis.renderer.line.strokeWidth = 1;
        valueAxis.renderer.line.stroke = am4core.color("#a2a5b9");
        const valueAxis2 = chart.yAxes.push(new am4charts.ValueAxis());
        valueAxis2.fontSize = 10;
        valueAxis2.renderer.labels.template.fill = am4core.color("#a2a5b9");
        valueAxis2.tooltip.disabled = false;
        valueAxis2.renderer.minWidth = 35;
        valueAxis2.renderer.opposite = true;
        valueAxis2.renderer.grid.template.disabled = true;
        const gradient1 = new am4core.LinearGradient();
        gradient1.addColor(am4core.color("#5DB6F3"));
        gradient1.addColor(am4core.color("#5770E1"));
        const series1 = chart.series.push(new am4charts.LineSeries());
        series1.name = "Price";
        series1.dataFields.dateX = "date";
        series1.dataFields.valueY = "usd";
        series1.tooltipText = "{valueY.usd}";
        series1.stroke = gradient1;
        series1.tensionX = 0.8;
        series1.strokeWidth = 2;
        const gradient2 = new am4core.LinearGradient();
        gradient2.addColor(am4core.color("#F6B497"));
        gradient2.addColor(am4core.color("#E16A8B"));
        const series2 = chart.series.push(new am4charts.LineSeries());
        series2.name = "Volume";
        series2.dataFields.dateX = "date";
        series2.dataFields.valueY = "eth";
        series2.yAxis = valueAxis2;
        series2.tooltipText = "{valueY.eth}";
        series2.stroke = gradient2;
        series2.tensionX = 0.8;
        series2.strokeWidth = 2;
        chart.cursor = new am4charts.XYCursor();
        chart.cursor.behavior = "disabled";

        chart.legend = new am4charts.Legend();
        chart.legend.fill = gradient1;
        chart.legend.data[0].fill = gradient1;
        chart.legend.data[1].fill = gradient2;
        chart.legend.useDefaultMarker = true;
        chart.legend.markers.template.height = 10;
        chart.legend.markers.template.width = 10;
        chart.legend.fontSize = 12;
        chart.legend.labels.template.fill = am4core.color("#a2a5b9");
        chart.legend.marginBottom = 20;
        chart.legend.position = "top";
        chart.legend.contentAlign = "right";
        chart.paddingTop = 0;
        chart.seriesContainer.draggable = false;
        chart.seriesContainer.resizable = false;
        chart.maxZoomLevel = 1;
        chart.seriesContainer.events.disableType("doublehit");
        chart.chartContainer.background.events.disableType("doublehit");

        const marker = chart.legend.markers.template.children.getIndex(0);
        marker.cornerRadius(12, 12, 12, 12);
        this.chart = chart;
    },
    beforeDestroy() {
        if (this.chart) {
            this.chart.dispose();
        }
    }
};
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
.price-chart {
    width: 100%;
    height: 220px;
}
</style>
