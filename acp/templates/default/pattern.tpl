<!DOCTYPE html>
<html>
    <head>
        <meta charset="<?php echo $config->info->charset ?>" />
        <link rel="stylesheet" href="<?php echo self::$templateDir ?>/style.css" />
        <title><?php echo self::getTitle() ?></title>
    </head>
    <body>
        <header>
            <hgroup>
                <h1><?php echo $config->info->title ?></h1>
                <h2><?php echo language::get("acp"); ?></h2>
            </hgroup>
            <?php if($currentUser->isLogged): ?>
                <span id="user">
                    <span><?php echo language::get("hello") ?></span>, 
                    <a href="../<?php echo $router->prepareLink("user", "profile", $currentUser->login, $currentUser->id) ?>"><?php echo $currentUser->login ?></a>!
                    [ <a href="../"><?php echo language::get("leave") ?></a> ]
                </span>
            <?php endif; ?>
        </header>
        <nav id="top-menu">
            <ul>
                <li class="active more">
                    <a href="#">Menu 1</a>
                    <ul>
                        <li class="more">
                            <a href="#">Menu 1.1</a>
                            <ul>
                                <li><a href="#">Menu 1.1.1</a></li>
                                <li class="more">
                                    <a href="#">Menu 1.1.2</a>
                                    <ul>
                                        <li><a href="#">Menu 1.1.2.1</a></li>
                                        <li><a href="#">Menu 1.1.2.2</a></li>
                                    </ul>
                                </li>
                            </ul>
                        </li>
                        <li><a href="#">Menu 1.2</a></li>
                        <li><a href="#">Menu 1.2</a></li>
                    </ul>
                </li>
                <li><a href="#">Menu 2</a></li>
                <li><a href="#">Menu 3</a></li>
            </ul>
        </nav>
        <div id="main">
            <nav id="side-menu">
                <dl>
                    <dt>Menu</dt>
                    <dd><a href="#"><img src="<?php echo self::$templateDir ?>/img/ico/folder.png"/><span>Add</span></a></dd>
                    <dd><a href="#"><img src="<?php echo self::$templateDir ?>/img/ico/folder.png"/><span>Delete</span></a></dd>
                    <dd><a href="#"><img src="<?php echo self::$templateDir ?>/img/ico/folder.png"/><span>Brick</span></a></dd>
                    <dd><a href="#"><img src="<?php echo self::$templateDir ?>/img/ico/folder.png"/><span>Show</span></a></dd>
                    <dd><a href="#"><img src="<?php echo self::$templateDir ?>/img/ico/folder.png"/><span>List</span></a></dd>
                </dl>
                <dl>
                    <dt>Menu</dt>
                    <dd><a href="#"><img src="<?php echo self::$templateDir ?>/img/ico/folder.png"/><span>Add</span></a></dd>
                    <dd><a href="#"><img src="<?php echo self::$templateDir ?>/img/ico/folder.png"/><span>Delete</span></a></dd>
                    <dd><a href="#"><img src="<?php echo self::$templateDir ?>/img/ico/folder.png"/><span>Brick</span></a></dd>
                    <dd><a href="#"><img src="<?php echo self::$templateDir ?>/img/ico/folder.png"/><span>Show</span></a></dd>
                    <dd><a href="#"><img src="<?php echo self::$templateDir ?>/img/ico/folder.png"/><span>List</span></a></dd>
                </dl>
                <dl class="active">
                    <dt>Menu</dt>
                    <dd><a href="#"><img src="<?php echo self::$templateDir ?>/img/ico/folder.png"/><span>Add</span></a></dd>
                    <dd><a href="#"><img src="<?php echo self::$templateDir ?>/img/ico/folder.png"/><span>Delete</span></a></dd>
                    <dd><a href="#"><img src="<?php echo self::$templateDir ?>/img/ico/folder.png"/><span>Brick</span></a></dd>
                    <dd><a href="#"><img src="<?php echo self::$templateDir ?>/img/ico/folder.png"/><span>Show</span></a></dd>
                    <dd><a href="#"><img src="<?php echo self::$templateDir ?>/img/ico/folder.png"/><span>List</span></a></dd>
                </dl>
                <dl>
                    <dt>Menu</dt>
                    <dd><a href="#"><img src="<?php echo self::$templateDir ?>/img/ico/folder.png"/><span>Add</span></a></dd>
                    <dd><a href="#"><img src="<?php echo self::$templateDir ?>/img/ico/folder.png"/><span>Delete</span></a></dd>
                    <dd><a href="#"><img src="<?php echo self::$templateDir ?>/img/ico/folder.png"/><span>Brick</span></a></dd>
                    <dd><a href="#"><img src="<?php echo self::$templateDir ?>/img/ico/folder.png"/><span>Show</span></a></dd>
                    <dd><a href="#"><img src="<?php echo self::$templateDir ?>/img/ico/folder.png"/><span>List</span></a></dd>
                </dl>
            </nav>
            <article>
                <h1>Dashboard</h1>
                <section class="left">
                    <table>
                        <tr> <th>id</th> <th>title</th> <th>author</th> <th>tools</th> </tr>
                        <tr> <td>1.</td> <td>Lorem ipsum dolor sit amet.</td> <td>Kadet</td> <td></td> </tr>
                        <tr> <td>1.</td> <td>Lorem ipsum dolor sit amet.</td> <td>Kadet</td> <td></td> </tr>
                        <tr> <td>1.</td> <td>Lorem ipsum dolor sit amet.</td> <td>Kadet</td> <td></td> </tr>
                        <tr> <td>1.</td> <td>Lorem ipsum dolor sit amet.</td> <td>Kadet</td> <td></td> </tr>
                        <tr> <td>1.</td> <td>Lorem ipsum dolor sit amet.</td> <td>Kadet</td> <td></td> </tr>
                        <tr> <td>1.</td> <td>Lorem ipsum dolor sit amet.</td> <td>Kadet</td> <td></td> </tr>
                        <tr> <td>1.</td> <td>Lorem ipsum dolor sit amet.</td> <td>Kadet</td> <td></td> </tr>
                        <tr> <td>1.</td> <td>Lorem ipsum dolor sit amet.</td> <td>Kadet</td> <td></td> </tr>
                        <tr> <td>1.</td> <td>Lorem ipsum dolor sit amet.</td> <td>Kadet</td> <td></td> </tr>
                    </table>
                    <table>
                        <tr> <th>id</th> <th>title</th> <th>author</th> <th>tools</th> </tr>
                        <tr> <td>1.</td> <td>Lorem ipsum dolor sit amet.</td> <td>Kadet</td> <td></td> </tr>
                    </table>
                </section>
                <section class="right">
                    <div class="warning">
                        Oh noes! Some warning!
                    </div>
                    <div class="error">
                        Shit happens.
                    </div>
                    <div class="ok">
                        Good!
                    </div>
                    <div class="info">
                        Some strange news.
                    </div>
                    <table>
                        <tr> <th class="auto"></th> <th>title</th> <th>author</th> <th>tools</th> </tr>
                        <tr> <td><input type="checkbox" /></td> <td>Lorem ipsum dolor sit amet.</td> <td>Kadet</td> <td></td> </tr>
                        <tr> <td><input type="checkbox" /></td> <td>Lorem ipsum dolor sit amet.</td> <td>Kadet</td> <td></td> </tr>
                        <tr> <td><input type="checkbox" /></td> <td>Lorem ipsum dolor sit amet.</td> <td>Kadet</td> <td></td> </tr>
                        <tr> <td><input type="checkbox" /></td> <td>Lorem ipsum dolor sit amet.</td> <td>Kadet</td> <td></td> </tr>
                        <tr> <td><input type="checkbox" /></td> <td>Lorem ipsum dolor sit amet.</td> <td>Kadet</td> <td></td> </tr>
                        <tr> <td><input type="checkbox" /></td> <td>Lorem ipsum dolor sit amet.</td> <td>Kadet</td> <td></td> </tr>
                    </table>
                </section>
            </article>
        </div>
        <script src="<?php echo self::$templateDir ?>/js/jquery.js"></script>
        <script src="<?php echo self::$templateDir ?>/js/menu.js"></script>
    </body>
</html>