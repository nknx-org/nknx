<template>
    <app-layout>
        <ContentWrapper>
            <h1 class="page__title">Dashboard</h1>
            <Grid>
                <GeneralMiningStats
                    :userPerformanceData="userPerformanceData"
                    :nodeMinedHistory="nodeMinedHistory"
                />
                <GeneralNetworkStats :blockchainData="blockchainSummary" />
                <MediumNews :news="mediumNews" />
                <DailyMined :nodeMinedHistory="nodeMinedHistory" />
                <DailyBalance :walletSumHistory="walletSumHistory" />
                <MarketStats :historyData="historyData" />
            </Grid>
        </ContentWrapper>
    </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
import ContentWrapper from "@/Components/Global/ContentWrapper";
import Grid from "@/Components/Global/Grid";
import MediumNews from "@/Components/Widgets/MediumNews";
import MarketStats from "@/Components/Widgets/MarketStats";
import GeneralNetworkStats from "@/Components/Widgets/GeneralNetworkStats";
import GeneralMiningStats from "@/Components/Widgets/GeneralMiningStats";
import DailyMined from "@/Components/Widgets/DailyMined";
import DailyBalance from "@/Components/Widgets/DailyBalance";

export default {
    props: [
        "mediumNews",
        "blockchainSummary",
        "userPerformanceData",
        "historyData",
        "walletSumHistory",
        "nodeMinedHistory"
    ],
    components: {
        AppLayout,
        ContentWrapper,
        Grid,
        MediumNews,
        MarketStats,
        GeneralNetworkStats,
        GeneralMiningStats,
        DailyMined,
        DailyBalance
    }
};
</script>
