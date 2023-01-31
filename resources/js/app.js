require("./bootstrap");

// Import modules...
import Vue from "vue";
import VueMeta from "vue-meta";
import VueMq from "vue-mq";
import PortalVue from "portal-vue";
import VueMoment from "vue-moment";
import moment from "moment-timezone";
import SvgVue from "svg-vue";
import VueLodash from "vue-lodash";
import VueLoaders from "vue-loaders";
import VTooltip from "v-tooltip";
import VueBus from "vue-bus";
import VueClipboard from "vue-clipboard2";
import axios from "axios";
import VueAxios from "vue-axios";
import Notifications from "vue-notification";
import vueSmoothScroll from "vue-smooth-scroll";

import "vue-loaders/dist/vue-loaders.css";

import lodash from "lodash";

import { App, plugin } from "@inertiajs/inertia-vue";
import { InertiaProgress } from "@inertiajs/progress/src";

import store from "./Store";

Vue.config.productionTip = false;
Vue.mixin({ methods: { route: window.route } });
Vue.use(plugin);
Vue.use(vueSmoothScroll);
Vue.use(VueAxios, axios);
Vue.use(PortalVue);
Vue.use(VueMeta);
Vue.use(SvgVue);
Vue.use(VueBus);
Vue.use(Notifications);
Vue.use(VueClipboard);
Vue.use(VueMoment, {
    moment
});
Vue.use(VueLoaders);
Vue.use(VTooltip);
Vue.use(VueLodash, { lodash: lodash });
Vue.use(VueMq, {
    breakpoints: {
        // default breakpoints - customize this
        xs: 320,
        sm: 600,
        md: 960,
        lg: 1200,
        llg: 1440,
        xl: Infinity
    },
    defaultBreakpoint: "default" // customize this for SSR
});

Vue.filter("commaNumber", function(x) {
    return x
        .toString()
        .replace(/(\d)(?=(?:\d{3})+(?:\.|$))|(\.\d*)$/g, function(m, s1, s2) {
            return s2 || s1 + ",";
        });
});

Vue.filter("nknValue", function(x) {
    return Number(parseFloat(x) / 100000000);
});

Vue.filter("nodeVersion", function(x) {
    return x.toString().slice(0, 6);
});

Vue.filter("hideText", function(x) {
    if (x === null) return "n/a";

    return x.slice(0, 5) + "*****";
});

Vue.filter("isNknAddress", x => {
    const regexp = /^((^NKN([A-Za-z0-9]){33}){1})$/;
    return regexp.test(x);
});

Vue.filter("boolToString", x => {
    return x ? "yes" : "no";
});

Vue.filter("avg", function(arr) {
    return (
        arr.reduce((p, c) => Number(p) + Number(c), 0) / arr.length
    ).toFixed(0);
});

Vue.filter("hexConverter", function(hex) {
    hex = hex.toString();
    let str = "";
    for (let i = 0; i < hex.length && hex.substr(i, 2) !== "00"; i += 2)
        str += String.fromCharCode(parseInt(hex.substr(i, 2), 16));
    return str;
});

Vue.filter("striphtml", function(value) {
    const div = document.createElement("div");
    div.innerHTML = value;
    const text = div.textContent || div.innerText || "";
    return text;
});

Vue.filter("excerpt", function(value, arg1) {
    return (
        value
            .split(/\s+/)
            .slice(0, arg1)
            .join(" ") + "..."
    );
});

InertiaProgress.init();

const el = document.getElementById("app");

new Vue({
    store,
    metaInfo: {
        titleTemplate: title => (title ? `${title} - NKNx` : "NKNx")
    },
    render: h =>
        h(App, {
            props: {
                initialPage: JSON.parse(el.dataset.page),
                resolveComponent: name =>
                    import(`@/Pages/${name}`).then(module => module.default)
            }
        })
}).$mount(el);
