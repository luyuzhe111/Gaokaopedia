<template>
  <view class="content">
    <!-- <view class="top">
      <view class="search">
        <image class="icon" src="../../static/sousuo_icon@2x.png"></image>
        <input type="text" placeholder="请输入搜索内容" class="input" placeholder-style="color:rgba(189,196,206,1);font-size:28rpx;">
      </view>
      <view class="searchBtn">搜索</view>
    </view> -->
	<view class="nodata" v-if="collegeList.length==0">暂无数据</view>
    <view class="contentBox">
      <view class="item" @click="tocollegepage(item)" v-for="(item,index) in collegeList" :key="index">{{item.name}}</view>
     
      
    </view>
  </view>
</template>

<script>
import app from "../../App.vue";
export default {
data(){
  return{
    university_id:'',
    collegeList:[]
  }
},
methods:{
  getAllcollege(){
    var that = this
    var url="university/getCollege"
    var params = {
      university_id:that.university_id
    }
    app.post(url,params,"get").then((res)=>{
      console.log(res)
      this.collegeList=res
    }).catch((err)=>{
      console.log(err)
    })        
  },
  tocollegepage(item){
    let id = item.university_id
    let name = item.name
    uni.navigateTo({
      url:'./academyDetails?university_id='+id+'&name='+name+'&college_id='+item.id
    })
  }
},
onLoad(options){
  console.log(options)
  this.university_id=options.university_id
  this.getAllcollege()
}
}
</script>

<style lang="less">
	page{
		background-color: rgba(249,249,249,1);
	}
.content{
  
  font-size: 28rpx;
  .top{
    padding: 18rpx 32rpx;
    display: flex;
    background-color: #fff;
  }
  .search{
    // background-color: #fff;
    display: flex;
    // text-align: center;
    align-items: center;
    width:596rpx;
    height:64rpx;
    background:rgba(238,238,238,1);
    opacity:1;
    border-radius:40rpx;
    padding: 0 28rpx;
    box-sizing: border-box;
    .icon{
      width: 30rpx;
      height: 30rpx;
    }
    .input{
      height: 64rpx;
      width: 500rpx;
      text-align: center;
    }
    
  }
    .searchBtn{
      color:rgba(6,18,30,1);
      font-size: 28rpx;
      line-height: 64rpx;
      margin-left: 20rpx;
    }
  .contentBox{
    width: 750rpx;
    // height: ;
    display: flex;
    flex-wrap: wrap;
    padding: 24rpx 18rpx 24rpx 32rpx;
    .item{
      width:220rpx;
      height:112rpx;
      background:rgba(255,255,255,1);
      box-shadow:0rpx 2rpx 4rpx rgba(0,0,0,0.06);
      opacity:1;
      border-radius:8rpx;
      font-size: 24rpx;
      color:rgba(45,85,117,1);
      padding: 0 38rpx;
      box-sizing: border-box;
      text-align: center;
      margin-right: 14rpx;
      text-align: center;
	  margin-bottom: 30rpx;  
	  display: flex;
	  align-items: center;
	  justify-content: center;
    }
  }
}
</style>