<?php

require_once '../functions.php';

xiu_get_current_user();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $author = $_POST['author'];
    $content = $_POST['content'];
    $created = $_POST['created'];
    $status = $_POST['status'];
  }
$comments = xiu_fetch_all("select * from comments where status = 'held' or status='rejected';");
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Comments &laquo; Admin</title>
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
        <h1>所有评论</h1>
      </div>
      <!-- 有错误信息时展示 -->
      <!-- <div class="alert alert-danger">
        <strong>错误！</strong>发生XXX错误
      </div> -->
      <div class="page-action">
        <!-- show when multiple checked -->
        <div class="btn-batch" style="display: none">
          <button id="btn-approve" class="btn btn-info btn-sm">批量批准</button>
          <button id="btn-reject" class="btn btn-warning btn-sm">批量拒绝</button>
           <a id="btn_delete" class="btn btn-danger btn-sm" href="/admin/comments-delete.php;" style="display: none">批量删除</a>
        </div>
        <ul class="pagination pagination-sm pull-right">
          <li><a href="#">上一页</a></li>
          <li><a href="#">1</a></li>
          <li><a href="#">2</a></li>
          <li><a href="#">3</a></li>
          <li><a href="#">下一页</a></li>
        </ul>
      </div>
      <table class="table table-striped table-bordered table-hover">
        <thead>
          <tr>
            <th class="text-center" width="40"><input type="checkbox"></th>
            <th>作者</th>
            <th>评论</th>
            <th>评论在</th>
            <th>提交于</th>
            <th>状态</th>
            <th class="text-center" width="100">操作</th>
          </tr>
        </thead>
        <tbody>
           <?php foreach ($comments as $item): ?>
          <tr class="danger">
            <td class="text-center"><input type="checkbox"></td>
            <td><?php echo $item['author']; ?></td>
            <td><?php echo $item['content']; ?></td>
            <td><?php echo $item['created']; ?></td>
            <td><?php echo $item['created']; ?></td>
            <td class="text-center"><?php echo $item['status']=='rejected'? '拒绝' : '待审核'; ?></td>
            <td class="text-center">
              <a href="/admin/comments.php" class="btn btn-info btn-xs">批准</a>
              <a href="/admin/comments-delete.php?id=<?php echo $item['id']; ?>" class="btn btn-danger btn-xs">删除</a>
            </td>
          </tr>
          <?php endforeach ?>
        </tbody>
      </table>
    </div>
  </div>

  <?php $current_page = 'comments'; ?>
  <?php include 'inc/sidebar.php'; ?>

  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script>
    $(function ($) {
      var $tbodyCheckboxs = $('tbody input')
      var $btnDelete = $('#btn_delete')
      var $btnApprove = $('#btn_approve')
      var $btnReject = $('#btn_reject')
      var allCheckeds = []
      $tbodyCheckboxs.on('change', function () {

        var id = $(this).data('id')
        if ($(this).prop('checked')) {
          allCheckeds.push(id)
        } else {
          allCheckeds.splice(allCheckeds.indexOf(id), 1)
        }
        allCheckeds.length ? $btnDelete.fadeIn() : $btnDelete.fadeOut()
        $btnDelete.prop('search', '?id=' + allCheckeds)
        allCheckeds.length ? $btnApprove.fadeIn() : $btnApprove.fadeOut()
        $btnDelete.prop('search', '?id=' + allCheckeds)
        allCheckeds.length ? $btnReject.fadeIn() : $btnReject.fadeOut()
        $btnDelete.prop('search', '?id=' + allCheckeds)
      })
    })
  </script>
  <script>NProgress.done()</script>
</body>
</html>
