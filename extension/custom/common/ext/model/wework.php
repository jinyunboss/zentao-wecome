<?php

/**
 * Juage a method of one module is open or not?
 *
 * @param  string $module
 * @param  string $method
 * @access public
 * @return bool
 */
public function isOpenMethod(string $module, string $method): bool
{
    return $this->loadExtension('wework')->isOpenMethod($module, $method);
}