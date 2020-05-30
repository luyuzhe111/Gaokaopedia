<template>
	<view class="content">
		<view class="conet_top">
			<view class="top">
				<view class="search">
					<image class="icon" src="../../static/sousuo_icon@2x.png"></image>
					<input
						type="text"
						placeholder="请输入搜索内容"
						class="input"
						placeholder-style="color:rgba(189,196,206,1);font-size:28rpx;"
						style="font-size:28rpx;"
						v-model="word"
						 @confirm="getschoollistk"
					/> 
				</view>
				<view class="searchBtn" @click="getschoollistk">搜索</view>
			</view>
			<view class="type">
				<view class="paiming" :class="{ active: active1 }" @click="paimingShow">排名</view>
				<view class="diqu" :class="{ active: active2 }" @click="diquShow">地区</view>
				<view class="paimingPopup" v-if="isPaimingShow">
					<view class="title" @click="selectalllevel" :class="levelindex==-2?'selactive':''">全部</view>
					<view class="item" @click="paimingHide" :class="levelindex==index?'selactive':''" :data-id="item.id" :data-index="index" v-for="(item, index) in levelList" :key="index">{{ item.name }}</view>
					<!-- <view class="item" @click="paimingHide">{{item.name}}</view> -->
				</view>
				<view class="diquPopup" v-if="isDiquShow">
					<view class="title" @click="selectallprovince" :class="selpro==-1?'selactive':''" :data-index="-2">全部地区</view>
					<view class="cont">
						<view class="colum">
							<view class="item" :class="selpro==index?'selactive':''" @click="selectprovince" v-for="(item, index) in provinceList" :key="index" :data-index="index" :data-id="item.code">{{ item.name }}</view>
						</view>
						<view class="colum">
							<view class="item" :class="selcity==index?'selactive':''" @click="selectcity" v-for="(item, index) in cityList" :key="index" :data-index="index" :data-id="item.code">{{ item.name }}</view>
						</view>
					</view>
				</view>
			</view>
		</view>
		
		<view class="nodata" v-if="schoolList.length==0">暂无数据</view>

		<view class="contentBox" v-else>
			<view class="item" v-for="(item, index) in schoolList" :key="index" @click="godetail(item)">
				<view class="title">{{ item.name }}</view>
				<image class="icon" :src="item.icon_image"></image>
			</view>
		</view>
	</view>
</template>

<script>
import app from '../../App.vue';

export default {
	data() {
		return {
			selpro:-1,
			selcity:-1,
			levelindex:-1,
			isPaimingShow: false,
			isDiquShow: false,
			active1: false,
			// isDiquShow:false,
			active2: false,
			province_id: '',
			city_id: '',
			level_id: '',
			provinceList: [],
			cityList: [],
			schoolList: [],
			word: '',
			page: '1',
			levelList: []
		};
	},
	methods: {
		paimingShow(event) {
			this.isPaimingShow = true;
			this.isDiquShow = false;
			(this.active1 = true), (this.active2 = false);
		},
		paimingHide(e) {
			let id=e.currentTarget.dataset.id;
			let index=e.currentTarget.dataset.index;
			
			this.levelindex=index;
			this.city_id='';
			this.level_id=id;
			this.page=1;
			this.schoolList=[];
			this.getschoollist();
			this.isPaimingShow = false;
			(this.active1 = false), (this.active2 = false);
		},
		diquShow() {
			this.isDiquShow = true;
			this.isPaimingShow = false;
			(this.active2 = true), (this.active1 = false);
		},
		diquHide() {
			(this.isDiquShow = false), (this.active1 = false), (this.active2 = false);
		},
		//获取全部地区(省)
		getAllProvince() {
			var url = 'city/getAllProvince';
			var params = {};
			app.post(url, params, 'get')
				.then(res => {
					this.provinceList = res;
					console.log(res);
				})
				.catch(err => {
					console.log(err);
				});
		},

		//市
		getAllCity() {
			var url = 'city/getCity';
			var params = {
				province_id: this.province_id
			};
			app.post(url, params, 'get')
				.then(res => {
					console.log(res);
					this.cityList = res;
				})
				.catch(err => {
					console.log(err);
				});
		},
		//点击选中的省
		selectprovince(e) {
			this.province_id = e.currentTarget.dataset.id;
			this.selpro=e.currentTarget.dataset.index;
			this.selcity=-1;
			this.getAllCity();
		},
		//点击全部省
		selectallprovince() {
			this.selpro=-2;
			this.selcity=-1;
			this.city_id='';
			this.city_id='';
			this.level_id='';
			this.page=1;
			this.schoolList=[];
			this.getschoollist();
			
			this.isDiquShow = false;
		},
		getschoollistk(){
			this.page=1;
			this.schoolList=[];
			
			this.getschoollist();
		},
		//点击选中全部市
		selectallcity() {
			this.level_id='';
			this.page=1;
			this.schoolList=[];
			this.isDiquShow = false;
			this.getschoollist();
		},
		//点击选中的市
		selectcity(e) {
			this.city_id = e.currentTarget.dataset.id;
			this.selcity=e.currentTarget.dataset.index;
			this.level_id='';
			this.page=1;
			this.schoolList=[];
			this.getschoollist();
			this.isDiquShow = false;
		},
		// getAllSchool(){
		//   var url="city/getUniversityByCity"
		//   var params ={
		//     city_id:this.city_id
		//   }
		//   app.post(url,params,"get").then((res)=>{
		//     console.log(res)
		//     this.schoolList=res
		//   }).catch((err)=>{
		//     console.log(err)
		//   })
		// },
		// 获取大学列表
		getschoollist() {
			let that = this;
			var url = 'university/getUniversityList';
			var params = {
				page: that.page,
				size: 10,
				word: that.word,
				level_id: that.level_id,
				city_id: that.city_id
			};
			app.post(url, params,'post')
				.then(res => {
					console.log(res);
					that.schoolList =that.schoolList.concat(res) ;
				})
				.catch(err => {});
		},
		//获取全部排名
		getAllLevel() {
			let that = this;
			var url = 'university/getUniversityLevel';
			var params = {};
			app.post(url, params, 'get')
				.then(res => {
					that.levelList = res;
				})
				.catch(err => {
					console.log(err);
				});
		},
		//点击全部排名
		selectalllevel() {
			this.isPaimingShow = false;
			this.levelindex=-2;
			this.level_id='';
			this.province_id='';
			this.page=1;
			this.schoolList=[];
			this.getschoollist();
		},
		// 进入学校详情页
		godetail(item) {
			let id = item.id;
			uni.navigateTo({
				url: './schoolDetails?id=' + id
			});
		}
	},
	onLoad() {
		this.getAllProvince();
		this.getschoollist();
		this.getAllLevel();
	},
	onReachBottom() {
		let newpage=this.page;
		newpage++;
		this.page=newpage;
		this.getschoollist();
		
	}
};
</script>

<style lang="less">
page{
	height: 100%;
	width: 100%;
}
.content {
	background-color: rgba(249, 249, 249, 1);
	height: 100%;
	display: flex;
	flex-direction: column;
	.top {
		padding: 18rpx 32rpx;
		display: flex;
		background-color: #fff;
	}
	.search {
		display: flex;
		align-items: center;
		width: 596rpx;
		height: 64rpx;
		background: rgba(238, 238, 238, 1);
		opacity: 1;
		border-radius: 40rpx;
		padding: 0 28rpx;
		box-sizing: border-box;
		.icon {
			width: 30rpx;
			height: 30rpx;
		}
		.input {
			height: 64rpx;
			width: 500rpx;
			text-align: center;
		}
	}
	.searchBtn {
		color: rgba(6, 18, 30, 1);
		font-size: 28rpx;
		line-height: 64rpx;
		margin-left: 20rpx;
	}
	.type {
		display: flex;
		color: rgba(6, 18, 30, 1);
		font-size: 24rpx;
		background-color: #fff;
		padding: 24rpx 32rpx;
		margin-top: 2rpx;
		.paiming,
		.diqu {
			margin-right: 72rpx;
		}
		.paimingPopup,
		.diquPopup {
			width: 750rpx;
			height: 600rpx;
			position: absolute;
			top: 184rpx;
			left: 0;
			background-color: #fff;
			padding: 22rpx 32rpx;
			box-sizing: border-box;
			z-index: 999;
			box-shadow: 0px 4px 4px rgba(0, 0, 0, 0.06);
			border-top: 1rpx solid rgba(249, 249, 249, 1);
			.item {
				margin-bottom: 22rpx;
				// height: 74rpx;
				width: 170rpx;
			}
			.title {
				margin-bottom: 20rpx;
			}
			.cont {
				display: flex;
				// flex-direction: column;
				flex-wrap: wrap;
				justify-content: start;
				.colum {
					overflow: auto;
					height: 500rpx;
					width: 30%;
				}
			}
		}
		.active {
			color: #05849d;
		}
	}
	.contentBox {
		padding: 24rpx 32rpx;
		box-sizing: border-box;
		flex: 1;
		overflow-y: scroll;
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
				border-radius: 50%;
				margin-top: 20rpx;
			}
		}
	}
}
</style>
