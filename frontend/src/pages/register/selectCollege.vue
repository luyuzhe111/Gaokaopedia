<template>
<!-- 选择高中页 -->
  <view class="content">
      <view class="selectCity">
          <view class="title">所在地区</view>
          <view class="selectBtn" @click="diquShow">
              <view class="uni-input">{{isRegionShow ? region : '请选择所在地区'}}</view>
              <image class="icon" src="../../static/zhuandao_icon@2x.png"></image>
          </view>
      </view>
      <view class="diquPopup" v-if="isDiquShow">
         
          <view class="cont">
            <view class="colum" style="overflow: auto;height: 600rpx;">
             <!-- <view class="title" @click="selectprovince" :data-id="''">全部省</view> -->
              <view class="item" @click="selectprovince" v-for="(item,index) in provinceList" :key="index" :data-id="item.code">{{item.name}}</view>
              
            </view>
            <view class="colum" style="overflow: auto;height: 600rpx;">
             <!-- <view class="title" @click="selectcity" :data-id='""'>全部市</view> -->
              <view class="item" @click="selectcity" v-for="(item,index) in cityList" :key="index" :data-id="item.code">{{item.name}}</view>
            
            </view>
            <!-- <view class="colum" style="overflow: auto;height: 224rpx;">
             <view class="title" @click="selectschool" :data-id="null">全部学校</view>
              <view class="item" @click="selectschool" v-for="(item,index) in schoolList" :key="index" :data-id="item.id">{{item.name}}</view>
              
            </view> -->
            
          </view>
        </view>
      <view class="list">
          <view class="item"  @click="selectschool" v-for="(item,index) in schoolList" :key="index" :data-id="item">{{item.name}}</view>
          
      </view>
      <view class="bottomItem">
          没有找到你的大学？
          <navigator class="btn" hover-class="none" url="./addSchool">点击此处立即添加</navigator>
      </view>
  </view>
</template>

<script>
import app from "../../App.vue";
export default {
    data(){
        return{
           isDiquShow:false,
           region:[] ,
           isRegionShow:false,
           provinceList:[],
           cityList:[],
           schoolList:[],
           province_id:"",
           city_id:'',
           school_id:''
        }
    },
    onLoad(){
        this.getAllProvince() 
        this. getAllSchool()  
    },
    methods:{
        diquShow(){
            this.isDiquShow=true

        },
        //点击选中的省
  selectprovince(e){
    this.province_id=e.currentTarget.dataset.id
    console.log(e.currentTarget.dataset.id)
    this.getAllCity()
    
  },
  //点击选中的市
  selectcity(e){
    this.city_id=e.currentTarget.dataset.id
    this.getAllSchool()
  },
        selRegion(){
            this.isRegionShow=true
        },
       bindRegionChange(e){
           var arr=e.target.value
           var str=e.target.value.toString()
           
           this.region = arr.join("-")
           
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
            console.log(res)
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

        //学校
        getAllSchool(){
            var url="city/getUniversityByCity"
            var params ={
                // name:,
                city_id:this.city_id
            }
            app.post(url,params,"get").then((res)=>{
            console.log(res)
            this.schoolList=res
        
            }).catch((err)=>{
            console.log(err)
            })
        },


        //点击选中的省
        selectprovince(e){
            this.province_id=e.currentTarget.dataset.id
            this.getAllCity()
            
        },
        //点击选中的市
        selectcity(e){
            this.city_id=e.currentTarget.dataset.id;
			console.log(this.city_id,'我是城市ID')
            this.getAllSchool()
            this.isDiquShow=false
        },
       selectschool(e){
           console.log(e.currentTarget.dataset.id.id)
           this.school_id=e.currentTarget.dataset.id.id
           console.log(this.school_id)
        //    this.school_id=e.currentTarget.dataset.id
           var name = e.currentTarget.dataset.id.name
            //this.$emit('school_id',this.school_id)
            // var id = this.school_id
            let pages = getCurrentPages(); 
            let prevPage = pages[ pages.length - 2 ]
            prevPage.$vm.uni_id=this.school_id
            prevPage.$vm.collegename = name
            
            uni.navigateBack()
       }
       
    }

}
</script>

<style lang="less">
.content{
    background:rgba(249,249,249,1);
    color:rgba(6,18,30,1);
    height: 100vh;
    font-size: 28rpx;
	display: flex; 
	flex-direction: column;
    // padding: 0 32rpx;
    .selectCity{
        display: flex;
        justify-content: space-between;
        padding: 30rpx 32rpx;
        background-color: #fff;
        font-size: 28rpx;
        .selectBtn{
            color:rgba(189,196,206,1);
            display: flex;
            align-items: center;
            .icon{
                width: 13rpx;
                height: 24rpx;
                margin-left: 8rpx;
            }
        }
    }
    .list{
        font-size: 28rpx;
        padding: 12rpx 32rpx;
		flex: 1;
		margin-bottom: 100rpx;
		overflow-y: scroll;
        .item{
            padding: 28rpx 0;
            border-bottom: 1rpx solid rgba(238,238,238,1);
        }
    }
    .bottomItem{
        font-size: 28rpx;
        display: flex;
        padding: 20rpx 32rpx;
        // margin-top: 122rpx;
        position: absolute;
        bottom: 0;
        .btn{
            color:rgba(129,195,191,1);
            text-decoration: underline;
        }
    }
    .diquPopup{
          width: 750rpx;
        //   height: 314rpx;
          position: absolute;
          top: 84rpx;
          left: 0;
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

            }
            display: flex;
            // height: 314rpx;
            // flex-direction: column;
            flex-wrap: wrap;
            justify-content: start;
          }
        }
}
</style>