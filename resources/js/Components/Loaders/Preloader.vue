<template>
    <div class="content-loader">
        <SimpleLoader
            class="content-loader__icon"
            :name="name"
            :color="color"
            :scale="scale"
        ></SimpleLoader>
        <div class="content-loader__title">{{ title }}</div>
    </div>
</template>

<script>
import SimpleLoader from "@/Components/Loaders/SimpleLoader";

export default {
    components: { SimpleLoader },
    props: {
        name: {
            type: String,
            default: "ball-spin-fade-loader"
        },
        color: {
            type: String,
            default: "#4b4ded",
            required: false
        },
        title: {
            type: String,
            default: "",
            required: false
        },
        scale: {
            type: Number,
            default: 1,
            required: false
        }
    }
};
</script>
