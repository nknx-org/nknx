<template>
    <Card
        col="12"
        padding="none"
        title="FD Configurations (Click to select)"
        :counter="`${fd_configurations.total} in total`"
    >
        <template v-slot:headerControls>
            <Pagination
                routeName="fastdeploy.index"
                :pageData="fd_configurations"
                pageName="fd_configuration_page"
            />
        </template>
        <div class="overflow-x">
            <table class="table">
                <thead class="table__header">
                    <tr class="table__row">
                        <th style="width: 1%;"></th>
                        <th class="table__title" style="width: 1%;">Label</th>
                        <th class="table__title" style="width: 50%;">
                            Beneficiary Address
                        </th>
                        <th class="table__title" style="width: 1%;">
                            Sync Mode
                        </th>
                        <th class="table__title" style="width: 1%;">
                            UFW?
                        </th>
                        <th class="table__title" style="width: 1%;">Actions</th>
                    </tr>
                </thead>
                <tbody class="table__body">
                    <tr
                        v-for="(config, i) in fd_configurations.data"
                        :key="i"
                        class="table__row"
                        :class="
                            selectedConfig.id === config.id
                                ? 'table__row_active'
                                : null
                        "
                        @click="selectConfig(config)"
                    >
                        <td>
                            <Radio
                                :name="config"
                                :label="config"
                                :value="selectedConfig"
                                :show-text="false"
                                @change="selectConfig"
                            />
                        </td>
                        <td class="table__item">
                            {{ config.label }}
                        </td>
                        <td
                            class="table__item font-mono text_wrap_none"
                            style="font-size: 12px; color: #5769df; max-width: 0px;"
                        >
                            {{ config.beneficiary_addr }}
                        </td>
                        <td class="table__item">
                            {{ config.sync_mode }}
                        </td>
                        <td class="table__item">
                            {{ !config.disable_ufw | boolToString }}
                        </td>

                        <td class="table__item">
                            <v-popover
                                offset="4"
                                :popover="{
                                    defaultPlacement: 'bottom'
                                }"
                                @click.native.stop="setActiveConfig(config)"
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
            title="Delete FD Config"
            subtitle="Fast Deploy"
            descr=""
        >
            <template v-slot:descr>
                Are you sure you would like to delete this FD config:
                <b class="font-semibold">{{ activeConfig.label }}</b
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
                    :loading="deleteFdConfigForm.processing"
                    @click="deleteFdConfig"
                >
                    Confirm
                </Button>
            </template>
        </ModalCard>
    </Card>
</template>

<script>
import { mapMutations } from "vuex";

import FormMixin from "@/Mixins/FormMixin.js";

import Card from "@/Components/Global/Card";
import Button from "@/Components/Global/Button";
import ModalContent from "@/Components/Global/ModalContent";
import ControlWrapper from "@/Components/Global/ControlWrapper";
import ModalCard from "@/Components/Global/ModalCard";
import Pagination from "@/Components/Global/Pagination";
import Radio from "@/Components/Global/Radio";

export default {
    props: ["fd_configurations"],
    mixins: [FormMixin],
    components: {
        Card,
        Button,
        ModalContent,
        ControlWrapper,
        ModalCard,
        Pagination,
        Radio
    },
    data: function() {
        return {
            deleteFdConfigForm: this.$inertia.form(),
            showRemove: false,
            activeConfig: {},
            selectedConfig: { id: 0 }
        };
    },
    watch: {
        "fd_configurations.data": {
            deep: true,
            handler() {
                this.selectConfig({ id: 0 });
            }
        }
    },
    computed: {},
    mounted() {},
    methods: {
        ...mapMutations({
            setActiveFdConfig: "fdConfig/setActiveFdConfig"
        }),
        selectConfig(config) {
            this.selectedConfig = config;

            this.setActiveFdConfig(config);
        },
        setActiveConfig(config) {
            this.activeConfig = config;
        },
        deleteFdConfig() {
            this.deleteFdConfigForm.delete(
                route("fdConfigurations.destroy", {
                    configuration: this.activeConfig
                }),
                {
                    preserveScroll: true,
                    preserveState: true,
                    onSuccess: () => {
                        this.success = true;

                        this._.delay(() => {
                            this.showRemove = false;
                            this.activeConfig = {};
                        }, 1000);
                    }
                }
            );
        }
    }
};
</script>
