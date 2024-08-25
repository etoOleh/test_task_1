<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle("Тестовое 1"); ?><?$APPLICATION->IncludeComponent(
	"orm.local:example.orm",
	"",
	Array(
		"CACHE_FILTER" => "N",
		"CACHE_TIME" => "36000000",
		"CACHE_TYPE" => "A"
	)
);?><?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>