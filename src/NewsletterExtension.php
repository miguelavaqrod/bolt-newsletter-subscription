<?php

namespace Bolt\Extension\Miguelavaqrod\Newsletter;

use Bolt\Extension\SimpleExtension;
use Bolt\Storage\Entity\Content;

/**
 * Newsletter extension class.
 *
 * RETURNED VALUES:
 *
 * 0: Verifying email sent. All OK.
 * 1: Error sending email
 * 2: Error saving subscriber email in DB
 * 3: Subscriber email already registered
 * 20: Unsubscribed email

 * 10: Subscriber email verified
 * 11: Error saving verified email info to DB
 * 12: Error in subscriber email or token sent for verifying
 *
 * 99: Subscriber email not valid
 */
class NewsletterExtension extends SimpleExtension
{
    /**
     * {@inheritdoc}
     */
    protected function registerTwigFunctions()
    {
        return [
            'checknewsletter' => 'checkNewsletterFunction'
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function checkNewsletterFunction()
    {
        $app         = $this->getContainer();
        $config      = $this->getConfig();
        $contenttype = $config['contenttype'];

        // Check if page with email and token params
        if (   $app['request']->getMethod() == 'GET'
            && $app['request']->get('email') != ''
            && $app['request']->get('token') != '' ) {

            if ( empty($contenttype) ) {
                $app['logger.system']->error("Newsletter Extension has new missing config requirements : contenttype", ['event' => 'extensions']);
                return;
            }

            // Not need to escape thanks to Doctrine
            // @see http://stackoverflow.com/questions/8463711/do-i-need-to-escape-values-when-using-entityrepositoryfindby
            $nemail = $app['request']->get('email');
            $token  = $app['request']->get('token');

            try {
                $repo = $app['storage']->getRepository($contenttype);
            }
            catch (\Exception $e) {
                $app['logger.system']->error(sprintf("Newsletter Extension can not query repository : %s", $contenttype), ['event' => 'extensions']);
                return;
            }

            $result = $repo->findOneBy([
                'email' => $nemail,
                'token' => $token
            ]);

            if (   $result !== false // Record found
                && ! empty($result->getId() ) ) {

                if ($app['request']->get('unsubscribe') == 'yes') { // Unsubscribe requested - delete the record.

                    $result = $repo->delete($result);

                    return new \Twig_Markup('20', 'UTF-8');
                }

                $result->setStatus('published');

                if ( $repo->save($result) !== false ) {
                    // Subscriber email verified
                    return new \Twig_Markup('10', 'UTF-8');
                }
                else {
                    // Error saving verified email info to DB
                    return new \Twig_Markup('11', 'UTF-8');
                }
            }
            else {
                // Error in subscriber email or token sent for verifying
                return new \Twig_Markup('12', 'UTF-8');
            }
        }
        elseif ( $app['request']->getMethod() == 'POST' ) { // If new subscriber is being added

            $nemail = $app['request']->get($config['newsletter_field'] );

            if ( empty($nemail) ) {
                return;
            }
            elseif ( empty($contenttype) ) {
                $app['logger.system']->error("Newsletter Extension has new missing config requirements : contenttype", ['event' => 'extensions']);
                return;
            }
            elseif( filter_var($nemail, FILTER_VALIDATE_EMAIL) ) {
                try {
                    $repo = $app['storage']->getRepository($contenttype);
                }
                catch (\Exception $e) {
                    $app['logger.system']->error(sprintf("Newsletter Extension can not query repository : %s", $contenttype), ['event' => 'extensions']);
                    return;
                }

                $result = $repo->findOneBy([
                    'email' => $nemail
                ]);

                 if (  $result != false && ! empty($result->getId() ) ) {
                    if ($result->status == 'published') {
                        // Subscriber email already registered
                        return new \Twig_Markup('3', 'UTF-8');
                    } else {
                        // Subscriber already exists but is not verified. Delete and Recreate.
                        $result = $repo->delete($result);
                    }
                    
                }
                
                    $token = '' . rand();
                    $repo  = $app['storage']->getRepository($contenttype);
                    $subscription = new Content([
                        'title'  => $nemail,
                        'slug'   => 'slug-news' . rand(),
                        'email'  => $nemail,
                        'token'  => $token,
                        'status' => 'held'
                    ]);

                    if ( $repo->save($subscription) !== false ) {
                        $body  = $config['body'];
                        $body .= '<br><br><a href="' . $app['paths']['rooturl'] . $config['path'] . '?email=' . $nemail . '&token=' . $token . '">' . $config['link'] . '</a>';

                        $message = \Swift_Message::newInstance()
                            ->setSubject($config['subject'] )
                            ->setBody(strip_tags($body) )
                            ->addPart($body, 'text/html')
                            ->setTo(array($nemail) )
                            ->setFrom(array($config['email_from'] => $config['email_from_name']) );

                        $res = $app['mailer']->send($message);

                        if ( $res ) {
                            // Verifying email sent. All OK.
                            return new \Twig_Markup('0', 'UTF-8');
                        }
                        else {
                            // Error sending verifying email.
                            return new \Twig_Markup('1', 'UTF-8');
                        }
                    }
                    else {
                        // Error saving subscriber email in DB
                        return new \Twig_Markup('2', 'UTF-8');
                    }
                }
            }
            else {
                // Subscriber email not valid
                return new \Twig_Markup('99', 'UTF-8');
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDisplayName()
    {
        return 'Newsletter Subscription';
    }
}
