<?php
// Newsletter Subscription Extension for Bolt

namespace Bolt\Extension\miguelavaqrod\newsletter;

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
            
            $nemail = $this->app['request']->get($this->config['newsletter_field']);
            
            if (filter_var($nemail, FILTER_VALIDATE_EMAIL)) {
                $html = '<script> $(document).ready(function(){ alert("'.$nemail.'"); }); </script>';
                return new \Twig_Markup($html, 'UTF-8');                
            }else{
                $html = '<script> $(document).ready(function(){ alert("ERROR"); }); </script>';
                return new \Twig_Markup($html, 'UTF-8');
            }

        }
    }

}
