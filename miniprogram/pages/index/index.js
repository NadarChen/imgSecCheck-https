Page({
  chooseImage: function() {
    wx.chooseImage({
        count: 1,
        sizeType: [ "compressed" ],
        sourceType: [ "album", "camera" ],
        success: function(e) {
            var a = e.tempFilePaths[0];
            wx.showLoading({
                title: "安全检测..."
            });
            wx.uploadFile({
                url: 'http://www.test.com/imgSecCheck.php', //仅为示例，非真实的接口地址
                filePath: a,
                name: 'img',
                formData:{
                    'user': 'test'
                },
                success: function(res) {
                    wx.hideLoading();
                    var data = JSON.parse(res.data);
                    console.log('testCode', res);
                    if (data.errcode === 87014) {
                        wx.showModal({
                            title: "请勿使用违法违规内容",
                            content: "图片含有违法违规内容",
                            showCancel: !1,
                            confirmText: "知道了"
                        }), console.log("内容安全检查不通过");
                    } else {
                      wx.showModal({
                        content: "图片OK"
                      })
                    }
                }
            })
        }
    });
  }
})
