/*
 * @category Cminds
 * @package  Cminds_Supplierfrontendproductuploader
 * @author   CreativeMinds Developers <info@cminds.com>
 */

function doAjaxCall(url, params, successFunc, failureFunc) {
    if (!successFunc) {
        successFunc = function(){};
    }
    if (!failureFunc) {
        failureFunc = function(){};
    }

    jQuery.ajax({
        url : url,
        data : params,
        success : successFunc,
        failure : failureFunc
    });
}
var Downloadable = Class.create();

Downloadable.prototype = {
    initialize : function(deleteLinkUrl, deleteSampleUrl, linkDeleteConfirmationText, sampleDeleteConfirmationText) {
        var el = this;
        this.deleteLinkUrl = deleteLinkUrl;
        this.deleteSampleUrl = deleteSampleUrl;
        this.linkDeleteConfirmationText = linkDeleteConfirmationText;
        this.sampleDeleteConfirmationText = sampleDeleteConfirmationText;

        jQuery(document).ready(function(){
            jQuery(".remove").click(function(e) {
                e.preventDefault();
                
                if (jQuery(this).data("type") === "sample") {
                    el.onDeleteSampleEventClickHook(e, this);
                } else {
                    el.onDeleteLinkEventClickHook(e, this);
                }
            });
            jQuery("input[type=text], input[type=file]").change(function(e) {
                var parent = jQuery(this).parent();
                if(parent[0].nodeName != "TD") {
                    parent = parent.parent().parent();
                }
                parent.find("input[type=radio]").attr("checked", "checked");
            });
        });
    },

     onDeleteLinkEventClickHook: function(e, el) {
        var confirmAlert = confirm(this.linkDeleteConfirmationText);

        if (confirmAlert) {
            var me = this;
            doAjaxCall(
                this.deleteLinkUrl,
                {
                    id: jQuery(el).data("id")
                },
                function(response) {
                    me.onAjaxSuccess(response, el);
                }
            )
        }
    },

    onDeleteSampleEventClickHook : function(e, el) {
        var confirmAlert = confirm(this.sampleDeleteConfirmationText);

        if (confirmAlert) {
            var me = this;
            doAjaxCall(
                this.deleteSampleUrl,
                {
                    id: jQuery(el).data("id")
                },
                function(response) {
                    me.onAjaxSuccess(response, el);
                }
            )
        }
    },

    onAjaxSuccess : function (response, element) {
        jQuery(element).parent().parent().remove();
    }
};
