<?php

class weworkModel extends model {

    /*
     *生成随机密码 ,pw_length 密码长度
     */
    function create_password($pw_length = 20)
    {
        $randpwd = '';
        for ($i = 0; $i < $pw_length; $i++)
        {
            $randpwd .= chr(mt_rand(33, 126));
        }
        return $randpwd;
    }

    /*
      使用企业微信用户ID和真实姓名创建禅道用户并设置随机密码
    */
    public function create_user($name,$account)
    {
        $password = $this->create_password(20);
        $user = new stdclass();
        $user->realname = $name;
        $user->account = $account;
        $user->password1 = $password;
        $user->password2 = $password;
        $user->visions = 'rnd,';
        // echo json_encode($user,JSON_PRETTY_PRINT);
        $this->dao->begin();
        $this->dao->insert(TABLE_USER)->data($user, 'password1,password2')
            ->checkIF($user->account, 'account', 'unique')
            ->checkIF($user->account, 'account', 'account')
            ->checkIF($user->email, 'email', 'email')
            ->autoCheck()
            ->exec();
        if(dao::isError()) return $this->rollback();

        $userID = $this->dao->lastInsertID();
        $this->dao->commit();
        return $userID;
    }


    public function bindWebhook()
    {

    }
}

?>