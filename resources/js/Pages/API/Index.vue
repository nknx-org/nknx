<template>
    <app-layout>
        <ContentWrapper>
            <h1 class="page__title">Account Settings</h1>
            <Navigation :routes="routes" />
            <Grid class="col_12 grid" customHeight>
                <ApiTokenCreate
                    class="grid_a"
                    :available-permissions="availablePermissions"
                    :default-permissions="defaultPermissions"
                /><ApiTokenList
                    v-if="tokens.length"
                    class="grid_b"
                    :tokens="tokens"
                    :available-permissions="availablePermissions"
                />
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
import ApiTokenCreate from "@/Components/Widgets/ApiTokenCreate";
import ApiTokenList from "@/Components/Widgets/ApiTokenList";

export default {
    props: ["tokens", "defaultPermissions", "availablePermissions"],

    components: {
        AppLayout,
        ContentWrapper,
        Grid,
        ApiTokenCreate,
        Navigation,
        ApiTokenList
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
    },
    mounted() {
        console.log(route());
    }
};
</script>
