<template>
    <SparklineStats
        title="Total Balance"
        :change="change"
        :dailyValue="valueArr"
        symbol="NKN"
    >
        <DailyBalanceChart :walletSumHistory="walletSumHistory" />

    </SparklineStats>
</template>
<script>
import SparklineStats from "@/Components/Global/SparklineStats";
import DailyBalanceChart from "@/Components/Charts/DailyBalanceChart";
import HiddenContent from "@/Components/Global/HiddenContent";

export default {
    components: {
        SparklineStats,
        DailyBalanceChart,
        HiddenContent
    },
    data: () => {
        return {};
    },
    props:['walletSumHistory'],
    computed: {
        change: function() {
          const data = this.walletSumHistory
          if (data.length>1) {
            let day1 = data[data.length-1].sum
            let day2 = data[data.length-2].sum

            if (day1 > 0 && day2 > 0) {
              return (((day1 - day2) / day1) * 100).toFixed(2)
            } else {
              return 0
            }
          } else {
            return '0'
          }
        },
        valueArr: function() {
          return this.walletSumHistory.length
            ? Number(this.walletSumHistory[this.walletSumHistory.length-1].sum).toFixed(2)
            : '0'
        }
    },
    destroyed() {},
    mounted: function() {},
    methods: {}
};
</script>
