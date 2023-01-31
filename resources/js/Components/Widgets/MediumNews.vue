<template>
    <Card :col="$mq === 'xl' ? '4' : '12'" row="4">
        <div class="news">
            <div class="news__header">
                <h3 class="news__heading">Latest News</h3>
                <svg-vue icon="medium" class="news__icon"></svg-vue>
            </div>

            <transition-group tag="div" name="fade-out-in">
                <div
                    v-for="news in paginatedData"
                    :key="news.title"
                    class="news__item"
                >
                    <div class="news-item__header">
                        <div class="news-item__date">
                            {{ $moment(news.pubDate).format("DD/MM/YYYY") }}
                        </div>
                        <div
                            class="news-item__authour"
                            v-html="'By ' + news.author"
                        ></div>
                    </div>
                    <div
                        class="news-item__title text_wrap_none"
                        v-html="news.title"
                    ></div>
                    <div class="news-item__content">
                        {{ news.content | striphtml | excerpt(20) }}
                    </div>
                    <a
                        class="news-item__more"
                        target="_blank"
                        :href="news.link"
                    >
                        Read More
                        <svg-vue icon="arrow-right"></svg-vue>
                    </a>
                    <div class="news-item__divider"></div>
                </div>
            </transition-group>

            <div class="news__footer">
                <div class="news__pages">
                    Showing {{ currentNumber }} of
                    {{ news.length }}
                </div>
                <div class="news__buttons">
                    <div
                        v-if="pageNumber != 0"
                        class="news__button news__button_prev"
                        @click="prevPage"
                    >
                        <span class="fe fe-chevron-left"></span>
                    </div>
                    <div
                        v-if="pageNumber <= pageCount - 1"
                        class="news__button news__button_next"
                        @click="nextPage"
                    >
                        <span class="fe fe-chevron-right"></span>
                    </div>
                </div>
            </div>
        </div>
    </Card>
</template>

<script>
import Card from "@/Components/Global/Card";
import NewsLoader from "@/Components/Loaders/NewsLoader";

export default {
    components: {
        Card,
        NewsLoader
    },
    props: ["news"],
    data: () => {
        return {
            pageNumber: 0,
            itemsOnPage: 5,
            currentItems: 5,
            currentNumber: 5
        };
    },
    computed: {
        pageCount() {
            const dataLength = this.news.length;
            return Math.floor(dataLength / this.itemsOnPage);
        },
        paginatedData() {
            const start = this.pageNumber * this.itemsOnPage;
            const end = start + this.itemsOnPage;
            return this.news.slice(start, end);
        }
    },
    destroyed() {},
    methods: {
        nextPage() {
            this.pageNumber++;
            this.currentNumber += this.paginatedData.length;
            this.currentItems = this.paginatedData.length;
        },
        prevPage() {
            this.pageNumber--;
            this.currentNumber -= this.currentItems;
            this.currentItems = this.paginatedData.length;
        }
    }
};
</script>
