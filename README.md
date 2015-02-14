Newsletter Subscription Extension
=================================

"Newsletter Susbscription" is a small extension to save registered emails in database.
Use it by simply placing the following in your template, for example, inside a `script` tag at the bottom of the page, and make whatever use you need with returning values:

    {{ checknewsletter }}

The extension will first email users with a link so thay can verify themselves. Once verified, the status of the database record will be "Published".

You are free to create the subscription form. Just take into account to set "POST" as form method and, obvioulsy, include the field name set in config.yml as an `input` form control.

The default form field is "newsletter_email". You can customize it by editing the `config.yml` file.

    newsletter_field: another_name

You can also customize the subject field of the email sent and a small sentence in the email's body.

----

Be sure to setup email smtp settings in your Bolt config file, so the extension can send verifying emails.

Right now you need to manually create the content type for this extension. As an example:



    subscribers:
        name: Subscribers
        singular_name: Subscriber
        fields:
            email:
                label: Email
                type: text
                group: Content
            token:
                label: Token
                type: text
                readonly: true
                class: narrow
        viewless: true
        default_status: held
        searchable: false
        show_on_dashboard: false
        
----
    
Returned values:

    When inserting a new subscriber
        0: Verifying email sent. All OK
        1: Error sending verifying email
        2: Error saving subscriber email in DB
        3: Subscriber email already registered
        99: Subscriber email not valid
        
    When verifying an email
        10: Subscriber email verified
        11: Error saving verified email info to DB
        12: Error in subscriber email or token sent for verifying 

This extension does not force any form style or similar.
It lets you create the email subscription form freely. You just need to include the field set in `config.yml`.
Additionally, it just inform you about the status of the action using raw numeric strings (returned values), so you later can do whatever you want with them (for example, compare them and make use of modals to inform the user, but you are free to make whatever you want).

For example:
Inserting a script tag in the bottom of the page (jQuery ready version)...

    <script>
     var res = '{{ checknewsletter() }}';
     $(document).ready(function(){
        switch(res){
            case '0':
                alert('Verification email sent.');
                break;
            case '1':
                alert('Error sending verification email.');
                break;
            case '2':
                alert('Error saving subscriber to database.');
                break;
            case '3':
                alert('Subscriber email already registered.');
                break;
            case '99':
                alert('Subscriber email not valid.');
                break;
            case '10':
                alert('Subscriber email verified.');
                break;
            case '11':
                alert('Error saving verified subscriber to database.');
                break;
            case '12':
                alert('Error in subscriber email or token sent for verifying.');
                break;
        }
     });
    </script>
