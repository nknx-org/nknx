<template>
    <ModalCard
        v-show="showModal"
        title="Password Confirmation"
        subtitle="Auth"
        descr=""
        :show.sync="showModal"
    >
        <template v-slot:body>
            <div class="col_6 mb-4 text-sm text-gray-600">
                This is a secure area of the application. Please confirm your
                password before continuing.
            </div>
            <ControlWrapper
                title="Password"
                col="6"
                showError
                :errorMsg="form.errors.password"
            >
                <Input
                    v-model="form.password"
                    type="password"
                    :errorMsg="form.errors.password"
                    @focus="clearErrors"
                />
            </ControlWrapper>
        </template>

        <template v-slot:footer>
            <Button size="large" theme="text" @click="showModal = false">
                Cancel
            </Button>
            <Button
                size="large"
                theme="primary-ghost"
                :disabled="disabled"
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
    emits: ["onConfirm"],
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
            form: {
                password: "",
                processing: false,
                errors: {
                    password: ""
                }
            }
        };
    },
    computed: {
        disabled() {
            return !this.form.password.length;
        }
    },
    watch: {
        showModal() {
            this.form = {
                password: "",
                processing: false,
                errors: {
                    password: ""
                }
            };
        }
    },
    mounted() {},
    methods: {
        clearErrors() {
            this.form.errors.password = "";
        },
        submit() {
            console.log(this);
            axios
                .post(route("password.confirm"), {
                    password: this.form.password
                })
                .then(() => {
                    this.form.processing = false;
                    this.showModal = false;
                    this.$nextTick(() => this.$emit("onConfirm"));
                })
                .catch(error => {
                    this.form.processing = false;
                    this.form.errors.password =
                        error.response.data.errors.password[0];
                });
        }
    }
};
</script>
