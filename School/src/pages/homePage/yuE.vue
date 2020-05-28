<template>
  <view class="content">
      <view class="item" v-for="(item,index) in mingxilist" :key="index">
          <view class="left">
              <view class="title">{{item.memo}}</view>
              <view class="time">{{item.createtime}}</view>
          </view>
          <view class="right">¥{{item.money}}</view>
      </view>
      <view class="nodata" v-if="mingxilist.length==0">暂无数据</view>
  </view>
</template>

<script>
import app from "../../App.vue";
export default {
    data(){
        return{
            page:1,
            size:10,
            mingxilist:[]
        }
    },
    methods:{
        getyuE(){
            var that = this
            var url = "money/listMoneyLog"
            var token = uni.getStorageSync('token')
            var params = {
                token:token,
                page:that.page,
                size:that.size
            }
            app.post(url,params,"get").then((res)=>{
                console.log(res)
                that.mingxilist=res
            }).catch((err)=>{
                console.log(err)
            })
        }
    },
    onLoad(){
        this.getyuE()
    }
}
</script>

<style lang="less">
.content{
    .nodata{
        font-size: 28rpx;
        text-align: center;
        padding: 20rpx 0;
    }
    .item{
        width: 686rpx;
        margin: 0 auto;
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 24rpx 0;
        border-bottom: 1rpx solid rgba(238,238,238,1);
        .left{
            .title{
                font-size: 28rpx;
            }
            .time{
                font-size: 24rpx;
                color:rgba(140,145,152,1);
            }
        }
        .right{
            font-size: 28rpx;
            color:rgba(239,76,70,1);
        }
    }
}
</style>