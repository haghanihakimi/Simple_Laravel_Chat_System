<template>
  <div class="contacts-list-tab-container">
    <ul class="nav">
      <li v-for="tab in tabs" :key="tab" class="nav-item">
        <a href="javascript:void(0)" class="nav-link" :class="{active: tab === selected}" @click="setTab(tab)">{{tab}}</a>
      </li>
    </ul>
    <slot></slot>
  </div>
</template>

<script>

  export default {
    name: "ContactRequestConfTab",
    props: {
      tabs: {
        type: Array,
        require: true,
      },
      selected: {
        type: String,
        require: true,
      }
    },
    methods: {
      setTab(tab) {
        this.$emit('selected', tab)
      }
    }
  }
</script>