<template>
    <ModalCard
        v-show="showModal"
        title="2FA Setup"
        subtitle="Two Factor Authentication"
        descr="Add additional security to your account using two factor
            authentication."
        :show.sync="showModal"
    >
        <template v-slot:body>
            <div class="col_6">
                <div v-if="qrCode">
                    <div class="text-sm text-gray-600 mb-4">
                        <p>
                            Two factor authentication is now enabled. Scan the
                            following QR code using your phone's authenticator
                            application.
                        </p>
                    </div>

                    <div
                        class="mt-4 dark:p-4 dark:w-56 dark:bg-white text-center"
                        v-html="qrCode"
                    ></div>
                </div>

                <div v-if="recoveryCodes.length > 0">
                    <div class="mt-4 text-sm text-gray-600">
                        <p class="font-semibold">
                            Store these recovery codes in a secure password
                            manager. They can be used to recover access to your
                            account if your two factor authentication device is
                            lost.
                        </p>
                    </div>

                    <ul
                        class="mt-4 px-4 py-4 font-mono text-sm bg-gray-100 rounded-lg text-center"
                    >
                        <li v-for="code in recoveryCodes" :key="code">
                            {{ code }}
                        </li>
                    </ul>
                </div>
            </div>
        </template>

        <template v-slot:footer>
            <Button size="large" theme="text" @click="closeModal">
                Close
            </Button>
        </template>
    </ModalCard>
</template>

<script>
import FormMixin from "@/Mixins/FormMixin.js";

import Button from "@/Components/Global/Button";
import ModalContent from "@/Components/Global/ModalContent";
import ModalCard from "@/Components/Global/ModalCard";
import ControlWrapper from "@/Components/Global/ControlWrapper";
import Input from "@/Components/Global/Input";

export default {
    props: ["sessions"],
    mixins: [FormMixin],
    components: {
        Button,
        ModalContent,
        ModalCard,
        ControlWrapper,
        Input
    },
    data: function() {
        return {
            qrCode: null,
            recoveryCodes: []
        };
    },
    computed: {},
    watch: {
        showModal(newVal) {
            if (newVal) {
                this.showQrCode();
                this.showRecoveryCodes();
            }
        }
    },
    mounted() {},
    methods: {
        showQrCode() {
            axios.get("/user/two-factor-qr-code").then(response => {
                this.qrCode = response.data.svg;
            });
        },

        showRecoveryCodes() {
            axios.get("/user/two-factor-recovery-codes").then(response => {
                this.recoveryCodes = response.data;
            });
        }
    }
};
</script>
