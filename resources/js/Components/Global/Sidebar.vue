<template>
    <aside class="sidebar" :class="[{ sidebar_expanded: sidebarExpanded }]">
        <svg-vue class="sidebar__logo" icon="logo" />

        <div class="sidebar__nav">
            <inertia-link
                v-for="route in routes"
                :key="route.path"
                :href="projectUrl(route.path)"
                :class="
                    currentRoute(route.path) ? 'sidebar__item_active' : null
                "
                class="sidebar__item"
                @click="
                    $mq === 'xs' || $mq === 'sm' || $mq === 'md'
                        ? toggleSidebar()
                        : false
                "
            >
                <svg-vue class="sidebar__icon" :icon="route.icon" />
            </inertia-link>
        </div>
        <div class="sidebar__bottom">

            <NotificationsButton
                v-if="$mq !== 'xs' && $mq !== 'sm' && $mq !== 'md'"
                class="sidebar__notifications"
            />
            <a
                href="https://discord.gg/HBFXTGG2P4"
                target="_blank"
                class="sidebar__item">
                <svg-vue class="sidebar__icon" :icon="'Discord'" />
            </a>
            <AvatarDropdown class="sidebar__avatar"></AvatarDropdown>
        </div>
    </aside>
</template>

<script>
import AvatarDropdown from "@/Components/Global/AvatarDropdown";
import NotificationsButton from "@/Components/Global/NotificationsButton";

import { mapGetters } from "vuex";
export default {
    components: { AvatarDropdown, NotificationsButton },
    data: () => {
        return {
            routes: [
                {
                    path: "dashboard",
                    icon: "dashboard",
                    title: "Dashboard"
                },
                // {
                //     path: "network.index",
                //     icon: "fe-activity",
                //     title: "Network"
                // },
                {
                    path: "wallets.index",
                    icon: "wallet",
                    title: "Wallets"
                },
                {
                    path: "nodes.index",
                    icon: "nodes",
                    title: "Nodes"
                },
                {
                    path: "fastdeploy.index",
                    icon: "fastDeploy",
                    title: "FastDeploy"
                }
                // {
                //   path: 'profile.show',
                //   icon: 'fe-settings',
                //   title: 'Account Settings'
                // }
            ]
        };
    },
    computed: {
        ...mapGetters({
            sidebarExpanded: "sidebar/get",
            topbarExpanded: "topbar/getTopbar"
        })
    },
    mounted() {},
    methods: {
        projectUrl(page) {
            return route(page);
        },
        currentRoute(page) {
            return route().current(page);
        },
        toggleSidebar() {
            this.$store.dispatch("sidebar/toggleSidebar");
            const burger = document.getElementsByClassName(
                "headerbar__toggle"
            )[0];
            const toggleClass = "arrow";
            burger.classList.toggle(toggleClass);
        }
    }
};
</script>
