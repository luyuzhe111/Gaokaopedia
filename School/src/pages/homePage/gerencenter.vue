<template>
	<view class="content">
		<view class="card">
			<view class="item">
				<view class="name">姓名</view>
				<!-- <view class="cont">爱学习的王笑笑</view> -->
				<input type="text" v-model="username" class="cont" :placeholder="userinfolist.nickname=='undefide' ? '' : userinfolist.nickname"
				 placeholder-style="text-align: right" style="text-align: right">
			</view>
			<view class="item">
				<view class="name">头像</view>
				<image class="photo" @click="upload" :src="isUpload ? image : userinfolist.head_image"></image>
			</view>
			<view class="item">
				<view class="name">高中</view>
				<view class="cont" >{{userinfolist.school_info.name==undefined?'':userinfolist.school_info.name}}
					<!-- <image class="icon" src="../../static/dizhi_btn@2x.png"></image> -->
				</view>
			</view>
			<!-- <picker mode="date" :value="date" :start="startDate" :end="endDate" @change="bindDateChange">
				
			</picker> -->
			<view class="item" style="border-bottom:none">
				<view class="name">入学年份</view>
				<view class="cont">{{userinfolist.starttime==undefined?'':userinfolist.starttime}}
					<!-- <image class="icon" src="../../static/dizhi_btn@2x.png"></image> -->
				</view>
			</view>
		</view>
		<view class="btn" @click="update">保存</view>
	</view>
</template>

<script>
	import app from "../../App.vue";
	export default {
		data() {
			return {
				username: '',
				image: '',
				image2: '',
				isUpload: false,
				userinfolist: {}
			}
		},
		computed: {
			startDate() {
				return this.getDate('start');
			},
			endDate() {
				return this.getDate('end');
			}
		},
		methods: {
			
			// 选择年月日
			bindDateChange: function(e) {
				this.userinfolist.starttime = e.target.value;
				this.userinfolist=this.userinfolist
			},
			getDate(type) {
				const date = new Date();
				let year = date.getFullYear();
				let month = date.getMonth() + 1;
				let day = date.getDate();

				if (type === 'start') {
					year = year - 60;
				} else if (type === 'end') {
					year = year + 2;
				}
				month = month > 9 ? month : '0' + month;;
				day = day > 9 ? day : '0' + day;
				return `${year}-${month}-${day}`;
			},
			upload() {
				var that = this;
				uni.chooseImage({
					count: 1,
					sizeType: ['original', 'compressed'], //可以指定是原图还是压缩图，默认二者都有
					sourceType: ['album'], //从相册选择
					success: function(res) {
						const tempFilePaths = res.tempFilePaths;
						that.image = tempFilePaths[0];
						that.image2 = tempFilePaths[0];
						app.upload('image', res.tempFilePaths[0], "post").then((res) => {
							console.log(res)
							//  let newimage=that.image
							that.image =  app.globalData.imageBaseUrl+kurl
							that.image2 = res.kurl
							console.log(res)
							console.log(that.image)
							console.log(res.url)
							//  newimage.push(res.url)
							that.isUpload = true
						}).catch((err) => {
							console.log(err)
						})
						//  console.log("tempFilePaths[0]",tempFilePaths[0])  //能够打印出选中的图片
						//  console.log(res)
						//  that.head_image=tempFilePaths[0]
						//  that.iconcheck = 1;//点击后图片更改状态由0变成1
					},
					error: function(e) {
						console.log(e);
					}
				});
			},
			timestampToTime(timestamp) {
			    var date = new Date(timestamp * 1000);//时间戳为10位需*1000，
			    var Y = date.getFullYear();
			    var M = date.getMonth() + 1 < 10 ? '0'+(date.getMonth()+1) : date.getMonth()+1  
			    var D = date.getDate()<10?'0'+(date.getDate()):date.getDate();
			    var h = date.getHours() + ':';
			    var m = date.getMinutes() + ':';
			    var s = date.getSeconds();
			    return Y +"-"+ M+'-' + D 
			},
			getuserinfo() {
				var that = this
				var url = "student/getMyInfo"
				var token = uni.getStorageSync('token')
				var params = {
					token: token
				}
				app.post(url, params, "get").then((res) => {
					console.log(res)
					res.starttime=that.timestampToTime(res.starttime)
					that.userinfolist = res
				}).catch((err) => {
					console.log(err)
				})
			},
			update() {
				var that = this
				var url = "student/updateStudentInfo"
				var token = uni.getStorageSync('token')
				var params = {
					token: token,
					nickname: that.userinfolist.nickname,
					head_image: that.image2 == '' ? that.userinfolist.head_image : that.image2
				}
				app.post(url, params, "post").then((res) => {
					console.log(res)


					uni.showToast({
						title: '个人信息保存成功',
						icon: 'none'
					})
					setTimeout(() => {
						uni.navigateBack()
					}, 1500);

				}).catch((err) => {
					console.log(err)
					if (err.code == 0) {
						uni.showToast({
							title: err.msg,
							icon: 'none'
						})
					}
				})

			}
		},
		onLoad() {
			this.getuserinfo()
		}

	}
</script>

<style lang="less">
	.content {
		background-color: rgba(249, 249, 249, 1);
		padding: 32rpx 0;
		height: 100vh;

		.card {
			width: 686rpx;
			// height:428rpx;
			background: rgba(255, 255, 255, 1);
			opacity: 1;
			border-radius: 20rpx;
			margin: 0 auto;

			.item {
				width: 622rpx;
				display: flex;
				justify-content: space-between;
				align-items: center;
				padding: 32rpx 0;
				box-sizing: border-box;
				font-size: 28rpx;
				margin: 0 auto;
				border-bottom: 1rpx solid rgba(238, 238, 238, 1);

				.photo {
					width: 64rpx;
					height: 64rpx;
					border-radius: 50%;
				}

				.icon {
					width: 11rpx;
					height: 22rpx;
					margin-left: 10rpx;
				}
			}
		}

		.btn {
			width: 684rpx;
			height: 80rpx;
			background: rgba(45, 85, 117, 1);
			opacity: 1;
			border-radius: 8rpx;
			color: #fff;
			line-height: 80rpx;
			text-align: center;
			margin: 0 auto;
			margin-top: 620rpx;
		}
	}
</style>
