<?php
/**
 * 地图相关API
 */

namespace app\api\controller;
use app\common\controller\Common;

class Map extends Common
{
    /**
     * 通过百度地图API接口-经纬度获取地址信息
     * @param $lat 纬度
     * @param $lng 经度
     * @return mixed
     */
    public function baiduLocation($lat, $lng)
    {
        $key = $this->config['bdmap_key'];
        $url = "http://api.map.baidu.com/geocoder/v2/?callback=renderReverse&location=" . $lat . "," . $lng . "&output=json&pois=1&ak=" . $key;
        $fileContents = file_get_contents($url);
        $contents = ltrim($fileContents, "renderReverse&&renderReverse(");
        $contents = rtrim($contents, ")");
        echo $contents;
    }
}