// 图片预览
(function () {
    /**
     * 缺少一个提交上传图片的按钮,现在暂时的想法是提交提单,将所有的信息提交
     * 
     **/

    // 图片上传
    // var conval = '';
    listImg = []; // 存放图片 用于id
    imgdataArr = []; //存放图片 用于提交
    files_g = [];
    imgMsg = {};
    var base64Url;
    // input包裹
    var iploadimg = document.getElementsByClassName('iploadimg')[0];
    var form = document.getElementsByTagName('form')[4];
    // input
    var updataimg = document.getElementById('updataimg');
    // label上传按钮
    var labelup = document.getElementsByTagName('label')[0];

    labelup.addEventListener('change', function (e) {
        addImg(e);
    });


    // 上传图片
    function addImg(e) {
        var filenumMax = 2; //上传图片个数
        var formData = new FormData();
        var file = e.target.files || e.dataTransfer.files; //fileList
        console.log(file)

        if (!file || !window.FileReader) return; //如果不支持直接return
        if (/^image/.test(file[0].type)) {
            for (var i = 0, len = file.length; i < len; i++) {
                (function (f) {

                    var filereade = new FileReader(); //newFileReader对象,读取文件
                    console.log(filereade)
                    filereade.onload = function (evt) {
                        const files = e.srcElement.files[0];
                        files_g = files;
                        console.log(files_g);
                        var imgURL = window.URL.createObjectURL(files) // imgURL就是你的图片的本地路径，两部就能解决问题
                        console.log(imgURL);
                        base64Url = imgURL;
                        var filename = f.name; //文件名

                        imgMsg = {
                            name: filename,
                            base64Url: base64Url
                        };

                        //imgdataArr.push(imgMsg);

                        let fileStreamSize = calculaFileSize(base64Url);
                        let compressAfterImgUrl = "";
                        let compressAfterImgSize = "";
                        let newImg = createNewImg(base64Url);

                        var div = document.createElement("div");
                        var img = document.createElement("img");
                        var icons = document.createElement("i");
                        div.className = 'deposit';
                        icons.className = 'iconfont iconshanchu deleimg';
                        img.src = imgURL;
                        listImg.push(img);
                        console.log(listImg);
                        imgId(listImg);
                        div.appendChild(img);
                        div.appendChild(icons);
                        // 删除图片,用于传参

                        deleteImgIcon(icons, listImg);

                        var imglen = listImg.length;
                        console.log(imglen + '图片个数');
                        // 限制图片大小
                        if (fileStreamSize > 5242880) {
                            try { //图片过大可能压缩失败，抛出异常
                                ompressAfterImgUrl = compressImg(img);
                                compressAfterImgSize = calculaFileSize(compressAfterImgUrl);
                                return
                            } catch (error) {
                                compressAfterImgSize = base64Url;
                                alert("上传的图片过大，无法压缩，使用原图");
                            }
                        }
                        // 限制图片个数
                        if (imglen > filenumMax) {
                            alert(`你上传了${imglen}张图片,最多上传${filenumMax}张`);
                            moreimgdel(imglen, listImg);
                            return false;
                        }

                        iploadimg.insertBefore(div, form);

                        // 提交信息
                        formData.append("files_g", files_g);
                        $.ajax({
                            url: "/user/upfiles/upload",
                            type: 'POST',
                            contentType: false,
                            processData: false,
                            data: formData, // 图片
                            success: function (data) {
                                imgdataArr.push(data.url);
                            },
                            error: function (data) {
                                alert('上传失败');
                            }

                        });
                    }
                    filereade.readAsDataURL(f);
                    //console.log(filereade)

                })(e.target.files[i]);
            }
        } else {
            alert(`文件${file.name}不是图片`);
        }
    }


    //添加图片ID
    function imgId(listimg) {
        var ids = '';
        return ids = listimg.map(function (imgval, index) {
            if (index >= 4) return false;
            console.log(index + '--index--');
            return listimg[index].index = index;
        });
    }

    // 删除图片
    function deleteImgIcon(icon, listimg) {
        // console.log(icon)
        // var retuimg_id = imgId(listimg);
        //  console.log(retuimg_id);
        
        for (let i = 0, len = listimg.length; i < len; i++) {
            icon.i = i;
            icon.addEventListener('touchend', function (ev) {
                console.log(this.i)
                console.log(this)
                var span_parent = this.parentNode; //deposit
                console.log(span_parent);
                console.log(span_parent.parentNode)

                span_parent.remove(span_parent);
                span_parent.classList.remove("deposit");
                
                console.log(listImg);
                console.log(this.i);
                listimg.splice(this.i, 1);
                updataimg.value = ''; // 在删除图片函数中将,input的值置空,图片上传后删除,无法再上传的问题
                ev.preventDefault();
                
            }, false);
        }
    }

    // 上传图片多了删除图片
    function moreimgdel(imglen, listimg) {
        var retuimg_id = imgId(listimg);
        listimg.splice(imglen - 1, 1);
        updataimg.value = ''; // 在删除图片函数中将,input的值置空,图片上传后删除,无法再上传的问题
        console.log(updataimg)
    }

    // 压缩图片
    function compressImg(img) {
        let self = this;
        const maxSize = 200 * 1024; //200K
        const maxWidth = 640; //设置最大宽度
        const maxHeight = maxWidth; //设置最大高度
        let canvas = document.createElement('canvas');
        let ctx = canvas.getContext('2d');

        if (img.height > maxHeight) {
            //按最大高度等比缩放
            img.width *= maxHeight / img.height;
            img.height = maxHeight;
        }
        if (img.width > maxWidth) {
            //按最大宽度等比缩放
            img.height *= maxWidth / img.width;
            img.width = maxWidth;
        }
        canvas.width = img.width
        canvas.height = img.height

        const fileSize = calculaFileSize(base64Url);
        const compressRate = getCompressRate(maxSize, fileSize);
        const mineType = getBase64Type(base64Url)
        let data = canvas.toDataURL(mineType, 0.2) //data url的形式，压缩为20%
        return data;
    }

    //计算图片大小
    function calculaFileSize(base64) {
        // 计算base64文件流大小
        base64 = base64.substring(22)
        const equalIndex = base64.indexOf('=')
        if (base64.indexOf('=') > 0) {
            base64 = base64.substring(0, equalIndex)
        }
        var strLength = base64.length
        var fileLength = parseInt(strLength - (strLength / 8) * 2)
        return fileLength
    }

    //创建图像
    function createNewImg(base64Url) {
        // 创建一个新图像
        const imgWidth = 640;
        const imgHeight = 640;
        let img = new Image();
        img.src = base64Url;

        if (!img.width || img.width == 0) {
            // 有时图片会压缩失败出现无法获取的情况，也就没有宽和高
            img.width = imgWidth;
            img.height = imgHeight;
        }
        return img;
    }

    // 点击提交
    $('#subMess').bind('click', function () {

        if ($('.form_company_ipt').val() == '') {
            alert('企业称呼没写');
            return false;
        }
        if ($('.form_phone_ipt').val() == '') {
            alert('手机号没写');
            return false;
        }else {
            $.ajax({
                url: '',
                type: 'POST',
                cache: false, //上传文件不需要缓存
                data: {
                    form_name_ipt: $('.form_company_ipt').val(), //企业称呼
                    form_phone_ipt: $('.form_phone_ipt').val(), //手机号没写
                    form_area_ipt : localStorage.getItem('the-area'),
                    form_title_ipt : '企业入驻',
                    form_content_ipt: imgdataArr //图片信息
                }, //图片
                success: function (data) {
                    alert(data.text);
                    if (data.code===1) {
                        location.href='/';
                    }
                },
                error: function (data) {
                    alert('上传失败');
                }
    
            });
        } 
    });

})();