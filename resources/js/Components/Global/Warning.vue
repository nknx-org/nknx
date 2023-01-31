<template>
    <div class="warning" :class="`warning_state_${type}`">
        <svg-vue
            icon="warning"
            class="warning__icon"
            :class="`warning__icon_state_${type}`"
        />
        <span class="warning__text"><slot></slot></span>
    </div>
</template>

<script>
export default {
    components: {},
    props: {
        type: {
            type: String,
            default: "",
            required: false
        }
    },
    data() {
        return {};
    },
    created() {},
    mounted() {},
    methods: {}
};
</script>
