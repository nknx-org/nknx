<template>
    <Card
        col="12"
        padding="none"
        title="API Tokens"
        :counter="`${tokens.length} in total`"
    >
        <div class="overflow-x">
            <table class="table">
                <thead class="table__header">
                    <tr class="table__row">
                        <th class="table__title" style="width: 30%;">Name</th>
                        <th class="table__title" style="width: 20%;">
                            Last used
                        </th>
                        <th class="table__title">Permissions</th>
                        <th class="table__title" style="width: 1%;">Actions</th>
                    </tr>
                </thead>
                <tbody class="table__body">
                    <tr
                        v-for="token in tokens"
                        :key="token.id"
                        class="table__row"
                    >
                        <td class="table__item">
                            {{ token.name }}
                        </td>
                        <td class="table__item">
                            {{ token.last_used_ago || "never" }}
                        </td>
                        <td class="table__item">
                            {{ token.abilities.join(", ") }}
                        </td>
                        <td class="table__item">
                            <v-popover
                                offset="4"
                                :popover="{
                                    defaultPlacement: 'bottom'
                                }"
                                @click.native.stop="setActiveToken(token)"
                            >
                                <div
                                    class="popover-more popover-more_horizontal tooltip-target"
                                >
                                    <svg-vue
                                        icon="more-horizontal"
                                        class="popover-more-icon popover-more-icon_horizontal"
                                    />
                                </div>
                                <template slot="popover">
                                    <div class="popover_actions">
                                        <div class="popover__actions">
                                            <div
                                                class="popover__actions-item"
                                                @click="showEdit = true"
                                            >
                                                Edit
                                            </div>
                                            <div
                                                class="popover__actions-item text_color_danger"
                                                @click="showRemove = true"
                                            >
                                                Remove
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </v-popover>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <ModalCard
            v-show="showRemove"
            :show.sync="showRemove"
            title="Delete API Token"
            subtitle="User Profile"
            descr="API tokens allow third-party services to authenticate with our
                application on your behalf."
        >
            <template v-slot:descr>
                Are you sure you would like to delete this API token:
                <b class="font-semibold">{{ activeToken.name }}</b
                >?
            </template>

            <template v-slot:footer>
                <Button size="large" theme="text" @click="showRemove = false">
                    Close
                </Button>
                <Button
                    size="large"
                    theme="primary"
                    :success.sync="success"
                    :loading="deleteApiTokenForm.processing"
                    @click="deleteApiToken"
                >
                    Confirm
                </Button>
            </template>
        </ModalCard>

        <ModalCard
            v-show="showEdit"
            :show.sync="showEdit"
            :title="`API Token Permissions: ${activeToken.name || ''}`"
            subtitle="User Profile"
            descr="API tokens allow third-party services to authenticate with our
                application on your behalf."
        >
            <template v-slot:body>
                <ControlWrapper
                    title="Permissions"
                    col="6"
                    type="row"
                    :showError="false"
                >
                    <Checkbox
                        v-for="permission in availablePermissions"
                        :key="permission"
                        v-model="updateApiTokenForm.permissions"
                        :value="permission"
                        :title="permission"
                    />
                </ControlWrapper>
            </template>

            <template v-slot:footer>
                <Button size="large" theme="text" @click="showEdit = false">
                    Close
                </Button>
                <Button
                    size="large"
                    theme="primary"
                    :success.sync="success"
                    :loading="updateApiTokenForm.processing"
                    @click="updateApiToken"
                >
                    Update
                </Button>
            </template>
        </ModalCard>
    </Card>
</template>

<script>
import FormMixin from "@/Mixins/FormMixin.js";

import Card from "@/Components/Global/Card";
import Button from "@/Components/Global/Button";
import ModalContent from "@/Components/Global/ModalContent";
import ControlWrapper from "@/Components/Global/ControlWrapper";
import Input from "@/Components/Global/Input";
import Checkbox from "@/Components/Global/Checkbox";
import ModalCard from "@/Components/Global/ModalCard";

export default {
    props: ["tokens", "defaultPermissions", "availablePermissions"],
    mixins: [FormMixin],
    components: {
        Card,
        Button,
        ModalContent,
        ControlWrapper,
        Input,
        Checkbox,
        ModalCard
    },
    data: function() {
        return {
            deleteApiTokenForm: this.$inertia.form(),
            updateApiTokenForm: this.$inertia.form({
                permissions: []
            }),
            showRemove: false,
            showEdit: false,
            activeToken: {}
        };
    },
    computed: {},
    mounted() {},
    methods: {
        setActiveToken(token) {
            this.updateApiTokenForm.permissions = token.abilities;
            this.activeToken = token;
        },
        updateApiToken() {
            this.updateApiTokenForm.put(
                route("api-tokens.update", this.activeToken),
                {
                    preserveScroll: true,
                    preserveState: true,
                    onSuccess: () => {
                        this._.delay(() => {
                            this.showEdit = false;
                            this.activeToken = {};
                        }, 1000);
                    }
                }
            );
        },
        deleteApiToken() {
            this.deleteApiTokenForm.delete(
                route("api-tokens.destroy", this.activeToken),
                {
                    preserveScroll: true,
                    preserveState: true,
                    onSuccess: () => {
                        this.success = true;
                        this._.delay(() => {
                            this.showRemove = false;
                            this.activeToken = {};
                        }, 1000);
                    }
                }
            );
        }
    }
};
</script>
