
$(function() {
    console.log("thing");
    $('#build-report-btn').click(function() {
        let url = $('#build-report-btn').closest('form').attr("action");
        $('.alert').remove();
        $('#build-report-btn .spinner-border').show();        
        $.ajax({
            url: url,
            type: 'POST',
            success: function(data) {
                $('#build-report-btn .spinner-border').hide();
                showMessage(data);
            },
            error: function(response) {
                $('#build-report-btn .spinner-border').hide();
                showMessage(response.data);
            }
        });
    });

    function showMessage(data) {
        try {
            data = JSON.parse(data);
        } catch {
            data = { "severity": "danger", "text": data};
        }
        let alert = $('<div class="mt-3 alert"></div>');
        alert.addClass('alert-' + data.severity);
        alert.text(data.text);
        $('.card').before(alert);
    }
});