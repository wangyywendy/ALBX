<?php

require_once 'config.php';

/**
 * 封装大家公用的函数
 */
session_start();

// 定义函数时一定要注意：函数名与内置函数冲突问题
// JS 判断方式：typeof fn === 'function'
// PHP 判断函数是否定义的方式： function_exists('get_current_user')

/**
 * 获取当前登录用户信息，如果没有获取到则自动跳转到登录页面
 * @return [type] [description]
 */
function xiu_get_current_user () {
  if (isset($GLOBALS['current_user'])) {
    // 已经执行过了（重复调用导致）
    return $GLOBALS['current_user'];
  }

  // 启动会话
  //session_start();

  if (empty($_SESSION['current_logged_in_user_id']) || !is_numeric($_SESSION['current_logged_in_user_id'])) {
    // 没有登录标识就代表没有登录
    // 跳转到登录页
    header('Location: /admin/login.php');
    exit; // 结束代码继续执行
  }

  // 根据 ID 获取当前登录用户信息（定义成全局的，方便后续使用）
  $GLOBALS['current_user'] = xiu_query(sprintf('select * from users where id = %d limit 1', intval($_SESSION['current_logged_in_user_id'])))[0];

  return $GLOBALS['current_user'];
}

/**
 * 通过一个数据库查询获取多条数据
 * => 索引数组套关联数组
 */
// function xiu_fetch ($sql) {
//   $conn = mysqli_connect(XIU_DB_HOST, XIU_DB_USER, XIU_DB_PASS, XIU_DB_NAME);
//   if (!$conn) {
//     die('<h1>Connect Error (' . mysqli_connect_errno() . ') ' . mysqli_connect_error() . '</h1>');

//   $query = mysqli_query($conn, $sql);
//   if (!$query) {
//     // 查询失败
//     return false;
//   }

//   while ($row = mysqli_fetch_assoc($query)) {
//     $result[] = $row;
//   }

//   mysqli_free_result($query);
//   mysqli_close($conn);
//   mysqli_set_charset($conn, 'utf8');

//   return $result;
// }
/**
 * 根据配置文件信息创建一个数据库连接，注意用完以后需要关闭
 * @return mysqli 数据库连接对象
 */
function xiu_fetch () {
   $conn = mysqli_connect(XIU_DB_HOST, XIU_DB_USER, XIU_DB_PASS, XIU_DB_NAME);

  if (!$conn) {
    // 如果连接失败报错
    die('<h1>Connect Error (' . mysqli_connect_errno() . ') ' . mysqli_connect_error() . '</h1>');
  }

  // 设置数据库编码
  mysqli_set_charset($conn, 'utf8');

  return $conn;
}


/**
 * 获取单条数据
 * => 关联数组
 */
// function xiu_fetch_one ($sql) {
//   $res = xiu_fetch($sql);
//   return isset($res[0]) ? $res[0] : null;
// }

/**
 * 执行一个查询语句，返回查询到的数据（关联数组混合索引数组）
 * @param  string $sql 需要执行的查询语句
 * @return array       查询到的数据（二维数组）
 */
function xiu_query ($sql) {
  // 获取数据库连接
  $conn = xiu_fetch();

  // 定义结果数据容器，用于装载查询到的数据
  $data = array();

  // 执行参数中指定的 SQL 语句
  if ($query = mysqli_query($conn, $sql)) {
    // 查询成功，则获取结果集中的数据

    // 遍历每一行的数据
    while ($row = mysqli_fetch_array($query)) {
      // 追加到结果数据容器中
      $data[] = $row;
    }

    // 释放结果集
    mysqli_free_result($query);
  }

  // 关闭数据库连接
  mysqli_close($conn);

  // 返回容器中的数据
  return $data;
}

/**
 * 执行一个增删改语句
 */
function xiu_execute ($sql) {
  $conn = mysqli_connect(XIU_DB_HOST, XIU_DB_USER, XIU_DB_PASS, XIU_DB_NAME);
  if (!$conn) {
    exit('连接失败');
  }

  $query = mysqli_query($conn, $sql);
  if (!$query) {
    // 查询失败
    return false;
  }

  // 对于增删修改类的操作都是获取受影响行数
  $affected_rows = mysqli_affected_rows($conn);

  mysqli_close($conn);

  return $affected_rows;
}

/**
 * 输出分页链接
 * @param  integer $page    当前页码
 * @param  integer $total   总页数
 * @param  string  $format  链接模板，%d 会被替换为具体页数
 * @param  integer $visible 可见页码数量（可选参数，默认为 5）
 * @example
 *   <?php xiu_pagination(2, 10, '/list.php?page=%d', 5); ?>
 */
function xiu_pagination ($page, $total, $format, $visible = 5) {
  // 计算起始页码
  // 当前页左侧应有几个页码数，如果一共是 5 个，则左边是 2 个，右边是两个
  $left = floor($visible / 2);
  // 开始页码
  $begin = $page - $left;
  // 确保开始不能小于 1
  $begin = $begin < 1 ? 1 : $begin;
  // 结束页码
  $end = $begin + $visible - 1;
  // 确保结束不能大于最大值 $total
  $end = $end > $total ? $total : $end;
  // 如果 $end 变了，$begin 也要跟着一起变
  $begin = $end - $visible + 1;
  // 确保开始不能小于 1
  $begin = $begin < 1 ? 1 : $begin;

  // 上一页
  if ($page - 1 > 0) {
    printf('<li><a href="%s">&laquo;</a></li>', sprintf($format, $page - 1));
  }

  // 省略号
  if ($begin > 1) {
    print('<li class="disabled"><span>···</span></li>');
  }

  // 数字页码
  for ($i = $begin; $i <= $end; $i++) {
    // 经过以上的计算 $i 的类型可能是 float 类型，所以此处用 == 比较合适
    $activeClass = $i == $page ? ' class="active"' : '';
    printf('<li%s><a href="%s">%d</a></li>', $activeClass, sprintf($format, $i), $i);
  }

  // 省略号
  if ($end < $total) {
    print('<li class="disabled"><span>···</span></li>');
  }

  // 下一页
  if ($page + 1 <= $total) {
    printf('<li><a href="%s">&raquo;</a></li>', sprintf($format, $page + 1));
  }
}
