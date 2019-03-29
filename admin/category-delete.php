<?php

/**
 * 根据客户端传递过来的ID删除对应数据
 */

require_once '../functions.php';

if (empty($_GET['id'])) {
  //exit('缺少必要参数');
  xiu_execute(sprintf('delete from categories where id in (%s)', $_GET['id']));
}

//$id = $_GET['id'];

//$rows = xiu_execute('delete from categories where id in (' . $id . ');');

// if ($rows > 0) {}
//header('Location: /admin/categories.php');

$target = empty($_SERVER['HTTP_REFERER']) ? '/admin/categories.php' : $_SERVER['HTTP_REFERER'];
header('Location: ' . $target);
