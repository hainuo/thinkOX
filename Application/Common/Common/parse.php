<?php
/**
 * Created by PhpStorm.
 * User: caipeichao
 * Date: 4/2/14
 * Time: 2:46 PM
 */

function parse_weibo_content($content)
{
    $content = shorten_white_space($content);
    $content = op_t($content);
    $content = parse_url_link($content);
    $content = parse_expression($content);
    $content = parse_at_users($content);
    return $content;
}

function parse_comment_content($content)
{
    //就目前而言，评论内容和微博的格式是一样的。
    return parse_weibo_content($content);
}

function shorten_white_space($content)
{
    $content = preg_replace('/\s+/', ' ', $content);
    return $content;
}

function parse_expression($content)
{
    return preg_replace_callback("/(\\[.+?\\])/is", 'parse_expression_callback', $content);
}

function parse_expression_callback($data)
{
    if (preg_match("/#.+#/i", $data[0])) {
        return $data[0];
    }
    $allexpression = D('Weibo/Expression')->getAllExpression();
    $info = $allexpression[$data[0]];
    if ($info) {
        return preg_replace("/\\[.+?\\]/i", "<img src='" . __ROOT__ . "/Public/static/image/expression/miniblog/" . $info['filename'] . "' />", $data[0]);
    } else {
        return $data[0];
    }
}

function parse_at_users($content)
{
    //找出被AT的用户
    $at_usernames = get_at_usernames($content);

    //将@用户替换成链接
    foreach ($at_usernames as $e) {
        $user = D('ucenter_member')->where(array('username' => $e))->find();
        if($user){
            $query_user = query_user(array('space_url'), $user['uid']);
            $content = str_replace("@$e ", "<a ucard=\"$user[id]\" href=\"$query_user[space_url]\">@$e </a>", $content);
        }
    }

    //返回替换的文本
    return $content;
}

function get_at_usernames($content)
{
    //正则表达式匹配
    $user_pattern = "/\\@([^\\#|\\s]+)\\s/";
    preg_match_all($user_pattern, $content, $users);

    //返回用户名列表
    return array_unique($users[1]);
}

function get_at_uids($content)
{
    $usernames = get_at_usernames($content);
    $result = array();
    foreach ($usernames as $username) {
        $user = D('User/UcenterMember')->where(array('username' => $username))->field('id')->find();
        $result[] = $user['id'];
    }
    return $result;
}

function parse_url_link($content)
{
    $content = preg_replace("#((http|https|ftp)://(\S*?\.\S*?))(\s|\;|\)|\]|\[|\{|\}|,|\"|'|:|\<|$|\.\s)#ie",
        "'<a href=\"$1\" target=\"_blank\"><i class=\"glyphicon glyphicon-link\" title=\"$1\"></i></a>$4'", $content
    );
    return $content;
}