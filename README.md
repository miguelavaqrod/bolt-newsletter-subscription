Newsletter Subscription Extension
=================================

"Newsletter Susbscription" is a small extension to save registered emails in database.
Use it by simply placing the following in your template, near the subscription form:

    {{ checknewsletter }}

The extension will first email users with a link so thay can verify themselves. Once verified, the status of the database record will be "Published".

You are free to create the subscription form. Just take into account to set "POST" as form method and, obvioulsy, include the field name set in config.yml.

The default form field is "newsletter_email". You can customize it by editing the `config.yml` file.

    name: another_name

You can also customize the subject field of the email sent and a small sentence in the email's body.
