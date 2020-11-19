<?php

    // Add Crawler Names to whitelist
    $whitelistedBots = array(
        'Googlebot',
        'AdsBot',
        'Mediapartners-Google',
        'bingbot',
        'BingPreview',
        'msnbot',
        'adidxbot'
    );
    foreach ($whitelistedBots as $bot) {
        $whitelist = Mage::getModel('tm_botprotection/whitelist');
        $whitelist->setData('is_ip_range', 1);
        $whitelist->setData('ip_from_unpacked', '0.0.0.0');
        $whitelist->setData('ip_to_unpacked', '255.255.255.255');
        $whitelist->setData('crawler_name', $bot);
        $whitelist->save();
    }

    // create CMS pages for responses
    $url = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB);
    $prefixes = array('http://', 'https://', 'www.');
    foreach ($prefixes as $prefix) {
        if (substr($url, 0, strlen($prefix)) == $prefix) {
            $url = substr($url, strlen($prefix));
        }
    }
    $url = rtrim($url, '/');

    $pages = array(
        array(
            'title' => 'Are you a human?',
            'root_template' => 'empty',
            'meta_keywords' => 'Page keywords',
            'meta_description' => 'Page description',
            'identifier' => 'are-you-human',
            'content' => "<div class=\"bot-box\">\r\n"
                ."<p><span style=\"font-size: xx-large; font-family: helvetica;\">One more step</span></p>\r\n"
                ."<p>Complete security check to access ".$url."</p>\r\n"
                ."<p>{{widget type=\"tm_botprotection/human_confirm_form\"}}</p>\r\n"
                ."</div>",
            'is_active' => 1,
            'stores' => array(0),
            'sort_order' => 0
        ),
        array(
            'title' => 'Access denied',
            'root_template' => 'empty',
            'meta_keywords' => 'Page keywords',
            'meta_description' => 'Page description',
            'identifier' => 'access-denied',
            'content' => "<div class=\"bot-box\">\r\n"
                ."<h1><span style=\"font-size: xx-large;\">Access denied</span></h1>\r\n"
                ."<p>&nbsp;</p>\r\n"
                ."<h2><span style=\"font-size: large; color: #808080;\">What happened?</span></h2>\r\n"
                ."<p>The owner of this site has banned your IP or your browser from accessing this site.</p>\r\n"
                ."</div>",
            'is_active' => 1,
            'stores' => array(0),
            'sort_order' => 0
        )
    );

    foreach ($pages as $page) {
        if (!Mage::getModel('cms/page')->load($page['identifier'])->getId())
        {
            $cmsPage = Mage::getModel('cms/page');
            $cmsPage->setData($page)->save();
        }
    }

    //enable CAPTCHA and add human confirm form
    $botCheckForm = 'human_confirm';
    $captchaEnabled = Mage::getConfig()->getNode('customer/captcha/enable', 'default', 0 );
    if ($captchaEnabled != '0') {
        $forms = Mage::getConfig()->getNode('customer/captcha/forms', 'default', 0 );
        if ($forms) {
            $forms = implode(',', array($forms, $botCheckForm));
        } else {
            $forms = $botCheckForm;
        }
    } else {
        $forms = $botCheckForm;
        Mage::getConfig()->saveConfig('customer/captcha/enable', '1', 'default', 0);
    }
    Mage::getConfig()->saveConfig('customer/captcha/forms', $forms, 'default', 0);
