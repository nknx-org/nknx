<template>
    <Card col="12" class="profile-notifications">
        <ModalContent>
            <template slot="body">
                <div class="col_6">
                    <div>
                        <div
                            class="profile-notifications__row profile-notifications__header"
                        >
                            <h3>
                                Notify me on
                            </h3>
                            <h3 class="text_align_center">
                                Email
                            </h3>
                            <h3 class="text_align_center">
                                Portal
                            </h3>
                            <h3 class="text_align_center">
                                App
                            </h3>
                        </div>
                        <div class="profile-notifications__content">
                            <div class="profile-notifications__item">
                                <div class="profile-notifications__row">
                                    <div class="profile-notifications__title">
                                        Node Updates
                                    </div>
                                    <!-- <Checkbox
                                        class="profile-notifications__checkbox"
                                        :value="false"
                                    />
                                    <Checkbox
                                        class="profile-notifications__checkbox"
                                        :value="false"
                                    /> -->
                                </div>
                                <div
                                    class="profile-notifications__row"
                                >
                                    <div
                                        class="profile-notifications__subtitle"
                                    >
                                        Node offline
                                    </div>
                                    <Checkbox
                                        class="profile-notifications__checkbox"
                                        v-model="form.node_offline"
                                        value="mail"
                                    /><Checkbox
                                        class="profile-notifications__checkbox"
                                        v-model="form.node_offline"
                                        value="database"
                                    /><Checkbox
                                        class="profile-notifications__checkbox"
                                        v-model="form.node_offline"
                                        value="fcm"
                                        disabled="true"
                                    />
                                </div>
                                <div
                                    class="profile-notifications__row"
                                >
                                    <div
                                        class="profile-notifications__subtitle"
                                    >
                                        Node online again
                                    </div>
                                    <Checkbox
                                        class="profile-notifications__checkbox"
                                        v-model="form.node_online_again"
                                        value="mail"
                                    /><Checkbox
                                        class="profile-notifications__checkbox"
                                        v-model="form.node_online_again"
                                        value="database"
                                    /><Checkbox
                                        class="profile-notifications__checkbox"
                                        v-model="form.node_online_again"
                                        value="fcm"
                                        disabled="true"
                                    />
                                </div>
                                 <div
                                    class="profile-notifications__row"
                                >
                                    <div
                                        class="profile-notifications__subtitle"
                                    >
                                        Needs ID generation
                                    </div>
                                    <Checkbox
                                        class="profile-notifications__checkbox"
                                        v-model="form.needs_id_generation"
                                        value="mail"
                                    /><Checkbox
                                        class="profile-notifications__checkbox"
                                        v-model="form.needs_id_generation"
                                        value="database"
                                    /><Checkbox
                                        class="profile-notifications__checkbox"
                                        v-model="form.needs_id_generation"
                                        value="fcm"
                                        disabled="true"
                                    />
                                </div>
                                <div
                                    class="profile-notifications__row"
                                >
                                    <div
                                        class="profile-notifications__subtitle"
                                    >
                                        Node generated ID
                                    </div>
                                    <Checkbox
                                        class="profile-notifications__checkbox"
                                        v-model="form.node_generated_id"
                                        value="mail"
                                    /><Checkbox
                                        class="profile-notifications__checkbox"
                                        v-model="form.node_generated_id"
                                        value="database"
                                    /><Checkbox
                                        class="profile-notifications__checkbox"
                                        v-model="form.node_generated_id"
                                        value="fcm"
                                        disabled="true"
                                    />
                                </div>
                                <div
                                    class="profile-notifications__row"
                                >
                                    <div
                                        class="profile-notifications__subtitle"
                                    >
                                        Node mined a block
                                    </div>
                                    <Checkbox
                                        class="profile-notifications__checkbox"
                                        v-model="form.node_mined_block"
                                        value="mail"
                                    /><Checkbox
                                        class="profile-notifications__checkbox"
                                        v-model="form.node_mined_block"
                                        value="database"
                                    /><Checkbox
                                        class="profile-notifications__checkbox"
                                        v-model="form.node_mined_block"
                                        value="fcm"
                                        disabled="true"
                                    />
                                </div>
                            </div>

                            <div class="profile-notifications__item">
                                <div class="profile-notifications__row">
                                    <div class="profile-notifications__title">
                                        Wallet Updates
                                    </div>
                                    <!-- <Checkbox
                                        class="profile-notifications__checkbox"
                                        v-model="walletUpdatesEmail"
                                        :value="walletUpdatesEmail"
                                    />
                                    <Checkbox
                                        class="profile-notifications__checkbox"
                                        v-model="walletUpdatesDatabase"
                                        :value="walletUpdatesDatabase"
                                    /> -->
                                </div>
                                <div
                                    class="profile-notifications__row"
                                >
                                    <div
                                        class="profile-notifications__subtitle"
                                    >
                                        Received NKN
                                    </div>
                                    <Checkbox
                                        class="profile-notifications__checkbox"
                                        v-model="form.receive_nkn"
                                        value="mail"
                                    /><Checkbox
                                        class="profile-notifications__checkbox"
                                        v-model="form.receive_nkn"
                                        value="database"
                                    /><Checkbox
                                        class="profile-notifications__checkbox"
                                        v-model="form.receive_nkn"
                                        value="fcm"
                                        disabled="true"
                                    />
                                </div>
                                <div
                                    class="profile-notifications__row"
                                >
                                    <div
                                        class="profile-notifications__subtitle"
                                    >
                                        Send NKN
                                    </div>
                                    <Checkbox
                                        class="profile-notifications__checkbox"
                                        v-model="form.send_nkn"
                                        value="mail"
                                    /><Checkbox
                                        class="profile-notifications__checkbox"
                                        v-model="form.send_nkn"
                                        value="database"
                                    /><Checkbox
                                        class="profile-notifications__checkbox"
                                        v-model="form.send_nkn"
                                        value="fcm"
                                        disabled="true"
                                    />
                                </div>
                            </div>

                            <div class="profile-notifications__item">
                                <div class="profile-notifications__row">
                                    <div class="profile-notifications__title">
                                        FastDeploy events
                                    </div>
                                    <!-- <Checkbox
                                        class="profile-notifications__checkbox"
                                        v-model="fastDeployEentsEmail"
                                        :value="fastDeployEentsEmail"
                                    />
                                    <Checkbox
                                        class="profile-notifications__checkbox"
                                        v-model="fastDeployEentsDatabase"
                                        :value="fastDeployEentsDatabase"
                                    /> -->
                                </div>
                                <div
                                    class="profile-notifications__row"
                                >
                                    <div
                                        class="profile-notifications__subtitle"
                                    >
                                        Node installation started
                                    </div>
                                    <Checkbox
                                        class="profile-notifications__checkbox"
                                        v-model="form.node_installation_started"
                                        value="mail"
                                    /><Checkbox
                                        class="profile-notifications__checkbox"
                                        v-model="form.node_installation_started"
                                        value="database"
                                    /><Checkbox
                                        class="profile-notifications__checkbox"
                                        v-model="form.node_installation_started"
                                        value="fcm"
                                        disabled="true"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </template>

            <template slot="footer">
                <Button
                    size="large"
                    theme="primary-ghost"
                    :success.sync="success"
                    :loading="form.processing"
                    @click="saveData"
                >
                    Update
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
import Checkbox from "@/Components/Global/Checkbox";
import ModalCard from "@/Components/Global/ModalCard";

export default {
    mixins: [FormMixin],
    components: {
        Card,
        Button,
        ModalContent,
        ControlWrapper,
        Checkbox,
        ModalCard
    },
    data: function() {
        return {
            form: this.$inertia.form({
                node_offline: this.$page.props.user.notificationPreferences['node_offline'] ? this.$page.props.user.notificationPreferences['node_offline'] : [],
                node_online_again: this.$page.props.user.notificationPreferences['node_online_again'] ? this.$page.props.user.notificationPreferences['node_online_again'] : [],
                needs_id_generation: this.$page.props.user.notificationPreferences['needs_id_generation'] ? this.$page.props.user.notificationPreferences['needs_id_generation'] : [],
                node_generated_id: this.$page.props.user.notificationPreferences['node_generated_id'] ? this.$page.props.user.notificationPreferences['node_generated_id'] : [],
                node_mined_block: this.$page.props.user.notificationPreferences['node_mined_block'] ? this.$page.props.user.notificationPreferences['node_mined_block'] : [],
                receive_nkn: this.$page.props.user.notificationPreferences['receive_nkn'] ? this.$page.props.user.notificationPreferences['receive_nkn'] : [],
                send_nkn: this.$page.props.user.notificationPreferences['send_nkn'] ? this.$page.props.user.notificationPreferences['send_nkn'] : [],
                node_installation_started: this.$page.props.user.notificationPreferences['node_installation_started'] ? this.$page.props.user.notificationPreferences['node_installation_started'] : [],
            }),
            titles: {
                node_offline: "Node offline",
                node_online_again: "Node online again",
                needs_id_generation: "Needs ID generation",
                node_generated_id: "Node generated ID",
                node_mined_block: "Node mined a block",
                receive_nkn: "Received NKN",
                send_nkn: "Send NKN",
                node_installation_started: "Node installation started",
            }
        };
    },
    computed: {
        nodeUpdatesEmail: {
            get() {
                return this.getAllUpdates("nodeUpdates", "mail");
            },
            set(newVal) {
                this.setUpdates(newVal, "nodeUpdates", "mail");
            }
        },
        nodeUpdatesDatabase: {
            get() {
                return this.getAllUpdates("nodeUpdates", "database");
            },
            set(newVal) {
                this.setUpdates(newVal, "nodeUpdates", "database");
            }
        },
        walletUpdatesEmail: {
            get() {
                return this.getAllUpdates("walletUpdates", "mail");
            },
            set(newVal) {
                this.setUpdates(newVal, "walletUpdates", "mail");
            }
        },
        walletUpdatesDatabase: {
            get() {
                return this.getAllUpdates("walletUpdates", "database");
            },
            set(newVal) {
                this.setUpdates(newVal, "walletUpdates", "database");
            }
        },
        fastDeployEentsEmail: {
            get() {
                return this.getAllUpdates("fastDeployEents", "mail");
            },
            set(newVal) {
                this.setUpdates(newVal, "fastDeployEents", "mail");
            }
        },
        fastDeployEentsDatabase: {
            get() {
                return this.getAllUpdates("fastDeployEents", "database");
            },
            set(newVal) {
                this.setUpdates(newVal, "fastDeployEents", "database");
            }
        }
    },
    mounted() {},
    methods: {
        selectAllNodeNotifications(type) {
            if (!form.node_offline.inArray(type)) {
                form.node_offline.push(type);
            }
            if (!form.node_online_again.inArray(type)) {
                form.node_online_again.push(type);
            }
            if (!form.needs_id_generation.inArray(type)) {
                form.needs_id_generation.push(type);
            }
            if (!form.node_generated_id.inArray(type)) {
                form.node_generated_id.push(type);
            }
            if (!form.node_mined_block.inArray(type)) {
                form.node_mined_block.push(type);
            }
        },
        selectAllWalletNotifications(type) {
            if (!form.receive_nkn.inArray(type)) {
                form.receive_nkn.push(type);
            }
            if (!form.send_nkn.inArray(type)) {
                form.send_nkn.push(type);
            }
        },
        selectAllFastDeployNotifications(type) {
            if (!form.node_installation_started.inArray(type)) {
                form.node_installation_started.push(type);
            }
        },
        getAllUpdates(name, updateFormat) {
            return this._.every(this.form[name], x =>
                this._.some(x, y => y === updateFormat)
            );
        },
        setUpdates(newVal, name, updateformat) {
            this._.each(this.form[name], x =>
                this.$delete(x, this._.indexOf(x, updateformat))
            );

            if (newVal) {
                this._.each(this.form[name], x => x.push(updateformat));
            }
        },
        saveData() {
            this.form.post(route("user-profile-notifications.update"), {
                errorBag: "updateProfileNotifications",
                preserveScroll: true,
                onSuccess: () => (this.success = true)
            });
        }
    }
};
</script>
