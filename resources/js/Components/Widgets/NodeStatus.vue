<template>
    <span class="node-status" :class="'node-status_state_' + statusConfig.name">
        <span :class="'node-status__icon fe fe-' + statusConfig.icon"> </span>
        <span class="node-status__text">{{ status }} </span>
    </span>
</template>

<script>
export default {
    components: {},
    props: {
        status: {
            type: String,
            default: "Offline"
        }
    },
    data: () => {
        return {};
    },
    computed: {
        statusConfig() {
            let name = "";
            let icon = "";

            switch (this.status) {
                case "OFFLINE":
                    name = "offline";
                    icon = "cloud-off";
                    break;
                case "INVALID_IPV6":
                    name = "invalid";
                    icon = "x-circle";
                    break;
                case "GENERATE_ID":
                    name = "warning";
                    icon = "alert-triangle";
                    break;
                case "WAIT_FOR_SYNCING":
                    name = "warning";
                    icon = "refresh-cw";
                    break;
                case "SYNC_STARTED":
                    name = "warning";
                    icon = "refresh-cw";
                    break;
                case "SYNC_FINISHED":
                    name = "warning";
                    icon = "refresh-cw";
                    break;
                case "PERSIST_FINISHED":
                    name = "mining";
                    icon = "activity";
                    break;
                case "PRUNING_DB":
                    name = "warning";
                    icon = "download-cloud";
                    break;
                default:
                    return false;
            }

            return {
                name,
                icon
            };
        }
    },
    mounted: function() {},
    methods: {}
};
</script>
