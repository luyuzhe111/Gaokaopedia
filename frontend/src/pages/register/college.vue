<template>
	<view class="content">
		<view class="studentMsg">
			<view class="item">
				<view class="title">姓名</view>
				<input type="text" v-model="username" class="inpt" placeholder="请输入姓名" placeholder-style="color:rgba(189,196,206,1);font-size:28rpx;" />
			</view>
			<view class="item">
				<view class="title">邮箱</view>
				<input type="text" v-model="email" class="inpt" placeholder="请输入邮箱" placeholder-style="color:rgba(189,196,206,1);font-size:28rpx;" />
			</view>
			<view class="item" style="border-bottom:none">
				<view class="title">头像</view>
				<image @click="upload" class="photo" :src="isUpload ? uploadImg : '../../static/touxiang_img@2x.png'"></image>
			</view>
		</view>
		<view class="schoolTitle">高中信息</view>
		<view class="studentMsg">
			<view class="item" @click="selhigh">
				<view class="title">高中</view>
				<view class="selectBtn">
					{{ isHighShow ? name : '请选择就读高中' }}
					<image class="icon" src="../../static/zhuandao_icon@2x.png"></image>
				</view>
			</view>
			<view class="item">
				<view class="title">毕业年份</view>
				<!-- <view class="selectBtn">请选择毕业年份<image class="icon" src="../../static/zhuandao_icon@2x.png"></image></view> -->
				<view @click="selDate" style="display: flex;justify-content: space-between;align-items: center;">
					<picker mode="date" :value="date" start="2000-01-01" @change="bindTimeChange">
						<view class="selectBtn">{{ isDateShow ? date : '请选择毕业年份' }}</view>
					</picker>
					<image class="icon" src="../../static/zhuandao_icon@2x.png"></image>
				</view>
			</view>
			<view class="item" @click="subPopupShow">
				<view class="title">选考科目</view>
				<view class="selectBtn">
					{{ subid.toString() != '' ? subname.toString() : '请选择选考科目' }}
					<image class="icon" src="../../static/zhuandao_icon@2x.png"></image>
				</view>
			</view>
			<view class="item" style="border-bottom:none" @click="stylePopupShow">
				<view class="title">升学方式</view>
				<view class="selectBtn">
					{{ isupnameshow ? upname : '请选择升学方式' }}
					<image class="icon" src="../../static/zhuandao_icon@2x.png"></image>
				</view>
			</view>
		</view>
		<view class="schoolTitle">大学信息</view>
		<view class="studentMsg">
			<view class="item" @click="selcollege">
				<view class="title">就读大学</view>
				<view class="selectBtn">
					{{ isNameShow ? collegename : '请选择就读大学' }}
					<image class="icon" src="../../static/zhuandao_icon@2x.png"></image>
				</view>
			</view>
			<view class="item">
				<view class="title">所属学院</view>
				<view class="selectBtn" @click="tocollege">
					{{ iscollgegshow ? academyname : '请选择所属学院' }}
					<image class="icon" src="../../static/zhuandao_icon@2x.png"></image>
				</view>
			</view>
			<view class="item" style="border-bottom:none" @click="tomajor">
				<view class="title">就读专业</view>
				<view class="selectBtn">
					{{ ismajorshow ? majorname : '请选择所属专业' }}
					<image class="icon" src="../../static/zhuandao_icon@2x.png"></image>
				</view>
			</view>
		</view>
		<view class="okBtn" @click="collegeregister">注册</view>
		<view class="subPopup" v-if="isSubPopupShow">
			<view class="selectCard">
				<view class="buttonBox">
					<view class="closeBtn" @click="subPopupHide">取消</view>
					<view class="confimBtn" @click="subPopupHide">确定</view>
				</view>
				<view class="subBox">
					<view
						class="subject"
						@click="selsub"
						:class="{ active: subid.indexOf(item.id) != -1 }"
						v-for="(item, index) in sublist"
						:key="index"
						:data-id="item.id"
						:data-name="item.name"
					>
						{{ item.name }}
					</view>
				</view>
			</view>
		</view>
		<view class="stylePopup" v-if="isStylePopupShow">
			<view class="styleCard">
				<view class="buttonBox">
					<view class="closeBtn" @click="stylePopupHide">取消</view>
					<view class="confimBtn" @click="stylePopupHide">确定</view>
				</view>
				<view class="subBox">
					<view
						class="subject"
						:class="{ active: upid == item.id }"
						@click="selup"
						v-for="(item, index) in uplist"
						:key="index"
						:data-id="item.id"
						:data-name="item.name"
					>
						{{ item.name }}
					</view>
				</view>
			</view>
		</view>
	</view>
</template>

<script>
import app from '../../App.vue';

export default {
	data() {
		return {
			isSubPopupShow: false,
			isStylePopupShow: false,
			username: '',
			image: '',
			isUpload: false,
			date: '请选择毕业年份',
			isDateShow: false,
			uplist: [],
			upid: '',
			subid: [],
			sublist: [],
			collegename: '请选择就读大学',
			isNameShow: false,
			isHighShow: false,
			name: '请选择就读高中',
			subname: [],
			issubnameshow: false,
			upname: '请选择升学方式',
			isupnameshow: false,
			school_id: '',
			uni_id: '',
			academyname: '请选择所属学院',
			iscollgegshow: false,
			majorname: '请选择所属专业',
			ismajorshow: false,
			email: '',
			majorid: '',
			academyid: '',
			chuanimg: '',
			baseUrl: app.globalData.imageBaseUrl,
			uploadImg: ''
		};
	},
	methods: {
		selDate() {
			this.isDateShow = true;
		},
		subPopupShow() {
			this.isSubPopupShow = true;
		},
		subPopupHide() {
			this.isSubPopupShow = false;
		},
		stylePopupShow() {
			this.isStylePopupShow = true;
		},
		stylePopupHide() {
			this.isStylePopupShow = false;
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
					app.upload('image', res.tempFilePaths[0], 'post')
						.then(res => {
							that.image = res.url;
							that.chuanimg = res.kurl;
							that.uploadImg = that.baseUrl + res.kurl;
							that.isUpload = true;
						})
						.catch(err => {
							console.log(err);
						});
				},
				error: function(e) {
					console.log(e);
				}
			});
		},
		bindTimeChange: function(e) {
			this.date = e.target.value;
		},
		getUpType() {
			var that = this;
			var url = 'university/getAllUp';
			var params = {};
			app.post(url, params, 'get')
				.then(res => {
					console.log(res);
					that.uplist = res;
				})
				.catch(err => {
					console.log(err);
				});
		},
		getAllSub() {
			var that = this;
			var url = 'university/listSubject';
			var params = {};
			app.post(url, params, 'get')
				.then(res => {
					that.sublist = res;
					console.log(res);
				})
				.catch(err => {
					console.log(err);
				});
		},
		selup(e) {
			this.isupnameshow = true;
			console.log(e.currentTarget.dataset);
			this.upid = e.currentTarget.dataset.id;
			this.upname = e.currentTarget.dataset.name;
		},
		selsub(e) {
			Array.prototype.remove = function(val) {
				var index = this.indexOf(val);
				if (index > -1) {
					this.splice(index, 1);
				}
			};
			this.issubnameshow = true;
			if (this.subid.indexOf(e.currentTarget.dataset.id) != -1) {
				this.subid.remove(e.currentTarget.dataset.id);
				this.subname.remove(e.currentTarget.dataset.name);
			} else {
				this.subid.push(e.currentTarget.dataset.id);
				this.subname.push(e.currentTarget.dataset.name);
			}
			console.log(this.subid);
		},
		selcollege() {
			this.isNameShow = true;
			uni.navigateTo({
				url: './selectCollege'
			});
		},
		selhigh() {
			this.isHighShow = true;
			uni.navigateTo({
				url: './selectSchool'
			});
		},
		tocollege() {
			this.iscollgegshow = true;
			var school_id = this.uni_id;
			console.log(school_id);
			uni.navigateTo({
				url: './selectAcademy?uni_id=' + school_id
			});
		},
		tomajor() {
			this.ismajorshow = true;

			uni.navigateTo({
				url: './selectMajor'
			});
		},
		collegeregister() {
			var that = this;
			if (that.username == '') {
				uni.showToast({
					title: '姓名不能为空',
					icon: 'none'
				});
				return false;
			}
			var reg = /^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/;
			if (!reg.test(that.email)) {
				uni.showToast({
					title: '请输入正确邮箱',
					icon: 'none'
				});
				return false;
			}
			if (that.image == '') {
				uni.showToast({
					title: '请选择头像',
					icon: 'none'
				});
				return false;
			}
			if (that.name == '请选择就读高中') {
				uni.showToast({
					title: '请选择高中',
					icon: 'none'
				});
				return false;
			}
			if (that.date == '请选择毕业年份') {
				uni.showToast({
					title: '请选择毕业年份',
					icon: 'none'
				});
				return false;
			}
			if (that.subname == '请选择选考科目') {
				uni.showToast({
					title: '请选择选考科目',
					icon: 'none'
				});
				return false;
			}
			if (that.upname == '请选择升学方式') {
				uni.showToast({
					title: '请选择升学方式',
					icon: 'none'
				});
				return false;
			}
			if (that.collegename == '请选择就读大学') {
				uni.showToast({
					title: '请选择就读大学',
					icon: 'none'
				});
				return false;
			}
			if (that.academyname == '请选择所属学院') {
				uni.showToast({
					title: '请选择所属学院',
					icon: 'none'
				});
				return false;
			}
			if (that.majorname == '请选择所属专业') {
				uni.showToast({
					title: '请选择所属专业',
					icon: 'none'
				});
				return false;
			}
			var url = 'register/register';
			var token = uni.getStorageSync('token');
			var params = {
				token: token,
				level: 2,
				nickname: that.username,
				head_image: that.chuanimg,
				school_id: that.school_id,
				email: that.email,
				endtime: that.date,
				subject_ids: that.subid.toString(),
				up_id: that.upid,
				university_id: that.uni_id,
				graduated_id: that.majorid,
				college_id: that.academyid
			};
			app.post(url, params, 'post')
				.then(res => {
					console.log(res);
					// console.log(res)
					uni.showToast({
						title: '注册成功',
						icon: 'none'
					});
					uni.setStorageSync("chosetype",2)
					setTimeout(() => {
						uni.switchTab({
							url: '/pages/schoolmate/schoolmate?type=' + 2
						});
					}, 1500);
				})
				.catch(err => {
					console.log(err);
					if (err.code == 0) {
						uni.showToast({
							title: err.msg,
							icon: 'none'
						});
					}
				});
		}
	},
	onLoad() {
		this.getUpType();
		this.getAllSub();
	}
};
</script>

<style lang="less">
.content {
	padding: 32rpx;
	background: rgba(249, 249, 249, 1);
	font-size: 28rpx;
	.studentMsg {
		background-color: #fff;
		padding: 0rpx 32rpx;
		border-radius: 20rpx;
		.item {
			height: 114rpx;
			display: flex;
			justify-content: space-between;
			align-items: center;
			border-bottom: 1rpx solid rgba(238, 238, 238, 1);
			font-size: 28rpx;
			.title {
				width:172rpx;
				// line-height: 114px;
				padding: 30rpx 0;
				// box-sizing: border-box;
			}
			.selectBtn {
				color: rgba(189, 196, 206, 1);
				line-height: 114rpx;
			}
			.icon {
				width: 13rpx;
				height: 24rpx;
				margin-left: 8rpx;
			}
			.inpt {
				width:100%;
				height: 114rpx;
				text-align: right;
				// width: 250rpx;
			}
			.photo {
				width: 64rpx;
				height: 64rpx;
				margin-top: 20rpx;
				border-radius: 50%;
			}
		}
	}
	.schoolTitle {
		font-size: 32rpx;
		color: rgba(45, 85, 117, 1);
		margin-top: 44rpx;
		margin-bottom: 12rpx;
	}
	.okBtn {
		width: 686rpx;
		height: 80rpx;
		background: rgba(45, 85, 117, 1);
		opacity: 1;
		border-radius: 8rpx;
		font-size: 28rpx;
		color: #fff;
		line-height: 80rpx;
		text-align: center;
		margin-top: 56rpx;
	}
	.subPopup {
		width: 100%;
		height: 100%;
		background-color: rgba(0, 0, 0, 0.5);
		position: fixed;
		top: 0;
		left: 0;
		right: 0;
		z-index: 999;
		.selectCard {
			width: 750rpx;
			height: 426rpx;
			background: rgba(255, 255, 255, 1);
			// opacity:1;
			border-radius: 20px 20px 0px 0px;
			position: absolute;
			bottom: 0;
			.buttonBox {
				display: flex;
				justify-content: space-between;
				padding: 28rpx 30rpx;
				font-size: 28rpx;
				.closeBtn {
					color: rgba(140, 145, 152, 1);
				}
				.confimBtn {
					color: rgba(45, 85, 117, 1);
				}
			}
			.subBox {
				display: flex;
				flex-wrap: wrap;
				justify-content: space-between;
				padding: 16rpx 32rpx;
				.active {
					background: rgba(129, 195, 191, 1);
					border: 2rpx solid rgba(129, 195, 191, 1);
					color: #fff;
				}
			}
			.subject {
				width: 28%;
				height: 56rpx;
				background: #fff;
				border: 1rpx solid rgba(6, 18, 30, 1);
				opacity: 1;
				border-radius: 40rpx;
				text-align: center;
				line-height: 56rpx;
				color: rgba(6, 18, 30, 1);
				font-size: 28rpx;
				margin-bottom: 36rpx;
			}
		}
	}
	.stylePopup {
		width: 100%;
		height: 100%;
		background-color: rgba(0, 0, 0, 0.5);
		position: fixed;
		top: 0;
		left: 0;
		right: 0;
		z-index: 999;
		.styleCard {
			width: 750rpx;
			height: 228rpx;
			background: rgba(255, 255, 255, 1);
			// opacity:1;
			border-radius: 20px 20px 0px 0px;
			position: absolute;
			bottom: 0;
			.buttonBox {
				display: flex;
				justify-content: space-between;
				padding: 28rpx 30rpx;
				font-size: 28rpx;
				.closeBtn {
					color: rgba(140, 145, 152, 1);
				}
				.confimBtn {
					color: rgba(45, 85, 117, 1);
				}
			}
			.subBox {
				display: flex;
				flex-wrap: wrap;
				justify-content: space-around;
				padding: 16rpx 32rpx;
				.active {
					background: rgba(129, 195, 191, 1);
					border: 2rpx solid rgba(129, 195, 191, 1);
					color: #fff;
				}
			}
			.subject {
				width: 28%;
				height: 56rpx;
				background: #fff;
				border: 1rpx solid rgba(6, 18, 30, 1);
				opacity: 1;
				border-radius: 40rpx;
				text-align: center;
				line-height: 56rpx;
				color: rgba(6, 18, 30, 1);
				font-size: 28rpx;
				margin-bottom: 36rpx;
			}
		}
	}
}
</style>
