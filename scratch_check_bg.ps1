Add-Type -AssemblyName System.Drawing
$file = "C:\Users\LENOVO\.gemini\antigravity\brain\893b5868-3241-48fd-b04c-d8055458ff7a\.user_uploaded\media_1791959562815_e9b7e474.png"
$bmp = [System.Drawing.Bitmap]::FromFile($file)
$p1 = $bmp.GetPixel(10, 10)
$p2 = $bmp.GetPixel(100, 10)
$p3 = $bmp.GetPixel(400, 10)
$p4 = $bmp.GetPixel(200, 200)
Write-Output "P(10,10): R=$($p1.R) G=$($p1.G) B=$($p1.B) A=$($p1.A)"
Write-Output "P(100,10): R=$($p2.R) G=$($p2.G) B=$($p2.B) A=$($p2.A)"
Write-Output "P(400,10): R=$($p3.R) G=$($p3.G) B=$($p3.B) A=$($p3.A)"
Write-Output "P(200,200): R=$($p4.R) G=$($p4.G) B=$($p4.B) A=$($p4.A)"
$bmp.Dispose()
