<template>
  <view class="content">
      <image class="bg" src="../../static/bg_img@2x(2).png"></image>
      <view class="card">
          <view class="title">提现到微信余额（元）</view>
          <!-- <view class="money">100.00</view> -->
          <input type="digit" v-model="money" class="money"  placeholder-style="padding-bottom: 10rpx;color:rgba(189,196,206,1);font-size: 80rpx;margin-top: 16rpx;border-bottom: 1rpx solid rgba(112,112,112,1);">
          <view class="text">最多可提{{yue}}元，<text @click="tixianall">全部提现</text></view>
          <view class="btn" @click="tixian">提现</view>
      </view>
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
    data(){
        return{
            money:'',
            yue:''
        }
    },
    methods:{
        tixian(){
            var that = this
			if(that.money==0){
				uni.showToast({
					title:'提现金额不能为0',
					icon:'none'
				})
			}else{
				if(that.money==''){
				    uni.showToast({
				        title:"提现金额不能为空",
				        icon:"none"
				    })
				    return false
				}
				if(that.money>that.yue){
				    uni.showToast({
				        title:"提现金额不足",
				        icon:"none"
				    })
				    return false
				}
				
				var url = "money/gotMoney"
				var token = uni.getStorageSync('token')
				var params = {
				    token:token,
				    money:that.money
				}
				app.post(url,params,"post").then((res)=>{
				    console.log(res)
					uni.showToast({
						title:'提现成功',
						icon:'none'
					})
					setTimeout(function(){
						uni.navigateBack({
							checked:true
						})
					},1500)
				}).catch((err)=>{
				    console.log(err);
					uni.showToast({
						title:err.msg,
						icon:'none',
						duration:2500
					})
					
				})
			}
            
        },
		// 全部提现
		tixianall(){
			let that=this;
			if(that.yue==0.00){
				uni.showToast({
					title:"余额为0，不能提现",
					icon:"none"
				})
			}else{
				uni.showModal({
				    title: '提示',
				    content: '是否要全部提现',
				    success: function (res) {
				        if (res.confirm) {
							that.money=that.yue
				            var url = "money/gotMoney"
				            var token = uni.getStorageSync('token')
				            var params = {
				                token:token,
				                money:that.yue
				            }
				            app.post(url,params,"post").then((res)=>{
				                console.log(res)
								uni.showToast({
									title:'提现成功',
									icon:'none'
								})
								setTimeout(function(){
									uni.navigateBack({
										checked:true
									})
								},1500)
				            }).catch((err)=>{
				                console.log(err);
				            	uni.showToast({
				            		title:err.msg,
				            		icon:'none'
				            	})
				            })
				        } else if (res.cancel) {
				            console.log('用户点击取消');
				        }
				    }
				});
			}
			
		}
    },
    onLoad(options){
        this.yue=options.money;
		this.money=options.money
    }

}
</script>

<style lang="less">
.content{
    background-color: #F9F9F9;
    .bg{
        width: 750rpx;
        height: 292rpx;
    }
    .card{
        width:686rpx;
        height:514rpx;
        background:rgba(255,255,255,1);
        box-shadow:0rpx 4rpx 6rpx rgba(45,85,117,0.1);
        opacity:1;
        border-radius:20rpx;
        position: absolute;
        top: 26rpx;
        left: 32rpx;
        padding: 68rpx 46rpx 40rpx;
        box-sizing: border-box;
        color:rgba(6,18,30,1);
        .title{
            font-size: 28rpx;
        }
        .money{
            color:rgba(189,196,206,1);
            font-size: 80rpx;
            margin-top: 16rpx;
            border-bottom: 1rpx solid rgba(112,112,112,1);
            height: 90rpx;
        }
        .text{
            font-size:24rpx;
            margin-top: 22rpx;
            text{
                color: #81C3BF;
                
            }
        }
        .btn{
            width:596rpx;
            height:76rpx;
            background:rgba(45,85,117,1);
            opacity:1;
            border-radius:8rpx;
            text-align: center;
            line-height: 76rpx;
            color: #fff;
            font-size: 28rpx;
            margin-top: 90rpx;
        }
    }
    .textBox{
        width:636rpx;
        height:586rpx;
        font-size:24rpx;
        font-family:PingFang SC;
        font-weight:400;
        line-height:40rpx;
        color:rgba(6,18,30,1);
        opacity:1;
        margin: 0 auto;
        margin-top: 274rpx;
    }
}
</style>