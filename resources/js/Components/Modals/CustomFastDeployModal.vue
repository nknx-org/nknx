<template>
    <ModalCard v-show="showModal" title="Custom Deployment" subtitle="Fast Deploy" descr="" :show.sync="showModal"
        width="720px">
        <template v-slot:body>
            <Preloader v-show="preloader" title="Hold on, loading your configurations..." />
            <TextWarning class="col_6" title="WARNING">
                When you're using custom deployment NKNx can't configure anything that is happening outside the machine it is deploying to.
                <br/><br/>Therefore please make sure <b style="font-weight: 800;">your node meets the following requirements</b>:
                <ul style="list-style-type: disc;  list-style-position: inside;margin-left: 15px; margin-top:.6rem">
                    <li style="display: list-item;">The node needs to be reachable from the Internet through an <b style="font-weight: 800;">IPv4 Address</b></li>
                    <li style="display: list-item;">Following <b style="font-weight: 800;">Ports</b> need to be accessible from outside / forwarded in your home router:
                        <ul style="list-style-type: disc;  list-style-position: inside;margin-left: 15px; margin-top:.2rem">
                            <li style="display: list-item;"><b style="font-weight: 800;">443</b> (TCP)</li>
                            <li style="display: list-item;"><b style="font-weight: 800;">30001 - 30005</b> (TCP)</li>
                            <li style="display: list-item;"><b style="font-weight: 800;">30010 - 30011</b> (TCP)</li>
                            <li style="display: list-item;"><b style="font-weight: 800;">30020 - 30021</b> (TCP)</li>
                            <li style="display: list-item;"><b style="font-weight: 800;">32768 - 65535</b> (TCP & UDP)</li>
                        </ul>
                    </li>
                </ul>
            </TextWarning>
            <ControlWrapper title="Label" col="6">
                <Input v-model="label" placeholder="Enter your node label" />
            </ControlWrapper>
            <ControlWrapper title="System Architecture" col="6">
                <Radio v-for="picker in pickers" :key="picker.title" class="radio_column" :name="picker.value"
                    :label="picker.value" :labelText="picker.title" showText :value="architecture"
                    @change="changeArchitecture" />
            </ControlWrapper>
            <div class="col_6">
                <div class="mt-3 text-sm text-gray-600">
                    <p class="font-semibold">
                        Here is your FastDeploy script. Please make sure to run
                        it as root-user to make sure everything will run without
                        errors.
                    </p>
                </div>

                <div class="mt-4 bg-gray-100 px-4 py-2 rounded font-mono text-sm text-gray-500 break-all cursor-pointer"
                    v-clipboard:copy="script" @click="copyNotify">
                    {{ script }}
                    <svg-vue style="position: relative;top: 4px;margin-left: 2px;" class="copy__icon"
                        icon="copy"></svg-vue>
                </div>
            </div>
        </template>

        <template v-slot:footer>
            <Button size="large" theme="text" @click="showModal = false">
                Close
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
import Radio from "@/Components/Global/Radio";
import Preloader from "@/Components/Loaders/Preloader";
import TextWarning from "@/Components/Global/TextWarning";

export default {
    mixins: [FormMixin],
    components: {
        TextWarning,
        Button,
        ModalContent,
        ModalCard,
        ControlWrapper,
        Input,
        Radio,
        Preloader
    },
    data: function () {
        return {
            preloader: true,
            label: "My-Node-1",
            architecture: "linux-amd64",
            pickers: [
                {
                    title: "AMD-based machines (nearly all VPS)",
                    value: "linux-amd64"
                },
                {
                    title:
                        "ARMv6-based machines (f.e. Raspberry Pi v1 and Zero)",
                    value: "linux-armv6"
                },
                {
                    title:
                        "ARMv7-based machines (f.e. Raspberry Pi v2 Model B)",
                    value: "linux-armv7"
                },
                {
                    title:
                        "ARMv8-based machines (f.e. Raspberry Pi v2 Model B+ to v4)",
                    value: "linux-armv7"
                }
            ]
        };
    },
    computed: {
        ...mapState({
            activeProfiles: state => state.fdConfig.activeProfiles,
            activeProvider: state => state.fdConfig.activeProvider,
            activeFdConfig: state => state.fdConfig.activeFdConfig
        }),
        script() {
            return `wget -O install.sh 'https://nknx.org/api/v1/fast-deploy/install/${this.activeFdConfig.uuid}/${this.architecture}/${this.label}'; bash install.sh`;
        }
    },
    watch: {
        showModal(newVal) {
            if (newVal) {
                this.label = "My-Node-1";
                this.architecture = "linux-amd64";
                this.preloader = false;
            }
        }
    },
    mounted() { },
    methods: {
        changeArchitecture(newValue) {
            console.log(newValue);
            this.architecture = newValue;
        },
        copyNotify() {
            this.$notify({
                group: "notifications",
                text: "Script copied to clipboard",
                type: "success"
            });
        }
    }
};
</script>
