export default {
    props: {
        openModalEvent: {
            type: String,
            default: "openModal"
        }
    },
    data: function() {
        return {
            form: this.$inertia.form(),
            showModal: false,
            success: false
        };
    },
    mounted() {
        this.$bus.$on(this.openModalEvent, this.openModal);
    },
    beforeDestroy() {
        this.$bus.$off(this.openModalEvent);
    },
    methods: {
        openModal() {
            this.showModal = true;
        },
        closeModal() {
            this.showModal = false;
            this.form.reset();
            this.clearErrors();
        },
        clearErrors() {
            this.form.clearErrors();
        }
    }
};
