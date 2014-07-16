;(function(window){

    var likes_collector =
    {
        data        : null,
        dltype      : null,
        $form       : null,
        $songs      : null,
        path_dl     : null,
        path_getzip : null,
        likes       : {},
        
        init: function(path_dl, path_getzip)
        {
            this.path_dl     = path_dl;
            this.path_getzip = path_getzip;
            this.$form       = $('#download-form');
            this.$songs      = $('article.song');
            
            $('#download, #all_download').on('click', function(e)
            {
                e.preventDefault();
                self.data   = self.$form.serializeArray(),
                self.dltype = $(this).attr('id');
                self.get_likes();
            });
        },

        get_likes: function()
        {
            self.$songs.each(function()
            {
                var id    = $(this).find('input[name=id]').val(),
                    title = $(this).find('input[name=title]').val(),
                    link  = $(this).find('input[name=link]').val();
                
                if($(this).find('.checkbox').prop('checked') && self.dltype == 'download') {
                    self.likes[id] = {};
                    self.likes[id]['title'] = title;
                    self.likes[id]['link']  = link;
                } else if (self.dltype == 'all_download') {
                    self.likes[id] = {};
                    self.likes[id]['title'] = title;
                    self.likes[id]['link']  = link;
                }
            });

            self.data = {'user_id':'{{user_id}}', 'likes':self.likes};
            self.collect_data();
        },

        collect_data: function()
        {
            $.ajax({
                type: "POST",
                url : self.path_dl,
                data: self.data,
                success: function()
                {
                    document.location = self.path_getzip;
                }
            });
        }
    }

    var self = likes_collector;
    window.likes_collector = likes_collector;

})(window)