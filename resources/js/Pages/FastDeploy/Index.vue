<template>
    <app-layout>
        <ContentWrapper>
            <h1 class="page__title">NKNx FastDeploy</h1>

            <Grid class="col_12 grid" customHeight>
                <FdConfigCreate class="grid_a" />
                <FdConfigList
                    class="grid_b"
                    :fd_configurations="fd_configurations"
                />
                <FdChoice class="grid_c" :vps_keys="vps_keys" />
                <FdEventsList class="grid_d" :fd_events="fd_events" />
            </Grid>
        </ContentWrapper>
    </app-layout>
</template>

<style lang="scss" scoped>
@import "@@/scss/app.variables";
@import "@@/scss/app.mixins";

.grid {
    grid-template-areas:
        "a b b"
        "c c c"
        "d d d";
    grid-template-columns: 1fr 1fr 1fr;

    @include size-llg {
        grid-template-areas:
            "a a"
            "b b"
            "c c"
            "d d";
        grid-template-columns: 1fr 1fr;
    }

    @include size-lg {
        grid-template-areas:
            "a"
            "b"
            "c"
            "d";
        grid-template-columns: 1fr;
    }

    &_a {
        grid-area: a;
    }

    &_b {
        grid-area: b;
    }

    &_c {
        grid-area: c;
    }

    &_d {
        grid-area: d;
    }
}
</style>

<script>
import { mapMutations } from "vuex";

import AppLayout from "@/Layouts/AppLayout";
import ContentWrapper from "@/Components/Global/ContentWrapper";
import Grid from "@/Components/Global/Grid";
import FdConfigCreate from "@/Components/Widgets/FdConfigCreate";
import FdConfigList from "@/Components/Widgets/FdConfigList";
import FdEventsList from "@/Components/Widgets/FdEventsList";
import FdChoice from "@/Components/Widgets/FdChoice";

export default {
    props: ["fd_configurations", "fd_events", "vps_keys", "ssh_keys"],
    components: {
        AppLayout,
        ContentWrapper,
        Grid,
        FdConfigCreate,
        FdConfigList,
        FdEventsList,
        FdChoice
    },
    data() {
        return {};
    },
    methods: {
        ...mapMutations({
            setSsh: "fdConfig/setSsh"
        })
    },
    mounted() {
        this.setSsh(this.ssh_keys.length > 0);
    }
};
</script>
