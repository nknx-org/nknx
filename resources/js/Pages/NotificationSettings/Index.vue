<template>
    <app-layout>
        <ContentWrapper>
            <h1 class="page__title">Account Settings</h1>
            <Navigation :routes="routes" />
            <Grid class="col_12 ">
                <ProfileNotifications />
            </Grid>
        </ContentWrapper>
    </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
import ContentWrapper from "@/Components/Global/ContentWrapper";
import Grid from "@/Components/Global/Grid";
import Navigation from "@/Components/Global/Navigation";
import ProfileNotifications from "@/Components/Widgets/ProfileNotifications";

export default {
    props: ["sessions"],

    components: {
        AppLayout,
        ContentWrapper,
        Grid,
        ProfileNotifications,
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
