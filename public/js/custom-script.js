
$(document).on( 'initEditor', function ( e, inst ) {
    //inst.on( 'displayOrder', function  ( e, mode, action )  {
    //    console.log( 'displayOrder' );
        //$('.DTE_Form_Buttons .btn').addClass('waves-effect waves-light');
    //});
    inst.on( 'open', function ( e, mode, action ) {
        $('.DTE_Form_Buttons .btn').addClass('el-button el-button--info');
        $('.DTE_Header').addClass('block-header block-header-default').removeClass('modal-header');
        $('.DTE_Header_Content').addClass('block-title');
        $('.DTE_Header .btn-close').addClass('btn-block-option').removeClass('btn-close').append('<i class="fa fa-fw fa-times"></i>');
        $('.DTE_Body').addClass('block-content fs-sm').removeClass('modal-body');
        $('.DTE_Footer').addClass('block-content block-content-full text-end bg-body').removeClass('modal-footer');
        $('.modal-dialog').addClass('modal-header-purple');
    })
});
/*
(function() {
    var Editor = $.fn.dataTable.Editor;
    Editor.display.modal = $.extend(true, {}, Editor.models.displayController, {
        init: function(editor) {
            return Editor.display.modal;
        },

        open: function(editor, append, callback) {
            Editor.display.modal.close(editor);

            $(append).find('.DTE_Header').hide();
            $(append).find('.DTE_Footer').hide();
            $(append).find('.DTE_Body').css('padding', 0);
            createModal(
                editor.title(),
                append,
                null,
                null,
                function(e) {
                    editor.submit();
                    $('.modal').modal('close');
                },
                null
            )

            if (callback) {
                callback();
            }
        },

        close: function(editor, callback) {

            $('.modal').modal('close');

            if (callback) {
                callback();
            }
        }
    });
})();
*/

function setPageTitle(title, subtitle){
    $("#page_title").text(title);
    if (subtitle) $("#page_subtitle").text(subtitle);
}

function addBreadcrumbItem(title, url){
    let new_breadcrumb_item = $('<li></li>').addClass('breadcrumb-item');
    if (url) {
        let new_anchor = $('<a></a>').addClass('link-fx');
        new_anchor.text(title);
        new_anchor.attr('href', url);
        new_anchor.appendTo(new_breadcrumb_item);
    }else{
        new_breadcrumb_item.text(title);
    }
    new_breadcrumb_item.appendTo('ol.breadcrumb');
}

function setMenu(href){
    // $(".content-side .active").removeClass("active");
    const pathname = document.location.pathname;
    // const pathname1 = pathname.split('/')[1];
    if (pathname.includes('/home')) {
        $('#nav-dashboard a.menu-link').addClass("active");
    } else if (pathname.includes('/client/1-1') || pathname.includes('/meeting')) {
        $('#nav-one-on-one a.menu-link').addClass("active");
    } else if (pathname.includes('/client/deal')) {
        $('#nav-deals a.menu-link').addClass("active");
    } else if (pathname.includes('user/') || pathname.includes('/client/pipeline') || pathname.includes('/organization/') || pathname.includes('/client/member')) {
        $('#nav-settings a.menu-link').addClass("active");
    } else if (pathname.includes('admin/organization')) {
        $('#nav-organizations a.menu-link').addClass("active");
    } else if (pathname.includes('admin/user')) {
        $('#nav-users a.menu-link').addClass("active");
    } else if (pathname.includes('admin/translation')) {
        $('#nav-translations a.menu-link').addClass("active");
    }
    $('.content-side a.nav-main-link[href="'+href+'"]').parents('ul.nav-main-submenu').parent('li.nav-main-item').addClass("open");
}
setMenu(document.location.href);

function createModal(title, message, page, classes, onOpen, onOk, onClose){
    let div = document.createElement('div');
    div.className = 'modal';
    div.id = 'modal';
    div.setAttribute('tabindex', '-1');
    div.setAttribute('role', 'dialog');

    let dialog = document.createElement('div');
    dialog.className = 'modal-dialog modal-header-purple '+classes;
    let content = document.createElement('div');
    content.className = 'modal-content';
    let block = document.createElement('div');
    block.className = "block block-rounded block-transparent mb-0";
    content.appendChild(block);
    dialog.appendChild(content);
    div.appendChild(dialog);

    let header = document.createElement('div');
    header.className = "block-header block-header-default";
    let header_title = document.createElement('h3');
    header_title.className = "block-title";
    header_title.appendChild( document.createTextNode(title) );
    header.appendChild(header_title);
    let header_options = document.createElement('div');
    header_options.className = "block-options";
    let header_close_btn = document.createElement('button');
    header_close_btn.className = "btn-block-option";
    header_close_btn.type ="button"
    header_close_btn.setAttribute('data-bs-dismiss', 'modal');
    header_close_btn.innerHTML = '<i class="fa fa-fw fa-times"></i>'
    header_options.appendChild(header_close_btn);
    header.appendChild(header_options);
    block.appendChild(header);

    let contents = document.createElement('div');
    contents.className= "block-content fs-sm"
    if (message === null) message = '';
    contents.innerHTML = '<p>'+message+'</p>';
    if ((message)&&(typeof message === 'object')) {
        contents = message;
    }
    block.appendChild(contents);

    let footer = document.createElement('div');
    footer.className = 'block-content block-content-full text-end bg-body';

    let close = document.createElement('button');
    close.className = 'el-button outlined-button me-2';
    close.setAttribute('data-bs-dismiss', 'modal');
    close.appendChild( document.createTextNode('Close') );
    footer.appendChild(close);

    if (onOk){
        var ok = document.createElement('button');
        ok.className = 'el-button el-button--info';
        //ok.setAttribute('data-bs-dismiss', 'modal');
        ok.appendChild( document.createTextNode('Ok') );
        //ok.onclick = onOk;
        footer.appendChild(ok);
    }

    block.appendChild(footer);

    div.addEventListener('shown.bs.modal', event => {
        //alert("Ready");
        //console.log(event);
        if (page) {
            $(contents).empty();
            $(contents).load(page, function () {
                if (onOpen) onOpen(event);
            });
        }
    });
    div.addEventListener('hidden.bs.modal', event => {
        $('#modal').modal('dispose').remove();//$('#modal').remove();
        if (onClose) onClose(event);
    });


    $('#modal').modal('dispose').remove();
    //$('#modal').remove();
    document.body.appendChild(div);
    let myModal = bootstrap.Modal.getOrCreateInstance('#modal', {
        backdrop: true
    });
    if (onOk) ok.onclick = function() { onOk(myModal) };
    myModal.show();
}

function createModalOverModal(title, message, page, classes, onOpen, onOk, onClose, zIndex, margin, closeBtnText, okBtnText){
    let div = document.createElement('div');
    div.className = 'modal';
    div.style.cssText = '--bs-modal-zindex: ' + zIndex + '; --bs-modal-margin: ' + margin;
    div.id = 'modal'+zIndex;
    div.setAttribute('tabindex', '-1');
    div.setAttribute('role', 'dialog');

    let dialog = document.createElement('div');
    dialog.className = 'modal-dialog modal-header-purple '+classes;
    let content = document.createElement('div');
    content.className = 'modal-content';
    let block = document.createElement('div');
    block.className = "block block-rounded block-transparent mb-0";
    content.appendChild(block);
    dialog.appendChild(content);
    div.appendChild(dialog);

    let header = document.createElement('div');
    header.className = "block-header block-header-default";
    let header_title = document.createElement('h3');
    header_title.className = "block-title";
    header_title.appendChild( document.createTextNode(title) );
    header.appendChild(header_title);
    let header_options = document.createElement('div');
    header_options.className = "block-options";
    let header_close_btn = document.createElement('button');
    header_close_btn.className = "btn-block-option";
    header_close_btn.type ="button"
    header_close_btn.setAttribute('data-bs-dismiss', 'modal');
    header_close_btn.innerHTML = '<i class="fa fa-fw fa-times"></i>'
    header_options.appendChild(header_close_btn);
    header.appendChild(header_options);
    block.appendChild(header);

    let contents = document.createElement('div');
    contents.className= "block-content fs-sm"
    if (message === null) message = '';
    contents.innerHTML = '<p>'+message+'</p>';
    if ((message)&&(typeof message === 'object')) {
        contents = message;
    }
    block.appendChild(contents);

    let footer = document.createElement('div');
    footer.className = 'block-content block-content-full text-end bg-body';

    let close = document.createElement('button');
    close.className = 'el-button outlined-button me-2';
    close.setAttribute('data-bs-dismiss', 'modal');
    if(!closeBtnText) closeBtnText = 'Close';
    close.appendChild( document.createTextNode(closeBtnText) );
    footer.appendChild(close);

    if (onOk){
        var ok = document.createElement('button');
        ok.className = 'el-button el-button--info';
        //ok.setAttribute('data-bs-dismiss', 'modal');
        if(!okBtnText) okBtnText = 'Ok';
        ok.appendChild( document.createTextNode(okBtnText) );
        //ok.onclick = onOk;
        footer.appendChild(ok);
    }

    block.appendChild(footer);

    div.addEventListener('shown.bs.modal', event => {
        //alert("Ready");
        //console.log(event);
        if (page) {
            $(contents).empty();
            $(contents).load(page, function () {
                if (onOpen) onOpen(event);
            });
        }
    });
    div.addEventListener('hidden.bs.modal', event => {
        $('#modal'+zIndex).modal('dispose').remove();//$('#modal').remove();
        if (onClose) onClose(event);
    });


    $('#modal'+zIndex).modal('dispose').remove();
    //$('#modal').remove();
    document.body.appendChild(div);
    let myModal = bootstrap.Modal.getOrCreateInstance('#modal'+zIndex, {
        backdrop: true,
    });
    if (onOk) ok.onclick = function() { onOk(myModal) };
    myModal.show();
    if ($('.modal-backdrop.show').length > 1) {
        $('.modal-backdrop.show').last().css('z-index', Number(zIndex)-1);
    }
}

function submitAjaxForm(url, form_data, onsuccess) {
    $.ajax({
        url: url,
        method: "POST",
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        dataType: 'json',
        data: form_data,
        processData: false,
        contentType: false,
        success: function(result){
            if (onsuccess) onsuccess(result);
        },
        error: function(xhr, status, error){
            console.error('AJAX Error:', xhr, status, error);
            console.error('Response:', xhr.responseJSON || xhr.responseText);
            
            let errorMessage = 'An error occurred';
            let debugInfo = '';
            
            // Try to parse JSON error response
            if (xhr.responseJSON) {
                errorMessage = xhr.responseJSON.message || xhr.responseJSON.error || errorMessage;
                
                // Handle validation errors
                if (xhr.responseJSON.errors) {
                    let validationErrors = [];
                    $.each(xhr.responseJSON.errors, function(field, messages) {
                        validationErrors.push(field + ': ' + messages.join(', '));
                    });
                    errorMessage = validationErrors.join('\n');
                }
                
                // Include debug info if available
                if (xhr.responseJSON.debug) {
                    debugInfo = '\n\nDebug Info:\n' + JSON.stringify(xhr.responseJSON.debug, null, 2);
                }
            } else if (xhr.responseText) {
                try {
                    let parsed = JSON.parse(xhr.responseText);
                    errorMessage = parsed.message || parsed.error || errorMessage;
                    if (parsed.debug) {
                        debugInfo = '\n\nDebug Info:\n' + JSON.stringify(parsed.debug, null, 2);
                    }
                } catch (e) {
                    errorMessage = xhr.responseText || errorMessage;
                }
            }
            
            // Show error to user
            alert('Error: ' + errorMessage + debugInfo);
        },
        complete: function(jqXHR, textStatus){ }
    });
}

function submitAjaxFormWithValidation($form, onsuccess) {
    //if (!$form.valid) return false;
    if (!$form.get(0).checkValidity()) {
        $form.addClass('was-validated');
        return false;
    }
    let form_data = new FormData($form[0]);
    let action = $form.attr('action');
    submitAjaxForm(action, form_data, onsuccess);
}

function fillNotifications(url, app_url){

    function createNotification(id, title, time){
        let li = document.createElement('li');

        let anchor = document.createElement('a');
        anchor.className = "text-dark d-flex py-2";
        anchor.href = app_url+'/notification/'+id;
        li.appendChild(anchor);

        let status = document.createElement('div');
        status.className = "flex-shrink-0 me-2 ms-3";
        let icon = document.createElement('i');
        icon.className = "fa fa-fw fa-dot-circle text-primary"; // fa-check-circle fa-times-circle text-primary text-danger
        status.appendChild(icon);
        anchor.appendChild(status);

        let contents = document.createElement('div');
        contents.className = "flex-grow-1 pe-2";
        let content_title = document.createElement('div');
        content_title.className = "fw-semibold";
        content_title.innerHTML = title;
        contents.appendChild(content_title);
        let content_time = document.createElement('span');
        content_time.className = "fw-medium text-muted";
        content_time.textContent = time;
        contents.appendChild(content_time);

        anchor.appendChild(contents);

        return li;
    }

    $.ajax({
        url: url,
        method: "GET",
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        dataType: 'json',
        success: function(data){
            console.log(data);
            let ulist = $('#notifications-dropdown');
            ulist.empty();
            $.each(data, function(idx, el){
                ulist.append( createNotification(el.id, el.title, el.created_at) );
            })
        },
        error: function(er){
            console.log(er);
        },
        complete: function(jqXHR, textStatus){ }
    });
}

function saveStateLocal(var_name, value) {
    localStorage[var_name] = value;
}

function getStateLocal(var_name) {
    if (localStorage[var_name]) {
        return localStorage[var_name];
    }else return '';
}

