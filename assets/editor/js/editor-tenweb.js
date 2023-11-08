jQuery( window ).on( 'elementor:init', function() {

  elementor.channels.editor.on( 'section:activated', function( sectionName, editor ) {
    var editedElement = editor.getOption( 'editedElementView' );
    var model = editedElement.getEditModel();
    var currentElementType = model.get( 'elType' );

    if ( 'widget' === currentElementType ) {
      currentElementType = model.get( 'widgetType' );
    }

    if ( 'twbb-flip-box' === currentElementType ) {
      var isSideBSection = -1 !== [ 'section_side_b_content', 'section_style_b' ].indexOf( sectionName );

      editedElement.$el.toggleClass( 'tenweb-flip-box--flipped', isSideBSection );

      var $backLayer = editedElement.$el.find( '.tenweb-flip-box__back' );

      if ( isSideBSection ) {
        $backLayer.css( 'transition', 'none' );
      }
      else {
        setTimeout( function() {
          $backLayer.css( 'transition', '' );
        }, 10 );
      }
    }
    else {
      editedElement.$el.parent().find('.elementor-widget-twbb-flip-box').each(function() {
        var container = jQuery(this);
        container.removeClass( 'tenweb-flip-box--flipped' );

        var $backLayer = container.find( '.tenweb-flip-box__back' );

        setTimeout( function() {
          $backLayer.css( 'transition', '' );
        }, 10 );
      });
    }
  } );

});
var tenwebShareButtons = (function() {
  return {
    networksClassDictionary: {
      pocket: 'fab fa-get-pocket',
      email: 'fa fa-envelope',
      print: 'fa fa-print'
    },

    networks: {
      'facebook': 'Facebook',
      'twitter': 'Twitter',
      'linkedin': 'LinkedIn',
      'pinterest': 'Pinterest',
      'reddit': 'Reddit',
      'vk': 'VK',
      'odnoklassniki': 'OK',
      'tumblr': 'Tumblr',
      'delicious': 'Delicious',
      'digg': 'Digg',
      'skype': 'Skype',
      'stumbleupon': 'StumbleUpon',
      'telegram': 'Telegram',
      'pocket': 'Pocket',
      'xing': 'XING',
      'whatsapp': 'WhatsApp',
      'email': 'Email',
      'print': 'Print',
    },

    getNetworkClass: function( networkName ) {
      return this.networksClassDictionary[ networkName ] || 'fab fa-' + networkName;
    },

    getNetworkTitle: function( buttonSettings ) {
      return buttonSettings.text || this.networks[ buttonSettings.button ];
    }
  };
})();
jQuery( window ).on( 'elementor:init', function() {

    elementor.channels.editor.on('elementorPlaylistWidget:setVideoData', function (e) {
        $e.run('document/elements/settings', {
            container: e.container,
            settings: {
                thumbnail: {
                    url: e.currentItem.thumbnail ? e.currentItem.thumbnail.url : ''
                },
                title: e.currentItem.video_title ? e.currentItem.video_title : '',
                duration: e.currentItem.duration ? e.currentItem.duration : ''
            },
            options: {
                external: true
            }
        });
    });

});

    var rest_route = twbb_editor.rest_route + "10webBuilder/conditions/";
    var conditions_added = false;
    var saved_conditions_length = 0;
    var btn_type = "publish";

    function hide_condition_popup() {
        var condition_text = '', condition_count;
        if ( twbb_editor.admin_condition_class ) {
            window.parent.tb_remove();
        } else {
            jQuery('.twbb-condition-popup-overlay').hide();
            jQuery('.twbb-condition-popup-overlay .twbb-condition-notif-container').html('').hide();
            jQuery('.twbb-condition-section-wrapper').css('display', 'block');
            jQuery('#twbb-condition-save').html(twbb_editor.texts.publish);
            btn_type = "publish";
        }
        condition_count = jQuery('.twbb-condition-section-wrapper').children().length;
        if( condition_count != 0 ) {
            if(jQuery('.twbb-condition-section-wrapper').children().length === 1) {
                condition_text = condition_count + ' condition';
            } else {
                condition_text = condition_count + ' conditions';
            }
        } else {
            condition_text = 'Add Condition';
        }
        window.parent.jQuery('.display_admin_condition_popup.selected_condition').html(condition_text);
    }

    jQuery(document).ready(function () {

        if ( twbb_editor.admin_condition_class ) {
            jQuery('.twbb-condition-popup-overlay').addClass(twbb_editor.admin_condition_class);
            jQuery('.twbb-condition-popup-overlay').show();
            if (conditions_added === false) {
                saved_conditions_length = twbb_editor.conditions.length;

                if (saved_conditions_length > 0) {
                    show_popup_loading();
                }

                for (var i in twbb_editor.conditions) {
                    add_condtion_html(twbb_editor.conditions[i]);
                }
                conditions_added = true;
            }
        }

        jQuery('.twbb-condition-popup-overlay').on('click', function (e) {
            var el = jQuery(e.target);
            if (
                el.hasClass('twbb-condition-popup-overlay') ||
                el.hasClass('twbb-condition-popup-close') ||
                el.closest('.twbb-condition-popup-close').length > 0
            ) {
                hide_condition_popup()
            }
        });

        jQuery(document).on('keyup', function (evt) {
            var $popup = jQuery('.twbb-condition-popup-overlay');
            if (evt.key === "Escape" && $popup.is(':visible')) {
                hide_condition_popup()
            }
        });

        jQuery('#twbb-condition-add-new').on('click', function (e) {
            e.preventDefault();
            add_condtion_html([]);
            return false;
        });

        jQuery('#twbb-condition-save').on('click', function (e) {
            e.preventDefault();

            var data_for_save = [];
            var sections = jQuery('.twbb-condition-section');

            if (check_widgets_type() === false) {
                return;
            }
            show_popup_loading();
            if (sections.length > 0) {
                sections.each(function () {

                    var $_this = jQuery(this);
                    var condition_data = {};
                    $_this.find('select').each(function () {
                        if (this.getAttribute('data-level') == '5') {
                            condition_data[this.getAttribute('data-name')] = jQuery(this).select2('val');
                        } else {
                            condition_data[this.getAttribute('data-name')] = this.value;
                        }
                    });
                    data_for_save.push(condition_data);
                });
            } else {
                data_for_save = [];
            }

            jQuery.ajax({
                type: "POST",
                url: rest_route + "save_conditions",
                data: {
                    conditions: JSON.stringify(data_for_save),
                    post_id: twbb_editor.post_id
                },
                beforeSend: function (xhr) {
                    xhr.setRequestHeader('X-WP-Nonce', twbb_editor.rest_nonce);
                }
            }).done(function (data) {
                hide_popup_loading();
                hide_condition_popup();
            }).fail(function (data) {
                hide_popup_loading();
                alert('Failed');
            });

            return false;
        });
    });

    function add_condtion_html(options) {
        var html = '<div class="twbb-condition-section">' +
            '<div class="twbb-condition-section-selects"></div>' +
            '<span class="twbb_condition_change_loader"><i class="fas fa-spin fa-circle-notch" aria-hidden="true"></i></span>' +
            '<div class="twbb-condition-delete"></div>' +
            '</div>';
        if(jQuery("#elementor-preview-iframe").contents().find('.twbb-condition-section-wrapper').length) {
              jQuery("#elementor-preview-iframe").contents().find('.twbb-condition-section-wrapper').append(html);
              var condition_section = jQuery("#elementor-preview-iframe").contents().find('.twbb-condition-section').last().find('.twbb-condition-section-selects');
        } else {
              jQuery('.twbb-condition-section-wrapper').append(html);
              var condition_section = jQuery('.twbb-condition-section').last().find('.twbb-condition-section-selects');
        }
        new condition(condition_section, options);
    }

    function show_popup_loading() {
      jQuery('.twbb-condition-popup-content').addClass('twbb-condition-popup-loading');
    }

    function hide_popup_loading() {
      jQuery('.twbb-condition-popup-content').removeClass('twbb-condition-popup-loading');
    }

    function saved_condition_added() {
        saved_conditions_length--;
        if (saved_conditions_length <= 0) {
            hide_popup_loading();
        }
    }

    function check_widgets_type() {
        var notifications = [];

        var notif_container = jQuery('.twbb-condition-notif-container');

        notif_container.html('').hide();


        if (twbb_editor.twbb_template_type === "twbb_archive") {
            if (twbb_is_widget_added("twbb-posts-archive") === false) {
                // notifications.push('<p class="twbb_c_error">Archive template required Posts Archive widget.</p>');
            }
        } else if (twbb_editor.twbb_template_type === "twbb_single" ) {
            if ( false === twbb_is_widget_added("twbbpost-content") && false === twbb_is_widget_added("post-content") && false === twbb_is_widget_added("twbb_product-content") && false === twbb_is_widget_added("product-content") ) {
                notifications.push('<p class="twbb_c_error"><span>!</span> ' + twbb_editor.texts.content_missing + '</p>');
            }
        }

        if (notifications.length > 0 && btn_type === "publish") {

            notif_container.html(notifications.join('')).show();
            btn_type = "continue";
            jQuery('.twbb-condition-section-wrapper').css('display', 'none');
            jQuery('#twbb-condition-save').html(twbb_editor.texts.continue);

            return false;
        } else {
            return true;
        }
    }

    var condition = function (condition_section, twbb_options) {
        var _this = this;

        var archive_static_pages = ['author', 'date', 'search'];
        var singular_static_pages = ['front_page', 'not_found'];
        var last_select_id;
        var saved_options_added = false;

        this.condition_section = condition_section;
        this.cache = [];
        this.options = twbb_options;
        this.template_type = twbb_editor.twbb_template_type;

        this.init = function () {

            set_last_select_id();
            add_condition_type();
            add_page_type();

            // this.options = [];
            this.condition_section.on('change', function (e) {

                var el = jQuery(e.target);
                var level = parseInt(el.data('level'));
                if (level < 2) {
                    return true;
                }
            });

            this.condition_section.closest('.twbb-condition-section').find('.twbb-condition-delete').on('click', function () {
                if (confirm(twbb_editor.texts.are_your_sure)) {
                    _this.condition_section.closest('.twbb-condition-section').remove();
                }
            });
        };

        function add_condition_type() {
            var html = '<select class="twbb-condition-type" data-name="condition_type" data-level="1">' +
                '<option ' + get_selected('condition_type', 'include') + ' value="include">' + twbb_editor.texts.include + '</option>' +
                '<option ' + get_selected('condition_type', 'exclude') + ' value="exclude">' + twbb_editor.texts.exclude + '</option>' +
                '</select>';

            remove_saved_option('condition_type');
            _this.condition_section.append(html);
        }

        function add_page_type() {
            var style = "";
            if (_this.template_type === "twbb_single" ) {
                _this.options.page_type = "singular";
                style = "style='display:none;'";
            } else if (_this.template_type === "twbb_archive") {
                _this.options.page_type = "archive";
                style = "style='display:none;'";
            }

            var html = '<select ' + style + ' class="twbb-condition-page-type" data-name="page_type" data-level="2">' +
                '<option ' + get_selected('page_type', 'general') + ' value="general">' + twbb_editor.texts.general + '</option>' +
                '<option ' + get_selected('page_type', 'archive') + ' value="archive">' + twbb_editor.texts.archive + '</option>' +
                '<option ' + get_selected('page_type', 'singular') + ' value="singular">' + twbb_editor.texts.singular + '</option>' +
                '</select>';

            remove_saved_option('page_type');
            _this.condition_section.append(html);

            _this.condition_section.find('.twbb-condition-page-type').on('change', function (e) {
              var value = e.target.value;

                level_changed(2);
                if (value === 'general') {
                    return true;
                }

                add_new_select({}, 'post_types/' + value, add_page_type_options, {"type": value}, value);
            }).trigger('change');
        }

        function add_page_type_options() {
            var options = this.response.options;
            var args = this.args;
            var options_html = '';
            /* Check if no condition is set or condition is removed, as it's post type is removed. */
            var got_selected = typeof _this.options['post_type'] === "undefined" || _this.options['post_type'] === '';
            for (var i in options) {
                got_selected = got_selected || 'selected' === get_selected('post_type', options[i].id);
                options_html += '<option ' + get_selected('post_type', options[i].id) + ' value="' + options[i].id + '">' + options[i].text + '</option>';
            }

            if ( got_selected === true ) {
                remove_saved_option('post_type');
                var class_name = 'twbb-condition-post-types twbb-condition-post-types-' + args.type;

                var html = '<select class="' + class_name + '" data-name="post_type" data-level="3">' +
                  options_html +
                  '</select>';

                _this.condition_section.append(html);
                _this.condition_section.find('.twbb-condition-post-types-' + args.type).on('change', function (e) {

                    level_changed(3);

                    if (args.type === 'archive') {
                        archive_type_on_change(e);
                    } else {
                        singular_type_on_change(e);
                    }
                }).trigger('change');
            }
            else {
                alert(twbb_editor.texts.condition_removed);
                _this.condition_section.closest('.twbb-condition-section').remove();
                saved_condition_added();
            }
        }

        function singular_type_on_change(e) {
            var value = e.target.value;

            if (value === 'all' || in_array(value, singular_static_pages)) {
                return true;
            }

            var cache_key = 'singular_type_' + value;

            add_new_select({}, 'post_filter_types/' + value, add_post_filter_types, {"type": value}, cache_key);
        }

        function add_post_filter_types() {
            var options = this.response.options;
            var options_html = '';

          for (var i in options) {
                options_html += '<option ' + get_selected('filter_type', options[i].id) + ' value="' + options[i].id + '">' + options[i].text + '</option>';
            }
            remove_saved_option('filter_type');
            var class_name = 'twbb-condition-post-filter-type';

            var html = '<select class="' + class_name + '" data-name="filter_type" data-level="4">' +
                options_html +
                '</select>';

          _this.condition_section.append(html);

            _this.condition_section.find('.twbb-condition-post-filter-type').on('change', function (e) {
                var value = e.target.value;

                level_changed(4);
                if (value === 'all') {
                    return;
                }

                add_select2(value);

            }).trigger('change');
        }

        function archive_type_on_change(e) {

            var value = e.target.value;

            if (value === 'all' || in_array(value, archive_static_pages)) {
                return true;
            }

            var cache_key = 'archive_type_' + value;

            add_new_select({}, 'archive_filter_types/' + value, add_archive_filter_types, {"type": value}, cache_key);
        }

        function add_archive_filter_types() {
            var options = this.response.options;
            var options_html = '';

            for (var i in options) {
                options_html += '<option ' + get_selected('filter_type', options[i].id) + ' value="' + options[i].id + '">' + options[i].text + '</option>';
            }
            remove_saved_option('filter_type');
            var class_name = 'twbb-condition-archive-filter-type';

            var html = '<select class="' + class_name + '" data-name="filter_type" data-level="4">' +
                options_html +
                '</select>';

            _this.condition_section.append(html);

            _this.condition_section.find('.twbb-condition-archive-filter-type').on('change', function (e) {
                var value = e.target.value;

                level_changed(4);
                if (value === 'all') {
                    return true;
                }

                add_select2(value);

            }).trigger('change');
        }

        function add_select2(search_in) {
            var rest_url = rest_route + ((search_in === 'specific_posts') ? 'posts/' : 'taxonomy/');


            var options_html = "";

            for (var i in _this.options.specific_pages_options) {
                var value = _this.options.specific_pages_options[i]['id'];
                var title = _this.options.specific_pages_options[i]['text'];

                options_html += '<option selected value="' + value + '">' + title + '</option>';
            }
            remove_saved_option('specific_pages_options');

            var html = '<select class="twbb-condition-get-specific-filters" multiple="multiple" data-name="specific_pages" data-level="5">' + options_html + '</select>';
            _this.condition_section.append(html);
            _this.condition_section.find('.twbb-condition-get-specific-filters').select2({
                ajax: {
                    url: rest_url,
                    data: function (params) {

                        var query = {
                            search_in: search_in,
                            search: params.term,
                            post_type: _this.condition_section.find('.twbb-condition-post-types-singular').val()
                        };

                        return query;
                    },
                    beforeSend: function (xhr) {
                        xhr.setRequestHeader('X-WP-Nonce', twbb_editor.rest_nonce);
                    },
                    processResults: function (data) {
                        return {
                            "results": data.data.options,
                        };

                    },
                }
            });

            level_changed(5);
        }

        function level_changed(level) {
            if (_this.template_type === 'twbb_archive' || _this.template_type === 'twbb_single') {
                _this.condition_section.attr('data-currentLevel', level - 1);
            } else {
                _this.condition_section.attr('data-currentLevel', level);
            }

            for (var i = level + 1; i <= 5; i++) {
                var el = _this.condition_section.find('select[data-level="' + i + '"]');

                if (el.length === 0) {
                    continue;
                }

                if (i === 5) {
                  el.select2('destroy');
                }

                el.remove();
            }
      _this.condition_section.find("#condition_change_loader").remove();


    }

        function add_new_select(args, endpoint, done, callback_args, cache_key) {

             if (cache_key === null || typeof _this.cache[cache_key] === 'undefined') {
                _this.condition_section.parent().find(".twbb_condition_change_loader").css("visibility", "visible");
                do_ajax(args, endpoint, done, callback_args, cache_key)
            } else {
                done.apply({
                    'response': _this.cache[cache_key],
                    'args': callback_args,
                });
            }
        }

        function do_ajax(args, endpoint, done, callback_args, cache_key) {

            var url = rest_route + endpoint;

            jQuery.ajax({
                type: "GET",
                url: url,
                data: args,
                beforeSend: function (xhr) {
                    xhr.setRequestHeader('X-WP-Nonce', twbb_editor.rest_nonce);
                }
            }).done(function (data) {

                if (data.success === false) {
                    alert('Failed');
                    return true;
                }

                if (typeof cache_key !== "undefined") {
                    _this.cache[cache_key] = data.data;
                }

                if (typeof done === "function") {

                    var done_args = {
                        response: data.data,
                        args: callback_args
                    };

                    done.apply(done_args);
                }
              if(jQuery("#elementor-preview-iframe").contents().find('.twbb-condition-section-wrapper').length) {
                jQuery("#elementor-preview-iframe").contents().find(".twbb_condition_change_loader").css('visibility', 'hidden');
              } else {
                jQuery(".twbb_condition_change_loader").css('visibility', 'hidden');
              }



            }).fail(function (data) {
                alert('Failed');
            });


        }

        function get_selected(key, value) {

            if (saved_options_added === true) {
                return "";
            }

            if (typeof _this.options[key] !== "undefined" && _this.options[key] == value) {
                return "selected";
            } else {
                return "";
            }
        }

        function remove_saved_option(key) {

            if (saved_options_added === true) {
                return;
            }

            if (typeof _this.options[key] !== "undefined") {
                if (key === "specific_pages_options") {
                    _this.options.specific_pages_options = [];
                } else {
                    delete _this.options[key];
                }
            }

            /* Last selection reached or first selection does not exist */
            if (last_select_id === key/* || ('post_type' == key && saved_options_added !== true)*/) {
                saved_options_added = true;
                saved_condition_added();
            }


        }

        function set_last_select_id() {
            if (_this.options.page_type === 'general') {
                last_select_id = 'page_type';
                return;
            }

            if (
                _this.options.post_type === 'all' ||
                in_array(_this.options.post_type, singular_static_pages) ||
                in_array(_this.options.post_type, archive_static_pages)
            ) {
                last_select_id = 'post_type';
                return;
            }

            if (_this.options.filter_type === 'all') {
                last_select_id = 'filter_type';
            } else {
                last_select_id = 'specific_pages_options';
            }
        }

        function in_array(key, arr) {
            for (var i in arr) {
                if (arr[i] === key) {
                    return true;
                }
            }

            return false;
        }

        this.init();
    };

jQuery(document).ready(function () {

    /*var $menu = jQuery("#tmpl-elementor-template-library-header-menu");
    var menu_item = '<div class="elementor-template-library-menu-item" id="elementor-template-library-menu-pre-made-tenweb" data-template-source="remote" data-template-type="tenweb">10WEB</div>';

    $menu.html(menu_item + $menu.html());

    var replace = [
        {
            "selector": '#tmpl-elementor-template-library-templates',
            "search": "'page' === activeType",
            "replace": "('page' === activeType || 'tenweb' === activeType)"
        },
        {
            "selector": '#tmpl-elementor-template-library-template-remote',
            "search": "'page' === type",
            "replace": "('page' === type || 'tenweb' === type)"
        }
    ];

    for (var i in replace) {
        var $el = jQuery(replace[i].selector);
        $el.html($el.html().replace(replace[i].search, replace[i].replace))
    }*/
});



var plugin_status = {};
/* 1 -- installed, 2 -- activated*/
var currentElType;
/* 1 -- if other plugin already installed or activated but not refresh, 2 -- if the action is in progress */
var need_reload = 0;

var plugin_slug;


jQuery(window).on('elementor:init', function () {
    elementor.channels.editor.on('section:activated', function (sectionName, editor) {
        var editedElement = editor.getOption('editedElementView');
        var model = editedElement.getEditModel();
        var currentElementType = model.get('elType');
        if ('widget' === currentElementType) {
            currentElementType = model.get('widgetType');
        }
        currentElType = currentElementType;

        if ( need_reload == 2 ) {
          setTimeout(function () {
            jQuery('.one_click_action').parent().html("<p class='twbb_description'>" + twbb.inprogress_msg + "</p>");
          }, 0);
        } else if ( need_reload == 1 ) {
            setTimeout(function () {
                jQuery('.one_click_action').parent().html("<p class='twbb_description'>" + twbb.reload_msg + "</p>");
            }, 0);
        } else {

            if (!(currentElementType in plugin_status)) { /* if obj has key plugin_status */
                plugin_status[currentElementType] = 0;
            }
            else if (plugin_status[currentElType] == 1) { /* if installed and activated  */
                setTimeout(function () {
                    jQuery('#install_plugin').parent().html("<p class='twbb_description'>" + twbb.install_success + "</p>");
                }, 0);
            }
            else if (plugin_status[currentElType] == 2) { /* if activated  */
                setTimeout(function () {
                    jQuery('#activate_plugin').parent().html("<p class='twbb_description'>" + twbb.activate_success + "</p>");
                }, 0);
            }
            else if (plugin_status[currentElType] == 3) { /* if updated  */
                setTimeout(function () {
                    jQuery('#activate_plugin').parent().html("<p class='twbb_description'>" + twbb.update_success + "</p>");
                }, 0);
            }
        }
    });
});

jQuery(document).ready(function () {

    jQuery(document).on('click', "#install_plugin", function () {
        jQuery(this).find(".spinner").css({"display": "inline-block", "visibility": "visible"});
        plugin_slug = jQuery(this).data("slug");
        if( jQuery(this).attr('data-is_paid') != '0' ) {
          jQuery.ajax({
            type: "POST",
            url: twbb.action_endpoint,
            data: {
              action: "install-activate",
              origin: "10web",
              product_id: jQuery(this).data("id"),
              tenweb_nonce: twbb.ajaxnonce
            },
            beforeSend: function (xhr) {
              xhr.setRequestHeader('X-WP-Nonce', twbb.ajaxnonce);
              need_reload = 2;
            },
            success: function (response) {
              jQuery(this).find(".spinner").css({"display": "none", "visibility": "hidden"});
              jQuery('#install_plugin').parent().html("<p class='twbb_description'>" + twbb.install_success + "</p>");
              plugin_status[currentElType] = 1;
              need_reload = 1;
            },
            failure: function (errorMsg) {
              need_reload = 1;
            },
            error: function (error) {
              window.location.reload();
            }
          });
        } else {
          twbb_install_external_plugin_free( plugin_slug );
        }
    });

    jQuery(document).on('click', "#activate_plugin", function () {
        jQuery(this).find(".spinner").css({"display": "inline-block", "visibility": "visible"});
        var plugin_slug = jQuery(this).data("slug");
        jQuery.ajax({
            type: "POST",
            url: twbb.action_endpoint,
            data: {
                action: "activate",
                origin: "10web",
                product_id: jQuery(this).data("id"),
                tenweb_nonce: twbb.ajaxnonce
            },
            beforeSend: function (xhr) {
                xhr.setRequestHeader('X-WP-Nonce', twbb.ajaxnonce);
                need_reload = 2;
            },
            success: function (response) {
                jQuery("#plugin_status").val("1");
                jQuery(this).find(".spinner").css({"display": "none", "visibility": "hidden"});
                jQuery('#activate_plugin').parent().html("<p class='twbb_description'>" + twbb.activate_success + "</p>");
                plugin_status[currentElType] = 2;
                need_reload = 1;
            },
            failure: function (errorMsg) {
                window.location.reload();
            },
            error: function (error) {
                window.location.reload();
            }
        });
    });

    jQuery(document).on('click', "#update_plugin", function () {

        jQuery(this).find(".spinner").css({"display": "inline-block", "visibility": "visible"});
        var plugin_slug = jQuery(this).data("slug");

        jQuery.ajax({
            type: "POST",
            url: twbb.action_endpoint,
            data: {
                action: "update",
                origin: "10web",
                product_id: jQuery(this).data("id"),
                tenweb_nonce: twbb.ajaxnonce
            },
            beforeSend: function (xhr) {
                xhr.setRequestHeader('X-WP-Nonce', twbb.ajaxnonce);
                need_reload = 2;
            },
            success: function (response) {
                jQuery("#plugin_status").val("1");
                jQuery(this).find(".spinner").css({"display": "none", "visibility": "hidden"});
                jQuery('#update_plugin').parent().html("<p class='twbb_description'>" + twbb.update_success + "</p>");
                plugin_status[currentElType] = 3;
                need_reload = 1;
            },
            failure: function (errorMsg) {
                window.location.reload();
            },
            error: function (error) {
                window.location.reload();
            }
        });
    });

    function twbb_install_external_plugin_free( plugin_slug ) {
        jQuery.ajax({
            type: "POST",
            url: twbb.action_endpoint,
            data: {
                action: "install-activate",
                origin: "wp.org",
                type: "plugin",
                slug: plugin_slug,
                tenweb_nonce: twbb.ajaxnonce
            },
            beforeSend: function (xhr) {
                xhr.setRequestHeader('X-WP-Nonce', twbb.ajaxnonce);
                need_reload = 2;
            },
            success: function (response) {
                jQuery(this).find(".spinner").css({"display": "none", "visibility": "hidden"});
                jQuery('#install_plugin').parent().html("<p class='twbb_description'>" + twbb.install_success + "</p>");
                plugin_status[currentElType] = 1;
                need_reload = 1;
            },
            failure: function (errorMsg) {
                window.location.reload();
            },
            error: function (error) {
                window.location.reload();
            }
        });
    }
});

jQuery(window).on('elementor:init', function () {
    var selectAjax = elementor.modules.controls.Select2.extend({

        isFirstTime: true,

        getSelect2DefaultOptions: function () {
            var _this = this;

            return jQuery.extend(elementor.modules.controls.Select2.prototype.getSelect2DefaultOptions.apply(this, arguments), {
                ajax: {
                    transport: function (params, success, failure) {

						var data = {
                            q: params.data.q,
                            filter_by: _this.model.get('filter_by'),
                            action: 'twbb_editor_get_posts'
                        };

                        var args = ['twbb_editor_select_ajax_get_options', {
                            data: data,
                            success: success,
                            error: failure
                        }];

                        return elementor.ajax.send.apply(elementor.ajax, args);
                    }
                },
                escapeMarkup: function (markup) {
                    return markup;
                },
                minimumInputLength: 1
            });
        },
        setSavedOptions: function () {
            var _this = this
            var ids = this.getControlValue();
            var filterBy = this.model.get('filter_by');

            if ( !ids || !filterBy ) {
                return;
            }

            if ( !Array.isArray(ids) ) {
                ids = [ids];
            }
            elementor.ajax.loadObjects({
                action: 'twbb_editor_select_ajax_get_saved_options',
                ids: ids,
                data: {
                    filter_by: filterBy,
                    unique_id: '' + _this.cid + filterBy
                },
                before: function () {
                    _this.disableControl();
                },
                success: function (data) {

                    _this.isFirstTime = false;

                    _this.model.set('options', data);

                    _this.render();
                }
            });
        },

        disableControl: function () {
            this.ui.select.prop('disabled', true);
            this.$el.find('.elementor-control-title').after('<span class="elementor-control-spinner">&nbsp;<i class="fa fa-spinner fa-spin"></i>&nbsp;</span>');
        },

        applySavedValue: function () {
            setTimeout(elementor.modules.controls.Select2.prototype.applySavedValue.bind(this));
            if (this.isFirstTime) {
                this.setSavedOptions();
            }
        }
    });

    elementor.addControlView('TWBBSelectAjax', selectAjax);
});
var start_data;
var end_data;
var diff_data={};
var loaded_templates;
var smallBG = true;

/* add Global Styles link to wuick navigation header */
jQuery(document).on('click', '.twbb_global_styles',function(){
  if (jQuery('#elementor-panel-header-kit-close').css('display') == 'none' ) {
    if (jQuery('#elementor-panel-header-menu-button .eicon-chevron-left').length == 0) {
      jQuery('#elementor-panel-header-menu-button').trigger('click');
    }
    jQuery('.elementor-panel-menu-item-global-settings').trigger('click');
  }
});

jQuery(document).on('click', '#elementor-panel-header-wrapper, #elementor-panel-elements-navigation',function(){
  twbb_create_settings_button();
});

function twbb_create_settings_button() {
  var checkExist = setInterval(function() {
    if (document.querySelector('#elementor-panel-elements-search-area') != null
      && document.querySelector('.twbb_global_styles') == null) {
      jQuery("#elementor-panel-elements-search-area").after("<div class=\"twbb_global_styles_cont\"><div class=\"twbb_global_styles_tooltip\" style=\"display: none;\">You can edit your whole website’s color and font all at once.</div><div class=\"twbb_global_styles\">Site Settings</div></div>")
      jQuery(".twbb_global_styles").hover(function () {
          jQuery(".twbb_global_styles_tooltip").show();
        },
        function () {
          jQuery(".twbb_global_styles_tooltip").hide();
        });
      clearInterval(checkExist);
    }
  }, 100);
}
window.addEventListener('DOMContentLoaded', function() {
  twbb_create_settings_button();
});

jQuery(document).ready(function () {
  jQuery("#wp-admin-bar-elementor_edit_page .ab-sub-wrapper").remove();

  jQuery("#template_popup_container select").on("click",function () {
    if(jQuery(this).hasClass("opened")) {
      jQuery(this).removeClass("opened");
    } else {
      jQuery(this).addClass("opened");
    }
  });

  /*Edit button*/
    if(twbb_options.is_post_template == 1){
        jQuery(".template_popup").addClass("template");
        jQuery(".template_popup h2#website_structure, .template_popup #website_structure_content").addClass("active");
    }
    else {
        jQuery(".template_popup").addClass("page");
    }

    if (typeof twbb_editor !== "undefined" && twbb_editor.twbb_template_type != "" && twbb_editor.twbb_template_type != "false") {
        jQuery('body').addClass(twbb_editor.twbb_template_type + '_template' + " twbb_template");
    }

  /* Template edit button Show/Hide */
  jQuery( "div[data-elementor-type='twbb_header'], div[data-elementor-type='twbb_single'], div[data-elementor-type='twbb_archive'], div[data-elementor-type='twbb_footer']" ).hover(function() {
      if( self == top || twbb_options.is_post_template == '1' || jQuery(this).find(".edit_templ").length == 0 ) { /* Check if the parent has iframe */
        return;
      }
      jQuery(this).css("position","relative");
      jQuery(this).find( ".edit_templ" ).show();
      jQuery(this).addClass("twbb_template-border");
  }, function() {
      if( self == top || twbb_options.is_post_template == '1' || jQuery(this).find(".edit_templ").length == 0 ) { /* Check if the parent has iframe */
        return;
      }
    jQuery(this).find( ".edit_templ" ).hide();
      jQuery(this).removeClass("twbb_template-border");
    }
  );

   /* Popup Show template section click */
  jQuery(document).on("click", ".edit_templ_button", function (e) {
    if( self == top ) { /* Check if the parent has iframe */
      return;
    }
    var loaded_tmp = twbb_options.loaded_templates;
    Object.keys(loaded_tmp).forEach( function(k){
      jQuery('#'+k+'_template').val(loaded_tmp[k]); /* Set current to select */
      if(jQuery('#'+k+'_template').length) {
        var edit_href = jQuery('#' + k + '_template').closest(".template_row").find('.edit_template_global').attr('href').replace('{post_id}', loaded_tmp[k]);
        jQuery('#' + k + '_template').closest(".template_row").find('.edit_template_global').attr('href', edit_href);
      }
    });
    var template = (jQuery(this).parent().data("template") != "") ? jQuery(this).parent().data("template") : "twbb_header";

    jQuery("#twbb_header_container, #twbb_footer_container, #twbb_single_container, #twbb_archive_container").hide();
    /* hide select open arrow icon */
    jQuery("#" + template + "_container #" + template + "_template").attr("class", "");
    /* If template has only one template hide arrow icons */
    if(jQuery("#" + template + "_container #" + template + "_template").data("single") == '1') {
      jQuery("#" + template + "_container #" + template + "_template").css({'background':'none'});
      jQuery("#" + template + "_container #" + template + "_template").attr("disabled","disabled");
    }

    jQuery(".template_popup.page_layout, #" + template + "_container").show();

    start_data = {
      "header_template": jQuery("#twbb_header_template").val(),
      "single_template": jQuery("#twbb_single_template").val(),
      "archive_template": jQuery("#twbb_archive_template").val(),
      "footer_template": jQuery("#twbb_footer_template").val(),
    };
    jQuery("#template_popup_container .template_select select").each(function () {
      jQuery(this).attr("data-current", jQuery(this).val());
    });

  });

  jQuery(document).on("click", ".twbb_nav_footer_menu, .twbb_nav_header_menu", function (e) {
    var loaded_tmp = twbb_options.loaded_templates;
    var iframe = jQuery("#elementor-preview-iframe").contents();
    Object.keys(loaded_tmp).forEach( function(k){

      iframe.find('#'+k+'_template').val(loaded_tmp[k]); /* Set current to select */
      var edit_href = iframe.find('#'+k+'_template').closest(".template_row").find('.edit_template_global').attr('href').replace('{post_id}',loaded_tmp[k]);
      iframe.find('#'+k+'_template').closest(".template_row").find('.edit_template_global').attr('href', edit_href);
    });
    var template = (jQuery(this).parent().data("template") != "") ? jQuery(this).parent().data("template") : "twbb_header";

    /* Check if template is created */
    if(!iframe.find("#" + template + "_container #"+template+"_template option").length) {
      iframe.find("#twbb_header_container, #twbb_footer_container, #twbb_single_container, #twbb_archive_container").hide();
      iframe.find("#" + template + "_container #" + template + "_template").attr("class", "");
      iframe.find(".template_popup.page_layout, #" + template + "_container .template_row").css({'display':'none'});
      iframe.find(".template_popup.page_layout, #" + template + "_container").css({'height':'unset'});
      iframe.find(".template_popup.page_layout, #" + template + "_container").show();
      jQuery(".twbb_sub_menu").hide();
      return;
    } else if( !( template in loaded_tmp ) ) {
      //Removed an existing one to be sure the newly created will be selected.
      iframe.find("#" + template + "_container #"+template+"_template").find("option[value=0]").remove();
      iframe.find("#" + template + "_container #"+template+"_template").prepend('<option selected value="0">Choose template</option>');
    }

    iframe.find("#twbb_header_container, #twbb_footer_container, #twbb_single_container, #twbb_archive_container").hide();
    iframe.find("#" + template + "_container #" + template + "_template").attr("class", "");
    if(iframe.find("#" + template + "_container #" + template + "_template").data("single") == '1' && iframe.find("#" + template + "_container #" + template + "_template").val() != '0' ) {
      iframe.find("#" + template + "_container #" + template + "_template").addClass('single');
      iframe.find("#" + template + "_container #" + template + "_template").attr("disabled","disabled");
      iframe.find("#" + template + "_container #" + template + "_template").css({'background':'none'});

    }
    iframe.find(".template_popup.page_layout, #" + template + "_container").show();
    if( self == top ) {
      start_data = {
        "header_template": jQuery("#elementor-preview-iframe").contents().find("#twbb_header_template").val(),
        "single_template": jQuery("#elementor-preview-iframe").contents().find("#twbb_single_template").val(),
        "archive_template": jQuery("#elementor-preview-iframe").contents().find("#twbb_archive_template").val(),
        "footer_template": jQuery("#elementor-preview-iframe").contents().find("#twbb_footer_template").val(),
      };
    } else {
      start_data = {
        "header_template": jQuery("#twbb_header_template").val(),
        "single_template": jQuery("#twbb_single_template").val(),
        "archive_template": jQuery("#twbb_archive_template").val(),
        "footer_template": jQuery("#twbb_footer_template").val(),
      };

    }
    jQuery("#template_popup_container .template_select select").each(function () {
      jQuery(this).attr("data-current", jQuery(this).val());
    });
    jQuery(".twbb_sub_menu").hide();
  });

  jQuery(document).on("click",".add-template-link", function(){
    window.open(jQuery(this).attr("href"));
  });
  /* Show save button in popup */
  jQuery(document).on("change", "#template_popup_container .template_select select", function (e) {
    if(!jQuery(this).hasClass("active")){
      jQuery(this).parent().find(".edit_template_global").hide();
      jQuery(this).closest(".template_row").find(".template_select").addClass("active");
    }

    /* return edit button when select changing to start position */
    if(jQuery(this).attr("data-current") == jQuery(this).val()) {
      jQuery(this).closest(".template_row").find(".template_select").removeClass("active");
      if(jQuery(this).val() != 0) {
        jQuery(this).parent().find(".edit_template_global").show();

      }
    }

  });

  /* Save page templates action from popup */
  jQuery(document).on("click", "#twbb_popup_save", function () {
    jQuery(".twbb-save-popup-loader").css("display","inline-block");
    if( self == top ) {
      end_data = {
        "header_template" : jQuery("#elementor-preview-iframe").contents().find("#twbb_header_template").val(),
        "single_template" : jQuery("#elementor-preview-iframe").contents().find("#twbb_single_template").val(),
        "archive_template" : jQuery("#elementor-preview-iframe").contents().find("#twbb_archive_template").val(),
        "footer_template" : jQuery("#elementor-preview-iframe").contents().find("#twbb_footer_template").val(),
      };
    } else {
      end_data = {
        "header_template" : jQuery("#twbb_header_template").val(),
        "single_template" : jQuery("#twbb_single_template").val(),
        "archive_template" : jQuery("#twbb_archive_template").val(),
        "footer_template" : jQuery("#twbb_footer_template").val(),
      };
    }
    Object.keys(end_data).forEach(function(k){
      if( typeof start_data == 'undefined' ) {
        start_data = window.parent.start_data;
      }
      if( start_data[k] != end_data[k] ) {
          diff_data[k] = end_data[k];
      }
    });
    start_data = new Object;
    data = diff_data;
    data['current_post_id'] = twbb_options.post_id;
    data['task'] = 'save_popup';
    data['page_type'] = twbb_options.twbb_page_type;
    jQuery.ajax({
      type: "GET",
      url: twbb_options.popup_template_ajax,
      data: data,
      beforeSend: function (xhr) {
        xhr.setRequestHeader('X-WP-Nonce', twbb_options.rest_nonce);
      }
    }).done(function (data) {
      elementor.reloadPreview();
    }).fail(function (data) {
      alert('Failed');
    });
  });


  /* Close/Hide popup */
  jQuery(document).mouseup(function (e){
    var container = jQuery("#template_popup_container,#template_popup_container, .twbb-condition-popup-overlay");
    if (!container.is(e.target) && container.has(e.target).length === 0) {
      jQuery(".template_popup").hide();
      jQuery(".template_popup.page_layout").hide();
      jQuery(".template_popup.website_structure").hide();
    }
  });

  jQuery(document).on("click", "#template_popup_container .close_popup, #template_popup_container .close_popup", function (e) {
      jQuery(".template_popup.page_layout").hide();
      jQuery(".template_popup.website_structure").hide();
  });

  /* Edit current template from popup */
  jQuery(document).on("click", ".edit_template_global, .edit_template, .structure-section-edit, .more a", function () {
      if(jQuery(this).closest("body.elementor-editor-active").length){
          window.open( jQuery(this).attr("href"), "_blank" );
      }
  });

  /* Show finder popup */
  jQuery(document).on("click", ".twbb_finder", function () {
    if ( 'function' == typeof( parent.elementorCommon.finder.getLayout ) ) {
      parent.elementorCommon.finder.getLayout().showModal();
    }
    else {
      parent.$e.route('finder');
    }
    jQuery("#elementor-preview-iframe").contents().find(".template_popup").hide();
  });

  if( self != top ) {
    window.parent.twbb_options.loaded_templates = twbb_options.loaded_templates;
  }
});

jQuery (window).on('elementor:loaded', function () {
  elementor.on('preview:loaded', function () {
    var iframe = jQuery("#elementor-preview-iframe").contents();

    /* Hide Custom Header if Panel closed */
    jQuery("#elementor-mode-switcher").on("click", function () {
      if ( !jQuery("body").hasClass("elementor-editor-preview") ) {
        jQuery("#twbb_custom_header").hide();
        jQuery("#elementor-panel-header-title img").hide();
      } else {
        jQuery("#twbb_custom_header").show();
        jQuery("#elementor-panel-header-title img").show();
      }
    });

  /*--Move header from iframe --*/
  var html = iframe.find("#twbb_custom_header").wrap('<p/>').parent().html();
  iframe.find("#twbb_custom_header").unwrap();
  iframe.find("#twbb_custom_header").remove();
  // Remove from top as well as header is being recreated each time the preview is loaded.
  jQuery("#twbb_custom_header").remove();
  jQuery("#elementor-responsive-bar").after(html);

  if( twbb_options.header_button != 'header_footer' ) {
    jQuery("#twbb_custom_header .header_footer").hide();
    if (twbb_options.header_button == 'condition') {
      jQuery("#twbb_custom_header .advanced").show();
    }
  }
  jQuery("#twbb_custom_header").show();


    jQuery('.advanced').click(  function (e) {
      jQuery('.twbb-condition-popup-overlay').show();
      if (conditions_added === false) {
        saved_conditions_length = twbb_editor.conditions.length;

        if (saved_conditions_length > 0) {
          show_popup_loading();
        }

        for (var i in twbb_editor.conditions) {
          add_condtion_html(twbb_editor.conditions[i]);
        }
        conditions_added = true;
      }
    });

    jQuery(document).on("click",".elementor-templates-modal__header__close .eicon-close",function(){
      iframe.find(".template_popup.twbb_finder_popup_layout").hide();
    });

    jQuery(document).on("click", "#elementor-finder__modal", function(e) {
      if(!jQuery(e.target).closest(".dialog-widget-content.dialog-lightbox-widget-content.ui-draggable.ui-draggable-handle").length) {
        iframe.find(".template_popup.twbb_finder_popup_layout").hide();
      }
    });


    var width = jQuery("#elementor-preview").width();
    responsive_css_header( width );
    preview_resize();

  /* Scroll using perfect-scrollbar.js library which include elementor */
  var ps = '';
  if ( typeof PerfectScrollbar != "undefined" && jQuery('.twbb_sub_menu_cont .twbb_sub_menu').length ) {
    ps = new PerfectScrollbar('.twbb_sub_menu_cont .twbb_sub_menu',{
      suppressScrollX: true
    });
  }

    var is_current = jQuery("#twbb_custom_header .website_structure .twbb_sub_menu .title_container .current").length;
    /**/
    jQuery( "#twbb_custom_header .website_structure .twbb_sub_menu_cont .twbb_sub_menu .title").each( function() {
      var title_container = jQuery(this).find(".title_container");
      if( (title_container.hasClass('opened') && !is_current) || jQuery(this).find(".current").length) {
        title_container.removeClass('closed');
        title_container.addClass('opened');
        title_container.parent().addClass('active');
        title_container.parent().find(".twbb-widget-icon.twbb-arrow-down").removeClass("twbb-arrow-down").addClass("twbb-arrow-up");
        is_current = true;
      } else {
        title_container.addClass('closed');
        title_container.removeClass('opened');
        title_container.parent().removeClass('active');
        title_container.parent().find(".twbb-widget-icon.twbb-arrow-up").removeClass("twbb-arrow-up").addClass("twbb-arrow-down");
      }
    });

    if( !is_current ) {
      jQuery("#twbb_custom_header .website_structure .twbb_sub_menu_cont .twbb_sub_menu .title .title_container").first().removeClass('closed').addClass('opened');
    }

    /* open/close sections in Website Structure menu */
    jQuery("#twbb_custom_header .website_structure .twbb_sub_menu_cont .twbb_sub_menu .title>label").on("click", function() {
      if(jQuery(this).parent().find(".title_container").hasClass('opened')) {
        jQuery(this).parent().find(".title_container").removeClass('opened');
        jQuery(this).parent().find(".title_container").addClass('closed');
        jQuery(this).parent().removeClass('active');
        jQuery(this).parent().find(".twbb-widget-icon.twbb-arrow-up").removeClass("twbb-arrow-up").addClass("twbb-arrow-down");
      } else {
        jQuery(this).parent().find(".title_container").removeClass('closed');
        jQuery(this).parent().find(".title_container").addClass('opened');
        jQuery(this).parent().addClass('active');
        jQuery(this).parent().find(".twbb-widget-icon.twbb-arrow-down").removeClass("twbb-arrow-down").addClass("twbb-arrow-up");

      }
      /* Perfect scroll */
      if(ps != '') {
        ps.update();
      }
    });

    /* Disable preview icon if template */
    if( twbb_options.is_post_template == 1 ) {
      jQuery("#elementor-panel-footer-saver-preview, #elementor-panel-footer-saver-preview-label").on("click", function () {
        return false;
      });

      jQuery("#elementor-panel-footer-saver-preview").css({
        "opacity":"0.2",
        "cursor":"default"
      });

      jQuery("#elementor-panel-footer-saver-preview").attr("data-tooltip","");
    }
  });
});

jQuery( window ).on( 'elementor:init', function() {
  /* Hide 'Have a look message.' */
  if( twbb_options.is_post_template == 1 ) {
    elementor.saver.on( 'page:status:change', function () {
      setTimeout( function () {
        jQuery('#elementor-toast').hide();
        elementor.notifications.getToast().hide();
      } );
    } );
  }

} );

jQuery(window).on('resize', function(){
  var width = jQuery("#elementor-preview").width();
  responsive_css_header( width )
});

function twbb_add_widget(name, widget) {

  if (typeof twbb_widgets[name] === "undefined") {
    twbb_widgets[name] = [];
  }
  twbb_widgets[name].push(widget);
}

function twbb_get_widgets(name) {
  if (typeof twbb_widgets[name] === "undefined") {
    return [];
  } else {
    return twbb_widgets[name];
  }
}

function twbb_is_widget_added(name) {
  var previewIframe = jQuery('#elementor-preview-iframe').contents();
  return (jQuery('.elementor-widget-' + name).length > 0 || previewIframe.find('.elementor-widget-' + name).length > 0);
}

function preview_resize() {
  /* -- Header & Footer menu submenu show/hide -- */
  jQuery(".twbb_nav li").on("mouseover", function() {
      jQuery(".twbb_nav .twbb_sub_menu, .twbb_nav .twbb_sub_menu_cont").hide();
      jQuery(this).find(".twbb_sub_menu").show();
      jQuery(this).find(".twbb_sub_menu_cont").show();
  }).mouseleave(function() {
      jQuery(this).find(".twbb_sub_menu").hide();
      jQuery(this).find(".twbb_sub_menu_cont").hide();
    });
  jQuery(".twbb_upgrade_for_trial_users").on("mouseover", function() {
    jQuery(this).find(".twbb_upgrade_submenu").show();
  }).mouseleave(function() {
    jQuery(this).find(".twbb_upgrade_submenu").hide();
  });

  var clicking = false;
  jQuery('.ui-resizable-handle').mousedown(function(){
    clicking = true;
  });
  jQuery(document).mouseup(function(){
    clicking = false;
  });

  jQuery(document).mousemove('.ui-resizable-handle',function(){
    if(clicking == false) return;
    // Mouse click + moving logic here
    var width = jQuery("#elementor-preview").width();
    responsive_css_header( width );
  });
}

var resp_status = {'default': false, 'default_small': false, '900': false, '950': false, '1150': false, '1250': false, '1370': false};

function reset_resp_status( resp_status, current_key ) {
  for (var key in resp_status) {
    if( key != current_key ) {
      resp_status[key] = false;
    } else {
      resp_status[key] = true;
    }
  }
}
function responsive_css_header( width ) {

  /* During the window load width return null */
  if( !width ) {
    width = 1300;
  }

  if ( width < 1150) {
    if ( !resp_status['default_small'] ) {
      reset_resp_status(resp_status, 'default_small');
      jQuery("#twbb_custom_header .twbb_dashboard a").css({"margin": "0 20px 0 10px"});
      jQuery("#twbb_custom_header .twbb_upgrade_for_trial_users").css({"padding-right": "10px"});
      jQuery("#twbb_custom_header .twbb_nav li").css("margin-right", "10px");
      jQuery("#twbb_custom_header .twbb_nav .twbb_sub_menu .title_container li a").css({"padding": "0px 70px 0 10px", "overflow-wrap": "break-word"});
      jQuery("#twbb_custom_header a").css({"font-size": "12px"});
      jQuery("#twbb_custom_header label").css({"font-size": "12px"});
      jQuery("#twbb_custom_header #twbb_website_structure").css({"padding": "0 25px"});
    }
  }

  if ( width < 900 ) {
      if ( !resp_status['900'] ) {
        reset_resp_status(resp_status, '900');
        jQuery("#twbb_custom_header .nav_prev_next").hide();
        jQuery("#twbb_custom_header .twbb_watch_video").hide();
        jQuery(".twbb_finder").find("label").hide();

      }
  } else if ( width < 950 ) {
      if ( !resp_status['950'] ) {
        reset_resp_status(resp_status, '950');
        jQuery(".nav_prev_next").hide();
        jQuery("#twbb_custom_header .twbb_watch_video").show();
        jQuery("#twbb_custom_header #display_finder").show();
        jQuery(".twbb_finder").find("label").show();
      }
  } else if ( width < 1150 ) {
      if ( !resp_status['1150'] ) {
        reset_resp_status(resp_status, '1150');
        jQuery(".nav_prev_next").show();
        jQuery("#twbb_custom_header .twbb_nav li").css("margin-right", "10px");
        jQuery("#twbb_custom_header a").css({"font-size": "12px"});
        jQuery("#twbb_custom_header #twbb_website_structure").css({"padding": "0 25px"});
        jQuery("#twbb_custom_header .twbb_dashboard a").css({"margin": "0 20px 0 15px"});
        jQuery("#twbb_custom_header .twbb_upgrade_for_trial_users").css({"padding-right": "15px"});
      }
  } else if ( width < 1250 ) {
      if ( !resp_status['1250'] ) {
        reset_resp_status(resp_status, '1250');
        jQuery(".twbb_finder").find("label").show();
        jQuery(".nav_prev_next").hide();
        jQuery("#twbb_custom_header .twbb_nav li").css("margin-right", "10px");
        jQuery("#twbb_custom_header a").css("font-size", "12px");
        jQuery("#twbb_custom_header .twbb_dashboard a").css({"font-size": "12px", "line-height": "18px"});
        jQuery("#twbb_custom_header label").css("font-size", "12px");
      }
  } else if ( width < 1370 ) {
      if ( !resp_status['1370'] ) {
        reset_resp_status(resp_status, '1370');
        jQuery("#twbb_custom_header .twbb_dashboard a").css({"margin": "0 20px 0 20px"});
        jQuery("#twbb_custom_header .twbb_nav li").css("margin-right", "10px");
        jQuery("#twbb_custom_header #display_finder").show();
        jQuery(".twbb_finder").find("label").show();
        jQuery(".nav_prev_next").show();
      }
  } else {
    if ( !resp_status['default'] ) {
      reset_resp_status(resp_status, 'default');
      jQuery(".twbb_finder").find("label").show();
      jQuery("#twbb_custom_header #display_finder").show();
      jQuery("#twbb_custom_header a").css({"font-size": "12px"});
      jQuery("#twbb_custom_header .twbb_dashboard a").css({"font-size": "12px", "line-height": "18px"});
      jQuery("#twbb_custom_header .website_structure .title_container a").css({"font-size": "14px"});
      jQuery("#twbb_custom_header .website_structure .twbb_sub_menu li a.view_more").css({"font-size": "12px"});
      jQuery("#twbb_custom_header label").css({"font-size": "12px"});
      jQuery("#twbb_custom_header .twbb_dashboard a").css({"margin": "0px 30px 0 20px"});
      jQuery("#twbb_custom_header .twbb_upgrade_for_trial_users").css({"padding-right": "20px"});
      jQuery("#twbb_custom_header .twbb_nav .twbb_sub_menu .title_container li a").css({"padding": "0px 70px 0 10px", "overflow-wrap": "break-word"});
      jQuery("#twbb_custom_header #twbb_website_structure").css({"padding": "0 25px"});
      jQuery("#twbb_custom_header .twbb_nav li.header_footer, #twbb_custom_header .twbb_nav li.advanced").css("margin-right", "20px");
    }
  }
}
