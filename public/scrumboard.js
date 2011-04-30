(function (window, $, undefined) {

  function onInit() {
    $('body').delegate('click', '#dashboard li a', onDashBoardStoryClick);
  }

  function onHeaderTitleClick(elmTarget,e) {
    $('#dashboard').show();
    $('.story-view').hide();
  }

  function onDashBoardStoryClick(elmTarget, e) {
    var storyId = elmTarget.data('id');
    $('#story-view-' + storyId).siblings().removeClass('current');
    $('#story-view-' + storyId).addClass('current').show();
    $('#dashboard').hide();
    initSingleStoryView();
  }
  
  function onItemToggle(elmTarget, e) {
    elmTarget.toggleClass('expanded');
    elmTarget.parent().next().toggle();
  }

  function initSingleStoryView() {
    function resize_stories() {
      // Recalculate width according to browser width
      var total_width = $(window).width()-10;
      var count = $('.story-view.current div.header h1').length;
      var wrapper_width = Math.floor(total_width/count);
      var inner_width = wrapper_width-5;
      $('.story-group').width(total_width);
      $('.header h1').width(wrapper_width);
      $('.story, .state,.header h1').width(inner_width);
    }

    resize_stories();
    
    $(window).resize(function(){
      resize_stories();
    });

    $('.story-group').delegate('.story-add', 'click', function(){
      $('#story-add-form').data('id', $(this).data('id')).modal();
    });
    
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
            $.post(update_url_base+'/'+item_id, {'state':state, '_method':'PUT'}, function(data){
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
  }

  Podio.Event.bind(Podio.Event.Types.init, onInit);
  Podio.Event.UI.bind('click', '#dashboard li a', onDashBoardStoryClick);
  Podio.Event.UI.bind('click', '.story-item .toggle', onItemToggle);
  Podio.Event.UI.bind('click', 'header', onHeaderTitleClick);

})(window, jQuery);