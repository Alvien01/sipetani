import re

def get_category_id(product_name):
    name = product_name.lower()
    
    # 5: Kebutuhan dapur
    dapur_keywords = ['sampo', 'shampo', 'sabun', 'rinso', 'soklin', 'pepsodent', 'sikat', 'harpic', 'dahlia', 'paseo', 'tisu', 'wipol', 'sunlight', 'mama lime', 'clear', 'pantene', 'lifebuoy', 'charm', 'laurier', 'gas', 'sendok', 'pisau', 'detol', 'dettol', 'gentle gen', 'daia', 'glade', 'bayfresh', 'kapur barus', 'pewangi', 'deterjen', 'pembalut', 'shinzui', 'giv', 'sunsilk', 'head & shoulders']
    if any(re.search(rf'\b{k}\b', name) or k in name for k in dapur_keywords):
        return 5
        
    # 6: Bumbu dapur
    bumbu_keywords = ['bumbu', 'bmb', 'garam', 'gula', 'merica', 'ladaku', 'penyedap', 'masako', 'royco', 'kecap', 'saus', 'saos', 'sambal', 'smbl', 'cengkeh', 'tumbar', 'ketumbar', 'kemiri', 'desaku', 'bango', 'sasa', 'lada', 'minyak', 'bimoli', 'sunco', 'kunyit', 'jahe', 'laos', 'terasi', 'vanili', 'baking powder', 'tepung', 'tpg', 'sajiku', 'kobe', 'santan', 'kara', 'nutrijell', 'abon', 'saori']
    if any(re.search(rf'\b{k}\b', name) or k in name for k in bumbu_keywords):
        return 6
        
    # 1: Sayur
    sayur_keywords = ['sayur', 'bayam', 'kangkung', 'sawi', 'tomat', 'buncis', 'brokoli', 'wortel', 'kubis', 'terong', 'selada', 'hidroponik', 'caisin', 'pakcoy', 'lombok', 'cabe', 'bawang', 'daun', 'seledri', 'pete', 'kacang panjang', 'kemangi']
    if any(re.search(rf'\b{k}\b', name) for k in sayur_keywords):
        return 1
        
    # 2: Buah
    buah_keywords = ['buah', 'apel', 'jeruk', 'pisang', 'mangga', 'anggur', 'melon', 'semangka', 'pepaya', 'manggis', 'strawberry', 'alpukat', 'jambu', 'sirsak', 'nanas', 'santang', 'cavendish', 'kurma', 'kelapa']
    if any(re.search(rf'\b{k}\b', name) for k in buah_keywords):
        return 2
        
    # 4: Cemilan
    cemilan_keywords = ['cemilan', 'keripik', 'piattos', 'snack', 'biskuit', 'chitato', 'yupi', 'roma', 'coklat', 'choco', 'es krim', 'walls', 'paddle pop', 'cornetto', 'agar', 'jelly', 'kacang', 'wafer', 'nabati', 'oreo', 'puding', 'permen', 'chocolatos', 'kue', 'kuaci', 'kacang atom', 'kacang kulit', 'malkist', 'beng beng', 'silverqueen', 'taro', 'cheetos']
    if any(re.search(rf'\b{k}\b', name) or k in name for k in cemilan_keywords):
        return 4
        
    # 3: Makanan (Default/Catch-all untuk sosis, nugget, mie, beras, susu, kopi, dll)
    return 3

path = r"D:\Code\sipetani\database\seeders\ProductSeeder.php"
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

def replace_func(match):
    full_str = match.group(0)
    # Check if 'id_kategori' already exists
    if "'id_kategori'" in full_str:
        return full_str
        
    name_match = re.search(r"'product_name'\s*=>\s*'([^']+)'", full_str)
    if name_match:
        cat_id = get_category_id(name_match.group(1))
        # Insert 'id_kategori' => X, after product_name
        return re.sub(
            r"('product_name'\s*=>\s*'[^']+',)",
            rf"\1 'id_kategori' => {cat_id},",
            full_str
        )
    return full_str

# Match lines that represent a product array
new_content = re.sub(r"\['id'\s*=>\s*\d+.*?\](?=,|$)", replace_func, content)

with open(path, 'w', encoding='utf-8') as f:
    f.write(new_content)

print("ProductSeeder updated successfully.")
