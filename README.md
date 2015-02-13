Newsletter Subscription Extension
=================================

"Newsletter Susbscription" is a small extension to save registered emails in database.
Use it by simply placing the following in your template, near the subscription form:

    {{ checknewsletter }}

You are free to create the subscription form. Just take into account to set POSt as action and, obvioulsy, include the field name set in config.yml.

The default form field is "newsletter_email". You can customize it by editing the `config.yml` file.

    name: another_name
