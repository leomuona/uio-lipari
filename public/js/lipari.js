var baseDir = '/lipari';
// DOCUMENT READY - START
$(document).ready(function(){
    updatePasswordfieldsVisibility();
    $('#ldap').change(function(){
        updatePasswordfieldsVisibility();    
    });
    $('#add_to_group_popup').hide();
    $('#add_users_button').click(function(){
        showAddUsersToGroupPopup();    
    });
    $('#remove_users_button').click(function(){
        removeUsersFromGroup(); 
    });
    $('#delete_user_form').submit(function(event){
    	var answer = confirm("Haluatko varmasti poistaa käyttäjän?");
    	if (!answer) {
    		event.preventDefault();
    		return false;
    	}
    	return true;
    });
});
// DOCUMENT READY - END

// user forms password fields and ldap compability
function updatePasswordfieldsVisibility() {
    if ($('#ldap').is(':checked')) {
        $('#pswd1').val('');
        $('#pswd2').val('');
        $('#pswd1-label').hide();
        $('#pswd1-element').hide();
        $('#pswd2-label').hide();
        $('#pswd2-element').hide();
    } else {
        $('#pswd1-label').show();
        $('#pswd1-element').show();
        $('#pswd2-label').show();
        $('#pswd2-element').show();
    }
}

// GROUP'S USERS FUNCTIONALITY
function updateUserlist(gid) {
    $.ajax({
        type: "GET",
        url: baseDir + "/group-users/group-users",
        data: "gid=" + gid,
        dataType: "html"
    }).done(function(html){
        $('#groups_userselect_div').html(html);
    });
}

function updateAllUsersList(gid) {
    $.ajax({
        type: "GET",
        url: baseDir + "/group-users/all-users",
        data: "gid=" + gid,
        dataType: "html"
    }).done(function(html){
        $('#all_userselect_div').html(html);
    });
}

function showAddUsersToGroupPopup() {
    var gid = $('#gid').html();
    updateAllUsersList(gid);
    var popup = $('#add_to_group_popup');
    popup.show("fast");
}

function addUsersToGroup() {
    var uids = $('#allUsers').val();
    var gid = $('#gid').html();
    $.ajax({
        type: "POST",
        url: baseDir + "/group-users/add-users",
        data: {"gid": gid, "uids": uids}
    }).done(function(resp){
        console.log(resp);
        updateUserlist(gid);
    });
    $('#add_to_group_popup').hide("fast");
}

function removeUsersFromGroup() {
    var checkText = "Haluatko varmasti poistaa valitut käyttäjät ryhmästä?";
    var uids = $('#groups_userselect').val();
    var gid = $('#gid').html();
    var check = confirm(checkText);
    if (check) {
        $.ajax({
            type: "POST",
            url: baseDir + "/group-users/remove-users",
            data: {"gid": gid, "uids": uids}
        }).done(function(resp){
            console.log(resp);
            updateUserlist(gid);
        });
    }
}