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


use Sprog\Wildcard;

class Mail extends \rex_mailer
{
    protected $variables = [];
    protected $fragment  = NULL;

    public function __construct()
    {
        parent::__construct();
        $this->fragment = new \rex_fragment();
        $this->fragment->setVar('base_url', \rex_yrewrite::getFullPath());
        $this->fragment->setVar('logo_path', 'resources/img/email/logo.png');
        $this->IsHTML(TRUE);
        return $this;
    }

    public function setFragmentPath($fragment_path)
    {
        $this->fragment->setVar('fragment_path', $fragment_path);
    }

    public function setVar($key, $value)
    {
        $this->fragment->setVar($key, $value);
    }

    public function send($debug = FALSE)
    {
        $this->Body = $this->fragment->parse('simpleshop/email/master.php');

        $this->Subject = Wildcard::parse(Wildcard::parse($this->Subject));
        $this->Body    = Wildcard::parse(Wildcard::parse($this->Body));

        if ($debug)
        {
            pr($this->Subject);
            echo $this->Body;
            exit;
        }
        return parent::send();
    }
}

