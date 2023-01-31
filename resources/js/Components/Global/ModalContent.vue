<template>
    <div class="modal-content">
        <div class="modal-content__body">
            <slot name="body" />
        </div>
        <div
            v-if="$slots.footer"
            class="modal-content__footer"
            :class="footerMargin ? 'modal-content__footer_margin' : false"
        >
            <slot name="footer" />
        </div>
    </div>
</template>

<script>
export default {
    props: {
        footerMargin: {
            type: Boolean,
            default: false
        }
    },
    components: {},
    data: function() {
        return {};
    },
    computed: {},
    watch: {},
    mounted() {},
    methods: {}
};
</script>
