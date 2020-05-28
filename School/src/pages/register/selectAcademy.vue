<template>
  <view class="content">
    <!-- <view class="top">
      <view class="search">
        <image class="icon" src="../../static/sousuo_icon@2x.png"></image>
        <input type="text" placeholder="请输入搜索内容" class="input" placeholder-style="color:rgba(189,196,206,1);font-size:28rpx;">
      </view>
      <view class="searchBtn">搜索</view>
    </view> -->
    <view class="contentBox">
      <view class="item" v-for="(item,index) in academylist" :key="index" @click="selacademy" :data-item="item">{{item.name}}</view>
      
    </view>
  </view>
</template>

<script>
import app from "../../App.vue";
export default {
  data(){
    return{
      academylist:[],
      university_id:''
    }
  },
  methods:{
    getAllAcademy(){
      var that = this
      var url = "University/getCollege"
      var params = {
        university_id:that.university_id
      }
      app.post(url,params,"get").then((res)=>{
        console.log(res)
        that.academylist=res
      }).catch((err)=>{
        console.log(err)
      })
    },
    selacademy(e){
      console.log(e.currentTarget.dataset)
      // this.school_id=e.currentTarget.dataset.id.id
      var name = e.currentTarget.dataset.item.name
      var id = e.currentTarget.dataset.item.id
      //this.$emit('school_id',this.school_id)
      // var id = this.school_id
      let pages = getCurrentPages(); 
      let prevPage = pages[ pages.length - 2 ]
      prevPage.$vm.academyname=name
      prevPage.$vm.academyid=id
      // prevPage.$vm.name = name
      uni.navigateBack()     
    }
   
  },
  onLoad(options){
    console.log(options)
    this.university_id=options.uni_id
    console.log(options.uni_id)
    this.getAllAcademy()
  }

}
</script>

<style lang="less">
	page{
		background-color: rgba(249,249,249,1);
	}
.content{
  // background-color: rgba(249,249,249,1);
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
	  display:flex;
	  align-items: center;
	  justify-content: center;
      // line-height: 112rpx;
      background:rgba(255,255,255,1);
      box-shadow:0rpx 2rpx 4rpx rgba(0,0,0,0.06);
      opacity:1;
      border-radius:8rpx;
      font-size: 24rpx;
      color:rgba(45,85,117,1);
      padding: 0rpx 38rpx;
      box-sizing: border-box;
      text-align: center;
      margin-right: 14rpx;
      margin-bottom: 16rpx;
	  // overflow: hidden;
	  // white-space: nowrap;
	  // text-overflow: ellipsis;
    }
  }
}
</style>