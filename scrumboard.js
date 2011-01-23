$(function() {
  $('.story-group').each(function(){
    var current_id = '#'+$(this).attr('id');
    $(current_id+' .story-item-state li').draggable({
      cancel: ".spinner",
      revert: "invalid", // when not dropped, the item will revert back to its initial position
      containment: current_id, // stick to demo-frame if present
      helper: "clone",
      cursor: "move"
    });

    $(current_id+' .story-item-state').droppable({
      accept: current_id+' .story-item-state > li',
      activeClass: 'ui-state-highlight',
      drop: function(event, ui) {
        var old_state = $(ui.draggable).parents('ul').data('state');
        var state = $(this).data('state');
        if (state != old_state) {
          var item_id = $(ui.draggable).data('id');
          $(this).append(ui.draggable);
          $(this).css('background', '#eee');
          
          // Make Ajax request to change state on Podio
          $(ui.draggable).append('<div class="spinner"></div>');
          $.post('update.php', {item_id:item_id, state:state}, function(data){
            if (data == 'ok') {
              $(ui.draggable).find('.spinner').remove();
            }
          });
          
          
        }
      },
      over: function() {
        $(this).css('background', '#65D6FD');
      },
      out: function() {
        $(this).css('background', '#eee');
      }
    });
  });
});
