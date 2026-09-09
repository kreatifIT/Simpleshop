Changelog
=========

Version 1.3.0 – 09.09.2026
---------------------------

### Neue Features

* Eigenes Feld `notification_recipients` in den DFI-Einstellungen
  (Schnittstellen-Einstellungen) für die Empfänger der
  API-Fehlerbenachrichtigung - mehrere Adressen kommagetrennt möglich.
  Ersetzt die bisherige Nutzung von `rex_prj_settings.tablet_mail_recipient`
  (`DfiShipping::notifyApiFailure()`), die shop-übergreifend für mehrere
  Zwecke verwendet wird und daher für DFI-spezifische Fehler nicht mehr
  passend war.

Version 1.2.0 – 21.08.2026
---------------------------

### Neue Features

* Der manuelle "Bestellung an DFI übertragen"-Button im Backend
  (`order_functions.php`) überträgt jetzt ebenfalls als Pre-Ordine über
  `/v2/pad/addorder` (`DfiOrderHandler::submitPreOrder()`) statt über
  `/v2/addorder` - Aktion umbenannt zu `resend_dfi_preorder`
* `DfiOrderHandler::buildPayload()` berechnet jetzt korrekte Netto-Werte für
  die DFI-Übermittlung (Produkte + Versandkosten + Fees), da im Shop alle
  Preise brutto gespeichert sind:
  * Neues Feld `vat_rate` auf `rex_prj_country` (Backend-Länderliste,
    editierbar pro Land), per Migration einmalig befüllt mit den
    EU-27-Standardsätzen, `0%` für alle Nicht-EU-Länder (steuerfreier
    Export); überschreibt bei einem Reinstall keine bereits gesetzten Werte
  * MwSt.-Satz wird anhand des Landes der **Rechnungsadresse** ermittelt
    (`Order::getInvoiceAddress()`), nicht der Versandadresse; Fallback `22%`
    falls kein Land/keine Rate hinterlegt ist
  * `total_without_tax` pro Produkt sowie `shipping_cost`/`fees` werden mit
    diesem Satz aus den (unveränderten) Bruttowerten herausgerechnet -
    unabhängig vom individuell am Produkt hinterlegten Steuersatz, da alle
    Preise als einheitlich brutto-22%-basiert angenommen werden
  * `vat` wird als Restgröße (`total - Summe der Nettobeträge`) berechnet,
    nicht mehr aus `Order::getValue('taxes')` übernommen - garantiert, dass
    `total = Summe(netto) + vat` für DFI's eigenen Abgleich exakt aufgeht
  * Alle Netto-/MwSt.-Werte werden auf 3 statt 2 Nachkommastellen gerundet -
    verhindert einen Rundungsfehler auf DFI-Seite (z.B. 46,00€ als 45,99€
    hinterlegt), der entsteht, wenn DFI die auf 2 Nachkommastellen
    gerundeten Einzelwerte wieder aufsummiert

### Bugfixes

* `dfi_addorder_sent_at`/`dfi_preorder_sent_at` hatten `only_empty => 0`
  (YForm-Datestamp), wodurch das Feld bei **jedem** Backend-Speichern der
  Bestellung automatisch auf "jetzt" gesetzt wurde - unabhängig von einer
  tatsächlichen DFI-Übertragung. Auf `only_empty => 2` geändert, Felder
  werden jetzt ausschließlich von `DfiOrderHandler` gesteuert.

### Neue Felder auf `rex_prj_country`

* `vat_rate` (decimal(5,2), Default `22`)

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
