<template>
    <ModalCard
        v-show="showModal"
        title="Delete Account"
        subtitle="Account Settings"
        descr="Descr"
        :show.sync="showModal"
    >
        <template v-slot:descr>
            Yes, I want to delete all of my account data - including nodes,
            wallets, regular snapshots and all the other stuff from NKNx.
        </template>
        <template v-slot:body>
            <ControlWrapper
                title="Password"
                col="6"
                :errorMsg="form.errors.password"
                showError
            >
                <Input
                    v-model="form.password"
                    :errorMsg="form.errors.password"
                    placeholder="Enter your password"
                    @focus="clearErrors"
                    type="password"
                />
            </ControlWrapper>
        </template>

        <template v-slot:footer>
            <Button size="large" theme="text" @click="closeModal">
                Cancel
            </Button>
            <Button
                size="large"
                theme="primary"
                :success.sync="success"
                :loading="form.processing"
                @click="submit"
            >
                Confirm
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
            form: this.$inertia.form({
                password: ""
            })
        };
    },
    computed: {},
    mounted() {},
    methods: {
        submit() {
            this.form.delete(route("current-user.destroy"), {
                preserveScroll: true,
                onSuccess: () => {
                    this.success = true;

                    this._.delay(() => {
                        this.closeModal();
                    }, 1000);
                }
            });
        }
    }
};
</script>
