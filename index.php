<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <script src="https://d3js.org/d3.v3.min.js"></script>
    <title>Welcome</title>
</head>
<style>
    rect {
        fill: none;
        pointer-events: all;
    }
    .rabbit{
        position: absolute;
        z-index: 100
    }
    .test{
        position: absolute;
        left: 20%;
        z-index: 98
    }
    circle {
        fill: none;
        stroke-width: 3.5px;
    }
</style>
<body background="./imgs/us.jpg">
    <div class="rabbit">
        <p>
            <img id="img1" src="./imgs/hahaha.gif"/>
        </p>
    </div>
    <div class="test">
        <table width="500" border="2">
            <tr>
                <td height="100">
                    &nbsp;
                    <?php
                    $ipinfo = GetIpLookup();
                    var_dump($ipinfo);
                    echo $ipinfo['province'];
                    ?>
                </td>
            </tr>
        </table>
    </div>
    <script>
        var width= document.documentElement.clientWidth*0.98;
        var height= document.documentElement.clientHeight*0.98;
    //    setInterval("viewcheck();", 100);  //每隔100ms调整画布大小
        var i= 0;  //hsl变化初始值
        var img1= document.getElementById("img1");
        var imgratio= img1.width / img1.height;
        if(width > height){
            img1.width = width * 0.17;
            img1.height = img1.width / imgratio;
        }
        else{
            img1.height = height * 0.17;
            img1.width = img1.height * imgratio;
        }
        var svg= d3.select("body")
                    .append("svg")
                    .attr("width", width)
                    .attr("height", height)
                    .style("position", "absolute")
                    .style("z-index", 10);
        svg.append("rect")
            .attr("width", width)
            .attr("height", height)
            .on("click", drawcircle);

        window.onresize = function(){  //窗口大小改变监听器
            width= document.documentElement.clientWidth*0.98;
            height= document.documentElement.clientHeight*0.98;
            if(width > height){
                img1.width = width * 0.17;
                img1.height = img1.width / imgratio;
            }
            else{
                img1.height = height * 0.17;
                img1.width = img1.height * imgratio;
            }
            d3.select("svg")
                .attr("width", width)
                .attr("height", height);
            d3.select("rect")
                .attr("width", width)
                .attr("height", height);
        }

        function drawcircle(){
            var mouse= d3.mouse(this);
            var maxr= height*0.25;
            svg.insert("circle", "rect")
                .attr("cx", mouse[0])
                .attr("cy", mouse[1])
                .attr("r", 1e-6)
                .style("stroke", d3.hsl((i=(i+15)%360), 1, .5))
                .style("stroke-opacity", 1)
              .transition()
                .duration(2000)
                .ease(Math.sqrt)
                .attr("r", maxr)
                .style("stroke-opacity", 1e-6)
                .remove();

            d3.event.preventDefault();
        }

    </script>
    <?php
    //以下为获取ip函数
    function GetIp(){
        $realip = '';
        $unknown = 'unknown';
        if (isset($_SERVER)){
            if(isset($_SERVER['HTTP_X_FORWARDED_FOR']) && !empty($_SERVER['HTTP_X_FORWARDED_FOR']) && strcasecmp($_SERVER['HTTP_X_FORWARDED_FOR'], $unknown)){
                $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
                foreach($arr as $ip){
                    $ip = trim($ip);
                    if ($ip != 'unknown'){
                        $realip = $ip;
                        break;
                    }
                }
            }else if(isset($_SERVER['HTTP_CLIENT_IP']) && !empty($_SERVER['HTTP_CLIENT_IP']) && strcasecmp($_SERVER['HTTP_CLIENT_IP'], $unknown)){
                $realip = $_SERVER['HTTP_CLIENT_IP'];
            }else if(isset($_SERVER['REMOTE_ADDR']) && !empty($_SERVER['REMOTE_ADDR']) && strcasecmp($_SERVER['REMOTE_ADDR'], $unknown)){
                $realip = $_SERVER['REMOTE_ADDR'];
            }else{
                $realip = $unknown;
            }
        }else{
            if(getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), $unknown)){
                $realip = getenv("HTTP_X_FORWARDED_FOR");
            }else if(getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), $unknown)){
                $realip = getenv("HTTP_CLIENT_IP");
            }else if(getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), $unknown)){
                $realip = getenv("REMOTE_ADDR");
            }else{
                $realip = $unknown;
            }
        }
        $realip = preg_match("/[\d\.]{7,15}/", $realip, $matches) ? $matches[0] : $unknown;
        return $realip;
    }

    //以下为根据IP判断地区函数
    function GetIpLookup($ip = ''){
        if(empty($ip)){
            $ip = GetIp();
        }
        $res = @file_get_contents('http://int.dpool.sina.com.cn/iplookup/iplookup.php?format=js&ip=' . $ip);
        if(empty($res)){ return false; }
        $jsonMatches = array();
        preg_match('#\{.+?\}#', $res, $jsonMatches);
        if(!isset($jsonMatches[0])){ return false; }
        $json = json_decode($jsonMatches[0], true);
        if(isset($json['ret']) && $json['ret'] == 1){
            $json['ip'] = $ip;
            unset($json['ret']);
        }else{
            return false;
        }
        return $json;
    }
    ?>
</body>
</html>