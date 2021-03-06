<template>
  <div ref="chartdiv" class="wallet-balance-history__chart"></div>
</template>

<script>
import * as am4core from '@amcharts/amcharts4/core'
import * as am4charts from '@amcharts/amcharts4/charts'
import { mapGetters } from 'vuex'
export default {
  props: {
    dataSet: {
      type: String,
      default: '1week'
    }
  },
  computed: mapGetters({
    activeWallet: 'activeWallet/getActiveWallet'
  }),
  watch: {
    activeWallet: function(newVal, oldVal) {
      this.drawChart()
    }
  },
  mounted() {
    this.drawChart()
  },
  beforeDestroy() {
    if (this.chart) {
      this.chart.dispose()
    }
  },
  methods: {
    drawChart() {
      am4core.options.queue = true
      am4core.options.onlyShowOnViewport = true
      const chart = am4core.create(this.$refs.chartdiv, am4charts.XYChart)
      let txAverage = Array.from(this.activeWallet.wallet_snapshots)
      let timeUnit = 'hour'
      let dayFormat = ''
      if (this.dataSet === '1day') {
        timeUnit = 'hour'
        dayFormat = 'EEE'
        txAverage = txAverage.slice(0, 24)
      } else if (this.dataSet === '3days') {
        timeUnit = 'hour'
        dayFormat = 'EEE'
        txAverage = txAverage.slice(0, 72)
      } else if (this.dataSet === '1week') {
        timeUnit = 'day'
        dayFormat = 'dd/MM'
        txAverage = txAverage.slice(0, 7)
      }

      const data = []
      for (let i = 0; i <= txAverage.length - 1; i++) {
        data.push({
          date: this.$moment(txAverage[i][timeUnit]).toDate(),
          count: txAverage[i].balance
        })
      }
      chart.data = data
      chart.seriesContainer.draggable = false
      chart.seriesContainer.resizable = false
      chart.maxZoomLevel = 1
      chart.seriesContainer.events.disableType('doublehit')
      chart.chartContainer.background.events.disableType('doublehit')

      const dateAxis = chart.xAxes.push(new am4charts.DateAxis())
      dateAxis.renderer.grid.template.disabled = true
      dateAxis.startLocation = 0.4
      dateAxis.endLocation = 0.6
      dateAxis.cursorTooltipEnabled = false
      dateAxis.dateFormats.setKey('day', dayFormat)
      dateAxis.periodChangeDateFormats.setKey('day', dayFormat)
      dateAxis.baseInterval = {
        timeUnit: timeUnit,
        count: 1
      } // hourly
      dateAxis.fontSize = 11
      dateAxis.renderer.labels.template.fill = am4core.color('#A2A5B9')
      const valueAxis = chart.yAxes.push(new am4charts.ValueAxis())
      valueAxis.min = 0
      valueAxis.renderer.baseGrid.disabled = true
      valueAxis.cursorTooltipEnabled = false
      valueAxis.renderer.opposite = true
      valueAxis.fontSize = 11
      valueAxis.renderer.labels.template.fill = am4core.color('#A2A5B9')
      valueAxis.renderer.minWidth = 35
      valueAxis.renderer.grid.template.strokeDasharray = '3'
      valueAxis.renderer.line.strokeOpacity = 0.5
      valueAxis.renderer.line.strokeWidth = 0
      valueAxis.renderer.line.stroke = am4core.color('#a2a5b9')
      const series = chart.series.push(new am4charts.LineSeries())
      series.dataFields.dateX = 'date'
      series.dataFields.valueY = 'count'
      series.tooltipText = '{valueY.value}'
      series.stroke = am4core.color('#5769DF')
      series.fill = am4core.color('#5769DF')
      series.fillOpacity = 0.1
      series.tensionX = 0.8
      series.strokeWidth = 2
      series.tooltip.getFillFromObject = false
      series.tooltip.autoTextColor = false
      series.tooltip.html =
        "<div><div style='font-weight: 300; font-size: 10px;color:#A2A5B9;line-height:normal;margin-bottom:4px;'>{date}</div> <div style='font-size:12px;color:#272634;line-height:normal;'>{count} NKN</div></div>"
      series.tooltip.pointerOrientation = 'vertical'
      chart.cursor = new am4charts.XYCursor()
      chart.cursor.lineY.disabled = true
      chart.cursor.lineX.disabled = true
      chart.cursor.behavior = 'disabled'

      var bullet = series.bullets.push(new am4charts.CircleBullet())
      bullet.stroke = am4core.color('#5769DF')
      bullet.stroke.alpha = 0.5
      bullet.circle.strokeWidth = 8
      bullet.circle.radius = 4
      bullet.circle.fill = am4core.color('#5769DF')
      var bullethover = bullet.states.create('hover')
      bullethover.properties.scale = 1.3
      this.chart = chart
    }
  }
}
</script>
