;$(function() {
    'use strict';
    
    $(document).on('click', '.js-add-to-favorites', function(e) {
        var $btn = $(this),
            id = $btn.data('id'),
            type = $btn.data('type'),
            url = $btn.data('url'),
            actionAdd = $btn.data('action-add'),
            actionDelete = $btn.data('action-delete'),
            titleAdd = $btn.data('title-add'),
            titleDelete = $btn.data('title-delete'),
            isActive = $btn.hasClass('btn-success'),
            ajaxAction = !isActive ? actionAdd : actionDelete;
        
        e.preventDefault();
        
        $btn.prop('disabled', true);
        
        $.ajax({
            url: url,
            type: 'POST',
            data: {
                ACTION: ajaxAction,
                ENTITY_TYPE: type,
                ENTITY_ID: id
            },
            dataType: 'json'
            
        }).done(function(result) {
            if (result.success) {
                if (result.action === actionAdd)
                    $btn.removeClass('btn-default').addClass('btn-success').text(titleDelete);
                else
                    $btn.removeClass('btn-success').addClass('btn-default').text(titleAdd);
            }
            else {
                console.log(result.errors);
            }
            
            $btn.prop('disabled', false);
            
        }).error(function (jqXHR, textStatus, errorThrown) {
            console.log(textStatus, errorThrown);
        });
    });
});