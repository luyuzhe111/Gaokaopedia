<template>
  <view class="content">
    <image class="bg" src="../../static/bg_img2.png"></image>
    <image class="photo" :src="token=='' ? '../../static/touxiang_img@2x.png' : userinfolist.head_image"></image>
    <view class="title">{{userinfolist.nickname}}</view>
    <view  class="bannerBox" @click="toPubArticle">
        <image class="banner" src="../../static/fabuwenzhang_img@2x.png"></image>
        <view class="textBtn">发布文章<image class="rBtn" src="../../static/dizhi_btn@2x2.png"></image></view>
    </view>

    <view class="item" @click="togeren">
        <view class="name"><image class="icon" src="../../static/8_icon@2x.png"></image>个人信息</view>
        <image class="right" src="../../static/dizhi_btn@2x.png"></image>
    </view>

    <view class="item" @click="tocollegeCellect">
        <view class="name"><image class="icon" src="../../static/7_icon@2x.png"></image>我的文章</view>
        <image class="right" src="../../static/dizhi_btn@2x.png"></image>
    </view>

    <view class="item" @click="tofollowPeople">
        <view class="name"><image class="icon" src="../../static/6_icon@2x.png"></image>关注的人</view>
        <image class="right" src="../../static/dizhi_btn@2x.png"></image>
    </view>

    <view class="item" @click="tomywallet">
        <view class="name"><image class="icon" src="../../static/5_icon@2x.png"></image>我的钱包</view>
        <image class="right" src="../../static/dizhi_btn@2x.png"></image>
    </view>

    <view class="item" @click="tomycomment">
        <view class="name"><image class="icon" src="../../static/4_icon@2x.png"></image>我的留言</view>
        <image class="right" src="../../static/dizhi_btn@2x.png"></image>
    </view>
    <!-- <tab></tab> -->
    <!-- <view class="collegefooter" v-if="type == 2">
			<view class="teacherfootitem" @click="footerselChange" :data-id="2" data-jump="1" :data-url="'/pages/schoolmate/schoolmate?type='+2">
				<view class="teacherfootitemtop">
					<image :src="footersel==2?'/static/zhaoxiaoyou_icon@2x2.png':'/static/zhaoxiaoyou_icon@2x.png'"></image>
				</view>
				<view class="teacherfootname" :class="footersel==2?'selactive':''">找校友</view>
			</view>
			<view class="teacherfootitem" @click="footerselChange" :data-id="3" >
				<view class="teacherfootitemtop">
					<image :src="footersel==3?'/static/wodezhuye_icon@2x2.png':'/static/wodezhuye_icon@2x.png'"></image>
				</view>
				<view class="teacherfootname" :class="footersel==3?'selactive':''">我的主页</view>
			</view>
  	</view> -->
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
data(){
	
    return{
        currentTabIndex: 1,
		footersel:3,
        type:"",
        userinfolist:{},
        token:'',
		user_id:""
    }
},
methods:{
    toPubArticle(){
        wx.navigateTo({
            url:"./pubArticle"
        })
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
					that.user_id=res.user_id
				}).catch((err)=>{
					console.log(err)
            	})
    },
    	// 底部导航跳转
			footerselChange(e){
				let url=e.currentTarget.dataset.url;
				
				
				uni.reLaunch({
					
					url:url
				})
            },
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
					}, 1500);
		}else{
			wx.navigateTo({
			    url:'./collegegerencenter'
			})
		}
    },
    tocollegeCellect(){
        if(uni.getStorageSync('token')==''){
			uni.showToast({
						title:"请登陆后操作",
						icon:"none"
					})
					setTimeout(() => {
						uni.navigateTo({
							url:'../register/register?istoken='+1
						})
					}, 1500);
		}else{
			wx.navigateTo({
			    url:'./collegeCellect'
			})
		}
    },
    tofollowPeople(){
        if(uni.getStorageSync('token')==''){
			uni.showToast({
						title:"请登陆后操作",
						icon:"none"
					})
					setTimeout(() => {
						uni.navigateTo({
							url:'../register/register?istoken='+1
						})
					}, 1500);
		}else{
			wx.navigateTo({
			    url:'./followPeople'
			})
		}
    },
    tomywallet(){
        if(uni.getStorageSync('token')==''){
			uni.showToast({
						title:"请登陆后操作",
						icon:"none"
					})
					setTimeout(() => {
						uni.navigateTo({
							url:'../register/register?istoken='+1
						})
					}, 1500);
		}else{
			wx.navigateTo({
			    url:'./mywallet'
			})
		}
    },
    tomycomment(){
        if(uni.getStorageSync('token')==''){
			uni.showToast({
						title:"请登陆后操作",
						icon:"none"
					})
					setTimeout(() => {
						uni.navigateTo({
							url:'../register/register?istoken='+1
						})
					}, 1500);
		}else{
			wx.navigateTo({
			    url:'./mycomment?user_id='+this.user_id
			})
		}
    }
},
onLoad(options){
			console.log(options.type)
			var type=options.type
            this.type=type
            
            this.token=uni.getStorageSync('token')
        },
        onShow(){
            this.getuserinfo()
        }
}
</script>

<style lang="less">
.content{
    text-align: center;
    color:rgba(6,18,30,1);
    .bg{
        width: 750rpx;
        height: 154rpx;
    }
    .photo{
        width: 136rpx;
        height: 136rpx;
        position: absolute;
        top: 52rpx;
        left: 50%;
        transform: translate(-50%);
		border-radius: 50%;
    }
    .title{
        margin-top: 66rpx;
    }
    .bannerBox{
        position: relative;
        margin: 0 auto;
        margin-top: 78rpx;
        margin-bottom: 30rpx;
        width: 686rpx;
        height: 140rpx;
        .banner{
            width: 686rpx;
            height: 140rpx;
        }
        .textBtn{
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            left: 72rpx;
            color: #fff;
            .rBtn{
                width: 11rpx;
                height: 20rpx;
                margin-left: 18rpx;
            }
        }
    }
    
    .item{
        display: flex;
        align-items: center;
        justify-content: space-between;
        width: 686rpx;
        margin: 0 auto;
        padding: 32rpx 0;
        border-bottom: 1rpx solid rgba(238,238,238,1);
        .icon{
            width: 36rpx;
            height: 36rpx;
            margin-right: 12rpx;
        }
        .name{
            font-size: 28rpx;
            display: flex;
            align-items: center;
        }
        .right{
            width: 10rpx;
            height: 19rpx;
        }
    }
}
</style>