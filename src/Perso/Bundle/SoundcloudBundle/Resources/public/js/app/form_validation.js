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