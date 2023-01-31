<template>
    <div class="landing" id="home">
        <header class="header" :class="[headerActive ? 'header_active' : '']" ref="header">
            <div class="container">
                <div class="row">
                    <div class="col-12 header__row">
                        <svg-vue class="header__logo" icon="logo-full" />
                        <ul class="header__nav">
                            <li class="header__nav-item">
                                <a href="#home" v-smooth-scroll class="header__nav-link">Home</a>
                            </li>
                            <li class="header__nav-item">
                                <a href="#features" v-smooth-scroll class="header__nav-link">Features</a>
                            </li>
                        </ul>
                        <div class="header__actions">
                            <Button v-if="$page.props.user" size="large" theme="text" tag="inertia-link"
                                :href="route('dashboard')">
                                Dashboard
                            </Button>
                            <template v-else>
                                <Button size="large" theme="text" tag="inertia-link" :href="route('login')">
                                    Sign In
                                </Button>
                                <Button size="large" theme="primary" tag="inertia-link" :href="route('register')">
                                    Sign Up
                                </Button>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <section class="welcome section">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <h1 class="welcome__title">
                            A New Kind of Mining for the New Kind of Network
                        </h1>
                        <div class="welcome__descr">
                            <div class="welcome__text text_descr">
                                Community built from the ground up, bringing node management and
                                simple mining to all
                            </div>
                            <Button size="large" theme="primary" tag="inertia-link" :href="route('login')" width="full">
                                Get started
                            </Button>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 text_align_center">
                        <img class="welcome__hero" src="/assets/images/landing.png" />
                    </div>
                </div>
            </div>
        </section>

        <section id="features" class="section about bg_primary">
            <img class="about__img" :src="`/assets/images/${activeAbout}.png`" />
            <div class="container">
                <div class="row">
                    <div class="offset-lg-7 col-lg-5">
                        <h3>
                            An advanced node and wallet tracker, made to manage everything in
                            a single place.
                        </h3>
                        <div class="about__items">
                            <div class="about__item" :class="{
                                about__item_active: activeAbout === 'Nodes',
                            }" @click="activeAbout = 'Nodes'">
                                <div class="about__icon">
                                    <svg-vue icon="dot-points" class="about__icon-svg" />
                                </div>
                                <div class="about__descr">
                                    <div class="about__title">All nodes at a glance</div>
                                    <div class="about__text">
                                        Our neat status filter allows you to inspect instantly nodes
                                        that are not working as intended.
                                    </div>
                                </div>
                            </div>

                            <div class="about__item" :class="{
                                about__item_active: activeAbout === 'Batch',
                            }" @click="activeAbout = 'Batch'">
                                <div class="about__icon">
                                    <svg-vue icon="batch" class="about__icon-svg" />
                                </div>
                                <div class="about__descr">
                                    <div class="about__title">Wallet tracking</div>
                                    <div class="about__text">
                                        Want to see the NKN flowing? With our built-in wallet
                                        tracking we got you covered!
                                    </div>
                                </div>
                            </div>

                            <div class="about__item" :class="{
                                about__item_active: activeAbout === 'FD',
                            }" @click="activeAbout = 'FD'">
                                <div class="about__icon">
                                    <svg-vue icon="fastDeploy" class="about__icon-svg" />
                                </div>
                                <div class="about__descr">
                                    <div class="about__title">Fast Deploy</div>
                                    <div class="about__text">
                                        With our NKNx Fast Deploy creating a NKN node is just a
                                        click away.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="features section">
            <div class="container">
                <div class="row">
                    <div class="col-12 col-lg-5">
                        <h2>NKNx is more than just an app...</h2>
                        <p class="features__descr text_descr">
                            ...it's a whole ecosystem! We're working closely with the NKN
                            Foundation, community and other projects to provide you the things you
                            really need.
                        </p>
                    </div>
                    <div class="col-12">
                        <div class="features__cards">
                            <div class="features__card">
                                <svg-vue icon="chat" class="features__card-icon"></svg-vue>
                                <div class="features__card-title">
                                    Automated Monitoring and notifications
                                </div>

                                <div class="features__card-descr text_label">
                                    You don't want to always check the state of your nodes? Our
                                    system will always look for your nodes and notify via mail you
                                    if something is wrong. You can also configure on which events
                                    you want to receive notifications.
                                </div>
                            </div>

                            <div class="features__card">
                                <svg-vue icon="code" class="features__card-icon"></svg-vue>
                                <div class="features__card-title">
                                    One-Click Installation with FastDeploy
                                </div>

                                <div class="features__card-descr text_label">
                                    We developed an own, easy to use node installation method
                                    called FastDeploy. It allows you to install your nodes
                                    either directly from NKNx (on supported VPS providers) or with
                                    a one-line snippet.
                                </div>
                            </div>

                            <div class="features__card">
                                <svg-vue icon="earth" class="features__card-icon"></svg-vue>
                                <div class="features__card-title">
                                    Ultra-fast Node ID Generation Service
                                </div>

                                <div class="features__card-descr text_label">
                                    Getting NKN mainnet tokens is not always easy - especially
                                    when you're new to mining NKN. Our node ID Generation Service
                                    can be used with crypto and will send you the needed NKN in no
                                    time.
                                </div>
                            </div>

                            <div class="features__card">
                                <svg-vue icon="heart" class="features__card-icon"></svg-vue>
                                <div class="features__card-title">
                                    Mainnet swaps powered by our own solution
                                </div>

                                <div class="features__card-descr text_label">
                                    All our services can be used with BTC and LTC. We are crypto
                                    and we run on crypto - so no credit card or personal data
                                    required. We don't use any third party providers and host all
                                    systems on our own.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="pricing" class="section pay bg_primary">
            <div class="container">
                <div class="row">
                    <div class="col-lg-6">
                        <img class="pay__img" :src="`/assets/images/NknxPay.png`" />
                    </div>
                    <div class="col-lg-6">
                        <h3>Easy node ID generation with NKNx Swap.</h3>
                        <p class="pay__descr text_descr">
                            To provide you an easy and anonymous way to get started with NKN Mainnet Tokens we use our
                            own swap service called NKNx Swap.<br /><br />With NKNx Swap you can
                            use your favourite cryptocurrencies to easily get your nodes ready for mining. No credit
                            card or third-party service involved - NKNx Swap is fast, reliable and easy to use.<br /><br />Currently
                            we support BTC and LTC as a swap option.
                        </p>
                    </div>
                </div>
            </div>
        </section>



        <section class="mining section">
            <div class="container">
                <div class="row">
                    <div class="col-lg-8">
                        <div class="mining__left">
                            <svg-vue icon="logo" class="mining__icon"></svg-vue>
                            <div class="mining__text">
                                <h3 class="mining__title">Start monitoring your nodes today</h3>
                                <div class="text_descr">
                                    Create an account, add your nodes and you’re ready to roll.
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-2 offset-lg-2 mining__right">
                        <Button size="large" theme="peach" tag="inertia-link" :href="route('login')" width="full">
                            Get started
                        </Button>
                    </div>
                </div>
            </div>
        </section>

        <footer class="footer bg_primary">
            <div class="container footer__section">
                <div class="row">
                    <div class="col-lg-4">
                        <div class="footer__descr">
                            <div class="footer__descr-top">
                                <svg-vue class="footer__descr-logo" icon="nkn-logo-white" />
                                <h5 class="footer__descr-heading">New Kind of Network</h5>
                            </div>
                            <div class="footer__descr-about text_small">
                                NKN is a New Kind of Network, a Cellular Automata powered,
                                decentralized data relay network built on Blockchain &
                                incentivized by a native token.
                            </div>
                            <div class="footer__socials">
                                <a class="footer__socials-item" href="https://twitter.com/nknX_org" target="_blank">
                                    <svg-vue class="footer__socials-icon" icon="twitter" />
                                </a>
                                <a class="footer__socials-item" href="https://t.me/nknorg" target="_blank">
                                    <svg-vue class="footer__socials-icon" icon="telegram" />
                                </a>
                                <a class="footer__socials-item" href="https://medium.com/nknx/" target="_blank">
                                    <svg-vue class="footer__socials-icon" icon="medium-white" />
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-2 offset-lg-2">
                        <div class="footer__nav">
                            <h5 class="footer__nav-title">About NKN</h5>
                            <a class="footer__nav-item text_small" href="https://nkn.org/technology/our-solution/"
                                target="_blank">About</a>
                            <a class="footer__nav-item text_small" href="https://nkn.org" target="_blank">Official
                                Website</a>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="footer__nav">
                            <h5 class="footer__nav-title">Other tools</h5>
                            <a class="footer__nav-item text_small" href="http://nscan.io/" target="_blank">nScan.io - the NKN Blockchain explorer</a>
                            <a class="footer__nav-item text_small" href="https://vanity.nknx.org/" target="_blank">NKNx
                                Vanity - create beautiful wallet addresses</a>
                            <a class="footer__nav-item text_small" href="https://vault.rule110.io/"
                                target="_blank">Vault - an easy to use NKN mainnet wallet</a>




                        </div>
                    </div>
                </div>
                <div class="footer__divider"></div>
                <div class="row">
                    <div class="col-lg-3">
                        <div class="text_small footer__copyright">
                            © 2023 NKNx. All rights reserved.
                        </div>
                    </div>
                </div>
            </div>
        </footer>
    </div>
</template>

<script>
import Button from "@/Components/Global/Button";
import ButtonSwitch from "@/Components/Global/ButtonSwitch";

export default {
    components: {
        Button,
        ButtonSwitch,
    },
    data: () => {
        return {
            activePeriod: "Monthly",
            headerActive: false,
            activeAbout: "Nodes",
        };
    },
    computed: {
        isMonthly() {
            return this.activePeriod === "Monthly";
        },
        currentPeriod() {
            return this.isMonthly ? "Month" : "Year";
        },
        standartPrice() {
            return this.getPrice(9.99);
        },
        standardPlusPrice() {
            return this.getPrice(19.99);
        },
        proPrice() {
            return this.getPrice(29.99);
        },
        proPlusPrice() {
            return this.getPrice(59.99);
        },
        ultimatePrice() {
            return this.getPrice(99.99);
        },
    },
    mounted() {
        window.addEventListener("scroll", this.updateScroll);
    },
    destroyed() {
        window.removeEventListener("scroll", this.updateScroll);
    },
    methods: {
        getPrice(price) {
            return this.isMonthly ? price : (price * 10).toFixed(2);
        },
        updateScroll() {
            this.headerActive = window.scrollY > this.$refs.header.clientHeight;
        },
    },
};
</script>
