<?php

require_once '../functions.php';

xiu_get_current_user();

function add_users () {
  if (empty($_POST['email']) || empty($_POST['slug']) || empty($_POST['nickname']) || empty($_POST['password'])) {
    $GLOBALS['message'] = '请完整填写表单！';
    $GLOBALS['success'] = false;
    return;
  }
  $sql = sprintf("insert into users values (null, '%s', '%s', '%s', '%s', null, null, 'unactivated')",
      $_POST['slug'],
      $_POST['email'],
      $_POST['password'],
      $_POST['nickname']
    );
    // 响应结果
    $message = xiu_execute($sql) > 0 ? '保存成功' : '保存失败';
  // 接收并保存
  // $email = $_POST['email'];
  // $slug = $_POST['slug'];
  // $nickname = $_POST['nickname'];
  // $password = $_POST['password'];
  // $status = "activated";

  // $rows = xiu_execute("insert into users values (null, '{$slug}', '{$email}', '{$password}', '{$nickname}', null, null, '{$status}');");

  $GLOBALS['success'] = $rows > 0;
  $GLOBALS['message'] = $rows <= 0 ? '添加失败！' : '添加成功！';
}

function edit_users () {
  $sql = sprintf("update users set slug = '%s', email = '%s', nickname = '%s' where id = %d",
      $_POST['slug'],
      $_POST['email'],
      $_POST['nickname'],
      $_POST['id']
    );
    // 响应结果
    $message = xiu_execute($sql) > 0 ? '保存成功' : '保存失败';
  // global $current_edit_users;

  // // 接收并保存
  // $id = $current_edit_users['id'];
  // $email = empty($_POST['email']) ? $current_edit_users['email'] : $_POST['email'];
  // // 同步数据
  // $current_edit_users['email'] = $email;
  // $slug = empty($_POST['slug']) ? $current_edit_users['slug'] : $_POST['slug'];
  // $current_edit_users['slug'] = $slug;
  // $nickname = empty($_POST['nickname']) ? $current_edit_users['nickname'] : $_POST['nickname'];
  // $current_edit_users['nickname'] = $nickname;
  // $password = empty($_POST['password']) ? $current_edit_users['password'] : $_POST['password'];
  // $current_edit_users['password'] = $password;

  // // insert into categories values (null, 'slug', 'name');
  // $rows = xiu_execute("update users set slug = '{$slug}', email = '{$email}', nickname = '{$nickname}', password = '{$password}' where id = {$id}");

  // $GLOBALS['success'] = $rows > 0;
  // $GLOBALS['message'] = $rows <= 0 ? '更新失败！' : '更新成功！';
}


if (empty($_GET['id'])) {
  // 添加
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    add_users();
  }
} else {
  //$current_edit_users = xiu_query('select * from users where id = ' . $_GET['id']);
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    edit_users();
  }
}

// 查询全部的分类数据
$users = xiu_query('select * from users;');
function convert_status ($status) {
  switch ($status) {
    case 'unactivated':
      return '未激活';
    case 'activated':
      return '已激活';
    case 'forbidden':
      return '禁止';
    case 'trashed':
      return '回收站';
    default:
      return '未知';
  }
}

?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Users &laquo; Admin</title>
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
        <h1>用户</h1>
      </div>
      <!-- 有错误信息时展示 -->
      <?php if (isset($message)) : ?>
      <div class="alert alert-<?php echo $message == '保存成功' ? 'success' : 'danger'; ?>">
        <strong><?php echo $message == '保存成功' ? '成功' : '错误'; ?>！</strong><?php echo $message; ?>
      </div>
      <?php endif; ?>
      <div class="row">
        <div class="col-md-4">
            <form actiob="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" autocomplete="off">
            <h2>添加新用户</h2>
            <div class="form-group">
              <label for="email">邮箱</label>
              <input id="email" class="form-control" name="email" type="email" placeholder="邮箱">
            </div>
            <div class="form-group">
              <label for="slug">别名</label>
              <input id="slug" class="form-control" name="slug" type="text" placeholder="slug">
              <p class="help-block">https://zce.me/author/<strong>slug</strong></p>
            </div>
            <div class="form-group">
              <label for="nickname">昵称</label>
              <input id="nickname" class="form-control" name="nickname" type="text" placeholder="昵称">
            </div>
            <div class="form-group">
              <label for="password">密码</label>
              <input id="password" class="form-control" name="password" type="text" placeholder="密码">
            </div>
            <div class="form-group">
              <button class="btn btn-primary btn-save" type="submit">添加</button>
              <button class="btn btn-default btn-cancel" type="button" style="display: none;">取消</button>
            </div>
          </form>
        </div>
        <div class="col-md-8">
          <div class="page-action">
            <!-- show when multiple checked -->
            <a id="btn_delete" class="btn btn-danger btn-sm" href="/admin/users-delete.php;" style="display: none">批量删除</a>
          </div>
          <table class="table table-striped table-bordered table-hover">
            <thead>
               <tr>
                <th class="text-center" width="40"><input type="checkbox"></th>
                <th class="text-center" width="80">头像</th>
                <th>邮箱</th>
                <th>别名</th>
                <th>昵称</th>
                <th>状态</th>
                <th class="text-center" width="100">操作</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($users as $item): ?>
              <tr data-id="<?php echo $item['id']; ?>">
                <td class="text-center"><input type="checkbox"></td>
                <td class="text-center"><img class="avatar" src="<?php echo empty($item['avatar']) ? '/static/assets/img/default.png' : $item['avatar']; ?>"></td>
                <td><?php echo $item['email']; ?></td>
                <td><?php echo $item['slug']; ?></td>
                <td><?php echo $item['nickname']; ?></td>
                <td><?php echo convert_status($item['status']); ?></td>
                <td class="text-center">
                  <a href="/admin/users.php?id=<?php echo $item['id']; ?>" class="btn btn-default btn-xs">编辑</a>
                  <a href="/admin/users-delete.php?id=<?php echo $item['id']; ?>;" class="btn btn-danger btn-xs">删除</a>
                </td>
              </tr>                 
              <?php endforeach ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <?php $current_page = 'users'; ?>
  <?php include 'inc/sidebar.php'; ?>

  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script>
    // 1. 不要重复使用无意义的选择操作，应该采用变量去本地化
    $(function ($) {
      // 在表格中的任意一个 checkbox 选中状态变化时
      var $tbodyCheckboxs = $('tbody input')
      var $btnDelete = $('#btn_delete')
      var $thCheckbox = $('th > input[type=checkbox]')

      // 定义一个数组记录被选中的
      var allCheckeds = []
      $tbodyCheckboxs.on('change', function () {

        var id = $(this).data('id')

        // 根据有没有选中当前这个 checkbox 决定是添加还是移除
        if ($(this).prop('checked')) {
          allCheckeds.push(id)
        } else {
          allCheckeds.splice(allCheckeds.indexOf(id), 1)
        }

        allCheckeds.length ? $btnDelete.fadeIn() : $btnDelete.fadeOut()
        $btnDelete.prop('search', '?id=' + allCheckeds)
      })

       $thCheckbox.on('change', function () {
        var checked = $(this).prop('checked')
        // 设置每一行的选中状态并触发 上面 👆 的事件
        $tbodyCheckbox.prop('checked', checked).trigger('change')
      })
        /**
       * slug 预览
       */
      $('#slug').on('input', function () {
        $(this).next().children().text($(this).val())
      })

      /**
       * 编辑分类
       */
      $('.btn-edit').on('click', function () {
        // 变量本地化（效率）
        var $tr = $(this).parent().parent()
        var $tds = $tr.children()

        // 拿到当前行数据
        var id = $tr.data('id')
        var email = $tds.eq(2).text()
        var slug = $tds.eq(3).text()
        var nickname = $tds.eq(4).text()

        // 将数据放到表单中
        $('#id').val(id)
        $('#email').val(email)
        $('#slug').val(slug).trigger('input')
        $('#nickname').val(nickname)
        $('#password').parent().hide()

        // 界面显示变化
        $('form > h2').text('编辑用户')
        $('form > div > .btn-save').text('保存')
        $('form > div > .btn-cancel').show()
      })

      /**
       * 取消编辑
       */
      $('.btn-cancel').on('click', function () {
        // 清空表单元素上的数据
        $('#id').val('')
        $('#email').val('')
        $('#slug').val('').trigger('input')
        $('#nickname').val('')
        $('#password').parent().show()

        // 界面显示变化
        $('form > h2').text('添加新用户')
        $('form > div > .btn-save').text('添加')
        $('form > div > .btn-cancel').hide()
      })

    })
  </script>
  <script>NProgress.done()</script>
</body>
</html>
