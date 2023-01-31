<template>
    <notifications
        group="notifications"
        position="bottom center"
        width="500"
        class="snackbar"
        animation-name="fade-bottom"
    >
        <template slot="body" slot-scope="item">
            <div
                class="snackbar__body"
                :class="`snackbar__body_type_${item.item.type}`"
            >
                {{ item.item.text }}
            </div>
        </template>
    </notifications>
</template>

<script>
export default {
    components: {},
    data() {
        return {};
    }
};
</script>
