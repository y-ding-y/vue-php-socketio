<template>
    <div class="ant-notification-notice" :style="{height: onlineheight+'px',width: '300px' }">
        <div style="height: 100%;overflow-y:scroll">
            <div class="header-item"> 在線人數：{{$store.getters['socket/msg'].onlineCount}}</div>
            <a-list item-layout="horizontal" :data-source="onlinelist">
                <a-list-item slot="renderItem" slot-scope="item">
                    <a-list-item-meta :description="item.deptname">
                        <a-avatar slot="avatar" class="avatar" size="large" shape="circle"
                            :src="item.avatar ? (icons + '/file/' + item.avatar + '/large') : '/avatar.png'">
                        </a-avatar>
                        <a slot="title" @click="hanlde_chat(item)">{{ item.name }}
                            <a-tag v-if="item.newcount>0" color="red">
                                未读：{{item.newcount}}
                            </a-tag>
                        </a>
                        <!-- {{$store.getters['socket/newmsg']}} -->
                    </a-list-item-meta>
                </a-list-item>
            </a-list>
        </div>

        <a-modal :key="i" :title="toname" :visible="visible" :confirm-loading="confirmLoading" @ok="handleOk"
            @cancel="handleCancel">
            <div ref="chardiv" :style="{height:mainchatheight+'px'}"
                style="background-color:rgb(255, 235, 214);overflow-y: scroll;padding-top: 10px;padding-bottom: 10px;">
                <div ref="content">
                    <div v-for="(item,index) in chatlist" :key="index" style="margin-top:5px ;">
                        <div v-if="item.type=='my'"
                            style="display:flex;justify-content: end; width: 100%;   text-align: right;padding-right: 10px; ">
                            <div style="margin-right: 5px;">
                                <span
                                    style="font-size: 2px;color:grey;height:4px ;line-height:4px ;font-weight: 100;">{{item.timers}}</span>
                                <div style="font-size: 18px;height:24px ;line-height:24px ;font-weight: 500;">
                                    {{item.msg}}</div>
                            </div>
                            <div>
                                <a-avatar slot="avatar" class="avatar" size="large" shape="circle"
                                    :src="myavatar ? (icons + '/file/' + myavatar + '/large') : '/avatar.png'">
                                </a-avatar>
                            </div>
                        </div>

                        <div v-else-if="item.type=='user'"
                            style="display:flex;width: 100%; text-align: left;padding-left: 10px;  ">
                            <div>
                                <a-avatar slot="avatar" class="avatar" size="large" shape="circle"
                                    :src=" toavatar ? (icons + '/file/' +  toavatar + '/large') : '/avatar.png'">
                                </a-avatar>
                            </div>
                            <div style="margin-left:5px">
                                <span
                                    style="font-size: 2px;color:grey;height:4px ;line-height:4px ;font-weight: 100;">{{item.timers}}</span>
                                <div style="font-size: 18px;height:24px ;line-height:24px ;font-weight: 500;">
                                    {{item.msg}}</div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <div>
                <a-input v-model="msg" type="textarea" style="background-color:rgb(231, 231, 231)"
                    :style="{height:messageheight+'px'}">
                </a-input>
            </div>
        </a-modal>
    </div>

</template>
<script>
    import { mapState } from 'vuex'
    export default {
        name: "HeaderOnlinelist",
        data() {
            return {
                icons: process.env.VUE_APP_API_WEB_URL,
                ModalText: 'Content of the modal',
                visible: false,
                confirmLoading: false,
                msg: "",
                toname: "",
                toempno: "",
                toavatar: "",
                myavatar: this.$store.getters['account/user'].avatar,
                myempno: this.$store.getters['account/user'].empno,
                i: 0
            };
        },
        mounted() {

        },
        computed: {
            ...mapState('setting', ['lang', 'pageMinHeight']),
            onlinelist() {
                var arr = []
                for (var key in this.$store.getters['socket/empnolist']) {
                    arr.push(this.$store.getters['socket/empnolist'][key])
                }
                return arr
            },
            chatlist() {
                return this.$store.getters['socket/chat'][this.toempno]
            },
            onlineheight() {
                return (this.pageMinHeight / 4) * 3
            },

            mainchatheight() {
                return (this.pageMinHeight / 5) * 2
            },
            messageheight() {
                return (this.pageMinHeight / 5)
            },
        },
        watch: { 
            chatlist() {
                this.$nextTick(() => {
                    this.$refs["chardiv"].scrollTop = this.$refs["chardiv"].scrollHeight - 50;
                })
            },

        },
        methods: {
            handleOk() {
                if (this.$store.getters['socket/empnolist'][this.toempno] != undefined) {
                    var params = {
                        msg: this.msg,
                        toempno: this.toempno,
                        type: "my",
                        toavatar: this.toavatar,
                        myempno: this.myempno,
                        timers: new Date().format('Y-m-d H:i:s')
                    };
                    this.msg = "";
                    this.$socket.emit("chat", params)
                }
                else {
                    this.$message.error("不在线")
                }
            },
            handleCancel() {
                this.visible = false;
                this.msg = "";
                this.toempno = ""
                this.$store.commit("socket/setpersonlcount", "");
            },
            hanlde_chat(e) {
                this.toempno = e.empno
                this.toavatar = e.avatar
                this.toname = e.name
                if (e.newcount == undefined)
                    e.newcount = 0
                this.$store.commit("socket/setpersonlcount", e.empno);
                if (this.toempno != this.myempno) {
                    this.visible = true;
                }
            }
        },
    };
</script>



<style lang="less">
    *::-webkit-scrollbar {
        width: 7px;
        height: 10px;
        background-color: transparent;
    }

    /*定义滚动条高宽及背景 高宽分别对应横竖滚动条的尺寸*/
    *::-webkit-scrollbar-track {
        background-color: @primary-4;
    }

    /*定义滚动条轨道 内阴影+圆角*/
    *::-webkit-scrollbar-thumb {
        background-color: @primary-color;
        border-radius: 6px;
    }

    /*定义滑块 内阴影+圆角*/
    .scrollbarHide::-webkit-scrollbar {
        display: none
    }

    .scrollbarShow::-webkit-scrollbar {
        display: block
    }
</style>
