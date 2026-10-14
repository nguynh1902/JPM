Add-Type -AssemblyName System.Drawing
$file = "C:\xampp\htdocs\JPM\screenshot_full.png"
$bmp = [System.Drawing.Bitmap]::FromFile($file)
# Find the sponsors section: let's crop from Y=1300 to 2000
$rect = New-Object System.Drawing.Rectangle(0, 1400, $bmp.Width, 700)
$crop = $bmp.Clone($rect, $bmp.PixelFormat)
$crop.Save("C:\xampp\htdocs\JPM\scratch_sponsors_crop.png", [System.Drawing.Imaging.ImageFormat]::Png)
$crop.Dispose()
$bmp.Dispose()
Write-Output "Saved sponsors crop"
