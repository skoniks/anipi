window.$d = {
    cN: function (n = '') {
        return n.split(' ').filter(function (i) {
            return !!i;
        });
    },
    cAdd: function (n = '', c = '') {
        var l = this.cN(n), i = l.indexOf(c);
        if(i == -1) l.push(c);
        return l.join(' ');
    },
    cDel: function (n = '', c = '') {
        var l = this.cN(n), i = l.indexOf(c);
        if(i != -1) l.splice(i, 1);
        return l.join(' ');
    },
    cTog: function (n = '', c = '') {
        return this.cN(n).indexOf(c) == -1 ? this.cAdd(n, c) : this.cDel(n, c);
    },
    qS: function (q) {
        return document.querySelector(q);
    },
    qSA: function (q) {
        return document.querySelectorAll(q);
    },
}
window.script = {
    search: false,
    grid: {
        columns: [], _scroll: false,
        update: function () {
            var count = Math.max(Math.min(Math.floor(window.innerWidth / 300), 4), 1);
            if(script.grid.columns.length != count){
                script.grid.columns = [];
                $d.qS('.grid').innerHTML = '';
                for (var i=0; i<count; i++) script.grid.columns[i] = 0, $d.qS('.grid').innerHTML += '<div class="column" id="c' + i + '"></div>';
                script.post.redraw();
            }
        },
        min: function () {
            var min = 0, size = this.columns[0];
            for (var i in this.columns) if(this.columns[i] < size) min = i, size = this.columns[i];
            return min;
        },
        scroll: function (e) {
            if(window.screen.height + window.pageYOffset > document.body.scrollHeight - 1000)
                if(script.download.loading){
                    if(script.grid._scroll) clearTimeout(script.grid._scroll);
                    script.grid._scroll = setTimeout(function () {
                        script.grid._scroll = false;
                        script.grid.scroll(e);
                    }, 1000);
                } else script.download.get();
        },
    },
    post: {
        offset: 0, list: [], queue: [], timeout: false,
        load: function (post) {
            var img = new Image();
            img.src = post.src;
            img.onload = () => {
                this.queue.push({
                    img: img,
                    post: post,
                }), this.list.push({
                    img: img,
                    post: post,
                }), this.draw();
            }
        },
        draw: function () {
            if(this.timeout) return;
            if(!this.queue.length) return;
            this.timeout = setTimeout(function () {
                clearTimeout(script.post.timeout);
                script.post.timeout = false;
                script.post.draw();
            }, 50);
            var item = this.queue.splice(0, 1)[0],
                size = JSON.parse(item.post.size),
                ratio = size.height / size.width,
                column = script.grid.min(),
                el = document.createElement('div');
            el.className = 'item';
            el.dataset.id = item.post.id;
            el.appendChild(item.img);
            script.grid.columns[column] += ratio;
            $d.qS('.column#c' + column).appendChild(el);
        },
        redraw: function () {
            for (var i in this.list) this.queue.push(this.list[i]);
            this.draw();
        },
    },
    download: {
        rand: Math.floor(Math.random() * 9999), loading: false,
        get: function () {
            this.loading = true;
            fetch('/load', {
                method: 'post',
                headers: {"Content-type": "application/x-www-form-urlencoded; charset=UTF-8"},
                body: 'rand=' + this.rand + '&offset=' + script.post.offset,
            }).then(function(response) {
                response.json().then(function(data) {
                    for(var i in data) script.post.load(data[i]);
                    script.post.offset += data.length;
                    setTimeout(function () {
                        script.download.loading = false;
                    }, 1000);
                });
            }).catch(function (e) {
                script.download.loading = false;
                console.warn(e);
            });
        },
    },
    upload: {
        tags: [], loading: false, tloading: false, ttimeout: false,
        tAdd: function (tag) {
            tag = tag.trim();
            if(this.tags.indexOf(tag) != -1) return;
            this.tags.push(tag);
            var el = document.createElement('li');
            el.className = 'tag';
            el.dataset.tag = tag;
            el.innerHTML = tag;
            el.onclick = function () {
                script.upload.tDel(tag);
            }
            var ch = $d.qS('.upload .tags').children;
            $d.qS('.upload .tags').insertBefore(el, ch[ch.length-1]);
        },
        tDel: function (tag) {
            this.tags.splice(this.tags.indexOf(tag), 1);
            $d.qS('.upload .tags .tag[data-tag="' + tag + '"]').remove();
        },
        tPred: function (tag) {
            $d.qS('.upload .pred .tag[data-tag="' + tag + '"]').remove();
            $d.qS('.upload .tags input').value = ''
            this.tAdd(tag);
        },
        tLoad: function () {
            var t = $d.qS('.upload .tags input').value.trim();
            if(t.length >= 3){
                if(script.upload.tloading){
                    if(!this.ttimeout) this.ttimeout = setTimeout(function () {
                        clearTimeout(script.upload.ttimeout);
                        script.upload.ttimeout = false;
                        script.upload.tLoad();
                    }, 200);
                } else {
                    script.upload.tloading = true;
                    fetch('/tload', {
                        method: 'post',
                        headers: {"Content-type": "application/x-www-form-urlencoded; charset=UTF-8"},
                        body: 'tag=' + t,
                    }).then(function(response) {
                        response.json().then(function(data) {
                            $d.qS('.upload .pred').innerHTML = '';
                            for(var i in data){
                                var el = document.createElement('li');
                                el.className = 'tag';
                                el.dataset.tag = data[i].name;
                                el.innerHTML = data[i].name;
                                el.onclick = function () {
                                    script.upload.tPred(data[i].name);
                                    console.log(data[i]);
                                }
                                $d.qS('.upload .pred').appendChild(el);
                            }
                            setTimeout(function () {
                                script.upload.tloading = false;
                            }, 1000);
                        });
                    }).catch(function (e) {
                        script.upload.tloading = false;
                        console.warn(e);
                    });
                }
            } else {
                $d.qS('.upload .pred').innerHTML = '';
            }
        },
        select: function () {
            $d.qS('.upload input[type=file]').click();
        },
        preview: function (event) {
            $d.qS('.upload img').src = (event.target.files[0] ? URL.createObjectURL(event.target.files[0]) : '');
        },
        send: function () {
            if(this.loading) return notify('Подождите');
            var file = $d.qS('.upload input[type=file]').files[0];
            if(!file) return notify('Файл не выбран');
            notify('Загрузка файла');
            var form = new FormData($d.qS('form.upload'));
            form.append('tags', this.tags);
            this.loading = true;
            fetch('/upload', {
                method: 'post',
                body: form,
            }).then(function(response) {
                response.json().then(function(data) {
                    !data.message || notify(data.message);
                    script.upload.loading = false;
                });
            }).catch(function (e) {
                script.upload.loading = false;
                onsole.log(e);
            });
        },
    }
}
window.modal = {
    close: function () {
        $d.qS('body').style.overflow = '';
        var mc = $d.qS('.modal-container');
        mc.className = $d.cAdd(mc.className, 'fadeout');
        setTimeout(function () {
            mc.className = $d.cAdd(mc.className, 'hidden');
            mc.className = $d.cDel(mc.className, 'fadeout');
            $d.qSA('.modal').forEach(function(item){
                item.className = $d.cAdd(item.className, 'hidden');
            });
        }, 500);
    },
    open: function (modal) {
        $d.qS('body').style.overflow = 'hidden';
        var mc = $d.qS('.modal-container'),
            m = $d.qS('.modal#' + modal);
        mc.className = $d.cAdd(mc.className, 'fadein');
        mc.className = $d.cDel(mc.className, 'hidden');
        $d.qSA('.modal').forEach(function(item){
            item.className = $d.cAdd(item.className, 'hidden');
        });
        m.className = $d.cDel(m.className, 'hidden');
    },
}
window.addEventListener('resize', script.grid.update, true);
window.addEventListener('scroll', script.grid.scroll, true);
window.onload = function () {
    script.grid.update(), script.download.get();
    $d.qS('.upload .tags input').onkeydown = function(e) {
        if(e.key == 'Enter' || e.key == ',') e.preventDefault(), script.upload.tAdd(this.value), this.value = '';
        if(e.key == 'Backspace' && this.value == '' && script.upload.tags.length) script.upload.tDel(script.upload.tags[script.upload.tags.length-1]);
        setTimeout(script.upload.tLoad, 50);
    }
}
function notify(text = '', time = 5) {
    var el = document.createElement('div');
    el.className = 'fadein';
    el.innerHTML = '<span>' + text + '</span>';
    $d.qS('.notify').appendChild(el);
    el.addEventListener('click', function(e) {
        !el || (el.className = 'fadeout', setTimeout(function () {
            !el || $d.qS('.notify').removeChild(el), el = false;
        }, 500));
    })
    setTimeout(function () {
        !el || (el.className = 'fadeout', setTimeout(function () {
            !el || $d.qS('.notify').removeChild(el), el = false;
        }, 500));
    }, time * 1000);
}
