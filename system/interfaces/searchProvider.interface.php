<?php
/**
 * Created by JetBrains PhpStorm.
 *
 * @author Kadet <kadet1090@gmail.com>
 * @package Search
 * @license WTFPL
 */
interface searchProviderInterface {
    /**
     * @param string $query
     * @param int $page
     * @param array $config
     * @return view
     */
    public function getResults($query, $page = 1, $config = array());

    /**
     * @return view
     */
    public function getConfig();
}