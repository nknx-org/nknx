<template>
    <Card
        :col="
            $mq === 'xl' ? '4' : $mq === 'llg' ? '6' : $mq === 'lg' ? '6' : '12'
        "
        headerBorder
        title="Two Factor Authentication"
    >
        <ModalContent>
            <template slot="body">
                <div class="col_6">
                    <div
                        class="max-w-xl text-sm text-gray-600 mb-4"
                        v-text="
                            twoFactorEnabled
                                ? 'You have enabled two factor authentication.'
                                : 'You have not enabled two factor authentication.'
                        "
                    ></div>

                    <div
                        class="max-w-xl text-sm text-gray-600 mb-4"
                        v-if="!twoFactorEnabled"
                    >
                        When two factor authentication is enabled, you will be
                        prompted for a secure, random token during
                        authentication. You may retrieve this token from your
                        phone's Google Authenticator application.
                    </div>

                    <div class="max-w-xl text-sm text-gray-600 mb-4" v-else>
                        You can disable 2FA anytime by pressing the button
                        below.
                    </div>
                </div>
            </template>
            <template slot="footer">
                <Button
                    v-if="!twoFactorEnabled"
                    size="large"
                    theme="primary"
                    width="full"
                    @click="openEnablePasswordConfirm"
                    :loading="loading"
                >
                    Enable
                </Button>
                <Button
                    v-else
                    size="large"
                    theme="danger"
                    width="full"
                    :loading="loading"
                    @click="openDisablePasswordConfirm"
                >
                    Disable
                </Button>
            </template>
        </ModalContent>
        <ConfirmPasswordModal
            openModalEvent="openEnablePasswordConfirmModal"
            @onConfirm="enableTwoFactorAuthentication"
        />
        <ConfirmPasswordModal
            openModalEvent="openDisablePasswordConfirmModal"
            @onConfirm="disableTwoFactorAuthentication"
        />
    </Card>
</template>

<script>
import Card from "@/Components/Global/Card";
import Button from "@/Components/Global/Button";
import ModalContent from "@/Components/Global/ModalContent";
import ConfirmPasswordModal from "@/Components/Modals/ConfirmPasswordModal";

export default {
    components: {
        Card,
        Button,
        ModalContent,
        ConfirmPasswordModal
    },
    data: function() {
        return {
            loading: false
        };
    },
    computed: {
        twoFactorEnabled() {
            return this.$page.props.user.two_factor_enabled;
        }
    },
    mounted() {},
    methods: {
        openEnablePasswordConfirm() {
            this.$bus.emit("openEnablePasswordConfirmModal");
        },
        openDisablePasswordConfirm() {
            this.$bus.emit("openDisablePasswordConfirmModal");
        },
        enableTwoFactorAuthentication() {
            this.loading = true;

            this.$inertia.post(
                "/user/two-factor-authentication",
                {},
                {
                    preserveScroll: true,
                    onFinish: () => {
                        this.$bus.emit("openTwoFactorAuthModal");
                        this.loading = false;
                    }
                }
            );
        },
        disableTwoFactorAuthentication() {
            this.loading = true;

            this.$inertia.delete("/user/two-factor-authentication", {
                preserveScroll: true,
                onFinish: () => (this.loading = false)
            });
        }
    }
};
</script>
