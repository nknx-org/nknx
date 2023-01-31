<template>
  <vue-loaders :name="name" :color="color" :scale="scale"></vue-loaders>
</template>

<script>
export default {
  props: {
    name: {
      type: String,
      default: '',
      required: true,
    },
    color: {
      type: String,
      default: '#4b4ded',
      required: false,
    },
    scale: {
      type: Number,
      default: 1,
      required: false,
    },
  },
}
</script>
