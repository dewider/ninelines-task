<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
$this->setFrameMode(true);
?>

<div class="container-fluid">
    <div class="row">
        <? foreach ($arResult['YEARS'] as $key => $val) : ?>
            <button data-year="<?= $key ?>" class="col-md-2 tab<?=($val == 'true') ? " selected": ''?>"
            <?=($val == 'true') ? " disabled": ''?>>
                <?= $key ?>
            </button>
        <? endforeach; ?>
    </div>
</div>
<div class="container-fluid newslist">
    <? foreach ($arResult['ITEMS'] as $item) : ?>
        <div class="row news">
            <div class="col-md-12">
                <h3><?= $item['NAME'] ?></h3>
            </div>
            <div class="col-md-12">
                <p><?= $item['ACTIVE_FROM_FORMATED'] ?></p>
            </div>
            <div class="col-md-3 news__img">
                <img src="<?= $item['PREVIEW_PICTURE_SRC'] ?>" alt="">
            </div>
            <div class="col-md-9">
                <p><?= $item['PREVIEW_TEXT'] ?></p>
            </div>
        </div>
    <? endforeach; ?>
    <?
    $APPLICATION->IncludeComponent(
        "bitrix:main.pagenavigation",
        "",
        array(
            "NAV_OBJECT" => $arResult['NAV'],
            "SEF_MODE" => "N",
        ),
        $component
    );
    ?>
</div>