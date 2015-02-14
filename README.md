Newsletter Subscription Extension
=================================

"Newsletter Susbscription" is a small extension to save registered emails in database.
Use it by simply placing the following in your template, inside a `script` tag at the bottom of the page, and make whatever use you need with returning values:

    {{ checknewsletter }}

The extension will first email users with a link so thay can verify themselves. Once verified, the status of the database record will be "Published".

You are free to create the subscription form. Just take into account to set "POST" as form method and, obvioulsy, include the field name set in config.yml.

The default form field is "newsletter_email". You can customize it by editing the `config.yml` file.

    name: another_name

You can also customize the subject field of the email sent and a small sentence in the email's body.

----

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
        default_status: published
        searchable: false
        show_on_dashboard: false 
    
Returned values:

    When inserting a new subscriber
        0: Email sent. All OK
        1: Error sending email
        2: Error saving email in DB
        3: Email already registered
        99: Email not valid
    When verifying an email
        10: Email verified
        11: Error saving verified email info to DB
        12: Error in email or token sent for verifying 

This extension does not force any form style or similar.
It let you create the email subscription form freely. You just need to include the field set in `config.yml`.
Additionally, it just inform you about the status of the function using raw strings, so you later can do whatever you want with them (for example, make use of madals to inform the user, but you are free to make whatever you want).
