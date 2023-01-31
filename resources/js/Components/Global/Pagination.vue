<template>
    <div
        class="pagination"
        :class="
            paginationOptions.current_page === null
                ? 'pagination_disabled'
                : 'pagination_enabled'
        "
        :disabled="paginationOptions.current_page === null"
    >
        <div
            class="pagination__button"
            :class="!isPrevPage ? 'pagination__button_disabled' : null"
            @click="isPrevPage ? changePage('prev') : false"
        >
            <svg-vue
                class="pagination__arrow pagination__arrow_prev"
                :icon="paginationIcon"
                :class="[
                    !isPrevPage ? 'pagination__arrow_disabled' : null,
                    `pagination__arrow_type_${paginationType}`
                ]"
            />
        </div>

        <div
            class="pagination__button"
            :class="[!isNextPage ? 'pagination__button_disabled' : null]"
            @click="isNextPage ? changePage('next') : false"
        >
            <svg-vue
                class="pagination__arrow"
                :icon="paginationIcon"
                :class="[
                    !isNextPage ? 'pagination__arrow_disabled' : null,
                    `pagination__arrow_type_${paginationType}`
                ]"
            />
        </div>
    </div>
</template>

<script>
export default {
    components: {},
    props: {
        routeData: {
            type: Object
        },
        pageData: {
            type: Object
        },
        routeName: {
            type: String
        },
        paginationData: {
            type: Object,
            default: () => {
                return { per_page: 10, search: "" };
            }
        },
        custom: {
            type: Boolean,
            default: false
        },
        paginationIcon: {
            type: String,
            default: "arrowRight"
        },
        pageName: {
            type: String,
            default: "page"
        },
        paginationType: {
            type: String,
            default: "stroke"
        }
    },
    data: () => {
        return {};
    },
    computed: {
        paginationOptions() {
            return {
                prev_page_url: this.pageData.prev_page_url,
                next_page_url: this.pageData.next_page_url,
                current_page: this.pageData.current_page
            };
        },
        isPrevPage() {
            return !this._.isEmpty(this.paginationOptions.prev_page_url);
        },
        isNextPage() {
            return !this._.isEmpty(this.paginationOptions.next_page_url);
        }
    },
    mounted() {
        if (!this.custom) {
            this.$nextTick(() => {
                this.getResults(1, this.routeName);
            });
        }
    },
    methods: {
        getResults(page = 1, route) {
            let routeQuery = this._.pickBy(this.routeData);
            let paginationData = this._.pickBy(this.paginationData);

            paginationData[this.pageName] = page;
            this.$inertia.get(this.route(route, routeQuery), paginationData, {
                preserveScroll: true,
                preserveState: true
            });
        },
        changePage(type) {
            if (!this.custom) {
                const page =
                    type === "prev"
                        ? this.paginationOptions.current_page - 1
                        : this.paginationOptions.current_page + 1;
                this.getResults(page, this.routeName);
            } else {
                if (type === "prev") {
                    this.paginationOptions.current_page -= 1;
                } else {
                    this.paginationOptions.current_page += 1;
                }

                this.$listeners.getData(this.paginationOptions.current_page);
            }
        }
    }
};
</script>
