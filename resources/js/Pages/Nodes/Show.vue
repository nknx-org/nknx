<template>
    <app-layout>
        <div class="page__node">
            <ContentWrapper class="page__node-wrapper">
                <Grid>
                    <NodePanel />
                    <NodeInstallationProgress v-if="node.syncState == 'INSTALLING'">INSTALLING</NodeInstallationProgress>
                    <div v-else >Hello</div>
                </Grid>
            </ContentWrapper>
        </div>
    </app-layout>
</template>

<script>

import AppLayout from "@/Layouts/AppLayout";
import ContentWrapper from "@/Components/Global/ContentWrapper";
import Grid from "@/Components/Global/Grid";
import NodePanel from "@/Components/Widgets/NodePanel";
import NodeInstallationProgress from "@/Components/Widgets/NodeInstallationProgress";

export default {
    props: ["node"],
    components: {
        AppLayout,
        ContentWrapper,
        Grid,
        NodePanel,
        NodeInstallationProgress
    },
    data() {
        return {};
    },

    methods: {
    }
};
</script>
