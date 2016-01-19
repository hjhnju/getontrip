<!--
 * @ignore
 * @file list.tpl
 * @author fanyy 
 * @time 16-1-15
-->


<!-- target: returnSightNearbyList --> 
<!-- for: ${list} as ${item} -->
<li class="sight_item" style="background-image: url(${item.image})">
  <a class="sight_link" href="/m/sight/guide?id=${item.id}"> 
     <div class="img_mask"></div>
     <div class="title">${item.name}</div>
     <div class="detail">
        <div class="distance"> 
          <i class="location"></i>
          <span class="number">${item.dis}</span>
          <span class="text">${item.dis_unit}</span>
        </div>
        <div class="like"> 
          <span class="number fr">${item.param1}</span> 
          <i class="content fr"></i>
          <i class="ver fr"></i> 
          <span class="number fr">${item.param3}</span> 
          <i class="collect fr"></i>
        </div>
     </div>
  </a>
</li> 
<!-- /for --> 
<!-- /target -->

<!-- target: returnGuideNearbyList --> 
<!-- for: ${list} as ${item} -->
<li class="item">
  <a href="/m/sight/landscape?id=${item.id}" class="item_link">
    <div class="bg_img" style="background-image: url(${item.image})"></div> 
    <div class="content-box">
      <div class="title">
         <span class="name">${item.name}</span>
         <span class="location"></span>
         <span class="dis">${item.dis}</span>
         <span class="dis_unit">${item.dis_unit}</span>
      </div>
      <div class="content">
        ${item.content}
      </div>
       
    </div>
    <div class="headset-box">
       <div class="headset"></div>
       <div class="time">${item.audio_len}</div>
    </div>
    
  </a> 
</li>
<!-- /for --> 
<!-- /target -->