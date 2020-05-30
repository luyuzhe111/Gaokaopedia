<template>
	<view class="content">
		<image class="bg" src="../../static/bg_img2.png"></image>
		<image class="photo" :src="token=='' ? '../../static/touxiang_img@2x.png' :head_image" style="border-radius:50%;"></image>
		<view class="title">{{userinfolist.nickname}}</view>
		<view class="cardchang">
			<image class="banner" src="../../static/banner_img@2x.png" @click="toBuyCard"></image>
			<view class="cardread">
				<view class="buynow">立即购买畅读卡</view>
				<view class="readmany">畅读更多优秀文章</view>
			</view>
			
		</view>
		
		<view class="item" @click="togeren">
			<view class="name">
				<image class="icon" src="../../static/8_icon@2x.png"></image>个人信息
			</view>
			<image class="right" src="../../static/dizhi_btn@2x.png"></image>
		</view>

		<view class="item" @click="tocellect">
			<view class="name">
				<image class="icon" src="../../static/7_icon@2x.png"></image>收藏文章
			</view>
			<image class="right" src="../../static/dizhi_btn@2x.png"></image>
		</view>

		<view class="item" @click="tofollew">
			<view class="name">
				<image class="icon" src="../../static/6_icon@2x.png"></image>关注的人
			</view>
			<image class="right" src="../../static/dizhi_btn@2x.png"></image>
		</view>

		<view class="item" @click="tocustom">
			<view class="name">
				<image class="icon" src="../../static/5_icon@2x.png"></image>消费记录
			</view>
			<image class="right" src="../../static/dizhi_btn@2x.png"></image>
		</view>

		<view class="item" @click="tocomment">
			<view class="name">
				<image class="icon" src="../../static/4_icon@2x.png"></image>我的留言
			</view>
			<image class="right" src="../../static/dizhi_btn@2x.png"></image>
		</view>
		<!-- <tab></tab> -->
		<!-- <tab :current="currentTabIndex" @getData="tabClick"></tab> -->
		<!-- <view class="teacherfooter" v-if="type==1">
			
			<view class="teacherfootitem"  @click="jumpschool==true?footerselChange(1,'/pages/school/school?type=1'):''" :data-id="1" :data-url="'/pages/school/school?type='+1">
				<view class="teacherfootitemtop" >
					<image :src="footersel==1?'/static/xuexiao_icon@2x.png':'/static/xuexiao_icon@2x3.png'"></image>
				</view>
				<view class="teacherfootname" :class="footersel==1?'selactive':''">学校</view>
			</view>
			<view class="teacherfootitem" @click="jumpschool==true?footerselChange(2,'/pages/schoolmate/schoolmate?type=1'):''" :data-id="2" :data-url="'/pages/schoolmate/schoolmate?type='+1">
				<view class="teacherfootitemtop">
					<image :src="footersel==2?'/static/zhaoxiaoyou_icon@2x2.png':'/static/zhaoxiaoyou_icon@2x.png'"></image>
				</view>
				<view class="teacherfootname" :class="footersel==2?'selactive':''">找校友</view>
			</view>
			<view class="teacherfootitem"  :data-id="3" :data-url="'/pages/homePage/homePage?type='+1">
				<view class="teacherfootitemtop">
					<image :src="footersel==3?'/static/wodezhuye_icon@2x2.png':'/static/wodezhuye_icon@2x.png'"></image>
				</view>
				<view class="teacherfootname" :class="footersel==3?'selactive':''">我的主页</view>
			</view>
		</view>
		<view class="collegefooter" v-if="type == 2">
			<view class="teacherfootitem" @click="jumpschoolk==true?footerselChangek(2,'/pages/schoolmate/schoolmate?type=2'):''" :data-id="2" :data-url="'/pages/schoolmate/schoolmate?type='+2">
				<view class="teacherfootitemtop">
					<image :src="footersel==2?'/static/zhaoxiaoyou_icon@2x2.png':'/static/zhaoxiaoyou_icon@2x.png'"></image>
				</view>
				<view class="teacherfootname" :class="footersel==2?'selactive':''">找校友</view>
			</view>
			<view class="teacherfootitem"  :data-id="3" :data-url="'/pages/homePage/collegeHome?type='+2">
				<view class="teacherfootitemtop">
					<image :src="footersel==3?'/static/wodezhuye_icon@2x2.png':'/static/wodezhuye_icon@2x.png'"></image>
				</view>
				<view class="teacherfootname" :class="footersel==3?'selactive':''">我的主页</view>
			</view>
  		</view>
	
	 -->
	 
	 <tabBar :current="currentTabIndex" backgroundColor="#fbfbfb" color="#999" tintColor="#42b983"></tabBar>
	</view>
</template>

<script>
	import app from "../../App.vue";
	import tabBar from '../../components/tabvue/tabvue.vue'
	
	export default {
		components: {
			tabBar,
		},
		data() {
			return {
				currentTabIndex: 2,
				footersel:3,
				type:"",
				userinfolist:{},
				token:'',
				head_image:'',
				jumpschool:true,
				jumpschoolk:true,
				
			}
		},
		methods: {
			toBuyCard() {
				if(uni.getStorageSync('token')==''){
					uni.navigateTo({
						url:'../register/register?istoken='+1
					})
				}else{
					wx.navigateTo({
						url:'./buyCard'
					})
				}
			},
			// 底部导航跳转
			footerselChange(id,url){
				// let url=e.currentTarget.dataset.url;
				console.log(url)
				uni.redirectTo({
				
					url:url
				})
				this.jumpschool=false
			},
			footerselChangek(id,url){
				uni.redirectTo({
				
					url:url
				})
				this.jumpschoolk=false
			},
			getuserinfo(){
				var that = this
				var url = "student/getMyInfo"
				var token = uni.getStorageSync('token')
				var params = {
				token:token
				}
				app.post(url,params,"get").then((res)=>{
					console.log(res)
					that.userinfolist=res;
					console.log(res.head_image)
					that.head_image=res.head_image
				}).catch((err)=>{
					console.log(err)
            	})
			},
			// tocomment(){
			// 	uni.navigateTo({
			// 		url:'./mycomment?user_id='+this.userinfolist.user_id
			// 	})
			// },
			togeren(){
				if(uni.getStorageSync('token')==''){
					uni.showToast({
						title:"请登陆后操作",
						icon:"none"
					})
					setTimeout(() => {
						uni.navigateTo({
							url:'../register/register?istoken='+1
						})
					},1500);
				}else{
					wx.navigateTo({
						url:'./gerencenter'
					})
				}
			},
			tocellect(){
				if(uni.getStorageSync('token')==''){
					uni.showToast({
						title:"请登陆后操作",
						icon:"none"
					})
					setTimeout(() => {
						uni.navigateTo({
							url:'../register/register?istoken='+1
						})
					},1500);
				}else{
					wx.navigateTo({
						url:'./cellect'
					})
				}
			},
			tofollew(){
				if(uni.getStorageSync('token')==''){
					uni.showToast({
						title:"请登陆后操作",
						icon:"none"
					})
					setTimeout(() => {
						uni.navigateTo({
							url:'../register/register?istoken='+1
						})
					},1500);
				}else{
					wx.navigateTo({
						url:'./followPeople'
					})
				}
			},
			tocustom(){
				if(uni.getStorageSync('token')==''){
					uni.showToast({
						title:"请登陆后操作",
						icon:"none"
					})
					setTimeout(() => {
						uni.navigateTo({
							url:'../register/register?istoken='+1
						})
					},1500);
				}else{
					wx.navigateTo({
						url:'./consumePage'
					})
				}
			},
			tocomment(){
				if(uni.getStorageSync('token')==''){
					uni.showToast({
						title:"请登陆后操作",
						icon:"none"
					})
					setTimeout(() => {
						uni.navigateTo({
							url:'../register/register?istoken='+1
						})
					},1500);
				}else{
					uni.navigateTo({
					url:'./mycomment?user_id='+this.userinfolist.user_id
				})
				}
			}
		},
		onLoad(options){
			this.token=uni.getStorageSync('token')
			console.log(options.type)
			var type=options.type
			this.type=type
			this.getuserinfo()
		},
	}
</script>

<style lang="less">
	
	
	.content {
		text-align: center;
		color: rgba(6, 18, 30, 1);
		padding-bottom: 120rpx;
		.bg {
			width: 750rpx;
			height: 154rpx;
		}

		.photo {
			width: 136rpx;
			height: 136rpx;
			position: absolute;
			top: 52rpx;
			left: 50%;
			transform: translate(-50%);
		}

		.title {
			margin-top: 66rpx;
		}

		.banner {
			margin-top: 78rpx;
			width: 686rpx;
			height: 216rpx;
			margin-bottom: 30rpx;
		}
		.cardchang{
			position: relative;
		}
		.buynow{
			color:#fff;
			font-size: 28rpx;
			text-align: left;
		}
		.readmany{
			color:#fff;
			font-size: 36rpx;
			text-align: left;
			font-weight: bold;
		}
		.cardread{
			position: absolute;
			top:111rpx;
			left:72rpx;
		}

		.item {
			display: flex;
			align-items: center;
			justify-content: space-between;
			width: 686rpx;
			margin: 0 auto;
			padding: 32rpx 0;
			border-bottom: 1rpx solid rgba(238, 238, 238, 1);

			.icon {
				width: 36rpx;
				height: 36rpx;
				margin-right: 12rpx;
			}

			.name {
				font-size: 28rpx;
				display: flex;
				align-items: center;
			}

			.right {
				width: 10rpx;
				height: 19rpx;
			}
		}
	}
</style>
