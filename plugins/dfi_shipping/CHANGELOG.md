Changelog
=========

Version 1.1.0 – 20.08.2026
---------------------------

### Neue Features

* Automatische Übermittlung eines Pre-Ordine an DFI (`/v2/pad/addorder`) direkt
  bei Bestellabschluss (`DfiOrderHandler::submitPreOrder()`, Hook auf
  `simpleshop.Order.completeOrder`) - unabhängig von der (weiterhin manuellen)
  vollständigen Bestellübermittlung über den bestehenden
  "Bestellung erneut an DFI übertragen"-Button
  * Versandkosten/Akzise werden dabei live neu über DFI berechnet
  * Übertragungszeitstempel (`dfi_preorder_sent_at`) und Rohantwort
    (`dfi_preorder_response`) werden separat auf der Bestellung festgehalten
* `shipping_cost`, das an `/v2/addorder` übermittelt wird, entspricht jetzt
  dem tatsächlich berechneten Preis (manueller Wert bei manueller
  Konfiguration, DFI-Wert bei DFI-Berechnung) statt immer DFI's eigenem
  `shipping_cost` aus der Response; `fees` kommen weiterhin immer live von
  DFI, unabhängig vom manuell/DFI-Umschalter
* `calculatePrice()` fällt bei fehlender Adresse/PLZ oder API-Fehler auf die
  `general_costs`-Einstellung zurück statt auf die zuvor gespeicherte
  `shipping_costs` der Bestellung (die bei einer neuen Bestellung `0` ist)
* Bei einem fehlgeschlagenen `/v2/shipping`-Aufruf wird eine Benachrichtigung
  an `rex_prj_settings.tablet_mail_recipient` verschickt
  (`fragments/simpleshop/email/dfi_api_failure.php`)
* `Dfi::post()` erkennt jetzt auch fachliche Fehler in einer HTTP-200-Antwort
  (`{"status": false, "error": "..."}`) und wirft dafür eine `DfiException`,
  statt die leere Antwort stillschweigend als gültig zu behandeln
* `dfi_addorder_sent_at` wird nur noch bei tatsächlich erfolgreicher
  Übermittlung gesetzt, nicht mehr vor dem Übermittlungsversuch

### Neue Felder auf `rex_shop_order`

* `dfi_preorder_sent_at` (datetime)
* `dfi_preorder_response` (text)

Version 1.0.0 – 29.07.2026
---------------------------

### Neue Features

* DFI-API-Anbindung (`lib/Dfi.php`, `lib/DfiException.php`) via Guzzle, Token
  konfigurierbar über eine eigene Backend-Einstellungsseite
  (`simpleshop.DfiShipping.Settings`)
* Neues Shipping-Plugin `dfi_shipping` (`lib/DfiShipping.php`), berechnet
  Versandkosten live über `/v2/shipping`
  * SKU = `rex_shop_product_has_feature.code` bei Produkten mit Variante,
    sonst `rex_shop_product.ax_code`
  * Akzise (`fees` aus der API-Response) wird separat ausgewiesen
    (Checkout-Summary + Rechnungs-PDF), fließt aber weiterhin in die
    Gesamtsumme ein
  * Request-Level-Cache verhindert mehrfache/inkonsistente Live-Aufrufe
    innerhalb derselben Anfrage
  * Rohdaten der `/v2/shipping`-Response werden auf der Bestellung
    gespeichert (`shipping_api_response`)
* Automatische Bestellübermittlung an DFI (`/v2/addorder`) bei
  Bestellabschluss (`lib/DfiOrderHandler.php`, Hook auf
  `simpleshop.Order.completeOrder`)
  * Versandkosten/Akzise werden dabei aus der zuvor gespeicherten
    `shipping_api_response` übernommen, nicht neu berechnet (DFI liefert bei
    wiederholten Aufrufen leicht abweichende Werte)
  * Übertragungszeitstempel (`dfi_addorder_sent_at`) und Rohantwort
    (`dfi_addorder_response`) werden auf der Bestellung festgehalten
  * Fehlschläge werden geloggt (`rex_logger::logException`), blockieren aber
    nicht den Bestellabschluss
  * Tracking-Nummer aus der Response (`tracking`) wird in der
    Bestellbestätigungs-E-Mail an den Kunden angezeigt

### Neue Felder auf `rex_shop_order`

* `shipping_api_response` (text)
* `dfi_addorder_sent_at` (datetime)
* `dfi_addorder_response` (text)

### Neue Sprog-Wildcards

* `label.excise_fee` (DE: Akzise / IT: Accisa / EN: Excise Duty)
* `label.tracking_number` (DE: Tracking-Nummer / IT: Numero di tracciamento / EN: Tracking Number)
* `simpleshop.dfi_shipping` (DE: DFI Versand / IT: Spedizione DFI / EN: DFI Shipping)

### Bugfixes (allgemein, betrifft alle Shipping-Plugins)

* `Order::calculatePrices()` addierte die Versandkosten über
  `$shipping->getPrice()`, dessen Preis-Cache auf einer Objektinstanz liegt,
  die von `Order::getValue('shipping')` bei jedem Aufruf neu rekonstruiert
  wird (Kreatif-Model-Deserialisierung) – der Preis ging dadurch verloren und
  floss nicht in die Gesamtsumme ein. Fix: `calculatePrices()` verwendet
  jetzt den bereits persistierten `shipping_costs`-Wert.
* `Plugin::getByClass()` warf eine Exception, wenn eine in einer alten
  Session/Bestellung referenzierte Plugin-Klasse (z.B. nach Deaktivierung von
  `default_shipping`) nicht mehr registriert ist – führte zum Absturz beim
  Warenkorb-Zugriff. Fix: gibt jetzt `null` zurück, `Kreatif\Model::unprepareValue()`
  behandelt das analog zu "Klasse existiert nicht".
