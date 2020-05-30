<template>
  <view class="content">
      <image class="background" :src="universitylist.des_image=='http://school.t.brotop.cn'?'../../static/bg_img@2x.png':universitylist.des_image"></image>
      <view class="titleBox">
        <view class="title">{{universitylist.name}}</view>
        <view class="eng">Hebei University of Technology</view>
      </view>
	  
      <view class="schoolMsg">
        <view class="titleAcademy">
            <image class="icon" :src="universitylist.icon_image=='http://school.t.brotop.cn'?'../../static/logo_img@2x.png':universitylist.icon_image"></image>
            <view class="txt">学校介绍</view>
        </view>
		<view class="text">
			<view class="" v-if="universitylist.des_content==''">暂无简介</view>
			<rich-text :nodes="universitylist.des_content"></rich-text>
		</view>
       <!-- <view 
            {{universitylist.des_content}}
        </view> -->
      </view>
  </view>
</template>
<script>
import app from "../../App.vue";
export default {
    data(){
        return{
            university_id:'',
            universitylist:[]
        }
    },
    methods:{
        getschoolmsg(){
            var that = this
            var url = "university/getUniversityInfo"
            var params = {
                university_id:that.university_id
            }
            app.post(url,params,"get").then((res)=>{
                console.log(res)
                that.universitylist=res
            }).catch((err)=>{
                console.log(err)
            })
        }
    },
    onLoad(options){
        this.university_id=options.uni_id
        this.getschoolmsg()
    }

}
</script>

<style lang="less">
.content{
    .background{
        width: 750rpx;
        height: 334rpx;
        position: absolute;
        top: 0;
        z-index: -1;
    }
    // background: url('../../static/bg_img@2x.png') no-repeat;
    // background-size: 750rpx 334rpx;
    // height: 100vh;
    // overflow: hidden;
    // padding: 0 32rpx;
    
    .titleBox{
        margin-top: 126rpx;
        margin-left: 32rpx;
        .title{
            color:rgba(255,255,255,1);
            font-size: 36rpx;
        }
        .eng{
            color:rgba(255,255,255,1);
            font-size: 24rpx;
        }
    }
    .schoolMsg{
        width:686rpx;
        // height:2294rpx;
        background:rgba(255,255,255,1);
        box-shadow:0rpx 4rpx 6rpx rgba(45,85,117,0.1);
        opacity:1;
        border-radius:20rpx;
        margin-top: 30rpx;
        padding: 58rpx 32rpx;
        box-sizing: border-box;
        margin-left: 32rpx;
        .titleAcademy{
            display: flex;
            align-items: center;
            .icon{
            width: 48rpx;
            height: 48rpx;
            }
            .txt{
                color:rgba(45,85,117,1);
                font-size: 32rpx;
                margin-left: 16rpx;
            }
        }
        .text{
            color:rgba(6,18,30,1);
            font-size: 24rpx;
            margin-top: 36rpx;
            margin-bottom: 38rpx;
        }
        .pic{
            width: 612rpx;
            height: 408rpx;
        }
    }
    
}
</style>