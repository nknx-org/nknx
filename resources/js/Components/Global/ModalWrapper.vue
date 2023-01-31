<template>
    <transition name="modal-wrapper">
        <div class="modal-wrapper">
            <div class="modal-wrapper__container">
                <slot />
            </div>
        </div>
    </transition>
</template>

<script>
export default {
    components: {},
    props: {},
    data: () => {
        return {};
    },
    mounted() {},
    methods: {}
};
</script>
