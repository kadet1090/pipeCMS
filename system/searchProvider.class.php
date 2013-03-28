<?php
/**
 * Created by JetBrains PhpStorm.
 *
 * @author Kadet <kadet1090@gmail.com>
 * @package 
 * @license WTFPL
 */

abstract class searchProvider implements searchProviderInterface {
    public $resultsTemplate = '';
    public $configTemplate = '';

    public $name = '';

    public function getConfig() {
        return null;
    }
}