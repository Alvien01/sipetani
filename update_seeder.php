<?php

function get_category_id($product_name) {
    $name = strtolower($product_name);
    
    // 5: Kebutuhan dapur
    $dapur_keywords = ['sampo', 'shampo', 'sabun', 'rinso', 'soklin', 'pepsodent', 'sikat', 'harpic', 'dahlia', 'paseo', 'tisu', 'wipol', 'sunlight', 'mama lime', 'clear', 'pantene', 'lifebuoy', 'charm', 'laurier', 'gas', 'sendok', 'pisau', 'detol', 'dettol', 'gentle gen', 'daia', 'glade', 'bayfresh', 'kapur barus', 'pewangi', 'deterjen', 'pembalut', 'shinzui', 'giv', 'sunsilk', 'head & shoulders'];
    foreach ($dapur_keywords as $k) {
        if (strpos($name, $k) !== false) return 5;
    }
        
    // 6: Bumbu dapur
    $bumbu_keywords = ['bumbu', 'bmb', 'garam', 'gula', 'merica', 'ladaku', 'penyedap', 'masako', 'royco', 'kecap', 'saus', 'saos', 'sambal', 'smbl', 'cengkeh', 'tumbar', 'ketumbar', 'kemiri', 'desaku', 'bango', 'sasa', 'lada', 'minyak', 'bimoli', 'sunco', 'kunyit', 'jahe', 'laos', 'terasi', 'vanili', 'baking powder', 'tepung', 'tpg', 'sajiku', 'kobe', 'santan', 'kara', 'nutrijell', 'abon', 'saori'];
    foreach ($bumbu_keywords as $k) {
        if (strpos($name, $k) !== false) return 6;
    }
        
    // 1: Sayur
    $sayur_keywords = ['sayur', 'bayam', 'kangkung', 'sawi', 'tomat', 'buncis', 'brokoli', 'wortel', 'kubis', 'terong', 'selada', 'hidroponik', 'caisin', 'pakcoy', 'lombok', 'cabe', 'bawang', 'daun', 'seledri', 'pete', 'kacang panjang', 'kemangi'];
    foreach ($sayur_keywords as $k) {
        if (strpos($name, $k) !== false) return 1;
    }
        
    // 2: Buah
    $buah_keywords = ['buah', 'apel', 'jeruk', 'pisang', 'mangga', 'anggur', 'melon', 'semangka', 'pepaya', 'manggis', 'strawberry', 'alpukat', 'jambu', 'sirsak', 'nanas', 'santang', 'cavendish', 'kurma', 'kelapa'];
    foreach ($buah_keywords as $k) {
        if (strpos($name, $k) !== false) return 2;
    }
        
    // 4: Cemilan
    $cemilan_keywords = ['cemilan', 'keripik', 'piattos', 'snack', 'biskuit', 'chitato', 'yupi', 'roma', 'coklat', 'choco', 'es krim', 'walls', 'paddle pop', 'cornetto', 'agar', 'jelly', 'kacang', 'wafer', 'nabati', 'oreo', 'puding', 'permen', 'chocolatos', 'kue', 'kuaci', 'kacang atom', 'kacang kulit', 'malkist', 'beng beng', 'silverqueen', 'taro', 'cheetos'];
    foreach ($cemilan_keywords as $k) {
        if (strpos($name, $k) !== false) return 4;
    }
        
    // 3: Makanan (Default)
    return 3;
}

$path = 'D:\Code\sipetani\database\seeders\ProductSeeder.php';
$content = file_get_contents($path);

$content = preg_replace_callback(
    "/\['id'(.*?)\]/s",
    function ($matches) {
        $full_str = $matches[0];
        
        // Skip if already contains id_kategori
        if (strpos($full_str, "'id_kategori'") !== false) {
            return $full_str;
        }
        
        if (preg_match("/'product_name'\s*=>\s*'(.*?)',\s*'slug'/", $full_str, $name_match)) {
            $cat_id = get_category_id($name_match[1]);
            
            $full_str = preg_replace(
                "/('product_name'\s*=>\s*'.*?',\s*)/",
                "$1'id_kategori' => $cat_id, ",
                $full_str,
                1 // Replace only the first occurrence to avoid messing up other things
            );
        }
        return $full_str;
    },
    $content
);

file_put_contents($path, $content);
echo "ProductSeeder updated successfully.\n";
