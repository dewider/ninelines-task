<?

use \Bitrix\Main\Loader,
	\Bitrix\Main\Localization\Loc,
	\Bitrix\Iblock\Iblock,
	\Bitrix\Main\UI\PageNavigation,
	\Bitrix\Main\UI\Extension,
	\Bitrix\Main\Entity\ExpressionField;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

if (!Loader::includeModule('iblock')) {
	ShowError(Loc::getMessage('IBLOCK_MODULE_NOT_INSTALLED'));
	return;
}


class SimpleNewsComponent extends CBitrixComponent
{
	private $iblockClass, $selectedYear, $paramYear;

	public function __construct($component = null)
	{
		parent::__construct($component);
		Extension::load('ui.bootstrap4');
	}

	public function onPrepareComponentParams($params)
	{
		$params = parent::onPrepareComponentParams($params);
		$params['NEWS_COUNT'] = (isset($params['NEWS_COUNT'])) ? intval($params['NEWS_COUNT']) : 5;
		return $params;
	}

	protected function checkIblock()
	{
		if (!empty($this->iblockClass)) return $this->iblockClass;
		if (empty($this->arParams['IBLOCK_ID'])) return false;

		$this->iblockClass = Iblock::wakeUp($this->arParams['IBLOCK_ID'])->getEntityDataClass();
		if (!$this->iblockClass) {
			ShowError(Loc::getMessage('SET_API_CODE'));
			return false;
		}

		return $this->iblockClass;
	}

	protected function getYears()
	{
		if (!$this->checkIblock()) return;

		$dbItems = $this->iblockClass::getList([
			'select' => [new ExpressionField('YEAR', 'YEAR(%s)', ['ACTIVE_FROM'])],
			'filter' => [
				'ACTIVE' => 'Y'
			],
			'order' => [
				'YEAR' => 'DESC'
			],
			'group' => ['YEAR'],
			'cache' => [
				'ttl' => 3600
			]
		]);

		$this->arResult['YEARS'] = [];
		$dif = 9999;
		while ($arItem = $dbItems->fetch()) {
			if (abs($this->paramYear - intval($arItem['YEAR'])) < $dif) {
				$this->selectedYear = intval($arItem['YEAR']);
				$dif = abs($this->paramYear - $arItem['YEAR']);
			}
			$this->arResult['YEARS'][$arItem['YEAR']] = 'false';
		}
		$this->arResult['YEARS'][$this->selectedYear] = 'true';
	}

	protected function getItems()
	{
		if (!$this->checkIblock()) return;

		$dbItems = $this->iblockClass::getList([
			'select' => [
				'NAME', 'PREVIEW_TEXT', 'PREVIEW_PICTURE', 'ACTIVE_FROM',
				new ExpressionField('YEAR', 'YEAR(%s)', ['ACTIVE_FROM'])
			],
			'filter' => [
				'ACTIVE' => 'Y',
				'YEAR' => $this->selectedYear
			],
			'order' => [
				'ACTIVE_FROM' => 'DESC'
			],
			'offset' => $this->arResult['NAV']->getOffset(),
			'limit' => $this->arResult['NAV']->getLimit(),
			'count_total' => true,
			'cache' => [
				'ttl' => 3600
			]
		]);
		$this->arResult['COUNT'] = $dbItems->getCount();
		$this->arResult['NAV']->setRecordCount($this->arResult['COUNT']);

		$this->arResult['ITEMS'] = [];
		while ($arItem = $dbItems->fetch()) {
			$arItem['PREVIEW_PICTURE_SRC'] = CFile::GetPath($arItem['PREVIEW_PICTURE']);
			$arItem['ACTIVE_FROM_FORMATED'] = $arItem['ACTIVE_FROM']->format('d.m.Y');
			array_push($this->arResult['ITEMS'], $arItem);
		}
	}

	public function executeComponent()
	{
		global $APPLICATION, $USER;

		$this->arResult['NAV'] = new PageNavigation("nav-news");
		$this->arResult['NAV']->setPageSize($this->arParams['NEWS_COUNT'])->initFromUri();

		$this->paramYear = (isset($_REQUEST['year'])
			&& intval($_REQUEST['year']) >= 1970
			&& intval($_REQUEST['year']) <= 3000)
			? intval($_REQUEST['year']) : date("Y");

		$CACHE_ID = SITE_ID . "|" . $APPLICATION->GetCurPage() . "|";
		foreach ($this->arParams as $k => $v)
			if (strncmp("~", $k, 1))
				$CACHE_ID .= "," . $k . "=" . $v;
		$CACHE_ID .= "|" . $USER->GetGroups()
			. "|year=" . $this->paramYear
			. "|" . $this->arResult['NAV']->getCurrentPage();

		if ($this->StartResultCache($this->arParams['CACHE_TIME'], $CACHE_ID)) {
			$this->getYears();
			$this->getItems();
			$this->includeComponentTemplate();
		}

		$APPLICATION->SetTitle(GetMessage('NEWS_COUNT_PRE') . $this->arResult['COUNT'] . GetMessage('NEWS_COUNT_POST'));
	}
}
