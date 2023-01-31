<template>
    <div class="textarea" :class="[errorMsg.length ? 'textarea_error' : null]">
        <textarea
            class="textarea__controller"
            :class="[errorMsg.length ? 'textarea__controller_error' : null]"
            type="text"
            :rows="rows"
            :placeholder="placeholder"
            :value="value"
            :required="required"
            @input="updateValue($event.target.value)"
            @focus="$listeners.focus ? $listeners.focus() : false"
        />
    </div>
</template>

<script>
export default {
    components: {},
    props: {
        rows: {
            type: String,
            default: "5"
        },
        placeholder: {
            type: String,
            default: "Enter your data"
        },
        value: {
            type: String,
            required: true
        },
        required: {
            type: Boolean,
            default: false
        },
        errorMsg: {
            type: String,
            default: ""
        }
    },

    methods: {
        updateValue(value) {
            this.$emit("input", value);
        }
    }
};
</script>
