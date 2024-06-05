

<script src="https://wwcdn.weixin.qq.com/node/open/js/wecom-jssdk-1.3.1.js"></script>
<script>
    const redirect_uri = window.location.origin  + `<?php echo helper::createLink('wework','login') ?>`

    var submit=document.getElementById("submit");
    var loginPanel=document.getElementById("loginPanel");

    var o = document.createElement("button");
    o.innerHTML = "企业微信登录"
    o.className = "btn success"
    o.type = "button"

    o.addEventListener("click", () => {
        // 获取当前时间的时间戳（毫秒）
        const timestamp = Date.now();
        // 将时间戳转换为字符串
        const timestampString = timestamp.toString();

        // 初始化登录组件
        const wwLogin = ww.createWWLoginPanel({
            el: '#ww_login',
            params: {
                login_type: 'CorpApp',
                appid: "<?php
                    global $config;
                    global $app;
                    $app->loadConfig('wework');
                    echo $config->wework->corp_id;
                    ?>",
                agentid: "<?php
                    global $config;
                    global $app;
                    $app->loadConfig('wework');
                    echo $config->wework->agent_id;
                    ?>",
                redirect_uri: redirect_uri,
                state: timestampString,
                redirect_type: 'callback',
            },
            onCheckWeComLogin({ isWeComLogin }) {
                console.log(isWeComLogin)
            },
            onLoginSuccess({ code }) {

                console.log(`重定向url: ${redirect_uri}?code=${code}&state=${timestampString}` )
                setTimeout(function() {
                    // 在这里放置你想要延迟执行的代码
                    window.location.href = `${redirect_uri}?code=${code}&state=${timestampString}`
                }, 1000); // 1000 毫秒等于 1 秒


            },
            onLoginFail(err) {
                console.log(err)
            },
        })
        o.disabled = true;
    });

    var d = document.createElement("div");
    d.type = "div"
    d.id = 'ww_login'

    submit.parentNode.insertBefore(o,submit);
    submit.parentNode.parentNode.appendChild(d,submit);
</script>