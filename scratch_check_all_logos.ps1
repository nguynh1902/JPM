Add-Type -AssemblyName System.Drawing
foreach ($name in @('yamaha_thuonghieu.png', 'honda_marine_thuonghieu.png', 'suzuki_marine_thuonghieu.png', 'mecury_thuonghieu.png', 'tohatsu_thuonghieu.png', 'yanmar_thuonghieu.png', 'seadoo_thuonghieu.png', 'jetki_thuonghieu.png')) {
    $file = "C:\xampp\htdocs\JPM\FE_JPM\public\client\image\$name"
    $bmp = [System.Drawing.Bitmap]::FromFile($file)
    $minX = $bmp.Width; $maxX = 0; $minY = $bmp.Height; $maxY = 0
    for ($y = 0; $y -lt $bmp.Height; $y += 5) {
        for ($x = 0; $x -lt $bmp.Width; $x += 5) {
            $p = $bmp.GetPixel($x, $y)
            if ($p.A -gt 10) {
                if ($x -lt $minX) { $minX = $x }
                if ($x -gt $maxX) { $maxX = $x }
                if ($y -lt $minY) { $minY = $y }
                if ($y -gt $maxY) { $maxY = $y }
            }
        }
    }
    Write-Output "$name : W=$($bmp.Width) H=$($bmp.Height) | Content: W=$($maxX-$minX) H=$($maxY-$minY)"
    $bmp.Dispose()
}
