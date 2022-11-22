import { Bar, mixins } from 'vue-chartjs'
const { reactiveProp } = mixins

export default {
  extends: Bar,
  mixins: [reactiveProp],
  props: ['options'],
  mounted () {
    // this.chartData is created in the mixin.
    // If you want to pass options please create a local options object
    var horizonalLinePlugin = {
              id: 'horizontalLine',
              afterDraw: function(chart) {
                    if (typeof chart.config.options.lineAt != 'undefined') {
                        var lineAt = chart.config.options.lineAt;
                        var ctxPlugin = chart.chart.ctx;
                        var xAxe = chart.scales[chart.config.options.scales.xAxes[0].id];
                        var yAxe = chart.scales[chart.config.options.scales.yAxes[0].id];
                        ctxPlugin.strokeStyle = "red";
                        ctxPlugin.beginPath();
                        lineAt = yAxe.getPixelForValue(lineAt);
                        ctxPlugin.moveTo(xAxe.left, lineAt);
                        ctxPlugin.lineTo(xAxe.right, lineAt);
                        ctxPlugin.stroke();
                    }
              }
      };
    this.addPlugin(horizonalLinePlugin);
    this.renderChart(this.chartData, this.options)
  }
}
