<template>
    <div>
        <span @click="fetchNotice" class="header-notice">
            <a-badge class="notice-badge" :count="$store.getters['socket/totalnewcount']" :overflow-count="99">
                <!-- count="9" -->
                <a-icon type="aliwangwang" :class="['header-notice-icon']" />
            </a-badge>
        </span>

        <header-onlinelist v-if="visible"
            :style="{zIndex:999, position:'absolute',backgroundColor: 'antiquewhite',right: '12px',top:drawerheight+'px'}">
        </header-onlinelist>
    </div>
</template>

<script>
    import HeaderOnlinelist from "./onlinelist.vue";

    import { mapGetters, mapState } from 'vuex'
    export default {
        name: 'HeaderNotication',
        data() {
            return {
                loading: false,
                show: false,
                i: 0,
                visible: false,
            }
        },
        computed: {
            ...mapGetters('account', ['user']),
            ...mapState('setting', ['lang', 'pageMinHeight']),
            drawerheight() {
                return window.innerHeight / 2 - ((this.pageMinHeight / 4) * 3) / 2;
            },
        },
        created() {
            // this.$socket.connect();
            // //this.$socket.emit('login', this.user.empno);
            // this.$store.commit("socket/setoldchat", "");
            // this.$store.commit("socket/setoldnewcount", "");

        },
        components: {
            HeaderOnlinelist
        },
        methods: {
            fetchNotice() {
                this.$store.commit("socket/setoldnewmsg", "");
                if (this.visible) {
                    this.visible = false
                }
                else {
                    this.visible = true
                }
                if (this.loading) {
                    this.loading = false
                    return
                }
                this.loadding = true
                setTimeout(() => {
                    this.loadding = false
                }, 1000)
            }
        }
    }
</script>

<style lang="less">
    .header-notice {
        display: inline-block;
        transition: all 0.3s;

        span {
            vertical-align: initial;
        }

        .notice-badge {
            color: inherit;

            .header-notice-icon {
                font-size: 16px;
                padding: 4px;
            }
        }
    }

    .dropdown-tabs {
        background-color: @base-bg-color;
        box-shadow: 0 2px 8px @shadow-color;
        border-radius: 4px;

        .tab-pane {
            padding: 0 24px 12px;
            min-height: 250px;
        }
    }
</style>
