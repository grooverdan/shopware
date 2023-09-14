import template from './sw-cms-preview-youtube-video.html.twig';
import './sw-cms-preview-youtube-video.scss';

/**
 * @private
 * @package content
 */
export default {
    template,

    computed: {
        assetFilter() {
            return Shopware.Filter.getByName('asset');
        },
    },
};
