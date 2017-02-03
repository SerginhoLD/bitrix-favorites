$(function() {
    $(document).on('click', '.js-addToFavorites', function(e) {
        var $btn = $(this),
            id = $btn.data('id'),
            type = $btn.data('type'),
            url = $btn.data('url'),
            actionAdd = $btn.data('action-add'),
            actionDelete = $btn.data('action-delete'),
            isActive = $btn.hasClass('active'),
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
                    $btn.addClass('active');
                else
                    $btn.removeClass('active');
                
                $btn.prop('disabled', false);
            }
            else {
                console.log(result.errors);
            }
            
        }).error(function (e1, e2, e3) {
            console.log(e1, e2, e3);
        });
    });
});