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
        if ($this->app['config']->getWhichEnd() == 'frontend') {
            $this->addTwigFunction('checknewsletter', 'checkNewsletter');
        }
    }

    public function checkNewsletter()
    {                
        if($this->app['request']->getMethod() == 'POST'){
            
            $nemail = $this->app['request']->get($this->config['newsletter_field']);
            
            if (filter_var($nemail, FILTER_VALIDATE_EMAIL)) {
                         
                $query = sprintf("SELECT id FROM %s WHERE email = '%s';", $this->config['table_name'], $nemail);
                $id = $this->app['db']->executeQuery($query)->fetch();
                if (!empty($id['id'])) {
                    
                    $html = '<script> $(document).ready(function(){ alert("ALREADY REGISTERED"); }); </script>';
                    return new \Twig_Markup($html, 'UTF-8');
                    
                } else {
                    
                    $query = sprintf("INSERT INTO %s (slug, datecreated, datechanged, datepublish, ownerid, status, email) VALUES ('%s', '%s', '%s', '%s', 1, 'held', '%s')", 
                    $this->config['table_name'],
                    'slug-news' . rand(),
                    date("Y-m-d h:m:i"),
                    date("Y-m-d h:m:i"),
                    date("Y-m-d h:m:i"),
                    $nemail
                    );
                    
                    if($this->app['db']->executeQuery($query)){
                        
                        $html = '<script> $(document).ready(function(){ alert("OK"); }); </script>';
                        return new \Twig_Markup($html, 'UTF-8');  
                        
                    }else{
                        
                        $html = '<script> $(document).ready(function(){ alert("DB ERROR"); }); </script>';
                        return new \Twig_Markup($html, 'UTF-8'); 
                        
                    }
                }
                             
            }else{
                
                $html = '<script> $(document).ready(function(){ alert("ERROR"); }); </script>';
                return new \Twig_Markup($html, 'UTF-8');
                
            }

        }
    }

}
