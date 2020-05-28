<template>
	<view class="content">
		<view class="search">
			<view class="input" @click="tosearchpage">
				<image class="icon" src="../../static/sousuo_icon@2x.png"></image>
				<view class="text">请输入搜索的学校</view>
				<!-- <input type="text" placeholder="请输入搜索的学校" placeholder-style="color:rgba(189,196,206,1);font-size:28rpx;"/> -->
			</view>
		</view>
	<!-- url="./schoolDetails" -->
		<view class="contentBox">
			<view class="item" v-for="(item,index) in schoollist" :key="index" @click="godetail(item)">
				<view class="title">{{item.name}}</view>
				<image class="icon" src="item.icon_image"></image>
			</view>
			<!-- <view class="item" @click="godetail">
				<view class="title">河北工业大学</view>
				<image class="icon" src="../../static/xuexiao_icon@2x.png"></image>
			</view>
			<view class="item">
				<view class="title">河北工业大学</view>
				<image class="icon" src="../../static/xuexiao_icon@2x.png"></image>
			</view>
			<view class="item">
				<view class="title">河北工业大学</view>
				<image class="icon" src="../../static/xuexiao_icon@2x.png"></image>
			</view> -->
		</view>
		<view class="weiguanzhu" v-if="isnulldata==true||token==''">
			<view class="title">请关注更多学校</view>
			<view class="btn" @click="tosearchpage">立即关注</view>
		</view>
		<!-- <tab :current="currentTabIndex" @getData="tabClick"></tab> -->
		<!-- <view class="teacherfooter">
			<view class="teacherfootitem">
				<view class="teacherfootitemtop" @click="footerselChange" :data-id="1"   >
					<image :src="footersel==1?'/static/xuexiao_icon@2x.png':'/static/xuexiao_icon@2x3.png'"></image>
				</view>
				<view class="teacherfootname" :class="footersel==1?'selactive':''">学校</view>
			</view>
			<view class="teacherfootitem" @click="jumpshchool==true?footerselChange(2,'/pages/schoolmate/schoolmate?type=1'):''" :data-id="2" :data-url="'/pages/schoolmate/schoolmate?type='+1">
				<view class="teacherfootitemtop">
					<image :src="footersel==2?'/static/zhaoxiaoyou_icon@2x2.png':'/static/zhaoxiaoyou_icon@2x.png'"></image>
				</view>
				<view class="teacherfootname" :class="footersel==2?'selactive':''">找校友</view>
			</view>
			<view class="teacherfootitem" @click="jumpshchool==true?footerselChange(3,'/pages/homePage/homePage?type=1'):''" :data-id="3" :data-url="'/pages/homePage/homePage?type='+1">
				<view class="teacherfootitemtop">
					<image :src="footersel==3?'/static/wodezhuye_icon@2x2.png':'/static/wodezhuye_icon@2x.png'"></image>
				</view>
				<view class="teacherfootname" :class="footersel==3?'selactive':''">我的主页</view>
			</view>
		</view> -->
		<tabBar :current="currentTabIndex" backgroundColor="#fbfbfb" color="#999" tintColor="#42b983"></tabBar>


	</view>
</template>

<script>
	import app from "../../App.vue";
	import tabBar from '../../components/tabvue/tabvue.vue'
	
	export default {
		components: {
			tabBar,
		},
		data() {
			return {
				currentTabIndex: 0,
				footersel:1,
				page:1,
				word:'',
				schoollist:[],
				isnulldata:false,
				token:'',
				isRegister: 0,
				jumpshchool:true
			}
		},
		onLoad(options){
			
			
			console.log(options);
			// this.getschoollist();
			// this.isuesrlogin()
			
			
		},
		onShow() {
			 this.code2Token();
			this.token=uni.getStorageSync('token')
			uni.hideTabBar({
			
			})
			this.page=1;
			this.schoollist=[];
			this.getlikeschool();
			this.jumpshchool=true
		},
		methods: {
			// 底部导航跳转
			footerselChange(id,url) {
				console.log(34783478)
				// let url = e.currentTarget.dataset.url;
				// console.log(url)
				uni.redirectTo({
					url: url
				})
				this.jumpshchool=false
			},
			code2Token(){
				let that = this;
				uni.login({
					success(res) {
						let url = 'common/codeToToken'
						app.post(url,{
							code: res.code
						}).then(r=>{
							console.log()
							// uni.setStorageSync('token',r.userInfo.token);
							uni.setStorageSync('isRegister',r.userInfo.is_register);
							that.isRegister = r.userInfo.is_register;
						})
					}
				})
			},
			
			// 获取大学列表
			// getschoollist(){
			// 	 let that = this;
			// 	    var url = 'university/getUniversityList';
			// 	    var params = {
			// 	      page:that.page,
			// 	      size:10,
			// 		  word:'',
			// 		  level_id:'',
			// 		  province_id:''
			// 	    }
			// 	    app.post(url, params,"post").then((res) => {
			// 	      console.log(res);
			// 		  that.schoollist=that.schoollist.concat(res)
				
			// 	    }).catch((err) => {
				
			// 	    })
			// }, 

			//获取关注的大学
			getlikeschool(){
    			var token = uni.getStorageSync('token')
				let that = this
				var url = "university/getMyLikeUniversity"
				var params ={
					page:that.page,
					size:10,
      				token:token
				}
				app.post(url,params,"get").then((res)=>{
					that.schoollist=that.schoollist.concat(res)
					
					if(that.schoollist.length==0){
						that.isnulldata=true
					}else{
						that.isnulldata=false
					}
					
					console.log('显示关注',this.isnulldata)
				}).catch((err)=>{
					console.log(err)
				})
			},
			// 进入学校详情页
			godetail(item){
				if(uni.getStorageSync('token')==''){
					uni.showToast({
						title:"请登陆后操作",
						icon:"none"
					})
					setTimeout(() => {
						uni.navigateTo({
							url:'../register/register?istoken='+1
						})
					}, 1500);
				}else{
					let id=item.id;
					uni.navigateTo({
						url:'./schoolDetails?id='+id
					})
				}
				
			},
			toschool(){
			console.log(3434)
		},
		tosearchpage(){
			let token=uni.getStorageSync("token")
			
			if(uni.getStorageSync('token')==''){
				uni.showToast({
					title:"请登陆后操作",
					icon:"none"
				})
				setTimeout(() => {
					uni.navigateTo({
						url:'../register/register?istoken='+1
					})
				}, 1500);
			}else{
				if(this.isRegister == 0){
					uni.showToast({
						title: '请完善信息后操作',
						icon: 'none'
					})
					setTimeout(() => {
						uni.navigateTo({
							url:'../register/register?istoken='+1
						})
					}, 1500);
				}else{
					wx.navigateTo({
						url:'./searchSchool'
					})
				}
			}
		},
		
		
		onReachBottom: function() {
		      let newpage=this.page;
		      newpage++;
		      this. page=newpage
		      this.getlikeschool()
		  },

		//   isuserlogin(){
		// 	  console.log(6666)
		// 	  var url ='register/isRegister';
		// 	  var params = {
				 
		// 	  }
		// 	  app.post(url,params,"post").then((res)=>{
		// 		  console.log(res)
		// 	  }).catch((err) => {
				
		// 	})
		//   }
		},
		
	}
</script>

<style lang="less">
	page{
		background: rgba(249, 249, 249, 1);
		// height: 100%;
	}
	.content {
		
		// height: 100%;
		padding-bottom: 120rpx;
		box-sizing: border-box;
		.search {
			width: 750rpx;
			height: 82rpx;
			background: rgba(45, 85, 117, 1);
			overflow: hidden;
			position: fixed;
			top: 0;

			.input {
				width: 686rpx;
				height: 64rpx;
				background-color: #fff;
				border-radius: 32rpx;
				margin: 0 auto;
				padding: 0 28rpx;
				box-sizing: border-box;
				display: flex;
				align-items: center;
				text-align: center;

				.icon {
					width: 30rpx;
					height: 30rpx;
				}

				.text {
					color: rgba(189, 196, 206, 1);
					font-size: 28rpx;
					margin: 0 auto;
				}
			}
		}
		.weiguanzhu{
			.title{
				text-align: center;
				margin-top: 346rpx;
			}
			.btn{
				// width: ;
				width:220rpx;
				height:80rpx;
				background:rgba(45,85,117,1);
				opacity:1;
				margin: 0 auto;
				border-radius:8rpx;
				margin-top: 92rpx;
				color: #fff;
				line-height: 80rpx;
				text-align: center;
				
			}
		}

		.contentBox {
			padding: 24rpx 32rpx;
			margin-top: 82rpx;

			.item {
				width: 686rpx;
				height: 160rpx;
				background: rgba(255, 255, 255, 1);
				opacity: 1;
				border-radius: 8rpx;
				display: flex;
				font-size: 28rpx;
				color: rgba(61, 68, 77, 1);
				justify-content: space-between;
				padding: 0 40rpx;
				box-sizing: border-box;
				margin-bottom: 24rpx;

				.title {
					line-height: 160rpx;
				}

				.icon {
					width: 120rpx;
					height: 120rpx;
					margin-top: 20rpx;
				}
			}
		}
	}
</style>
