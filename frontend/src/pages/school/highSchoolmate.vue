<template>
  <view class="content">
	  <view class="nodata" v-if="hightmatelist.length==0">暂无数据</view>
	  <view v-else>
		  <view class="item" v-for="(item,index) in hightmatelist" :key="index" @click="toacademypage(item)">
		      <image class="photo" :src="item.head_image" style="border-radius:50%"></image>
		      <view class="cont">
		          <view class="title">{{item.nickname}}</view>
		          <view class="academy">
		              <image class="icon" src="../../static/xuexiao_icon@2x2.png"></image>{{item.university_name}} {{item.college_name}} {{item.graduated_name}}
		          </view>
		          <view class="fen">{{item.up_name}}</view>
		      </view> 
		      <!-- <view class="right">
		          <image src="../../static/yiguanzhu_img@2x.png"></image>
		          <view class="text">已关注</view>
		      </view> -->
		      <view class="right" v-if="item.is_like==1" @click="notlike(item)">
		          <image src="../../static/yiguanzhu_img@2x.png"></image>
		          <view class="text">已关注</view>
		      </view>
		      <view class="right" @click="like(item)" v-else style="width:132rpx;height:54rpx;background:rgba(129,195,191,1);border-radius:32rpx 0rpx 0rpx 32rpx;">
		          <!-- <image src="../../static/yiguanzhu_img@2x.png"></image> -->
		          <view class="text"> <image class="icon" src="../../static/guanzhu_icon@2x.png"></image>关注</view>
		      </view>
		  </view>
		    
		    
	  </view>
      
     
  </view>
</template>

<script>
import app from "../../App.vue";
export default {
    data(){
        return{
                page:"1",
                size:"10",
                university_id:"",
                graduated_id:"",
                up_id:"",
                hightmatelist:[],
				type:'',
				schooltype:'',
				college_id:''
        }
    },
    onLoad(options){
		console.log('32348394893',options)
		this.schooltype=options.schooltype;
		this.university_id=options.university_id;
		if(options.college_id){
			this.college_id=options.college_id;
		}
        this.gethightmate();
    },
    methods:{
        gethightmate(){
    	    var token = uni.getStorageSync('token')
            var that=this
            var url="student/getStudentList"
            var params={
                token:token,
                page:that.page,
				type:that.schooltype,
                size:that.size,
                university_id:that.university_id,
                graduated_id:that.graduated_id,
				college_id:that.college_id,
                up_id:that.up_id
            }
            app.post(url,params,"get").then((res)=>{
                console.log(res)
                that.hightmatelist=res
            }).catch((err)=>{
                console.log(err)
            })
        },
        toacademypage(item){
           let id=item.user_id;
           let uni_id=item.university_id
			uni.navigateTo({
				url:'./schoolmateMsg?user_id='+id+'&university_id='+uni_id+'&type='+1
			})
        },
        like(item){
            var that = this
            var url = "student/likeStudent"
            var token = uni.getStorageSync('token')
            var params = {
                token:token,
                userb_id:item.user_id
            }
            app.post(url,params,"post").then((res)=>{
                console.log(res)
                item.is_like=1
            }).catch((err)=>{
                console.log(err)
            })
        },
        notlike(item){
            var that = this
            var url = "student/likeStudent"
            var token = uni.getStorageSync('token')
            var params = {
                token:token,
                userb_id:item.user_id
            }
            app.post(url,params,"post").then((res)=>{
                console.log(res)
                item.is_like=0
            }).catch((err)=>{
                console.log(err)
            })
        }
    }
}
</script>

<style lang="less">
.content{
    background:rgba(249,249,249,1);
    padding: 24rpx 0;
    height: 100vh;
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
            top: 38rpx;
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
                display: flex;
                align-items: center;
                .icon{
                    width: 28rpx;
                    height: 28rpx;
                }
            }
        }
    }
}
</style>