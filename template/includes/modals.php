<div class="modal-container hidden">
    <div class="modal-overlay"></div>
    <div class="modal-table">
        <div class="modal-cell">
            <div class="modal hidden" id="upload">
                <div class="title">
                    <span>Добавить изображение</span>
                    <a class="close" onclick="modal.close()"><i class="fa fa-times"></i></a>
                </div>
                <div class="content">
                    <form class="upload">
                        <img src="">
                        <input type="file" name="file" accept="image/*" onchange="script.upload.preview(event)">
                        <ul class="tags">
                            <li><input type="text" placeholder="Теги через запятую"></li>
                        </ul>
                        <ul class="pred"></ul>
                        <div class="menu">
                            <button type="button" onclick="script.upload.select()">Выбрать</button>
                            <button type="button" onclick="script.upload.send()">Загрузить</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
