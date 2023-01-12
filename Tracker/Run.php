<?php
/**
 * @copyright   © EAX LEX SRL. All rights reserved.
 **/

namespace Mktr\Tracker;

use Mktr\Tracker\Model\Cron;

class Run
{
    private static $init = null;

    public static function init() {
        if (self::$init == null) { self::$init = new self(); }
        return self::$init;
    }

    public function __construct()
    {
        register_deactivation_hook( __FILE__, [$this, 'unInstall'] );
        add_action('init', array($this, 'addRoute'), 0);

        if (is_admin())
        {
            Admin::loadAdmin();
        } else {
            Front::loadFront();
        }

        // add_action('woocommerce_loaded', function (){  });
        add_action('MKTR_CRON', array($this, "cronAction"));
    }

    public function cronAction() {
        Cron::cronAction();
    }

    public function addRoute() {
        add_rewrite_tag('%'.Config::$name.'%', '([^&]+)');

        /* Todo: AddToActivate */
        add_rewrite_rule(
            Config::$name.'/([^/]+)/([^/]+)/?',
            'index.php?'.Config::$name.'=$matches[2]',
            'top' );
    }

    public function unInstall() {
        wp_clear_scheduled_hook( 'MKTR_CRON' );
    }
}
