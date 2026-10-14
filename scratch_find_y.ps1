Add-Type -AssemblyName System.Drawing
$file = "C:\xampp\htdocs\JPM\screenshot_full.png"
$bmp = [System.Drawing.Bitmap]::FromFile($file)
for ($y = 1500; $y -lt $bmp.Height; $y += 50) {
    $p = $bmp.GetPixel(600, $y)
    Write-Output "Y=$($y) R=$($p.R) G=$($p.G) B=$($p.B)"
}
$bmp.Dispose()
