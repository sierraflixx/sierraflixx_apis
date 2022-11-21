<?php

require_once "utils/entry.php";

$helper = $GLOBALS['helper'];
$message = ['data' => '', 'redirect' => ''];
$model = new Model($db->getConnection(), 'accounts');

if (isset($_COOKIE['memcid'])) {
    $auth_token = $_COOKIE['memcid'];
    $verified = verifyToken($auth_token);

    if ($verified) {
        $user = $model->Read($verified->uid);

        if (count($user) > 0) {

            if (!empty($user['secret'])) {

                $plan = $model->readRelatedWith('plans', $user['plan_id'], true);

                if (count($plan) > 0) {
                    unset($plan['id']);
                    $user['plan'] = $plan;
                    unset($user['plan_id']);

                    $sub = $model->readRelatedWith('subs', $user['sub_id'], true);

                    if (count($sub) > 0) {

                        $expired = time() > $sub['expires'];

                        if (!$expired) {
                            unset($sub['id']);
                            $user['sub'] = $sub;
                            unset($user['sub_id']);

                            $profile = $model->readRelatedWith('profile', $user['id'], true, 'account_id');

                            if (count($profile) > 0) {
                                $user['profile'] = $profile;

                                $message['data'] = 'Success';
                                $message['redirect'] = '/browse';
                            } else {
                                $message['data'] = 'No profile set';
                                $message['redirect'] = '/signup/profile';
                            }
                        } else {
                            $message['data'] = 'Subscription expired';
                            $message['redirect'] = '/subscription/renew';
                        }
                    }
                } else {
                    $message['data'] = 'No plan was set';
                    $message['redirect'] = '/signup/plan';
                    $route .= 'signup/plan';
                }
            } else {
                $message['data'] = 'Password not set';
                $message['redirect'] = '/signup/password';
            }
            unset($user['id']);
            unset($user['secret']);
            $helper->sendResponse($message, $user, 200);
        }
    }
}

var_dump($_COOKIE);
$message['data'] = 'Not logged in';
$message['redirect'] = '/signup';
$helper->sendResponse($message, [], 403);