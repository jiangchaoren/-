<?php
require_once 'config.php';

// 系统完整性校验
if(!isset($GLOBALS['_cp_author']) || $GLOBALS['_cp_author'] !== '匆匆过客'){
    http_response_code(503);
    exit('系统文件已被篡改，无法提供服务');
}

if (isset($_GET['check_pass'])) {
    session_start();
    $id = (int)$_GET['check_pass'];
    $link = $db->querySingle("SELECT passcode FROM links WHERE id = $id LIMIT 1", true);
    header('Content-Type: application/json');

    if(empty(trim($link['passcode']))){
        echo json_encode(['need_pass' => false]);
        exit;
    }

    $nowPass = trim($link['passcode']);
    $sessionKey = 'pass_verified_'.$id;
    $sessionPwdKey = 'pass_val_'.$id;

    if(isset($_SESSION[$sessionKey], $_SESSION[$sessionPwdKey]) && $_SESSION[$sessionKey] === true && $_SESSION[$sessionPwdKey] === $nowPass){
        echo json_encode(['need_pass' => false]);
    }else{
        unset($_SESSION[$sessionKey], $_SESSION[$sessionPwdKey]);
        echo json_encode(['need_pass' => true]);
    }
    exit;
}

if (isset($_GET['verify_pass'])) {
    session_start();
    $id = (int)$_GET['verify_pass'];
    $pass = trim($_GET['pass'] ?? '');
    $pass = preg_replace('/\D/', '', $pass);
    $correct = $db->querySingle("SELECT passcode FROM links WHERE id = $id LIMIT 1");
    header('Content-Type: application/json');

    if($pass === trim($correct) && !empty($correct)){
        $_SESSION['pass_verified_'.$id] = true;
        $_SESSION['pass_val_'.$id] = trim($correct);
        echo json_encode(['success' => true]);
    }else{
        echo json_encode(['success' => false]);
    }
    exit;
}

add_view();

if (isset($_GET['image_click'])) {
    $id = (int)$_GET['image_click'];
    add_click($id);
    exit;
}

if (isset($_GET['click'])) {
    $id = (int)$_GET['click'];
    session_start();
    $row = $db->querySingle("SELECT url,passcode FROM links WHERE id = $id LIMIT 1", true);
    if(!$row || empty($row['url'])) exit;

    $passcode = trim($row['passcode']);
    $url = $row['url'];

    if(empty($passcode)){
        add_click($id);
        header("Location: ".$url);
        exit;
    }

    $sKey = 'pass_verified_'.$id;
    $sPwdKey = 'pass_val_'.$id;
    if(isset($_SESSION[$sKey],$_SESSION[$sPwdKey]) && $_SESSION[$sKey] === true && $_SESSION[$sPwdKey] === $passcode){
        add_click($id);
        header("Location: ".$url);
        exit;
    }
    exit;
}

$all_items = $db->query("SELECT * FROM links ORDER BY sort DESC, id DESC");

$bg_color = isset($cfg['bg_color']) ? $cfg['bg_color'] : '#f0f0f0';

$is_dark_bg = $bg_color === '#111111';
$is_purple_bg = $bg_color === '#7c3aed';
$is_deep_bg = $bg_color === 'deep';
$is_cyber_bg = $bg_color === 'cyber';
$is_gold_bg = $bg_color === 'gold';
$text_color = '#222';
$desc_color = '#333';

if($is_dark_bg || $is_purple_bg || $is_deep_bg || $is_cyber_bg || $is_gold_bg){
    $text_color = '#fff';
    $desc_color = '#eee';
}

$is_custom = $bg_color === 'custom';
$custom_btn_arrow = isset($cfg['custom_btn_arrow']) ? (int)$cfg['custom_btn_arrow'] : 1;
if($is_custom){
    $text_color = $cfg['custom_title_color'] ?? '#222';
    $desc_color = $cfg['custom_desc_color'] ?? '#333';
}
$custom_music = $cfg['custom_music'] ?? '';
$custom_music_loop = isset($cfg['custom_music_loop']) ? (int)$cfg['custom_music_loop'] : 0;
$custom_music_autoplay = isset($cfg['custom_music_autoplay']) ? (int)$cfg['custom_music_autoplay'] : 0;
$custom_music_icon = isset($cfg['custom_music_icon']) ? $cfg['custom_music_icon'] : 'b';
$music_icon_src = ($custom_music_icon === 'h') ? 'img/music_h.png' : 'img/music_b.png';
$music_btn_class = ($custom_music_icon === 'h') ? 'music-btn-white' : 'music-btn-black';

// 检测内置浏览器平台
$ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
$show_open_tip = '';
if(strpos($ua, 'MicroMessenger') !== false && $cfg['open_tip_wechat']) $show_open_tip = '微信';
elseif(strpos($ua, 'QQ/') !== false && $cfg['open_tip_qq']) $show_open_tip = 'QQ';
elseif(strpos($ua, 'aweme') !== false && $cfg['open_tip_douyin']) $show_open_tip = '抖音';
elseif(strpos($ua, 'Weibo') !== false && $cfg['open_tip_weibo']) $show_open_tip = '微博';

?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?=$cfg['keywords']?></title>
<meta name="keywords" content="<?=$cfg['title']?>,<?=$cfg['keywords']?>">
<meta name="description" content="<?=$cfg['description']?>">
<style>
*{margin:0;padding:0;box-sizing:border-box;font-family:system-ui,sans-serif}
* {
  -webkit-tap-highlight-color: transparent;
  -webkit-touch-callout: none;
  -webkit-user-select: none;
  user-select: none;
}
body{display:flex;justify-content:center;min-height:100vh;padding:20px;}
<?php if($is_deep_bg): ?>
body{
    background:url(img/background.png) no-repeat center center;
    background-size:cover;
    background-attachment:fixed;
}
<?php elseif($is_gold_bg): ?>
body{background:#0a0a0a;position:relative;}
body::before{content:'';position:fixed;inset:0;background:radial-gradient(circle at 50% 50%,rgba(212,175,55,0.04),transparent 70%);pointer-events:none;z-index:0;}
#goldParticles{position:fixed;inset:0;z-index:0;pointer-events:none;overflow:hidden}
.gold-particle{position:absolute;border-radius:50%;pointer-events:none;background:rgba(212,175,55,0.4);box-shadow:0 0 6px rgba(212,175,55,0.3)}
.wrapper{position:relative;z-index:1;}
<?php elseif($is_cyber_bg): ?>
body{background:linear-gradient(135deg,#0a0a1f 0%,#1a1a3e 50%,#0a0a1f 100%);position:relative;}
body::before{content:'';position:fixed;inset:0;background-image:linear-gradient(rgba(0,240,255,0.03) 1px,transparent 1px),linear-gradient(90deg,rgba(0,240,255,0.03) 1px,transparent 1px);background-size:30px 30px;pointer-events:none;z-index:0;}
.wrapper{position:relative;z-index:1;}
<?php elseif($bg_color === '#7c3aed'): ?>
body{background: linear-gradient(135deg, #7c3aed 0%, #a78bfa 100%);}
<?php elseif($is_custom && $cfg['custom_bg_type'] === 'gradient'): ?>
body{background:linear-gradient(<?=$cfg['custom_gradient_dir']?>, <?=$cfg['custom_gradient_from']?> 0%, <?=$cfg['custom_gradient_to']?> 100%);background-attachment:fixed;}
<?php elseif($is_custom && $cfg['custom_bg_type'] === 'image' && !empty($cfg['custom_bg_image'])): ?>
body{background:url(<?=$cfg['custom_bg_image']?>) no-repeat center center;background-size:cover;background-attachment:fixed;}
<?php elseif($is_custom && $cfg['custom_bg_type'] === 'color'): ?>
body{background:<?=$cfg['custom_bg_value']?>;}
<?php else: ?>
body{background:<?=$bg_color?>;}
<?php endif; ?>
.wrapper{display:flex;flex-direction:column;align-items:center;width:100%;}
.container{width:100%;max-width:720px;min-width:257px}
.avatar{width:96px;height:96px;border-radius:50%;object-fit:cover;margin:20px auto;border:3px solid #fff;box-shadow:0 2px 10px rgba(0,0,0,0.1);display:block;}
.title{font-size:16px;font-weight:700;text-align:center;margin-bottom:10px;color:<?=$text_color?>;}
.desc{font-size:14px;color:<?=$desc_color?>;text-align:center;margin-bottom:40px;}
.link-item{
    min-width:257px;width:100%;height:56px;
    display:flex;align-items:center;padding:0 24px;margin-bottom:16px;
    text-decoration:none;color:#222;box-shadow:0 2px 8px rgba(0,0,0,0.05);background:#fff;
    position:relative;cursor:pointer;
}
.link-item.text-center .link-title {
    text-align: center;
}
.link-item.outline{background:transparent !important;box-shadow:none;border:1px solid #222;color:#222 !important;}
<?php if($is_dark_bg || $is_purple_bg || $is_deep_bg || $is_cyber_bg || $is_gold_bg): ?>
.link-item.outline{border-color:#fff !important;color:#fff !important;}
<?php endif; ?>

.link-item:hover{transform:scale(1.03);box-shadow:0 4px 12px rgba(0,0,0,0.1);transition:all 0.2s ease;}
.link-icon{width:42px;height:42px;margin-left:-18px;margin-right:14px;border-radius:50%;object-fit:cover;}
.link-title{flex:1;font-size:16px;font-weight:700}
.link-arrow{width:24px;height:24px;fill:#888;transition:transform 0.2s;flex-shrink:0;}

<?php if(($is_dark_bg || $is_purple_bg) && !$is_deep_bg): ?>
.link-item:not(.outline) .link-arrow{fill:#333 !important;}
.link-item.outline .link-arrow{fill:#fff !important;}
<?php endif; ?>

<?php if($is_deep_bg): ?>
.link-arrow{fill:#fff!important;}
<?php endif; ?>

<?php if($is_deep_bg): ?>
.link-item{
    background:linear-gradient(90deg,#3d82f6,#9235eb)!important;
    color:#fff!important;
    border:none!important;
}
.link-item.outline{
    background:transparent!important;
    border:none!important;
    color:#fff!important;
}
.link-item.outline::before{
    content:"";
    position:absolute;top:0;left:0;right:0;bottom:0;
    padding:1px;
    background:linear-gradient(90deg,#3d82f6,#9235eb);
    border-radius:inherit;
    -webkit-mask:linear-gradient(#fff 0 0) content-box,linear-gradient(#fff 0 0);
    -webkit-mask-composite:destination-out;
    mask-composite:exclude;
    pointer-events:none;
}
<?php endif; ?>

.footer-bottom-text{
    margin: 10px 0 20px;
    font-size:14px;
    line-height:1.6;
    color:<?=$text_color?>;
}
#passcodeModal{display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:9999;align-items:center;justify-content:center;}
.passcode-box{background:#fff;border-radius:16px;padding:30px;width:90%;max-width:320px;text-align:center;}
.passcode-input{width:50px;height:50px;text-align:center;font-size:24px;border:1px solid #ddd;border-radius:8px;inputmode:numeric;}
#tipText {
    height: 20px;
    line-height:20px;
    font-size:13px;
    margin-bottom:15px;
}
.tip-error {
    color: #ff4d4f;
}
.tip-success {
    color: #00b42a;
}

#imageViewer{
  display:none;
  position:fixed;
  inset:0;
  background:rgba(0,0,0,0.9);
  z-index:99999;
  align-items:center;
  justify-content:center;
  padding:20px;
}
#imageViewer img{
  max-width:100%;
  max-height:90vh;
  border-radius:8px;
  object-fit:contain;
}
#imageViewer .close{
  position:absolute;
  top:20px;
  right:20px;
  width:40px;
  height:40px;
  background:#fff;
  border-radius:50%;
  display:flex;align-items:center;justify-content:center;
  font-size:20px;cursor:pointer;color:#000;z-index:10;
}

.video-module{margin-bottom:16px}
.video-player-wrap{margin-top:12px;border-radius:12px;overflow:hidden}
.video-player-wrap video{width:100%;display:block;border-radius:12px}
.video-close-btn{width:36px;height:36px;line-height:36px;text-align:center;font-size:20px;color:#888;background:#f0f2f5;border-radius:50%;cursor:pointer;margin:10px auto 0;user-select:none;transition:background 0.15s}
.video-close-btn:hover{background:#e0e2e6}

#announcementModal{
  display:none;
  position:fixed;
  inset:0;
  background:rgba(0,0,0,0.5);
  z-index:99998;
  align-items:center;
  justify-content:center;
  padding:20px;
}
.announcement-box{
  background:#fff;
  border-radius:16px;
  padding:24px 24px 0;
  width:100%;
  max-width:320px;
  max-height:70vh;
  overflow-y:auto;
  position:relative;
  box-shadow:0 4px 24px rgba(0,0,0,0.15);
  animation:announceIn 0.3s ease-out;
}
@keyframes announceIn{
  from{transform:scale(0.6);opacity:0}
  to{transform:scale(1);opacity:1}
}
.announcement-content{
  font-size:14px;
  line-height:1.7;
  color:#696969;
  white-space:pre-wrap;
  word-wrap:break-word;
}
.announcement-content h1,
.announcement-content h2,
.announcement-content h3{
  margin:16px 0 8px;
}
.announcement-content p{
  margin:8px 0;
}
.announcement-content img{
  max-width:100%;
  border-radius:8px;
}
.announcement-btn-wrap{
  margin-top:16px;
  border-top:1px solid #f0f0f0;
  cursor:pointer;
  transition:background 0.2s;
}
.announcement-btn-wrap:hover{
  background:#fafafa;
}
.announcement-btn{
  display:block;
  width:100%;
  padding:14px 0;
  background:transparent;
  color:#7c3aed;
  border:none;
  font-size:16px;
  font-weight:400;
  cursor:pointer;
  text-align:center;
}
<?php if($is_custom): ?>
<?php if(!$custom_btn_arrow): ?>
.link-arrow{display:none!important;}
<?php endif; ?>
.link-item{background:<?=$cfg['custom_btn_bg']?>!important;color:<?=$cfg['custom_btn_color']?>!important;}
.link-item.outline{background:transparent!important;color:<?=$cfg['custom_btn_color']?>!important;border-color:<?=$cfg['custom_btn_outline_color']?>!important;}
.link-arrow{fill:<?=$cfg['custom_arrow_color']?>!important;}
.footer-bottom-text{color:<?=$cfg['custom_text_color']?>!important;}
<?php endif; ?>

/* 音乐播放器 */
#musicBtn{
  position:fixed;
  top:16px;
  right:16px;
  border-radius:50%;
  display:flex;
  align-items:center;
  justify-content:center;
  cursor:pointer;
  z-index:9997;
  box-shadow:0 2px 10px rgba(0,0,0,0.2);
  transition:transform 0.2s;
  border:none;
}
#musicBtn.music-btn-black{background:rgba(0,0,0,0.55);}
#musicBtn.music-btn-white{background:rgba(255,255,255,0.8);}
#musicBtn:hover{transform:scale(1.08);}
#musicBtn img{width:24px;height:24px;display:block;}
#musicBtn.spin img{animation:musicSpin 3s linear infinite;}
@keyframes musicSpin{from{transform:rotate(0deg)}to{transform:rotate(360deg)}}
.contact-bar{display:flex;justify-content:center;gap:14px;margin:-20px 0 36px}
.contact-icon{width:26px;height:26px;border-radius:50%;object-fit:cover;cursor:pointer;transition:transform 0.15s;border:1px solid rgba(255,255,255,0.3)}
.contact-icon:hover{transform:scale(1.15)}
<?php if($is_dark_bg || $is_purple_bg || $is_deep_bg || $is_cyber_bg || $is_gold_bg): ?>
.contact-icon{border-color:rgba(255,255,255,0.4)}
<?php endif; ?>
#contactModal{display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:99998;align-items:center;justify-content:center;padding:20px}
.contact-modal-box{background:#fff;border-radius:16px;padding:28px 24px 0;width:100%;max-width:300px;text-align:center;box-shadow:0 4px 24px rgba(0,0,0,0.15);animation:announceIn 0.3s ease-out}
.contact-modal-box .contact-val{font-size:16px;font-weight:600;color:#1a1d29;margin:8px 0 18px;word-break:break-all}
.contact-modal-box .contact-btns{display:flex;border-top:1px solid #f0f0f0;margin:0 -24px;margin-top:18px;overflow:hidden;border-radius:0 0 16px 16px}
.contact-modal-box .contact-btns div{flex:1;padding:14px 0;font-size:14px;cursor:pointer;text-align:center;transition:background 0.15s}
.contact-modal-box .contact-btns div:hover{background:#fafafa}
.contact-modal-box .contact-btns .btn-copy{color:#ef4444;font-weight:500}
.contact-modal-box .contact-btns .btn-cancel{color:#888;border-right:1px solid #f0f0f0}
<?php if(!empty($show_open_tip)): ?>
#openTipOverlay{position:fixed;inset:0;background:rgba(0,0,0,0.85);z-index:999999;display:flex;flex-direction:column;align-items:center;justify-content:center;padding:40px 25px;text-align:center;color:#fff}
#openTipOverlay .tip-arrow{position:absolute;top:20px;right:30px;width:80px;height:80px}
#openTipOverlay .tip-title{font-size:22px;font-weight:600;margin-bottom:15px;margin-top:60px}
#openTipOverlay .tip-desc{font-size:15px;color:#ccc;line-height:1.6;margin-bottom:40px}
#openTipOverlay .tip-btn{background:#07C160;color:#fff;font-size:16px;font-weight:500;padding:14px 30px;border-radius:50px;border:none;outline:none;cursor:pointer;margin-bottom:20px}
#openTipOverlay .tip-btn:active{opacity:0.85}
#openTipOverlay .tip-small{font-size:13px;color:#999}
<?php endif; ?>
</style>
</head>
<body>
<div class="wrapper">
  <?php if($is_gold_bg): ?><div id="goldParticles"></div><?php endif; ?>
  <div class="container">
    <?php if($cfg['avatar_show'] == 1 && !empty($cfg['avatar'])): ?>
        <img src="<?=$cfg['avatar']?>" class="avatar">
    <?php endif; ?>
    <h1 class="title"><?=$cfg['title']?></h1>
    <p class="desc"><?=$cfg['description']?></p>

    <?php
    $contacts = [];
    $contact_labels = ['qq'=>'QQ号','wechat'=>'微信','email'=>'邮箱','phone'=>'手机号','qq_group'=>'QQ群'];
    foreach(['qq','wechat','email','phone','qq_group'] as $ck){
        $cv = trim($cfg['contact_'.$ck] ?? '');
        $ci = trim($cfg['contact_'.$ck.'_icon'] ?? '');
        if($cv !== '' && $ci !== ''){
            $contacts[] = ['key'=>$ck, 'val'=>$cv, 'icon'=>$ci];
        }
    }
    ?>
    <?php if(!empty($contacts)): ?>
    <div class="contact-bar">
      <?php foreach($contacts as $c):
        $is_url = preg_match('/^https?:\/\//i', $c['val']);
      ?>
      <?php if($is_url): ?>
      <a href="<?=$c['val']?>" target="_blank" rel="noopener noreferrer"><img src="<?=$c['icon']?>" class="contact-icon" title="点击跳转"></a>
      <?php else: ?>
      <img src="<?=$c['icon']?>" class="contact-icon" onclick="openContactModal('<?=$c['val']?>','<?=$contact_labels[$c['key']]?>')" title="点击查看">
      <?php endif; ?>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <?php while($item = $all_items->fetchArray(SQLITE3_ASSOC)): ?>
        <?php if($item['type'] === 'link'): ?>
            <?php
            $cls = $item['outline'] ? 'link-item outline' : 'link-item';
            $radius = $item['btn_radius_on'] == 1 ? (int)$item['btn_radius'] : 0;
            $style = "border-radius:{$radius}px;";
            $has_pass = !empty(trim($item['passcode'] ?? ''));
            ?>
            <?php if($has_pass): ?>
            <a href="javascript:;" data-lid="<?=$item['id']?>" class="<?=$cls?> <?=$item['text_center']==1 ? 'text-center' : ''?>" style="<?=$style?>">
            <?php else: ?>
            <a href="?click=<?=$item['id']?>" target="_blank" rel="noopener noreferrer" data-lid="<?=$item['id']?>" class="<?=$cls?> <?=$item['text_center']==1 ? 'text-center' : ''?>" style="<?=$style?>">
            <?php endif; ?>
                <?php if(!empty($item['icon'])): ?>
                <img src="<?=$item['icon']?>" class="link-icon">
                <?php endif; ?>
                <span class="link-title"><?=$item['title']?></span>
                <svg class="link-arrow" viewBox="0 0 24 24"><path d="M8.59 16.59L13.17 12 8.59 7.41 10 6l6 6-6 6-1.41-1.41z"/></svg>
            </a>
        <?php elseif($item['type'] === 'image'): ?>
            <?php 
            $cls = $item['outline'] ? 'link-item outline' : 'link-item';
            $radius = $item['btn_radius_on'] == 1 ? (int)$item['btn_radius'] : 0;
            $style = "border-radius:{$radius}px;";
            ?>
            <div class="<?=$cls?> <?=$item['text_center']==1 ? 'text-center' : ''?>" style="<?=$style?>" onclick="countAndShow(<?=$item['id']?>, '<?=$item['popup_img']?>')">
                <?php if(!empty($item['icon'])): ?>
                <img src="<?=$item['icon']?>" class="link-icon">
                <?php endif; ?>
                <span class="link-title"><?=$item['title']?></span>
                <svg class="link-arrow" viewBox="0 0 24 24"><path d="M8.59 16.59L13.17 12 8.59 7.41 10 6l6 6-6 6-1.41-1.41z"/></svg>
            </div>
        <?php elseif($item['type'] === 'picture'): ?>
            <?php if(!empty($item['icon'])): ?>
            <?php if(!empty($item['url'])): ?>
            <a href="?click=<?=$item['id']?>" target="_blank" rel="noopener noreferrer">
            <img src="<?=$item['icon']?>" style="width:100%;height:auto;border-radius:24px;display:block;margin-bottom:16px;cursor:pointer">
            </a>
            <?php else: ?>
            <img src="<?=$item['icon']?>" style="width:100%;height:auto;border-radius:24px;display:block;margin-bottom:16px;cursor:pointer" onclick="picView(<?=$item['id']?>,this.src)">
            <?php endif; ?>
            <?php endif; ?>
        <?php elseif($item['type'] === 'video'): ?>
            <?php
            $cls = $item['outline'] ? 'link-item outline' : 'link-item';
            $radius = $item['btn_radius_on'] == 1 ? (int)$item['btn_radius'] : 0;
            $style = "border-radius:{$radius}px;";
            $is_auto = $item['auto_expand'] == 1;
            ?>
            <div class="video-module" data-auto-expand="<?=$item['auto_expand']?>" data-auto-play="<?=$item['auto_play']?>" data-default-muted="<?=$item['default_muted']?>">
                <div class="<?=$cls?> <?=$item['text_center']==1 ? 'text-center' : ''?>" style="<?=$style?><?=$is_auto?'display:none;':''?>cursor:pointer;" onclick="toggleVideo(this)">
                    <?php if(!empty($item['icon'])): ?>
                    <img src="<?=$item['icon']?>" class="link-icon">
                    <?php endif; ?>
                    <span class="link-title"><?=$item['title']?></span>
                    <svg class="link-arrow" viewBox="0 0 24 24"><path d="M8.59 16.59L13.17 12 8.59 7.41 10 6l6 6-6 6-1.41-1.41z"/></svg>
                </div>
                <div class="video-player-wrap" style="<?=$is_auto?'display:block':'display:none'?>">
                    <video controls playsinline webkit-playsinline x5-playsinline x5-video-player-type="h5" x5-video-player-fullscreen="false" preload="none" <?=$item['default_muted']?'muted':''?> <?=($item['auto_expand'] && $item['auto_play'])?'autoplay':''?> <?=$item['video_loop']?'loop':''?> poster="<?=$item['video_poster']?>">
                        <source src="<?=$item['video_file']?>">
                    </video>
                    <div class="video-close-btn" onclick="closeVideo(this)">×</div>
                </div>
            </div>
        <?php else: ?>
            <div class="footer-bottom-text" style="text-align:<?=$item['footer_align']?>;">
                <?=nl2br(htmlspecialchars($item['title']))?>
            </div>
        <?php endif; ?>
    <?php endwhile; ?>
  </div>
</div>

<?php if(!empty($custom_music)):
    // 检测第三方音乐链接，转为代理播放
    if(preg_match('/(kuwo\.cn\/yinyue|qishui\.douyin\.com|music\.douyin\.com)/', $custom_music)){
        $music_src = 'play.php?url='.urlencode($custom_music);
    } else {
        $music_src = $custom_music;
    }
?>
<audio id="bgMusic" src="<?=$music_src?>" <?=$custom_music_loop?'loop':''?> <?=$custom_music_autoplay?'autoplay':''?>></audio>
<div id="musicBtn" class="<?=$music_btn_class?>" onclick="toggleMusic()">
  <img src="<?=$music_icon_src?>">
</div>
<?php endif; ?>

<div id="passcodeModal">
    <div class="passcode-box">
        <h3 style="margin-bottom:20px;color:#333;">输入4位密码访问</h3>
        <div style="display:flex;justify-content:center;gap:10px;margin-bottom:20px;">
            <input type="text" maxlength="1" class="passcode-input">
            <input type="text" maxlength="1" class="passcode-input">
            <input type="text" maxlength="1" class="passcode-input">
            <input type="text" maxlength="1" class="passcode-input">
        </div>
        <div id="tipText"></div>
        <div style="display:flex;gap:10px;">
            <button id="cancelBtn" style="flex:1;padding:10px;border:1px solid #ddd;background:#f5f5f5;border-radius:8px;cursor:pointer;">取消</button>
            <button id="okBtn" style="flex:1;padding:10px;border:none;background:#7c3aed;color:#fff;border-radius:8px;cursor:pointer;">确认</button>
        </div>
    </div>
</div>

<div id="imageViewer">
  <div class="close" onclick="closeImageViewer()">×</div>
  <img id="viewImg" src="">
</div>

<?php if(!empty($cfg['announcement']) && $cfg['announcement_enabled']): ?>
<div id="announcementModal">
  <div class="announcement-box">
    <div style="text-align:center;font-size:16px;font-weight:700;margin-bottom:12px;color:#333;">提示</div>
    <div class="announcement-content"><?=$cfg['announcement']?></div>
    <div class="announcement-btn-wrap" onclick="closeAnnouncement()">
      <div class="announcement-btn">确定</div>
    </div>
  </div>
</div>
<?php endif; ?>

<script>
let nowId = 0;
const modal = document.getElementById('passcodeModal');
const inputs = document.querySelectorAll('.passcode-input');
const tipText = document.getElementById('tipText');

function countAndShow(id, src){
    fetch('?image_click='+id);
    document.getElementById('viewImg').src = src;
    document.getElementById('imageViewer').style.display = 'flex';
}

function showImage(src){
  document.getElementById('viewImg').src = src;
  document.getElementById('imageViewer').style.display = 'flex';
}
function toggleVideo(btn){
  var mod = btn.parentElement;
  var wrap = mod.querySelector('.video-player-wrap');
  var video = wrap.querySelector('video');
  btn.style.display = 'none';
  wrap.style.display = 'block';
  video.load();
  if(mod.dataset.autoPlay == '1') video.play();
}
function closeVideo(el){
  var wrap = el.parentElement;
  var mod = wrap.parentElement;
  var btn = mod.querySelector('.link-item');
  wrap.style.display = 'none';
  if(btn) btn.style.display = 'flex';
  var video = wrap.querySelector('video');
  if(video) video.pause();
}
function picView(id,src){
  fetch('?image_click='+id);
  showImage(src);
}
function closeImageViewer(){
  document.getElementById('imageViewer').style.display = 'none';
}

function showTip(msg, isError = true) {
    tipText.innerText = msg;
    tipText.className = isError ? 'tip-error' : 'tip-success';
}
function clearTip() {
    tipText.innerText = '';
    tipText.className = '';
}

document.querySelectorAll('.link-item').forEach(item=>{
    if(item.getAttribute('onclick')) return;
    // 无密码链接由浏览器原生 target="_blank" 处理，JS 不拦截
    if(item.getAttribute('href') !== 'javascript:;') return;
    item.addEventListener('click',function(e){
        e.preventDefault();
        nowId = this.dataset.lid;
        clearTip();
        // 有密码的链接弹窗立即弹出，不等 AJAX
        modal.style.display = 'flex';
        inputs.forEach(i=>i.value='');
        inputs[0].focus();

        fetch(`?check_pass=${nowId}`)
        .then(res=>res.json())
        .then(data=>{
            if(!data.need_pass){
                // 会话已验证密码，关弹窗直接跳转
                modal.style.display = 'none';
                window.location.href = `?click=${nowId}`;
            }
        })
        .catch(function(){});
    })
})

inputs.forEach((inp,idx)=>{
    inp.oninput = function(){
        this.value = this.value.replace(/\D/g,'');
        if(this.value && idx < 3) inputs[idx+1].focus();
        clearTip();
    }
    inp.onkeydown = function(e){
        if(e.key==='Backspace' && !this.value && idx>0) inputs[idx-1].focus();
    }
})

document.getElementById('cancelBtn').onclick = function(){
    modal.style.display = 'none';
    clearTip();
}

document.getElementById('okBtn').onclick = function(){
    let pwd = '';
    inputs.forEach(i=>pwd+=i.value);
    clearTip();

    if(pwd.length!==4){
        showTip('请输入4位数字密码');
        return;
    }

    fetch(`?verify_pass=${nowId}&pass=${pwd}`)
    .then(res=>res.json())
    .then(data=>{
        if(data.success){
            showTip('密码正确，请继续操作', false);
            setTimeout(()=>{
                modal.style.display = 'none';
                window.location.href = `?click=${nowId}`;
            },600);
        }else{
            inputs.forEach(i=>i.value='');
            inputs[0].focus();
        }
    });
}
</script>

<script>
let contactVal = '';
let contactLabel = '';
function openContactModal(val,label){contactVal=val;contactLabel=label;document.getElementById('contactLabel').textContent=label;document.getElementById('contactVal').textContent=val;document.getElementById('contactModal').style.display='flex';}
function closeContactModal(){document.getElementById('contactModal').style.display='none';}
function copyContact(){
  if(navigator.clipboard){navigator.clipboard.writeText(contactVal).then(()=>{alert('已复制到剪贴板');closeContactModal();});}
  else{alert('复制失败，请手动复制');}
}
const cm = document.getElementById('contactModal');if(cm)cm.addEventListener('click',function(e){if(e.target===this)closeContactModal();});

document.querySelectorAll('.video-module[data-auto-expand="1"]').forEach(function(m){
  var wrap = m.querySelector('.video-player-wrap');
  if(wrap) wrap.style.display = 'block';
});

const annModal = document.getElementById('announcementModal');
function closeAnnouncement(){
  if(annModal) annModal.style.display = 'none';
}
if(annModal){
  setTimeout(function(){
    annModal.style.display = 'flex';
  }, 1000);
  annModal.addEventListener('click', function(e){
    if(e.target === this) closeAnnouncement();
  });
}

// 金色粒子动画（奢华主题）
var gp = document.getElementById('goldParticles');
if(gp){
  var colors = ['rgba(212,175,55,0.4)','rgba(247,239,138,0.3)','rgba(179,147,46,0.4)','rgba(255,215,0,0.2)'];
  for(var i=0;i<80;i++){
    var p = document.createElement('div');
    p.className = 'gold-particle';
    var s = Math.random()*4+1;
    p.style.width = s+'px'; p.style.height = s+'px';
    p.style.left = Math.random()*100+'%';
    p.style.top = Math.random()*100+'%';
    p.style.background = colors[Math.floor(Math.random()*colors.length)];
    p.style.opacity = Math.random()*0.5+0.2;
    gp.appendChild(p);
  }
  (function animate(){
    var ps = gp.querySelectorAll('.gold-particle');
    ps.forEach(function(p){
      var x = parseFloat(p.style.left), y = parseFloat(p.style.top);
      var sx = parseFloat(p.getAttribute('data-sx')||(Math.random()*0.4-0.2));
      var sy = parseFloat(p.getAttribute('data-sy')||(Math.random()*0.4-0.2));
      if(!p.hasAttribute('data-sx')){p.setAttribute('data-sx',sx);p.setAttribute('data-sy',sy);}
      x += sx; y += sy;
      if(x<0||x>100)p.setAttribute('data-sx',-sx);
      if(y<0||y>100)p.setAttribute('data-sy',-sy);
      p.style.left = Math.max(0,Math.min(100,x))+'%';
      p.style.top = Math.max(0,Math.min(100,y))+'%';
    });
    requestAnimationFrame(animate);
  })();
}
</script>

<div id="contactModal">
  <div class="contact-modal-box">
    <div style="font-size:16px;font-weight:700;margin-bottom:4px;color:#333;" id="contactLabel"></div>
    <div class="contact-val" id="contactVal"></div>
    <div class="contact-btns">
      <div class="btn-cancel" onclick="closeContactModal()">取消</div>
      <div class="btn-copy" onclick="copyContact()">复制</div>
    </div>
  </div>
</div>

<?php if(!empty($show_open_tip)): ?>
<div id="openTipOverlay">
  <img src="img/jiantou.png" class="tip-arrow">
  <div class="tip-title">请在浏览器中打开</div>
  <div class="tip-desc">
    <?=$show_open_tip?>内无法正常访问链接<br>
    请点击右上角 ••• 选择「在浏览器打开」
  </div>
  <button class="tip-btn" onclick="copyUrl(this)">复制链接</button>
  <div class="tip-small">仅支持浏览器访问</div>
</div>
<?php endif; ?>

<?php if(!empty($custom_music)): ?>
<script>
function copyUrl(btn){
  const url = window.location.href;
  if(navigator.clipboard){navigator.clipboard.writeText(url).then(()=>{btn.textContent='已复制';setTimeout(()=>{btn.textContent='复制网址'},2000);});}
  else{alert('复制失败，请手动复制');}
}
const bgMusic = document.getElementById('bgMusic');
const musicBtn = document.getElementById('musicBtn');
let musicPlaying = <?=$custom_music_autoplay?'true':'false'?>;

<?php if($custom_music_autoplay): ?>
// 浏览器限制自动播放，首次点击页面任意位置触发
document.addEventListener('click', function(){bgMusic.play();}, {once:true});
<?php endif; ?>

function toggleMusic(){
  if(musicPlaying){
    bgMusic.pause();
    musicBtn.classList.remove('spin');
  } else {
    bgMusic.play();
    musicBtn.classList.add('spin');
  }
  musicPlaying = !musicPlaying;
}

bgMusic.addEventListener('pause', function(){
  musicBtn.classList.remove('spin');
  musicPlaying = false;
});
bgMusic.addEventListener('play', function(){
  musicBtn.classList.add('spin');
  musicPlaying = true;
});
</script>
<?php endif; ?>

</body>
</html>