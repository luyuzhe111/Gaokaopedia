<template>
  <view class="content">
      <view class="title">
          <image class="photo" :src="image" style="border-radius: 50%;"></image>
          <view class="name">{{nickname}}</view>
      </view>
      <scroll-view class="contentBox" :scroll-anchoring="true" :scroll-y="true" style="height: 81vh;" @scrolltoupper="end_fn" :scroll-top="top" :scroll-into-view="top" @scroll="scroll">
		  <view class="contentbox" v-for="(item,index) in meslist" :key="index">
			  <view class="comment"  v-if="item.userb_id==user_id">
			      <span>{{item.des_content}}</span>
			      <view class="time">{{item.createtime}}</view>
			  </view>
			  		 
			  		
			  <view class="comment" :style="{'margin-left:246rpx;': user_id==item.usera_id}" v-if="item.usera_id==user_id">
			      <span>{{item.des_content}}</span>
			      <view class="time">{{item.createtime}}</view>
			  </view>
		  </view>
		 
			 
		 
          
      </scroll-view>
      <view class="inputBox">
          <input type="text" class="input" v-model="des_content">
          <view class="btn" @click="sendmsg">发送</view>
      </view>
  </view>
</template>

<script>
import app from "../../App.vue";
export default {
	
    data(){
        return{
            des_content:'',
            userb_id:'',
            page:1,
            size:10,
            meslist:[],
            image:'',
            nickname:'',
            user_id:'',
			usera_id:'',
			chuanuserid:'',
			top:200
        }
    },
    methods:{
		
		// 触底事件
		end_fn(){
			console.log(34734478)
			let newpage=this.page;
			newpage++;
			this.page=newpage;
			this.getmeslist()
		},
		scroll(e){
			console.log(e);
			this.top=e.detail.scrollTop;
		},
			
		// 获取个人信息
		getmyinfo(){
			let that=this;
			var url = "student/getMyInfo"
			var params={
				token:uni.getStorageSync('token')
			}
			app.post(url,params,"get").then((res)=>{
				console.log(res)
				// this.user_id
			}).catch((err)=>{
				console.log(err)
			})
		},
        sendmsg(){
            var that = this
            var url = "mes/sendMes"
            var params = {
                token:uni.getStorageSync('token'),
                userb_id:that.chuanuserid,
                des_content:that.des_content
            }
            app.post(url,params,"post").then((res)=>{
                console.log(res)
				var date=new Date();
				var year=date.getFullYear();
				var month=date.getMonth()+1<10?'0'+(date.getMonth()+1):date.getMonth()+1;
				var day=date.getDate()<10?'0'+(date.getDate()):date.getDate();
				var datek=year+'-'+month+'-'+day
				let obj={}
				if(that.user_id==that.usera_id){
					console.log(43839438489)
					 obj={
						 usera_id:that.usera_id,
						 userb_id:that.userb_id,
						 des_content:that.des_content,
						 createtime:datek
					}
				}else{
					console.log(2222888888)
					 obj={
						 usera_id:that.userb_id,
						 userb_id:that.usera_id,
						 des_content:that.des_content,
						 createtime:datek
					}
				}
               
				
				that.meslist.push(obj);
				that.meslist=that.meslist;
				console.log(that.meslist)
				 that.des_content='';
				 wx.getSystemInfo({
				        success: function (res) {
				           wx.createSelectorQuery().select('.contentbox').boundingClientRect(function (rect) {
				 								console.log('777666554432',rect)
				              var is_1_height = Number(rect.height) // 节点的宽度
				                that.top=that.top+is_1_height*2;
				 								 console.log(that.top)
				                
				            }).exec();
				        }
				    });
				
				 
            }).catch((err)=>{
                console.log(err)
            })
        },
        getmeslist(){
            var that = this
            var url = "mes/getMesDetail"
            var params = {
                token:uni.getStorageSync('token'),
                page:that.page,
                size:that.size,
                userb_id:that.chuanuserid
            }
            app.post(url,params,"get").then((res)=>{
                console.log(res)
				
				// res.forEach(function(value,index,array){
				// 	arr.unshift(value)
				// })
				
				let new_arr = [];
				 new_arr = res.concat(that.meslist);
				that.meslist = new_arr;
                 // that.meslist=that.meslist.concat(res); 
				// that.meslist=that.meslist.reverse();
				// that.meslist=that.meslist
				console.log('3478834984',that.meslist)
				if(that.page==1){
					setTimeout(function(){
							wx.getSystemInfo({
							       success: function (res) {
							          wx.createSelectorQuery().select('.contentbox').boundingClientRect(function (rect) {
												console.log('777666554432kkkkkk',rect)
							             var is_1_height =that.meslist.length*Number(rect.height) // 节点的宽度
							               that.top=is_1_height;
										
							               
							           }).exec();
							       }
							   });			 
					},500)
				}
				
				
            }).catch((err)=>{
                console.log(err)
            })
        }
        
    },
    onLoad(options){
		
        console.log(options)
        this.userb_id=options.userb_id
        this.user_id=options.user_id
        this.nickname=options.nickname
        this.image=options.head_image
		this.usera_id=options.usera_id;
		if(this.user_id==this.usera_id){
			this.chuanuserid=this.userb_id
		}else{
			this.chuanuserid=this.usera_id
		}
		
		console.log('11222',this.userb_id,this.user_id)
        this.getmeslist();
		// 获取个人信息
		 this.getmyinfo();
		
    },
	onReachBottom(){
		
	}
    
}
</script>

<style lang="less">
	page{
		background-color: #F9F9F9;
		height:100%;
	}
.content{
	height:100%;
    // background-color: #F9F9F9;
    .title{
        display: flex;
        align-items: center;
        background-color: #fff;
        padding: 16rpx 32rpx;
		margin-bottom:20rpx;
        .photo{
            width: 68rpx;
            height: 68rpx;
        }
        .name{
            font-size: 28rpx;
            margin-left: 18rpx;
        }
    }
    .contentBox{
        // height: 100vh;
        background-color: #F9F9F9;
		margin-bottom:120rpx;
        .right{
            margin-left:246rpx;
        }
        .comment{
            color:rgba(6,18,30,1);
            font-size: 24rpx;
            width:472rpx;
            
            background:rgba(255,255,255,1);
            padding: 22rpx 18rpx;
            box-sizing: border-box;
            margin: 56rpx 32rpx;
            
            .time{
                text-align: right;
                color: #8C9198;
                font-size: 22rpx;
            }
        }
    }
    .inputBox{
        display: flex;
        background-color: #fff;
        padding: 16rpx 32rpx;
        position: fixed;
        bottom: 0;
        .input{
            width:506rpx;
            height:68rpx;
            background:rgba(238,238,238,1);
            opacity:1;
            border-radius:8rpx;
			padding:0 20rpx;
			box-sizing: border-box;
			color:#8C9198;
			font-size: 28rpx;
        }
        .btn{
            width:150rpx;
            height:68rpx;
            background:rgba(129,195,191,1);
            opacity:1;
            border-radius:8rpx;
            color: #fff;
            text-align: center;
            line-height: 68rpx;
            font-size: 24rpx;
            margin-left: 30rpx;
        }
    }
}
</style>