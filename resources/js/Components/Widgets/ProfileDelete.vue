<template>
    <Card
        :col="
            $mq === 'xl' ? '4' : $mq === 'llg' ? '6' : $mq === 'lg' ? '6' : '12'
        "
        headerBorder
        title="Danger Area"
    >
        <ModalContent>
            <template slot="body">
                <div class="col_6">
                    <div class="max-w-xl text-sm text-gray-600 mb-4">
                        For us it is very important to keep your private data
                        safe! If you don’t want to use NKNx anymore you can
                        delete your account entirely. This will remove all your
                        account data, including nodes, wallets, regular
                        snapshots, everything!
                    </div>
                </div>
            </template>
            <template slot="footer">
                <Button
                    size="large"
                    theme="danger"
                    width="full"
                    @click="openModal"
                >
                    Delete My Account
                </Button>
            </template>
        </ModalContent>
    </Card>
</template>

<script>
import Card from "@/Components/Global/Card";
import Button from "@/Components/Global/Button";
import ModalContent from "@/Components/Global/ModalContent";

export default {
    components: {
        Card,
        Button,
        ModalContent
    },
    data: function() {
        return {};
    },
    computed: {},
    mounted() {},
    methods: {
        openModal() {
            this.$bus.emit("openDeleteAccountConfirmModal");
        }
    }
};
</script>
