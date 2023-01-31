<template>
    <app-layout>

        <ContentWrapper>
            <h1 class="page__title">
                    Network Page
            </h1>

            <div class="py-12">
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                        Overall props for every site:<br/><br/>
                        {{$page.props}}
                    </div>
                </div>
            </div>

            <div class="py-12">
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                        Wallet prop for only this site (served by NetworkController.php):<br/><br/>
                        {{network}}
                    </div>
                </div>
            </div>

        </ContentWrapper>

    </app-layout>
</template>

<script>
    import AppLayout from '@/Layouts/AppLayout'
    import ContentWrapper from '@/Components/Global/ContentWrapper'

    export default {
        props:['network'],
        components: {
            AppLayout,
            ContentWrapper
        },
        data() {
            return {
            }
        },
        methods: {

        }
    }
</script>
