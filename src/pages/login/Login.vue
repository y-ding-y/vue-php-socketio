<template>
	<common-layout>
		<div class="top">
			<div class="header">
				<header-icon :size="iconSize" />
				<span class="title">{{ systemName }}</span>
			</div>
			<div class="desc">{{ $t('description') }}</div>
		</div>
		<div class="login">
			<a-form @submit="onSubmit" :form="form">
				<a-tabs size="large" :tabBarStyle="{ textAlign: 'center' }" style="padding: 0 2px;">
					<a-tab-pane :tab="$t('tabtitle')" key="1">
						<a-alert type="error" :closable="true" v-show="error" :message="error" showIcon
							style="margin-bottom: 24px;" />
						<a-form-item>
							<a-input autocomplete="autocomplete" size="large" :placeholder="$t('nameplaceholder')"
								v-decorator="[
                  'username',
                  {
                    rules: [
                      {
                        required: true,
                        message: $t('nametip'),
                        whitespace: true,
                      },
                    ],
                  },
                ]">
								<a-icon slot="prefix" type="user" />
							</a-input>
						</a-form-item>
						<a-form-item>
							<a-input size="large" :placeholder="$t('pwdplaceholder')" autocomplete="autocomplete"
								type="password" v-decorator="[
                  'password',
                  {
                    rules: [
                      {
                        required: true,
                        message: $t('pwdtip'),
                        whitespace: true,
                      },
                    ],
                  },
                ]">
								<a-icon slot="prefix" type="lock" />
							</a-input>
						</a-form-item>
					</a-tab-pane>
				</a-tabs>
				<div>
					<a-checkbox :checked="true" v-decorator="['savelogin']">{{
						$t('autologin')
						}}</a-checkbox>
				</div>
				<a-form-item>
					<a-button :loading="logging" style="width: 100%;margin-top: 24px" size="large" htmlType="submit"
						type="primary">{{ $t('login') }}</a-button>
				</a-form-item>
			</a-form>
		</div>
	</common-layout>
</template>

<script>
	import CommonLayout from '@/layouts/CommonLayout'
	import HeaderIcon from '@/layouts/header/HeaderIcon'
	import { login, getRoutesConfig } from '@/services/user'
	import { setAuthorization } from '@/utils/request'
	import { loadRoutes } from '@/utils/routerUtil'
	import { sayHello } from '@/utils/util'
	import { mapState, mapMutations } from 'vuex'

	export default {
		name: 'Login',
		i18n: require('./i18n'),
		components: { CommonLayout, HeaderIcon },
		data() {
			return {
				iconSize: 36,
				logging: false,
				error: '',
				form: this.$form.createForm(this),
			}
		},
		computed: {
			...mapState('setting', ['lang']),
			systemName() {
				return this.$store.state.setting.systemName
			},
		},
		methods: {
			...mapMutations('account', [
				'setUser',
				'setToken',
				'setPermissions',
				'setRoles',
			]),
			...mapMutations('setting', ['setLang']),
			onSubmit(e) {
				e.preventDefault()
				this.form.validateFields((err) => {
					if (!err) {
						this.logging = true
						const username = this.form.getFieldValue('username')
						const password = this.form.getFieldValue('password')
						const savelogin = this.form.getFieldValue('savelogin')
						login(username, password, savelogin).then(this.afterLogin)
					} else {
						this.logging = false
					}
				})
			},
			afterLogin(res) {
				this.logging = false
				const loginRes = res.data
				if (loginRes.code >= 0) {
					const { user, permissions, roles } = loginRes.data
					this.setUser(user)
					this.setPermissions(permissions)
					this.setRoles(roles)
					setAuthorization({
						token: loginRes.data.token,
						expireAt: new Date(loginRes.data.expireAt),
					})
					this.setLang(user.lang || process.env.VUE_APP_LANG)

					/**
					 * 获取路由配置
					 */
					getRoutesConfig().then((result) => {
						const routesConfig = result.data.data
						loadRoutes(routesConfig)
						const path = this.$route.path
						/**
						 * 如果登錄頁面就是當前頁,則跳轉到用戶設定的首頁或site首頁
						 * 否則就刷新當前頁
						 */
						if (path === '/login') {
							this.$router.push(user.homepage || '/' + process.env.VUE_APP_SITE)
						} else {
							this.$router.go(0)
						}
						this.$message.success(
							this.$t('welcome', {
								name: user.name,
								hello: sayHello()[this.lang],
							}),
							3
						)
					})
				} else {
					this.error = loginRes.message
				}
			},
		},
	}
</script>

<style lang="less" scoped>
	.common-layout {
		.top {
			text-align: center;

			.header {
				height: 40px;

				.title {
					height: 40px;
					position: relative;
					top: 7px;
					color: @title-color;
					font-family: 'Myriad Pro', 'Helvetica Neue', Arial, Helvetica,
						sans-serif;
					font-size: 26px;
					font-weight: 600;
					margin-left: 30px;
				}
			}

			.desc {
				font-size: 14px;
				color: @text-color-second;
				margin: 12px 0;
			}
		}

		.login {
			width: 300px;
			margin: 0 auto;

			@media screen and (max-width: 576px) {
				width: 95%;
			}

			@media screen and (max-width: 320px) {
				.captcha-button {
					font-size: 14px;
				}
			}
		}
	}
</style>
