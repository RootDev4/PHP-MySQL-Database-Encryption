$(document).ready(function()
{
    $('input.enckey').keypress(function()
    {
        if ($(this).val().length > 6)
        {
            $('button[name="encryptDB"]').removeAttr('disabled');
        }
        else
        {
            $('button[name="encryptDB"]').attr('disabled', 'disabled');
        }
    });
    
    $('#pwgen').click(function()
    {
        var length = 10,
            charset = "$%&/abcdefghijklnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789+-_.!?=()[]#",
            key = '';
        
        for (var i = 0, n = charset.length; i < length; ++i)
        {
            key += charset.charAt(Math.floor(Math.random() * n));
        }
        
        $('input.enckey').val(key);
        $('button[name="encryptDB"]').removeAttr('disabled');
    });
});