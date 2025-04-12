# Major changes

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

    * ~~[Markdown](https://www.drupal.org/project/markdown/issues/3288447)~~ Now has a Drupal 10 release.

    * ~~[Paranoia](https://www.drupal.org/project/paranoia/issues/3289009)~~ Now has a Drupal 10 release.

  * The [Rules module](https://www.drupal.org/project/rules) has been switched from [3.0.0-alpha7](https://www.drupal.org/project/rules/releases/8.x-3.0-alpha7) to [3.x-dev@dev](https://www.drupal.org/project/rules/releases/8.x-3.x-dev) for Drupal 10 support; the patch for [Support upcasting entity IDs to full entity contexts [#2800749]](https://www.drupal.org/project/rules/issues/2800749#comment-14332836) has been removed because it's in the dev release.

  * Uninstalled [the deprecated Swift Mailer module](https://www.drupal.org/project/swiftmailer) and [the Mail System module](https://www.drupal.org/project/mailsystem) that it required. This was only ever used for [Commerce](https://www.drupal.org/project/commerce) emails, which we haven't used for some time.

* 7.x:

  * Drupal core 10.1.

  * Uninstalled and removed the AdvAgg module as [Drupal core 10.1 provides much of what we needed it for](https://www.drupal.org/project/advagg/issues/3308099#comment-15025561).

* 8.x:

  * Increased major versions of the following Omnipedia modules:

    * [`omnipedia_access`](https://gitlab.com/neurocracy/omnipedia/modules/omnipedia-access) 4.x to 5.x.

    * [`omnipedia_block`](https://gitlab.com/neurocracy/omnipedia/modules/omnipedia-block) 5.x to 6.x.

    * [`omnipedia_user`](https://gitlab.com/neurocracy/omnipedia/modules/omnipedia-user) 5.x to 6.x.

  * [Uninstalled and removed `omnipedia_commerce` and all Commerce modules](https://gitlab.com/neurocracy/omnipedia/omnipedia/-/issues/4):

    * Uninstalled the [`omnipedia_commerce` module](https://gitlab.com/neurocracy/omnipedia/modules/omnipedia-commerce).

    * Uninstalled the [Permissions by Term module](https://www.drupal.org/project/permissions_by_term); this was used for access control for the season pass; due to multiple issues with the quality of the module's code and unnecessary complications it introduced, we've decided to completely remove it as well; a sampling of the worst issues:

      * [Permissions by Term - Moderately critical - Access bypass - SA-CONTRIB-2022-055](https://www.drupal.org/sa-contrib-2022-055): a security issue that sat unfixed publicly in the issue queue for months until the security team got involved.

      * [Regression updating to 3.1.22 for Drupal 9.x using loadTemplate from twig [#3354478]](https://www.drupal.org/project/permissions_by_term/issues/3354478#comment-15058868): a badly thought out and implemented hack that somehow made it into a stable release for potentially thousands of sites that resulted in a fatal error when trying to view the node edit form, rendering content uneditable; the linked comment in the issue goes into more detail.

      * [Node Access Rebuilt unnecessarily if no change to user terms. [#3170389]](https://www.drupal.org/project/permissions_by_term/issues/3170389#comment-14242763)

      * [Permissions by Entity PHP notice for Commerce Product entities [#3231188]](https://www.drupal.org/project/permissions_by_term/issues/3231188#comment-14209558)

      * [Respect the Require all terms granted and Permission mode settings in Permission by entity module [#3072171]](https://www.drupal.org/project/permissions_by_term/issues/3072171#comment-14209554)

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

* 9.x:

  * This is the culimation of nearly a year of work that was done in parallel with the main branches to port Omnipedia to [DigitalOcean's App Platform](https://www.digitalocean.com/products/app-platform). It required significant engineering to rework the entire front-end and back-end build processes to fully automate them, because the file system on App Platform is ephemeral with each deployment occurring on a completely fresh file system. To get a sense of the changes involved, see [the comparison between the `8.x` and `8.x_digitalocean` branches](../../compare/8.x...8.x_digitalocean). The work occurred in these branches:

    * [`4.x_digitalocean`](../../tree/4.x_digitalocean)

    * [`5.x_digitalocean`](../../tree/5.x_digitalocean)

    * [`6.x_digitalocean`](../../tree/6.x_digitalocean)

    * [`7.x_digitalocean`](../../tree/7.x_digitalocean)

    * [`8.x_digitalocean`](../../tree/8.x_digitalocean)

  * All uploaded files and generated image styles are hosted on [DigitalOcean Spaces](https://www.digitalocean.com/products/spaces), their S3-compatible object storage; this is necessary due to the emphemeral nature of the App Platform file system, meaning they would get wiped on the next deploy if stored locally. On the Drupal side, this is implemented using the [S3 File System module](https://www.drupal.org/project/s3fs).

  * The entire Composer and Yarn install process is now automated and installs everything on deploy; all front-end CSS and other assets are built via Yarn and Webpack during this process.

  * Email is now sent out using a third-party email service as [DigitalOcean blocks sending of email directly from their Droplets or App Platform to prevent use of their services for spam](https://docs.digitalocean.com/support/why-is-smtp-blocked/).

  * [Session cookie security has been hardened significantly](https://scotthelme.co.uk/tough-cookies/), along with many other security improvements.

  * The web root directory has been renamed from `drupal` to `web` to be more in line with standard Drupal practices.

* 10.x:

  * Increased [`drupal/ambientimpact_ux` to 2.x](https://github.com/Ambient-Impact/drupal-ambientimpact-ux/tree/2.x) from 1.x.

  * Increased [`omnipedia_attached_data` to 5.x](https://gitlab.com/neurocracy/omnipedia/modules/omnipedia-attached-data/tree/5.x) from 4.x.

  * Increased [`omnipedia_content` to 7.x](https://gitlab.com/neurocracy/omnipedia/modules/omnipedia-content/tree/7.x) from 6.x.

  * Increased [`omnipedia_site_theme` to 8.x](https://gitlab.com/neurocracy/omnipedia/modules/omnipedia-site-theme/tree/8.x) from 7.x.
