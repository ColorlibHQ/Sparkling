jQuery( document ).ready(function() {
    var mediaControl = {

        // Initializes a new media manager or returns an existing frame.
        // @see wp.media.featuredImage.frame()
        selector: null,
        size: null,
        container: null,
        frame: function() {
            if ( this._frame ) {
                return this._frame;

            }

            this._frame = wp.media({
                title: Sparkling.frameTitle,
                button: {
                    text: Sparkling.frameButton
                },
                multiple: false
            });

            this._frame.on( 'open', this.updateFrame ).state( 'library' ).on( 'select', this.select );

            return this._frame;

        },

        select: function() {

            // Do something when the "update" button is clicked after a selection is made.
            // Ask the frame for its selection: reading the first ".selected"
            // thumbnail in the DOM picked up stale selections from other frames.
            var selection = mediaControl.frame().state().get( 'selection' ).first();
            var id = selection ? selection.get( 'id' ) : null;
            var selector = jQuery( '.sparkling-media-container' ).find( mediaControl.selector );
            var data = {
                action: 'sparkling_get_attachment_media',
                attachment_id: id,
                nonce: Sparkling.nonce
            };

            if ( ! id || ! selector.length ) {
                return false;
            }

            jQuery.post( Sparkling.ajaxurl, data, function( response ) {
                if ( ! response || ! response.success || ! response.data ) {
                    return;
                }
                var currentImage = jQuery( mediaControl.container ).find( 'img' );
                if ( currentImage.length > 0 ) {
                    currentImage.replaceWith( response.data.image );
                }else{
                    jQuery( mediaControl.container ).find( '.attachment-media-view' ).append( response.data.image );
                }
                jQuery( mediaControl.container ).find( '.attachment-media-view .placeholder' ).hide();
                selector.val( response.data.id ).trigger('change');
            }, 'json' );

        },

        init: function() {
            var context = jQuery( '#wpbody, .wp-customizer' );
            context.on( 'click', '.sparkling-media-container .upload-button', function( e ) {
                var container = jQuery( this ).parents( '.sparkling-media-container' ),
                    sibling = container.find( 'input' ),
                    id = sibling.attr( 'id' );
                e.preventDefault();
                mediaControl.container = container;
                mediaControl.selector = '#' + id;
                mediaControl.frame().open();

            });

            context.on( 'click', '.sparkling-media-container .remove-button', function( e ) {
                var container = jQuery( this ).parents( '.sparkling-media-container' ),
                    sibling = container.find( 'input' ),
                    img = container.find( 'img' ),
                    placeholder = container.find( '.placeholder' );
                e.preventDefault();
                img.remove();
                placeholder.show();
                sibling.val( '' ).trigger( 'change' );
            });

        }

    };

    mediaControl.init();
})