<template>
    <div ref="chartdiv" class="price-chart"></div>
</template>

<script>
import * as am4core from "@amcharts/amcharts4/core";
import * as am4charts from "@amcharts/amcharts4/charts";

export default {
    props: ["nodeMinedHistory"],
    mounted() {
        am4core.options.queue = true;
        am4core.options.onlyShowOnViewport = true;
        const chart = am4core.create(this.$refs.chartdiv, am4charts.XYChart);
        // let nodesAverage = this.nodeMinedHistory;
        // const data = [];
        // if (nodesAverage.length) {
        //     for (let i = 0; i < nodesAverage.length; i++) {
        //         data.push({
        //             date: this.$moment(nodesAverage[i].created_at).toDate(),
        //             count: nodesAverage[i].sum
        //         });
        //     }
        // }
        // console.log(data)

        chart.data = this.nodeMinedHistory;
        chart.responsive.enabled = true;

        const dateAxis = chart.xAxes.push(new am4charts.DateAxis());
        dateAxis.renderer.grid.template.disabled = true;
        dateAxis.renderer.labels.template.disabled = true;
        dateAxis.startLocation = 0.5;
        dateAxis.endLocation = 0.5;
        dateAxis.cursorTooltipEnabled = false;
        dateAxis.dateFormats.setKey("day", "dd/MM");
        dateAxis.periodChangeDateFormats.setKey("day", "dd/MM");
        const valueAxis = chart.yAxes.push(new am4charts.ValueAxis());
        valueAxis.min = 0;
        valueAxis.renderer.grid.template.disabled = true;
        valueAxis.renderer.baseGrid.disabled = true;
        valueAxis.renderer.labels.template.disabled = true;
        valueAxis.cursorTooltipEnabled = false;
        const gradient1 = new am4core.LinearGradient();
        gradient1.addColor(am4core.color("#61E786"));
        gradient1.addColor(am4core.color("#2CCB96"));
        const series = chart.series.push(new am4charts.LineSeries());
        series.dataFields.dateX = "created_at";
        series.dataFields.valueY = "sum";
        series.tooltipText = "{valueY.value} NKN";
        series.stroke = gradient1;
        series.fill = gradient1;
        series.fillOpacity = 0.1;
        series.tensionX = 0.8;
        series.strokeWidth = 2;
        chart.cursor = new am4charts.XYCursor();
        chart.cursor.lineY.disabled = true;
        chart.cursor.lineX.disabled = true;
        chart.cursor.behavior = "disabled";

        chart.seriesContainer.draggable = false;
        chart.seriesContainer.resizable = false;
        chart.maxZoomLevel = 1;
        chart.seriesContainer.events.disableType("doublehit");
        chart.chartContainer.background.events.disableType("doublehit");
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
<style lang="scss" scoped>
.price-chart {
    width: 100%;
    height: 120px;
}
</style>
