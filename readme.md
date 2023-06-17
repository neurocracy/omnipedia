This is the top level project for [Omnipedia](https://omnipedia.app/). This
repository is and will likely remain private for security reasons, so this
readme is fairly minimal. Individual modules, themes, and other things can be
found in public repositories under this namespace.

----

# Major breaking changes

The following major version bumps indicate breaking changes:

* 5.x: Drupal core 9.5

* 6.x:

  * Drupal core 10.1.

  * Many compatibility/deprecation fixes to our modules and themes for Drupal 10/10.1, with a lot of them requiring major version increases.

  * Replaced [`Ambient-Impact/drupal-modules`](https://github.com/Ambient-Impact/drupal-modules) Git submodule with subtree split individual modules.

  * Uninstalled and removed the AdvAgg module as Drupal core 10.1 provides much of what we needed it for.

  * Uninstalled and removed [the deprecated Swift Mailer module](https://www.drupal.org/project/swiftmailer) and [the Mail System module](https://www.drupal.org/project/mailsystem) that it required.
