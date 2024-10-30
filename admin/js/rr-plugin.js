/*
 * Plugin : Contact Form 7 Round Robin
 * File : rr-plugin.js
 * Dessription: Script for ajax and validates
 * Author: iCreate Advertising Solutions
 * Author URI: http://icreateadvertising.com.au/
 * Version: 1.0
 */

jQuery(document).ready(function ($) {
    // Fetch the users while change the form
    $('#cf7-form').change(function () {
        var formID = $(this).val();
        if (jQuery('#cf7-form').hasClass('rr-filed-not-valid') && formID)
            jQuery('#cf7-form').removeClass('rr-filed-not-valid');
        if (formID) {
            //jQuery('#cf7-form').css('display','none');
            $('.form-select').append('<span class="spinner" style="display:inline-block;"></span>');
            $.ajax({
                type: "POST",
                url: rrAjax.ajaxurl,
                data: {
                    'action': 'rr_ajax_request',
                    'formID': formID,
                    'process': 'getUser'
                },
                success: function (data) {
                    $('.user-items').html(data);
                    checkFormStatus(formID);
                    //jQuery('#cf7-form').css('display','block');
                    $('.form-select .spinner').remove();
                },
                error: function (errorThrown) {
                    console.log("This has thrown an error:" + errorThrown);
                }
            });
        }
    });

    // Uesr row will append while click on add email
    var incVal = 1;
    $('#add-user').click(function () {
        $('.form-select #rr-users option:first-child').attr("selected", "selected");
        var newUser = '<li class="user-item new ' + incVal + '"> <span class="item-name"><label for="user-name-new-' + incVal + '" class="label">Name</label> <input type="text" name="user-name-new-' + incVal + '" id="user-name-new-' + incVal + '" value="" /></span> <span class="item-email"><label for="user-email-new-' + incVal + '" class="label">Email</label><input type="text" name="user-email-new-' + incVal + '" id="user-email-new-' + incVal + '" value="" /></span> <span class="item-active"><label for="" class="label">&nbsp</label><input type="checkbox" value="1" name="is-user-active-new-' + incVal + '" id="is-user-active-new-' + incVal + '" /><label for="is-user-active">Active</label></span> <span class="item-edit"><label for="" class="label">&nbsp</label><input type="button" name="user-save-new-' + incVal + '" id="user-save-new-' + incVal + '" value="Save" onclick="addNewUser(' + incVal + ')" /></span><span class="item-delete"></span></li>';
        $('.user-details').html(newUser);
        //$('#add-user').prop("disabled",true);
        //incVal = incVal + 1;

    });

    // Fetch user details for selected user
    $('#rr-users').change(function () {
        var userId = $('#rr-users').val();
        if (userId) {
            $('.user-details').html('<li><span class="spinner" style="display:inline-block;"></span></li>');
            $.ajax({
                type: "POST",
                url: rrAjax.ajaxurl,
                data: {
                    'action': 'rr_ajax_request',
                    'userID': userId,
                    'process': 'getUserDetails'
                },
                success: function (data) {
                    $('.user-details').html(data);
                    //$('.user-details .spinner').remove();
                    console.log('setting up date picker');
                    jQuery('.item-holidays .rr-date').datepicker({
                        dateFormat: 'yy-mm-dd',
                    });
                },
                error: function (errorThrown) {
                    console.log("This has thrown an error:" + errorThrown);
                }
            });
        } else {
            $('.user-details').html('<li><span class="msg rr-error">Please select the valid user.</span></li>');
        }
    });

});

// Enable or disable the form to Roun Robin
function formAssignToRR() {
    var chkFormID = jQuery('#cf7-form').val();
    var isRR = 0;
    var strMsg;
    if (jQuery('#is-round-robin').is(':checked'))
        isRR = 1;
    if (chkFormID) {
        jQuery.ajax({
            type: "POST",
            url: rrAjax.ajaxurl,
            data: {
                'action': 'rr_ajax_request',
                'formID': chkFormID,
                'isRR': isRR,
                'process': 'assignForm'
            },
            success: function (data) {
                if (data) {
                    if (isRR)
                        strMsg = '<span class="spinner" style="display:inline-block;"></span> The form enabled successfully for round robin process.';
                    else
                        strMsg = '<span class="spinner" style="display:inline-block;"></span> The form disabled successfully for round robin process.';
                    jQuery('.check-form .msg').addClass('rr-success').html(strMsg);
                }
                setTimeOutMsg('check-form', 'rr-success');
            },
            error: function (errorThrown) {
                console.log("This has thrown an error:" + errorThrown);
            }
        });
    } else {
        strMsg = 'Please select the valid form.';
        jQuery('.check-form  .msg').addClass('rr-error').html(strMsg);
        jQuery('#cf7-form').addClass('rr-filed-not-valid').focus();
        setTimeOutMsg('check-form', 'rr-error');
    }
}

// Check form status is enabled or disabled
function checkFormStatus(formId) {
    jQuery.ajax({
        type: "POST",
        url: rrAjax.ajaxurl,
        data: {
            'action': 'rr_ajax_request',
            'formID': formId,
            'process': 'formStatus'
        },
        success: function (data) {
            if (data == 1) {
                jQuery('#is-round-robin').prop('checked', true);
            } else {
                jQuery('#is-round-robin').prop('checked', false);
            }
        },
        error: function (errorThrown) {
            console.log("This has thrown an error:" + errorThrown);
        }
    });
}

// Assign users to Form

function assignUsersToForm() {
    var chkFormID = jQuery('#cf7-form').val();
    var chkUsersID = [];
    var inc = 0;
    jQuery('.user-items input[type=checkbox]:checked').each(function () {
        chkUsersID[inc++] = jQuery(this).val();
    });

    if (chkFormID) {
        jQuery('.user-items-wraper').append('<span class="spinner" style="display:inline-block;"></span>');
        jQuery.ajax({
            type: "POST",
            url: rrAjax.ajaxurl,
            data: {
                'action': 'rr_ajax_request',
                'formID': chkFormID,
                'usersId': chkUsersID,
                'process': 'usersAssignToForm'
            },
            success: function (data) {
                if (data) {
                    jQuery('.user-items-wraper > .spinner').remove();
                }
            },
            error: function (errorThrown) {
                console.log("This has thrown an error:" + errorThrown);
            }
        });
    } else {
        strMsg = 'Please select the valid form.';
        jQuery('.check-form  .msg').addClass('rr-error').html(strMsg);
        jQuery('#cf7-form').addClass('rr-filed-not-valid').focus();
        setTimeOutMsg('check-form', 'rr-error');
    }
}

// Reload User Select

function reloadUserSelect(userId) {
    //jQuery('.form-select ').html( '<label for="rr-users">Users</label><span class="spinner" style="display:inline-block;"></span>' );
    jQuery.ajax({
        type: "POST",
        url: rrAjax.ajaxurl,
        data: {
            'action': 'rr_ajax_request',
            'userID': userId,
            'process': 'reloadUserSelect'
        },
        success: function (data) {
            if (data) {
                jQuery('.form-select #rr-users').html(data);
            }
        },
        error: function (errorThrown) {
            console.log("This has thrown an error:" + errorThrown);
        }
    });
}

// call ajax function for store the user
function addNewUser(fieldId) {
    //var formID = jQuery('#cf7-form').val();
    var userEmail = jQuery('#user-email-new-' + fieldId).val();
    var userName = jQuery('#user-name-new-' + fieldId).val();
    var isError = false;
    if (!userName) {
        jQuery('#user-name-new-' + fieldId).css('border-color', 'red');
        isError = true;
    } else {
        jQuery('#user-name-new-' + fieldId).css('border-color', '#333');
    }
    if (!isValidEmail(userEmail)) {
        jQuery('#user-email-new-' + fieldId).css('border-color', 'red');
        isError = true;
    } else {
        jQuery('#user-email-new-' + fieldId).css('border-color', '#333');
    }
    if (isError)
        return false;
    var userActive = 0;
    if (jQuery('#is-user-active-new-' + fieldId).is(':checked'))
        userActive = jQuery('#is-user-active-new-' + fieldId).val();

    jQuery('.user-details li.new.' + fieldId + ' .item-edit').append('<span class="spinner" style="display:inline-block;"></span>');
    jQuery.ajax({
        type: "POST",
        url: rrAjax.ajaxurl,
        data: {
            'action': 'rr_ajax_request',
            'name': userName,
            'email': userEmail,
            'active': userActive,
            'process': 'addUser'
        },
        success: function (data) {
            if (data == 'user exist') {
                jQuery('.user-details #user-email-new-' + fieldId).css('border-color', 'red');
                jQuery('.user-details-wraper .msg').text('Email already exist.').addClass('rr-error');
                setTimeOutMsg('user-details-wraper', 'rr-error');
                jQuery('.user-details li.new.' + fieldId + ' .item-edit .spinner').remove();
            } else if (data == 'failed') {
                jQuery('.user-details-wraper .msg').text('User add process is error.Pls try again later.').addClass('rr-error');
                setTimeOutMsg('user-details-wraper', 'rr-error');
                jQuery('.user-details li.new.' + fieldId + ' .item-edit .spinner').remove();
            } else {
                jQuery('.user-details li.new.' + fieldId).html(data);
                var userId = jQuery('.user-details li.new.' + fieldId + ' span.item-id').text();
                reloadUserSelect(userId);
                jQuery('.user-details li.new.' + fieldId).addClass(userId);
                jQuery('.user-details li.' + userId).removeClass(fieldId).removeClass('new');
            }
        },
        error: function (errorThrown) {
            console.log("This has thrown an error:" + errorThrown);
        }
    });
    //return true;
}

// call ajax function for edit the user
function editUser(userId) {
    //var formID = jQuery('#cf7-form').val();
    var userActive = 0;
    var userEmail = jQuery('#user-email-' + userId).val();
    var userName = jQuery('#user-name-' + userId).val();

    var holidaysStart = jQuery('#holidays-start-' + userId).val();
    var holidaysEnd = jQuery('#holidays-end-' + userId).val();

    var isError = false;
    if (!userName) {
        jQuery('#user-name-' + userId).css('border-color', 'red');
        isError = true;
    } else {
        jQuery('#user-name-' + userId).css('border-color', '#333');
    }

    if ( ! isValidDateRange(holidaysStart, holidaysEnd)) {
        jQuery('.rr-date').css('border-color', 'red');
        isError = true;
    }
    else {
        jQuery('.rr-date').css('border-color', '#333');
    }

    // Validate the Email
    if (!isValidEmail(userEmail)) {
        jQuery('#user-email-' + userId).css('border-color', 'red');
        isError = true;
    } else {
        jQuery('#user-email-' + userId).css('border-color', '#333');
    }

    if (jQuery('#is-user-active-' + userId).is(':checked'))
        userActive = jQuery('#is-user-active-' + userId).val();

    if (isError)
        return false;

    jQuery('.user-details li.' + userId + ' .item-edit').append('<span class="spinner" style="display:inline-block;"></span>');
    jQuery.ajax({
        type: "POST",
        url: rrAjax.ajaxurl,
        data: {
            'action': 'rr_ajax_request',
            'name': userName,
            'email': userEmail,
            'active': userActive,
            'holidaysStart': holidaysStart,
            'holidaysEnd': holidaysEnd,
            'userId': userId,
            'process': 'editUser'
        },
        success: function (data) {
            if (data == 'user exist') {
                jQuery('.user-details #user-email-' + userId).css('border-color', 'red');
                jQuery('.user-details-wraper .msg').text('Email already exist.').addClass('rr-error');
                setTimeOutMsg('user-details-wraper', 'rr-error');
                jQuery('.user-details li.' + userId + ' .item-edit .spinner').remove();
            } else if (data) {
                reloadUserSelect(userId);
                jQuery('.user-details li.' + userId + ' .item-edit .spinner').remove();
            } else {
                jQuery('.user-details-wraper .msg').text('User add process is error.Pls try again later.').addClass('rr-error');
                setTimeOutMsg('user-details-wraper', 'rr-error');
                jQuery('.user-details li.' + userId + ' .item-edit .spinner').remove();
            }

        },
        error: function (errorThrown) {
            console.log("This has thrown an error:" + errorThrown);
        }
    });

}

// call ajax function for delete the user
function deleteUser(userId) {
    // var formID = jQuery('#cf7-form').val();
    var checkConfirm = confirm("Are you sure for delete the user?");
    if (checkConfirm == true) {
        jQuery('.user-details li.' + userId + ' .item-delete').append('<span class="spinner" style="display:inline-block;"></span>');
        jQuery.ajax({
            type: "POST",
            url: rrAjax.ajaxurl,
            data: {
                'action': 'rr_ajax_request',
                'userId': userId,
                'process': 'deleteUser'
            },
            success: function (data) {
                if (data) {
                    reloadUserSelect('');
                    jQuery('.user-details').html('<li><span class="msg rr-success">The user deleted successfully.</span></li>');
                }
            },
            error: function (errorThrown) {
                console.log("This has thrown an error:" + errorThrown);
            }
        });
    }

}

function clearHolidays() {
    jQuery('.rr-date').val('');
}

// Clear the message after some time
function setTimeOutMsg(txtClear, txtClass) {
    setTimeout(function () {
        jQuery('.' + txtClear + '  .msg').removeClass(txtClass).text('');
    }, 3000);
}

function isValidDateRange(start, end) {
    if ( start == '' || end == '' ) {
        return true
    }

    start = start.replace(/\D/g, '');
    end = end.replace(/\D/g, '');
    return (start <= end);
}

// Validate Fields
function isValidEmail(emailAddress) {
    var filter = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return filter.test(emailAddress);
};