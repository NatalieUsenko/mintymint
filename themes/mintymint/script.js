jQuery(document).ready(function ($) {
    $('#companies_list').find('.section-list a').click(function (event) {
        event.preventDefault();
        $.ajax({
            url: '/wp-admin/admin-ajax.php',
            type: "POST",
            data: {
                action: 'my_ajax_action',
                term_id: $(this).data('id'),
                page_id: $(this).parents('.section-list').data('page'),
                search_val: $('#cs').val(),
            },

            success: function (response) {
                let json = JSON.parse(response);
                $('#companies_list .section_breadcrumbs').html(json.companies_breadcrumbs);
                $('#companies_list .section-items').html(json.companies_items);
            }
        });
    });

    $('#csearchsubmit').click(function (event) {
        event.preventDefault();
        if ($('#cs').val().length> 0){
            $.ajax({
                url: '/wp-admin/admin-ajax.php',
                type: "POST",
                data: {
                    action: 'search_ajax_action',
                    page_id: $('.section-list').data('page'),
                    search_val: $('#cs').val(),
                },

                success: function (response) {
                    let json = JSON.parse(response);
                    $('#companies_list .section_breadcrumbs').html(json.companies_breadcrumbs);
                    $('#companies_list .section-items').html(json.companies_items);
                }
            });
        }
    });


});

function page_nav(event){
    event.preventDefault();
    jQuery.ajax({
        url: '/wp-admin/admin-ajax.php',
        type: "POST",
        data: {
            action: 'page_ajax_action',
            term_id: jQuery(event.target.parentElement).parents('.page-pagination').data('term-id'),
            paged: jQuery(event.target.parentElement).data('paged'),
            search_val: jQuery('#cs').val(),
        },

        success: function (response) {
            console.log(response);
            let json = JSON.parse(response);
            jQuery('#companies_list .section-items').html(json.companies_items);
        }
    });
}