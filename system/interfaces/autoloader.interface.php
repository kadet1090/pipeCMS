<?php
/** interface klasy autoladera
  * @author kadet {@link mailto:kadet@pog.ugu.pl napisz do mnie!}
  * @copyright
  * @access public
  * @license  http://creativecommons.org/licenses/by-nc-sa/3.0/pl/{@link}
  * @link nextra.xaa.pl
  * @name autoLoader
  * @version 1.0
  * @package classInterface
  **/
interface autoloaderInterface
{
    public function ragister();
    public function load($className);
}
?>
