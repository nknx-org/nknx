<template>
    <div v-on-clickaway="close" class="select">
        <div
            class="select__button"
            :class="[open ? 'select__button_active' : null]"
            @click="toggleSelect"
        >
            <div class="select__value">{{ itemValue(value) }}</div>
            <svg-vue
                icon="dropDownIcon"
                class="select__toggle"
                :class="open ? 'select__toggle_open' : null"
            />
        </div>

        <div
            class="select__list"
            :class="[
                open ? 'select__list_open' : null,
                `select__list_position_${position}`
            ]"
        >
            <div
                v-for="(item, i) in items"
                :key="i"
                class="select__item"
                @click="selectItem(item)"
            >
                {{ itemValue(item) }}
            </div>
        </div>
    </div>
</template>

<script>
import { mixin as clickaway } from "vue-clickaway";

export default {
    components: {},
    mixins: [clickaway],
    props: {
        value: {
            type: [Object, String, Number],
            default: "",
            required: true
        },
        items: {
            type: Array,
            default: () => []
        },
        itemText: {
            type: String,
            default: ""
        },
        position: {
            type: String,
            default: "bottom"
        }
    },
    data: () => {
        return {
            open: false
        };
    },
    computed: {},
    watch: {},
    mounted() {},
    methods: {
        itemValue(item) {
            return this.itemText.length ? item[this.itemText] : item;
        },
        close() {
            this.open = false;
        },
        toggleSelect() {
            this.open = !this.open;
        },
        selectItem(item) {
            this.$emit("update:value", item);

            this.close();

            if (this.$listeners.change) {
                this.$listeners.change(item);
            }
        }
    }
};
</script>
