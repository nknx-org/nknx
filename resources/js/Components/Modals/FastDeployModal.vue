<template>
    <ModalCard v-show="showModal" :title="activeProvider" subtitle="Deploy NKN Nodes to" descr=""
        :show.sync="showModal">
        <template v-slot:body>
            <Preloader v-show="preloader" title="Hold on, loading your configurations..." />
            <TextWarning v-if="!ssh && activeProvider === 'AWS'" class="col_6" title="WARNING">
                AWS requires you to have at least one SSH key added to your server.
                Public Key authentication is enforced by AWS by default so you won't be
                able to remotely log into your node when you proceed now. You can add
                your public SSH keys through your
                <inertia-link href="/user/profile" class="text_link">Account's profile</inertia-link>
                and FastDeploy will automatically add them to your node while
                installing.
            </TextWarning>
            <ControlWrapper v-if="keys.length > 1" title="VPS Keys" col="6" showError
                :errorMsg="form.errors.vps_key_id">
                <Select :value.sync="form.vps_key_id" :items="keys" item-text="profile_name" />
            </ControlWrapper>
            <ControlWrapper title="Server Size" col="6" showError :errorMsg="form.errors.size">
                <Select :items="sizes" :value.sync="form.size" item-text="shortcut" />
                <span v-if="activeFdConfig.sync_mode !== 'Lite Sync' && activeProvider != 'AWS'" class="card__subtitle"
                    style="margin-top:1em">Notice: Your node will use a full-sized chain. Therefore all images that only
                    offer &lt;40GB on disk space have been removed from this list.</span>
                <span v-if="activeProvider == 'AWS'" class="card__subtitle" style="margin-top:1em">Notice: FastDeploy
                    will automatically create a disk of 50GB and attach it to your node instance - regardless of the
                    Sync Mode you chose.</span>
            </ControlWrapper>
            <ControlWrapper title="Region" col="6" showError
                :errorMsg="form.errors.region">
                <Select :items="regionNames" :value.sync="form.region" />
            </ControlWrapper>

            <ControlWrapper title="Number of nodes" col="2" showError :errorMsg="form.errors.names">
                <Input v-model="count" :errorMsg="form.errors.names" @focus="clearErrors" type="number" />
            </ControlWrapper>
            <ControlWrapper title="Label" col="4" showError :errorMsg="form.errors.names">
                <div style="overflow: auto; max-height: 200px">
                    <Input v-for="(label, i) in form.names" :key="i" v-model="form.names[i]"
                        :placeholder="`My-FastDeploy-Node${incrementStart + i}`" :errorMsg="form.errors.names"
                        @focus="clearErrors" multiple />
                </div>
            </ControlWrapper>
        </template>

        <template v-slot:footer>
            <Button size="large" theme="text" @click="showModal = false">
                Cancel
            </Button>
            <Button size="large" theme="primary-ghost" :disabled="disabled" :success.sync="success"
                :loading="form.processing" @click="submit">
                Confirm
            </Button>
        </template>
    </ModalCard>
</template>

<script>
import { mapState } from "vuex";

import FormMixin from "@/Mixins/FormMixin.js";

import Button from "@/Components/Global/Button";
import ModalContent from "@/Components/Global/ModalContent";
import ModalCard from "@/Components/Global/ModalCard";
import ControlWrapper from "@/Components/Global/ControlWrapper";
import Input from "@/Components/Global/Input";
import Select from "@/Components/Global/Select";
import Preloader from "@/Components/Loaders/Preloader";
import TextWarning from "@/Components/Global/TextWarning";

export default {
    mixins: [FormMixin],
    components: {
        Button,
        ModalContent,
        ModalCard,
        ControlWrapper,
        Input,
        Select,
        Preloader,
        TextWarning,
    },
    data: function () {
        return {
            form: this.$inertia.form({
                provider: "",
                names: [],
                size: "",
                region: "",
                vps_key_id: "",
            }),
            sizes: [],
            keys: [],
            labels: [],
            count: 1,
            nodeCounter: 0,
            preloader: true,
            incrementStart: 0,
            masterText: "My-FastDeploy-Node",
        };
    },
    computed: {
        ...mapState({
            activeProfiles: (state) => state.fdConfig.activeProfiles,
            activeProvider: (state) => state.fdConfig.activeProvider,
            activeFdConfig: (state) => state.fdConfig.activeFdConfig,
            ssh: (state) => state.fdConfig.ssh,
        }),
        disabled() {
            if (
                this.form.size !== "" &&
                this.preloader === false &&
                this.form.region !== "" &&
                this.form.vps_key_id !== "" &&
                this.count > 0 &&
                this.form.names.length > 0
            ) {
                return false;
            } else {
                return true;
            }
        },
        regionNames() {
            return !this.preloader
                ? this.form.size.regions.map((region) => region.name)
                : [];
        },
        labelsCopy() {
            return this.form.names.slice();
        },
        selectedKey() {
            return this.form.vps_key_id;
        }
    },
    watch: {
        labelsCopy: function (newVal, oldVal) {
            if (this.labelsCopy.length > 0) {
                if (newVal[0] !== oldVal[0]) {
                    const string = newVal[0];
                    const num = string ? string.match(/\d+$/) : false;
                    const numLength = num ? num[0].length : 0;
                    const masterNum = num ? parseInt(num[0], 10) : 0;
                    const masterText = string.slice(0, string.length - numLength);

                    this.masterText = masterText;
                    this.incrementStart = masterNum - 1;

                    this.form.names.forEach((label, i) => {
                        if (i !== 0) {
                            this.$set(this.form.names, i, `${masterText}${masterNum + i}`);
                        }
                    });
                }
            }
        },
        count(newVal, oldVal) {
            if (newVal < 1) {
                this.count = 1;
            }

            const isInc = newVal - oldVal > 0;
            const labelsLength = this.form.names.length;
            const incrementStart = this.incrementStart;

            if (isInc) {
                for (let i = labelsLength + 1; i <= newVal; i++) {
                    this.form.names.push(`${this.masterText}${incrementStart + i}`);
                }
            } else {
                this.form.names = this.form.names.slice(0, newVal);
            }
        },
        "form.size"(newVal, oldVal) {
            if (this.preloader) return;

            const activeSize = this.form.size;
            const activeRegion = this.form.region;
            const ifRegion = this.form.size.regions.filter(
                (region) => region.name === activeRegion
            );

            this.form.region = ifRegion.length
                ? ifRegion[0].name
                : activeSize.regions[0].name;
        },
        showModal(newVal) {
            if (newVal) {
                this.preloadData();
            }
        },
    },
    mounted() { },
    methods: {
        async preloadData() {
            this.preloader = true;
            await this.getSizes();
            await this.getKeys();

            this.incrementStart = this.nodeCounter;
            this.form.names = [`My-FastDeploy-Node-${this.incrementStart + 1}`];

            this.preloader = false;
        },
        async getSizes() {
            const provider = this.activeProvider.toLowerCase();


            try {
                const response = await this.axios.get(
                    `fast-deploy/helpers/${provider}/sizes`
                );

                const { sync_mode } = this.activeFdConfig;


                this.sizes =
                    sync_mode !== "Lite Sync"
                        ? response.data.filter((x) => x.disk > 40)
                        : response.data;

                this.sizes.forEach((item) => {
                    const slug =
                        provider === "aws" || provider === "hetzner"
                            ? `${item.slug} - `
                            : "";
                    const descr = provider ===
                        "digitalocean"
                        ? ` (${item.description})`
                        : "";
                    item.shortcut =
                        `${slug}${item.memory / 1024}GB RAM - ${item.vcpus} CPU - ${item.disk
                        }GB SSD${descr}: ${item.priceMonthly.toFixed(2)}$/mo`

                });
                this.form.size = this.sizes[0];
                this.form.region = this.form.size.regions[0].name;
            } catch {
                this.sizes = [];
                this.showModal = false;
                this.$notify({
                    group: "notifications",
                    text: "Error on fetching sizes",
                    type: "error",
                });
            }
        },
        async getKeys() {
            this.keys = this.activeProfiles;
            this.form.vps_key_id = this.keys[0];
        },
        validateLabels(str) {
            const regExp = /^[a-zA-Z0-9.-]*$/;
            return regExp.test(str);
        },
        submit() {
            const labels = this.form.names.join("-");
            const isValidLabels = this.validateLabels(labels);

            if (!isValidLabels) {
                this.$notify({
                    group: "notifications",
                    text: "Invalid label format",
                    type: "error",
                });
                return;
            }

            const provider = this.activeProvider;
            const activeSize = this.form.size;
            const activeRegion = activeSize.regions.filter(
                (region) => region.name === this.form.region
            )[0];

            let payloadSize = "";
            let payloadRegion = "";

            switch (provider) {
                case "DigitalOcean":
                    payloadSize = activeSize.slug;
                    payloadRegion = activeRegion.slug;
                    break;
                case "Vultr":
                    payloadSize = activeSize.vpsId;
                    payloadRegion = activeRegion.DCID;
                    break;
                case "Hetzner":
                    payloadSize = activeSize.vpsId;
                    payloadRegion = activeRegion.slug;
                    break;
                case "AWS":
                    payloadSize = activeSize.slug;
                    payloadRegion = activeRegion.slug;
                    break;
                default:
                    return false;
            }

            this.form
                .transform((data) => ({
                    ...data,
                    provider,
                    names: this.form.names.join(),
                    size: payloadSize,
                    region: payloadRegion,
                    vps_key_id: this.form.vps_key_id.id,
                }))
                .post(
                    `fast-deploy/configurations/${this.activeFdConfig.id}/deployment`,
                    {
                        preserveScroll: true,
                        onError: (errors) => { },
                        onSuccess: () => {
                            this.success = true;

                            this._.delay(() => {
                                this.closeModal();
                            }, 1000);
                        },
                    }
                );
        },
    },
};
</script>
