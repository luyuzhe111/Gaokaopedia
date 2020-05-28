<template>
  <view class="content">
      <view class="item" @click="toschoolmate" v-for="(item,index) in likepeoplelist" :key="index" :data-id="item.user_id">
          <image class="photo" :src="item.head_image" style="border-radius: 50%;"></image>
          <view class="cont"> 
              <view class="title">{{item.nickname}}</view>
              <view class="academy">
                  <image class="icon" src="../../static/xuexiao_icon@2x2.png"></image>{{ item.university_name }} &nbsp;&nbsp;&nbsp;
						{{ item.college_name }}&nbsp;&nbsp;&nbsp; {{ item.graduated_name }}
              </view>
              <view class="fen">{{item.up_name}}</view>
          </view>
         <view class="right" >
         	<image src="../../static/yiguanzhu_img@2x.png"></image>
         	<view class="text">已关注</view>
         </view>
         <!-- <view class="right" v-else style="width:132rpx;height:54rpx;background:rgba(129,195,191,1);border-radius:32rpx 0rpx 0rpx 32rpx;">
         	<image src="../../static/yiguanzhu_img@2x.png"></image>
         	<view class="text" style="display:flex;align-items: center;">
         		<image class="icon" src="../../static/guanzhu_icon@2x.png" style="width:28rpx;height:28rpx;"></image>关注
         	</view>
         </view> -->
      </view>
      <view class="nodata" v-if="likepeoplelist.length==0">暂无数据</view>
      
      

  </view>
</template>

<script>
import app from "../../App.vue";
export default {
data(){
    return{
        page:1,
        size:10,
        likepeoplelist:[],
        
    }
},
methods:{
    getlikepeople(){
        var that = this
        var url = 'student/getMyLikeStudent'
        var token = uni.getStorageSync('token')
        var params = {
            token:token,
            page:that.page,
            size:that.size
        }
        app.post(url,params,"get").then((res)=>{
            console.log(res)
            that.likepeoplelist=res
        }).catch((err)=>{
            console.log(err)
        })
    },
    toschoolmate(e){
        var id = e.currentTarget.dataset.id
        console.log(id)
        uni.navigateTo({
            url:"../school/schoolmateMsg?user_id="+id
        })
    }
},
onShow() {
	this.page=1;
	this.likepeoplelist=[];
	 this.getlikepeople()
},
onLoad(){
   
}
}
</script>

<style lang="less">

.content{
    background:rgba(249,249,249,1);
    height: 100vh;
    padding: 24rpx 0;
    .nodata{
        font-size: 28rpx;
        text-align: center;
        padding: 20rpx 0;
    }
    .item{
        width:686rpx;
        height:172rpx;
        background:rgba(255,255,255,1);
        opacity:1;
        border-radius:8px;
        margin: 0 auto;
        background-color: #fff;
        display: flex;
        padding: 24rpx 32rpx;
        box-sizing: border-box;
        position: relative;
        margin-bottom: 24rpx;
        .photo{
            width: 112rpx;
            height: 112rpx;
            margin-right: 26rpx;
            margin-top: 6rpx;
        }
        .cont{
            .title{
                color:rgba(61,68,77,1);
                font-size: 28rpx;
            }
            .academy{
                color:rgba(61,68,77,1);
                font-size: 22rpx;
                margin-top: 4rpx;
                display: flex;
                align-items: center;
                // line-height: 24rpx;
                .icon{
                    width: 24rpx;
                    height: 24rpx;
                    margin-right: 6rpx;
                }
            }
            .fen{
                font-size: 22rpx;
                color:rgba(140,145,152,1);
                margin-top: 10rpx;
            }
        }
        .right{
            width: 132rpx;
            height: 54rpx;
            position: absolute;
            right: 0;
            top: 16rpx;
            image{
                width: 132rpx;
                height: 54rpx;
            }
            .text{
                color: #fff;
                font-size: 24rpx;
                width: 80rpx;
                text-align: center;
                position: absolute;
                top: 50%;
                left: 50%;
                transform: translate(-50%,-50%);
            }
        }
    }
}
</style>