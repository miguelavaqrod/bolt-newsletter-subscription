Newsletter Subscription Extension
=================================

### ! Update v1.0 to v2.0
`table_name` config is no more used and replace by `contenttype`. Basically, you have to change line `table_name: bolt_subscribers` by `contenttype: subscribers` on your local `app/config/extensions/newsletter.miguelavaqrod.yml`.

---

**"Newsletter Subscription" is a small extension to save registered emails in database.**

### Requirements
- Bolt 3.x installation

### Installation
1. Login to your Bolt installation
2. Go to "View/Install Extensions" (Hover over "Extras" menu item)
3. Type `newsletter` into the input field
4. Click on the extension name
5. Click on "Browse Versions"
6. Click on "Install This Version" on the latest stable version
7. Right now you need to manually create the content type for this extension. As an example:
```YAML
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
```


### Details
Use it by simply placing the following twig function in your template, and make whatever use you need with returning values (see section below):
```Twig
    {{ checknewsletter() }}
```

When the twig function is processed, it will check request params and returned values if necessary. It will email users with a link so they can verify themselves. Once verified, the status of the database record will be "Published".

Returned values for twig function `checknewsletter()`:
```YAML
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
```

For example:
Inserting a script tag in the bottom of the page (jQuery ready version)...
```html
<script>
     var res = '{{ checknewsletter() }}';
     $(document).ready(function(){
        switch(res){
            case '0':
                alert('Verifying email sent. All OK.');
                break;
            case '1':
                alert('Error sending verifying email.');
                break;
            case '2':
                alert('Error saving subscriber email in DB.');
                break;
            case '3':
                alert('Subscriber email already registered.');
                break;
            case '20':
                alert('Your Email has been successfully removed from our system.');
                break;
                alert('Subscriber email not valid.');
                break;
            case '10':
                alert('Subscriber email verified.');
                break;
            case '11':
                alert('Error saving verified email info to DB.');
                break;
            case '12':
                alert('Error in subscriber email or token sent for verifying.');
                break;
        }
     });
</script>
```

Or add a twig section :
```Twig
{% set checknewsletter = checknewsletter() %}
{% if checknewsletter == '0' %}
    <p class="callout warning">Verifying email sent. All OK.</p>
{% elseif checknewsletter == '1' or checknewsletter == '2' or checknewsletter == '11' or checknewsletter == '12' %}
    <p class="callout alert">Getting a problem, please <a href="/contact">contact us</a>.</p>
{% elseif checknewsletter == '3' %}
    <p class="callout secondary">Subscriber email already registered.</p>
{% elseif checknewsletter == '10' %}
    <p class="callout success">Thanks, you'll receive our next news.</p>
{% elseif checknewsletter == '20' %}
    <p class="callout success">Your email has been successfully removed from our system.</p>
{% endif %}
```

You are free to create the subscription form. Just take into account to set "POST" as form method and, obviously, include the field name set in config.yml as an `input` form control. By example :
```html
<form method="post" action="#">
  <input type="email" value="" name="newsletter_email" placeholder="Your email…">
  <button type="submit">Subscribe</button>
</form>
```

Be sure to **setup email smtp settings in your Bolt config file**, so the extension can send verifying emails.

### Configuration
The default form field is "newsletter_email". You can customize it by editing the `config.yml` file. You can also customize the subject field of the email sent and a small sentence in the email's body.
```YAML
contenttype: subscribers           # Entity name where subscribers are saved
newsletter_field: newsletter_email # HTTP GET Parameter = Form field name

email_from: info@mywebsite.com     # Sender's email for confirmation email
email_from_name: My Website        # Sender's name for confirmation email

subject: Email from my website     # Subject for confirmation email
body: Please follow the link to verify your email.
link: Click Here                   # Link label for confirmation email

path: subscribe 				   # Path to page that contains your subscription signup
```

### Unsubscribe
You could enable unsubscription with this extension. When the twig function `checknewsletter()` is processed, it checks if request method is `GET` and if there are valid params `email`, `token` and `unsubscribe=yes`. So if you add a link to your template (or your email), with GET params `email=xxx&token=yyy&unsubscribe=yes`, users could unsubscribe themself.
