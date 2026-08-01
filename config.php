<?php
// Session 持久化（后台登录7天内免重复登录）
ini_set('session.cookie_lifetime', 604800);
ini_set('session.gc_maxlifetime', 604800);
session_set_cookie_params(['lifetime' => 604800, 'path' => '/', 'samesite' => 'Lax']);
session_start();
ini_set('display_errors', 0);
error_reporting(0);

$db = new SQLite3('link3.db');

$db->exec('CREATE TABLE IF NOT EXISTS config (
    id INTEGER PRIMARY KEY, 
    title TEXT, 
    keywords TEXT, 
    description TEXT, 
    avatar TEXT, 
    avatar_show INTEGER DEFAULT 1, 
    btn_radius INTEGER DEFAULT 30, 
    btn_radius_on INTEGER DEFAULT 1, 
    text_center INTEGER DEFAULT 0, 
    bg_color TEXT DEFAULT "#f0f0f0",
    footer_text TEXT DEFAULT "",
    footer_align TEXT DEFAULT "left"
)');

$db->exec('CREATE TABLE IF NOT EXISTS links (
    id INTEGER PRIMARY KEY, 
    title TEXT, 
    url TEXT, 
    icon TEXT, 
    sort INTEGER DEFAULT 0, 
    outline INTEGER DEFAULT 0,
    passcode TEXT DEFAULT "",
    type TEXT DEFAULT "link",
    footer_align TEXT DEFAULT "left"
)');

$db->exec('CREATE TABLE IF NOT EXISTS link_stats (date TEXT, type TEXT, target TEXT, count INTEGER DEFAULT 0)');

try {
    $db->exec("ALTER TABLE links ADD COLUMN passcode TEXT DEFAULT ''");
} catch (Exception $e) {}
try {
    $db->exec("ALTER TABLE links ADD COLUMN type TEXT DEFAULT 'link'");
} catch (Exception $e) {}
try {
    $db->exec("ALTER TABLE links ADD COLUMN footer_align TEXT DEFAULT 'left'");
} catch (Exception $e) {}
try {
    $db->exec("ALTER TABLE config ADD COLUMN footer_text TEXT DEFAULT ''");
} catch (Exception $e) {}
try {
    $db->exec("ALTER TABLE config ADD COLUMN footer_align TEXT DEFAULT 'left'");
} catch (Exception $e) {}
try {
    $db->exec("ALTER TABLE config ADD COLUMN announcement TEXT DEFAULT ''");
} catch (Exception $e) {}
try {
    $db->exec("ALTER TABLE config ADD COLUMN announcement_enabled INTEGER DEFAULT 0");
} catch (Exception $e) {}
try {
    $db->exec("ALTER TABLE config ADD COLUMN custom_bg_type TEXT DEFAULT 'color'");
} catch (Exception $e) {}
try {
    $db->exec("ALTER TABLE config ADD COLUMN custom_bg_value TEXT DEFAULT '#f0f0f0'");
} catch (Exception $e) {}
try {
    $db->exec("ALTER TABLE config ADD COLUMN custom_btn_bg TEXT DEFAULT '#ffffff'");
} catch (Exception $e) {}
try {
    $db->exec("ALTER TABLE config ADD COLUMN custom_btn_color TEXT DEFAULT '#222222'");
} catch (Exception $e) {}
try {
    $db->exec("ALTER TABLE config ADD COLUMN custom_btn_arrow INTEGER DEFAULT 1");
} catch (Exception $e) {}
try {
    $db->exec("ALTER TABLE config ADD COLUMN custom_btn_shape TEXT DEFAULT 'round'");
} catch (Exception $e) {}
try {
    $db->exec("ALTER TABLE config ADD COLUMN custom_gradient_from TEXT DEFAULT '#667eea'");
} catch (Exception $e) {}
try {
    $db->exec("ALTER TABLE config ADD COLUMN custom_gradient_to TEXT DEFAULT '#764ba2'");
} catch (Exception $e) {}
try {
    $db->exec("ALTER TABLE config ADD COLUMN custom_gradient_dir TEXT DEFAULT '135deg'");
} catch (Exception $e) {}
try {
    $db->exec("ALTER TABLE config ADD COLUMN custom_bg_image TEXT DEFAULT ''");
} catch (Exception $e) {}
try {
    $db->exec("ALTER TABLE config ADD COLUMN custom_btn_outline_color TEXT DEFAULT '#222222'");
} catch (Exception $e) {}
try {
    $db->exec("ALTER TABLE config ADD COLUMN custom_arrow_color TEXT DEFAULT '#888888'");
} catch (Exception $e) {}
try {
    $db->exec("ALTER TABLE config ADD COLUMN custom_title_color TEXT DEFAULT '#222222'");
} catch (Exception $e) {}
try {
    $db->exec("ALTER TABLE config ADD COLUMN custom_desc_color TEXT DEFAULT '#333333'");
} catch (Exception $e) {}
try {
    $db->exec("ALTER TABLE config ADD COLUMN custom_text_color TEXT DEFAULT '#333333'");
} catch (Exception $e) {}
try {
    $db->exec("ALTER TABLE config ADD COLUMN admin_pwd TEXT DEFAULT 'admin888'");
} catch (Exception $e) {}
try {
    $db->exec("ALTER TABLE config ADD COLUMN custom_music TEXT DEFAULT ''");
} catch (Exception $e) {}
try {
    $db->exec("ALTER TABLE config ADD COLUMN custom_music_loop INTEGER DEFAULT 0");
} catch (Exception $e) {}
try {
    $db->exec("ALTER TABLE config ADD COLUMN custom_music_autoplay INTEGER DEFAULT 0");
} catch (Exception $e) {}
try {
    $db->exec("ALTER TABLE config ADD COLUMN custom_music_icon TEXT DEFAULT 'b'");
} catch (Exception $e) {}
try {
    $db->exec("ALTER TABLE config ADD COLUMN admin_user TEXT DEFAULT 'admin'");
} catch (Exception $e) {}
try {
    $db->exec("ALTER TABLE config ADD COLUMN admin_captcha INTEGER DEFAULT 1");
} catch (Exception $e) {}
try {
    $db->exec("ALTER TABLE config ADD COLUMN contact_qq TEXT DEFAULT ''");
} catch (Exception $e) {}
try {
    $db->exec("ALTER TABLE config ADD COLUMN contact_qq_icon TEXT DEFAULT ''");
} catch (Exception $e) {}
try {
    $db->exec("ALTER TABLE config ADD COLUMN contact_wechat TEXT DEFAULT ''");
} catch (Exception $e) {}
try {
    $db->exec("ALTER TABLE config ADD COLUMN contact_wechat_icon TEXT DEFAULT ''");
} catch (Exception $e) {}
try {
    $db->exec("ALTER TABLE config ADD COLUMN contact_email TEXT DEFAULT ''");
} catch (Exception $e) {}
try {
    $db->exec("ALTER TABLE config ADD COLUMN contact_email_icon TEXT DEFAULT ''");
} catch (Exception $e) {}
try {
    $db->exec("ALTER TABLE config ADD COLUMN contact_phone TEXT DEFAULT ''");
} catch (Exception $e) {}
try {
    $db->exec("ALTER TABLE config ADD COLUMN contact_phone_icon TEXT DEFAULT ''");
} catch (Exception $e) {}
try {
    $db->exec("ALTER TABLE config ADD COLUMN open_tip_wechat INTEGER DEFAULT 0");
} catch (Exception $e) {}
try {
    $db->exec("ALTER TABLE config ADD COLUMN open_tip_qq INTEGER DEFAULT 0");
} catch (Exception $e) {}
try {
    $db->exec("ALTER TABLE config ADD COLUMN open_tip_douyin INTEGER DEFAULT 0");
} catch (Exception $e) {}
try {
    $db->exec("ALTER TABLE config ADD COLUMN open_tip_weibo INTEGER DEFAULT 0");
} catch (Exception $e) {}

$cfg = $db->querySingle('SELECT * FROM config WHERE id=1', true);
if(!$cfg){
    $db->exec("INSERT INTO config (id,title,keywords,description,avatar,avatar_show,btn_radius,btn_radius_on,text_center,bg_color,footer_text,footer_align,announcement,announcement_enabled,
        custom_bg_type,custom_bg_value,custom_btn_bg,custom_btn_color,custom_btn_arrow,custom_btn_shape,custom_gradient_from,custom_gradient_to,custom_gradient_dir,custom_bg_image,
        custom_btn_outline_color,custom_arrow_color,custom_title_color,custom_desc_color,custom_text_color,admin_pwd,
        custom_music,custom_music_loop,custom_music_autoplay,custom_music_icon,admin_user,admin_captcha,
        contact_qq,contact_qq_icon,contact_wechat,contact_wechat_icon,contact_email,contact_email_icon,contact_phone,contact_phone_icon,
        open_tip_wechat,open_tip_qq,open_tip_douyin,open_tip_weibo)
    VALUES (1,'我的Link3主页','link3,主页','高仿Link3个人主页','',1,30,1,0,'#f0f0f0','','left','',0,
        'color','#f0f0f0','#ffffff','#222222',1,'round','#667eea','#764ba2','135deg','',
        '#222222','#888888','#222222','#333333','#333333','admin888',
        '',0,0,'b','admin',1,
        '','','','','','','','',
        0,0,0,0)");
    $cfg = $db->querySingle('SELECT * FROM config WHERE id=1', true);
}

define('ADMIN_PWD', $cfg['admin_pwd'] ?? 'admin888');
define('ADMIN_USER', $cfg['admin_user'] ?? 'admin');

if(!is_dir('uploads')) mkdir('uploads', 0755);

function is_login(){
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    return isset($_SESSION['admin_login']) && $_SESSION['admin_login'] === true;
}

function safe($str){
    global $db;
    return $db->escapeString(trim($str));
}

function today(){
    return date('Y-m-d');
}

function add_view(){
    global $db;
    $t = today();
    $db->exec("INSERT OR IGNORE INTO link_stats (date,type,target,count) VALUES ('$t','view','home',0)");
    $db->exec("UPDATE link_stats SET count=count+1 WHERE date='$t' AND type='view' AND target='home'");
}

function add_click($id){
    global $db;
    $t = today();
    $id = (int)$id;
    $db->exec("INSERT OR IGNORE INTO link_stats (date,type,target,count) VALUES ('$t','click','$id',0)");
    $db->exec("UPDATE link_stats SET count=count+1 WHERE date='$t' AND type='click' AND target=$id");
}

function get_7days(){
    $days = [];
    for($i=6;$i>=0;$i--){
        $days[] = date('Y-m-d', strtotime("-$i days"));
    }
    return $days;
}

function clear_old_data(){
    global $db;
    $d = date('Y-m-d', strtotime('-7 days'));
    $db->exec("DELETE FROM link_stats WHERE date < '$d'");
}
clear_old_data();
try {$db->exec("ALTER TABLE links ADD COLUMN video_file TEXT DEFAULT ''");}catch(Exception $e){}
try {$db->exec("ALTER TABLE links ADD COLUMN auto_expand INTEGER DEFAULT 0");}catch(Exception $e){}
try {$db->exec("ALTER TABLE links ADD COLUMN auto_play INTEGER DEFAULT 0");}catch(Exception $e){}
try {$db->exec("ALTER TABLE links ADD COLUMN default_muted INTEGER DEFAULT 0");}catch(Exception $e){}
try {$db->exec("ALTER TABLE links ADD COLUMN video_loop INTEGER DEFAULT 0");}catch(Exception $e){}
try {$db->exec("ALTER TABLE links ADD COLUMN video_poster TEXT DEFAULT ''");}catch(Exception $e){}
try {$db->exec("ALTER TABLE config ADD COLUMN contact_qq_group TEXT DEFAULT ''");}catch(Exception $e){}
try {$db->exec("ALTER TABLE config ADD COLUMN contact_qq_group_icon TEXT DEFAULT ''");}catch(Exception $e){}

// ===== 系统完整性凭据（修改将导致前端无法显示）=====
$GLOBALS['_cp_author'] = '匆匆过客';
$GLOBALS['_cp_wx'] = 'https://i.imgs.ovh/2026/06/23/fe56ae205a348cee0c7daa6e5acaa93e.png';
$GLOBALS['_cp_xy'] = 'https://i.imgs.ovh/2026/06/23/e8ea7235edb2633b6c7c774d700caf54.png';
// ===== END =====
?>