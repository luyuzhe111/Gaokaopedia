<template>
  <view class="content">
      <view class="box">
          <view class="item">
              <input type="text" v-model="title" placeholder="请添加文章标题" placeholder-style="font-size:32rpx;color:rgba(189,196,206,1);" style="">
          </view>
          <view class="item">
              <view class="left">文章类型</view>
              <view class="right" @click="seltype">
                  <!-- <input type="text" class="input" placeholder="请选择文章类型" placeholder-style="color:rgba(189,196,206,1);font-size:28rpx"> -->
                  <picker mode="selector" :value="type" @change="bindTypeChange" :range="typearray" range-key='name' :data-id="typearray[type].id">
                    <view class="text" style="color:rgba(189,196,206,1);font-size:28rpx">{{istypeshow? typearray[type].name : '请选择文章类型'}}</view>
                  </picker>
                  <image class="icon" src="../../static/dizhi_btn@2x2.png"></image>
              </view>
          </view>
          <view class="item">
              <view class="left">开放范围</view>
              <view class="right">
                 <view class="selected" @click="selectrange" data-id='1'>
                     <image class="icon" :src="show_type==1 ? '../../static/xuanzhong_img@2x.png' : '../../static/weixuanzhong_img@2x.png'"></image>
                     仅校友
                 </view>
                 <view class="selected" @click="selectrange" data-id='2'>
                     <image class="icon" :src="show_type==2 ? '../../static/xuanzhong_img@2x.png' : '../../static/weixuanzhong_img@2x.png'"></image>
                     全部
                 </view>
              </view>
          </view>
          <view class="item2">
              <textarea v-model="articlecontent" name="" id="" cols="30" rows="10" class="textarea" placeholder="请输入文章内容" placeholder-style="color:rgba(189,196,206,1);font-size:28rpx" maxlength="1000"></textarea>
              <view class="tips">可输入1000字</view>
          </view>
          <view class="picBox">
              <!-- <view class="boxitem">
                <image class="pic" src="../../static/bg_img@2x.png"></image>
                <image class="icon" src="../../static/shanchu_icon@2x.png"></image>
              </view>
              <view class="boxitem">
                <image class="pic" src="../../static/bg_img@2x.png"></image>
                <image class="icon" src="../../static/shanchu_icon@2x.png"></image>
              </view>
              <view class="boxitem">
                <image class="pic" src="../../static/bg_img@2x.png"></image>
                <image class="icon" src="../../static/shanchu_icon@2x.png"></image>
              </view>
              <view class="boxitem">
                <image class="pic" src="../../static/bg_img@2x.png"></image>
                <image class="icon" src="../../static/shanchu_icon@2x.png"></image>
              </view> -->
              
              <view class="boxitem">
                  <view class="imagebox" v-for="(item,index) in image" :key="index">
                      <image :src="item" style="width:128rpx;height:128rpx;margin-right: 24rpx;margin-bottom: 24rpx;"></image>
                      <image class="icon" src="../../static/shanchu_icon@2x.png" @click="delimage(index)"></image>
                  </view>
                  <image class="pic" src="../../static/shangchuan_img@2x.png" v-if="image.length<5"  @click="upload"></image>
              </view>
             
          </view>
      </view>
      <view class="btn" @click="pubarticle">确认发布</view>
  </view>
</template>

<script>
import app from "../../App.vue";
export default {
    data(){
        return{
            title:'',
            // range:'1',
            type:0,
            istypeshow:false,
            typearray:["经验","感受"],
            articlecontent:'',
            image:[],
            image2:[],
            isUpload:false,
            article_type_id:'',
            show_type:1,
            
        }
    },
    methods:{
        selectrange(e){
            this.show_type=e.currentTarget.dataset.id
            
        },
        seltype(){
            this.istypeshow=true
        },
        bindTypeChange(e){
            console.log(e.target)
            this.type = e.target.value
            console.log(e.currentTarget)
            this.article_type_id=this.typearray[e.target.value].id;
			console.log(this.article_type_id,999999)
        },
            upload(){
               var that = this;
               uni.chooseImage({
				   
                count: 1,
                sizeType: ['original', 'compressed'], //可以指定是原图还是压缩图，默认二者都有
                sourceType: ['album'], //从相册选择
                success: function (res) {
                 const tempFilePaths = res.tempFilePaths;
                //  that.image = that.image.push(tempFilePaths[0]) ;
                 app.upload('image',res.tempFilePaths[0],"post").then((res)=>{
					 console.log(res)
                    //  let newimage=that.image
                    var url= app.globalData.imageBaseUrl+res.kurl
                    //  that.image=app.globalData.imageBaseUrl+res.url
                     console.log(res)
                     console.log(res.url)
                     console.log(that.image)
                     that.image.push(url)
                     that.image2.push(res.kurl)
                     console.log(that.image)

                    //  that.image=that.image

                     that.isUpload=true
                 }).catch((err)=>{
                     console.log(err)
                 })
                //  console.log("tempFilePaths[0]",tempFilePaths[0])  //能够打印出选中的图片
                //  console.log(res)
                //  that.head_image=tempFilePaths[0]
                //  that.iconcheck = 1;//点击后图片更改状态由0变成1
                },
                error : function(e){
                 console.log(e);
                }
               });
            },
        pubarticle(){
            var that= this
             if(that.title==''){
                uni.showToast({
                    title:"请输入文章标题",
                    icon:"none"
                })
                return false
            }
            if(that.type==''){
                uni.showToast({
                    title:"请选择文章类型",
                    icon:"none"
                })
                return false
            }
            if(that.articlecontent==''){
                uni.showToast({
                    title:"请输入文章内容",
                    icon:"none"
                })
                return false
            }else if (that.articlecontent.length>1000){
                uni.showToast({
                    title:"已超过最大字数",
                    icon:"none"
                })
            }
            
            // if(that.image.length==0){
            //     uni.showToast({
            //         title:"请添加文章图片",
            //         icon:"none"
            //     })
            //     return false
            // }
            var url = "article/addArticle"
            var token = uni.getStorageSync('token')
            console.log(token)
            var params={
                token:token,
                title:that.title,
                article_type_id:that.article_type_id,
                show_type:that.show_type,
                des_content:that.articlecontent,
                des_images:that.image2.join(",")
            }
            app.post(url,params,"post").then((res)=>{
                console.log(res)
                uni.showToast({
                    title:"发布成功",
                    icon:"none"
                })
                setTimeout(() => {
                     uni.navigateBack()
                },1500);
               
                // uni.navigateTo({
                //     url:"./homePage"
                // })
            }).catch((err)=>{
                console.log(err)
            })
        },
        getAllType(){
            var that = this
            var url = "article/getArticleType"
            var params = {}
            app.post(url,params,"get").then((res)=>{
                console.log(res)
                that.typearray=res
            }).catch((err)=>{
                console.log(err)
            })
        },
        delimage(index){
            this.image.splice(index,1)
			this.image2.splice(index,1)
			this.image=this.image;
			this.image2=this.image2;
        }
    },
    onLoad(){
        this.getAllType()
    }

}
</script>

<style lang="less">
.content{
    background-color: #F9F9F9;
    padding: 32rpx;
    .box{
        background-color: #fff;
        width:686rpx;
        // height:976rpx;
        background:rgba(255,255,255,1);
        opacity:1;
        border-radius:20rpx;
        margin: 0 auto;
        .item{
            width:622rpx;
            margin:0 auto;
            padding: 32rpx 0;
            border-bottom: 1rpx solid rgba(238,238,238,1);
            display: flex;
            justify-content: space-between;
           
            .left{
                font-size: 28rpx;
            }
            .right{
                display: flex;
                align-items: center;
                font-size: 28rpx;
               
                .input{
                    width: 200rpx;
                    
                }
                .icon{
                    width: 13rpx;
                    height: 25rpx;
                    margin-left: 8rpx;
                }
                .selected{
                    .icon{
                        width: 20rpx;
                        height: 20rpx;
                        margin-left: 40rpx;
                        margin-right: 10rpx;
                    }
                }
            }
        }
        .item2{
            padding: 32rpx;
            width: 686rpx;
            .textarea{
                width: 622rpx;
                font-size: 28rpx;
            }
            .tips{
                font-size: 24rpx;
                text-align: right;
                width: 622rpx;
            }
        }
        .picBox{
            padding: 32rpx;
			box-sizing: border-box;
            width: 686rpx;
            display: flex;
            flex-wrap: wrap;
            .boxitem{
                width: 100%;
                
                // margin-right: 24rpx;
                // margin-bottom: 24rpx;
                display: flex;
                flex-wrap: wrap;
                justify-content: start;
                .imagebox{
                    width: 128rpx;
                    height: 128rpx;
                    position: relative;
                    display: inline-block;
                    margin-right: 10rpx;margin-bottom: 24rpx;
                    
                }
                .pic{
                    width: 128rpx;
                    height: 128rpx;
                    margin-left: 24rpx;margin-bottom: 24rpx;
                }
                .icon{
                    width: 36rpx;
                    height: 36rpx;
                    position: absolute;
                    right: -18rpx;
                    top: -18rpx;

                }
            }
        }
    }
    .btn{
        width:684rpx;
        height:80rpx;
        background:rgba(45,85,117,1);
        opacity:1;
        border-radius:8rpx;
        color: #fff;
        text-align: center;
        line-height: 80rpx;
        font-size: 28rpx;
        margin-top: 54rpx;
    }
}
</style>