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
                    
                    $token = ''. rand();
                    $query = sprintf("INSERT INTO %s (slug, datecreated, datechanged, datepublish, ownerid, status, email, token) VALUES ('%s', '%s', '%s', '%s', 1, 'held', '%s', '%s')", 
                    $this->config['table_name'],
                    'slug-news' . rand(),
                    date("Y-m-d h:m:i"),
                    date("Y-m-d h:m:i"),
                    date("Y-m-d h:m:i"),
                    $nemail,
                    $token
                    );
                    
                    if($this->app['db']->executeQuery($query)){

                        $body = $this->config['body'];
                        $body .= '<br><br><a href="'.$this->app['paths']->hosturl.'?email='.$nemail.'&token='.$token.'">'.$this->config['link'].'</a>'; 
                       
                        $message = \Swift_Message::newInstance()
                            ->setSubject($this->config['subject'])
                            ->setBody(strip_tags($body))
                            ->addPart($body, 'text/html')
                            ->setTo(array($nemail))
                            ->setFrom(array($this->config['email_from'] => $this->config['email_from_name']));
                        
                        $res = $this->app['mailer']->send($message);
                        
                        if($res){
                            $html = '<script> $(document).ready(function(){ alert("OK"); }); </script>';
                            return new \Twig_Markup($html, 'UTF-8');
                        }else{
                            $html = '<script> $(document).ready(function(){ alert("EMAIL ERROR"); }); </script>';
                            return new \Twig_Markup($html, 'UTF-8');                            
                        }
                                               
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
