function AddSmilie(Which){
add_msg = '[img]';
add_msg += Which;
add_msg +='[/img]';
opener.document.newentry.message.value += add_msg;
}