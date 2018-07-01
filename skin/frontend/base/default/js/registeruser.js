    //< ![CDATA[
        var customForm = new VarienForm('registerForm');

        function func() {
            jQuery(".select2_categories").select2('close');
            var inst = jQuery('[data-remodal-id=modal]').remodal();
            inst.open();
        }
    
        jQuery(function ($) {
    
            $('.select2_categories').select2({
                placeholder: "Select categories",
                allowClear: true,
                formatNoMatches: function(term) {
                    return "<a class='addCategory' onClick='return func()'>Suggest this Category!</a> ";
                }
            });
            
            $('.remodal').on('click', '.remodal-confirm', function() {
                new_categories = $('.select2_addcategory').select2("val");
                if(new_categories.length > 0) {
                    $('.addcategories').val(new_categories.toString());
                    $('.select2_addcategory').select2('data', []);
                    swal({
                        title: "Thanks!",
                        text: "You suggested new category.",
                        type: "success",
                        timer: 2000 ,
                        inputPlaceholder: 'I agree to <a href="#blahblahMore"></a>'
                    });
                }
            });
    
            $(".select2_addcategory").select2({
                tags: []
            });
            $("input[name=check-all-categories]").change(function() {
                if($(this).is(':checked')) {
                    array = [];
                    $('input[name="allcategories[]"]').each(function() {
                        array.push($(this).val());
                    });
                    $('.select2_categories').select2('val', array);
                } else {
                    $('.select2_categories').val('').trigger("change");
                }
            });
        });     
        //]]>