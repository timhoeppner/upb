var counter=0;
function check_submit()
{
counter++;
if (counter>1)
{
alert('You cannot submit the form again! Please Wait.');
return false;
}
}