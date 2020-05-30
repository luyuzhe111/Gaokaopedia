<template>
	<view class="content">
		<view class="teacherfooter">
			<!-- @click="footerselChange" :data-id="1" :index="1" -->
			<view class="teacherfootitem" @tap="switchTab(item)" v-for="(item,index) in tabList" :key="index" :data-path="item.pagePath">
				<view class="teacherfootitemtop">
					<image :src="current==index?item.iconactive:item.icon"></image>
				</view>
				<view class="teacherfootname" :class="current==index?'selactive':''">{{item.text}}</view>
			</view>
			<!-- <view class="teacherfootitem" @click="footerselChange" :data-id="2">
				<view class="teacherfootitemtop">
					<image :src="footersel==2?'/static/zhaoxiaoyou_icon@2x2.png':'/static/zhaoxiaoyou_icon@2x.png'"></image>
				</view>
				<view class="teacherfootname" :class="footersel==2?'selactive':''">找校友</view>
			</view>
			<view class="teacherfootitem" @click="footerselChange" :data-id="3">
				<view class="teacherfootitemtop">
					<image :src="footersel==3?'/static/wodezhuye_icon@2x2.png':'/static/wodezhuye_icon@2x.png'"></image>
				</view>
				<view class="teacherfootname" :class="footersel==3?'selactive':''">我的主页</view>
			</view> -->
		</view>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				footersel: 1,
				tabList: [{
						icon: '/static/xuexiao_icon@2x3.png',
						iconactive:'/static/xuexiao_icon@2x.png',
						text: '学校',
						pagePath: "/pages/school/school",
						index:0
					},
					{
						icon: '/static/zhaoxiaoyou_icon@2x.png',
						iconactive:'/static/zhaoxiaoyou_icon@2x2.png',
						text: '我的校友',
						pagePath: "/pages/schoolmate/schoolmate",
						index:1
					},
					{
						icon: '/static/wodezhuye_icon@2x.png',
						iconactive:'/static/wodezhuye_icon@2x2.png',
						text: '我的主页',
						pagePath: "/pages/homePage/homePage",
						index:2
					}
				],
				currentTabIndex: this.current
			}
		},
		props: ["current"],
		methods: {
			
			switchTab(item){
				// console.log(item)
				let url=item.pagePath;
				// console.log(url)
				uni.switchTab({url})
				// this.$emit('getData', item.index)
				// this.currentTabIndex = item.index;
				
			}
			// footerselChange(event) {
			// 	console.log(event)
			// 	console.log(event.currentTarget.dataset.id)
			// 	var id = event.currentTarget.dataset.id
			// 	this.footersel = id;
			// 	this.$emit("returnDate", this.footersel)

			// 	if (id == 1) {
			// 		wx.redirectTo({
			// 			url: "/pages/school/school"
			// 		})
			// 	} else if (id == 2) {
			// 		wx.redirectTo({
			// 			url: "/pages/schoolmate/schoolmate"
			// 		})
			// 	} else {
			// 		wx.redirectTo({
			// 			url: "/pages/homePage/homePage"
			// 		})
			// 	}


			// }
		}
	}
</script>

<style>
	.teacherfooter {
		width: 750rpx;
		display: flex;
		align-items: center;
		justify-content: space-between;
		padding: 18rpx 82rpx;
		box-sizing: border-box;
		position: fixed;
		bottom: 0;
		left: 0;
		background: #fff;
		font-size: 22rpx;
	}

	.teacherfootitem {
		display: felx;
		flex-direction: column;
		align-items: center;
		justify-content: center;
	}

	.teacherfootitemtop {
		margin: 0 auto;
		display: flex;
		justify-content: center;
	}

	.shou {
		width: 37rpx;
		height: 37rpx;
		font-size: 0;
	}

	image {
		width: 48rpx;
		height: 48rpx;
		margin: 0 auto;
	}
</style>
