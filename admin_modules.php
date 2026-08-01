<div class="card">
  <div class="toolbar">
    <div class="toolbar-left">
      <span class="toolbar-info">共 <?php $link_list_all = $db->query('SELECT COUNT(*) as cnt FROM links'); $link_count = $link_list_all->fetchArray(SQLITE3_ASSOC); echo $link_count['cnt']; ?> 个模块</span>
    </div>
    <div class="toolbar-right">
      <a href="javascript:openAddModal()" class="btn-add"><img src="img/add.png" class="btn-add-icon"> 链接</a>
      <a href="javascript:openAddTextModal()" class="btn-add"><img src="img/add.png" class="btn-add-icon"> 文字</a>
      <a href="javascript:openAddImageModal()" class="btn-add"><img src="img/add.png" class="btn-add-icon"> 弹图</a>
      <a href="javascript:openAddPictureModal()" class="btn-add"><img src="img/add.png" class="btn-add-icon"> 图片</a>
	      <a href="javascript:openAddVideoModal()" class="btn-add"><img src="img/add.png" class="btn-add-icon"> 视频</a>
    </div>
  </div>
</div>

<div class="module-preview-list" id="modulePreviewList">
  <?php $link_list = $db->query('SELECT * FROM links ORDER BY sort DESC,id DESC'); ?>
  <?php while($l = $link_list->fetchArray(SQLITE3_ASSOC)):
    $radius = $l['btn_radius_on'] == 1 ? (int)$l['btn_radius'] : 0;
  ?>
  <div class="module-preview-item" draggable="true" data-id="<?=$l['id']?>">
    <span style="cursor:grab;color:#bbb;font-size:14px;flex-shrink:0;user-select:none;">⠿</span>

    <?php if($l['type']=='text'): ?>
      <span class="preview-type-badge text">文字</span>
    <?php elseif($l['type']=='image'): ?>
      <span class="preview-type-badge image">弹图</span>
    <?php elseif($l['type']=='picture'): ?>
      <span class="preview-type-badge image">图片</span>
    <?php elseif($l['type']=='video'): ?>
      <span class="preview-type-badge link">视频</span>
    <?php else: ?>
      <span class="preview-type-badge link">链接</span>
    <?php endif; ?>

    <?php if($l['type'] == 'link'): ?>
    <div class="preview-col">
      <div class="preview-link-btn<?=$l['outline']?' outline':''?><?=$l['text_center']?' text-center':''?>" style="border-radius:<?=$radius?>px">
        <?php if(!empty($l['icon'])): ?>
        <img src="<?=$l['icon']?>" class="preview-link-icon">
        <?php endif; ?>
        <span class="preview-link-title"><?=htmlspecialchars($l['title'])?></span>
        <?php if(!empty($l['passcode'])): ?><span style="font-size:13px;margin-right:2px;flex-shrink:0;">🔒</span><?php endif; ?>
        <svg class="preview-link-arrow" viewBox="0 0 24 24"><path d="M8.59 16.59L13.17 12 8.59 7.41 10 6l6 6-6 6-1.41-1.41z"/></svg>
      </div>
    </div>

    <?php elseif($l['type'] == 'text'): ?>
    <div class="preview-text" style="text-align:<?=$l['footer_align']?>">
      <?=nl2br(htmlspecialchars($l['title']))?>
    </div>

    <?php elseif($l['type'] == 'image'): ?>
    <div class="preview-col">
      <div class="preview-link-btn<?=$l['outline']?' outline':''?><?=$l['text_center']?' text-center':''?>" style="border-radius:<?=$radius?>px">
        <?php if(!empty($l['icon'])): ?>
        <img src="<?=$l['icon']?>" class="preview-link-icon">
        <?php endif; ?>
        <span class="preview-link-title"><?=htmlspecialchars($l['title'])?></span>
        <?php if(!empty($l['passcode'])): ?><span style="font-size:13px;margin-right:2px;flex-shrink:0;">🔒</span><?php endif; ?>
        <svg class="preview-link-arrow" viewBox="0 0 24 24"><path d="M8.59 16.59L13.17 12 8.59 7.41 10 6l6 6-6 6-1.41-1.41z"/></svg>
      </div>
    </div>

    <?php elseif($l['type'] == 'picture'): ?>
    <div class="preview-picture">
      <?php if(!empty($l['icon'])): ?>
      <img src="<?=$l['icon']?>">
      <?php endif; ?>
    </div>

    <?php elseif($l['type'] == 'video'): ?>
    <div class="preview-col">
      <div class="preview-link-btn<?=$l['outline']?' outline':''?><?=$l['text_center']?' text-center':''?>" style="border-radius:<?=$radius?>px">
        <?php if(!empty($l['icon'])): ?>
        <img src="<?=$l['icon']?>" class="preview-link-icon">
        <?php endif; ?>
        <span class="preview-link-title"><?=htmlspecialchars($l['title'])?></span>
        <?php if(!empty($l['video_file'])): ?><span style="font-size:11px;color:#999;margin-right:4px;">▶</span><?php endif; ?>
        <svg class="preview-link-arrow" viewBox="0 0 24 24"><path d="M8.59 16.59L13.17 12 8.59 7.41 10 6l6 6-6 6-1.41-1.41z"/></svg>
      </div>
    </div>
    <?php endif; ?>

    <div class="preview-actions">
      <button type="button" class="action-btn action-move-up" title="上移" onclick="moveItem(this, -1)"><svg viewBox="0 0 24 24" width="16" height="16"><path d="M7.41 15.41L12 10.83l4.59 4.58L18 14l-6-6-6 6 1.41 1.41z" fill="currentColor"/></svg></button>
      <button type="button" class="action-btn action-move-down" title="下移" onclick="moveItem(this, 1)"><svg viewBox="0 0 24 24" width="16" height="16"><path d="M7.41 8.59L12 13.17l4.59-4.58L18 10l-6 6-6-6 1.41-1.41z" fill="currentColor"/></svg></button>
      <?php if($l['type'] == 'link'): ?>
      <a href="javascript:openEditModal(<?=$l['id']?>,'<?=htmlspecialchars($l['title'],ENT_QUOTES)?>','<?=htmlspecialchars($l['url'] ?? '',ENT_QUOTES)?>',<?=$l['sort']?>,<?=$l['outline']??0?>,<?=$l['text_center']??1?>,<?=$l['btn_radius_on']??1?>,<?=$l['btn_radius']??30?>,'<?=$l['passcode']??''?>','<?=$l['icon']??''?>')" class="action-btn" title="编辑"><svg viewBox="0 0 24 24" width="16" height="16"><path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z" fill="currentColor"/></svg></a>
      <?php elseif($l['type'] == 'text'): ?>
      <a href="javascript:openEditTextModal(<?=$l['id']?>,'<?=htmlspecialchars($l['title'],ENT_QUOTES)?>',<?=$l['sort']?>,'<?=$l['footer_align']?>')" class="action-btn" title="编辑"><svg viewBox="0 0 24 24" width="16" height="16"><path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z" fill="currentColor"/></svg></a>
      <?php elseif($l['type'] == 'image'): ?>
      <a href="javascript:openEditImageModal(<?=$l['id']?>,'<?=htmlspecialchars($l['title'],ENT_QUOTES)?>',<?=$l['sort']?>,<?=$l['outline']??0?>,'<?=$l['icon']??''?>','<?=$l['popup_img']??''?>',<?=$l['text_center']??1?>,<?=$l['btn_radius_on']??1?>,<?=$l['btn_radius']??30?>)" class="action-btn" title="编辑"><svg viewBox="0 0 24 24" width="16" height="16"><path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z" fill="currentColor"/></svg></a>
      <?php elseif($l['type'] == 'picture'): ?>
      <a href="javascript:openEditPictureModal(<?=$l['id']?>,'<?=htmlspecialchars($l['url'] ?? '',ENT_QUOTES)?>',<?=$l['sort']?>,'<?=$l['icon']??''?>','<?=htmlspecialchars($l['title'] ?? '',ENT_QUOTES)?>')" class="action-btn" title="编辑"><svg viewBox="0 0 24 24" width="16" height="16"><path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z" fill="currentColor"/></svg></a>
      <?php elseif($l['type'] == 'video'): ?>
      <a href="javascript:openEditVideoModal(<?=$l['id']?>,'<?=htmlspecialchars($l['title'],ENT_QUOTES)?>',<?=$l['sort']?>,<?=$l['outline']??0?>,<?=$l['text_center']??1?>,<?=$l['btn_radius_on']??1?>,<?=$l['btn_radius']??30?>,'<?=$l['icon']??''?>','<?=htmlspecialchars($l['video_file'] ?? '',ENT_QUOTES)?>',<?=$l['auto_expand']??0?>,<?=$l['auto_play']??0?>,<?=$l['default_muted']??0?>,<?=$l['video_loop']??0?>,'<?=htmlspecialchars($l['video_poster'] ?? '',ENT_QUOTES)?>')" class="action-btn" title="编辑"><svg viewBox="0 0 24 24" width="16" height="16"><path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z" fill="currentColor"/></svg></a>
      <?php endif; ?>
      <a href="?del=<?=$l['id']?>" class="action-btn action-del" title="删除" onclick="return confirm('确定删除？')"><svg viewBox="0 0 24 24" width="16" height="16"><path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z" fill="currentColor"/></svg></a>
    </div>
  </div>
  <?php endwhile; ?>
</div>

<script>
(function(){
  var list = document.getElementById('modulePreviewList');
  if(!list) return;
  var dragId = null;

  function saveOrder(){
    var items = list.querySelectorAll('.module-preview-item');
    var ids = [];
    for(var i=0;i<items.length;i++) ids.push(items[i].dataset.id);
    if(ids.length === 0) return;
    // 确保 POST 到 admin.php（而不是可能被直接访问的 admin_modules.php）
    var baseUrl = window.location.pathname.replace(/[^/]*$/, '') + 'admin.php';
    fetch(baseUrl, {
      method:'POST',
      headers:{'Content-Type':'application/x-www-form-urlencoded'},
      body:'ajax_sort=1&ids='+ids.join(',')
    }).then(function(r){
      if(!r.ok) console.error('排序保存失败: HTTP '+r.status);
    }).catch(function(e){
      console.error('排序保存失败:', e);
    });
  }

  function clearDragStates(){
    var items = list.querySelectorAll('.module-preview-item');
    for(var i=0;i<items.length;i++) items[i].classList.remove('dragging','drag-over');
  }

  // 上下移动
  window.moveItem = function(btn, dir){
    var item = btn.closest('.module-preview-item');
    if(!item) return;
    var parent = item.parentNode;
    var all = parent.querySelectorAll('.module-preview-item');
    var idx = Array.prototype.indexOf.call(all, item);
    var target = all[idx + dir];
    if(!target) return;
    if(dir < 0){
      parent.insertBefore(item, target);
    }else{
      parent.insertBefore(item, target.nextSibling);
    }
    saveOrder();
  };

  // ===== Desktop: HTML5 Drag & Drop =====
  list.addEventListener('dragstart', function(e){
    var item = e.target.closest('.module-preview-item');
    if(!item) return;
    dragId = item.dataset.id;
    item.classList.add('dragging');
    e.dataTransfer.effectAllowed = 'move';
  });

  list.addEventListener('dragover', function(e){
    e.preventDefault();
    e.dataTransfer.dropEffect = 'move';
    var item = e.target.closest('.module-preview-item');
    if(item && item.dataset.id !== dragId) item.classList.add('drag-over');
  });

  list.addEventListener('dragleave', function(e){
    var item = e.target.closest('.module-preview-item');
    if(item) item.classList.remove('drag-over');
  });

  list.addEventListener('drop', function(e){
    e.preventDefault();
    var target = e.target.closest('.module-preview-item');
    if(!target || target.dataset.id === dragId) return;
    target.classList.remove('drag-over');

    var src = list.querySelector('.module-preview-item[data-id="'+dragId+'"]');
    if(!src) return;

    var all = list.querySelectorAll('.module-preview-item');
    var srcIdx = Array.prototype.indexOf.call(all, src);
    var dstIdx = Array.prototype.indexOf.call(all, target);

    if(srcIdx < dstIdx){
      target.parentNode.insertBefore(src, target.nextSibling);
    }else{
      target.parentNode.insertBefore(src, target);
    }
    saveOrder();
  });

  list.addEventListener('dragend', function(){
    dragId = null;
    clearDragStates();
  });
})();
</script>

<!-- 以下弹窗保持不变 -->
<div class="modal modal-hidden" id="addModal">
  <div class="modal-content">
    <span class="modal-close" onclick="closeAddModal()">×</span>
    <h2>添加链接</h2>
    <form method="post" enctype="multipart/form-data">
      <div class="modal-item"><label>标题</label><input name="ltitle" required></div>
      <div class="modal-item"><label>链接</label><input name="lurl" required></div>
      <div class="modal-item">
        <label>样式</label>
        <div class="style-checkboxes">
          <label><input type="checkbox" name="outline"> 镂空</label>
          <label><input type="checkbox" name="text_center" checked> 居中</label>
          <label><input type="checkbox" name="btn_radius_on" checked> 圆角</label>
          <label class="radius-group">圆角值 <input type="text" name="btn_radius" value="30" style="width:46px;padding:4px 6px;border:1px solid #d0d5dd;border-radius:6px;font-size:13px;text-align:center;outline:none"></label>
        </div>
      </div>
      <div class="modal-item"><label>加密</label><input name="passcode" maxlength="4" placeholder="4位数字密码"></div>
      <div class="modal-item"><label>图标</label>
        <div class="avatar-upload-wrapper">
          <div class="upload-btn-box">上传图标</div>
          <input type="file" name="licon" accept="image/*" onchange="previewModalIcon(this,'add_icon_preview')">
        </div>
        <div id="add_icon_preview" style="display:none;margin-left:10px"></div>
      </div>
      <div class="modal-bottom"><button name="add_link">添加</button></div>
    </form>
  </div>
</div>

<div class="modal modal-hidden" id="editModal">
  <div class="modal-content">
    <span class="modal-close" onclick="closeEditModal()">×</span>
    <h2>编辑链接</h2>
    <form method="post" enctype="multipart/form-data">
      <input type="hidden" name="id" id="edit_id">
      <input type="hidden" name="sort" id="edit_sort">
      <div class="modal-item"><label>标题</label><input name="ltitle" id="edit_title" required></div>
      <div class="modal-item"><label>链接</label><input name="lurl" id="edit_url" required></div>
      <div class="modal-item">
        <label>样式</label>
        <div class="style-checkboxes">
          <label><input type="checkbox" name="outline" id="edit_outline"> 镂空</label>
          <label><input type="checkbox" name="text_center" id="edit_text_center"> 居中</label>
          <label><input type="checkbox" name="btn_radius_on" id="edit_btn_radius_on"> 圆角</label>
          <label class="radius-group">圆角值 <input type="text" name="btn_radius" id="edit_btn_radius" style="width:46px;padding:4px 6px;border:1px solid #d0d5dd;border-radius:6px;font-size:13px;text-align:center;outline:none"></label>
        </div>
      </div>
      <div class="modal-item"><label>加密</label><input name="passcode" id="edit_passcode" maxlength="4" placeholder="4位数字密码"></div>
      <div class="modal-item"><label>图标</label>
        <input type="hidden" name="del_icon" id="del_icon" value="0">
        <div id="edit_icon_container"></div>
        <input type="file" name="licon" id="edit_icon_file" style="display:none" accept="image/*" onchange="previewEditIcon(this)">
      </div>
      <div class="modal-bottom"><button name="edit_link">保存</button></div>
    </form>
  </div>
</div>

<div class="modal modal-hidden" id="addTextModal">
  <div class="modal-content">
    <span class="modal-close" onclick="closeAddTextModal()">×</span>
    <h2>添加文字</h2>
    <form method="post">
      <div class="modal-item"><label>文字</label><textarea name="text_content" required></textarea></div>
      <div class="modal-item"><label>对齐</label>
        <div class="style-checkboxes">
          <label><input type="radio" name="text_align" value="left" checked> 左</label>
          <label><input type="radio" name="text_align" value="center"> 中</label>
          <label><input type="radio" name="text_align" value="right"> 右</label>
        </div>
      </div>
      <div class="modal-bottom"><button name="add_text">添加</button></div>
    </form>
  </div>
</div>

<div class="modal modal-hidden" id="editTextModal">
  <div class="modal-content">
    <span class="modal-close" onclick="closeEditTextModal()">×</span>
    <h2>编辑文字</h2>
    <form method="post">
      <input type="hidden" name="id" id="edit_text_id">
      <input type="hidden" name="text_sort" id="edit_text_sort">
      <div class="modal-item"><label>文字</label><textarea name="text_content" id="edit_text_content" required></textarea></div>
      <div class="modal-item"><label>对齐</label>
        <div class="style-checkboxes">
          <label><input type="radio" name="text_align" value="left" id="a_left"> 左</label>
          <label><input type="radio" name="text_align" value="center" id="a_center"> 中</label>
          <label><input type="radio" name="text_align" value="right" id="a_right"> 右</label>
        </div>
      </div>
      <div class="modal-bottom"><button name="edit_text">保存</button></div>
    </form>
  </div>
</div>

<div class="modal modal-hidden" id="addImageModal">
  <div class="modal-content">
    <span class="modal-close" onclick="closeAddImageModal()">×</span>
    <h2>添加弹图</h2>
    <form method="post" enctype="multipart/form-data">
      <div class="modal-item"><label>文字</label><input name="img_title" required></div>
      <div class="modal-item">
        <label>样式</label>
        <div class="style-checkboxes">
          <label><input type="checkbox" name="outline"> 镂空</label>
          <label><input type="checkbox" name="text_center" checked> 居中</label>
          <label><input type="checkbox" name="btn_radius_on" checked> 圆角</label>
          <label class="radius-group">圆角值 <input type="text" name="btn_radius" value="30" style="width:46px;padding:4px 6px;border:1px solid #d0d5dd;border-radius:6px;font-size:13px;text-align:center;outline:none"></label>
        </div>
      </div>
      <div class="modal-item"><label>图标</label>
        <div class="avatar-upload-wrapper">
          <div class="upload-btn-box">上传图标</div>
          <input type="file" name="licon" accept="image/*" onchange="previewModalIcon(this,'add_img_icon_preview')">
        </div>
        <div id="add_img_icon_preview" style="display:none;margin-left:10px"></div>
      </div>
      <div class="modal-item"><label>大图</label>
        <div class="avatar-upload-wrapper">
          <div class="upload-btn-box">上传大图</div>
          <input type="file" name="popup_img" required accept="image/*" onchange="previewModalIcon(this,'add_popup_preview')">
        </div>
        <div id="add_popup_preview" style="display:none;margin-left:10px"></div>
      </div>
      <div class="modal-bottom"><button name="add_image">添加</button></div>
    </form>
  </div>
</div>

<div class="modal modal-hidden" id="editImageModal">
  <div class="modal-content">
    <span class="modal-close" onclick="closeEditImageModal()">×</span>
    <h2>编辑弹图</h2>
    <form method="post" enctype="multipart/form-data">
      <input type="hidden" name="id" id="edit_image_id">
      <input type="hidden" name="del_icon" id="del_icon_img" value="0">
      <input type="hidden" name="del_popup" id="del_popup_img" value="0">
      <input type="hidden" name="img_sort" id="edit_image_sort">
      <div class="modal-item"><label>文字</label><input name="img_title" id="edit_image_title" required></div>
      <div class="modal-item">
        <label>样式</label>
        <div class="style-checkboxes">
          <label><input type="checkbox" name="outline" id="edit_img_outline"> 镂空</label>
          <label><input type="checkbox" name="text_center" id="edit_img_text_center"> 居中</label>
          <label><input type="checkbox" name="btn_radius_on" id="edit_img_btn_radius_on"> 圆角</label>
          <label class="radius-group">圆角值 <input type="text" name="btn_radius" id="edit_img_btn_radius" style="width:46px;padding:4px 6px;border:1px solid #d0d5dd;border-radius:6px;font-size:13px;text-align:center;outline:none"></label>
        </div>
      </div>
      <div class="modal-item"><label>图标</label>
        <div id="edit_image_icon_container"></div>
        <input type="file" name="licon" id="edit_image_icon_file" style="display:none" accept="image/*" onchange="previewEditImageIcon(this)">
      </div>
      <div class="modal-item"><label>大图</label>
        <div id="edit_image_popup_container"></div>
        <input type="file" name="popup_img" id="edit_image_popup_file" style="display:none" accept="image/*" onchange="previewEditPopup(this)">
      </div>
      <div class="modal-bottom"><button name="edit_image">保存</button></div>
    </form>
  </div>
</div>

<!-- 添加图片 -->
<div class="modal modal-hidden" id="addPictureModal">
  <div class="modal-content">
    <span class="modal-close" onclick="closeAddPictureModal()">×</span>
    <h2>添加图片</h2>
    <form method="post" enctype="multipart/form-data">
      <div class="modal-item"><label>标题</label><input name="pic_title" placeholder="用于识别和统计"></div>
      <div class="modal-item"><label>图片</label>
        <div class="avatar-upload-wrapper" style="flex:1">
          <div class="upload-btn-box">上传图片</div>
          <input type="file" name="picture_file" required accept="image/*" onchange="previewModalIcon(this,'add_picture_preview')">
        </div>
        <div id="add_picture_preview" style="display:none"></div>
      </div>
      <div class="modal-item"><label>链接</label><input name="pic_url" placeholder="选填，点击图片跳转链接"></div>
      <div class="modal-bottom"><button name="add_picture">添加</button></div>
    </form>
  </div>
</div>

<!-- 编辑图片 -->
<div class="modal modal-hidden" id="editPictureModal">
  <div class="modal-content">
    <span class="modal-close" onclick="closeEditPictureModal()">×</span>
    <h2>编辑图片</h2>
    <form method="post" enctype="multipart/form-data">
      <input type="hidden" name="pic_id" id="edit_pic_id">
      <div class="modal-item"><label>标题</label><input name="pic_title" id="edit_pic_title" placeholder="用于识别和统计"></div>
      <div class="modal-item"><label>图片</label>
        <div id="edit_pic_icon_container"></div>
        <input type="file" name="picture_file" id="edit_pic_file" style="display:none" accept="image/*" onchange="previewEditPictureIcon(this)">
      </div>
      <div class="modal-item"><label>链接</label><input name="pic_url" id="edit_pic_url" placeholder="选填，点击图片跳转链接"></div>
      <input type="hidden" name="pic_sort" id="edit_pic_sort">
      <div class="modal-bottom"><button name="edit_picture">保存</button></div>
    </form>
  </div>
</div>

<!-- 添加视频 -->
<div class="modal modal-hidden" id="addVideoModal">
  <div class="modal-content">
    <span class="modal-close" onclick="closeAddVideoModal()">×</span>
    <h2>添加视频</h2>
    <form method="post" enctype="multipart/form-data">
      <div class="modal-item"><label>标题</label><input name="video_title" required></div>
      <div class="modal-item"><label>视频</label>
        <div style="flex:1;display:flex;flex-direction:column;gap:6px">
          <div class="avatar-upload-wrapper">
            <div class="upload-btn-box">上传视频</div>
            <input type="file" name="video_file" accept="video/mp4,video/webm,video/ogg" onchange="previewVideoName(this)">
          </div>
          <div style="font-size:12px;color:#999">或</div>
          <input name="video_url" placeholder="视频直链 URL（选填）" style="padding:7px 12px;border:1px solid #d0d5dd;border-radius:6px;font-size:13px;outline:none;width:100%">
        </div>
      </div>
      <div class="modal-item">
        <label>样式</label>
        <div class="style-checkboxes">
          <label><input type="checkbox" name="outline"> 镂空</label>
          <label><input type="checkbox" name="text_center" checked> 居中</label>
          <label><input type="checkbox" name="btn_radius_on" checked> 圆角</label>
          <label class="radius-group">圆角值 <input type="text" name="btn_radius" value="30" style="width:46px;padding:4px 6px;border:1px solid #d0d5dd;border-radius:6px;font-size:13px;text-align:center;outline:none"></label>
        </div>
      </div>
      <div class="modal-item"><label>图标</label>
        <div class="avatar-upload-wrapper">
          <div class="upload-btn-box">上传图标</div>
          <input type="file" name="licon" accept="image/*" onchange="previewModalIcon(this,'add_video_icon_preview')">
        </div>
        <div id="add_video_icon_preview" style="display:none;margin-left:10px"></div>
      </div>
      <div class="modal-item"><label>封面图</label>
        <div class="avatar-upload-wrapper">
          <div class="upload-btn-box">上传封面</div>
          <input type="file" name="video_poster" accept="image/*" onchange="previewModalIcon(this,'add_video_poster_preview')">
        </div>
        <div id="add_video_poster_preview" style="display:none;margin-left:10px"></div>
      </div>
      <div class="modal-item" style="flex-wrap:wrap;gap:8px">
        <label style="min-width:auto">功能</label>
        <label style="font-weight:400;font-size:13px;cursor:pointer;display:inline-flex;align-items:center;gap:4px;color:#333;padding:4px 8px;background:#f5f6f8;border-radius:6px"><input type="checkbox" name="auto_expand" style="accent-color:#6366f1;width:15px;height:15px"> 自动展开</label>
        <label style="font-weight:400;font-size:13px;cursor:pointer;display:inline-flex;align-items:center;gap:4px;color:#333;padding:4px 8px;background:#f5f6f8;border-radius:6px"><input type="checkbox" name="auto_play" style="accent-color:#6366f1;width:15px;height:15px"> 自动播放</label>
        <label style="font-weight:400;font-size:13px;cursor:pointer;display:inline-flex;align-items:center;gap:4px;color:#333;padding:4px 8px;background:#f5f6f8;border-radius:6px"><input type="checkbox" name="default_muted" style="accent-color:#6366f1;width:15px;height:15px"> 默认静音</label>
        <label style="font-weight:400;font-size:13px;cursor:pointer;display:inline-flex;align-items:center;gap:4px;color:#333;padding:4px 8px;background:#f5f6f8;border-radius:6px"><input type="checkbox" name="video_loop" style="accent-color:#6366f1;width:15px;height:15px"> 循环播放</label>
      </div>
      <div class="modal-bottom"><button name="add_video">添加</button></div>
    </form>
  </div>
</div>

<!-- 编辑视频 -->
<div class="modal modal-hidden" id="editVideoModal">
  <div class="modal-content">
    <span class="modal-close" onclick="closeEditVideoModal()">×</span>
    <h2>编辑视频</h2>
    <form method="post" enctype="multipart/form-data">
      <input type="hidden" name="vid" id="edit_vid">
      <input type="hidden" name="video_sort" id="edit_video_sort">
      <div class="modal-item"><label>标题</label><input name="video_title" id="edit_video_title" required></div>
      <div class="modal-item"><label>视频</label>
        <div style="flex:1;display:flex;flex-direction:column;gap:6px">
          <div id="edit_video_file_container"></div>
          <div style="font-size:12px;color:#999">或</div>
          <input name="video_url" id="edit_video_url" placeholder="视频直链 URL（选填）" style="padding:7px 12px;border:1px solid #d0d5dd;border-radius:6px;font-size:13px;outline:none;width:100%">
        </div>
      </div>
      <div class="modal-item">
        <label>样式</label>
        <div class="style-checkboxes">
          <label><input type="checkbox" name="outline" id="edit_video_outline"> 镂空</label>
          <label><input type="checkbox" name="text_center" id="edit_video_text_center"> 居中</label>
          <label><input type="checkbox" name="btn_radius_on" id="edit_video_btn_radius_on"> 圆角</label>
          <label class="radius-group">圆角值 <input type="text" name="btn_radius" id="edit_video_btn_radius" style="width:46px;padding:4px 6px;border:1px solid #d0d5dd;border-radius:6px;font-size:13px;text-align:center;outline:none"></label>
        </div>
      </div>
      <div class="modal-item"><label>图标</label>
        <input type="hidden" name="del_icon_video" id="del_icon_video" value="0">
        <div id="edit_video_icon_container"></div>
        <input type="file" name="licon" id="edit_video_icon_file" style="display:none" accept="image/*" onchange="previewEditVideoIcon(this)">
      </div>
      <div class="modal-item"><label>封面图</label>
        <input type="hidden" name="del_video_poster" id="del_video_poster" value="0">
        <div id="edit_video_poster_container"></div>
        <input type="file" name="video_poster" id="edit_video_poster_file" style="display:none" accept="image/*" onchange="previewEditVideoPoster(this)">
      </div>
      <div class="modal-item" style="flex-wrap:wrap;gap:8px">
        <label style="min-width:auto">功能</label>
        <label style="font-weight:400;font-size:13px;cursor:pointer;display:inline-flex;align-items:center;gap:4px;color:#333;padding:4px 8px;background:#f5f6f8;border-radius:6px"><input type="checkbox" name="auto_expand" id="edit_video_auto_expand" style="accent-color:#6366f1;width:15px;height:15px"> 自动展开</label>
        <label style="font-weight:400;font-size:13px;cursor:pointer;display:inline-flex;align-items:center;gap:4px;color:#333;padding:4px 8px;background:#f5f6f8;border-radius:6px"><input type="checkbox" name="auto_play" id="edit_video_auto_play" style="accent-color:#6366f1;width:15px;height:15px"> 自动播放</label>
        <label style="font-weight:400;font-size:13px;cursor:pointer;display:inline-flex;align-items:center;gap:4px;color:#333;padding:4px 8px;background:#f5f6f8;border-radius:6px"><input type="checkbox" name="default_muted" id="edit_video_default_muted" style="accent-color:#6366f1;width:15px;height:15px"> 默认静音</label>
        <label style="font-weight:400;font-size:13px;cursor:pointer;display:inline-flex;align-items:center;gap:4px;color:#333;padding:4px 8px;background:#f5f6f8;border-radius:6px"><input type="checkbox" name="video_loop" id="edit_video_loop" style="accent-color:#6366f1;width:15px;height:15px"> 循环播放</label>
      </div>
      <div class="modal-bottom"><button name="edit_video">保存</button></div>
    </form>
  </div>
</div>
