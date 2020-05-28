<template>
	<view class="content">
		<view class="bg" style="position: relative;">
			<image src="../../static/bg_img@2x(2).png"></image>
			<view class="swiper_image" style="position: absolute;top:26rpx;left:58rpx">
				<swiper :indicator-dots="indicatorDots" :autoplay="autoplay" :interval="interval" :duration="duration" class="swiper_item_img"
				 @change="swiperChange" :circular="true">
					<block v-for="(item,index) in cardlist" :key="index">
						<swiper-item style="position: relative;">
							<image src="../../static/card_img@2x.png" class="slide-image" />
							<view style="position: absolute;top:62rpx;left:40rpx;width:500rpx;height:284rpx">
								<view class="title">{{item.name}}</view>
								<view class="money">{{item.money}}/月</view>
								<view style="display:flex;align-items:center;margin-top:70rpx;justify-content: space-between;width:556rpx">
									<view class="time" v-if="item.vip_endtime">到期日期：{{item.vip_endtime}}</view>
									<view class="time" v-else style="width:200rpx"></view>
									<view class="btn" style="margin-left:20rpx" @click="buycard" :data-id="item.id">立即购买<image class="icon" src="../../static/dizhi_btn@2x2.png"></image>
									</view>
								</view>

							</view>
						</swiper-item>

					</block>
				</swiper>
				<view class="dots">
					<block v-for="(item,index) in cardlist" :key="index">
						<view class="dot" :class="index == currentSwiper ? ' active' : ''"></view>
					</block>
				</view>
			</view>
		</view>



		<!-- <view class="cardText">
		  <view style="position: relative;">
			  <swiper class="swiper" style="636rpx;height:360rpx;position: relative;" :indicator-dots="indicatorDots" :autoplay="autoplay" :interval="interval" :duration="duration" :indicator-color="indicatorColor" :indicator-active-color="indicatorActiveColor" @change="swiperChange" :circle="true" circular="true">
			     
			      <swiper-item style="636rpx;height:360rpx" v-for="(item,index) in cardlist" :key="index">
			  				 
			  				  		  <image  src="../../static/card_img@2x.png"></image>
			  						  <view style="position: absolute;top:62rpx;left:40rpx;width:500rpx;height:284rpx">
			  							  <view class="title">{{item.name}}</view>
			  							  <view class="money">{{item.money}}/月</view>
			  							  <view style="display:flex;align-items:center;margin-top:70rpx;justify-content: space-between;width:556rpx">
			  								  <view class="time" v-if="item.vip_endtime">到期日期：{{item.vip_endtime}}</view>
			  								   <view class="time" v-else style="width:200rpx"></view>
			  								  <view class="btn" style="margin-left:20rpx" @click="buycard" :data-id="item.id">立即购买<image class="icon" src="../../static/dizhi_btn@2x2.png"></image></view>
			  							  </view>
			  							  
			  						  </view>
			      </swiper-item>
			  
			    
			  </swiper>
			           
			  <view class="dots">
			    <block  v-for="(item,index) in cardlist" :key="index">
			      <view class="dot" :class="index == currentSwiper ? 'active' : ''"></view>
			    </block>
			  </view>
		  </view>
          
         
      </view>
     
	 -->


		<view class="textBox">
			YHA青年旅舍会员卡（一年）是国际青年旅舍联盟会员身份证明，全球通用，也是旅行者入住青年旅舍的凭证。拥有会员卡，可以享受国内外国际青年旅舍住宿价格的优惠，同时部分海外青年旅舍只允许会员入住。
			成为YHA青年旅舍会员的好处：
			<view>1）入住青年旅舍享受房价上的优惠；</view>
			<view>2）在世界各地享有食、住、行、游、购、娱等逾3,000项优惠，如：在全球多个国际机场和车船站，凭会员卡兑换外币可免收手续费；观光、租车、购物、参团、购买车船票等均可能有折扣，折扣率高达50%。单是在澳大利亚，优惠项目便接近800种；</view>
			<view>3）以优惠价格参与多项由青年旅舍举办的各类文化、旅游等方面的交流活动；</view>
			<view>4）优先参与青年旅舍组织的国际交流活动。</view>

		</view>
	</view>
</template>

<script>
	import app from "../../App.vue";
	export default {
		data() {
			return {
				name: '',
				province_id: '',
				city_id: '',
				cardlist: [],
				background: ['color1', 'color2', 'color3'],
				indicatorColor: 'rgba(255,255,255,1)',
				indicatorActiveColor: '#ffffff',
				imgUrls: [1, 2],
				vip_id: '',
				indicatorDots: false,
				autoplay: true,
				interval: 3000,
				duration: 2000,
				currentSwiper: 0,
			}
		},
		methods: {
			getcardmsg() {
				var that = this
				var url = "vip/getVipConf"
				var params = {
					name: that.name,
					province_id: that.name,
					city_id: that.name
				}
				app.post(url, params, "post").then((res) => {
					console.log(res)
					that.cardlist = res
				}).catch((err) => {
					console.log(err)
				})
			},

			swiperChange: function(e) {
				this.currentSwiper = e.detail.current

			},
			buycard(e) {
				var that = this
				let vip_id = e.currentTarget.dataset.id;
				console.log(vip_id)
				var url = "vip/buyVip"
				var header = {
					// token:uni.getStorageSync('token'),

				}
				var params = {
					token: uni.getStorageSync('token'),
					vip_id: vip_id
				}
				console.log(params)
				app.post(url, params, "post").then((res) => {
					console.log(res)
					that.payment(res)
				}).catch((err) => {
					console.log(err)
				})
			},
			payment(res) {
				let that = this;
				var timeStamp = res.timeStamp.toString();
				// console.log(timeStamp);
				wx.requestPayment({
					timeStamp: timeStamp,
					nonceStr: res.nonceStr,
					package: res.package,
					signType: res.signType,
					paySign: res.paySign,
					success: function(res) {
						console.log(res)
						that.getcardmsg()
						// wx.navigateTo({
						// url: '/pages/orderbox/orderbox?index=' + 0
						// })
					},
					fail: function(res) {
						console.log(123);
						console.log(res);
					}
				})
			},
		},
		onLoad() {
			this.getcardmsg()
		}
	}
</script>

<style lang="less">
	.content {

		/* 轮播图 */
		.swiper_image {
			width: 636rpx;
			height: 360rpx;
			font-size: 0;
			margin: 0 auto;
			position: relative;
			// border-radius: 20rpx;
		}

		swiper {
			width: 636rpx;
			height: 360rpx;
			// border-radius: 20rpx;
		}

		.swiper_image image {
			width: 100%;
			height: 100%;
			border-radius: 20rpx;
		}

		.swiper_item_img swiper-item {
			width: 636rpx;
			height: 360rpx;
			// border-radius: 20rpx;
		}

		.swiper_item_img swiper-item image {
			width: 100%;
			height: 100%;
			border-radius: 20rpx;
		}

		swiper-item {
			position: relative;
		}

		swiper-item image {
			border-radius: 20rpx;
		}

		.picimg {
			width: 156rpx;
			height: 76rpx;
			position: absolute;
			right: 36rpx;
			bottom: 30rpx;
		}

		.dots {
			height: 36rpx;
			display: flex;
			flex-direction: row;
			position: absolute;
			left: 50%;
			transform: translateX(-50%);
			bottom: 10rpx;
		}

		/*未选中时的小圆点样式 */

		.dot {
			width: 8rpx;
			height: 8rpx;
			border-radius: 50%;
			margin-right: 16rpx;
			background-color: #fff;
		}

		/*选中以后的小圆点样式  */

		.active {
			width: 24rpx;
			height: 8rpx;
			border-radius: 10rpx;
			background-color: #fff;
		}
		.title {
			color: rgba(130, 75, 59, 1);
			font-size: 50rpx;
		
		}
		
		.money {
			color: rgba(130, 75, 59, 1);
			font-size: 36rpx;
		}
		
		.time {
			color: rgba(130, 75, 59, 1);
			font-size: 24rpx;
			// margin-top: 70rpx;
			// position: absolute;
			// left:26rpx;
			// bottom:68rpx
		}
		
		.btn {
			width: 206rpx;
			height: 60rpx;
			background: rgba(130, 75, 59, 1);
			opacity: 1;
			border-radius: 42rpx;
			color: rgba(255, 255, 255, 1);
			font-size: 32rpx;
			text-align: center;
			line-height: 60rpx;
		
			// margin-left:20rpx;
			// position: absolute;
			// right: 26rpx;
			// bottom: 52rpx;
			.icon {
				width: 15rpx;
				height: 27rpx;
				margin-left: 10rpx;
			}
		}


		.bg {
			width: 750rpx;
			height: 292rpx;
		}

		.card {
			width: 636rpx;
			height: 360rpx;
			// position: absolute;
			// top: 26rpx;
			// left: 50%;
			// transform: translate(-50%);
		}

		.cardText {
			width: 636rpx;
			height: 360rpx;
			position: absolute;
			top: 26rpx;
			left: 50%;
			transform: translate(-50%);
			// padding: 30rpx 40rpx;
			// box-sizing: border-box;
			z-index: 777;

			.title {
				color: rgba(130, 75, 59, 1);
				font-size: 50rpx;

			}

			.money {
				color: rgba(130, 75, 59, 1);
				font-size: 36rpx;
			}

			.time {
				color: rgba(130, 75, 59, 1);
				font-size: 24rpx;
				// margin-top: 70rpx;
				// position: absolute;
				// left:26rpx;
				// bottom:68rpx
			}

			.btn {
				width: 206rpx;
				height: 60rpx;
				background: rgba(130, 75, 59, 1);
				opacity: 1;
				border-radius: 42rpx;
				color: rgba(255, 255, 255, 1);
				font-size: 32rpx;
				text-align: center;
				line-height: 60rpx;

				// margin-left:20rpx;
				// position: absolute;
				// right: 26rpx;
				// bottom: 52rpx;
				.icon {
					width: 15rpx;
					height: 27rpx;
					margin-left: 10rpx;
				}
			}

			.dots {
				position: absolute;
				bottom: 36rpx;
				left: 50%;
				transform: translate(-50%);
				width: 50rpx;
				height: 10rpx;
				z-index: 999;

				// overflow: hidden;
				// margin:0 auto;
				// display: flex;
				// justify-content: space-between;
				.dot {
					width: 10rpx;
					height: 10rpx;
					background-color: #fff;
					border-radius: 12rpx;
					float: left;
				}

				.active {
					width: 30rpx;
					height: 10rpx;
				}
			}

			.big {
				width: 24rpx;
				height: 8rpx;
				position: absolute;
				bottom: 10rpx;
				left: 298rpx;
			}

			.small {
				width: 8rpx;
				height: 8rpx;
				position: absolute;
				bottom: 10rpx;
				left: 330rpx;
			}
		}

		.textBox {
			font-size: 24rpx;
			width: 640rpx;
			margin: 0 auto;
			margin-top: 138rpx;
		}
	}
</style>
