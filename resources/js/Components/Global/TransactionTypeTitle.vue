<template>
    <div>{{ title }}</div>
</template>

<script>
export default {
    props: {
        type: {
            type: String,
            default: ""
        }
    },
    data: () => {
        return {
            title: "",
            className: ""
        };
    },
    watch: {
        type: function(newVal, oldVal) {
            this.getTxTitle();
        }
    },
    mounted() {
        this.getTxTitle();
    },
    methods: {
        getTxTitle() {
            const type = this.type;
            switch (type) {
                case "all":
                    this.title = "All Transactions";
                    this.className = "all";
                    break;
                case "COINBASE_TYPE":
                    this.title = "Mining Reward";
                    this.className = "reward";
                    break;
                case "TRANSFER_ASSET_TYPE":
                    this.title = "Transfer";
                    this.className = "transfer";
                    break;
                case "SIG_CHAIN_TXN_TYPE":
                    this.title = "Signature Chain";
                    this.className = "sigchain";
                    break;
                case "SUBSCRIBE_TYPE":
                    this.title = "Subscription";
                    this.className = "subscription";
                    break;
                case "REGISTER_NAME_TYPE":
                    this.title = "Wallet Name Registration";
                    this.className = "wallet-name-registration";
                    break;
                case "TRANSFER_NAME_TYPE":
                    this.title = "Wallet Name Transfer";
                    this.className = "wallet-name-transfer";
                    break;
                case "DELETE_NAME_TYPE":
                    this.title = "Wallet Name Deletion";
                    this.className = "wallet-name-deletion";
                    break;
                case "GENERATE_ID_TYPE":
                    this.title = "ID Generation";
                    this.className = "generate-id";
                    break;
                case "NANO_PAY_TYPE":
                    this.title = "Nano Pay";
                    this.className = "nano-pay";
                    break;
                default:
                    return false;
            }
        }
    }
};
</script>
