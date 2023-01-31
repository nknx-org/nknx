<template>
    <div class="control-wrapper" :class="`col_${col}`">
        <label v-if="title" class="control-wrapper__title">{{ title }}</label>
        <div v-if="type === 'row'" class="control-wrapper__row"><slot /></div>
        <slot v-else />
        <div
            v-if="showError"
            class="control-wrapper__error"
            :class="showError ? 'control-wrapper__error_visible' : null"
        >
            {{ errorMsg }}
        </div>
    </div>
</template>

<script>
export default {
    props: {
        title: {
            type: String,
            default: ""
        },
        col: {
            type: String,
            default: "1"
        },
        type: {
            type: String,
            default: "column"
        },
        errorMsg: {
            type: String,
            default: ""
        },
        showError: {
            type: Boolean,
            default: false
        }
    },

    methods: {}
};
</script>
