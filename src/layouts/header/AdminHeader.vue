<template>
  <a-layout-header :class="[headerTheme, 'admin-header']">
    <div :class="['admin-header-wide', layout, pageWidth]">
      <router-link v-if="isMobile || layout === 'head'" to="/" :class="['logo', isMobile ? null : 'pc', headerTheme]">
        <header-icon />
        <h1 v-if="!isMobile">
          {{ systemName }}
        </h1>
        <sup>{{ systemVersion }}</sup>
      </router-link>
      <a-divider v-if="isMobile" type="vertical" />
      <a-icon v-if="layout !== 'head'" class="trigger" :type="collapsed ? 'menu-unfold' : 'menu-fold'"
        @click="toggleCollapse" />
      <a-breadcrumb v-if="layout == 'side' && !isMobile" style="display: inline">
        <a-breadcrumb-item :key="index" v-for="(item, index) in routeCrumbs">
          <router-link v-if="routeCrumbs.length - 1 > index" :to="item.url">{{
            item.name
            }}</router-link>
          <span v-else :to="item.url">{{ item.name }}</span>
        </a-breadcrumb-item>
      </a-breadcrumb>
      <div v-if="layout !== 'side' && !isMobile" class="admin-header-menu" :style="`width: ${menuWidth};`">
        <i-menu class="head-menu" :theme="headerTheme" mode="horizontal" :options="menuData" @select="onSelect" />
      </div>
      <div :class="['admin-header-right', headerTheme]">
        <header-search class="header-item" @active="(val) => (searchActive = val)" v-show="false" />
        <a-tooltip class="header-item" title="帮助文档" placement="bottom" v-show="false">
          <a href="https://iczer.gitee.io/vue-antd-admin-docs/" target="_blank">
            <a-icon type="question-circle-o" />
          </a>
        </a-tooltip>

        
        <header-notication class="header-item"  v-if="false" />
        <header-notice class="header-item" v-if="false" />
        <header-fullscreen class="header-item" v-on:fullscreen="fullscreen" />
        <header-favor class="header-item" />
        <header-avatar class="header-item" />
        <header-lang class="header-item" />
        <header-site class="header-item" />
      </div>
    </div>
  </a-layout-header>
</template>

<script>
  import HeaderAvatar from "./HeaderAvatar";
  import HeaderFullscreen from "./HeaderFullscreen";
  import HeaderFavor from "./HeaderFavor";
  import HeaderIcon from "./HeaderIcon";
  import HeaderLang from "./HeaderLang";
  import HeaderNotice from "./HeaderNotice";
  import HeaderNotication from "./HeaderNotication";
  import HeaderSearch from "./HeaderSearch";
  import HeaderSite from "./HeaderSite";
  import IMenu from "@/components/menu/menu";
  import { mapState } from "vuex";
  import { getRoutesMatch } from "@/utils/routerUtil";

  export default {
    name: "AdminHeader",
    components: {
      IMenu,
      HeaderAvatar,
      HeaderFullscreen,
      HeaderFavor,
      HeaderIcon,
      HeaderLang,
      HeaderNotice,
      HeaderSearch,
      HeaderSite,
      HeaderNotication
    },
    props: ["collapsed", "menuData"],
    data() {
      return {
        searchActive: false,
      };
    },
    computed: {
      ...mapState("setting", [
        "theme",
        "isMobile",
        "layout",
        "systemName",
        "systemVersion",
        "pageWidth",
      ]),
      headerTheme() {
        if (
          this.layout == "side" &&
          this.theme.mode == "dark" &&
          !this.isMobile
        ) {
          return "light";
        }
        return this.theme.mode;
      },
      menuWidth() {
        const { layout, searchActive } = this;
        const headWidth = layout === "head" ? "100% - 188px" : "100%";
        const extraWidth = searchActive ? "600px" : "400px";
        return `calc(${headWidth} - ${extraWidth})`;
      },
      routeCrumbs() {
        return getRoutesMatch(this.$route.fullPath);
      },
    },
    methods: {
      toggleCollapse() {
        this.$emit("toggleCollapse");
      },
      onSelect(obj) {
        this.$emit("menuSelect", obj);
      },
      fullscreen() {
        this.$emit("fullscreen");
      }
    },
  };
</script>

<style lang="less" scoped>
  @import "index";
</style>
