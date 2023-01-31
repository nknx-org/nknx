<template>
    <component
        :is="tag"
        class="button"
        :class="[
            `button_theme_${theme}`,
            `button_size_${size}`,
            `button_radius_${radius}`,
            `button_width_${width}`,
            loading ? 'button_loading' : null,
            success ? 'button_success' : null,
            iconOnly ? 'button_icon-only' : null,
            inline ? 'button_inline' : null
        ]"
        :href="href"
        :disabled="disabled"
        v-on="$listeners"
    >
        <template v-if="!loading && !success">
            <svg-vue
                v-if="icon"
                :icon="icon"
                class="button__icon"
                :class="[iconOnly ? 'button__icon_icon-only' : null]"
            ></svg-vue>
            <slot />
        </template>

        <SimpleLoader
            v-if="loading"
            name="ball-pulse"
            :scale="0.5"
            class="button__action button__action_loading"
            color="white"
        />

        <svg-vue icon="checks" v-if="success" class="button__action" />
    </component>
</template>

<script>
import SimpleLoader from "@/Components/Loaders/SimpleLoader";

export default {
    components: {
        SimpleLoader
    },
    props: {
        tag: {
            type: String,
            default: "button"
        },
        icon: {
            type: String,
            default: ""
        },
        disabled: {
            type: Boolean,
            default: false
        },
        inline: {
            type: Boolean,
            default: false
        },
        theme: {
            type: String,
            default: "primary"
        },
        radius: {
            type: String,
            default: "medium"
        },
        size: {
            type: String,
            default: "large"
        },
        width: {
            type: String,
            default: "default"
        },
        loading: {
            type: Boolean,
            default: false
        },
        success: {
            type: Boolean,
            default: false
        },
        href: {
            type: String,
            default: ""
        },
        iconOnly: {
            type: Boolean,
            default: false
        }
    },
    data() {
        return {};
    },
    watch: {
        success(newVal) {
            if (newVal === true) {
                this._.delay(() => {
                    this.$emit("update:success", false);
                }, 1000);
            }
        }
    },
    methods: {}
};
</script>
