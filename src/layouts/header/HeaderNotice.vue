<template>
	<a-dropdown :trigger="['click']" v-model="show">
		<div slot="overlay">
			<a-spin :spinning="loading">
				<a-tabs class="dropdown-tabs" :tabBarStyle="{textAlign: 'center'}" :style="{width: '297px'}">
					<a-tab-pane tab="通知" key="1">

					</a-tab-pane>
					<a-tab-pane tab="消息" key="2">
						<a-list class="tab-pane">
							<a-list-item>
								<a-list-item-meta title="你收到了 14 份新周报" description="一年前">
									<a-avatar style="background-color: white" slot="avatar"
										src="https://gw.alipayobjects.com/zos/rmsportal/ThXAXghbEsBCCSDihZxY.png" />
								</a-list-item-meta>
							</a-list-item>
						</a-list>
					</a-tab-pane>
					<a-tab-pane tab="待办" key="3">
						<a-list class="tab-pane"></a-list>
					</a-tab-pane>
				</a-tabs>
			</a-spin>
		</div>
		<span @click="fetchNotice" class="header-notice">
			<a-badge class="notice-badge" count="9" :overflow-count="10">
				<a-icon :class="['header-notice-icon']" type="bell" />
			</a-badge>
		</span>
	</a-dropdown>
</template>

<script>
	import { mapGetters } from 'vuex'
	export default {
		name: 'HeaderNotice',
		data() {
			return {
				loading: false,
				show: false
			}
		},
		computed: {
			...mapGetters('account', ['user']),
		},
		created() {
			this.$socket.connect();
			this.$socket.emit('login', JSON.stringify(this.user));
		},
		methods: {
			fetchNotice() {
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
