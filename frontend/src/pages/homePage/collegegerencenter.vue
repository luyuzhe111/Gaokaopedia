<template>
  <view class="content">
      <view class="card">
          <view class="item">
              <view class="name">姓名</view>
              <input v-model="username" type="text" class="cont" :placeholder="userinfolist.nickname=='undefinde'? '' : userinfolist.nickname" placeholder-style="text-align: right;font-size:28rpx;" style="width:146rpx;text-align: right">
              <!-- <view class="cont">爱学习的王笑笑</view> -->
          </view>
          <!-- <view class="item">
              <view class="name">邮箱</view>
              <input v-model="email" type="text" class="cont" placeholder="{{userinfolist.email}}" placeholder-style="text-align: right;font-size:28rpx;" style="width:220rpx;text-align: right">
          </view> -->
          <view class="item" style="border:none">
              <view class="name">头像</view>
              <image class="photo" @click="upload" :src="isUpload ? image : userinfolist.head_image"></image>
          </view>
          
      </view>
      <view class="title">高中信息</view>
      <view class="card">
          <view class="item">
              <view class="name">高中</view>
              <!-- <input type="text" class="cont" placeholder="爱学习的王笑笑" placeholder-style="text-align: right;font-size:28rpx;color:#BDC4CE" style="width:200rpx"> -->
              <view class="cont">
                {{userinfolist.school_info.name}}
                <!-- <image class="icon" src="../../static/dizhi_btn@2x2.png"></image> -->
              </view>
          </view>
          <view class="item">
              <view class="name">毕业年份</view>
              <!-- <input type="text" class="cont" placeholder="爱学习的王笑笑" placeholder-style="text-align: right;font-size:28rpx;color:#BDC4CE" style="width:200rpx"> -->
              <view class="cont">
                  {{userinfolist.endtime}}
                <!-- <image class="icon" src="../../static/dizhi_btn@2x2.png"></image> -->
              </view>
          </view>
          <!-- <view class="item">
              <view class="name">高考学科</view>
              <input type="text" class="cont" placeholder="爱学习的王笑笑" placeholder-style="text-align: right;font-size:28rpx;color:#BDC4CE" style="width:200rpx">
              <view class="cont">
                语数外
                <image class="icon" src="../../static/dizhi_btn@2x2.png"></image>
              </view>
          </view> -->
          <view class="item">
              <view class="name">选考科目</view>
              <!-- <input type="text" class="cont" placeholder="爱学习的王笑笑" placeholder-style="text-align: right;font-size:28rpx;color:#BDC4CE" style="width:200rpx"> -->
              <view class="cont" >
				  <text v-for="(item,index) in userinfolist.subject_list" :key="index">{{item.name}}</text>
               <!-- {{userinfolist.subject_list[0].name}} -->
                <!-- <image class="icon" src="../../static/dizhi_btn@2x2.png"></image> -->
              </view>
          </view>
          <view class="item">
              <view class="name">升学方式</view>
              <!-- <input type="text" class="cont" placeholder="爱学习的王笑笑" placeholder-style="text-align: right;font-size:28rpx;color:#BDC4CE" style="width:200rpx"> -->
              <view class="cont">
                {{userinfolist.up_info.name}}
                <!-- <image class="icon" src="../../static/dizhi_btn@2x2.png"></image> -->
              </view>
          </view>
      </view>
       <view class="title">大学信息</view>
       <view class="card">
          <view class="item">
              <view class="name">就读大学</view>
              <!-- <input type="text" class="cont" placeholder="爱学习的王笑笑" placeholder-style="text-align: right;font-size:28rpx;color:#BDC4CE" style="width:200rpx"> -->
              <view class="cont">
                {{userinfolist.university_info.name}}
                <!-- <image class="icon" src="../../static/dizhi_btn@2x2.png"></image> -->
              </view>
          </view>
          <view class="item">
              <view class="name">所属学院</view>
              <!-- <input type="text" class="cont" placeholder="爱学习的王笑笑" placeholder-style="text-align: right;font-size:28rpx;color:#BDC4CE" style="width:200rpx"> -->
              <view class="cont">
               {{userinfolist.college_info.name}}
                <!-- <image class="icon" src="../../static/dizhi_btn@2x2.png"></image> -->
              </view>
          </view>
          <view class="item">
              <view class="name">就读专业</view>
              <!-- <input type="text" class="cont" placeholder="爱学习的王笑笑" placeholder-style="text-align: right;font-size:28rpx;color:#BDC4CE" style="width:200rpx"> -->
              <view class="cont">
                {{userinfolist.graduated_info.name}}
                <!-- <image class="icon" src="../../static/dizhi_btn@2x2.png"></image> -->
              </view>
          </view>
          
          
      </view>
      <view class="btn" @click="update">保存</view>
  </view>
</template>

<script>
import app from "../../App.vue";
export default {
data(){
  return{
    username:'',
    image:'',
    image2:'',
    isUpload:false,
    userinfolist:[],
    school_id:'',
    name:'',
    email:'',
    date: '2020',
    isDateShow:false
  }
},
methods:{
  getuserinfo(){
    var that = this
    var url = "student/getMyInfo"
    var token = uni.getStorageSync('token')
    var params = {
      token:token
    }
    app.post(url,params,"get").then((res)=>{
      console.log(res)
      that.userinfolist=res
    }).catch((err)=>{
      console.log(err)
    })
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
          that.image=app.globalData.imageBaseUrl+res.kurl
          that.image2=res.kurl
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
  bindTimeChange(e){
    this.date = e.target.value
  },
  update(){
    var that = this
    var url = "student/updateStudentInfo"
    var token = uni.getStorageSync('token')
    var params= {
      token:token,
      nickname:that.username=='' ? that.userinfolist.nickname : that.username,
      head_image:that.image2=='' ? that.userinfolist.head_image : that.image2
    }
    app.post(url,params,"post").then((res)=>{
      console.log(res)
      res.head_image=that.image
      res.nickname=that.username
      uni.showToast({
          title:'个人信息保存成功',
          icon:'none'
      })
      setTimeout(() => {
        uni.navigateBack()
      },1500);
      
    }).catch((err)=>{
      console.log(err)
      if(err.code==0){
        uni.showToast({
          title:err.msg,
          icon:'none'
        })
      }
    })
    
  }
},
onLoad(){
  this.getuserinfo()
}
}
</script>

<style lang="less">
.content{
    background-color: rgba(249,249,249,1);
    padding: 32rpx 32rpx 40rpx;
    // height: 100vh;
    .card{
        width:686rpx;
        // height:428rpx;
        background:rgba(255,255,255,1);
        opacity:1;
        border-radius:20rpx;
        margin: 0 auto;
        
        .item{
            width: 622rpx;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 32rpx 0;
            box-sizing: border-box;
            font-size: 28rpx;
            margin: 0 auto;
            border-bottom: 1rpx solid rgba(238,238,238,1);
            .cont{
              display: flex;
              align-items: center;
             
            }
            .photo{
                width: 64rpx;
                height: 64rpx;
                border-radius: 50%;
            }
            .icon{
                width: 11rpx;
                height: 22rpx;
                margin-left: 12rpx;
               
            }
            
            
        }
    }
    .title{
      color:rgba(45,85,117,1);
      font-size: 32rpx;
      margin-top: 44rpx;
      margin-bottom: 12rpx;
    }
    .btn{
        width:684rpx;
        height:80rpx;
        background:rgba(45,85,117,1);
        opacity:1;
        border-radius:8rpx;
        color: #fff;
        line-height: 80rpx;
        text-align: center;
        margin: 0 auto;
        margin-top: 66rpx;
    }
}
</style>