<template>
    <ModalWrapper v-show="show">
        <transition name="modal">
            <Card
                v-show="show"
                class="modal-card"
                :subtitle="subtitle"
                :descr="descr"
                :title="title"
                type="modal"
                :style="width.length ? `width: ${width}` : null"
                :modalTemplate="true"
                :overflow="overflow"
            >
                <template v-slot:headerControls>
                    <div class="modal-card__close" @click="close">
                        <svg-vue
                            icon="close"
                            class="modal-card__close-icon"
                        ></svg-vue>
                    </div>
                </template>

                <template v-slot:body>
                    <div v-if="$slots.descr" class="modal-card__descr">
                        <slot name="descr" />
                    </div>
                    <slot name="body" />
                </template>

                <template v-slot:footer>
                    <slot name="footer" />
                </template>
            </Card>
        </transition>
    </ModalWrapper>
</template>

<script>
import Card from "@/Components/Global/Card";
import ModalWrapper from "@/Components/Global/ModalWrapper";

export default {
    components: { Card, ModalWrapper },
    props: {
        title: {
            type: String,
            default: "",
            required: true
        },
        subtitle: {
            type: String,
            default: "",
            required: true
        },
        descr: {
            type: String,
            default: "",
            required: false
        },
        width: {
            type: String,
            default: ""
        },
        show: {
            type: Boolean,
            default: false
        },
        overflow: {
            type: Boolean,
            default: true
        }
    },
    data: () => {
        return {};
    },
    watch: {
        show() {
            this.$store.dispatch("scrollOverflow/toggleScrollOverflow");
        }
    },
    mounted() {},
    methods: {
        close() {
            this.$emit("update:show", false);
        }
    }
};
</script>
