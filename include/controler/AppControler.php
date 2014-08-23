<?php
require_once plugin_dir_path(__FILE__) . '/../lib/PHP_GCM/Constants.php';
require_once plugin_dir_path(__FILE__) . '/../lib/PHP_GCM/InvalidRequestException.php';
require_once plugin_dir_path(__FILE__) . '/../lib/PHP_GCM/Message.php';
require_once plugin_dir_path(__FILE__) . '/../lib/PHP_GCM/MulticastResult.php';
require_once plugin_dir_path(__FILE__) . '/../lib/PHP_GCM/Result.php';
require_once plugin_dir_path(__FILE__) . '/../lib/PHP_GCM/Sender.php';

use PHP_GCM\Constants as Constants;
use PHP_GCM\InvalidRequestException as InvalidRequestException;
use PHP_GCM\Message as Message;
use PHP_GCM\Result as Result;
use PHP_GCM\Sender as Sender;

class AppControler extends BaseControler
{
    /**
     * @param $appID
     * @return bool
     */
    public static function isExist($appID)
    {
        global $wpdb;
        $query = "SELECT ID
                    FROM $wpdb->posts
                    WHERE
                          ID = {$appID}
                          AND post_status = 'publish'
                          AND post_type = 'app'";
        $result = $wpdb->get_row($query, OBJECT);

        if ($result != null) {
            return $result->ID;
        } else {
            return false;
        }
    }

    public static function getAppIdByProjectId($projectID)
    {
        global $wpdb;
        $query = "SELECT DISTINCT ID
                    FROM $wpdb->posts,$wpdb->postmeta
                    WHERE $wpdb->posts.ID = $wpdb->postmeta.post_id
                        AND $wpdb->postmeta.meta_key = 'app_detail_projectid'
                        AND $wpdb->postmeta.meta_value = '{$projectID}'
                        AND $wpdb->posts.post_status = 'publish'
                        AND $wpdb->posts.post_type = 'app'";
        $result = $wpdb->get_row($query, OBJECT);

        if ($result != null)
        {
            return $result->ID;
        }
        else
        {
            return false;
        }
    }

    public static function push($app, $message){

        $appmeta = get_post_meta($app->ID);
        $messagemeta = get_post_meta($message->ID);

        $gcmApiKey = $appmeta['app_detail_apikey'][0];
        $collapseKey =$messagemeta['message_collapse_key'][0];
        $payloadData = json_decode($message->post_content, true);
        //add notification ID
        $payloadData["messageid"] = $message->ID;
        $numberOfRetryAttempts = 1;


        $devices = get_posts(array(
            'connected_type' => 'app_to_device',
            'connected_items' => $app
        ));

        $deviceRegistrationIds = array();

        foreach($devices as $device)
        {
            $devicemeta = get_post_meta($device->ID);
            $deviceRegistrationIds[] = $devicemeta['device_RegistrationId'][0];
        }

        $sender = new Sender($gcmApiKey);
        $message = new Message($collapseKey, $payloadData);
        try {
            $sendResults = $sender->sendMulti($message, $deviceRegistrationIds, $numberOfRetryAttempts);
            if ($sendResults->getCanonicalIds() > 0) {
                $results = $sendResults->getResults();

                $index = 0;
                foreach($results as $result)
                {
                    $canonicalRegistrationId = $result->getCanonicalRegistrationId();
                    if($canonicalRegistrationId) {
                        DeviceControler::updateRegistrationId($deviceRegistrationIds[$index], $canonicalRegistrationId);
                    }
                    $index++;
                }
            }
            echo "<pre > ";
            print_r($sendResults);
            echo "</pre > ";
            die();
            return $sendResults;
        } catch (InvalidArgumentException $e) {
            return new WP_Error("AppControler_push_fail",$e->getMessage());
        } catch (InvalidRequestException $e) {
            return new WP_Error("AppControler_push_fail",$e->getDescription());
        } catch (Exception $e) {
            return new WP_Error("AppControler_push_fail",$e->getMessage());
        }
    }
} 