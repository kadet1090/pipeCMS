<div id="slider">
    <div class="slider-items">
        <ul>
            <?php foreach($slides as $slide): ?>
            <li style="background: url('<?php echo $slide->img ?>')">
                <a href="<?php echo $slide->url ?>">
                    <span class="desc"><?php echo $slide->title ?></span>
                </a>
            </li>
            <?php endforeach; ?>
        </ul>
    </div>
    <nav>
        <a href="#t" class="back"><img src="<?php echo self::$templateDir ?>/img/ArrowLeft.png" alt="&raquo;" /></a>
        <ul>
            <?php for($count = count($slides), $i = 0; $i < $count; $i++): ?>
            <li><a href="#t"<?php if($i == 0): ?> class="active"<?php endif;?>><?php echo $i + 1; ?></a></li>
            <?php endfor; ?>
        </ul>
        <a href="#t" class="next"><img src="<?php echo self::$templateDir ?>/img/ArrowRight.png" alt="&raquo;" /></a>
    </nav>
</div>