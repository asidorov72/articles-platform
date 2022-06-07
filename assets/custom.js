$(document).ready(function() {
    $(".comments_state_btn").click(function(e){

        var commentsStateBtn = $(this);
        var commentsState = commentsStateBtn.val();
        var articleId = $('#article_id').val();
        var commentsContainer = $('#comments-container');

        $.ajax({
            url:       '/article/'+articleId+'/comments/' + commentsState,
            type:      'POST',
            dataType:  'json',
            async:     true,
            data:      { 'state' : commentsState, 'articleId' : articleId },

            success: function(data, status) {
                if (commentsState === 'on') {
                    commentsContainer.attr('class', '');
                    commentsContainer.addClass("row comments-container-show");

                    //var e = $('<tr><th>Name</th><th>Address</th></tr>');

                    // var e = $('<tr><th>Name</th><th>Address</th></tr>');
                    // $('#student').html('');
                    // $('#student').append(e);
                    //
                    // for(i = 0; i < data.length; i++) {
                    //     student = data[i];
                    //     var e = $('<tr><td id = "name"></td><td id = "address"></td></tr>');
                    //
                    //     $('#name', e).html(student['name']);
                    //     $('#address', e).html(student['address']);
                    //     $('#student').append(e);
                    // }
                } else {
                    commentsContainer.attr('class', '');
                    commentsContainer.addClass("row comments-container-hide");
                }
            },
            error : function(xhr, textStatus, errorThrown) {
                console.log('Ajax request failed.');
            }
        });
    });
});
