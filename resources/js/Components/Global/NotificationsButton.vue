<template>
    <inertia-link href="/notifications" class="notifications-button">
        <svg-vue class="notifications-button__icon" icon="bell" />
        <div v-if="notifications > 0" class="notifications-button__badge">
            {{ notifications }}
        </div>
        <!-- <div
            class="notifications-button__dropdown"
            :class="{ notifications__dropdown_open: open }"
        >
            <div class="notifications-button__dropdown-header">
                <div class="notifications-button__dropdown-title">
                    Notifications
                </div>
                <intertia-link
                    class="notifications-button__dropdown-all"
                    href="/notifications"
                    >View All</intertia-link
                >
            </div>
            <ul class="notifications-button__dropdown-content">
                <li
                    v-if="!notifications.length"
                    class="notifications-button__dropdown-item"
                >
                    You don't have any notifications
                </li>
                <li v-else v-for="item in notifications" :key="item.id">
                    <div class="notifications-button__dropdown-item-header">
                        <div class="notifications-button__dropdown-item-title">
                            {{ item.data.header }}
                        </div>
                        <div class="notifications-button__dropdown-item-date">
                            {{ $moment(item.created_at).fromNow() }}
                        </div>
                    </div>
                    <div class="notifications-button__dropdown-item-text">
                        {{ item.data.excerpt }}
                    </div>
                </li>
            </ul>
        </div> -->
    </inertia-link>
</template>

<script>
export default {
    components: {},
    data: () => {
        return {
            open: true
        };
    },
    computed: {
        notifications() {
            return this.$page.props.newNotifications;
        }
    },
    mounted() {},
    methods: {}
};
</script>
