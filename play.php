<?php
$url = $_GET['url'] ?? '';
if(!$url){
    header('HTTP/1.0 400 Bad Request');
    exit;
}

// ============================================================
// 1. 酷我音乐
// ============================================================
if(preg_match('/kuwo\.cn\/yinyue\/(\d+)/', $url, $m)){
    $id = $m[1];
    $api = 'http://antiserver.kuwo.cn/anti.s?type=convert_url&rid='.$id.'&format=mp3';
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $api,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_TIMEOUT => 15,
        CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
        CURLOPT_REFERER => 'http://www.kuwo.cn/',
    ]);
    $play_url = trim(curl_exec($ch));
    curl_close($ch);

    if($play_url && preg_match('/^https?:\/\//', $play_url)){
        header('Content-Type: audio/mpeg');
        header('Accept-Ranges: none');
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $play_url,
            CURLOPT_FILE => fopen('php://output', 'w'),
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_BUFFERSIZE => 8192,
            CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
            CURLOPT_REFERER => 'http://www.kuwo.cn/',
        ]);
        curl_exec($ch);
        curl_close($ch);
        exit;
    }
}

// ============================================================
// 2. 汽水音乐 (qishui.douyin.com / music.douyin.com)
// ============================================================
$qishui_track_id = '';
if(preg_match('/qishui\.douyin\.com\/s\/([a-zA-Z0-9]+)/', $url, $m)){
    // 短链，需先跟跳转获取 track_id
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_TIMEOUT => 15,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
    ]);
    curl_exec($ch);
    $final_url = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
    curl_close($ch);
    parse_str(parse_url($final_url, PHP_URL_QUERY), $qparams);
    $qishui_track_id = $qparams['track_id'] ?? '';
}
elseif(preg_match('/(?:music\.douyin\.com|qishui\.douyin\.com)\/.*track_id=(\d+)/', $url, $m)){
    $qishui_track_id = $m[1];
}
elseif(preg_match('/douyin\.com\/qishui\/song\/(\d+)/', $url, $m)){
    $qishui_track_id = $m[1];
}

if($qishui_track_id){
    // 获取分享页面，提取 loaderData 中的音频地址
    $page_url = 'https://music.douyin.com/qishui/share/track?track_id='.$qishui_track_id;
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $page_url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_TIMEOUT => 20,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
        CURLOPT_REFERER => 'https://music.douyin.com/',
    ]);
    $html = curl_exec($ch);
    curl_close($ch);

    // 提取 _ROUTER_DATA JSON（逐括号匹配，支持嵌套）
    $audio_url = '';
    $rd_start = strpos($html, '_ROUTER_DATA = ');
    if($rd_start !== false){
        $rd_start += strlen('_ROUTER_DATA = ');
        $depth = 0;
        $rd_end = $rd_start;
        for($i = $rd_start; $i < strlen($html); $i++){
            if($html[$i] === '{') $depth++;
            elseif($html[$i] === '}') { $depth--; if($depth === 0) { $rd_end = $i; break; } }
        }
        $json = substr($html, $rd_start, $rd_end - $rd_start + 1);
        $router = json_decode($json, true);
        if($router){
            $audio_url = $router['loaderData']['track_page']['audioWithLyricsOption']['url'] ?? '';
        }
    }
    if($audio_url){
            // 代理音频流
            $audio_url = str_replace('\\/', '/', $audio_url);
            $audio_url = preg_replace('/^http:/i', 'https:', $audio_url);

            header('Content-Type: audio/mp4');
            header('Accept-Ranges: none');

            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => $audio_url,
                CURLOPT_FILE => fopen('php://output', 'w'),
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_BUFFERSIZE => 8192,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                CURLOPT_REFERER => 'https://music.douyin.com/',
            ]);
            curl_exec($ch);
            curl_close($ch);
            exit;
        }

    http_response_code(502);
    exit('无法获取汽水音乐音频源');
}

http_response_code(404);
exit('不支持的链接格式');
