<template>
  <div class="contact-requests-tab-contents" v-if="isSelected">
    <slot></slot>
  </div>
</template>
<script>
export default {
  props: {
    isSelected: {
      type: Boolean
    }
  }
}
</script>