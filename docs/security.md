# Security

We go above and beyond what most projects would consider "good enough" to make
it difficult for anyone to compromise our work and thus endanger not only us,
but our users, and hope that we can demonstrate the implementation of many
security best practices.

## Reporting

If you find a security vulnerability that we've missed, **do not open an
issue**, but [contact us privately](https://omnipedia.app/contact) instead.

## Drupal

Drupal as a project has [a very proactive security
team](https://www.drupal.org/features/security) that regularly publishes
security advisories and co-ordinates fixes.

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
