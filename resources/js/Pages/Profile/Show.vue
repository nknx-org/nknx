<template>
    <app-layout>
        <ContentWrapper>
            <h1 class="page__title">Account Settings</h1>
            <Navigation :routes="routes" />
            <Grid class="col_12 grid" customHeight>
                <ProfileData class="grid_a" :user="$page.props.user" />
                <ProfilePassword class="grid_b" />
                <ProfileBrowserSessions class="grid_c" :sessions="sessions" />
                <ProfileTwoFactorAuth class="grid_d" />
                <ProfileDelete class="grid_e" />
            </Grid>
        </ContentWrapper>
    </app-layout>
</template>

<style lang="scss" scoped>
@import "@@/scss/app.variables";
@import "@@/scss/app.mixins";

.grid {
    grid-template-areas:
        "a b c"
        "d b e";
    grid-template-columns: 1fr 1fr 1fr;

    @include size-llg {
        grid-template-areas:
            "a b"
            "c b"
            "c d"
            "e e";
        grid-template-columns: 1fr 1fr;
    }

    @include size-lg {
        grid-template-areas:
            "a"
            "b"
            "c"
            "d"
            "e";
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
    &_e {
        grid-area: e;
    }
}
</style>

<script>
import AppLayout from "@/Layouts/AppLayout";
import ContentWrapper from "@/Components/Global/ContentWrapper";
import Grid from "@/Components/Global/Grid";
import Navigation from "@/Components/Global/Navigation";
import ProfileData from "@/Components/Widgets/ProfileData";
import ProfilePassword from "@/Components/Widgets/ProfilePassword";
import ProfileBrowserSessions from "@/Components/Widgets/ProfileBrowserSessions";
import ProfileTwoFactorAuth from "@/Components/Widgets/ProfileTwoFactorAuth";
import ProfileDelete from "@/Components/Widgets/ProfileDelete";

export default {
    props: ["sessions"],

    components: {
        AppLayout,
        ContentWrapper,
        Grid,
        ProfileData,
        ProfilePassword,
        ProfileBrowserSessions,
        ProfileTwoFactorAuth,
        ProfileDelete,
        Navigation
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
    mounted() {}
};
</script>
