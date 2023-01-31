<template>
    <Card
        col="12"
        padding="none"
        title="SSH Keys"
        :counter="`${sshKeys.length} in total`"
    >
        <div class="overflow-x">
            <table class="table">
                <thead class="table__header">
                    <tr class="table__row">
                        <th class="table__title" style="width: 1%;">Name</th>
                        <th class="table__title" style="width: 50%;">
                            Fingerprint
                        </th>
                        <th class="table__title" style="width: 20%;">
                            Added
                        </th>
                        <th class="table__title" style="width: 1%;">Actions</th>
                    </tr>
                </thead>
                <tbody class="table__body">
                    <tr v-for="key in sshKeys" :key="key.id" class="table__row">
                        <td class="table__item">
                            {{ key.name }}
                        </td>
                        <td
                            class="table__item font-mono text_wrap_none"
                            style="font-size: 12px; color: #5769df; max-width: 0px;"
                        >
                            {{ "SHA256:" + key.fingerprint }}
                        </td>
                        <td class="table__item">
                            {{ $moment(key.created_at).format("DD/MM/YYYY") }}
                        </td>
                        <td class="table__item">
                            <v-popover
                                offset="4"
                                :popover="{
                                    defaultPlacement: 'bottom'
                                }"
                                @click.native.stop="setActiveKey(key)"
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
            title="Delete SSH Key"
            subtitle="User Profile"
            descr=""
        >
            <template v-slot:descr>
                Are you sure you would like to delete this SSH key:
                <b class="font-semibold">{{ activeKey.name }}</b
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
                    :loading="deleteSshKeyForm.processing"
                    @click="deleteSshKey"
                >
                    Confirm
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
    props: ["ssh-keys"],
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
            deleteSshKeyForm: this.$inertia.form(),
            showRemove: false,
            showEdit: false,
            activeKey: {}
        };
    },
    computed: {},
    mounted() {},
    methods: {
        setActiveKey(key) {
            this.activeKey = key;
        },
        deleteSshKey() {
            this.deleteSshKeyForm.delete(
                route("sshKeys.destroy", this.activeKey),
                {
                    preserveScroll: true,
                    preserveState: true,
                    onSuccess: () => {
                        this.success = true;

                        this._.delay(() => {
                            this.showRemove = false;
                            this.activeKey = {};
                        }, 1000);
                    }
                }
            );
        }
    }
};
</script>
