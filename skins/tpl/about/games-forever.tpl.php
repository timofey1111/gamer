<style>
    #main{width:960px;margin:0 auto;overflow:hidden;padding-top:10px;}
    .right-menu{margin-left:230px;}
    .left-content, .center-content, .right-content{float:left;margin-left:20px;width:220px;}
    .inner-content h3{margin:0 0 15px 0;color:#1fbba6;}
    .inner-content p{margin-bottom:5px;color:#63738e;font-size:16px; }
    .inner-content a{color:#1fbba6;font-size:16px;}
    .inner-content{margin-bottom:15px;}
</style>

<div id="main">
    <?include $_SERVER['DOCUMENT_ROOT'] . '/skins/tpl/block/menu-about.block.tpl.php';?>
 <div class="right-menu">
        <div class="left-content">
            <div class="inner-content">
                <h3>Dead Island</h3>
                <p>Описание игры. Описание игры. Описание игры.</p>
                <a href="#">Ссылка на игру</a>
            </div>
            <img src="/skins/img/game-poster.jpg"/>
        </div>
        <div class="center-content">
            <div class="inner-content">
                <h3>Dead Island</h3>
                <p>Описание игры. Описание игры. Описание игры.</p>
                <a href="#">Ссылка на игру</a>
            </div>
            <img src="/skins/img/game-poster.jpg"/>
        </div>
        <div class="right-content">
            <div class="inner-content">
                <h3>Dead Island</h3>
                <p>Описание игры. Описание игры. Описание игры.</p>
                <a href="#">Ссылка на игру</a>
            </div>
            <img src="/skins/img/game-poster.jpg"/>
        </div>
    </div>
    <? include "/skins/tpl/block/share-soc.block.tpl.php"; ?>
</div>