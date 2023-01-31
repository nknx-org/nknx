<template>
    <svg-vue
        icon="info"
        v-tooltip="{
            content: text,
            placement: 'bottom-center',
            offset: 0
        }"
        class="tooltip-icon"
    ></svg-vue>
</template>

<script>
export default {
    components: {},
    props: {
        text: {
            type: String,
            default: "Description",
            required: true
        }
    },
    data: () => {
        return {};
    },
    mounted() {},
    methods: {}
};
</script>
