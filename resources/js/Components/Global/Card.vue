<template>
    <div
        class="card"
        :class="[
            col ? `col_${col}` : null,
            colStart ? `col_start_${colStart}` : null,
            colEnd ? `col_end_${colEnd}` : null,
            row ? `row_${row}` : null,
            padding ? `card_padding_${padding}` : null,
            hover ? 'card_hover' : null,
            overflow ? 'card_overflow' : null
        ]"
    >
        <template v-if="modalTemplate">
            <div class="card__header" :class="`card__header_${type}`">
                <div class="card__header-left">
                    <div v-if="subtitle" class="card__subtitle">
                        {{ subtitle }}
                        <TooltipIcon v-if="descr" :text="descr" />
                    </div>
                    <h3 class="card__title card__title_modal">
                        {{ title }}
                    </h3>
                </div>
                <div v-if="$slots.headerControls" class="card__header-controls">
                    <slot name="headerControls"></slot>
                </div>
            </div>

            <ModalContent class="card__body" footerMargin>
                <template slot="body">
                    <slot name="body"></slot>
                </template>

                <template slot="footer">
                    <slot name="footer"></slot>
                </template>
            </ModalContent>
        </template>

        <div
            v-else-if="title"
            class="card__header"
            :class="headerBorder ? 'card__header_border' : null"
        >
            <h3 v-if="title" class="card__title">
                {{ title }}
                <span v-if="counter" class="card__counter text_wrap_none">{{
                    counter
                }}</span>
            </h3>
            <div v-if="$slots.headerControls" class="card__header-controls">
                <slot name="headerControls"></slot>
            </div>
        </div>
        <slot />
    </div>
</template>

<script>
import ModalContent from "@/Components/Global/ModalContent";
import TooltipIcon from "@/Components/Global/TooltipIcon";

export default {
    components: { ModalContent, TooltipIcon },
    props: {
        col: {
            type: String,
            default: "12"
        },
        colStart: {
            type: String,
            default: "auto"
        },
        colEnd: {
            type: String,
            default: "auto"
        },
        row: {
            type: String,
            default: "1"
        },
        title: {
            type: String,
            default: ""
        },
        descr: {
            type: String,
            default: ""
        },
        subtitle: {
            type: String,
            default: ""
        },
        padding: {
            type: String,
            default: "normal"
        },
        hover: {
            type: Boolean,
            default: false
        },
        overflow: {
            type: Boolean,
            default: true
        },
        headerBorder: {
            type: Boolean,
            default: false
        },
        modalTemplate: {
            type: Boolean,
            default: false
        },
        footerDivider: {
            type: Boolean,
            default: true,
            required: false
        },
        type: {
            type: String,
            default: "card",
            required: false
        },
        counter: {
            type: [String, Number],
            default: "",
            required: false
        }
    },
    data: () => {
        return {};
    },
    mounted: function() {},
    methods: {}
};
</script>
