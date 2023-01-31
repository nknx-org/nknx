<template>
  <div :class="['content-wrapper', centered === true ? 'content-wrapper_centered' : null]">
    <slot/>
  </div>
</template>

<script>
export default {
  components: {},
  props: {
    centered: {
      type: Boolean,
      default: true
    }
  },
  data: () => {
    return {}
  },
  mounted: function() {},
  methods: {}
}
</script>
