<?php

/* 登录GET参数 */
$filter->wework = new stdclass();
$filter->wework->login = new stdclass();
$filter->wework->login->get['code'] = 'reg::any';
$filter->wework->login->get['state'] = 'reg::any';

$filter->wework->bind = new stdclass();
$filter->wework->bind->get['webhookID'] = 'reg::any';
