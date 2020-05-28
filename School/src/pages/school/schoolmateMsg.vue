<template>
	<view class="content">
		<view class="msgCard">
			<image :src="matemsg.head_image" class="photo"></image>
			<view class="title">{{matemsg.nickname==undefined?'':matemsg.nickname}}</view>
			<view class="o">{{matemsg.university_name==undefined?'':matemsg.university_name}} |
				{{matemsg.college_name==undefined?'':matemsg.college_name}} | {{matemsg.end_year==undefined?'':matemsg.end_year}}</view>
			<view class="t">{{matemsg.school_name==undefined?'':matemsg.school_name}} | <text v-for="(item,index) in matemsg.subject_names"
				 :key="index">{{item}}</text> | {{matemsg.up_name==undefined?'':matemsg.up_name}}</view>
			<view class="mall">个人邮箱：{{matemsg.email==undefined?'':matemsg.email}}</view>
			<view class="right" v-if="matemsg.is_like==1" @click="notlike">
				<image src="../../static/yiguanzhu_img@2x.png"></image>
				<view class="text">已关注</view>
			</view>
			<view class="right" @click="like" v-else style="width:132rpx;height:54rpx;background:rgba(129,195,191,1);border-radius:32rpx 0rpx 0rpx 32rpx;">
				<!-- <image src="../../static/yiguanzhu_img@2x.png"></image> -->
				<view class="text">
					<image class="icon" src="../../static/guanzhu_icon@2x.png"></image>关注
				</view>
			</view>
		</view>
		<view class="tabCard">
			<!-- <view class="item"  v-for="(item,index) in articletypelist" :key="index">{{item.name}}</view> -->
			<view class="item" :class="sel==index?'active':''" @click="selecttype(item,index)" v-for="(item,index) in articletypelist"
			 :key="index" :data-id="item.id">{{item.name}}
				<image class="selected" src="../../static/xuanzhong_icon@2x.png" v-if="sel==index"></image>
				<!-- <image class="selected" src="" v-else></image> -->
			</view>
		</view>
		<view class="nodata" v-if="articleList.length==0">暂无数据</view>
		<view class="articl" v-else>
			<view v-for="(item,index) in articleList" :key="index" @click="toarticlemsg(item)">
				<view class="articltitle">
					<image class="photo" :src="item.head_image" @click.stop="avatarjump(item.user_id)"></image>
					<view class="msg">
						<view class="name">{{item.nickname}}</view>
						<view class="tips">{{item.article_type_name}}</view>
					</view>
				</view>
				<view class="articleBox">
					<view class="title">{{item.title}}</view>
					<view class="txt">
						<view class="textblock">
							{{item.des_content}}
						</view>

						<!-- <text class="alltext" style="color:#2D5575;font-size:24rpx;font-weight:bold;" @click="toarticlemsg(item)" v-if="item.des_content.length>150">全部</text> -->
					</view>
					<view style="display:flex;">
						<image :src="item" v-for="(item,indexk) in item.des_images" :key="indexk" @click.stop="preimg(index,indexk)"
						 style="margin-right:10rpx;margin-bottom:10rpx"></image>
					</view>

					<!-- <image src="../../static/bg_img@2x.png"></image> -->
				</view>
			</view>


		</view>
		<view v-if="type==1">
			<view class="botmBox">
				<view class="liuyan" @click="liuyanBoxShow">
					<image src="../../static/liuyan_icon@2x.png"></image>
					<view class="txt">我要留言</view>
				</view>
				<view class="daxie" style="border:none" @click="daxieShow">
					<image src="../../static/dashang_icon@2x.png"></image>
					<view class="txt">我要答谢</view>
				</view>
			</view>
			<view class="popup" v-if="isliuyanBoxShow">
				<view class="card">
					<view class="title">我要留言</view>
					<view class="liuyanContent">
						<textarea name="" id="" cols="30" rows="10" class="txt" v-model="content" placeholder="请输入你想请教的问题"
						 placeholder-style="color:rgba(189,196,206,1);font-size:24rpx;"></textarea>
						<view class="num">{{content.length}}/200</view>

					</view>
					<view class="btn" @click="sendmsg">发送留言</view>
					<view class="close" @click="liuyanBoxHide">
						<image src="../../static/guanbi_icon@2x.png"></image>
					</view>
				</view>
			</view>
			<view class="popup2" v-if="isdaxieBoxShow">
				<view class="card">
					<view class="title">我要答谢</view>
					<view v-if="moneyshow">
						<view>
							<view class="box" :class="{'active':isactive==1}" @click="picked" data-id=1 data-money=1>¥1</view>
							<view class="box" :class="{'active':isactive==2}" @click="picked" data-id=2 data-money=6>¥6</view>
							<view class="box" :class="{'active':isactive==3}" @click="picked" data-id=3 data-money=10>¥10</view>
							<view class="box" :class="{'active':isactive==4}" @click="picked" data-id=4 data-money=30>¥30</view>
							<view class="box" :class="{'active':isactive==5}" @click="picked" data-id=5 data-money=50>¥50</view>
							<view class="box" :class="{'active':isactive==6}" @click="picked" data-id=6 data-money=100>¥100</view>
							<view class="box" :class="{'active':isactive==7}" @click="moneyShow" data-id=7>其他金额</view>
						</view>

						<view class="btn" @click="pickedsure" style="margin:28rpx auto 0;">确定</view>
					</view>
					<view v-else>
						<view class="input">
							<view class="txt">¥</view>
							<input type="text" placeholder="请输入答谢金额" placeholder-style="color:rgba(189,196,206,1);font-size:28rpx;" @input="entermoney">
						</view>
						<view class="btn" @click="moneyHide">确定</view>
					</view>
					<view class="close" @click="daxieHide">
						<image src="../../static/guanbi_icon@2x.png"></image>
					</view>
				</view>
			</view>
		</view>
	</view>
</template>

<script>
	import app from "../../App.vue";
	export default {
		data() {
			return {
				isliuyanBoxShow: false,
				isdaxieBoxShow: false,
				moneyshow: true,
				id: '',
				matemsg: {},
				articletypelist: [],
				articleList: [],
				user_id: '',
				page: '1',
				size: '10',
				type_id: '',
				content: '',
				isactive: 0,
				type: '',
				money: '',
				sel: 0

			}
		},
		onLoad(options) {
			console.log('我的数据', options)
			this.id = options.id;
			this.user_id = options.user_id
			this.type = options.type
			console.log(options)
			this.getmatemsg()
			this.getarticletype();
			this.getAllArticle()

		},
		methods: {
			// 跳转作者头像
			avatarjump(userid){
				// uni.navigateTo({
				// 	url: "../school/schoolmateMsg?user_id=" + id + "&type=" + this.type
				// })
				this.page=1;
				this.articleList=[];
				this.sel = 0;
				this.type_id = '';
				this.user_id=userid;
				this.getmatemsg();
				this.getAllArticle()
			},
			
			// 进入文章详情
			toarticl(e) {
				var id = e.currentTarget.dataset.id
				uni.navigateTo({
					url: '../school/article?article_id=' + id
				})
			},
			preimg(index, indexk) {
				let that = this;
				uni.previewImage({
					current: that.articleList[index].des_images[indexk],
					urls: that.articleList[index].des_images,
					success: function(res) {},
					fail: function(res) {},
					complete: function(res) {},
				})

			},
			liuyanBoxShow() {
				this.isliuyanBoxShow = true
			},
			liuyanBoxHide() {
				this.isliuyanBoxShow = false
			},
			daxieShow() {
				this.isdaxieBoxShow = true;

			},
			daxieHide() {
				this.isdaxieBoxShow = false
				this.moneyshow = true
				this.isactive = 1
			},
			moneyShow() {
				this.moneyshow = false
				this.isactive = 7
			},
			//输入其他金额
			entermoney(e) {
				this.money = e.detail.value
			},
			moneyHide() {
				// this.isactive=e.currentTarget.dataset.id
				// this.money=e.currentTarget.dataset.money
				var that = this;
				var reg = /^-?\d+$/

				if (!reg.test(that.money)) {
					uni.showToast({
						title: '请输入整数金额',
						icon: 'none'
					})

					return false
				}
				var url = "thank/pay"
				var token = uni.getStorageSync('token')
				var params = {
					token: token,
					money: that.money,
					userb_id: that.user_id
				}
				app.post(url, params, "post").then((res) => {
					console.log(res)
					var timeStamp = res.timeStamp.toString();
					// console.log(timeStamp);
					wx.requestPayment({
						timeStamp: timeStamp,
						nonceStr: res.nonceStr,
						package: res.package,
						signType: res.signType,
						paySign: res.paySign,
						success: function(res) {
							console.log(res)
							wx.navigateTo({
								url: '/pages/orderbox/orderbox?index=' + 0
							})
						},
						fail: function(res) {
							console.log(123);
							console.log(res);
						}
					})
				}).catch((err) => {
					console.log(err)
				})
				// this.moneyshow=true
			},
			getmatemsg() {
				let token = uni.getStorageSync('token')
				let that = this
				var url = "student/getStudentInfo"
				var params = {
					user_id: that.user_id,
					token: token
				}
				app.post(url, params, "get").then((res) => {
					console.log(res)
					that.matemsg = res
					// that.user_id=res.user_id
					console.log(res.university_id)
				}).catch((err) => {
					console.log(err)
				})
			},
			getarticletype() {
				var that = this
				var url = "article/getArticleType"
				var params = {}
				app.post(url, params, "get").then((res) => {
					this.articletypelist = res;
					let obj = {
						id: '',
						name: '全部'
					}
					res.unshift(obj)

					console.log(res)
				}).catch((err) => {
					console.log(err)
				})

			},
			getAllArticle() {
				var token = uni.getStorageSync('token')
				var that = this
				var url = "article/getArticleList"
				var params = {
					// university_id:that.university_id,
					page: that.page,
					size: that.size,
					user_id: that.user_id,
					type_id: that.type_id,
					token: token
				}
				app.post(url, params, "get").then((res) => {
					// res.forEach(function(value,index,array){
					// 	value.head_image=app.globalData.imageBaseUrl+value.head_image
					// })
					console.log(res)
					if(res.length!=0){
						this.articleList =this.articleList.concat(res)
						
					}else{
						if(that.page>1){
							uni.showToast({
								title:'没有更多了~',
								icon:"none"
							})
							
						}
					}
					
					 
				}).catch((err) => {
					console.log(err)
				})
			},
			sendmsg() {
				var that = this
				var url = "mes/sendMes"
				var token = uni.getStorageSync('token')
				var params = {
					token: token,
					userb_id: that.user_id,
					des_content: that.content
				}
				app.post(url, params, "post").then((res) => {
					console.log(res)
					uni.showToast({
						title:'发送成功',
						icon:"none"
					})
					that.content = ''
					that.isliuyanBoxShow = false
				}).catch((err) => {
					console.log(err)
				})
			},
			picked(e) {
				this.isactive = e.currentTarget.dataset.id
				this.money = e.currentTarget.dataset.money

			},
			pickedsure() {
				var that = this;
				if (that.money == '') {
					uni.showToast({
						title: "请选择答谢金额",
						icon: "none"
					})
				}
				var url = "thank/pay"
				var token = uni.getStorageSync('token')
				var params = {
					token: token,
					money: that.money,
					userb_id: that.user_id
				}
				app.post(url, params, "post").then((res) => {
					console.log(res)
					var timeStamp = res.timeStamp.toString();
					// console.log(timeStamp);
					wx.requestPayment({
						timeStamp: timeStamp,
						nonceStr: res.nonceStr,
						package: res.package,
						signType: res.signType,
						paySign: res.paySign,
						success: function(res) {
							console.log(res)
							uni.showToast({
								title:'打赏成功',
								icon:'none'
							})
							setTimeout(function(){
								wx.navigateTo({
									url: '/pages/orderbox/orderbox?index=' + 0
								})
							},1500)
							
						},
						fail: function(res) {
							console.log(123);
							console.log(res);
						}
					})
				}).catch((err) => {
					console.log(err)
				})
			},
			toarticlemsg(item) {
				console.log('文章详情', item)
				uni.navigateTo({
					url: '../school/article?article_id=' + item.id
				})
			},
			// payment(res) {
			//     let that = this;
			//     var timeStamp = res.timeStamp.toString();
			//     // console.log(timeStamp);
			//     wx.requestPayment({
			//     timeStamp: timeStamp,
			//     nonceStr: res.nonceStr,
			//     package: res.package,
			//     signType: res.signType,
			//     paySign: res.paySign,
			//     success: function(res) {
			//         console.log(res)
			//         wx.navigateTo({
			//         url: '/pages/orderbox/orderbox?index=' + 0
			//         })
			//     },
			//     fail: function(res) {
			//         console.log(123);
			//         console.log(res);
			//     }
			//     })
			// },
			like() {
				var that = this
				var url = "student/likeStudent"
				var token = uni.getStorageSync('token')
				var params = {
					token: token,
					userb_id: that.user_id
				}
				app.post(url, params, "post").then((res) => {
					console.log(res)
					that.matemsg.is_like = 1
				}).catch((err) => {
					console.log(err)
				})
			},
			notlike() {
				var that = this
				var url = "student/likeStudent"
				var token = uni.getStorageSync('token')
				var params = {
					token: token,
					userb_id: that.user_id
				}
				app.post(url, params, "post").then((res) => {
					console.log(res)
					that.matemsg.is_like = 0
				}).catch((err) => {
					console.log(err)
				})
			},
			selecttype(item, index) {
				this.sel = index
				this.type_id = item.id;
				this.page=1;
				this.articleList=[];
				this.getAllArticle()
			},
		},
		onReachBottom() {
			let newpage=this.page;
			newpage++;
			this.page=newpage;
			this.getAllArticle()
		}
	}
</script>

<style lang="less">
	.popup2 {
		width: 100%;
		height: 100%;
		background: rgba(0, 0, 0, .6);
		// opacity:1;
		// border-radius:8rpx;
		position: fixed;
		top: 0;
		right: 0;
		z-index: 999;

		.card {
			width: 628rpx;
			height: 574rpx;
			background: rgba(255, 255, 255, 1);
			opacity: 1;
			border-radius: 8rpx;
			position: absolute;
			top: 50%;
			left: 50%;
			transform: translate(-50%, -50%);
			padding: 0 8rpx 0 36rpx;
			box-sizing: border-box;

			.close {
				width: 24rpx;
				height: 24rpx;
				position: absolute;
				top: 22rpx;
				right: 22rpx;

				image {
					width: 24rpx;
					height: 24rpx;
				}
			}

			.title {
				color: rgba(6, 18, 30, 1);
				font-size: 28rpx;
				text-align: center;
				margin-top: 62rpx;
				margin-bottom: 60rpx;
				font-weight: 500;
			}

			.input {
				width: 532rpx;
				height: 72rpx;
				background: rgba(255, 255, 255, 1);
				border: 1rpx solid rgba(6, 18, 30, 1);
				opacity: 1;
				border-radius: 36rpx;
				display: flex;
				align-items: center;
				padding: 0 144rpx;
				box-sizing: border-box;
				margin-top: 110rpx;

				input {
					width: 220rpx;
					height: 72rpx;
					margin-left: 20rpx;
				}
			}

			.btn {
				width: 220rpx;
				height: 60rpx;
				background: rgba(45, 85, 117, 1);
				opacity: 1;
				border-radius: 8rpx;
				color: #fff;
				font-size: 28rpx;
				line-height: 60rpx;
				text-align: center;
				margin: 0 auto;
				margin-top: 110rpx;
			}

			.box {
				width: 166rpx;
				height: 56rpx;
				background: rgba(255, 255, 255, 1);
				border: 1rpx solid rgba(6, 18, 30, 1);
				opacity: 1;
				border-radius: 40rpx;
				text-align: center;
				font-size: 28rpx;
				line-height: 56rpx;
				color: rgba(6, 18, 30, 1);
				display: inline-block;
				margin-right: 26rpx;
				margin-top: 40rpx;
			}

			.active {
				background: rgba(129, 195, 191, 1);
				border: 1rpx solid rgba(129, 195, 191);
				color: #fff;

			}
		}

	}

	.popup {
		width: 100%;
		height: 100%;
		background: rgba(0, 0, 0, .6);
		// opacity:1;
		// border-radius:8rpx;
		position: fixed;
		top: 0;
		right: 0;
		z-index: 999;

		.card {
			width: 628rpx;
			height: 674rpx;
			background: rgba(255, 255, 255, 1);
			opacity: 1;
			border-radius: 8rpx;
			position: absolute;
			top: 50%;
			left: 50%;
			transform: translate(-50%, -50%);

			.title {
				color: rgba(6, 18, 30, 1);
				font-size: 28rpx;
				text-align: center;
				margin-top: 62rpx;
			}

			.liuyanContent {
				width: 538rpx;
				height: 366rpx;
				background: rgba(249, 249, 249, 1);
				opacity: 1;
				border-radius: 8rpx;
				margin: 0 auto;
				margin-top: 26rpx;
				padding: 28rpx;
				box-sizing: border-box;
				position: relative;

				.num {
					position: absolute;
					bottom: 16rpx;
					right: 24rpx;
					font-size: 24rpx;
				}

				.txt {
					width: 100%;
					height: 100%;

				}
			}

			.btn {
				width: 220rpx;
				height: 60rpx;
				background: rgba(45, 85, 117, 1);
				opacity: 1;
				border-radius: 8rpx;
				color: #fff;
				font-size: 28rpx;
				line-height: 60rpx;
				text-align: center;
				margin: 0 auto;
				margin-top: 60rpx;
			}

			.close {
				width: 24rpx;
				height: 24rpx;
				position: absolute;
				top: 22rpx;
				right: 22rpx;

				image {
					width: 24rpx;
					height: 24rpx;
				}
			}
		}

	}

	.content {
		background: url("../../static/bg_img@2x(2).png") no-repeat;
		// background-color: rgba(249,249,249,1);
		// height: 100vh;
		background-size: 750rpx 292rpx;
		overflow: hidden;
		color: rgba(6, 18, 30, 1);
		padding-bottom: 80rpx;

		.msgCard {
			width: 686rpx;
			height: 438rpx;
			background: rgba(255, 255, 255, 1);
			box-shadow: 0rpx 4rpx 6rpx rgba(45, 85, 117, 0.1);
			opacity: 1;
			border-radius: 20rpx;
			margin: 0 auto;
			margin-top: 26rpx;
			text-align: center;
			position: relative;

			.photo {
				width: 136rpx;
				height: 136rpx;
				margin-top: 50rpx;
				border-radius: 50%;
			}

			.title {
				font-size: 32rpx;
				margin-top: 24rpx;
				margin-bottom: 24rpx;
			}

			.o,
			.t {
				color: rgba(140, 145, 152, 1);
				font-size: 22rpx;
				margin-bottom: 4rpx;
			}

			.mall {
				color: rgba(140, 145, 152, 1);
				font-size: 22rpx;
				margin-top: 14rpx;
			}

			.right {
				width: 132rpx;
				height: 54rpx;
				position: absolute;
				right: 0;
				top: 38rpx;

				image {
					width: 132rpx;
					height: 54rpx;
				}

				.text {
					color: #fff;
					font-size: 24rpx;
					width: 80rpx;
					text-align: center;
					position: absolute;
					top: 50%;
					left: 50%;
					transform: translate(-50%, -50%);
					display: flex;
					align-items: center;

					.icon {
						width: 28rpx;
						height: 28rpx;
					}
				}
			}
		}

		// .tabCard{
		//         height:80rpx;
		//         margin: 0 auto;
		//         background:rgba(255,255,255,1);
		//         margin-top: 22rpx;
		//         overflow-x: auto;
		//         width: 686rpx;
		//         box-sizing: border-box;
		//         // display: flex;
		//         // flex-wrap: wrap;
		//         white-space:nowrap;
		//         padding: 10rpx 0 0 32rpx;
		//         .item{
		//             margin-right: 40rpx;
		//             color:rgba(6,18,30,1);
		//             font-size: 28rpx;
		//             // width: 112rpx;
		//             display: inline-block;
		//             margin-right: 44rpx;
		//             // float: left;
		//             // height: 70rpx;
		//         }
		//         .active{
		//             color:rgba(5,132,157,1);
		//             background: url("../../static/xuanzhong_icon@2x.png") no-repeat;
		//             background-size: 60rpx 15rpx;
		//             background-position: 0 44rpx;
		//             height: 60rpx;

		//         }
		// }
		.tabCard {
			height: 70rpx;
			background: rgba(255, 255, 255, 1);
			margin-top: 22rpx;
			overflow-x: auto;
			display: flex;
			// flex-wrap: wrap;
			white-space: nowrap;
			padding: 10rpx 0 0 32rpx;

			.item {
				margin-right: 40rpx;
				color: rgba(6, 18, 30, 1);
				font-size: 28rpx;
				// width: 112rpx;
				display: inline-block;
				margin-right: 44rpx;
				// float: left;
				// height: 70rpx;
				display: flex;
				flex-direction: column;
				align-items: center;
				// justify-content: start;
			}

			.active {
				color: rgba(5, 132, 157, 1);



			}

			.selected {
				width: 60rpx;
				height: 15rpx;

				margin-top: 8rpx;
			}
		}

		.articl {
			background-color: #fff;
			margin: 0 auto;
			margin-top: 24rpx;
			padding: 0 34rpx 32rpx;
			box-sizing: border-box;
			box-shadow: 0rpx 4rpx 6rpx rgba(45, 85, 117, 0.1);
			border-radius: 20rpx;
			margin-bottom: 42rpx;
			width: 686rpx;
			box-sizing: border-box;
			height: auto !important;

			.nodata {
				font-size: 28rpx;
				text-align: center;
			}

			.articltitle {
				border-bottom: 1rpx solid rgba(238, 238, 238, 1);
				padding: 40rpx 0 26rpx 0;
				display: flex;
				font-size: 24rpx;

				.name {
					color: rgba(61, 68, 77, 1);
				}

				.tips {
					color: rgba(140, 145, 152, 1);
				}

				.photo {
					width: 68rpx;
					height: 68rpx;
					margin-right: 20rpx;
					border-radius: 50%;
				}
			}

			.articleBox {
				.title {
					color: rgba(6, 18, 30, 1);
					font-size: 28rpx;
					margin-top: 24rpx;
				}

				.txt {
					color: rgba(91, 94, 99, 1);
					font-size: 24rpx;
					margin-top: 8rpx;
					margin-bottom: 20rpx;
				}

				image {
					width: 128rpx;
					height: 128rpx;
					margin-right: 20rpx;
					margin-bottom: 20rpx;
				}

			}
		}

		.botmBox {
			width: 750rpx;
			height: 100rpx;
			position: fixed;
			bottom: 0;
			z-index: 888;
			display: flex;
			background-color: #fff;
			padding: 30rpx 0;
			box-sizing: border-box;

			.liuyan,
			.daxie {
				display: flex;
				width: 50%;
				padding: 0rpx 110rpx;
				box-sizing: border-box;
				font-size: 28rpx;
				height: 50rpx;
				border-right: 1rpx solid rgba(238, 238, 238, 1);
				align-items: center;

				image {
					width: 32rpx;
					height: 32rpx;
					margin-right: 8rpx;

				}
			}
		}


	}
</style>
