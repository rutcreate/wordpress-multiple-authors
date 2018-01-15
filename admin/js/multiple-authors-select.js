/* globals global */
jQuery(function($) {

    if (!window.multipleAuthors) {
        return;
    }

    var ajaxUrl = window.multipleAuthors.ajaxUrl;
    var users = window.multipleAuthors.users;

    function getUser(userId) {
        for (var i = 0; i < users.length; i++) {
            if ( users[i].id == userId ) {
                return users[i];
            }
        }
    }

    $('.multiple-authors-meta-box:not(.processed)').each(function() {
        var $component = $(this);
        var $sortableList = $component.find('.sortable');
        var $selectList = $component.find('select');
        var inputName = $component.data('name');
        var inputValue = $component.data('value');
        var values = inputValue ? inputValue.toString().split(',') : [];

        function showOption(userId) {
            $selectList.find('.option-user-' + userId).show();
            $selectList.val('');
        }

        function hideOption(userId) {
            $selectList.find('.option-user-' + userId).hide();
            $selectList.val('');
        }

        function addItem(userId, withoutRefresh) {
            var user = getUser(userId);
            if (user) {
                $('\
                <li class="user-item-' + userId + '">\
                    <a href="#" data-id="' + user.id + '" class="button button-small remove">✗</a>\
                    <span style="cursor:move;">' + user.display_name + '</span>\
                    <input type="hidden" value="' + user.id + '" name="' + inputName + '" />\
                </li>').appendTo($sortableList);
            }

            if (!withoutRefresh) {
                refreshList();
            }
        }

        function removeItem(userId) {
            $('.user-item-' + userId).remove();
            refreshList();
        }

        function refreshList() {
            $sortableList.sortable('refresh');
        }

        // Create list items.
        for (var i = 0; i < values.length; i++) {
            addItem(values[i], true);
        }

        // Create select list.
        $('<option value="">- Select User -</option>').appendTo($selectList);
        for (var i = 0; i < users.length; i++) {
            var user = users[i];
            $('<option></option>')
                .val(user.id)
                .text(user.display_name)
                .addClass('option-user-' + user.id)
                .appendTo($selectList);

            if (values.indexOf(user.id) >= 0) {
                hideOption(user.id);
            }
        }
        $selectList.on('change', function(e) {
            var userId = e.target.value.toString();
            if (userId) {
                hideOption(userId);
                addItem(userId)
            }
        })

        $sortableList
            .disableSelection()
            .sortable({
                update: function(event, ui) {
                    // updateValues();
                }
            });

        $component.on('click', '.remove', function(e) {
            e.preventDefault();
            var userId = $(this).data('id');
            removeItem(userId);
            showOption(userId);
        });

        $component.addClass('processed');
    });

});
