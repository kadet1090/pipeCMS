<h2><?php echo language::get('comments'); ?></h2>
<?php foreach($comments as $no => $comment): ?>
    <div class="content comments" id="comment-<?php echo $no+1 ?>">

        <span style="float: right">
            <?php echo $comment->author->login ?>
            | <?php echo date('d.m.Y H:i', $comment->date) ?>
            <?php if($currentUser->hasPermission('comment/delete')): ?>| <a href="<?php echo $router->prepareLink('comments', 'delete', $type, $comment->id) ?>"><?php echo _("delete") ?></a><?php endif; ?>
            <?php if($currentUser->hasPermission('comment/edit')): ?>| <a href="<?php echo $router->prepareLink('comments', 'edit', $type, $comment->id) ?>"><?php echo _("edit") ?></a><?php endif; ?>
            | <a href="#comment-<?php echo $no+1 ?>">#<?php echo $no+1 ?></a>
        </span>
        <?php echo BBcode::parse($comment->content); ?>
    </div>
<?php endforeach; ?>
<?php if($currentUser->hasPermission('comment/add')): ?>
<form method="POST" action="<?php echo $router->prepareLink('comments', 'add', $type, $id) ?>">
    <textarea id="content" name="content" rows="5"></textarea>
    <input type="submit" name="submit" class="tick" value="<?php echo __('add'); ?>" />
</form>
<?php endif; ?>