<template>
    <div class="divider" :class="`divider_type_${type}`"></div>
</template>

<script>
export default {
    components: {},
    props: {
        type: {
            type: String,
            default: "vertical"
        }
    },
    data: () => {
        return {};
    },
    mounted() {},
    methods: {}
};
</script>
