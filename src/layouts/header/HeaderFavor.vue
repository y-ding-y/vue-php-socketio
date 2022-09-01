<template>
  <a-dropdown class="header-item">
    <div>
      <a-tooltip
        placement="left"
        :title="$t(isFavored ? 'cancle' : 'collect')"
        slot="tabBarExtraContent"
      >
        <a-icon
          @click="checkFavor"
          type="heart"
          :theme="isFavored ? 'filled' : 'outlined'"
          :class="isFavored ? 'favor' : null"
        />
      </a-tooltip>
    </div>
    <a-menu :selected-keys="[$route.path]" slot="overlay">
      <a-menu-item v-for="item in favorList" :key="item.url"
        ><a :href="item.url" :target="item.type == 'link' ? '_blank' : '_self'"
          ><a-icon :type="item.type"/> <span v-html="favorName(item)"></span></a
      ></a-menu-item>
    </a-menu>
  </a-dropdown>
</template>

<script>
import { reqGet, reqPost, reqDelete } from '@/services/base'
import { getFullPathName } from '@/utils/routerUtil'
import { _debounce } from '@/utils/util'

export default {
  name: 'HeaderFavor',
  i18n: {
    messages: {
      cn: {
        collect: '点击可收藏此页面',
        cancle: '点击可取消收藏此页面',
        favordelete: '已经删除 {name}',
        favorinsert: '已经收藏 {name}',
      },
      tw: {
        collect: '點擊可收藏此頁面',
        cancle: '點擊可取消收藏此頁面',
        favordelete: '已經刪除 {name}',
        favorinsert: '已經收藏 {name}',
      },
      us: {
        collect: 'click to collect this page',
        cancle: 'click to cancle the collection of this page',
        favordelete: 'delete {name} from favors',
        favorinsert: 'add {name} into favors',
      },
    },
  },
  data() {
    return {
      favorList: [],
    }
  },
  created() {
    reqGet('FAVOR').then((res) => {
      if (res.code !== 0) {
        this.$message.error(res.message, 2)
      } else {
        this.favorList = res.data
          .map((item) => {
            item.url = item.url === null ? '' : item.url
            item.type = item.url.toLowerCase().startsWith('http')
              ? 'link'
              : 'book'
            item.key = item.url
            return item
          })
          .filter((item) => item.display === 'Y')
      }
    })
  },
  computed: {
    isFavored: function() {
      return this.currFavor ? true : false
    },
    currFavor: function() {
      const path = this.$route.path
      const item = this.favorList.find((item) => {
        return item.url == path
      })
      return item
    },
    fullPathName: function() {
      return getFullPathName(this.$route.fullPath)
    },
  },
  methods: {
    changeFavor: function(item) {
      const { url } = item
      if (url.toLowerCase().startsWith('http')) window.open(url)
      else this.$router.push(url)
    },
    favorName: function(item) {
      return item.type === 'link'
        ? item.note || item.url
        : getFullPathName(item.url) || item.note
    },
    checkFavor: _debounce(function() {
      const data = {
        url: this.$route.path,
        note: this.$route.name,
        type: 'book',
        display: 'Y',
      }
      const msg = { name: getFullPathName(data.url) }

      if (this.isFavored) {
        const { sn, url } = this.currFavor
        reqDelete('FAVOR', { url, sn }).then((res) => {
          if (res.code === 0) {
            this.$message.warn(this.$t('favordelete', msg), 2)
            this.favorList = this.favorList.filter((e) => {
              return e.url !== data.url
            })
          } else {
            this.$message.error(res.message, 2)
          }
        })
      } else {
        this.favorList = [...this.favorList, data]
        reqPost('FAVOR', data).then((res) => {
          if (res.code === 0) {
            this.$message.success(this.$t('favorinsert', msg), 2)
          } else {
            this.$message.error(res.message, 2)
          }
        })
      }
    }),
  },
}
</script>

<style lang="less" scoped>
.favor {
  color: @primary-color;
}
</style>
