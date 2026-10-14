Add-Type -AssemblyName System.Drawing
$file = "C:\xampp\htdocs\JPM\FE_JPM\public\client\image\mecury_thuonghieu.png"
$bmp = [System.Drawing.Bitmap]::FromFile($file)
$rect = New-Object System.Drawing.Rectangle(400, 500, 600, 200)
$crop = $bmp.Clone($rect, $bmp.PixelFormat)
$crop.Save("C:\xampp\htdocs\JPM\scratch_mercury_crop.png", [System.Drawing.Imaging.ImageFormat]::Png)
$crop.Dispose()
$bmp.Dispose()
Write-Output "Saved crop to scratch_mercury_crop.png"
