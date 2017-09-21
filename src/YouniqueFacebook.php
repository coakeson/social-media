<?php
namespace Younique\Social;

/**
 * Class YouniqueFacebook
 *
 * Wraps all Facebook activities that are used in the Younique apps
 */
class YouniqueFacebook{

    const PHOTO_TYPE_SMALL = 'small';
    const PHOTO_TYPE_NORMAL = 'normal';
    const PHOTO_TYPE_LARGE = 'large';
    const PHOTO_TYPE_SQUARE = 'square';
    const PHOTO_TYPE_ALBUM = 'album';

    private $version = NULL;

    public static function instance(){
        static $obj = NULL;

        if($obj === NULL){
            $obj = new static();
        }

        return $obj;
    }

    public function __construct($version = 'v2.9'){
        $this->version = $version;
    }

    private function getBaseURL(){
        return 'https://graph.facebook.com/' . $this->version;
    }

    /**
     * Returns a URL that will redirect the browser to the image location for download
     * @param int|string $facebook_id
     * @param string $type
     * @param int $width
     * @param int $height
     * @return string
     */
    public function getPhotoURL($facebook_id, $type = self::PHOTO_TYPE_NORMAL, $width = NULL, $height = NULL){
        $url = $this->getBaseURL() . '/' . $facebook_id . '/picture?type=' . $type;
        if($width){
            $url .= '&width=' . $width;
        }
        if($height) {
            $url .= '&height=' . $height;
        }
        return $url;
    }

    /**
     * Returns HTML markup for an IMG tag with the profile photo of the specified user
     * @param int|string $facebook_id
     * @param string $type
     * @param array $classes
     * @return string
     */
    public function getPhotoImageHTML($facebook_id, $type = self::PHOTO_TYPE_NORMAL, $options = array()){

        if(empty($facebook_id)){

            return $this->Html->image("blank_profile_pic.svg", array("class" => "svg", "nopin" => "no-pin", "alt" => $order->buyer));
        }

        $output = '<img src="' . $this->getPhotoURL($facebook_id, $type) . '"';
        if(isset($options['class'])){
            if(is_array($options['class'])){
                $options['class'] = implode(' ', $options['class']);
            }
            $output .= ' class="' . $options['class'] . '"';
        }
        if(isset($options['alt'])){
            $output .= ' alt="' . implode(' ', htmlspecialchars($options['alt'])) . '"';
        }
        if(isset($options['title'])){
            $output .= ' title="' . implode(' ', htmlspecialchars($options['title'])) . '"';
        }
        $output .= ' />';
        return $output;
    }

    /**
     * Returns a single string that includes everything to allow Facebook JS calls on a page
     *
     * @param array $options
     * @return string
     */
    public function getJSInitScriptHTML($options = array()){
        // Set defaults
        $options += array(
            'cookie' => true,
            'xfbml' => true,
            'status' => true,
            'logPageView' => true,
        );

        return '
        <script type="text/javascript" data-meta="Facebook Init">
            window.fbAsyncInit = function() {
                FB.init({
                    appId      : ' . set_env('FACEBOOK_APP_ID') . ',
                    cookie     : ' . ($options['cookie'] ? 'true' : 'false') . ',
                    xfbml      : ' . ($options['xfbml'] ? 'true' : 'false') . ',
                    status     : ' . ($options['status'] ? 'true' : 'false') . ',
                    version    : "' . $this->version . '"
                });
                
                ' . ($options['logPageView'] ? 'FB.AppEvents.logPageView();' : '') . '
            };
            
            (function(d, s, id){
                var js, fjs = d.getElementsByTagName(s)[0];
                if (d.getElementById(id)) {return;}
                js = d.createElement(s); js.id = id;
                js.src = "//connect.facebook.net/en_US/sdk.js";
                fjs.parentNode.insertBefore(js, fjs);
            }(document, "script", "facebook-jssdk"));
        </script>
        ';
    }
}
