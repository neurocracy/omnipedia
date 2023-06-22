This is the top level project for [Omnipedia](https://omnipedia.app/). This
repository is currently private for security reasons but may be open sourced at
a later date in part or in full after a thorough review of anything that could
be a security risk. Individual modules, themes, and other things can be found
in public repositories under this namespace.

----

# Major breaking changes

The following major version bumps indicate breaking changes:

* 5.x:

  * Drupal core 9.5.

  * Replaced [`Ambient-Impact/drupal-modules`](https://github.com/Ambient-Impact/drupal-modules) Git submodule with subtree split individual modules.

  * First pass at upgrading our code to be compatible with Drupal 10.

* 6.x:

  * Drupal core 10.0.

  * The following modules have been temporarily switched over to forks for Drupal 10 support:

    * [Development Environment](https://www.drupal.org/project/development_environment/issues/3286975)

    * [Image Field Caption](https://www.drupal.org/project/image_field_caption/issues/3355337)

    * [Markdown](https://www.drupal.org/project/markdown/issues/3288447)

    * [Paranoia](https://www.drupal.org/project/paranoia/issues/3289009)

  * The [Rules module](https://www.drupal.org/project/rules) has been switched from [3.0.0-alpha7](https://www.drupal.org/project/rules/releases/8.x-3.0-alpha7) to [3.x-dev@dev](https://www.drupal.org/project/rules/releases/8.x-3.x-dev) for Drupal 10 support; the patch for [Support upcasting entity IDs to full entity contexts [#2800749]](https://www.drupal.org/project/rules/issues/2800749#comment-14332836) has been removed because it's in the dev release.

  * Uninstalled [the deprecated Swift Mailer module](https://www.drupal.org/project/swiftmailer) and [the Mail System module](https://www.drupal.org/project/mailsystem) that it required. This was only ever used for [Commerce](https://www.drupal.org/project/commerce) emails, which we haven't used for some time.

* 7.x:

  * Drupal core 10.1.

  * Uninstalled and removed the AdvAgg module as [Drupal core 10.1 provides much of what we needed it for](https://www.drupal.org/project/advagg/issues/3308099#comment-15025561).
