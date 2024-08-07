<?php

/**
 * Juage a method of one module is open or not?
 *
 * @param  string $module
 * @param  string $method
 * @access public
 * @return bool
 */
public function isOpenMethod($module,$method): bool
{
    return $this->loadExtension('wework')->isOpenMethod($module, $method);
}
public function checkIframe($whitelist = null)
{
    return $this->loadExtension('wework')->checkIframe($whitelist);

}
