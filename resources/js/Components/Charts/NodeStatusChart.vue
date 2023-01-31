<template>
    <div ref="chartdiv" class="node-status__chart" />
</template>

<script>
import * as am4core from "@amcharts/amcharts4/core";
import * as am4charts from "@amcharts/amcharts4/charts";
import am4themes_animated from "@amcharts/amcharts4/themes/animated";

am4core.useTheme(am4themes_animated);

export default {
    props: ["states"],
    components: {},
    data: () => {
        return {};
    },
    watch: {
        states: {
            deep: true,
            handler(newVal, oldVal) {
                if (_.isEqual(newVal, oldVal)) return;
                this.drawChart();
            }
        }
    },
    destroyed() {},
    mounted: function() {
        this.drawChart();
    },
    beforeDestroy() {
        if (this.chart) {
            this.chart.dispose();
        }
    },
    methods: {
        drawChart() {
            am4core.options.queue = true;
            am4core.options.onlyShowOnViewport = true;
            const chart = am4core.create(
                this.$refs.chartdiv,
                am4charts.PieChart
            );
            // Add data
            chart.data = Object.keys(this.states).map(key => {
                if (key !== "ALL") {
                    return {
                        key,
                        value: this.states[key]
                    };
                }
            });
            chart.seriesContainer.draggable = false;
            chart.seriesContainer.resizable = false;
            chart.maxZoomLevel = 1;
            chart.seriesContainer.events.disableType("doublehit");
            chart.chartContainer.background.events.disableType("doublehit");
            chart.responsive.enabled = true;

            chart.legend = new am4charts.Legend();
            chart.legend.useDefaultMarker = true;

            chart.legend.marginTop = "16px";
            chart.legend.labels.template.fontSize = "14px";
            chart.legend.labels.template.fill = "#757981";
            chart.legend.labels.template.fontWeight = 500;
            chart.legend.labels.template.paddingLeft = 8;

            chart.legend.valueLabels.template.disabled = true;

            let markerTemplate = chart.legend.markers.template;
            markerTemplate.width = 14;
            markerTemplate.height = 6;
            markerTemplate.borderRadius = 1000;

            let marker = chart.legend.markers.template.children.getIndex(0);
            marker.cornerRadius(12, 12, 12, 12);
            marker.strokeWidth = 2;
            marker.strokeOpacity = 1;
            marker.stroke = am4core.color("#ccc");

            // Add and configure Series
            var pieSeries = chart.series.push(new am4charts.PieSeries());
            pieSeries.labels.template.disabled = true;
            pieSeries.ticks.template.disabled = true;
            pieSeries.dataFields.value = "value";
            pieSeries.dataFields.category = "key";
            pieSeries.colors.list = [
                am4core.color("#5ce2b2"),
                am4core.color("#b35deb"),
                am4core.color("#f4a271"),
                am4core.color("#7987e5"),
                am4core.color("#5769df"),
                am4core.color("#4654b2"),
                am4core.color("#a2a5b9")
            ];
            pieSeries.tooltip.getFillFromObject = false;
            pieSeries.tooltip.autoTextColor = false;
            pieSeries.tooltip.html =
                "<div style='display:flex;align-items:center; font-size:14px;'><span>{category}</span> <span style='font-weight: bold; margin-left: 8px;'>{value}</span></div>";
            pieSeries.tooltip.label.fill = am4core.color("#272634");
            pieSeries.tooltip.background.fill = am4core.color("#FFFFFF");
            var shadow = pieSeries.tooltip.background.filters.getIndex(0);
            shadow.dx = 0;
            shadow.dy = 1;
            shadow.blur = 6;
            shadow.opacity = 0.1;
            shadow.color = am4core.color("#000");
            // Let's cut a hole in our Pie chart the size of 50% the radius
            chart.innerRadius = am4core.percent(55);
            chart.innerRadius = 100;

            var label = chart.seriesContainer.createChild(am4core.Label);

            label.html =
                "<div style='text-align:center;color:#414348;font-size:42px;'>" +
                this.states.ALL +
                "</div><div style='font-size: 13px;font-weight: 500;color: #757981;text-align: center; line-height: 25px;'>Nodes</div>";
            label.horizontalCenter = "middle";
            label.verticalCenter = "middle";

            this.chart = chart;
        }
    }
};
</script>
