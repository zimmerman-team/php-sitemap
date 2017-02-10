<?php

require_once 'Sitemap.php';

$oipaUrl = 'http://staging-odi.oipa.nl';
$odiUrl = 'http://staging-odi.zz-demos.net';

$siteMap = new Sitemap($odiUrl);

$siteMap->setPath('xmls/');

//Static URLs
$siteMap->addItem('/transparency', '1.0', 'monthly', 'Today');
$siteMap->addItem('/where-the-money-comes-from', '1.0', 'monthly', 'Today');
$siteMap->addItem('/where-the-money-goes/countries', '1.0', 'monthly', 'Today');
$siteMap->addItem('/where-the-money-goes/regions', '1.0', 'monthly', 'Today');
$siteMap->addItem('/where-the-money-goes/sectors', '1.0', 'monthly', 'Today');
$siteMap->addItem('/where-the-money-goes/receiving-organisations', '1.0', 'monthly', 'Today');
$siteMap->addItem('/projects', '1.0', 'monthly', 'Today');


//Dynamic URLs
$donorIDURL = $oipaUrl . '/api/transactions/aggregations/?format=json&group_by=provider_org&transaction_type=1&aggregations=activity_count';
$countryIDURL = $oipaUrl . '/api/activities/aggregations/?format=json&group_by=recipient_country&aggregations=count';
$regionIDURL = $oipaUrl . '/api/activities/aggregations/?format=json&group_by=recipient_region&aggregations=count';
$sectorIDURL = $oipaUrl . '/api/activities/aggregations/?format=json&group_by=sector&aggregations=count';
$receiverOrgURL = $odiUrl . '/oipa_api/transactions/aggregations/?format=json&reporting_organisation=GB-CHC-228248&group_by=receiver_org&aggregations=activity_count,disbursement&transaction_type=3';
$projectActivityURL = $odiUrl . '/oipa_api/activities/?format=json&reporting_organisation=GB-CHC-228248&page=1&page_size=400&fields=id&related_activity_type_not=2';
$programIDURL = $odiUrl . '/oipa_api/activities/?format=json&reporting_organisation=GB-CHC-228248&page=1&page_size=400&fields=id&related_activity_type=2';


// create curl resource 
$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, $donorIDURL);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$donorRawData = curl_exec($ch);

curl_setopt($ch, CURLOPT_URL, $countryIDURL);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$countryRawData = curl_exec($ch);

curl_setopt($ch, CURLOPT_URL, $regionIDURL);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$regionRawData = curl_exec($ch);

curl_setopt($ch, CURLOPT_URL, $sectorIDURL);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$sectorRawData = curl_exec($ch);

curl_setopt($ch, CURLOPT_URL, $receiverOrgURL);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$receiverOrgRawData = curl_exec($ch);

curl_setopt($ch, CURLOPT_URL, $projectActivityURL);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$projectActivityRawData = curl_exec($ch);

curl_setopt($ch, CURLOPT_URL, $programIDURL);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$programRawData = curl_exec($ch);

curl_close($ch);


#JSON parse and get IDs
$donorData = json_decode($donorRawData);
$countryData = json_decode($countryRawData);
$regionData = json_decode($regionRawData);
$sectorData = json_decode($sectorRawData);
$receiverOrgData = json_decode($receiverOrgRawData);
$projectActivityData = json_decode($projectActivityRawData);
$programData = json_decode($programRawData);

foreach ($donorData->results as $item) {
    $siteMap->addItem('/donor/' . $item->provider_org, '1.0', 'daily', 'Today');
}

foreach ($countryData->results as $item) {
    $siteMap->addItem('/country/' . $item->recipient_country->code, '1.0', 'daily', 'Today');
}

foreach ($regionData->results as $item) {
    $siteMap->addItem('/region/' . $item->recipient_region->code, '1.0', 'daily', 'Today');
}

foreach ($sectorData->results as $item) {
    $siteMap->addItem('/sector/' . $item->sector->code, '1.0', 'daily', 'Today');
}

foreach ($receiverOrgData->results as $item) {
    $siteMap->addItem('/partner/' . $item->receiver_org, '1.0', 'daily', 'Today');
}

foreach ($projectActivityData->results as $item) {
    $siteMap->addItem('/project/' . $item->id, '1.0', 'daily', 'Today');
}

foreach ($programData->results as $item) {
    $siteMap->addItem('/program/' . $item->id, '1.0', 'daily', 'Today');
}

$siteMap->createSitemapIndex($odiUrl . '/sitemap/', 'Today');