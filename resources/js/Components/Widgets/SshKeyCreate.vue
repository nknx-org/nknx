<template>
    <Card
        :col="
            $mq === 'xl' ? '4' : $mq === 'llg' ? '6' : $mq === 'lg' ? '6' : '12'
        "
        headerBorder
        title="New SSH Key"
    >
        <ModalContent>
            <template slot="body">
                <div class="col_6 max-w-xl text-sm text-gray-600">
                    An SSH key is an access credential in the SSH protocol. It gets automatically added to each of your fastDeploy nodes you deploy through NKNx. This gives you a save access to your node's terminal in case you need it.
                </div>
                <ControlWrapper
                    title="Name"
                    col="6"
                    showError
                    :errorMsg="form.errors.name"
                >
                    <Input
                        v-model="form.name"
                        placeholder="SSH title"
                        :errorMsg="form.errors.name"
                        @focus="clearErrors"
                    />
                </ControlWrapper>
                <ControlWrapper
                    title="Key"
                    col="6"
                    showError
                    :errorMsg="form.errors.pubkey"
                >
                    <Textarea
                        v-model.trim="form.pubkey"
                        :errorMsg="form.errors.pubkey"
                        placeholder="Begins with 'ssh-rsa', 'ecdsa-sha2-nistp256', 'ecdsa-sha2-nistp384', 'ecdsa-sha2-nistp521', 'ssh-ed25519', 'sk-ecdsa-sha2-nistp256@openssh.com', or 'sk-ssh-ed25519@openssh.com'"
                        @focus="clearErrors"
                    />
                </ControlWrapper>
            </template>
            <template slot="footer">
                <Button
                    size="large"
                    theme="primary-ghost"
                    :disabled="disabled"
                    :success.sync="success"
                    :loading="form.processing"
                    @click="submit"
                >
                    Add
                </Button>
            </template>
        </ModalContent>
    </Card>
</template>

<script>
import FormMixin from "@/Mixins/FormMixin.js";

import Card from "@/Components/Global/Card";
import Button from "@/Components/Global/Button";
import ModalContent from "@/Components/Global/ModalContent";
import ControlWrapper from "@/Components/Global/ControlWrapper";
import Input from "@/Components/Global/Input";
import ModalCard from "@/Components/Global/ModalCard";
import Textarea from "@/Components/Global/Textarea";

export default {
    props: [],
    mixins: [FormMixin],
    components: {
        Card,
        Button,
        ModalContent,
        ControlWrapper,
        Input,
        ModalCard,
        Textarea
    },
    data: function() {
        return {
            form: this.$inertia.form({
                name: "",
                pubkey: ""
            })
        };
    },
    computed: {
        disabled() {
            if (this.form.name.length > 0 && this.form.pubkey.length > 0) {
                return false;
            } else {
                return true;
            }
        }
    },
    mounted() {},
    methods: {
        submit() {
            this.form.post(route("sshKeys.store"), {
                preserveScroll: true,
                onSuccess: () => {},
                onFinish: () => {
                    this.form.reset();
                }
            });
        }
    }
};
</script>
