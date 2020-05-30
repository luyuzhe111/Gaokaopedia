<template>
  <view class="content">
      <image class="background" :src="university.data.des_image==''?'../../static/bg_img@2x.png':university.des_image"></image>

    <view class="titleBox">
        <view class="title">{{university.name}}</view>
        <view class="eng">{{name}}</view>
    </view>
            <view class="table">
                <view class="colum" @click="highschoollist">
                    <view class="num">{{university.total1}}</view>
                    <view class="title">高中校友</view>
                </view>   
                <view class="colum" @click="popupShow" data-type="1">
                    <view class="num">{{university.total3}}</view>
                    <view class="title">同城学长</view>
                </view>
                <view class="colum" @click="popupShow" data-type=2>
                    <view class="num">{{university.total2}}</view>
                    <view class="title">同省学长</view>
                </view>
            </view> 
          <view class="titleAcademy">
              <image class="icon" src="../../static/wenzhang_icon@2x.png"></image>
              <view class="txt">文章分享</view>
          </view>
          <view class="tabCard">
              <!-- <view class="item" @click="selecttype" v-for="(item,index) in articletypelist" :key="index" :data-id="item.id">{{item.name}}
                  <image class="selected" src="../../static/xuanzhong_icon@2x.png"></image>
              </view> -->
               <view class="item" :class="sel==index?'active':''" @click="selecttype(item,index)" v-for="(item,index) in articletypelist" :key="index" :data-id="item.id">{{item.name}}
                  <image class="selected" src="../../static/xuanzhong_icon@2x.png" v-if="sel==index"></image>
                  <!-- <image class="selected" src="" v-else></image> -->
              </view>
             
          </view>
		   <view class="nodata" v-if="articleList.length==0">暂无数据</view>
          <view class="articl" v-else>
              <view v-for="(item,index) in articleList" :key="index" style="border-bottom:1rpx solid #eee">
                <view class="articltitle" >
                    <image class="photo" :src="item.head_image"></image>
                    <view class="msg">
                        <view class="name">{{item.nickname}}</view>
                        <view class="tips">{{item.article_type_name}}</view>
                    </view>
                </view>
                <view class="articleBox">
                    <view class="title">{{item.title}}</view>
                    <view class="txt">{{item.des_content}}</view>
                    <image :src="item" v-for="(item,index) in item.des_images" :key="index"></image>
                    <!-- <image src="../../static/bg_img@2x.png"></image> -->
                </view>
              </view>
             
              
          </view>
         
          <view class="popup" v-if="isPopupShow">
            <view class="card">
                <view class="close" @click="popupHide">
                    <image src="../../static/guanbi_icon@2x.png"></image>
                </view>
                <view class="one">购买畅读卡</view>
                <view class="two">可查看更多同城、同省学长学姐</view>
                <view class="btn" @click="tobuy">立即购买</view>
            </view>
        </view>
            
          
          <!-- <view class="moreCont">
              <image class="moreicon" src="../../static/jiazai_icon@2x.png"></image>
              <view class="t">加载中</view>
          </view>  -->
  </view>
</template>

<script>
import app from "../../App.vue";
export default {
    data(){
        return{
            university_id:'',
            name:'',
            university:[],
            articletypelist:[],
            articleList:[],
            page:'1',
            size:'10',
            type_id:'',
            sel:0,
            isPopupShow:false,
			vip_level:'',
			college_id:''
        }
        
    },
    onLoad(options){
		console.log(348348934,options)
        this.university_id=options.university_id
        this.name=options.name;
		this.college_id=options.college_id
        this.getschooldetails()
        this.getarticletype()
        this.getAllArticle()
		this.getpersoninfo()
    },
    methods:{
		// 获取个人信息
		getpersoninfo(){
			let that = this;
			var url='student/getMyInfo';
			var params={
			   
			}
			app.post(url,params,"get").then((res)=>{
			    console.log(res)
			    this.vip_level=res.vip_level
			}).catch((err)=>{
			    console.log(err)
			})
		},
		highschoollist(){
			uni.navigateTo({
				url:'./highSchoolmate?schooltype='+1+'&university_id='+this.university_id+'&college_id='+this.college_id
			})
			
		},
        popupShow(e){
           let type=e.currentTarget.dataset.type;
           if(type==1){
           	if(this.vip_level==1||this.vip_level==2){
           		uni.navigateTo({
           			url:"./highSchoolmate?schooltype="+2+'&university_id='+this.university_id+'&college_id='+this.college_id
           		})
           	}else{
           		this.isPopupShow=true
           	}
           }else if(type==2){  
           	if(this.vip_level==2){
           		uni.navigateTo({ 
           			url:"./highSchoolmate?schooltype="+3+'&university_id='+this.university_id+'&college_id='+this.college_id
           		})
           		
           	}else{
           		this.isPopupShow=true
           	}
           }
           
        },
        getschooldetails(){
    	    var token = uni.getStorageSync('token')
            let that = this;
            var url='university/getCollegeDetail'
            var params={
                college_id:that.college_id,
                token:token
            }
            app.post(url,params,"get").then((res)=>{
                console.log(res)
                this.university=res
            }).catch((err)=>{
                console.log(err)
            })
        },
        getarticletype(){
            var that = this
            var url = "article/getArticleType"
            var params = {}
            app.post(url,params,"get").then((res)=>{
                this.articletypelist=res
                console.log(res)
            }).catch((err)=>{
                console.log(err)
            })
        },
        //获取所有文章
        getAllArticle(){
            var token = uni.getStorageSync('token')
            var that=this
            var url="article/getArticleList"
            var params = {
                university_id:that.university_id,
				college_id:that.college_id,
                page:that.page,
                size:that.size,
                type_id:that.type_id,
                token:token
            }
            app.post(url,params,"get").then((res)=>{
                this.articleList=res
            }).catch((err)=>{
                console.log(err)
            })
        },
        selecttype(item,index){
            this.sel=index
            this.type_id=item.id
            this.getAllArticle()
        },
        popupHide(){
            this.isPopupShow=false
        },
    }
}
</script>

<style lang="less">
.content{
    // background-color: rgba(249,249,249,1);

    .background{
        width: 750rpx;
        height: 334rpx;
        position: absolute;
        top: 0;
        z-index: -1;
    }
    // background: url('../../static/bg_img@2x.png') no-repeat;
    // background-size: 750rpx 334rpx;
    // height: 100vh;
    overflow: hidden;
    padding-bottom: 30rpx;
    .titleBox{
        margin-top: 146rpx;
        margin-left: 32rpx;
        .title{
            color:rgba(255,255,255,1);
            font-size: 36rpx;
        }
        .eng{
            color:rgba(255,255,255,1);
            font-size: 24rpx;
        }
    }
     .table{
            // border:1rpx solid rgba(213,234,227,1);
            display: flex;
            width: 686rpx;
            height: 114rpx;
                padding-top: 14rpx;
                box-sizing: border-box;
                margin-top: 46rpx;
                background-color: #fff;
                border-radius:8rpx;
                margin-left: 32rpx;
            .colum{
                width: 230rpx;
                height: 80rpx;
                font-size: 32rpx;
                text-align: center;
                color:rgba(45,85,117,1);
                // border-right: 1rpx solid rgba(213,234,227,1);

                .num{
                }
                .title{
                    font-size: 22rpx;
                    // margin-top: 4rpx;
                }
            }
        }
        .titleAcademy{
            display: flex;
            align-items: center;
            margin-top: 52rpx;
            margin-left: 32rpx;
            .icon{
                width: 48rpx;
                height: 48rpx;
            }
            .txt{
                color:rgba(45,85,117,1);
                font-size: 32rpx;
                margin-left: 16rpx;
            }
            .more{
                display: flex;
                align-items: center;
                color:rgba(61,68,77,1);
                font-size: 28rpx;
                margin-left: 362rpx;
                .right{
                    width: 10rpx;
                    height: 18rpx;
                    margin-left: 8rpx;
                }
            }
        }
        .tabCard{
             height:70rpx;
            background:rgba(255,255,255,1);
            margin-top: 22rpx;
            overflow-x: auto;
            display: flex;
            // flex-wrap: wrap;
            white-space:nowrap;
            padding: 10rpx 0 0 32rpx;
            .item{
                margin-right: 40rpx;
                color:rgba(6,18,30,1);
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
                .selected{
                     width: 60rpx;
                height: 15rpx;
                
                margin-top: 8rpx;
                }
            }
            .active{
                color:rgba(5,132,157,1);
                
                
            }
        }
        .articl{
            .nodata{
                text-align: center;
                font-size: 28rpx;
            }
            background-color: #fff;
            margin-top: 24rpx;
            padding: 0 34rpx;
            box-sizing: border-box;
            box-shadow:0rpx 4rpx 6rpx rgba(45,85,117,0.1);
            border-radius:20rpx;
            margin-bottom: 42rpx;
			padding-bottom: 32rpx;
			box-sizing: border-box;
            .articltitle{
                // border-bottom: 1rpx solid rgba(238,238,238,1);
                padding: 40rpx 0 26rpx 0;
                display: flex;
                font-size: 24rpx;
                .name{
                    color:rgba(61,68,77,1);
                }
                .tips{
                    color:rgba(140,145,152,1);
                }
                .photo{
                    width: 68rpx;
                    height: 68rpx;
                    margin-right: 20rpx;
                }
            }
            .articleBox{
                .title{
                    color:rgba(6,18,30,1);
                    font-size: 28rpx;
                    margin-top: 24rpx;
                }
                .txt{
                    color:rgba(91,94,99,1);
                    font-size: 24rpx;
                    margin-top: 8rpx;
                    margin-bottom: 20rpx;
                }
                image{
                    width: 128rpx;
                    height: 128rpx;
                    margin-right: 20rpx;
                    margin-bottom: 20rpx;
                }
                
            }
        }
         .moreCont{
            color:rgba(45,85,117,1);
            font-size: 28rpx;
            // line-height: 44rpx;
            height: 44rpx;
            width: 150rpx;
            display: flex;
            align-items: center;
            margin: 0 auto;
            // margin-top: 40rpx;
            .moreicon{
                width: 44rpx;
                height: 44rpx;
                margin-right: 16rpx;
            }
        }
         .popup{
        width: 100%;
        height: 100%;
        background:rgba(6,18,30,0.5);
        // opacity:0.6;
        position: fixed;
        top: 0;
        right: 0;
        z-index: 999;
        .card{
            width: 628rpx;
            height: 288rpx;
            background:rgba(255,255,255,1);
            position: fixed;
            top: 50%;
            left: 50%;
            opacity:1;
            transform: translate(-50%,-50%);
            // z-index: 999;
            position: relative;
            text-align: center;
            overflow: hidden;
            .close{
                width: 35rpx;
                height: 35rpx;
                position: absolute;
                top: 12rpx;
                right: 22rpx;
                image{
                    width: 35rpx;
                    height: 35rpx;
                }
            }
            .one{
                color:rgba(6,18,30,1);
                font-size: 28rpx;
                margin-top: 40rpx;
            }
            .two{
                color:rgba(6,18,30,1);
                font-size: 28rpx; 
                margin-top: 14rpx;
            }
            .btn{
                width:220rpx;
                height:60rpx;
                background:rgba(45,85,117,1);
                opacity:1;
                border-radius:8rpx;
                color: #fff;
                font-size:28rpx;
                text-align: center;
                line-height: 60rpx;
                margin: 0 auto;
                margin-top: 40rpx;
            }
        }
    }
}
</style>