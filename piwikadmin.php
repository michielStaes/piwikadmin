<?php

namespace Grav\Plugin;

use Grav\Common\Plugin;


class PiwikAdminPlugin extends Plugin
{
    /**
     * @return array
     *
     * The getSubscribedEvents() gives the core a list of events
     *     that the plugin wants to listen to. The key of each
     *     array section is the event that the plugin listens to
     *     and the value (in the form of an array) contains the
     *     callable (or function) as well as the priority. The
     *     higher the number the higher the priority.
     */

    public static function getSubscribedEvents() {
        return [
            'onAdminTwigTemplatePaths' => ['onAdminTwigTemplatePaths', 0],
            'onAssetsInitialized' => ['onAssetsInitialized', 0]
        ];
    }

    private function getRootPath() {
        $uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
        $root_path = str_replace(' ', '%20', rtrim(substr($_SERVER['PHP_SELF'], 0, strpos($_SERVER['PHP_SELF'], 'index.php')), '/'));
        // check if userdir in the path and workaround PHP bug with PHP_SELF
        if (strpos($uri, '/~') !== false && strpos($_SERVER['PHP_SELF'], '/~') === false) {
            $root_path = substr($uri, 0, strpos($uri, '/', 1)) . $root_path;
        }
        return $root_path;
    }

    private function getSetup(){

        $piwiksetup = [
            'hosted_url'    => $this->config->get('plugins.piwikadmin.hosted_url', ""),
            'trackingId'    => $this->config->get('plugins.piwikadmin.trackingId', 1)
        ];

        return $piwiksetup;
    }

    private function getUserConfiguration(){

        $piwikconfig = [
          'track_subdomain'          => $this->config->get('plugins.piwikadmin.track_subdomains', false),
          'prepend_domain'           => $this->config->get('plugins.piwikadmin.prepend_domain', false),
          'hide_alias'               => $this->config->get('plugins.piwikadmin.hide_alias', false),
          'track_disabled_js'        => $this->config->get('plugins.piwikadmin.track_disabled_js', false),
          'track_custom_vars'        => $this->config->get('plugins.piwikadmin.track_custom_vars', false),
          'do_not_track'             => $this->config->get('plugins.piwikadmin.do_not_track', false),
          'disable_tracking_cookies' => $this->config->get('plugins.piwikadmin.disable_tracking_cookies', false)
        ];

        return $piwikconfig;
    }

    private function addCodeOptions($array, $string) {

        $piwikCode = [
            'track_subdomain'          => '_paq.push(["setCookieDomain", "*.'.$this->grav['uri']->host().'"]);',
            'prepend_domain'           => '_paq.push(["setDocumentTitle", document.domain + "/" + document.title]);',
            'hide_alias'               => '_paq.push(["setDomains", ["*.'.$this->grav['uri']->host().''.$this->getRootPath().'"]]);',
            'do_not_track'             => '_paq.push(["setDoNotTrack", true]);',
            'disable_tracking_cookies' => '_paq.push(["disableCookies"]);'
        ];

        $string .= "var _paq = _paq || [];\n";

        foreach ($array as $key => $value){
            if($value){
                $string .= ($piwikCode[$key]);
                $string .= "\n";
            }

        }

        $custom_vars = $this->config->get('plugins.piwikadmin.track_custom_vars');
        if($custom_vars == false){
        }else{
            $i = 1;
            foreach ($custom_vars as $key => $value) {
                $string .= '_paq.push(["setCustomVariable",'.$i.', "'.$key.'", "'.$value.'", "visit"])';
                $string .= "\n";
                $i++;
                if ($i == 6) break;
            }
        }

        return $string;
    }

    private function getTrackingCode()
    {

        $setup = $this->getSetup();
        $options = $this->getUserConfiguration();

        //removing the http:// start of url as piwik does not require it.
        $search = array('http://','https://');
        $hosted_url = str_replace($search,'',$setup['hosted_url']);

        $code = '';
        $code = $this->addCodeOptions($options, $code);
        $code .= "_paq.push(['trackPageView']);\n";
        $code .= "_paq.push(['enableLinkTracking']);\n";
        $code .= "(function() {\n";
        $code .= "var u='//".$hosted_url."';\n";
        $code .= "_paq.push(['setTrackerUrl', u+'piwik.php']);\n";
        $code .= "_paq.push(['setSiteId', '".$setup['trackingId']."']);\n";
        $code .= "var d=document, g=d.createElement('script'), s=d.getElementsByTagName('script')[0];\n";
        $code .= "g.type='text/javascript'; g.async=true; g.defer=true; g.src=u+'piwik.js'; s.parentNode.insertBefore(g,s);\n";
        $code .= "})();\n";

//        if($options['track_disabled_js']) {
//            $code = '<noscript><p><img src="'.$hosted_url.'piwik.php?idsite='.$setup['trackingId'].'&rec=1" style="border:0;" alt="" /></p></noscript>';
//        }

        return $code;
    }

    public function onAssetsInitialized()
    {
        // Don't proceed if we are in the admin plugin
        if ($this->isAdmin()) {
                $assets = $this->grav['assets'];
                $assets->addCss('user/plugins/piwikadmin/css/piwikadmin.css', 1);
        }

        $this->enable([
            'onAdminMenu' => ['onAdminMenu', 0],
        ]);

        $trackingId = trim($this->config->get('plugins.piwikadmin.trackingId', ''));
        if (empty($trackingId)) return;

        $hosted_url = trim($this->config->get('plugins.piwikadmin.hosted_url', ''));
        if (empty($hosted_url)) return;

        $code = $this->getTrackingCode();
        $this->grav['assets']->addInlineJs($code);
    }

    /**
     *
     * Add plugin templates path
     */
    public function onAdminTwigTemplatePaths($event) {

        $event['paths'] = array_merge($event['paths'], [__DIR__ . '/admin/templates']);
        return $event;
    }

    public function onAdminMenu() {

        $this->grav['twig']->plugins_hooked_nav['Stats'] = ['route' => 'stats', 'icon' => ' fa-bar-chart-o'];
    }

}
