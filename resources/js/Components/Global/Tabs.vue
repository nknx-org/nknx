<template>
    <div class="tabs">
        <div v-if="tabs.length" class="tabs__header">
            <div v-if="$slots.tabsControls" class="tabs__control">
                <slot name="tabsControls"></slot>
            </div>

            <div class="tabs__container">
                <div
                    v-for="(tab, index) in tabs"
                    :key="index"
                    :ref="tab"
                    class="tabs__item"
                    :class="[
                        { tabs__item_active: tab === selectedTab },
                        `tabs__item_type_${type}`
                    ]"
                    @click="selectTab(tab)"
                >
                    {{ tab }}
                </div>
            </div>
        </div>
        <slot name="tabsContent"></slot>
    </div>
</template>

<script>
export default {
    components: {},
    props: {
        tabs: {
            type: Array,
            default: () => []
        },
        type: {
            type: String,
            default: ""
        },
        activeTab: {
            type: String,
            default: ""
        }
    },
    data() {
        return {
            selectedTab: "" // selected tab,
        };
    },
    watch: {
        selectedTab(newVal) {
            this.$emit("update:activeTab", newVal);
        }
    },
    created() {},
    mounted() {
        this.selectTab(this.tabs[0]);
    },
    methods: {
        selectTab(title) {
            this.selectedTab = title;

            // loop over all the tabs
            this.$children.forEach(tab => {
                if (!this._.isEmpty(tab.title)) {
                    tab.isActive = tab.title === title;
                }
            });
        }
    }
};
</script>
