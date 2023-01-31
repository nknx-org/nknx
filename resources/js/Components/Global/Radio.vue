<template>
    <div class="radio">
        <input
            v-model="radioButtonValue"
            class="radio__control"
            type="radio"
            :value="label"
        />
        <label class="radio__label"></label>
        <span
            v-if="showText"
            class="radio__text"
            v-text="labelText ? labelText : label"
        >
        </span>
    </div>
</template>

<script>
export default {
    props: {
        value: {
            type: [String, Number, Object],
            default: "",
            required: true
        },
        label: {
            type: [String, Number, Object],
            default: "",
            required: true
        },
        labelText: {
            type: [String, Number, Object],
            default: "",
            required: false
        },
        showText: {
            type: Boolean,
            default: true
        }
    },
    computed: {
        radioButtonValue: {
            get() {
                return this.value;
            },
            set() {
                this.$emit("change", this.label);
            }
        }
    }
};
</script>
