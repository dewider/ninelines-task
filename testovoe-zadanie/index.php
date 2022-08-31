<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Тестовое задание");
?><?$APPLICATION->IncludeComponent(
	"test:simplenews.comp",
	"",
	Array(
		"IBLOCK_ID" => "13",
		"NEWS_COUNT" => 1,
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => 36000000
	)
);?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>