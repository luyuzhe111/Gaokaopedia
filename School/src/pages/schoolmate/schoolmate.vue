<template>
	<view class="content" @click="hidekk">
		<view @click.stop="showhide">
			<view class="top">
				<view class="search">
					<image class="icon" src="../../static/sousuo_icon@2x.png"></image>
					<input type="text" v-model="word" placeholder="请输入搜索内容" class="input" placeholder-style="color:rgba(189,196,206,1);font-size:28rpx;"
					 style="font-size:28rpx;" @confirm="getmatelist">
				</view>
				<view class="searchBtn" @click="getmatelist">搜索</view>
			</view>
			<view class="type">
				<view class="typeitem" @click="diquShow" :class="{'active':active1}">地区</view>
				<!-- <view class="typeitem" @click="schoolShow" :class="{'active':active2}">学校</view> -->
				<view class="typeitem" @click="majorShow" :class="{'active':active3}">专业</view>
				<view class="typeitem" @click="styleShow" :class="{'active':active4}">升学方式</view>
				<view class="diquPopup" v-if="isDiquShow">

					<view class="cont">
						<view class="colum" style="overflow: auto;height: 600rpx;">
							<view class="title" @click="selectallprovince" :class="selpro==-2?'selactive':''" :data-index="-2">全部</view>
							<view class="item" :class="selpro==index?'selactive':''" @click="selectprovince" v-for="(item,index) in provinceList" :key="index" :data-index="index" :data-id="item.code">{{item.name}}</view>

						</view>
						<view class="colum" style="overflow: auto;height: 600rpx;">
							<!-- <view class="title" @click="selectallcity" :data-id="null">这个省的市</view> -->
							<view class="item" @click="selectcity" :class="selty==index?'selactive':''" v-for="(item,index) in cityList" :key="index" :data-index="index" :data-id="item.code">{{item.name}}</view>

						</view>
						<view class="colum" style="overflow: auto;height: 600rpx;">
							<!-- <view class="title" @click="selectschool" :data-id="null">全部学校</view> -->
							<view class="item" @click="selectschool" :class="selstuent==index?'selactive':''" v-for="(item,index) in schoolList" :key="index" :data-id="item.id" :data-index="index">{{item.name}}</view>
						</view>
					</view>
				</view>
				<!-- <view class="schoolPopup" v-if="isSchoolShow">
				        <view class="cont">
				        <view class="title" @click="selectschool" :data-id="null">全部</view>
				          
				            <view class="item" style="width:330rpx" @click="selectschool" v-for="(item,index) in schoolList" :key="index" :data-id="item.id">{{item.name}}</view>
				         
				         
				        </view>
				      </view> -->
				<view class="majorPopup" v-if="isMajorShow">
					<view class="cont" style="display:flex;flex-direction: column;overflow: auto;height: 600rpx;">
						<!-- <view class="title" @click="selectgraduated" :data-id="null">全部</view> -->
						<view class="item" :class="major==index?'selactive':''" :data-index="index" style="width:330rpx" @click="selectgraduated" v-for="(item,index) in graduatedList" :key="index"
						 :data-id="item.id">{{item.name}}</view>
					</view>
				</view>

				<view class="stylePopup" v-if="isStyleShow">
					<view class="cont" style="display:flex;flex-direction: column;overflow: auto;height: 600rpx;">
						<!-- <view class="title" @click="selectup" :data-id="null">全部</view> -->
						<view class="item" :class="upindex==index?'selactive':''" style="width:230rpx" @click="selectup" :data-index="index" v-for="(item,index) in upList" :key="index" :data-id="item.id">{{item.name}}</view>

					</view>
				</view>
			</view>
			<view class="mateitem" v-for="(item,index) in schoolmateList" :key="index" @click="todetile" :data-id="item">
				<image class="photo" :src="item.head_image"></image>
				<view class="cont">
					<view class="title">{{item.nickname}}</view>
					<view class="academy">
						<image class="icon" src="../../static/xuexiao_icon@2x2.png"></image>{{ item.university_name }} &nbsp;&nbsp;&nbsp;
						{{ item.college_name }}&nbsp;&nbsp;&nbsp; {{ item.graduated_name }}
					</view>
					<view class="fen">{{item.up_name}}</view>
				</view>
				<view class="right" v-if="item.is_like==1" @click.stop=like(index,item.user_id,2)>
					<image src="../../static/yiguanzhu_img@2x.png"></image>
					<view class="text">已关注</view>
				</view>
				<view class="right" v-else style="width:132rpx;height:54rpx;background:rgba(129,195,191,1);border-radius:32rpx 0rpx 0rpx 32rpx;">
					<!-- <image src="../../static/yiguanzhu_img@2x.png"></image> -->
					<view class="text" style="align-items: center;" @click.stop=like(index,item.user_id,1)>
						<image class="icon" src="../../static/guanzhu_icon@2x.png"></image>关注
					</view>
				</view>
			</view>
			<view class="nodata" v-if="schoolmateList.length==0">暂无数据</view>

			<!-- <view class="box">
				        <view class="text"><text @click="tobuy">购买畅读卡</text>可以额外找到888人</view>
				    </view> -->
			<!-- <tab :current="currentTabIndex" @getData="tabClick"></tab> -->
			
			<tabBar :current="currentTabIndex" backgroundColor="#fbfbfb" color="#999" tintColor="#42b983"></tabBar>
			<!-- <view class="teacherfooter" v-if="type == 1">

				<view class="teacherfootitem" @click="jumpschool==true?footerselChange(1,'/pages/school/school?type=1'):''" :data-id="1" :data-url="'/pages/school/school?type='+1">
					<view class="teacherfootitemtop" >
						<image :src="footersel==1?'/static/xuexiao_icon@2x.png':'/static/xuexiao_icon@2x3.png'"></image>
					</view>
					<view class="teacherfootname" :class="footersel==1?'selactive':''">学校</view>
				</view>
				<view class="teacherfootitem"  :data-id="2" :data-url="'/pages/schoolmate/schoolmate?type='+1">
					<view class="teacherfootitemtop">
						<image :src="footersel==2?'/static/zhaoxiaoyou_icon@2x2.png':'/static/zhaoxiaoyou_icon@2x.png'"></image>
					</view>
					<view class="teacherfootname" :class="footersel==2?'selactive':''">找校友</view>
				</view>
				<view class="teacherfootitem" @click="jumpschool==true?footerselChange(3,'/pages/homePage/homePage?type=1'):''" :data-id="3" :data-url="'/pages/homePage/homePage?type='+1">
					<view class="teacherfootitemtop">
						<image :src="footersel==3?'/static/wodezhuye_icon@2x2.png':'/static/wodezhuye_icon@2x.png'"></image>
					</view>
					<view class="teacherfootname" :class="footersel==3?'selactive':''">我的主页</view>
				</view>
			</view>
			<view class="collegefooter" v-if="type == 2">
				<view class="teacherfootitem" >
					<view class="teacherfootitemtop">
						<image :src="footersel==2?'/static/zhaoxiaoyou_icon@2x2.png':'/static/zhaoxiaoyou_icon@2x.png'"></image>
					</view>
					<view class="teacherfootname" :class="footersel==2?'selactive':''">找校友</view>
				</view>
				
				<view class="teacherfootitem" @click="footerselChangek" :data-id="3">
					<view class="teacherfootitemtop">
						<image :src="footersel==3?'/static/wodezhuye_icon@2x2.png':'/static/wodezhuye_icon@2x.png'"></image>
					</view>
					<view class="teacherfootname" :class="footersel==3?'selactive':''">我的主页</view>
				</view>
			</view> -->
		
		
		</view>
	</view>
</template>
<script>
	import app from "../../App.vue";
	import tabBar from '../../components/tabvue/tabvue.vue'
	let chosetype=uni.getStorageSync("chosetype");
	export default {
		components: {
			tabBar,
		},
		data() {
			return {
				selpro:-1,
				selty:-1,
				selstuent:-1,
				major:-1,
				upindex:-1,
				isDiquShow: false,
				// isSchoolShow:false,
				isMajorShow: false,
				isStyleShow: false,
				active1: false,
				// active2:false,
				active3: false,
				active4: false,
				currentTabIndex: chosetype==1?1:0,
				footersel: 2,
				type: "",
				province_id: "",
				city_id: '',
				schoolmate: {
					page: '1',
					size: '10',
					type: '1',
					university_id: '',
					graduated_id: '',
					up_id: '',

				},
				school: {
					page: '1',
					size: '10',
					word: ''
				},
				graduatedList: [],
				upList: [],
				schoolmateList: [],
				provinceList: [],
				cityList: [],
				schoolList: [],
				word: "",
				vip_level:'',
				jumpschool:true,
				jumpschoolk:true
			}
		},
		methods: {
			hidekk() {
				this.isDiquShow = false
			},
			showhide() {

			},
			// 关注和取消关注
			like(index,userid,type) {
				var that = this
				var url = "student/likeStudent"
				var token = uni.getStorageSync('token')
				var params = {
					token: token,
					userb_id: userid
				}
				app.post(url, params, "post").then((res) => {
					if(type==1){
						uni.showToast({
							title:"关注成功",
							icon:"none"
						})
						that.schoolmateList[index].is_like=1;
						that.schoolmateList=that.schoolmateList
					}else{
						uni.showToast({
							title:"取消关注成功",
							icon:"none"
						})
						that.schoolmateList[index].is_like=2;
						that.schoolmateList=that.schoolmateList
					}
				}).catch((err) => {
					console.log(err)
				})
			},
			tobuy() {
				if (uni.getStorageSync('token') == '') {
					uni.navigateTo({
						url: '../register/register?istoken=' + 1
					})
				} else {
					uni.navigateTo({
						url: '../homePage/buyCard'
					})
				}

			},
			diquShow() {
				this.isDiquShow = !this.isDiquShow;
				this.isSchoolShow = false;
				this.isMajorShow = false
				this.isStyleShow = false
				this.active1 = true,
				this.active2 = false
				this.active3 = false
				this.active4 = false
			},
			hide() {
				this.isDiquShow = false,
				this.isSchoolShow = false;
				this.isMajorShow = false
				this.isStyleShow = false,
				this.active1 = false,
				this.active2 = false
				this.active3 = false
				this.active4 = false
			},
			// schoolShow(){
			//   this.isSchoolShow=true;
			//   this.isDiquShow=false,
			//     this.isMajorShow=false
			//     this.isStyleShow=false
			//   this.active1=false,
			//   this.active2=true
			//   this.active3=false
			//   this.active4=false
			// },

			majorShow() {
				this.isMajorShow = !this.isMajorShow;
				this.isDiquShow = false,
					this.isSchoolShow = false;
				this.isStyleShow = false
				this.active1 = false,
					this.active2 = false
				this.active3 = true
				this.active4 = false
			},

			styleShow() {
				console.log(37844374378)
				this.isStyleShow = !this.isStyleShow;
				this.isMajorShow = false;
				this.isSchoolShow = false;
				this.isDiquShow = false;
				this.active1 = false;
				this.active2 = false;
				this.active3 = false;
				this.active4 = true;
				// this.this.schoolmate.page=1;
				// this.schoolmateList=[]

			}, 

			// 底部导航跳转
			footerselChange(id,url) {
				// let url = e.currentTarget.dataset.url;
				// console.log(e)
				uni.redirectTo({
					url: url
				})
				this.jumpschool=false
			},
			footerselChangek(e){
				
				// let url = e.currentTarget.dataset.url;
				
				// console.log(e)
				uni.navigateTo({
					url: '/pages/homePage/collegeHome?type=2'
				})
				this.jumpschoolk=false
			},
			// 获取个人信息
			getpersoninfo(){
				let that = this;
				var url='student/getMyInfo';
				var params={
				   
				}
				app.post(url,params,"get").then((res)=>{
				    console.log(res)
				    this.vip_level=res.vip_level;
					console.log('3478783473474',that.vip_level)
					that.getmatelist()
				}).catch((err)=>{
				    console.log(err)
				})
			},
			//获取学长列表
			getmatelist() {
				if (uni.getStorageSync('token') == '') {
					uni.showToast({
						title: "请登陆后操作",
						icon: "none"
					})
					setTimeout(() => {
						uni.navigateTo({
							url: '../register/register?istoken=' + 1
						})
					}, 1500);

				} else {
					// this.schoolmate.page=1;
					// this.schoolmateList=[];
					var url = "student/getStudentList"
					var token = uni.getStorageSync('token')
					let type='';
					console.log('3443743873478',this.vip_level)
					if(this.vip_level==0){
						type=1
					}else if(this.vip_level==1){
						type=2
					}else if(this.vip_level==2){
						type=3
					}
					var params = {
						page: this.schoolmate.page,
						size: this.schoolmate.size,
						type: type,
						university_id: this.schoolmate.university_id,
						graduated_id: this.schoolmate.graduated_id,
						up_id: this.schoolmate.up_id,
						word: this.word,
						token: token
					} 
					app.post(url, params, "get").then((res) => {
						this.schoolmateList = this.schoolmate.page==1?res:this.schoolmateList.concat(res)
					}).catch((err) => {
						console.log(err)
					})
				}
 
			},

			//获取所有专业
			getAllGraduated() {
				var url = "university/getGraduated"
				var parmas = {}
				app.post(url, parmas, "get").then((res) => {
					console.log(res)
					this.graduatedList = res
				}).catch((err) => {
					console.log(err)
				})
			},
			//获取所有升学方式
			getAllUp() {
				var url = "university/getAllUp"
				var params = {}
				app.post(url, params, "get").then((res) => {
					console.log(res[0].name)
					// let obj = {
					// 	id: '',
					// 	name: '全部'
					// }
					// res.unshift(obj)
					this.upList = res
				}).catch((err) => {
					console.log(err)
				})
			},
			//获取全部地区(省)
			getAllProvince() {
				var url = "city/getAllProvince"
				var params = {}
				app.post(url, params, "get").then((res) => {
					// let obj={
					// 	code:'',
					// 	name:'全部省'
					// }
					// res.unshift(obj)
					this.provinceList = res
				}).catch((err) => {
					console.log(err)
				})
			},

			//市
			getAllCity() {
				var url = "city/getCity"
				var params = {
					province_id: this.province_id
				}
				app.post(url, params, "get").then((res) => {
					console.log(res)

					// let obj={
					// 	code:'',
					// 	name:'全部市'
					// }
					// res.unshift(obj)
					this.cityList = res
				}).catch((err) => {
					console.log(err)
				})
			},

			//学校
			getAllSchool() {
				var url = "city/getUniversityByCity"
				var params = {
					city_id: this.city_id
				}
				app.post(url, params, "get").then((res) => {
					console.log(res)
					// let obj={
					// 	id:'',
					// 	name:'全部学校'
					// }
					// res.unshift(obj)
					this.schoolList = res
				}).catch((err) => {
					console.log(err)
				})
			},
			//点击选中的升学方式
			selectup(e) {
				console.log('34783443797')
				this.schoolmate.up_id = e.currentTarget.dataset.id
				this.schoolmate.university_id=''
				this.schoolmate.graduated_id=''
				this.schoolmate.page = 1;
				this.word='';
				this.schoolmateList = [];
				this.upindex=e.currentTarget.dataset.index;
				this.getmatelist()
				this.isStyleShow = false
			},

			//点击选中的专业
			selectgraduated(e) {
				this.schoolmate.graduated_id = e.currentTarget.dataset.id;
				console.log('选择的id', this.schoolmate.graduated_id)
				this.isMajorShow = false;
				this.schoolmate.page = 1;
				this.schoolmate.page=1,
				this.schoolmate.university_id=''
				this.schoolmate.up_id=''
				this.word=''
				this.schoolmateList = [];
				this.major=e.currentTarget.dataset.index;
				this.getmatelist()
			},
			//点击选中的学校
			selectschool(e) {
				this.selstuent=e.currentTarget.dataset.index;
				
				this.schoolmate.page = 1
				this.schoolmate.graduated_id = ''
				this.schoolmate.up_id = ''
				this.word = '';
				this.isDiquShow = false
				this.schoolmate.university_id = e.currentTarget.dataset.id;
				this.schoolmateList=[]
				this.getmatelist()

			},
			//点击选中的省
			selectprovince(e) {
				if (this.province_id != e.currentTarget.dataset.id) {
					this.province_id = e.currentTarget.dataset.id;
					let index=e.currentTarget.dataset.index;
					this.selpro=index;
					this.selty=-1;
					this.selstuent=-1;
					this.schoolList = [];
					this.getAllCity()
				}

			},
			//点击全部省
			selectallprovince(e) {
				this.schoolmate.page = 1,
				this.schoolmate.university_id = ''
				this.schoolmate.graduated_id = ''
				this.schoolmate.up_id = ''
				this.word = ''
				this.schoolmateList = [];
				this.selpro=e.currentTarget.dataset.index;
				this.selty=-1;
				this.selstuent=-1;
				this.getmatelist()
				this.isDiquShow = false
			},
			//点击选中全部市
			selectallcity() {
				this.getmatelist()
				this.isDiquShow = false

			},
			//点击选中的市
			selectcity(e) {
				if (this.city_id != e.currentTarget.dataset.id) {
					this.city_id = e.currentTarget.dataset.id;
					let index=e.currentTarget.dataset.index;
					 this.selty=index;
					 this.selstuent=-1;
					this.schoolList = [];
					this.getAllSchool()
				}
			},
			todetile(e) {
				if (uni.getStorageSync('token') == '') {
					uni.navigateTo({
						url: '../register/register?istoken=' + 1
					})
				} else {
					var type = this.type
					var id = e.currentTarget.dataset.id.user_id
					console.log(e.currentTarget.dataset.id)
					uni.navigateTo({
						url: "../school/schoolmateMsg?user_id=" + id + "&type=" + type
					})
				}

			}
		},
		onLoad(options) {
			console.log(options.type)
			var type = options.type
			this.type = type
			// this.getmatelist()
			// this.getAllSchool()
			this.getAllGraduated()
			this.getAllUp()
			this.getAllProvince();
			

		},
		onShow() {
			uni.hideTabBar({
			
			})
			this.page = 1;
			this.schoolmateList = []
			
			// 获取个人信息
			this.getpersoninfo()
			this.jumpschool=true;
			this.jumpschoolk=true
		},
		onReachBottom() {
			let newpage=this.schoolmate.page;
			newpage++;
			this.schoolmate.page=newpage;
			this.getmatelist()
		}

	}
</script>

<style lang="less">
	page{
		background-color: rgba(249, 249, 249, 1);
	}
	.content {
		
		// height: 100vh;
		padding-bottom: 120rpx;
		box-sizing: border-box;
		.nodata {
			text-align: center;
			font-size: 28rpx;
			padding: 20rpx 0;
		}

		.top {
			padding: 18rpx 32rpx;
			display: flex;
			background-color: #fff;
		}

		.search {
			// background-color: #fff;
			display: flex;
			// text-align: center;
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
			margin-bottom: 24rpx;

			.typeitem {
				width: 132rpx;
			}

			.active {
				color: #05849D;
			}

			.diquPopup,
			.schoolPopup,
			.majorPopup,
			.stylePopup {
				width: 750rpx;
				// height: 314rpx;
				position: absolute;
				top: 184rpx;
				left: 0;
				background-color: #fff;
				padding: 22rpx 32rpx;
				// box-sizing: border-box;
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
					.colum {
						width: 30%;

					}

					display: flex;
					// height: 314rpx;
					// flex-direction: column;
					flex-wrap: wrap;
					justify-content: start;
				}
			}
		}

		.mateitem {
			width: 686rpx;
			height: 172rpx;
			background: rgba(255, 255, 255, 1);
			opacity: 1;
			border-radius: 8px;
			margin: 0 auto;
			background-color: #fff;
			display: flex;
			padding: 24rpx 32rpx;
			box-sizing: border-box;
			position: relative;
			margin-bottom: 24rpx;

			.photo {
				width: 112rpx;
				height: 112rpx;
				margin-right: 26rpx;
				margin-top: 6rpx;
				border-radius: 50%;

			}

			.cont {
				.title {
					color: rgba(61, 68, 77, 1);
					font-size: 28rpx;
				}

				.academy {
					color: rgba(61, 68, 77, 1);
					font-size: 22rpx;
					margin-top: 4rpx;
					display: flex;
					align-items: center;

					// line-height: 24rpx;
					.icon {
						width: 24rpx;
						height: 24rpx;
						margin-right: 6rpx;
					}
				}

				.fen {
					font-size: 22rpx;
					color: rgba(140, 145, 152, 1);
					margin-top: 10rpx;
				}
			}

			.right {
				width: 132rpx;
				height: 54rpx;
				position: absolute;
				right: 0;
				top: 16rpx;
				display: flex;
				align-items: center;

				.icon {
					width: 28rpx;
					height: 28rpx;
					// margin-right: 6rpx;
				}

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
				}
			}
		}

		.box {
			width: 686rpx;
			height: 110rpx;
			background-color: #fff;
			margin: 0 auto;
			font-size: 28rpx;
			line-height: 110rpx;
			text-align: center;

			text {
				color: rgba(129, 195, 191, 1);
			}
		}

	}

	.tabBar {
		display: flex;
		position: fixed;
		width: 100%;
		height: 100rpx;
		background-color: #fff;
		bottom: 0;

		.item {
			flex: 4;
			text-align: center;
			font-size: 22rpx;
			color: rgba(189, 196, 206, 1);
			// display: ;
			margin-top: 10rpx;

			image {
				width: 48rpx;
				height: 48rpx;
			}
		}
	}
</style>
