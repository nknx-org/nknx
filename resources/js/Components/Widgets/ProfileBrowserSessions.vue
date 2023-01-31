<template>
    <Card
        :col="
            $mq === 'xl' ? '4' : $mq === 'llg' ? '6' : $mq === 'lg' ? '6' : '12'
        "
        headerBorder
        title="Browser Sessions"
    >
        <ModalContent>
            <template slot="body">
                <div class="col_6">
                    <div class="max-w-xl text-sm text-gray-600">
                        If necessary, you may log out of all of your other
                        browser sessions across all of your devices. Some of
                        your recent sessions are listed below; however, this
                        list may not be exhaustive. If you feel your account has
                        been compromised, you should also update your password.
                    </div>

                    <!-- Other Browser Sessions -->
                    <div class="mt-5 mb-4 space-y-6" v-if="sessions.length > 0">
                        <div
                            class="flex items-center"
                            v-for="(session, i) in sessions"
                            :key="i"
                        >
                            <div>
                                <svg
                                    fill="none"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                    class="w-8 h-8 text-gray-500"
                                    v-if="session.agent.is_desktop"
                                >
                                    <path
                                        d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"
                                    ></path>
                                </svg>

                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 24 24"
                                    stroke-width="2"
                                    stroke="currentColor"
                                    fill="none"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    class="w-8 h-8 text-gray-500"
                                    v-else
                                >
                                    <path
                                        d="M0 0h24v24H0z"
                                        stroke="none"
                                    ></path>
                                    <rect
                                        x="7"
                                        y="4"
                                        width="10"
                                        height="16"
                                        rx="1"
                                    ></rect>
                                    <path d="M11 5h2M12 17v.01"></path>
                                </svg>
                            </div>

                            <div class="ml-3">
                                <div class="text-sm text-gray-600">
                                    {{ session.agent.platform }} -
                                    {{ session.agent.browser }}
                                </div>

                                <div>
                                    <div class="text-xs text-gray-500">
                                        {{ session.ip_address }},

                                        <span
                                            class="text-green-500 font-semibold"
                                            v-if="session.is_current_device"
                                            >This device</span
                                        >
                                        <span v-else
                                            >Last active
                                            {{ session.last_active }}</span
                                        >
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
            <template slot="footer">
                <Button
                    size="large"
                    theme="primary"
                    width="full"
                    @click="openModal"
                >
                    Log Out Other Browser Sessions
                </Button>
            </template>
        </ModalContent>
    </Card>
</template>

<script>
import Card from "@/Components/Global/Card";
import Button from "@/Components/Global/Button";
import ModalContent from "@/Components/Global/ModalContent";

export default {
    props: ["sessions"],
    components: {
        Card,
        Button,
        ModalContent
    },
    data: function() {
        return {};
    },
    computed: {},
    mounted() {},
    methods: {
        openModal() {
            this.$bus.emit("openLogoutSessionsModal");
        }
    }
};
</script>
