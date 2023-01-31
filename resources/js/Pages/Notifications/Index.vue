<template>
    <app-layout>
        <ContentWrapper>
            <h1 class="page__title">Notifications</h1>
            <template v-if="!notifications.data.length">
                <div>
                    You don't have any notifications yet
                </div>
            </template>

            <template v-else>
                <div class="row">
                    <div class="col-lg-4">
                        <div class="notifications-pagination">
                            <div class="notifications-pagination__info">
                                Showing {{ notifications.from }} to
                                {{ notifications.to }} of
                                {{ notifications.total }}
                            </div>
                            <Pagination
                                routeName="notifications.index"
                                :pageData="notifications"
                            />
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-4">
                        <Card
                            class="notifications-preview"
                            padding="none"
                            :custom="true"
                        >
                            <div
                                class="notifications-preview__item"
                                :class="{
                                    'notifications-preview__item_active':
                                        item.id === activeTab.id,
                                    'notifications-preview__item_unread': _.isNull(
                                        item.read_at
                                    )
                                }"
                                v-for="item in notifications.data"
                                :key="item.id"
                                @click="readNotification(item)"
                            >
                                <div class="notifications-preview__header">
                                    <div
                                        class="notifications-preview__title text_wrap_none"
                                    >
                                        {{ item.data.header }}
                                    </div>
                                    <div class="notifications-preview__date">
                                        {{ $moment(item.created_at).fromNow() }}
                                    </div>
                                </div>
                                <div
                                    class="notifications-preview__text text_wrap_none"
                                >
                                    {{ item.data.excerpt }}
                                </div>
                            </div>
                        </Card>
                    </div>

                    <div class="col-lg-8" v-if="activeTab">
                        <Card
                            class="notifications-tab"
                            padding="none"
                            :custom="true"
                        >
                            <div class="notifications-tab__header">
                                <div class="notifications-tab__header-left">
                                    <div class="notifications-tab__title">
                                        {{ activeTab.data.header }}
                                    </div>
                                    <div class="notifications-tab__from">
                                        From: NKNx Team
                                    </div>
                                </div>
                                <div
                                    class="notifications-tab__delete"
                                    @click="deleteNotification(activeTab.id)"
                                >
                                    <svg-vue
                                        icon="trash"
                                        class="notifications-tab__delete-icon"
                                    ></svg-vue>
                                </div>
                            </div>
                            <div class="notifications-tab__content">
                                <div v-html="activeTab.data.text"></div>
                            </div>
                        </Card>
                    </div>
                </div>
            </template>
        </ContentWrapper>
    </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
import ContentWrapper from "@/Components/Global/ContentWrapper";
import Card from "@/Components/Global/Card";
import Pagination from "@/Components/Global/Pagination";

export default {
    props: ["notifications"],
    components: {
        AppLayout,
        ContentWrapper,
        Card,
        Pagination
    },
    data() {
        return {
            activeTab: false
        };
    },
    watch: {},
    mounted() {
        this.$nextTick(() => {
            this.initTab();
        });
    },
    computed: {},
    methods: {
        initTab() {
            if (this.notifications.data.length) {
                this.readNotification(this.notifications.data[0]);
            }
        },
        readNotification(item) {
            this.activeTab = item;

            if (!this._.isNull(item.read_at)) return;

            this.$inertia.put(
                route("notifications.update", { notification: item.id }),
                { read: true }
            );
        },
        deleteNotification(id) {
            this.$inertia.delete(
                route("notifications.destroy", { notification: id }),
                {
                    onFinish: visit => {
                        this.initTab();
                    }
                },
                { preserveState: true }
            );
        }
    }
};
</script>
