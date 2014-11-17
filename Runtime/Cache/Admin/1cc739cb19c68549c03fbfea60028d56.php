<?php if (!defined('THINK_PATH')) exit();?><!doctype html>
<html lang="zh-CN">
    <head>
        <meta charset="UTF-8">
        <title><?php echo C('WEB_SITE_TITLE');?></title>
        <meta name="keywords" content="baby" />
        <meta name="description" content="baby" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="renderer" content="webkit">

    </head>
    <body>
        <div class="container">
            


    <form class="form-signin" role="form">
        <h3 class="form-signin-heading" style="text-align: center;"><span class="icon-desktop"></span> baby管理系统</h3>
        <div class="form-group">
            <div class="input-group input-group-lg">
                <span class="input-group-addon"><span class="glyphicon glyphicon-user"></span></span>
                <input type="text" class="form-control" placeholder="用户名">
            </div>
        </div>
        <div class="form-group">
            <div class="input-group input-group-lg">
                <span class="input-group-addon"><span class="icon-key"></span></span>
                <input type="text" class="form-control" placeholder="密码">
            </div>
        </div>
        <div class="form-group">
            <div class="input-group input-group-lg">
                <span class="input-group-addon"><span class="icon-login-verifycode"></span></span>
                <input type="text" class="form-control" placeholder="请输入验证码">
            </div>
        </div>
        <div class="form-group">
            <div class="input-group input-group-lg">
                <img onclick="this.src=this.src+'?'+Math.random()" src="<?php echo U('/Admin/User/createVerify');?>" class="img-responsive" alt="Responsive image"> 
            </div>
        </div>
        <div class="form-group">
            <div class="checkbox">
                <label>
                    <input type="checkbox" value=""> 记住登录
                </label>
            </div>
        </div>
        <button class="btn btn-lg btn-primary btn-block" type="submit">登录</button>

    </form>



    <div ng-app="">
        <p>在输入框中尝试输入：</p>
        <p>姓名：<input type="text" ng-model="name"></p>
        <p ng-bind="name"></p>
    </div>


            

            
        </div>
        <link rel="stylesheet" type="text/css" href="/Public/Common/bootstrap/css/bootstrap.min.css" media="all">
        <link rel="stylesheet" type="text/css" href="/Public/Common/bootstrap/css/font-awesome.min.css" media="all">
        <link rel="stylesheet" type="text/css" href="/Public/Common/bootstrap/css/bootstrap-theme.min.css" media="all">
        <script type="text/javascript" src="/Public/Common/angularjs/angular.min.js"></script>
        <script type="text/javascript" src="/Public/Common/jquery-1.11.1.min.js"></script>
        <script type="text/javascript" src="/Public/Common/bootstrap/js/bootstrap.min.js"></script>
    
    <script type="text/javascript">
        function formController($scope) {
            $scope.master = {firstName: "John", lastName: "Doe"};
            $scope.reset = function () {
                $scope.user = angular.copy($scope.master);
            };
            $scope.reset();
        }
        ;
    </script>

</body>
</html>