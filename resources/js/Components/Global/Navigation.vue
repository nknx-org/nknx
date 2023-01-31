<template>
    <div class="navigation">
        <inertia-link
            v-for="route in routes"
            :key="route.path"
            :href="projectUrl(route.path)"
            :class="currentRoute(route.path) ? 'navigation__item_active' : null"
            class="navigation__item"
        >
            {{ route.title }}
        </inertia-link>
    </div>
</template>

<script>
export default {
    components: {},
    props: {
        routes: {
            type: Array,
            default: () => []
        }
    },
    data() {
        return {};
    },
    created() {},
    mounted() {},
    methods: {
        currentRoute(page) {
            return route().current(page);
        },
        projectUrl(page) {
            return route(page);
        }
    }
};
</script>
