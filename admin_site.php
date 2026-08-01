<form method="post" enctype="multipart/form-data">
<div class="card">
  <div class="card-title">基本信息</div>
  <div class="site-setting-row">
    <div class="avatar-col">
      <div class="avatar-wrap">
        <?php if(!empty($cfg['avatar'])): ?>
          <img src="<?=$cfg['avatar']?>" class="avatar-preview" id="previewImg">
          <a href="?del_avatar=1" class="avatar-del" onclick="return confirm('确定删除？')">×</a>
        <?php else: ?>
          <div class="avatar-preview"></div>
        <?php endif; ?>
      </div>
      <div class="avatar-upload-wrapper">
        <div class="avatar-upload-btn">上传头像</div>
        <input type="file" name="avatar" accept="image/*" onchange="previewAvatar(this)">
      </div>
    </div>
    <div class="input-col">
      <div class="top-row">
        <input type="text" name="title" class="site-input" value="<?=$cfg['title']?>" placeholder="主页名称">
        <input type="text" name="keywords" class="site-input" value="<?=$cfg['keywords']?>" placeholder="浏览器标题">
      </div>
      <textarea name="description" class="site-textarea" placeholder="个人简介"><?=$cfg['description']?></textarea>
    </div>
  </div>
</div>

<div class="card">
  <div class="card-title">联系方式</div>
  <p style="font-size:13px;color:#888;margin-bottom:12px;">填写链接则可点击跳转，填写账号则弹窗复制</p>
  <?php
  $contact_fields = [
    'qq' => 'QQ',
    'wechat' => '微信',
    'email' => '邮箱',
    'phone' => '手机号',
    'qq_group' => 'QQ群'
  ];
  foreach($contact_fields as $key => $label):
    $val = $cfg['contact_'.$key] ?? '';
    $icon = $cfg['contact_'.$key.'_icon'] ?? '';
  ?>
  <div class="contact-row">
    <span class="contact-label"><?=$label?></span>
    <input type="text" name="contact_<?=$key?>" class="site-input" value="<?=htmlspecialchars($val)?>" placeholder="填写<?=$label?>号">
    <div class="contact-icon-wrap">
      <?php if(!empty($icon)): ?>
        <div class="contact-icon-preview">
          <img src="<?=$icon?>" class="contact-icon-img">
          <a href="?del_contact_icon=<?=$key?>" class="contact-icon-del" onclick="return confirm('确定删除图标？')">×</a>
        </div>
      <?php endif; ?>
      <div class="avatar-upload-wrapper">
        <div class="upload-btn-box">图标</div>
        <input type="file" name="contact_<?=$key?>_icon" accept="image/*" onchange="previewContactIcon(this,'contact_icon_<?=$key?>')">
      </div>
      <div id="contact_icon_<?=$key?>" class="contact-icon-preview-new" style="display:none"></div>
    </div>
  </div>
  <?php endforeach; ?>
</div>

<div class="card">
  <div class="card-title">公告弹窗</div>
  <label class="switch-label">
    <input type="checkbox" name="announcement_enabled" value="1" <?=$cfg['announcement_enabled']?'checked':''?>>
    <span class="switch-slider"></span>
    <span>启用公告弹窗 <span style="font-size:12px;color:#999;font-weight:400">（如添加并开启了视频模块中的自动展开功能，部分浏览器会遮盖公告）</span></span>
  </label>
  <textarea name="announcement" class="site-textarea" style="margin-top:12px" placeholder="在此输入公告内容，支持HTML"><?=htmlspecialchars($cfg['announcement']??'')?></textarea>
</div>

<div class="card">
  <div class="card-title">背景音乐</div>
  <!-- 第一行：状态 + 上传按钮 -->
  <div class="music-row">
    <?php if(!empty($cfg['custom_music'])): ?>
      <?php
        $_music_full = basename($cfg['custom_music']);
        $_music_is_url = preg_match('/^https?:\/\//', $cfg['custom_music']);
      ?>
      <span class="music-status"><?=$_music_is_url?'音乐地址':'已上传'?>: <?=htmlspecialchars($_music_is_url ? $cfg['custom_music'] : $_music_full)?></span>
      <a href="?del_music=1" class="music-del" onclick="return confirm('确定删除音乐？')">删除</a>
    <?php endif; ?>
    <div class="avatar-upload-wrapper">
      <div class="avatar-upload-btn" id="musicUploadBtn">上传音乐</div>
      <input type="file" id="musicFileInput" accept=".mp3,.flac">
    </div>
    <!-- 上传进度条 -->
    <div id="musicProgressWrap" style="display:none;flex:1;min-width:150px;">
      <div style="background:#f0f2f5;border-radius:6px;height:8px;overflow:hidden;">
        <div id="musicProgressBar" style="height:100%;width:0%;background:linear-gradient(135deg,#6366f1,#a78bfa);border-radius:6px;transition:width 0.3s;"></div>
      </div>
      <span id="musicProgressText" style="font-size:12px;color:#888;margin-top:2px;display:block;">准备上传...</span>
    </div>
    <input type="hidden" name="custom_music_uploaded" id="customMusicUploaded" value="">
  </div>
  <!-- 第二行：音乐链接 -->
  <div class="music-row" style="margin-top:8px;">
    <input type="text" name="custom_music_url" class="site-input" value="<?=(!empty($cfg['custom_music']) && preg_match('/^https?:\/\//', $cfg['custom_music'])) ? htmlspecialchars($cfg['custom_music']) : ''?>" placeholder="粘贴歌曲链接" style="max-width:400px;flex:1;">
    <span style="font-size:13px;color:#888;white-space:nowrap;">支持直连和酷我/汽水的免费歌曲链接</span>
  </div>
  <!-- 第三行：循环播 / 自动播 / 图标 -->
  <div class="music-row" style="margin-top:8px;">
    <label class="inline-label">
      <input type="checkbox" name="custom_music_loop" value="1" <?=$cfg['custom_music_loop']?'checked':''?>> 循环播
    </label>
    <label class="inline-label">
      <input type="checkbox" name="custom_music_autoplay" value="1" <?=$cfg['custom_music_autoplay']?'checked':''?>> 自动播
    </label>
    <span class="sep">|</span>
    <span class="music-icon-label">图标:</span>
    <label class="inline-label">
      <input type="radio" name="custom_music_icon" value="b" <?=($cfg['custom_music_icon']??'b')==='b'?'checked':''?>>
      <img src="img/music_b.png" style="width:18px;height:18px;vertical-align:middle;background:#333;border-radius:50%;">
    </label>
    <label class="inline-label">
      <input type="radio" name="custom_music_icon" value="h" <?=($cfg['custom_music_icon']??'')==='h'?'checked':''?>>
      <img src="img/music_h.png" style="width:18px;height:18px;vertical-align:middle;">
    </label>
  </div>
</div>

<script>
document.getElementById('musicFileInput').addEventListener('change', function(){
  var file = this.files[0];
  if(!file) return;
  var ext = file.name.split('.').pop().toLowerCase();
  if(['mp3','flac'].indexOf(ext) === -1){
    alert('仅支持 MP3 和 FLAC 格式');
    this.value = '';
    return;
  }
  var fd = new FormData();
  fd.append('music_file', file);
  var xhr = new XMLHttpRequest();
  xhr.upload.addEventListener('progress', function(e){
    if(e.lengthComputable){
      var pct = Math.round(e.loaded / e.total * 100);
      document.getElementById('musicProgressBar').style.width = pct + '%';
      document.getElementById('musicProgressText').textContent = '上传中 ' + pct + '%';
    }
  });
  xhr.addEventListener('load', function(){
    if(xhr.status === 200){
      try{
        var r = JSON.parse(xhr.responseText);
        if(r.success){
          document.getElementById('musicProgressText').textContent = '✓ 上传完成';
          document.getElementById('musicProgressBar').style.width = '100%';
          document.getElementById('customMusicUploaded').value = r.path;
        } else {
          document.getElementById('musicProgressText').textContent = '✗ ' + (r.error || '上传失败');
        }
      }catch(e){
        document.getElementById('musicProgressText').textContent = '✗ 解析响应失败';
      }
    } else {
      document.getElementById('musicProgressText').textContent = '✗ 上传失败（' + xhr.status + '）';
    }
  });
  xhr.addEventListener('error', function(){
    document.getElementById('musicProgressText').textContent = '✗ 网络错误';
  });
  document.getElementById('musicProgressWrap').style.display = 'block';
  document.getElementById('musicProgressText').textContent = '正在上传...';
  document.getElementById('musicProgressBar').style.width = '0%';
  xhr.open('POST', 'admin.php?act=ajax_upload_music', true);
  xhr.send(fd);
});
</script>

<div class="card">
  <div class="card-title">在浏览器中打开</div>
  <p style="font-size:13px;color:#888;margin-bottom:12px;">勾选平台后，在该平台内打开前台时会提示跳转浏览器</p>
  <div style="display:flex;gap:16px;flex-wrap:wrap;">
    <label class="inline-label"><input type="checkbox" name="open_tip_wechat" value="1" <?=$cfg['open_tip_wechat']?'checked':''?>> 微信</label>
    <label class="inline-label"><input type="checkbox" name="open_tip_qq" value="1" <?=$cfg['open_tip_qq']?'checked':''?>> QQ</label>
    <label class="inline-label"><input type="checkbox" name="open_tip_douyin" value="1" <?=$cfg['open_tip_douyin']?'checked':''?>> 抖音</label>
    <label class="inline-label"><input type="checkbox" name="open_tip_weibo" value="1" <?=$cfg['open_tip_weibo']?'checked':''?>> 微博</label>
  </div>
</div>
<div class="save-wrapper">
  <button name="save_all" class="btn-save">保存所有设置</button>
</div>
</form>
