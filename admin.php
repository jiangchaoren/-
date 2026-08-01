<?php
include 'config.php';

$cfg = $db->querySingle("SELECT * FROM config WHERE id=1", true);

if(isset($_POST['pwd'])){
    $captcha_ok = isset($_SESSION['captcha']) && strtoupper(trim($_POST['captcha'] ?? '')) === $_SESSION['captcha'];
    $user_ok = ($_POST['user'] ?? '') === ADMIN_USER;

    if(!$captcha_ok){
        $msg = '验证码错误';
    } elseif(!$user_ok){
        $msg = '用户名或密码错误';
    } elseif($_POST['pwd'] === ADMIN_PWD){
        $_SESSION['admin_login'] = true;
        unset($_SESSION['captcha']);
        header('Location:admin.php');exit;
    } else {
        $msg = '用户名或密码错误';
    }
}

if(isset($_GET['act']) && $_GET['act'] === 'logout'){
    session_destroy();
    header('Location:admin.php');exit;
}

if(!is_login()){
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>管理后台 - 登录</title>
<style>
*{margin:0;padding:0;box-sizing:border-box;font-family:-apple-system,BlinkMacSystemFont,"Segoe UI","Roboto",sans-serif}
body{display:flex;min-height:100vh;background:#f0f2f5}

/* 左侧品牌区 */
.login-side{
  width:380px;background:#1a1d29;display:flex;flex-direction:column;
  align-items:center;justify-content:center;padding:40px;flex-shrink:0;
}
.login-side .brand-icon{width:64px;height:64px;margin-bottom:20px}
.login-side h1{color:#fff;font-size:24px;font-weight:700;margin-bottom:8px}
.login-side p{color:rgba(255,255,255,0.5);font-size:14px;text-align:center}
/* 右侧登录区 */
.login-main{flex:1;display:flex;align-items:center;justify-content:center;padding:40px}
.login-card{width:380px}
.login-card h2{font-size:22px;font-weight:700;color:#1a1d29;margin-bottom:4px}
.login-card .login-sub{font-size:13px;color:#888;margin-bottom:28px}
.form-group{margin-bottom:18px}
.form-group label{display:block;font-size:13px;font-weight:500;color:#555;margin-bottom:6px}
.form-group input{
  width:100%;padding:10px 14px;border:1px solid #e0e0e0;border-radius:8px;
  font-size:14px;outline:none;transition:all 0.15s;background:#fafbfc;
}
.form-group input:focus{border-color:#6366f1;box-shadow:0 0 0 3px rgba(99,102,241,0.1);background:#fff}
.captcha-row{display:flex;gap:10px;align-items:center}
.captcha-row input{flex:1}
.captcha-row img{border-radius:6px;cursor:pointer;height:44px;border:1px solid #e0e0e0}
.captcha-row a{font-size:12px;color:#6366f1;text-decoration:none;cursor:pointer;white-space:nowrap}
.btn-login{
  width:100%;padding:11px;background:linear-gradient(135deg,#6366f1,#a78bfa);
  color:#fff;border:none;border-radius:8px;font-size:15px;font-weight:500;
  cursor:pointer;transition:opacity 0.15s;margin-top:6px;
}
.btn-login:hover{opacity:0.92}
.msg{
  text-align:center;font-size:13px;padding:10px 14px;border-radius:6px;margin-bottom:18px;
}
.msg.error{background:#fef2f2;color:#ef4444;border:1px solid #fecaca}
.msg.success{background:#f0fdf4;color:#22c55e;border:1px solid #bbf7d0}
.login-footer{text-align:center;margin-top:24px;font-size:12px;color:#bbb}
@media(max-width:768px){
  .login-side{display:none}
  .login-main{padding:20px}
  .login-card{width:100%}
}
</style>
</head>
<body>
<div class="login-side">
  <svg class="brand-icon" viewBox="0 0 24 24" fill="none" stroke="#6366f1" stroke-width="2">
    <path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/>
  </svg>
  <h1>管理后台</h1>
  <p>请输入您的账户信息登录</p>
</div>
<div class="login-main">
  <div class="login-card">
    <h2>欢迎回来</h2>
    <p class="login-sub">请登录以管理你的网站</p>
    <?php if(isset($msg)): ?>
    <div class="msg error"><?=$msg?></div>
    <?php endif; ?>
    <form method="post" id="loginForm">
      <div class="form-group">
        <label>用户名</label>
        <input type="text" name="user" placeholder="请输入用户名" required>
      </div>
      <div class="form-group">
        <label>密码</label>
        <input type="password" name="pwd" placeholder="请输入密码" required>
      </div>
      <div class="form-group">
        <label>验证码</label>
        <div class="captcha-row">
          <input type="text" name="captcha" placeholder="验证码" maxlength="4" required style="text-transform:uppercase">
          <img src="captcha.php?<?=time()?>" id="captchaImg" onclick="this.src='captcha.php?'+Date.now()" title="点击刷新">
          <a onclick="document.getElementById('captchaImg').src='captcha.php?'+Date.now()">换一张</a>
        </div>
      </div>
      <button class="btn-login">登 录</button>
    </form>
    <div class="login-footer">Link3 Admin Panel</div>
  </div>
</div>
</body>
</html>
<?php
exit;
}

// AJAX 音乐上传接口（带进度条）
if(isset($_GET['act']) && $_GET['act'] === 'ajax_upload_music' && isset($_FILES['music_file'])){
    header('Content-Type: application/json');
    $ext = strtolower(pathinfo($_FILES['music_file']['name'], PATHINFO_EXTENSION));
    if(!in_array($ext, ['mp3', 'flac'])){
        echo json_encode(['success' => false, 'error' => '仅支持 MP3 和 FLAC 格式']);
        exit;
    }
    $name = 'uploads/music_'.time().rand(100,999).'.'.$ext;
    if(move_uploaded_file($_FILES['music_file']['tmp_name'], $name)){
        echo json_encode(['success' => true, 'path' => $name]);
    } else {
        echo json_encode(['success' => false, 'error' => '文件上传失败']);
    }
    exit;
}

if(isset($_GET['del_avatar'])){
    if(!empty($cfg['avatar']) && file_exists($cfg['avatar'])){
        unlink($cfg['avatar']);
    }
    $db->exec("UPDATE config SET avatar='' WHERE id=1");
    header('Location:admin.php');
    exit;
}

if(isset($_POST['save_all'])){
    $title = safe($_POST['title']);
    $keywords = safe($_POST['keywords']);
    $description = safe($_POST['description']);

    $avatar = $cfg['avatar'];
    if($_FILES['avatar']['tmp_name']){
        $ext = pathinfo($_FILES['avatar']['name'],PATHINFO_EXTENSION);
        $avatar = 'uploads/avatar_'.time().rand(100,999).'.'.$ext;
        move_uploaded_file($_FILES['avatar']['tmp_name'], $avatar);
    }

    $bg_color = isset($_POST['bg_color']) ? safe($_POST['bg_color']) : ($cfg['bg_color'] ?? '#f0f0f0');
    $announcement = safe($_POST['announcement']);
    $announcement_enabled = isset($_POST['announcement_enabled']) ? 1 : 0;
    $custom_music_loop = isset($_POST['custom_music_loop']) ? 1 : 0;
    $custom_music_autoplay = isset($_POST['custom_music_autoplay']) ? 1 : 0;
    $custom_music_icon = isset($_POST['custom_music_icon']) ? safe($_POST['custom_music_icon']) : 'b';

    $custom_music = $cfg['custom_music'] ?? '';
    // 音乐地址优先
    if(!empty($_POST['custom_music_url'])){
        $custom_music = safe($_POST['custom_music_url']);
    }
    // AJAX 预上传的音乐路径
    elseif(!empty($_POST['custom_music_uploaded'])){
        $custom_music = safe($_POST['custom_music_uploaded']);
    }
    // 表单直传（兼容方式）
    elseif(isset($_FILES['custom_music_file']) && $_FILES['custom_music_file']['tmp_name']){
        $ext = strtolower(pathinfo($_FILES['custom_music_file']['name'],PATHINFO_EXTENSION));
        if(in_array($ext, ['mp3', 'flac'])){
            $custom_music = 'uploads/music_'.time().rand(100,999).'.'.$ext;
            move_uploaded_file($_FILES['custom_music_file']['tmp_name'], $custom_music);
        }
    }

    $db->exec("UPDATE config SET
        title='$title',
        keywords='$keywords',
        description='$description',
        avatar='$avatar',
        bg_color='$bg_color',
        announcement='$announcement',
        announcement_enabled=$announcement_enabled,
        custom_music='$custom_music',
        custom_music_loop=$custom_music_loop,
        custom_music_autoplay=$custom_music_autoplay,
        custom_music_icon='$custom_music_icon'
        WHERE id=1");

    foreach(['qq','wechat','email','phone','qq_group'] as $ck){
        $contact_val = safe($_POST['contact_'.$ck] ?? '');
        $contact_icon = $cfg['contact_'.$ck.'_icon'] ?? '';
        if(isset($_FILES['contact_'.$ck.'_icon']) && $_FILES['contact_'.$ck.'_icon']['tmp_name']){
            $ext = pathinfo($_FILES['contact_'.$ck.'_icon']['name'],PATHINFO_EXTENSION);
            $contact_icon = 'uploads/contact_'.$ck.'_'.time().rand(100,999).'.'.$ext;
            move_uploaded_file($_FILES['contact_'.$ck.'_icon']['tmp_name'], $contact_icon);
        }
        $db->exec("UPDATE config SET contact_{$ck}='$contact_val', contact_{$ck}_icon='$contact_icon' WHERE id=1");
    }

    $open_tip_wechat = isset($_POST['open_tip_wechat']) ? 1 : 0;
    $open_tip_qq = isset($_POST['open_tip_qq']) ? 1 : 0;
    $open_tip_douyin = isset($_POST['open_tip_douyin']) ? 1 : 0;
    $open_tip_weibo = isset($_POST['open_tip_weibo']) ? 1 : 0;
    $db->exec("UPDATE config SET open_tip_wechat=$open_tip_wechat, open_tip_qq=$open_tip_qq, open_tip_douyin=$open_tip_douyin, open_tip_weibo=$open_tip_weibo WHERE id=1");

    header('Location: admin.php?saved=1');
    exit;
}

if(isset($_GET['del_music'])){
    if(!empty($cfg['custom_music']) && file_exists($cfg['custom_music'])){
        unlink($cfg['custom_music']);
    }
    $db->exec("UPDATE config SET custom_music='' WHERE id=1");
    header('Location:admin.php');
    exit;
}

$contact_keys = ['qq','wechat','email','phone','qq_group'];
if(isset($_GET['del_contact_icon']) && in_array($_GET['del_contact_icon'], $contact_keys)){
    $k = $_GET['del_contact_icon'];
    $icon = $cfg['contact_'.$k.'_icon'] ?? '';
    if(!empty($icon) && file_exists($icon)) unlink($icon);
    $db->exec("UPDATE config SET contact_{$k}_icon='' WHERE id=1");
    header('Location:admin.php');
    exit;
}

if(isset($_POST['save_custom_style'])){
    $custom_bg_type = safe($_POST['custom_bg_type']);
    $custom_btn_bg = safe($_POST['custom_btn_bg']);
    $custom_btn_color = safe($_POST['custom_btn_color']);
    $custom_btn_arrow = isset($_POST['custom_btn_arrow']) ? 1 : 0;
    $custom_gradient_dir = safe($_POST['custom_gradient_dir']);
    $custom_btn_outline_color = safe($_POST['custom_btn_outline_color']);
    $custom_arrow_color = safe($_POST['custom_arrow_color']);
    $custom_title_color = safe($_POST['custom_title_color']);
    $custom_desc_color = safe($_POST['custom_desc_color']);
    $custom_text_color = safe($_POST['custom_text_color']);

    $bg_color = safe($_POST['bg_color']);

    if($custom_bg_type === 'color'){
        $custom_bg_value = safe($_POST['custom_bg_color']);
        $custom_gradient_from = '';
        $custom_gradient_to = '';
        $custom_bg_image = '';
    } elseif($custom_bg_type === 'gradient'){
        $custom_bg_value = '';
        $custom_gradient_from = safe($_POST['custom_gradient_from']);
        $custom_gradient_to = safe($_POST['custom_gradient_to']);
        $custom_bg_image = '';
    } else {
        $custom_bg_value = '';
        $custom_gradient_from = '';
        $custom_gradient_to = '';
    }

    $custom_bg_image = $cfg['custom_bg_image'] ?? '';
    if($custom_bg_type === 'image' && isset($_FILES['custom_bg_image_file']) && $_FILES['custom_bg_image_file']['tmp_name']){
        $ext = pathinfo($_FILES['custom_bg_image_file']['name'],PATHINFO_EXTENSION);
        $custom_bg_image = 'uploads/bg_'.time().rand(100,999).'.'.$ext;
        move_uploaded_file($_FILES['custom_bg_image_file']['tmp_name'], $custom_bg_image);
    }
    if($custom_bg_type !== 'image'){
        $custom_bg_image = '';
    }

    $db->exec("UPDATE config SET
        bg_color='$bg_color',
        custom_bg_type='$custom_bg_type',
        custom_bg_value='$custom_bg_value',
        custom_btn_bg='$custom_btn_bg',
        custom_btn_color='$custom_btn_color',
        custom_btn_arrow=$custom_btn_arrow,
        custom_gradient_from='$custom_gradient_from',
        custom_gradient_to='$custom_gradient_to',
        custom_gradient_dir='$custom_gradient_dir',
        custom_bg_image='$custom_bg_image',
        custom_btn_outline_color='$custom_btn_outline_color',
        custom_arrow_color='$custom_arrow_color',
        custom_title_color='$custom_title_color',
        custom_desc_color='$custom_desc_color',
        custom_text_color='$custom_text_color'
        WHERE id=1");

    header('Location: admin.php?saved=1');
    exit;
}

if(isset($_POST['change_pwd'])){
    $old = $_POST['old_pwd'] ?? '';
    $new = $_POST['new_pwd'] ?? '';
    $confirm = $_POST['confirm_pwd'] ?? '';

    if($old !== ADMIN_PWD){
        echo "<script>alert('原密码错误！');history.back();</script>";
        exit;
    }
    if(strlen($new) < 4){
        echo "<script>alert('新密码至少4位！');history.back();</script>";
        exit;
    }
    if($new !== $confirm){
        echo "<script>alert('两次密码不一致！');history.back();</script>";
        exit;
    }

    $new_safe = safe($new);
    $db->exec("UPDATE config SET admin_pwd='$new_safe' WHERE id=1");
    session_destroy();
    echo "<script>alert('密码修改成功，请重新登录！');location.href='admin.php';</script>";
    exit;
}

if(isset($_POST['ajax_sort']) && isset($_POST['ids'])){
    header('Content-Type: application/json');
    $ids = array_map('intval', explode(',', $_POST['ids']));
    $sort = count($ids);
    foreach($ids as $id){
        if($id > 0){
            $db->exec("UPDATE links SET sort=$sort WHERE id=$id");
            $sort--;
        }
    }
    echo json_encode(['success'=>true]);
    exit;
}

if(isset($_POST['add_link'])){
    $ltitle = safe($_POST['ltitle']);
    $lurl = safe($_POST['lurl']);
    $sort = (int)$_POST['sort'];
    $outline = isset($_POST['outline']) ? 1 : 0;
    $text_center = isset($_POST['text_center']) ? 1 : 0;
    $btn_radius_on = isset($_POST['btn_radius_on']) ? 1 : 0;
    $btn_radius = (int)$_POST['btn_radius'];
    $passcode = trim($_POST['passcode'] ?? '');
    $passcode = preg_replace('/\D/', '', $passcode);
    if(strlen($passcode) > 4) $passcode = substr($passcode,0,4);
    $icon = '';

    if(isset($_FILES['licon']) && $_FILES['licon']['tmp_name']){
        $ext = pathinfo($_FILES['licon']['name'],PATHINFO_EXTENSION);
        $icon = 'uploads/'.time().rand(100,999).'.'.$ext;
        move_uploaded_file($_FILES['licon']['tmp_name'], $icon);
    }

    $db->exec("INSERT INTO links
    (title,url,icon,sort,outline,passcode,type,text_center,btn_radius_on,btn_radius)
    VALUES ('$ltitle','$lurl','$icon',$sort,$outline,'$passcode','link',$text_center,$btn_radius_on,$btn_radius)");

    header('Location: admin.php?saved=1&toast_msg='.urlencode('添加成功'));
    exit;
}

if(isset($_POST['add_text'])){
    $title = safe($_POST['text_content']);
    $align = safe($_POST['text_align']);
    $sort = (int)$_POST['text_sort'];
    $db->exec("INSERT INTO links (title,sort,type,footer_align) VALUES ('$title',$sort,'text','$align')");
    header('Location: admin.php?saved=1&toast_msg='.urlencode('文字添加成功'));
    exit;
}

if(isset($_POST['edit_link'])){
    $id = (int)$_POST['id'];
    $ltitle = safe($_POST['ltitle']);
    $lurl = safe($_POST['lurl']);
    $sort = (int)$_POST['sort'];
    $outline = isset($_POST['outline']) ? 1 : 0;
    $text_center = isset($_POST['text_center']) ? 1 : 0;
    $btn_radius_on = isset($_POST['btn_radius_on']) ? 1 : 0;
    $btn_radius = (int)$_POST['btn_radius'];
    $passcode = trim($_POST['passcode'] ?? '');
    $passcode = preg_replace('/\D/', '', $passcode);
    if(strlen($passcode) > 4) $passcode = substr($passcode,0,4);

    $old = $db->querySingle("SELECT icon FROM links WHERE id=$id");
    $icon = $old;

    if(isset($_POST['del_icon']) && $_POST['del_icon'] == 1){
        if($old && file_exists($old)) unlink($old);
        $icon = '';
    }
    elseif($_FILES['licon']['tmp_name']){
        $ext = pathinfo($_FILES['licon']['name'],PATHINFO_EXTENSION);
        $icon = 'uploads/'.time().rand(100,999).'.'.$ext;
        move_uploaded_file($_FILES['licon']['tmp_name'], $icon);
        if($old && file_exists($old)) unlink($old);
    }

    $db->exec("UPDATE links SET
        title='$ltitle',
        url='$lurl',
        icon='$icon',
        sort=$sort,
        outline=$outline,
        passcode='$passcode',
        text_center=$text_center,
        btn_radius_on=$btn_radius_on,
        btn_radius=$btn_radius
        WHERE id=$id");

    header('Location: admin.php?saved=1&toast_msg='.urlencode('修改成功'));
    exit;
}

if(isset($_POST['edit_text'])){
    $id = (int)$_POST['id'];
    $title = safe($_POST['text_content']);
    $align = safe($_POST['text_align']);
    $sort = (int)$_POST['text_sort'];
    $db->exec("UPDATE links SET title='$title',sort=$sort,footer_align='$align' WHERE id=$id");
    header('Location: admin.php?saved=1&toast_msg='.urlencode('文字修改成功'));
    exit;
}

if(isset($_POST['add_image'])){
    $title = safe($_POST['img_title']);
    $sort = (int)$_POST['img_sort'];
    $outline = isset($_POST['outline']) ? 1 : 0;
    $text_center = isset($_POST['text_center']) ? 1 : 0;
    $btn_radius_on = isset($_POST['btn_radius_on']) ? 1 : 0;
    $btn_radius = (int)$_POST['btn_radius'];
    $icon = '';
    $popup_img = '';

    if(isset($_FILES['licon']) && !empty($_FILES['licon']['tmp_name'])){
        $ext = pathinfo($_FILES['licon']['name'],PATHINFO_EXTENSION);
        $icon = 'uploads/'.time().rand(100,999).'.'.$ext;
        move_uploaded_file($_FILES['licon']['tmp_name'], $icon);
    }

    if(isset($_FILES['popup_img']) && $_FILES['popup_img']['tmp_name']){
        $ext = pathinfo($_FILES['popup_img']['name'],PATHINFO_EXTENSION);
        $popup_img = 'uploads/'.time().rand(100,999).'.'.$ext;
        move_uploaded_file($_FILES['popup_img']['tmp_name'], $popup_img);
    }

    $db->exec("INSERT INTO links (title,icon,popup_img,sort,type,outline,text_center,btn_radius_on,btn_radius)
    VALUES ('$title','$icon','$popup_img',$sort,'image',$outline,$text_center,$btn_radius_on,$btn_radius)");
    header('Location: admin.php?saved=1&toast_msg='.urlencode('弹出图添加成功'));
    exit;
}

if(isset($_POST['edit_image'])){
    $id = (int)$_POST['id'];
    $title = safe($_POST['img_title']);
    $sort = (int)$_POST['img_sort'];
    $outline = isset($_POST['outline']) ? 1 : 0;
    $text_center = isset($_POST['text_center']) ? 1 : 0;
    $btn_radius_on = isset($_POST['btn_radius_on']) ? 1 : 0;
    $btn_radius = (int)$_POST['btn_radius'];
    $old = $db->querySingle("SELECT icon,popup_img FROM links WHERE id=$id",true);
    $icon = $old['icon'];
    $popup_img = $old['popup_img'];

    if(isset($_POST['del_icon']) && $_POST['del_icon'] == 1){
        if($old['icon'] && file_exists($old['icon'])) unlink($old['icon']);
        $icon = '';
    }
    elseif(!empty($_FILES['licon']['tmp_name'])){
        $ext = pathinfo($_FILES['licon']['name'],PATHINFO_EXTENSION);
        $icon = 'uploads/'.time().rand(100,999).'.'.$ext;
        move_uploaded_file($_FILES['licon']['tmp_name'], $icon);
        if($old['icon'] && file_exists($old['icon'])) unlink($old['icon']);
    }

    if(isset($_POST['del_popup']) && $_POST['del_popup'] == 1){
        if($old['popup_img'] && file_exists($old['popup_img'])) unlink($old['popup_img']);
        $popup_img = '';
    }
    elseif(!empty($_FILES['popup_img']['tmp_name'])){
        $ext = pathinfo($_FILES['popup_img']['name'],PATHINFO_EXTENSION);
        $popup_img = 'uploads/'.time().rand(100,999).'.'.$ext;
        move_uploaded_file($_FILES['popup_img']['tmp_name'], $popup_img);
        if($old['popup_img'] && file_exists($old['popup_img'])) unlink($old['popup_img']);
    }

    $db->exec("UPDATE links SET
        title='$title',
        icon='$icon',
        popup_img='$popup_img',
        sort=$sort,
        outline=$outline,
        text_center=$text_center,
        btn_radius_on=$btn_radius_on,
        btn_radius=$btn_radius
        WHERE id=$id");
    header('Location: admin.php?saved=1&toast_msg='.urlencode('弹出图修改成功'));
    exit;
}

if(isset($_POST['add_picture'])){
    $title = safe($_POST['pic_title'] ?? '');
    $url = safe($_POST['pic_url'] ?? '');
    $sort = (int)$_POST['pic_sort'];
    $icon = '';
    if(isset($_FILES['picture_file']) && $_FILES['picture_file']['tmp_name']){
        $ext = pathinfo($_FILES['picture_file']['name'],PATHINFO_EXTENSION);
        $icon = 'uploads/pic_'.time().rand(100,999).'.'.$ext;
        move_uploaded_file($_FILES['picture_file']['tmp_name'], $icon);
    }
    $db->exec("INSERT INTO links (title,url,icon,sort,type) VALUES ('$title','$url','$icon',$sort,'picture')");
    header('Location: admin.php?saved=1&toast_msg='.urlencode('图片添加成功'));
    exit;
}

if(isset($_POST['edit_picture'])){
    $id = (int)$_POST['pic_id'];
    $title = safe($_POST['pic_title'] ?? '');
    $url = safe($_POST['pic_url'] ?? '');
    $sort = (int)$_POST['pic_sort'];
    $old = $db->querySingle("SELECT icon FROM links WHERE id=$id");
    $icon = $old;
    if(isset($_POST['del_icon_pic']) && $_POST['del_icon_pic'] == 1){
        if($old && file_exists($old)) unlink($old);
        $icon = '';
    }
    elseif(isset($_FILES['picture_file']) && $_FILES['picture_file']['tmp_name']){
        $ext = pathinfo($_FILES['picture_file']['name'],PATHINFO_EXTENSION);
        $icon = 'uploads/pic_'.time().rand(100,999).'.'.$ext;
        move_uploaded_file($_FILES['picture_file']['tmp_name'], $icon);
        if($old && file_exists($old)) unlink($old);
    }
    $db->exec("UPDATE links SET title='$title',url='$url',icon='$icon',sort=$sort WHERE id=$id");
    header('Location: admin.php?saved=1&toast_msg='.urlencode('图片修改成功'));
    exit;
}

if(isset($_POST['add_video'])){
    $title = safe($_POST['video_title'] ?? '');
    $sort = (int)$_POST['video_sort'];
    $outline = isset($_POST['outline']) ? 1 : 0;
    $text_center = isset($_POST['text_center']) ? 1 : 0;
    $btn_radius_on = isset($_POST['btn_radius_on']) ? 1 : 0;
    $btn_radius = (int)$_POST['btn_radius'];
    $auto_expand = isset($_POST['auto_expand']) ? 1 : 0;
    $auto_play = isset($_POST['auto_play']) ? 1 : 0;
    $default_muted = isset($_POST['default_muted']) ? 1 : 0;
    $video_loop = isset($_POST['video_loop']) ? 1 : 0;
    $icon = '';
    $video_file = '';

    if(isset($_FILES['video_file']) && !empty($_FILES['video_file']['tmp_name'])){
        $ext = strtolower(pathinfo($_FILES['video_file']['name'], PATHINFO_EXTENSION));
        if(in_array($ext, ['mp4','webm','ogg'])){
            $video_file = 'uploads/video_'.time().rand(100,999).'.'.$ext;
            move_uploaded_file($_FILES['video_file']['tmp_name'], $video_file);
        }
    } elseif(!empty($_POST['video_url'])){
        $video_file = safe($_POST['video_url']);
    }

    if(isset($_FILES['licon']) && !empty($_FILES['licon']['tmp_name'])){
        $ext = pathinfo($_FILES['licon']['name'],PATHINFO_EXTENSION);
        $icon = 'uploads/'.time().rand(100,999).'.'.$ext;
        move_uploaded_file($_FILES['licon']['tmp_name'], $icon);
    }

    $poster = '';
    if(isset($_FILES['video_poster']) && !empty($_FILES['video_poster']['tmp_name'])){
        $ext = pathinfo($_FILES['video_poster']['name'],PATHINFO_EXTENSION);
        $poster = 'uploads/poster_'.time().rand(100,999).'.'.$ext;
        move_uploaded_file($_FILES['video_poster']['tmp_name'], $poster);
    }

    $db->exec("INSERT INTO links (title,url,icon,video_file,video_poster,sort,type,outline,text_center,btn_radius_on,btn_radius,auto_expand,auto_play,default_muted,video_loop)
    VALUES ('$title','','$icon','$video_file','$poster',$sort,'video',$outline,$text_center,$btn_radius_on,$btn_radius,$auto_expand,$auto_play,$default_muted,$video_loop)");
    header('Location: admin.php?saved=1&toast_msg='.urlencode('视频添加成功'));
    exit;
}

if(isset($_POST['edit_video'])){
    $id = (int)$_POST['vid'];
    $title = safe($_POST['video_title'] ?? '');
    $sort = (int)$_POST['video_sort'];
    $outline = isset($_POST['outline']) ? 1 : 0;
    $text_center = isset($_POST['text_center']) ? 1 : 0;
    $btn_radius_on = isset($_POST['btn_radius_on']) ? 1 : 0;
    $btn_radius = (int)$_POST['btn_radius'];
    $auto_expand = isset($_POST['auto_expand']) ? 1 : 0;
    $auto_play = isset($_POST['auto_play']) ? 1 : 0;
    $default_muted = isset($_POST['default_muted']) ? 1 : 0;
    $video_loop = isset($_POST['video_loop']) ? 1 : 0;

    $old = $db->querySingle("SELECT icon,video_file,video_poster FROM links WHERE id=$id", true);
    $icon = $old['icon'];
    $video_file = $old['video_file'];
    $poster = $old['video_poster'];

    if(isset($_POST['del_icon_video']) && $_POST['del_icon_video'] == 1){
        if($old['icon'] && file_exists($old['icon'])) unlink($old['icon']);
        $icon = '';
    }
    elseif(!empty($_FILES['licon']['tmp_name'])){
        $ext = pathinfo($_FILES['licon']['name'],PATHINFO_EXTENSION);
        $icon = 'uploads/'.time().rand(100,999).'.'.$ext;
        move_uploaded_file($_FILES['licon']['tmp_name'], $icon);
        if($old['icon'] && file_exists($old['icon'])) unlink($old['icon']);
    }

    if(isset($_POST['del_video_poster']) && $_POST['del_video_poster'] == 1){
        if($old['video_poster'] && file_exists($old['video_poster'])) unlink($old['video_poster']);
        $poster = '';
    }
    elseif(!empty($_FILES['video_poster']['tmp_name'])){
        $ext = pathinfo($_FILES['video_poster']['name'],PATHINFO_EXTENSION);
        $new_poster = 'uploads/poster_'.time().rand(100,999).'.'.$ext;
        move_uploaded_file($_FILES['video_poster']['tmp_name'], $new_poster);
        if($old['video_poster'] && file_exists($old['video_poster'])) unlink($old['video_poster']);
        $poster = $new_poster;
    }

    if(isset($_POST['del_video_file']) && $_POST['del_video_file'] == 1){
        if($old['video_file'] && file_exists($old['video_file'])) unlink($old['video_file']);
        $video_file = '';
    }
    elseif(isset($_FILES['video_file']) && !empty($_FILES['video_file']['tmp_name'])){
        $ext = strtolower(pathinfo($_FILES['video_file']['name'], PATHINFO_EXTENSION));
        if(in_array($ext, ['mp4','webm','ogg'])){
            $new_video = 'uploads/video_'.time().rand(100,999).'.'.$ext;
            move_uploaded_file($_FILES['video_file']['tmp_name'], $new_video);
            if($old['video_file'] && file_exists($old['video_file'])) unlink($old['video_file']);
            $video_file = $new_video;
        }
    } elseif(!empty($_POST['video_url'])){
        if($old['video_file'] && file_exists($old['video_file'])) unlink($old['video_file']);
        $video_file = safe($_POST['video_url']);
    }

    $db->exec("UPDATE links SET
        title='$title',
        icon='$icon',
        video_file='$video_file',
        sort=$sort,
        outline=$outline,
        text_center=$text_center,
        btn_radius_on=$btn_radius_on,
        btn_radius=$btn_radius,
        auto_expand=$auto_expand,
        auto_play=$auto_play,
        default_muted=$default_muted,
        video_loop=$video_loop,
        video_poster='$poster'
        WHERE id=$id");
    header('Location: admin.php?saved=1&toast_msg='.urlencode('视频修改成功'));
    exit;
}

if(isset($_GET['del'])){
    $id = (int)$_GET['del'];
    $item = $db->querySingle("SELECT icon,popup_img,video_file,type FROM links WHERE id=$id",true);
    if($item['type'] === 'link' && $item['icon'] && file_exists($item['icon'])){
        unlink($item['icon']);
    }
    if($item['type'] === 'image'){
        if($item['icon'] && file_exists($item['icon'])) unlink($item['icon']);
        if($item['popup_img'] && file_exists($item['popup_img'])) unlink($item['popup_img']);
    }
    if($item['type'] === 'video'){
        if($item['icon'] && file_exists($item['icon'])) unlink($item['icon']);
        if($item['video_file'] && file_exists($item['video_file'])) unlink($item['video_file']);
        if($item['video_poster'] && file_exists($item['video_poster'])) unlink($item['video_poster']);
    }
    if($item['type'] === 'picture'){
        if($item['icon'] && file_exists($item['icon'])) unlink($item['icon']);
    }
    $db->exec("DELETE FROM links WHERE id=$id");
    header('Location:admin.php');exit;
}

$days = get_7days();

$views = [];
foreach($days as $d){
    $views[$d] = $db->querySingle("SELECT count FROM link_stats WHERE date='$d' AND type='view' AND target='home'") ?: 0;
}

$clicks = [];
$all_links = [];
$res = $db->query('SELECT id,title,outline,type,text_center,btn_radius_on,btn_radius FROM links WHERE type IN ("link","image","video") OR type="picture"');
while($l = $res->fetchArray(SQLITE3_ASSOC)) $all_links[$l['id']] = $l;
foreach($all_links as $id=>$item){
    $c = 0;
    foreach($days as $d){
        $c += $db->querySingle("SELECT count FROM link_stats WHERE date='$d' AND type='click' AND target='$id'") ?: 0;
    }
    $clicks[$id] = ['title'=>$item['title'], 'total'=>$c];
}

$bg_color = isset($cfg['bg_color']) ? $cfg['bg_color'] : '#f0f0f0';
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>管理后台</title>
<style>
*{margin:0;padding:0;box-sizing:border-box;font-family:-apple-system,BlinkMacSystemFont,"Segoe UI","Roboto",sans-serif}
body{display:flex;min-height:100vh;background:#f0f2f5;color:#333}

/* ===== 侧边栏 ===== */
.sidebar{
  width:220px;background:#1a1d29;display:flex;flex-direction:column;flex-shrink:0;
  position:fixed;top:0;left:0;bottom:0;z-index:100;
}
.sidebar-logo{
  padding:20px 16px;border-bottom:1px solid rgba(255,255,255,0.08);
  display:flex;align-items:center;gap:10px;
}
.sidebar-logo svg{width:28px;height:28px;}
.sidebar-logo span{color:#fff;font-size:16px;font-weight:600;letter-spacing:1px}
.sidebar-nav{flex:1;padding:12px 8px;display:flex;flex-direction:column;gap:2px}
.nav-item{
  display:flex;align-items:center;gap:10px;padding:10px 14px;border-radius:8px;
  color:rgba(255,255,255,0.6);cursor:pointer;transition:all 0.15s;text-decoration:none;
  font-size:14px;border:none;background:none;width:100%;text-align:left;
}
.nav-item:hover{color:#fff;background:rgba(255,255,255,0.08)}
.nav-item.active{color:#fff;background:rgba(99,102,241,0.2);font-weight:500}
.nav-item .nav-icon{width:20px;height:20px;display:flex;align-items:center;justify-content:center;font-size:18px}
.sidebar-footer{
  padding:12px 8px;border-top:1px solid rgba(255,255,255,0.08);
  display:flex;flex-direction:column;gap:2px;
}

/* ===== 主区域 ===== */
.main{
  margin-left:220px;flex:1;display:flex;flex-direction:column;min-height:100vh;
}
.topbar{
  height:56px;background:#fff;border-bottom:1px solid #e8eaed;
  display:flex;align-items:center;justify-content:space-between;
  padding:0 28px;position:sticky;top:0;z-index:50;
}
.topbar-title{font-size:16px;font-weight:600;color:#1a1d29}
.topbar-actions{display:flex;gap:16px;align-items:center}
.topbar-link{
  color:#666;text-decoration:none;font-size:13px;padding:6px 12px;
  border-radius:6px;transition:all 0.15s;
}
.topbar-link:hover{background:#f0f2f5;color:#1a1d29}
.content{padding:24px 28px;flex:1;max-width:800px;width:100%;margin:0 auto}

.page-section{display:none}
.page-section.active{display:block}

.card{
  background:#fff;border-radius:10px;padding:20px 24px;margin-bottom:16px;
  border:1px solid #e8eaed;
}
.card-title{
  font-size:15px;font-weight:600;color:#1a1d29;margin-bottom:16px;
  padding-bottom:12px;border-bottom:1px solid #f0f2f5;
}
.section-header{margin-bottom:20px}
.section-header h2{font-size:20px;font-weight:700;color:#1a1d29;margin-bottom:4px}
.section-desc{font-size:13px;color:#888}

.stats-grid{display:grid;grid-template-columns:1fr 1fr;gap:16px}
@media(max-width:768px){.stats-grid{grid-template-columns:1fr}}
.stat-card{background:#fff;border-radius:10px;padding:20px;border:1px solid #e8eaed}
.stat-card-header{display:flex;align-items:center;gap:8px;margin-bottom:16px;padding-bottom:12px;border-bottom:1px solid #f0f2f5}
.stat-icon{font-size:20px}
.stat-label{font-size:14px;font-weight:600;color:#1a1d29}
.stat-list{display:flex;flex-direction:column;gap:8px}
.stat-row{display:flex;align-items:center;gap:10px}
.stat-date{width:140px;font-size:13px;color:#666;flex-shrink:0;white-space:nowrap}
.stat-bar-wrapper{flex:1;height:6px;background:#f0f2f5;border-radius:3px;overflow:hidden}
.stat-bar{height:100%;background:#6366f1;border-radius:3px;transition:width 0.3s;min-width:4px}
.stat-bar-purple{background:#a78bfa}
.stat-num{width:40px;text-align:right;font-size:13px;color:#1a1d29}
.stat-empty{text-align:center;color:#bbb;padding:20px;font-size:13px}

.site-setting-row{display:flex;gap:20px;align-items:flex-start}
.avatar-col{display:flex;flex-direction:column;align-items:center;gap:10px;flex-shrink:0}
.avatar-preview{width:80px;height:80px;border-radius:50%;object-fit:cover;background:#f0f2f5;border:2px solid #fff;box-shadow:0 2px 8px rgba(0,0,0,0.08)}
.avatar-wrap{position:relative;width:80px;height:80px}
.avatar-del{position:absolute;top:-6px;right:-6px;width:22px;height:22px;background:#ff4757;color:#fff;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:12px;text-decoration:none}
.avatar-upload-wrapper{position:relative;display:inline-block}
.avatar-upload-wrapper input[type=file]{position:absolute;top:0;left:0;width:100%;height:100%;opacity:0;cursor:pointer}
.avatar-upload-btn{padding:8px 16px;background:#fff;border:1px solid #ddd;border-radius:8px;cursor:pointer;font-size:13px}
.input-col{flex:1;display:flex;flex-direction:column;gap:8px}
.top-row{display:flex;gap:8px}
.site-input,.site-textarea{width:100%;padding:10px 14px;border-radius:8px;background:#f7f8fa;border:1px solid #f0f2f5;outline:none;transition:all 0.15s;font-size:14px}
.site-input:focus,.site-textarea:focus{border-color:#6366f1;box-shadow:0 0 0 3px rgba(99,102,241,0.1)}
.site-textarea{min-height:80px;resize:vertical}
.switch-label{display:flex;align-items:center;gap:10px;cursor:pointer;font-size:14px}
.switch-label input[type=checkbox]{width:18px;height:18px;accent-color:#6366f1}
.music-row{display:flex;flex-wrap:wrap;gap:12px;align-items:center}
.contact-row{display:flex;align-items:center;gap:10px;margin-bottom:10px;flex-wrap:wrap}
.contact-label{min-width:48px;font-size:13px;font-weight:500;color:#555}
.contact-row .site-input{flex:1;min-width:120px}
.contact-icon-wrap{display:flex;align-items:center;gap:8px;flex-shrink:0}
.contact-icon-preview{position:relative;display:inline-block}
.contact-icon-img{width:28px;height:28px;border-radius:50%;object-fit:cover;border:1px solid #eee}
.contact-icon-del{position:absolute;top:-6px;right:-6px;width:16px;height:16px;background:#ff4757;color:#fff;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:10px;text-decoration:none}
.contact-icon-preview-new{display:inline-block;margin-left:4px}
.music-status{color:#28a745;font-size:13px}
.music-del{color:#ff4757;font-size:13px;text-decoration:none}
.inline-label{display:inline-flex;align-items:center;gap:4px;font-size:13px;cursor:pointer}
.sep{color:#ddd;font-size:13px}
.music-icon-label{font-size:13px;color:#555}

.bg-preview-group{display:flex;flex-wrap:wrap;gap:10px;justify-content:center}
.bg-preview-item{text-align:center}
.bg-preview-item img{width:80px;height:108px;object-fit:cover;border-radius:6px;border:2px solid transparent;cursor:pointer;transition:all 0.15s}
.bg-preview-item img.active{border-color:#6366f1;box-shadow:0 0 0 3px rgba(99,102,241,0.15)}
.bg-preview-item p{font-size:12px;color:#888;margin-top:4px}


.toolbar{display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;flex-wrap:wrap;gap:8px}
.toolbar-info{font-size:13px;color:#888}
.toolbar-right{display:flex;gap:6px;flex-wrap:wrap}
.btn-add{
  display:inline-flex;align-items:center;gap:4px;padding:7px 16px;border-radius:8px;
  background:#6366f1;color:#fff;text-decoration:none;font-size:13px;transition:opacity 0.15s;
}
.btn-add:hover{opacity:0.9}
.btn-add-icon{width:14px;height:14px}
.table-wrap{overflow-x:auto}
table{width:100%;border-collapse:collapse;font-size:13px}
td,th{padding:10px 12px;border-bottom:1px solid #f0f2f5;text-align:left;white-space:nowrap}
th{background:#fafbfc;font-weight:600;color:#555;font-size:12px;text-transform:uppercase;letter-spacing:0.3px}
tr:hover td{background:#fafbfe}
.badge{padding:3px 8px;border-radius:4px;font-size:11px;font-weight:500}
.badge-text{background:#f0f0f0;color:#666}

.module-preview-list{display:flex;flex-direction:column;gap:10px;margin-bottom:16px}
.module-preview-item{
  display:flex;align-items:center;gap:8px;
  background:#f8f9fa;border:none;border-radius:10px;padding:4px 10px;
  transition:box-shadow 0.15s;
  -webkit-touch-callout:none;-webkit-user-select:none;user-select:none;touch-action:manipulation;
}
.module-preview-item:hover{box-shadow:0 2px 8px rgba(0,0,0,0.06)}
.module-preview-item{cursor:grab}
.module-preview-item:active{cursor:grabbing}
.module-preview-item.dragging{opacity:0.4;box-shadow:0 4px 12px rgba(0,0,0,0.15)}
.module-preview-item.drag-over{border-top:2px solid #6366f1}
.preview-type-badge{font-size:11px;padding:2px 8px;border-radius:4px;font-weight:500;line-height:20px;flex-shrink:0}
.preview-type-badge.link{background:#eef2ff;color:#6366f1}
.preview-type-badge.text{background:#f0f0f0;color:#666}
.preview-type-badge.image{background:#ecfdf5;color:#10b981}
.preview-col{flex:1;min-width:0;max-width:420px}
.preview-link-btn{
  width:100%;height:56px;
  display:flex;align-items:center;padding:0 20px;
  color:#222;box-shadow:0 2px 8px rgba(0,0,0,0.05);background:#fff;
  box-sizing:border-box;border:none;pointer-events:none;border-radius:8px;
}
.preview-link-btn.outline{background:transparent!important;box-shadow:none;border:1px solid #222;color:#222!important}
.preview-link-btn.text-center .preview-link-title{text-align:center}
.preview-link-icon{width:36px;height:36px;margin-left:-10px;margin-right:12px;border-radius:50%;object-fit:cover;flex-shrink:0}
.preview-link-title{flex:1;font-size:15px;font-weight:600;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.preview-link-arrow{width:20px;height:20px;fill:#888;flex-shrink:0}
.preview-text{font-size:13px;line-height:1.5;padding:8px 16px;color:#333;background:#f9f9f9;border-radius:6px;flex:1;max-width:420px}
.preview-picture{flex:1;max-width:200px}
.preview-picture img{width:100%;height:auto;border-radius:12px;display:block}
.preview-actions{display:flex;gap:4px;flex-shrink:0;margin-left:auto}
.preview-actions .action-btn{
  display:inline-flex;align-items:center;justify-content:center;
  width:30px;height:30px;border-radius:6px;color:#666;text-decoration:none;transition:all 0.15s;
  background:#f0f2f5;border:1px solid #e0e2e6;
  -webkit-touch-callout:none;user-select:none;touch-action:manipulation;
}
.preview-actions .action-btn:hover{background:#e8eaf0;color:#6366f1;border-color:#6366f1}
.preview-actions .action-del:hover{background:#fef2f2;color:#ef4444;border-color:#ef4444}
.preview-actions .action-btn svg{width:14px;height:14px;display:block}
.preview-actions .action-move-up:hover{background:#e8eaf0;color:#f59e0b;border-color:#f59e0b}
.preview-actions .action-move-down:hover{background:#e8eaf0;color:#f59e0b;border-color:#f59e0b}

.bg-type-select{display:flex;gap:10px;margin-bottom:16px}
.radio-card{
  flex:1;padding:12px;border-radius:8px;border:2px solid #e8eaed;
  text-align:center;cursor:pointer;font-size:13px;font-weight:500;transition:all 0.15s;
}
.radio-card.active{border-color:#6366f1;background:#eef2ff;color:#6366f1}
.radio-card input{display:none}
.bg-type-fields{display:flex;flex-direction:column;gap:12px}
.color-field{display:flex;align-items:center;gap:8px;padding:4px 12px;background:#f5f6f8;border-radius:8px}
.color-field label{font-size:13px;color:#555;white-space:nowrap}
.color-field input[type=color]{width:56px;height:34px;padding:2px;border:1px solid #ddd;border-radius:6px;cursor:pointer;background:none}
.color-field select{padding:8px 12px;border-radius:6px;border:1px solid #ddd;font-size:13px;background:#fff}
.gradient-row{display:flex;flex-direction:row;flex-wrap:wrap;gap:10px}
.gradient-row .color-field{flex-shrink:0}
.sm-select{padding:4px 8px !important;font-size:12px !important;height:30px}
.style-grid{display:flex;flex-wrap:wrap;gap:12px 20px;align-items:center}
.style-grid .color-field{flex-shrink:0}

.save-wrapper{text-align:center;margin-top:8px;margin-bottom:16px}
.btn-save{
  padding:10px 36px;border-radius:10px;border:none;
  background:linear-gradient(135deg,#6366f1,#a78bfa);color:#fff;
  font-size:14px;cursor:pointer;transition:opacity 0.15s;
}
.btn-save:hover{opacity:0.9}

.modal{
  position:fixed;top:0;left:0;width:100%;height:100%;
  background:rgba(0,0,0,0.4);display:flex;align-items:center;justify-content:center;
  z-index:9999;backdrop-filter:blur(2px);
}
.modal-content{background:#fff;width:480px;max-width:90vw;border-radius:12px;padding:24px;position:relative;max-height:85vh;overflow-y:auto}
.modal-close{position:absolute;top:14px;right:16px;font-size:22px;cursor:pointer;color:#999;line-height:1}
.modal h2{font-size:16px;margin-bottom:20px;font-weight:600}
.modal-hidden{display:none !important}
.modal-item{display:flex;align-items:center;gap:12px;margin-bottom:16px}
.modal-item label{min-width:72px;font-size:13px;color:#555;font-weight:500}
.modal-item input:not([type=file]):not([type=checkbox]):not([type=radio]):not([type=hidden]),.modal-item textarea{
  padding:7px 12px;border:1px solid #d0d5dd;border-radius:6px;font-size:13px;flex:1;outline:none;
  background:#fff;transition:all 0.2s;box-shadow:0 1px 2px rgba(0,0,0,0.03);height:32px;box-sizing:border-box
}
.modal-item textarea{height:auto;min-height:60px}
.modal-item input:not([type=file]):not([type=checkbox]):not([type=radio]):not([type=hidden]):focus,.modal-item textarea:focus{border-color:#6366f1;box-shadow:0 0 0 3px rgba(99,102,241,0.12);background:#fff}
.modal-item textarea{min-height:70px;resize:vertical;line-height:1.5}
.modal-item .style-checkboxes{display:flex;align-items:center;gap:8px;flex:1}
.modal-item .style-checkboxes label{min-width:auto;font-weight:400;font-size:13px;cursor:pointer;display:inline-flex;align-items:center;gap:4px;color:#333;padding:4px 8px;background:#f5f6f8;border-radius:6px;transition:background 0.15s;white-space:nowrap}
.modal-item .style-checkboxes label:hover{background:#eef0f4}
.modal-item .style-checkboxes input[type=checkbox],.modal-item .style-checkboxes input[type=radio]{accent-color:#6366f1;width:15px;height:15px}
.modal-item .style-checkboxes .radius-group{margin-left:auto;display:inline-flex;align-items:center;gap:4px}
.modal-bottom{text-align:center;margin-top:20px}
.modal-bottom button{
  padding:9px 32px;background:#6366f1;color:#fff;border:none;border-radius:8px;font-size:14px;cursor:pointer;
}
.modal-bottom button:hover{opacity:0.9}

.upload-btn-box{
  padding:6px 12px;background:#f5f5f5;border:1px dashed #ccc;
  border-radius:6px;cursor:pointer;font-size:12px;display:inline-block;
}
.img-preview-wrap{position:relative;display:inline-block;cursor:pointer;vertical-align:middle}
.img-preview-wrap .del-x{
  position:absolute;top:-8px;right:-8px;width:20px;height:20px;
  background:#ff4757;color:#fff;border-radius:50%;
  display:flex;align-items:center;justify-content:center;
  font-size:12px;cursor:pointer;
}

@media(max-width:768px){
  .sidebar{width:56px}
  .sidebar-logo span,.nav-item span{display:none}
  .sidebar-logo{padding:16px 12px;justify-content:center}
  .nav-item{justify-content:center;padding:10px}
  .main{margin-left:56px}
  .content{padding:16px}
  .topbar{padding:0 16px}
  .site-setting-row{flex-direction:column;align-items:center}
  .top-row{flex-direction:column}
}

.bg-preview-item img.active{border-color:#6366f1;box-shadow:0 0 0 3px rgba(99,102,241,0.15)}

/* 版权信息弹窗 */
.admin-footer{text-align:center;padding:30px 20px 20px;font-size:13px;color:#999}
.admin-footer a{color:#6366f1;cursor:pointer;text-decoration:none}
.admin-footer a:hover{text-decoration:underline}
#qrModal{display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:99999;align-items:center;justify-content:center;padding:20px}
.qr-modal-box{background:#fff;border-radius:16px;padding:28px 24px 20px;width:100%;max-width:380px;text-align:center;box-shadow:0 4px 24px rgba(0,0,0,0.15)}
.qr-modal-desc{font-size:14px;color:#333;margin-bottom:20px}
.qr-container{display:flex;justify-content:center;gap:20px}
.qr-item{flex:1;max-width:140px}
.qr-item img{width:100%;height:auto;border-radius:8px;display:block}
.qr-item p{font-size:12px;color:#666;margin-top:8px;line-height:1.4}
</style>
</head>
<body>

<div class="sidebar">
  <div class="sidebar-logo">
    <svg viewBox="0 0 24 24" fill="none" stroke="#6366f1" stroke-width="2"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/></svg>
    <span>管理后台</span>
  </div>
  <div class="sidebar-nav">
    <button class="nav-item active" onclick="switchPage('home', this)">
      <span class="nav-icon">🏠</span><span>首页</span>
    </button>
    <button class="nav-item" onclick="switchPage('site', this)">
      <span class="nav-icon">⚙️</span><span>网站设置</span>
    </button>
    <button class="nav-item" onclick="switchPage('modules', this)">
      <span class="nav-icon">📦</span><span>模块管理</span>
    </button>
    <button class="nav-item" onclick="switchPage('style', this)">
      <span class="nav-icon">🎨</span><span>系统风格</span>
    </button>
  </div>
  </div>
</div>

<div class="main">
  <div class="topbar">
    <div class="topbar-title" id="pageTitle">首页</div>
    <div class="topbar-actions">
      <a href="./" target="_blank" class="topbar-link">🌐 访问前台</a>
      <a href="javascript:openPwdModal()" class="topbar-link">🔑 修改密码</a>
      <a href="?act=logout" class="topbar-link">🚪 退出</a>
    </div>
  </div>

  <!-- Toast 通知 -->
  <div id="toast" style="display:none;position:fixed;top:20px;left:50%;transform:translateX(-50%);z-index:99999;background:#22c55e;color:#fff;padding:12px 28px;border-radius:8px;font-size:14px;font-weight:500;box-shadow:0 4px 20px rgba(0,0,0,0.15);transition:opacity 0.3s;"></div>
  <div class="content">
    <div class="page-section active" id="page-home"><?php include 'admin_home.php'; ?></div>
    <div class="page-section" id="page-site"><?php include 'admin_site.php'; ?></div>
    <div class="page-section" id="page-modules"><?php include 'admin_modules.php'; ?></div>
    <div class="page-section" id="page-style"><?php include 'admin_custom_style.php'; ?></div>
  </div>
  <div class="admin-footer">
    本站由 <a href="javascript:;" onclick="openQRModal()"><?=$GLOBALS['_cp_author']?></a> 提供技术支持
  </div>
</div>

<div class="modal modal-hidden" id="pwdModal">
<div class="modal-content" style="width:400px;">
<span class="modal-close" onclick="closePwdModal()">×</span>
<h2>修改密码</h2>
<form method="post">
  <div class="modal-item"><label>原密码</label><input type="password" name="old_pwd" required></div>
  <div class="modal-item"><label>新密码</label><input type="password" name="new_pwd" required minlength="4"></div>
  <div class="modal-item"><label>确认密码</label><input type="password" name="confirm_pwd" required minlength="4"></div>
  <div class="modal-bottom"><button name="change_pwd">确认修改</button></div>
</form>
</div>
</div>

<script>
const pageNames = {
  home: '首页',
  site: '网站设置',
  modules: '模块管理',
  style: '系统风格'
};

function switchPage(page, btn){
  document.querySelectorAll('.page-section').forEach(el=>el.classList.remove('active'));
  document.querySelectorAll('.nav-item').forEach(el=>el.classList.remove('active'));
  document.getElementById('page-'+page).classList.add('active');
  if(btn) btn.classList.add('active');
  document.getElementById('pageTitle').textContent = pageNames[page] || page;
  sessionStorage.setItem('admin_page', page);
}

const customWrapper = document.getElementById('customSettingsWrapper');

function toggleCustomSettings(show){
  if(customWrapper){
    customWrapper.style.display = show ? 'block' : 'none';
  }
}

document.querySelectorAll('.bg-preview-item img').forEach(img=>{
  img.onclick = function(){
    let val = this.getAttribute('data-val');
    if(val === 'custom'){
      document.querySelectorAll('input[name="bg_color"]').forEach(inp=>{ inp.value = 'custom'; });
      document.querySelectorAll('.bg-preview-item img').forEach(i=>i.classList.remove('active'));
      this.classList.add('active');
      toggleCustomSettings(true);
      const target = document.getElementById('customStyleSettings');
      if(target) setTimeout(()=>target.scrollIntoView({behavior:'smooth',block:'start'}),100);
      return;
    }
    document.querySelectorAll('input[name="bg_color"]').forEach(inp=>{ inp.value = val; });
    document.querySelectorAll('.bg-preview-item img').forEach(i=>i.classList.remove('active'));
    this.classList.add('active');
    toggleCustomSettings(false);
  };
});


(function(){
  let nowVal = "<?=$bg_color?>";
  document.querySelectorAll('.bg-preview-item img').forEach(img=>{
    if(img.getAttribute('data-val') === nowVal){
      img.classList.add('active');
      document.querySelectorAll('input[name="bg_color"]').forEach(inp=>{ inp.value = nowVal; });
    }
  });
  if(nowVal !== 'custom') toggleCustomSettings(false);
})();

function previewAvatar(input){
  let p = document.getElementById('previewImg');
  if(!p){
    document.querySelector('.avatar-wrap').innerHTML = '<img src="" class="avatar-preview" id="previewImg">';
    p = document.getElementById('previewImg');
  }
  if(input.files[0]){
    let r = new FileReader();
    r.onload = function(e){p.src = e.target.result;};
    r.readAsDataURL(input.files[0]);
  }
}

function previewContactIcon(input,previewId){
  let previewBox = document.getElementById(previewId);
  if(input.files[0]){
    let r = new FileReader();
    r.onload = function(e){
      previewBox.innerHTML = '<img src="'+e.target.result+'" width="28" height="28" style="border-radius:50%;object-fit:cover;border:1px solid #eee">';
      previewBox.style.display = "inline-block";
    };
    r.readAsDataURL(input.files[0]);
  }
}

function previewModalIcon(input,previewId){
  let previewBox = document.getElementById(previewId);
  if(input.files[0]){
    let r = new FileReader();
    r.onload = function(e){
      previewBox.innerHTML = '<img src="'+e.target.result+'" width="32" style="border-radius:4px">';
      previewBox.style.display = "inline-block";
    };
    r.readAsDataURL(input.files[0]);
  }
}

function refreshEditIcon(src){
  const container = document.getElementById('edit_icon_container');
  const delInput = document.getElementById('del_icon');
  if(src){
    container.innerHTML = '<div class="img-preview-wrap" onclick="document.getElementById(\'edit_icon_file\').click()">' +
      '<img src="'+src+'" width="32" id="edit_icon_preview" style="border-radius:4px">' +
      '<div class="del-x" onclick="event.stopPropagation();refreshEditIcon(\'\');delInput.value=1;">×</div></div>';
  } else {
    container.innerHTML = '<div class="upload-btn-box" onclick="document.getElementById(\'edit_icon_file\').click()">上传图标</div>';
    delInput.value = 1;
  }
}

function previewEditIcon(input){
  if(input.files[0]){
    let r = new FileReader();
    r.onload = function(e){refreshEditIcon(e.target.result);document.getElementById('del_icon').value = 0;};
    r.readAsDataURL(input.files[0]);
  }
}

function refreshEditImageIcon(src){
  const c = document.getElementById('edit_image_icon_container');
  const delInput = document.getElementById('del_icon_img');
  if(src){
    c.innerHTML = '<div class="img-preview-wrap" onclick="document.getElementById(\'edit_image_icon_file\').click()">' +
      '<img src="'+src+'" width="32" style="border-radius:4px">' +
      '<div class="del-x" onclick="event.stopPropagation();refreshEditImageIcon(\'\');delInput.value=1;">×</div></div>';
  } else {
    c.innerHTML = '<div class="upload-btn-box" onclick="document.getElementById(\'edit_image_icon_file\').click()">上传图标</div>';
    delInput.value = 1;
  }
}

function refreshEditPopup(src){
  const c = document.getElementById('edit_image_popup_container');
  const delInput = document.getElementById('del_popup_img');
  if(src){
    c.innerHTML = '<div class="img-preview-wrap" onclick="document.getElementById(\'edit_image_popup_file\').click()">' +
      '<img src="'+src+'" width="60" style="border-radius:4px">' +
      '<div class="del-x" onclick="event.stopPropagation();refreshEditPopup(\'\');delInput.value=1;">×</div></div>';
  } else {
    c.innerHTML = '<div class="upload-btn-box" onclick="document.getElementById(\'edit_image_popup_file\').click()">上传大图</div>';
    delInput.value = 1;
  }
}

function previewEditImageIcon(input){
  if(input.files[0]){
    let r = new FileReader();
    r.onload = function(e){refreshEditImageIcon(e.target.result);document.getElementById('del_icon_img').value = 0;};
    r.readAsDataURL(input.files[0]);
  }
}

function previewEditPopup(input){
  if(input.files[0]){
    let r = new FileReader();
    r.onload = function(e){refreshEditPopup(e.target.result);document.getElementById('del_popup_img').value = 0;};
    r.readAsDataURL(input.files[0]);
  }
}

function openAddModal(){document.getElementById('addModal').classList.remove('modal-hidden')}
function closeAddModal(){document.getElementById('addModal').classList.add('modal-hidden')}
function openEditModal(id,t,u,s,o,tc,bron,br,p,i){
  document.getElementById('edit_id').value=id; document.getElementById('edit_title').value=t; document.getElementById('edit_url').value=u; document.getElementById('edit_sort').value=s;
  document.getElementById('edit_outline').checked=o==1;
  document.getElementById('edit_text_center').checked=tc==1;
  document.getElementById('edit_btn_radius_on').checked=bron==1;
  document.getElementById('edit_btn_radius').value=br;
  document.getElementById('edit_passcode').value=p;
  refreshEditIcon(i||'');
  document.getElementById('editModal').classList.remove('modal-hidden');
}
function closeEditModal(){document.getElementById('editModal').classList.add('modal-hidden')}

function openAddTextModal(){document.getElementById('addTextModal').classList.remove('modal-hidden')}
function closeAddTextModal(){document.getElementById('addTextModal').classList.add('modal-hidden')}
function openEditTextModal(id,t,s,a){
  document.getElementById('edit_text_id').value=id; document.getElementById('edit_text_content').value=t; document.getElementById('edit_text_sort').value=s;
  document.getElementById('a_left').checked=a=='left';document.getElementById('a_center').checked=a=='center';document.getElementById('a_right').checked=a=='right';
  document.getElementById('editTextModal').classList.remove('modal-hidden');
}
function closeEditTextModal(){document.getElementById('editTextModal').classList.add('modal-hidden')}

function openAddImageModal(){document.getElementById('addImageModal').classList.remove('modal-hidden')}
function closeAddImageModal(){document.getElementById('addImageModal').classList.add('modal-hidden')}
function openEditImageModal(id,t,s,o,i,p,tc,bron,br){
  document.getElementById('edit_image_id').value=id; document.getElementById('edit_image_title').value=t; document.getElementById('edit_image_sort').value=s;
  document.getElementById('edit_img_outline').checked=o==1;
  document.getElementById('edit_img_text_center').checked=tc==1;
  document.getElementById('edit_img_btn_radius_on').checked=bron==1;
  document.getElementById('edit_img_btn_radius').value=br;
  refreshEditImageIcon(i||'');
  refreshEditPopup(p||'');
  document.getElementById('editImageModal').classList.remove('modal-hidden');
}
function closeEditImageModal(){document.getElementById('editImageModal').classList.add('modal-hidden')}

function openAddPictureModal(){document.getElementById('addPictureModal').classList.remove('modal-hidden')}
function closeAddPictureModal(){document.getElementById('addPictureModal').classList.add('modal-hidden')}
function openEditPictureModal(id,u,s,i,t){
  document.getElementById('edit_pic_id').value=id;
  document.getElementById('edit_pic_url').value=u;
  document.getElementById('edit_pic_sort').value=s;
  if(t!==undefined) document.getElementById('edit_pic_title').value=t;
  refreshEditPictureIcon(i||'');
  document.getElementById('editPictureModal').classList.remove('modal-hidden');
}
function closeEditPictureModal(){document.getElementById('editPictureModal').classList.add('modal-hidden')}

function previewVideoName(input){
  if(input.files[0]) input.previousElementSibling.textContent = input.files[0].name;
}

function openAddVideoModal(){document.getElementById('addVideoModal').classList.remove('modal-hidden')}
function closeAddVideoModal(){document.getElementById('addVideoModal').classList.add('modal-hidden')}
function openEditVideoModal(id,t,s,o,tc,bron,br,i,vf,ae,ap,dm,vl,p){
  document.getElementById('edit_vid').value=id;
  document.getElementById('edit_video_title').value=t;
  document.getElementById('edit_video_sort').value=s;
  document.getElementById('edit_video_outline').checked=o==1;
  document.getElementById('edit_video_text_center').checked=tc==1;
  document.getElementById('edit_video_btn_radius_on').checked=bron==1;
  document.getElementById('edit_video_btn_radius').value=br;
  document.getElementById('edit_video_auto_expand').checked=ae==1;
  document.getElementById('edit_video_auto_play').checked=ap==1;
  document.getElementById('edit_video_default_muted').checked=dm==1;
  document.getElementById('edit_video_loop').checked=vl==1;
  document.getElementById('edit_video_url').value='';
  refreshEditVideoIcon(i||'');
  refreshEditVideoFile(vf||'');
  refreshEditVideoPoster(p||'');
  document.getElementById('editVideoModal').classList.remove('modal-hidden');
}
function closeEditVideoModal(){document.getElementById('editVideoModal').classList.add('modal-hidden')}
function refreshEditVideoIcon(src){
  const c=document.getElementById('edit_video_icon_container');
  const delInput=document.getElementById('del_icon_video');
  if(src){
    c.innerHTML='<div class="img-preview-wrap" onclick="document.getElementById(\'edit_video_icon_file\').click()"><img src="'+src+'" width="32" style="border-radius:4px"><div class="del-x" onclick="event.stopPropagation();refreshEditVideoIcon(\'\');delInput.value=1;">×</div></div>';
  }else{
    c.innerHTML='<div class="upload-btn-box" onclick="document.getElementById(\'edit_video_icon_file\').click()">上传图标</div>';
    delInput.value=0;
  }
}
function refreshEditVideoFile(src){
  const c=document.getElementById('edit_video_file_container');
  if(src){
    c.innerHTML='<div style="display:flex;align-items:center;gap:6px;background:#f5f6f8;padding:6px 10px;border-radius:6px;font-size:12px;color:#555"><span style="flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">🎬 '+src.split("/").pop()+'</span><span onclick="event.stopPropagation();refreshEditVideoFile(\'\');" style="cursor:pointer;color:#ef4444;font-size:14px;">×</span></div>';
  }else{
    c.innerHTML='<div class="avatar-upload-wrapper"><div class="upload-btn-box">上传视频</div><input type="file" name="video_file" accept="video/mp4,video/webm,video/ogg" onchange="previewVideoName(this)"></div>';
  }
}
function refreshEditVideoPoster(src){
  const c=document.getElementById('edit_video_poster_container');
  const delInput=document.getElementById('del_video_poster');
  if(src){
    c.innerHTML='<div class="img-preview-wrap" onclick="document.getElementById(\'edit_video_poster_file\').click()"><img src="'+src+'" width="60" height="60" style="border-radius:6px;object-fit:cover;border:1px solid #eee"><div class="del-x" onclick="event.stopPropagation();refreshEditVideoPoster(\'\');delInput.value=1;">×</div></div>';
  }else{
    c.innerHTML='<div class="upload-btn-box" onclick="document.getElementById(\'edit_video_poster_file\').click()">上传封面</div>';
    delInput.value=0;
  }
}
function previewEditVideoPoster(input){
  if(input.files[0]){
    let r=new FileReader();
    r.onload=function(e){refreshEditVideoPoster(e.target.result);document.getElementById('del_video_poster').value=0;};
    r.readAsDataURL(input.files[0]);
  }
}
function previewEditVideoIcon(input){
  if(input.files[0]){
    let r=new FileReader();
    r.onload=function(e){refreshEditVideoIcon(e.target.result);document.getElementById('del_icon_video').value=0;};
    r.readAsDataURL(input.files[0]);
  }
}
function refreshEditPictureIcon(src){
  const c=document.getElementById('edit_pic_icon_container');
  if(src){
    c.innerHTML='<div class="img-preview-wrap" onclick="document.getElementById(\'edit_pic_file\').click()"><img src="'+src+'" width="60" height="60" style="border-radius:6px;object-fit:cover;border:1px solid #eee"><div class="del-x" onclick="event.stopPropagation();refreshEditPictureIcon(\'\');">×</div></div>';
    document.getElementById('edit_pic_file').value='';
  }else{
    c.innerHTML='<div class="upload-btn-box" onclick="document.getElementById(\'edit_pic_file\').click()">上传图片</div>';
  }
}
function previewEditPictureIcon(input){
  if(input.files[0]){
    let r=new FileReader();
    r.onload=function(e){refreshEditPictureIcon(e.target.result);};
    r.readAsDataURL(input.files[0]);
  }
}

function openPwdModal(){document.getElementById('pwdModal').classList.remove('modal-hidden')}
function closePwdModal(){document.getElementById('pwdModal').classList.add('modal-hidden')}

function switchBgType(type){
  document.querySelectorAll('.bg-type-fields').forEach(function(el){el.style.display='none';});
  const el = document.getElementById('bg_fields_'+type);
  el.style.display = type==='gradient' ? 'flex' : 'block';
  document.querySelectorAll('.radio-card').forEach(function(el){el.classList.remove('active');});
  document.querySelector('.radio-card input[value="'+type+'"]').parentElement.classList.add('active');
}

const savedPage = sessionStorage.getItem('admin_page');
if(savedPage && pageNames[savedPage]){
  const btn = document.querySelector('.sidebar-nav [onclick*="'+savedPage+'"]');
  switchPage(savedPage, btn);
}

// Toast 通知
(function(){
  var t = document.getElementById('toast');
  if(!t) return;
  var urlParams = new URLSearchParams(window.location.search);
  if(urlParams.get('saved') === '1'){
    t.textContent = urlParams.get('toast_msg') || '保存成功';
    t.style.display = 'block';
    t.style.opacity = '1';
    setTimeout(function(){ t.style.opacity = '0'; }, 2000);
    setTimeout(function(){ t.style.display = 'none'; }, 2300);
    var url = window.location.pathname + window.location.hash;
    window.history.replaceState({}, '', url);
  }
})();

// 二维码弹窗
function openQRModal(){ var m = document.getElementById('qrModal'); if(m) m.style.display = 'flex'; }
document.addEventListener('click', function(e){ var m = document.getElementById('qrModal'); if(m && m.style.display === 'flex' && e.target === m) m.style.display = 'none'; });
</script>

<div id="qrModal">
  <div class="qr-modal-box">
    <div class="qr-modal-desc">如有问题可以联系我</div>
    <div class="qr-container">
      <div class="qr-item">
        <img src="<?=$GLOBALS['_cp_wx']?>" alt="微信">
        <p>微信扫一扫 加好友</p>
      </div>
      <div class="qr-item">
        <img src="<?=$GLOBALS['_cp_xy']?>" alt="闲鱼">
        <p>闲鱼扫一扫 找模版</p>
      </div>
    </div>
  </div>
</div>
</body>
</html>
