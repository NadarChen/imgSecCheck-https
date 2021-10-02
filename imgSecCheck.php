<?php

$file = $_FILES["img"];
uploadImg($file);


function getToken()
{
    $wxAppID = '';
    $wxAppSecret = '';
    $grantUrl = sprintf(
        'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=%s&secret=%s',
        $wxAppID,
        $wxAppSecret
    );

    $res = json_decode(curl_get($grantUrl));
    $access_token = $res->access_token;
    return $access_token;
}

function checkContent($token, $media)
{
    $imgSecCheckUrl = "https://api.weixin.qq.com/wxa/img_sec_check?access_token=" . $token;
    return curl_post($imgSecCheckUrl, $media);
}

function uploadImg($file)
{
    // 图片存放位置
    $img_path = realpath("C:\\dev\\upload_tmp\\");

    // 先判断有没有错
    if ($file["error"] == 0) {
        // 成功 
        // 判断传输的文件是否是图片，类型是否合适
        // 获取传输的文件类型
        $typeArr = explode("/", $file["type"]);
        if($typeArr[0]== "image"){
            // 如果是图片类型
            $imgType = array("png","jpg","jpeg","gif");
            if (in_array($typeArr[1], $imgType)) { // 图片格式是数组中的一个
                // 类型检查无误，保存到文件夹内
                // 给图片定一个新名字
                $basename = date("Y-m-d").'_'.time().".".$typeArr[1];
                $image = $img_path.'\\'.$basename;

                $bol = move_uploaded_file($file["tmp_name"], $image);
                if ($bol) {
                    // 获取access_token
                    $token = getToken();

                    $data = new \CURLFile(realpath($image));
                    $data->setMimeType("image/png");
                    $postdata['media'] = $data;

                    $checkRes = checkContent($token, $postdata);

                    print_r($checkRes);
                } else {
                    echo "上传失败！";
                };
            };
        } else {
            // 不是图片类型
            echo "没有图片，再检查一下吧！";
        };
    } else {
        // 失败
        echo $file["error"];
    };

}


function curl_get($url)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    //不做证书校验,部署在linux环境下请改为true
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
    $file_contents = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return $file_contents;
}

function curl_post($url, array $params = array())
{
    // $data_string = json_encode($params);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
    curl_setopt(
        $ch, CURLOPT_HTTPHEADER,
        array(
            'Content-Type: multipart/form-data'
        )
    );
    $data = curl_exec($ch);
    curl_close($ch);
    return ($data);
}