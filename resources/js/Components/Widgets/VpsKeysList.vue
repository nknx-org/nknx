<template>
    <Card
        col="12"
        padding="none"
        title="VPS Keys"
        :counter="`${vpsKeys.total} in total`"
    >
        <template v-slot:headerControls>
            <Pagination
                :paginationData.sync="paginationData"
                routeName="vpsKeys.index"
                :pageData="vpsKeys"
            />
        </template>
        <div class="overflow-x">
            <table class="table">
                <thead class="table__header">
                    <tr class="table__row">
                        <th class="table__title" style="width: 1%;">Name</th>
                        <th class="table__title" style="width: 20%;">
                            Provider
                        </th>
                        <th class="table__title">Key</th>
                        <th class="table__title">Secret</th>
                        <th class="table__title" style="width: 1%;">Actions</th>
                    </tr>
                </thead>
                <tbody class="table__body">
                    <tr
                        v-for="key in vpsKeys.data"
                        :key="key.id"
                        class="table__row"
                    >
                        <td class="table__item">
                            {{ key.profile_name }}
                        </td>
                        <td class="table__item">
                            {{ key.provider }}
                        </td>
                        <td class="table__item">
                            {{ key.api_token | hideText }}
                        </td>
                        <td class="table__item">
                            {{ key.api_secret | hideText }}
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
            title="Delete VPS Key"
            subtitle="User Profile"
            descr=""
        >
            <template v-slot:descr>
                Are you sure you would like to delete this VPS key:
                <b class="font-semibold">{{ activeKey.profile_name }}</b
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
                    :loading="deleteVpsKeyForm.processing"
                    @click="deleteVpsKey"
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
import Pagination from "@/Components/Global/Pagination";

export default {
    props: ["vps-keys"],
    mixins: [FormMixin],
    components: {
        Card,
        Button,
        ModalContent,
        ControlWrapper,
        Input,
        Checkbox,
        ModalCard,
        Pagination
    },
    data: function() {
        return {
            deleteVpsKeyForm: this.$inertia.form(),
            showRemove: false,
            showEdit: false,
            activeKey: {},
            paginationData: {
                search: "",
                per_page: 10
            }
        };
    },
    computed: {},
    mounted() {},
    methods: {
        setActiveKey(key) {
            this.activeKey = key;
        },
        deleteVpsKey() {
            this.deleteVpsKeyForm.delete(
                route("vpsKeys.destroy", this.activeKey),
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
