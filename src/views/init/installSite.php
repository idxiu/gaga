<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>login</title>
    <!-- Latest compiled and minified CSS -->
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <link rel="stylesheet" href="../../public/css/init.css">
</head>
<body>

<div class="zaly_container" >
    <div class="zaly_login zaly_login_by_phone">
        <div class="" style="height:8rem;background-color: #6B52FF; text-align: center;">

            <div style="font-size:2.25rem;line-height: 8rem;font-family:PingFangSC-Regular;font-weight:500;color: #FFFFFF;">
                检测站点信息
            </div>
        </div>

        <div class="initDiv " style="margin-top:2rem;" >

            <div class=" d-flex flex-row justify-content-between margin-top3 ext_open_ssl" isLoad="<?php echo $isLoadOpenssl;?>" >
                1. PHP版本大于7.0.0 （后续版本会支持5.x.x）
                <?php if($isPhpVersionValid){ echo " <img src='../../public/img/msg/member_select.png' style='margin-left: 3rem;width: 1.5rem;height: 1.5rem;'/>
"; } else { echo "<img src='../../public/img/msg/btn-x.png' style='margin-left: 3rem;width: 1.5rem;height: 1.5rem;' />" ;}?>

            </div>

            <div class=" d-flex flex-row justify-content-between margin-top3 ext_open_ssl" isLoad="<?php echo $isLoadOpenssl;?>" >
                2. 有效支持OpenSSL（某些Windows版本PHP集成的Openssl有Bug）
                   <?php if($isLoadOpenssl==1){ echo " <img src='../../public/img/msg/member_select.png' style='margin-left: 3rem;width: 1.5rem;height: 1.5rem;'/>
"; } else { echo "<img src='../../public/img/msg/btn-x.png' style='margin-left: 3rem;width: 1.5rem;height: 1.5rem;' />" ;}?>

            </div>

            <div class=" d-flex flex-row justify-content-left margin-top3 ext_pdo_sqlite" isLoad="<?php echo $isLoadPDOSqlite;?>" >
                3. 是否安装PDO_Sqlite
                <?php if($isLoadPDOSqlite==1){ echo " <img src='../../public/img/msg/member_select.png' style='margin-left: 3rem;width: 1.5rem;height: 1.5rem;'/>
"; } else { echo "<img src='../../public/img/msg/btn-x.png' style='margin-left: 3rem;width: 1.5rem;height: 1.5rem;' />" ;}?>

            </div>

            <div class=" d-flex flex-row justify-content-left margin-top3 ext_curl"  isLoad="<?php echo $isLoadCurl;?>" >
                4. 是否安装Curl
                <?php if($isLoadCurl==1){ echo " <img src='../../public/img/msg/member_select.png' style='margin-left: 3rem;width: 1.5rem;height: 1.5rem;'/>
"; } else { echo "<img src='../../public/img/msg/btn-x.png' style='margin-left: 3rem;width: 1.5rem;height: 1.5rem;' />" ;}?>

            </div>

            <div class=" d-flex flex-row justify-content-left margin-top3 ext_is_write"  isLoad="<?php echo $isWritePermission;?>" >
                5. 当前目录写权限
                <?php if($isWritePermission==1){ echo " <img src='../../public/img/msg/member_select.png' style='margin-left: 3rem;width: 1.5rem;height: 1.5rem;'/>
"; } else { echo "<img src='../../public/img/msg/btn-x.png' style='margin-left: 3rem;width: 1.5rem;height: 1.5rem;' />" ;}?>

            </div>

            <div class="d-flex flex-row input_div justify-content-between margin-top3" >
                6. 请选择登录方式：<select id="verifyPluginId">
                    <option pluginId="105">本地账户密码校验</option>
                    <option pluginId="100">平台校验</option>
                </select>
            </div>


            <div class="d-flex flex-row justify-content-center ">
                <button type="button" class="btn login_button" ><span class="span_btn_tip">初始化数据</span></button>
            </div>
        </div>
    </div>

</div>


<script src="../../public/js/jquery.min.js"></script>
<script>
    $(document).on("click", ".login_button",function () {
         var isLoadOpenssl = $(".ext_open_ssl").attr("isLoad");
         var isPdoSqlite = $(".ext_pdo_sqlite").attr("isLoad");
         var isCurl = $(".ext_curl").attr("isLoad");
         var isWrite = $(".ext_is_write").attr("isLoad");

         if(isLoadOpenssl != 1) {
            alert("请先安装openssl");
            return false;
         }
         if(isPdoSqlite != 1) {
            alert("请先安装pdo_sqlite");
            return false;
         }
         if(isCurl != 1) {
            alert("请先安装is_curl");
            return false;
         }

         if(isWrite != 1) {
             alert("当前目录不可写");
             return false;
         }

        var selector = document.getElementById('verifyPluginId');
        var pluginId = $(selector[selector.selectedIndex]).attr("pluginId");
        var data = {
            pluginId : pluginId,
        };
        $.ajax({
            method: "POST",
            url:"./index.php?action=installDB",
            data: data,
            success:function (resp) {
                console.log("init db sqlite " + resp);
                if(resp == "success") {
                    window.location.href="./index.php?action=page.logout";
                } else {
                    alert("初始化失败");
                }
            }
        });
    });
    </script>
</body>
</html>
