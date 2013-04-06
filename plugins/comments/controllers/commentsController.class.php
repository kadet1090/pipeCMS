<?php
class commentsController extends controller
{
    public static $dir;

    public function __call($type, $arguments)
    {
        view::$robots = "all";
        $params = $arguments[0];

        $id = (int)$params['id'];
        $content = $params['content'];

        if(isset($content->additional_data) && isset($content->additional_data['comments']) && !$content->additional_data['comments']) return false;

        $model = new commentsModel();
        $comments = $model->getComments($id, $type);

        if(!$comments && !self::$user->hasPermission('comment/add')) return null;

        $view = new HTMLview('comments/list.tpl', self::$dir.'/template');
        $view->comments = $comments;
        $view->type = $type;
        $view->id = $id;
        return $view;
    }

    public function add($params, $data)
    {
        if(!self::$router->match('news')) throw new messageException(language::get('error'), language::get('errWrongURL'));
        if(!self::$user->hasPermission('comment/add')) throw new messageException(language::get('error'), language::get('errNoPermission'));

        $type = $params['name'];
        $id = $params['id'];

        $model = new commentsModel();
        $model->add($type, $id, $data['content'], self::$user->id, time());

        return controller::message(__('success'), __('commentAdded'));
    }

    public function delete($params, $data)
    {
        if(!self::$router->match('news')) throw new messageException(language::get('error'), language::get('errWrongURL'));
        if(!self::$user->hasPermission('comment/delete')) throw new messageException(language::get('error'), language::get('errNoPermission'));

        $type = $params['name'];
        $id = $params['id'];

        $model = new commentsModel();
        $model->delete($id);

        return controller::message(__('success'), __('commentDeleted'));
    }

    public function edit($params, $data)
    {
        if(!self::$router->match('news')) throw new messageException(language::get('error'), language::get('errWrongURL'));
        if(!self::$user->hasPermission('comment/edit')) throw new messageException(language::get('error'), language::get('errNoPermission'));

        $id = $params['id'];

        $model = new commentsModel();
        $comment = $model->get($id);

        if(!isset($data["submit"]))
        {
            $view = new HTMLview('comments/editor.tpl', self::$dir.'/template');
            $view->comment = $comment;

            return $view;
        }
        else
        {
            if(empty($data["content"])) throw new messageException(__('error'), __('emptyComment'));
            $model->edit($id, $data["content"]);

            return controller::message(__('success'), __('commentEdited'), array('url' => $data["referrer"]));
        }
    }
}
?>
