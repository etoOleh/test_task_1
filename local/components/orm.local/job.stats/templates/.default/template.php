<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;

Loader::includeModule('orm.local');
?>

    <form action="<?=POST_FORM_ACTION_URI?>" method="post" enctype="multipart/form-data">
        <button name="start" type="submit" value="1" class="btn btn-primary">START</button>
    </form>

    <form action="<?=POST_FORM_ACTION_URI?>" method="post" enctype="multipart/form-data">
        <button name="stop" type="submit" value="1" class="btn btn-primary">STOP</button>
    </form>

    <form action="<?=POST_FORM_ACTION_URI?>" method="post" enctype="multipart/form-data">
        <button name="pause" type="submit" value="1" class="btn btn-primary">PAUSE</button>
    </form>

    <form action="<?=POST_FORM_ACTION_URI?>" method="post" enctype="multipart/form-data">
        <button name="nopause" type="submit" value="1" class="btn btn-primary">CONTINUE</button>
    </form>
