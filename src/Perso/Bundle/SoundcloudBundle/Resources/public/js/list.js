;(function(window) {

    list =
    {
        $songOrgaLi : null,
        $content    : null,

        init: function()
        {
            self.$songOrgaLi = $('.song_organisation li');
            self.$content = $('.content');

            self.$songOrgaLi.on('click', function(orga)
            {
                self.organise(orga, this);
            })
        },

        organise: function(orga, thelist)
        {
            self.$songOrgaLi.removeClass('select');
            $(thelist).addClass('select');
            var index = self.$songOrgaLi.index(thelist);
            if(index == 0){
                self.$content.addClass('list');
                return;
            }else{
                self.$content.removeClass('list')
            }
            return orga;
        }
    }

    var self = list;
    window.list = list;

})(window)