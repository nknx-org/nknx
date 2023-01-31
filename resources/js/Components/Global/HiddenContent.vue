<template>
    <div class="hidden-content">
        <div class="hidden-content__top">
            <svg-vue class="hidden-content__line" icon="line-left" />
            <div class="hidden-content__icon">
                <svg-vue class="hidden-content__icon-svg" icon="key" />
            </div>
            <svg-vue
                class="hidden-content__line hidden-content__line_right"
                icon="line-left"
            />
        </div>

        <div class="hidden-content__text">
            <div class="hidden-content__title">
                {{ title }} —<br />
                Not available in Free tier
            </div>
            <div class="hidden-content__descr">
                {{ descr }}
            </div>

            <Button
                v-if="showButton"
                class="hidden-content__button"
                size="large"
                theme="peach"
                tag="a"
                :href="`/user/billing`"
                width="full"
            >
                Get started
            </Button>
        </div>
    </div>
</template>

<script>
import Button from "@/Components/Global/Button";

export default {
    components: { Button },
    props: {
        title: {
            type: String,
            default: ""
        },
        descr: {
            type: String,
            default:
                "Feel free to get a subcription text about how nknx is fucking cool and amazing"
        },
        showButton: {
            type: Boolean,
            default: true
        }
    },
    data: () => {
        return {};
    },
    mounted() {},
    methods: {}
};
</script>
