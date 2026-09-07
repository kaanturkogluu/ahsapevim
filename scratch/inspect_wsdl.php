<?php
$content = file_get_contents('scratch_wsdl.xml');
$xml = simplexml_load_string($content);

$types = $xml->xpath('//*[local-name()="complexType"]');
foreach ($types as $t) {
    $tName = (string)$t['name'];
    if (in_array($tName, ['ShippingDeliveryItemDetailVO', 'shippingDeliveryItemDetailVO'])) {
        echo "=== ComplexType: $tName ===" . PHP_EOL;
        foreach ($t->xpath('.//*[local-name()="element"]') as $el) {
            echo "  " . $el['name'] . " (" . $el['type'] . ")" . PHP_EOL;
        }
    }
}
