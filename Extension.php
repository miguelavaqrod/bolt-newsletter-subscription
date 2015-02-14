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
        if($this->app['request']->getMethod() == 'GET' && $this->app['request']->get('email') != '' && $this->app['request']->get('token') != ''){
        
            $nemail = $this->app['request']->get('email');
            $token = $this->app['request']->get('token');
            $query = sprintf("SELECT id FROM %s WHERE email = '%s' AND token = '%s';", $this->config['table_name'], $nemail, $token);
            $id = $this->app['db']->executeQuery($query)->fetch();
            if (!empty($id['id'])) {
                
                $query = sprintf("UPDATE %s SET datepublish = '%s', status = 'published' WHERE id = %s", $this->config['table_name'], date("Y-m-d h:m:i"), $id['id']);
                if($this->app['db']->executeQuery($query)){
                    
                    $html = '<script> $(document).ready(function(){ alert("EMAIL VERIFIED"); }); </script>';
                    return new \Twig_Markup($html, 'UTF-8');
                                    
                }else{
                    
                    $html = '<script> $(document).ready(function(){ alert("DB ERROR UPDATING"); }); </script>';
                    return new \Twig_Markup($html, 'UTF-8');                    
                }
                
            }else{
                
                $html = '<script> $(document).ready(function(){ alert("EMAIL OR TOKEN ERROR"); }); </script>';
                return new \Twig_Markup($html, 'UTF-8');
                                
            }          
        
        }elseif($this->app['request']->getMethod() == 'POST'){
            
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
                        $body .= '<br><br><a href="'.$this->app['paths']['rooturl'].'?email='.$nemail.'&token='.$token.'">'.$this->config['link'].'</a>'; 
                       
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
