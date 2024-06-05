<?php

class wework extends control
{

    public function __construct(string $moduleName = '', string $methodName = '')
    {
        parent::__construct($moduleName, $methodName);
        $this->loadModel('user');
        $this->loadModel('webhook');
    }

    /*
        通过企业微信应用的用户列表直接创建禅道用户
    */
    public function sync()
    {

        $this->app->loadClass('wechatapi', true);
        $wechatApi = new wechatapi($this->config->wework->corp_id, $this->config->wework->secret, $this->config->wework->agent_id);
        $AllUsers = $wechatApi->getAllUsers();
        var_dump($wechatApi->getErrors());
        foreach ( $AllUsers['data'] as $key => $value) {
            printf("姓名: %s, 企业微信ID: %s</br>",$key,$value);
            $data = $this->user->getById($value);
            if($data){
//                if($data->deleted){
//                    printf('用户: %s 存在系统中,但已被删除,需要重新创建...</br>',$key);
//                    $data->deleted = 0;
//                    $this->dao->delete()->from(TABLE_USER)->where('account')->eq($value)->exec();
//                    $created = $this->wework->create_user($key,$value);
//                    continue;
//                }
                printf('用户: %s 存在系统中,跳过创建...</br>',$key);
            }else{
                printf('用户: %s 不存在，需要创建...</br>',$key);

                $created = $this->wework->create_user($key,$value);

                if($created){
                    printf('用户: %s 创建成功，用户ID: %s</br>',$key,$created);
                }else{
                    printf('用户: %s 创建失败！！</br>',$key);
                }
            }
        }
    }

    /*
      直接将所有用户直接绑定到企业微信的webhook中
    */
    public function bind(): bool
    {
        if(!isset($_GET['webhookID'])) {
            echo "无法获取webhookid";
            return false;
        }

        $webhookID = $_GET['webhookID'];

        $allUser = $this->user->getList();
        $userList = array();
        foreach ($allUser as $user){
            $userList[] = $user->account;
        }


        if(!$userList) {
            echo "无法获取用户列表！！！";
            return false;
        }

        $this->dao->delete()->from(TABLE_OAUTH)
            ->where('providerType')->eq('webhook')
            ->andWhere('providerID')->eq($webhookID)
            ->andWhere('account')->in(array_keys($userList))
            ->exec();

        foreach($userList as  $userid)
        {
            printf('开始绑定用户: %s 到id为 %s的webhook应用</br>',$userid,$webhookID);
            if(empty($userid)) continue;

            $oauth = new stdclass();
            $oauth->account      = $userid;
            $oauth->openID       = $userid;
            $oauth->providerType = 'webhook';
            $oauth->providerID   = $webhookID;
            $this->dao->insert(TABLE_OAUTH)->data($oauth)->exec();
        }
        return !dao::isError();
    }

    public function login()
    {

        if(isset($_GET['code']) and isset($_GET['state'])) {
            if($this->get->state)   $state  = $this->get->state;
            if($this->get->code)   $code  = $this->get->code;
            if(empty($code)) echo 'code不能为空';
            if(empty($state)) echo 'state不能为空';

            if (empty($code)) return $this->locate($this->createLink('user', 'login'));

            $this->app->loadClass('wechatapi', true);
            $wechatApi = new wechatapi($this->config->wework->corp_id, $this->config->wework->secret, $this->config->wework->agent_id);
            $token = $wechatApi->getToken();
            $response = $wechatApi->queryAPI($wechatApi->apiUrl . "auth/getuserinfo?access_token=${token}&code=${code}");
            var_dump($wechatApi->getErrors());
            if ($wechatApi->isError()) return $this->locate($this->createLink('user', 'login'));;

            $this->userid = $response->userid;
            $user = $this->user->getById($this->userid);

            if($user){
                $account = $user->account;
                $this->user = $this->loadModel('user');

                $this->user->cleanLocked($account);
                $user->rights = $this->user->authorize($account);
                $user->groups = $this->user->getGroups($account);
                $this->session->set('user', $user);
                $this->app->user = $this->session->user;
                $this->loadModel('action')->create('user', $user->id, 'login');
                $this->loadModel('score')->create('user', 'login');
                /* Keep login. */
                if($this->post->keepLogin) $this->user->keepLogin($user);


                $this->locate($this->createLink('my', ));
            }else{
                $this->locate($this->createLink('user', 'login'));
            }

        }
    }


}