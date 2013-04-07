<?php
class notificationsController extends controller
{
    public function setRead($params = array(), $data = array())
    {
        $id = $params['page'];
        $model = new notificationsModel();
        $model->setRead($id);

        return self::message(__('success'), __('notificationMarkedAsRead'));
    }

    public static function push($receiver, $message, $type, $date, $prior = 5)
    {
        //if(in_array($type, array('info', 'warning', 'error', 'success'))) return false;
        $model = new notificationsModel();
        $model->push($receiver, $message, $type, $date, $prior);

        return true;
    }
}
?>  