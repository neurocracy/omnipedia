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

* 8.x:

  * Increased major versions of the following Omnipedia modules:

    * [`omnipedia_access`](https://github.com/neurocracy/drupal-omnipedia-access) 4.x to 5.x.

    * [`omnipedia_block`](https://github.com/neurocracy/drupal-omnipedia-block) 5.x to 6.x.

    * [`omnipedia_user`](https://github.com/neurocracy/drupal-omnipedia-user) 5.x to 6.x.

  * [Uninstalled and removed `omnipedia_commerce` and all Commerce modules](https://github.com/neurocracy/omnipedia/issues/4):

    * Uninstalled the [`omnipedia_commerce` module](https://github.com/neurocracy/drupal-omnipedia-commerce).

    * Uninstalled the [Permissions by Term module](https://www.drupal.org/project/permissions_by_term); this was used for access control for the season pass; due to multiple issues with the quality of the module's code and unnecessary complications it introduced, we've decided to completely remove it as well.

    * Uninstalled all [Commerce Core modules](https://www.drupal.org/project/commerce).

    * Uninstalled the [Commerce Cart Redirection module](https://www.drupal.org/project/commerce_cart_redirection).
    * Uninstalled the [Commerce PayPal module](https://www.drupal.org/project/commerce_paypal).

    * Uninstalled the [Commerce Product Limits module](https://www.drupal.org/project/commerce_product_limits).

    * Uninstalled the [Commerce Stripe module](https://www.drupal.org/project/commerce_stripe).

    * Uninstalled their dependencies:

      * The [Address module](https://www.drupal.org/project/address).

      * The [Entity API module](https://www.drupal.org/project/entity).

      * The [Entity Reference Revisions module](https://www.drupal.org/project/entity_reference_revisions).

      * The [Inline Entity Form module](https://www.drupal.org/project/inline_entity_form).

      * The [Profile module](https://www.drupal.org/project/profile).

      * The [State Machine module](https://www.drupal.org/project/state_machine).

    * Removed all their related configuration, fields, entity types, permissions, and so on.
