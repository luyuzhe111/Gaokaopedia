<template>
  <view class="content">
      <view class="selectCard">
          <view class="item">
              <view class="title">所在地区</view>
              <view class="selectBtn" @click="seldiqu">{{provname}}-{{cityname}}<image class="icon" src="../../static/zhuandao_icon@2x.png"></image></view>
          </view>

           <view class="item" style="border-bottom:none">
              <view class="title">高中名称</view>
              <input type="text" v-model="schoolname" class="inpt" placeholder="请输入高中名称" placeholder-style="color:rgba(189,196,206,1);font-size:28rpx;">
          </view>
      </view>
      <view class="diquPopup" v-if="isDiquShow">
         
          <view class="cont">
            <view class="colum" style="overflow: auto;height: 540rpx;">
             <!-- <view class="title" @click="selectprovince" :data-id="''">全部省</view> -->
              <view class="item" @click="selectprovince" v-for="(item,index) in provinceList" :key="index" :data-id="item.code" :data-prov="item.name">{{item.name}}</view>
              
            </view>
            <view class="colum" style="overflow: auto;height: 540rpx;">
             <!-- <view class="title" @click="selectcity" :data-id="''">全部市</view> -->
              <view class="item" @click="selectcity" v-for="(item,index) in cityList" :key="index" :data-id="item.code" :data-city="item.name">{{item.name}}</view>
            
            </view>
            <!-- <view class="colum" style="overflow: auto;height: 224rpx;">
             <view class="title" @click="selectschool" :data-id="null">全部学校</view>
              <view class="item" @click="selectschool" v-for="(item,index) in schoolList" :key="index" :data-id="item.id">{{item.name}}</view>
              
            </view> -->
            
          </view>
        </view>
      <view class="btn" @click="popupShow">提交</view>
      <view class="popup" v-if="isPopupShow">
          <view class="card">
              <view class="title">提交成功</view>
              <view class="tips">学校将在72小时内审核添加</view>
              <view class="okBtn" @click="popupHide">确定</view>
          </view>
      </view>
  </view>

</template>

<script>
import app from "../../App.vue";
export default {
    data(){
        return{
            isPopupShow:false,
            isDiquShow:false,
            province_id:'',
            city_id:'',
            school_id:'',
            cityList:[],
            provinceList:[],
            provname:'XX省',
            cityname:'XX市',
            schoolname:''

        }
    },
    methods:{
        popupShow(){
            
            var that = this
            var url = "school/addOneSchool"
            var params = {
                name:that.schoolname,
                province_id:that.province_id,
                city_id:that.city_id
            }
            app.post(url,params,"post").then((res)=>{
                console.log(res)
                this.isPopupShow=true
            }).catch((err)=>{
                console.log(err)
            })
        },
        popupHide(){
            this.isPopupShow=false
            uni.navigateBack()
        },
        seldiqu(){
            this.isDiquShow=true
        },
        //点击选中的省
        selectprovince(e){
            this.province_id=e.currentTarget.dataset.id
            this.getAllCity()
            this.provname=e.currentTarget.dataset.prov
        },
        //点击选中的市
        selectcity(e){
            this.city_id=e.currentTarget.dataset.id
            // this.getAllSchool()
            this.isDiquShow=false
            this.cityname=e.currentTarget.dataset.city

            
        },
       //获取全部地区(省)
        getAllProvince(){
            var url ="city/getAllProvince"
            var params ={}
            app.post(url,params,"get").then((res)=>{
            // var nullData = {
            //     code:'',
            //     name:"全部省"
            // }
            // res.unshift(nullData)
            // console.log(res)
            this.provinceList=res
            }).catch((err)=>{
            console.log(err)
            })
        },

        //市
        getAllCity(){
            var url = "city/getCity"
            var params = {
                province_id:this.province_id
            }
            app.post(url,params,"get").then((res)=>{
            console.log(res)
            this.cityList=res
            }).catch((err)=>{
            console.log(err)
            })
        },
    },
    onLoad(){
        this.getAllProvince() 
    }
}
</script>

<style lang="less">
    .diquPopup{
        font-size: 28rpx;
          width: 686rpx;
          height: 600rpx;
          position: absolute;
          top: 124rpx;
          left: 50%;
          transform: translate(-50%);
          background-color: #fff;
          padding: 22rpx 32rpx;
          box-sizing: border-box;
          z-index: 999;
          box-shadow:0px 4px 4px rgba(0,0,0,0.06);
          border-top: 1rpx solid rgba(249,249,249,1);
          .item{
            margin-bottom: 22rpx;
            // height: 74rpx;
            width: 170rpx;
          }
          .title{
            margin-bottom: 20rpx;
          }
          .cont{
            .colum{
              width: 30%;
                height: 500rpx;
            }
            display: flex;
            height: 600rpx;
            flex-direction: column;
            flex-wrap: wrap;
            justify-content: start;
          }
    }
.content{
    background:rgba(249,249,249,1);
    padding: 32rpx;
    height: 100vh;
    box-sizing: border-box;
    .selectCard{
        background-color: #fff;
        border-radius:20rpx;
        .item{
            display: flex;
            justify-content: space-between;
            padding: 30rpx 32rpx;
            // background-color: #fff;
            border-bottom: 1rpx solid rgba(238,238,238,1);
            font-size: 28rpx;
            .selectBtn{
                color:rgba(189,196,206,1);
                .icon{
                    width: 13rpx;
                    height: 24rpx;
                    margin-left: 8rpx;
                }
            }
            .inpt{
                // height: 30rpx;
                text-align: right;
                // width: 250rpx;
            }
        }
    }
    .btn{
        width:686rpx;
        height:80rpx;
        background:rgba(45,85,117,1);
        opacity:1;
        border-radius:8rpx;
        color: #fff;
        line-height: 80rpx;
        text-align: center;
        font-size: 28rpx;
        position: absolute;
        bottom: 32rpx;
    }
    .popup{
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        z-index: 999;
        .card{
            width: 628rpx;
            height: 294rpx;
            background-color: #fff;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%,-50%);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            text-align: center;
            padding: 40rpx 0;
            box-sizing: border-box;
            .title{
                font-size: 32rpx;
            }
            .tips{
                font-size: 24rpx;
            }
            .okBtn{
                width:220rpx;
                height:60rpx;
                background:rgba(45,85,117,1);
                opacity:1;
                border-radius:8rpx;
                text-align: center;
                line-height: 60rpx;
                color: #fff;
                font-size: 28rpx;
                margin: 0 auto;
            }
        }
        
    }
}
</style>