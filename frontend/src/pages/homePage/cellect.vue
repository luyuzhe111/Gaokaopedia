<template>
	<view class="content">

		<view class="title">
			<view class="history" :class="{active:active1}" @click="historyShow">浏览历史</view>
			<view class="cellect" :class="{active:active2}" @click="cellectShow">我的收藏</view>
		</view>
		<view class="historyBox" v-if="isHistoryShow">

			<view class="nodata" v-if="historylist.length==0">暂无数据</view>
			<view v-else>
				<view class="liulan">
					<view class="liulantitle">浏览历史</view>
					<view class="del" @click="delhistory">清空</view>
					<!-- <view class="del">完成</view> -->
				</view>
				<view class="mateitem" v-for="(item,index) in historylist" :key="index" @click="toarticl" :data-id="item.id">
					<view class="contentBox">
						
						<image class="photo" style="border-radius: 50%;" :src="item.head_image"></image>
						<view class="cont">
							<view class="title">{{item.nickname}}</view>
							<view class="fen">{{item.title}}</view>
							<view class="txt">{{item.content}}</view>
						</view>
						<view class="right" :class="item.is_like_user==0?'like':''">
							<view class="text" @click.stop="attention(item,index)" v-if="item.is_like_user==0" style="display:flex;justify-content: center;align-items: center;color:#81C3BF;">
								<view class='xin' style="width:28rpx;height:28rpx;font-size: 0;">
									<image src="../../static/xin.png" mode=""></image>
								</view>
								关注
							</view>
							<view class="text" @click.stop="noattention(item,index)" v-else>已关注</view>
						</view>
				
					</view>
				</view>
				
			</view>
			

		</view>

		<!-- 我的收藏 -->
		<view class="cellectBox" v-else>
			
			<view class="nodata" v-if="cellectlist.length==0">暂无数据</view>
			
			<view v-else>
				<view class="liulan">
					<view class="liulantitle">我的收藏</view>
					<!-- <view class="del" @click="change2" v-if="isdel">完成</view>
					<view class="del" @click="change" v-else>编辑</view> -->
				</view>
				<view class="mateitem" v-for="(item,index) in cellectlist" :key="index" @click="toarticl" :data-id="item.id">
					<image class="delBtn" src="../../static/shanchu_icon@2x.png" v-if="isdel"  @click.stop="delarcial(item,index)"></image>
					<view class="contentBox" :style="{width:width}">
						
						<image class="photo" :src="item.head_image" style="border-radius: 50%;"></image>
						<view class="cont">
							<view class="title">{{item.nickname}}</view>
							<view class="fen">{{item.title}}</view>
							<view class="txt">{{item.content}}</view>
						</view>
						<view class="right" :class="item.is_like_user==0?'like':''">
							<view class="text" @click.stop="attention(item,index)" v-if="item.is_like_user==0" style="display:flex;justify-content: center;align-items: center;color:#81C3BF;">
								<view class='xin' style="width:28rpx;height:28rpx;font-size: 0;">
									<image src="../../static/xin.png" mode=""></image>
								</view>
								关注
							</view>
							<view class="text" @click.stop="noattention(item,index)" v-else>已关注</view>
						</view>
				
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
				active1: true,
				active2: false,
				isHistoryShow: true,
				isdel: false,
				width: "660rpx",
				historypage: 1,
				collectpage: 1,
				page: 1,
				size: 10,
				cellectlist: [],
				historylist: []
			}
		},
		methods: {
			toarticl(e) {
				var id = e.currentTarget.dataset.id
				uni.navigateTo({
					url: '../school/article?article_id=' + id
				})
			},
			// 
			delarcial(item, index) {
					var that = this
					uni.showModal({
						title: '提示',
						content: '是否删除该收藏',
						success: function(res) {
							if (res.confirm) {
								var url = "article/delArticle"
								var params = {
									token: uni.getStorageSync('token'),
									article_id: item.id
								}
								app.post(url, params, "post").then((res) => {
									console.log(res)
									that.cellectlist.splice(index, 1);
									
									uni.showToast({
										title: '删除成功',
										icon: 'none'
									})
								}).catch((err) => {
									console.log(err)
									uni.showToast({
										title:err.msg,
										icon:'none'
									})
								})
							} else if (res.cancel) {
								console.log('用户点击取消');
							}
						}
					});
			
				},
			
			// 关注和取消关注
			attention(item, index) {
				var that = this
				var url = "article/likeArticle"
				var token = uni.getStorageSync('token')
				var params = {
					article_id: item.id
				}
				app.post(url, params, "get").then((res) => {
					uni.showToast({
						title: "关注成功",
						icon: 'none'
					})
					if (that.active1 == true) {
						that.historylist[index].is_like_user = 1
					} else if (that.active2 == true) {
						that.cellectlist[index].is_like_user = 1

					}


				}).catch((err) => {
					console.log(err)
				})
			},
			noattention(item, index) {
				var that = this
				var url = "article/likeArticle"
				var token = uni.getStorageSync('token')
				var params = {
					article_id: item.id
				}
				app.post(url, params, "get").then((res) => {
					uni.showToast({
						title: "取消关注成功",
						icon: 'none'
					})
					if (that.active1 == true) {
						that.historylist[index].is_like_user = 0
					} else if (that.active2 == true) {
						that.cellectlist[index].is_like_user = 0

					}


				}).catch((err) => {
					console.log(err)
				})

			},
			historyShow() {
				this.active1 = true;
				this.active2 = false;
				this.isHistoryShow = true;
				this.historypage = 1;
				this.historylist = [];
				this.gethistory()
			},
			cellectShow() {
				this.isHistoryShow = false;
				this.active2 = true;
				this.active1 = false;
				this.collectpage = 1;
				this.cellectlist = [];
				this.getMyCellect()
			},
			change() {
				this.isdel = true
				this.width = "620rpx"

			},
			change2() {
				this.isdel = false
				this.width = "660rpx"
			},
			getMyCellect() {
				var that = this
				var url = "article/getMyLikeArticle"
				var token = uni.getStorageSync('token')
				var params = {
					token: token,
					page: that.collectpage,
					size: that.size
				}
				app.post(url, params, "get").then((res) => {
					console.log(res)
					that.cellectlist = that.cellectlist.concat(res)
				}).catch((err) => {
					console.log(err)
				})
			},
			gethistory() {
				var that = this
				var url = "article/getLookArticleList"
				var token = uni.getStorageSync('token')
				var params = {
					token: token,
					page: that.historypage,
					size: that.size
				}
				app.post(url, params, "get").then((res) => {
					console.log(res)
					that.historylist = that.historylist.concat(res)
				}).catch((err) => {
					console.log(err)
				})
			},
			delhistory() {
				var that = this;
				uni.showModal({
					title: '提示',
					content: '是否清空浏览记录',
					success: function(res) {
						if (res.confirm) {
							var url = "article/delLookArticle"
							var token = uni.getStorageSync('token')
							var params = {
								token: token
							}
							app.post(url, params, "post").then((res) => {
								that.historylist = res
								console.log(res)
								uni.showToast({
									title: '浏览记录已清空',
									icon:"none"
								})
								this.historypage=1;
								this.historylist=[];
								this.gethistory()
								
							}).catch((err) => {
								console.log(err)
							})
						} else if (res.cancel) {
							console.log('用户点击取消');
						}
					}
				});
			
			},
			
			
		},
		onLoad() {
			this.gethistory()
		},
		onReachBottom() {
			if (this.active1 == true) {
				let newpage = this.historypage;
				newpage++;
				this.historypage = newpage
				this.gethistory();
			} else if (this.active2 == true) {
				let newpage = this.collectpage;
				this.collectpage = newpage;
				newpage++;
				this.getMyCellect();
			}

		}

	}
</script>

<style lang="less">
	.content {

		// background-color: #F9F9F9;
		.nodata {
			font-size: 28rpx;
			text-align: center;
		}

		.title {
			background-color: #fff;
			display: flex;

			.history,
			.cellect {
				width: 50%;
				text-align: center;
				font-size: 28rpx;
				padding: 24rpx 0;
			}

			.active {
				background: url("../../static/xuanzhong_icon@2x.png") no-repeat;
				background-size: 60rpx 12rpx;
				background-position: 156rpx 70rpx;
				color: rgba(5, 132, 157, 1);
			}
		}

		.liulan {
			display: flex;
			font-size: 32rpx;
			width: 686rpx;
			margin: 0 auto;
			justify-content: space-between;
			padding: 30rpx 0;

			.liulantitle {
				color: rgba(45, 85, 117, 1);
			}

			.del {
				font-size: 24rpx;
			}
		}

		.mateitem {
			display: flex;
			align-items: center;
			width: 686rpx;
			// height:172rpx;
			background: rgba(255, 255, 255, 1);
			opacity: 1;
			border-radius: 20rpx;
			margin: 0 auto;
			background-color: #fff;
			padding: 24rpx 0;
			// box-sizing: border-box;
			margin-bottom: 24rpx;

			.delBtn {
				width: 28rpx;
				height: 28rpx;
				// position: absolute;
				// top: 50%;
				// transform: translateY(-50%);
				// left: 28rpx;
				margin-left: 26rpx;
			}

			.contentBox {
				display: flex;
				// align-items: center;
				position: relative;
				flex-wrap: wrap;
				margin-left: 26rpx;
				width: 660rpx;

				.photo {
					width: 68rpx;
					height: 68rpx;
					margin-right: 26rpx;
					margin-top: 6rpx;
				}

				.cont {
					.title {
						color: rgba(61, 68, 77, 1);
						font-size: 24rpx;
					}

					.fen {
						font-size: 24rpx;
						color: rgba(140, 145, 152, 1);
						margin-top: 4rpx;
					}
				}

				.right {
					width: 118rpx;
					height: 44rpx;
					border: 2rpx solid rgba(189, 196, 206, 1);
					opacity: 1;
					border-radius: 40rpx;
					position: absolute;
					right: 26rpx;
					top: 32rpx;

					.text {
						color: rgba(189, 196, 206, 1);
						text-align: center;
						line-height: 44rpx;
						font-size: 24rpx;
					}
				}

				.txt {
					// width: 600rpx;
					font-size: 28rpx;
					margin-top: 24rpx;
				}
			}


		}
	}
</style>
