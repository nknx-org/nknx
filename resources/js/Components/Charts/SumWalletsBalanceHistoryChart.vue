<template>
    <div ref="chartdiv" class="wallet-balance-history__chart"></div>
</template>

<script>
import * as am4core from "@amcharts/amcharts4/core";
import * as am4charts from "@amcharts/amcharts4/charts";
import am4themes_animated from "@amcharts/amcharts4/themes/animated";

am4core.useTheme(am4themes_animated);

export default {
    props: ["snapshots"],
    watch: {
        snapshots: function(newVal, oldVal) {
            if (_.isEqual(newVal, oldVal)) return;
            this.drawChart();
        }
    },
    mounted() {
        this.drawChart();
    },
    beforeDestroy() {
        if (this.chart) {
            this.chart.dispose();
        }
    },
    methods: {
        drawChart() {
            am4core.options.autoDispose = true;
            am4core.options.queue = true;
            am4core.options.onlyShowOnViewport = true;
            const chart = am4core.create(
                this.$refs.chartdiv,
                am4charts.XYChart
            );
            let txAverage = [];

            try {
                txAverage = Array.from(this.snapshots);
            } catch {}

            const data = [];

            txAverage.forEach(x => {
                data.push({
                    date: this.$moment(x.date).toDate(),
                    count: x.balance
                });
            });

            chart.data = data;
            chart.seriesContainer.draggable = false;
            chart.seriesContainer.resizable = false;
            chart.maxZoomLevel = 1;
            chart.seriesContainer.events.disableType("doublehit");
            chart.chartContainer.background.events.disableType("doublehit");
            chart.responsive.enabled = true;

            const dateAxis = chart.xAxes.push(new am4charts.DateAxis());
            dateAxis.renderer.minGridDistance = 50;
            dateAxis.renderer.grid.template.location = 0.5;
            dateAxis.startLocation = 0.5;
            dateAxis.endLocation = 0.7;
            dateAxis.cursorTooltipEnabled = true;
            dateAxis.renderer.grid.template.stroke = am4core.color("#757981");
            dateAxis.renderer.line.strokeWidth = 0;
            dateAxis.fontSize = 12;
            dateAxis.fontWeight = 500;
            dateAxis.renderer.labels.template.fill = am4core.color("#757981");
            dateAxis.dateFormats.setKey("day", "dd/MM");
            dateAxis.periodChangeDateFormats.setKey("day", "dd/MM");

            const valueAxis = chart.yAxes.push(new am4charts.ValueAxis());
            valueAxis.min = 0;
            valueAxis.cursorTooltipEnabled = true;
            valueAxis.fontSize = 12;
            valueAxis.fontWeight = 500;
            valueAxis.renderer.labels.template.fill = am4core.color("#757981");
            valueAxis.renderer.minWidth = 35;
            valueAxis.renderer.line.strokeOpacity = 1;
            valueAxis.renderer.line.strokeWidth = 0;
            valueAxis.renderer.grid.template.stroke = am4core.color("#EBEDF1");
            valueAxis.renderer.grid.template.disabled = true;
            valueAxis.tooltip.disabled = false;
            valueAxis.includeRangesInMinMax = true;

            const series = chart.series.push(new am4charts.LineSeries());
            series.dataFields.dateX = "date";
            series.dataFields.valueY = "count";
            series.stroke = am4core.color("#5769DF");
            // series.fill = am4core.color("#5769DF");
            // series.fillOpacity = 0.1;
            series.tensionX = 0.8;
            series.strokeWidth = 2;

            chart.cursor = new am4charts.XYCursor();
            chart.cursor.behavior = "disabled";

            this.chart = chart;
        }
    }
};
</script>
