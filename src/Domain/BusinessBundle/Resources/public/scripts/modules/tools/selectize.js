$( document ).ready( function() {

    applySelectizePlugin();

    function applySelectizePlugin() {
        var elements = $( 'input.selectize-control:not(.selectized)' );

        elements.removeClass( 'form-control' );

        elements.selectize({
            plugins: [
                'restore_on_backspace',
                'remove_button'
            ],
            persist: false,
            create: true,
            createFilter: keywordValidator
        });
    }

    function keywordValidator() {
        var value = this.lastQuery;
        var errors = [];

        if ( !value ) {
            errors.push( errorList.keyword.notBlank );
        }

        if ( value.length > 255 ) {
            errors.push( errorList.keyword.maxLength );
        }

        if ( value && value.length < 2 ) {
            errors.push( errorList.keyword.minLength );
        }

        var validateOneWord = validators.keyword.oneWord;

        if ( !validateOneWord.test( value )) {
            errors.push( errorList.keyword.oneWord );
        }

        handleKeywordValidationError( errors );

        return !errors.length;
    }

    function handleKeywordValidationError( errors ) {
        var parent = $( '#sonata-ba-field-container-' + formId + '_keywordText' );

        parent.find( '.sonata-ba-field-error-messages').remove();

        if ( errors.length ) {
            var errorHtml = '<div class="help-inline sonata-ba-field-error-messages"><ul class="list-unstyled">';

            $.each(errors, function( index, value ) {
                errorHtml += '<li><i class="fa fa-exclamation-circle" aria-hidden="true"></i> ' + value + '</li>';
            });

            errorHtml += '</ul></div>';

            parent.append( errorHtml );
        }
    }
});