(function (window, $, undefined) {

  function onInit() {
    $('ul.status li').tipsy({gravity: 'e'});
    $('.graph .target, .graph .actual').tipsy({gravity: 's'});
    $('.tooltip').tipsy({gravity: 'n'});
  }
  
  function onDashBoardStoryClick(elmTarget, e) {
    // Single story click: switch to board view and scroll to story
    onDashBoardToggleClick();
    var storyId = elmTarget.data('id');
    $('html,body').scrollTop($('#story-' + storyId).offset().top - 75);
  }
  function onDashBoardToggleClick(elmTarget, e) {
    $('#dashboard, #stories').toggle();
   initSingleStoryView();
   $('html, body').scrollTop(0);
  }
  
  function initSingleStoryView() {
    function resize_stories() {
      // Recalculate width according to browser width
      var total_width = $(window).width()-10;
      $('.story-group').width(total_width);

      total_width = total_width-200; // width of the story-header
      var count = $('#stories').data('count');
      var wrapper_width = Math.floor(total_width/count);
      $('.story, .state,.header h1').width(wrapper_width);
      
    }

    resize_stories();
    
    $(window).resize(function(){
      resize_stories();
    });

    $('.story-group').each(function(){
      var current_id = '#'+$(this).attr('id');
      $(current_id+' .story-item-state li').draggable({
        cancel: ".spinner",
        revert: "invalid", // when not dropped, the item will revert back to its initial position
        containment: current_id, // stick to demo-frame if present
        helper: "clone",
        cursor: "move",
        start: function(event, ui){
          $(ui.helper).width($(ui.helper).parent().width());
        }
      });

      $(current_id+' .story-item-state').droppable({
        accept: current_id+' .story-item-state > li',
        activeClass: 'ui-state-highlight',
        tolerance: 'pointer', 
        drop: function(event, ui) {
          var old_state = $(ui.draggable).parents('ul').data('state');
          var state = $(this).data('state');
          if (state != old_state) {
            var item_id = $(ui.draggable).data('id');
            $(this).append(ui.draggable);
            // $(this).css('background', '#eee');

            // Make Ajax request to change state on Podio
            $(ui.draggable).append('<div class="spinner"></div>');
            $.post(update_url_base+'/'+item_id, {'state':state, '_method':'PUT'}, function(data){
              $(ui.draggable).find('.spinner').remove();
            });
          }
        },
        over: function(event, ui) {
          $(ui.helper)
            .removeClass('dragging-0')
            .removeClass('dragging-1')
            .removeClass('dragging-2')
            .removeClass('dragging-3')
            .removeClass('dragging-4')
            .addClass('dragging-'+$(this).attr('data-state-id'));
        }
      });
    });
  }

  Podio.Event.bind(Podio.Event.Types.init, onInit);
  Podio.Event.UI.bind('click', '#dashboard ul.stories > li', onDashBoardStoryClick);
  Podio.Event.UI.bind('click', '#switch-view', onDashBoardToggleClick);

})(window, jQuery);