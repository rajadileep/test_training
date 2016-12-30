jQuery(document).ready(function($) {
    
    $('body').on("click",".upme-profile-tab",function(){
         upfp_load_posts_list($(this),'post_tab',0);
    });
        
    $('body').on("click","#upfp_list_posts",function(){
         upfp_load_posts_list($(this),'post_list',0);
    });
    
    $('body').on("click",".upfp-post-delete",function(){
        var post_id = $(this).attr("data-post-id");
        upfp_delete_posts_list($(this),post_id);
    });
    
    $('body').on('click','#upfp_init_create_post',function (e)
    {
        $('#upfp-posts-create-form-panel').show();
        $('#upfp-posts-list-panel').hide();
        $('#upfp-posts-edit-form-panel').hide();
    });
    
    $('body').on('click','#upfp_list_posts',function (e)
    {
        $('#upfp-posts-create-form-panel').hide();
        $('#upfp-posts-edit-form-panel').hide();
        $('#upfp-posts-list-panel').show();
    });
    
    $('body').on('submit','#upfp-posts-create-form',function (e)
    {
         
        $('#upfp_new_post_ajax').show();
        $('#upfp-new-posts-message').hide();
        $('#upfp-new-posts-message').parent().removeClass('upfp-error').removeClass('upfp-success');
        
        var form = $('#upfp-posts-create-form');
        var formObj = $(this);
        var formURL = UPFPFront.AdminAjax+'?action=upfp_create_new_post';
        var formData = new FormData(this);
        
        e.preventDefault();
        
        tinymce.execCommand('mceRemoveEditor', true, 'upfp_post_content'); 
        var init = tinymce.extend( {}, tinyMCEPreInit.mceInit[ 'upfp_post_content' ] );
        try { tinymce.init( init ); } catch(e){console.log(e);}
        
        
        $.ajax({
            url: formURL,
            type: 'POST',
            data:  formData,
            mimeType:"multipart/form-data",
            contentType: false,
            cache: false,
            processData:false,
            success: function(data, textStatus, jqXHR)
            {
                var response = jQuery.parseJSON(data);
                if(response.status == 'success') {
                    
          
                    tinymce.execCommand('mceRemoveEditor', true, 'upfp_post_content'); 
                    

                    // init editor for newly appended div
                    var init = tinymce.extend( {}, tinyMCEPreInit.mceInit[ 'upfp_post_content' ] );
                    try { tinymce.init( init ); } catch(e){console.log(e);}
                    tinymce.get('upfp_post_content').setContent('');
                    
                    
                    $("#upfp_post_categories option[value='']").attr("selected",true); 
                    $("#upfp_post_categories").trigger("chosen:updated");
                        
                    $("#upfp_post_tags").val('');
                    $("#upfp_post_title").val('');                  
                    $('#upfp_post_featured').val('');  
                    
                    $('#upfp-new-posts-message').parent().addClass('upfp-success');
                    $('#upfp-new-posts-message').html(response.msg);
                    
                    
                }else{
                    $('#upfp-new-posts-message').parent().addClass('upfp-error');
                    $('#upfp-new-posts-message').html(response.msg);
                }

                $('#upfp-new-posts-message').show();
                $('#upfp_new_post_ajax').hide();

            },
            error: function(jqXHR, textStatus, errorThrown)
            {
                var obj = jQuery.parseJSON(data);

                console.log('error');
            }
        });
        
        
    });
    
    $('body').on('submit','#upfp-posts-edit-form',function (e)
    {
         
        $('#upfp_edit_post_update_ajax').show();
        $('#upfp-edit-posts-message').hide();
        $('#upfp-edit-posts-message').parent().removeClass('upfp-error').removeClass('upfp-success');
        
        var form = $('#upfp-posts-edit-form');
        var formObj = $(this);
        var formURL = UPFPFront.AdminAjax+'?action=upfp_edit_post';
        var formData = new FormData(this);
        
        e.preventDefault();
        
        tinymce.execCommand('mceRemoveEditor', true, 'upfp_edit_post_content'); 
        var init = tinymce.extend( {}, tinyMCEPreInit.mceInit[ 'upfp_edit_post_content' ] );
        try { tinymce.init( init ); } catch(e){console.log(e);}
        
        $.ajax({
            url: formURL,
            type: 'POST',
            data:  formData,
            mimeType:"multipart/form-data",
            contentType: false,
            cache: false,
            processData:false,
            success: function(data, textStatus, jqXHR)
            {
                var response = jQuery.parseJSON(data);
                if(response.status == 'success') {
                    
                
                    tinymce.execCommand('mceRemoveEditor', true, 'upfp_edit_post_content'); 
//               
                    // init editor for newly appended div
                    var init = tinymce.extend( {}, tinyMCEPreInit.mceInit[ 'upfp_edit_post_content' ] );
                    try { tinymce.init( init ); } catch(e){console.log(e);}
                    tinymce.get('upfp_edit_post_content').setContent(response.content);
                    
                    $('#upfp-edit-posts-message').parent().addClass('upfp-success');
                    $('#upfp-edit-posts-message').html(response.msg);
                }else{
                    $('#upfp-edit-posts-message').parent().addClass('upfp-error');
                    $('#upfp-edit-posts-message').html(response.msg);
                }

                $('#upfp-edit-posts-message').show();
                $('#upfp_edit_post_update_ajax').hide();
            },
            error: function(jqXHR, textStatus, errorThrown)
            {
                var obj = jQuery.parseJSON(data);

                console.log('error');
            }
        });
        
        
    });
    
    $('body').on('click','.upfp-post-edit',function (e)
    {
        e.preventDefault();
        
        tinymce.execCommand('mceRemoveEditor', true, 'upfp_edit_post_content'); 
        var init = tinymce.extend( {}, tinyMCEPreInit.mceInit[ 'upfp_edit_post_content' ] );
        try { tinymce.init( init ); } catch(e){console.log(e);}
        
        var post_id = $(this).attr('data-post-id');
        jQuery.post(
                UPFPFront.AdminAjax,
                {
                    'action': 'upfp_get_post_edit_data',
                    'post_id' : post_id,
                },
                function(response){
                    if(response.status == 'success'){
                        $('#upfp-posts-edit-form-panel #upfp_edit_post_title').val(response.title);
                        
                        tinymce.execCommand('mceRemoveEditor', true, 'upfp_edit_post_content'); 
                        var init = tinymce.extend( {}, tinyMCEPreInit.mceInit[ 'upfp_edit_post_content' ] );
                        try { tinymce.init( init ); } catch(e){console.log(e);}
                        tinymce.get('upfp_edit_post_content').setContent(response.content);

                        $.each(response.categories, function(i,e){
//                            console.log(i);
                            
                            if($("#upfp_edit_post_categories option[value='" + e.id + "']")){
                                console.log(e.id);
                            }
                            $("#upfp_edit_post_categories option[value='" + e.id + "']").attr("selected",true); 
                        });
                        $("#upfp_edit_post_categories").trigger("chosen:updated");
                        
                        $("#upfp_edit_post_tags").val(response.tags);
                        $('#upfp_edit_post_id').val(response.id);
                        
                        if(response.featured_image != ''){
                            $('#upfp_edit_post_featured_image').attr('src', response.featured_image );
                            $('#upfp_edit_post_featured_image_panel').show();
                        }                        
                        
                        $('#upfp-posts-create-form-panel').hide();
                        $('#upfp-posts-list-panel').hide();
                        $('#upfp-posts-edit-form-panel').show();
                        
                    }
                    $('#upfp-posts-list-panel').html(response.msg);

                },"json");
    });
    
    $('body').on('click','#upfp_edit_post_featured_image_delete',function (e)
    {
        e.preventDefault();
        
        $('#upfp_edit_post_featured_ajax_delete').show();
        
        var post_id = $('#upfp_edit_post_id').val();
        jQuery.post(
                UPFPFront.AdminAjax,
                {
                    'action': 'upfp_delete_post_featured',
                    'post_id' : post_id,
                    'upfp_new_post_nonce_field' : $('#upfp_new_post_nonce_field').val()
                },
                function(response){
                    if(response.status == 'success'){
                        $('#upfp_edit_post_featured_image_panel').hide();
                        $('#upfp_edit_post_featured_image').attr('src','');
                        $('#upfp_edit_post_featured_ajax_delete').hide();
                    }

                },"json");
    });

    $('body').on('click','#upfp_more_posts',function (e)
    {
        var offset = jQuery('#upfp_more_posts').attr('data-offset');
        upfp_load_more_posts($(this),'post_list',offset);
    });

});

function upfp_load_posts_list(posts_panel,type,offset){
    
    if(posts_panel.attr('data-tab-id') == 'upme-posts-panel' || type == 'post_list'){
        
        jQuery('#upfp-posts-list-panel').html('<div id="upfp-delete-post-ajax-loader"><img src="' + UPFPFront.UPFP_PLUGIN_URL + 'img/loading.gif" id="upfp_delete_post_ajax" /></div>');
        jQuery('#upfp-delete-post-ajax-loader').show();   
        
        jQuery.post(
            UPFPFront.AdminAjax,
            {
                'action': 'upfp_get_posts_list',
                'upfp_post_offset' : offset,
            },
            function(response){

                jQuery('#upfp-posts-list-panel').html(response.msg);
                if(response.load_more == '1'){
                    jQuery('#upfp_more_posts').show();
                    jQuery('#upfp_more_posts').attr('data-offset',response.offset);
                }else{
                    jQuery('#upfp_more_posts').hide();
                }
            },"json");
    } 
}

function upfp_load_more_posts(posts_panel,type,offset){
    
    if(posts_panel.attr('data-tab-id') == 'upme-posts-panel' || type == 'post_list'){
        jQuery('#upfp-delete-post-ajax-loader').show();
        jQuery.post(
            UPFPFront.AdminAjax,
            {
                'action': 'upfp_get_posts_list',
                'upfp_post_offset' : offset,
                'upfp_more_posts' : '1',
            },
            function(response){
                jQuery('#upfp-delete-post-ajax-loader').hide();
                jQuery('#upfp_more_posts').parent().before(response.msg);
                if(response.load_more == '1'){
                    jQuery('#upfp_more_posts').show();
                    jQuery('#upfp_more_posts').attr('data-offset',response.offset);
                }else{
                    jQuery('#upfp_more_posts').hide();
                }
                
            },"json");
    } 
}

function upfp_delete_posts_list(delete_button,post_id){
    
    if(!upfp_confirm_post_delete()){
        return;
    }
    
    jQuery('#upfp-post-list-msg').removeClass('upfp-error').removeClass('upfp-success').hide();
    
    jQuery.post(
            UPFPFront.AdminAjax,
            {
                'action': 'upfp_delete_post',
                'upfp_post_id' : post_id,
                'upfp_delete_post_nonce_field' : jQuery('#upfp_delete_post_nonce_field').val(),
            },
            function(response){
                if(response.status == 'success'){
                    delete_button.parent().parent().remove();
                    jQuery('#upfp-post-list-msg').addClass('upfp-success');
                }else{
                    jQuery('#upfp-post-list-msg').addClass('upfp-error');
                }
                jQuery('#upfp-post-list-msg').html(response.msg);
                jQuery('#upfp-post-list-msg').show();
            },"json");
}

function upfp_confirm_post_delete(){
    var confirmed = confirm(UPFPFront.confirmPostDelete);
    return confirmed;
}

