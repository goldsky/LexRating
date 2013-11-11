function getRating(objId, holder) {
    if (!objId || !holder) {
        return;
    }
    $.ajaxSetup({cache: false});
    $.get(($('.lexrating-wrapper').data('connector-path') || 'assets/components/') + 'lexrating/connector.php',{
        action: 'web/count/get',
        id: objId
    }, function(data) {
        var response = JSON.parse(data);
        if (response.object) {
            $(holder).rateit('value', response.object.value);
            var readonly = (response.object.allowedToVote === true) ? false : true;
            $(holder).rateit('readonly', readonly);
            var counterHolder = '#count_' + $(holder).prop('id');
            $(counterHolder).text(response.object['total.voters']);
        }
    });
}

$(document).ready(function(){
    $('.rateit').bind('rated reset', function (e) {
        var ri = $(this);
        var value = ri.rateit('value');
        var objID = ri.data('objectid');
        var extended = ri.data('extended');
        ri.rateit('readonly', true);
        $.ajax({
            url: ($('.lexrating-wrapper').data('connector-path') || 'assets/components/') + 'lexrating/connector.php',
            data: {
                action: 'web/count/set',
                id: objID,
                Count: value,
                Extended: extended
            },
            type: 'POST',
            cache: false,
            success: function (data) {
                var response = JSON.parse(data);
                if (response.object.value) {
                    ri.rateit('value', response.object.value);
                    var readonly = (response.object.allowedToVote === true) ? false : true;
                    ri.rateit('readonly', readonly);
                    var counterHolder = '#count_' + ri.prop('id');
                    $(counterHolder).text(response.object['total.voters']);
                }
            },
            error: function (jxhr, msg, err) {
                console.log('jxhr, msg, err', jxhr, msg, err)
            }
        });
    });
});