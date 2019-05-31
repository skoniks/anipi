<div class="header">
    <div class="container">
        <div class="logo">
            <a href="/">
                <img src="/assets/img/icon.png" alt="ANIPI.CC">
                <span>ANIPI.CC</span>
            </a>
        </div>
        <span class="menu">
            <?php if(auth()->user()): ?>
                <a style="width: 50px;">
                    <img src="<?php echo(auth()->user()['avatar']); ?>">
                </a>
                <a onclick="modal.open('upload')">Добавить</a>
                <a href="/logout">Выйти</a>
            <?php else: ?>
                <a href="/login">Войти</a>
            <?php endif; ?>
        </span>
    </div>
</div>
