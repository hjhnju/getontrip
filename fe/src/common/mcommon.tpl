<!--
 * @ignore
 * @file error.tpl
 * @author fanyy 
 * @time 15-12-7
-->

<!-- target: Loading -->
<div class="tuzhi-loading">&nbsp;</div>
<!-- /target -->

<!-- target: Error -->
<div class="tuzhi-error">
    <div class="tuzhi-error-msg">${msg}</div>
</div>
<!-- /target -->


<!-- target: returnNavList --> 
<!-- for: ${list} as ${item} -->
  <!-- if: ${item.id}=='landscape' -->
    <li data-id="${item.id}" data-type="${item.type}">
      <a href="/m/sight/guide?id=${sightId}&tagId=${item.id}">${item.name}</a>
    </li>   
  <!-- elif: ${item.id}=='book' -->
    <!-- <li data-id="${item.id}" data-type="${item.type}">
        <a href="/m/sight/booklist?id=${sightId}&tagId=${item.id}">${item.name}</a>
      </li>  -->  
  <!-- elif: ${item.id}=='video' -->
    <!-- <li data-id="${item.id}" data-type="${item.type}">
      <a href="/m/sight/videolist?id=${sightId}&tagId=${item.id}">${item.name}</a>
    </li>  -->
  <!-- elif: ${item.id}=='food' -->
    <li data-id="${item.id}" data-type="${item.type}">
      <a href="/m/sight/foodlist?id=${sightId}&tagId=${item.id}">${item.name}</a>
    </li> 
  <!-- elif: ${item.id}=='specialty' -->
    <li data-id="${item.id}" data-type="${item.type}">
      <a href="/m/sight/specialtylist?id=${sightId}&tagId=${item.id}">${item.name}</a>
    </li> 
  <!-- else -->
    <li data-id="${item.id}" data-type="${item.type}">
      <a href="/m/sight/topiclist?id=${sightId}&tagId=${item.id}">${item.name}</a>
    </li>     
  <!-- /if -->    
     
<!-- /for --> 
<!-- /target -->

