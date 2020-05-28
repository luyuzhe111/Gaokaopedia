<script>
	export default {
		onLaunch: function() {
			console.log('App Launch')
			
		},
		onShow: function() {
			uni.hideTabBar({
			
			})
			console.log('App Show')
		},
		onHide: function() {
			console.log('App Hide')
		},
		post: function(url, data,method) {
			var promise = new Promise((resolve, reject) => {
				//init
				let that = this,
					token = uni.getStorageSync('token'),
					header = {
						 'token': token || ''
					},
					postData;
				//网络请求
				uni.request({
					url: this.globalData.baseUrl + url,
					data: data,
					method: method,
					header: header,
					success: function(res) {
						console.log(res)
						
						//返回取得的数据
						if (res.data.code == '1') {
							resolve(res.data.data);
						}else {
							// uni.showToast({
							// 	title: res.data.msg,
							// 	icon: 'none'
							// });
							reject(res.data);
						}
					},
					fail: function(e) {
						reject('网络出错');
						uni.hideNavigationBarLoading();
					}
				});
			});
			return promise;
		},


		upload(filetype, file) {
		var promise = new Promise((resolve, reject) => {
			wx.showNavigationBarLoading()
			wx.showLoading({
			title: '上传中',
			})
			let url = 'https://school.t.brotop.cn/api/common/upload';
			let head = {
			'token':uni.getStorageSync('token'),
			'XX-Device-Type': ''
			}
			console.log(head)
			let typename = {
			filetype: filetype
			}
			wx.uploadFile({
			url: url, //仅为示例，非真实的接口地址
			filePath: file,
			name: 'file',
			header: head,
			formData: typename,
			success: function (res) {
				console.log(res)
				console.log('上传文件后', res)
				let temdata = JSON.parse(res.data);
				console.log(temdata)
				let urlobj = {
				url: temdata.data.url,
				kurl: temdata.data.save_path
				}
				resolve(urlobj);
	
			},
			fail: function (res) {
				reject('网络出错');
				wx.hideNavigationBarLoading()
				wx.hideLoading()
			},
			complete: () => {
				wx.hideNavigationBarLoading()
				wx.hideLoading()
			},
			})
		});
		return promise;
		},
		globalData: {
			userInfo: null,
			baseUrl: 'https://school.t.brotop.cn/api/',
			imageBaseUrl: 'http://school.t.brotop.cn'
		},
	}
</script>

<style lang="less">
	.textblock{
		height:auto;
		display: -webkit-box;
		-webkit-box-orient: vertical;
		-webkit-line-clamp: 3;
		overflow: hidden;
	}
	/*每个页面公共css */
	image{
		width:100%;
		height:100%;
	}
	.nodata{
		color:#999;
		font-size: 32rpx;
		text-align: center;
		margin :100rpx auto 0;
	}
	button::after{
		border:none;
		outline:none;
	}
.teacherfooter {
		width: 750rpx;
		display: flex;
		align-items: center;
		justify-content: space-between;
		padding: 18rpx 82rpx;
		box-sizing: border-box;
		position: fixed;
		bottom: 0;
		left: 0;
		background: #fff;
		font-size: 22rpx;
		.teacherfootitem {
			display: felx;
			flex-direction: column;
			align-items: center;
			justify-content: center;
			.teacherfootitemtop {
				margin: 0 auto;
				display: flex;
				justify-content: center;
				image {
					width: 48rpx;
					height: 48rpx;
					margin: 0 auto;
				}
			}
			.teacherfootname{
				margin-top:20rpx;
			}
		}
	}

.collegefooter{
	width: 750rpx;
		display: flex;
		align-items: center;
		justify-content: space-between;
		padding: 18rpx 82rpx;
		box-sizing: border-box;
		position: fixed;
		bottom: 0;
		left: 0;
		background: #fff;
		font-size: 22rpx;
		.teacherfootitem {
			display: felx;
			flex-direction: column;
			align-items: center;
			justify-content: center;
			width: 50%;
			text-align: center;
			.teacherfootitemtop {
				margin: 0 auto;
				display: flex;
				justify-content: center;
				image {
					width: 48rpx;
					height: 48rpx;
					margin: 0 auto;
				}
			}
		}
}	

	

	.shou {
		width: 52rpx;
		height: 37rpx;
		font-size: 0;
	}

	
	.selactive{
		color:#2D5575
	}
</style>
