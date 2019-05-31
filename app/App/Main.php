<?php
namespace App;
class Main extends App {
    function index() {
        return view('layout');
    }
    function load($request) {
        $rand = intval($request['rand'] ?? 0);
        $offset = intval($request['offset'] ?? 0);
        $limit = config('posts.load_limit');
        return $this->json(db()->select("SELECT `id`, `src`, `size` FROM posts WHERE `status` = 1 ORDER BY RAND($rand) LIMIT $limit OFFSET $offset"));
    }
    function tload($request) {
        $tag = $this->e(trim($request['tag']));
        $tags = db()->select("SELECT `name` FROM tags WHERE `name` LIKE '$tag%' ORDER BY `count` DESC LIMIT 5");
        return $this->json($tags);
    }
    function upload($request) {
        if(!isset($request['file'])) return $this->json(['message' => 'Файл не выбран']);
        $file = file_get_contents($request['file']['tmp_name']);
        if(!$file) return $this->json(['message' => 'Ошибка загрузки']);
        $size = getimagesizefromstring($file);
        if($size[0] < config('posts.min_width')) return $this->json(['message' => 'Мин. ширина '.config('posts.min_width').'px']);
        if($size[0]/$size[1] > 2 || $size[1]/$size[0] > 2) return $this->json(['message' => 'Соотношение сторон < 2/1']);
        $size = $this->json([
            'width' => $size[0],
            'height' => $size[1],
        ]);
        $img = imagecreatefromstring($file);
        if(!$img) return $this->json(['message' => 'Неверный формат']);
        $phash = $this->phash($img);
        $name = $this->gname();
        $view = $this->gimg($img);
        imagedestroy($img);
        $hash = sha1($view);
        $user = auth()->user();
        if(db()->select("SELECT `id` FROM posts WHERE hash='$hash' OR phash='$phash' LIMIT 1")) return $this->json(['message' => 'Изображение уже существует']);
        $post = db()->insert("INSERT INTO posts(`size`, `hash`, `phash`, `user`, `tags`) VALUES ('$size', '$hash', '$phash', '$user[id]', '')");
        $s3 = s3()->put('sknx', $name.'.jpg', $view, 'image/jpeg');
        if(!$s3) return $this->json(['message' => 'Ошибка загрузки']);
        $tags = explode(',', $request['tags']);
        foreach ($tags as $tag) {
             if($tag = $this->e(substr($tag, 0, 255))){
                 if($tdb = db()->select("SELECT * FROM tags WHERE `name` LIKE '$tag' LIMIT 1")[0] ?? false){
                     db()->update("UPDATE tags SET `count`=`count`+1 WHERE `id`=$tdb[id]");
                 } else {
                     db()->insert("INSERT INTO tags(`name`, `count`) VALUES ('$tag', '1')");
                 }
                 $ntags[] = $tag;
             }
        }
        $tags = implode(',', $ntags ?? []);
        db()->update("UPDATE posts SET `src`='$s3[ObjectURL]', `tags`='$tags', `status`=1 WHERE `id`=$post");
        return $this->json(['message' => 'Изображение загружено']);
    }
    function gname() {
        return base_convert(time().rand(0, 9).rand(0, 9).rand(0, 9).rand(0, 9).rand(0, 9), 10, 36);
    }
    function gimg($img) {
        ob_start();
        imagejpeg($img);
        return ob_get_clean();
    }
    function phash($img) {
        $scale = 16;
        $pimg = imagescale($img, $scale, $scale);
        // imagefilter($pimg, IMG_FILTER_GRAYSCALE);
        $bytes = '';
        for ($y=0; $y < $scale; $y++) for ($x=0; $x < $scale; $x++) {
            $rgb = imagecolorat($pimg, $x, $y);
            $r = ($rgb >> 16) & 255;
            $g = ($rgb >> 8) & 255;
            $b = $rgb & 255;
            $gray = $r + $g + $b;
            $tone[] = floor($gray / 12);
        }
        imagedestroy($pimg);
        $avg = floor(array_sum($tone) / count($tone));
        foreach ($tone as $t) $bytes .= $t >= $avg ? 1 : 0;
        return base_convert($bytes, 2, 36);
    }
}
?>
