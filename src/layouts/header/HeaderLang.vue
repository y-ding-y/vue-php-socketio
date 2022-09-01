<template>
  <a-dropdown class="lang header-item">
    <div><a-icon type="global" /> {{ langAlias }}</div>
    <a-menu
      @click="(val) => setLang(val.key)"
      :selected-keys="[lang]"
      slot="overlay"
    >
      <a-menu-item v-for="lang in langs" :key="lang.key">{{
        lang.short + ' ' + lang.name
      }}</a-menu-item>
    </a-menu>
  </a-dropdown>
</template>

<script>
import { mapGetters, mapMutations } from 'vuex'
import { langs } from '@/config'

export default {
  name: 'HeaderLanguage',
  data() {
    return { langs }
  },
  computed: {
    ...mapGetters('setting', ['lang']),
    langAlias() {
      const { alias } = this.langs.find((item) => item.key == this.lang)
      return alias
    },
  },
  methods: {
    ...mapMutations('setting', ['setLang']),
  },
}
</script>

<style lang="less"></style>
