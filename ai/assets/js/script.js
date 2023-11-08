
class TWBAI {
  constructor() {
    this.cache = {};
    this.option = '';
    this.type = 'text';
    this.model = {};
    this.view = {};
  }

  cacheElements() {
    let self = this;
    self.cache.controls = [
      "text",
      "title",
      "title_text",
      "description_text",
      "editor",
      "tab_title",
      "tab_content",
      "inner_text",
      "testimonial_content",
      "testimonial_name",
      "testimonial_job",
      "alert_title",
      "alert_description",
      "link_text",
      "prefix",
      "suffix",
    ];
    self.cache.sub_controls = [
      "tabs",
      "icon_list",
      "social_icon_list",
      "slides",
    ];
    self.cache.coming_soon_controls = [
      "image",
      "selected_icon",
      "social_icon",
      "selected_active_icon",
      "dismiss_icon",
      "testimonial_image",
      "custom_css",
      "html",
    ];

    self.build();
  }

  ai_button(cont) {
    let self = this;
    let all_controls = self.cache.controls.concat(self.cache.coming_soon_controls);
    let coming_soon_controls = self.cache.coming_soon_controls.map(element => "elementor-control-" + element);
    for (let i in all_controls) {
      cont.find(".elementor-control-" + all_controls[i]).each(function () {
        if (jQuery(this).find(".twb-ai-button").length === 0) {
          let is_coming_soon = self.containsAny(jQuery(this).attr("class").split(" "), coming_soon_controls);
          let label = jQuery(this).find(".elementor-control-title");
          let button = jQuery("<button>", {
            class: "twb-ai-button" + ((window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches &&
              twbb_admin_vars.twbb_ui_theme == 'auto' ||
              twbb_admin_vars.twbb_ui_theme == 'dark') ? " twbb-ai-dark" : ""),
            "data-type": all_controls[i],
            html: jQuery(this).hasClass("elementor-label-block") ? "Write with AI" : "",
            onClick: is_coming_soon ? "" : "twbAIOb.event(this)"
          });
          // Add the button after the label.
          if(!is_coming_soon) jQuery(label).after(button);
          // Add tooltip to the coming soon buttons.
          if (is_coming_soon) {
            let tooltip = jQuery("<div>", {
              class: "twb-ai-button-tooltip",
              html: "Coming soon",
              style: "display: none;"
            });
            jQuery(button).append(tooltip).hover(function () {
                jQuery(this).find(".twb-ai-button-tooltip").show();
              },
              function () {
                jQuery(this).find(".twb-ai-button-tooltip").hide();
              });
          }
        }
      });
    }
  }

  build() {
    let self = this;

    // On adding/editing widgets.
    elementor.hooks.addAction( 'panel/open_editor/widget', function( panel, model, view ) {
      self.model = model;
      self.view = view;
      self.ai_button(panel.$el);

      // On closing/opening sections.
      jQuery('#elementor-controls').on('mouseenter', function(){
        self.ai_button(jQuery("#elementor-controls"));
      });
    });

    // On changing between tabs/sections.
    jQuery(document).on('click', '#elementor-panel-page-editor', function(){
      self.ai_button(jQuery("#elementor-controls"));
      jQuery('.elementor-control-type-section').on('click', function(){
        self.ai_button(jQuery("#elementor-controls"));
      });
    });
  }

  event(that) {
    let self = this;
    self.option = that;
    self.type = jQuery(that).data("type");
    let current_text = self.getSetting();

    self.show_ai_popup( current_text );
    self.use_text_click_event();
    self.new_prompt_click_event();
  }

  new_prompt_click_event() {
    let self = this;
    jQuery(document).on("click", ".twbb-ai-new-prompt-button", function(){
      jQuery(".twbb-ai-error-message").hide();
      self.show_ai_popup( '' );
    });
  }

  use_text_click_event() {
    let self = this;
    jQuery(document).on("click", ".twbb-ai-use-text-button", function(){
        let selectedText = jQuery(this).closest(".twbb-ai-suggested-propmts-container").find(".twbb-ai-text").val();
        self.setSetting(selectedText);
        self.hide_ai_popup();
    });
  }

  hide_ai_popup() {
    jQuery(".twbb-ai-popup-layout, .twbb-ai-popup-container, .twbb-ai-propmts-empty-container, .twbb-ai-text-prompts, .twbb-ai-headline-prompts, .twbb-ai-propmts-result-container").hide();
  }

  show_ai_popup( text ) {
    let self = this;
    if ( text == '' ) {
        jQuery(document).find(".twbb-ai-description-input").val('').trigger("change");
        if( self.type.indexOf("title") === -1 ) {
          jQuery(".twbb-ai-text-prompts").show();
        } else {
          jQuery(".twbb-ai-headline-prompts").show();
        }
        jQuery(".twbb-ai-propmts-result-container").hide();
        jQuery(".twbb-ai-popup-layout, .twbb-ai-popup-container, .twbb-ai-propmts-empty-container").show();
    }
    else {
        jQuery(".twbb-ai-result-textarea").val(text);
        jQuery(".twbb-ai-propmts-empty-container").hide();
        jQuery(".twbb-ai-popup-layout, .twbb-ai-popup-container, .twbb-ai-propmts-result-container").show();
    }
  }

  /**
   * Change the setting value by type.
   *
   * @param value
   */
  setSetting(value) {
    let self = this;

    self.model.setSetting(self.getType(), value);
    self.view.render();
    self.view.renderHTML();
  }

  /**
   * Get setting by type.
   *
   * @returns {*}
   */
  getSetting() {
    let self = this;

    return self.model.getSetting(self.getType());
  }

  /**
   * Get the type of option. Difference is between dynamic options.
   *
   * @returns {string}
   */
  getType() {
    let self = this;
    let type = self.type;
    let tabsContainer = self.option.closest(".elementor-repeater-fields-wrapper");
    if ( tabsContainer != null ) {
      let childIndex = Array.prototype.indexOf.call(tabsContainer.children, self.option.closest(".elementor-repeater-fields"));
      let parentType = "";

      for (let i in self.cache.sub_controls) {
        if ( tabsContainer.closest(".elementor-control").classList.contains("elementor-control-" + self.cache.sub_controls[i]) ) {
          parentType = self.cache.sub_controls[i];
        }
      }

      type = parentType + "." + childIndex + "." + self.type;
    }

    return type;
  }

  init() {
    this.cacheElements();
  }

  containsAny(source, target) {
    var result = source.filter(function (item) {
      return target.indexOf(item) > -1
    });
    return (result.length > 0);
  }
}

let twbAIOb;
jQuery(function() {
  twbAIOb = new TWBAI();
  twbAIOb.init();
});

jQuery(document).ready(function() {
  if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches &&
  twbb_admin_vars.twbb_ui_theme == 'auto' ||
  twbb_admin_vars.twbb_ui_theme == 'dark') {
    jQuery(".twbb-ai-popup-container").addClass("twbb-ai-popup-dark");
  }

  jQuery(document).on("click", ".twbb-ai-close, .twbb-ai-popup-layout", function(){
    twbAIOb.hide_ai_popup();
  });


  jQuery(document).on("click", ".twbb-ai-suggested-propmt", function(){
    let prompt = jQuery(this).text();
    jQuery(document).find(".twbb-ai-description-input").val(prompt).change();
  });

  jQuery(document).on("change paste keyup", ".twbb-ai-description-input", function(){
    if( jQuery(this).val() != '' ) {
      jQuery(".twbb-ai-propmts-empty-container .twbb-ai-suggested-propmts-content").hide();
    } else {
      jQuery(".twbb-ai-propmts-empty-container .twbb-ai-suggested-propmts-content").show();
    }
  });

  jQuery(document).on("click", ".twbb-ai-action-button", function(){
      let selectedText = jQuery(this).closest(".twbb-ai-suggested-propmts-container").find(".twbb-ai-text").val();
      if( selectedText == '' ) {
        let message = "Please fill out this field";
        jQuery(document).find(".twbb-ai-error-message").text(message).show();
        return;
      }

      twbb_send_request( jQuery(this), selectedText );
  });

  jQuery(document).on("click", ".twbb-ai-select-value", function() {
    jQuery(".twbb-ai-select-container").addClass("twbb-ai-select-closed");
    jQuery(".twbb-ai-select-options-container").hide();
    let parent = jQuery(this).closest(".twbb-ai-select-container");
    if( parent.hasClass("twbb-ai-select-closed") ) {
        parent.find(".twbb-ai-select-options-container").show();
        parent.removeClass("twbb-ai-select-closed");
    } else {
        parent.find(".twbb-ai-select-options-container").hide();
        parent.addClass("twbb-ai-select-closed");
    }
  });

  /* Close select if click on popup */
  jQuery(document).on("click", ".twbb-ai-popup-container", function(event ) {
    var target = jQuery( event.target );
    if( !target.is(".twbb-ai-select-container, .twbb-ai-select-value") ) {
      jQuery(".twbb-ai-select-options-container").hide();
      jQuery(".twbb-ai-select-container").addClass("twbb-ai-select-closed");
    }
  });
});


/** Show/hide loading
* @param show boolean
*/
function show_hide_loading( show ) {
  if( show ) {
      jQuery(".twbb-ai-loading").show();
      jQuery(".twbb-ai-popup-content").hide();
  } else {
      jQuery(".twbb-ai-loading").hide();
      jQuery(".twbb-ai-popup-content").show();
  }
}


function twbb_send_request( that, selectedText ) {
  jQuery(document).find(".twbb-ai-error-message").hide();
  let action = jQuery(that).data("action");
  let params = {};
  if( action == 'change_tone') {
      let tone = jQuery(that).data("value");
      params = {"text": selectedText, "tone": tone};
  } else if(action == 'translate_to') {
      let language = jQuery(that).data("value");
      params = {"text": selectedText, "language": language};
  } else {
      params = {"text": selectedText}
  }

  show_hide_loading(1);
  let ob = new RestRequest("builder/" + action, params, "POST", function ( success ) {
    let output = success['data']['output'];
    twbAIOb.show_ai_popup(output);
    show_hide_loading(0);
  }, function(err) {
      show_hide_loading(0);
  }, function(err) {
      show_hide_loading(0);
  });

  ob.twbb_send_rest_request();

}

function twbb_hide_ai_popup() {
  jQuery(".twbb-ai-popup-layout, .twbb-ai-popup-container, .twbb-ai-propmts-empty-container, .twbb-ai-text-prompts, .twbb-ai-headline-prompts, .twbb-ai-propmts-result-container").hide();
}