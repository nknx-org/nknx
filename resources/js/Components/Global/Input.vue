<template>
    <div
        class="input"
        :class="[
            readonly ? 'input_readonly' : null,
            disabled ? 'input_disabled' : null,
            errorMsg.length ? 'input_error' : null,
            multiple ? 'input_multiple' : null
        ]"
    >
        <input
            class="input__controller"
            :class="[
                after ? 'input__controller_after' : null,
                $slots.inputIcon ? 'input__controller_icon' : null,
                `input__controller_size_${size}`,
                readonly ? 'input__controller_readonly' : null,
                disabled ? 'input__controller_disabled' : null,
                errorMsg.length ? 'input__controller_error' : null,
                `input__controller_size_${size}`
            ]"
            :id="id"
            :type="type"
            :placeholder="placeholder"
            :value="value"
            :required="required"
            :readonly="readonly"
            :autofocus="autofocus"
            :disabled="disabled"
            @focus="$listeners.focus ? $listeners.focus() : false"
            @input="updateValue($event.target.value)"
            @keydown="type === 'number' ? numFilter($event) : false"
        />
        <div v-if="type === 'number'" class="input__actions">
            <span class="input__actions-icon fe fe-chevron-up" @click="inc" />
            <span class="input__actions-icon fe fe-chevron-down" @click="dec" />
        </div>
        <div v-if="after" class="input__after">{{ after }}</div>
        <div v-if="$slots.inputIcon" class="input__icon">
            <slot name="inputIcon" />
        </div>
    </div>
</template>

<script>
export default {
    components: {},
    props: {
        type: {
            type: String,
            default: "text"
        },
        id: {
            type: [Object, String],
            default: null
        },
        size: {
            type: String,
            default: "default"
        },
        placeholder: {
            type: String,
            default: "Enter your data"
        },
        value: {
            type: [String, Number],
            default: ""
        },
        required: {
            type: Boolean,
            default: false
        },
        autofocus: {
            type: Boolean,
            default: false
        },
        readonly: {
            type: Boolean,
            default: false
        },
        disabled: {
            type: Boolean,
            default: false
        },
        after: {
            type: String,
            default: ""
        },
        size: {
            type: String,
            default: ""
        },
        errorMsg: {
            type: String,
            default: ""
        },
        multiple: {
            type: Boolean,
            default: false
        }
    },
    data: () => {
        return {
            change: () => {}
        };
    },
    mounted() {
        if (this.$listeners.change) {
            this.change = this._.debounce(() => {
                this.$listeners.change();
            }, 1000);
        }
    },
    methods: {
        inc() {
            this.$emit("input", this.value + 1);
        },
        dec() {
            this.$emit("input", this.value - 1);
        },
        numFilter(e) {
            if ([69, 187, 189, 38, 40].includes(e.keyCode)) {
                e.preventDefault();
            }
        },
        updateValue(value) {
            this.$emit("input", value);

            if (this.$listeners.change) {
                this.change();
            }
        }
    }
};
</script>
