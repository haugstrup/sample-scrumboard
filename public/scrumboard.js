(function (window, $, undefined) {

  function onInit() {
    $('ul.status li').tipsy({gravity: 'e'});
    $('.graph .target, .graph .actual').tipsy({gravity: 's'});
    $('.tooltip').tipsy({gravity: 'n'});
  }

  function onFilterClick(elmTarget, e) {
    elmTarget.toggleClass('active');
    var enabled = elmTarget.hasClass('active');
    if (enabled) {
      elmTarget.css('background-color', elmTarget.data('color'));
    }
    else {
      elmTarget.css('background-color', 'transparent');
    }
    var currentFilter = [];
    elmTarget.parent().find('li.active').each(function(){
      currentFilter.push('area-'+$(this).data('id'));
    });
    if (currentFilter.length === 0) {
      $('ul.stories > li').show();
      $('#stories > .items > .story-group').show();
    }
    else {
      $('ul.stories > li').each(function(){
        var is_visible = false;
        var currentStory = $(this);
        $.each(currentFilter, function(index, value) {
          console.log('testing '+value);
          if (currentStory.hasClass(value)) {
            is_visible = true;
          }
        });
        if (is_visible) {
          $(this).show();
          $('#story-'+$(this).data('id')).show();
        }
        else {
          $(this).hide();
          $('#story-'+$(this).data('id')).hide();
        }
      });
    }
  }

  function onDashBoardStoryClick(elmTarget, e) {
    if (!$(e.target).hasClass('external-link')) {
      e.preventDefault();
      // Single story click: switch to board view and scroll to story
      onDashBoardToggleClick();
      var storyId = elmTarget.data('id');
      $('html,body').scrollTop($('#story-' + storyId).offset().top - 75);
    }
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

      total_width = total_width-175; // width of the story-header
      var count = $('#stories').data('count');
      var wrapper_width = Math.floor(total_width/count);
      $('.story, .state,.header h1').width(wrapper_width);

    }

    function set_story_height(current_id) {
      var states = $(current_id).find('.state');
      var max_height = 0;
      states.each(function(){
        var current_height = 0;
        $(this).find('li').each(function(){
          if (!$(this).attr('style')) {
            current_height += $(this).outerHeight(true);
          }
        });

        if (current_height > max_height) {
          max_height = current_height;
        }
      });
      states.find('ul').height(max_height);
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
          set_story_height(current_id);
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

            set_story_height('#'+$(this).parents('.story-group').attr('id'));

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

    var collapsedData = getCollapsedData().split(',');
    if (typeof collapsedData === 'object') {
      $.each(collapsedData, function(index, value){
        if (typeof value === 'string') {
          $('#story-'+value).addClass('collapsed').find('.user-list,.state').hide();
        }
      });
    }
  }

  function onScrumBoardToggleClick(elmTarget, e) {
    var elmParent = elmTarget.parents('.story-group');
    elmParent.find('.user-list, .state').toggle();
    elmParent.toggleClass('collapsed');
    if (elmParent.hasClass('collapsed')) {
      addCollapsed(elmParent.attr('data-id'));
    }
    else {
      removeCollapsed(elmParent.attr('data-id'));
    }
  }

  function getCollapsedData() {
    var data = false;
    if (typeof localStorage !== 'undefined' ) {
      data = localStorage.getItem("collapsedList");
    }
    return data ? data : '';
  }
  function addCollapsed(id) {
    if (typeof(localStorage) !== 'undefined' ) {
      var data = getCollapsedData();
      var return_value = '';
      if (typeof data === 'string') {
        data = data.split(',');
        if ($.inArray(id, data) == -1) {
          data.push(id);
        }
        return_value = data.join(',');
      }
      else {
        return_value = id;
      }
      localStorage.setItem("collapsedList", return_value);
    }
  }
  function removeCollapsed(id) {
    if (typeof(localStorage) !== 'undefined' ) {
      var data = getCollapsedData();
      var return_value = '';
      if (typeof data === 'string') {
        data = data.split(',');
        var idx = data.indexOf(id);
        if (idx != -1) {
          data.splice(idx, 1);
        }
        return_value = data.join(',');
      }
      localStorage.setItem("collapsedList", return_value);
    }
  }

  Podio.Event.bind(Podio.Event.Types.init, onInit);
  Podio.Event.UI.bind('click', '#dashboard ul.stories > li', onDashBoardStoryClick, true);
  Podio.Event.UI.bind('click', '#switch-view', onDashBoardToggleClick);
  Podio.Event.UI.bind('click', '.story-group h2', onScrumBoardToggleClick);
  Podio.Event.UI.bind('click', '.filter li', onFilterClick);

})(window, jQuery);
