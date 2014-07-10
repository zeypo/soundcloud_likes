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
;(function(window){

    formValidation = 
    {
        $form     : null,
        action    : null,
        stringurl : null,
        $loader   : null,

        init: function(form, action)
        {
            this.$form   = form;
            this.action  = action;
            this.$loader = $('#loader');

            this.$form.submit(function(e)
            {
                e.preventDefault();
                self.checkUrl();
            })
        },

        checkUrl: function()
        {
            var regexp = new RegExp('(https://|http://|http://www\.|www|)?soundcloud\.com/.+$', 'g');
            
            self.stringurl = self.$form.find('#userurl').val();
            var isavailable = regexp.test(self.stringurl);

            if(isavailable != true) {
                alert('mauvaise string');
                return;
            }

            self.sendDatas();
        },

        sendDatas: function()
        {
            var data = self.$form.serialize();
            self.$loader.html('ceci est un loader...');

            $.ajax({
                type: "POST",
                url: self.action,
                data: data,
                success: function(data)
                {
                    $('#content').empty().html(data);
                    self.$loader.empty();
                }
            });
        }
    }

    var self = formValidation;
    window.formValidation = formValidation;

})(window)