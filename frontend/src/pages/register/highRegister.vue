<template>
  <view class="content">
      <view class="regMsg">
          <view class="msgItem">
              <view class="title">姓名</view>
              <input type="text" class="inpt" v-model="nickname" placeholder="请填写姓名" placeholder-style="color:rgba(189,196,206,1);font-size:28rpx">
          </view>
          <view class="msgItem">
              <view class="title">邮箱</view>
              <input type="text" class="inpt" v-model="email" placeholder="请填写邮箱" placeholder-style="color:rgba(189,196,206,1);font-size:28rpx">
          </view>
          <view class="msgItem">
              <view class="title">头像</view>
              <view @click="upload">
              <image class="photo" :src="isUpload ? image : '../../static/touxiang_img@2x.png'"></image>
              </view>
          </view>
          <view class="msgItem" @click="selSchool">
              <view class="title">高中</view>
              <view class="select">{{isNameShow? name : '请选择高中'}}<image class="icon" src="../../static/zhuandao_icon@2x.png"></image></view>
              
          </view>
          <view class="msgItem" style="border-bottom: none;">
              <view class="title">入学年份</view>
              
              <view class="select" @click="selDate">
                  <picker mode="date" :value="date" start="2000-01-01" @change="bindTimeChange">
                    <view class="uni-input">{{isDateShow? date : '请选择入学年份'}}</view>
                  </picker>
                  <image class="icon" src="../../static/zhuandao_icon@2x.png"></image>
              </view>
          </view>
          <!-- <view class="uni-title uni-common-pl">时间选择器</view> -->
        
      </view>
      <view class="regBtn" @click="toSchool">注册</view>
  </view>
</template>

<script>
	import app from "../../App.vue";
export default {
	
    data(){
        return{
            level:'1',
            nickname:'',
            name:'请选择高中',
            image:'',
            school_id:'',
            email:'',
            isUpload:false,
            date: '请选择入学年份',
            isDateShow: false,
            isNameShow:false,
			chuanimg:''
        }
    },
	onLoad(options) {
        console.log(options)
        // this.school_id=options.school_id
	},
    methods:{
        selDate(){
            this.isDateShow=true
        },
        selSchool(){
            this.isNameShow=true
            uni.navigateTo({
                url:'./selectSchool'
            })
        },
        bindTimeChange: function(e) {
            this.date = e.target.value
        },
        toSchool(){
            var that=this
            if(that.nickname==''){
                uni.showToast({
                    title:"姓名不能为空",
                    icon:"none"
                })
                return false
            }
            var reg = /^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/ ;
            if(!reg.test(that.email)){
                uni.showToast({
                title:"请输入正确邮箱",
                icon:"none"
                })
                return false
            }
            if(that.image==''){
                uni.showToast({
                    title:"请选择头像",
                    icon:"none"
                })
                return false
            }
            if(that.name=='请选择高中'){
                uni.showToast({
                    title:"请选择高中",
                    icon:"none"
                })
                return false
            }
            if(that.date=='请选择入学年份'){
                uni.showToast({
                    title:"请选择入学年份",
                    icon:"none"
                })
                return false
            }
            var url="register/register"
            
            var params={
                level:that.level,
                nickname:that.nickname,
                head_image:that.chuanimg,
                school_id:that.school_id,
                email:that.email,
                starttime:this.date
                
            }
            app.post(url,params,"post").then((res)=>{
                console.log(res)
                uni.showToast({
                    title:"注册成功",
                    icon:"none"
                })
				uni.setStorageSync("chosetype",1)
                setTimeout(() => {
                    uni.switchTab({
                        url: "/pages/school/school?type="+1
                    })
                }, 1500);
                 
            
            }).catch((err)=>{
                console.log(err)
                if(err.code==0){
                    uni.showToast({
                        title:err.msg,
                        icon:'none'
                    })
                }
            })
            // wx.navigateTo({
            //     url:"../school/school"
            // })
           
                
           
        },
        upload(){
               var that = this;
               uni.chooseImage({
                count: 1,
                sizeType: ['original', 'compressed'], //可以指定是原图还是压缩图，默认二者都有
                sourceType: ['album'], //从相册选择
                success: function (res) {
                 const tempFilePaths = res.tempFilePaths;
                 that.image = tempFilePaths[0];
                 app.upload('image',res.tempFilePaths[0],"post").then((res)=>{
                    //  let newimage=that.image
                     that.image=app.globalData.imageBaseUrl+res.kurl;
					 that.chuanimg=res.kurl
                     console.log(res)
                     console.log(that.image)
                     console.log(res.url)
                    //  newimage.push(res.url)
                    that.isUpload=true
                 }).catch((err)=>{
                     console.log(err)
                 })
                //  console.log("tempFilePaths[0]",tempFilePaths[0])  //能够打印出选中的图片
                //  console.log(res)
                //  that.head_image=tempFilePaths[0]
                //  that.iconcheck = 1;//点击后图片更改状态由0变成1
                },
                error : function(e){
                 console.log(e);
                }
               });
              },
    }
}
</script>

<style lang="less">
page{
    overflow-y:hidden;
}
.content{
    background:rgba(249,249,249,1);
    height: 100vh;
    .regMsg{
        margin: 32rpx auto;
        width:686rpx;
        height:528rpx;
        background:rgba(255,255,255,1);
        opacity:1;
        border-radius:20rpx;
        padding: 0 32rpx;
        box-sizing: border-box;
        .msgItem{
            border-bottom: 1rpx solid rgba(238,238,238,1);
            display: flex;
            justify-content: space-between;
            // text-align: center;
            .title{
				width:172rpx;
                color:rgba(6,18,30,1);
                font-size: 28rpx;
                line-height: 105rpx;
            }
            .inpt{
				width:100%;
                text-align: right;
                height: 105rpx;
                font-size: 28rpx;
            }
            .photo{
                width: 64rpx;
                height: 64rpx;
                margin-top: 20rpx;
                border-radius: 50%;
            }
            .select{
                font-size: 28rpx;
                color:rgba(189,196,206,1);
                height: 105rpx;
                display: flex;
                line-height: 105rpx;
                align-items: center;
            }
            .icon{
                width: 13rpx;
                height: 24rpx;
                margin-left: 8rpx;
            }
        }
    }
    .regBtn{
        width:684rpx;
        height:80rpx;
        background:rgba(45,85,117,1);
        opacity:1;
        border-radius:8rpx;
        color: #fff;
        font-size: 28rpx;
        line-height: 80rpx;
        text-align: center;
        // margin: 0 auto;
        // margin-top: 600rpx;
        position: absolute;
        bottom: 84rpx;
        left: 50%;
        transform: translate(-50%);
    }
}
</style>