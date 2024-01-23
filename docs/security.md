# Security

We go above and beyond what most projects would consider "good enough" to make
it very difficult for anyone to compromise our work and hope that we can
demonstrate the implementation of many security best practices.

## Reporting

If you find a security vulnerability that we've missed, **do not open an
issue**, but [contact us privately](https://omnipedia.app/contact) instead.

## Drupal

Drupal as a project has [a very proactive security
team](https://www.drupal.org/features/security) that publishes security
advisories and co-ordinates fixes every week.

### Elevated user roles restricted in production

On the production (live) site, all user roles that have access to any
adminstration areas have permissions limited to the minimum required to edit
site content and administer user accounts; if one of these accounts is
compromised, this limits the damage someone can do.

No role is granted the ability to change permissions. Any changes to permissions
are made in local dev environments and pushed to the site as configuration,
which the site automatically imports as part of the deployment process.

Additionally, no user role is marked as an administrator to prevent accidentally
granting new permissions automatically when installing a new Drupal module.

### Two factor authentication required for elevated roles

User roles that have access to any administration areas have two factor
authentication as an enforced requirement so even if a password is compromised,
it alone can't be used to log in.

### Administrator user blocked

The administrator user created by Drupal core with a user ID of `1` - which
always has unconditional full access to every possible administration page or
configuration - is blocked in every environment; additionally, we have
mechanisms in place that prevent unblocking of that account.

### UI modules uninstalled in production

Various modules that provide a user interface to alter how content is output are
uninstalled in production, including the Drupal core Field UI and Views UI
modules. Any changes that need to be made to content output are made in local
dev environments, exported as configuration, and pushed to the production site
which automatically imports any updated configuration during the deployment
process.

## Content-Security-Policy (CSP)

We implement a fairly restrictive [Content-Security-Policy
header](https://developer.mozilla.org/docs/Web/HTTP/CSP) via [the Drupal module
of the same name](https://www.drupal.org/project/csp), so that in the unlikely
event someone finds a way to inject malicious code into our database that gets
sent to our users, their browsers will refuse to run most or all of it. See [our
Mozilla Observatory scan
results](https://observatory.mozilla.org/analyze/omnipedia.app).

## Session cookies

We have [fairly restrictive session
cookies](https://scotthelme.co.uk/tough-cookies/) to make it very difficult for
them to be leaked and used by anyone other than the user they're intended for.
