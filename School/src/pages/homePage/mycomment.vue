<template>
  <view class="content">
      <view class="item" @click="tocommentlist(item)"  v-for="(item,index) in commentlist" :key="index">
          <view class="toprow" style="display:flex;justify-content: space-between;">
			  <view style="display:flex;">
				  <image class="photo" :src="item.head_image" style="border-radius: 50%;"></image>
				  <view class="msg">
				      <view class="name">{{item.nickname}}</view>
				      <view class="time">{{item.createtime}}</view>
				  </view>
			  </view>
			  <view v-if="item.usera_id==user_id">
				  <view class="right" style="color:#BDC4CE">已读</view>
			  </view>
			  <view v-else>
				  <view class="right" v-if="item.readtime==null">未读</view>
				  <view class="right" style="color:#BDC4CE" v-else>已读</view>
			  </view>
             
             
          </view>
          <view class="bottomrow">
                {{item.des_content}}
          </view>
      </view>
      <view class="nodata" v-if="commentlist.length==0">暂无数据</view>
     
  </view>
</template>

<script>
import app from "../../App.vue";
export default {
    data(){
        return{
            page:1,
            size:10,
            commentlist:[],
            user_id:''
        }
    },
    methods:{
        getcommentlist(){
            var that = this
            var url = "mes/getMyMes"
            var token = uni.getStorageSync('token')
            var params = {
                token:token,
                page:that.page,
                size:that.size,
                
            }
            app.post(url,params,"get").then((res)=>{
                console.log(res)
				res.forEach(function(value,index,array){
					value.head_image=app.globalData.imageBaseUrl+value.head_image
				})
                that.commentlist=that.commentlist.concat(res)
            }).catch((err)=>{
                console.log(err)
            })
        },
        tocommentlist(item){
            uni.navigateTo({
                url:'./commentContent?userb_id='+item.userb_id+"&nickname="+item.nickname+"&head_image="+item.head_image+"&user_id="+this.user_id+'&usera_id='+item.usera_id
            })
			let chuanid='';
			if(this.user_id==item.userb_id){
				chuanid=item.usera_id
			}else{
				chuanid=item.userb_id
			}
            var that = this
            var url = "mes/isReadMes"
            var token = uni.getStorageSync('token')
            var params = {
                token:token,
                usera_id:chuanid
            }
            app.post(url,params,"post").then((res)=>{
                console.log(res)
            }).catch((err)=>{
                console.log(err)
            })
        }
    },
    onLoad(options){
		console.log(options)
        this.user_id=options.user_id
       
    },
	onShow(){
		this.page=1;
		this.commentlist=[];
		this.getcommentlist()
	},
	
	onReachBottom(){
		let newpage=this.page;
		newpage++;
		this.page=newpage;
		this.getcommentlist()
	}

}
</script>

<style lang="less">
.content{
    // background:rgba(249,249,249,1);
    padding: 28rpx 0;
    .nodata{
        font-size: 28rpx;
        text-align: center;
        padding: 20rpx 0;
    }
    .item{
        width: 686rpx;
        margin: 0 auto;
        background-color: #fff;
        padding: 32rpx 0;
        box-sizing: border-box;
        margin-bottom: 28rpx;
    }
    .toprow{
        display: flex;
        // justify-content: space-between;
        .photo{
            width: 68rpx;
            height: 68rpx;
        }
        .msg{
            // width: 200rpx;
            margin-left: 20rpx;
            .name{
                font-size: 24rpx;
            }
            .time{
                color:rgba(140,145,152,1);
                font-size:22rpx;
            }
        }
        .right{
            color:rgba(235,128,121,1);
            font-size: 24rpx;
            // margin-left: 324rpx;
            // float: right;
            width: 60rpx;
        }
        .readed{
            color:rgba(189,196,206,1);
            font-size: 24rpx;
            margin-left: 364rpx;
        }
    }
    .bottomrow{
        font-size:24rpx;
        margin-top: 24rpx;
    }
}
</style>