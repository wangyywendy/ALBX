<?php

require_once '../functions.php';

xiu_get_current_user();
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  if (!empty($_POST['site_logo'])) {
    xiu_execute(sprintf('update `options` set `value` = \'%s\' where `key` = \'site_logo\'', $_POST['site_logo']));
  }

  xiu_execute(sprintf('update `options` set `value` = \'%s\' where `key` = \'comment_status\'', !empty($_POST['comment_status'])));

  xiu_execute(sprintf('update `options` set `value` = \'%s\' where `key` = \'comment_reviewed\'', !empty($_POST['comment_reviewed'])));
}

$data = xiu_query('select * from options');
$options = array();

foreach ($data as $item) {
  $options[$item['key']] = $item['value'];
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Dashboard &laquo; Admin</title>
  <link rel="stylesheet" href="/static/assets/vendors/bootstrap/css/bootstrap.css">
  <link rel="stylesheet" href="/static/assets/vendors/font-awesome/css/font-awesome.css">
  <link rel="stylesheet" href="/static/assets/vendors/nprogress/nprogress.css">
  <link rel="stylesheet" href="/static/assets/css/admin.css">
  <script src="/static/assets/vendors/nprogress/nprogress.js"></script>
</head>
<body>
  <script>NProgress.start()</script>

  <div class="main">
    <?php include 'inc/navbar.php'; ?>

    <div class="container-fluid">
      <div class="page-title">
        <h1>我的个人资料</h1>
      </div>
      <!-- 有错误信息时展示 -->
      <!-- <div class="alert alert-danger">
        <strong>错误！</strong>发生XXX错误
      </div> -->
      <form class="form-horizontal">
        <div class="form-group">
          <label class="col-sm-3 control-label">头像</label>
          <div class="col-sm-6">
            <input id="site_logo" name="site_logo" type="hidden">
            <label class="form-image">
              <input id="avatar" type="file">
              <img src="<?php echo $options['site_logo']; ?>">
              <i class="mask fa fa-upload"></i>
            </label>
          </div>
        </div>
        <div class="form-group">
          <label for="email" class="col-sm-3 control-label">邮箱</label>
          <div class="col-sm-6">
            <input id="email" class="form-control" name="email" type="type" value="admin@sample.com" placeholder="邮箱" readonly>
            <p class="help-block">登录邮箱不允许修改</p>
          </div>
        </div>
        <div class="form-group">
          <label for="slug" class="col-sm-3 control-label">别名</label>
          <div class="col-sm-6">
            <input id="slug" class="form-control" name="slug" type="type" value="wendy" placeholder="slug">
            <p class="help-block">https://sample.com/author/<strong>zce</strong></p>
          </div>
        </div>
        <div class="form-group">
          <label for="nickname" class="col-sm-3 control-label">昵称</label>
          <div class="col-sm-6">
            <input id="nickname" class="form-control" name="nickname" type="type" value="wendy" placeholder="昵称">
            <p class="help-block">限制在 2-16 个字符</p>
          </div>
        </div>
        <div class="form-group">
          <label for="bio" class="col-sm-3 control-label">简介</label>
          <div class="col-sm-6">
            <textarea id="bio" class="form-control" placeholder="Bio" cols="30" rows="6">MAKE IT BETTER!</textarea>
          </div>
        </div>
        <div class="form-group">
          <div class="col-sm-offset-3 col-sm-6">
            <button type="submit" class="btn btn-primary">更新</button>
            <a class="btn btn-link" href="password-reset.php">修改密码</a>
          </div>
        </div>
      </form>
    </div>
  </div>

  <?php $current_page = 'profile'; ?>
  <?php include 'inc/sidebar.php'; ?>

  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script>
    $(function () {
      // 异步上传文件
      $('#avatar').on('change', function () {
        // 选择文件后异步上传文件
        var formData = new FormData()
        formData.append('file', $(this).prop('files')[0])

        // 上传图片
        $.ajax({
          url: '/admin/upload.php',
          cache: false,
          contentType: false,
          processData: false,
          data: formData,
          type: 'post',
          success: function (res) {
            if (res.success) {
              $('#site_logo').val(res.data)
              $('#avatar').siblings('img').attr('src', res.data).fadeIn()
            } else {
              alert('上传文件失败')
            }
          }
        })
      })
    })
  </script>
  <script>NProgress.done()</script>
</body>
</html>
