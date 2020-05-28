<template>
  <view class="content">
      <view class="title">{{articlelist.title}}</view>
      <view class="msg" style="display:flex;align-items: center;justify-content: space-between;">
		  <view style="display:flex">
			
			  <image class="photo" :src="articlelist.head_image"></image>
			  <view class="nameTime" >
			      <view class="name">{{articlelist.nickname}}</view>
			      <view class="time">{{articlelist.createtime}}</view>
			  </view>
		  </view>
         
          <view class="cellect" v-if="articlelist.is_like==0" @click="cellect">
              <image class="icon" src="../../static/shoucang.png"></image>
              <view class="txt">收藏</view>
          </view>
          <view class="cellect" v-else @click="notcellect" style="border:2rpx solid #BDC4CE;text-align: center;">
             
              <view class="txt" style="color: #BDC4CE;">已收藏</view>
          </view>
      </view>
      <view class="text">
         {{articlelist.des_content==undefined?'':articlelist.des_content}}
      </view>
	  <view style="display:flex;">
	  		<image :src="item" v-for="(item,index) in articlelist.des_images" :key="indexk" @click.stop="preimg(index)" style="margin-right:10rpx;margin-bottom:10rpx;width:128rpx;
			height:128rpx"></image>
	  </view>
      <!-- <image class="pic" src="../../static/bg_img@2x.png"></image>
      <view class="text">
          积极主动的工作态度统筹做好内挖潜力和外拓资源，要针对不同学科专业特点和社会需求情况进行总体分析和研判，对内结合实际出台和完善各项推动措施和帮扶举措，深入挖掘潜力，积极调动各方力量共同参与就业工作，对外主动联络汇集各类就业资源、拓展就业渠道。三是要把握学生需求，以耐心细致的工作作风统筹抓好措施保障与精准落实，要宣传、解读、贯彻好各项政策举措，准确把握学生特点和需求做好学生的就业指导、技能培训、心理辅导，特别要有针对性做好各类就业困难学生帮扶工作，做到“一生一策”。
      </view> -->
  </view>
</template>

<script>
import app from "../../App.vue";
export default {
    data(){
        return{
            article_id:'',
            articlelist:{}
        }
    },
    methods:{
        getArticleDetial(){
            var that = this
            var url = "article/getArticleInfo"
            var params = {
                article_id:that.article_id
            }
            app.post(url,params,"get").then((res)=>{
                console.log(res)
                that.articlelist=res
            }).catch((err)=>{
                console.log(err)
				uni.showToast({
					title:'文章已删除',
					icon:"none"
				})
				setTimeout(function(){
					uni.navigateBack({
						checked:true
					})
				},1500)
            })
        },
		preimg(index){
			let that=this;
			console.log(that.articlelist.des_images,index)
			uni.previewImage({
			current: that.articlelist.des_images[index],
			urls: that.articlelist.des_images,
			success: function (res) { },
			fail: function (res) { },
			complete: function (res) { },
			})
			
		},
         cellect(){
            var that = this
            var url = "article/likeArticle"
            var params = {
                token:uni.getStorageSync('token'),
                article_id:that.article_id
            }
            app.post(url,params,"get").then((res)=>{
                console.log(res)
                
               
                that.articlelist.is_like=1
            }).catch((err)=>{
                console.log(err)
            })
    },
    notcellect(){
            var that = this
            var url = "article/likeArticle"
            var params = {
                token:uni.getStorageSync('token'),
                article_id:that.article_id
            }
            app.post(url,params,"get").then((res)=>{
                console.log(res)
                
                
                that.articlelist.is_like=0
            }).catch((err)=>{
                console.log(err)
            })
    },
    },
   
    onLoad(options){
        this.article_id = options.article_id
        this.getArticleDetial()
    }

}
</script>

<style lang="less">
.content{
    padding: 32rpx;
    .title{
        font-weight:500;
        color:rgba(61,68,77,1);
        font-size:32rpx;
        
    }
    .msg{
        display: flex;
        margin-top: 40rpx;
        margin-bottom: 40rpx;
        .photo{
            width: 68rpx;
            height: 68rpx;
            margin-right: 20rpx;
			border-radius: 50%;
        }
        .name{
            color:rgba(61,68,77,1);
            font-size: 24rpx;
        }
        .time{
            color:rgba(140,145,152,1);
            font-size:24rpx;
           
        }
        .cellect{
            width:132rpx;
            height:54rpx;
            border:2rpx solid rgba(129,195,191,1);
            opacity:1;
            border-radius:40rpx;
            display: flex;
            align-items: center;
            padding: 0 22rpx;
            text-align: center;
            box-sizing: border-box;
            align-items: center;
            // margin-left: 340rpx;
            .icon{
                width: 32rpx;
                height: 32rpx;
            }
            .txt{
                color:rgba(129,195,191,1);
                font-size: 24rpx;
                margin-left: 6rpx;
                margin: 0 auto;
            }
        }
    }
    .text{
        font-size:24rpx;
        color:rgba(6,18,30,1);
        margin-bottom: 26rpx;
    }
    .pic{
        width: 686rpx;
        height: 454rpx;
        margin-bottom: 26rpx;
    }
}
</style>