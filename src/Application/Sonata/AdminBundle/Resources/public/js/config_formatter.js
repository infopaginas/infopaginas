$(function(){
    var editor,
        config,
        data;
    for (var instance in CKEDITOR.instances) {
        editor = CKEDITOR.instances[instance];
        config = CKEDITOR.instances[instance].config;
    }
    $('#'+instance).addClass('form-control');
    data = $('#'+instance).val();

    if (typeof editor != 'undefined') {
        editor.on('instanceReady', function() {
            $('.formatter').change(function(e){
                switch ($(this).val()) {
                    case 'text':
                        editor.destroy();
                        if (data) {
                            $('#'+instance).val(data);
                            data = null;
                        }
                        break;
                    case 'html':
                        editor = CKEDITOR.replace(instance, config);
                        break;
                }
            });
            $('.formatter').trigger('change');
        });
    }
});