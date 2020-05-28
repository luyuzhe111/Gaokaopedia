<template>
  <view class="content">
      <navigator class="mingxi" hover-class="none" url="./yuE">余额明细</navigator>
      <view class="title">钱包余额（元）</view>
      <view class="money">{{monery}}</view>
      <view class="btn" @click="totixian">立即提现</view>
  </view>
</template>

<script>
import app from "../../App.vue";
export default {
 data(){
        return{
            monery:''
        }
    },
    methods:{
        getyuE(){
            var that = this
            var url = "money/getStudentMoney"
            var token = uni.getStorageSync('token')
            var params = {
                token:token
            }
            app.post(url,params,"get").then((res)=>{
                console.log(res)
                that.monery=res
            }).catch((err)=>{
                console.log(err)
            })
        },
        totixian(){
            uni.navigateTo({
                url:'./tixianPage?money='+this.monery
            })
        }
    },
    onLoad(){
       
    },
	
	onShow() {
		 this.getyuE()
	}
}
</script>

<style lang="less">
.content{
    padding: 24rpx 32rpx;
    .mingxi{
        text-align: right;
        text-decoration:underline;
        font-size:24rpx;
    }
    .title{
        text-align: center;
        margin-top: 186rpx;
    }
    .money{
        text-align: center;
        font-size:88rpx;
        margin-top: 12rpx;
    }
    .btn{
        width:592rpx;
        height:80rpx;
        background:rgba(45,85,117,1);
        opacity:1;
        border-radius:8rpx;
        color: #fff;
        font-size: 28rpx;
        text-align: center;
        line-height: 80rpx;
        margin: 0 auto;
        margin-top: 376rpx;
    }
}
</style>