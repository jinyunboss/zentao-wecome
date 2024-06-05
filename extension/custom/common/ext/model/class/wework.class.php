<?php


class weworkCommon extends commonModel
{
    /**
     * Juage a method of one module is open or not?
     *
     * @param string $module
     * @param string $method
     * @access public
     * @return bool
     */
    public function isOpenMethod(string $module, string $method): bool
    {
        if ($module == 'wework' and $method == 'login') return true;
        return parent::isOpenMethod($module, $method);
    }
}