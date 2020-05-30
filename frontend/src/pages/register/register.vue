<template>
	<view class="content">
		<view class="register">
			<!-- url="./highRegister" -->
			<view class="type">
				<image class="photo" src="../../static/gaozhongsheng_img@2x.png"></image>
				<view class="text">我是高中生</view>
				<button class="btn_group" open-type="getUserInfo" withCredentials="true" lang="zh_CN" @getuserinfo="wxGetUserInfo"
				 :data-type="1"></button>
			</view>
			<!-- url="./college" -->
			<view class="type">
				<image class="photo" src="../../static/daxeusheng_img@2x.png"></image>
				<view class="text">我是大学生</view>
				<button class="btn_group" open-type="getUserInfo" withCredentials="true" lang="zh_CN" @getuserinfo="wxGetUserInfo"
				 :data-type="2"></button>
			</view>
		</view>

	</view>
</template>

<script>
	import app from "../../App.vue";
	export default {
		data() {
			return {
				iv: '',
				encrypted_data: '',
				session_key: '',
				openid: '',
				type:'',
				istoken:''
			}
		},
		onLoad(options) {
			if(options.istoken!=undefined){
				this.istoken=options.istoken
			}
			console.log(uni.getStorageSync('token'))
			console.log(this.istoken)
			if(uni.getStorageSync('token')!=''&&this.istoken==''){
				
				this.getusermsg()
			}else if (uni.getStorageSync('token')==''&&this.istoken==''){
				uni.setStorageSync("chosetype",1)
				uni.switchTab({
					url:'../school/school'
				})
			}
			this.get_code();
			
		},
		methods: {
			// 用code 换token 
			code2Token(){
				let that = this;
				uni.login({
					success(res) {
						let url = 'common/codeToToken'
						app.post(url,{
							code: res.code
						}).then(r=>{
							console.log('code换取token',r)
							uni.setStorageSync("token",r.userInfo.token)
							
							// uni.setStorageSync('token',r.userInfo.token);
							// uni.setStorageSync('isRegister',r.userInfo.is_register);
							// that.isRegister = r.userInfo.is_register;
						})
					}
				})
			},
			// 获取个人信息
			getusermsg(){
				console.log('3743473734737487834')
				
				var url = "student/getMyInfo"
				var params={
						token:uni.getStorageSync('token')
				}
				app.post(url,params,"get").then((res)=>{
					
					if(res.level==1){
						uni.setStorageSync("chosetype",1)
						wx.switchTab({
							url:'../school/school'
						})
					}else if (res.level==2){
						uni.setStorageSync("chosetype",2)
						wx.switchTab({
							url:'../schoolmate/schoolmate?type='+2
						})
					}
				}).catch((err)=>{
					console.log(err)
					uni.setStorageSync("chosetype",1)
					wx.switchTab({
						url:'../school/school'
					})
					uni.clearStorageSync()
				})
			},

			wxGetUserInfo(e) {
				let that = this;
				// type：1  高中生  2  大学生
				let type = e.currentTarget.dataset.type;
				console.log(type)
				that.type = type
				uni.getUserInfo({
					provider: 'weixin',
					success: function(infoRes) {
						console.log(infoRes)
						that.login()
						that.iv = infoRes.iv;
						that.encrypted_data = infoRes.encryptedData
						that.login()
					},
					fail(res) {

					}
				});


			},
			// 获取sessionkey
			get_code() {
				let that = this;
				uni.login({
					provider: "weixin",
					success(r) {
						
						let url = "common/getSessionKey";
						app.post(url, {
							code: r.code
						}, "post").then(r => {
							
							that.session_key = r.session_key;
							that.openid = r.openid;
							// uni.setStorageSync('openid', r.openid);
							// uni.setStorageSync('session_key', r.session_key);
						}).catch(err => {})

					}
				})
			},

			// 获取token  登录
			login() {
				let that = this;
				// uni.showLoading({ title: '加载中' })
				console.log(343438)
				console.log(that.session_key);
				console.log()
				let url = "common/login";
				let param = {
					sessionKey: that.session_key,
					encryptedData: that.encrypted_data,
					iv: that.iv
				}
				app.post(url, param, "get").then(r => {
					uni.setStorageSync('token', r.userInfo.token);
					// 判断是否注册过
					this.isuserlogin()

				}).catch(err => {
					console.log(err)
					
					
				})
			},
			
			isuserlogin(){
					var that = this
				//   console.log(6666)
				  var url ='register/isRegister';
				  var params = {
					  token:uni.getStorageSync('token')
				  }
				  app.post(url,params,"get").then((res)=>{
					  console.log(res)
					  
						var url = "student/getMyInfo"
						var params={
							token:uni.getStorageSync('token')
						}
						app.post(url,params,"get").then((res)=>{
							console.log(res)
							if(res.level==1){
								uni.setStorageSync("chosetype",1)
								wx.switchTab({
									url:'../school/school'
								})
							}else if (res.level==2){
								uni.setStorageSync("chosetype",2)
								wx.switchTab({
									url:'../schoolmate/schoolmate?type='+2
								})
							}
						}).catch((err)=>{
							console.log(err)
						})
				  }).catch((err) => {
					  console.log(err)
					  if(err.data==null){
						if (that.type == 1) {
								// console.log(9999)
								uni.navigateTo({
									url: "./highRegister"
								})
							} else {
								uni.navigateTo({
								url: "./college"
							})
						}
					  }
				})
			  }
		}

	}
</script>

<style lang="less">
	.content {
		background-color: #f9f9f9;
		height: 100vh;
		position: relative;
		width: 750rpx;

		.register {
			display: flex;
			position: absolute;
			top: 50%;
			left: 50%;
			transform: translate(-50%, -50%);
			// margin: 0 auto;
		}

		.type {
			width: 266rpx;
			height: 352rpx;
			background-color: #fff;
			text-align: center;
			margin: 0 22rpx;
			position: relative;

			.photo {
				width: 160rpx;
				height: 228rpx;
				margin-top: 36rpx;
			}

			.text {
				color: rgba(6, 18, 30, 1);
				font-size: 28rpx;
				margin-top: 20rpx;
			}

			.btn_group {
				width: 226rpx;
				height: 352rpx;
				position: absolute;
				top: 0;
				left: 0;
				background: transparent;
				border: none;
				outline: none
			}
		}
	}
</style>
