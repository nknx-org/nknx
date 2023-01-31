<template>
    <div class="nodes-warning">
        <span class="nodes-warning__icon fe fe-alert-triangle"></span>
        <div class="nodes-warning__right">
            <div class="nodes-warning__title">{{ title }}</div>
            <div class="nodes-warning__text">
                <slot />
            </div>
        </div>
    </div>
</template>

<script>
export default {
    components: {},
    props: {
        title: {
            type: String,
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
