/* globals global */
jQuery(function($) {

    if (!window.multipleAuthors) {
        return;
    }

    var ajaxUrl = window.multipleAuthors.ajaxUrl;

    function AuthorField(element) {
        this.$el = $(element);
        this.name = this.$el.data('name');
        this.max = this.$el.data('max') || 0;
        this.$wrapper = this.$el.find('.ma-field-author-input-wrapper');
        this.init();
    }

    AuthorField.prototype.init = function() {
        this.initSortable();
        if (this.$wrapper.find('.item').size() === 0) {
            this.addInput();
        }
        this.addAddButton();
        this.checkAddButtonState();
        this.bindEvents();
    };

    AuthorField.prototype.initSortable = function() {
        this.$wrapper
        .disableSelection()
        .sortable({
            handle: '.handle',
        });
    };

    AuthorField.prototype.addInput = function() {
        var $item = $('<li class="item"></li>');
        var $handle = $('<span class="handle">&#9868;</span>');
        var $input = $('<input type="text" class="ma-field-author-input regular-text" placeholder="Type here to find user." style="width:90%" />');
        var $inputValue = $('<input type="hidden" class="ma-field-author-value" name="' + this.name + '[]" />');
        var $closeButton = $('<a href="#" class="button button-small remove">&#10007;</a>');
        $item.append($handle);
        $item.append($input);
        $item.append($inputValue);
        $item.append($closeButton);
        this.applyEventItem($item);
        this.$wrapper.append($item);
        this.$wrapper.sortable('refresh');
    };

    AuthorField.prototype.addAddButton = function() {
        var field = this;
        this.$button = $('<a href="#" class="button">Add more user</a>');
        this.$button.on('click', function(e) {
            e.preventDefault();
            field.addInput();
            field.checkAddButtonState();
        });
        this.$el.append(this.$button);
    };

    AuthorField.prototype.checkAddButtonState = function() {
        if (this.max > 0 && this.$wrapper.find('.item').size() >= this.max) {
            this.$button.hide();
        } else {
            this.$button.show();
        }
    };

    AuthorField.prototype.applyEventItem = function($item) {
        var field = this;
        $item.find('.button.remove').on('click', function(e) {
            e.preventDefault();
            $item.remove();
            field.checkAddButtonState();
        });
        this.applyAutocomplete($item.find('.ma-field-author-input'));
    };

    AuthorField.prototype.bindEvents = function() {
        var field = this;
        this.$wrapper.find('.item:not(processed)').addClass('processed').each(function() {
            field.applyEventItem($(this));
        });
    };

    AuthorField.prototype.applyAutocomplete = function($input) {
        var field = this;
        $input.autocomplete({
            minLength: 1,
            source: function(request, response) {
                $(this.element.context).siblings('.ma-field-author-value').val('');

                $.ajax( {
                    url: ajaxUrl,
                    method: 'POST',
                    dataType: 'json',
                    data: {
                        search: request.term,
                        action: 'ma_get_users'
                    },
                    success: function( resp ) {
                        response( resp.data );
                    }
                } );
            },
            select: function(event, ui) {
                var id = ui.item.id.toString();
                $(this).siblings('.ma-field-author-value').val(id);

                var label = ui.item.label;
                $(this).val(label);

                field.$wrapper.sortable('refresh');

                field.checkAddButtonState();
            }
        });
    };

    $('.multiple-authors-meta-box:not(.processed)').each(function() {
        new AuthorField(this);
        console.log(this);
    });

});
