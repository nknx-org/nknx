<template>
    <ModalCard
        v-show="showModal"
        :title="activeWallet.address"
        subtitle="Receive NKN"
        descr="Use this qr code for payments or transfers"
        :show.sync="showModal"
        width="720px"
    >
        <template v-slot:descr>
            <vueQr
                :value="activeWallet.address"
                :size="250"
                style="text-align: center; margin-bottom: 16px;"
            ></vueQr>
            This is the QR Code for your wallet. Scan it for quickly getting
            your wallet address with your camera!
        </template>

        <template v-slot:footer>
            <Button size="large" theme="text" @click="closeModal">
                Cancel
            </Button>
        </template>
    </ModalCard>
</template>

<script>
import FormMixin from "@/Mixins/FormMixin.js";

import { mapState } from "vuex";

import Button from "@/Components/Global/Button";
import ModalContent from "@/Components/Global/ModalContent";
import ModalCard from "@/Components/Global/ModalCard";
import vueQr from "qrcode.vue";

export default {
    mixins: [FormMixin],
    components: {
        Button,
        ModalContent,
        ModalCard,
        vueQr
    },
    data() {
        return {};
    },
    computed: {
        ...mapState({
            activeWallet: state => state.wallets.activeWallet
        })
    },
    mounted() {},
    methods: {}
};
</script>
