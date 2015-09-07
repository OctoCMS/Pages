function saveTypeConfig(cb) {
    var jsonConfig = JSON.stringify(window.typeConfig);
    var activeTab = $('.nav-tabs li.active').data('key');

    $.post(window.adminUri + '/content-type/save/' + window.typeId, {definition: jsonConfig, activeTab: activeTab}, function (data) {
        $('.property-editor').html(data);
        attachEvents();

        if (cb) {
            cb();
        }
    });
}

$(document).ready(function () {
    attachEvents();
});

function attachEvents()
{
    $('select.select2, input.select2').select2('destroy');
    $('select.select2, input.select2').select2();

    $('.add-tab').on('click', function (e) {
        e.preventDefault();

        var newKey = window.typeConfig.length;
        window.typeConfig.push({protected: false, properties: [], name: "New Tab"});

        saveTypeConfig(function () {
            $('.tab-' + newKey + ' a').trigger('click');
        });
    });

    $('span.tab-title').on('dblclick', function () {
        var self = $(this);

        self.attr('contenteditable', true);

        self.on('keydown', function(e) {
            if(e.keyCode == 13 || e.keyCode == 9)
            {
                e.preventDefault();
                self.blur();
                return false;
            }
        });

        self.on('blur', function () {
            var tab = self.parents('li');
            var key = tab.data('key');

            if (window.typeConfig[key].name != self.text()) {
                window.typeConfig[key].name = self.text();
                saveTypeConfig();
            }
        });
    })

    $('.nav-tabs li a span.fa-close').on('click', function (e) {
        e.stopPropagation();

        if (confirm("Are you sure you want to delete this tab and the properties within it?")) {
            var tab = $(this).parents('li');
            var key = tab.data('key');

            window.typeConfig.splice(key, 1);

            $('.tab-0 a').trigger('click');

            tab.fadeOut(400, function () {
                tab.remove();
            });

            saveTypeConfig();
        }

        return false;
    });

    $('.new-property-form input[name="name"]').on('change', function () {
        var key = $(this).parents('.new-property-form').find('input[name="key"]');

        if (key.val() == '') {
            newKey = $(this).val().replace(/[^a-zA-Z0-9]/g, '');
            newKey = newKey.charAt(0).toLowerCase() + newKey.slice(1);
            key.val(newKey);
        }
    });

    $('.new-property-form').on('submit', function (e) {
        e.preventDefault();

        var tab = $(this).parents('.tab-pane').data('key');

        var property = {
            name: $(this).find('input[name=name]').val(),
            type: $(this).find('select[name=type]').val(),
            description: $(this).find('input[name=description]').val()
        };

        if (!window.typeConfig[tab].properties || window.typeConfig[tab].properties.length == 0) {
            window.typeConfig[tab].properties = {};
        }

        window.typeConfig[tab].properties[$(this).find('input[name=key]').val()] = property;

        saveTypeConfig();
    });

    $('.remove-property').on('click', function (e) {
        e.stopPropagation();

        if (confirm("Are you sure you want to delete this property?")) {
            var tab = $(this).parents('.tab-pane').data('key');
            var key = $(this).data('key');

            delete window.typeConfig[tab].properties[key];

            saveTypeConfig();
        }

        return false;
    });
}

