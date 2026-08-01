<form method="post" enctype="multipart/form-data">
<div class="card">
  <div class="card-title">背景主题</div>
  <p style="font-size:13px;color:#888;margin-bottom:12px;">选择预设背景，或选择"自定义"自行调配</p>
  <div class="bg-preview-group">
    <div class="bg-preview-item">
      <img src="img/1.jpg" data-val="#f0f0f0">
      <p>默认</p>
      <input type="hidden" name="bg_color" value="#f0f0f0">
    </div>
    <div class="bg-preview-item">
      <img src="img/2.jpg" data-val="#111111">
      <p>黑色</p>
      <input type="hidden" name="bg_color" value="#111111">
    </div>
    <div class="bg-preview-item">
      <img src="img/3.jpg" data-val="#7c3aed">
      <p>紫色</p>
      <input type="hidden" name="bg_color" value="#7c3aed">
    </div>
    <div class="bg-preview-item">
      <img src="img/4.jpg" data-val="#F472B6">
      <p>粉色</p>
      <input type="hidden" name="bg_color" value="#F472B6">
    </div>
    <div class="bg-preview-item">
      <img src="img/5.jpg" data-val="deep">
      <p>深邃</p>
      <input type="hidden" name="bg_color" value="deep">
    </div>
    <div class="bg-preview-item">
      <img src="img/9.jpg" data-val="cyber">
      <p>蓝格</p>
      <input type="hidden" name="bg_color" value="cyber">
    </div>
    <div class="bg-preview-item">
      <img src="img/2.jpg" data-val="gold">
      <p>黑(动态)</p>
      <input type="hidden" name="bg_color" value="gold">
    </div>
    <div class="bg-preview-item">
      <img src="img/7.jpg" data-val="custom" style="border:2px dashed #007bff;opacity:0.7">
      <p>自定义</p>
      <input type="hidden" name="bg_color" value="custom">
    </div>
  </div>
</div>

<div id="customSettingsWrapper" class="custom-settings-wrapper" style="display:<?=($bg_color==='custom'?'block':'none')?>">
<div class="card" id="customStyleSettings">
  <div class="card-title">自定义背景设置</div>
  <div class="bg-type-select">
    <label class="radio-card <?=($cfg['custom_bg_type']??'color')==='color'?'active':''?>">
      <input type="radio" name="custom_bg_type" value="color" onchange="switchBgType('color')" <?=($cfg['custom_bg_type']??'color')==='color'?'checked':''?>> 纯色
    </label>
    <label class="radio-card <?=($cfg['custom_bg_type']??'')==='gradient'?'active':''?>">
      <input type="radio" name="custom_bg_type" value="gradient" onchange="switchBgType('gradient')" <?=($cfg['custom_bg_type']??'')==='gradient'?'checked':''?>> 渐变色
    </label>
    <label class="radio-card <?=($cfg['custom_bg_type']??'')==='image'?'active':''?>">
      <input type="radio" name="custom_bg_type" value="image" onchange="switchBgType('image')" <?=($cfg['custom_bg_type']??'')==='image'?'checked':''?>> 背景图
    </label>
  </div>

  <div id="bg_fields_color" class="bg-type-fields" style="display:<?=($cfg['custom_bg_type']??'color')==='color'?'block':'none'?>">
    <div class="color-field"><label>背景颜色</label><input type="color" name="custom_bg_color" value="<?=$cfg['custom_bg_value']??'#f0f0f0'?>"></div>
  </div>

  <div id="bg_fields_gradient" class="bg-type-fields gradient-row" style="display:<?=($cfg['custom_bg_type']??'')==='gradient'?'flex':'none'?>">
    <div class="color-field"><label>起始颜色</label><input type="color" name="custom_gradient_from" value="<?=$cfg['custom_gradient_from']??'#667eea'?>"></div>
    <div class="color-field"><label>结束颜色</label><input type="color" name="custom_gradient_to" value="<?=$cfg['custom_gradient_to']??'#764ba2'?>"></div>
    <div class="color-field"><label>渐变方向</label>
      <select name="custom_gradient_dir" class="sm-select">
        <option value="135deg" <?=($cfg['custom_gradient_dir']??'')==='135deg'?'selected':''?>>↘ 右下</option>
        <option value="45deg" <?=($cfg['custom_gradient_dir']??'')==='45deg'?'selected':''?>>↗ 右上</option>
        <option value="90deg" <?=($cfg['custom_gradient_dir']??'')==='90deg'?'selected':''?>>→ 向右</option>
        <option value="180deg" <?=($cfg['custom_gradient_dir']??'')==='180deg'?'selected':''?>>↓ 向下</option>
        <option value="0deg" <?=($cfg['custom_gradient_dir']??'')==='0deg'?'selected':''?>>↑ 向上</option>
      </select>
    </div>
  </div>

  <div id="bg_fields_image" class="bg-type-fields" style="display:<?=($cfg['custom_bg_type']??'')==='image'?'block':'none'?>">
    <div class="color-field">
      <label>上传图片</label>
      <?php if(!empty($cfg['custom_bg_image'])): ?>
        <div style="display:flex;align-items:center;gap:10px;">
          <img src="<?=$cfg['custom_bg_image']?>" style="max-width:100px;max-height:60px;border-radius:4px;">
          <span style="color:#999;font-size:12px;">已上传</span>
        </div>
      <?php endif; ?>
      <input type="file" name="custom_bg_image_file" accept="image/*">
    </div>
  </div>
</div>

<div class="card">
  <div class="card-title">按钮样式</div>
  <div class="style-grid">
    <div class="color-field"><label>按钮底色</label><input type="color" name="custom_btn_bg" value="<?=$cfg['custom_btn_bg']??'#ffffff'?>"></div>
    <div class="color-field"><label>文字颜色</label><input type="color" name="custom_btn_color" value="<?=$cfg['custom_btn_color']??'#222222'?>"></div>
    <div class="color-field"><label>空心边框色</label><input type="color" name="custom_btn_outline_color" value="<?=$cfg['custom_btn_outline_color']??'#222222'?>"></div>
    <div class="color-field"><label>箭头颜色</label><input type="color" name="custom_arrow_color" value="<?=$cfg['custom_arrow_color']??'#888888'?>"></div>
    <div class="color-field">
      <label style="min-width:auto;gap:6px"><input type="checkbox" name="custom_btn_arrow" value="1" <?=$cfg['custom_btn_arrow']?'checked':''?>> 显示右侧箭头</label>
    </div>
  </div>
</div>

<div class="card">
  <div class="card-title">文字颜色</div>
  <div class="style-grid">
    <div class="color-field"><label>网站标题</label><input type="color" name="custom_title_color" value="<?=$cfg['custom_title_color']??'#222222'?>"></div>
    <div class="color-field"><label>个人简介</label><input type="color" name="custom_desc_color" value="<?=$cfg['custom_desc_color']??'#333333'?>"></div>
    <div class="color-field"><label>展示文字</label><input type="color" name="custom_text_color" value="<?=$cfg['custom_text_color']??'#333333'?>"></div>
  </div>
</div>

<div class="save-wrapper">
  </div>
</div>

<div class="save-wrapper">
  <button name="save_custom_style" class="btn-save">保存风格</button>
</div>
</form>
