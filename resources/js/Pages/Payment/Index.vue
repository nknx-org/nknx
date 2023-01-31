<template>
    <app-layout>

        <ContentWrapper>
            <h1 class="page__title">
                    Nodes Page
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
                        <StripeElements
                        :stripe-key="stripeKey"
                        :instance-options="instanceOptions"
                        :elements-options="elementsOptions"
                        #default="{ elements }"
                        ref="elms"
                        >
                        <StripeElement
                            type="card"
                            :elements="elements"
                            :options="cardOptions"
                            ref="card"
                        />
                        </StripeElements>
                        <button @click="pay" type="button">Pay</button>
                    </div>
                </div>
            </div>



        </ContentWrapper>

    </app-layout>
</template>

<script>
    import AppLayout from '@/Layouts/AppLayout'
    import ContentWrapper from '@/Components/Global/ContentWrapper'
    import { StripeElements, StripeElement } from 'vue-stripe-elements-plus'

    export default {
        props:['stripeKey','intent'],
        components: {
            AppLayout,
            ContentWrapper,
            StripeElements,
            StripeElement
        },
        data() {
            return {
                cardHolderName:'',
                instanceOptions: {
                    // https://stripe.com/docs/js/initializing#init_stripe_js-options
                },
                elementsOptions: {
                    hidePostalCode : true
                    // https://stripe.com/docs/js/elements_object/create#stripe_elements-options
                },
                cardOptions: {
                    // reactive
                    // remember about Vue 2 reactivity limitations when dealing with options
                    value: {
                        postalCode: ''
                    }
                    // https://stripe.com/docs/stripe.js#element-options
                }
            }
        },
        methods: {
            pay () {
                // ref in template
                const groupComponent = this.$refs.elms
                const cardComponent = this.$refs.card
                // Get stripe element
                const cardElement = cardComponent.stripeElement

                // Access instance methods, e.g. createToken()
                groupComponent.instance.confirmCardSetup(this.intent.client_secret, {
                    payment_method: {
                        card : cardElement,
                        billing_details: {name: this.$page.props.user.name}
                    }

                }).then(result => {
                    if (result.error) {
                        //error handling
                    } else {
                        this.$inertia.post('/purchase', {
                            payment_method : result.setupIntent.payment_method
                        }, {
                            preserveScroll: true,
                        })
                    }

                })
            }
        },
    }
</script>
