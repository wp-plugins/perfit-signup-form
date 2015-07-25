
optinPreview = {

    optinData: {
        form: {
        },
        fields: {
        },
        interests: {
        },
        confirmation: {
        }
    },

    selectFields: {
        "4": '<div class="p-field"><label><span>{{field}}</span></label><select name="fields[4]" class="p-select"><option value="">Sin especificar</option><option value="M">Masculino</option><option value="F">Femenino</option></select></div>',
        "5": '<div class="p-field"><label><span>{{field}}</span></label><select name="fields[5]" class="p-select"><option value="">Sin especificar</option><option value="es">Español</option><option value="en">Inglés</option><option value="de">Alemán</option><option value="fr">Francés</option><option value="it">Italiano</option><option value="pt">Portugués</option></select></div>',
        "7": '<div class="p-field"><label><span>{{field}}</span></label><div><label><input checked="checked" value="HTML" name="fields[7]" type="radio"> HTML</label></div><div><label><input value="TXT" name="fields[7]" type="radio"> TXT</label></div></div>'
    },

    init: function () {

        // Form render by default
        optinPreview.renderForm();

        // Capture events
        optinPreview.bindEvents();

        jQuery('.sortable').sortable({
            handle: '.handle',
            stop: function( event, ui ) {
                optinPreview.renderForm();
            }
        });

        this.hideDisplayName();
    },

    reset: function () {
        this.optinData.form = {};
        this.optinData.fields = {};
        this.optinData.interests = {};
    },

    getData: function () {

        this.reset();

        var thisOptin = this;

        // Obtain general form params
        jQuery('[data-optin]').each(function(index, item){
            var optinParams = jQuery(this).data('params')? jQuery(this).data('params') : {};
            thisOptin.optinData.form[jQuery(this).data('optin')] = {
                name: jQuery(this).data('optin'),
                value: jQuery(this).val(),
                params: optinParams
            };
        });

        // Obtain fields
        var autoIndex = 0;
        jQuery('[data-field]:checked').each(function(index, item){
            var optinParams = jQuery(this).data('params')? jQuery(this).data('params') : {};
            thisOptin.optinData.fields[jQuery(this).val()] = {
                name: jQuery(this).data('field'),
                value: jQuery(this).val(),
                params: optinParams,
                position: autoIndex++
            };
        });

        // Obtain interests
        var autoIndex = 0;
        jQuery('[data-interest]:checked').each(function(index, item){
            var optinParams = jQuery(this).data('params')? jQuery(this).data('params') : {};
            thisOptin.optinData.interests[jQuery(this).val()] = {
                name: jQuery(this).data('interest'),
                value: jQuery(this).val(),
                params: optinParams,
                position: autoIndex++
            };
        });

        // Obtain general form params
        jQuery('[data-optin]').each(function(index, item){
            var optinParams = jQuery(this).data('params')? jQuery(this).data('params') : {};
            thisOptin.optinData.confirmation[jQuery(this).data('optin')] = {
                name: jQuery(this).data('optin'),
                value: jQuery(this).val(),
                parentarams: optinParams
            };
        });

        return this.optinData;
    },

    getConfirmationTemplate: function () {
        return '<div class="email-preview">\
<dl class="dl-horizontal"><dt>De</dt><dd>{{fromName}} &lt;{{fromAddress}}&gt;</dd>\
<dt>Asunto</dt><dd>{{subject}}</dd></dl><div class="email-preview-wrapper">\
<div class="email-preview-main"><p class="email-preview-title">{{confirmationTitle}}</p>\
<dl class="dl-horizontal"><dt>Email:</dt><dd>usuario@ejemplo.com</dd>\
<dt>Nombre:</dt><dd>Nombre</dd><dt>Apellido:</dt><dd>Apellido</dd></dl>\
<p class="email-preview-header">{{header}}</p>\
<p><a class="email-preview-button">{{linkText}}</a></p>\
<p class="email-preview-footer">{{mailFooter}}</p></div></div></div>';
    },

    getInterestTemplate: function () {
        return '<div class="p-interest"><label><input name="interests[2]" type="checkbox" >{{interest}}</label></div>';
    },

    getFieldTemplate: function () {
        return '<div class="p-field"><label><span>{{field}}</span></label><input name="fields[1]" placeholder="{{field}}" type="text"></div>';
    },

    getTemplate: function () {
        return jQuery('.optin-template').html();
    },

    renderForm: function() {

        var optinPreview = this;

        // Get form data
        var optinData = this.getData();

        // Get field template
        var fieldTemplate = this.getFieldTemplate();

        // Get interest template
        var interestTemplate = this.getInterestTemplate();

        // Get full template
        var template = this.getTemplate();

        if (!template)
            return;

        // Apply design replacements
        jQuery.each(optinData.form, function(index, item) {
            template = template.replace('{{'+item.name+'}}', item.value);
        });

        // Apply field replacements
        var fieldContent = '';
        var arrOrder = {};
        jQuery.each(optinData.fields, function(index, item) {
            arrOrder[item.position] = item;
        });
        jQuery.each(arrOrder, function(index, item) {
            var name = (item.params.displayName != '')? item.params.displayName : item.name
            if (optinPreview.selectFields[item.value] != undefined) {
                fieldContent += optinPreview.selectFields[item.value].replace(/\{\{field\}\}/gi, name);
            }
            else {
                fieldContent += fieldTemplate.replace(/\{\{field\}\}/gi, name);
            }
        });
        template = template.replace('{{fields}}', fieldContent);

        // Apply interest replacements
        var interestContent = '';
        var arrOrder = {};
        jQuery.each(optinData.interests, function(index, item) {
            arrOrder[item.position] = item;
        });
        jQuery.each(arrOrder, function(index, item) {
            var name = (item.params.displayName != '')? item.params.displayName : item.name;
            newInterest = interestTemplate.replace(/\{\{interest\}\}/gi, name);
            if (item.params.default == 1) {
                newInterest = newInterest.replace(/type="checkbox"/gi, 'type="checkbox" checked="checked"');
            }
            interestContent += newInterest;
        });
        template = template.replace('{{interests}}', interestContent);

        jQuery('[data-preview=form]').html(template);
        // console.log(template);

        // For checked checkboxes show edit and displayName, for the rest hide them
        jQuery('[data-modal]').hide();
        jQuery('[data-role=display-name]').hide();
        jQuery('input:checked').each(function(ev){
            var parent = jQuery(this).parent().parent();
            if (jQuery(this).is(':checked') && parent.hasClass('checkbox')) {
                parent.find('[data-modal]').show();
                parent.find('[data-role=display-name]').show();
            }
        });

        // Get confirmation template
        var confirmationTemplate = this.getConfirmationTemplate();

        // Render confirmation form
        jQuery.each(optinData.confirmation, function(index, item) {
            confirmationTemplate = confirmationTemplate.replace('{{'+item.name+'}}', item.value);
        });

        jQuery('[data-preview=email]').html(confirmationTemplate);

    },

    bindEvents: function () {

        var optinPreview = this;

        this.enableRedirect();

        // Form submit validations
        jQuery('form.save').submit(function(ev){

            ev.preventDefault();

            jQuery('#gral-error').css({'display': 'none'});
            jQuery('.alert-success').css({'display': 'none'});
            jQuery('.input-error').removeClass('input-error');
            jQuery('.input-error-message').remove();

            var action = jQuery(this).attr('action');
            var data = jQuery(this).serialize();
            jQuery.post(action, data, function(result){
                if (result == 'true') {
//                    top.location.href=top.location.href+'?success=1';
                    var location = document.location.href.replace('?success=1', '');
                    document.location=location+'?success=1';
                    return true;
                }

                jQuery('.gral-error-msg').html(result.userMessage);
                jQuery('#gral-error').css({'display': 'block'});

                jQuery.each(result.validationErrors, function(index, item){
                    index = index.replace(/\./g, '-');
                    var newEl = jQuery('<div id="error-input-'+index+'" class="alert-danger input-error-message">'+item+'</div>');
                    if (jQuery('#input-'+index).length > 0) {
                        jQuery('#input-'+index).addClass('input-error');
                        newEl.insertAfter(jQuery('#input-'+index));
                    }
                    if (index == 'form-fields') {
                        newEl.insertAfter(jQuery('#fields-label'));
                    }
                    if (index == 'lists') {
                        newEl.insertAfter(jQuery('#lists-label'));
                    }
                });

            });

            return false;
        });

        // Checkbox changes
        jQuery('[data-update=change]').change(function(ev){

            if (jQuery(this).hasClass('fixed-field')) {
                jQuery(this).attr('checked', 'checked');
                return true;
            }
            optinPreview.renderForm();
        });

        // Input.keyUp
        jQuery('[data-update=keyUp]').keyup(function(ev){
            optinPreview.renderForm();
        });

        jQuery('.autoresize').change(function(ev){
            var width = jQuery(this).val().length;
            jQuery(this).css({'width': (((width)*6.5)-(width/3)+5)+'pt'});
        });
        jQuery('.autoresize').trigger('change');

        jQuery('[data-modal]').click(function(ev) {

            ev.preventDefault();

            jQuery(jQuery(this).data('target')).modal('show');
        })

        jQuery('[data-modal]').click(function(ev) {

            var optinData = optinPreview.getData();
            var keyName = jQuery(this).data('target');
            var varName = jQuery(this).data('var');
            var params = jQuery(this).data('params');
            var id = jQuery(this).data('id');

            ev.preventDefault();

            // Hide required checkbox for email field 
            jQuery('.field-required').show()
            if (id == 3) {
                jQuery('.field-required').hide()
            }

            // Populate modal data
            jQuery(jQuery(this).data('target')+' [data-name]').each(function(key, item){
                var inputVal = optinData[varName][id]['params'][jQuery(item).data('name')];

                if (jQuery(keyName+' [data-name="'+jQuery(item).data('name')+'"]').val() != 1) {
                    jQuery(keyName+' [data-name="'+jQuery(item).data('name')+'"]').val(inputVal);
                }

                jQuery(keyName+' [data-key]').val(id);

                if (optinData[varName][id]['params']['required'] == 1) {
                    jQuery(keyName+' [data-name="required"]').prop("checked", true);
                    console.log('si');
                }
                else {
                    jQuery(keyName+' [data-name="required"]').prop("checked", false);
                    console.log('no');
                }

            });
// console.log(jQuery(this).data('var'));

            jQuery(jQuery(this).data('target')).modal('show');


            jQuery(jQuery(this).data('target')).on('shown.bs.modal', function(ev){

                jQuery('shown.bs.modal').unbind(ev);

                jQuery('.modal.in form input[type="text"]:first').select().focus();

                jQuery('.modal.in form').submit(function(ev) {

                    ev.preventDefault();

                    jQuery('.alert').hide();

                    var id = jQuery(keyName+' [data-key]').val();
                    var varName = jQuery(keyName+' [data-varName]').val();

                    jQuery('.modal.in form [data-param]').each(function(key, item){
                        optinData[varName][id]['params'][jQuery(item).data('name')] = jQuery(item).val();

                        var parentEl = jQuery('#'+varName+' [data-model='+id+'] input[type=checkbox]');
                        var params = parentEl.data('params') || {};
                        params[jQuery(item).data('name')] = jQuery(item).val();

                        parentEl.data('params', params);
                    });

                    if (optinData[varName][id]['params']['required'])
                        optinData[varName][id]['params']['required'] = (jQuery('.modal.in form [data-name=required]').prop("checked"))? 1 : 0;

                    // Updates input params span
                    jQuery('#'+varName+' [data-model='+id+'] input[type="checkbox"]').data('params', optinData[varName][id]['params']);

                    // Updates html span
                    jQuery('#'+varName+' [data-model='+id+'] span.text-muted').html('('+optinData[varName][id]['params']['displayName']+')');
                    jQuery('#'+varName+' [data-model='+id+'] [data-role=display-name]').val(optinData[varName][id]['params']['displayName']);
                    jQuery('.modal.in').modal('hide');
                    jQuery('.autoresize').trigger('change');

                    if (jQuery('#'+varName+' [data-model='+id+'] [data-role=required]'))
                        jQuery('#'+varName+' [data-model='+id+'] [data-role=required]').val(optinData[varName][id]['params']['required']? 'true' : 'false');

                    jQuery('.modal.in form').unbind(ev);
                    optinPreview.hideDisplayName();
                    optinPreview.renderForm();
                });
            });

        })

    },

    enableRedirect: function () {
        jQuery('input[type=radio][name=redirect-bool]').change(function() {
            if (jQuery(this).val() == 1) 
                jQuery('#input-form-redirect').prop('disabled', false);
            else
                jQuery('#input-form-redirect').prop('disabled', true);
        });
    },

    hideDisplayName: function () {

        var fields = jQuery('#fields li');

        if (!fields)
            return;

        fields.each(function(index, item){
            var fieldName = jQuery(this).find('input[type="checkbox"]').data('field').trim();
            var displayName = jQuery(this).find('[data-role=display-name]').val().trim();

            if (displayName == '') {
                displayName = fieldName;
                jQuery(this).find('span.text-muted').val('('+fieldName+')');
                jQuery(this).find('[data-role=display-name]').val(fieldName);
            }

            if (fieldName.toLowerCase() == displayName.toLowerCase())
                jQuery(this).find('span.text-muted').hide();
            else
                jQuery(this).find('span.text-muted').show();

        });
    },

    test: function () {
        this.getData();
    }

}

jQuery(document).ready(function(ev){

    jQuery('[data-confirm]').click(function(ev){
        return confirm(jQuery(this).data('confirm'));
    });

    optinPreview.init();

    // Filter results
    jQuery('[data-filter]').keyup(function(ev){

        var target = jQuery(this).data('filter');
        var val = jQuery(this).val();

        jQuery(target).find('div.checkbox').each(function(index, item){
            var el = jQuery(this);
            var label = jQuery(this).find('span.list-name').text();
            (label.indexOf(val) > -1)? el.show() : el.hide();
        });

    });

    if (jQuery('#input-form-redirect')) {
        if (jQuery('#input-form-redirect').val() == '') {
            jQuery('#redirect-bool-1').prop('checked', false);
            jQuery('#redirect-bool-0').prop('checked', true).trigger('change');
        }
        else {
            jQuery('#redirect-bool-0').prop('checked', false);
            jQuery('#redirect-bool-1').prop('checked', true).trigger('change');
        }
    }

});



