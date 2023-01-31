<template>
    <div v-show="isActive" class="tab">
        <ModalContent>
            <template slot="body">
                <slot></slot>
            </template>
        </ModalContent>
    </div>
</template>

<script>
import ModalContent from "@/Components/Global/ModalContent";

export default {
    components: { ModalContent },
    props: {
        title: {
            type: String,
            default: "Tab"
        }
    },
    data() {
        return {
            isActive: true
        };
    }
};
</script>
