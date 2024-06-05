

<script>
    // const redirect_uri = window.location.origin  + `<?php echo helper::createLink('wework','sync') ?>`

    var actionBar=document.getElementById("actionBar");

    var o = document.createElement("button");
    o.innerHTML = "同步企业微信用户"
    o.className = "btn danger"
    o.type = "button"
    o.addEventListener('click', function(e) {
        let _this = e.target;

        return window.open(window.location.origin  + `<?php echo helper::createLink('wework','sync') ?>`)
    })



    actionBar.appendChild(o,actionBar);
</script>