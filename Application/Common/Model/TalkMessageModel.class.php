<?php
/**
 * 所属项目 OnePlus.
 * 开发者: 想天
 * 创建日期: 3/18/14
 * 创建时间: 9:12 PM
 * 版权所有 想天工作室(www.ourstu.com)
 */

namespace Common\Model;


use Think\Model;

class TalkMessageModel extends Model
{
    protected $_auto = array(
        array('create_time', NOW_TIME, self::MODEL_INSERT),
        array('status', '1', self::MODEL_INSERT),
    );

    public function addMessage($content, $uid, $talk_id)
    {
        $message['content'] = op_t($content);
        $message['uid'] = $uid;
        $message['talk_id'] = $talk_id;
        $message = $this->create($message);
        return $this->add($message);
    }

    /**发小系统提示消息
     * @param $content 内容
     * @param $to_uids 发送过去的对象
     * @param $talk_id 消息id
     */
    public function sendMessage($content, $to_uids, $talk_id)
    {
        foreach ($to_uids as $uid) {
            /**
             * @param $to_uids 接受消息的用户ID
             * @param string $content 内容
             * @param string $title 标题，默认为  您有新的消息
             * @param $url 链接地址，不提供则默认进入消息中心
             * @param $int $from_uid 发起消息的用户，根据用户自动确定左侧图标，如果为用户，则左侧显示头像
             * @param int $type 消息类型，0系统，1用户，2应用
             */
            if ($uid != is_login()) {
                D('Message')->sendMessage($uid, '对话内容：' . op_t($content), '您有新的会话消息', U('UserCenter/Message/talk', array('talk_id' => $talk_id)), is_login(), 1);
            }
        }


    }
} 