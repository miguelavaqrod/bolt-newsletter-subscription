<?php
// Newsletter Subscription Extension for Bolt

namespace Bolt\Extension\miguelavaqrod\newssubscription;

class Extension extends \Bolt\BaseExtension
{
    public function getName()
    {
        return "Newsletter Subscription";
    }

    public function initialize()
    {
        $this->addTwigFunction('checknewsletter', 'checkNewsletter');

    }

    public function checkNewsletter()
    {
        if($this->app['request']->getMethod() == 'POST'){

        }
    }

}
