<template>
    <div class="avatar-dropdown">
        <a
            v-on-clickaway="closeDropdown"
            class="avatar-dropdown__link"
            href="#"
            @click.prevent="showDropDown = !showDropDown"
        >
            <div class="avatar-dropdown__avatar">
                {{ $page.props.user.name.slice(0, 2) || "?" }}
            </div>
        </a>
        <transition tag="div" name="slide-fade" style="position: relative">
            <div v-if="showDropDown">
                <ul class="avatar-dropdown__menu">
                    <li>
                        <inertia-link
                            href="/user/profile"
                            class="avatar-dropdown__dd-link"
                            >Settings</inertia-link
                        >
                    </li>
                    <li>
                        <a
                            href="#"
                            class="avatar-dropdown__dd-link"
                            @click="logout"
                            >Logout</a
                        >
                    </li>
                </ul>
            </div>
        </transition>
    </div>
</template>

<script>
import { mapGetters } from "vuex";

import { mixin as clickaway } from "vue-clickaway";

export default {
    mixins: [clickaway],
    data() {
        return {
            showDropDown: false
        };
    },
    computed: {
        ...mapGetters({
            userData: "userData/getUserData"
        })
    },
    destroyed() {},
    mounted() {},
    methods: {
        closeDropdown() {
            this.showDropDown = false;
        },
        openSettings() {
            this.$router.push({ path: `/account-settings` });
        },
        logout() {
            this.$inertia.post(route("logout"));
        }
    }
};
</script>
