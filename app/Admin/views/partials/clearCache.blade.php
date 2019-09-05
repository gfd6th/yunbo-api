<li class="nav-item">
  <a class="nav-link active" href="#" id="clearCache">更新缓存</a>
</li>

<script>
$(document).ready(function(){


$('#clearCache').click(function(){
  $.get('/admin/clear', function(data){
      if(data ==='ok'){
        toastr.success('操作成功');
      } else {
        toastr.error('失败')
      }
  })
})

})
</script>