(function() {
    tinymce.create('tinymce.plugins.perfit', {
        /**
         * Initializes the plugin, this will be executed after the plugin has been created.
         * This call is done before the editor instance has finished it's initialization so use the onInit event
         * of the editor instance to intercept that event.
         *
         * @param {tinymce.Editor} ed Editor instance that the plugin is initialized in.
         * @param {string} url Absolute URL to where the plugin is located.
         */
        init : function(ed, url) {
            ed.addButton('perfit_optin', {
                title : 'Perfit Optin',
                cmd : 'optin',
                image : url + '/logoperfit.jpg'
            }); 

            ed.addCommand('optin', function() {

                ed.windowManager.open({
                    file : perfitConfig.url+'/tinymce_optin_list.php?', // file that contains HTML for our modal window
                    width : 320 + parseInt(ed.getLang('button.delta_width', 0)), // size of our window
                    height : 340 + parseInt(ed.getLang('button.delta_height', 0)), // size of our window
                    inline : 1
                }, {
                    optin : ''
                });

                // tb_show("Por favor seleccione el optin a insertar", "../wp-content/plugins/perfit/tinymce_optin_list.php?");
                // tinymce.DOM.setStyle(["TB_overlay", "TB_window", "TB_load"], "z-index", "999999")


                var number,
                    optin_list,
                    shortcode;

/*
                var number = prompt("Que optin quiere insertar?"),
                    optin_list,
                    shortcode;
*/

/*
                if (number !== null) {
                    number = parseInt(number);
                    if (number > 0) {
                        shortcode = '[perfit_optin ' + number + ']';
                        ed.execCommand('mceInsertContent', 0, shortcode);
                    }
                    else {
                        alert("El número es inválido");
                    }
                }
*/
            });

        },
 
        /**
         * Creates control instances based in the incomming name. This method is normally not
         * needed since the addButton method of the tinymce.Editor class is a more easy way of adding buttons
         * but you sometimes need to create more complex controls like listboxes, split buttons etc then this
         * method can be used to create those.
         *
         * @param {String} n Name of the control to create.
         * @param {tinymce.ControlManager} cm Control manager to use inorder to create new control.
         * @return {tinymce.ui.Control} New control instance or null if no control was created.
         */
        createControl : function(n, cm) {
            return null;
        },
 
        /**
         * Returns information about the plugin as a name/value array.
         * The current keys are longname, author, authorurl, infourl and version.
         *
         * @return {Object} Name/value array containing information about the plugin.
         */
        getInfo : function() {
            return {
                longname : 'Perfit Optin Buttons',
                author : 'Perfit Dev Team',
                authorurl : 'https://developers.myperfit.com/',
                infourl : 'https://developers.myperfit.com/',
                version : "0.1"
            };
        }
    });
 
    // Register plugin
    tinymce.PluginManager.add( 'perfit', tinymce.plugins.perfit );

    jQuery(function(){

        jQuery('#tinymce_optin_add').click(function(ev){
            ev.preventDefault();
            var number = jQuery('#tinymce_optin').val();
            console.log(number);
            shortcode = '[perfit_optin ' + number + ']';
            ed.execCommand('mceInsertContent', 0, shortcode);
            tb_remove();
        });
    });

})();
