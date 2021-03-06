jQuery(function() {
	
	jQuery(document).one('click', '.campaign__hero-cta--unsub', function(e) {
        var url =  '/wp-admin/admin-ajax.php?action=mailchimp_unsubscribe';
  
        $this = jQuery(this);

        e.stopImmediatePropagation();
        e.preventDefault();
        
        const campaign = $this.data('campaign');
        const list = $this.data('list');
        
        const data = {
            'campaign': campaign,
            'list': list
        };
        
        jQuery.ajax({
            url, 
            data,
            method: 'POST',
            success: function(resp) {
                const response = jQuery.parseJSON(resp);

                if (response.status === 'OK') {  
                    $this.addClass('campaign__hero-cta--sub');
                    $this.removeClass('campaign__hero-cta--unsub');
                    $this.text($this.data('sub-copy'));
                } 
            }
        });

        return false;
    });

    jQuery(document).one('click', '.campaign__hero-cta--sub', function(e) {

        e.stopImmediatePropagation();
        e.preventDefault();

        var $this = jQuery(this);
        var campaign = $this.data('campaign');
        var list = $this.data('list');

        var post = {
            'campaign': campaign,
            'list': list
        };

        var url =  '/wp-admin/admin-ajax.php?action=mailchimp_subscribe';

        jQuery.ajax({
            url: url,
            data: post,
            method: 'POST',
            success: function(response) {
                response = jQuery.parseJSON(response);
                if(response.status == 'OK') {
                    $this.removeClass('campaign__hero-cta--sub');
                    $this.addClass('campaign__hero-cta--unsub');
                    $this.text($this.data('unsub-copy'));
                } else {
                    
                }
            }
        });

        return false;
    });
});