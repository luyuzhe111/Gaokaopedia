<template>
  <view class="content">
	 
      <image class="background" :src="university.des_image=='http://school.t.brotop.cn'?'../../static/bg_img@2x.png':university.des_image"></image>
      <view class="contentBox">
          <image class="photo" :src="university.icon_image=='http://school.t.brotop.cn'?'../../static/logo_img@2x.png':university.icon_image"></image>
          <view class="title">{{university.name}}</view>
          <view class="cellectBtn" @click="cellectScool" v-if="university.is_like==0">
              <image class="icon" src="../../static/shoucang_icon@2x.png"></image>
              <view class="text">收藏</view>
          </view>
          <view class="cellectedbtn" @click="qxcellectScool"  v-else>
              <view class="text" style="text-align: center;">已收藏</view>
          </view>
          <view class="msg" @click="tomsgpage" style="display:flex">
			  
			  <text v-if="university.des_content==''">暂无简介</text>
			  
				  <rich-text :nodes="university.des_content" v-else></rich-text>
			 
           
            <text style="color:#81C3BF" v-if="university.des_content!=''">[更多]</text>
          </view>
          
      </view>
      <view class="schoolMsg">
            <view class="row" @click="copaywang">
                <image src="../../static/guanwang_icon@2x.png" class="icon"></image>
                <view class="websit">官方网站：<text class="lianjie">{{university.url}}</text></view>
            </view>
            <view class="row" @click="copyzhong">
                <image src="../../static/gongzhonghao_icon@2x.png" class="icon"></image>
                <view class="websit">本科生招生公众号：<text class="lianjie">{{university.wechat}}</text></view>
            </view>
            <view class="table">
                <view class="colum" hover-class="none" @click="highschoollist">
                    <view class="num">{{university.same_school_total_num}}</view>
                    <view class="title">高中校友</view>
                </view>
                <view class="colum" @click="popupShow" data-type="1">
                    <view class="num">{{university.same_city_total_num}}</view>
                    <view class="title">同城学长</view>
                </view>
                <view class="colum" @click="popupShow" style="border-right:none" data-type="2">
                    <view class="num">{{university.same_province_total_num}}</view>
                    <view class="title">同省学长</view>
                </view>
            </view>
      </view>
      <view class="schoolAcademy">
          <view class="titleAcademy">
              <image class="icon" src="../../static/yuanxi_icon@2x.png"></image>
              <view class="txt">学校院系</view>
              <view class="more" @click="toacademypage(uni_id)">检索更多
                  <image class="right" src="../../static/dizhi_btn@2x.png"></image>
              </view>
          </view>
		  <view class="nodata" v-if="collegeList.length==0" style="margin:27rpx auto 32rpx">暂无数据</view>
          <view class="card" v-else>
             
            <view class="item" style="width: 188rpx;" v-for="(item,index) in collegeList" :key="index" @click="tocollegepage(item)"><view class="bor">{{item.name}}</view></view>
                
             
          </view>
          <view class="titleAcademy">
              <image class="icon" src="../../static/wenzhang_icon@2x.png"></image>
              <view class="txt">文章分享</view>
          </view>
          <view class="tabCard">
              <view class="item" style="width:446rpx" :class="sel==index?'active':''" @click="selecttype(item,index)" v-for="(item,index) in articletypelist" :key="index" :data-id="item.id">{{item.name}}
                  <image class="selected" src="../../static/xuanzhong_icon@2x.png" v-if="sel==index"></image>
                  <!-- <image class="selected" src="" v-else></image> -->
              </view>
             
          </view>
		   <view class="nodata" v-if="articleList.length==0">暂无数据</view>
          <view class="articl" v-else>
              
           
              <view class="listitem" v-for="(item,index) in articleList"  :key="index" @click="toarticlemsg(item)" style="border-bottom:1rpx solid #eee">
                <view class="articltitle">
                    <image class="photo" :src="item.head_image" style="border-radius: 50%;"></image>
                    <view class="msg">
                        <view class="name">{{item.nickname}}</view>
                        <view class="tips">{{item.article_type_name}}</view>
                    </view>
                </view>
                <view class="articleBox">
                    <view class="title">{{item.title}}</view>
                    <view class="txt">{{item.des_content}}</view>
                    <image :src="item" v-for="(item,indexk) in item.des_images" :key="indexk" @click.stop="preimg(index,indexk)"></image>
                </view>
              </view>
          </view>

         
          <!-- <view class="moreCont" >
              <image class="moreicon" src="../../static/jiazai_icon@2x.png"></image>
              <view class="t">加载中</view>
          </view> -->
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
  </view>
</template>

<script>
import app from "../../App.vue";

export default {
    data(){
        return{
            isPopupShow:false,
            id:'',
            uni_id:'',
           
            university:{},
            collegeList:[],
            articletypelist:[

            ],
            articleList:[],
            page:'1',
            size:'10',
            type_id:'',
            iscellect:true,
            sel:0,
            baseurl:app.globalData.imageBaseUrl,
            isactive:false,
			vip_level:''
        }
    },
	
    onLoad(options){
		
        console.log(options)
        this.uni_id=options.id
        this.id=options.id;
        console.log(this.id)
        console.log(this.baseurl)
		this.getschooldetails()
		this.getollege()
		this.getarticletype()
		 this.getAllArticle();
    },
	onShow() {
		// 获取个人信息
		this.getpersoninfo();
		this.isPopupShow=false
		
	},
    methods:{
		// 复制官网
		copaywang(){
			uni.setClipboardData({
				data: this.university.url
			});
			
		},
		copyzhong(){
			
			uni.setClipboardData({
				data: this.university.wechat
			});
		},
		tocollegepage(item){
		  console.log(item)
		  let id = item.university_id 
		  let name = item.name
		  let college_id=item.id
		  uni.navigateTo({
		    url:'./academyDetails?university_id='+this.id+'&name='+name+"&college_id="+college_id
		  })
		},
		highschoollist(){
			uni.navigateTo({
				url:'./highSchoolmate?schooltype='+1+'&university_id='+this.id
			})
			
		},
        popupShow(e){
			let type=e.currentTarget.dataset.type;
			if(type==1){
				if(this.vip_level==1||this.vip_level==2){
					uni.navigateTo({
						url:"./highSchoolmate?schooltype="+2+'&university_id='+this.id
					})
				}else{
					this.isPopupShow=true
				}
			}else if(type==2){
				if(this.vip_level==2){
					uni.navigateTo({
						url:"./highSchoolmate?schooltype="+3+'&university_id='+this.id
					})
					
				}else{
					this.isPopupShow=true
				}
			}
			 
        },
        popupHide(){
            this.isPopupShow=false
        },
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
        //获取大学详细信息
        getschooldetails(){
    	    var token = uni.getStorageSync('token')
            let that = this;
            var url='university/getUniversityInfo';
            var params={
                university_id:that.uni_id,
                token:token
            }
            app.post(url,params,"get").then((res)=>{
                console.log(res)
                this.university=res
            }).catch((err)=>{
                console.log(err)
            })
        },
        //获取大学的学院
        getollege(){
            var that=this
            var url ="university/getCollege"
            var params = {
                university_id:that.uni_id
            }
            app.post(url,params,"get").then((res)=>{
                console.log(res)
                this.collegeList=res
            }).catch((err)=>{
                console.log(err)
            })
        },
        getarticletype(){
            var that = this
            var url = "article/getArticleType"
            var params = {}
            app.post(url,params,"get").then((res)=>{
                res.forEach((item,index)=>{
                   item.issel=''
                    return;
                })
				var obj={
					id:'',
					name:'全部'
				}
				res.unshift(obj)
                this.articletypelist=res;
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
                university_id:that.uni_id,
                page:that.page,
                size:that.size,
                type_id:that.type_id,
                token:token
            }
            app.post(url,params,"get").then((res)=>{
                 this.articleList=this.articleList.concat(res)
				 // this.articleList=res;
            }).catch((err)=>{
                console.log(err)
            })
        },
		preimg(index,indexk){
			let that=this;
			uni.previewImage({
			current: that.articleList[index].des_images[indexk],
			urls: that.articleList[index].des_images,
			success: function (res) { },
			fail: function (res) { },
			complete: function (res) { },
			})
		},
		toarticlemsg(item){
			console.log('文章详情',item)
		    uni.navigateTo({
		        url:'../school/article?article_id='+item.id
		    })
		},
        selecttype(item,index){
            this.sel=index
            this.type_id=item.id;
			this.articleList=[];
			this.page=1;
            this.getAllArticle()
        },
        toacademypage(item){
           
            console.log(item)
            uni.navigateTo({
                url:'./academy?university_id='+item
            })
        },
        cellectScool(){
            
            var that = this
            var token = uni.getStorageSync('token')
            var url = "university/likeUniversity"
            var params = {
                token:token,
                university_id:that.uni_id
            }
            app.post(url,params,"post").then((res)=>{
                console.log(res)
                this.university.is_like=1
                // this.iscellect=false
            }).catch((err)=>{
                console.log(err)
            })
        },
        qxcellectScool(){
            
            var that = this
            var token = uni.getStorageSync('token')
            var url = "university/likeUniversity"
            var params = {
                token:token,
                university_id:that.uni_id
            }
            app.post(url,params,"post").then((res)=>{
                console.log(res)
                this.university.is_like=0
                // this.iscellect=true
            }).catch((err)=>{
                console.log(err)
            })
        },
        tomsgpage(){
            var id = this.university.id
            uni.navigateTo({
                url:'./schoolMsg?uni_id='+id
            })
        },
        tobuy(){
            uni.navigateTo({
                url:'../homePage/buyCard'
            })
        },
        toarticle(item){
            uni.navigateTo({
                url:'./article?article_id='+item.id
            })
        }
        
    },
	onReachBottom() {
		// let newpage=this.page;
		// newpage++;
		// this.getAllArticle()
	}
    

}
</script>

<style lang="less">
.content{
    .background{
        width: 750rpx;
        height: 552rpx;
        // position: absolute;
        // top: 0;
        // z-index: 999;
    }
    .contentBox{
        position: relative;
        display: flex;
        justify-content: flex-end;
        padding-right: 32rpx;
        flex-wrap: wrap;
        .photo{
            width: 160rpx;
            height: 160rpx;
            position: absolute;
			border-radius: 50%;
            top: -50rpx;
            left: 36rpx;
        }
        .title{
            top: -60rpx;
            position: absolute;
            color: #fff;
            left: 220rpx;
            font-size: 32rpx;
        }
        .cellectBtn{
            width:148rpx;
            height:46rpx;
            background:rgba(129,195,191,1);
            opacity:1;
            border-radius:28rpx;
            position: absolute;
            top: -130rpx;
            right: 32rpx;
            padding:0 30rpx;
            box-sizing: border-box;
            font-size: 24rpx;
            text-align: center;
            color: #fff;
            // line-height: 46rpx;
            // vertical-align: middle;
            display: flex;
            align-items: center;
            .icon{
                width: 32rpx;
                height: 32rpx;
                // margin-right: 4rpx;
                // vertical-align: middle;
            }
            .text{
                // margin-left: 6rpx;
                margin: 0 auto;
            }
        }
        .cellectedbtn{
            background-color: #BDC4CE;
            width:148rpx;
            height:46rpx;
            border-radius:28rpx;
            position: absolute;
            top: -130rpx;
            right: 32rpx;
            // padding-left: 30rpx;
            box-sizing: border-box;
            font-size: 24rpx;
            color: #fff;
            // line-height: 46rpx;
            // vertical-align: middle;
            display: flex;
            align-items: center;
            .text{
                margin: 0 auto;
                text-align: center;
            }
        }
        .msg{
            color:#06121E;
            font-size: 24rpx;
            width: 500rpx;
            height: 106rpx;
        }
        
    }
    .schoolMsg{
       width: 686rpx;
       margin: 22rpx auto;
       border-top: 1rpx solid rgba(238,238,238,1);
       padding: 24rpx 0;
       box-sizing: border-box;
        .row{
            display: flex;
            align-items: center;
            font-size: 24rpx;
            margin-bottom: 20rpx;
            .lianjie{
                text-decoration:underline;
                color:rgba(45,85,117,1);
            }
            .icon{
                width: 32rpx;
                height: 32rpx;
                margin-right: 14rpx;
            }
        }
        .table{
            border:1rpx solid rgba(213,234,227,1);
            display: flex;
            height: 114rpx;
                padding-top: 14rpx;
                box-sizing: border-box;
                margin-top: 24rpx;

            .colum{
                width: 230rpx;
                height: 80rpx;
                font-size: 32rpx;
                text-align: center;
                color:rgba(45,85,117,1);
                border-right: 1rpx solid rgba(213,234,227,1);	
				.colum:last-child{
					border-right:none
				}
               
                .title{
                    font-size: 22rpx;
                    // margin-top: 4rpx;
                }
            }
        }
    }
    .schoolAcademy{
        background:rgba(249,249,249,1);
        padding: 38rpx 32rpx;
        .titleAcademy{
            display: flex;
            align-items: center;
            .icon{
                width: 48rpx;
                height: 48rpx;
            }
            .txt{
                color:rgba(45,85,117,1);
                font-size: 32rpx;
                margin-left: 16rpx;
				height:auto;
				display: -webkit-box;
				-webkit-box-orient: vertical;
				-webkit-line-clamp: 3;
				overflow: hidden;
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
        .card{
            width:686rpx;
            height:288rpx;
			overflow: hidden;
            background:rgba(255,255,255,1);
            box-shadow:0rpx 4rpx 6rpx rgba(45,85,117,0.1);
            opacity:1;
            border-radius:20rpx;
            display: flex;
            flex-wrap: wrap;
            padding: 0 40rpx;
            padding: 20rpx 0;
            box-sizing: border-box;
            margin-top: 16rpx;
            margin-bottom: 44rpx;
            
            .item{
                width:227rpx!important;
                // padding: 40rpx 0;
                // box-sizing: border-box;
                font-size: 24rpx;
                color:rgba(45,85,117,1);
                text-align: center;
                .bor{
                    // border-right: 1rpx solid rgba(238,238,238,1);
                    width: 227rpx;
					overflow: hidden;
					text-overflow: ellipsis;
					white-space: nowrap;
					padding:0 20rpx;
					box-sizing: border-box;
                    
                    height: 64rpx;
                    line-height: 64rpx;
                    box-sizing: border-box;
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
            }
            .active{
                color:rgba(5,132,157,1);
              
                
            }
            .selected{
                width: 60rpx;
                height: 15rpx;
                
                margin-top: 8rpx;
            }
        }
        .articl{
            background-color: #fff;
            margin-top: 24rpx;
            padding: 0 34rpx 32rpx;
			box-sizing: border-box;
            box-shadow:0rpx 4rpx 6rpx rgba(45,85,117,0.1);
            border-radius:20rpx;
            margin-bottom: 42rpx;
			.listitem:last-child{
				border-bottom:none
			}
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
					hieght:auto;
					display: -webkit-box;
					-webkit-box-orient: vertical;
					-webkit-line-clamp: 3;
					overflow: hidden;
                }
                image{
                    width: 128rpx;
                    height: 128rpx;
                    margin-right: 20rpx;
                    margin-bottom: 20rpx;
                }
                
            }
            .nodata{
                font-size: 28rpx;
                text-align: center;
                padding: 20rpx 0;
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
            .moreicon{
                width: 44rpx;
                height: 44rpx;
                margin-right: 16rpx;
            }
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
            // height: 288rpx;
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
			padding: 20rpx 0;
			box-sizing: border-box;
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