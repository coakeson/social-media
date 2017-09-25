
function escapeHtml(text) {
    var map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };

    return text.replace(/[&<>"']/g, function(m) { return map[m]; });
}

function YouniqueFacebook(version){
    this.version = version || 'v2.9';
    this.baseURL = 'https://graph.facebook.com/' + this.version;
}

/**
 * Static instance return implementation
 * @type {null}
 */
var youniqueFacebookObj = null;
YouniqueFacebook.instance = function(){
    if(youniqueFacebookObj === null) {
        youniqueFacebookObj = new YouniqueFacebook();
    }
    return youniqueFacebookObj;
};

/**
 * Static Constants
 * @type {string}
 */
YouniqueFacebook.PHOTO_TYPES = {
    small: 'small',
    normal: 'normal',
    large: 'large',
    square: 'square',
    album: 'album'
};

YouniqueFacebook.prototype.getBaseURL = function(){
  return this.baseURL;
};

/**
 * Gets the graph API URL for the passed ID with options
 * @param facebookId
 * @param type
 * @param width
 * @param height
 * @returns {string}
 */
YouniqueFacebook.prototype.getPhotoURL = function(facebookId, options){
    if(!facebookId){
        return null;
    }

    options = options || {};
    // Make sure we have a valid photo type or default to normal
    options.type = (YouniqueFacebook.PHOTO_TYPES[options.type] ? options.type : YouniqueFacebook.PHOTO_TYPES.normal);

    var url = this.baseURL + '/' + facebookId + '/picture?type=' + options.type;
    if(options.width){
        url += '&width=' . options.width;
    }
    if(options.height) {
        url += '&height=' . options.height;
    }

    return url;
};

/**
 * Gets the HTML IMG tag that contains the correct graph API image src
 * @param facebookId
 * @param type
 * @param options
 * @returns {*}
 */
YouniqueFacebook.prototype.getPhotoImageHTML = function(facebookId, options){
    if(!facebookId){
        return null;
    }

    var output = '<img src="' + this.getPhotoURL(facebookId, options) + '"';

    options = options || {};

    if (options.class) {
        if (Array.isArray(options.class)) {
            // Flatten to a string
            options.class = options.class.join(' ');
        }
        output += ' class="' + options.class + '"';
    }

    if (options.alt) {
        output += ' alt="' + escapeHtml(options.alt) + '"';
    }

    if (options.title) {
        output += ' title="' + escapeHtml(options.title) + '"';
    }

    output += ' />';
    return output;
};
