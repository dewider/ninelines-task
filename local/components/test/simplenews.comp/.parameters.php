<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$arComponentParameters = array(
    "PARAMETERS" => array(
        "IBLOCK_ID" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("PAR_IBLOCK"),
			"TYPE" => "STRING"
		),
        "NEWS_COUNT" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("PAR_NEWS_COUNT"),
			"TYPE" => "STRING",
			"DEFAULT" => 5
		),
		"CACHE_TIME" => array("DEFAULT" => 36000000),
    )
);
?>