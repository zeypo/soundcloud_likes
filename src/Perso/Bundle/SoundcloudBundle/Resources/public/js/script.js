$(function()
{

    $('.song_organisation li').on('click', function (orga) {
        $('.song_organisation li').removeClass('select');
        $(this).addClass('select');
        var index = $( ".song_organisation li" ).index( this );
        if(index == 0){
            $('.content').addClass('list');
            return  
        }else{
            $('.content').removeClass('list')
        }
        return orga;
    });
    
});