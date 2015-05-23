(function () {
    tinymce.create("tinymce.plugins.SpyroPressShortcodes", {
        init: function (d, e) {
            d.addCommand("spyropressOpenDialog", function (a, c) {
                spyropressSelectedShortcodeType = c.identifier;
                jQuery.get(spyropress_admin_settings['dialog_url']+'?code='+c.identifier+'&title='+c.title, function (b) {
                    jQuery("#spyropress-dialog").remove();
                    jQuery("body").append(b);
                    jQuery("#spyropress-dialog").hide();
                    var f = jQuery(window).width(),
                        b = jQuery(window).height();
                    f = 720 < f ? 720 : f;
                    f -= 80;
                    b -= 115;
                    tb_show("Insert SpyroPress Shortcode", "#TB_inline?width=" + f + "&height=" + b + "&inlineId=spyropress-dialog");
                    jQuery("#spyropress-options h3:first").text("Customize the " + c.title + " Shortcode");
                });
            });
            d.onNodeChange.add(function (a, c) {
                c.setDisabled("spyropress_shortcodes_button", a.selection.getContent().length > 0)
            })
        },
        createControl: function (d, e) {
            if (d == "spyropress_shortcodes_button") {
                d = e.createMenuButton("spyropress_shortcodes_button", {
                    title: "Insert Shortcode",
                    image: spyropress_admin_settings['favicon_url'],
                    icons: false
                });
                var a = this;
                d.onRenderMenu.add(function (c, b) {                   
                    c = b.addMenu({
                        title: "Buttons"
                    });
                        a.addWithDialog(c, "Button", "button");
                        a.addWithDialog(c, "Link Button", "button_link");
                    c = b.addMenu({
                        title: "Typography"
                    });
                        a.addImmediate( c, "Lead Text", "[lead]content goes here[/lead]" );
                        a.addWithDialog( c, "Inverted Text", "typo_inverted" );
                        a.addWithDialog( c, "Alternative Font", "typo_alt_font" );
                        a.addWithDialog( c, "Badges","typo_badges" );
                        a.addWithDialog( c, "Labels","typo_labels" );                        
                    c = b.addMenu({
                        title: "Image"
                    });
                        a.addWithDialog( c, "Promo Image", "promo_image" );
                        a.addWithDialog( c, "Image", "img");
                    
                    b.addSeparator();
                    a.addWithDialog( b, "Alerts", "ui_alerts" );
                    a.addWithDialog( b, "Lightbox", "ui_lightbox" );
                    a.addImmediate( b, "Inline List", "[inline_list]<ul><li>content goes here.</li><li>content goes here.</li><li>content goes here.</li></ul>[/inline_list]");
                    a.addWithDialog( b, "Progress Bar", "ui_progress_bar");
                    a.addWithDialog( b, "Tables", "ui_tables");
                    a.addWithDialog( b, "Tooltip", "ui_tooltip");
                });
                return d
            }
            return null
        },
        addImmediate: function (d, e, a) {
            d.add({
                title: e,
                onclick: function () {
                    tinyMCE.activeEditor.execCommand("mceInsertContent", false, a)
                }
            })
        },
        addWithDialog: function (d, e, a) {
            d.add({
                title: e,
                onclick: function () {
                    tinyMCE.activeEditor.execCommand("spyropressOpenDialog", false, {
                        title: e,
                        identifier: a
                    })
                }
            })
        },
        getInfo: function () {
        }
    });
    
    tinymce.PluginManager.add("SpyroPressShortcodes", tinymce.plugins.SpyroPressShortcodes);
})();