<?php
/** interfejs klasy mapy plików
  * @author kadet {@link mailto:kadet@pog.ugu.pl napisz do mnie!}
  * @copyright
  * @access public
  * @license  http://creativecommons.org/licenses/by-nc-sa/3.0/pl/{@link}
  * @link nextra.xaa.pl
  * @name autoLoader
  * @version 1.0
  * @package classInterface
  **/
interface classMapInterface
{
    public function getMap();
    public function saveMapToFile($fileName);
    public function loadMapFromFile($fileName);
}
?>
