<form method="POST" action="<?php echo $router->prepareLink('comments', 'edit', $comment->content_type, $comment->id) ?>">
    <textarea id="content" name="content" rows="10"><?php echo $comment->content ?></textarea>
    <input type="hidden" value="<?php echo $_SERVER['HTTP_REFERER'] ?>" name="referrer" />
    <input type="submit" name="submit" class="tick" value="<?php echo _('save') ?>" />
</form>