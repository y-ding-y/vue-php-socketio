<template>
  <span class="icon" :style="style">EF</span>
</template>

<script>
export default {
  name: 'HeaderIcon',
  props: {
    size: {
      type: Number,
      default: 32,
    },
  },
  data() {
    return {
      style: {
        width: this.size + 'px',
        height: this.size + 'px',
        lineHeight: this.size - 3 + 'px',
        fontSize: this.size - 3 + 'px',
      },
    }
  },
}
</script>
<style lang="less" scoped>
.icon {
  border-radius: 10%;
  color: white;
  text-align: center;
  background-color: @primary-color;
  display: inline-block;
  vertical-align: middle;
  &:hover {
    cursor: default;
    font-size: 20px;
    font-weight: 500;
    background-color: rgba(255, 255, 255, 10%);
    color: @primary-color;
    text-shadow: @primary-color 0 0 30px;
  }
}
</style>
