<template>
    <app-layout>
        <ContentWrapper>
            <h1 class="page__title">Account Settings</h1>
            <Navigation :routes="routes" />
            <Grid class="col_12 grid" customHeight>
                <VpsKeyCreate class="grid_a" />
                <VpsKeysList class="grid_b" :vps-keys="vpsKeys" />
            </Grid>
        </ContentWrapper>
    </app-layout>
</template>

<style lang="scss" scoped>
@import "@@/scss/app.variables";
@import "@@/scss/app.mixins";

.grid {
    grid-template-areas: "a b b";
    grid-template-columns: 1fr 1fr 1fr;

    @include size-llg {
        grid-template-areas:
            "a a"
            "b b";
        grid-template-columns: 1fr 1fr;
    }

    @include size-lg {
        grid-template-areas:
            "a"
            "b";
        grid-template-columns: 1fr;
    }

    &_a {
        grid-area: a;
    }

    &_b {
        grid-area: b;
    }
}
</style>

<script>
import AppLayout from "@/Layouts/AppLayout";
import ContentWrapper from "@/Components/Global/ContentWrapper";
import Grid from "@/Components/Global/Grid";
import Navigation from "@/Components/Global/Navigation";
import VpsKeyCreate from "@/Components/Widgets/VpsKeyCreate";
import VpsKeysList from "@/Components/Widgets/VpsKeysList";

export default {
    props: ["vps-keys"],

    components: {
        AppLayout,
        ContentWrapper,
        Grid,
        Navigation,
        VpsKeyCreate,
        VpsKeysList
    },
    data: () => {
        return {
            routes: [
                {
                    path: "profile.show",
                    title: "General"
                },
                {
                    path: "user-profile-notifications.index",
                    title: "Notification Settings "
                },
                {
                    path: "vpsKeys.index",
                    title: "VPS Keys"
                },
                {
                    path: "api-tokens.index",
                    title: "API Tokens"
                },
                {
                    path: "sshKeys.index",
                    title: "SSH Keys"
                }
            ]
        };
    }
};
</script>
