<template>
	<view class="content">
		
		<view class="teacherfooter">
			<view class="teacherfootitem" @click="gotab(item,index)" v-for="(item,index) in list" :key="index">
				<view class="teacherfootitemtop shou">
					<image :src="currentTabIndex == index?item.selectedIconPath:item.iconPath"></image>
				</view>
				<view class="teacherfootname " :class="currentTabIndex == index?'activename':''" >{{item.text}}</view>
				
			</view>
		</view>

	</view>

</template>

<script>
	let chosetype=uni.getStorageSync("chosetype");
	export default {
		data() {
			return {
				list:chosetype==1? [{
						"pagePath": "/pages/school/school",
						"iconPath": "/static/xuexiao_icon@2x3.png",
						"selectedIconPath": "/static/xuexiao_icon@2x.png",
						"text": "学校",
						
					}, {
						"pagePath": "/pages/schoolmate/schoolmate",
						"iconPath": "/static/zhaoxiaoyou_icon@2x.png",
						"selectedIconPath": "/static/zhaoxiaoyou_icon@2x2.png",
						"text": "找校友"
					},
					{
						"pagePath": "/pages/homePage/homePage",
						"iconPath": "/static/wodezhuye_icon@2x.png",
						"selectedIconPath": "/static/wodezhuye_icon@2x2.png",
						"text": "我的主页"
					}
					
				]:[{
						"pagePath": "/pages/schoolmate/schoolmate",
						"iconPath": "/static/zhaoxiaoyou_icon@2x.png",
						"selectedIconPath": "/static/zhaoxiaoyou_icon@2x2.png",
						"text": "找校友"
					},
					{
						"pagePath": "/pages/homePage/collegeHome",
						"iconPath": "/static/wodezhuye_icon@2x.png",
						"selectedIconPath": "/static/wodezhuye_icon@2x2.png",
						"text": "我的主页"
					}
					
				],
				
				currentTabIndex: this.current
			}
		},
		// props: ["current"],
		 props: {
		            current: { type: [Number, String],default: 0 },
		           
		        },
		onLoad() {
			
			// console.log('我是底部菜单',chosetype)
			// if(chosetype==1){
			// 	this.list=[{
			// 			"pagePath": "/pages/school/school",
			// 			"iconPath": "/static/xuexiao_icon@2x3.png",
			// 			"selectedIconPath": "/static/xuexiao_icon@2x.png",
			// 			"text": "学校",
						
			// 		}, {
			// 			"pagePath": "/pages/schoolmate/schoolmate",
			// 			"iconPath": "/static/zhaoxiaoyou_icon@2x.png",
			// 			"selectedIconPath": "/static/zhaoxiaoyou_icon@2x2.png",
			// 			"text": "找校友"
			// 		},
			// 		{
			// 			"pagePath": "/pages/homePage/homePage",
			// 			"iconPath": "/static/wodezhuye_icon@2x.png",
			// 			"selectedIconPath": "/static/wodezhuye_icon@2x2.png",
			// 			"text": "我的主页"
			// 		}
					
			// 	]
			// }else if(chosetype==2){
			// 	this.list=[{
			// 			"pagePath": "/pages/schoolmate/schoolmate",
			// 			"iconPath": "/static/zhaoxiaoyou_icon@2x.png",
			// 			"selectedIconPath": "/static/zhaoxiaoyou_icon@2x2.png",
			// 			"text": "找校友"
			// 		},
			// 		{
			// 			"pagePath": "/pages/homePage/homePage",
			// 			"iconPath": "/static/wodezhuye_icon@2x.png",
			// 			"selectedIconPath": "/static/wodezhuye_icon@2x2.png",
			// 			"text": "我的主页"
			// 		}
					
			// 	]
			// }
			
		},
		
		methods: {
			gotab(item,index) {
				console.log(item)
				
				console.log(item.pagePath);
				 let url=item.pagePath;
				
				  uni.switchTab({url})
				 // uni.switchTab({
					//  url:"/pages/nearshop/nearshop"
				 // })
			}

		},
		onShow(){
			
		}

	}
</script>

<style>
	
</style>
