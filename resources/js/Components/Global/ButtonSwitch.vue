<template>
    <div class="button-switch">
        <div
            class="button-switch__item"
            :class="value === first ? `button-switch__item_active` : null"
            @click="setSelect(first)"
        >
            <span class="button-switch__text">
                {{ first }}
            </span>
        </div>
        <div
            class="button-switch__item"
            :class="value === second ? `button-switch__item_active` : null"
            @click="setSelect(second)"
        >
            <span class="button-switch__text">
                {{ second }}
            </span>
        </div>
    </div>
</template>

<script>
export default {
    components: {},
    props: {
        first: {
            type: [String, Number],
            default: "",
            required: true
        },
        second: {
            type: [String, Number],
            default: "",
            required: true
        },
        value: {
            type: [String, Number],
            default: "",
            required: true
        }
    },
    computed: {},
    methods: {
        setSelect(item) {
            this.$emit("input", item);
        }
    }
};
</script>
