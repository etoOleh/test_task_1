<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle("Тестовое 2"); ?><?$APPLICATION->IncludeComponent(
	"orm.local:job.stats",
	"",
	Array(
		"CACHE_FILTER" => "N",
		"CACHE_TIME" => "36000000",
		"CACHE_TYPE" => "A"
	)
);?><?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>