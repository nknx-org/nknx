<template>
    <SparklineStats
        title="Total Mined blocks last 3 months"
        :change="change"
        :dailyValue="valueArr"
        symbol="Blocks"
    >
        <DailyMinedChart :nodeMinedHistory="nodeMinedHistory" />

    </SparklineStats>
</template>
<script>
import SparklineStats from "@/Components/Global/SparklineStats";
import DailyMinedChart from "@/Components/Charts/DailyMinedChart";
import HiddenContent from "@/Components/Global/HiddenContent";

export default {
    components: {
        SparklineStats,
        DailyMinedChart,
        HiddenContent
    },
    data: () => {
        return {};
    },
    props: ["nodeMinedHistory"],
    computed: {
        valueArr: function() {
            let totalMined = 0;
            this.nodeMinedHistory.forEach(node => {
                totalMined = totalMined + Number(node.sum);
            });
            return Number(totalMined);
        },
        change: function() {
            const data = this.nodeMinedHistory;
            if (data.length) {
                let day1 = data[data.length - 1].sum;
                let day2 = data[data.length - 2].sum;
                if (day1 > 0 && day2 > 0) {
                    return (((day1 - day2) / day1) * 100).toFixed(2);
                } else {
                    return (day1 * 100).toFixed(2);
                }
            } else {
                return "0";
            }
        }
    },
    destroyed() {},
    mounted: function() {},
    methods: {}
};
</script>
