=== Degriz EU Guarantee ===
Contributors: degriz
Tags: woocommerce, eu guarantee, legal guarantee, consumer rights, notice
Requires at least: 6.2
Tested up to: 7.1
Requires PHP: 8.0
Stable tag: 1.0.1
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

The official EU notice on the legal guarantee of conformity (Implementing Regulation (EU) 2025/1960) for WooCommerce: a button that opens it in a dialog in the footer, on the product page, in the cart and above Place order, and the PDF attached to the order confirmation.

== Description ==

Since 27 September 2026 Commission Implementing Regulation (EU) 2025/1960 requires every trader who sells goods to consumers in the EU to show the harmonised notice on the legal guarantee of conformity. This plugin, a port of the Magelan EuGuarantee module, shows the official notice.

* The official files of the European Commission, unchanged, in all 24 official languages (the picture and the PDF); the one that matches the site language is used, English otherwise. The regulation fixes the text, the graphics and the colours, so nothing is edited.
* A button "Your rights under the legal guarantee" opens the whole notice in a dialog (the example the guidelines give for a website), with a link to the Commission's PDF. On a phone, and without JavaScript, the button opens the PDF, which can be zoomed. Alternatively a text link or the picture itself.
* Where: the footer, the product page, the cart and right above the Place order button, each a separate switch. A link to the page the QR code of the notice points at always comes with it.
* The PDF is attached to the order confirmation e-mail sent to the customer.

Disclaimer: the notice is the official EU text (Commission Implementing Regulation (EU) 2025/1960) as of 19 November 2026 and is provided for information only. You alone are responsible for the correctness, completeness and legal compliance of what your shop shows. This is not legal advice. The plugin is provided "as is", without warranty of any kind, and its author is not liable for any damage arising from its use.

Not ported: the GARAN label for producer durability guarantees, the commercial guarantee fields, the customer group exclusion and the product listing note of the Magento module.

Compatible with HPOS and the Cart and Checkout blocks. Slovenian translation included (sl_SI).

== Installation ==

1. Upload the plugin to wp-content/plugins and activate it (WooCommerce must be active).
2. WooCommerce > EU guarantee settings: choose where the notice appears.

== Changelog ==

= 1.0.1 =
* Liability disclaimer in the settings, the README and the readme; the GPL licence text added. No functional change.

= 1.0.0 =
* First release, ported from the Magelan module for Magento 2.

== Upgrade Notice ==

= 1.0.1 =
Disclaimer added; no functional change.

= 1.0.0 =
First release.
