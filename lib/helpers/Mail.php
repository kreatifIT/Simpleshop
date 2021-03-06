<?php

/**
 * This file is part of the Simpleshop package.
 *
 * @author FriendsOfREDAXO
 * @author a.platter@kreatif.it
 * @author jan.kristinus@yakamara.de
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FriendsOfREDAXO\Simpleshop;


class Mail extends \Kreatif\Mail
{
    protected $variables = [];
    protected $fragment  = null;

    public function __construct()
    {
        parent::__construct();

        FragmentConfig::$data['email_styles']['use_mail_styles'] = true;
        return $this;
    }

    public function send($debug = false, $use_master_tpl = true)
    {
        if ($use_master_tpl) {

            try {
                $this->Body = $this->fragment->parse('simpleshop/email/master.php');
            }
            catch (\rex_exception $ex){
                $this->Body = $this->fragment->parse('kreatif/email/master.php');
            }
        }

        return parent::send($debug, false);
    }
}

